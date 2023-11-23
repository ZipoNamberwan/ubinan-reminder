<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SentMessages extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'sent_messages';

    public function user()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
