@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'plans.store']) !!}

		<div class="mb-3">
			{{ Form::label('nombre', 'Nombre', ['class'=>'form-label']) }}
			{{ Form::text('nombre', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('tipo', 'Tipo', ['class'=>'form-label']) }}
			{{ Form::text('tipo', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valor', 'Valor', ['class'=>'form-label']) }}
			{{ Form::text('valor', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('caducidad', 'Caducidad', ['class'=>'form-label']) }}
			{{ Form::string('caducidad', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('tokens', 'Tokens', ['class'=>'form-label']) }}
			{{ Form::text('tokens', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop