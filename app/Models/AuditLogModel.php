<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table      = 'audit_log';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    // Только вставка, редактирование лога не нужно
    protected $allowedFields = [
        'user_id', 'entity', 'entity_id',
        'action', 'changes', 'created_at'
    ];

    protected $useTimestamps = false; // только created_at, вручную
}
