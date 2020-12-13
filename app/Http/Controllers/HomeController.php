<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    
    public function upload(Request $request)
    {
        // return $request->file('file')->getClientOriginalExtension();


        // $img = Image::make($request->file('file'))->fit(200, 200)->save($request->file('file')->getClientOriginalName());
        // $img = Image::canvas(800, 600, '#ccc');

        return $img->response();
        return redirect()->route('home');
    }
}
