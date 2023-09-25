@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('respuestapdfs.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>guardar_pdf</th>
				<th>resumen</th>
				<th>nivel</th>
				<th>precisa</th>
				<th>idExistente</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($respuestapdfs as $respuestapdf)

				<tr>
					<td>{{ $respuestapdf->id }}</td>
					<td>{{ $respuestapdf->guardar_pdf }}</td>
					<td>{{ $respuestapdf->resumen }}</td>
					<td>{{ $respuestapdf->nivel }}</td>
					<td>{{ $respuestapdf->precisa }}</td>
					<td>{{ $respuestapdf->idExistente }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('respuestapdfs.show', [$respuestapdf->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('respuestapdfs.edit', [$respuestapdf->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['respuestapdfs.destroy', $respuestapdf->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
