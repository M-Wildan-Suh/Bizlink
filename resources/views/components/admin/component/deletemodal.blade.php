@props(['title', 'route'])
<div x-show="deletemodal"
    class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center px-4 z-50">
    <div class="w-full max-w-[720px] bg-white pb-6 rounded-md flex flex-col gap-4 relative overflow-hidden border-2 border-byolink-1">
        <button @click="deletemodal = false"
            class=" absolute top-6 right-6 w-6 h-6 text-white hover:text-red-500 duration-300">
            <svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                enable-background="new 0 0 512 512">
                <path
                    d="M437.5 386.6 306.9 256l130.6-130.6c14.1-14.1 14.1-36.8 0-50.9-14.1-14.1-36.8-14.1-50.9 0L256 205.1 125.4 74.5c-14.1-14.1-36.8-14.1-50.9 0-14.1 14.1-14.1 36.8 0 50.9L205.1 256 74.5 386.6c-14.1 14.1-14.1 36.8 0 50.9 14.1 14.1 36.8 14.1 50.9 0L256 306.9l130.6 130.6c14.1 14.1 36.8 14.1 50.9 0 14-14.1 14-36.9 0-50.9z"
                    fill="currentColor" class="fill-000000"></path>
            </svg>
        </button>
        <div class=" pt-6 pb-3 bg-byolink-1 text-white pr-7">
            <h2 class=" px-6 text-2xl font-bold">Are you sure you want to delete this data?</h2>
        </div>
        <p class="px-6 text-base">You are about to delete the data : {{$title}}</p>
        <div class="flex justify-end space-x-4 px-6">
            {{-- <button @click="confirmDeleteModal = false"
                class="px-4 py-2 bg-neutral-600 duration-300 hover:bg-byolink-1 text-white rounded">Cancel</button> --}}
            <form action="{{$route}}" method="POST"
                class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="px-4 py-2 bg-red-500 duration-300 hover:bg-red-900 text-white rounded">Delete</button>
            </form>
        </div>
    </div>
</div>