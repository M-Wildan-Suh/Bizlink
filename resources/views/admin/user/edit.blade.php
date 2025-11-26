<x-app-layout head="Edit Phone Number" title="Admin - Edit Phone Number">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-4">
        <form action="{{route('user.update', ['user' => $user->id])}}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="w-full p-4 sm:p-8 bg-white rounded-md shadow-md shadow-black/20 flex flex-col gap-6">
                <x-admin.component.textinput title="Username" placeholder="Input Username..." :value="old('name', $user->name)" name="name" />
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Input Email..." value="{{old('email', $user->email)}}" required
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                    </div>
                </div>
                <p class=" font-semibold text-lg pt-2">Ganti Password (opsional)</p>
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="password_old">Old Password</label>
                        <input type="password" id="password_old" name="password_old" placeholder="Input Password Lama..." value="{{old('password_old')}}" 
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="Input Password Baru..." value="{{old('password')}}"
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                    </div>
                </div>
                <div class="w-full">
                    <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" value="{{old('password_confirmation')}}" placeholder="Confirm New Password..."
                            class="text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100">
                    </div>
                </div>
                <x-admin.component.submitbutton title="Save" />
            </div>
        </form>
    </div>

    @include('components.admin.component.validationerror')
</x-app-layout>
