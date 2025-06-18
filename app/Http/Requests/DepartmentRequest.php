<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'branch_id' => 'required|integer|exists:branches,id',
            'name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'The branch is required.',
            'name.required' => 'The department name is required'
        ];
    }
}
