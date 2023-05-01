<!-- redirect --> este es de livewire
    session()->flash('message', ' correctamente.'); return redirect()->to('/dashboard');

// NOTIFIACIONES Y DETALLES

    <!-- el try con message -->
        try {
            session()->flash('message', ' correctamente.');
        } catch (\Throwable $th) {
            session()->flash('message', substr($th,3,200));
            session()->flash('message', 'Error al .');
        }



// LOG
    $ListaControladoresYnombreClase = (explode('\\',get_class($this)));
    $nombreC = end($ListaControladoresYnombreClase);
    Log::critical(' U -> '.Auth::user()->name. ' Accedio a la vista' .$nombreC );

    Log::info -> es como el succes
    log::alert -> el usuario no sabe
    Log::warning -> no deberia pasar, no es grave
    Log::critical -> no deberia pasar, es gravisimo (por lo general cuando falla un try-catch)

<!-- MESSAGE -->
    @if(session()->has('message'))
        <div class="text-center bg-green-100 rounded-lg py-5 px-6 text-base text-green-700 mb-3" role="alert">
            {{ session('message') }}
        </div>
    @endif

<!-- validator -->
    Validator::make($row, [
    ]);


    $validator = Validator::make($row, [
            '*.1' => 'date',
    ])->validate();
    // dd($validator->getData());


<!-- select --> este no es el de vue
    <div class="p-2 w-1/2">
        <div class="relative">
            <label for="empresaid" class="block text-sm font-medium text-gray-700 capitalize">empresa</label>

            <select class="form-select form-select-md mt-2
                    appearance-none block w-full px-4 py-2
                    text-lg font-normal text-gray-700 bg-white bg-clip-padding bg-no-repeat border border-solid border-gray-300 rounded transition ease-in-out m-0
                    focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" aria-label=".form-select-lg example" wire:model="empresaid" >
                @forelse($empresas as $generico)
                    <option class="capitalize" value="" selected>Seleccione empresa</option>
                    <option class="capitalize" value="{{$generico->id}}">{{$generico->nombre}}</option>
                @empty
                    <option class="capitalize" value="" selected>No hay {{ $laClase }}s registradas</option>
                @endforelse
            </select>
        </div>
    </div>


