@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('usuariopendientespagos.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>fecha_peticion</th>
				<th>fecha_aprovacion</th>
				<th>valorTotal</th>
				<th>tokensComprados</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($usuariopendientespagos as $usuariopendientespago)

				<tr>
					<td>{{ $usuariopendientespago->id }}</td>
					<td>{{ $usuariopendientespago->fecha_peticion }}</td>
					<td>{{ $usuariopendientespago->fecha_aprovacion }}</td>
					<td>{{ $usuariopendientespago->valorTotal }}</td>
					<td>{{ $usuariopendientespago->tokensComprados }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('usuariopendientespagos.show', [$usuariopendientespago->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('usuariopendientespagos.edit', [$usuariopendientespago->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['usuariopendientespagos.destroy', $usuariopendientespago->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
