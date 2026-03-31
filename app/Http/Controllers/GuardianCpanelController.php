<?php

namespace App\Http\Controllers;

use App\Models\GuardianWeb;
use App\Services\CpanelApiService;
use Illuminate\Http\Request;

class GuardianCpanelController extends Controller
{
    public function __construct(
        private readonly CpanelApiService $cpanelApiService
    ) {
    }

    public function createDomain(GuardianWeb $guardian)
    {
        try {
            $this->cpanelApiService->createDomain($guardian);
            $guardian->forceFill([
                'cpanel_domain_created_at' => now(),
            ])->save();

            return redirect()->back()->with('success', 'Domain berhasil dibuat di cPanel.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function deleteDomain(GuardianWeb $guardian)
    {
        try {
            $this->cpanelApiService->deleteDomain($guardian);
            $guardian->forceFill([
                'cpanel_domain_created_at' => null,
            ])->save();

            return redirect()->back()->with('success', 'Domain berhasil dihapus dari cPanel.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function files(Request $request, GuardianWeb $guardian)
    {
        try {
            $directory = $request->query('dir', $this->cpanelApiService->resolveDirectory($guardian));
            $response = $this->cpanelApiService->listFiles($guardian, $directory);
            $files = collect(data_get($response, 'data', []))
                ->sortBy([
                    fn (array $file) => ($file['type'] ?? 'file') !== 'dir',
                    fn (array $file) => strtolower($file['file'] ?? ''),
                ])
                ->values();

            $selectedFile = null;
            $fileContent = null;

            if ($request->filled('file')) {
                $selectedFile = $request->query('file');
                $fileResponse = $this->cpanelApiService->getFileContent($guardian, $selectedFile);
                $fileContent = data_get($fileResponse, 'data.content');
            }

            $breadcrumbs = $this->makeBreadcrumbs($guardian, $directory);

            return view('admin.guardian.files', compact('guardian', 'directory', 'files', 'breadcrumbs', 'selectedFile', 'fileContent'));
        } catch (\Throwable $e) {
            return redirect()->route('guardian.show', ['guardian' => $guardian->id])
                ->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function uploadFile(Request $request, GuardianWeb $guardian)
    {
        $validated = $request->validate([
            'dir' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'max:20480'],
        ]);

        try {
            $this->cpanelApiService->uploadFile($guardian, $validated['dir'], $request->file('file'));

            return redirect()->route('guardian.cpanel.files', [
                'guardian' => $guardian->id,
                'dir' => $validated['dir'],
            ])->with('success', 'File berhasil diupload.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function deleteFile(Request $request, GuardianWeb $guardian)
    {
        $validated = $request->validate([
            'dir' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->cpanelApiService->deletePath($guardian, $validated['path']);

            return redirect()->route('guardian.cpanel.files', [
                'guardian' => $guardian->id,
                'dir' => $validated['dir'],
            ])->with('success', 'File berhasil dipindahkan ke trash.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function createDirectory(Request $request, GuardianWeb $guardian)
    {
        $validated = $request->validate([
            'dir' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->cpanelApiService->createDirectory($guardian, $validated['dir'], $validated['name']);

            return redirect()->route('guardian.cpanel.files', [
                'guardian' => $guardian->id,
                'dir' => $validated['dir'],
            ])->with('success', 'Folder berhasil dibuat.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function createFile(Request $request, GuardianWeb $guardian)
    {
        $validated = $request->validate([
            'dir' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        try {
            $this->cpanelApiService->createFile($guardian, $validated['dir'], $validated['name'], $validated['content'] ?? '');

            return redirect()->route('guardian.cpanel.files', [
                'guardian' => $guardian->id,
                'dir' => $validated['dir'],
                'file' => trim($validated['dir'], '/') . '/' . trim($validated['name'], '/'),
            ])->with('success', 'File berhasil dibuat.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function renamePath(Request $request, GuardianWeb $guardian)
    {
        $validated = $request->validate([
            'dir' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->cpanelApiService->renamePath($guardian, $validated['path'], $validated['name']);

            return redirect()->route('guardian.cpanel.files', [
                'guardian' => $guardian->id,
                'dir' => $validated['dir'],
            ])->with('success', 'Nama file/folder berhasil diubah.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    public function saveFile(Request $request, GuardianWeb $guardian)
    {
        $validated = $request->validate([
            'dir' => ['required', 'string', 'max:255'],
            'path' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        try {
            $this->cpanelApiService->saveFileContent($guardian, $validated['path'], $validated['content'] ?? '');

            return redirect()->route('guardian.cpanel.files', [
                'guardian' => $guardian->id,
                'dir' => $validated['dir'],
                'file' => $validated['path'],
            ])->with('success', 'Isi file berhasil disimpan.');
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors(['cpanel' => $e->getMessage()]);
        }
    }

    private function makeBreadcrumbs(GuardianWeb $guardian, string $directory): array
    {
        $root = trim($this->cpanelApiService->resolveDirectory($guardian), '/');
        $current = trim(str_replace('\\', '/', $directory), '/');

        if ($current === '') {
            $current = $root;
        }

        if ($current !== $root && !str_starts_with($current, $root . '/')) {
            $current = $root;
        }

        $segments = explode('/', $current);
        $breadcrumbs = [];
        $path = '';

        foreach ($segments as $index => $segment) {
            $path = $index === 0 ? $segment : $path . '/' . $segment;
            $breadcrumbs[] = [
                'label' => $segment,
                'path' => $path,
            ];
        }

        return $breadcrumbs;
    }
}
