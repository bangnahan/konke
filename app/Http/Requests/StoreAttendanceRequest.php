<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'institution' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'attendance_status' => ['required', 'in:hadir,tidak_hadir,hadir_terlambat'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'full_name' => 'Nama Lengkap',
            'email' => 'Alamat Email',
            'phone_number' => 'Nomor WhatsApp',
            'institution' => 'Instansi (Wajihah)',
            'position' => 'Jabatan (Amanah)',
            'attendance_status' => 'Konfirmasi Kehadiran',
            'notes' => 'Catatan Tambahan',
        ];
    }
}
