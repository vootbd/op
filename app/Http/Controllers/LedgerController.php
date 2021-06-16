<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Validator;
use PDF;




class LedgerController extends Controller
{
    protected $user;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user->hasRole('operator') === false && $user->hasRole('seller') === false) {
                return abort(401);
            }
            return $next($request);
        });
    }

    public function ledgerSheet()
    {
        return view('admin.ledgers.operator-ledger-sheet');
    }

    public function ledgerSheetPdf(Request $request)
    {

        $rules = [
            'estimate' => 'max:40',
            'estimate_subject' => 'max:40',
            'overview' => 'max:40',
            'expiration_date' => 'max:40',
            'pa_estimate' => 'max:30',
            'pa_estimate_subject' => 'max:30',
            'pa_overview' => 'max:30',
            'pa_expiration_date' => 'max:30',
            'remarks.0' => 'max:60',
            'remarks.1' => 'max:60',
            'remarks.2' => 'max:60',
            'shipping' => 'numeric|nullable',
            'payment_deadline' => 'max:25',
            'item_name.0' => 'max:40',
            'item_name.1' => 'max:40',
            'item_name.2' => 'max:40',
            'item_name.3' => 'max:40',
            'item_name.4' => 'max:40',
            'item_name.5' => 'max:40',
            'item_name.6' => 'max:40',
            'item_name.7' => 'max:40',
            'item_name.8' => 'max:40',
            'item_name.9' => 'max:40',
            'item_name.10' => 'max:40',
            'item_name.11' => 'max:40',
            'item_name.12' => 'max:40',
            'item_name.13' => 'max:40',
            'price_last.*'=> 'numeric|nullable',
            'quantity.*'=> 'numeric|nullable',
            'price.*'=> 'numeric|nullable'
        ];

        $messages = [
            'estimate.max' => trans('ledger.max_char'),
            'estimate_subject.max' => trans('ledger.max_char'),
            'overview.max' => trans('ledger.max_char'),
            'expiration_date.max' => trans('ledger.max_char'),
            'pa_estimate.max' => trans('ledger.max_char_pay'),
            'pa_estimate_subject.max' => trans('ledger.max_char_pay'),
            'pa_overview.max' => trans('ledger.max_char_pay'),
            'pa_expiration_date.max' => trans('ledger.max_char_pay'),
            'shipping.max' => trans('ledger.max_char'),
            'payment_deadline.max' => trans('ledger.max_deadline'),
            'remarks.0.max' => trans('ledger.max_char_remark'),
            'remarks.1.max' => trans('ledger.max_char_remark'),
            'remarks.2.max' => trans('ledger.max_char_remark'),
            'item_name.0.max' => trans('ledger.max_char'),
            'item_name.1.max' => trans('ledger.max_char'),
            'item_name.2.max' => trans('ledger.max_char'),
            'item_name.3.max' => trans('ledger.max_char'),
            'item_name.4.max' => trans('ledger.max_char'),
            'item_name.5.max' => trans('ledger.max_char'),
            'item_name.6.max' => trans('ledger.max_char'),
            'item_name.7.max' => trans('ledger.max_char'),
            'item_name.8.max' => trans('ledger.max_char'),
            'item_name.9.max' => trans('ledger.max_char'),
            'item_name.10.max' => trans('ledger.max_char'),
            'item_name.11.max' => trans('ledger.max_char'),
            'item_name.12.max' => trans('ledger.max_char'),
            'item_name.13.max' => trans('ledger.max_char'),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        $data = $request->all();
        if ($validator->passes()) {
            $totalPrice = 0;
            if (isset($data['price_last']) && !empty($data['price_last'])) {
                foreach ($data['price_last'] as $subtotal) {
                    $totalPrice = $totalPrice + $subtotal;
                }
            }
            $pdf = PDF::loadView('admin.ledgers.ledger-sheet-pdf', compact('data','totalPrice'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('ledger-sheet.pdf'); 
        }
        $errorMessage = $validator->errors()->messages();
        return view('admin.ledgers.operator-ledger-sheet', compact('validator','errorMessage','data'));
    }
}
