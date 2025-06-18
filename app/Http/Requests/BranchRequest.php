<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchRequest extends FormRequest
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
            'branch_code' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'branch_short_name' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'branch_code.required' => 'The branch code is required.',
            'branch_name.required' => 'The branch name is required.',
            'branch_short_name.required' => 'The branch short name is required.',
        ];
    }
}
