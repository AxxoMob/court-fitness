<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Training plans — one row per (coach, player, week_of). week_of must be a
 * Monday; the model layer enforces this via the `isMonday` custom validation
 * rule (see validationMessages). Schema in migration 2026-04-23-130200.
 */
final class TrainingPlansModel extends Model
{
    protected $table            = 'training_plans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'coach_user_id',
        'player_user_id',
        'week_of',
        'training_target',
        'weight_unit',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'coach_user_id'   => 'required|is_natural_no_zero',
        'player_user_id'  => 'required|is_natural_no_zero|differs[coach_user_id]',
        'week_of'         => 'required|valid_date[Y-m-d]',
        'training_target' => 'required|max_length[100]',
        'weight_unit'     => 'required|in_list[kg,lb]',
        'notes'           => 'permit_empty|max_length[5000]',
    ];
}
