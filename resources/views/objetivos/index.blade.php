@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('objetivos.create') }}" class="btn btn-info">Create</a></div>

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
			@foreach($objetivos as $objetivo)

				<tr>
					<td>{{ $objetivo->id }}</td>
					<td>{{ $objetivo->nombre }}</td>
					<td>{{ $objetivo->descripcion }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('objetivos.show', [$objetivo->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('objetivos.edit', [$objetivo->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['objetivos.destroy', $objetivo->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
