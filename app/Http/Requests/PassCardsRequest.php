<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PassCardsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'cards' => ['required', 'array', 'size:3'],
            'cards.*' => ['required', 'string', 'distinct', 'regex:/^[2-9TJQKA][CDSH]$/'],
        ];
    }

    /**
     * @return list<string>
     */
    public function cardCodes(): array
    {
        /** @var list<string> $cards */
        $cards = array_values($this->validated('cards'));

        return $cards;
    }
}
