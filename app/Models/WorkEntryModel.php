<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkEntryModel extends Model
{
    protected $table      = 'work_entries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'work_date', 'employee_id', 'rate_id',
        'qty', 'rate_snapshot', 'rate_name', 'rate_unit',
        'amount', 'note', 'created_by',
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
