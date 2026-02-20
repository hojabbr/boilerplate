<?php

namespace App\Domains\Contact\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:65535'],
        ];
    }

    /**
     * Validated data shaped for StoreContactSubmission.
     *
     * @return array{name: string, email: string, subject?: string, message: string}
     */
    public function validatedData(): array
    {
        $v = $this->validated();
        $data = [
            'name' => (string) $v['name'],
            'email' => (string) $v['email'],
            'message' => (string) $v['message'],
        ];
        if (isset($v['subject']) && (string) $v['subject'] !== '') {
            $data['subject'] = (string) $v['subject'];
        }

        return $data;
    }
}
