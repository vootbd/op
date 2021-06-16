@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/csv-partial.css') }}">
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
    @if ($message = Session::get('warning'))
        <div class="custom-alert warning">
            <span class="rito rito-check"></span>
            <p>{{ $message['message']}} @if($message['url'] !='') <a href="{{asset('csvs/'.$message['url'])}}" download="{{asset('csvs/'.$message['url'])}}">{{trans('csv.url_more_details')}} {{$message['url']}}</a> @endif</p>
            <span class="rito rito-x" id="alertClose"></span>
        </div>
    @endif
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>離島 CSV 登録</h1>
        </div>
        {!! Form::open(array('route' => 'csv.island-create', 'method'=> 'POST', 'class'=> 'form-block', 'enctype' => 'multipart/form-data')) !!}
            @csrf
             <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('name', 'CSV ファイルを選択', ['class' => 'col-form-label']) !!} 
                    </div>
                    <div class="field-section">
                        <label for="file-upload" class="custom-file-upload">
                            <i class="rito rito-upload"></i> ファイルを選択
                        </label>
                        <input name="csv_file" id="file-upload" type="file" accept=".csv"/>
                        @if($errors->has('file_upload'))
                            <div class="error_msg">
                                {{ $errors->first('file_upload') }}
                            </div>
                        @endif
                        <span class="not-selected">選択されていません</span>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section label-section-error d-flex align-items-center">
                    </div>
                    <div class="field-section">
                        <span class="custom_error_msg"></span>
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
                <button type="submit" class="btn btn-download"><i class="rito rito-download"></i> CSV ダウンロード</button>
            </div>
            <div class="d-flex format-block-row">
                <div class="row-left">コントロールカラム</div>
                <div class="row-right">新規登録の場合は「n」更新する場合は「u」削除する場合は「d」を入力してください。</div>
            </div>
            <div class="d-flex format-block-row">
                <div class="row-left">離島 ID</div>
                <div class="row-right bg">新規登録の場合は空にしてください。既存の商品を更新する場合は、離島 ID を指定してください。</div>
            </div>
            <div class="d-flex format-block-row">
                <div class="row-left">離島の名前</div>
                <div class="row-right"></div>
            </div>
            <div class="d-flex format-block-row">
                <div class="row-left">離島の管轄自治体</div>
                <div class="row-right bg"></div>
            </div>
            <div class="d-flex format-block-row">
                <div class="row-left">管轄自治体コード</div>
                <div class="row-right"></div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('custom-scripts')
<script src="{{ asset('js/csv-partial.js') }}"></script>
@endpush