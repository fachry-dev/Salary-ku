<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;




class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

     
    public function run()
    {
        // Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);
        // No need to create Karyawan data for admin, unless admin is also an employee

        // Example Employee User 1
        $EmployeeUser = User::create([
            'name' => 'Budi Karyawan',
            'email' => 'budi@example.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        Employee::create([
            'user_id' => $EmployeeUser->id,
            'nip' => 'K001',
            'nama_lengkap' => 'Budi Santoso',
            'alamat' => 'Jl. Mawar No. 10',
            'no_telepon' => '081234567890',
            'tanggal_masuk' => '2023-01-15',
            'gaji_pokok' => 5000000,
            'status' => 'aktif', // Adding status field which is likely needed
        ]);

        // Example Employee User 2
        $EmployeeUser2 = User::create([
            'name' => 'Siti Karyawati',
            'email' => 'siti@example.com',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        Employee::create([
            'user_id' => $EmployeeUser2->id,
            'nip' => 'K002',
            'nama_lengkap' => 'Siti Aminah',
            'alamat' => 'Jl. Melati No. 12',
            'no_telepon' => '081234567891',
            'tanggal_masuk' => '2022-07-01',
            'gaji_pokok' => 6500000,
            'status' => 'aktif', // Adding status field which is likely needed
        ]);

        User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => bcrypt('123456'),
        'role' => 'admin'
    ]);
    
    }
    
}