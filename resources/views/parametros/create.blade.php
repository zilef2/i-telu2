@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'parametros.store']) !!}

		<div class="mb-3">
			{{ Form::label('prompEjercicios', 'PrompEjercicios', ['class'=>'form-label']) }}
			{{ Form::text('prompEjercicios', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('NumeroTicketDefecto', 'NumeroTicketDefecto', ['class'=>'form-label']) }}
			{{ Form::string('NumeroTicketDefecto', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop