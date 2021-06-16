@extends('front.layouts.frontend')

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('css/admin/css/pages-show.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}" />
@endpush

@section('content')
@if($page->page_css)
    <style>
        {{$page->page_css}}
    </style>
@endif
<div class="inner-content">
    @push('breadcrumb')
        <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">TOP</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a
            href="{{ route('pages.index') }}">ページ</a></li>
        <li class="breadcrumb-item active" aria-current="page">ページ編集</li>
    @endpush

    <section id="scrollTop" class="box-content mt-20">
        <div class="d-flex align-items-center page-title-top">
            <h1>ページ編集</h1>
        </div>
        <div class="box-inner-content">
            <div class="page-detail"> 
                <div class="page-title">
                    {{$page->page_title??''}}
                </div>
                <div class="page-details">
                    {!! nl2br($page->description)??'' !!}
                </div>
            </div>

    </section>
</div>
@endsection