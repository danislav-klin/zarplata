<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Складской отдел',
                'description' => 'Работа на складе, погрузка/разгрузка',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Транспортный отдел',
                'description' => 'Водители и экспедиторы',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Производственный отдел',
                'description' => 'Рабочие производственных линий',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Хозяйственный отдел',
                'description' => 'Уборка, обслуживание помещений',
                'is_active'   => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('departments')->insertBatch($data);
    }
}
