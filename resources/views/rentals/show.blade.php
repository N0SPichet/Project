@extends ('dashboard.main')
@section ('title', $rental->user->user_fname.' - '.$rental->house->district->name.' '.$rental->house->province->name.' - Trip')
@section('stylesheets')
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=qei14aeigd6p0lkquybi330fte0vp7ne9ullaou6d5ti437y"></script>
<script>
	tinymce.init({ 
		selector:'textarea',
		menubar: false
	});
</script>
@endsection

@section ('content')
<div class="container">
	<div class="row m-t-10">
		@if (Auth::user()->id == $rental->house->user_id)
		<div class="col-sm-12">
			<a href="{{ route('rentals.rentmyrooms', $rental->house->user_id) }}" class="btn btn-outline-secondary"><i class="fas fa-chevron-left"></i> Back</a>
		</div>
		@elseif (Auth::user()->id == $rental->user_id)
		<div class="col-sm-12">
			<a href="{{ route('rentals.mytrips', $rental->user_id) }}" class="btn btn-outline-secondary"><i class="fas fa-chevron-left"></i> Back</a>
		</div>
		@endif
	</div>
	<div class="row m-t-10">
		<div class="col-md-12">
			<p class="lead">Rental #ID {{ $rental->id }} (You're {{ Auth::user()->id == $rental->house->user_id? 'Host':'Renter' }})</p>
		</div>
		<div class="col-md-8 m-b-20">
			@if (isset($rental->checkinlist))
			<div class="m-b-20">
				<div class="card margin-content">
					<label>Checkin By Other</label>
					<p><b>Name :</b> {{ $rental->checkinlist->checkin_name }}</p>
					<p><b>Lastname :</b> {{ $rental->checkinlist->checkin_lastname }}</p>
					<p><b>Personal_id :</b> {{ $rental->checkinlist->checkin_personal_id }}</p>
					<p><b>Tel :</b> {{ $rental->checkinlist->checkin_tel }}</p>
				</div>
			</div>
			@endif
			<div class="card">
				<div class="margin-content">
					<label>Booking Detail</label>
					<p>@if($rental->house->checkType($rental->house_id)) <img src="{{ asset('images/houses/house.png')}}" style="height: 20px; width: 20px; margin-bottom: 10px;"> @else <img src="{{ asset('images/houses/apartment.png')}}" style="height: 20px; width: 20px; margin-bottom: 10px;"> @endif Room Name :  {{ $rental->house->house_title }}  </p>
						
					<p><i class="fas fa-user"></i> Hosted by : <a href="{{ route('users.show', $rental->house->user_id) }}" target="_blank" class="btn btn-outline-info">{{ $rental->house->user->user_fname }} {{ $rental->house->user->user_lname }}</a></p>
						
					<p><i class="fas fa-user"></i> Rented by : <a href="{{ route('users.show', $rental->user_id) }}" target="_blank" class="btn btn-outline-info">{{ $rental->user->user_fname }} {{ $rental->user->user_lname }}</a></p>
						
					<p><i class="far fa-calendar-alt"></i> Stay Date : {{ date('jS F, Y', strtotime($rental->rental_datein)) }} <i class="fas fa-long-arrow-alt-right"></i> {{ date('jS F, Y', strtotime($rental->rental_dateout)) }} ({{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout)) }} {{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout))>'1'?'days':'day' }}) </p>
					@if ($rental->house->checkType($rental->house_id))
					<p><i class="far fa-user"></i> Total guest : {{ $rental->rental_guest*$rental->no_rooms }} {{ $rental->rental_guest*$rental->no_rooms>1?'peoples':'people' }}</p>
					<p><i class="fas fa-bed"></i> Room : {{ $rental->no_rooms }} {{ $rental->no_rooms>1?'rooms':'room' }}</p>
					@else
						@if ($rental->no_type_single > 0)
						<p><i class="fas fa-bed"></i> Single Room (Standard) : {{ $rental->no_type_single }} {{ $rental->no_type_single > 1 ? 'Rooms':'Room' }}.</p>
						@endif
						@if ($rental->no_type_deluxe_single > 0)
						<p><i class="fas fa-bed"></i> Deluxe Single Room : {{ $rental->no_type_deluxe_single }} {{ $rental->no_type_deluxe_single > 1 ? 'Rooms':'Room' }}.</p>
						@endif
						@if ($rental->no_type_double_room > 0 )
						<p><i class="fas fa-bed"></i> Double Room (Standard) : {{ $rental->no_type_double_room }} {{ $rental->no_type_double_room > 1 ? 'Rooms':'Room' }}.</p>
						@endif
					@endif
					@if ($rental->select_food == '1')<p><i class="fas fa-utensils"></i> Food included</p> @else<p> <i class="fas fa-utensils"></i> Food are <span class="text-danger">not included</span></p> @endif
					@if ($rental->payment->payment_status == 'Approved')
					<p>Address : {{ $rental->house->house_address }} {{ $rental->house->sub_district->name }} {{ $rental->house->district->name }}, {{ $rental->house->province->name }}</p>
					<div id="map-canvas"></div>
					@endif
					<br>

					@if ($rental->host_decision == 'accept')
					<p class="text-primary"><b>Host Accepted.</b></p>
					@endif

					@if ( Auth::user()->id == $rental->user->id && $rental->payment->payment_status == 'Approved' )
					<p>Check in Code : <b>{{ $rental->checkincode }}{{ $rental->id }}</b><span> use this code to check in</span></p>
					@endif
						
					@if ( $rental->checkin_status == '1' )
					<p>Check in Status: <button class="btn-success">Confirmed</button></p>
					@elseif ($rental->checkin_status == '0')
					<p>Check in  Status: <span class="text-danger">NO</span></p>
					@endif
				</div>
			</div>
			@if($rental->payment->payment_status != null)
			<div class="m-t-20">
				<div class="card">
					@if (Auth::user()->id == $rental->user_id)
						@if($rental->payment->payment_status != 'Cancel')
						<div class="margin-content">
							<label>Payment</label>
							@if ($rental->payment->payment_status != 'Out of Date')
							<p><b>Bank Name :</b> {{ $rental->payment->payment_bankname }} </p>
							<p><b>Bank Holder :</b> {{ $rental->payment->payment_holder }} </p>
							<p><b>Bank Account :</b> {{ $rental->payment->payment_bankaccount }} </p>
							<p><b>Amount :</b> {{ $rental->payment->payment_amount }} Thai Baht</p>
							@endif
							<p><b>Status :</b> {{ $rental->payment->payment_status }}</b> </p>
							@if ($rental->payment->payment_status == 'Reject')
							<p class="text-danger">Your payment was incorrect. Please try to submit again.</p>
							<a href="{{ route('rentals.edit', $rental->id) }}" class="btn btn-md btn-warning">Resubmit</a>
							@endif

							@if ($rental->payment->payment_status != 'Out of Date')
							<div align="center">
								@if ($rental->payment->payment_transfer_slip != null)
								<a target="_blank" href="{{ asset('images/payments/'.$rental->payment_id.'/'.$rental->payment->payment_transfer_slip) }}"><img src="{{ asset('images/payments/'.$rental->payment_id.'/'.$rental->payment->payment_transfer_slip) }}" class="img-thumbnail" width="80" height="auto"></a>
								@else
								<img src="{{ asset('images/payments/default.png') }}" class="img-thumbnail" width="80" height="auto">
								@endif
							</div>
							<br>
							<p class="text-center">Transfer Slip</p>
							@endif
						</div>
						@else
						@if ($rental->payment->payment_status == 'Cancel')
						<div class="margin-content">
							<a href="#" class="btn btn-md btn-warning" style="width: 100px;">Refund</a>
						</div>
						@endif
						@endif
					@else
					@if ($rental->payment->payment_status == 'Waiting')
					<div class="margin-content">
						<p><b>Payment(Waiting) - this payment in waiting for check status.</b></p>
					</div>
					@endif
					@if ($rental->checkin_status == '0')
						@if ($rental->payment->payment_status == 'Approved')
						<div class="margin-content">
							<p>{{ $rental->user->user_fname }}'s has paid for this rental.</p>
						</div>
						@endif
					@else
					<div class="margin-content">
						<p>{{ $rental->user->user_fname }}'s already checkin.</p>
					</div>
					@endif
					@endif
				</div>
			</div>
			@elseif ($rental->host_decision == 'accept')
			<div class="m-t-20">
				<div class="card">
					<div class="margin-content">
						@if (Auth::user()->id == $rental->user->id)
							<p>{{ $rental->user->user_fname }}'s must have a payment in time and exactly as payment page show.</p>
							@if ($rental->house->checkType($rental->house_id))
							<p>Details</p>
							<p><i class="fas fa-bed"></i> <b>Room</b> {{ $rental->no_rooms }} {{ $rental->no_rooms > 1 ? 'rooms':'room' }} ({{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout)) }} {{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout))>'1'?'days':'day' }}) - {{ $room_price }} Thai baht ({{ $rental->room_price }} {{ $rental->house->houseprices->type_price=='1'?'Thai baht/person':'Thai baht/day' }}).</p>
							<p><i class="fas fa-utensils"></i> <b>Food</b> {{ $rental->select_food=='1'?'yes':'no' }} - {{ $food_price }} Thai baht ({{ $rental->house->houseprices->food_price }} {{ $rental->house->houseprices->type_price=='1'?'Thai baht/person':'Thai baht/day' }}).</p>
							@else
							<p>Details</p>
								@if ($rental->no_type_single > 0)
								<p><i class="fas fa-bed"></i> Single Room (Standard) : {{ $rental->no_type_single }} {{ $rental->no_type_single > 1 ? 'rooms':'room' }} ({{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout)) }} {{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout))>'1'?'days':'day' }}) - {{ $type_single_price }} Thai baht.</p>
								@endif
								
								@if ($rental->no_type_deluxe_single > 0)
								<p><i class="fas fa-bed"></i> Deluxe Single Room : {{ $rental->no_type_deluxe_single }} {{ $rental->no_type_deluxe_single > 1 ? 'rooms':'room' }} ({{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout)) }} {{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout))>'1'?'days':'day' }}) - {{ $type_deluxe_single_price }} Thai baht.</p>
								@endif

								@if ($rental->no_type_double_room > 0 )
								<p><i class="fas fa-bed"></i> Double Room (Standard) : {{ $rental->no_type_double_room }} {{ $rental->no_type_double_room > 1 ? 'rooms':'room' }} ({{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout)) }} {{ Carbon::parse($rental->rental_datein)->diffInDays(Carbon::parse($rental->rental_dateout))>'1'?'days':'day' }}) - {{ $type_double_room_price }} Thai baht.</p>
								@endif
							@endif
							<p><i class="fas fa-concierge-bell"></i> <b>Service fee</b> {{ $fee}} Thai baht.</p>
							<p><i class="fas fa-hand-holding-usd"></i> <b>Discount</b> {{ $discount }} Thai baht ({{ $rental->discount }}%)</p>
							<p><i class="fas fa-money-check"></i> <b>Total price</b> <span class="text-danger">{{$total_price}}</span> Thai baht!</p>
							@if ($rental->payment->payment_status == null)
							{!! Html::linkRoute('rentals.edit', 'Payment', array($rental->id), array('class' => 'btn btn-success btn-sm m-t-10 m-b-20')) !!}
							@else
							<button type="button" class="btn btn-success btn-sm m-t-10 m-b-20">
								<div class="text-center">Payment already submited</div>
							</button>
							@endif
						@else
						<label>{{ $rental->user->user_fname }}'s Not Paying Yet</label>
						@endif
					</div>
				</div>
			</div>
			@endif

			@if ($rental->host_decision == 'accept' && $rental->checkin_status == '1')
				@if ($rental->payment->payment_status != 'Out of Date' && $rental->payment->payment_status != 'Cancel' && $rental->payment->payment_status != 'Reject')
				<div class="m-t-20">
					<div class="card">
						<div class="margin-content">
							@if ($review != null )
								@if ($rental->user_id == Auth::user()->id || Auth::user()->id == $rental->house->user_id)
								<label>Review</label>
								<div class="comment">
									<div class="author-info">
										<img src="{{ 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($review->user->email))) . '?s=50&d=monsterid' }}" class="author-image">
										<div class="author-name">
											<h4>{{ $review->user->user_fname }} 
												@if (Auth::user()->id == $rental->user->id)
												<a href="{{ route('reviews.edit', $review->id)}}" style="position:absolute; bottom: 260px; right: 20px;" class="btn btn-default btn-sm"><i class="far fa-edit"></i></a>
												@endif
											</h4>
											<p class="author-time">{{ date('jS F, Y - g:iA', strtotime($review->created_at)) }}</p>
										</div>
									</div>
									<div class="comment-content">
										<p>cleaness : <span class="text-danger">{{ $review->clean }}</span></p>
										<p>amenities : <span class="text-danger">{{ $review->amenity }}</span></p>
										<p>services : <span class="text-danger">{{ $review->service }}</span></p>
										<p>host : <span class="text-danger">{{ $review->host }}</span></p>
										<p>{!! $review->comment !!}</p>
									</div>
								</div>
								@endif
							@endif

							@if ($review == null)
								@if ($rental->payment->payment_status == 'Approved' && $rental->checkin_status != '1')
									@if (Auth::user()->id == $rental->user_id)
									<p class="text-danger">Only Confirmed rental can review.</p>
									<p>To get Confirmed status : you must rent the room and have a completely check in.</p>
									@endif
								@endif

								@if ($rental->payment->payment_status == 'Approved' && $rental->checkin_status == '1')
									@if (Auth::user()->id == $rental->house->user_id)
									<label>Review</label>
									<p>No review</p>
									@elseif ( $rental->user->id == Auth::user()->id )
									<label>Write a Review</label>
									{!! Form::open(array('route' => 'reviews.store', 'data-parsley-validate' => '')) !!}
										{{ Form::hidden('house_id', $rental->house_id) }}
										{{ Form::hidden('rental_id', $rental->id) }}

										{{ Form::label('name', 'Review as') }}
										{{ Form::text('name', Auth::user()->user_fname . " " . Auth::user()->user_lname, ['class' => 'form-control', 'required' => '', 'readonly' => '']) }}
										{{ Form::label('clean', 'Clean') }}
										<div class="star-rating" align="center">
											@for ($i = 5; $i > 0; $i--)
											<input type="radio" name="clean" value="{{$i}}" id="clean-{{$i}}">
											<label for="clean-{{$i}}" title="star {{$i}}">
												<i class="far fa-star"></i>
											</label>
										@endfor
										</div>

										{{ Form::label('amenity', 'Amenity') }}
										<div class="star-rating" align="center">
										@for ($i = 5; $i > 0; $i--)
											<input type="radio" name="amenity" value="{{$i}}" id="amenity-{{$i}}">
											<label for="amenity-{{$i}}" title="star {{$i}}">
												<i class="far fa-star"></i>
											</label>
										@endfor
										</div>

										{{ Form::label('service', 'Service') }}
										<div class="star-rating" align="center">
										@for ($i = 5; $i > 0; $i--)
											<input type="radio" name="service" value="{{$i}}" id="service-{{$i}}">
											<label for="service-{{$i}}" title="star {{$i}}">
												<i class="far fa-star"></i>
											</label>
										@endfor
										</div>

										{{ Form::label('host', 'Host') }}
										<div class="star-rating" align="center">
										@for ($i = 5; $i > 0; $i--)
											<input type="radio" name="host" value="{{$i}}" id="host-{{$i}}">
											<label for="host-{{$i}}" title="star {{$i}}">
												<i class="far fa-star"></i>
											</label>
										@endfor
										</div>

										{{ Form::label('comment', 'Review') }}
										{{ Form::textarea('comment', null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px;', 'rows' => '5']) }}
										{{ Form::submit('Publish', array('class' => 'btn btn-success btn-md m-t-10 pull-right')) }}
									{!! Form::close() !!}
									@endif
								@endif

							@endif
						</div>
					</div>
				</div>
				@endif
			@endif		
		</div>

		<div class="col-md-4">
			@if ($rental->checkin_status == '0' && $rental->payment->payment_status == 'Approved' && Auth::user()->id == $rental->user_id)
			<div class="well">
				<h2>Check in Section</h2>
				<p><small>put checkin code here if it true, you will get granted status</small></p>
				@if ($errors->any())
				    <div class="alert alert-danger">
				        <ul>
				            @foreach ($errors->all() as $error)
				                <li>{{ $error }}</li>
				            @endforeach
				        </ul>
				    </div>
				@endif
				{{ Form::open(array('route' => 'rentals.checkin', 'data-parsley-validate' => '')) }}
					{{ Form::label('checkincode', 'Check in') }}
					{{ Form::text('checkincode', null, array('class' => 'form-control', 'required' => '', 'placeholder' => 'Renter Passport or Checkin Code')) }}
					<div class="text-center">
						{{ Form::submit('Check in', array('class' => 'btn btn-success btn-md m-t-20')) }}
					</div>
				{{ Form::close() }}
			</div>
			<hr>
			@endif
			<div class="well">
				<p><b>Rented by:</b> {{ $rental->user->user_fname }} {{ $rental->user->user_lname }} </p>
				<p><b>Created at:</b> {{ date('M j, Y H:m:s', strtotime($rental->created_at)) }} </p>
				<p><b>Last update:</b> {{ date('M j, Y H:m:s', strtotime($rental->updated_at)) }} </p>

				@if (Auth::user()->id == $rental->house->user_id)
				<hr>
				<div class="row">
					<div class="col-sm-8 float-left">
						@if ($rental->host_decision == 'waiting' && $rental->payment->payment_status != 'Cancel')
						{!! Form::open(['route' => ['rentals.accept-rentalrequest', $rental->id]]) !!}
							<button type="submit" class="btn btn-primary btn-block btn-sm"><i class="far fa-check-circle"></i> Accept</button>
						{!! Form::close() !!}

						{!! Form::open(['route' => ['rentals.reject-rentalrequest', $rental->id]]) !!}
							<button type="submit" class="btn btn-danger btn-block btn-sm m-t-10"><i class="far fa-times-circle"></i> Reject</button>
						{!! Form::close() !!}
						@elseif ($rental->host_decision == 'accept' && $rental->payment->payment_status != 'Cancel')
							<button class="btn btn-default btn-success btn-block btn-sm disabled"><i class="fas fa-check"></i> Accepted</button>
						@elseif ($rental->host_decision == 'reject' && $rental->payment->payment_status != 'Cancel')
							<button class="btn btn-default btn-danger btn-block btn-sm disabled"><i class="fas fa-check"></i> Rejected</button>
						@endif
					</div>
				</div>
				@endif
			</div>
			@if (Auth::user()->id == $rental->user_id)
			<div class="card m-t-10">
				<div class="margin-content" align="center">
					@if ($rental->checkin_status == '1')
					<a href="{{ route('diaries.tripdiary', [$rental->id, $rental->user_id]) }}" class="btn btn-primary btn-md">Write Diary for this great trip</a>
					@else
					<p>To write diary for this great trip, you must have <span class="text-success">Confirmed</span> status.</p>
					@endif
				</div>
			</div>
			@endif
			@if(Auth::user()->id == $rental->house->user_id && $rental->diary != null)
			@if($rental->diary->publish == 1 || $rental->diary->publish == 2)
			<div class="card m-t-10">
				<div class="margin-content text-success" align="center">
					<p><b><a target="_blank" href="{{ route('users.show', $rental->user_id) }}">{{ $rental->user->user_fname }}</a></b> write diary about this trip <a target="_blank" href="{{ route('diaries.show', $rental->diary->id) }}">Click to read</a></p>
				</div>
			</div>
			@endif
			@endif
		</div>
	</div>
</div>
@endsection

@section('scripts')
	<script type="text/javascript">
		$(document).ready(function() {

		});

		var lat = {{ $map->map_lat }};
		var lng = {{ $map->map_lng }};

		var map = new google.maps.Map(document.getElementById('map-canvas'), {
			center:{
				lat: lat,
				lng: lng
			},
			zoom: 16
		});

		var marker = new google.maps.Marker({
			position:{
				lat: lat,
				lng: lng
			},
			map: map,
			draggable: false
		});

		var circle = new google.maps.Circle({
			position:{
				lat: lat,
				lng: lng
			},
			strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#0000FF',
            fillOpacity: 0.3,
            map: map,
            center: {lat: lat, lng: lng},
            radius: Math.sqrt(10) * 60
		});
	</script>
@endsection
