<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticuloRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() {
        return
        [
			'nick'                      => 'required',
            'version'                   => 'nullable',
            'Portada'                   => 'nullable',
			'Resumen'                   => 'nullable',
			'Palabras_Clave'            => 'nullable',
			'Introduccion'              => 'nullable',
			'Revision_de_la_Literatura' => 'nullable',
			'Metodologia'               => 'nullable',
			'Resultados'                => 'nullable',
			'Discusion'                 => 'nullable',
			'Conclusiones'              => 'nullable',
			'Agradecimientos'           => 'nullable',
			'Referencias'               => 'nullable',
			'Anexos_o_Apendices'        => 'nullable',
        ];
    }
}
