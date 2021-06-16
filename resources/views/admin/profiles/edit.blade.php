@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/seller-profile-edit.css') }}">
@endpush

@push('breadcrumb')
@role('operator')
    <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
    <li class="breadcrumb-item active">プロフィールを編集</li>
@endrole
@role('seller')
    <li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
    <li class="breadcrumb-item active">プロフィール</li>
@endrole
@endpush

@section('content')
<div class="inner-content">

    @if ($message = Session::get('success'))
        <div class="custom-alert success">
            <span class="rito rito-check"></span>
            <p>{{ $message }}</p>
            <span class="rito rito-x" id="alertClose"></span>
        </div>
    @endif

    {!! Form::open(array('route' => ['profile.update', $data->id], 'method'=> 'PUT', 'class'=> 'form-block seller-profile-from-reset-'.$data->user_id, 'enctype' => 'multipart/form-data','id' => 'seller-profile-from-edit','data-id' => (Auth::user()->hasRole('seller')) ? 'seller': 'operator')) !!}
    @csrf
    <span class="get-seller-id" data-id="{{ $data->user_id }}"></span>
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>プロフィールを編集</h1>
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
                    {!! Form::textarea('message', $data->message, ['class' => 'description']) !!}
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
                    {!! Form::text('profile1', $data->profile1, ['class' => 'profile1', 'autocomplete' => 'off']) !!}
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
                    {!! Form::text('profile2', $data->profile2, ['class' => 'profile2', 'autocomplete' => 'off']) !!}
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
                    {!! Form::text('profile3', $data->profile3, ['class' => 'profile3', 'autocomplete' => 'off']) !!}
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
                    {!! Form::text('profile4', $data->profile4, ['class' => 'profile4', 'autocomplete' => 'off']) !!}
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
                    {!! Form::textarea('profile5', $data->profile5, ['class' => 'description', 'autocomplete' => 'off']) !!}
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
            <div class="form-group d-flex thumbnail-block">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('profile5', 'サムネイル画像', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section thumbnail-block cover-image drop-container" data-id="0">
                    <div class="btn-section upload-btn-block" id="drop-container" data-id="coverImage">
                        {!! Form::label('coverImage', 'ファイルを選択',['class'=>'btn btn-thumbnail']) !!}
                        {!! Form::file('coverimage', ['class' => 'thumbnail-input', 'id' => 'coverImage']) !!}
                        {!! Form::hidden('cover_image', getImageName($data->cover_image), ['class'=> 'product-image']) !!}
                        <div class="delete-btn{{ $data->cover_image ? '' : ' btn-hide' }}" id="coverImageDelete">画像を削除する</div>
                    </div>
                    <div class="image-name" id="coverImageName"></div>
                    <span id="preview" class="product-image">
                        <img id="coverImagePreview" src="{{ asset($data->cover_image_sm) }}" alt="">
                        <span class="formate-error coverImageerror">画像ファイルjpg、jpeg、pngを選択</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center form-submission">
            <button type="submit" class="btn btn-submit">変更する</button>
        </div>
        {!! Form::close() !!}
    </section>
    <div id="role_id_check" data-id="{{ Auth::user()->hasRole('seller') ? 'operator': 'seller' }}"></div>
</div>
@include('admin.partials.delete_modal')

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
