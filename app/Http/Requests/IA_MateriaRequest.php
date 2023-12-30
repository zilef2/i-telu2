<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IA_MateriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return
        [
			// 'UnObjetivo' => 'nullable',

            'nombre_mat' => 'required',
            'carrera_id' => 'required',
            // 'codigo_mat' => 'required',
//            'objetivo' => 'required',
            'nombre_unidad' => 'required',
            'Array_nombre_tema' => 'required',
            'Array_RA' => 'required',

            'Cuantas_u' => 'required|min:1',
            'Cuantas_t' => 'required|min:1',
        ];
    }
}
