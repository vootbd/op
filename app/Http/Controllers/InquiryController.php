<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use DB;
use Mail;
use App\Inquiry;
use App\Product;

class InquiryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('permission:inquery', ['only' => ['create', 'store']]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $product = Product::find($request['product']);
        $roles = DB::table('roles')->pluck('name', 'id');
        return view('admin.inquiry.create', compact('roles', 'product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'required|max:255',
            'inquiry_items' => 'required',
            'inquiry_content' => 'required|max:2000',
            'email' => 'required|email|max:120',
            'confrim_email' => 'required|same:email',
        ], [
            'name.max' => trans('error.max_char'),
            'email.max' => trans('error.max_mail'),
            'inquiry_content.max' => trans('error.max_textarea'),
            'confrim_email.same' => trans('error.conf_mail'),
        ]);

        $data = $request->all();
        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        $inquiry = Inquiry::create($data);

        //Email sent for inquiry start
        $maildata = array(
            'name'=> $data['name'] ,
            'email' => $data['email'],
            'inquiry_content' => $data['inquiry_content']
        );
        Mail::send('admin.inquiry.mail', $maildata, function ($m) use ($maildata) {
            $m->from('rito.db.portal@gmail.com', trans('inquiry.from_email'));
            $m->to($maildata['email'], $maildata['name'])->subject(trans('inquiry.to_email'));
            $m->bcc('alimul.razi@bjitgroup.com', $maildata['name'])->subject(trans('inquiry.to_email'));
        });
        //Email sent for inquiry end

        // user activity log
        createUserActivity($request, '作成する', $user->name.'<'.$user->email.'> 作成する '.$inquiry->name.' 問い合わせ', '一般的な', null);

        return redirect()->route('inquirys.create')
                        ->with('success', trans('inquiry.create'));
    }
}
