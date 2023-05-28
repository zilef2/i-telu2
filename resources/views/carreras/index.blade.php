@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('carreras.create') }}" class="btn btn-info">Create</a></div>

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
			@foreach($carreras as $carrera)

				<tr>
					<td>{{ $carrera->id }}</td>
					<td>{{ $carrera->nombre }}</td>
					<td>{{ $carrera->descripcion }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('carreras.show', [$carrera->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('carreras.edit', [$carrera->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['carreras.destroy', $carrera->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
