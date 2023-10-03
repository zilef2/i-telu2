@extends('default')

@section('content')

	<div class="d-flex justify-content-end mb-3"><a href="{{ route('articulos.create') }}" class="btn btn-info">Create</a></div>

	<table class="table table-bordered">
		<thead>
			<tr>
				<th>id</th>
				<th>nick</th>
				<th>Portada</th>
				<th>Resumen</th>
				<th>Palabras_Clave</th>
				<th>Introduccion</th>
				<th>Revisión_de_la_Literatura</th>
				<th>Metodologia</th>
				<th>Resultados</th>
				<th>Discusion</th>
				<th>Conclusiones</th>
				<th>Agradecimientos</th>
				<th>Referencias</th>
				<th>AnexosoApéndices</th>

				<th>Action</th>
			</tr>
		</thead>
		<tbody>
			@foreach($articulos as $articulo)

				<tr>
					<td>{{ $articulo->id }}</td>
					<td>{{ $articulo->nick }}</td>
					<td>{{ $articulo->Portada }}</td>
					<td>{{ $articulo->Resumen }}</td>
					<td>{{ $articulo->Palabras_Clave }}</td>
					<td>{{ $articulo->Introduccion }}</td>
					<td>{{ $articulo->Revisión_de_la_Literatura }}</td>
					<td>{{ $articulo->Metodologia }}</td>
					<td>{{ $articulo->Resultados }}</td>
					<td>{{ $articulo->Discusion }}</td>
					<td>{{ $articulo->Conclusiones }}</td>
					<td>{{ $articulo->Agradecimientos }}</td>
					<td>{{ $articulo->Referencias }}</td>
					<td>{{ $articulo->AnexosoApéndices }}</td>

					<td>
						<div class="d-flex gap-2">
                            <a href="{{ route('articulos.show', [$articulo->id]) }}" class="btn btn-info">Show</a>
                            <a href="{{ route('articulos.edit', [$articulo->id]) }}" class="btn btn-primary">Edit</a>
                            {!! Form::open(['method' => 'DELETE','route' => ['articulos.destroy', $articulo->id]]) !!}
                                {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                            {!! Form::close() !!}
                        </div>
					</td>
				</tr>

			@endforeach
		</tbody>
	</table>

@stop
