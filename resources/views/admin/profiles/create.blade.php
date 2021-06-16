@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/seller-profile-create.css') }}">
@endpush

@push('breadcrumb')
@role('operator')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
@endrole
@role('seller')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
@endrole
<li class="breadcrumb-item active" aria-current="page">プロフィールを登録</li>
@endpush

@section('content')

<div class="inner-content" onbeforeunload="return onBackPageEven()">
    @if ($message = Session::get('success'))
    <div class="custom-alert attention">
        <span class="rito rito-check"></span>
        <p>{{ $message }}</p>
        <span class="rito rito-x" id="alertClose"></span>
    </div>
    @endif
    {!! Form::open(array('route' => 'profile.store', 'method'=> 'POST', 'class'=> 'form-block seller-profile-from-reset-'.$id, 'enctype' => 'multipart/form-data','id' => 'seller-profile-from-create','data-id' => (Auth::user()->hasRole('seller')) ? 'seller': 'operator','data-seller' => $id)) !!}
        @csrf
    <span class="get-seller-id" data-id="{{ $id }}"></span>
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>プロフィールを登録</h1>
        </div>
        <div class="form-content">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('name', '離島', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section label">
                    <label>{{ !empty($islands[$users[0]->island_id]) ? $islands[$users[0]->island_id] : "" }}</label>
                </div>
            </div>
            {!! Form::hidden('seller_id', $id, ['class' => 'profile1', 'autocomplete' => 'off']) !!}
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('name', '事業者名', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section label">
                    <label>{{ !empty($users[0]->name) ? $users[0]->name: '' }}</label>
                </div>
            </div>
            <hr>
            <div class="form-group d-flex">
                <div class="label-section ">
                    {!! Form::label('message', '事業者メッセージ', ['class' => 'col-form-label']) !!} 
                </div>
                <div class="field-section">
                    {!! Form::textarea('message', null, ['class' => 'description']) !!}
                    @if($errors->has('message'))
                        <div class="error_msg">
                            {{ $errors->first('message') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('profile1', 'プロフィール１', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('profile1', null, ['class' => 'profile1', 'autocomplete' => 'off']) !!}
                    @if($errors->has('profile1'))
                        <div class="error_msg">
                            {{ $errors->first('profile1') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('profile2', 'プロフィール2', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('profile2', null, ['class' => 'profile2', 'autocomplete' => 'off']) !!}
                    @if($errors->has('profile2'))
                        <div class="error_msg">
                            {{ $errors->first('profile2') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('profile3', 'プロフィール3', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('profile3', null, ['class' => 'profile3', 'autocomplete' => 'off']) !!}
                    @if($errors->has('profile3'))
                        <div class="error_msg">
                            {{ $errors->first('profile3') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('profile4', 'プロフィール4', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::text('profile4', null, ['class' => 'profile4', 'autocomplete' => 'off']) !!}
                    @if($errors->has('profile4'))
                        <div class="error_msg">
                            {{ $errors->first('profile4') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-start">
                    {!! Form::label('profile5', 'プロフィール5', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    {!! Form::textarea('profile5', null, ['class' => 'description', 'autocomplete' => 'off']) !!}
                    @if($errors->has('profile5'))
                        <div class="error_msg">
                            {{ $errors->first('profile5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <section class="box-content mt-20">
        <div class="form-content">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('thumbnailImage', 'サムネイル画像', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section thumbnail-block cover-image drop-container" data-id="0">
                    <div class="btn-section upload-btn-block" id="drop-container" data-id="coverImage">
                        {!! Form::label('coverImage', 'ファイルを選択',['class'=>'btn btn-thumbnail']) !!}
                        {!! Form::hidden('cover_image',"", ['class' => 'product-image']) !!}
                        {!! Form::file('coverimage', ['class' => 'thumbnail-input', 'id' => 'coverImage']) !!}
                        <div class="delete-btn" id="coverImageDelete">画像を削除する</div>
                    </div>
                    <div class="image-name" id="coverImageName">選択されていません。</div>
                    <span id="preview" class="product-image">
                        <img id="coverImagePreview" src="" alt="">
                        <span class="formate-error coverImageerror">画像ファイルjpg、jpeg、pngを選択</span>
                    </span>
                </div>
            </div>  
        </div>
        <div class="d-flex justify-content-center form-submission">
            <button type="submit" class="btn btn-submit">登録する</button>
        </div>
        {!! Form::close() !!}
    </section>
    <div id="role_id_check" data-id="{{ Auth::user()->hasRole('seller') ? 'operator': 'seller' }}"></div>
</div>
@endsection

@push('custom-scripts')
<script>
    var profileId;
    var getRole;
    jQuery(window).bind('beforeunload', function(){
        profileId = $('.get-seller-id').data('id');
        getRole = $("#role_id_check").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal='',getRole);
    });
</script>
{{-- <script src="{{ asset('js/seller_profile.js') }}"></script> --}}
@endpush
