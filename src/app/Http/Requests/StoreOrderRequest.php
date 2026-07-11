<?php

namespace App\Http\Requests;

use App\Models\LayananLaundry;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'layanan_laundry_id' => ['required', 'exists:layanan_laundries,id,status,aktif'],
            'berat' => ['nullable', 'numeric', 'min:0.1'],
            'jumlah_item' => ['nullable', 'integer', 'min:1', 'max:10'],
            'metode_penyerahan' => ['required', 'in:antar_sendiri,jemput'],
            'alamat_penjemputan' => ['nullable', 'required_if:metode_penyerahan,jemput', 'string', 'max:1000'],
            'catatan_pelanggan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $layanan = LayananLaundry::find($this->input('layanan_laundry_id'));
            if (! $layanan) {
                return;
            }

            if ($layanan->tipe_layanan === 'kiloan' && ! $this->filled('berat')) {
                $validator->errors()->add('berat', 'Berat estimasi wajib diisi untuk layanan kiloan.');
            }

            if ($layanan->tipe_layanan === 'satuan' && ! $this->filled('jumlah_item')) {
                $validator->errors()->add('jumlah_item', 'Jumlah item wajib diisi untuk layanan satuan.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'layanan_laundry_id.required' => 'Silakan pilih layanan laundry.',
            'layanan_laundry_id.exists' => 'Layanan yang dipilih tidak valid.',
            'berat.numeric' => 'Berat estimasi harus berupa angka.',
            'berat.min' => 'Berat estimasi minimal 0.1 kg.',
            'jumlah_item.integer' => 'Jumlah item harus berupa angka.',
            'jumlah_item.min' => 'Jumlah item minimal 1.',
            'jumlah_item.max' => 'Jumlah item maksimal 10.',
            'metode_penyerahan.required' => 'Silakan pilih metode penyerahan.',
            'metode_penyerahan.in' => 'Metode penyerahan tidak valid.',
            'alamat_penjemputan.required_if' => 'Alamat penjemputan wajib diisi jika memilih metode jemput.',
            'catatan_pelanggan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
