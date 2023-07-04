@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('medidacontrols.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>tokens_usados</th>
				<th>user_id</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($medidacontrols as $medidacontrol)

				<tr>
					<td>{{ $medidacontrol->id }}</td>
					<td>{{ $medidacontrol->tokens_usados }}</td>
					<td>{{ $medidacontrol->user_id }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('medidacontrols.show', [$medidacontrol->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('medidacontrols.edit', [$medidacontrol->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['medidacontrols.destroy', $medidacontrol->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
