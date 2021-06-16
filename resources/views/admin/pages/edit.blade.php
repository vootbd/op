@extends('admin.layouts.admin')

@push('custom-style')
    <link rel="stylesheet" href="{{ asset('css/admin/css/pages-edit.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}" />
@endpush

@section('content')
<div class="inner-content">
    @push('breadcrumb')
        <li class="breadcrumb-item"><a href="{{ route('seller.list') }}">TOP</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a
                href="{{ route('pages.index') }}">ページ一覧</a></li>
        <li class="breadcrumb-item active" aria-current="page">ページ編集</li>
    @endpush

    {{-- @include('admin.partials.breadcrumb') --}}
    @if($message = Session::get('message'))
        <div class="custom-alert success">
            <span class="rito rito-check"></span>
            <p>{{ $message }}</p>
        </div>
    @endif
    @if($message = Session::get('message_danger'))
        <div class="custom-alert alert">
            <span class="rito"></span>
            <p>{{ $message }}</p>
        </div>
    @endif
    <div class="ajax-response-form-page-update"></div>
    <section id="scrollTop" class="box-content mt-20">
        <div class="d-flex align-items-center page-title">
            <h1>ページ編集</h1>
        </div>
        <div class="box-inner-content">
            {!! Form::open(array('route'=> ['pages.update',$page->id],'method'=> 'PATCH', 'class'=> 'form-block',
            'id'=>'form-page-update', 'enctype' => 'multipart/form-data')) !!}
            <div class="inner-form">
                @csrf
                <div class="form-content">
                    <div class="form-group">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('url_map', 'URL', ['class' => 'col-form-label']) !!}
                            <span class="required-span">必須</span>
                        </div>
                        <div class="field-section">
                            <div class="url-input">

                                <label for="url_map" class="class">
                                    <a href="{{ request()->getSchemeAndHttpHost().'/page'.$directoryName.Str::limit($page->url_map) }}"
                                        target="_self" class="link" style="color: #2094AC">
                                        {{ 'https://'.request()->getHost().'/pages'.$directoryName.Str::limit($page->url_map) }}
                                    </a>
                                </label>
                                <button type="button" class="btn-url-map">編集</button>

                                {!! Form::text('url_map', $page->url_map, ['class' => 'url-map-edit', 'data-type' =>
                                'update', 'id' => 'page_url_map','data-url' => '/urlCheck/pages', 'required' => true])
                                !!}
                                {!! Form::hidden('url_map_copy', $page->url_map, ['id' => 'url_map_copy']) !!}
                                <button type="button" class="d-none btn-check-url update"
                                    id="btn-check-url">重複チェック</button>

                            </div>
                            <div class="error_msg url_map"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('page_title', 'タイトル', ['class' => 'col-form-label']) !!}
                            <span class="required-span">必須</span>
                        </div>
                        <div class="field-section">
                            {!! Form::text('page_title', $page->page_title, ['class' => 'name', 'required' => true]) !!}
                            <div class="error_msg page_title"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('description', '本文', ['class' => 'col-form-label']) !!}
                            <span class="required-span">必須</span>
                        </div>
                        <div class="field-section">
                            {!! Form::textarea('description', $page->description, ['class' => 'xl-textarea', 'required'
                            => true]) !!}
                            <div class="error_msg description"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('page_css', 'ページCSS', ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="field-section">
                            {!! Form::textarea('page_css', $page->page_css, ['class' => 'name']) !!}
                            <div class="error_msg page_css"></div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <div class="label-section d-flex align-items-center">
                            {!! Form::label('search_keys', '検索ワード', ['class' => 'col-form-label']) !!}
                        </div>
                        <div class="field-section">
                            {!! Form::textarea('search_keys', $page->search_keys, ['class' => 'name']) !!}
                            <div class="error_msg search_keys"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="right-sidebar">
                <div class="publish-content">
                    <div class="publish-header">
                        <span>公開設定</span>
                    </div>
                    <div class="publish-body">
                        <div>
                            {!! Form::label('publishing_status', 'ステータス', ['class' => 'publish-label']) !!}
                            {!! Form::select('publishing_status', ['1' => '公開', '2' => '下書き', '3' =>
                            '非公開'],$page->is_active, ['class' => 'custom-select']) !!}
                            <div class="error_msg publishing_status"></div>
                        </div>
                        <div>
                            {!! Form::label('publishing_date', '公開日', ['class' => 'publish-label']) !!}
                            {!! Form::text('publishing_date',$page->publishing_date == null ? $page->publishing_date :
                            toLocalDate($page->publishing_date), ['class' => 'title publish', 'id' =>
                            "startDate",'placeholder'=>'選択してください ']) !!}
                            <div class="error_msg publishing_date"></div>
                        </div>
                        <div>
                            {!! Form::label('publication_end_date', '公開終了日', ['class' => 'publish-label']) !!}
                            @if($page->publishing_end_date != null)
                                {!! Form::text('publishing_end_date',$page->publishing_end_date == null ?
                                $page->publishing_end_date : toLocalDate($page->publishing_end_date), ['class' => 'title
                                publish', 'id' => "endDate",'placeholder'=>'選択してください ']) !!}
                            @else
                                <span id="publishing-end-date">
                                    {!! Form::text('publishing_end_date',$page->publishing_end_date == null ?
                                    $page->publishing_end_date : toLocalDate($page->publishing_end_date), ['class' =>
                                    'title', 'id' => "endDate",'placeholder'=>'選択してください ']) !!}
                                </span>
                            @endif
                            <div class="error_msg publishing_end_date"></div>
                        </div>
                        <div
                            class="publication-date publish {{ ($page->publishing_end_date == null) ? 'd-flex' : 'd-none' }}">
                            <button type="button" id="set" class="btn-set">設定する</button>
                        </div>
                        <div class="directory">
                            <label for="">ディレクトリ</label>
                            <div class="d-flex directory-label">{{ $activeDirectory['name'] }}
                            </div>
                            <div class="directory-change" id="change-dir">
                                <button type="button" class="btn">ディレクトリの変更</button>
                            </div>
                            <div class="directory-section" id="directory-section">
                                <ul class="directory-ul">
                                    @foreach($directories as $directory)
                                        <li>
                                            <label class="custom-radio">
                                                {!! Form::radio('directory',$directory['id'], $directory['id'] ==
                                                $activeDirectory['id'] ? 'checked':'',['class' => 'dir-radio']) !!}
                                                <span class="checkmark"></span>
                                                <span
                                                    class="checked-dir-name">{{ $directory['name'] }}</span>
                                            </label>
                                        <li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="error_msg directory"></div>
                        </div>
                        <div class="submit-section">
                            <div class="spinner-border text-primary" role="status" id="ajax-loader">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <button class="btn-submit" id="btn-form-submit">更新</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
</div>
@endsection

@push('custom-scripts')
    <script src="{{ asset('vendor/bs-v4-datepicker/gijgo.min.js') }}"></script>
    <script src="{{ asset('vendor/bs-v4-datepicker/messages.ja-jp.js') }}"></script>
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
    <script>
        if (window.location.protocol == 'http:') {
            var PAGE_URL_STATIC = location.protocol + SITEURL + "/page "
        } else {
            var PAGE_URL_STATIC = SITEURL + "/page "
        }
        directory_id = {{$page->directory_id}};
        page_edit = true;
        page_id = {{$page->id}};
        $('.select2').select2({
            "language": {
                "noResults": function () {
                    return "結果が見つかりません";
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    </script>
    <script src="{{ asset('js/page.js') }}"></script>
@endpush