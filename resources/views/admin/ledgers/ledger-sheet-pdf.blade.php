<!DOCTYPE html>
<html>

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title>Document</title>
   <style>
      @font-face {
         font-family: 'ipag';
         font-style: normal;
         font-weight: normal;
         src: url({{storage_path('/fonts/ipag.ttf')}}) format('truetype');
      }

      * {
         font-family: 'ipag';
         font-style: normal;
         font-weight: normal;
      }

      body {
         font-family: 'ipag';
         font-style: normal;
         margin: 1cm;
         font-weight: normal;
      }

      @page {
         margin: 0cm 0cm;
      }

      .page-break {
         page-break-after: always;
      }

      .pagenum::before {
         content: counter(page);
      }

      .bg-color {
         background-color: #F2F2F2;
      }

      .bg-row {
         background-color: #F2F2F2;
      }

      .br {
         border: 1px solid #EBEBEB;
         height: 30px;
      }

      .br-remove {
         border: 1px solid #fff;
         background-color: #fff;

      }

      .br-right {
         border-right: 1px solid #EBEBEB;
      }

      .remark:empty {
         height: 50px;
         font-size: 10px;
      }

      table th,
      td {
         color: #757575;
      }

      .costom-height {
         height: 25px;
      }
   </style>

</head>

