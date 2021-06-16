@extends('admin.layouts.admin')

@push('custom-style')
<link rel="stylesheet" href="{{ asset('css/admin/css/ledger-operator.css') }}">

<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/font-awesome.min.css') }}" />
<link rel="stylesheet" href="{{ asset('vendor/bs-v4-datepicker/gijgo.min.css') }}" />
@endpush

@push('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('seller.list') }}">管理 TOP</a></li>
<li class="breadcrumb-item">帳票作成</li>
@endpush

@section('content')
<div class="inner-content">
    {!! Form::open(array('route' => 'ledgers.sheet.pdf', 'method'=> 'POST', 'class'=> 'form-section', 'enctype' => 'multipart/form-data')) !!}
    @csrf
    <section class="box-content">
        <div class="d-flex align-items-center page-title">
            <h1>帳票の作成</h1>
        </div>
        <div class="form-block">
            <div class="form-content">
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('date-of-issue', '発行日', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section d-flex flex-column date-picker-block">
                        <div class="d-flex from-date">
                            {!! Form::text('date_of_issue', !empty($data) ? $data['date_of_issue']:'', ['class' => 'date-of-issue', 'id' => "startDate","readonly" => true]) !!}
                        </div>
                        <div class="error_msg" id="startDateError">
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('from-type', '帳票の種類
                        ', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section radio-block">
                        <label class="custom-radio">見積書
                            {!! Form::radio('type',1,['checked'=> "checked"]) !!}
                            <span class="checkmark"></span>
                        </label>
                        <label class="custom-radio">請求書
                            {!! Form::radio('type',2) !!}
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('message', 'メッセージ', ['class' => 'col-form-label']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('estimate', '1 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('estimate', !empty($data) ? $data['estimate']:'', ['class' => 'name', 'placeholder' => '以下の通りお見積り申し上げます。','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['estimate']))
                            {{ $errorMessage['estimate'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('estimate-subject', '2 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('estimate_subject', !empty($data) ? $data['estimate_subject']:'', ['class' => 'name', 'placeholder' => '件名:〇〇お見積','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['estimate_subject']))
                            {{ $errorMessage['estimate_subject'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('overview', '3 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('overview', !empty($data) ? $data['overview']:'', ['class' => 'name', 'placeholder' => '概要:','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['overview']))
                            {{ $errorMessage['overview'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('expiration-date', '4 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('expiration_date', !empty($data) ? $data['expiration_date']:'', ['class' => 'name', 'placeholder' => '有効期限:','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['expiration_date']))
                            {{ $errorMessage['expiration_date'][0] }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('payee', '振込先', ['class' => 'col-form-label']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('pa_estimate', '1 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('pa_estimate', !empty($data) ? $data['pa_estimate']:'', ['class' => 'name', 'placeholder' => '以下の通りお見積り申し上げます。','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['pa_estimate']))
                            {{ $errorMessage['pa_estimate'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('pa-estimate-subject', '2 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('pa_estimate_subject', !empty($data) ? $data['pa_estimate_subject']:'', ['class' => 'name', 'placeholder' => '件名:〇〇お見積','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['pa_estimate_subject']))
                            {{ $errorMessage['pa_estimate_subject'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('pa-overview', '3 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('pa_overview', !empty($data) ? $data['pa_overview']:'', ['class' => 'name', 'placeholder' => '概要:','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['pa_overview']))
                            {{ $errorMessage['pa_overview'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('pa-expiration-date', '4 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('pa_expiration_date', !empty($data) ? $data['pa_expiration_date']:'', ['class' => 'name', 'placeholder' => '有効期限:','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['pa_expiration_date']))
                            {{ $errorMessage['pa_expiration_date'][0] }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('remarks', '備考', ['class' => 'col-form-label']) !!}
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('estimate', '1 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('remarks[]',!empty($data) ? $data['remarks'][0]:'' , ['class' => 'name', 'placeholder' => '備考1行目','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['remarks.0']))
                            {{ $errorMessage['remarks.0'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('estimate-subject', '2 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('remarks[]', !empty($data) ? $data['remarks'][1]:'', ['class' => 'name', 'placeholder' => '備考2行目','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['remarks.1']))
                            {{ $errorMessage['remarks.1'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('overview', '3 行目', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section">
                        {!! Form::text('remarks[]', !empty($data) ? $data['remarks'][2]:'', ['class' => 'name', 'placeholder' => '備考3行目','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['remarks.2']))
                            {{ $errorMessage['remarks.2'][0] }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('shipping', '送料', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section shipping-block">
                        {!! Form::text('shipping', !empty($data) ? $data['shipping']:'', ['class' => 'shipping numeric','required' => false, 'onDrag'=>'return false', 'onDrop'=>'return false','autocomplete'=>'off' ]) !!}

                        <span class="d-flex align-items-center">円</span>
                    </div>
                </div>
                <div class="form-group d-flex border-bottom-0">
                    <div class="label-section d-flex align-items-center">
                    </div>
                    <div class="field-section shipping-block">
                        <div class="error_msg">
                            @if(!empty($errorMessage['shipping']))
                            {{ $errorMessage['shipping'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="form-group d-flex">
                    <div class="label-section d-flex align-items-center">
                        {!! Form::label('payment_deadline', 'お支払い期限', ['class' => 'col-form-label']) !!}
                    </div>
                    <div class="field-section ">
                        {!! Form::text('payment_deadline', !empty($data) ? $data['payment_deadline']:'', ['class' => 'name','required' => false]) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['payment_deadline']))
                            {{ $errorMessage['payment_deadline'][0] }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="box-content mt-20">
        <div class="form-block costum-form-block">
            <div class="form-content mobile" id="invoice-calculate">
                <div class="multi-item-header hd">
                    <div class="item-name common-hd">品目</div>
                    <div class="quantity common-hd">数量</div>
                    <div class="price common-hd">単価</div>
                    <div class="tax common-hd">税区分</div>
                    <div class="price-last common-hd">価格</div>
                </div>
                @php
                $count = 0;
                @endphp
                @if((isset($data['item_name']) && !empty($data['item_name'])) || (isset($data) && !empty($data)))
                @foreach($data['item_name'] as $row)
                <div class="form-group multi-item-header data-row" id="data-row-id-0">
                    <div class="item-name">
                        {!! Form::text('item_name[]', $row, ['class' => 'item-name-in']) !!}
                        <div class="error_msg">
                            @if(!empty($errorMessage['item_name.'.$count]))
                            {{ $errorMessage['item_name.'.$count][0] }}
                            @endif
                        </div>
                    </div>
                    <div class="quantity">
                        {!! Form::number('quantity[]', $data['quantity'][$count], ['class' => 'quantity-in numeric', 'step'=> '1', 'min'=>0]) !!}
                    </div>
                    <div class="price">
                        {!! Form::number('price[]', $data['price'][$count], ['class' => 'price-in numeric', 'step'=> '1', 'min'=>0]) !!}
                    </div>
                    <div class="tax">
                        {!! Form::select('tax[]', ['.08' => '8%', '.1' => '10%'], $data['tax'][$count], ['class' => 'custom-select tax-in']) !!}
                    </div>
                    <div class="price-last">
                        {!! Form::text('price_last[]', $data['price_last'][$count], ['class' => 'price-last-in numeric']) !!}
                    </div>
                </div>
                @php
                $count++;
                @endphp
                @endforeach
                @else
                <div class="form-group multi-item-header data-row" id="data-row-id-0">
                    <div class="item-name">
                        {!! Form::text('item_name[]', null, ['class' => 'item-name-in']) !!}
                        @if($errors->has("item_name.0"))
                        <div class="error_msg">
                            {{ $errors->first("item_name.0") }}
                        </div>
                        @endif
                    </div>
                    <div class="quantity">
                        {!! Form::number('quantity[]', null, ['class' => 'quantity-in numeric', 'step'=> '1', 'min'=>0]) !!}
                    </div>
                    <div class="price">
                        {!! Form::number('price[]', null, ['class' => 'price-in numeric', 'step'=> '1', 'min'=>0]) !!}
                    </div>
                    <div class="tax">
                        {!! Form::select('tax[]', ['.08' => '8%', '.1' => '10%'], 20, ['class' => 'custom-select tax-in']) !!}
                    </div>
                    <div class="price-last">
                        {!! Form::text('price_last[]', null, ['class' => 'price-last-in numeric']) !!}
                    </div>
                </div>
                @endif
                <div class="data-row-add"></div>
                <div class="d-flex justify-content-start more-section">
                    <button type="button" class="btn btn-add"><i class="rito rito-plus"></i> 行の追加</button>
                </div>
            </div>
        </div>
    </section>
    <div class="d-flex justify-content-center form-submission">
        <button type="submit" class="btn btn-submit">この内容で作成する</button>
    </div>
    {!! Form::close() !!}
</div>
@endsection
@push('custom-scripts')
<script src="{{ asset('vendor/bs-v4-datepicker/gijgo.min.js') }}"></script>
<script src="{{ asset('vendor/bs-v4-datepicker/messages.ja-jp.js') }}"></script>
<script>
    var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    $('#startDate').datepicker({
        uiLibrary: 'bootstrap4',
        locale: 'ja-jp',
        iconsLibrary: 'fontawesome',
        ignoreReadonly: true,
        allowInputToggle: true
    });

    $(document).ready(function() {
        $(document.body).on("keypress", '.numeric', function(event) {
            var charCode = (event.which) ? event.which : event.keyCode;

            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            } else if (charCode == 110) {
                return false;
            } else {
                return true;
            }

        });

        $(document.body).on("paste", '.numeric', function() {
            var element = $(this);
            setTimeout(function() {
                var inputVal =element.val();
                if(isNaN(inputVal)){
                   element.val('');
                   return false;
                }
            }, 100);
        });


        var max_items = 13;
        var wrapper = $(".data-row-add");
        var add_button = $(".btn-add");

        var x = "{{ $count }}";
        $(add_button).click(function(e) {
            e.preventDefault();
            if (x < max_items) {
                x++;
                $(wrapper).append(htmlRow(x));
            } else {
                alert("最大行14");
            }
        });

    });

    function htmlRow(x) {
        var view = '';
        view += '<div class="form-group multi-item-header data-row">';
        view += '<div class="item-name">';
        view += ' {!! Form::text("item_name[]", null, ["class" => "item-name-in"]) !!}'
        view += '@if($errors->has("item_name.' + x + '"))<div class="error_msg">{{ $errors->first("item_name.' + x + '") }}</div>@endif'
        view += '</div>';

        view += '<div class="quantity">';
        view += '{!! Form::number("quantity[]", null, ["class" => "quantity-in numeric","step"=> "1", "min"=>0]) !!}'
        view += '</div>';

        view += '<div class="price">';
        view += '{!! Form::number("price[]", null, ["class" => "price-in numeric","step"=> "1", "min"=>0]) !!}'
        view += '</div>';

        view += '<div class="tax">';
        view += '{!! Form::select("tax[]", [".08" => "8%", ".1" => "10%"], 20, ["class" => "custom-select tax-in"]) !!}'
        view += '</div>';

        view += '<div class="price-last">';
        view += '{!! Form::text("price_last[]", null, ["class" => "price-last-in numeric"]) !!}'
        view += '</div>';

        view += '</div>';
        return view;
    }
</script>
@endpush