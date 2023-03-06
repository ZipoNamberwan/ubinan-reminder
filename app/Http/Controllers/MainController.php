<?php

namespace App\Http\Controllers;

use App\Exceptions\NoScheduleException;
use App\Imports\MonthlyScheduleImport;
use App\Imports\MonthlyScheduleImportCheck;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class MainController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function harvestSchedule()
    {
        # code...
    }

    public function reminder()
    {
        # code...
    }
    public function manualReminder()
    {
        # code...
    }
    public function showUploadForm()
    {
        return view('admin.upload');
    }
    public function uploadSchedule(Request $request)
    {
        $rules = [
            'file' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Excel::import(new MonthlyScheduleImport, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            dd($failures);

            foreach ($failures as $failure) {
                $failure->row();
                $failure->attribute();
                $failure->errors();
                $failure->values();
            }
        }
    }
    public function settings()
    {
        # code...
    }
    public function checkUpload(Request $request)
    {
        try {
            Excel::import(new MonthlyScheduleImportCheck, $request->file('file'));
        } catch (NoScheduleException $e) {
            return ['is_data_exist' => true];
        }
        return ['is_data_exist' => false];
    }
    public function generateTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // Add some data to the first sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'tahun');
        $sheet->setCellValue('B1', 'sub_round');
        $sheet->setCellValue('C1', 'kode_kec');
        $sheet->setCellValue('D1', 'kode_desa');
        $sheet->setCellValue('E1', 'nbs');
        $sheet->setCellValue('F1', 'nama_sls');
        $sheet->setCellValue('G1', 'nks');
        $sheet->setCellValue('H1', 'no_sample');
        $sheet->setCellValue('I1', 'nama_krt');
        $sheet->setCellValue('J1', 'alamat');
        $sheet->setCellValue('K1', 'komoditas');
        $sheet->setCellValue('L1', 'panen');
        $sheet->setCellValue('M1', 'jenis_sampel');
        $sheet->setCellValue('N1', 'email_petugas');

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Penjelasan Template');
        $sheet->setCellValue('A1', 'Nama Kolom');
        $sheet->setCellValue('B1', 'Variabel');
        $sheet->setCellValue('C1', 'Penjelasan');
        $sheet->setCellValue('D1', 'Nilai yang bisa diisi');
        $sheet->setCellValue('A2', 'tahun');
        $sheet->setCellValue('B2', 'Tahun');
        $sheet->setCellValue('C2', 'Isikan tahun survei ubinan. Contoh: 2023');
        $sheet->setCellValue('D2', '2023' . "\n" . '2024');
        $sheet->setCellValue('A3', 'sub_round');
        $sheet->setCellValue('B3', 'Subround');
        $sheet->setCellValue('C3', 'Isikan subround survei ubinan. Contoh: 1');
        $sheet->setCellValue('D3', '1' . "\n" . '2' . "\n" . '3');
        $sheet->setCellValue('A4', 'kode_kec, kode_desa, dan nbs');
        $sheet->setCellValue('B4', 'Kode Kecamatan, Kode Desa dan Nomor Blok Sensus');
        $sheet->setCellValue('C4', 'Isikan kode kecamatan, kode desa dan nomor blok sensus. Contoh: 010, 009, 001B');
        $sheet->setCellValue('D4', 'Lihat Master Blok Sensus');
        $sheet->setCellValue('A5', 'nama_sls');
        $sheet->setCellValue('B5', 'Nama SLS');
        $sheet->setCellValue('C5', 'Isikan nama SLS. Contoh: RT 01 RW 02');
        $sheet->setCellValue('A6', 'nks');
        $sheet->setCellValue('B6', 'NKS');
        $sheet->setCellValue('C6', 'Isikan NKS. Contoh: 101444');
        $sheet->setCellValue('A7', 'no_sampel');
        $sheet->setCellValue('B7', 'No Sampel');
        $sheet->setCellValue('C7', 'Isikan nomor sampel. Contoh: 1');
        $sheet->setCellValue('A8', 'nama_krt');
        $sheet->setCellValue('B8', 'Nama KRT');
        $sheet->setCellValue('C8', 'Isikan nama KRT sampel. Contoh: BEBUN');
        $sheet->setCellValue('A9', 'alamat');
        $sheet->setCellValue('B9', 'Alamat');
        $sheet->setCellValue('C9', 'Isikan alamat sampel. Contoh: Dusun Krajan');
        $sheet->setCellValue('A10', 'komoditas');
        $sheet->setCellValue('B10', 'Komoditas');
        $sheet->setCellValue('C10', 'Isikan jenis komoditas sampel. Contoh: Jagung');
        $sheet->setCellValue('D10', 'Jagung' . "\n" . 'Kacang Tanah' . "\n" . 'Kedelai' . "\n" . 'Ubi Kayu' . "\n" . 'Ubi Jalar');
        $sheet->setCellValue('A11', 'panen');
        $sheet->setCellValue('B11', 'Bulan Panen');
        $sheet->setCellValue('C11', 'Isikan bulan panen. Contoh: 1');
        $sheet->setCellValue('D11', '1' . "\n" . '2' . "\n" . '3' . "\n" . '4');
        $sheet->setCellValue('A12', 'jenis_sampel');
        $sheet->setCellValue('B12', 'Jenis Sampel');
        $sheet->setCellValue('C12', 'Isikan jenis sampel, utama atau cadangan. Contoh: Utama');
        $sheet->setCellValue('D12', 'Utama' . "\n" . 'Cadangan');
        $sheet->setCellValue('A13', 'email_petugas');
        $sheet->setCellValue('B13', 'Email Petugas');
        $sheet->setCellValue('C13', 'Isikan email petugas untuk sampel. Bisa melihat sheet master petugas. Contoh: dopokan@gmail.com');
        $sheet->setCellValue('D13', 'Lihat Sheet Master Petugas');

        $style = $sheet->getStyle('A1:Z1000');
        $alignment = $style->getAlignment();
        $alignment->setVertical(Alignment::VERTICAL_TOP);

        $sheet->setCellValue('E4', 'Kolom kode_kec sampai panen disalin dari ekspor sampel Aplikasi Ubinan');

        $sheet->mergeCells('E4:E11');
        $style = $sheet->getStyle('E4');
        $alignment = $style->getAlignment();
        $alignment->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setWidth(50);

        $style = $sheet->getStyle('C1:e1000');
        $alignment = $style->getAlignment();
        $alignment->setWrapText(true);


        $style = $sheet->getStyle('A1:Z1');
        $font = $style->getFont();
        $font->setBold(true);
        $font->setSize(14);

        $style = $sheet->getStyle('E4');
        $font = $style->getFont();
        $font->setBold(true);
        $font->setSize(11);

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Master Petugas');
        $i = 1;
        $sheet->setCellValue('A' . $i, 'Nama Petugas');
        $sheet->setCellValue('B' . $i, 'Email');
        $i++;
        foreach (User::all() as $user) {
            $sheet->setCellValue('A' . $i, $user->name);
            $sheet->setCellValue('B' . $i, $user->email);
            $i++;
        }

        // Create a writer object
        $writer = new Xlsx($spreadsheet);

        // Set headers to download the file rather than displaying it
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the generated file to browser
        $writer->save('php://output');
    }
}
