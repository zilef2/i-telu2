@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'calificacions.store']) !!}

		<div class="mb-3">
			{{ Form::label('TipoPrueba', 'TipoPrueba', ['class'=>'form-label']) }}
			{{ Form::text('TipoPrueba', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('prompUsado', 'PrompUsado', ['class'=>'form-label']) }}
			{{ Form::text('prompUsado', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('valor', 'Valor', ['class'=>'form-label']) }}
			{{ Form::string('valor', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('tokens', 'Tokens', ['class'=>'form-label']) }}
			{{ Form::text('tokens', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop