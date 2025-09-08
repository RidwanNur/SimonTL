<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tindak_Lanjut_Log extends Model
{
        use HasFactory, Notifiable, HasRoles;

    protected $table='tindak_lanjut_log';
    protected $guarded=['id'];

    protected $casts = [
    'report_dateline' => 'date',
    ];
}
