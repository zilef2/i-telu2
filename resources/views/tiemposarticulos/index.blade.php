@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('tiemposarticulos.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>startTime</th>
				<th>endTime</th>
				<th>tiempoEscritura</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($tiemposarticulos as $tiemposarticulo)

				<tr>
					<td>{{ $tiemposarticulo->id }}</td>
					<td>{{ $tiemposarticulo->startTime }}</td>
					<td>{{ $tiemposarticulo->endTime }}</td>
					<td>{{ $tiemposarticulo->tiempoEscritura }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('tiemposarticulos.show', [$tiemposarticulo->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('tiemposarticulos.edit', [$tiemposarticulo->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['tiemposarticulos.destroy', $tiemposarticulo->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
