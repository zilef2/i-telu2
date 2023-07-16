<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnidadRequest extends FormRequest
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
                'nombre' => 'required|max:255|min:1',
                'descripcion' => 'nullable|max:255',
                'materia_id' => 'required|integer|min:1',
                'nsubtemas' => 'nullable|max:4|min:0',
                'subtema' => 'nullable',
                'resultAprendizaje' => 'nullable',

            ];
    }
}
