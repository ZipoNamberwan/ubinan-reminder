<?php

namespace App\Imports;

use App\Exceptions\NoScheduleException;
use App\Models\Month;
use App\Models\MonthlySchedule;
use App\Models\Year;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MonthlyScheduleImportCheck implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $c) {
            if ($c['tahun'] != null & $c['sub_round'] != null) {
                $month = Month::find(($c['sub_round'] - 1) * 4 + $c['panen'])->id;
                $year = Year::where(['name' => $c['tahun']])->first()->id;
                $ms = MonthlySchedule::where(['year_id' => $year, 'month_id' => $month])->get();
                if (count($ms) > 0) {
                    throw new NoScheduleException();
                }
            }
        }
    }

    public function headingRow(): int
    {
        return 1;
    }
}
