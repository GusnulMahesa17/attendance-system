<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuruRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $guruId = $this->route('guru') ? $this->route('guru')->id : null;

        return [
            'id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email,' . $guruId,
            'nip' => 'required|unique:users,nip,' . $guruId,
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|min:6'
        ];
    }
}
