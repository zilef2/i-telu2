@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('posicionusers.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>
				<th>importancia</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($posicionusers as $posicionuser)

				<tr>
					<td>{{ $posicionuser->id }}</td>
					<td>{{ $posicionuser->nombre }}</td>
					<td>{{ $posicionuser->importancia }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('posicionusers.show', [$posicionuser->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('posicionusers.edit', [$posicionuser->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['posicionusers.destroy', $posicionuser->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
