<x-layout.guest title="Bizlink" :category="$category">
    <div class=" w-full min-h-[calc(100vh-370px)]">
        <div class=" w-full py-6 sm:py-10 px-4 sm:px-6 space-y-8 sm:space-y-12">
        @include('components.guest.home.header')
            <div class=" w-full max-w-[1080px] mx-auto">
                <div class=" grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class=" md:col-span-3 w-full space-y-4 sm:space-y-6">
                        <div class=" w-full flex justify-between items-center">
                            <div class=" w-full flex items-center gap-2 sm:gap-4">
                                <div class=" w-1 sm:w-1.5 h-7 sm:h-10 bg-byolink-2 rounded-full"></div>
                                <p class=" text-xl sm:text-3xl font-bold text-center">Artikel Terbaru</p>
                            </div>
                            <a href="{{route('allarticle')}}">
                                <button class=" px-4 py-2 flex items-center gap-1 border rounded-full text-nowrap text-xs text-neutral-600 border-neutral-600 hover:text-byolink-1 hover:border-byolink-1 duration-300">
                                    <p>Lihat Lainnya</p>
                                    <div class=" w-3 aspect-square">
                                        <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><path d="M22 9a1 1 0 0 0 0 1.42l4.6 4.6H3.06a1 1 0 1 0 0 2h23.52L22 21.59A1 1 0 0 0 22 23a1 1 0 0 0 1.41 0l6.36-6.36a.88.88 0 0 0 0-1.27L23.42 9A1 1 0 0 0 22 9Z" data-name="Layer 2" fill="currentColor" class="fill-000000"></path></svg>
                                    </div>
                                </button>
                            </a>
                        </div>
                        <div class=" w-full grid grid-cols-2 md:grid-cols-3 gap-4">
                            @include('components.guest.product')
                        </div>
                        <div class=" w-full">
                            {{ $data->links() }}
                        </div>
                    </div>
                    <div class="">
                        <div class=" md:sticky top-24 space-y-4 sm:space-y-6">
                            <div class=" w-full flex items-center gap-2 sm:gap-4 h-7 sm:h-10">
                                <div class=" w-1 h-7 bg-byolink-2 rounded-full"></div>
                                <p class=" text-xl font-bold text-center">Artikel Populer</p>
                            </div>
                            <div class=" grid grid-cols-1 sm:grid-cols-2 md:grid-cols-1 gap-4 sm:gap-6">
                                @foreach ($trend as $item)
                                    <div class=" grid grid-cols-5 sm:grid-cols-4 gap-2">
                                        <a href="{{ route('business', ['slug' => $item->slug]) }}" aria-label="{{$item->judul}}">
                                            <div class=" w-full aspect-square rounded-md bg-white overflow-hidden">
                                                <img src="{{$item->banner ? asset('storage/images/article/banner/' . $item->banner) : asset('assets/images/placeholder.webp')}}"
                                                    class=" w-full h-full object-cover" alt="">
                                            </div>
                                        </a>
                                        <div class=" col-span-4 sm:col-span-3 flex flex-col justify-between">
                                            <a href="{{ route('business', ['slug' => $item->slug]) }}" aria-label="{{$item->judul}}">
                                                <p class=" line-clamp-2 text-sm h-10">{{$item->judul}}</p>
                                            </a>
                                            <div class="flex flex-row justify-between text-xs">
                                                <a href="{{ route('author', ['username' => $item->articles->user->slug]) }}" aria-label="{{$item->judul}}">
                                                    <p class="font-bold text-neutral-600 hover:text-blue-600 duration-300">{{$item->articles->user->name}}</p>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('components.guest.footer')
</x-layout.guest>