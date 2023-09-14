@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{{ Form::model($archivo, array('route' => array('archivos.update', $archivo->id), 'method' => 'PUT')) }}

		<div class="mb-3">
			{{ Form::label('nombre', 'Nombre', ['class'=>'form-label']) }}
			{{ Form::text('nombre', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('peso', 'Peso', ['class'=>'form-label']) }}
			{{ Form::text('peso', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('nick', 'Nick', ['class'=>'form-label']) }}
			{{ Form::text('nick', null, array('class' => 'form-control')) }}
		</div>

		{{ Form::submit('Edit', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}
@stop
