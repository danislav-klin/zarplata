<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table      = 'employees';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'first_name', 'last_name', 'surname',
        'email', 'phone', 'hired_at', 'note',
        'department_id', 'position_id', 'position',
        'personnel_number',
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
