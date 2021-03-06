<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\GlobalFunctionTraits;
use App\Models\CheckinList;
use App\Models\Diary;
use App\Models\House;
use App\Models\HouseImage;
use App\Models\Houserule;
use App\Models\Housetype;
use App\Models\Map;
use App\Models\Payment;
use App\Models\Rental;
use App\Models\Review;
use App\Models\Subscribe;
use App\Models\UserVerification;
use App\User;
use Carbon\Carbon;
use DateTime;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Image;
use Mail;
use Session;

class RentalController extends Controller
{
    use GlobalFunctionTraits;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Session::flash('fail', 'Unauthorized access.');
        return back();
    }

    public function mytrip($userId)
    {   
        $user = User::findOrFail($userId);
        if (Auth::user()->id == $user->id) {
            $rentals = Rental::where('user_id', $user->id)->orderBy('rental_datein', 'desc')->paginate(5);
            $reviews = Review::where('user_id', $user->id)->get();
            $rentals_id = array();
            foreach ($reviews as $key => $review) {
                array_push($rentals_id, $review->rental_id);
            }
            $rentals_not_review = Rental::whereNotIn('id', $rentals_id)->where('user_id', $user->id)->where('checkin_status', '1')->get();
            $data = array(
                'review_count' => $rentals_not_review->count()
            );
            return view('rentals.mytrip')->with('rentals', $rentals)->with($data);
        }
        Session::flash('fail', 'Unauthorized access.');
        return back();
    }

    public function not_reviews($userId)
    {
        $user = User::findOrFail($userId);
        if (Auth::user()->id == $user->id) {
            $myReviews = Review::where('user_id', Auth::user()->id)->get();
            $rentals_id = array();
            foreach ($myReviews as $key => $review) {
                array_push($rentals_id, $review->rental_id);
            }
            $rentals = Rental::whereNotIn('id', $rentals_id)->where('user_id', Auth::user()->id)->where('checkin_status', '1')->paginate(5);
            $data = array(
                'review_count' => $rentals->count()
            );
            return view('rentals.mytrip')->with('rentals', $rentals)->with($data);
        }
        Session::flash('fail', 'Unauthorized access.');
        return back();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rentals.create');
    }

    public function rentals_agreement(Request $request)
    {
        $this->validate($request, array(
            'datein' => 'required',
            'dateout' => 'required'
        ));
        $have_customer = '0';
        $datein = $request->datein;
        $dateout = $request->dateout;
        $house = House::find($request->house_id);
        if ($dateout > $datein) {
            if ($house->checkType($house->id)) {
                $food = $request->food;
                $guest = $request->guest;
                $room = $request->room;
                $now = Carbon::now();
                $now->subDay();
                $rentals = Rental::where('house_id', $house->id)->where('rental_datein', '>', $now)->where( function ($query) {
                    $query->where('host_decision', 'accept')->where('checkin_status', '0');
                })->orderBy('id', 'desc')->get();
                foreach ($rentals as $rental) {
                    if ($rental->payment->payment_status == 'Waiting' || $rental->payment->payment_status == 'Approved' || $rental->payment->payment_status == null) {
                        if ($datein > $rental->rental_datein) {
                            // echo "in after<br>";
                            if ($datein >= $rental->rental_dateout) {
                                // echo "in after out<br>";
                                if ($rental->payment->payment_status == 'Waiting' || $rental->payment->payment_status == 'Approved' || $rental->payment->payment_status == null) {
                                    $have_customer = '0';
                                }
                                else {
                                    $have_customer = '1';
                                }
                            }
                            else {
                                // echo "in before out";
                                $have_customer = '1';
                            }
                        }
                        elseif ($datein == $rental->rental_datein) {
                            // echo "in same<br>";
                            if ($rental->payment->payment_status == 'Waiting' || $rental->payment->payment_status == 'Approved' || $rental->payment->payment_status == null) {
                                if ($rental->no_rooms != $request->room) {
                                    $have_customer = '0';
                                }
                                else {
                                    $have_customer = '1';
                                }
                            }
                            else {
                                $have_customer = '1';
                            }
                        }
                        elseif ($datein < $rental->rental_datein) {
                            // echo "in before<br>";
                            if ($dateout <= $rental->rental_datein) {
                                // echo "out before in";
                                if ($rental->payment->payment_status == 'Waiting' || $rental->payment->payment_status == 'Approved' || $rental->payment->payment_status == null) {
                                    $have_customer = '0';
                                }
                                else {
                                    $have_customer = '1';
                                }
                            }
                            elseif ($dateout > $rental->rental_datein) {
                                // echo "out after out";
                                $have_customer = '1';
                            }
                        }
                    }
                }
                if ($have_customer == '0') {
                    $data = array(
                        'house_id' => $house->id,
                        'types' => 'room',
                        'datein' => $datein,
                        'dateout' => $dateout,
                        'guest' => $guest,
                        'food' => $food,
                        'no_rooms' => $room
                    );
                    return view('rentals.agreement')->with($data)->with('house', $house);
                }
                Session::flash('fail', 'We have a customer in this day. Please choose other day!');
                return back();
            }
            else {
                $no_type_single = '0';
                $no_type_deluxe_single = '0';
                $no_type_double_room = '0';
                if ($request->type_single) {
                    $no_type_single = $request->type_single;
                }
                if ($request->type_deluxe_single) {
                    $no_type_deluxe_single = $request->type_deluxe_single;
                }
                if ($request->type_double_room) {
                    $no_type_double_room = $request->type_double_room;
                }
                $data = array(
                    'house_id' => $house->id,
                    'types' => 'apartment',
                    'datein' => $datein,
                    'dateout' => $dateout,
                    'no_type_single' => $no_type_single,
                    'type_single_price' => $house->apartmentprices->single_price,
                    'no_type_deluxe_single' => $no_type_deluxe_single,
                    'type_deluxe_single_price' => $house->apartmentprices->deluxe_single_price,
                    'no_type_double_room' => $no_type_double_room,
                    'type_double_room_price' => $house->apartmentprices->double_price
                );
                return view('rentals.agreement')->with($data)->with('house', $house);
            }
        }
        else {
            Session::flash('fail', 'Invalid date format, date in should come before date out!');
            return back();
        }
    }

    public function booking_preview(Request $request)
    {
        $house = House::find($request->house_id);
        if (!is_null($house)) {
            $data = array();
            if ($request->types == 'room') {
                $data = array(
                    'house_id' => $house->id,
                    'types' => $request->types,
                    'datein' => $request->datein,
                    'dateout' => $request->dateout,
                    'guest' => $request->guest,
                    'food' => $request->food,
                    'no_rooms' => $request->no_rooms
                );
            }
            else {
                $no_type_single = '0';
                $no_type_deluxe_single = '0';
                $no_type_double_room = '0';
                if ($request->no_type_single) {
                    $no_type_single = $request->no_type_single;
                }
                if ($request->no_type_deluxe_single) {
                    $no_type_deluxe_single = $request->no_type_deluxe_single;
                }
                if ($request->no_type_double_room) {
                    $no_type_double_room = $request->no_type_double_room;
                }
                $data = array(
                    'house_id' => $house->id,
                    'types' => $request->types,
                    'datein' => $request->datein,
                    'dateout' => $request->dateout,
                    'no_type_single' => $no_type_single,
                    'type_single_price' => $house->apartmentprices->single_price,
                    'no_type_deluxe_single' => $no_type_deluxe_single,
                    'type_deluxe_single_price' => $house->apartmentprices->deluxe_single_price,
                    'no_type_double_room' => $no_type_double_room,
                    'type_double_room_price' => $house->apartmentprices->double_price
                );
            }
            return view('rentals.booking-preview')->with('house', $house)->with($data);
        }
        Session::flash('fail', 'This room is no longer available.');
        return redirect()->route('home');
    }

    public function accept_rentalrequest(Rental $rental)
    {
        if ($rental->payment->payment_status == null && $rental->payment->payment_status != 'Cancel' && $rental->payment->payment_status != 'Out of Date') {
            $rental->host_decision = 'accept';
            $rental->save();
            $premessage = "Dear " . $rental->user->user_fname;
            $detailmessage = "Your host was accepted your booking " . $rental->house->house_title . " Stay date " . date('jS F, Y', strtotime($rental->rental_datein)) . " to " . date('jS F, Y', strtotime($rental->rental_dateout));
            $endmessage = "Next please have a payment to complete booking!";

            $data = array(
                'email' => $rental->user->email,
                'subject' => "LTT - Booking request confirm",
                'bodyMessage' => $premessage,
                'detailmessage' => $detailmessage,
                'endmessage' => $endmessage,
                'rental' => $rental
            );

            Mail::send('emails.booking_accepted', $data, function($message) use ($data){
                $message->from('noreply@ltt.com');
                $message->to($data['email']);
                $message->subject($data['subject']);
            });

            Session::flash('success', 'Thank you for accept this request.');
            return redirect()->route('rentals.show', $rental->id);
        }
        else {
            $payment = Payment::find($rental->payment_id);
            Session::flash('fail', "Cannot accept - This trip is already $payment->payment_status.");
            return back();
        }

    }

    public function reject_rentalrequest(Rental $rental) 
    {
        if ($rental->payment->payment_status == null && $rental->payment->payment_status != 'Cancel' && $rental->payment->payment_status != 'Out of Date') {
            $rental->host_decision = 'reject';
            $rental->rental_checkroom = '1';
            $rental->save();
            Session::flash('success', 'This request was rejected.');
            return redirect()->route('rentals.show', $rental->id);
        }
        else {
            $payment = Payment::find($rental->payment_id);
            Session::flash('fail', "Cannot reject - This trip is already $payment->payment_status.");
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, array(
            'house_id' => 'required',
            'datein' => 'required',
            'dateout' => 'required',
        ));
        $house = House::find($request->house_id);
        $payment = new Payment;
        $payment->save();
        $rental = new Rental;
        if ($request->types == 'apartment') {            
            $rental->no_type_single = $request->no_type_single;
            $rental->type_single_price = $request->type_single_price;
            
            $rental->no_type_deluxe_single = $request->no_type_deluxe_single;
            $rental->type_deluxe_single_price = $request->type_deluxe_single_price;

            $rental->no_type_double_room = $request->no_type_double_room;
            $rental->type_double_room_price = $request->type_double_room_price;
            
            $rental->discount = $house->apartmentprices->discount;
        }
        else {
            $rental->rental_guest = $request->guest;
            $rental->no_rooms = $request->no_rooms;
            $rental->room_price = $house->houseprices->price;
            $rental->select_food = $request->food;
        }
        $rental->rental_datein = $request->datein;
        $rental->rental_dateout = $request->dateout;
        $rental->user_id = Auth::user()->id;
        $rental->house_id = $request->house_id;
        $rental->payment_id = $payment->id;
        $rental->save();

        $message = null;
        $endmessage = "Please check Rentals page to accept this request";

        $data = array(
            'email' => $rental->house->user->email,
            'subject' => "LTT - You have new customer for rental #".$rental->id,
            'bodyMessage' => $message,
            'endmessage' => $endmessage,
            'rental' => $rental
        );

        Mail::send('emails.booking_request', $data, function($message) use ($data){
            $message->from('noreply@ltt.com');
            $message->to($data['email']);
            $message->subject($data['subject']);
        });

        $message =  null;
        $endmessage = "Now, wait for host accept your booking and have a payment!";

        $data = array(
            'email' => $rental->user->email,
            'subject' => "LTT - Booking Confirmation for rental #".$rental->id,
            'bodyMessage' => $message,
            'endmessage' => $endmessage,
            'rental' => $rental
        );

        Mail::send('emails.booking_confirm', $data, function($message) use ($data){
            $message->from('noreply@ltt.com');
            $message->to($data['email']);
            $message->subject($data['subject']);
        });

        Session::flash('success', 'You was succussfully booking, Now wait for host accept your booking and have a payment!');
        return redirect()->route('rentals.show', $rental->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($rentalId)
    {
        $rental = Rental::find($rentalId);
        if (!is_null($rental)) {
            if (Auth::user()->id == $rental->user_id || Auth::user()->id == $rental->house->user_id || Auth::user()->hasRole('Admin')) {
                $types_id = $this->getTypeId('apartment');
                $house = House::where('id', $rental->house_id)->whereIn('housetype_id', $types_id)->first();
                $total_price = 0;
                $fee = 0;
                $discount = 0;
                if (!is_null($house)) {
                    $days = Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout));
                    $type_single_price = $rental->type_single_price*$rental->no_type_single*$days;
                    $type_deluxe_single_price = $rental->type_deluxe_single_price*$rental->no_type_deluxe_single*$days;
                    $type_double_room_price = $rental->type_double_room_price*$rental->no_type_double_room*$days;
                    $total_price = floor($type_single_price + $type_deluxe_single_price + $type_double_room_price);
                    $discount = floor($total_price*(0.01 * $rental->discount));
                    $fee = floor($total_price*0.1);
                    $total_price = $total_price + $fee - $discount;
                    $review = Review::where('user_id', $rental->user_id)->where('rental_id', $rental->id)->first();
                    $map = Map::where('houses_id', $rental->house_id)->first();
                    $data = array(
                        'type_single_price' => $type_single_price,
                        'type_deluxe_single_price' => $type_deluxe_single_price,
                        'type_double_room_price' => $type_double_room_price,
                        'total_price' => $total_price,
                        'discount' => $discount,
                        'fee' => $fee,
                        'types' => 'apartment'
                    );
                    return view('rentals.show')->with('rental', $rental)->with($data)->with('review', $review)->with('map', $map);
                }
                else {
                    $types_id = $this->getTypeId('room');
                    $house = House::where('id', $rental->house_id)->whereIn('housetype_id', $types_id)->first();
                    if (!is_null($house)) {
                        $days = Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout));
                        $room_price = 0;
                        $food_price = 0;
                        $guest = 1;
                        $guest_food = 1;
                        if ($house->houseprices->type_price == '1') {
                            $guest = $rental->rental_guest;
                            $guest_food = $rental->rental_guest;
                        }
                        if ($days < 7) {
                            $room_price = $rental->room_price*$guest*$days;
                            if ($rental->select_food == '1') {
                                $food_price = $rental->house->houseprices->food_price*$guest_food*$days;
                            }
                            $total_price = floor(($room_price + $food_price));
                            if (!$rental->house->checkType($rental->house_id)) {
                                $total_price *= $rental->no_rooms;
                            }
                            $fee = floor($total_price*0.1);
                            $total_price = $total_price + $fee - $discount;
                        }
                        elseif ($days/7 >= 1 && $days < 30) {
                            $room_price = $rental->room_price*$guest*$days;
                            if ($rental->select_food == '1') {
                                $food_price = $rental->house->houseprices->food_price*$guest_food*$days;
                            }
                            $total_price = floor($room_price + $food_price);
                            if (!$rental->house->checkType($rental->house_id)) {
                                $total_price *= $rental->no_rooms;
                            }
                            $discount = floor($total_price*(0.01 * $rental->house->houseprices->weekly_discount));
                            $fee = floor($total_price*0.1);
                            $total_price = $total_price + $fee - $discount;
                        }
                        else {
                            $room_price = $rental->room_price*$guest*$days;
                            if ($rental->select_food == '1') {
                                $food_price = $rental->house->houseprices->food_price*$guest_food*$days;
                            }
                            $total_price = floor($room_price + $food_price);
                            if (!$rental->house->checkType($rental->house_id)) {
                                $total_price *= $rental->no_rooms;
                            }
                            $discount = floor($total_price*(0.01 * $rental->house->houseprices->monthly_discount));
                            $fee = floor($total_price*0.1);
                            $total_price = $total_price + $fee - $discount;
                        }
                        $review = Review::where('user_id', $rental->user_id)->where('rental_id', $rental->id)->first();
                        $map = Map::where('houses_id', $rental->house_id)->first();
                        $data = array(
                            'food_price' => $food_price,
                            'room_price' => $room_price,
                            'total_price' => $total_price,
                            'discount' => $discount,
                            'fee' => $fee,
                            'types' => 'room'
                        );
                        return view('rentals.show')->with('rental', $rental)->with($data)->with('review', $review)->with('map', $map);
                    }
                }
            }
            Session::flash('fail', 'Unauthorized access.');
            return back();
        }
        else {
            Session::flash('fail', 'This rental is no longer available.');
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Rental $rental)
    {
        if (!is_null($rental)) {
            if (Auth::user()->id == $rental->user_id) {
                if (Carbon::parse($rental->rental_datein)->lte(Carbon::today())) {
                    $payment = $rental->payment;
                    if ($payment->payment_status != 'Approved') {
                        $payment->payment_status = 'Out of Date';
                        $payment->save();
                    }
                    Session::flash('success', 'This payment already '. $rental->payment->payment_status. '.');
                    return redirect()->route('rentals.show', $rental->id);;
                }
                $types_id = $this->getTypeId('apartment');
                $house = House::where('id', $rental->house_id)->whereIn('housetype_id', $types_id)->first();
                $payment = Payment::find($rental->payment_id);
                $days = Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout));
                $total_price = 0;
                $fee = 0;
                $discount = 0;
                if (!is_null($house)) {
                    if ($payment->payment_status != 'Approved' && $payment->payment_status != 'Cancel' && $payment->payment_status != 'Out of Date' && $rental->host_decision == 'accept') {
                        $type_single_price = $rental->type_single_price*$rental->no_type_single*$days;
                        $type_deluxe_single_price = $rental->type_deluxe_single_price*$rental->no_type_deluxe_single*$days;
                        $type_double_room_price = $rental->type_double_room_price*$rental->no_type_double_room*$days;
                        $total_price = floor($type_single_price + $type_deluxe_single_price + $type_double_room_price);
                        $discount = floor($total_price*(0.01 * $rental->discount));
                        $fee = floor($total_price*0.1);
                        $total_price = $total_price + $fee - $discount;
                        $data = array(
                            'days' => $days,
                            'type_single_price' => $type_single_price,
                            'type_deluxe_single_price' => $type_deluxe_single_price,
                            'type_double_room_price' => $type_double_room_price,
                            'total_price' => $total_price,
                            'discount' => $discount,
                            'fee' => $fee
                        );
                        return view('rentals.payment-apartment')->with($data)->with('rental', $rental)->with('payment', $payment);
                    }
                    elseif ($rental->host_decision == 'reject') {
                        Session::flash('fail', 'This payment already rejected by host.');
                        return back();
                    }
                    else {
                        Session::flash('success', 'This payment already '. $payment->payment_status. '.');
                        return back();
                    }
                    
                }
                else {
                    $types_id = $this->getTypeId('room');
                    $house = House::where('id', $rental->house_id)->whereIn('housetype_id', $types_id)->first();
                    if (!is_null($house)) {
                        if ($payment->payment_status != 'Approved' && $payment->payment_status != 'Cancel' && $payment->payment_status != 'Out of Date' && $rental->host_decision == 'accept') {
                            $food_price = 0;
                            $guest = 1;
                            $guest_food = 1;
                            if ($house->houseprices->type_price == '1') {
                                $guest = $rental->rental_guest;
                                $guest_food = $rental->rental_guest;
                            }
                            if ($days < 7) {
                                $room_price = $rental->room_price*$guest*$days;
                                if ($rental->select_food == '1') {
                                    $food_price = $rental->house->houseprices->food_price*$guest_food*$days;
                                }
                                $total_price = floor(($room_price + $food_price));
                                if (!$rental->house->checkType($rental->house_id)) {
                                    $total_price *= $rental->no_rooms;
                                }
                                $discount = 0;
                                $fee = floor($total_price*0.1);
                                $total_price = $total_price + $fee - $discount;
                            }
                            elseif ($days/7 >= 1 && $days < 30) {
                                $room_price = $rental->room_price*$guest*$days;
                                if ($rental->select_food == '1') {
                                    $food_price = $rental->house->houseprices->food_price*$guest_food*$days;
                                }
                                $total_price = floor($room_price + $food_price);
                                if (!$rental->house->checkType($rental->house_id)) {
                                    $total_price *= $rental->no_rooms;
                                }
                                $discount = $rental->house->houseprices->weekly_discount;
                                $fee = floor($total_price*0.1);
                                $total_price = $total_price + $fee - $discount;
                            }
                            else {
                                $room_price = $rental->room_price*$guest*$days;
                                if ($rental->select_food == '1') {
                                    $food_price = $rental->house->houseprices->food_price*$guest_food*$days;
                                }
                                $total_price = floor($room_price + $food_price);
                                if (!$rental->house->checkType($rental->house_id)) {
                                    $total_price *= $rental->no_rooms;
                                }
                                $discount = $rental->house->houseprices->monthly_discount;
                                $fee = floor($total_price*0.1);
                                $total_price = $total_price + $fee - $discount;
                            }
                            $data = array(
                                'id' => $rental->id,
                                'total_price' => $total_price,
                                'discount' => $discount,
                                'fee' => $fee,
                                'datein' => $rental->rental_datein,
                                'dateout' => $rental->rental_dateout,
                                'days' => $days,
                                'guest' => $rental->rental_guest
                            );    
                            return view('rentals.payment-room')->with($data)->with('rental', $rental)->with('payment', $payment);
                        }
                        elseif ($rental->host_decision == 'reject') {
                            Session::flash('fail', 'This payment already rejected by host.');
                            return back();
                        }
                    }
                }
            }
            Session::flash('fail', 'Unauthorized access.');
            return back();
        }
        else {
            Session::flash('fail', 'This trip is no longer available.');
            return back();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $paymentId)
    {
        $payment = Payment::find($paymentId);
        if (Auth::user()->id == $payment->rental->user_id) {
            $payment->payment_bankname = $request->banks_id;
            $payment->payment_bankaccount = $request->payment_bank_account;
            $payment->payment_holder = $request->payment_holder;
            $payment->payment_amount = $request->payment_amount;
            $payment->payment_status = $request->payment_status;
            if ($request->hasFile('payment_transfer_slip')) {
                if ($payment->payment_transfer_slip != null) {
                    $location = public_path('images/payments/'.$payment->id.'/'.$payment->payment_transfer_slip);
                    File::delete($location);
                }
                $image = $request->file('payment_transfer_slip');
                $filename = "trans_".time().Auth::user()->id.'.'.$image->getClientOriginalExtension();
                $location = public_path('images/payments/'.$payment->id.'/');
                if (!file_exists($location)) {
                    $result = File::makeDirectory($location, 0775, true);
                }
                $location = public_path('images/payments/'.$payment->id.'/'.$filename);
                Image::make($image)->save($location);
                $payment->payment_transfer_slip = $filename;
            }
            $payment->save();

            $rental = Rental::where('payment_id', $payment->id)->first();

            $premessage = "Dear " . $rental->user->user_fname . " " . $rental->user->user_lname . " , With reference to your request for bill payment via LTT Service as follows.";
            $detailmessage = $rental->user->user_fname . " " . $rental->user->user_lname . " has pay " . $rental->payment->payment_amount . " thai baht for booking room " . $rental->house->house_title . " Stay date " . date('jS F, Y', strtotime($rental->rental_datein)) . " to " . date('jS F, Y', strtotime($rental->rental_dateout)) . ".";
            $endmessage = "Now, wait for checking payment then you will completely booking and have a code for check-in.";

            $data = array(
                'email' => $rental->user->email,
                'subject' => "LTT - Result of Bill Payment (Waiting)",
                'bodyMessage' => $premessage,
                'detailmessage' =>  $detailmessage,
                'endmessage' => $endmessage,
                'rental' => $rental
            );

            Mail::send('emails.payment_confirm', $data, function($message) use ($data){
                $message->from('noreply@ltt.com');
                $message->to($data['email']);
                $message->subject($data['subject']);
            });

            return redirect()->route('rentals.show', $rental->id);
        }
        Session::flash('fail', 'Unauthorized access.');
        return back();
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

    public function rental_cancel($rentalId)
    {
        $rental = Rental::find($rentalId);
        $payment = Payment::find($rental->payment_id);

        if ($rental->checkin_status == '1') {
            Session::flash('fail', 'Cannot Cancel - Rental #ID' . $rental->id . ' is already check in.');
        }
        else if ($payment->payment_status == 'Reject') {
            Session::flash('fail', 'Cannot Cancel - Rental #ID' . $rental->id . ' is already rejected by admin.');
        }
        else if ($payment->payment_status == 'Cancel') {
            Session::flash('fail', 'Cannot Cancel - Rental #ID' . $rental->id . ' is already canceled by yourself.');
        }
        else if ($rental->host_decision == 'reject') {
            Session::flash('fail', 'Cannot Cancel - Rental #ID' . $rental->id . ' is already rejected by host.');
        }
        else {
            $payment->payment_status = "Cancel";
            $payment->save();
            $rental->checkincode = null;
            $rental->rental_checkroom = '1';
            $rental->save();
            if (!$rental->house->checkType($rental->house_id)) {
                $house = House::find($rental->house_id);
                $house->no_rooms = $house->no_rooms + $rental->no_rooms;
                $house->save();
            }
            Session::flash('success', 'This trip has been canceled.');
        }
        return redirect()->route('rentals.mytrips', $rental->user_id);
    }

    public function rentmyrooms($userId)
    {
        if (Auth::user()->id == $userId) {
            $now = Carbon::yesterday();
            $houses = House::where('user_id', Auth::user()->id)->get();
            $houses_id = array();
            foreach ($houses as $key => $house) {
                array_push($houses_id, $house->id);
            }
            
            /*payment with my house rentals*/
            $payments_confirmed_id = array();
            $rentals = Rental::where('rental_datein', '>=', $now)->whereIn('house_id', $houses_id)->get();
            foreach ($rentals as $key => $rental) {
                array_push($payments_confirmed_id, $rental->payment_id);
            }
            
            /*rental with payment approve status*/
            $payments = Payment::whereIn('id', $payments_confirmed_id)->where('payment_status', 'Approved')->get();
            $payments_approved_id = array();
            foreach ($payments as $key => $payment) {
                array_push($payments_approved_id, $payment->id);
            }
            $rentals_approved = Rental::whereIn('payment_id', $payments_approved_id)->orderBy('rental_datein')->get();
            $payment_approved_badge = Payment::whereIn('id', $payments_confirmed_id)->where('payment_status', 'Approved')->count();
            
            /*rental with waiting status*/
            $payments = Payment::whereIn('id', $payments_confirmed_id)->where('payment_status', 'Waiting')->get();
            $payments_waiting_id = array();
            foreach ($payments as $key => $payment) {
                array_push($payments_waiting_id, $payment->id);
            }
            $rentals_waiting = Rental::whereIn('payment_id', $payments_waiting_id)->orderBy('rental_datein')->get();
            $payment_waiting_badge = Rental::whereIn('payment_id', $payments_waiting_id)->count();

            /*payment status null*/
            $payments = Payment::whereIn('id', $payments_confirmed_id)->where('payment_status', null)->get();
            $payments_null_id = array();
            foreach ($payments as $key => $payment) {
                array_push($payments_null_id, $payment->id);
            }
            if (!is_null($houses)) {
                $rentals = Rental::whereIn('house_id', $houses_id)->orderBy('rental_datein')->get();
                $rental_new = Rental::whereIn('house_id', $houses_id)->where('host_decision', 'waiting')->whereIn('payment_id', $payments_null_id)->count();
                $rent_count = array();
                foreach ($houses as $key => $house) {
                    $rent_count_get = Rental::where('house_id', $house->id)->where('host_decision', 'waiting')->whereIn('payment_id', $payments_null_id)->count();
                    array_push($rent_count, $rent_count_get);
                }
                $data = array(
                    'rental_new' => $rental_new,
                    'payment_waiting_badge' => $payment_waiting_badge,
                    'payment_approved_badge' => $payment_approved_badge,
                    'rent_count' => $rent_count
                );
                return view('rentals.rentmyrooms')->with($data)->with('rentals', $rentals)->with('houses', $houses)->with('rentals_approved', $rentals_approved)->with('rentals_waiting', $rentals_waiting);
            }

            else{
                $rentals = Rental::where('id', '0')->get();
                $data = array(
                    'rental_new' => 0,
                    'payment_waiting_badge' => 0,
                    'payment_approved_badge' => 0
                );
                return view('rentals.rentmyrooms')->with($data)->with('rentals', $rentals)->with('houses', $houses)->with('arriverentals', $arriverentals)->with('waiting_payment', $waiting_payment);
            }
        }
        Session::flash('fail', 'Unauthorized access.');
        return redirect()->route('rentals.rentmyrooms', Auth::user()->id);
    }

    public function renthistories()
    {
        $now = Carbon::now();
        $houses = House::where('user_id', Auth::user()->id)->get();
        $rentals_approved = null;
        $rentals = null;
        if ($houses->count()) {
            $houses_id = array();
            foreach ($houses as $key => $house) {
                array_push($houses_id, $house->id);
            }
            foreach ($houses as $house) {
                $rentals_approved = Rental::where('rental_datein', '<', $now)->whereIn('house_id', $houses_id)->orderBy('id', 'desc')->where('checkin_status', '1')->get();
                $rentals = Rental::where('rental_datein', '<', $now)->whereIn('house_id', $houses_id)->orderBy('id', 'desc')->get();
            }
            return view('rentals.rhistories')->with('rentals', $rentals)->with('rentals_approved', $rentals_approved)->with('houses', $houses);
        }
        Session::flash('fail', 'Unauthorized access.');
        return redirect()->route('rentals.rentmyrooms', Auth::user()->id);
    }

    public function checkin_check(Request $request) {
        $this->validate($request, array(
            'checkincode' => 'required',
        ));
        $req_checkincode = str_replace(' ', '', $request->checkincode);
        $checkincode = substr($req_checkincode, 0, 10);
        $rent_id = substr($req_checkincode, 10);
        $rental = null;
        if (!is_null($rent_id)) {
            $rental = Rental::find($rent_id);
        }
        if (strlen($checkincode) == 9) {
            $reen_checkincode = substr($checkincode, 0, 3).substr($checkincode, 6, 3).substr($checkincode, 3, 3);
            $verification = UserVerification::where('passport', 'like', '%'.$reen_checkincode.'%')->first();
            if (!is_null($verification)) {
                $user = User::where('user_verifications_id', $verification->id)->first();
                $code = array();
                $code[0] = substr($user->verification->passport, 9, 3);
                $code[2] = substr($user->verification->passport, 12, 3);
                $code[1] = substr($user->verification->passport, 15, 3);
                $reen_code = $code[0].$code[1].$code[2];
                if ($checkincode == $reen_code) {
                    $today = Carbon::today();
                    $rentals = Rental::where('rental_datein', '>=', $today)->where('checkin_status', '0')->where('user_id', $user->id)->join('payments', 'rentals.payment_id', 'payments.id')->where('payment_status', 'Approved')->get();
                    if ($rentals->count()) {
                        $checkinBy = 'renter';
                        return view('rentals.checkin-preview')->with('checkinBy', $checkinBy)->with('rentals', $rentals)->with('checkincode', $checkincode);
                    }
                }
            }
        }
        if (!is_null($rental)) {
            if (Auth::user()->id == $rental->house->user_id && $rental->checkin_status == '0'){
                if ($checkincode == $rental->checkincode) {
                    $checkinBy = 'host';
                    return view('rentals.checkin-preview')->with('checkinBy', $checkinBy)->with('rental', $rental)->with('checkincode', $request->checkincode);
                }
                else {
                    Session::flash('fail', "code is invalid.");
                    return redirect()->route('rentals.rentmyrooms', Auth::user()->id)->withInput();
                }
            }
            Session::flash('fail', 'code not found.');
        }
        Session::flash('fail', 'for self checkin please use your passport.');
        return back()->withInput();
    }

    public function checkin_confirmed(Request $request) {
        $req_checkincode = str_replace(' ', '', $request->checkincode);
        $checkincode = substr($req_checkincode, 0, 10);
        $rental = Rental::find($request->select_rental);
        if (strlen($req_checkincode) >= 11) {
            $rental_id = substr($req_checkincode, 10);
            $rental = Rental::where('id', $rental_id)->first();
        }
        if (strlen($checkincode) == 9) {
            if ($request->current_code == $request->checkincode && Auth::user()->verification->secret == $request->renter_secret) {
                if ($rental->payment->payment_status == 'Approved') {
                    $rental->checkin_status = '1';
                    $rental->save();
                    $days = Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout));
                    for ($i = 0; $i <= $days+1; $i++) {
                        $diary = new Diary;
                        $diary->publish = '0';
                        if ($i == 0) {
                            $diary->title = 'Diary Title';
                            $diary->message = "Short story of this trip.";
                        }
                        if ($i != 0) {
                            $diary->message = 'Story about day '. $i;
                        }
                        $diary->days = $i;
                        $diary->user_id = $rental->user_id;
                        $diary->category_id = '1';
                        $diary->rental_id = $rental->id;
                        $diary->save();
                    }
                    $subscribe = Subscribe::where('writer', $diary->user_id)->where('follower', $rental->house->user_id)->first();
                    if (is_null($subscribe)) {
                        $subscribe = new Subscribe;
                    }
                    $subscribe->writer = $diary->user_id;
                    $subscribe->follower = $rental->house->user_id;
                    $subscribe->save();
                    Session::flash('success', 'Granted.');
                    return redirect()->route('rentals.show', $rental->id);
                }
            }
            else {
                $reen_checkincode = substr($checkincode, 0, 3).substr($checkincode, 6, 3).substr($checkincode, 3, 3);
                $verification = UserVerification::where('passport', 'like', '%'.$reen_checkincode.'%')->first();
                if (!is_null($verification)) {
                    $user = User::where('user_verifications_id', $verification->id)->first();
                    $code = array();
                    $code[0] = substr($user->verification->passport, 9, 3);
                    $code[2] = substr($user->verification->passport, 12, 3);
                    $code[1] = substr($user->verification->passport, 15, 3);
                    $reen_code = $code[0].$code[1].$code[2];
                    if ($checkincode == $reen_code) {
                        $today = Carbon::today();
                        $rentals = Rental::where('rental_datein', '>=', $today)->where('checkin_status', '0')->where('user_id', $user->id)->join('payments', 'rentals.payment_id', 'payments.id')->where('payment_status', 'Approved')->get();
                        if ($rentals->count()) {
                            $checkinBy = 'renter';
                            Session::flash('fail', 'Checkin Code change or Renter secret not match. Try again..');
                            return view('rentals.checkin-preview')->with('checkinBy', $checkinBy)->with('rentals', $rentals)->with('checkincode', $checkincode);
                        }
                    }
                }
            }
            Session::flash('fail', $request->checkincode . " is invalid.");
            return redirect()->route('rentals.show', $rental->id);
        }
        if (!is_null($rental)) {
            if ($checkincode == $rental->checkincode) {
                $this->validate($request, [
                    'checkin_name' => 'required',
                    'checkin_lastname' => 'required',
                    'checkin_personal_id' => 'required',
                    'checkin_tel' => 'required'
                ]);
                if ($request->current_code == $request->checkincode && Auth::user()->id == $rental->house->user_id && $rental->checkin_status == '0' && $request->checkin_name != null && $request->checkin_lastname != null && $request->checkin_personal_id != null && $request->checkin_tel != null){
                    if ($rental->payment->payment_status == 'Approved') {
                        CheckinList::create([
                            'checkin_name'=>$request->checkin_name,
                            'checkin_lastname'=>$request->checkin_lastname,
                            'checkin_personal_id'=>$request->checkin_personal_id,
                            'checkin_tel'=>$request->checkin_tel,
                            'rental_id'=>$rental->id
                        ]);
                        $rental->checkin_status = '1';
                        $rental->save();
                        $days = Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout));
                        for ($i = 0; $i <= $days+1; $i++) {
                            $diary = new Diary;
                            $diary->publish = '0';
                            if ($i == 0) {
                                $diary->title = 'Diary Title';
                                $diary->message = "Short story of this trip.";
                            }
                            if ($i != 0) {
                                $diary->message = 'Story about day '. $i;
                            }
                            $diary->days = $i;
                            $diary->user_id = $rental->user_id;
                            $diary->category_id = '1';
                            $diary->rental_id = $rental->id;
                            $diary->save();
                        }
                        $subscribe = Subscribe::where('writer', $diary->user_id)->where('follower', $rental->house->user_id)->first();
                        if (is_null($subscribe)) {
                            $subscribe = new Subscribe;
                        }
                        $subscribe->writer = $diary->user_id;
                        $subscribe->follower = $rental->house->user_id;
                        $subscribe->save();
                        return redirect()->route('rentals.show', $rental->id);
                    }
                    else {
                        Session::flash('fail', "Transection is not complete.");
                        if (Auth::user()->id == $rental->user_id) {
                            return redirect()->route('rentals.show', $rental->id);
                        }
                        return redirect()->route('rentals.rentmyrooms', Auth::user()->id)->withInput();
                    }
                }
                else {
                    Session::flash('fail', 'Checkin Code change or Checkin information is empty. Try again..');
                    $checkinBy = 'host';
                    return view('rentals.checkin-preview')->with('checkinBy', $checkinBy)->with('rental', $rental)->with('checkincode', $request->checkincode)->withInput();
                }
            }
        }
        Session::flash('fail', $request->checkincode . " is invalid.");
        return redirect()->route('rentals.rentmyrooms', Auth::user()->id)->withInput();
    }
}
