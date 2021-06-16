@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/role-edit.css') }}">
@endpush

@push('breadcrumb')
@role('operator')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
@endrole()
@role('seller')
<li class="breadcrumb-item"><a href="{{ route('sellerProductList') }}">管理 TOP</a></li>
@endrole()
<li class="breadcrumb-item active" aria-current="page">アカウント権限</li>
@endpush

@section('content')

<div class="inner-content">
    @if ($message = Session::get('success'))
    <div class="custom-alert attention">
        <span class="rito rito-check"></span>
        <p>{{ $message }}</p>
        <span class="rito rito-x" id="alertClose"></span>
    </div>
    @endif
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>運営アカウントを登録</h1>
        </div>
        {!! Form::model($role, ['method' => 'PATCH', 'class'=> 'pb-5', 'route' => ['roles.update', $role->id]]) !!}
        <div class="user-role-selection">
            <p class="name">ユーザー・グループ</p>
            {!! Form::select('name', ['1' => '管理者', '2' => '運営者', '3' => '事業者', '4' => 'バイヤー'], null, ['class' => 'custom-select sort-block', 'onchange' => 'getPagination(1)']) !!}
        </div>
        <div class="user-permission-block">
            <div class="module">モジュール</div>
            <div class="view">{{ Form::checkbox('asap', null, null, array('id'=>'view')) }} 見る</div>
            <div class="add">{{ Form::checkbox('asap', null, null, array('id'=>'add')) }} 追加</div>
            <div class="edit">{{ Form::checkbox('asap', null, null, array('id'=>'edit')) }} 編集する</div>
            <div class="delete">{{ Form::checkbox('asap', null, null, array('id'=>'delete')) }} 削除する</div>
            <div class="all"></div>
        </div>
        {{-- user block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[0]->module }}</div>
            <div class="view {{ $permission[0]->module }}">
                {{ Form::checkbox('permission[]', $permission[0]->id, in_array($permission[0]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[0]->module }}">
                {{ Form::checkbox('permission[]', $permission[1]->id, in_array($permission[1]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="edit {{ $permission[0]->module }}">
                {{ Form::checkbox('permission[]', $permission[2]->id, in_array($permission[2]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="delete {{ $permission[0]->module }}">
                {{ Form::checkbox('permission[]', $permission[3]->id, in_array($permission[3]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="all">{{ Form::checkbox('asap', null, null, array('id'=>'user-all')) }} すべて</div>
        </div>
        {{-- account permission block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[6]->module }}</div>
            <div class="view {{ $permission[6]->module }}">
                {{ Form::checkbox('permission[]', $permission[6]->id, in_array($permission[6]->id, $rolePermissions) ? true : false) }}</div>
            <div class="add {{ $permission[6]->module }}"></div>
            <div class="edit {{ $permission[6]->module }}"></div>
            <div class="delete {{ $permission[6]->module }}"></div>
            <div class="all {{ $permission[6]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'permission-all')) }} すべて</div>
        </div>
        {{-- activity authority block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[8]->module }}</div>
            <div class="view {{ $permission[8]->module }}">
                {{ Form::checkbox('permission[]', $permission[8]->id, in_array($permission[8]->id, $rolePermissions) ? true : false) }}</div>
            <div class="add {{ $permission[8]->module }}"></div>
            <div class="edit {{ $permission[8]->module }}"></div>
            <div class="delete {{ $permission[8]->module }}"></div>
            <div class="all {{ $permission[8]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'account-authority-all')) }} すべて</div>
        </div>
        {{-- activity log block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[9]->module }}</div>
            <div class="view {{ $permission[9]->module }}">
                {{ Form::checkbox('permission[]', $permission[9]->id, in_array($permission[9]->id, $rolePermissions) ? true : false) }}</div>
            <div class="add {{ $permission[9]->module }}"></div>
            <div class="edit {{ $permission[9]->module }}"></div>
            <div class="delete {{ $permission[9]->module }}"></div>
            <div class="all {{ $permission[9]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'activity-log-all')) }} すべて</div>
        </div>
        {{-- all account list block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[10]->module }}</div>
            <div class="view {{ $permission[10]->module }}">
                {{ Form::checkbox('permission[]', $permission[10]->id, in_array($permission[10]->id, $rolePermissions) ? true : false) }}</div>
            <div class="add {{ $permission[10]->module }}"></div>
            <div class="edit {{ $permission[10]->module }}"></div>
            <div class="delete {{ $permission[10]->module }}"></div>
            <div class="all {{ $permission[10]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'all-account-all')) }} すべて</div>
        </div>
        {{-- operator block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[11]->module }}</div>
            <div class="view {{ $permission[11]->module }}">
                {{ Form::checkbox('permission[]', $permission[11]->id, in_array($permission[11]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[11]->module }}">
                {{ Form::checkbox('permission[]', $permission[12]->id, in_array($permission[12]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="edit {{ $permission[11]->module }}">
                {{ Form::checkbox('permission[]', $permission[13]->id, in_array($permission[13]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="delete {{ $permission[11]->module }}">
                {{ Form::checkbox('permission[]', $permission[14]->id, in_array($permission[14]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="all {{ $permission[11]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'operator-all')) }} すべて</div>
        </div>
            {{-- buyer block --}}
            <div class="user-permission-block list">
            <div class="module">{{ $permission[15]->module }}</div>
            <div class="view {{ $permission[15]->module }}">
                {{ Form::checkbox('permission[]', $permission[15]->id, in_array($permission[15]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[15]->module }}">
                {{ Form::checkbox('permission[]', $permission[16]->id, in_array($permission[16]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="edit {{ $permission[15]->module }}">
                {{ Form::checkbox('permission[]', $permission[17]->id, in_array($permission[17]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="delete {{ $permission[15]->module }}">
                {{ Form::checkbox('permission[]', $permission[18]->id, in_array($permission[18]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="all {{ $permission[15]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'buyer-all')) }} すべて</div>
        </div>
        {{-- seller block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[19]->module }}</div>
            <div class="view {{ $permission[19]->module }}">
                {{ Form::checkbox('permission[]', $permission[19]->id, in_array($permission[19]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[19]->module }}">
                {{ Form::checkbox('permission[]', $permission[20]->id, in_array($permission[20]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="edit {{ $permission[19]->module }}">
                {{ Form::checkbox('permission[]', $permission[21]->id, in_array($permission[21]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="delete {{ $permission[19]->module }}">
                {{ Form::checkbox('permission[]', $permission[22]->id, in_array($permission[22]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="all {{ $permission[19]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'seller-all')) }} すべて</div>
        </div>
        {{-- island block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[23]->module }}</div>
            <div class="view {{ $permission[23]->module }}">
                {{ Form::checkbox('permission[]', $permission[23]->id, in_array($permission[23]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[23]->module }}">
                {{ Form::checkbox('permission[]', $permission[24]->id, in_array($permission[24]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="edit {{ $permission[23]->module }}">
                {{ Form::checkbox('permission[]', $permission[25]->id, in_array($permission[25]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="delete {{ $permission[23]->module }}">
                {{ Form::checkbox('permission[]', $permission[26]->id, in_array($permission[26]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="all {{ $permission[23]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'island-all')) }} すべて</div>
        </div>
        {{-- Category block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[27]->module }}</div>
            <div class="view {{ $permission[27]->module }}">
                {{ Form::checkbox('permission[]', $permission[27]->id, in_array($permission[27]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[27]->module }}">
                {{ Form::checkbox('permission[]', $permission[28]->id, in_array($permission[28]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="edit {{ $permission[27]->module }}">
                {{ Form::checkbox('permission[]', $permission[29]->id, in_array($permission[29]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="delete {{ $permission[27]->module }}">
                {{ Form::checkbox('permission[]', $permission[30]->id, in_array($permission[30]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="all {{ $permission[27]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'category-all')) }} すべて</div>
        </div>
        {{-- Product block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[31]->module }}</div>
            <div class="view {{ $permission[31]->module }}">
                {{ Form::checkbox('permission[]', $permission[31]->id, in_array($permission[31]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[31]->module }}">
                {{ Form::checkbox('permission[]', $permission[32]->id, in_array($permission[32]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="edit {{ $permission[31]->module }}">
                {{ Form::checkbox('permission[]', $permission[33]->id, in_array($permission[33]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="delete {{ $permission[31]->module }}">
                {{ Form::checkbox('permission[]', $permission[34]->id, in_array($permission[34]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="all {{ $permission[31]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'product-all')) }} すべて</div>
        </div>
        {{-- How to use block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[35]->module }}</div>
            <div class="view {{ $permission[35]->module }}">
                {{ Form::checkbox('permission[]', $permission[35]->id, in_array($permission[35]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[35]->module }}"></div>
            <div class="edit {{ $permission[35]->module }}"></div>
            <div class="delete {{ $permission[35]->module }}"></div>
            <div class="all {{ $permission[35]->module }}">{{ Form::checkbox('asap',null,null, array('id'=>'how-to-use-all')) }} すべて</div>
        </div>
        {{-- Contact --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[36]->module }}</div>
            <div class="view {{ $permission[36]->module }}">
                {{ Form::checkbox('permission[]', $permission[36]->id, in_array($permission[36]->id, $rolePermissions) ? true : false) }}
            </div>
            <div class="add {{ $permission[36]->module }}"></div>
            <div class="edit {{ $permission[36]->module }}"></div>
            <div class="delete {{ $permission[36]->module }}"></div>
            <div class="all {{ $permission[36]->module }}">{{ Form::checkbox('asap', null, null, array('id'=>'contact-all')) }} すべて</div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center d-flex justify-content-center">
            <button type="submit" class="btn btn-submit">
                <span>Update</span>
            </button>
        </div>
        {!! Form::close() !!}
    </section>
</div>

@endsection

@push('custom-scripts')
<script type="text/javascript">
var SITEURL = '{{ URL::to('') }}';

$(document).ready( function () {
    $("#user-all").change(function() {
        if($(this).prop("checked") == true){
            $(".user input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".user input").prop("checked", false);
        }
    });
    $("#permission-all").change(function() {
        if($(this).prop("checked") == true){
            $(".role input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".role input").prop("checked", false);
        }
    });
    $("#account-authority-all").change(function() {
        if($(this).prop("checked") == true){
            $(".account-authority input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".account-authority input").prop("checked", false);
        }
    });
    $("#activity-log-all").change(function() {
        if($(this).prop("checked") == true){
            $(".activity-log input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".activity-log input").prop("checked", false);
        }
    });
    $("#all-account-all").change(function() {
        if($(this).prop("checked") == true){
            $(".all-account-list input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".all-account-list input").prop("checked", false);
        }
    });
    $("#operator-all").change(function() {
        if($(this).prop("checked") == true){
            $(".operator input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".operator input").prop("checked", false);
        }
    });
    $("#buyer-all").change(function() {
        if($(this).prop("checked") == true){
            $(".buyer input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".buyer input").prop("checked", false);
        }
    });
    $("#seller-all").change(function() {
        if($(this).prop("checked") == true){
            $(".seller input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".seller input").prop("checked", false);
        }
    });
    $("#island-all").change(function() {
        if($(this).prop("checked") == true){
            $(".island input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".island input").prop("checked", false);
        }
    });
    $("#category-all").change(function() {
        if($(this).prop("checked") == true){
            $(".category input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".category input").prop("checked", false);
        }
    });
    $("#product-all").change(function() {
        if($(this).prop("checked") == true){
            $(".product input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".product input").prop("checked", false);
        }
    });
    $("#how-to-use-all").change(function() {
        if($(this).prop("checked") == true){
            $(".inquery input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".inquery input").prop("checked", false);
        }
    });
    $("#contact-all").change(function() {
        if($(this).prop("checked") == true){
            $(".contact input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".contact input").prop("checked", false);
        }
    });
    $("#view").change(function() {
        if($(this).prop("checked") == true){
            $(".view input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".view input").prop("checked", false);
        }
    });
    $("#add").change(function() {
        if($(this).prop("checked") == true){
            $(".add input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".add input").prop("checked", false);
        }
    });
    $("#edit").change(function() {
        if($(this).prop("checked") == true){
            $(".edit input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".edit input").prop("checked", false);
        }
    });
    $("#delete").change(function() {
        if($(this).prop("checked") == true){
            $(".delete input").prop("checked", true);
        }
        else if($(this).prop("checked") == false){
            $(".delete input").prop("checked", false);
        }
    });
});

</script>
@endpush