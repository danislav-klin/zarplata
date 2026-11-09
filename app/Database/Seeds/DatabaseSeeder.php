<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('UsersSeeder');
        $this->call('DepartmentsSeeder');
        $this->call('PositionsSeeder');
        $this->call('EmployeesSeeder');
        $this->call('UnitsSeeder');
        $this->call('RatesSeeder');
    }
}
