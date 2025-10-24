<div class="w-full max-w-[100vw] min-h-screen flex flex-row" x-data="{ open: true }">
    <div :class="open ? 'min-w-20 w-20 lg:min-w-72 lg:w-72' : 'min-w-20 w-20'"
        class=" hidden sm:block bg-white space-y-6 transition-all duration-300 overflow-x-hidden sticky top-0 h-screen">
        <div class="w-full h-20 p-4 flex items-end ">
            <div class="aspect-square h-full">
                <img src="{{ asset('assets/images/icon.png') }}" alt="">
            </div>
            <p class="font-bold text-2xl duration-300 pb-1" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">izlink
            </p>
        </div>
        <div class="pl-4 space-y-4">
            <x-admin.navbutton route="dashboard" :active="'dashboard'">
                <div class="min-w-6 h-6">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M4 13h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zm-1 7a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v4zm10 0a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-7a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v7zm1-10h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1z"
                            fill="currentColor" class="fill-000000"></path>
                    </svg>
                </div>
                <p class=" line-clamp-1 duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">Dashboard
                </p>
            </x-admin.navbutton>
            @if (Auth::user()->role === 'admin')    
                <x-admin.navbutton route="guardian.index" :active="['guardian.index', 'guardian.create', 'guardian.show']">
                    <div class="min-w-5 h-5 mx-0.5">
                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 1H5a4 4 0 0 0-4 4v14a4 4 0 0 0 4 4h14a4 4 0 0 0 4-4V5a4 4 0 0 0-4-4ZM9 4a1 1 0 1 1-1 1 1 1 0 0 1 1-1ZM5 4a1 1 0 1 1-1 1 1 1 0 0 1 1-1Zm14 5H5a1 1 0 0 1 0-2h14a1 1 0 0 1 0 2Zm0-3a1 1 0 1 1 1-1 1 1 0 0 1-1 1Z" fill="currentcolor" class="fill-232323"></path></svg>
                    </div>
                    <p class=" line-clamp-1 duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">Guardian Web</p>
                </x-admin.navbutton>
                <x-admin.navbutton route="user.index" :active="['user.index', 'user.create', 'user.show']">
                    <div class="min-w-5 h-5 mx-0.5">
                        <svg fill="none" viewBox="0 0 15 15" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M1.5 0A1.5 1.5 0 0 0 0 1.5v12A1.5 1.5 0 0 0 1.5 15h12a1.5 1.5 0 0 0 1.5-1.5v-12A1.5 1.5 0 0 0 13.5 0h-12Zm5 9A3.5 3.5 0 0 0 3 12.5v1a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-1A3.5 3.5 0 0 0 8.5 9h-2ZM5 5.5a2.5 2.5 0 1 1 5 0 2.5 2.5 0 0 1-5 0Z" fill="currentColor" fill-rule="evenodd" class="fill-000000"></path></svg>
                    </div>
                    <p class=" line-clamp-1 duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">User</p>
                </x-admin.navbutton>
                <x-admin.navbutton route="template.index" :active="['template.index', 'template.create', 'template.show']">
                    <div class="min-w-6 h-6">
                        <svg viewBox="-265 388.9 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            enable-background="new -265 388.9 64 64">
                            <path
                                d="M-210.6 410.9h-44.8c-.9 0-1.6-.7-1.6-1.6v-9.2c0-.9.7-1.6 1.6-1.6h44.8c.9 0 1.6.7 1.6 1.6v9.2c0 .9-.7 1.6-1.6 1.6zM-210.6 443.3h-11.8c-.9 0-1.6-.7-1.6-1.6v-25.6c0-.9.7-1.6 1.6-1.6h11.8c.9 0 1.6.7 1.6 1.6v25.6c0 .9-.7 1.6-1.6 1.6zM-229.6 443.3h-25.8c-.9 0-1.6-.7-1.6-1.6v-25.6c0-.9.7-1.6 1.6-1.6h25.8c.9 0 1.6.7 1.6 1.6v25.6c0 .9-.7 1.6-1.6 1.6z"
                                fill="currentColor" class="fill-000000"></path>
                        </svg>
                    </div>
                    <p class=" line-clamp-1 duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">Template</p>
                </x-admin.navbutton>
            @endif
            <x-admin.navbutton route="phone-number.index" :active="['phone-number.index', 'phone-number.create', 'phone-number.show']">
                <div class="min-w-5 h-5 mx-0.5">
                    <svg viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M384 32H64C28.65 32 0 60.65 0 96v320c0 35.35 28.65 64 64 64h320c35.35 0 64-28.65 64-64V96c0-35.35-28.7-64-64-64zm-32.4 289.5-11.62 50.39c-1.633 7.125-7.9 12.11-15.24 12.11-126.1 0-228.7-102.6-228.7-228.8 0-7.328 4.984-13.59 12.11-15.22l50.38-11.63c7.344-1.703 14.88 2.109 17.93 9.062l23.27 54.28a15.642 15.642 0 0 1-4.492 18.22L168.3 232c16.99 34.61 45.14 62.75 79.77 79.75l22.02-26.91c4.344-5.391 11.85-7.25 18.24-4.484l54.24 23.25c6.93 2.994 10.73 10.594 9.03 17.894z" fill="currentColor" class="fill-000000"></path></svg>
                </div>
                <p class=" line-clamp-1 duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">Phone Number</p>
            </x-admin.navbutton>
            <x-admin.navbutton route="article.index" :active="['article.index', 'article.create', 'article.show', 'articel-show.create', 'article-show.create', 'article-show.show', 'article.spintax', 'article.unique', 'article.filter', 'article.spintax.filter', 'article.unique.filter']">
                <div class="min-w-6 h-6">
                    <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg"><path d="M8 28h8v-8H8v8zm0 10h8v-8H8v8zm0-20h8v-8H8v8zm10 10h24v-8H18v8zm0 10h24v-8H18v8zm0-28v8h24v-8H18z" fill="currentColor" class="fill-000000"></path><path d="M0 0h48v48H0z" fill="none"></path></svg>
                </div>
                <p class=" line-clamp-1 duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">Article</p>
            </x-admin.navbutton>
        </div>
    </div>
    <div :class="open ? 'lg:max-w-[calc(100vw-288px)]' : ''" class="flex flex-col w-full flex-grow sm:max-w-[calc(100vw-80px)]">
        <div class=" hidden sm:flex w-full bg-white py-6 pl-12 pr-12 lg:pr-32 duration-300 sticky top-0 z-30">
            <div class="w-full mx-auto flex justify-between">
                <div class="flex gap-4 items-center">
                    <button id="openclose" class=" w-0 lg:w-6 aspect-square duration-300" @click="open = !open">
                        <svg class="duration-300" :class="open ? 'rotate-90' : ''" viewBox="0 0 32 32"
                            xml:space="preserve" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 32 32">
                            <path
                                d="M4 10h24a2 2 0 0 0 0-4H4a2 2 0 0 0 0 4zm24 4H4a2 2 0 0 0 0 4h24a2 2 0 0 0 0-4zm0 8H4a2 2 0 0 0 0 4h24a2 2 0 0 0 0-4z"
                                fill="currentColor" class="fill-000000"></path>
                        </svg>
                    </button>
                    <div class="text-2xl font-bold">{{ $head ?? '' }}</div>
                </div>
                <div x-data="{ open: false }" class="flex justify-end items-center text-neutral-600 relative">
                    <button @click="open = !open" class="flex gap-2 items-center">
                        <div>{{ Auth::user()->email }}</div>
                        <div class="w-4 h-4">
                            <svg :class="{ 'rotate-90': open, 'rotate-0': !open }"
                                class="transition-transform feather feather-chevron-right" fill="none"
                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <polyline points="9 18 15 12 9 6" />
                            </svg>
                        </div>
                    </button>

                    <!-- Floating Dropdown Menu with Slide-down Animation -->
                    <div x-show="open" @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute top-full right-0 mt-2 py-2 w-48 bg-white border rounded shadow-lg text-sm z-40">
                        <a href="{{route('profile.edit')}}" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
                        <form method="POST" class=" w-full" action="{{ route('logout') }}">
                            @csrf
                            <button
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200 w-full text-left">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class=" w-full">
            <div class=" sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 sm:pb-8 px-4 sm:hidden">
                <div class=" w-full px-6 py-3 bg-white rounded-md shadow-md shadow-black/20 text-xl font-bold">{{$head ?? ''}}</div>
            </div>
            {{ $slot }}
            @include('components.admin.mobile-navbar')
        </div>
    </div>
</div>
