<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RatesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Уборка помещений',          'rate' => 150.00, 'unit_id' => 1, 'description' => 'Почасовая оплата уборки',          'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Погрузка/разгрузка',        'rate' => 450.00, 'unit_id' => 2, 'description' => 'Оплата за тонну груза',            'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Перевозка груза',           'rate' => 35.00,  'unit_id' => 3, 'description' => 'Оплата за километр пробега',       'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Работа на линии',           'rate' => 200.00, 'unit_id' => 1, 'description' => 'Почасовая оплата оператора',       'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Упаковка товара',           'rate' => 5.00,   'unit_id' => 4, 'description' => 'Оплата за упакованную единицу',   'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Мойка помещений',           'rate' => 80.00,  'unit_id' => 5, 'description' => 'Оплата за квадратный метр',        'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Смена на складе',           'rate' => 1500.00,'unit_id' => 7, 'description' => 'Фиксированная оплата за смену',    'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['name' => 'Доставка (городская)',      'rate' => 800.00, 'unit_id' => 8, 'description' => 'Оплата за одну поездку',           'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('rates')->insertBatch($data);
    }
}
