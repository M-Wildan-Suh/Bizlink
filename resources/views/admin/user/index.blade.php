<x-app-layout head="User" title="Admin - User">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
            <div class="w-full flex flex-col md:flex-row gap-4 justify-between items-center">
                <div class=" flex w-full md:w-auto">
                    <a href="{{ route('user.create') }}"
                        class=" text-nowrap w-full text-center text-sm sm:text-base md:w-auto px-4 py-2 bg-byolink-1 text-white rounded-md font-semibold border border-byolink-1 hover:border-byolink-3 hover:bg-byolink-3 duration-300">
                        Tambah User
                    </a>
                </div>

                <!-- Search -->
                <div class=" w-full md:w-auto flex flex-row font-semibold duration-300">
                    <form action="{{route('user.index')}}" class=" w-full">
                        <input type="text" placeholder="Cari Username..." name="search" value="{{urlencode(request('search')) ?? ''}}"
                            class=" w-full text-sm sm:text-base md:w-auto py-2 px-3 border border-byolink-1 rounded-md overflow-hidden focus-within:border-byolink-3 font-normal">
                    </form>
                </div>
            </div>
            <table class="w-full text-sm sm:text-base rounded-md overflow-hidden">
                <thead>
                    <tr class="h-10 bg-byolink-1 text-white divide-x-2 divide-white">
                        <th class=" px-2 py-1 rounded-tl-md w-10">No</th>
                        <th class=" px-1 sm:px-2 py-1">Username</th>
                        <th class=" px-1 sm:px-2 py-1 hidden sm:table-cell">Email</th>
                        <th class=" px-1 sm:px-2 py-1 w-[90px] sm:w-[100px] rounded-tr-md">Opsi</th>
                    </tr>
                </thead>
                @forelse ($data as $item)
                    <tbody>
                        <tr class="{{ $loop->even ? 'bg-neutral-100' : 'bg-neutral-200' }} h-10 text-neutral-600 divide-x-2 divide-white">
                            <td class="px-3 py-1 text-center font-semibold">{{ $loop->iteration }}</td>
                            <td class="px-2 sm:px-4 py-1 min-h-10 font-semibold text-nowrap">{{$item->name}}</td>
                            <td class="px-2 sm:px-4 py-1 min-h-10 text-nowrap hidden sm:table-cell">{{$item->email}}</td>
                            <td class="px-1 sm:px-2">
                                <div class="flex gap-1 sm:gap-2 justify-center">
                                    <a href="{{ route('user.show', ['user' => $item->id]) }}" class="w-5 h-5 hover:text-green-500 duration-300">
                                        <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 17.75A3.25 3.25 0 0 0 6.25 21h4.915l.356-1.423c.162-.648.497-1.24.97-1.712l5.902-5.903a3.279 3.279 0 0 1 2.607-.95V6.25A3.25 3.25 0 0 0 17.75 3H11v4.75A3.25 3.25 0 0 1 7.75 11H3v6.75ZM9.5 3.44 3.44 9.5h4.31A1.75 1.75 0 0 0 9.5 7.75V3.44Zm9.6 9.23-5.903 5.902a2.686 2.686 0 0 0-.706 1.247l-.458 1.831a1.087 1.087 0 0 0 1.319 1.318l1.83-.457a2.685 2.685 0 0 0 1.248-.707l5.902-5.902A2.286 2.286 0 0 0 19.1 12.67Z"
                                                fill="currentColor"></path>
                                        </svg>
                                    </a>
                                    {{-- Delete --}}
                                    <div x-data="{ deletemodal:false }">
                                        <button @click="deletemodal = !deletemodal"
                                            class=" w-4 sm:w-5 aspect-square hover:text-red-500 duration-300">
                                            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z"
                                                    fill="currentColor" class="fill-000000"></path>
                                            </svg>
                                        </button>
                                        <x-admin.component.deletemodal :title="$item->name" :route="route('user.destroy', ['user' => $item->id])"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    @empty
                    <tr>
                        <td colspan="4" class=" bg-neutral-100 px-1 sm:px-2 py-1 text-center rounded-b-md text-neutral-600">Data tidak ditemukan</td>
                    </tr>
                @endforelse
            </table>
            {{ $data->links('vendor.pagination.admin') }}
        </div>
    </div>
    @include('components.admin.component.validationerror')
</x-app-layout>
