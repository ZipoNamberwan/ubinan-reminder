<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bs extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    protected $table = 'bs';

    public function village()
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function subdistrict()
    {
        return $this->village->subdistrict();
    }

    public function fullcode()
    {
        return "3513" . $this->subdistrict->code . $this->village->short_code . $this->short_code;
    }
    public function fullcodesegment()
    {
        return "3513" . $this->subdistrict->code;
    }

    public function fullname()
    {
        return $this->subdistrict->name . ", " . $this->village->name . ", " . $this->name;
    }
    public function fullnamesegment()
    {
        return $this->subdistrict->name . ", " . $this->village->name;
    }
}
