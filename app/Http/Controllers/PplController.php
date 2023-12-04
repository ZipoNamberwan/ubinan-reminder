<?php

namespace App\Http\Controllers;

use App\Models\Commodity;
use App\Models\HarvestSchedule;
use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\User;
use App\Models\Year;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PplController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = User::find(Auth::user()->id);
        $year = Year::where(['name' => date('Y')])->first()->id;
        $months = Month::all();
        foreach ($months as $month) {
            $sample_num = count(MonthlySchedule::where('user_id', $user->id)->where('month_id', $month->id)->where('year_id', $year)->get());
            $month->sample_num = $sample_num;
        }
        $currentmonth = Month::find(intval(date("m")));
        if ($request->month != null) {
            $currentmonth = Month::find($request->month);
            if ($currentmonth == null)
                return 'Failed parameter, redirect <a href="' . url('/') . '">' . url('/') . '</a>';
        }

        $schedules = MonthlySchedule::where('user_id', $user->id)->where('month_id', $currentmonth->id)->where('year_id', $year)->get();
        $total_sample = count($schedules);
        $total_sample_not_entry = count($schedules->filter(function ($q) {
            return $q->harvestSchedule == null;
        }));
        $sample_types = $schedules->pluck('commodity_id')->unique();
        $sample_types_name = [];
        foreach ($sample_types as $type) {
            $sample_types_name[] = Commodity::find($type)->name;
        }
        $sample_types_string = count($sample_types_name) > 0 ? implode(", ", $sample_types_name) : '-';

        if ($request->notentry == true) {
            $schedules = $schedules->filter(function ($q) {
                return $q->harvestSchedule == null;
            });
        }

        return view('ppl/schedule', [
            'months' => $months,
            'currentmonth' => $currentmonth,
            'schedules' => $schedules,
            'total_sample' => $total_sample,
            'total_sample_not_entry' => $total_sample_not_entry,
            'sample_types_string' => $sample_types_string
        ]);
    }

    function entryHarvestSchedule($id)
    {
        $schedule = MonthlySchedule::find($id);
        $mindate = $schedule->year->name . '-' . $schedule->month->code . '-01';
        $maxdate = date("Y-m-t", strtotime($mindate));
        if ($schedule->user->id != Auth::user()->id) {
            return abort(403);
        }
        return view('ppl/entry-schedule', ['schedule' => $schedule, 'mindate' => $mindate, 'maxdate' => $maxdate]);
    }

    function storeHarvestSchedule(Request $request, $id)
    {
        $this->validate($request, [
            'date' => 'required',
        ]);

        $schedule = MonthlySchedule::find($id);
        $harvestSchedule = $schedule->harvestSchedule;
        if ($harvestSchedule == null) {
            HarvestSchedule::create([
                'date' => $request->date,
                'respondent_name' => $schedule->name,
                'monthly_schedule_id' => $schedule->id,
            ]);
        } else {
            $harvestSchedule->update(
                ['date' => $request->date]
            );
        }

        return redirect('/jadwal-ubinan')->with('success-create', 'Jadwal Panen telah disimpan!');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
