@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('respuestaejercicios.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>core</th>
				<th>precisa</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($respuestaejercicios as $respuestaejercicio)

				<tr>
					<td>{{ $respuestaejercicio->id }}</td>
					<td>{{ $respuestaejercicio->core }}</td>
					<td>{{ $respuestaejercicio->precisa }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('respuestaejercicios.show', [$respuestaejercicio->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('respuestaejercicios.edit', [$respuestaejercicio->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['respuestaejercicios.destroy', $respuestaejercicio->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
