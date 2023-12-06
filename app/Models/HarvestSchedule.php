<?php

namespace App\Models;

use Database\Factories\HarvestScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

class HarvestSchedule extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function monthlySchedule()
    {
        return $this->belongsTo(MonthlySchedule::class);
    }

    protected static function newFactory(): Factory
    {
        return HarvestScheduleFactory::new();
    }
}
