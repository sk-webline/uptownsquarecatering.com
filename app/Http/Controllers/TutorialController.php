<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class TutorialController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function tutorial_page()
    {
        return view('frontend.tutorial');
    }
}
