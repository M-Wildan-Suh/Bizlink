<div class=" w-full max-w-[1080px] mx-auto">
    <div class=" w-full grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div class=" swiper w-full overflow-hidden relative rounded-md">
            <div class="swiper-wrapper">
                @foreach ($data->take(3) as $item)    
                    <div class=" swiper-slide w-full aspect-[5/4] overflow-hidden relative">
                        <div class=" absolute inset-0 ">
                            <img src="{{$item->banner ? asset('storage/images/article/banner/'. $item->banner) : asset('assets/images/placeholder.webp')}}" class=" w-full h-full object-cover" alt="">
                        </div>
                        <div style="box-shadow: 0px -178px 115px -74px rgba(0,0,0,0.75) inset;" class=" w-full h-full flex items-end relative bg-black/20">
                            <div class=" w-full py-4 text-white divide-y-2 divide-white/50">
                                <div class=" px-4 sm:px-6 pb-4 space-y-2">
                                    <div class=" w-full flex flex-wrap gap-2">
                                        @foreach ($item->articles->articlecategory->take(2) as $category)
                                            <a href="{{route('category', ['category' => $category->slug])}}">
                                                <div class=" py-0.5 px-3 bg-white text-gray-600 text-xs line-clamp-1 rounded-full">{{$category->category}}</div>
                                            </a>
                                        @endforeach
                                    </div>
                                    <a href="{{route('business', ['slug' => $item->slug])}}">
                                        <p class=" text-xl sm:text-3xl font-bold line-clamp-2">{{$item->judul}}</p>
                                    </a>
                                    <p class=" line-clamp-1 sm:line-clamp-2 text-sm sm:text-base">{!! nl2br(Str::limit(strip_tags($item->article), 200)) !!}</p>
                                </div>
                                <p class=" px-4 sm:px-6 pt-2 text-sm sm:text-base">
                                    <a href="{{ route('author', ['username' => $item->articles->user->slug]) }}" class=" font-semibold">{{$item->articles->user->name}}</a>, {{$item->date}}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class=" absolute bottom-0 z-10 pb-4 pr-4 right-0">
                <!-- If we need pagination -->
                <div class="">
                    <div class="pagination"></div>
                </div>
            </div>
        </div>
        <div class=" w-full grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            @foreach ($data->take(3) as $item)
                <div class=" w-full h-44 sm:h-56 md:h-full overflow-hidden relative rounded-md {{ $loop->index === 2 ? 'sm:col-span-2' : '' }}">
                    <div class=" absolute inset-0 ">
                        <img src="{{ $item->banner ? asset('storage/images/article/banner/'. $item->banner) : asset('assets/images/placeholder.webp')}}" class=" w-full h-full object-cover" alt="">
                    </div>
                    <div style="box-shadow: 0px -178px 115px -74px rgba(0,0,0,0.75) inset;" class=" w-full h-full flex items-end relative bg-black/20">
                        <div class=" w-full py-4 text-white">
                            <div class=" px-4 sm:px-6 space-y-2">
                                <div class=" w-full flex flex-wrap gap-2">
                                    @foreach ($item->articles->articlecategory->take(2) as $category)
                                        <a href="{{route('category', ['category' => $category->slug])}}">
                                            <div class=" py-0.5 px-3 bg-white text-gray-600 text-xs line-clamp-1 rounded-full">{{$category->category}}</div>
                                        </a>
                                    @endforeach
                                </div>
                                <a href="{{route('business', ['slug' => $item->slug])}}">
                                    <p class=" text-base sm:text-lg font-bold line-clamp-2">{{ $item->judul }}</p>
                                </a>
                            </div>
                            <p class=" px-4 sm:px-6 pt-2 text-xs">
                                <a href="{{ route('author', ['username' => $item->articles->user->slug]) }}" class=" font-semibold">{{$item->articles->user->name}}</a>, {{$item->date}}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
<style>
    .swiper-pagination-bullet {
        width: 12px;
        height: 12px;
        opacity: 0.5;
        background-color: white;
        transition-duration: 300ms;
        border-radius: 9999px;
    }

    .swiper-pagination-bullet-active {
        width: 24px;
        opacity: 1;
        background-color: white;
    }
</style>  
<script>
    window.addEventListener('load', function() {
        const swiper = new Swiper('.swiper', {
            direction: 'horizontal',
            loop: true,
            speed: 500,
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.pagination',
                clickable: true,
                renderBullet: function (index, className) {
                    return `<span class="${className}"></span>`;
                },
            },
        });
    });
</script>