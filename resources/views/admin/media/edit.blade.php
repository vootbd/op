@extends('admin.layouts.admin')

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('css/admin/css/media-edit.css') }}">
@endpush

@section('content')
    <div class="inner-content">
        @push('breadcrumb')
            <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">TOP</a></li>
            <li class="breadcrumb-item" aria-current="page"><a
                href="{{ route('medias.index') }}">メディア一覧</a></li>
            <li class="breadcrumb-item active" aria-current="page">メディア編集</li>
        @endpush
        @if ($message = Session::get('message'))
        <div class="custom-alert success">
            <span class="rito rito-check"></span>
            <p>{{ $message }}</p>
        </div>
        @endif
        <section id="scrollTop" class="box-content">
            <div class="d-flex align-items-center page-title">
                <h1>メディア編集</h1>
            </div>
            <div class="box-inner-content">
                {!! Form::open(['route' => ['medias.update', $media->id], 'method' => 'POST', 'class' => 'form-block', 'id' =>
                'form-media-update', 'enctype' => 'multipart/form-data']) !!}
                <div class="inner-form">
                    @csrf
                    @method('PUT')
                    <div class="form-content">
                        <div class="form-group">
                            <div class="label-section d-flex align-items-center">
                                {!! Form::label('display_name', 'タイトル', ['class' => 'col-form-label']) !!}
                            </div>
                            <div class="field-section">
                                {!! Form::text('display_name', $media->display_name, ['class' => 'name', 'required' => true]) !!}
                                <div class="error_msg display_name"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="image-block">
                                <img id="coverImagePreview" src="{{ ($media->extention == 'pdf') ? asset('image/pdf.jpg'):(($media->extention == 'svg') ? asset(config('constants.IMG.MEDIA.LOAD_PATH').$media->original_name):(($media->original_name !='') ? asset(config('constants.IMG.MEDIA.LOAD_PATH_MD').$media->original_name): asset('image/images-icon.svg'))) }}" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="label-section d-flex align-items-center">
                                {!! Form::label('alt_text', 'Alt', ['class' => 'col-form-label']) !!}
                            </div>
                            <div class="field-section">
                                {!! Form::text('alt_text', $media->alt_text, ['class' => 'alt_text']) !!}
                                <div class="error_msg alt_text"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="right-sidebar">
                    <div class="publish-content">
                        <div class="publish-header">
                            <span>メディア</span>
                        </div>
                        <div class="publish-body">
                            {!! Form::label('publishing_status', 'メディアの URL', ['class' => 'publish-label']) !!}
                            {!! Form::text('url',$media->url , ['class' =>'name']) !!}
                            <div class="image-info">
                                <p class="image-info-title publish-label">ファイル名</p>
                            <p class="image-info-data publish-label">{{ $media->original_name }}</p>
                                <h6 class="image-info-title publish-label">ファイルタイプ</h6>
                            <p class="image-info-data publish-label">{{ $media->extention}}</p>
                                <h6 class="image-info-title publish-label">ファイルサイズ</h6>
                            <p class="image-info-data publish-label">{{ round($media->size/1000, 1) }} KB</p>
                                <h6 class="image-info-title publish-label">サイズ</h6>
                            <p class="image-info-data publish-label">{{($media->extention == 'pdf' || $media->extention == 'svg') ? '-' : ($media->width.' X '.$media->height) }}</p>
                            </div>
                            <div class="submit-section">
                                <button type="reset" class="reset-btn">
                                    <span class="fa fa-trash custom-icon">
                                    </span>
                                </button>
                                <div class="spinner-border text-primary" role="status" id="ajax-loader">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <button class="btn-submit" id="btn-form-submit">登録</button>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </section>
    </div>
@endsection
@push('custom-scripts')
<script src="{{ asset('js/media.js') }}"></script>
@endpush