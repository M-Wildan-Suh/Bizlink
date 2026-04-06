<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected function getAssignableRoles(): array
    {
        $user = Auth::user();

        if ($user?->isSuperadmin()) {
            return [User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_USER];
        }

        return [User::ROLE_USER];
    }

    protected function ensureCanManageUserRole(User $targetUser): void
    {
        $actor = Auth::user();

        if ($actor?->isSuperadmin()) {
            return;
        }

        if (in_array($targetUser->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah akun admin atau superadmin.');
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $data = User::where('name', 'like', '%' . $request->search . '%')->paginate(10);
        } else {
            $data = User::paginate(10);
        }
        return view('admin.user.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = $this->getAssignableRoles();
        return view('admin.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:'.User::class,
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'role' => ['required', Rule::in($this->getAssignableRoles())],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        return redirect()->route('user.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManageUserRole($user);
        $roles = $this->getAssignableRoles();
        return view('admin.user.edit', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManageUserRole($user);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', Rule::in($this->getAssignableRoles())],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->slug = Str::slug($request->name);
        $user->email = $request->email;
        $user->role = $request->role;

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        // Cek jika user yang akan dihapus adalah user yang sedang login
        if ($user->id === Auth::id()) {
            throw ValidationException::withMessages([
                'user' => ['Anda tidak dapat menghapus akun Anda sendiri.'],
            ]);
        }

        // Cek jika user yang akan dihapus adalah user pertama
        $firstUserId = User::orderBy('id')->value('id');
        if ($user->id === $firstUserId) {
            throw ValidationException::withMessages([
                'user' => ['Pengguna pertama(Admin) tidak dapat dihapus.'],
            ]);
        }

        if (!Auth::user()?->isSuperadmin() && in_array($user->role, [User::ROLE_SUPERADMIN, User::ROLE_ADMIN], true)) {
            throw ValidationException::withMessages([
                'user' => ['Anda tidak dapat menghapus akun dengan role admin atau superadmin.'],
            ]);
        }

        $user->delete();

        return back();
    }
}
