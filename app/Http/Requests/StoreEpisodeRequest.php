<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEpisodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
            ],
            'month' => [
                'required',
            ],
            'year' => [
                'required',
            ],
            'vk' => [
                'required',
                'numeric',
                'between:0,1',
            ],
            'factor_night' => [
                'required',
                'numeric',
                'between:0,2',
            ],
            'factor_nef' => [
                'required',
                'numeric',
                'between:0,2',
            ],
        ];
    }
}
