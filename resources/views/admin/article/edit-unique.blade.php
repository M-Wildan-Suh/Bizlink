<x-admin.article.form head="Edit Article Unique" title="Admin - Edit Article Unique" :link="route('business', ['slug' => $articleShow->slug])" :form="route('article-show.update', ['article_show' => $articleShow->id])">
    @method('PUT')
    <x-admin.component.textinput title="Judul" placeholder="Masukkan Judul" :value=" old('judul', $articleShow->judul)" name="judul" />
    <x-admin.component.categoryinput title="Kategori" :tag="$category" :value="old('category', $articleShow->articles->articlecategory)" name="category[]" />
    <x-admin.component.taginput title="Tag" :tag="$tag" :value="old('tag', $articleShow->articles->articletag)" name="tag[]" />
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
                    <input type="radio" name="tlp" value="0" id="tlp_off" class="hidden peer" {{ old('tlp', $articleShow->telephone) == '0' ? 'checked' : ''}}>
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
                    <input type="radio" name="wa" value="0" id="wa_off" class="hidden peer" {{ old('wa', $articleShow->whatsapp) == '0' ? 'checked' : ''}}>
                    <label for="wa_off" class=" w-full cursor-pointer flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">Off</label>
                </div>
            </div>
        </div>
    </div>
    <x-admin.component.nochoseinput title="Phone Number (optional)" :phone="$phonenumber" :value="old('no_tlp', $articleShow->phoneNumber->no_tlp ?? null)" name="no_tlp" />

    @if (Auth::user()->role === 'admin')    
        <div class="flex flex-col gap-2">
            <label class="font-medium text-sm sm:text-base">Pilih Web (optional)</label>
            <select class="guardianweb" name="guardian" multiple="multiple">
                @foreach($guardian as $item)
                    <option value="{{ $item->id }}" {{ old('guardian', $articleShow->articles->guardian_web_id) === $item->id ? 'selected' : '' }}>{{ $item->url }}</option>
                @endforeach
            </select>
        </div>
        <script>
            window.addEventListener('load', function select2() {
                var $j = jQuery.noConflict();
                $j(document).ready(function() {
                    $j('.guardianweb').select2({
                        maximumSelectionLength: 1,
                        language: {
                            maximumSelected: function (args) {
                                return "Hanya bisa memilih satu saja";
                            }
                        }
                    });
                });
            });
        </script>
    @endif
        
    <div class=" w-full relative pt-10 sm:pt-11">
        <div class=" w-full">
            <input type="radio" name="status" value="publish" id="publish" class="hidden peer" checked>
            <label for="publish" class=" absolute {{$articleShow->status === 'schedule' ? 'w-[calc(33%-8px)]' : 'w-1/2'}} cursor-pointer left-0 top-0 flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">Publish</label>
        </div>

        @if ($articleShow->status === 'schedule')
            <div class=" w-full">
                <input type="radio" name="status" value="schedule" id="schedule" class="hidden peer" {{old('status', $articleShow->status) === 'schedule' ? 'checked' : ''}}>
                <label for="schedule" class=" absolute w-[calc(33%-8px)] cursor-pointer left-1/2 -translate-x-1/2 top-0 flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">Schedule</label>
                <div class="peer-checked:block hidden mt-4">
                    <input type="date" class=" w-full text-sm sm:text-base font-normal rounded-md border border-byolink-1 focus:ring-byolink-3 focus:border-byolink-3 bg-neutral-100" name="release" min="{{ date('Y-m-d') }}" value="{{ $articleShow->created_at->format('Y-m-d') }}" id="">
                </div>
            </div>
        @endif
        
        <div class=" w-full">
            <input type="radio" name="status" value="private" id="private" class="hidden peer" {{old('status', $articleShow->status) === 'private' ? 'checked' : ''}}>
            <label for="private" class=" absolute {{$articleShow->status === 'schedule' ? 'w-[calc(33%-8px)]' : 'w-1/2'}} cursor-pointer right-0 top-0 flex justify-center p-2 text-sm sm:text-base text-center font-medium rounded-md duration-300 peer-checked:bg-byolink-1 peer-checked:text-white">Private</label>
        </div>
    </div>
    <x-slot:additional>
        <div class=" w-full">
            <div class=" w-full flex flex-col max-w-full gap-2 text-sm sm:text-base font-semibold">
                <label>Banner</label>
                <div class="w-full sm:h-52 flex items-center justify-center overflow-hidden">
                    <div class=" aspect-[3/2] max-h-full max-w-full rounded-md overflow-hidden shadow-md shadow-black/20 ">
                        <x-admin.component.imageinput value="{{ $articleShow->banner ? asset('storage/images/article/banner/'.$articleShow->banner) : null}}" name="image" />
                    </div>
                </div>
            </div>
        </div>
        <div x-data="galleryComponent({{ $articleShow->articleshowgallery }}, {{ $articleShow->id }}, {{ $articleShow->article_id }})" class="flex flex-col gap-2">
            <label class="text-sm sm:text-base font-semibold" for="image_gallery">Galeri (Max 6)</label>
            <input type="file" class="hidden" id="image_gallery" name="image_gallery[]" multiple accept="image/*" @change="addImages($event)">
            <div class="w-full grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-4">
                <template x-for="(image, index) in images" :key="index">
                    <div class="w-full aspect-square flex items-center rounded-md relative overflow-hidden shadow-md shadow-black/20">
                        
                        <!-- Jika sedang loading -->
                        <template x-if="image.loading">
                            <div class="w-full h-full flex justify-center items-center bg-neutral-200">
                                <svg class="animate-spin w-8 h-8 text-byolink-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                            </div>
                        </template>
        
                        <!-- Jika sudah ada gambar -->
                        <template x-if="!image.loading">
                            <img :src="image.url" class="w-full h-full object-cover" alt="Gallery Image Preview">
                        </template>
        
                        <!-- Delete Image (hanya jika bukan loading) -->
                        <template x-if="!image.loading">
                            <label @click="deleteImage(index)" class="w-full text-transparent h-full absolute top-0 left-0 flex justify-center items-center p-[35%] hover:bg-black/60 hover:text-white/50 duration-300 cursor-pointer">
                                <svg viewBox="0 0 24 24" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"><path d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z" fill="currentColor"></path></svg>
                            </label>
                        </template>
                    </div>
                </template>
        
                <!-- Add Image -->
                <template x-if="images.length < 6">
                    <div for="image_gallery" class="w-full aspect-square border bg-neutral-100 border-byolink-1 rounded-md relative border-dashed overflow-hidden">
                        <label for="image_gallery" class="w-full text-byolink-1 h-full absolute top-0 left-0 flex justify-center items-center p-[35%] hover:bg-byolink-3 hover:text-white/50 duration-300 cursor-pointer">
                            <svg viewBox="0 0 24 24" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"><path d="m9 13 3-4 3 4.5V12h4V5c0-1.103-.897-2-2-2H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h8v-4H5l3-4 1 2z" fill="currentColor"></path><path d="M19 14h-2v3h-3v2h3v3h2v-3h3v-2h-3z" fill="currentColor"></path></svg>
                        </label>
                    </div>
                </template>
            </div>
        
            <p x-show="errorMessage" class="text-red-500" x-text="errorMessage"></p>
        </div>
        
        <script>
            function galleryComponent(initialImages = [], articleshowid, articleid) {
                return {
                    images: initialImages.map(item => ({
                        id: item.id,
                        article_gallery_id: item.article_gallery_id,
                        url: item.image ? `{{ asset('storage/images/article/gallery/') }}/${item.image}` : `{{ asset('assets/images/placeholder.png') }}`,
                        loading: false
                    })),
                    errorMessage: '',
                    addImages(event) {
                        const files = Array.from(event.target.files);
                        if (this.images.length + files.length > 6) {
                            this.errorMessage = 'You can only upload up to 6 images.';
                            return;
                        }
                        this.errorMessage = '';
        
                        files.forEach(file => {
                            // tambahkan dummy loading box
                            const tempIndex = this.images.push({ url: '', loading: true }) - 1;
        
                            const formData = new FormData();
                            formData.append('image_gallery', file);
                            formData.append('article_id', articleid);
                            formData.append('article_show_id', articleshowid);
        
                            axios.post('/admin/article-show-gallery', formData)
                                .then(response => {
                                    const newImage = response.data;
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.images[tempIndex] = {
                                            id: newImage.id,
                                            article_gallery_id: newImage.article_gallery_id ?? newImage.id, // fallback
                                            url: e.target.result,
                                            loading: false
                                        };
                                    };
                                    reader.readAsDataURL(file);
                                })
                                .catch(error => {
                                    console.error('Error uploading image:', error);
                                    this.errorMessage = 'Error uploading image. Please try again.';
                                    this.images.splice(tempIndex, 1); // hapus dummy jika gagal
                                });
                        });
                    },
                    deleteImage(index) {
                        const image = this.images[index];
                        axios.delete(`/admin/article-show-gallery/${image.article_gallery_id}`)
                            .then(() => {
                                this.images.splice(index, 1);
                            })
                            .catch(error => {
                                console.error('Error deleting image:', error);
                                this.errorMessage = 'Error deleting image. Please try again.';
                            });
                    }
                };
            }
        </script>
        
        <x-admin.component.linkinput title="Video (Link Youtube/Tiktok) (Optional)" placeholder="Masukkan link..." value="{{ old('link', ($articleShow->articles->video_type === 'youtube') ? $articleShow->articles->youtube : (($articleShow->articles->video_type === 'tiktok') ? $articleShow->articles->tiktok : '')) }}" name="link" link="Url" />
    </x-slot:additional>
    <x-slot:template>
        <div class=" space-y-2">
            <label for="template" class=" text-sm sm:text-base font-semibold">Template</label>
            <div class=" w-full grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($template as $item)
                    <label class="w-full rounded-md bg-white aspect-[2/3] overflow-hidden relative">
                        <input type="radio" name="template_id" value="{{$item->id}}" class="hidden peer" {{ old('template_id', $articleShow->template_id) == $item->id ? 'checked' : '' }}>
                        <img src="{{asset('/storage/images/template/'.$item->image)}}" class=" w-full h-full object-cover object-top" alt="">
                        <div class=" absolute inset-0 peer-checked:bg-black/50 duration-300">
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    </x-slot:template>

    @include('components.admin.component.success')
    @include('components.admin.component.validationerror')
</x-admin.article.form>