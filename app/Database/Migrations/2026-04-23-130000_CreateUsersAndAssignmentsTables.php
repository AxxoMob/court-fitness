<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: users + coach_player_assignments.
 *
 * users — identity records synced via SSO from HitCourt. hitcourt_user_id is the
 * stable external key; email/name are mirrored for display. role is VARCHAR(20)
 * (not ENUM) so HitCourt can add new roles without requiring a migration here
 * (per owner 2026-04-23: "Admin, Coach, Player and so on").
 *
 * coach_player_assignments — many-to-many linking. One active link per (coach,
 * player) pair; soft-deleted links can be re-created via un-delete.
 */
final class CreateUsersAndAssignmentsTables extends Migration
{
    public function up(): void
    {
        // users
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'hitcourt_user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'comment' => 'Stable identity from HitCourt'],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'first_name'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'family_name'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'role'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false, 'comment' => 'coach | player | admin | ... (from HitCourt JWT)'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('hitcourt_user_id');
        $this->forge->addKey('email');
        $this->forge->addKey('role');
        $this->forge->createTable('users');

        // coach_player_assignments
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'coach_user_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'player_user_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
            'assigned_date'   => ['type' => 'DATE', 'null' => false],
            'is_active'       => ['type' => 'TINYINT', 'constraint' => 1, 'null' => false, 'default' => 1],
            'notes'           => ['type' => 'TEXT', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['coach_user_id', 'player_user_id']);
        $this->forge->addKey('player_user_id');
        $this->forge->addForeignKey('coach_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('player_user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('coach_player_assignments');
    }

    public function down(): void
    {
        $this->forge->dropTable('coach_player_assignments', true);
        $this->forge->dropTable('users', true);
    }
}
