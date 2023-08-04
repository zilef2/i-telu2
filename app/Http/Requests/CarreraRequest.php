<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarreraRequest extends FormRequest
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
        $id = $this->route('carrera');

        return
        [
			'nombre' => 'required',
            'codigo' => 'required|string',
            // 'codigo' => 'required|string|unique:carreras',
            Rule::unique('carreras','codigo')->ignore($id)


			// 'descripcion' => 'required',
        ];
    }
}

