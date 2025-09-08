<?php

namespace App\Models;

use App\Models\Status;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bidang extends Model
{
        use HasFactory, Notifiable, HasRoles;


    protected $table='master_bidang';
    protected $guarded=['id'];

}
