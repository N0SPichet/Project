@extends ('main')

@section ('title', 'Review your settings | Rooms')

@section ('content')
<div class="container">
	<div class="row m-t-10">
		<div class="col-md-8 float-left">
			<h1>Review your settings</h1>
			<h2 align="center">{{ $house->house_title }}
				<small id="showPublish">
					@if ($house->publish == '1')
					<span class="text-success margin-top-20"><i class="fas fa-eye"></i> Published</span>
					@elseif ($house->publish == '0')
					<span class="text-danger margin-top-20"><i class="fas fa-eye-slash"></i> Private</span>
					@endif
				</small>
			</h2>
			<div class="col-md-12 clear-both">
				<h4>Cover Image</h4>
				<div class="col-md-6 float-left">
					<a id="single_image" href="{{ asset('images/houses/' . $house->cover_image) }}"><img src="{{ asset('images/houses/' . $house->cover_image) }}" class="img-responsive" style="border-radius: 5%"></a>
				</div>
				<div class="col-md-6 float-left">
					
				</div>
			</div>
			<div class="col-md-12 clear-both">
				<h4>Images</h4>
				<div class="gallery">
					@foreach ($images as $image)
					<div class="col-md-4 float-left">
						<a id="single_image" href="{{ asset('images/houses/' . $image->image_name) }}"><img src="{{ asset('images/houses/' . $image->image_name) }}" class="img-responsive" style="border-radius: 5%"></a>
						@if ($image->image_name != $house->cover_image && $house->users_id == Auth::user()->id)
						<a href="{{ route('rooms.detroyimage', $image->id)}}" style="position: absolute; top:2px; right: : 2px; z-index: 100;" class="btn btn-default btn-sm"><i class="fas fa-trash"></i></a>
						@endif
						<br>
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
						<p> How many guests can your place accommodate : {{ $house->house_capacity }} guest </p>
						<p> How many bedrooms : {{ $house->house_bedrooms }} room </p>
						<p> How many beds : {{ $house->house_beds }} bed </p>
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
						<p>Base price: ฿{{ $house->houseprices->price }}/Night/@if ($house->houseprices->price_perperson == '1')Person @elseif ($house->houseprices->price_perperson == '2')Day @endif</p>
						@if ($house->houseprices->food_price)
						<p>Food included : ฿{{ $house->houseprices->food_price }}/Night/@if ($house->houseprices->price_perperson == '1')Person @elseif ($house->houseprices->price_perperson == '2')Day @endif</p>
						<div class="alert alert-info" role="alert">
  							@if ($house->foods->breakfast == '1') Breakfast @endif 
  							@if ($house->foods->lunch == '1') @if($house->foods->lunch == '1' && $house->foods->breakfast == '1')/@endif Lunch @endif
  							@if ($house->foods->dinner == '1') @if($house->foods->dinner == '1' && $house->foods->lunch == '1')/@endif Dinner @endif
						</div>
						@endif
						<p>Weekly discount: {{ $house->houseprices->weekly_discount }}%</p>
						<p>Monthly discount: {{ $house->houseprices->monthly_discount }}%</p>
					</div>
				</div>
				</div>
		</div>
		<div class="col-md-4 float-left">
			<div class="well">
				<div class="dl-horizontal">
					<dt>Created by</dt>
					<dd>{{ $house->users->user_fname }} {{ $house->users->user_lname }}</dd>
					<dt>Created at</dt>
					<dd>{{ date("jS M, Y", strtotime($house->created_at)) }}</dd>
					<dt>Date modified</dt>
					<dd>{{ date("jS M, Y", strtotime($house->updated_at)) }}</dd>
				</div>
				<div class="margin-content">
				<p>Link to public <a href="{{ route('rooms.show', $house->id) }}" class="btn btn-outline-secondary">Link</a></p>
				</div>
				@if ($house->users_id == Auth::user()->id)
				<div class="row">
					<div class="col-sm-4 float-left">
						<a id="publish" class="btn btn-outline-info btn-block btn-h1-spacing">Publish</a>
					</div>
					<div class="col-sm-4 float-left">
						{!! Html::linkRoute('rooms.edit', 'Edit', array($house->id), array('class' => 'btn btn-outline-warning btn-block btn-h1-spacing')) !!}
					</div>
					<div class="col-sm-4 float-left">
						{!! Form::open(['route' => ['rooms.destroy', $house->id], 'method' => 'DELETE', 'style'=>'display:inline']) !!}
							{!! Form::submit('Delete', ['class' => 'btn btn-danger btn-block btn-h1-spacing']) !!}
						{!! Form::close() !!}
					</div>
				</div>
				@endif
				<hr>
				<div class="row">
					<div class="col-sm-6 float-left">
						{!! Html::linkRoute('rooms.index-myroom', 'Back to My Room', array(Auth::user()->id), array('class' => 'btn btn-outline-secondary')) !!}
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
	<script type="text/javascript">
		$(document).ready(function() {
			/* This is basic - uses default settings */
			$("a#single_image").fancybox({
				'transitionIn'	:	'elastic',
				'transitionOut'	:	'elastic',
				'speedIn'		:	200, 
				'speedOut'		:	200, 
				'overlayShow'	:	false
			});
			/* Using custom settings */
			$("a#inline").fancybox({
				'hideOnContentClick': true
			});
			/* Apply fancybox to multiple items */
			$("a.group").fancybox({
				'transitionIn'	:	'elastic',
				'transitionOut'	:	'elastic',
				'speedIn'		:	600, 
				'speedOut'		:	200, 
				'overlayShow'	:	false
			});

			$('#publish').on('click', function() {
				$.ajax({
					type: 'get',
					url: '{{ route('api.rooms.publish', $house->id) }}',
					data: {},
					success: function(response) {
						if (response.data == 1) {
							$('#showPublish').html('<span class="text-success margin-top-20"><i class="fas fa-eye"></i> Published</span>')
						}
						else if(response.data == 0) {
							$('#showPublish').html('<span class="text-danger margin-top-20"><i class="fas fa-eye-slash"></i> Private</span>')
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