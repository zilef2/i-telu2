@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'usuariopendientespagos.store']) !!}

		<div class="mb-3">
			{{ Form::label('fecha_peticion', 'Fecha_peticion', ['class'=>'form-label']) }}
			{{ Form::string('fecha_peticion', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('fecha_aprovacion', 'Fecha_aprovacion', ['class'=>'form-label']) }}
			{{ Form::string('fecha_aprovacion', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valorTotal', 'ValorTotal', ['class'=>'form-label']) }}
			{{ Form::string('valorTotal', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('tokensComprados', 'TokensComprados', ['class'=>'form-label']) }}
			{{ Form::text('tokensComprados', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop