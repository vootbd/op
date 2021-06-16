@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/directory-index.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/css/drag-drop-menu.css') }}"> 
@endpush

@section('content')
<div class="inner-content">
    @push('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">TOP</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pages.index') }}">ページ一覧</a></li>
    <li class="breadcrumb-item">ページ階層管理</a></li>
    @endpush

    @if ($message = Session::get('message'))
    <div class="custom-alert success">
        <span class="rito rito-check"></span>
        <p>{{ $message }}</p>
    </div>
    @endif
    <section id="scrollTop" class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>ページ階層管理</h1>
        </div>
        <div class="box-inner-content">
            {!! Form::open(array('route'=> ['directories.store'],'method'=> 'POST', 'id'=>'form-directory-create','class'=> 'form-block-directory', 'enctype' => 'multipart/form-data')) !!}
            @csrf
            {!! Form::hidden('directory_id',0, ['id' => 'directory-id']) !!}
            <div class="d-flex mobile-res">
                <div class="directory-dropdown">
                    <button type="button" id="dir-toggle-btn" data-id="0" data-name="ルート" class="d-flex align-items-center dir-dropbtn custom-select">ルート</button>
                    <div id="dir-dropdown" class="dir-dropdown-content">
                        <a href="javascript:void(0)" id="dir-blank" data-id="0" data-name="ルート" class="blank-dir select-directory">ルート</a>
                        @foreach ($directories as $directory)
                            <a href="javascript:void(0)" id="dir-{{$directory->id}}" data-id="{{$directory->id}}" data-name="{{$directory->name}}" class="root-dir select-directory">{{ $directory->name }}</a>
                            @foreach ($directory->children as $children)
                                <a href="javascript:void(0)" id="dir-{{$children->id}}" data-id="{{$children->id}}" data-name="{{$children->name}}" class="second-dir select-directory">
                                    -{{ $children->name }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                    <div class="error_msg directory_id"></div>
                </div>
                <div class="directory-input">
                    {!! Form::text('directory', null, ['id' => 'directory','class' => 'name', 'required' => true,'placeholder' => 'ディレクトリ名']) !!}
                    <div class="error_msg directory"></div>
                </div>
                <div class="submit-section position-relative">
                    <button class="btn btn-submit" id="btn-form-submit">公開</button>
                    <div class="spinner-border text-primary custom-spinner" role="status" id="ajax-loader">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
            <div class="cf nestable-lists">
                <div class="dd drag_disabled" id="nestable">
                    <ol class="dd-list">
                        @foreach ($directories as $directory)
                        <li class="dd-item" data-id="{{$directory->id}}">
                            <span class="dir-count">ページ {{count($directory->pages)}} 件</span>
                            <span class="fa fa-trash custom-icon trash pull-right" onclick="deleteModalDisplay(this)"></span>
                            <a href="{{ route('directories.edit', $directory->id) }}">{{$directory->name}}</a>
                            <div class="dd-handle"></div>
                            <ol class="dd-list">
                                @foreach ($directory->children as $children)
                                <li class="dd-item" data-id="{{$children->id}}">
                                    <span class="dir-count">ページ {{count($children->pages)}} 件</span>
                                    <span class="fa fa-trash custom-icon trash pull-right" onclick="deleteModalDisplay(this)"></span>
                                    <a href="{{ route('directories.edit', $children->id) }}">{{$children->name}}</a>
                                    <div class="dd-handle"></div>
                                    {{-- third child --}}
                                    <ol class="dd-list">
                                        @foreach ($children->children as $children2)
                                        <li class="dd-item" data-id="{{$children2->id}}">
                                            <span class="dir-count">ページ {{count($children2->pages)}} 件</span>
                                            <span class="fa fa-trash custom-icon trash pull-right" onclick="deleteModalDisplay(this)"></span>
                                            <a href="{{ route('directories.edit', $children2->id) }}">{{$children2->name}}</a>
                                            <div class="dd-handle"></div>
                                        </li>
                                        @endforeach
                                    </ol>
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
<script src="{{asset('js/directory.js')}}"></script>
@endpush