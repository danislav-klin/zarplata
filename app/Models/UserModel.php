<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array'; 
    protected $protectFields = true;

    protected $allowedFields = [
        'first_name',
        'last_name',
        'surname',
        'login',
        'email',
        'password_hash',
        'role',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    
}
