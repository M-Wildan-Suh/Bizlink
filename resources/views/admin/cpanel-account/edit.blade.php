<x-app-layout head="Edit cPanel Account" title="Admin - Edit cPanel Account">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <form action="{{ route('cpanel-account.update', ['cpanel_account' => $cpanelAccount->id]) }}" method="POST"
            x-data="{
                host: @js(old('host', $cpanelAccount->host)),
                port: @js((string) old('port', $cpanelAccount->port)),
                useSsl: @js((string) old('use_ssl', $cpanelAccount->use_ssl ? '1' : '0') === '1'),
                get loginUrl() {
                    const rawHost = (this.host || '').trim();

                    if (!rawHost) {
                        return '';
                    }

                    try {
                        const parsed = new URL(rawHost.includes('://') ? rawHost : `http://${rawHost}`);
                        const scheme = this.useSsl ? 'https' : 'http';

                        return `${scheme}://${parsed.hostname}:${this.port}`;
                    } catch (error) {
                        return '';
                    }
                }
            }">
            @csrf
            @method('PUT')
            <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
                <x-admin.component.textinput title="Account Name" placeholder="Mis. Hostinger Guardian 1" :value="old('name', $cpanelAccount->name)" name="name" />
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="host">Host</label>
                        <input type="text" id="host" name="host" placeholder="cpanel.domainanda.com"
                            x-model="host"
                            value="{{ old('host', $cpanelAccount->host) }}"
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                        <a :href="loginUrl || @js($cpanelAccount->login_url)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-sm text-byolink-1 hover:text-byolink-3 underline underline-offset-2 break-all">
                            Buka halaman login cPanel
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="w-full">
                        <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                            <label for="port">Port</label>
                            <input type="text" id="port" name="port" placeholder="2083"
                                x-model="port"
                                value="{{ old('port', $cpanelAccount->port) }}"
                                class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                        </div>
                    </div>
                    <x-admin.component.textinput title="Username" placeholder="username cPanel" :value="old('username', $cpanelAccount->username)" name="username" />
                </div>

                <x-admin.component.textinput title="Primary Domain" placeholder="contoh.com" :value="old('primary_domain', $cpanelAccount->primary_domain)" name="primary_domain" />

                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="api_token">API Token</label>
                        <textarea id="api_token" name="api_token" rows="5"
                            placeholder="Kosongkan jika token tidak ingin diganti"
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">{{ old('api_token') }}</textarea>
                        <p class="text-xs text-neutral-500">Token lama tidak ditampilkan. Isi field ini hanya jika ingin mengganti token.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex items-center gap-3 text-sm sm:text-base font-medium">
                        <input type="hidden" name="use_ssl" value="0">
                        <input type="checkbox" name="use_ssl" value="1" x-model="useSsl" {{ (string) old('use_ssl', $cpanelAccount->use_ssl ? '1' : '0') === '1' ? 'checked' : '' }}
                            class="rounded border-byolink-1 text-byolink-1 focus:ring-byolink-3">
                        Gunakan SSL
                    </label>

                    <label class="flex items-center gap-3 text-sm sm:text-base font-medium">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ (string) old('is_active', $cpanelAccount->is_active ? '1' : '0') === '1' ? 'checked' : '' }}
                            class="rounded border-byolink-1 text-byolink-1 focus:ring-byolink-3">
                        Status aktif
                    </label>
                </div>

                <x-admin.component.submitbutton title="Update" />
            </div>
        </form>
    </div>

    @include('components.admin.component.validationerror')
</x-app-layout>
