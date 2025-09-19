<x-admin.article.form head="Create Article Spintax" title="Admin - Create Article Spintax" :form="route('article.store')" >
    <x-admin.component.textinput title="Judul" placeholder="Masukkan Judul" :value="old('judul')" name="judul" />
    <x-admin.component.categoryinput title="Kategori" :tag="$category" :value="old('category')" name="category[]" />
    <x-admin.component.taginput title="Tag" :tag="$tag" :value="old('tag')" name="tag[]" />
    <x-admin.component.summernoteinput title="Artikel" :value="old('article')" name="article" />

    <div class="flex flex-col gap-2">
        <label class="font-medium text-sm sm:text-base">Pilih Web (optional)</label>
        <select class="guardianweb" name="guardian" multiple="multiple">
            @foreach($guardian as $item)
                <option value="{{ $item->id }}" {{ old('guardian') === $item->id ? 'selected' : '' }}>{{ $item->url }}</option>
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
    
    <x-slot:additional>
        <div x-data="imageBanner()" class="flex flex-col gap-2">
            <label class=" text-sm sm:text-base font-semibold" for="image_banner">Galeri (Max 12) (opsional)</label>
            <input type="file" class="hidden" id="image_banner" name="image_banner[]" multiple @change="previewImages($event)" accept="image/*">
          
            <!-- Pratinjau Gambar -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <!-- Loop Gambar -->
              <template x-for="(image, index) in images" :key="index">
                <div class="w-full aspect-square flex items-center rounded-md relative overflow-hidden">
                  <img :src="image" class="w-full h-full object-cover" alt="Banner Image Preview">
                  <!-- Tombol Hapus Gambar -->
                  <button type="button" @click="removeImage(index)" class="absolute inset-0 text-transparent hover:bg-black/60 hover:text-white/50 transition duration-300 p-[35%]">
                    <svg viewBox="0 0 24 24" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"><path d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z" fill="currentColor" class="fill-000000"></path></svg>
                  </button>
                </div>
              </template>
          
              <template x-if="images.length < max">
                <label for="image_banner" class="w-full aspect-square border bg-neutral-100 border-byolink-1 rounded-md relative border-dashed overflow-hidden cursor-pointer">
                    <div class="w-full text-byolink-1 h-full absolute top-0 left-0 flex justify-center items-center p-[35%] hover:bg-byolink-3 hover:text-white/50 duration-300">
                        <svg viewBox="0 0 24 24" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"><path d="m9 13 3-4 3 4.5V12h4V5c0-1.103-.897-2-2-2H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h8v-4H5l3-4 1 2z" fill="currentColor" class="fill-000000"></path><path d="M19 14h-2v3h-3v2h3v3h2v-3h3v-2h-3z" fill="currentColor" class="fill-000000"></path></svg>
                    </div>
                </label>
              </template>
            </div>
        </div>
        <div x-data="imageGallery()" class="flex flex-col gap-2">
            <label class=" text-sm sm:text-base font-semibold" for="image_gallery">Galeri (Max 12) (opsional)</label>
            <input type="file" class="hidden" id="image_gallery" name="image_gallery[]" multiple @change="previewImages($event)" accept="image/*">
          
            <!-- Pratinjau Gambar -->
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
              <!-- Loop Gambar -->
              <template x-for="(image, index) in images" :key="index">
                <div class="w-full aspect-square flex items-center rounded-md relative overflow-hidden">
                  <img :src="image" class="w-full h-full object-cover" alt="Gallery Image Preview">
                  <!-- Tombol Hapus Gambar -->
                  <button type="button" @click="removeImage(index)" class="absolute inset-0 text-transparent hover:bg-black/60 hover:text-white/50 transition duration-300 p-[35%]">
                    <svg viewBox="0 0 24 24" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"><path d="M19.5 8.99h-15a.5.5 0 0 0-.5.5v12.5a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9.49a.5.5 0 0 0-.5-.5Zm-9.25 11.5a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0Zm5 0a.75.75 0 0 1-1.5 0v-8.625a.75.75 0 0 1 1.5 0ZM20.922 4.851a11.806 11.806 0 0 0-4.12-1.07 4.945 4.945 0 0 0-9.607 0A12.157 12.157 0 0 0 3.18 4.805 1.943 1.943 0 0 0 2 6.476 1 1 0 0 0 3 7.49h18a1 1 0 0 0 1-.985 1.874 1.874 0 0 0-1.078-1.654ZM11.976 2.01A2.886 2.886 0 0 1 14.6 3.579a44.676 44.676 0 0 0-5.2 0 2.834 2.834 0 0 1 2.576-1.569Z" fill="currentColor" class="fill-000000"></path></svg>
                  </button>
                </div>
              </template>
          
              <template x-if="images.length < max">
                <label for="image_gallery" class="w-full aspect-square border bg-neutral-100 border-byolink-1 rounded-md relative border-dashed overflow-hidden cursor-pointer">
                    <div class="w-full text-byolink-1 h-full absolute top-0 left-0 flex justify-center items-center p-[35%] hover:bg-byolink-3 hover:text-white/50 duration-300">
                        <svg viewBox="0 0 24 24" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"><path d="m9 13 3-4 3 4.5V12h4V5c0-1.103-.897-2-2-2H4c-1.103 0-2 .897-2 2v12c0 1.103.897 2 2 2h8v-4H5l3-4 1 2z" fill="currentColor" class="fill-000000"></path><path d="M19 14h-2v3h-3v2h3v3h2v-3h3v-2h-3z" fill="currentColor" class="fill-000000"></path></svg>
                    </div>
                </label>
              </template>
            </div>
        </div>
        
        <script>
            function imageBanner() {
                return {
                    images: [],
                    files: [],
                    max: 6,
                
                    previewImages(event) {
                        const input = event.target;
                        const selected = Array.from(input.files).slice(0, this.max - this.files.length);
                
                        selected.forEach(file => {
                        const exists = this.files.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
                        if (exists) return;
                
                        this.files.push(file);
                        this.images.push(URL.createObjectURL(file));
                        });

                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                    },
                
                    removeImage(index) {
                        try { URL.revokeObjectURL(this.images[index]); } catch (e) {}
                
                        this.images.splice(index, 1);
                        this.files.splice(index, 1);
                
                        // sync input.files
                        const input = document.getElementById('banner');
                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                    },
                }
            }
            function imageGallery() {
                return {
                    images: [],
                    files: [],
                    max: 12,
                
                    previewImages(event) {
                        const input = event.target;
                        const selected = Array.from(input.files).slice(0, this.max - this.files.length);
                
                        selected.forEach(file => {
                        const exists = this.files.some(f => f.name === file.name && f.size === file.size && f.lastModified === file.lastModified);
                        if (exists) return;
                
                        this.files.push(file);
                        this.images.push(URL.createObjectURL(file));
                        });

                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                    },
                
                    removeImage(index) {
                        try { URL.revokeObjectURL(this.images[index]); } catch (e) {}
                
                        this.images.splice(index, 1);
                        this.files.splice(index, 1);
                
                        // sync input.files
                        const input = document.getElementById('image_gallery');
                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        input.files = dt.files;
                    },
                }
            }
        </script>
        <x-admin.component.linkinput title="Video (Link Youtube/Tiktok)" placeholder="Masukkan link..." :value="old('link')" name="link" link="Url" />
    </x-slot:additional>
    <x-slot:template>
        <div class=" space-y-2">
            <label for="template" class=" text-sm sm:text-base font-semibold">Template (min 1)</label>
            <div class=" w-full grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($template as $item)
                    <label class="w-full rounded-md bg-white aspect-[2/3] overflow-hidden relative">
                        <input type="checkbox" 
                            name="template_id[]" 
                            value="{{ $item->id }}" 
                            class="hidden peer"
                            {{ (is_array(old('template_id')) && in_array($item->id, old('template_id'))) || $loop->first ? 'checked' : '' }}>
                        
                        <img src="{{ asset('/storage/images/template/'.$item->image) }}" class="w-full h-full object-cover object-top" alt="">
                        <div class="absolute inset-0 peer-checked:bg-black/50 duration-300"></div>
                    </label>
                @endforeach
            </div>
        </div>
    </x-slot:template>
    @include('components.admin.component.validationerror')
</x-admin.article.form>