@php
    $desktopMenus = [
        [
            'type' => 'link',
            'label' => 'Dashboard',
            'route' => 'dashboard',
            'active' => 'dashboard',
            'icon' => 'dashboard',
        ],
        [
            'type' => 'accordion',
            'key' => 'guardian',
            'label' => 'Guardian',
            'icon' => 'guardian',
            'items' => [
                [
                    'label' => 'Domain',
                    'route' => 'guardian.index',
                    'active' => ['guardian.index', 'guardian.create', 'guardian.show', 'guardian.edit'],
                ],
                [
                    'label' => 'Article',
                    'route' => 'article.index',
                    'active' => [
                        'article.index',
                        'article.create',
                        'article.show',
                        'articel-show.create',
                        'article-show.create',
                        'article-show.show',
                        'article.spintax',
                        'article.unique',
                        'article.filter',
                        'article.spintax.filter',
                        'article.unique.filter',
                    ],
                ],
                [
                    'label' => 'Template',
                    'route' => 'template.index',
                    'active' => ['template.index', 'template.create', 'template.show', 'template.edit'],
                ],
                [
                    'label' => 'Phone Number',
                    'route' => 'phone-number.index',
                    'active' => ['phone-number.index', 'phone-number.create', 'phone-number.show', 'phone-number.edit'],
                ],
            ],
            'roles' => ['admin'],
        ],
        [
            'type' => 'accordion',
            'key' => 'landingpage',
            'label' => 'Landing Page',
            'icon' => 'guardian',
            'items' => [
            ],
            'roles' => ['admin'],
        ],
        [
            'type' => 'link',
            'label' => 'User',
            'route' => 'user.index',
            'active' => ['user.index', 'user.create', 'user.show'],
            'icon' => 'user',
            'roles' => ['admin'],
        ],
        [
            'type' => 'link',
            'label' => 'Traffic',
            'route' => 'traffic.index',
            'active' => ['traffic.index', 'traffic.create', 'traffic.show'],
            'icon' => 'traffic',
        ],
    ];

    $desktopMenus = collect($desktopMenus)
        ->filter(fn($menu) => !isset($menu['roles']) || in_array(Auth::user()->role, $menu['roles'], true))
        ->values();

    $accordionMenus = $desktopMenus->where('type', 'accordion')->values();
    $firstActiveAccordionKey = $accordionMenus
        ->first(fn($menu) => collect($menu['items'])->contains(fn($item) => request()->routeIs($item['active'])))['key'] ?? null;
    $accordionOpenState = $accordionMenus
        ->mapWithKeys(fn($menu) => [$menu['key'] => $menu['key'] === $firstActiveAccordionKey])
        ->all();
@endphp
<div class="w-full max-w-[100vw] min-h-screen flex flex-row" x-data='@json(["open" => true, "accordionOpen" => $accordionOpenState])'>
    <div :class="open ? 'min-w-20 w-20 lg:min-w-72 lg:w-72' : 'min-w-20 w-20'"
        class=" hidden sm:block bg-white space-y-6 transition-all duration-300 overflow-x-hidden sticky top-0 h-screen">
        <div class="w-full h-20 p-4 flex items-end ">
            <div class="aspect-square h-full">
                <img src="{{ asset('assets/images/icon.png') }}" alt="">
            </div>
            <p class="font-bold text-4xl duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">
                izlink
            </p>
        </div>
        <div class="pl-4 space-y-4">
            @foreach ($desktopMenus as $menu)
                @if ($menu['type'] === 'link')
                    <x-admin.navbutton :route="$menu['route']" :active="$menu['active']" :aria-label="$menu['label']">
                        <div class="min-w-6 h-6">
                            <x-admin.menu-icon :name="$menu['icon']" />
                        </div>
                        <p class="line-clamp-1 duration-300" :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">
                            {{ $menu['label'] }}
                        </p>
                    </x-admin.navbutton>
                @elseif ($menu['type'] === 'accordion')
                    @php
                        $accordionActive = collect($menu['items'])->contains(fn($item) => request()->routeIs($item['active']));
                    @endphp
                    <div>
                        <button type="button"
                            @click="if (accordionOpen['{{ $menu['key'] }}']) { accordionOpen['{{ $menu['key'] }}'] = false } else { Object.keys(accordionOpen).forEach((key) => accordionOpen[key] = false); accordionOpen['{{ $menu['key'] }}'] = true }"
                            class="{{ $accordionActive ? 'border-byolink-1 bg-byolink-1/80 text-white border-r-4' : 'text-neutral-500 hover:border-black hover:text-black hover:bg-neutral-100 hover:border-r-4' }} w-full flex flex-row gap-2 items-center p-3 rounded-l-md font-semibold duration-300">
                            <div class="min-w-5 h-5 mx-0.5">
                                <x-admin.menu-icon :name="$menu['icon']" />
                            </div>
                            <p class="line-clamp-1 duration-300 flex-1 text-left"
                                :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">
                                {{ $menu['label'] }}
                            </p>
                            <svg class="w-4 h-4 duration-300 shrink-0" :class="accordionOpen['{{ $menu['key'] }}'] ? 'rotate-90' : ''"
                                viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div x-show="accordionOpen['{{ $menu['key'] }}']" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1" class="mt-2 ml-2 space-y-2">
                            @foreach ($menu['items'] as $subMenu)
                                <a href="{{ route($subMenu['route']) }}" aria-label="{{ $subMenu['label'] }}"
                                    class="{{ request()->routeIs($subMenu['active']) ? 'border-byolink-1 bg-byolink-1/80 text-white border-r-4' : 'text-neutral-500 hover:border-black hover:text-black hover:bg-neutral-100 hover:border-r-4' }} w-full flex flex-row gap-2 items-center p-3 rounded-l-md font-semibold duration-300">
                                    <div class="min-w-2 h-2 rounded-full bg-current ml-2"></div>
                                    <p class="line-clamp-1 duration-300"
                                        :class="open ? 'opacity-0 lg:opacity-100' : 'opacity-0'">{{ $subMenu['label'] }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <div :class="open ? 'lg:max-w-[calc(100vw-288px)]' : ''"
        class="flex flex-col w-full flex-grow sm:max-w-[calc(100vw-80px)]">
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

                    <div x-show="open" @click.outside="open = false"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute top-full right-0 mt-2 py-2 w-48 bg-white border rounded shadow-lg text-sm z-40">
                        <a href="{{ route('profile.edit') }}"
                            class="block px-4 py-2 text-gray-800 hover:bg-gray-200">Profile</a>
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
                <div class=" w-full px-6 py-3 bg-white rounded-md shadow-md shadow-black/20 text-xl font-bold">
                    {{ $head ?? '' }}</div>
            </div>
            {{ $slot }}
            @include('components.admin.mobile-navbar')
        </div>
    </div>
</div>
