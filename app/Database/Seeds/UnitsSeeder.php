<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UnitsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'час',     'short_name' => 'ч',   'description' => 'Часы работы',           'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'тонна',   'short_name' => 'т',   'description' => 'Масса груза',           'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'км',      'short_name' => 'км',  'description' => 'Километры пробега',     'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'шт',      'short_name' => 'шт',  'description' => 'Штуки',                 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'м²',      'short_name' => 'м²',  'description' => 'Квадратные метры',      'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'м³',      'short_name' => 'м³',  'description' => 'Кубические метры',      'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'смена',   'short_name' => 'см',  'description' => 'Рабочая смена',         'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'поездка', 'short_name' => 'пзд', 'description' => 'Количество поездок',    'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('units')->insertBatch($data);
    }
}