<body>
   <div class="pdf-main-body">
      <p style="color:#2094AC;font-size:26px;margin-bottom: 10px;">{{ !empty($data['type'] == 1) ? '見積書': '請求書'}}</p>
      <div style="margin-bottom: 10px;background-color:#2094AC;height:5px"></div>
      <table width="100%" style="border-collapse: collapse; font-size: 10px;">
         <tr>
            <td align="left" colspan="9">
               <p style="font-size: 10px; padding:0; margin:0;">〒123-0000</p>
            </td>
            <td align="right">
               <p style="font-size: 12px; padding:0; margin:0;">[発行日] {{ !empty($data['date_of_issue']) ? $data['date_of_issue']: ' '}}
               </p>
            </td>
         </tr>
         <tr>
            <td align="left" colspan="9">
               <p style="font-size: 10px; padding:0; margin:0;">東京都新宿区新宿1-2-3</p>
            </td>
         </tr>
         <tr>
            <td align="left" colspan="9">
               <p style="font-size: 10px; padding:0; margin:0;">千成ビル203号</p>
            </td>
         </tr>
         <tr>
            <td align="left" colspan="9">
               <p style="font-size: 16px; padding:0; margin:0;">離島プロジェクト様</p>
            </td>
         </tr>
         <tr>
            <td align="left" colspan="9">
               <p style="font-size: 10px; padding:0; margin:0;">TEL:03-3333-3333</p>
            </td>
         </tr>
      </table>
      <table width="100%" style="border-collapse: collapse; font-size: 10px; padding-top: 16px;">
         <tr>
            <td align="left" width="75%">
               <p style="font-size: 10px; padding:0; margin:0;">{{ !empty($data['estimate']) ? $data['estimate']:' '}} </p>
            </td>
            <td align="right">
               <p style="font-size: 10px; padding:0; margin:0;">離島プロジェクト
               </p>
            </td>
         </tr>
         <tr>
            <td align="left">
               <p style="font-size: 10px; padding:0; margin:0;">{{ !empty($data['estimate_subject']) ? $data['estimate_subject']:' '}}</p>
            </td>
            <td align="right">
               <p style="font-size: 10px; padding:0; margin:0;">〒123-0000</p>
            </td>
         </tr>
         <tr>
            <td align="left">
               <p style="font-size: 10px; padding:0; margin:0;">{{ !empty($data['overview']) ? $data['overview']:' '}}</p>
            </td>
            <td align="right">
               <p style="font-size: 10px; padding:0; margin:0;">東京都新宿区新宿1-2-3</p>
            </td>
         </tr>
         <tr>
            <td align="left">
               <p style="font-size: 10px; padding:0; margin:0;">{{ !empty($data['expiration_date']) ? $data['expiration_date']:' '}}</p>
            </td>
            <td align="right">
               <p style="font-size: 10px; padding:0; margin:0;">千成ビル203号</p>
            </td>
         </tr>
         <tr>
            <td align="left">
               <p style="font-size: 10px; padding:0; margin:0;"> </p>
            </td>
            <td align="right">
               <p style="font-size: 10px; padding:0; margin:0;">千TEL:03-3333-3333</p>
            </td>
         </tr>
      </table>
      <table style="text-align: center; padding-top: 16px;border: 1px solid #EBEBEB;border-bottom: 1px solid #EBEBEB;border-left: 1px solid #EBEBEB;border-right: 1px solid #EBEBEB" width="100%" class="-resutl" cellspacing="0">
         <tr>
            <td align="left" width="20%" class="bg-color" style="border-top: 1px solid #EBEBEB;border-left: 1px solid #EBEBEB;border-right: 1px solid #EBEBEB;border-bottom: 1px solid #EBEBEB;">
               <p style="padding:0 10px 0 10px;font-size: 10px; margin:0;color:#2094AC">ご請求金額(振込)
               </p>
            </td>
            <td align="left" width="35%" style="border: 1px solid #EBEBEB;color:#2094AC">
               <p style="font-size: 20px; padding:0 10px 0 10px; margin:0;">{{'¥'.round($totalPrice+$data['shipping'])}}</p>
            </td>
            <td style="background-color: #F2F2F2;border: 1px solid #EBEBEB;margin:0; padding:0;">
               <p style="font-size: 10px; margin:0;color:#2094AC;padding:0 10px 0 10px">お振込先
               </p>
            </td>
         </tr>
         <tr>
            <td align="left" style="border-top: 2px solid #e0ebf3;border: 1px solid #EBEBEB" class="bg-color costom-height">
               <p style="font-size: 10px;padding:0 10px 0 10px; margin:0;color:#2094AC">お支払期限
               </p>
            </td>
            <td align="left" style="border: 1px solid #EBEBEB;color:#2094AC">
               <p align="left" style="font-size: 12px; padding-top:3px;padding:0 10px 0 10px; margin:0;">{{ !empty($data['payment_deadline']) ? $data['payment_deadline']:' '}}</p>
            </td>
            <td style="background-color: #F9F9F9;color:#2094AC">
               <p align="left" style="font-size: 10px; padding-top:3px;padding-left:20px; margin:0;">{{ !empty($data['pa_estimate']) ? $data['pa_estimate']:' '}}</p>
               <p align="left" style="font-size: 10px; padding-top:3px;padding-left:20px; margin:0;">{{ !empty($data['pa_estimate_subject']) ? $data['pa_estimate_subject']:' '}}</p>
               <p align="left" style="font-size: 10px; padding-top:3px;padding-left:20px; margin:0;">{{ !empty($data['pa_overview']) ? $data['pa_overview']:' '}}</p>
               <p align="left" style="font-size: 10px; padding-top:3px;padding-left:20px; margin:0;">{{ !empty($data['pa_expiration_date']) ? $data['pa_expiration_date']:' '}}</p>
            </td>
         </tr>
      </table>

      <table style="table-layout: fixed;text-align: center; padding-top: 20px;border: 1px solid #EBEBEB" width="100%" class="transfer-destination-resutl" cellspacing="0">
         <tr style="padding-top: 10px">
            <td class="br costom-height" width="40%" style="border: 0;">
               <p style="font-size: 13px; padding:0px; margin:0;">品目
               </p>
            </td>
            <td class="br costom-height" width="10%" style="border: 0;">
               <p style="font-size: 13px; padding:0px; margin:0;">数量
               </p>
            </td>
            <td class="br costom-height" width="25%" style="border: 0;">
               <p style="font-size: 13px; padding:0px; margin:0;">単価
               </p>
            </td>
            <td class="br costom-height" width="25%" style="border: 0;">
               <p style="font-size: 13px; padding:0px; margin:0;">金額(税込み)
               </p>
            </td>
         </tr>
         @for ($i = 0; $i <= 13; $i++) <tr class="{{ $i % 2 == 0 ? 'bg-row': '' }}">
            <td class="br costom-height">
               <p align="left" style="font-size: 10px; margin:0;padding:0 10px 0 10px;letter-spacing: 2px;">{{ !empty($data['item_name'][$i]) ? $data['item_name'][$i]:''}}</p>
            </td>
            <td class="br costom-height">
               <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px">{{ !empty($data['quantity'][$i]) ? $data['quantity'][$i]:''}}</p>
            </td>
            <td class="br costom-height">
               <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px">{{ !empty($data['price'][$i]) ? $data['price'][$i]:''}}</p>
            </td>
            <td class="br costom-height">
               <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px">{{ !empty($data['price_last'][$i]) ? '¥'.round($data['price_last'][$i]):''}}</p>
            </td>
            </tr>
            @endfor
            <tr class="bg-row">
               <td class="br-remove costom-height">
                  <p align="left" style="font-size: 10px; margin:0;padding:0 10px 0 10px;"></p>
               </td>
               <td class="br-remove br-right costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px;"></p>
               </td>
               <td class="br costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px;">商品合計</p>
               </td>
               <td class="costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px;">{{'¥'.round($totalPrice)}}</p>
               </td>
            </tr>
            <tr class="">
               <td class="br-remove costom-height">
                  <p align="left" style="font-size: 10px; margin:0;padding:0 10px 0 10px;"></p>
               </td>
               <td class="br-remove br-right costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px;"></p>
               </td>
               <td class="br costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px;">送料</p>
               </td>
               <td class="costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px;">{{'¥'.round($data['shipping'])}}</p>
               </td>
            </tr>
            <tr class="bg-row">
               <td class="br-remove costom-height">
                  <p align="left" style="font-size: 10px; margin:0;padding:0 10px 0 10px;"></p>
               </td>
               <td class="br-remove br-right costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0;padding:0 10px 0 10px;"></p>
               </td>
               <td class="br costom-height">
                  <p align="right" style="font-size: 10px; margin:0;padding:0 10px 0 10px;">請求金額</p>
               </td>
               <td class="costom-height">
                  <p align="right" style="font-size: 14px; margin:0;padding:5px;"><b>{{'¥'.round($totalPrice+$data['shipping'])}}</b></p>
               </td>
            </tr>
      </table>
      <div class="remark" style="margin-top:20px;border: 1px solid #EBEBEB;padding:10px 10px 20px 10px;color:#757575">
         <p style="margin:0;padding:0;font-size:10px;">{{ "<備考欄>" }}</p>
         @if(!empty($data['remarks']))
         @foreach($data['remarks'] as $item)
         <p style="margin:0;padding:0;font-size:10px;">{{ $item }}</p>
         @endforeach
         @endif
      </div>
   </div>
</body>


</html>