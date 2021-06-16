@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/seller-create.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}" />
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page"> 事業者アカウントを登録</li>
@endpush

@section('content')
<div class="inner-content">
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>事業者アカウントを登録</h1>
        </div>
        {!! Form::open(array('route' => ['users.store'], 'method'=> 'POST', 'class'=> 'form-block', 'id' =>'form-user-create', 'enctype' => 'multipart/form-data')) !!}
            @csrf
            <input type="hidden" value="seller" name="seller">
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('producer', '離島', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section selling-block">
                        <div id="append-data" class="d-flex"></div>
                        <select id="island_id" class="custom-select producer select2" name="island_id">
                            <option selected="selected" value="">離島名を選択してください</option>
                                @foreach ($islandDropDown as $item)    
                                    @if($item->islands != '[]')  
                                        <optgroup label="{{ $item->name }}"> 
                                            @foreach ($item->islands as $key => $value)     
                                                    <option value="{{ $value->id }}">{{ $value->name }} </option>   
                                            @endforeach  
                                        </optgroup>   
                                    @endif 
                                @endforeach  
                        </select>
                        <button class="btn btn-submit" id="button-isalnd" type="submit">選択</button>
                        <div class="error_msg description" id="island_id_exist_megess"></div>
                        @if($errors->has('island_ids'))
                             <div class="error_msg">
                                 {{ $errors->first('island_ids') }}
                             </div>
                         @endif
                         <div class="remove-text-block">x 関連する離島をすべて削除する</div>
                        <div class="island-info d-flex"><a href="{{ route('islands.create') }}" ><i class="rito rito-plus-circle"></i> 離島を追加する</a> </div>
                    </div>
                </div>
                <hr>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', '企業名', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('name', null, ['required' => true,'class' => 'name', 'id' => 'company_name_id']) !!}
                        <div class="company-name-error" id="error_msg_id">
                        </div>
                        @if($errors->has('name'))
                            <div class="error_msg">
                            {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('display_name', '表示名', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::text('display_name', null, ['class' => 'display-name']) !!}
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
                         {!! Form::textarea('explanation', null, ['class' => 'description']) !!}
                         @if($errors->has('explanation'))
                             <div class="error_msg">
                                 {{ $errors->first('explanation') }}
                             </div>
                         @endif
                     </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section ">
                        {!! Form::label('rank', '事業者のランク', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                         {!! Form::select('rank',['' => '選択してください','a' => 'A','b' => 'B','c' => 'C'], null, ['class' => 'custom-select rank']) !!}
                         @if($errors->has('rank'))
                             <div class="error_msg">
                                 {{ $errors->first('rank') }}
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
                         {!! Form::email('email', null, ['class' => 'email','autocomplete' => false]) !!}
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
                        {!! Form::label('password', 'パスワード', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::password('password', ['required' => true,'class' => 'password','onkeyup' => 'passwordValidation(this)','autocomplete' => false]) !!}
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
                        {!! Form::label('password_confirmation', 'パスワードの確認', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                         {!! Form::password('password_confirmation', ['required' => true,'class' => 'password']) !!}
                         @if($errors->has('password_confirmation'))
                             <div class="error_msg">
                                 {{ $errors->first('password_confirmation') }}
                             </div>
                         @endif
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
                         {!! Form::number('number_of_employe', null, ['required' => false,'class' => 'number-of-employe']) !!}
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
                         {!! Form::text('representative', null, ['required' => false,'class' => 'representative']) !!}
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
                         {!! Form::number('high_sales', null, ['required' => false,'class' => 'high-sales', 'autocomplete' => false]) !!}
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
                                {!! Form::text('telephone', null, ['class' => 'price d-flex align-items-end']) !!}
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
                                {!! Form::text('fax', null, ['class' => 'fax d-flex align-items-end']) !!}
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
                                {!! Form::email('contact_email', null, ['class' => 'contact-email d-flex align-items-end']) !!}
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
                                {!! Form::text('contact_name', null, ['class' => 'price d-flex align-items-end']) !!}
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
                         {!! Form::text('url', null, ['required' => false,'class' => 'url']) !!}
                         @if($errors->has('url'))
                             <div class="error_msg">
                                 {{ $errors->first('url') }}
                             </div>
                         @endif
                     </div>
                </div>
             </div>
             <div class="d-flex justify-content-center form-submission">
                <button type="button" onclick="softDeleteCheck()" class="btn btn-submit">登録する</button>
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
<script src="{{ asset('js/seller-create.js') }}"></script>
@endpush