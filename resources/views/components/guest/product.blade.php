@foreach ($data as $item)
    <div class=" w-full flex flex-col rounded-md overflow-hidden shadow-md shadow-black/20">
        <div class=" relative">
            <a href="{{ route('business', ['slug' => $item->slug]) }}" aria-label="{{$item->judul}}">
                <div class=" w-full aspect-[3/2] bg-white overflow-hidden">
                    <img src="{{$item->banner ? asset('storage/images/article/banner/' . $item->banner) : asset('assets/images/placeholder.webp')}}"
                        class=" w-full h-full object-cover" alt="">
                </div>
            </a>
            <div class=" absolute bottom-2 left-0 w-full px-2 flex flex-wrap gap-2 overflow-hidden">
                @foreach ($item->articles->articlecategory as $category)
                    <a href="{{route('category', ['category' => $category->slug])}}" aria-label="{{$category->category}}">
                        <div class=" py-0.5 px-3 bg-white text-gray-600 text-xs rounded-full line-clamp-1">{{$category->category}}</div>
                    </a>
                @endforeach
            </div>
        </div>
        <div class=" py-4 px-2 text-sm flex flex-grow flex-col gap-2 justify-between">
            <a href="{{ route('business', ['slug' => $item->slug]) }}" aria-label="{{$item->judul}}">
                <p class=" line-clamp-2 font-bold hover:text-blue-600 duration-300">{{ $item->judul }}</p>
            </a>
            <div class=" grid text-xs sm:text-sm sm:grid-cols-2 gap-2">
                <a href="{{ route('author', ['username' => $item->articles->user->slug]) }}" aria-label="{{$item->judul}}">
                    <p class="font-bold text-neutral-600 hover:text-blue-600 duration-300">{{$item->articles->user->name}}</p>
                </a>
                <p class=" text-right text-neutral-600">{{$item->date}}</p>
            </div>
        </div>
    </div>
@endforeach
