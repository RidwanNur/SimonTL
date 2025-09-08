<?php

namespace App\Models;

use App\Models\TindakLanjut;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
    use HasFactory, Notifiable, HasRoles;


    protected $table='master_status';
    protected $guarded=['id'];


    public function statusTL(){

     return $this->belongsTo(TindakLanjut::class);
        
    }
}
