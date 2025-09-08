<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Date;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
 
    	for($i = 1; $i <= 1; $i++){
 
    	      // insert data ke table pegawai menggunakan Faker
    		$user = User::create([
    			'username' => $faker->name,
    			'password' => Hash::make('123456'),
    			'instansi' => 'BPKP',
                'status' => 1,
                'created_at' => Carbon::now()
    		]);
            $user->assignRole('inspektorat');
    	}
    }
    
}
