<x-app-layout head="Create cPanel Account" title="Admin - Create cPanel Account">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <form action="{{ route('cpanel-account.store') }}" method="POST"
            x-data="{
                host: @js(old('host', '')),
                port: @js((string) old('port', '2083')),
                useSsl: @js((string) old('use_ssl', '1') === '1'),
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
            <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
                <x-admin.component.textinput title="Account Name" placeholder="Mis. Hostinger Guardian 1" :value="old('name')" name="name" />
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="host">Host</label>
                        <input type="text" id="host" name="host" placeholder="cpanel.domainanda.com"
                            x-model="host"
                            value="{{ old('host') }}"
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                        <template x-if="loginUrl">
                            <a :href="loginUrl"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-sm text-byolink-1 hover:text-byolink-3 underline underline-offset-2 break-all">
                                Buka halaman login cPanel
                            </a>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="w-full">
                        <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                            <label for="port">Port</label>
                            <input type="text" id="port" name="port" placeholder="2083"
                                x-model="port"
                                value="{{ old('port', '2083') }}"
                                class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                        </div>
                    </div>
                    <x-admin.component.textinput title="Username" placeholder="username cPanel" :value="old('username')" name="username" />
                </div>

                <x-admin.component.textinput title="Primary Domain" placeholder="contoh.com" :value="old('primary_domain')" name="primary_domain" />

                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="api_token">API Token</label>
                        <textarea id="api_token" name="api_token" rows="5" placeholder="Masukkan API token cPanel"
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">{{ old('api_token') }}</textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex items-center gap-3 text-sm sm:text-base font-medium">
                        <input type="hidden" name="use_ssl" value="0">
                        <input type="checkbox" name="use_ssl" value="1" x-model="useSsl" {{ (string) old('use_ssl', '1') === '1' ? 'checked' : '' }}
                            class="rounded border-byolink-1 text-byolink-1 focus:ring-byolink-3">
                        Gunakan SSL
                    </label>

                    <label class="flex items-center gap-3 text-sm sm:text-base font-medium">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ (string) old('is_active', '1') === '1' ? 'checked' : '' }}
                            class="rounded border-byolink-1 text-byolink-1 focus:ring-byolink-3">
                        Status aktif
                    </label>
                </div>

                <x-admin.component.submitbutton title="Save" />
            </div>
        </form>
    </div>

    @include('components.admin.component.validationerror')
</x-app-layout>
