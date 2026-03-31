<x-admin.article.form head="Edit Article Generated" title="Admin - Edit Article Generated" :link="route('business', ['slug' => $articleShow->slug])" :form="route('article-generated.update', ['article_generated' => $articleShow->id])">
    @method('PUT')
    <x-admin.component.textinput title="Judul" placeholder="Masukkan Judul" :value="old('judul', $articleShow->judul)" name="judul" />
    <x-admin.component.summernoteinput title="Artikel" :value="old('article', $articleShow->article)" name="article" />
    <div class=" grid grid-cols-2 gap-4">
        <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
            <label>Telephone</label>
            <div class=" w-full grid grid-cols-2 gap-4">
                <div class=" w-full">
                    <input type="radio" name="tlp" value="1" id="tlp_on" class="hidden peer" checked>
                    <label for="tlp_on" class=" w-full cursor-pointer flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">On</label>
                </div>
                
                <div class=" w-full">
                    <input type="radio" name="tlp" value="0" id="tlp_off" class="hidden peer" {{old('tlp', $articleShow->telephone) === '0' ? 'checked' : ''}}>
                    <label for="tlp_off" class=" w-full cursor-pointer flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">Off</label>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 text-sm sm:text-base font-medium">
            <label>WhatsApp</label>
            <div class=" w-full grid grid-cols-2 gap-4">
                <div class=" w-full">
                    <input type="radio" name="wa" value="1" id="wa_on" class="hidden peer" checked>
                    <label for="wa_on" class=" w-full cursor-pointer flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">On</label>
                </div>
                
                <div class=" w-full">
                    <input type="radio" name="wa" value="0" id="wa_off" class="hidden peer" {{old('wa', $articleShow->whatsapp) === '0' ? 'checked' : ''}}>
                    <label for="wa_off" class=" w-full cursor-pointer flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">Off</label>
                </div>
            </div>
        </div>
    </div>
    <x-admin.component.nochoseinput title="Phone Number (optional)" :phone="$phonenumber" :value="old('no_tlp', $articleShow->phoneNumber->no_tlp ?? null)" name="no_tlp" />
    <x-slot:additional>
        <div class=" w-full">
            <div class=" w-full flex flex-col max-w-full gap-2 text-sm sm:text-base font-semibold">
                <label>Banner</label>
                <div class="w-full grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-4">
                    @foreach ($article->articlebanner as $item)
                        <label class="w-full rounded-md bg-white aspect-square overflow-hidden relative">
                            <input type="radio" name="banner" value="{{$item->image}}" class="hidden peer" {{ old('banner', $articleShow->banner) === $item->image ? 'checked' : '' }}>
                            <img src="{{asset('storage/images/article/banner/'.$item->image)}}" class=" w-full h-full object-cover object-top" alt="">
                            <div class=" absolute inset-0 peer-checked:bg-black/50 duration-300">
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class=" w-full">
            <div class=" w-full flex flex-col max-w-full gap-2 text-sm sm:text-base font-semibold">
                <label class="text-sm sm:text-base font-semibold" for="image_gallery">Galeri (Max 6)</label>
                <div class="w-full grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-4" id="gallery-container">
                    @foreach ($article->articlegallery as $item)
                        <label class="w-full rounded-md bg-white aspect-square overflow-hidden relative">
                            <input type="checkbox" name="gallery[]" value="{{$item->id}}" class="hidden gallery-checkbox peer" {{ is_array(old('gallery')) ? (in_array($item->id, old('gallery')) ? 'checked' : '') : ($articleShow->articleshowgallery->contains('article_gallery_id', $item->id) ? 'checked' : '') }}>
                            <img src="{{asset('storage/images/article/gallery/'.$item->image)}}" class=" w-full h-full object-cover object-top" alt="">
                            <div class=" absolute inset-0 peer-checked:bg-black/50 duration-300">
                            </div>
                        </label>
                    @endforeach
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const max = 6;
                            const checkboxes = document.querySelectorAll('.gallery-checkbox');
                        
                            checkboxes.forEach(cb => {
                                cb.addEventListener('change', () => {
                                    const checked = document.querySelectorAll('.gallery-checkbox:checked').length;
                                    checkboxes.forEach(box => {
                                        box.disabled = !box.checked && checked >= max;
                                    });
                                });
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
        <x-admin.component.linkinput title="Video (Link Youtube/Tiktok) (Optional)" placeholder="Masukkan link..." value="{{ old('link',  ($articleShow->articles->video_type === 'youtube') ? $articleShow->articles->youtube : (($articleShow->articles->video_type === 'tiktok') ? $articleShow->articles->tiktok : '')) }}" name="link" link="Url" />
    </x-slot:additional>
    @include('components.admin.component.success')
    @include('components.admin.component.validationerror')
</x-admin.article.form>
