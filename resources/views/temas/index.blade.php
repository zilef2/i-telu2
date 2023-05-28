@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('temas.create') }}" class="btn btn-info">Create</a></div>

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
			@foreach($temas as $tema)

				<tr>
					<td>{{ $tema->id }}</td>
					<td>{{ $tema->nombre }}</td>
					<td>{{ $tema->descripcion }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('temas.show', [$tema->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('temas.edit', [$tema->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['temas.destroy', $tema->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
