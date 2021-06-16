@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/role-edit.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('users.index') }}">管理 TOP</a></li>
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
            <h1>アカウント権限</h1>
        </div>
        {!! Form::model($role, ['method' => 'PATCH', 'class'=> 'pb-5', 'route' => ['roles.update', $role->id]]) !!}
        <div class="user-role-selection">
            <p class="name">ユーザー役割</p>
            <select class="custom-select sort-block" id="dynamic_select" name="name">
                <option value="1"@if($role->id == 1) selected="selected"@endif>管理者</option>
                <option value="2"@if($role->id == 2) selected="selected"@endif>運営者</option>
                <option value="3"@if($role->id == 3) selected="selected"@endif>事業者</option>
                <option value="4"@if($role->id == 4) selected="selected"@endif>バイヤー</option>
                <option value="5"@if($role->id == 5) selected="selected"@endif>地域商社</option>
            </select>
        </div>
        <div class="user-permission-block sp">
            <div class="module">モジュール</div>
            <div class="custom-control custom-checkbox view">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'view', 'onchange' => 'selectCheck(this)']) !!}
                {!! Form::label('view','見る', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="custom-control custom-checkbox add">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'add', 'onchange' => 'selectCheck(this)']) !!}
                {!! Form::label('add','追加', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="custom-control custom-checkbox edit">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'edit', 'onchange' => 'selectCheck(this)']) !!}
                {!! Form::label('edit','編集する', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="custom-control custom-checkbox delete">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'delete', 'onchange' => 'selectCheck(this)']) !!}
                {!! Form::label('delete','削除する', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block"></div>
        </div>
        @if($role->id == 1)
        {{-- user permission --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[0]->display_name }}</div>
            <div class="view {{ $permission[0]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="add {{ $permission[0]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="edit {{ $permission[0]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[0]->id, in_array($permission[0]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[0]->id]) !!}
                {!! Form::label('view'.$permission[0]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[0]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="all sp all-check-block custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'role-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('role-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- activity log block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[1]->display_name }}</div>
            <div class="view {{ $permission[1]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[1]->id, in_array($permission[1]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[1]->id]) !!}
                {!! Form::label('view'.$permission[1]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[1]->module }}"></div>
            <div class="edit {{ $permission[1]->module }}"></div>
            <div class="delete {{ $permission[1]->module }}"></div>
            <div class="all sp all-check-block {{ $permission[1]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'activity-log-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('activity-log-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- Unblock users block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[2]->display_name }}</div>
            <div class="view {{ $permission[2]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[2]->id, in_array($permission[2]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[2]->id]) !!}
                {!! Form::label('view'.$permission[2]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[2]->module }}"></div>
            <div class="edit {{ $permission[2]->module }}"></div>
            <div class="delete {{ $permission[2]->module }}"></div>
            <div class="all sp all-check-block {{ $permission[2]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'account-unblock-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('account-unblock-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- all account list block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[3]->display_name }}</div>
            <div class="view {{ $permission[3]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[3]->id, in_array($permission[3]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[3]->id]) !!}
                {!! Form::label('view'.$permission[3]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[3]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="edit {{ $permission[3]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[4]->id, in_array($permission[4]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[4]->id]) !!}
                {!! Form::label('view'.$permission[4]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[3]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[5]->id, in_array($permission[5]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[5]->id]) !!}
                {!! Form::label('view'.$permission[5]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[3]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'all-account-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('all-account-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- operator create block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[6]->display_name }}</div>
            <div class="view {{ $permission[6]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="add {{ $permission[6]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[6]->id, in_array($permission[6]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[6]->id]) !!}
                {!! Form::label('view'.$permission[6]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[6]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="delete {{ $permission[6]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="all sp all-check-block {{ $permission[6]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'operator-create-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('operator-create-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        @endif
        @if($role->id == 1 || $role->id == 2)
        {{-- buyer block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[7]->display_name }}</div>
            <div class="view {{ $permission[7]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[7]->id, in_array($permission[7]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[7]->id]) !!}
                {!! Form::label('view'.$permission[7]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[7]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[8]->id, in_array($permission[8]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[8]->id]) !!}
                {!! Form::label('view'.$permission[8]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[7]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[9]->id, in_array($permission[9]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[9]->id]) !!}
                {!! Form::label('view'.$permission[9]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[7]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[10]->id, in_array($permission[10]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[10]->id]) !!}
                {!! Form::label('view'.$permission[10]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[7]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'buyer-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('buyer-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- seller block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[11]->display_name }}</div>
            <div class="view {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[11]->id, in_array($permission[11]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[11]->id]) !!}
                {!! Form::label('view'.$permission[11]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[12]->id, in_array($permission[12]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[12]->id]) !!}
                {!! Form::label('view'.$permission[12]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[13]->id, in_array($permission[13]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[13]->id]) !!}
                {!! Form::label('view'.$permission[13]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[14]->id, in_array($permission[14]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[14]->id]) !!}
                {!! Form::label('view'.$permission[14]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[11]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'seller-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('seller-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- island block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[15]->display_name }}</div>
            <div class="view {{ $permission[15]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[15]->id, in_array($permission[15]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[15]->id]) !!}
                {!! Form::label('view'.$permission[15]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[15]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[16]->id, in_array($permission[16]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[16]->id]) !!}
                {!! Form::label('view'.$permission[16]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[15]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[17]->id, in_array($permission[17]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[17]->id]) !!}
                {!! Form::label('view'.$permission[17]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[15]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[18]->id, in_array($permission[18]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[18]->id]) !!}
                {!! Form::label('view'.$permission[18]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[15]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'island-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('island-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- Category block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[19]->display_name }}</div>
            <div class="view {{ $permission[19]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[19]->id, in_array($permission[19]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[19]->id]) !!}
                {!! Form::label('view'.$permission[19]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[19]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[20]->id, in_array($permission[20]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[20]->id]) !!}
                {!! Form::label('view'.$permission[20]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[19]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[21]->id, in_array($permission[21]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[21]->id]) !!}
                {!! Form::label('view'.$permission[21]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[19]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[22]->id, in_array($permission[22]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[22]->id]) !!}
                {!! Form::label('view'.$permission[22]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[19]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'category-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('category-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- Product block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[23]->display_name }}</div>
            <div class="view {{ $permission[23]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[23]->id, in_array($permission[23]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[23]->id]) !!}
                {!! Form::label('view'.$permission[23]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[23]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[24]->id, in_array($permission[24]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[24]->id]) !!}
                {!! Form::label('view'.$permission[24]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[23]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[25]->id, in_array($permission[25]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[25]->id]) !!}
                {!! Form::label('view'.$permission[25]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[23]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[26]->id, in_array($permission[26]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[26]->id]) !!}
                {!! Form::label('view'.$permission[26]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[23]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'product-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('product-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        @endif
        @if($role->id == 3)
        {{-- seller buyer block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[27]->display_name }}</div>
            <div class="view {{ $permission[27]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[27]->id, in_array($permission[27]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[27]->id]) !!}
                {!! Form::label('view'.$permission[27]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[27]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[28]->id, in_array($permission[28]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[28]->id]) !!}
                {!! Form::label('view'.$permission[28]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[27]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[29]->id, in_array($permission[29]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[29]->id]) !!}
                {!! Form::label('view'.$permission[29]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[27]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[30]->id, in_array($permission[30]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[30]->id]) !!}
                {!! Form::label('view'.$permission[30]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[27]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'seller-buyer-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('seller-buyer-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- seller Product block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[31]->display_name }}</div>
            <div class="view {{ $permission[31]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[31]->id, in_array($permission[31]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[31]->id]) !!}
                {!! Form::label('view'.$permission[31]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[31]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">追加</span>
                {!! Form::checkbox('permission[]', $permission[32]->id, in_array($permission[32]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[32]->id]) !!}
                {!! Form::label('view'.$permission[32]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="edit {{ $permission[31]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">編集する</span>
                {!! Form::checkbox('permission[]', $permission[33]->id, in_array($permission[33]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[33]->id]) !!}
                {!! Form::label('view'.$permission[33]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="delete {{ $permission[31]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">削除する</span>
                {!! Form::checkbox('permission[]', $permission[34]->id, in_array($permission[34]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[34]->id]) !!}
                {!! Form::label('view'.$permission[34]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="all sp all-check-block {{ $permission[31]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'seller-product-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('seller-product-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        @endif
        @if($role->id == 4)
        {{-- Buyer product list block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[35]->display_name }}</div>
            <div class="view {{ $permission[35]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[35]->id, in_array($permission[35]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[35]->id]) !!}
                {!! Form::label('view'.$permission[35]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[35]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="edit {{ $permission[35]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="delete {{ $permission[35]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="all sp all-check-block {{ $permission[35]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'buyer-product-list-all']) !!}
                {!! Form::label('buyer-product-list-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- Buyer Product detail --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[36]->display_name }}</div>
            <div class="view {{ $permission[36]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[36]->id, in_array($permission[36]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[36]->id]) !!}
                {!! Form::label('view'.$permission[36]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[36]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="edit {{ $permission[36]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="delete {{ $permission[36]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="all sp all-check-block {{ $permission[36]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'buyer-product-detail-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('buyer-product-detail-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        @endif
        {{-- Vendor Block  --}}
        @if($role->id == 5)
             {{-- seller block --}}
             <div class="user-permission-block list">
                <div class="module">{{ $permission[11]->display_name }}</div>
                <div class="view {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                    <span class="sp-span">見る</span>
                    {!! Form::checkbox('permission[]', $permission[11]->id, in_array($permission[11]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[11]->id]) !!}
                    {!! Form::label('view'.$permission[11]->id,' ', ['class' => 'custom-control-label']) !!}
                </div>
                <div class="add {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                    <span class="sp-span">追加</span>
                    {!! Form::checkbox('permission[]', $permission[12]->id, in_array($permission[12]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[12]->id]) !!}
                    {!! Form::label('view'.$permission[12]->id,' ', ['class' => 'custom-control-label']) !!}
                </div>
                <div class="edit {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                    <span class="sp-span">編集する</span>
                    {!! Form::checkbox('permission[]', $permission[13]->id, in_array($permission[13]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[13]->id]) !!}
                    {!! Form::label('view'.$permission[13]->id,' ', ['class' => 'custom-control-label']) !!}
                </div>
                <div class="delete {{ $permission[11]->module }} custom-control custom-checkbox block-data">
                    <span class="sp-span">削除する</span>
                    {!! Form::checkbox('permission[]', $permission[14]->id, in_array($permission[14]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[14]->id]) !!}
                    {!! Form::label('view'.$permission[14]->id,' ', ['class' => 'custom-control-label']) !!}
                </div>
                <div class="all sp all-check-block {{ $permission[11]->module }} custom-control custom-checkbox">
                    {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'seller-all', 'onchange' => 'selectCheckAll(this)']) !!}
                    {!! Form::label('seller-all','すべて', ['class' => 'custom-control-label']) !!}
                </div>
            </div>
        {{-- Buyer product list block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[35]->display_name }}</div>
            <div class="view {{ $permission[35]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[35]->id, in_array($permission[35]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[35]->id]) !!}
                {!! Form::label('view'.$permission[35]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[35]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="edit {{ $permission[35]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="delete {{ $permission[35]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="all sp all-check-block {{ $permission[35]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'buyer-product-list-all']) !!}
                {!! Form::label('buyer-product-list-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        {{-- Buyer Product detail --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[36]->display_name }}</div>
            <div class="view {{ $permission[36]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[36]->id, in_array($permission[36]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[36]->id]) !!}
                {!! Form::label('view'.$permission[36]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[36]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="edit {{ $permission[36]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="delete {{ $permission[36]->module }} custom-control custom-checkbox block-data">
            </div>
            <div class="all sp all-check-block {{ $permission[36]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'buyer-product-detail-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('buyer-product-detail-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        @endif
        {{-- Inqurey block --}}
        <div class="user-permission-block list">
            <div class="module">{{ $permission[37]->display_name }}</div>
            <div class="view {{ $permission[37]->module }} custom-control custom-checkbox block-data">
                <span class="sp-span">見る</span>
                {!! Form::checkbox('permission[]', $permission[37]->id, in_array($permission[37]->id, $rolePermissions) ? true : false, ['class' => 'custom-control-input', 'id' => 'view'.$permission[37]->id]) !!}
                {!! Form::label('view'.$permission[37]->id,' ', ['class' => 'custom-control-label']) !!}
            </div>
            <div class="add {{ $permission[37]->module }}"></div>
            <div class="edit {{ $permission[37]->module }}"></div>
            <div class="delete {{ $permission[37]->module }}"></div>
            <div class="all sp all-check-block {{ $permission[37]->module }} custom-control custom-checkbox">
                {!! Form::checkbox('asap', null, null, ['class' => 'custom-control-input', 'id' => 'inquery-all', 'onchange' => 'selectCheckAll(this)']) !!}
                {!! Form::label('inquery-all','すべて', ['class' => 'custom-control-label']) !!}
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center d-flex justify-content-center">
            <button type="submit" class="btn btn-submit">
                <span>更新</span>
            </button>
        </div>
        {!! Form::close() !!}
    </section>
</div>

@endsection

@push('custom-scripts')
<script type="text/javascript">
var SITEURL = '{{ URL::to('') }}';

function selectCheckAll(e) {
    var name = e.id.split('-all')[0];
    if($(e).prop("checked") == true){
        $("."+ name +" input").prop("checked", true);
    }
    else if($(e).prop("checked") == false){
        $("."+ name +" input").prop("checked", false);
    }
}

function selectCheck(e) {
    var name = e.id;
    if($(e).prop("checked") == true){
        $("."+ name +" input").prop("checked", true);
    }
    else if($(e).prop("checked") == false){
        $("."+ name +" input").prop("checked", false);
    }
}

$(document).ready( function () {
    $('#dynamic_select').on('change', function () {
        var data = $(this).val();
        var url = SITEURL + '/roles/' + data + '/edit';
          if (url) {
              window.location = url;
          }
          return false;
    });
});

</script>
@endpush