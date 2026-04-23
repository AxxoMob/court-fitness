<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Users model. court-fitness never creates users via a local registration —
 * rows here are mirrors of HitCourt identities, upserted on SSO handoff.
 *
 * The `role` column is VARCHAR(20) intentionally — see HL-11 / session 2
 * handover: HitCourt may issue Admin, Coach, Player, or other future roles,
 * and court-fitness routes on the string value without locking an ENUM.
 */
final class UsersModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'hitcourt_user_id',
        'email',
        'first_name',
        'family_name',
        'role',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'hitcourt_user_id' => 'required|is_natural_no_zero',
        'email'            => 'required|valid_email|max_length[255]',
        'first_name'       => 'required|max_length[100]',
        'family_name'      => 'required|max_length[100]',
        'role'             => 'required|max_length[20]',
    ];

    /**
     * Look up a local user by the HitCourt identity.
     */
    public function findByHitCourtId(int $hitcourtUserId): ?array
    {
        $row = $this->where('hitcourt_user_id', $hitcourtUserId)->first();

        return $row ?: null;
    }

    /**
     * Create-or-update from SSO JWT claims. Returns the full user row.
     */
    public function upsertFromJwt(array $claims): array
    {
        $data = [
            'hitcourt_user_id' => (int) $claims['hitcourt_user_id'],
            'email'            => (string) $claims['email'],
            'first_name'       => (string) $claims['first_name'],
            'family_name'      => (string) $claims['family_name'],
            'role'             => strtolower((string) $claims['role']),
        ];

        $existing = $this->findByHitCourtId($data['hitcourt_user_id']);

        if ($existing !== null) {
            $this->update((int) $existing['id'], $data);
            $row = $this->find((int) $existing['id']);
        } else {
            $id  = $this->insert($data, true);
            $row = $this->find((int) $id);
        }

        return is_array($row) ? $row : [];
    }
}
