<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkEntriesTable extends Migration
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
            'work_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'rate_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'qty' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => '0.00',
            ],
            'rate_snapshot' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'rate_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 200,
                'null'       => true,
            ],
            'rate_unit' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'default'    => '0.00',
            ],
            'note' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->createTable('work_entries', true, ['ENGINE' => 'InnoDB']);
        
        $this->db->query('
            ALTER TABLE `work_entries` 
            ADD CONSTRAINT `fk_workentries_employee` 
            FOREIGN KEY (`employee_id`) 
            REFERENCES `employees`(`id`) 
            ON DELETE RESTRICT
        ');
        
        $this->db->query('
            ALTER TABLE `work_entries` 
            ADD CONSTRAINT `fk_workentries_rate` 
            FOREIGN KEY (`rate_id`) 
            REFERENCES `rates`(`id`) 
            ON DELETE SET NULL
        ');
        
        $this->db->query('
            ALTER TABLE `work_entries` 
            ADD CONSTRAINT `fk_workentries_createdby` 
            FOREIGN KEY (`created_by`) 
            REFERENCES `users`(`id`) 
            ON DELETE SET NULL
        ');
        
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        $this->forge->dropTable('work_entries', true);
    }
}
