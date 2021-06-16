@extends('admin.layouts.admin')

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('css/admin/css/media-create.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/dropzone/dropzone.min.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="#">TOP</a></li>
<li class="breadcrumb-item" aria-current="page"><a
        href="{{ route('medias.index') }}">メディア一覧</a></li>
<li class="breadcrumb-item active" aria-current="page">メディア登録</li>
@endpush

@section('content')
<div class="inner-content">
    <section id="scrollTop" class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>メディア登録</h1>
        </div>
        <div class="box-inner-content">
            <div class="error_msg" id="error_msg"></div>
            {!! Form::open(['route' => 'medias.upload', 'method' => 'POST', 'class' => 'form-block dropzone']) !!}
            @csrf
            <div class="form-content dz-default dz-message">
                <div class="form-group">
                    <div class="field-section">
                        <div class="d-flex justify-content-center align-items-center">
                            <p>ファイルをドラッグアンドドロップでアップロード</p>
                        </div>
                    </div>
                    <div class="preview">

                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="footer">
                <ul class="footer-text">
                    <li>アップロードできるファイルサイズは10MBまでです。</li>
                    <li>アップロードできるファイルの種類は下記をご確認ください。<br/>jpg、jpeg、png、gif、svg、pdf</li>
                </ul>
            </div>
        </div>
    </section>
</div>
@endsection
@push('custom-scripts')
    <script src="{{ asset('vendor/dropzone/js/dropzone.min.js') }}"
    integrity="sha512-8l10HpXwk93V4i9Sm38Y1F3H4KJlarwdLndY9S5v+hSAODWMx3QcAVECA23NTMKPtDOi53VFfhIuSsBjjfNGnA=="
    crossorigin="anonymous">
    </script>
    <script>
        var dictFileTooBigMassage = "{{ trans('media.max_size_img') }}";
        var dictInvalidFileTypeMassage = "{{ trans('media.file_types') }}";
        Dropzone.autoDiscover = false;
        // Dropzone.options.productForm = false;
        var acceptedFileTypes = "image/*"; //dropzone requires this param be a comma separated list
        let token = $('meta[name="csrf-token"]').attr("content");
        $(function() {
            var myDropzone = new Dropzone(".dropzone", {
                paramName: "thumbnail_image",
                maxFilesize: 10,
                // url: "/media/upload",
                addRemoveLinks: true,
                autoProcessQueue: true,
                uploadMultiple: false,
                parallelUploads: 1,
                maxFiles: 1,
                addRemoveLinks: true,
                dictRemoveFile: 'Remove',
                timeout: 50000,
                acceptedFiles: ".jpeg,.jpg,.png,.gif,.pdf,.svg",
                params: {
                    _token: token,
                },
                dictFileTooBig:dictFileTooBigMassage,
                dictInvalidFileType: dictInvalidFileTypeMassage
            });
            myDropzone.on("removedfile", function(file) {
                
            });
            myDropzone.on("addedfile", function(file) {
                /* Maybe display some more file information on your page */
                //console.log("File Added");
            });
            myDropzone.on("success", function(file, response) {
                if (response.success == false) {
                    $('#error_msg').html(response.data.thumbnail_image[0]);
                    appendErrorMessage(formId, response.data);
                }else{
                    window.location.href = response.redirects;
                }
            });
            myDropzone.on("error", function(file, message, xhr) {
                if(xhr == null) myDropzone.removeFile(file);
                $('#error_msg').html(message);
            });
            myDropzone.on("sending", function(file, xhr, formData) {
                // Will send the filesize along with the file as POST data.
            });
        });
    </script>
@endpush