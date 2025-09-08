<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TindakLanjut extends Model
{
    use HasFactory, Notifiable, HasRoles;


    protected $table='tindak_lanjut';
    protected $guarded=['id'];



    public function status(){
            return $this->belongsTo(Status::class, 'followup_status', 'id');
    }

}
