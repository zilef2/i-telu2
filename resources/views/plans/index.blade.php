@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('plans.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nombre</th>
				<th>tipo</th>
				<th>valor</th>
				<th>caducidad</th>
				<th>tokens</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($plans as $plan)

				<tr>
					<td>{{ $plan->id }}</td>
					<td>{{ $plan->nombre }}</td>
					<td>{{ $plan->tipo }}</td>
					<td>{{ $plan->valor }}</td>
					<td>{{ $plan->caducidad }}</td>
					<td>{{ $plan->tokens }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('plans.show', [$plan->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('plans.edit', [$plan->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['plans.destroy', $plan->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
