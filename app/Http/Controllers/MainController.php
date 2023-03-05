<?php

namespace App\Http\Controllers;

use App\Imports\MonthlyScheduleImport;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_IOFactory;

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
    public function generateTemplate()
    {
        
    }
}
