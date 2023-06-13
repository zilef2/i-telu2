<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Models\materia;
use App\Http\Requests\MateriumRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Objetivo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

use OpenAI;
use OpenAI\Exceptions\ErrorException;

class MateriasController extends Controller {

    public function index(Request $request) {
        if(Auth::user()->isAdmin < 1){
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            // log::channel('soloadmin')->info('Vista:' . $nombreC. '|  U:'.Auth::user()->name );
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||materia|| ' );
        }

        $titulo = __('app.label.materias');
        $permissions = auth()->user()->roles->pluck('name')[0];
        $materias = materia::query();
        
        if($permissions === "operator") {
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            $nombresTabla =[//0: como se ven //1 como es la BD //2??
                ["Acciones","#"],
                [],
                [null,null,null]
            ];
            $nombresTabla[0][] = ["nombre", "observaciones"];
            
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1][] = ["s_nombre", "s_descripcion"]; 
            
            //campos ordenables
            $nombresTabla[2][] = ["s_nombre", "s_descripcion"]; 
        }else{ // not operator
            $titulo = 'materia';
            
            if ($request->has('search')) {
                $materias->where('descripcion','LIKE', "%".$request->search."%");
                // $materias->whereMonth('descripcion', $request->search);
                // $materias->OrwhereMonth('fecha_fin', $request->search);
                $materias->orWhere('nombre', 'LIKE', "%" . $request->search . "%");
            }
            
            if ($request->has(['field', 'order'])) {
                $materias->orderBy($request->field, $request->order);
            }else{
                $materias->orderBy('nombre');
            }
            $perPage = $request->has('perPage') ? $request->perPage : 10;

            //0: como se ven //1 como es la BD //2 orden
            $nombresTabla =[
                ["Acciones","#"],
                [],
                [null,null]
            ];
            $nombresTabla[0] = array_merge($nombresTabla[0] , ["nombre","descripcion","carrera","usuarios"]);
            //m for money || t for datetime || d date || i for integer || s string || b boolean 
            $nombresTabla[1] = array_merge($nombresTabla[1] , ["s_nombre", "s_descripcion","i_carrera_id","s_usuarios"]);
            //campos ordenables
            $nombresTabla[2] = array_merge($nombresTabla[2] , ["nombre", "descripcion","",""]);
        }
        //hijos materias 
            $materias = $materias->get()->map(function ($materia){
                $materia->hijo = $materia->carrera_nombre();
                $materia->muchos = $materia->users_nombres();
                $objetif = $materia->objetivos();
                $materia->objetivs = $objetif->first() != null ? implode(". ",$materia->objetivos()->pluck('nombre')->toArray()) : '';
                return $materia;
            });
            // dd($materias);
            $page = request('page', 1); // Current page number
            $total = $materias->count();
            $paginated = new LengthAwarePaginator(
                $materias->forPage($page, $perPage),
                $total,
                $perPage,
                $page,
                ['path' => request()->url()]
            );
            
            $carrerasSelect = Carrera::all();

