<x-app-layout head="Create Phone Number" title="Admin - Create Phone Number">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <form action="{{route('phone-number.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
                <x-admin.component.textinput title="No. Telephone" placeholder="Input no. telephone" :value="old('no_tlp')" name="no_tlp" />
                <div class=" w-full">
                    <div class="flex flex-col gap-2">
                        <label class="font-medium text-sm sm:text-base">Chose Category</label>
                        <select class="js-example-basic-single" name="category[]" multiple="multiple">
                            @foreach($category as $item)
                                <option value="{{ $item->id }}" {{ in_array($item->id, old('category', [])) ? 'selected' : '' }}>{{ $item->category }}</option>
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
