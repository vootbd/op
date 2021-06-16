@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/directorie-edit.css') }}">
@endpush

@section('content')
<div class="inner-content">
    @push('breadcrumb')
    <li class="breadcrumb-item"><a href="{{route('seller.list')}}">TOP</a></li>
    <li class="breadcrumb-item active" aria-current="page"><a href="{{route('pages.index')}}">ページ</a></li>
    <li class="breadcrumb-item active" aria-current="page">ページ階層編集</li>
    @endpush

    @if ($message = Session::get('message'))
    <div class="custom-alert success">
        <span class="rito rito-check"></span>
        <p>{{ $message }}</p>
    </div>
    @endif
    <div class="field"></div>
    <section id="scrollTop" class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>ページ階層編集</h1>
        </div>
        <div class="box-inner-content">
            {!! Form::open(array('route'=> ['directories.update',$directorie->id],'id'=>'form-directory-update', 'method'=> 'POST', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
            @csrf
            @method('PUT')
            {!! Form::hidden('directory_id',$directorie->id, ['id' => 'directorie-id']) !!}
            <div class="form-content">
                <div class="form-group">
                    <div class="label-section d-flex align-items-center dir-name">
                        {!! Form::label('name', '階層名', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('name', $directorie->caption, ['class' => 'name', 'required' => true]) !!}
                        <div class="error_msg name"></div>
                    </div>
                </div>
                <div class="url-section">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('directory', 'URL', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="form-group d-flex">
                        <div class="label-section align-items-center">
                            {!! Form::label('directory', 'https://'.request()->getHost().'/'.$parent_dir, ['class' => 'class']) !!}
                        </div>
                        <div class="field-section">
                            <div class="url-input">
                                {!! Form::text('directory', $directorie->name, ['class' => 'url-map-edit', 'required' => true, 'data-type' => 'update' ,'data-url' => '/urlCheck/islands', 'id' => 'directory','maxlength'=>config('constants.CUSTOM_URL_MAX_LENGTH', 500)]) !!}
                                {!! Form::hidden('url_copy', $directorie->name, ['id' => 'url_copy']) !!}
                            </div>
                            <div class="error_msg url_copy"></div>
                            <div class="error_msg directory"></div>
                        </div>
                    </div>
                </div>
            </div>
            <hr/>
            <div class="d-flex justify-content-left form-submission">
                <button type="submit" class="btn btn-submit">編集する</button>
            </div>
            {!! Form::close() !!}
        </div>
    </section>
</div>
@endsection
@push('custom-scripts')
<script src="{{asset('js/jquery.nestable.js')}}"></script>
<script src="{{asset('js/directory.js')}}"></script>
<script>
</script>
@endpush