<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePositionsTable extends Migration
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
                'constraint' => 150,
                'null'       => false,
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('positions', true, ['ENGINE' => 'InnoDB']);
        
        $this->db->query('
            ALTER TABLE `positions` 
            ADD CONSTRAINT `fk_positions_department` 
            FOREIGN KEY (`department_id`) 
            REFERENCES `departments`(`id`) 
            ON DELETE SET NULL
        ');
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        $this->forge->dropTable('positions', true);
    }
}
