@foreach ($data as $item)
    <tr class="{{ $data->currentPage() == 1 ? ($loop->even ? 'bg-neutral-100' : 'bg-neutral-200') : ($loop->even ? 'bg-neutral-200' : 'bg-neutral-100')  }} h-10 text-neutral-600 divide-x-2 divide-white">
        <td class="px-3 py-1 text-center font-semibold">{{ $loop->iteration + ($data->currentPage() == 1 ? 0 : 1) + ($data->currentPage() - 1) * 20 }}</td>
        <td class="px-2 sm:px-4 py-1 min-h-10 font-semibold break-all">
            <a href="https://{{ $item->url }}" class=" hover:text-byolink-1 duration-300 line-clamp-1"
                target="__blank">{{ $item->url }}</a>
        </td>
        <td class="px-2 sm:px-4 py-1 min-h-10 text-nowrap text-center hidden sm:table-cell">{{ $item->template ?? '' }}</td>
        <td class="px-2 sm:px-4 py-1 min-h-10 text-nowrap text-center hidden sm:table-cell">{{ $item->spintaxcount }}
            ({{ $item->spincount }})</td>
        <td class="px-2 sm:px-4 py-1 min-h-10 text-nowrap text-center hidden sm:table-cell">{{ $item->uniquecount }}</td>
        <td class="px-1 sm:px-2">
            <div class="flex gap-1 sm:gap-2 items-center justify-center">
                <button @click="detail({{ $item->id }})"
                    class="w-5 h-5 hover:text-green-500 duration-300 block md:hidden">
                    <svg  id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" class=" w-full h-full"  xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path fill="currentColor" d="M98.9,184.7l1.8,2.1l136,156.5c4.6,5.3,11.5,8.6,19.2,8.6c7.7,0,14.6-3.4,19.2-8.6L411,187.1l2.3-2.6  c1.7-2.5,2.7-5.5,2.7-8.7c0-8.7-7.4-15.8-16.6-15.8v0H112.6v0c-9.2,0-16.6,7.1-16.6,15.8C96,179.1,97.1,182.2,98.9,184.7z"/></svg>
                </button>
                @if ($item->url != 'bizlink.sites.id')
                    <div x-data="{ copied: false, original: '{{ $item->code }}' }" class=" w-5 h-5">
                        <button
                            @click="navigator.clipboard.writeText(original).then(() => { 
                                copied = true; 
                                setTimeout(() => copied = false, 1000); 
                            })"
                            class="w-5 aspect-square duration-300 hover:text-blue-500 relative"
                            :class="copied ? ' text-green-500' : ''"
                            :aria-label="copied ? 'Sudah Disalin' : 'salin kode'">
                            <svg fill="none" class=" w-full h-full" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M5.5028 4.62704L5.5 6.75V17.2542C5.5 19.0491 6.95507 20.5042 8.75 20.5042L17.3663 20.5045C17.0573 21.3782 16.224 22.0042 15.2444 22.0042H8.75C6.12665 22.0042 4 19.8776 4 17.2542V6.75C4 5.76929 4.62745 4.93512 5.5028 4.62704ZM17.75 2C18.9926 2 20 3.00736 20 4.25V17.25C20 18.4926 18.9926 19.5 17.75 19.5H8.75C7.50736 19.5 6.5 18.4926 6.5 17.25V4.25C6.5 3.00736 7.50736 2 8.75 2H17.75Z"
                                    fill="currentColor" />
                            </svg>
                            <div x-show="copied"
                                class=" absolute left-1/2 bottom-full bg-black text-white flex text-xs px-1 py-1 rounded-md">
                                Copied
                            </div>
                        </button>
                    </div>

                    <a href="https://{{ $item->url }}/sitemap" aria-label="Lihat sitemap {{ $item->url }}" target="_blank"
                        class="w-5 h-5 hover:text-blue-500 duration-300">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title/><g data-name="Layer 2" id="Layer_2"><path fill="currentColor" d="M19,16a3,3,0,0,0-1.48.41L13,12.54V7.82a3,3,0,1,0-2,0v4.72L6.48,16.41A3,3,0,0,0,5,16a3,3,0,1,0,3,3,3,3,0,0,0-.21-1.08L12,14.32l4.21,3.6A3,3,0,0,0,16,19a3,3,0,1,0,3-3Z"/></g></svg>
                    </a>

                    <a href="{{ route('guardian.show', ['guardian' => $item->id]) }}" aria-label="Edit guardian {{ $item->url }}"
                        class="w-5 h-5 hover:text-green-500 duration-300">
                        <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M3 17.75A3.25 3.25 0 0 0 6.25 21h4.915l.356-1.423c.162-.648.497-1.24.97-1.712l5.902-5.903a3.279 3.279 0 0 1 2.607-.95V6.25A3.25 3.25 0 0 0 17.75 3H11v4.75A3.25 3.25 0 0 1 7.75 11H3v6.75ZM9.5 3.44 3.44 9.5h4.31A1.75 1.75 0 0 0 9.5 7.75V3.44Zm9.6 9.23-5.903 5.902a2.686 2.686 0 0 0-.706 1.247l-.458 1.831a1.087 1.087 0 0 0 1.319 1.318l1.83-.457a2.685 2.685 0 0 0 1.248-.707l5.902-5.902A2.286 2.286 0 0 0 19.1 12.67Z"
                                fill="currentColor"></path>
                        </svg>
                    </a>

                    {{-- Delete --}}
                    <div x-data="{ deletemodal: false }" class=" w-5 h-5">
                        <button @click="deletemodal = !deletemodal"
                            class=" w-4 sm:w-5 aspect-square hover:text-red-500 duration-300">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z"
                                    fill="currentColor" class="fill-000000"></path>
                            </svg>
                        </button>
                        <x-admin.component.deletemodal :title="$item->url" :route="route('guardian.destroy', ['guardian' => $item->id])" />
                    </div>
                @endif
            </div>
        </td>
    </tr>
    <tr x-show="openedIds.includes({{ $item->id }})" class="{{ $loop->even ? 'bg-neutral-100' : 'bg-neutral-200' }} md:hidden border-t-2 border-b-2 border-white h-10 text-neutral-600 divide-x-2 divide-white">
        <td colspan="6" class=" pl-12 p-2">
            <div class=" w-full flex flex-col gap-2 text-sm">
                <p>Template : {{ $item->template ?? '' }}</p>
                <p>Spintax : {{ $item->spintaxcount }} ({{ $item->spincount }})</p>
                <p>Unique : {{ $item->uniquecount }}</p>
            </div>
        </td>
    </tr>
@endforeach
