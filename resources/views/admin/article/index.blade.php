<x-app-layout head="Artikel" title="Admin - Artikel">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
            <div class="w-full flex flex-col lg:flex-row gap-4 justify-between items-center">
                <div class=" w-full lg:w-auto grid grid-cols-2 sm:grid-cols-3 gap-2 sm:gap-4">
                    <a href="{{ route('article.create') }}"
                        class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                        +Artikel Spintax
                    </a>
                    <a href="{{ route('article-show.create') }}"
                        class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                        +Artikel Unique
                    </a>
                    <a href="{{ route('source-code.index') }}"
                        class=" col-span-2 sm:col-span-1 text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                        Short Code
                    </a>

                </div>

                <!-- Search -->
                <div class=" w-full lg:w-auto flex flex-row font-semibold duration-300">
                    <form action="{{ url()->current() }}" class=" w-full">
                        <input type="text" placeholder="Search Title..." name="search"
                            value="{{ urlencode(request('search')) ?? '' }}"
                            class=" w-full text-sm sm:text-base lg:w-auto py-2 px-3 border border-byolink-1 rounded-md overflow-hidden focus-within:border-byolink-3 font-normal">
                    </form>
                </div>
            </div>
            <div class=" w-full grid grid-cols-3 gap-2 sm:gap-4">
                <a href="{{ route('article.index') }}"
                    class="{{ request()->routeIs(['article.index', 'article.filter']) ? 'bg-byolink-1 text-white' : ' text-black rounded-md hover:text-white bg-neutral-200 hover:bg-byolink-1' }} text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 font-semibold rounded-md duration-300">
                    All
                </a>
                <a href="{{ route('article.spintax') }}"
                    class="{{ request()->routeIs(['article.spintax', 'article.spintax.filter']) ? 'bg-byolink-1 text-white' : ' text-black rounded-md hover:text-white bg-neutral-200 hover:bg-byolink-1' }} text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 font-semibold rounded-md duration-300">
                    Spintax
                </a>
                <a href="{{ route('article.unique') }}"
                    class="{{ request()->routeIs(['article.unique', 'article.unique.filter']) ? 'bg-byolink-1 text-white' : ' text-black rounded-md hover:text-white bg-neutral-200 hover:bg-byolink-1' }} text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 font-semibold rounded-md duration-300">
                    Unique
                </a>
            </div>
            <div class=" w-full flex flex-row sm:justify-end gap-4 text-sm sm:text-base">
                <div x-data="{ status: '{{ $status ?? 'all' }}', web: '{{ $filterweb ?? 'all' }}', category: '{{ $filtercat ?? 'all' }}' }" class=" flex flex-row justify-between lg:justify-start gap-4">
                    <div class=" w-full grid grid-cols-3 gap-2">
                        <div class="flex items-center gap-2">
                            <p class=" flex text-nowrap flex-nowrap">S<span class=" hidden sm:block">tatus</span> : </p>
                            <select
                                class=" text-neutral-600 border-neutral-600 w-full text-sm border pl-2 px-8 py-0.5 rounded-full"
                                x-model="status" name="status" id="">
                                <option value="all">All</option>
                                <option value="schedule">Schedule</option>
                                <option value="publish">Publish</option>
                                <option value="private">Private</option>
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class=" flex text-nowrap flex-nowrap">K<span class=" hidden sm:block">ategori</span> :
                            </p>
                            <select
                                class=" text-neutral-600 border-neutral-600 w-full text-sm border pl-2 px-8 py-0.5 rounded-full"
                                x-model="category" name="status" id="">
                                <option value="all">All</option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}">{{ $item->category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class=" flex text-nowrap flex-nowrap">G<span class=" hidden sm:block">uardian</span> :
                            </p>
                            <select
                                class=" text-neutral-600 border-neutral-600 w-full text-sm border pl-2 px-8 py-0.5 rounded-full"
                                x-model="web" name="web" id="">
                                <option value="all">All</option>
                                <option value="main">Main</option>
                                @foreach ($web as $item)
                                    <option value="{{ $item->id }}">{{ $item->url }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <a
                        :href="`{{ preg_replace('#/status/[^/]+/category/[^/]+/web/[^/]+#', '', url()->current()) }}/status/${status}/category/${category}/web/${web}`">
                        <button
                            class=" w-full bg-byolink-1 hover:bg-byolink-3 duration-300 rounded-full text-white px-2 py-0.5 text-sm">Cari</button>
                    </a>
                </div>
            </div>
            <table class="w-full max-w-full text-sm sm:text-base rounded-md">
                <thead>
                    <tr class="h-10 bg-byolink-1 text-white divide-x-2 divide-white">
                        <th class=" w-10 px-2 py-1 rounded-tl-md">No</th>
                        <th class=" px-1 sm:px-2 py-1 relative">Title</th>
                        <th class=" px-1 sm:px-2 py-1 relative hidden md:table-cell">Category</th>
                        <th class=" px-1 sm:px-2 py-1 relative hidden md:table-cell">Author</th>
                        <th class=" px-1 sm:px-2 py-1 relative hidden md:table-cell">Guardian</th>
                        <th class=" px-1 sm:px-2 py-1 w-[90px] sm:w-[100px] rounded-tr-md">Option</th>
                    </tr>
                </thead>
                <tbody id="guardian-container" x-data="tableToggle()">
                    @include('admin.article.row')
                </tbody>
                <tr>
                    <td id="loader" colspan="6" class=" text-center text-neutral-600 h-10">
                        {{$data->count() > 20 ? 'Loading...' : 'Semua data telah dimuat'}}
                    </td>
                </tr>
            </table>
            <script>
                let page = 2;
                let loading = false;

                function tableToggle() {
                    return {
                        openedIds: [],
                        detail(id) {
                            const index = this.openedIds.indexOf(id);
                            if (index === -1) {
                                this.openedIds.push(id); // buka
                            } else {
                                this.openedIds.splice(index, 1); // tutup
                            }
                        }
                    };
                }

                window.addEventListener('scroll', () => {
                    if (loading) return;

                    const loader = document.getElementById('loader');

                    const search = "{!! request('search') ? '&search=' . urlencode(request('search')) : '' !!}";

                    // Scroll benar-benar mentok
                    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight) {
                        loading = true;
                        loader.textContent = 'Loading...';

                        fetch(`?page=${page}${search}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.text())
                            .then(html => {
                                // Tambahkan delay 1 detik sebelum tampilkan data
                                setTimeout(() => {
                                    if (html.trim() !== '') {
                                        document.getElementById('guardian-container').insertAdjacentHTML(
                                            'beforeend', html);
                                        page++;
                                        loading = false;
                                        loader.textContent = 'Loading...';
                                    } else {
                                        loader.textContent = 'Semua data telah dimuat';
                                    }
                                }, 500); // delay 1 detik
                            })
                            .catch(() => {
                                loader.textContent = 'Gagal memuat data';
                                loading = false;
                            });
                    }
                });
            </script>
        </div>
    </div>

    @include('components.admin.component.success')
</x-app-layout>
