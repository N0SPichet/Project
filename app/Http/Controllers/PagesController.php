<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Diary;
use App\Category;
use App\Rental;
use App\User;
use App\House;
use App\Himage;
use Carbon\Carbon;
use Session;

class PagesController extends Controller
{
    public function index(){
        $houses = House::orderBy('updated_at', 'desc')->paginate(10);
        $images = Himage::all();
        return view('pages.home')->with('houses', $houses)->with('images', $images);
    }

    public function userprofile(){
        $users = User::where('email', Auth::user()->email)->get();
        return view('users.profile', compact('users'));
    }

    public function mydiaries(){
        $diaries = Diary::where('users_id', Auth::user()->id)->orderBy('updated_at', 'desc')->paginate(10);
        return view('diaries.mydiary')->with('diaries', $diaries);
    }

    public function single($id)    {
        $diary = Diary::find($id);
        $categories = Category::all();
        return view('diaries.single')->with('diary', $diary)->with('categories', $categories);
    }

    public function mytrip(){
        $rentals = Rental::where('users_id', Auth::user()->id)->orderBy('updated_at', 'desc')->paginate(10);
        return view('rentals.mytrip')->with('rentals', $rentals);
    }

    public function aboutus(){
    	return view('pages.about');
    }
}
