<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterInstansiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $opd = [
                'BPKP',
                'Inspektorat Nabire',
                'Inspektorat Mimika',
                'Inspektorat Paniai',
                'Inspektorat Deiyai',
                'Inspektorat Dogiyai'
        ];

        $rows = array_map(function ($name) {
            return [
                'nama_instansi' => $name,
                'created_at' => now()
            ];
        }, $opd);
        
        DB::table('master_instansi')->insert($rows);
    }
}
