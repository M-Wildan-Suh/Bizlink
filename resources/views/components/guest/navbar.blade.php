{{-- Navigation --}}
<div class="" x-data="{open : false}">
    <div class=" fixed top-0 left-0 w-full bg-white px-4 md:px-8 py-4 z-50 shadow-md shadow-black/10">
        <div class=" w-full max-w-[1080px] mx-auto flex items-center gap-10 justify-between">
            <a href="{{ route('home') }}" aria-label="Kembali ke Beranda Bizlink">
                <div class=" h-10 flex items-center overflow-hidden">
                    {{-- <p class=" text-3xl sm:text-4xl font-bold">Bizlink</p> --}}
                    <img src="{{ asset('assets/images/logo.webp') }}" class=" max-h-full" alt="Logo Bizlink">
                </div>
            </a>
            <div class=" hidden md:block w-full max-w-lg">
                <form action="{{route('allarticle')}}" method="get">
                    <div class=" flex items-center justify-between h-10 bg-white">
                        <input type="text" name="search" value="{{ request('search') }}" class=" min-w-0 sm:flex-grow h-10 text-sm px-4 sm:px-6 border-r-0 rounded-l-full focus:border-byolink-1 focus:ring-0" placeholder="Cari Artikel....">
                        <button aria-label="Cari" class=" px-4 sm:px-6 bg-byolink-1 hover:bg-byolink-3 rounded-r-full text-white duration-300 h-10">
                            <div class=" w-[18px] aspect-square overflow-hidden">
                                <svg aria-hidden="true" class="e-font-icon-svg e-fas-search" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
            <div class=" hidden md:flex flex-row gap-6 items-center text-neutral-500">
                <x-guest.nav-button route="{{route('home')}}" active="{{request()->routeIs('home')}}">Beranda</x-guest.nav-button>
                <div x-data="{ article : false }" class=" relative">
                    <button @click="article = !article" class="{{ request()->routeIs('allarticle', 'pageallarticle', 'author', 'pageauthor', 'category', 'pagecategory', 'tag', 'pagetag') ? 'text-byolink-1' : 'hover:text-black hover:-translate-y-1'}} text-lg font-black py-2 duration-300" aria-label="Artikel">Artikel</button>
                    <div x-show="article" class=" absolute top-full left-0 bg-white py-2 rounded-md shadow-md shadow-black/20 text-sm">
                        <div class=" max-h-36 overflow-auto flex flex-col gap-1">
                            <a href="{{route('allarticle')}}" class=" w-full  text-nowrap px-4 hover:bg-neutral-100 duration-300 py-1">Artikel Terbaru</a>
                            @foreach ($category as $item)
                                <a href="{{route('category', ['category' => $item->slug])}}" class=" w-full  text-nowrap px-4 hover:bg-neutral-100 duration-300 py-1">{{$item->category}}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <x-guest.nav-button route="{{ request()->routeIs('business') ? route('home') : '' }}#kontak" active="">Kontak</x-guest.nav-button>
                {{-- @if (Route::has('login'))
                    @auth
                        <form method="POST" class="" action="{{ route('logout') }}">
                            @csrf
                            <button class=" py-1.5 px-5 bg-red-600 rounded-md font-semibold text-white hover:bg-red-900 duration-300">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class=" md:mr-0">
                            <button class=" py-1.5 px-5 bg-byolink-2 rounded-md font-semibold text-white hover:bg-black duration-300">Login</button>
                        </a>
                    @endauth
                @endif --}}
            </div>
            <button @click="open = !open" :class="{'text-third': open, 'text-second': !open}" aria-label="Menu" class=" flex md:hidden gap-2 items-center duration-300">
                <div class=" w-6 h-6">
                    <svg viewBox="0 0 32 32" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 32 32"><path d="M4 10h24a2 2 0 0 0 0-4H4a2 2 0 0 0 0 4zm24 4H4a2 2 0 0 0 0 4h24a2 2 0 0 0 0-4zm0 8H4a2 2 0 0 0 0 4h24a2 2 0 0 0 0-4z" fill="currentColor" class="fill-000000"></path></svg>
                </div>
            </button>
        </div>
    </div>
    <div :class="{' top-[70px] sm:top-20': open, '-translate-y-full top-0': !open}" class=" fixed flex md:hidden flex-col bg-white w-full left-0 justify-center gap-4 font-semibold text-neutral-600 pt-2 px-4 pb-4 duration-300 z-40">
        <x-guest.nav-button route="{{route('home')}}" active="{{request()->routeIs('home')}}">Beranda</x-guest.nav-button>
        <div x-data="{ article : false }">
            <button @click="article = !article" class="{{ request()->routeIs('allarticle', 'pageallarticle', 'author', 'pageauthor', 'category', 'pagecategory', 'tag', 'pagetag') ? 'text-byolink-1' : 'hover:text-black hover:-translate-y-1'}} text-lg font-black py-2 duration-300" aria-label="Artikel">Artikel</button>
            <div x-show="article" class=" max-h-36 overflow-auto py-2 flex flex-col gap-1 text-sm">
                <a href="{{route('allarticle')}}" class=" w-full  text-nowrap px-4 hover:bg-neutral-100 duration-300 py-1">Artikel Terbaru</a>
                @foreach ($category as $item)
                    <a href="{{route('category', ['category' => $item->slug])}}" class=" w-full  text-nowrap px-4 hover:bg-neutral-100 duration-300 py-1">{{$item->category}}</a>
                @endforeach
            </div>
        </div>
        <x-guest.nav-button route="{{ request()->routeIs('business') ? route('home') : '' }}#kontak" active="">Kontak</x-guest.nav-button>
        <form action="{{route('allarticle')}}" method="get">
            <div class=" flex items-center justify-between h-10 bg-white">
                <input type="text" name="search" value="{{ request('search') }}" class="flex-grow h-10 text-sm px-4 sm:px-6 border-r-0 rounded-l-full focus:border-byolink-1 focus:ring-0" placeholder="Cari Artikel....">
                <button class=" px-6 bg-byolink-1 hover:bg-byolink-3 rounded-r-full text-white duration-300 h-10" aria-label="cari">
                    <div class=" w-[18px] aspect-square overflow-hidden">
                        <svg aria-hidden="true" class="e-font-icon-svg e-fas-search" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg>
                    </div>
                </button>
            </div>
        </form>
        {{-- @if (Route::has('login'))
            @auth
                <x-guest.nav-button route="{{route('profile.edit')}}" active="{{request()->routeIs('profile.edit')}}">Profile</x-guest.nav-button>
                <form method="POST" class=" flex w-full" action="{{ route('logout') }}">
                    @csrf
                    <button class=" w-full py-2 bg-red-600 rounded-md font-semibold text-white hover:bg-red-900 duration-300">
                        Logout
                    </a>
                </form>
            @else
                <a href="{{ route('login') }}" class="md:mr-0">
                    <button class=" w-full py-2 px-4 bg-byolink-2 rounded-md font-semibold text-white hover:bg-byolink-3 duration-300">Login</button>
                </a>
            @endauth
        @endif --}}
    </div>
</div>
