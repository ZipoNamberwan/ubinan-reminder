<?php

namespace App\Imports;

use App\Models\Bs;
use App\Models\Commodity;
use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\SampleType;
use App\Models\SubSegment;
use App\Models\User;
use App\Models\Year;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithValidation;

class MonthlyScheduleImportPadi implements ToModel, WithHeadingRow, SkipsEmptyRows, WithBatchInserts, WithValidation, WithMultipleSheets
{

    public $year;
    public $subround;

    public function __construct($year, $subround)
    {
        $this->year = $year;
        $this->subround = $subround;
    }
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new MonthlySchedule([
            'user_id' => User::where(['email' => $row['no_hp']])->first()->id,
            'month_id' => Month::find($row['panen'])->id,
            'year_id' => Year::find($this->year)->id,
            'commodity_id' => 1,
            'bs_id' => Bs::where('long_code', 'like', ('%' . $row['kode_kec'] . $row['kode_desa'] . '001%'))->first()->id,
            'address' => '',
            'name' => '',
            'nks' => 1,
            'sample_number' => 1,
            'segment' => $row['no_segmen'],
            'subsegment_id' => SubSegment::where(['code' => $row['subsegmen']])->first()->id,
            'sample_type_id' => SampleType::where(['name' => ucfirst(strtolower($row['jenis_sampel']))])->first()->id,
        ]);
    }

    public function headingRow(): int
    {
        return 1;
    }

    public function rules(): array
    {
        return [
            '*.kode_kec' => 'required',
            '*.kode_desa' => 'required',
            // '*.nbs' => 'required',
            // '*.nama_sls' => 'required',
            // '*.nks' => 'required',
            // '*.no_sample' => 'required',
            // '*.nama_krt' => 'required',
            // '*.alamat' => 'required',
            // '*.komoditas' => ['required', Rule::in(Commodity::where('id', '!=', 1)->get()->pluck(['name'])),],
            '*.panen' => ['required', Rule::in([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]),],
            '*.jenis_sampel' => ['required', Rule::in(['Utama', 'Cadangan']),],
            '*.no_segmen' => ['required', 'min:1', 'max:99'],
            '*.subsegmen' => ['required', Rule::in(SubSegment::all()->pluck('code')),],
            '*.no_hp' => ['required', Rule::in(User::all()->pluck('email'))],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'kode_kec.required' => ':attribute kosong',
            'kode_desa.required' => ':attribute kosong',
            'nbs.required' => ':attribute kosong',
            'nama_sls.required' => ':attribute kosong',
            'nks.required' => ':attribute kosong',
            'no_sample.required' => ':attribute kosong',
            'nama_krt.required' => ':attribute kosong',
            'alamat.required' => ':attribute kosong',
            'komoditas.required' => ':attribute kosong',
            'komoditas.in' => ':attribute tidak ada dalam master',
            'panen.required' => ':attribute kosong',
            'jenis_sampel.required' => ':attribute kosong',
            'jenis_sampel.in' => ':attribute tidak ada dalam master',
            'no_hp.required' => ':attribute kosong',
            'no_hp.in' => ':attribute tidak ada dalam master',
        ];
    }

    public function customValidationAttributes()
    {
        return [
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
            'no_hp' => 'No HP Petugas',
            'no_segmen' => 'Nomor Segmen',
            'subsegmen' => 'Sub Segmen'
        ];
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function sheets(): array
    {
        return [
            'Template' => $this,
        ];
    }
}
