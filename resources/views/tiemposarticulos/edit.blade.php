@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($tiemposarticulo, array('route' => array('tiemposarticulos.update', $tiemposarticulo->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('startTime', 'StartTime', ['class'=>'form-label']) }}
			{{ Form::string('startTime', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('endTime', 'EndTime', ['class'=>'form-label']) }}
			{{ Form::string('endTime', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('tiempoEscritura', 'TiempoEscritura', ['class'=>'form-label']) }}
			{{ Form::string('tiempoEscritura', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
