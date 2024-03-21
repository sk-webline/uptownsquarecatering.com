<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BlogCategory;
use App\Blog;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $blogs = Blog::orderBy('created_at', 'desc');

        if ($request->search != null){
            $blogs = $blogs->where('title', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }

        $blogs = $blogs->paginate(15);

        return view('backend.blog_system.blog.index', compact('blogs','sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $blog_categories = BlogCategory::all();
        return view('backend.blog_system.blog.create', compact('blog_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|max:255',
        ]);

        $blog = new Blog;

        $blog->category_id = $request->category_id ?? 0;
        $blog->title = $request->title;
        $blog->banner = $request->banner;
        $blog->photos = $request->photos;
        $blog->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $blog->short_description = $request->short_description;
        $blog->description = $request->description;
        if ($request->has('featured')) {
          $blog->featured = 1;
        }

        $blog->meta_title = $request->meta_title;
        $blog->meta_img = $request->meta_img;
        $blog->meta_description = $request->meta_description;
        $blog->meta_keywords = $request->meta_keywords;

        $blog->save();

        flash(translate('Blog post has been created successfully'))->success();
        return redirect()->route('blog.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $blog = Blog::find($id);
        $blog_categories = BlogCategory::all();

        return view('backend.blog_system.blog.edit', compact('blog','blog_categories'));
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
        $request->validate([
            'title' => 'required|max:255',
        ]);

        $blog = Blog::find($id);
        $blog->featured = 0;

        $blog->category_id = $request->category_id ?? 0;
        $blog->title = $request->title;
        $blog->banner = $request->banner;
        $blog->photos = $request->photos;
        $blog->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        $blog->short_description = $request->short_description;
        $blog->description = $request->description;
        if ($request->has('featured')) {
          $blog->featured = 1;
        }

        $blog->meta_title = $request->meta_title;
        $blog->meta_img = $request->meta_img;
        $blog->meta_description = $request->meta_description;
        $blog->meta_keywords = $request->meta_keywords;

        $blog->save();

        flash(translate('Blog post has been updated successfully'))->success();
        return redirect()->route('blog.index');
    }

    public function change_status(Request $request) {
        $blog = Blog::find($request->id);
        $blog->status = $request->status;

        $blog->save();
        return 1;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Blog::find($id)->delete();

        return redirect('admin/blog');
    }


    public function all_blog() {
        $has_next_blogs = false;
        $blogs = Blog::where('status', 1)->orderBy('created_at', 'desc')->limit(6)->get();
        $next_blogs = Blog::where('status', 1)->orderBy('created_at', 'desc')->offset(6)->limit(6)->get();
        if(count($next_blogs) > 0){
          $has_next_blogs = true;
        }
        return view("frontend.blog.listing", compact('blogs', 'has_next_blogs'));
    }

    public function load_blog(Request $request) {
      $has_next_blogs = false;
      $offset = 6 * $request->page;
      $next_offset = 6 * ($request->page + 1);
      $blogs = Blog::where('status', 1)->orderBy('created_at', 'desc')->offset($offset)->limit(6)->get();
      $next_blogs = Blog::where('status', 1)->orderBy('created_at', 'desc')->offset($next_offset)->limit(6)->get();
      if(count($next_blogs) > 0) {
        $has_next_blogs = true;
      }
      return array('status' => 1, 'has_next' => $has_next_blogs, 'view' => view('frontend.blog.load_listing', compact('blogs'))->render());
    }

    public function blog_details($slug) {
        $blog = Blog::where('slug', $slug)->first();
        return view("frontend.blog.details", compact('blog'));
    }
}
