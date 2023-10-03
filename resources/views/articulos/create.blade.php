@extends('default')

@section('content')

	@if($errors->any())
		<div class="alert alert-danger">
			@foreach ($errors->all() as $error)
				{{ $error }} <br>
			@endforeach
		</div>
	@endif

	{!! Form::open(['route' => 'articulos.store']) !!}

		<div class="mb-3">
			{{ Form::label('nick', 'Nick', ['class'=>'form-label']) }}
			{{ Form::text('nick', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Portada', 'Portada', ['class'=>'form-label']) }}
			{{ Form::text('Portada', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Resumen', 'Resumen', ['class'=>'form-label']) }}
			{{ Form::text('Resumen', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Palabras_Clave', 'Palabras_Clave', ['class'=>'form-label']) }}
			{{ Form::text('Palabras_Clave', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Introduccion', 'Introduccion', ['class'=>'form-label']) }}
			{{ Form::text('Introduccion', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Revisión_de_la_Literatura', 'Revisión_de_la_Literatura', ['class'=>'form-label']) }}
			{{ Form::text('Revisión_de_la_Literatura', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Metodologia', 'Metodologia', ['class'=>'form-label']) }}
			{{ Form::text('Metodologia', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Resultados', 'Resultados', ['class'=>'form-label']) }}
			{{ Form::text('Resultados', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Discusion', 'Discusion', ['class'=>'form-label']) }}
			{{ Form::text('Discusion', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Conclusiones', 'Conclusiones', ['class'=>'form-label']) }}
			{{ Form::text('Conclusiones', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Agradecimientos', 'Agradecimientos', ['class'=>'form-label']) }}
			{{ Form::text('Agradecimientos', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('Referencias', 'Referencias', ['class'=>'form-label']) }}
			{{ Form::text('Referencias', null, array('class' => 'form-control')) }}
		</div>
		<div class="mb-3">
			{{ Form::label('AnexosoApéndices', 'AnexosoApéndices', ['class'=>'form-label']) }}
			{{ Form::text('AnexosoApéndices', null, array('class' => 'form-control')) }}
		</div>


		{{ Form::submit('Create', array('class' => 'btn btn-primary')) }}

	{{ Form::close() }}


@stop