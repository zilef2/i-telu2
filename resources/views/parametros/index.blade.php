@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('parametros.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>prompEjercicios</th>
				<th>NumeroTicketDefecto</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($parametros as $parametro)

				<tr>
					<td>{{ $parametro->id }}</td>
					<td>{{ $parametro->prompEjercicios }}</td>
					<td>{{ $parametro->NumeroTicketDefecto }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('parametros.show', [$parametro->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('parametros.edit', [$parametro->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['parametros.destroy', $parametro->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