        $errorMessage = '';
        // $userMessage = $userMessage === '' ? '' : $userMessage;
        // dd($userMessage);
        return Inertia::render('materia/Index', [ //carpeta
            'title'          =>  $titulo,
            'filters'        =>  $request->all(['search', 'field', 'order']),
            'perPage'        =>  (int) $perPage,
            'fromController' =>  $paginated,
            'breadcrumbs'    =>  [['label' => __('app.label.materias'), 
                                    'href' => route('materia.index')]],
            'nombresTabla'   =>  $nombresTabla,
            'errorMessage' => $errorMessage,
            'carrerasSelect' => $carrerasSelect,
        ]);
    }//fin index



    public function lookForTemas($id) {
        $materia = Materia::find($id);
        $objetivo = $materia->objetivos->first();
        $temas = $materia->temas;
        foreach ($temas as $key => $value) {
            // $temas[$key]->preguntas = 'asd';
            $temas[$key]->mat = $value->materia_nombre();
            $subtopicos = $value->subtopicos;
            foreach ($subtopicos as $subkey => $subtopic) {
                $subtopicos[$subkey]->ejer = $subtopic->ejercicios;
                // dd($subtopic->ejercicios);
            }
            $temas[$key]->sub = $subtopicos;
        }
        // dd($temas);


        // $valoresSelect = [];
        // $valoresSelect[] = [ 'label' => $objetivo, 'value' => 1];
        // $valoresSelect[] = [ 'label' => 'Intermedio', 'value' => 2];
        // $valoresSelect[] = [ 'label' => 'Preparacion', 'value' => 3];

        return [$temas, $objetivo];
    }

    public function VistaTema($id,$temaSelec = "", $subtopicoSelec = "", $ejercicioSelec = "") {
       
        //cuando entra - universidad - carrera
        //materia - tema
        //propuesta (intro)
        // dd( $temaSelec,$subtopicoSelec,$ejercicioSelec);

        if($temaSelec != ''){
            $client = OpenAI::client('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');
            $result = $client->completions()->create([
                'model' => 'text-davinci-003',
                // nivel de dificultadad,  
                'prompt' => 
                // eres un academico con exp en la asignatura X con mas de 20 años, responda: X2 , con un nivel X3. con un nivel (Bachillerato, Universitario o posgrado)
                'eres un academico, apasionado en la asignatura Fisica Clasica con mas de 20 años de experiencia, el tema es: '.$temaSelec.', el sub-tema es: '.$subtopicoSelec.' 
                    responda el siguiente ejercicio: '.$ejercicioSelec.' . con un nivel Universitario',
                // 'Eres un experto en la materia universitaria: Fisica mecanica. se desea saber '.$ejercicioSelec.' para estudiantes. Antes de resolver la pregunta, genera un contexto de almenos 20 palabras.',
                'max_tokens' => 423 // Adjust the response length as needed
            ]);
            // 'Eres un experto en la materia universitaria: '.$pregunta.', se lo mas cordial posible. Propon 2 ejercicios, 1 muy sencillo y otro mas dificil, para estudiantes que desean estudiar para un parcial de la materia '.$pregunta.'. Antes de darles los ejercicios, dales un contexto de almenos 20 palabras.'
            $respuesta = $result['choices'][0]["text"];
        }else{
            $respuesta = '';
        }

        $temasYValores = $this->lookForTemas(intval($id));
        return Inertia::render('materia/vistaTem', [ //carpeta
            'elid'              =>  intval($id),
            'title'             =>  'Seleccione una leccion',
            'perPage'           =>  10,
            'fromController'    =>  $temasYValores[0],
            'respuesta'         =>  $respuesta,
            'valoresSelect'     =>  $temasYValores[1],
            'temaSelec'         => $temaSelec,
            'subtopicoSelec'    => $subtopicoSelec,
            'ejercicioSelec'    => $ejercicioSelec,
            'breadcrumbs'       =>  [['label' => __('app.label.materias'), 
                                    'href' => route('materia.index')]],

        ]);
    }

    public function store(MateriumRequest $request) {
        DB::beginTransaction();
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||materia|| ' );

        try {
            $materia = materia::create([
                'nombre' => $request->nombre,
                //otrosCampos
                'descripcion' => $request->descripcion,
                'carrera_id' =>  $request->carrera_id,
            ]);
            Objetivo::create([
                'nombre' =>  $request->UnObjetivo,
                'descripcion' =>  '',
                'materia_id' =>  $materia->id,
            ]);
            DB::commit();
            Log::info("U -> ".Auth::user()->name." Guardo materia ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.created_successfully2', ['nombre' => $materia->nombre]));

        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en Guardar materia ".$request->nombre." - ".$th->getMessage());

            return back()->with('error', __('app.label.created_error', ['nombre' => __('app.label.materia')]) . $th->getMessage());
        }
    }

    public function show(materia $materia) { }
    public function edit(materia $materia) { }

    public function update(Request $request, $id) {
        $materia = Materia::find($id);
        DB::beginTransaction();
            $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
            log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||materia|| ' );
        try {
            // dd($materia,$request->nombre);
            $materia->update([
                'nombre' => $request->nombre,
                'descripcion' => '',
                'carrera_id' =>  $request->carrera_id,
            ]);

            if($request->otroObjetivo != ''){
                Objetivo::create([
                    'nombre' =>  $request->otroObjetivo,
                    'descripcion' =>  '',
                    'materia_id' =>  $materia->id,
                ]);
            }
            DB::commit();
            Log::info("U -> ".Auth::user()->name." actualizo materia ".$request->nombre." correctamente");

            return back()->with('success', __('app.label.updated_successfully2', ['nombre' => $materia->nombre]));
        } catch (\Throwable $th) {
            
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en actualizar materia ".$request->nombre." - ".$th->getMessage());
            return back()->with('error', __('app.label.updated_error', ['nombre' => $materia->nombre]) . $th->getMessage());
        }
    }

    // public function destroy(materia $materia)
    public function destroy($id) {
        $ListaControladoresYnombreClase = (explode('\\',get_class($this))); $nombreC = end($ListaControladoresYnombreClase);
        log::info('Vista: ' . $nombreC. 'U:'.Auth::user()->name. ' ||materia|| ' );

        DB::beginTransaction();

        try {
            $materias = materia::findOrFail($id);
            Log::info($nombreC." U -> ".Auth::user()->name."La materia id:".$id." y nombre:".$materias->nombre." ha sido borrada correctamente");
            $materias->delete();
            DB::commit();
            return back()->with('success', __('app.label.deleted_successfully2',['nombre' => $materias->nombre]));
            
        } catch (\Throwable $th) {
            DB::rollback();
            Log::alert("U -> ".Auth::user()->name." fallo en borrar materia ".$id." - ".$th->getMessage());
            return back()->with('error', __('app.label.deleted_error', ['name' => __('app.label.materias')]) . $th->getMessage());
        }
    }
}
