<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HarvestSchedule extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function monthlySchedule()
    {
        return $this->belongsTo(MonthlySchedule::class);
    }
}
