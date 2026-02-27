<x-app-layout head="Short Code" title="Admin - Short Code">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
            <!-- Top Actions -->
            <div x-data="{ showModal: false }" class="w-full flex flex-col sm:flex-row gap-2 justify-between items-center">
                <button @click="showModal = true"
                    class=" w-full text-sm sm:text-base sm:w-auto px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                    Input Short Code
                </button>
                <!-- Create Modal -->
                <div x-show="showModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center p-4 z-40">
                    <div
                        class="w-full max-w-[720px] max-h-full bg-white pb-6 rounded-md flex flex-col gap-4 relative overflow-hidden border-2 border-byolink-1">
                        <button @click="showModal = false"
                            class=" absolute top-6 right-6 w-6 h-6 text-white hover:text-red-500 duration-300">
                            <svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                                enable-background="new 0 0 512 512">
                                <path
                                    d="M437.5 386.6 306.9 256l130.6-130.6c14.1-14.1 14.1-36.8 0-50.9-14.1-14.1-36.8-14.1-50.9 0L256 205.1 125.4 74.5c-14.1-14.1-36.8-14.1-50.9 0-14.1 14.1-14.1 36.8 0 50.9L205.1 256 74.5 386.6c-14.1 14.1-14.1 36.8 0 50.9 14.1 14.1 36.8 14.1 50.9 0L256 306.9l130.6 130.6c14.1 14.1 36.8 14.1 50.9 0 14-14.1 14-36.9 0-50.9z"
                                    fill="currentColor" class="fill-000000"></path>
                            </svg>
                        </button>
                        <div class=" pt-6 pb-3 bg-byolink-1 text-white">
                            <h2 class=" px-6 text-2xl font-bold">Tambah Short Code</h2>
                        </div>
                        <div class=" max-h-full overflow-auto">
                            <form action="{{ route('source-code.store') }}" method="POST">
                                @csrf
                                <div class=" w-full overflow-auto max-h-full px-6 space-y-4">
                                    <div class=" space-y-4">
                                        <x-admin.component.textinput title="Title" placeholder="Input Title"
                                            :value="''" name="title" />
                                        <div class="flex flex-col gap-2">
                                            <label class="font-medium text-sm sm:text-base">Content (Separate using "," or "enter")</label>
                                            <div class=" w-full flex gap-1">
                                                <select class="js-example-basic-single" name="content[]"
                                                    multiple="multiple"></select>
                                                <label for="txt"
                                                    class="min-w-10 w-10 h-10 max-h-10 p-3 rounded-md bg-byolink-1 text-white font-semibold hover:bg-byolink-3 duration-300 relative overflow-hidden">
                                                    <svg id="icon-default" viewBox="0 0 24 24"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="m20.535 7.122-5.656-5.658a4.981 4.981 0 0 0-1.417-.977c-.035-.018-.066-.04-.1-.055A4.984 4.984 0 0 0 11.343 0H6a4 4 0 0 0-4 4v16a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4v-9.343a4.968 4.968 0 0 0-.433-2.016.85.85 0 0 0-.055-.1 4.976 4.976 0 0 0-.977-1.419ZM18.586 8H16a2 2 0 0 1-2-2V3.414ZM20 20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h5.343a3 3 0 0 1 .657.078V6a4 4 0 0 0 4 4h3.92a2.953 2.953 0 0 1 .08.657Z"
                                                            fill="currentColor" class="fill-232323"></path>
                                                        <path
                                                            d="M13 15h-2v-2a1 1 0 0 0-2 0v2H7a1 1 0 0 0 0 2h2v2a1 1 0 0 0 2 0v-2h2a1 1 0 0 0 0-2Z"
                                                            fill="currentColor" class="fill-232323"></path>
                                                    </svg>

                                                    <svg id="icon-loading" class="hidden animate-spin" fill="none"
                                                        viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M4 24c0 11.046 8.954 20 20 20s20-8.954 20-20S35.046 4 24 4"
                                                            stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="4" />
                                                    </svg>
                                                </label>
                                                <input type="file" id="txt" class=" hidden" accept=".txt">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-end space-x-4">
                                        <x-admin.component.submitbutton title="Tambah" />
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Search -->
                {{-- <div class=" w-full sm:w-auto flex flex-row font-semibold duration-300">
                        <input type="text" x-model="search" placeholder="Cari..."
                            class=" w-full text-sm sm:text-base sm:w-auto py-2 px-3 border border-byolink-1 rounded-md overflow-hidden focus-within:border-byolink-3 font-normal">
                    </div> --}}
            </div>

            <!-- Table -->
            <div class="w-full">
                <table class="w-full text-sm sm:text-base rounded-md overflow-hidden">
                    <thead>
                        <tr class="h-10 bg-byolink-1 text-white divide-x-2 divide-white">
                            <th class=" px-1 sm:px-2 py-1 w-10">No</th>
                            <th class=" px-1 sm:px-2 py-1">Title</th>
                            <th class=" px-1 sm:px-2 py-1 w-20">Option</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="h-10 text-neutral-600 bg-neutral-200 divide-x-2 divide-white">
                            <td class=" px-2 sm:px-4 py-2 text-center font-semibold">1</td>
                            <td class="px-2 sm:px-4 py-2 font-semibold">[pa_judul]</td>
                            <td></td>
                        </tr>
                        @foreach ($data as $item)
                            <tr
                                class="{{ $loop->even ? 'bg-neutral-200' : 'bg-neutral-100' }} h-10 text-neutral-600 divide-x-2 divide-white">
                                <td class=" px-2 sm:px-4 py-2 text-center font-semibold">{{ $loop->iteration + 1 }}</td>
                                <td class=" px-2 sm:px-4 py-2 font-semibold">{{ $item->title }}</td>
                                <td class=" px-1 sm:px-2">
                                    <div class="flex gap-2 justify-center">
                                        <button onclick="downloadText(`{{ str_replace('`', '\\`', $item->content) }}`, '{{$item->title}}')" class="w-4 sm:w-5 aspect-square hover:text-blue-500 duration-300">
                                            <svg class=" w-full h-full" viewBox="0 0 384 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z"/></svg>
                                        </button>

                                        <!-- Edit -->
                                        <a href="{{ route('source-code.show', ['source_code' => $item->id]) }}" aria-label="Edit short code {{ $item->title }}"
                                            class=" w-4 sm:w-5 aspect-square hover:text-green-500 duration-300">
                                            <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M3 17.75A3.25 3.25 0 0 0 6.25 21h4.915l.356-1.423c.162-.648.497-1.24.97-1.712l5.902-5.903a3.279 3.279 0 0 1 2.607-.95V6.25A3.25 3.25 0 0 0 17.75 3H11v4.75A3.25 3.25 0 0 1 7.75 11H3v6.75ZM9.5 3.44 3.44 9.5h4.31A1.75 1.75 0 0 0 9.5 7.75V3.44Zm9.6 9.23-5.903 5.902a2.686 2.686 0 0 0-.706 1.247l-.458 1.831a1.087 1.087 0 0 0 1.319 1.318l1.83-.457a2.685 2.685 0 0 0 1.248-.707l5.902-5.902A2.286 2.286 0 0 0 19.1 12.67Z"
                                                    fill="currentColor" class="fill-212121"></path>
                                            </svg>
                                        </a>

                                        <!-- Delete -->
                                        <div x-data="{ deletemodal: false }" class=" w-5 h-5">
                                            <button @click="deletemodal = !deletemodal"
                                                class=" w-4 sm:w-5 aspect-square hover:text-red-500 duration-300">
                                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z"
                                                        fill="currentColor" class="fill-000000"></path>
                                                </svg>
                                            </button>
                                            <x-admin.component.deletemodal :title="$item->title" :route="route('source-code.destroy', ['source_code' => $item->id])" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <style>
        .select2 {
            width: 100% !important;
            max-width: calc(100% - 48px) !important;
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

        .selection .select2-search__field {
            width: 100% !important;
            flex-grow: 1 !important;
            min-width: 60px !important;
            padding: 0.3rem 0 !important;
            /* samakan dengan padding selection */
            font-size: 0.875rem !important;
            line-height: 1.25rem !important;
            background-color: transparent !important;
            border: none !important;
            outline: none !important;
        }

        .select2-dropdown {
            font-size: 0.875rem !important;
            overflow: hidden;
            border-radius: 0 0 0.375rem 0.375rem !important;
            border: 2px solid #1e40af;
        }
    </style>
</x-app-layout>
<script>
    function downloadText(data, title) {
        // Ganti koma dengan line break
        const content = data.split(',').join('\n');

        // Buat Blob file
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);

        // Buat element <a> untuk download
        const a = document.createElement('a');
        a.href = url;
        a.download = title+'.txt';
        a.click();

        // Bersihkan URL Object
        URL.revokeObjectURL(url);
    }
    window.addEventListener('load', function select2() {
        var $j = jQuery.noConflict();

        $j(document).ready(function() {
            const select = $j('.js-example-basic-single');

            // Inisialisasi Select2
            select.select2({
                tags: true,
                tokenSeparators: [','],
            });

            // File input handler
            document.getElementById('txt').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (!file) return;

                // Tampilkan ikon loading
                document.getElementById('icon-default').classList.add('hidden');
                document.getElementById('icon-loading').classList.remove('hidden');

                const reader = new FileReader();
                reader.onload = function(e) {
                    const lines = e.target.result.split(/\r?\n/);
                    lines.forEach(line => {
                        const trimmed = line.trim();
                        if (trimmed.length > 0) {
                            if (select.find('option').filter((_, opt) => opt
                                    .value === trimmed).length === 0) {
                                const newOption = new Option(trimmed, trimmed, true,
                                    true);
                                select.append(newOption).trigger('change');
                            } else {
                                select.find('option[value="' + trimmed + '"]').prop(
                                    'selected', true);
                                select.trigger('change');
                            }
                        }
                    });

                    // Sembunyikan ikon loading, tampilkan ikon default kembali
                    document.getElementById('icon-loading').classList.add('hidden');
                    document.getElementById('icon-default').classList.remove('hidden');
                };

                reader.readAsText(file);
            });
        });
    });
</script>
