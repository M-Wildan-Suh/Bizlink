<?php

namespace App\Services;

use App\Models\CpanelAccount;
use App\Models\GuardianWeb;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CpanelApiService
{
    public function createDomain(GuardianWeb $guardian): array
    {
        $account = $this->getGuardianAccount($guardian);
        $directory = $this->resolveDirectory($guardian);
        $domainType = $guardian->cpanel_domain_type ?: 'addon_domain';

        if ($domainType === 'subdomain') {
            $rootDomain = $account->primary_domain;

            if (blank($rootDomain)) {
                throw new RuntimeException('Primary domain pada akun cPanel wajib diisi untuk membuat subdomain.');
            }

            if (!$this->isSubdomainOf($guardian->url, $rootDomain)) {
                throw new RuntimeException('URL guardian tidak cocok dengan primary domain akun cPanel untuk mode subdomain.');
            }

            $subdomain = $this->extractSubdomainLabel($guardian->url, $rootDomain);

            return $this->uapi($account, 'SubDomain', 'addsubdomain', [
                'domain' => $subdomain,
                'rootdomain' => $rootDomain,
                'dir' => '/' . ltrim($directory, '/'),
                'disallowdot' => 0,
                'canoff' => 1,
            ]);
        }

        $rootDomain = $account->primary_domain;

        if (blank($rootDomain)) {
            throw new RuntimeException('Primary domain pada akun cPanel wajib diisi untuk membuat addon domain.');
        }

        $subdomainAlias = $this->makeAddonCreateSubdomain($guardian->url);

        return $this->api2($account, 'AddonDomain', 'addaddondomain', [
            'newdomain' => $guardian->url,
            'subdomain' => $subdomainAlias,
            'dir' => $directory,
            'ftp_is_optional' => 1,
        ]);
    }

    public function deleteDomain(GuardianWeb $guardian): array
    {
        $account = $this->getGuardianAccount($guardian);
        $domainType = $guardian->cpanel_domain_type ?: 'addon_domain';

        if ($domainType === 'subdomain') {
            return $this->api2($account, 'SubDomain', 'delsubdomain', [
                'domain' => $guardian->url,
            ]);
        }

        $subdomains = $this->makeAddonDeleteSubdomains($guardian->url, $account->primary_domain);
        $lastException = null;

        foreach ($subdomains as $subdomain) {
            try {
                return $this->api2($account, 'AddonDomain', 'deladdondomain', [
                    'domain' => $guardian->url,
                    'subdomain' => $subdomain,
                ]);
            } catch (RuntimeException $e) {
                $lastException = $e;
            }
        }

        throw $lastException ?: new RuntimeException('Gagal menghapus addon domain.');
    }

    public function listFiles(GuardianWeb $guardian, string $directory): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);
        $directory = $this->normalizeManagedPath($guardian, $directory);

        return $this->uapi($account, 'Fileman', 'list_files', [
            'dir' => $directory,
            'check_for_leaf_directories' => 1,
            'include_mime' => 1,
            'show_hidden' => 1,
            'types' => 'file|dir',
        ]);
    }

    public function uploadFile(GuardianWeb $guardian, string $directory, UploadedFile $file): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);
        $directory = $this->normalizeManagedPath($guardian, $directory);
        $response = $this->http($account)
            ->asMultipart()
            ->attach('file-1', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName())
            ->post($this->executeUrl($account, 'Fileman/upload_files'), [
                'dir' => $directory,
            ]);

        return $this->decodeResponse($response);
    }

    public function createDirectory(GuardianWeb $guardian, string $directory, string $name): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);

        return $this->api2($account, 'Fileman', 'mkdir', [
            'path' => $this->normalizeManagedPath($guardian, $directory),
            'name' => $this->sanitizeNewItemName($name),
        ]);
    }

    public function createFile(GuardianWeb $guardian, string $directory, string $name, string $content = ''): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);

        return $this->uapi($account, 'Fileman', 'save_file_content', [
            'dir' => $this->normalizeManagedPath($guardian, $directory),
            'file' => $this->sanitizeNewItemName($name),
            'content' => $content,
            'from_charset' => 'utf-8',
            'to_charset' => 'utf-8',
        ]);
    }

    public function getFileContent(GuardianWeb $guardian, string $path): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);
        [$directory, $file] = $this->splitManagedFilePath($guardian, $path);

        return $this->uapi($account, 'Fileman', 'get_file_content', [
            'dir' => $directory,
            'file' => $file,
        ]);
    }

    public function saveFileContent(GuardianWeb $guardian, string $path, string $content): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);
        [$directory, $file] = $this->splitManagedFilePath($guardian, $path);

        return $this->uapi($account, 'Fileman', 'save_file_content', [
            'dir' => $directory,
            'file' => $file,
            'content' => $content,
            'from_charset' => 'utf-8',
            'to_charset' => 'utf-8',
        ]);
    }

    public function renamePath(GuardianWeb $guardian, string $sourcePath, string $newName): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);
        $sourcePath = $this->normalizeManagedPath($guardian, $sourcePath);

        return $this->api2($account, 'Fileman', 'fileop', [
            'op' => 'rename',
            'sourcefiles' => $sourcePath,
            'destfiles' => $this->sanitizeNewItemName($newName),
            'doubledecode' => 1,
        ]);
    }

    public function deletePath(GuardianWeb $guardian, string $sourceFile): array
    {
        $this->ensureFileManagerAccessible($guardian);
        $account = $this->getGuardianAccount($guardian);

        return $this->api2($account, 'Fileman', 'fileop', [
            'op' => 'trash',
            'sourcefiles' => $this->normalizeManagedPath($guardian, $sourceFile),
            'doubledecode' => 1,
        ]);
    }

    public function ensureFileManagerAccessible(GuardianWeb $guardian): void
    {
        if (!$guardian->cpanel_domain_created_at) {
            throw new RuntimeException('File Manager baru bisa diakses setelah domain berhasil dibuat di cPanel.');
        }
    }

    public function resolveDirectory(GuardianWeb $guardian): string
    {
        $directory = $guardian->url;

        if (blank($directory)) {
            throw new RuntimeException('Directory guardian belum diatur.');
        }

        return trim($directory);
    }

    private function getGuardianAccount(GuardianWeb $guardian): CpanelAccount
    {
        $guardian->loadMissing('cpanelAccount');

        if (!$guardian->use_cpanel || !$guardian->cpanelAccount) {
            throw new RuntimeException('Guardian ini tidak terhubung ke akun cPanel.');
        }

        return $guardian->cpanelAccount;
    }

    private function http(CpanelAccount $account)
    {
        return Http::withHeaders([
            'Authorization' => sprintf('cpanel %s:%s', $account->username, $account->api_token),
            'Accept' => 'application/json',
        ])->withoutVerifying();
    }

    private function uapi(CpanelAccount $account, string $module, string $function, array $query = []): array
    {
        $response = $this->http($account)->get($this->executeUrl($account, $module . '/' . $function), $query);

        return $this->decodeResponse($response);
    }

    private function api2(CpanelAccount $account, string $module, string $function, array $query = []): array
    {
        $payload = array_merge([
            'cpanel_jsonapi_user' => $account->username,
            'cpanel_jsonapi_apiversion' => 2,
            'cpanel_jsonapi_module' => $module,
            'cpanel_jsonapi_func' => $function,
        ], $query);

        $response = $this->http($account)->get($this->jsonApiUrl($account), $payload);

        return $this->decodeResponse($response);
    }

    private function executeUrl(CpanelAccount $account, string $path): string
    {
        return sprintf('%s/execute/%s', $this->baseUrl($account), ltrim($path, '/'));
    }

    private function jsonApiUrl(CpanelAccount $account): string
    {
        return sprintf('%s/json-api/cpanel', $this->baseUrl($account));
    }

    private function baseUrl(CpanelAccount $account): string
    {
        $scheme = $account->use_ssl ? 'https' : 'http';

        return sprintf('%s://%s:%s', $scheme, $account->host, $account->port);
    }

    private function decodeResponse(Response $response): array
    {
        if (!$response->successful()) {
            throw new RuntimeException('cPanel request gagal dengan status HTTP ' . $response->status() . '.');
        }

        $json = $response->json();

        if (!is_array($json)) {
            throw new RuntimeException('Response cPanel tidak valid.');
        }

        $status = data_get($json, 'status', data_get($json, 'result.status'));
        $errors = data_get($json, 'errors', data_get($json, 'result.errors', data_get($json, 'cpanelresult.error')));

        if (isset($json['cpanelresult']) && empty($errors)) {
            $errors = collect(data_get($json, 'cpanelresult.data', []))
                ->pluck('err')
                ->filter()
                ->values()
                ->all();
        }

        if ($status === 0 || (!empty($errors) && $errors !== '')) {
            $message = is_array($errors) ? implode(' ', $errors) : (string) $errors;
            throw new RuntimeException(trim($message) ?: 'cPanel mengembalikan error yang tidak diketahui.');
        }

        return $json;
    }

    private function normalizeManagedPath(GuardianWeb $guardian, ?string $path = null): string
    {
        $root = trim(str_replace('\\', '/', $this->resolveDirectory($guardian)), '/');
        $path = trim(str_replace('\\', '/', (string) $path), '/');

        if ($path === '') {
            return $root;
        }

        $segments = array_values(array_filter(explode('/', $path), fn ($segment) => $segment !== ''));

        if (in_array('..', $segments, true)) {
            throw new RuntimeException('Path tidak valid.');
        }

        $normalized = implode('/', $segments);

        if ($normalized !== $root && !str_starts_with($normalized, $root . '/')) {
            $normalized = $root . '/' . ltrim($normalized, '/');
        }

        if ($normalized !== $root && !str_starts_with($normalized, $root . '/')) {
            throw new RuntimeException('Path berada di luar direktori guardian.');
        }

        return $normalized;
    }

    private function splitManagedFilePath(GuardianWeb $guardian, string $path): array
    {
        $normalizedPath = $this->normalizeManagedPath($guardian, $path);
        $directory = trim(dirname($normalizedPath), '/');
        $file = basename($normalizedPath);

        if ($file === '' || $file === '.' || $file === '..') {
            throw new RuntimeException('Nama file tidak valid.');
        }

        return [$directory, $file];
    }

    private function sanitizeNewItemName(string $name): string
    {
        $name = trim(str_replace('\\', '/', $name));

        if ($name === '' || str_contains($name, '/')) {
            throw new RuntimeException('Nama file/folder tidak valid.');
        }

        if (in_array($name, ['.', '..'], true)) {
            throw new RuntimeException('Nama file/folder tidak valid.');
        }

        return $name;
    }

    private function isSubdomainOf(string $domain, string $rootDomain): bool
    {
        return str_ends_with($domain, '.' . $rootDomain);
    }

    private function extractSubdomainLabel(string $domain, string $rootDomain): string
    {
        return (string) str($domain)->beforeLast('.' . $rootDomain);
    }

    private function makeAddonCreateSubdomain(string $domain): string
    {
        return $this->makeAddonLabel($domain);
    }

    private function makeAddonDeleteSubdomains(string $domain, ?string $rootDomain): array
    {
        if (blank($rootDomain)) {
            throw new RuntimeException('Primary domain pada akun cPanel wajib diisi untuk menghapus addon domain.');
        }

        $labels = [
            $this->makeAddonLabel($domain),
            $this->makeLegacyAddonLabel($domain),
        ];

        return collect($labels)
            ->filter()
            ->unique()
            ->map(fn (string $label) => $label . '_' . $rootDomain)
            ->values()
            ->all();
    }

    private function makeAddonLabel(string $domain): string
    {
        $host = parse_url('http://' . ltrim($domain, '/'), PHP_URL_HOST) ?: $domain;
        $segments = array_values(array_filter(explode('.', strtolower((string) $host))));

        if (count($segments) > 1) {
            array_pop($segments);
        }

        $labelSource = implode('-', $segments);

        if ($labelSource === '') {
            $labelSource = strtolower((string) $host);
        }

        $label = preg_replace('/[^a-z0-9]+/', '-', $labelSource);
        $label = preg_replace('/-+/', '-', (string) $label);
        $label = trim((string) $label, '-');

        return substr((string) $label, 0, 50);
    }

    private function makeLegacyAddonLabel(string $domain): string
    {
        $label = preg_replace('/[^a-z0-9]+/', '-', strtolower($domain));
        $label = str_replace('.', '-', (string) $label);
        $label = preg_replace('/-+/', '-', (string) $label);
        $label = trim((string) $label, '-');

        return substr((string) $label, 0, 50);
    }
}
