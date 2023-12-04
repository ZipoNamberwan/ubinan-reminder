<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function schedules()
    {
        return $this->hasMany(MonthlySchedule::class, 'month_id');
    }
}
