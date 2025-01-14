<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\ProfitTracked;

class RequestProfitRequest extends AbstractRequest
{
    public function rules(): array
    {
        return [
            'rule' => 'required|in:' . implode(',', ProfitTracked::getAllRules()),
            'billions' => 'required|numeric|min:0',
            'range' => [
                'required_if:rule,' . ProfitTracked::RULE_BETWEEN,
                'array',
                function ($attribute, $value, $fail) {
                    if ($this->input('rule') === ProfitTracked::RULE_BETWEEN) {
                        if (count($value) !== 2) {
                            $fail('O campo range deve conter exatamente 2 valores.');
                        } elseif (!is_numeric($value[0]) || !is_numeric($value[1])) {
                            $fail('Ambos os valores em range devem ser numéricos.');
                        } elseif ($value[0] >= $value[1]) {
                            $fail('O primeiro valor do range deve ser menor que o segundo.');
                        }
                    }
                }
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'rule.required' => 'O campo :attribute é obrigatório',
            'rule.in' => 'O campo :attribute é inválido',
            'billions.required' => 'O campo :attribute é obrigatório',
            'billions.numeric' => 'O campo :attribute deve ser um número',
            'billions.min' => 'O campo :attribute deve ser no mínimo 0',
            'range.required_if' => 'O campo :attribute é obrigatório quando a rule for ' . ProfitTracked::RULE_BETWEEN,
        ];
    }
}
