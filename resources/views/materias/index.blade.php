@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('materias.create') }}" class="btn btn-info">Create</a></div>

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
			@foreach($materias as $materium)

				<tr>
					<td>{{ $materium->id }}</td>
					<td>{{ $materium->nombre }}</td>
					<td>{{ $materium->descripcion }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('materias.show', [$materium->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('materias.edit', [$materium->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['materias.destroy', $materium->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
