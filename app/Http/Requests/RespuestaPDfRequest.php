<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RespuestaPDfRequest extends FormRequest
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
			'guardar_pdf' => 'required',
			'resumen' => 'required',
			'nivel' => 'required',
			'precisa' => 'required',
			'idExistente' => 'required',
        ];
    }
}
