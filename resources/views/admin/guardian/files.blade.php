<x-app-layout head="Guardian File Manager" title="Admin - Guardian File Manager">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <div x-data="{ showUploadModal: false, showFolderModal: false, showFileModal: false, showEditorModal: {{ $selectedFile ? 'true' : 'false' }} }"
            class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
            <div class="flex flex-col gap-3">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-lg font-bold">{{ $guardian->url }}</p>
                        <p class="text-sm text-neutral-500">Directory aktif: {{ $directory }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button @click="showUploadModal = true"
                            class="px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                            Upload
                        </button>
                        <button @click="showFolderModal = true"
                            class="px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                            + Folder
                        </button>
                        <button @click="showFileModal = true"
                            class="px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                            + File
                        </button>
                        <a href="{{ route('guardian.cpanel.files', ['guardian' => $guardian->id, 'dir' => $directory]) }}"
                            class="px-4 py-2 bg-white text-byolink-1 rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:text-byolink-3 duration-300">
                            Refresh
                        </a>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-sm">
                    @foreach ($breadcrumbs as $crumb)
                        <a href="{{ route('guardian.cpanel.files', ['guardian' => $guardian->id, 'dir' => $crumb['path']]) }}"
                            class="px-2 py-1 rounded-md {{ $loop->last ? 'bg-byolink-1 text-white' : 'bg-neutral-100 hover:bg-neutral-200 text-neutral-700' }} duration-300">
                            {{ $crumb['label'] }}
                        </a>
                        @unless ($loop->last)
                            <span class="text-neutral-400">/</span>
                        @endunless
                    @endforeach
                </div>

                @if (session('success'))
                    <div class="rounded-md bg-green-100 text-green-700 px-4 py-2 text-sm font-medium">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <div x-show="showUploadModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center p-4 z-40">
                <div
                    class="w-full max-w-[720px] max-h-full bg-white pb-6 rounded-md flex flex-col gap-4 relative overflow-hidden border-2 border-byolink-1">
                    <button @click="showUploadModal = false"
                        class="absolute top-6 right-6 w-6 h-6 text-white hover:text-red-500 duration-300">
                        <svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            enable-background="new 0 0 512 512">
                            <path
                                d="M437.5 386.6 306.9 256l130.6-130.6c14.1-14.1 14.1-36.8 0-50.9-14.1-14.1-36.8-14.1-50.9 0L256 205.1 125.4 74.5c-14.1-14.1-36.8-14.1-50.9 0-14.1 14.1-14.1 36.8 0 50.9L205.1 256 74.5 386.6c-14.1 14.1-14.1 36.8 0 50.9 14.1 14.1 36.8 14.1 50.9 0L256 306.9l130.6 130.6c14.1 14.1 36.8 14.1 50.9 0 14-14.1 14-36.9 0-50.9z"
                                fill="currentColor"></path>
                        </svg>
                    </button>
                    <div class="pt-6 pb-3 bg-byolink-1 text-white">
                        <h2 class="px-6 text-2xl font-bold">Upload File</h2>
                    </div>
                    <div class="px-6">
                        <form action="{{ route('guardian.cpanel.files.upload', ['guardian' => $guardian->id]) }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <input type="hidden" name="dir" value="{{ $directory }}">
                            <input type="file" id="file" name="file"
                                class="w-full text-sm font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100 file:mr-3 file:border-0 file:bg-byolink-1 file:px-4 file:py-2 file:text-white">
                            <button type="submit"
                                class="px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                                Upload
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="showFolderModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center p-4 z-40">
                <div
                    class="w-full max-w-[720px] max-h-full bg-white pb-6 rounded-md flex flex-col gap-4 relative overflow-hidden border-2 border-byolink-1">
                    <button @click="showFolderModal = false"
                        class="absolute top-6 right-6 w-6 h-6 text-white hover:text-red-500 duration-300">
                        <svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            enable-background="new 0 0 512 512">
                            <path
                                d="M437.5 386.6 306.9 256l130.6-130.6c14.1-14.1 14.1-36.8 0-50.9-14.1-14.1-36.8-14.1-50.9 0L256 205.1 125.4 74.5c-14.1-14.1-36.8-14.1-50.9 0-14.1 14.1-14.1 36.8 0 50.9L205.1 256 74.5 386.6c-14.1 14.1-14.1 36.8 0 50.9 14.1 14.1 36.8 14.1 50.9 0L256 306.9l130.6 130.6c14.1 14.1 36.8 14.1 50.9 0 14-14.1 14-36.9 0-50.9z"
                                fill="currentColor"></path>
                        </svg>
                    </button>
                    <div class="pt-6 pb-3 bg-byolink-1 text-white">
                        <h2 class="px-6 text-2xl font-bold">Tambah Folder</h2>
                    </div>
                    <div class="px-6">
                        <form action="{{ route('guardian.cpanel.files.folder', ['guardian' => $guardian->id]) }}"
                            method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="dir" value="{{ $directory }}">
                            <input type="text" name="name" placeholder="nama-folder"
                                class="w-full text-sm font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                            <button type="submit"
                                class="px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                                Buat Folder
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="showFileModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center p-4 z-40">
                <div
                    class="w-full max-w-[720px] max-h-full bg-white pb-6 rounded-md flex flex-col gap-4 relative overflow-hidden border-2 border-byolink-1">
                    <button @click="showFileModal = false"
                        class="absolute top-6 right-6 w-6 h-6 text-white hover:text-red-500 duration-300">
                        <svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            enable-background="new 0 0 512 512">
                            <path
                                d="M437.5 386.6 306.9 256l130.6-130.6c14.1-14.1 14.1-36.8 0-50.9-14.1-14.1-36.8-14.1-50.9 0L256 205.1 125.4 74.5c-14.1-14.1-36.8-14.1-50.9 0-14.1 14.1-14.1 36.8 0 50.9L205.1 256 74.5 386.6c-14.1 14.1-14.1 36.8 0 50.9 14.1 14.1 36.8 14.1 50.9 0L256 306.9l130.6 130.6c14.1 14.1 36.8 14.1 50.9 0 14-14.1 14-36.9 0-50.9z"
                                fill="currentColor"></path>
                        </svg>
                    </button>
                    <div class="pt-6 pb-3 bg-byolink-1 text-white">
                        <h2 class="px-6 text-2xl font-bold">Tambah File</h2>
                    </div>
                    <div class="px-6">
                        <form action="{{ route('guardian.cpanel.files.file', ['guardian' => $guardian->id]) }}"
                            method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="dir" value="{{ $directory }}">
                            <input type="text" name="name" placeholder="index.php"
                                class="w-full text-sm font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                            <button type="submit"
                                class="px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                                Buat File
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div x-show="showEditorModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center p-4 z-40">
                <div
                    class="w-full max-w-[960px] max-h-full bg-white pb-6 rounded-md flex flex-col gap-4 relative overflow-hidden border-2 border-byolink-1">
                    <button
                        @click="window.location.href='{{ route('guardian.cpanel.files', ['guardian' => $guardian->id, 'dir' => $directory]) }}'"
                        class="absolute top-6 right-6 w-6 h-6 text-white hover:text-red-500 duration-300">
                        <svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            enable-background="new 0 0 512 512">
                            <path
                                d="M437.5 386.6 306.9 256l130.6-130.6c14.1-14.1 14.1-36.8 0-50.9-14.1-14.1-36.8-14.1-50.9 0L256 205.1 125.4 74.5c-14.1-14.1-36.8-14.1-50.9 0-14.1 14.1-14.1 36.8 0 50.9L205.1 256 74.5 386.6c-14.1 14.1-14.1 36.8 0 50.9 14.1 14.1 36.8 14.1 50.9 0L256 306.9l130.6 130.6c14.1 14.1 36.8 14.1 50.9 0 14-14.1 14-36.9 0-50.9z"
                                fill="currentColor"></path>
                        </svg>
                    </button>
                    <div class="pt-6 pb-3 bg-byolink-1 text-white">
                        <h2 class="px-6 text-2xl font-bold">Editor File</h2>
                    </div>
                    <div class="px-6">
                        @if ($selectedFile)
                            <form action="{{ route('guardian.cpanel.files.save', ['guardian' => $guardian->id]) }}"
                                method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="dir" value="{{ $directory }}">
                                <input type="hidden" name="path" value="{{ $selectedFile }}">
                                <p class="text-sm text-neutral-500 break-all">{{ $selectedFile }}</p>
                                <textarea name="content" rows="22"
                                    class="w-full text-sm font-mono rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">{{ old('content', $fileContent) }}</textarea>
                                <button type="submit"
                                    class="px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                                    Simpan Perubahan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto rounded-md border border-neutral-200">
                <table class="w-full text-sm sm:text-base overflow-hidden">
                    <thead>
                        <tr class="h-10 bg-byolink-1 text-white divide-x-2 divide-white">
                            <th class="px-3 py-2 text-left">Name</th>
                            <th class="px-3 py-2 hidden md:table-cell text-left">Type</th>
                            <th class="px-3 py-2 hidden md:table-cell text-left">Size</th>
                            <th class="px-3 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($files as $file)
                            @php
                                $fileName = $file['file'] ?? ($file['filename'] ?? ($file['name'] ?? '-'));
                                $isDir = ($file['type'] ?? null) === 'dir' || ($file['isdir'] ?? 0) == 1;
                                $fullPath = trim($directory, '/') . '/' . ltrim($fileName, '/');
                            @endphp
                            <tr
                                class="{{ $loop->even ? 'bg-neutral-50' : 'bg-white' }} text-neutral-700 border-t border-neutral-200 align-top">
                                <td class="px-3 py-3">
                                    <div x-data="{ editing: false, name: @js($fileName) }" class="flex flex-col gap-1">
                                        <div x-show="!editing"
                                            @dblclick="editing = true; $nextTick(() => $refs.renameInput.focus())"
                                            class="font-semibold cursor-text select-none">
                                            {{ $fileName }}{{ $isDir ? '/' : '' }}
                                        </div>
                                        <form x-show="editing"
                                            action="{{ route('guardian.cpanel.files.rename', ['guardian' => $guardian->id]) }}"
                                            method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="dir" value="{{ $directory }}">
                                            <input type="hidden" name="path" value="{{ $fullPath }}">
                                            <input x-ref="renameInput" x-model="name" type="text" name="name"
                                                @keydown.enter.prevent="$el.form.submit()"
                                                @keydown.escape.prevent="editing = false; name = @js($fileName)"
                                                @blur="editing = false; name = @js($fileName)"
                                                class="w-full text-sm font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                                        </form>
                                        <span class="text-[11px] text-neutral-400">Double click untuk rename</span>
                                        <span class="text-xs text-neutral-400 break-all">{{ $fullPath }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 hidden md:table-cell">
                                    {{ $isDir ? 'Directory' : $file['mimetype'] ?? ($file['mime_type'] ?? 'File') }}
                                </td>
                                <td class="px-3 py-3 hidden md:table-cell">
                                    {{ $file['humansize'] ?? ($file['size'] ?? '-') }}</td>
                                <td class="px-3 py-3">
                                    <div class="flex flex-wrap gap-3">
                                        @if ($isDir)
                                            <a href="{{ route('guardian.cpanel.files', ['guardian' => $guardian->id, 'dir' => $fullPath]) }}"
                                                class="w-5 h-5 hover:text-byolink-1 duration-300"
                                                aria-label="Buka folder {{ $fileName }}">
                                                <svg class=" w-full h-full" viewBox='0 0 24 24'
                                                    xmlns='http://www.w3.org/2000/svg'
                                                    xmlns:xlink='http://www.w3.org/1999/xlink'>
                                                    <rect width='24' height='24' stroke='none'
                                                        fill='#000000' opacity='0' />
                                                    <g transform="matrix(0.71 0 0 0.71 12 12)">
                                                        <path
                                                            style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-dashoffset: 0; stroke-linejoin: miter; stroke-miterlimit: 4; fill: rgb(0,0,0); fill-rule: nonzero; opacity: 1;"
                                                            transform=" translate(-15, -15)"
                                                            d="M 5 4 C 3.895 4 3 4.895 3 6 L 3 9 L 3 11 L 22 11 L 27 11 L 27 8 C 27 6.895 26.105 6 25 6 L 12.199219 6 L 11.582031 4.9707031 C 11.221031 4.3687031 10.570187 4 9.8671875 4 L 5 4 z M 2.5019531 13 C 1.4929531 13 0.77040625 13.977406 1.0664062 14.941406 L 4.0351562 24.587891 C 4.2941563 25.426891 5.0692656 26 5.9472656 26 L 15 26 L 24.052734 26 C 24.930734 26 25.705844 25.426891 25.964844 24.587891 L 28.933594 14.941406 C 29.229594 13.977406 28.507047 13 27.498047 13 L 15 13 L 2.5019531 13 z"
                                                            stroke-linecap="round" />
                                                    </g>
                                                </svg>
                                            </a>
                                        @else
                                            <a href="{{ route('guardian.cpanel.files', ['guardian' => $guardian->id, 'dir' => $directory, 'file' => $fullPath]) }}"
                                                class="w-5 h-5 hover:text-green-500 duration-300"
                                                aria-label="Edit file {{ $fileName }}">
                                                <svg fill="none" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M3 17.75A3.25 3.25 0 0 0 6.25 21h4.915l.356-1.423c.162-.648.497-1.24.97-1.712l5.902-5.903a3.279 3.279 0 0 1 2.607-.95V6.25A3.25 3.25 0 0 0 17.75 3H11v4.75A3.25 3.25 0 0 1 7.75 11H3v6.75ZM9.5 3.44 3.44 9.5h4.31A1.75 1.75 0 0 0 9.5 7.75V3.44Zm9.6 9.23-5.903 5.902a2.686 2.686 0 0 0-.706 1.247l-.458 1.831a1.087 1.087 0 0 0 1.319 1.318l1.83-.457a2.685 2.685 0 0 0 1.248-.707l5.902-5.902A2.286 2.286 0 0 0 19.1 12.67Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </a>
                                        @endif
                                        <form
                                            action="{{ route('guardian.cpanel.files.delete', ['guardian' => $guardian->id]) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="dir" value="{{ $directory }}">
                                            <input type="hidden" name="path" value="{{ $fullPath }}">
                                            @method('DELETE')
                                            <button type="submit" class="w-5 h-5 hover:text-red-500 duration-300"
                                                aria-label="Hapus {{ $fileName }}">
                                                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z"
                                                        fill="currentColor"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="bg-neutral-100">
                                <td colspan="4" class="px-3 py-4 text-center text-neutral-500">Tidak ada file pada
                                    folder ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('components.admin.component.validationerror')
</x-app-layout>
