<?php

namespace App\Models;

use CodeIgniter\Model;

class RateModel extends Model
{
    protected $table            = 'rates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'rate', 'unit_id', 'description', 'is_active'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'    => 'required|min_length[2]|max_length[255]',
        'rate'    => 'required|decimal',
        'unit_id' => 'required|is_natural_no_zero',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Метод для получения ставок с именами единиц измерения
    public function getRatesWithUnits()
    {
        return $this->select('rates.*, units.name as unit_name, units.short_name as unit_short')
            ->join('units', 'units.id = rates.unit_id', 'left')
            ->findAll();
    }
}
