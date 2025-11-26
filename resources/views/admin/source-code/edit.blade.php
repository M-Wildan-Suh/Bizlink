<x-app-layout head="Edit Short Code" title="Admin - Edit Short Code">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <form action="{{route('source-code.update', ['source_code' => $sourceCode->id])}}" method="POST">
            @csrf
            @method('PUT')
            <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
                <x-admin.component.textinput title="Title" placeholder="input Title" :value="$sourceCode->title" name="title" />
                <x-admin.component.taginput title="Content" :value="$sourceCode->tag" name="content[]" />
                <x-admin.component.submitbutton title="Save" />
            </div>
        </form>
    </div>
</x-app-layout>
