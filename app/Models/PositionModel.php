<?php

namespace App\Models;

use CodeIgniter\Model;

class PositionModel extends Model
{
    protected $table      = 'positions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name', 'department_id', 'description',
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
