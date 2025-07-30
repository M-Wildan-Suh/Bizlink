@foreach ($data as $item)
    <tr class="{{ $loop->even ? 'bg-neutral-100' : 'bg-neutral-200' }} h-10 text-neutral-600 divide-x-2 divide-white">
        <td class="px-3 py-1 text-center font-semibold">{{ $loop->iteration + (($data->currentPage() - 1) * 20) }}</td>
        <td class="px-2 sm:px-4 py-1 min-h-10 font-semibold max-w-full break-all">
            @if ($item->article_type === 'unique')
                <a href="{{ route('business', ['slug' => $item->articleshow->first()->slug]) }}">
                    <p class="line-clamp-2">{{$item->judul}}</p>
                </a>
            @else
                <p class=" w-full max-w-full line-clamp-2">{{$item->judul}}</p>
            @endif
        </td>
        <td class="px-3 py-1 text-center hidden md:table-cell">
            @foreach ($item->articlecategory as $cat)
                {{$cat->category}}
            @endforeach
        </td>
        <td class="px-3 py-1 text-center text-nowrap hidden md:table-cell">{{ $item->user->name }}</td>
        <td class="px-1 sm:px-2">
            <div class="flex gap-1 sm:gap-2 justify-center">
                <button @click="detail({{ $item->id }})"
                    class="w-5 h-5 hover:text-green-500 duration-300 block md:hidden">
                    <svg  id="Layer_1" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" class=" w-full h-full"  xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path fill="currentColor" d="M98.9,184.7l1.8,2.1l136,156.5c4.6,5.3,11.5,8.6,19.2,8.6c7.7,0,14.6-3.4,19.2-8.6L411,187.1l2.3-2.6  c1.7-2.5,2.7-5.5,2.7-8.7c0-8.7-7.4-15.8-16.6-15.8v0H112.6v0c-9.2,0-16.6,7.1-16.6,15.8C96,179.1,97.1,182.2,98.9,184.7z"/></svg>
                </button>
                @if ($item->article_type === 'spintax')
                    {{-- Article Generated --}}
                    <a href="{{ route('article.spin', ['id' => $item->id])}}" target="__blank">
                        <button class="w-5 h-5 hover:text-blue-500 duration-300 relative group">
                            <svg viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"><path d="M20 10H4c-1.1 0-2 .9-2 2s.9 2 2 2h16c1.1 0 2-.9 2-2s-.9-2-2-2zM4 8h12c1.1 0 2-.9 2-2s-.9-2-2-2H4c-1.1 0-2 .9-2 2s.9 2 2 2zM16 16H4c-1.1 0-2 .9-2 2s.9 2 2 2h12c1.1 0 2-.9 2-2s-.9-2-2-2z" fill="currentColor" class="fill-000000"></path></svg>
                            <div class=" absolute gap-0.5 hidden group-hover:flex left-1/2 bottom-full bg-black text-white text-xs px-1 py-1 rounded-md">
                                <span class=" text-green-500">{{ $item->articleshow->where('article_id', $item->id)->where('status', 'publish')->count() }}</span>
                                /
                                <span class=" text-red-500">{{ $item->articleshow->where('article_id', $item->id)->count() }}</span>
                            </div>
                        </button>
                    </a>

                    {{-- Edit --}}
                    <div class="relative" x-data="{ open: false, generatemodal:false }">
                        <button @click="open = !open" class="w-5 h-5 hover:text-green-500 duration-300">
                            <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M3 17.75A3.25 3.25 0 0 0 6.25 21h4.915l.356-1.423c.162-.648.497-1.24.97-1.712l5.902-5.903a3.279 3.279 0 0 1 2.607-.95V6.25A3.25 3.25 0 0 0 17.75 3H11v4.75A3.25 3.25 0 0 1 7.75 11H3v6.75ZM9.5 3.44 3.44 9.5h4.31A1.75 1.75 0 0 0 9.5 7.75V3.44Zm9.6 9.23-5.903 5.902a2.686 2.686 0 0 0-.706 1.247l-.458 1.831a1.087 1.087 0 0 0 1.319 1.318l1.83-.457a2.685 2.685 0 0 0 1.248-.707l5.902-5.902A2.286 2.286 0 0 0 19.1 12.67Z"
                                    fill="currentColor"></path>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.outside="open = false" class="absolute z-10 mt-1 right-0 bottom-0 bg-white border rounded shadow-lg w-32">
                            <a href="{{ route('article.show', ['article' => $item->id]) }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Edit</a>
                            <a href="{{ route('shuffle.image', ['id' => $item->id]) }}" class="block px-4 py-2 text-sm hover:bg-gray-100">Shuffle Image</a>
                            <button @click="generatemodal = !generatemodal" class=" w-full text-left block px-4 py-2 text-sm hover:bg-gray-100">Generate</button>
                        </div>

                        <x-admin.component.generatemodal :id="$item->id" :title="$item->judul" :route="route('article.generate', ['id' => $item->id])"/>
                    </div>

                    {{-- Delete --}}
                    <div class=" relative" x-data="{ open: false, deletemodal: false, deletegeneratemodal: false }">
                        <button @click="open = !open"
                            class="w-5 h-5 hover:text-red-500 duration-300 relative">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z"
                                fill="currentColor"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" class="absolute z-30 mt-1 right-0 bottom-0 bg-white border rounded shadow-lg w-52">
                            <button  @click="deletegeneratemodal = !deletegeneratemodal" class=" w-full text-left block px-4 py-2 text-sm hover:bg-gray-100">Delete Generate Artikel</button>
                            <button @click="deletemodal = !deletemodal" class="w-full text-left block px-4 py-2 text-sm hover:bg-gray-100">Delete Artikel</button>
                            
                            <div x-show="deletegeneratemodal"
                                class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-40">
                                <div class="w-full max-w-[720px] bg-white pb-6 rounded-md flex flex-col gap-4 relative overflow-hidden border-2 border-byolink-1">
                                    <button @click="deletegeneratemodal = false"
                                        class=" absolute top-6 right-6 w-6 h-6 text-white hover:text-red-500 duration-300">
                                        <svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                                            enable-background="new 0 0 512 512">
                                            <path
                                                d="M437.5 386.6 306.9 256l130.6-130.6c14.1-14.1 14.1-36.8 0-50.9-14.1-14.1-36.8-14.1-50.9 0L256 205.1 125.4 74.5c-14.1-14.1-36.8-14.1-50.9 0-14.1 14.1-14.1 36.8 0 50.9L205.1 256 74.5 386.6c-14.1 14.1-14.1 36.8 0 50.9 14.1 14.1 36.8 14.1 50.9 0L256 306.9l130.6 130.6c14.1 14.1 36.8 14.1 50.9 0 14-14.1 14-36.9 0-50.9z"
                                                fill="currentColor" class="fill-000000"></path>
                                        </svg>
                                    </button>
                                    <div class=" pt-6 pb-3 bg-byolink-1 text-white">
                                        <h2 class=" px-6 text-2xl font-bold">Tentunkan jumlah artikel yang akan di hapus</h2>
                                    </div>
                                    <form action="{{ route('article.generate.destroy', ['id' => $item->id]) }}`" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <div class=" px-6 pb-4">
                                            <x-admin.component.numberinput title="Artikel yang di hapus akan di ambil dari artikel terlama yang disimpan" placeholder="Masukkan jumlah artikel yang akan di hapus" :value="''" name="total" />
                                        </div>
                                        <div class="flex justify-end space-x-4 px-6">
                                            <button @click="deletegeneratemodal = false" type="button"
                                                class="px-4 py-2 text-sm sm:text-base bg-neutral-600 duration-300 hover:bg-byolink-1 text-white rounded-md">Cancel</button>
                                            <button type="submit"
                                                class="px-4 py-2 bg-red-500 duration-300 hover:bg-red-900 text-white rounded">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <x-admin.component.deletemodal :title="$item->judul" :route="route('article.destroy', ['article' => $item->id])"/>
                        </div>
                    </div>
                @elseif ($item->article_type === 'unique')
                    {{-- Edit --}}
                    <a href="{{ route('article-show.show', [ 'article_show' => $item->articleshow->first()->id ]) }}"
                        class="w-5 h-5 hover:text-green-500 duration-300">
                        <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 17.75A3.25 3.25 0 0 0 6.25 21h4.915l.356-1.423c.162-.648.497-1.24.97-1.712l5.902-5.903a3.279 3.279 0 0 1 2.607-.95V6.25A3.25 3.25 0 0 0 17.75 3H11v4.75A3.25 3.25 0 0 1 7.75 11H3v6.75ZM9.5 3.44 3.44 9.5h4.31A1.75 1.75 0 0 0 9.5 7.75V3.44Zm9.6 9.23-5.903 5.902a2.686 2.686 0 0 0-.706 1.247l-.458 1.831a1.087 1.087 0 0 0 1.319 1.318l1.83-.457a2.685 2.685 0 0 0 1.248-.707l5.902-5.902A2.286 2.286 0 0 0 19.1 12.67Z"
                                fill="currentColor"></path>
                        </svg>
                    </a>

                    {{-- Delete --}}
                    <div x-data="{ deletemodal:false }">
                        <button @click="deletemodal = !deletemodal"
                            class=" w-4 sm:w-5 aspect-square hover:text-red-500 duration-300">
                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z"
                                    fill="currentColor" class="fill-000000"></path>
                            </svg>
                        </button>
                        <x-admin.component.deletemodal :title="$item->judul" :route="route('article.destroy', ['article' => $item->id])"/>
                    </div>
                @endif
            </div>
        </td>
    </tr>
    <tr x-show="openedIds.includes({{ $item->id }})" class="{{ $loop->even ? 'bg-neutral-100' : 'bg-neutral-200' }} md:hidden border-t-2 border-b-2 border-white h-10 text-neutral-600 divide-x-2 divide-white">
        <td colspan="3" class=" pl-12 p-2">
            <div class=" w-full flex flex-col gap-2 text-sm">
                <div class=" flex flex-row gap-1">
                    <p class=" text-nowrap">Kategori :</p>
                    @forelse ($item->articlecategory as $cat)
                        <span>
                            {{$cat->category}}
                        </span>
                    @empty
                        <p>--</p>
                    @endforelse
                </div>
                <p>Author : {{ $item->user->name }}</p>
            </div>
        </td>
    </tr>
@endforeach