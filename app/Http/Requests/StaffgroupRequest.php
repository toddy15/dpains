<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffgroupRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'staffgroup' => [
                'required',
            ],
            'weight' => [
                'required',
                'numeric',
            ],
        ];
    }
}
