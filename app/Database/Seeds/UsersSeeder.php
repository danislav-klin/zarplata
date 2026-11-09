<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'first_name'    => 'Админ',
                'last_name'     => 'Системный',
                'surname'       => 'Иванович',
                'login'         => 'admin',
                'email'         => 'admin@example.com',
                'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'          => 'admin',
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'first_name'    => 'Менеджер',
                'last_name'     => 'Тестовый',
                'surname'       => 'Петрович',
                'login'         => 'manager',
                'email'         => 'manager@example.com',
                'password_hash' => password_hash('manager123', PASSWORD_DEFAULT),
                'role'          => 'manager',
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
