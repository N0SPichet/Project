@extends ('admin.layouts.app')
@section ('title', 'Edit Amenity')

@section ('content')
<div class="container">
	<div class="row m-t-10">
		<div class="col-sm-12">
			<a href="{{ route('comp.amenities.show', $amenity->id) }}" class="btn btn-outline-secondary"><i class="fas fa-chevron-left"></i> Back to Amenity</a>
		</div>
	</div>
	<div class="row m-t-10">
		<div class="col-md-6">
			{!! Form::model($amenity, ['route' => ['comp.amenities.update', $amenity->id], 'method' => 'PUT']) !!}
			{{ Form::label('name', 'Amenity name') }}
			{{ Form::text('name', null, ['class' => 'form-control']) }}
			{{ Form::submit('Save Changes', ['class' => 'btn btn-success m-t-20']) }}
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection
