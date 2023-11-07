@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('cuotas.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>numeroDeLaCuota</th>
				<th>numeroDecuotas</th>
				<th>valor</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($cuotas as $cuota)

				<tr>
					<td>{{ $cuota->id }}</td>
					<td>{{ $cuota->numeroDeLaCuota }}</td>
					<td>{{ $cuota->numeroDecuotas }}</td>
					<td>{{ $cuota->valor }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('cuotas.show', [$cuota->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('cuotas.edit', [$cuota->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['cuotas.destroy', $cuota->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
