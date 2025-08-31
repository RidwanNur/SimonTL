<?php

namespace App\Models;

use App\Models\SKP;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Laporan extends Model
{
    use HasFactory, Notifiable, HasRoles;


    protected $table='laporan_hasil_pengawasan';
    protected $guarded=['id'];

    protected $casts = [
    'report_deadline' => 'date',
];
}
