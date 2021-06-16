@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/categorie-index.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/css/drag-drop-menu.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item active" aria-current="page">カテゴリー一覧</li>
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
            <h1>カテゴリー登録</h1>
        </div>
        <div class="box-inner-content">
            <button class="menu-button" onclick="collapseForm(this)"><div class="d-flex align-items-center"><span class="fa fa-plus-circle custom-icon"></span><span>カテゴリーの作成</span></div></button>
            <button class="menu-button enable-ordering" ><div class="d-flex align-items-center"><span class="fa fa-bars custom-icon"></span><span id="text-change">カテゴリーの並び替え</span><div></button>
            <a class="menu-button btn btn-download" href="{{ route('csv.category-export')}}"><i class="rito rito-download custom-icon"></i>CSV ダウンロード </a>
            <a class="menu-button btn btn-setting" href="{{ route('csv.control','category')}}"><i class="rito rito-settings custom-icon"></i>CSV 出力項目設定</a>
            {!! Form::open(array('route'=>'categories.store','method'=> 'POST', 'class'=> $errors->any()? 'form-block display-content':'form-block' )) !!}
            @csrf
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('dropdowns', 'カテゴリー階層', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        <select name="dropdowns" class="custom-select" id="dropdowns">
                            {!! $dropdowns !!}
                        </select>
                        @if($errors->has('dropdowns'))
                            <div class="error_msg">
                                {{ $errors->first('dropdowns') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'カテゴリー名', ['class' => 'col-form-label']) !!}
                        <span class="required-span">必須</span>
                    </div>
                    <div class="field-section">
                        {!! Form::text('name', null, ['class' => 'name', 'required' => true]) !!}
                        @if($errors->has('name'))
                            <div class="error_msg">
                                {{ $errors->first('name') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="d-flex justify-content-left form-submission">
                    <button type="submit" class="btn btn-submit">作成</button>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="cf nestable-lists">
                <div class="dd drag_disabled" id="nestable">
                    <ol class="dd-list">
                        @foreach ($categories as $category)
                        <li class="dd-item" data-id="{{$category->id}}">
                            <span class="rito rito-trash-2 custom-icon trash pull-right" onclick="deleteModalDisplay(this)"></span>
                            <a href={{route('categories.edit',$category->id)}}>{{$category->name}}</a>
                            <div class="dd-handle"></div>
                            <ol class="dd-list">
                                @foreach ($category->children as $children)
                                <li class="dd-item" data-id="{{$children->id}}">
                                    <span class="rito rito-trash-2 custom-icon trash pull-right" onclick="deleteModalDisplay(this)"></span>
                                    <a href={{route('categories.edit',$children->id)}}>{{$children->name}}</a>
                                    <div class="dd-handle"></div>
                                </li>
                                @endforeach
                            </ol>
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </section>
</div>

@include('admin.partials.delete_modal')

@endsection

@push('custom-scripts')
<script src="{{asset('js/jquery.nestable.js')}}"></script>
<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var dragDiv = $('#nestable').nestable({
    group:1,
    maxDepth : 2,
    expandBtnHTML:'',
    collapseBtnHTML: ''
});
$(document).ready(function()
{
    var updateOutput = function(e)
    {
        $(".enable-ordering").attr("disabled",true);
        var list   = e.length ? e : $(e.target),output = list.data('output');
        var category_data = $('#nestable').nestable('serialize');
        var json_category_data = JSON.stringify(category_data);
        $(".enable-ordering").html('').prepend('<div class="spinner-border spinner-border-sm text-primary"></div>');
        if (window.JSON) {
            $.ajax({
                type:'POST',
                url:'/categories/update-sorting',
                data:{categories:json_category_data},
                success:function(data){
                    location.reload();
                }
            });
        } else {
            alert('JSON browser support required for this demo.');
        }
    };
    $('.enable-ordering').on('click',function(){
        $(dragDiv).toggleClass('drag_disabled')
        $(this).toggleClass('inverse')
        $(this).attr("id","saveOrder");
        $("#text-change").text(function(i, v){
            return v === 'カテゴリーの並び替え' ? '並び替えを保存する' : 'カテゴリーの並び替え'
        })
        $("#saveOrder").on('click',updateOutput);
    })
});

function deleteModalDisplay(current_span){
    var li_id = $(current_span).parent().data('id')
    $('#delete-modal').modal('show');
    $('#modal-delete-button').attr('onclick', 'removeCategory('+li_id+')');
}
function removeCategory(li_id){
    $('.dd-item').filter('[data-id='+li_id+']').remove();
    $.ajax({
        type:'DELETE',
        url:'categories/delete',
        data:{id:li_id,categories:$('#nestable').nestable('serialize')},
        success:function(data){
            $('#dropdowns').html(data);
        }
    });
    $('#delete-modal').modal('hide');
}
function collapseForm(button){
    var form = $('.form-block')
    if($(form).css('display') == 'none'){
        $(form).show(500)
    }
    else{
        $(form).hide(500)
    }
}
</script>
@endpush