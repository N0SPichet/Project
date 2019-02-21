<?php

namespace App\Http\Controllers;

use App\Category;
use App\Diary;
use App\Models\Himage;
use App\Models\House;
use App\Payment;
use App\Rental;
use App\User;
use Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mail;
use Session;

class PagesController extends Controller
{
    /*publish flag 0 private, 1 public, 2 trash, 3 permanant delete*/
    
    public function index() {
        $houses = House::where('publish', '1')->inRandomOrder()->paginate(10);
        $images = Himage::all();
        return view('pages.home')->with('houses', $houses)->with('images', $images);
    }

    public function indexSearch(Request $request) {
        if ($request->search) {
            $state = Addressstate::where('state_name', 'like', '%'.$request->search.'%')->first();
            if ($state != NULL) {
                $houses = House::where('publish', '1')->where('addressstates_id', $state->id)->paginate(10);
                $images = Himage::all();
            }
            else {
                $houses = NULL;
                $images = NULL;
            }
            return view('pages.home')->with('houses', $houses)->with('images', $images);
        }
        else {
            return redirect()->route('home');
        }
    }

    //Diaries
    public function mydiaries()
    {
        if (Auth::check()) {
            $diaries = Diary::where('users_id', Auth::user()->id)->whereNull('days')->orWhere('days', '0')->orderBy('created_at', 'desc')->paginate(10);
            return view('diaries.mydiary')->with('diaries', $diaries);
            }
        else {
            Session::flash('success', 'You need to login first!');
            return redirect()->route('login');
        }
    }

    public function introroom()
    {
        if (Auth::check()) {
            return view('hosts.introroom');
        }
    }

    public function introapartment()
    {
        if (Auth::check()) {
            return view('hosts.introapartment');
        }
    }

    public function summary()
    {
        if (Auth::check()) {
            $house_id = House::where('users_id', Auth::user()->id)->get();
            $h_id = array();
            foreach ($house_id as $key => $house) {
                $h_id[$key] = $house->id;
            }

            $rentals = Rental::whereIn('houses_id', $h_id)->count();

            $rental_accept = Rental::whereIn('houses_id', $h_id)->where(function ($query) {
                $query->where('host_decision', 'ACCEPT');
            })->count();

            $rental_reject = Rental::whereIn('houses_id', $h_id)->where(function ($query) {
                $query->where('host_decision', 'REJECT');
            })->count();

            $rental_ignore = Rental::whereIn('houses_id', $h_id)->where(function ($query) {
                $query->where('host_decision', NULL)->where('rental_checkroom', '!=', '1');
            })->count();

            $rental_id = Rental::whereIn('houses_id', $h_id)->get();
            $p_id = array();
            foreach ($rental_id as $key => $rental) {
                $p_id[$key] = $rental->payments_id;
            }

            $payment_approved = Payment::whereIn('id', $p_id)->where(function ($query) {
                $query->where('payment_status', 'Approved');
            })->get();
            $total_payment = 0;
            foreach ($payment_approved as $key => $payment) {
                $total_payment += $payment->payment_amount * 0.9;
            }

            $payment_approved = Payment::whereIn('id', $p_id)->where(function ($query) {
                $lastmonth =  new Carbon('last month');
                $query->where('payment_status', 'Approved')->where('created_at', '>=', $lastmonth);
            })->get();
            $payment_lastmonth = 0;
            foreach ($payment_approved as $key => $payment) {
                $payment_lastmonth += $payment->payment_amount * 0.9;
            }

            $rentals_id = Rental::whereIn('houses_id', $h_id)->where(function ($query) {
                $query->where('host_decision', 'ACCEPT');
            })->get();
            $p_id = array();
            foreach ($rentals_id as $key => $rental) {
                $p_id[$key] = $rental->payments_id;
            }

            $approved = Payment::whereIn('id', $p_id)->where(function ($query) {
                $query->where('payment_status', 'Approved');
            })->count();

            $waiting = Payment::whereIn('id', $p_id)->where(function ($query) {
                $query->where('payment_status', 'Waiting');
            })->count();

            $reject = Payment::whereIn('id', $p_id)->where(function ($query) {
                $query->where('payment_status', 'Reject');
            })->count();

            $cancel = Payment::whereIn('id', $p_id)->where(function ($query) {
                $query->where('payment_status', 'Cancel');
            })->count();

            $ofd = Payment::whereIn('id', $p_id)->where(function ($query) {
                $query->where('payment_status', 'Out of Date');
            })->count();

            $data = array(
                'rental_accept' => $rental_accept,
                'rental_reject' => $rental_reject,
                'rental_ignore' => $rental_ignore,
                'total_payment' => $total_payment,
                'payment_lastmonth' => $payment_lastmonth,
                'approved' => $approved,
                'waiting' => $waiting,
                'reject' => $reject,
                'cancel' => $cancel,
                'ofd' => $ofd,
                'rental_id' => $rental_id
            );

            return view('pages.summary')->with('rentals', $rentals)->with($data);
        }
        else {
            Session::flash('success', 'You need to login first!');
            return redirect()->route('login');
        }
    }

    public function aboutus()
    {
    	return view('pages.about');
    }
}
