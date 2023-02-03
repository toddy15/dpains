<?php

namespace App\Http\Requests;

use App\Services\Helper;
use Illuminate\Foundation\Http\FormRequest;

class DueShiftRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'year' => [
                'required',
                'numeric',
                'min:'.Helper::$firstYear,
            ],
            'nights' => [
                'required',
                'numeric',
            ],
            'nefs' => [
                'required',
                'numeric',
            ],
        ];
    }
}
