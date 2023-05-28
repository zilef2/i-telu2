@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('subtopicos.create') }}" class="btn btn-info">Create</a></div>

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
			@foreach($subtopicos as $subtopico)

				<tr>
					<td>{{ $subtopico->id }}</td>
					<td>{{ $subtopico->nombre }}</td>
					<td>{{ $subtopico->descripcion }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('subtopicos.show', [$subtopico->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('subtopicos.edit', [$subtopico->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['subtopicos.destroy', $subtopico->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
