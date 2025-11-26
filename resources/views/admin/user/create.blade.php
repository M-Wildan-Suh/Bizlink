<x-app-layout head="Create Phone Number" title="Admin - Create Phone Number">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <form action="{{route('user.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
                <x-admin.component.textinput title="Username" placeholder="Input Username..." value="{{old('name')}}" name="name" />
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Input Email..." required value="{{old('email')}}"
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Input Password..." value="{{old('password')}}" required 
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" value="{{old('password_confirmation')}}" placeholder="Confirm Password..." required 
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                    </div>
                </div>
                <x-admin.component.submitbutton title="Save" />
            </div>
        </form>
    </div>

    @include('components.admin.component.validationerror')
</x-app-layout>
