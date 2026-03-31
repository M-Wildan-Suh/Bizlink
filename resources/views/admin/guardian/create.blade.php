<x-app-layout head="Create Guardian Web" title="Admin - Create Guardian Web">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <form action="{{route('guardian.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div x-data="{ useCpanel: {{ (string) old('use_cpanel', '0') === '1' ? 'true' : 'false' }} }"
                class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
                <x-admin.component.linkinput title="Guardian Web Url" placeholder="Input link..." :value="old('url')" name="url" link="Url" />
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="flex items-center gap-3 text-sm sm:text-base font-medium">
                        <input type="hidden" name="use_cpanel" value="0">
                        <input type="checkbox" name="use_cpanel" value="1" x-model="useCpanel"
                            class="rounded border-byolink-1 text-byolink-1 focus:ring-byolink-3">
                        Hubungkan ke cPanel
                    </label>
                </div>

                <div x-show="useCpanel" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="w-full">
                        <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                            <label for="cpanel_account_id">Akun cPanel</label>
                            <select id="cpanel_account_id" name="cpanel_account_id"
                                class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                                <option value="">Pilih akun cPanel</option>
                                @foreach($cpanelAccounts as $cpanelAccount)
                                    <option value="{{ $cpanelAccount->id }}" {{ (string) old('cpanel_account_id') === (string) $cpanelAccount->id ? 'selected' : '' }}>
                                        {{ $cpanelAccount->name }} - {{ $cpanelAccount->host }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="w-full">
                        <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                            <label for="cpanel_domain_type">Jenis Domain cPanel</label>
                            <select id="cpanel_domain_type" name="cpanel_domain_type"
                                class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                                <option value="addon_domain" {{ old('cpanel_domain_type', 'addon_domain') === 'addon_domain' ? 'selected' : '' }}>Addon Domain</option>
                                <option value="subdomain" {{ old('cpanel_domain_type') === 'subdomain' ? 'selected' : '' }}>Subdomain</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class=" w-full space-y-6">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm sm:text-base">Chose Article</label>
                        <select class="js-example-basic-single" name="article[]" multiple="multiple">
                            @foreach($article as $item)
                                <option value="{{ $item->id }}" {{ in_array($item->id, old('article', [])) ? 'selected' : '' }}>{{ $item->judul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <script>
                        window.addEventListener('load', function select2() {
                            var $j = jQuery.noConflict();
                            $j(document).ready(function() {
                                $j('.js-example-basic-single').select2();
                            });
                        });
                    </script>
                    <style>
                        .select2 {
                            width: 100% !important;
                        }
                
                        .selection .select2-selection {
                            width: 100% !important;
                            border-color: #3b82f6 !important;
                            background-color: #f5f5f5 !important;
                            min-height: 40px !important;
                            padding: 0.3rem 0.75rem !important;
                            border-radius: 0.375rem !important;
                        }
                
                        .selection .select2-selection:focus,
                        .selection .select2-selection:focus-within {
                            border: 2px solid;
                            border-radius: 0.375rem 0.375rem 0 0 !important;
                            border-color: #1e40af !important;
                        }
                        .selection li {
                            margin-top: 0px !important;
                            margin-left: 0px !important;
                            margin-right: 0.25rem !important;
                            font-size: 0.875rem !important;
                            line-height: 1.25rem !important;
                        }
                        .selection textarea {
                            margin-top: 0px !important;
                            margin-left: 0px !important;
                            margin-bottom: 2px !important;
                            font-size: 0.875rem !important;
                            line-height: 1.25rem !important;
                        }
                        .select2-dropdown {
                            font-size: 0.875rem !important;
                            overflow: hidden;
                            border-radius: 0 0 0.375rem 0.375rem !important;
                            border: 2px solid #1e40af;
                        }
                    </style>
                </div>
                <x-admin.component.submitbutton title="Save" />
            </div>
        </form>
    </div>

    @include('components.admin.component.validationerror')
</x-app-layout>
