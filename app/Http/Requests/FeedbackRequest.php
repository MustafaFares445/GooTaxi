<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'fullName' => ['required'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['required'],
            'message' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
