<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PositionsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Складской отдел
            ['name' => 'Кладовщик',            'department_id' => 1, 'description' => 'Учёт товаров на складе',         'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Грузчик',              'department_id' => 1, 'description' => 'Погрузка и разгрузка',           'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Транспортный отдел
            ['name' => 'Водитель',             'department_id' => 2, 'description' => 'Управление транспортом',         'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Экспедитор',           'department_id' => 2, 'description' => 'Сопровождение грузов',           'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Производственный отдел
            ['name' => 'Оператор линии',       'department_id' => 3, 'description' => 'Работа на оборудовании',         'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Мастер цеха',          'department_id' => 3, 'description' => 'Контроль качества продукции',    'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Хозяйственный отдел
            ['name' => 'Уборщица',             'department_id' => 4, 'description' => 'Уборка помещений',               'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Сантехник',            'department_id' => 4, 'description' => 'Обслуживание коммуникаций',      'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('positions')->insertBatch($data);
    }
}
