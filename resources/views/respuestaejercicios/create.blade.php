@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'respuestaejercicios.store']) !!}

		<div class="mb-3">
			{{ Form::label('core', 'Core', ['class'=>'form-label']) }}
			{{ Form::text('core', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('precisa', 'Precisa', ['class'=>'form-label']) }}
			{{ Form::text('precisa', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop