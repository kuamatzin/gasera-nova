<?php

namespace App\Policies;

use App\Models\Record;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RecordPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Record $record): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return match ($user->role) {
            'admin' => true,
            'abogado', 'coordinador', 'director', 'gestor', 'cliente' => false,
            default => false,
        };
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Record $record): bool
    {
        return match ($user->role) {
            'admin', 'gestor', 'abogado' => true,
            'coordinador', 'director', 'cliente' => false,
            default => false,
        };
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Record $record): bool
    {
        return match ($user->role) {
            'admin' => true,
            'abogado', 'coordinador', 'director', 'gestor', 'cliente' => false,
            default => false,
        };
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Record $record): bool
    {
        return match ($user->role) {
            'admin' => true,
            'abogado', 'coordinador', 'director', 'gestor', 'cliente' => false,
            default => false,
        };
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Record $record): bool
    {
        return match ($user->role) {
            'admin' => true,
            'abogado', 'coordinador', 'director', 'gestor', 'cliente' => false,
            default => false,
        };
    }

    public function replicate(User $user, Record $record): bool
    {
        return false;
    }
}
