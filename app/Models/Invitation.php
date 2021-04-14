<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    public function worker() {
        return $this->belongsTo(\App\Models\User::class,'worker_id');
    }

    public function admin() {
        return $this->belongsTo(\App\Models\User::class,'admin_id');
    }
}
