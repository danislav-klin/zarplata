<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['first_name' => 'Иван',      'last_name' => 'Иванов',     'surname' => 'Иванович',     'email' => 'ivanov@example.com',     'phone' => '+7 (912) 345-67-01', 'hired_at' => '2023-01-10', 'personnel_number' => '00101', 'department_id' => 1, 'position_id' => 1, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Пётр',      'last_name' => 'Петров',     'surname' => 'Петрович',     'email' => 'petrov@example.com',     'phone' => '+7 (912) 345-67-02', 'hired_at' => '2023-02-15', 'personnel_number' => '00102', 'department_id' => 1, 'position_id' => 2, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Алексей',   'last_name' => 'Смирнов',    'surname' => 'Петрович',     'email' => 'smirnov@example.com',    'phone' => '+7 (913) 456-78-90', 'hired_at' => '2023-01-15', 'personnel_number' => '00201', 'department_id' => 2, 'position_id' => 3, 'note' => 'Опытный водитель',    'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Дмитрий',   'last_name' => 'Попов',      'surname' => 'Андреевич',    'email' => 'popov@example.com',      'phone' => '+7 (914) 567-89-01', 'hired_at' => '2023-03-10', 'personnel_number' => '00203', 'department_id' => 2, 'position_id' => 4, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Сергей',    'last_name' => 'Михайлов',   'surname' => 'Владимирович', 'email' => 'mikhailov@example.com',  'phone' => '+7 (916) 789-01-23', 'hired_at' => '2023-05-12', 'personnel_number' => '00205', 'department_id' => 3, 'position_id' => 5, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Николай',   'last_name' => 'Соколов',    'surname' => 'Алексеевич',   'email' => 'sokolov@example.com',    'phone' => '+7 (918) 901-23-45', 'hired_at' => '2023-07-22', 'personnel_number' => '00207', 'department_id' => 3, 'position_id' => 6, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Ольга',     'last_name' => 'Кузнецова',  'surname' => 'Ивановна',     'email' => 'kuznetsova@example.com', 'phone' => '+7 (913) 456-78-91', 'hired_at' => '2023-02-20', 'personnel_number' => '00202', 'department_id' => 4, 'position_id' => 7, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Екатерина', 'last_name' => 'Васильева',  'surname' => 'Сергеевна',    'email' => 'vasilieva@example.com',  'phone' => '+7 (915) 678-90-12', 'hired_at' => '2023-04-05', 'personnel_number' => '00204', 'department_id' => 4, 'position_id' => 7, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Анна',      'last_name' => 'Фёдорова',   'surname' => 'Дмитриевна',   'email' => 'fedorova@example.com',   'phone' => '+7 (917) 890-12-34', 'hired_at' => '2023-06-18', 'personnel_number' => '00206', 'department_id' => 1, 'position_id' => 1, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Татьяна',   'last_name' => 'Морозова',   'surname' => 'Игоревна',     'email' => 'morozova@example.com',   'phone' => '+7 (919) 012-34-56', 'hired_at' => '2023-08-30', 'personnel_number' => '00208', 'department_id' => 2, 'position_id' => 3, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Владимир',  'last_name' => 'Волков',     'surname' => 'Юрьевич',      'email' => 'volkov@example.com',     'phone' => '+7 (920) 123-45-67', 'hired_at' => '2023-09-14', 'personnel_number' => '00209', 'department_id' => 1, 'position_id' => 2, 'note' => 'Стажёр',              'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['first_name' => 'Марина',    'last_name' => 'Новикова',   'surname' => 'Павловна',     'email' => 'novikova@example.com',   'phone' => '+7 (921) 234-56-78', 'hired_at' => '2023-10-05', 'personnel_number' => '00210', 'department_id' => 3, 'position_id' => 5, 'note' => null,                  'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('employees')->insertBatch($data);
    }
}
