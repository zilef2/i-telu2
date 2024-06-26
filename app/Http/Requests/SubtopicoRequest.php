<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubtopicoRequest extends FormRequest
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
        return [
			'enum' => 'nullable',
			'nombre' => 'required',
			'codigo' => 'nullable', //todo: unique

			'unidad_id' => 'required|min:1',
			'descripcion' => 'nullable',
			'resultado_aprendizaje' => 'required',
        ];
    }
}
