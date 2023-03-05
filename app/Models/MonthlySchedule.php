<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySchedule extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function harvestSchedule()
    {
        return $this->hasOne(HarvestSchedule::class);
    }
    public function bs()
    {
        return $this->belongsTo(Bs::class, 'bs_id');
    }
    public function month()
    {
        return $this->belongsTo(Month::class, 'month_id');
    }
    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
    public function commodity()
    {
        return $this->belongsTo(Commodity::class, 'commodity_id');
    }
    public function sampleType()
    {
        return $this->belongsTo(SampleType::class, 'sample_type_id');
    }
}
