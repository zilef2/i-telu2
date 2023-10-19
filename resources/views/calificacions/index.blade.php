@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('calificacions.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>TipoPrueba</th>
				<th>prompUsado</th>
				<th>valor</th>
				<th>tokens</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($calificacions as $calificacion)

				<tr>
					<td>{{ $calificacion->id }}</td>
					<td>{{ $calificacion->TipoPrueba }}</td>
					<td>{{ $calificacion->prompUsado }}</td>
					<td>{{ $calificacion->valor }}</td>
					<td>{{ $calificacion->tokens }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('calificacions.show', [$calificacion->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('calificacions.edit', [$calificacion->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['calificacions.destroy', $calificacion->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
