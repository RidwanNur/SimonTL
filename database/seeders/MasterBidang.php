<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MasterBidang extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bid = [
                'IPP',
                'APD',
                'Investigasi',
                'P3A',
        ];

        $rows = array_map(function ($name) {
            return [
                'nama_bidang' => $name,
                'created_at' => now()
            ];
        }, $bid);
        
        DB::table('master_bidang')->insert($rows);
    }
}
