@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('grupos.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>
				<th>codigo</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($grupos as $grupo)

				<tr>
					<td>{{ $grupo->id }}</td>
					<td>{{ $grupo->nombre }}</td>
					<td>{{ $grupo->codigo }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('grupos.show', [$grupo->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('grupos.edit', [$grupo->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['grupos.destroy', $grupo->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
