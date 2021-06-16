<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Validator;
use Session;
use App\Comment;
use App\User;

class CommentContoller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $sellerId = $request->input('seller');
        return view('admin.comments.create',compact('sellerId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$id)
    {
        $user = Auth::user();
        $this->validate($request, [
            'comment' => 'max:2000',
        ], [
            'comment.max' => trans('user.max_char')
        ]);

        $data = $request->all();
        $data['seller_id'] = $id;
        $data['created_by'] = $user->id;
        $data['comment'] = $data['comment'];
        $createComment = Comment::create($data);
        User::where('id', $id)->update(array('is_comment' => 1,'is_comment_type' => 0));

        if($createComment){
            $message = trans('error.comment_create');
        }else {
            $message = trans('error.failed');
        }
        return redirect()->route('comments.edit',$id)
                        ->with('success',$message);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $data = Comment::where('seller_id',$id)->first();
        return view('admin.comments.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $this->validate($request, [
            'comment' => 'max:2000',
        ], [
            'comment.max' => trans('user.max_char')
        ]);

        $input = $request->all();
        $comment = Comment::find($id);
        $comment['updated_by'] = $user->id;
        User::where('id', $id)->update(array('is_comment_type' => 0));
        $updatedComment = $comment->update($input);
        if($updatedComment){
            $message = trans('error.comment_create');
        }else {
            $message = trans('error.failed');
        }
        return redirect()->route('comments.edit',$comment->seller_id)
        ->with('success',$message);

        // $user = Auth::user();
        // $data = $request->input();
        // $res = array(
        //     'success' => false,
        //     'message' => trans('error.default'),
        //     'rs_class' => 'danger',
        //     'data' => []
        // );

        // $rules = [
        //     'comment' => 'max:2000'
        // ];

        // $messages = [
        //     'comment.max' => trans('user.max_char'),
        // ];

        // $validator = Validator::make($request->all(), $rules, $messages);

        // if ($validator->passes()) {
        //     $comment  = Comment::where('seller_id',$id)->first();
        //     $comment->seller_id = $id;
        //     $comment->comment = $data['comment'];
        //     $comment->updated_by = Auth::user()->id;
        //     $updateComment =  $comment->save();
        //     User::where('id', $id)->update(array('is_comment_type' => 0));
        //     if ($updateComment) {
        //         $res['success'] = true;
        //         $res['rs_class'] = 'success';
        //         $res['message'] = trans('error.comment_create');
        //         $res['redirects'] = url('/comments/'.$id.'/edit');
        //         Session::flash('message', $res['message']);
        //     } else {
        //         $res['message'] =  trans('error.failed');
        //     }
        // } else {
        //     $res['message'] =  trans('error.wrong');
        //     $res['data'] = $validator->errors()->messages();
        // }
        // return Response::json($res);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
