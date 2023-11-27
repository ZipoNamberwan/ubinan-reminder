<?php

namespace App\Http\Controllers;

use App\Helpers\Utilities;
use App\Imports\MonthlyScheduleImport;
use App\Models\MonthlySchedule;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class MainController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }
    public function index()
    {
        $user = User::find(Auth::user()->id);
        if ($user->hasRole('Admin') | $user->hasRole('PML')) {
            return redirect('/jadwal-panen');
        } else {
            return redirect('/jadwal-ubinan');
        }
    }
    public function manualReminder()
    {
        # code...
    }
    public function settings()
    {
        # code...
    }
}
