@props(['title', 'name', 'value'])

<div class=" w-full">
    <div class=" w-full flex flex-col max-w-full gap-2 text-sm sm:text-base font-medium">
        <label for="summernote">{{$title}}</label>
        <textarea id="summernote" name="{{$name}}">{!! nl2br($value == '' ? '' : $value) !!}</textarea>
    </div>
</div>
<script>
    window.addEventListener('load', function summernote() {
        jQuery(document).ready(function($) {
            $('#summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']], 
                    ['font', ['bold', 'italic', 'underline']], 
                    ['para', ['ul', 'ol', 'paragraph']], 
                    ['insert', ['link']], 
                    ['view', ['fullscreen']]
                ],
                styleTags: [
                    { title: 'Paragraph', tag: 'p', value: 'p' },
                    { title: 'Heading', tag: 'h3', value: 'h3' },
                ]
            });
        });
    });
</script>
<style>
    .note-editable div {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Override gaya default Tailwind untuk h1 hingga h6 */
    .note-editor strong, p, h1, h2, h3, h4, h5, h6 {
        color: inherit !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .note-editor font {
        color: inherit;
    }

    /* Kustomisasi tambahan untuk masing-masing elemen heading */
    .note-editor ol {
        padding-left: 16px;
        list-style-type: decimal;
    }

    .note-editor ul {
        padding-left: 16px;
        list-style-type: disc;
    }

    .note-editor span {
        font-size: inherit !important;
    }

    .note-editor p {
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
    }

    .note-editor li {
        font-size: 0.875rem !important;
        line-height: 1.25rem !important;
    }

    .note-editor h1 {
        font-size: 1.875rem !important;
        line-height: 2.25rem !important;
    }

    .note-editor h2 {
        font-size: 1.5rem !important;
        line-height: 2rem !important;
    }

    .note-editor h3 {
        font-size: 1rem !important;
        line-height: 1.5rem !important;
    }

    .note-editor h4 {
        font-size: 1rem !important;
        line-height: 1.5rem !important;
    }

    .note-editor h5 {
        font-size: 0.75rem !important;
        line-height: 1.25rem !important;
    }

    .note-editor h6 {
        font-size: 0.5rem !important;
        line-height: 0.75rem !important;
    }

    .note-editable > * + * {
        margin-top: 0 !important;
    }

    @media screen and (min-width: 640px) {
        .note-editor p {
            font-size: 1rem !important;
            line-height: 1.5rem !important;
        }
        .note-editor li {
            font-size: 1rem !important;
            line-height: 1.5rem !important;
        }
        .note-editor h3 {
            font-size: 1.25rem !important;
            line-height: 1.75rem !important;
        }
        .note-editable > * + * {
            margin-top: 0 !important;
        }
    }

    .note-editable {
        background-color: white;
        white-space: normal !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }

    .note-editor {
        max-width: 100%;
        border-color: #3b82f6 !important;
        /* background-color: #f5f5f5 !important; */
        border-radius: 0.375rem !important;
    }
    
    .note-toolbar {
        border-radius: 0.375rem 0.375rem 0 0 !important;
    }

    .note-modal {
        transform: translateY(-50%);
        top: 50%;
    }
</style>