<?php

namespace App\Models;

use Database\Factories\MonthlyScheduleFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonthlySchedule extends Model
{
    use HasFactory;
    protected $guarded = [];
    use SoftDeletes;

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
    public function subSegment()
    {
        return $this->belongsTo(SubSegment::class, 'subsegment_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function newFactory(): Factory
    {
        return MonthlyScheduleFactory::new();
    }
}
