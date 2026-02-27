@props(['columns', 'data', 'create', 'edit', 'delete'])
<div x-data="{ showModal: false, deleteProductId: null }" class="px-4">
    {{-- Data --}}
    <div class="w-full bg-white rounded-md shadow-md shadow-black/20">
    <div x-data="tableData({{ $data->toJson() }})" class=" w-full p-4 space-y-4 mx-auto rounded-lg">
        <div class="grid grid-cols-2 sm:flex sm: flex-row justify-between w-full gap-2">
            <a href="{{$create}}">
                <button class="text-sm w-full py-2 px-3 rounded-md bg-[#DB9F24] text-white font-semibold hover:bg-black duration-300">Tambah</button>
            </a>
            <input type="text" x-model="search" placeholder="Search..." class="w-full sm:max-w-[30%] py-2 text-sm rounded-md border border-[#DB9F24] focus:ring-black focus:border-black bg-neutral-100">
        </div>
        <table class="w-full divide-y divide-black rounded-md max-w-full overflow-hidden">
            <thead class="bg-[#DB9F24] text-white">
                <tr>
                    @foreach($columns as $column)
                        <th @if($column['key'] !== 'actions') @click="sortBy('{{ $column['key'] }}')" @endif class="py-3 text-center text-xs font-medium uppercase cursor-pointer">
                            {{ $column['label'] }}
                            <span x-show="sortColumn === '{{ $column['key'] }}' && sortOrder === 'asc'">&#9650;</span>
                            <span x-show="sortColumn === '{{ $column['key'] }}' && sortOrder === 'desc'">&#9660;</span>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
            <template x-for="product in filteredData" :key="product.id">
                <tr>
                    @foreach($columns as $column)
                        @if($column['key'] === 'actions')
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                <div class="flex flex-row gap-2 justify-center">
                                    <a :href="{{$edit}}" aria-label="Edit data" class="text-blue-500 hover:text-blue-700">
                                        <div class="w-3 aspect-square">
                                            <svg viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M0 14.2V18h3.8l11-11.1L11 3.1 0 14.2ZM17.7 4c.4-.4.4-1 0-1.4L15.4.3c-.4-.4-1-.4-1.4 0l-1.8 1.8L16 5.9 17.7 4Z" fill="currentColor" fill-rule="evenodd" class="fill-000000"></path>
                                            </svg>
                                        </div>
                                    </a>                                    
                                    <button @click.prevent="showModal = true; deleteProductId = product.id" class="text-red-500 hover:text-red-700 w-3 aspect-square">
                                        <svg viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"><path d="M18.9 8H5.1c-.6 0-1.1.5-1 1.1l1.6 13.1c.1 1 1 1.7 2 1.7h8.5c1 0 1.9-.7 2-1.7l1.6-13.1c.1-.6-.3-1.1-.9-1.1zM20 2h-5c0-1.1-.9-2-2-2h-2C9.9 0 9 .9 9 2H4c-1.1 0-2 .9-2 2v1c0 .6.4 1 1 1h18c.6 0 1-.4 1-1V4c0-1.1-.9-2-2-2z" fill="currentColor" class="fill-000000"></path></svg>
                                    </button>
                                </div>
                            </td>
                        @else
                            <td class="px-2 py-4 whitespace-nowrap text-sm text-center text-gray-500 text-wrap" x-text="product['{{ $column['key'] }}']"></td>
                        @endif
                    @endforeach
                </tr>
            </template>
        </tbody>
        </table>
    </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="showModal" 
        class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 px-4" 
        x-transition:enter="transition ease-out duration-300" 
        x-transition:enter-start="opacity-0" 
        x-transition:enter-end="opacity-100" 
        x-transition:leave="transition ease-in duration-300" 
        x-transition:leave-start="opacity-100" 
        x-transition:leave-end="opacity-0">
    
        <div @click.outside="showModal = false" class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4">Konfirmasi Penghapusan</h2>
            <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus Data ini?</p>
            <div class="flex justify-end">
                <button @click="showModal = false" 
                        class="bg-black text-white py-2 px-4 rounded-md hover:bg-gray-400 mr-2 duration-300">
                    Batal
                </button>
                <form :action="{{$delete}}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-[#db2a24] text-white py-2 px-4 rounded-md hover:bg-black duration-300">
                        Hapus
                    </button>
                </form>            
            </div>
        </div>
    </div>

    <script>
        function tableData(initialData) {
            return {
                sortColumn: 'product',
                sortOrder: 'asc',
                search: '',
                data: initialData,
                get columns() {
                    // Extract column keys from the data's first item
                    return Object.keys(this.data[0] || {});
                },
                get filteredData() {
                    const searchTerm = this.search.toLowerCase();
                    return this.data.filter(product => {
                        // Check if any column contains the search term
                        return this.columns.some(key => {
                            const value = product[key]?.toString().toLowerCase() || '';
                            return value.includes(searchTerm);
                        });
                    }).slice().sort((a, b) => {
                        const modifier = this.sortOrder === 'asc' ? 1 : -1;
                        if (a[this.sortColumn] < b[this.sortColumn]) return -1 * modifier;
                        if (a[this.sortColumn] > b[this.sortColumn]) return 1 * modifier;
                        return 0;
                    });
                },
                sortBy(column) {
                    if (this.sortColumn === column) {
                        this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortOrder = 'asc';
                    }
                }
            };
        }

    </script>
</div>
