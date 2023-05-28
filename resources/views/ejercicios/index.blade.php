@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('ejercicios.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>
				<th>descripcion</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($ejercicios as $ejercicio)

				<tr>
					<td>{{ $ejercicio->id }}</td>
					<td>{{ $ejercicio->nombre }}</td>
					<td>{{ $ejercicio->descripcion }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('ejercicios.show', [$ejercicio->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('ejercicios.edit', [$ejercicio->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['ejercicios.destroy', $ejercicio->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
