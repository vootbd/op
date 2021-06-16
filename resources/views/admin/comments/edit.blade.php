@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/comment-create.css') }}">
@endpush

@push('breadcrumb')
@role('seller')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a>
@endrole
@role('operator')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a>
@endrole
<li class="breadcrumb-item active" aria-current="page">商談メモを編集</li>
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
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>商談メモを編集</h1>
        </div>
            {!! Form::open(array('route' => ['comments.update', $data->id], 'method'=> 'PATCH', 'class'=> 'form-block seller-profile-from-reset-'.$data->seller_id, 'enctype' => 'multipart/form-data','id' => 'comment-form-update','data-id' => (Auth::user()->hasRole('seller')) ? 'seller': 'operator')) !!}
            @csrf
            <span class="get-seller-id" data-id="{{ $data->seller_id }}"></span>

             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('comment', '商談メモ', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::textarea('comment', $data->comment, ['class' => 'comment','placeholder' => false]) !!}
                        <div class="error_msg comment"></div>
                    </div>
                </div>
             </div>
            <div class="d-flex justify-content-center form-submission">
                {{-- <div class="spinner-border text-primary" role="status" id="ajax-loader">
                    <span class="sr-only">Loading...</span>
                </div> --}}
                <button type="submit" class="btn btn-submit" id="btn-form-submit">登録する</button>
            </div>
        {!! Form::close() !!}
    </section>
    <div id="role_id_check" data-id="{{ Auth::user()->hasRole('seller') ? 'operator': 'seller' }}"></div>
    @role('seller')
    <div class="mt-seller"></div>
    @endrole
</div>
@endsection
@push('custom-scripts')
{{-- <script src="{{ asset('js/operator-seller-comment.js') }}"></script> --}}
<script>
    var profileId;
    var getRole;
    jQuery(window).bind('beforeunload', function(){
        profileId = $('.get-seller-id').data('id');
        getRole = $("#role_id_check").data("id");
        ajaxRealTimeFormDataGet(profileId,inputVal='',getRole,'is_comment');
    });
</script>
@endpush