<?php

namespace App\Http\Controllers;

use App\Models\CpanelAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CpanelAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = CpanelAccount::orderByDesc('created_at')->get();

        return view('admin.cpanel-account.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.cpanel-account.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique(CpanelAccount::class, 'name')],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cpanel_accounts')->where(fn ($query) => $query->where('host', $request->host)),
            ],
            'primary_domain' => ['nullable', 'string', 'max:255'],
            'api_token' => ['required', 'string'],
            'use_ssl' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        CpanelAccount::create([
            'name' => $validated['name'],
            'host' => $validated['host'],
            'port' => $validated['port'],
            'username' => $validated['username'],
            'primary_domain' => $validated['primary_domain'] ?? null,
            'api_token' => $validated['api_token'],
            'use_ssl' => $request->boolean('use_ssl', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('cpanel-account.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(CpanelAccount $cpanelAccount)
    {
        return view('admin.cpanel-account.edit', compact('cpanelAccount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CpanelAccount $cpanelAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CpanelAccount $cpanelAccount)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique(CpanelAccount::class, 'name')->ignore($cpanelAccount->id)],
            'host' => ['required', 'string', 'max:255'],
            'port' => ['required', 'integer', 'between:1,65535'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('cpanel_accounts')
                    ->where(fn ($query) => $query->where('host', $request->host))
                    ->ignore($cpanelAccount->id),
            ],
            'primary_domain' => ['nullable', 'string', 'max:255'],
            'api_token' => ['nullable', 'string'],
            'use_ssl' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'host' => $validated['host'],
            'port' => $validated['port'],
            'username' => $validated['username'],
            'primary_domain' => $validated['primary_domain'] ?? null,
            'use_ssl' => $request->boolean('use_ssl', true),
            'is_active' => $request->boolean('is_active', true),
        ];

        if (!empty($validated['api_token'])) {
            $payload['api_token'] = $validated['api_token'];
        }

        $cpanelAccount->update($payload);

        return redirect()->route('cpanel-account.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CpanelAccount $cpanelAccount)
    {
        $cpanelAccount->delete();

        return redirect()->back();
    }
}
