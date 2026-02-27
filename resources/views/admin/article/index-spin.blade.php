<x-app-layout head="Artikel" title="Admin - Artikel">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-6">
        <div class=" w-full sm:py-4 p-4 sm:p-6 bg-white rounded-md shadow-md shadow-black/20">
            <div class=" flex items-center justify-between">
                <div class=" flex items-center gap-2 text-sm sm:text-base">
                    <a href="{{route('article.index')}}" class=" text-byolink-1 hover:text-byolink-3 duration-300">Article</a>
                    <p class=" text-neutral-600">/</p>
                    <p class=" text-neutral-600 flex flex-row gap-1">List Spintax <span class=" hidden md:block">{{ optional($article->articlecategory->first())->category }}</span></p>
                </div>
                <a href="{{route('article.show', ['article' => $article->id])}}" class=" text-sm sm:text-base text-byolink-1 hover:text-byolink-3 duration-300 text-nowrap" target="__blank">Edit Spintax</a>
            </div>
        </div>
        <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
            <div class=" w-full flex flex-col sm:flex-row justify-between gap-4 text-sm sm:text-base">
                <div class=" w-full sm:w-auto flex justify-between gap-4 sm:justify-start">
                    <div class=" flex flex-row items-center gap-1">
                        <div class=" relative group w-4 sm:w-5 aspect-square text-red-500">
                            <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><g data-name="16-Time"><path d="M24 0a24 24 0 1 0 24 24A24 24 0 0 0 24 0Zm0 46a22 22 0 1 1 22-22 22 22 0 0 1-22 22Z" fill="#ff0000" class="fill-000000"></path><path d="M24 6a18 18 0 1 0 18 18A18 18 0 0 0 24 6Zm1 33.95V35h-2v4.95A16 16 0 0 1 8.05 25H14v-2H8.05A16 16 0 0 1 23 8.05V13h2V8.05A16 16 0 0 1 39.95 23H34v2h5.95A16 16 0 0 1 25 39.95Z" fill="#ff0000" class="fill-000000"></path><path d="M25 17h-2v7a1 1 0 0 0 .29.71l6 6 1.41-1.41-5.7-5.71Z" fill="currentColor" class="fill-000000"></path></g></svg>
                            <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap pointer-events-none z-10">
                                Schedule
                            </div>
                        </div>
                        <p class=" text-nowrap font-semibold">: <span class=" text-red-500">{{$count->schedule}}</span>/{{$count->all}}</p>
                        
                    </div>
                    <div class=" flex flex-row items-center gap-1">
                        <div class=" relative group w-4 sm:w-5 aspect-square text-green-500">
                            <svg viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><path d="M62.869 16.842h-.057c-1.096.007-1.266-.945-1.285-1.387V8.542c0-.738-.598-1.336-1.334-1.336H40.428c-4.959 0-6.809 1.838-7.268 2.415l-.126.175-.001.003-.391.536h-.001c-.525.721-.982.414-1.202.178l-.748-.933-.37-.446c-.622-.653-2.243-1.928-5.456-1.928H3.808c-.738 0-1.336.598-1.336 1.336v6.825c0 1.195-.649 1.435-1.047 1.475h-.296c-.324.02-1.078.208-1.078 1.595v32.529c0 .798.647 1.444 1.445 1.444h19.218c6.131 0 8.803 2.312 9.604 3.199l.457.566h.001s1.226 1.387 2.518 0l.268-.314v.001l.004-.003.342-.4c.01-.012.092-.104.211-.226 1.023-.995 3.58-2.823 8.58-2.823h19.805c.799 0 1.445-.646 1.445-1.444V18.122c0-1.176-.851-1.274-1.08-1.28zm-4.318 29.02c0 .641-.535 1.158-1.201 1.158H41.055c-5.799 0-7.904 2.62-7.904 2.62l-.267.358-.125.173h-.001c-.553.751-1.366.006-1.37.002l-.375-.469c-.278-.334-2.408-2.685-7.563-2.685H6.65c-.663 0-1.199-.518-1.199-1.158V11.199c0-.64.536-1.158 1.199-1.158h13.043c9.702 0 10.621 5.511 10.684 7.112v24.106c0 1.752.835 2.081 1.306 2.129h.701c.48-.048 1.243-.333 1.243-1.7V16.842h-.008c.072-2.005.854-6.802 6.689-6.802H57.35c.666 0 1.201.518 1.201 1.158v34.664z" fill="currentColor" class="fill-241f20"></path></svg>
                            <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap pointer-events-none z-10">
                                Publish
                            </div>
                        </div>
                        <p class=" text-nowrap font-semibold">: <span class=" text-green-500">{{$count->publish}}</span>/{{$count->all}}</p>
                    </div>
                    <div class=" flex flex-row items-center gap-1">
                        <div class=" relative group w-4 sm:w-5 aspect-square text-purple-500">
                            <svg viewBox="0 0 32 32" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"><g fill="currentColor" class="fill-333333"><path d="M25 13V9a9 9 0 0 0-9-9 9 9 0 0 0-9 9v4a3 3 0 0 0-3 3v7a9 9 0 0 0 9 9h6a9 9 0 0 0 9-9v-7a3 3 0 0 0-3-3zM9 9a7 7 0 1 1 14 0v4h-2V9.002a5 5 0 0 0-5-5 5 5 0 0 0-5 5V13H9V9zm11 0v4h-8V9a4 4 0 0 1 8 0zm6 10v4c0 3.859-3.141 7-7 7h-6c-3.859 0-7-3.141-7-7v-7a1 1 0 0 1 1-1h18c.551 0 1 .448 1 1v3z"></path><path d="M16 19a2 2 0 0 0-2 2c0 .607.333 1.76.667 2.672.272.742.614 1.326 1.333 1.326.782 0 1.061-.578 1.334-1.316.338-.914.666-2.073.666-2.682a2 2 0 0 0-2-2z"></path></g></svg>
                            <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-black text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition duration-200 whitespace-nowrap pointer-events-none z-10">
                                Private
                            </div>
                        </div>
                        <p class=" text-nowrap font-semibold">: <span class=" text-purple-500">{{$count->private}}</span>/{{$count->all}}</p>
                    </div>
                </div>
                <div class=" w-full md:w-auto flex flex-row gap-2 font-semibold duration-300">
                    <a href="{{ route('article.export', ['id' => $article->id]) }}" aria-label="Export artikel spintax" target="_blank">
                        <button
                            class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-2 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                            <svg class=" w-5 sm:w-6" viewBox="0 0 24 24"xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M18 22a2 2 0 0 0 2-2v-5l-5 4v-3H8v-2h7v-3l5 4V8l-6-6H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12zM13 4l5 5h-5V4z"/></svg>
                        </button>
                    </a>
                    <form action="{{ url()->current() }}" class=" w-full">
                        <input type="text" placeholder="Cari Judul..." name="search" value="{{urlencode(request('search')) ?? ''}}"
                            class=" w-full text-sm sm:text-base md:w-auto py-2 px-3 border border-byolink-1 rounded-md overflow-hidden focus-within:border-byolink-3 font-normal">
                    </form>
                </div>
            </div>
            <table class="w-full max-w-full text-sm sm:text-base rounded-md">
                <thead>
                    <tr class="h-10 bg-byolink-1 text-white divide-x-2 divide-white">
                        <th class=" w-10 px-2 py-1 rounded-tl-md">No</th>
                        <th x-data="{ filter: false }" class=" px-1 sm:px-2 py-1 relative">
                            Judul
                        </th>
                        <th class=" px-1 sm:px-2 py-1 w-[90px] sm:w-[100px] rounded-tr-md">Opsi</th>
                    </tr>
                </thead>
                @forelse ($data as $item)
                    @php
                        $rowBg = $loop->even ? 'bg-neutral-100' : 'bg-neutral-200';
                    @endphp
                    <tr
                        class="{{ $item->status === 'schedule' ? 'bg-red-100' : ($item->status === 'publish' ? 'bg-green-100' : 'bg-purple-100') }} h-10 text-neutral-600 divide-x-2 divide-white">
                        <td class="px-3 py-1 text-center font-semibold">{{ $loop->iteration }}</td>
                        <td class="px-2 sm:px-4 py-1 min-h-10 font-semibold flex">
                            <p class="line-clamp-2">
                                <a href="{{ $item->articles->guardian ? 'https://'.$item->articles->guardian->url.'/'.$item->slug : route('business', ['slug' => $item->slug]) }}" rel="noopener noreferrer" target="_blank">{{$item->judul}}</a>
                            </p>
                        </td>
                        <td class="px-1 sm:px-2">
                            <div class="flex gap-1 sm:gap-2 justify-center">
                                <!-- Edit -->
                                <a href="{{ route('article-generated.show', ['article_generated' => $item->id]) }}" aria-label="Edit generated artikel {{ $item->judul }}"
                                    class="w-5 h-5 hover:text-green-500 duration-300">
                                    <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 17.75A3.25 3.25 0 0 0 6.25 21h4.915l.356-1.423c.162-.648.497-1.24.97-1.712l5.902-5.903a3.279 3.279 0 0 1 2.607-.95V6.25A3.25 3.25 0 0 0 17.75 3H11v4.75A3.25 3.25 0 0 1 7.75 11H3v6.75ZM9.5 3.44 3.44 9.5h4.31A1.75 1.75 0 0 0 9.5 7.75V3.44Zm9.6 9.23-5.903 5.902a2.686 2.686 0 0 0-.706 1.247l-.458 1.831a1.087 1.087 0 0 0 1.319 1.318l1.83-.457a2.685 2.685 0 0 0 1.248-.707l5.902-5.902A2.286 2.286 0 0 0 19.1 12.67Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </a>
        
                                <!-- Delete -->
                                <div x-data="{ deletemodal:false }">
                                    <button @click="deletemodal = !deletemodal"
                                        class="w-5 h-5 hover:text-red-500 duration-300">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z"
                                            fill="currentColor"></path>
                                        </svg>
                                    </button>
                                    <x-admin.component.deletemodal :title="$item->judul" :route="route('article-show.destroy', ['article_show' => $item->id])"/>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                @empty
                    <tr>
                        <td colspan="3" class=" bg-neutral-100 px-1 sm:px-2 py-1 text-center rounded-b-md text-neutral-600">Data tidak ditemukan</td>
                    </tr>
                @endforelse
            </table>
            {{ $data->links('vendor.pagination.admin') }}
        </div>
    </div>

    @include('components.admin.component.success')
</x-app-layout>
