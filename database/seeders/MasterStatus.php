<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterStatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status = [
                'Buat Tindak Lanjut',
                'Mengirim Notifikasi TL ke OPD',
                'di Tindak Lanjut',
                'Assign ke Tim terkait',
                'Verifikasi Tim',
                'Selesai'
        ];

        $rows = array_map(function ($name) {
            return [
                'name_status' => $name,
                'created_at' => now()
            ];
        }, $status);
        
        DB::table('master_status')->insert($rows);
    }
}
