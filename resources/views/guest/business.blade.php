@php
    preg_match('/<(p|div)[^>]*>(.*?)<\/\1>/is', $data->article, $matches);
    $firstBlock = $matches[2] ?? $data->article;

    $cleanText = strip_tags($firstBlock);

    // Hapus &nbsp; dan decode entity lain seperti &amp;, &quot;, dll
    $cleanText = str_replace('&nbsp;', ' ', $cleanText);
    $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5);

    $sentence = Str::limit(trim($cleanText), 155);
@endphp
<x-layout.guest :title="$data->judul. ' - Bizlink'" :desc="$sentence" :tags="$data->articles->articletag" :category="$category">
    <div class="w-full bg-neutral-50">
        <div class="w-full px-4 py-6 sm:px-6 sm:py-8">
            <div class="mx-auto flex w-full max-w-[1080px] flex-col gap-6">
                <div class="overflow-hidden rounded-md bg-white shadow-md shadow-black/10">
                    <div class="relative aspect-[16/8] w-full bg-neutral-200 sm:aspect-[16/6]">
                        <img src="{{ $data->banner ? asset('storage/images/article/banner/' . $data->banner) : asset('assets/images/placeholder.webp') }}"
                            class="h-full w-full object-cover" alt="{{ $data->judul }}">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 p-5 text-white sm:p-8">
                            <div class="mb-3 flex flex-wrap gap-2">
                                @foreach ($data->articles->articlecategory as $item)
                                    <a href="{{ route('category', ['category' => $item->slug]) }}"
                                        class="rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur">
                                        {{ $item->category }}
                                    </a>
                                @endforeach
                            </div>
                            <h1 class="max-w-4xl text-2xl font-bold leading-tight sm:text-4xl">{{ $data->judul }}</h1>
                            <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-white/90 sm:text-base">
                                <a href="{{ route('author', ['username' => $data->articles->user->slug]) }}" class="font-semibold hover:underline">
                                    {{ $data->articles->user->name }}
                                </a>
                                <span>{{ $data->date }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">
                    <div class="space-y-6">
                        @if ($data->articleshowgallery->isNotEmpty())
                            <div class="business-gallery-swiper swiper w-full overflow-hidden">
                                <div class="swiper-wrapper">
                                    @foreach ($data->articleshowgallery as $item)
                                        <a data-fancybox="gallery"
                                            href="{{ asset('storage/images/article/gallery/' . $item->image) }}"
                                            class="swiper-slide block overflow-hidden rounded-md bg-neutral-100">
                                            <img src="{{ asset('storage/images/article/gallery/' . $item->image) }}"
                                                class="aspect-[3/4] h-full w-full object-cover"
                                                alt="{{ $item->image_alt ?? $data->judul }}">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="rounded-md bg-white p-5 shadow-md shadow-black/10 sm:p-8">
                            <div class="mb-6 space-y-4 border-b border-neutral-200 pb-6">
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($data->articles->articlecategory as $item)
                                        <a href="{{ route('category', ['category' => $item->slug]) }}"
                                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 sm:text-sm">
                                            {{ $item->category }}
                                        </a>
                                    @endforeach
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-sm text-neutral-600 sm:text-base">
                                    <a href="{{ route('author', ['username' => $data->articles->user->slug]) }}"
                                        class="font-semibold text-neutral-900 hover:text-blue-600 duration-300">
                                        {{ $data->articles->user->name }}
                                    </a>
                                    <span class="text-neutral-300">|</span>
                                    <span>{{ $data->date }}</span>
                                </div>
                            </div>
                            <div class="article-content text-neutral-700">
                                {!! $data->article !!}
                            </div>
                        </div>

                        @if ($data->articles->video_type != 'none')
                            <div class="rounded-md bg-white p-5 shadow-md shadow-black/10 sm:p-8">
                                @include('components.guest.' . $data->articles->video_type)
                            </div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        @if ($data->articles->articletag->isNotEmpty())
                            <div class="rounded-md bg-white p-5 shadow-md shadow-black/10 sm:p-6">
                                <div class="mb-4 flex items-center gap-3">
                                    <div class="h-8 w-1 rounded-full bg-byolink-2"></div>
                                    <h2 class="text-xl font-bold text-neutral-900">Tag</h2>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($data->articles->articletag as $item)
                                        <a href="{{ route('tag', ['tag' => $item->slug]) }}"
                                            class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold lowercase text-blue-700 sm:text-sm">
                                            #{{ $item->tag }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="rounded-md bg-white p-5 shadow-md shadow-black/10 sm:p-6">
                            <div class="mb-4 flex items-center gap-3">
                                <div class="h-8 w-1 rounded-full bg-byolink-2"></div>
                                <h2 class="text-xl font-bold text-neutral-900">Rekomendasi Artikel</h2>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-1">
                                @forelse ($recommendations as $item)
                                    <div class="grid grid-cols-5 gap-2">
                                        <a href="{{ route('business', ['slug' => $item->slug]) }}" aria-label="{{ $item->judul }}">
                                            <div class="aspect-square w-full overflow-hidden rounded-md bg-white">
                                                <img src="{{ $item->banner ? asset('storage/images/article/banner/' . $item->banner) : asset('assets/images/placeholder.webp') }}"
                                                    class="h-full w-full object-cover" alt="{{ $item->judul }}">
                                            </div>
                                        </a>
                                        <div class="col-span-4 flex flex-col justify-between">
                                            <a href="{{ route('business', ['slug' => $item->slug]) }}" aria-label="{{ $item->judul }}">
                                                <p class="line-clamp-2 text-sm font-semibold hover:text-blue-600 duration-300">{{ $item->judul }}</p>
                                            </a>
                                            <div class="space-y-1 text-xs text-neutral-600">
                                                <a href="{{ route('author', ['username' => $item->articles->user->slug]) }}" aria-label="{{ $item->articles->user->name }}">
                                                    <p class="font-bold hover:text-blue-600 duration-300">{{ $item->articles->user->name }}</p>
                                                </a>
                                                <p>{{ $item->date }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-neutral-600">Belum ada artikel lain dengan kategori yang sama.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($data->telephone || $data->whatsapp)
            <div class="sticky bottom-0 z-20 border-y border-neutral-200 bg-white/95 px-4 py-3 backdrop-blur">
                <div class="mx-auto grid w-full max-w-[1080px] {{ ($data->telephone && $data->whatsapp) ? 'grid-cols-2' : 'grid-cols-1' }} gap-3 sm:gap-4">
                    @if ($data->telephone)
                        <a href="tel:{{ $data->no_tlp }}"
                            class="flex items-center justify-center rounded-full bg-byolink-1 px-4 py-3 text-sm font-semibold text-white transition duration-300 hover:bg-byolink-3 sm:text-base">
                            Telephone
                        </a>
                    @endif
                    @if ($data->whatsapp)
                        <a href="https://wa.me/{{ $data->no_tlp }}?text={{ urlencode('Halo saya dapat info dari ' . url()->current()) }}"
                            target="__blank"
                            class="flex items-center justify-center rounded-full bg-green-500 px-4 py-3 text-sm font-semibold text-white transition duration-300 hover:bg-green-600 sm:text-base">
                            WhatsApp
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @include('components.guest.footer')
    </div>
    @if (Auth::user())    
        <a href="{{$data->articles->article_type === 'spintax' ? route('article-generated.show', ['article_generated' => $data->id]) : route('article-show.show', ['article_show' => $data->id])}}" target="__blank">
            <button class=" fixed top-24 right-8 bg-white text-black font-semibold hover:bg-byolink-1 hover:text-white duration-300 px-4 py-2 rounded-full">Edit</button>
        </a>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const galleryElement = document.querySelector('.business-gallery-swiper');

            if (!galleryElement || typeof Swiper === 'undefined') {
                return;
            }

            new Swiper(galleryElement, {
                spaceBetween: 12,
                slidesPerView: 1.15,
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },
            });
        });
    </script>
    <style>
        .article-content div,
        .article-content img,
        .article-content iframe {
            max-width: 100% !important;
        }

        .article-content img {
            height: auto !important;
            border-radius: 1rem;
        }

        .article-content a {
            color: #2563eb;
            font-weight: 600;
        }

        .article-content ol,
        .article-content ul {
            padding-left: 1.25rem !important;
        }

        .article-content ol {
            list-style-type: decimal;
        }

        .article-content ul {
            list-style-type: disc;
        }

        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4,
        .article-content h5,
        .article-content h6 {
            color: #171717 !important;
            font-weight: 700;
            margin-bottom: 0.75rem !important;
        }

        .article-content p,
        .article-content li {
            margin-bottom: 1rem !important;
            font-size: 1rem !important;
            line-height: 1.75rem !important;
        }
    </style>
</x-layout.guest>
