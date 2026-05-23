<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        date_default_timezone_set('Asia/Jakarta');
        $faker = \Faker\Factory::create('id_ID');

        // 1. KITA BUAT MANUAL USER ADMIN & GUEST AGAR PASTI ADA DI DATABASE
        $pasti_ada = [
            [
                'username'   => 'karen13',
                'email'      => 'karen13@gmail.com',
                'password'   => password_hash('1234567', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'created_at' => date("Y-m-d H:i:s")
            ],
            [
                'username'   => 'spuspita',
                'email'      => 'spuspita@gmail.com',
                'password'   => password_hash('1234567', PASSWORD_DEFAULT),
                'role'       => 'guest',
                'created_at' => date("Y-m-d H:i:s")
            ]
        ];

        // Masukkan data admin & guest yang sudah pasti ini ke database
        foreach ($pasti_ada as $user) {
            $this->db->table('user')->insert($user);
        }

        // 2. SISANYA (8 USER) BIARKAN ACAK MENGGUNAKAN FAKER
        for ($i = 0; $i < 8; $i++) {
            $data = [
                'username'   => $faker->userName,
                'email'      => $faker->email,
                'password'   => password_hash('1234567', PASSWORD_DEFAULT),
                'role'       => $faker->randomElement(['admin', 'guest']),
                'created_at' => date("Y-m-d H:i:s")
            ];
            $this->db->table('user')->insert($data);
        }
    }
}