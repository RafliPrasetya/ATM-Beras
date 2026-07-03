<?php

namespace App\Http\Requests\Api;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidateRfidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rfid_uid' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'rfid_uid.required' => 'RFID UID wajib diisi',
            'rfid_uid.string'   => 'RFID UID harus berupa string',
            'rfid_uid.max'      => 'RFID UID maksimal 50 karakter',
        ];
    }

    /**
     * Override default behavior agar validasi gagal mengembalikan JSON
     * bukan redirect (penting untuk API yang dikonsumsi Raspberry Pi).
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors())
        );
    }
}
