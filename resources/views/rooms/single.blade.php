@extends ('dashboard.main')
@section ('title', 'Review your settings | Rooms')

@section ('content')
<div class="container">
	<div class="row m-t-10">
		<div class="col-sm-12">
			@if (Auth::user()->id == $house->user_id)
			<a href="{{route('rooms.index')}}" class="btn btn-outline-secondary">Back to My Room</a>
			@endif
		</div>
	</div>
	<div class="row m-t-10">
		<div class="col-md-8 float-left">
			<h1>Review your settings</h1>
			<h2 align="center">{{ $house->house_title }}
				<small id="showPublish" class="m-t-20" style="font-size: 18px;">
					@if ($house->publish == '2')
					<span class="text-danger"><i class="fas fa-trash"></i> Trash</span>
					@elseif ($house->publish == '1')
					<span class="text-success"><i class="fas fa-eye"></i> Published</span>
					@elseif ($house->publish == '0')
					<span class="text-danger"><i class="fas fa-eye-slash"></i> Private</span>
					@endif
				</small>
			</h2>
			<div class="col-md-12 clear-both">
				<h4>Cover Image</h4>
				<div class="col-md-6 float-left margin-content">
					<a href="{{ asset('images/houses/'.$house->id.'/'.$house->cover_image) }}"><img src="{{ asset('images/houses/'.$house->id.'/'.$house->cover_image) }}" class="img-responsive" style="border-radius: 2%"></a>
				</div>
				<div class="col-md-6 float-left margin-content">
					<p></p>
				</div>
			</div>
			<div class="col-md-12 clear-both">
				<h4>Images</h4>
				<div class="gallery">
					@foreach ($house->images as $image)
					<div class="margin-content box">
						<div class="img-box">
							<a href="{{ asset('images/houses/'.$house->id.'/'.$image->name) }}"><img src="{{ asset('images/houses/'.$house->id.'/'.$image->name) }}" class="img-responsive" style="border-radius: 2%"></a>
							@if ($image->name != $house->cover_image && Auth::user()->id == $house->user_id)
							<a href="{{ route('rooms.detroyimage', $image->id)}}" class="btn btn-default btn-sm with-trash"><i class="fas fa-trash"></i></a>
							@endif
						</div>
					</div>
					@endforeach
				</div>
			</div>
			<div class="col-md-12">
				<h4>Room</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						<p> Room Description {!! $house->house_description !!}</p>
						<hr>
						<p> About your place (optional) {!! $house->about_your_place !!}</p>
						<hr>
						<p> What guests can access (optional) {!! $house->guest_can_access !!}</p>
						<hr>
						<p> Other things to note (optional) {!! $house->optional_note !!}</p>
						<hr>
						<p> About the neighborhood (optional) {!! $house->about_neighborhood !!} </p>
						<hr>
						<p> Type of property : {{ $house->house_property }} </p>
						<hr>
						<p> Guests have : {{ $house->house_guestspace }} </p>
					</div>
				</div>

				<h4>Bedroom</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						<p> How many guests can your place accommodate : {{ $house->house_capacity }} {{ $house->house_capacity>1?'guests':'guest' }}/room </p>
						<p> How many bedrooms : {{ $house->house_bedrooms }} {{ $house->house_bedrooms>1?'rooms':'room' }}</p>
						<p> How many beds : {{ $house->house_beds }} bed/room </p>
					</div>
				</div>

				<h4>Bathroom</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						<p> How many bathrooms : {{ $house->house_bathroom }} room </p>
						<p> Is the bathroom private :  
							@if ($house->house_bathroomprivate == 1)
								Yes
							@elseif ($house->house_bathroomprivate != 1)
								No
							@endif
						</p>
					</div>
				</div>

				<h4>Address</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						<p> {{ $house->house_address }} {{ $house->sub_district->name }} {{ $house->district->name }}, {{ $house->province->name }} </p>
						<p>Postcode {{ $house->house_postcode }} </p>
					</div>
						<div id="map-canvas"></div>
					
				</div>

				<h4>Amenities</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						@foreach ($house->houseamenities as $houseamenity)
							<p>{{ $houseamenity->name }}</p>
						@endforeach
					</div>
				</div>

				<h4>Shared space</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						@foreach ($house->housespaces as $housespace)
							<p>{{ $housespace->name }}</p>
						@endforeach
					</div>
				</div>

				<h4>Your House Rules</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						@foreach ($house->houserules as $houserule)
							<p>{{ $houserule->name }}</p>
						@endforeach
						@if ($house->optional_rules)
						<br>
						<p> {{ $house->optional_rules }}</p>
						@endif
					</div>
				</div>

				<h4>Availability</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						<p>Advance notice: {{$house->guestarrives->notice }}</p>
						@if ($house->guestarrives->checkin_to == 'Flexible')
						<p>Check-in: Anytime after {{ $house->guestarrives->checkin_from }}</p>
						@else
						<p>Check-in: {{ $house->guestarrives->checkin_from }} - {{ $house->guestarrives->checkin_to }}</p>
						@endif
					</div>
				</div>

				<h4>Pricing</h4>
				<div class="card">
					<div style="margin-left: 10px; margin-right: 10px;">
						<p>Base price: {{ $house->houseprices->price }} Thai baht/night{{ $house->houseprices->type_price=='1'?'/person':'' }}</p>
						@if ($house->houseprices->food_price)
						<p>Food included : {{ $house->houseprices->food_price }} Thai baht/night{{ $house->houseprices->type_price=='1'?'/person':'' }}</p>
						<div class="alert alert-info" role="alert">
  							@if ($house->foods->breakfast == '1')Breakfast
	  							@if ($house->foods->lunch == '1')/ Lunch
	  								@if ($house->foods->dinner == '1')/ Dinner
	  								@endif
	  							@else
	  								@if ($house->foods->dinner == '1')/ Dinner
	  								@endif
	  							@endif
	  						@elseif ($house->foods->lunch == '1')Lunch
	  							@if ($house->foods->dinner == '1')/ Dinner
	  							@endif
	  						@else
  								@if ($house->foods->dinner == '1')Dinner
  								@endif
  							@endif
						</div>
						@endif
						<p>Weekly discount: {{ $house->houseprices->weekly_discount }}%</p>
						<p>Monthly discount: {{ $house->houseprices->monthly_discount }}%</p>
					</div>
				</div>
				</div>
		</div>
		<div class="col-md-4 float-left">
			<div class="col-md-12">
				<div class="dl-horizontal">
					<p><b>Created by</b> {{ $house->user->user_fname }} {{ $house->user->user_lname }}</p>
					<p><b>Created at</b> {{ date("jS M, Y", strtotime($house->created_at)) }}</p>
					<p><b>Last modified</b> {{ $house->updated_at->diffForHumans() }}</p>
				</div>
				<div class="margin-content">
				<p>Link to public <a target="_blank" href="{{ route('rooms.show', $house->id) }}" class="btn btn-outline-secondary">Link</a></p>
				</div>
				@if (Auth::user()->id == $house->user_id || Auth::user()->hasRole('Admin'))
				<div class="row">
					<div class="margin-auto text-center">
						@if ($house->publish == '1' || $house->publish == '0')
						<div class="col">
							<a id="publish" class="btn btn-outline-info m-t-20">Publish</a>
						</div>
						@endif
						@if ($house->publish == '2')
						<div class="col">
							{!! Html::linkRoute('rooms.restore', 'Restore', array($house->id), array('class' => 'btn btn-outline-warning m-t-20')) !!}
						</div>
						@else
						<div class="col">
							{!! Html::linkRoute('rooms.edit', 'Edit', array($house->id), array('class' => 'btn btn-outline-warning m-t-20')) !!}
						</div>
						@endif
						@if ($house->publish == '2')
						@if ($house->rentals->count() == 0)
						<div class="col">
							{!! Form::open(['route' => ['rooms.destroy', $house->id], 'method' => 'DELETE', 'style'=>'display:inline']) !!}
								{!! Form::submit('Delete', ['class' => 'btn btn-danger m-t-20']) !!}
							{!! Form::close() !!}
						</div>
						@else
						<div class="col">
							{!! Form::open(['route' => ['rooms.permanent.delete', $house->id], 'method' => 'PUT', 'style'=>'display:inline']) !!}
								{!! Form::submit('Permanent Delete', ['class' => 'btn btn-danger m-t-20']) !!}
							{!! Form::close() !!}
						</div>
						@endif
						@else
						<div class="col">
							{!! Html::linkRoute('rooms.temp.delete', 'Move to Trash', [$house->id], ['class' => 'btn btn-danger m-t-20']) !!}
						</div>
						@endif
					</div>
				</div>
				@endif
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$('#publish').on('click', function() {
			$.ajax({
				type: 'get',
				url: '{{ route('api.rooms.publish', $house->id) }}',
				data: {},
				success: function(response) {
					if (response.data == 1) {
						$('#showPublish').html('<span class="text-success m-t-20"><i class="fas fa-eye"></i> Published</span>')
					}
					else if(response.data == 0) {
						$('#showPublish').html('<span class="text-danger m-t-20"><i class="fas fa-eye-slash"></i> Private</span>')
					}
				}
			});
		});
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
		draggable: true
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
