@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'cuotas.store']) !!}

		<div class="mb-3">
			{{ Form::label('numeroDeLaCuota', 'NumeroDeLaCuota', ['class'=>'form-label']) }}
			{{ Form::text('numeroDeLaCuota', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('numeroDecuotas', 'NumeroDecuotas', ['class'=>'form-label']) }}
			{{ Form::text('numeroDecuotas', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valor', 'Valor', ['class'=>'form-label']) }}
			{{ Form::string('valor', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop