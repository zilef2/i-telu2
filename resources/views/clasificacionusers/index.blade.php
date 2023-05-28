@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('clasificacionusers.create') }}" class="btn btn-info">Create</a></div>

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
			@foreach($clasificacionusers as $clasificacionuser)

				<tr>
					<td>{{ $clasificacionuser->id }}</td>
					<td>{{ $clasificacionuser->nombre }}</td>
					<td>{{ $clasificacionuser->descripcion }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('clasificacionusers.show', [$clasificacionuser->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('clasificacionusers.edit', [$clasificacionuser->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['clasificacionusers.destroy', $clasificacionuser->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
