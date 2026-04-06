<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class NewsRelevanceChartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'date_format:Y-m-d'],
            'to'   => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.required'      => 'A data inicial é obrigatória.',
            'from.date_format'   => 'A data inicial deve estar no formato YYYY-MM-DD.',
            'to.required'        => 'A data final é obrigatória.',
            'to.date_format'     => 'A data final deve estar no formato YYYY-MM-DD.',
            'to.after_or_equal'  => 'A data final deve ser igual ou posterior à data inicial.',
        ];
    }
}
