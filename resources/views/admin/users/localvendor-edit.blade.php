@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/localvendor.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}" />
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page"> 地域商社アカウントを編集</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>地域商社アカウントを編集</h1>
        </div>
        {!! Form::open(array('route' => ['users.update',$localVendor->id], 'method'=> 'PUT', 'class'=> 'form-block', 'id' =>'user-edit-form', 'enctype' => 'multipart/form-data')) !!}
            @csrf
            <input type="hidden" value="vendor" name="vendor">
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('', 'アカウント種別', ['class' => 'col-form-label']) !!}    
                    </div>
                    <div class="field-section">
                        <p class="name">{{ $localVendor->roles[0]->name_jp }}</p>
                        <input type="hidden" name="roleName" value="{{ $localVendor->roles[0]->name_jp }}">
                    </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('status', 'ステータス', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        <p id="statusData">{{ $localVendor->is_active ? '有効' : '停止させる' }} <span onclick="changeStatus({{ $localVendor->id }})" data-value="{{ $localVendor->is_active ? 0 : 1 }}" class="status">{{ $localVendor->is_active ? '停止させる' : '有効' }}</span> </p>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'アカウント名', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        <p>{{ $localVendor->name }}</p>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('display_name', '表示名', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::text('display_name', $localVendor->display_name, ['class' => 'display-name']) !!}
                         @if($errors->has('display_name'))
                             <div class="error_msg">
                                 {{ $errors->first('display_name') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section ">
                        {!! Form::label('explanation', 'アカウント説明', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::textarea('explanation', $localVendor->explanation, ['class' => 'description']) !!}
                         @if($errors->has('explanation'))
                             <div class="error_msg">
                                 {{ $errors->first('explanation') }}
                             </div>
                         @endif
                     </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('email', 'メールアドレス', ['required' => true,'class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::email('email', $localVendor->email, ['class' => 'email','autocomplete' => false]) !!}
                         @if($errors->has('email'))
                             <div class="error_msg">
                                 {{ $errors->first('email') }}
                             </div>
                         @endif
                    </div>
                    <span class="loader-email-check"></span>
                    <span  class="loader-email-box"> <i class="rito rito-check"></i></span>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('password', '新しいパスワード', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::password('password', ['required' => false,'class' => 'password','onkeyup' => 'passwordValidation(this)','autocomplete' => false]) !!}
                         <span class="invalid-feedback"></span>
                         @if($errors->has('password'))
                             <div class="error_msg">
                                 {{ $errors->first('password') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('password_confirmation', '新しいパスワードの確認', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        <div id="sellerIds">
                            @foreach($localVendor->localvendorSellers as $sellers)
                                <input class="d-none seller-{{$sellers->seller_id}}" type="hidden" name="seller_ids[]" value="{{$sellers->seller_id}}">
                            @endforeach
                        </div>
                         {!! Form::password('password_confirmation', ['required' => false,'class' => 'password']) !!}
                         @if($errors->has('password_confirmation'))
                             <div class="error_msg">
                                 {{ $errors->first('password_confirmation') }}
                             </div>
                         @endif
                     </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section ">
                        {!! Form::label('shimashare_seller_id', '島シェア販売者ID', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::text('shimashare_seller_id', isset($localVendor->localvendorEcmallId[0]->ecmall_seller_id) ? $localVendor->localvendorEcmallId[0]->ecmall_seller_id : '' , ['class' => 'shimashare_seller_id']) !!}
                         @if($errors->has('shimashare_seller_id'))
                             <div class="error_msg">
                                 {{ $errors->first('shimashare_seller_id') }}
                             </div>
                         @endif
                     </div>
                </div>
             </div>
    </section>
    {{-- seller select section start --}}
    <section class="box-content mt-20">
        <div class="d-flex align-items-center page-title">
            <h1>関連する事業者の登録</h1>
        </div>
        <div class="form-block">
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('producer', '事業者', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section selling-block">
                        <select id="seller_id" class="custom-select producer select2" name="seller_id">
                            <option selected="selected" value="">事業者名を選択してください</option>
                                @foreach ($sellerList as $item)    
                                    @if($item != '[]')   
                                        <option {{(isset($item->localVendorId) && $item->localVendorId != null && $item->localVendorId != $localVendor->id)  ? 'disabled':''}} value="{{ $item->id }}">{{ $item->name }} {{(isset($item->localVendorId) && $item->localVendorId != null && $item->localVendorId != $localVendor->id)  ? '['.$localVendorNameById[$item->localVendorId].']':''}} </option>   
                                    @endif 
                                @endforeach  
                        </select>
                        <button class="btn btn-submit" id="button-localvendor">追加</button>
                        <div class="error_msg description" id="island_id_exist_megess"></div>
                        @if($errors->has('seller_ids'))
                             <div class="error_msg">
                                 {{ $errors->first('seller_ids') }}
                             </div>
                         @endif
                        

                        <select class="csv-settings-option" name="available_columns[]" id="append-data" multiple="multiple" size="10">
                             @foreach ($localVendor->localvendorSellers as $seller)
                                <option class="seller-{{$seller->seller_id}}" value="{{$seller->seller_id}}">{{$sellerNameById[$seller->seller_id]}}</option>
                             @endforeach
                        </select>
                        <button class="btn btn-submit" id="button-localvendor-delete" type="button">削除</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- Contact section start--}}
    <section class="box-content mt-20">
        <div class="d-flex align-items-center page-title">
            <h1>詳細情報</h1>
        </div>
        <div class="form-block">
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('number_of_employe', '従業員数', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::number('number_of_employe', $localVendorContacts[0]->number_of_employe, ['required' => false,'class' => 'number-of-employe']) !!}
                         @if($errors->has('number_of_employe'))
                             <div class="error_msg">
                                 {{ $errors->first('number_of_employe') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('representative', '代表者⽒名', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::text('representative', $localVendorContacts[0]->representative, ['required' => false,'class' => 'representative']) !!}
                         @if($errors->has('representative'))
                             <div class="error_msg">
                                 {{ $errors->first('representative') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('high_sales', '年間売上⾼', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::number('high_sales', $localVendorContacts[0]->high_sales, ['required' => false,'class' => 'high-sales', 'autocomplete' => false]) !!}
                         @if($errors->has('high_sales'))
                             <div class="error_msg">
                                 {{ $errors->first('high_sales') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('telephone', '連絡先情報', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section d-flex contact-info-block">
                        <div class="sub-contact d-flex">
                            <div class="d-flex align-items-center">
                                <p class="d-flex align-items-center fix-width">TEL</p>
                            </div>
                            <div class="contact-input d-flex">
                                {!! Form::text('telephone', $localVendorContacts[0]->telephone, ['class' => 'price d-flex align-items-end']) !!}
                                @if($errors->has('telephone'))
                                    <div class="error_msg">
                                        {{ $errors->first('telephone') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                       <hr>
                       <div class="sub-contact d-flex">
                            <div class="d-flex align-items-center">
                                <p class="d-flex align-items-center fix-width">FAX</p>
                            </div>
                            <div class="contact-input d-flex">
                                {!! Form::text('fax', $localVendorContacts[0]->fax, ['class' => 'fax d-flex align-items-end']) !!}
                                @if($errors->has('fax'))
                                    <div class="error_msg">
                                        {{ $errors->first('fax') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr>
                       <div class="sub-contact d-flex">
                            <div class="d-flex align-items-center">
                                <p class="d-flex align-items-center fix-width">Email</p>
                            </div>
                            <div class="contact-input d-flex">
                                {!! Form::email('contact_email', $localVendorContacts[0]->contact_email, ['class' => 'contact-email d-flex align-items-end']) !!}
                                @if($errors->has('contact_email'))
                                    <div class="error_msg">
                                        {{ $errors->first('contact_email') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <hr>
                       <div class="sub-contact d-flex">
                            <div class="d-flex align-items-center">
                                <p class="d-flex align-items-center fix-width">担当者名</p>
                            </div>
                            <div class="contact-input d-flex">
                                {!! Form::text('contact_name', $localVendorContacts[0]->contact_name, ['class' => 'price d-flex align-items-end']) !!}
                                @if($errors->has('contact_name'))
                                    <div class="error_msg">
                                        {{ $errors->first('contact_name') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('url', 'ホームページURL', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::text('url', $localVendorContacts[0]->url, ['required' => false,'class' => 'url']) !!}
                         @if($errors->has('url'))
                             <div class="error_msg">
                                 {{ $errors->first('url') }}
                             </div>
                         @endif
                     </div>
                </div>
             </div>
             <div class="d-flex justify-content-center form-submission">
                <button type="submit" class="btn btn-submit" formnovalidate>編集する</button>
                {{-- <button type="button" onclick="softDeleteCheck()" class="btn btn-submit">登録する</button> --}}
            </div>
        </div>
        {!! Form::close() !!}
    </section>
    {{-- Contact section end--}}
    @include('admin.partials.softdelete_check')
</div>
@endsection
@push('custom-scripts')
<script src="{{ asset('js/softDeleteCheck.js') }}"></script>
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('js/localvendor-create.js') }}"></script>
<script type="text/javascript">
    var SITEURL = '{{ URL::to('') }}';
    var url = SITEURL + '/update/user/status';
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    function changeStatus(id) {
        var status = $('.status').data('value');
        $('.status').addClass('pointer-none');
        $.ajax({
            type: "GET",
            dataType: "json",
            url: url + '/' + id,
            data: {
                'status': status
            },
            success: function (response) {
                if(response.success == 1) {
                    $('#statusData').html('有効 <span onclick="changeStatus(' + id + ')" data-value="0" class="status">停止させる</span>');
                } else {
                    $('#statusData').html('停止させる <span onclick="changeStatus(' + id + ')" data-value="1" class="status">有効</span>');
                }
                $('.status').removeClass('pointer-none');
            },
            error: function (response) {
                console.log('Error:', response);
            }
        });
    }

</script>
@endpush