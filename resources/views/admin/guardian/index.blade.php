<x-app-layout head="Guardian Web" title="Admin - Guardian Web">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
            <div class="w-full flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class=" w-full md:w-auto flex gap-2">
                    <a href="{{ route('guardian.create') }}">
                        <button
                            class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                            Tambah Web
                        </button>
                    </a>
                    <a href="{{ route('guardian.export') }}" target="__blank">
                        <button
                            class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                            <svg height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M18 22a2 2 0 0 0 2-2v-5l-5 4v-3H8v-2h7v-3l5 4V8l-6-6H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12zM13 4l5 5h-5V4z"/></svg>
                        </button>
                    </a>
                    
                </div>

                <!-- Search -->
                <div class=" w-full md:w-auto flex flex-row font-semibold duration-300">
                    <form action="{{route('guardian.index')}}" class=" w-full">
                        <input type="text" placeholder="Cari Url..." name="search" value="{{urlencode(request('search')) ?? ''}}"
                            class=" w-full text-sm sm:text-base md:w-auto py-2 px-3 border border-byolink-1 rounded-md overflow-hidden focus-within:border-byolink-3 font-normal">
                    </form>
                </div>
            </div>
            <table class="w-full text-sm sm:text-base rounded-md overflow-hidden">
                <thead>
                    <tr class="h-10 bg-byolink-1 text-white divide-x-2 divide-white">
                        <th class=" px-2 py-1 rounded-tl-md w-10">No</th>
                        <th class=" px-1 sm:px-2 py-1">Url</th>
                        <th class=" px-1 sm:px-2 py-1 min-w-10 hidden sm:table-cell">Template</th>
                        <th class=" px-1 sm:px-2 py-1 min-w-10 hidden sm:table-cell">
                            <div class=" flex justify-center">
                                S<span class=" hidden sm:block">pintax</span>
                            </div>
                        </th>
                        <th class=" px-1 sm:px-2 py-1 min-w-10 hidden sm:table-cell">
                            <div class=" flex justify-center">
                                U<span class=" hidden sm:block">nique</span>
                            </div>
                        </th>
                        <th class=" px-1 sm:px-2 py-1 w-[90px] sm:w-[100px] rounded-tr-md">Opsi</th>
                    </tr>
                </thead>
                <tbody id="guardian-container" x-data="tableToggle()">
                    @include('admin.guardian.row')
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
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(response => response.text())
                        .then(html => {
                            // Tambahkan delay 1 detik sebelum tampilkan data
                            setTimeout(() => {
                                if (html.trim() !== '') {
                                    document.getElementById('guardian-container').insertAdjacentHTML('beforeend', html);
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
    @include('components.admin.component.validationerror')
</x-app-layout>
