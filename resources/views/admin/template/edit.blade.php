<x-app-layout head="Edit Template" title="Admin - Edit Template">
    <div class="sm:pl-12 sm:pr-12 lg:pr-32 duration-300 pt-8 pb-20 sm:pb-8 px-4 space-y-6">
        <div class="w-full rounded-md shadow-md shadow-black/20 relative overflow-hidden">
            <div id="background" class=" absolute inset-0 flex items-center justify-center">
                <style>
                    #background {
                        @if (old('bg_type', $template->bg_type ?? null) === "normal")
                            background-color: {{old('bg_normal_color', $template->bg_main_color ?? null)}};
                        @elseif (old('bg_type', $template->bg_type ?? null) === "gradient")
                            background : linear-gradient(to bottom, {{old('bg_main_color',$template->bg_main_color ?? null)}}, {{old('bg_second_color',$template->bg_second_color ?? null)}});
                        @endif
                    };
                </style>
                <img style="display: {{old('bg_type', $template->bg_type ?? null) === "image" ? "block" : "none"}}" id="bg_image-now" src="{{asset('storage/images/template/background/'.$template->bg_image)}}" class=" w-full h-full object-cover object-center" alt="">
            </div>
            <form action="{{ route('template.update', ['template' => $template->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('components.admin.template.background')
                <div class="relative w-full p-4 sm:p-6 bg-white">
                    <x-admin.component.textinput title="Name" placeholder="Input Template Name..." :value="old('name', $template->name)" name="name" />
                </div>
                <div class=" space-y-4 sm:space-y-6 relative p-4 sm:p-6">
                    <div class=" w-full">
                        <div class="w-full flex items-center justify-center">
                            <div class=" w-[400px] aspect-[3/2] max-h-full max-w-full rounded-md overflow-hidden shadow-md shadow-black/20 relative">
                                @include('components.admin.template.header')
                                <div class=" w-full">
                                    <img id="header" src="{{asset('assets/images/template/header/'.old('header', $template->head_type).'.jpg')}}" class=" w-full" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" w-full">
                        <div class="w-full flex items-center justify-center">
                            <div class=" w-[400px] rounded-md overflow-hidden relative">
                                @include('components.admin.template.gallery')
                                <div class=" w-full">
                                    <img id="gallery" src="{{asset('assets/images/template/gallery/'.old('gallery', $template->gallery_type).'.png')}}" class=" w-full" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" w-full">
                        <div class=" w-full flex items-center justify-center">
                            <div id="desc" style="background-color: {{old('desc_main_color', $template->desc_main_color) ?? ''}}; color: {{old('desc_text_color', $template->desc_text_color) ?? ''}}" class=" max-w-[400px] w-full rounded-md shadow-md p-4 space-y-2 overflow-hidden relative">
                                @include('components.admin.template.article')
                                <div class=" w-full flex flex-wrap gap-2">
                                    <a href="{{route('category', ['category' => 'makam-svargaloka'])}}">
                                        <button id="tagdesc" style="background-color: {{old('desc_second_color', $template->desc_second_color) ?? ''}}" class=" px-2 py-1 text-xs text-white rounded-md">Makam Svargaloka</button>
                                    </a>
                                </div>
                                <p class="text-lg font-bold">Biaya Pemakaman Premium Dekat Di Jogja Svargaloka Memorial Garden</p>
                                <div class=" flex gap-4 items-center text-sm">
                                    <div class=" flex gap-1.5 items-center">
                                        <div id="descicon" style="color: {{old('desc_second_color', $template->desc_second_color) ?? ''}}" class=" w-4 aspect-square">
                                            <svg class="feather feather-clock" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                                        </div>
                                        <p class=" opacity-80">20:22</p>
                                    </div>
                                    <div class=" flex gap-1.5 items-center">
                                        <div id="descicon" style="color: {{old('desc_second_color', $template->desc_second_color) ?? ''}}" class=" w-4 aspect-square">
                                            <svg class="feather feather-calendar" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><rect height="18" rx="2" ry="2" width="18" x="3" y="4"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                                        </div>
                                        <p class=" opacity-80">25 Januari 2025</p>
                                    </div>
                                </div>
                                <div class=" article text-sm">
                                    <p>
                                        <a href="">Svargaloka</a> – Ada sebagai opsi untuk Bapak/Ibu yang menginginkan tempat atau tanah makam yang premium serta mewah di daerah Yogyakarta. Kami memahami bahwa memberi rasa kasih sayang terhadap keluarga tercinta dibutuhkan suasana yang tenang, indah, dan berkesan.
                                        <br>
                                        <br>
                                        Oleh sebab itu, Svargaloka menawari tanah pemakaman mewah dengan biaya yang transparan, dirancang untuk menciptakan suasana yang berbeda dan penuh makna.
                                        <br>
                                        <br>
                                        #svargaloka #tanah makam mewah
                                    </p>
                                </div>
                                <style>
                                    .article a {
                                        font-weight: 700;
                                        color: {{old('desc_second_color', $template->desc_second_color) ?? ''}};
                                    }
                                </style>
                            </div>
                        </div>
                    </div>
                    <div class=" w-full">
                        <div class="w-full flex items-center justify-center">
                            <div class=" w-[400px] aspect-video max-h-full max-w-full rounded-md overflow-hidden shadow-md shadow-black/20 relative">
                                <img src="{{asset('assets/images/ytplaceholder.jpg')}}" class=" w-full h-full object-cover" alt="">
                                <div class=" absolute inset-0 flex items-center justify-center bg-black/10">
                                    <div class=" w-10">
                                        <svg viewBox="0 0 30 30" xmlns="http://www.w3.org/2000/svg"><g data-name="Layer 2"><path d="M15 2.5A12.5 12.5 0 1 0 27.5 15 12.514 12.514 0 0 0 15 2.5Zm4.968 14.14-5.647 3.942a2 2 0 0 1-3.144-1.64v-7.883a2 2 0 0 1 3.144-1.641l5.647 3.941a2 2 0 0 1 0 3.28Z" fill="none"></path><path d="M15 0a15 15 0 1 0 15 15A15.016 15.016 0 0 0 15 0Zm0 27.5A12.5 12.5 0 1 1 27.5 15 12.514 12.514 0 0 1 15 27.5Z" fill="#ffffff" class="fill-000000"></path><path d="M19.968 13.36 14.32 9.417a2 2 0 0 0-3.144 1.64v7.883a2 2 0 0 0 3.144 1.641l5.647-3.941v-.001a2 2 0 0 0 0-3.28Z" fill="#ffffff" class="fill-000000"></path></g></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=" w-full">
                        <div class="w-full flex items-center justify-center">
                            <div class=" w-full max-w-[400px] relative">
                                @include('components.admin.template.contact')
                                <div class=" w-full grid grid-cols-2 gap-2 text-sm">
                                    <button id="phone" type="button" style="background-color: {{old('contact_main_color', $template->contact_main_color ?? null) ?? '#1d588d'}}" class=" w-full flex items-center justify-center gap-0.5 py-2 text-white rounded-full">
                                        <div class=" w-4 aspect-square">
                                            <svg viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h256v256H0z"></path><path d="M92.5 124.8a83.6 83.6 0 0 0 39 38.9 8 8 0 0 0 7.9-.6l25-16.7a7.9 7.9 0 0 1 7.6-.7l46.8 20.1a7.9 7.9 0 0 1 4.8 8.3A48 48 0 0 1 176 216 136 136 0 0 1 40 80a48 48 0 0 1 41.9-47.6 7.9 7.9 0 0 1 8.3 4.8l20.1 46.9a8 8 0 0 1-.6 7.5L93 117a8 8 0 0 0-.5 7.8Z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="16" class="stroke-000000"></path></svg>
                                        </div>
                                        <p>Telephone</p>
                                    </button>
                                    <button id="wa" type="button" style="background-color: {{old('contact_second_color', $template->contact_second_color ?? null) ?? '#25d366'}}" class=" w-full flex items-center justify-center gap-0.5 py-2 text-white rounded-full">
                                        <div class=" w-4 aspect-square">
                                            <svg viewBox="0 0 56.693 56.693" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 56.693 56.693"><path d="M46.38 10.714C41.73 6.057 35.544 3.492 28.954 3.489c-13.579 0-24.63 11.05-24.636 24.633a24.589 24.589 0 0 0 3.289 12.316L4.112 53.204l13.06-3.426a24.614 24.614 0 0 0 11.772 2.999h.01c13.577 0 24.63-11.052 24.635-24.635.002-6.582-2.558-12.772-7.209-17.428zM28.954 48.616h-.009a20.445 20.445 0 0 1-10.421-2.854l-.748-.444-7.75 2.033 2.07-7.555-.488-.775a20.427 20.427 0 0 1-3.13-10.897c.004-11.29 9.19-20.474 20.484-20.474a20.336 20.336 0 0 1 14.476 6.005 20.352 20.352 0 0 1 5.991 14.485c-.004 11.29-9.19 20.476-20.475 20.476z" fill-rule="evenodd" clip-rule="evenodd" fill="currentColor" class="fill-000000"></path><path d="M40.185 33.281c-.615-.308-3.642-1.797-4.206-2.003-.564-.205-.975-.308-1.385.308-.41.617-1.59 2.003-1.949 2.414-.359.41-.718.462-1.334.154-.615-.308-2.599-.958-4.95-3.055-1.83-1.632-3.065-3.648-3.424-4.264-.36-.617-.038-.95.27-1.257.277-.276.615-.719.923-1.078.308-.36.41-.616.616-1.027.205-.41.102-.77-.052-1.078-.153-.308-1.384-3.338-1.897-4.57-.5-1.2-1.008-1.038-1.385-1.057-.359-.018-.77-.022-1.18-.022s-1.077.154-1.642.77c-.564.616-2.154 2.106-2.154 5.135 0 3.03 2.206 5.957 2.513 6.368.308.41 4.341 6.628 10.516 9.294a35.341 35.341 0 0 0 3.509 1.297c1.474.469 2.816.402 3.877.244 1.183-.177 3.642-1.49 4.155-2.927.513-1.438.513-2.67.359-2.927-.154-.257-.564-.41-1.18-.719z" fill-rule="evenodd" clip-rule="evenodd" fill="currentColor" class="fill-000000"></path></svg>
                                        </div>
                                        <p>WhatsApp</p>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative w-full p-4 sm:p-6 bg-white">
                    <x-admin.component.submitbutton title="Edit" />
                </div>
            </form>
        </div>
    </div>

    @include('components.admin.component.validationerror')
</x-app-layout>