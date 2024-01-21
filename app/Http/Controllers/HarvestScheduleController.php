<?php

namespace App\Http\Controllers;

use App\Helpers\Utilities;
use App\Imports\MonthlyScheduleImport;
use App\Imports\MonthlyScheduleImportPadi;
use App\Models\Bs;
use App\Models\Commodity;
use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\SampleType;
use App\Models\Subdistrict;
use App\Models\User;
use App\Models\Village;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class HarvestScheduleController extends Controller
{
    public function harvestSchedule()
    {
        $current_month = intval(date("m"));
        $currentsubround = (int) (floor(($current_month - 1) / 4) + 1);

        $subrounds = [1, 2, 3];

        return view('admin.monthly-schedule-monitoring', ['subrounds' => $subrounds, 'currentsubround' => $currentsubround]);
    }
    public function deleteHarvestSchedule($id)
    {
        MonthlySchedule::destroy($id);
        return redirect('/jadwal-panen')->with('success-delete', 'Jadwal Panen Bulanan dihapus!');
    }
    public function createHarvestSchedule()
    {
        $subdistricts = Subdistrict::all();
        $commodities = Commodity::all();
        $users = User::role('PPL')->get();
        $months = Month::all();
        $sampleTypes = SampleType::all();
        $current_month = intval(date("m"));

        return view('admin.add-schedule', ['current_month' => $current_month, 'subdistricts' => $subdistricts, 'subdistricts' => $subdistricts, 'commodities' => $commodities, 'users' => $users, 'months' => $months, 'sampleTypes' => $sampleTypes]);
    }
    public function storeHarvestSchedule(Request $request)
    {
        $this->validate($request, [
            'subdistrict' => 'required',
            'village' => 'required',
            'bs' => 'required',
            'name' => 'required',
            'nks' => 'required',
            'sample_number' => 'required',
            'commodity' => 'required',
            'sample-type' => 'required',
            'month' => 'required',
            'user' => 'required',
            'address' => 'required',
        ]);

        MonthlySchedule::create([
            'bs_id' => $request->bs,
            'name' => $request->name,
            'nks' => $request->nks,
            'sample_number' => $request->sample_number,
            'commodity_id' => $request->commodity,
            'sample_type_id' => $request['sample-type'],
            'month_id' => $request->month,
            'year_id' => Year::firstWhere('name', date("Y"))->id,
            'user_id' => $request->user,
            'address' => $request->address,
        ]);

        return redirect('/jadwal-panen')->with('success-create', 'Jadwal Panen Bulanan telah ditambah!');
    }
    public function editHarvestSchedule($id)
    {
        $schedule = MonthlySchedule::find($id);
        $subdistricts = Subdistrict::all();
        $commodities = Commodity::all();
        $users = User::role('PPL')->get();
        $months = Month::all();
        $sampleTypes = SampleType::all();

        return view('admin.edit-schedule', ['schedule' => $schedule, 'subdistricts' => $subdistricts, 'subdistricts' => $subdistricts, 'commodities' => $commodities, 'users' => $users, 'months' => $months, 'sampleTypes' => $sampleTypes]);
    }
    public function updateHarvestSchedule(Request $request, $id)
    {
        $this->validate($request, [
            'subdistrict' => 'required',
            'village' => 'required',
            'bs' => 'required',
            'name' => 'required',
            'nks' => 'required',
            'sample_number' => 'required',
            'commodity' => 'required',
            'sample-type' => 'required',
            'month' => 'required',
            'user' => 'required',
            'address' => 'required',
        ]);

        $schedule = MonthlySchedule::find($id);
        $schedule->update([
            'bs_id' => $request->bs,
            'name' => $request->name,
            'nks' => $request->nks,
            'sample_number' => $request->sample_number,
            'commodity_id' => $request->commodity,
            'sample_type_id' => $request['sample-type'],
            'month_id' => $request->month,
            'year_id' => Year::firstWhere('name', date("Y"))->id,
            'user_id' => $request->user,
            'address' => $request->address,
        ]);

        return redirect('/jadwal-panen')->with('success-create', 'Jadwal Panen Bulanan telah diubah!');
    }
    public function getScheduleData(Request $request)
    {
        if (Auth::user() == null) {
            abort(403);
        }
        $user = User::find(Auth::user()->id);
        $year = Year::where(['name' => date('Y')])->first()->id;
        $subround = null;

        if ($request->subround != null & ($request->subround == 1 | $request->subround == 2 | $request->subround == 3)) {
            $subround = $request->subround;
        } else {
            $current_month = intval(date("m"));
            $subround = (int) (floor(($current_month - 1) / 4) + 1);
        }

        $max = $subround * 4;
        $min = $max - 3;
        $months = range($min, $max);

        $recordsTotal = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->count();
        if ($user->hasRole('PML')) {
            $recordsTotal = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->whereIn('user_id', $user->getPPLs->pluck('id'))->count();
        } else if ($user->hasRole('PPL')) {
            $recordsTotal = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->where('user_id', $user->id)->count();
        }

        $orderColumn = 'id_bs';
        $orderDir = 'desc';
        if ($request->order != null) {
            if ($request->order[0]['dir'] == 'asc') {
                $orderDir = 'asc';
            } else {
                $orderDir = 'desc';
            }
            if ($request->order[0]['column'] == '1') {
                $orderColumn = 'id_bs';
            } else if ($request->order[0]['column'] == '2') {
                $orderColumn = 'commodity_id';
            } else if ($request->order[0]['column'] == '3') {
                $orderColumn = 'month_id';
            } else if ($request->order[0]['column'] == '4') {
                $orderColumn = 'sampel_type_id';
            } else if ($request->order[0]['column'] == '5') {
                $orderColumn = 'user_id';
            }
        }

        $searchkeyword = $request->search['value'];
        $schedule = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->get();
        if ($user->hasRole('PML')) {
            $schedule = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->whereIn('user_id', $user->getPPLs->pluck('id'))->get();
        } else if ($user->hasRole('PPL')) {
            $schedule = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->where('user_id', $user->id)->get();
        }

        if ($searchkeyword != null) {
            $schedule = $schedule->filter(function ($q) use (
                $searchkeyword
            ) {
                return Str::contains(strtolower($q->bs->subdistrict->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->bs->village->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->bs->name), strtolower($searchkeyword)) ||
                    Str::contains($q->bs->fullcode(), $searchkeyword) ||
                    Str::contains(strtolower($q->user->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->commodity->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->address), strtolower($searchkeyword));
            });
        }
        $recordsFiltered = $schedule->count();

        if ($orderDir == 'asc') {
            $schedule = $schedule->sortBy($orderColumn);
        } else {
            $schedule = $schedule->sortByDesc($orderColumn);
        }

        if ($request->length != -1) {
            $schedule = $schedule->skip($request->start)
                ->take($request->length);
        }

        $scheduleArray = array();
        $i = $request->start + 1;
        foreach ($schedule as $sch) {
            $schData = array();
            $schData["index"] = $i;
            $schData["id"] = $sch->id;
            $schData["user_id"] = $sch->user->id;
            $schData["user_name"] = $sch->user->name;
            $schData["commodity_id"] = $sch->commodity->id;
            $schData["commodity_name"] = $sch->commodity->name;
            $schData["month_id"] = $sch->month->id;
            $schData["month_name"] = $sch->month->name;
            $schData["bs_id"] = $sch->commodity->id != 1 ? $sch->bs->fullcode() : $sch->bs->fullcodesegment() . sprintf('%02d', $sch->segment);
            $schData["bs_name"] = $sch->commodity->id != 1 ? $sch->bs->fullname() : $sch->bs->fullnamesegment();
            $schData["resp_name"] = $sch->commodity->id != 1 ? ucfirst(strtolower($sch->name)) : $sch->subSegment->code;
            $schData["resp_address"] = ucfirst(strtolower($sch->address));
            $schData["nks"] = $sch->commodity->id != 1 ? $sch->nks : $sch->subSegment->code;
            $schData["sample_number"] = $sch->sample_number;
            $schData["sample_type_id"] = $sch->sampleType->id;
            $schData["sample_type_name"] = $sch->sampleType->name;
            $schData["harvest_schedule"] = $sch->harvestSchedule != null ? $sch->harvestSchedule->date : null;
            $schData["harvest_schedule_reminder_num"] = $sch->harvestSchedule != null ? $sch->harvestSchedule->reminder_num : null;
            $schData["monthly_schedule_reminder_num"] = $sch->reminder_num;
            $scheduleArray[] = $schData;
            $i++;
        }
        // dd($scheduleArray);
        return json_encode([
            "draw" => $request->draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $scheduleArray
        ]);
    }
    public function downloadSchedule(Request $request)
    {
        $year = Year::where(['name' => date('Y')])->first()->id;
        $subround = null;

        if ($request->subroundhidden != null & ($request->subroundhidden == 1 | $request->subroundhidden == 2 | $request->subroundhidden == 3)) {
            $subround = $request->subroundhidden;
        } else {
            $current_month = intval(date("m"));
            $subround = (int) (floor(($current_month - 1) / 4) + 1);
        }

        $max = $subround * 4;
        $min = $max - 3;
        $months = range($min, $max);
        $user = User::find(Auth::user()->id);
        $schedules = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->get();
        if ($user->hasRole('PML')) {
            $schedules = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->whereIn('user_id', $user->getPPLs->pluck('id'))->get();
        } else if ($user->hasRole('PPL')) {
            $schedules = MonthlySchedule::where(['year_id' => $year])->whereIn('month_id', $months)->where('user_id', $user->id)->get();
        }

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $startrow = 1;
        $activeWorksheet->setCellValue('A' . $startrow, 'Kode Kecamatan');
        $activeWorksheet->setCellValue('B' . $startrow, 'Kode Desa');
        $activeWorksheet->setCellValue('C' . $startrow, 'Blok Sensus');
        $activeWorksheet->setCellValue('D' . $startrow, 'NKS');
        $activeWorksheet->setCellValue('E' . $startrow, 'Nomor Sampel');
        $activeWorksheet->setCellValue('F' . $startrow, 'Nama KRT');
        $activeWorksheet->setCellValue('G' . $startrow, 'Alamat');
        $activeWorksheet->setCellValue('H' . $startrow, 'Komoditas');
        $activeWorksheet->setCellValue('I' . $startrow, 'Bulan Panen');
        $activeWorksheet->setCellValue('J' . $startrow, 'Jumlah Reminder Bulan Panen Terkirim');
        $activeWorksheet->setCellValue('K' . $startrow, 'Perkiraan Tanggal Panen');
        $activeWorksheet->setCellValue('L' . $startrow, 'Jumlah Reminder Panen Terkirim');
        $activeWorksheet->setCellValue('M' . $startrow, 'Jenis Sampel');
        $activeWorksheet->setCellValue('N' . $startrow, 'Petugas');
        $activeWorksheet->setCellValue('O' . $startrow, 'Email Petugas');
        $startrow++;

        foreach ($schedules as $schedule) {
            $activeWorksheet->setCellValueExplicit('A' . $startrow, $schedule->bs->village->subdistrict->code, DataType::TYPE_STRING);
            $activeWorksheet->setCellValueExplicit('B' . $startrow, $schedule->bs->village->short_code, DataType::TYPE_STRING);
            $activeWorksheet->setCellValue('C' . $startrow, $schedule->bs->short_code);
            $activeWorksheet->setCellValueExplicit('D' . $startrow, $schedule->nks, DataType::TYPE_STRING);
            $activeWorksheet->setCellValue('E' . $startrow, $schedule->sample_number);
            $activeWorksheet->setCellValue('F' . $startrow, $schedule->name);
            $activeWorksheet->setCellValue('G' . $startrow, $schedule->address);
            $activeWorksheet->setCellValue('H' . $startrow, $schedule->commodity->name);
            $activeWorksheet->setCellValue('I' . $startrow, $schedule->month->name);
            $activeWorksheet->setCellValue('J' . $startrow, $schedule->reminder_num);
            $activeWorksheet->setCellValue('K' . $startrow, $schedule->harvestSchedule != null ? $schedule->harvestSchedule->date : null);
            $activeWorksheet->setCellValue('L' . $startrow, $schedule->harvestSchedule != null ? $schedule->harvestSchedule->reminder_num : null);
            $activeWorksheet->setCellValue('M' . $startrow, $schedule->sampleType->name);
            $activeWorksheet->setCellValue('N' . $startrow, $schedule->user->name);
            $activeWorksheet->setCellValue('O' . $startrow, $schedule->user->email);
            $startrow++;
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Subround-' . $subround . '.xlsx"');
        $writer->save('php://output');
    }
    public function checkUpload(Request $request)
    {
        $max = $request->subround * 4;
        $min = $max - 3;
        $months = range($min, $max);
        $year = Year::find($request->year)->id;
        $ms = MonthlySchedule::where('commodity_id', '!=', 1)->where(['year_id' => $year])->whereIn('month_id', $months)->get();

        if (count($ms) > 0) {
            return ['is_data_exist' => true];
        } else {
            return ['is_data_exist' => false];
        }
    }
    public function showUploadFormPalawija()
    {
        $years = Year::all();
        $currentyear = Year::firstWhere('name', date("Y"));

        if ($currentyear == null) {
            $currentyear = Year::all()->last();
        }

        $subrounds = [1, 2, 3];

        return view('admin.upload', ['years' => $years, 'currentyear' => $currentyear, 'subrounds' => $subrounds]);
    }

    public function showUploadFormPadi()
    {
        $years = Year::all();
        $currentyear = Year::firstWhere('name', date("Y"));

        if ($currentyear == null) {
            $currentyear = Year::all()->last();
        }

        $subrounds = [1, 2, 3];

        return view('admin.upload-padi', ['years' => $years, 'currentyear' => $currentyear, 'subrounds' => $subrounds]);
    }
    public function uploadSchedulePalawija(Request $request)
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
            $ms = MonthlySchedule::where('commodity_id', '!=', 1)->where(['year_id' => $year])->whereIn('month_id', $months)->get();

            Excel::import(new MonthlyScheduleImport($request->year, $request->subround), $request->file('file'));

            MonthlySchedule::destroy($ms->pluck('id'));

            return redirect('/upload-palawija')->with('success-create', 'Data Jadwal Ubinan Bulanan telah disimpan!');
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
    public function uploadSchedulePadi(Request $request)
    {
        $rules = [
            'file' => 'required',
            'year' => 'required',
            // 'subround' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Excel::import(new MonthlyScheduleImportPadi($request->year, $request->subround), $request->file('file'));

            return redirect('/upload-padi')->with('success-create', 'Data Jadwal Ubinan Bulanan telah disimpan!');
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
    public function getVillage($id)
    {
        return json_encode(Village::where('subdistrict_id', $id)->get());
    }
    public function getBs($id)
    {
        return json_encode(Bs::where('village_id', $id)->get());
    }
    public function generateTemplatePalawija()
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
        $sheet->setCellValue('L1', 'no_hp');

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
        $sheet->setCellValue('D11', '1' . "\n" . '2' . "\n" . '3' . "\n" . '4' . "\n" . '5' . "\n" . '6' . "\n" . '7' . "\n" . '8' . "\n" . '9' . "\n" . '10' . "\n" . '11' . "\n" . '12');
        $sheet->setCellValue('A12', 'jenis_sampel');
        $sheet->setCellValue('B12', 'Jenis Sampel');
        $sheet->setCellValue('C12', 'Isikan jenis sampel, utama atau cadangan. Contoh: Utama');
        $sheet->setCellValue('D12', 'Utama' . "\n" . 'Cadangan');
        $sheet->setCellValue('A13', 'email_petugas');
        $sheet->setCellValue('B13', 'Email Petugas');
        $sheet->setCellValue('C13', 'Isikan no hape petugas untuk sampel tanpa angka 0 atau +62. Bisa melihat sheet master petugas. Contoh: 812345678910');
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
        $sheet->setCellValue('B' . $i, 'No HP');
        $i++;
        foreach (User::all() as $user) {
            $sheet->setCellValue('A' . $i, $user->name);
            $sheet->setCellValue('B' . $i, "'" . $user->email);
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
        header('Content-Disposition: attachment;filename="template palawija.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the generated file to browser
        $writer->save('php://output');
    }

    public function generateTemplatePadi()
    {
        $spreadsheet = new Spreadsheet();

        // Add some data to the first sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template');
        $sheet->setCellValue('A1', 'kode_kec');
        $sheet->setCellValue('B1', 'kode_desa');
        $sheet->setCellValue('C1', 'no_segmen');
        $sheet->setCellValue('D1', 'subsegmen');
        $sheet->setCellValue('E1', 'komoditas');
        $sheet->setCellValue('F1', 'panen');
        $sheet->setCellValue('G1', 'jenis_sampel');
        $sheet->setCellValue('H1', 'no_hp');

        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Penjelasan Template');
        $sheet->setCellValue('A3', 'Nama Kolom');
        $sheet->setCellValue('B3', 'Variabel');
        $sheet->setCellValue('C3', 'Penjelasan');
        $sheet->setCellValue('D3', 'Nilai yang bisa diisi');
        $sheet->setCellValue('A4', 'kode_kec dan kode_desa');
        $sheet->setCellValue('B4', 'Kode Kecamatan dand Kode Desa');
        $sheet->setCellValue('C4', 'Isikan kode kecamatan dan kode desa. Contoh: 010, 009');
        $sheet->setCellValue('D4', 'Lihat Master Blok Sensus');
        $sheet->setCellValue('A5', 'no_segmen');
        $sheet->setCellValue('B5', 'Nomor Segmen');
        $sheet->setCellValue('C5', 'Isikan nomor segmen dalam dua digit. Contoh: 01');
        $sheet->setCellValue('A6', 'subsegmen');
        $sheet->setCellValue('B6', 'Subsegmen');
        $sheet->setCellValue('C6', 'Isikan subsegmen. Contoh: A1, A2, A3, B1, B2, B3, C1, C2, C3');
        $sheet->setCellValue('C6', 'A1' . "\n" . 'A2' . "\n" . 'A3' . "\n" . 'B1' . "\n" . 'B2' . "\n" . 'B3' . "\n" . 'C1' . "\n" . 'C2' . "\n" . 'C3');
        $sheet->setCellValue('A7', 'komoditas');
        $sheet->setCellValue('B7', 'Komoditas');
        $sheet->setCellValue('C7', 'Isikan jenis komoditas sampel. Contoh: Padi');
        $sheet->setCellValue('D7', 'Padi');
        $sheet->setCellValue('A8', 'panen');
        $sheet->setCellValue('B8', 'Bulan Panen');
        $sheet->setCellValue('C8', 'Isikan bulan panen. Contoh: 1');
        $sheet->setCellValue('D8', '1' . "\n" . '2' . "\n" . '3' . "\n" . '4' . "\n" . '5' . "\n" . '6' . "\n" . '7' . "\n" . '8' . "\n" . '9' . "\n" . '10' . "\n" . '11' . "\n" . '12');
        $sheet->setCellValue('A9', 'jenis_sampel');
        $sheet->setCellValue('B9', 'Jenis Sampel');
        $sheet->setCellValue('C9', 'Isikan jenis sampel, utama atau cadangan. Contoh: Utama');
        $sheet->setCellValue('D9', 'Utama' . "\n" . 'Cadangan');
        $sheet->setCellValue('A10', 'email_petugas');
        $sheet->setCellValue('B10', 'Email Petugas');
        $sheet->setCellValue('C10', 'Isikan no hape petugas untuk sampel tanpa angka 0 atau +62. Bisa melihat sheet master petugas. Contoh: 812345678910');
        $sheet->setCellValue('D10', 'Lihat Sheet Master Petugas');

        $style = $sheet->getStyle('A1:Z1000');
        $alignment = $style->getAlignment();
        $alignment->setVertical(Alignment::VERTICAL_TOP);

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
        $sheet->setCellValue('B' . $i, 'No HP');
        $i++;
        foreach (User::all() as $user) {
            $sheet->setCellValue('A' . $i, $user->name);
            $sheet->setCellValue('B' . $i, "'" . $user->email);
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
        header('Content-Disposition: attachment;filename="template padi.xlsx"');
        header('Cache-Control: max-age=0');

        // Output the generated file to browser
        $writer->save('php://output');
    }

    function dashboard()
    {
        return view('dashboard');
    }
}
