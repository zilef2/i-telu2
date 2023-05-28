@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('universidads.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($universidads as $universidad)

				<tr>
					<td>{{ $universidad->id }}</td>
					<td>{{ $universidad->nombre }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('universidads.show', [$universidad->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('universidads.edit', [$universidad->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['universidads.destroy', $universidad->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
