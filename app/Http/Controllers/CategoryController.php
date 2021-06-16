<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:category-list|category-create|category-delete', ['only' => ['index']]);
        $this->middleware('permission:category-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:category-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:category-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function categorySorting($categories)
    {
        //dd($categories);
        if(isset($categories) && $categories != ''){
            for($i=0; $i<count($categories); $i++){
                $category = Category::find($categories[$i]->id);
                $category->order = $i;
                $category->parent_id = null;
                $category->save();
                if(isset($categories[$i]->children)){
                    for($j=0; $j<count($categories[$i]->children); $j++){
                        $category = Category::find($categories[$i]->children[$j]->id);
                        $category->order = $j ;
                        $category->parent_id = $categories[$i]->id;
                        $category->save();
                    }
                }
            }
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::where('parent_id',null)->whereNull('deleted_at')->orderBy('order')->orderBy('id')->with('children')->get();
        return view('admin.categories.index')->with([
            'categories' => $categories,
            'dropdowns' => $this->parentCategory()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
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
        $validation = Validator::make($request->all(),[
            'name' => 'required|max:40'
        ],[
            'name.max' => trans('category.max_char'),
        ]);
        if($validation->fails()){
            return redirect()->back()->withInput()->with([
                'errors' => $validation->errors()
            ]);
        }
        $parent_id = $request->dropdowns;
        if($parent_id==null){
            $order = Category::max('order');
        }else{
            $order = Category::where('parent_id',$parent_id)->max('order');
        }
        $category=Category::create([
            'name'=>$request->name,
            'parent_id'=>$parent_id,
            'order'=> $order === null ? 0 : $order+1,
            'created_by' => $user->id,
            'updated_by' => $user->id
        ]);
        // user activity log
        createUserActivity($request, '作成する', $user->name.'<'.$user->email.'> 作成する '.$category->name.' カテゴリー', '一般的な', null);
        $categories = Category::where('parent_id',null)->orderBy('order')->orderBy('id','DESC')->with('children')->get();
        return redirect()->route('categories.index')->with([
            'success' => trans('category.create')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Category::find($id);
        $parent_category = Category::where('parent_id',null)->select('name','id')->orderBy('order')->orderBy('id','DESC')->get()->toArray();
        return view('admin.categories.edit', compact('data','parent_category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'required|max:40'
        ], [
            'name.max' => trans('category.max_char'),
        ]);

        $input = $request->all();
        $categorie = Category::find($id);
        $categorie['updated_by'] = $user->id;
        $categorie->update($input);

        // user activity log
        createUserActivity($request, '更新', $user->name.'<'.$user->email.'> 更新 '.$categorie->name.' カテゴリー', '一般的な', null);

        return redirect()->route('categories.index')
                        ->with('success',trans('category.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Categorie  $categorie
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        $category = Category::find($request->id);
        if(isset($category->children)){
            foreach($category->children as $children){
                $children->delete();
            }
        }
        $category->delete();
        $this->categorySorting($request->categories);
        // user activity log
        createUserActivity($request, '削除する', $user->name.'<'.$user->email.'> 削除する '.$category->name.' カテゴリー', '一般的な', null);
        return $this->parentCategory();
    }

    public function ajaxDelete(Request $request)
    {
        $category = Category::find($request->id);
        if(isset($category->children)){
            foreach($category->children as $children){
                $children->delete();
            }
        }
        $category->delete();
        $this->categorySorting($request->categories);
        return $this->parentCategory();
    }

    protected function parentCategory()
    {
        $categories = Category::where('parent_id',null)->orderBy('order')->get();
        $option = '<option value="">根底</option>';
        foreach($categories as $category){
            $option.="<option value=$category->id>$category->name</option>";
        }
        return $option;
    }

    public function ajaxUpdate(Request $request)
    {
        $categories = json_decode($request->categories);
        $this->categorySorting($categories);
        return $this->parentCategory();
    }
}
