<?php

namespace App\Http\Controllers;

use App\Exceptions\NoScheduleException;
use App\Helpers\Utilities;
use App\Imports\MonthlyScheduleImport;
use App\Imports\MonthlyScheduleImportCheck;
use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\User;
use App\Models\Year;
use Exception;
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
        $current_month = intval(date("m"));
        $currentsubround = (int) (floor($current_month / 4) + 1);

        $subrounds = [1, 2, 3];

        return view('admin.monthly-schedule-monitoring', ['subrounds' => $subrounds, 'currentsubround' => $currentsubround]);
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
        $years = Year::all();
        $currentyear = Year::firstWhere('name', date("Y"));

        if ($currentyear == null) {
            $currentyear = Year::all()->last();
        }

        $subrounds = [1, 2, 3];

        return view('admin.upload', ['years' => $years, 'currentyear' => $currentyear, 'subrounds' => $subrounds]);
    }
    public function uploadSchedule(Request $request)
    {
        $rules = [
            'file' => 'required',
            'year' => 'required',
            'subround' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $max = $request->subround * 4;
            $min = $max - 3;
            $months = range($min, $max);
            $year = Year::find($request->year)->id;
            $ms = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->get();

            Excel::import(new MonthlyScheduleImport($request->year, $request->subround), $request->file('file'));

            MonthlySchedule::destroy($ms->pluck('id'));

            return redirect('/upload')->with('success-create', 'Data Jadwal Ubinan Bulanan telah disimpan!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $formattedFailures = [];
            $failures = $e->failures();
            foreach ($failures as $failure) {
                $failure->row();
                $failure->attribute();
                $failure->errors();
                $failure->values();
                if (!array_key_exists($failure->row(), $formattedFailures)) {
                    $row = [$failure->attribute()];
                    $formattedFailures[$failure->row()] = $row;
                } else {
                    $formattedFailures[$failure->row()][] = $failure->attribute();
                }
            }

            ksort($formattedFailures);

            $failures = [];
            foreach ($formattedFailures as $key => $value) {
                $failures[$key] = Utilities::getSentenceFromArray($value);
            }

            return view('admin.failed-upload', ['failures' => $failures]);
        }
    }
    public function settings()
    {
        # code...
    }
    public function checkUpload(Request $request)
    {
        $max = $request->subround * 4;
        $min = $max - 3;
        $months = range($min, $max);
        $year = Year::find($request->year)->id;
        $ms = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->get();
        if (count($ms) > 0) {
            return ['is_data_exist' => true];
        } else {
            return ['is_data_exist' => false];
        }
    }
    public function generateTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // Add some data to the first sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');
        $sheet->setCellValue('A1', 'kode_kec');
        $sheet->setCellValue('B1', 'kode_desa');
        $sheet->setCellValue('C1', 'nbs');
        $sheet->setCellValue('D1', 'nama_sls');
        $sheet->setCellValue('E1', 'nks');
        $sheet->setCellValue('F1', 'no_sample');
        $sheet->setCellValue('G1', 'nama_krt');
        $sheet->setCellValue('H1', 'alamat');
        $sheet->setCellValue('I1', 'komoditas');
        $sheet->setCellValue('J1', 'panen');
        $sheet->setCellValue('K1', 'jenis_sampel');
        $sheet->setCellValue('L1', 'email_petugas');

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Penjelasan Template');
        $sheet->setCellValue('A3', 'Nama Kolom');
        $sheet->setCellValue('B3', 'Variabel');
        $sheet->setCellValue('C3', 'Penjelasan');
        $sheet->setCellValue('D3', 'Nilai yang bisa diisi');
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


        $style = $sheet->getStyle('A3:Z3');
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

        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $style = $sheet->getStyle('A1:Z1');
        $font = $style->getFont();
        $font->setBold(true);
        $font->setSize(11);

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
