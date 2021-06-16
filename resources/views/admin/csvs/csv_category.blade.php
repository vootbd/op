@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/csv-partial.css') }}">
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item active">カテゴリー CSV 登録</li>
@endpush

@section('content')
<div class="inner-content">
    @if ($message = Session::get('message'))
    <div class="custom-alert success">
        <span class="rito rito-check"></span>
        <p>{{ $message }}</p>
        <span class="rito rito-x" id="alertClose"></span>
    </div>
    @endif
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>カテゴリー CSV 登録</h1>
        </div>
        {!! Form::open(array('route' => 'csv.category.create', 'method'=> 'POST', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
        @csrf
        <div class="form-content">
            <div class="form-group d-flex">
                <div class="label-section d-flex align-items-center">
                    {!! Form::label('name', 'CSV ファイルを選択', ['class' => 'col-form-label']) !!}
                </div>
                <div class="field-section">
                    <label for="file-upload" class="custom-file-upload">
                        <i class="rito rito-upload"></i>ファイルを選択
                    </label>
                    <input name="csv_file" id="file-upload" type="file" accept=".csv" />
                    @if($errors->has('csv_file'))
                    <div class="error_msg">
                        {{ $errors->first('csv_file') }}
                    </div>
                    @endif
                    <span class="not-selected">選択されていません</span>
                </div>
            </div>
            <div class="form-group d-flex">
                <div class="label-section label-section-error d-flex align-items-center">
                </div>
                <div class="field-section">
                    <span class="custom_error_msg csv-upload">
                        @if ($message = Session::get('warning'))
                        {{$message['message']}}
                        @if($message['downloadUrl'] !='')
                            <a href="/csvs/errors/download/{{$message['fileName']}}">{{trans('csv.download')}}</a>
                        @endif
                        @endif
                    </span>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center form-submission">
            <button type="submit" class="btn btn-submit">登録する</button>
        </div>
        {!! Form::close() !!}
    </section>
    <section class="box-content mt-20">
        <div class="d-flex align-items-center page-title">
            <h1>CSV ファイルフォーマット</h1>
        </div>
        <div class="csv-file-format-block">
            <div class="d-flex download-section">
                <a class="btn btn-download" href="{{ asset('csvformat/csv-category_template.csv') }}" download="{{ asset('csvformat/csv-category_template.csv') }}"><i class="rito rito-download"></i>雛形ファイルダウンロード</a>
            </div>
            <div class="d-flex format-block-row">
                <div class="row-left">コントロールカラム</div>
                <div class="row-right">新規登録の場合は「n」更新する場合は「u」削除する場合は「d」を入力してください。</div>
            </div>
            @foreach ($categoryFields as $key => $item)
            <div class="d-flex format-block-row">
                <div class="row-left">{{ $item->column_label }}</div>
                <div class="row-right {{ ($key % 2 == 0) ? 'bg' : '' }}">{{ $item->column_description }}</div>
            </div>
            @endforeach
        </div>
    </section>
</div>

@endsection
@push('custom-scripts')
<script src="{{ asset('js/csv-partial.js') }}"></script>
@endpush