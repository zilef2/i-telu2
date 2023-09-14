@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('archivos.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>
				<th>peso</th>
				<th>nick</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($archivos as $archivo)

				<tr>
					<td>{{ $archivo->id }}</td>
					<td>{{ $archivo->nombre }}</td>
					<td>{{ $archivo->peso }}</td>
					<td>{{ $archivo->nick }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('archivos.show', [$archivo->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('archivos.edit', [$archivo->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['archivos.destroy', $archivo->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
