@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($respuestapdf, array('route' => array('respuestapdfs.update', $respuestapdf->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('guardar_pdf', 'Guardar_pdf', ['class'=>'form-label']) }}
			{{ Form::text('guardar_pdf', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('resumen', 'Resumen', ['class'=>'form-label']) }}
			{{ Form::text('resumen', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('nivel', 'Nivel', ['class'=>'form-label']) }}
			{{ Form::text('nivel', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('precisa', 'Precisa', ['class'=>'form-label']) }}
			{{ Form::text('precisa', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('idExistente', 'IdExistente', ['class'=>'form-label']) }}
			{{ Form::text('idExistente', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
