<?php

namespace App\Http\Requests\Api;

use App\Helpers\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProcessTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rfid_uid'   => ['required', 'string', 'max:50'],
            'machine_id' => ['required', 'integer', 'exists:machines,id'],
            // jumlah_ambil dikirim dalam KG dari Raspberry Pi
            'jumlah_ambil' => ['required', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'rfid_uid.required'     => 'RFID UID wajib diisi',
            'machine_id.required'   => 'ID mesin wajib diisi',
            'machine_id.exists'     => 'Mesin tidak ditemukan',
            'jumlah_ambil.required' => 'Jumlah pengambilan wajib diisi',
            'jumlah_ambil.integer'  => 'Jumlah pengambilan harus berupa angka',
            'jumlah_ambil.min'      => 'Jumlah pengambilan minimal 1 kg',
            'jumlah_ambil.max'      => 'Jumlah pengambilan maksimal 50 kg',
        ];
    }

    /**
     * Override default behavior agar validasi gagal mengembalikan JSON.
     */
    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(
            ApiResponse::validationError($validator->errors())
        );
    }
}
