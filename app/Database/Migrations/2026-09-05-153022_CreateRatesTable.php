<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRatesTable extends Migration
{
    public function up()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => false,
            ],
            'rate' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('rates', true, ['ENGINE' => 'InnoDB']);
        
        $this->db->query('
            ALTER TABLE `rates` 
            ADD CONSTRAINT `fk_rates_unit` 
            FOREIGN KEY (`unit_id`) 
            REFERENCES `units`(`id`) 
            ON DELETE CASCADE
        ');
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        $this->forge->dropTable('rates', true);
    }
}
