<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Faq;
use App\Language;
use App\FaqTranslation;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $faqs = Faq::orderBy('created_at', 'desc');
        
        if ($request->search != null){
            $faqs = $faqs->where('title', 'like', '%'.$request->search.'%');
            $sort_search = $request->search;
        }

        $faqs = $faqs->paginate(15);

        return view('backend.faqs.faq.index', compact('faqs','sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.faqs.faq.create');
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
            'description' => 'required',
        ]);

        $faq = new Faq;
        
        $faq->title = $request->title;
        $faq->description = $request->description;

        $faq->save();

        $faq_translation = FaqTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'faq_id' => $faq->id]);
        $faq_translation->title = $request->title;
        $faq_translation->description = $request->description;
        $faq_translation->save();

        flash(translate('Faq has been created successfully'))->success();
        return redirect()->route('faq.index');
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
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $faq = Faq::find($id);
        
        return view('backend.faqs.faq.edit', compact('faq', 'lang'));
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
            'description' => 'required',
        ]);

        $faq = Faq::find($id);

        if($request->lang == env("DEFAULT_LANGUAGE")) {
          $faq->title = $request->title;
          $faq->description = $request->description;
        }

        
        $faq->save();

        $faq_translation = FaqTranslation::firstOrNew(['lang' => $request->lang, 'faq_id' => $faq->id]);
        $faq_translation->title = $request->title;
        $faq_translation->description = $request->description;
        $faq_translation->save();

        flash(translate('Faq has been updated successfully'))->success();
        return redirect()->route('faq.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);

        // Category Translations Delete
        foreach ($faq->faq_translations as $key => $faq_translation) {
          $faq_translation->delete();
        }

        Faq::find($id)->delete();

      return redirect()->route('faq.index');
    }
}
