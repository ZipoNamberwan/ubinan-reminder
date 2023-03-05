<?php

namespace App\Imports;

use App\Models\Bs;
use App\Models\Commodity;
use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\SampleType;
use App\Models\User;
use App\Models\Year;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MonthlyScheduleImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new MonthlySchedule([
            'user_id' => User::where(['email' => $row['email_petugas']])->first()->id,
            'month_id' => Month::find(($row['sub_round'] - 1) * 4 + $row['panen'])->id,
            'year_id' => Year::where(['name' => $row['tahun']])->first()->id,
            'commodity_id' => Commodity::where(['name' => $row['komoditas']])->first()->id,
            'bs_id' => Bs::where(['code' => ($row['kode_kec'] . $row['kode_desa'] . $row['nbs'])])->first()->id,
            'address' => $row['alamat'],
            'name' => $row['nama_krt'],
            'sample_type_id' => SampleType::where(['name' => $row['jenis_sampel']])->first()->id,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            '*.tahun' => 'required|numeric',
            '*.sub_round' => 'required|numeric',
            '*.kode_kec' => 'required',
            '*.kode_desa' => 'required',
            '*.nbs' => 'required',
            '*.nama_sls' => 'required',
            '*.nks' => 'required',
            '*.no_sample' => 'required',
            '*.nama_krt' => 'required',
            '*.alamat' => 'required',
            '*.komoditas' => ['required', Rule::in(Commodity::all()->pluck(['name'])),],
            '*.panen' => ['required', Rule::in([1, 2, 3, 4]),],
            '*.jenis_sampel' => ['required', Rule::in(['Utama', 'Cadangan']),],
            '*.email_petugas' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'tahun.required' => ':attribute kosong',
            'sub_round.required' => ':attribute kosong',
            'kode_kec.required' => ':attribute kosong',
            'kode_desa.required' => ':attribute kosong',
            'nbs.required' => ':attribute kosong',
            'nama_sls.required' => ':attribute kosong',
            'nks.required' => ':attribute kosong',
            'no_sample.required' => ':attribute kosong',
            'nama_krt.required' => ':attribute kosong',
            'alamat.required' => ':attribute kosong',
            'komoditas.required' => ':attribute kosong',
            'panen.required' => ':attribute kosong',
            'jenis_sampel.required' => ':attribute kosong',
            'email_petugas.required' => ':attribute kosong',
        ];
    }

    public function customValidationAttributes()
    {
        return [
            'tahun' => 'Tahun',
            'sub_round' => 'Sub Round',
            'kode_kec' => 'Kode Kecamatan',
            'kode_desa' => 'Kode Desa',
            'nbs' => 'Kode Blok Sensus',
            'nama_sls' => 'Nama SLS',
            'nks' => 'NKS',
            'no_sample' => 'No Sampel',
            'nama_krt' => 'Nama KRT',
            'alamat' => 'Alamat',
            'komoditas' => 'Jenis Komoditas',
            'panen' => 'Perkiraan Bulan Panen',
            'jenis_sampel' => 'Jenis Sampel',
            'email_petugas' => 'Email Petugas',
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }
}
