@props(['link' => null, 'head' => null, 'form' => null, 'title' => null, 'spintax' => null])
<x-app-layout :head="$head" :title="$title">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-6">
        <div class=" w-full sm:py-4 p-4 sm:p-6 bg-white rounded-md shadow-md shadow-black/20">
            <div class=" flex items-center justify-between">
                <div class=" flex items-center gap-2 text-sm sm:text-base">
                    <a href="{{route('article.index')}}" class=" text-byolink-1 hover:text-byolink-3 duration-300">Article</a>
                    <p class=" text-neutral-600">/</p>
                    <p class=" text-neutral-600">{{$head}}</p>
                </div>
                <div class=" flex gap-4">
                    @if ($spintax)
                        <a href="{{$spintax}}" class=" flex gap-1 text-sm sm:text-base text-byolink-1 hover:text-byolink-3 duration-300" target="__blank">List <span class=" hidden sm:block">Article</span></a>
                    @endif
                    @if ($link)
                        <a href="{{$link}}" class=" flex gap-1 text-sm sm:text-base text-byolink-1 hover:text-byolink-3 duration-300" target="__blank">Open <span class=" hidden sm:block">Article</span></a>
                    @endif
                </div>
            </div>
        </div>
        <div class=" w-full p-4 sm:p-6 bg-white rounded-md shadow-md shadow-black/20">
            <form action="{{$form}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div x-data="{tab : 'content'}" class="space-y-4 sm:space-y-6">
                    <div class=" w-full grid grid-cols-3 gap-4 text-sm sm:text-base">
                        <button @click="tab ='content'" type="button" :class="tab === 'content' ? 'border-byolink-1 text-byolink-1' : 'text-neutral-500 border-neutral-400 hover:text-black hover:border-black'" class=" w-full duration-300 border-b-2 pb-2 sm:pb-4 font-bold">Konten</button>
                        <button @click="tab ='image'" type="button" :class="tab === 'image' ? 'border-byolink-1 text-byolink-1' : 'text-neutral-500 border-neutral-400 hover:text-black hover:border-black'" class=" w-full duration-300 border-b-2 pb-2 sm:pb-4 font-bold">Image</button>
                        <button @click="tab ='template'" type="button" :class="tab === 'template' ? 'border-byolink-1 text-byolink-1' : 'text-neutral-500 border-neutral-400 hover:text-black hover:border-black'" class=" w-full duration-300 border-b-2 pb-2 sm:pb-4 font-bold">Template</button>
                    </div>
                    <div x-show="tab === 'content'" class=" space-y-4 sm:space-y-6">
                        {{$slot}}
                        <div class=" w-full">
                            <button @click="tab ='image'" type="button" class="w-full py-3 text-sm sm:text-base rounded-md bg-byolink-1 text-white font-semibold hover:bg-byolink-3 duration-300">Next</button>
                        </div>
                    </div>
                    <div x-show="tab === 'image'" class=" space-y-4 sm:space-y-6">
                        {{$additional ?? ''}}
                        <div class=" w-full grid grid-cols-2 gap-4">
                            <button @click="tab ='content'" type="button" class="w-full py-3 text-sm sm:text-base rounded-md bg-byolink-1 text-white font-semibold hover:bg-byolink-3 duration-300">Back</button>
                            <button @click="tab ='template'" type="button" class="w-full py-3 text-sm sm:text-base rounded-md bg-byolink-1 text-white font-semibold hover:bg-byolink-3 duration-300">Next</button>
                        </div>
                    </div>
                    <div x-show="tab === 'template'" class=" space-y-4 sm:space-y-6">
                        {{$template ?? ''}}
                        <div class=" w-full grid grid-cols-2 gap-4">
                            <button @click="tab ='image'" type="button" class="w-full py-3 text-sm sm:text-base rounded-md bg-byolink-1 text-white font-semibold hover:bg-byolink-3 duration-300">Back</button>
                            <x-admin.component.submitbutton title="Save" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>