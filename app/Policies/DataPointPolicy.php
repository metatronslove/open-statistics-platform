<?php

namespace App\Policies;

use App\Models\DataPoint;
use App\Models\User;
use App\Models\DataProvider;
use Illuminate\Auth\Access\Response;

class DataPointPolicy
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
    public function view(User $user, DataPoint $dataPoint): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'statistician') {
            return $dataPoint->dataset->created_by === $user->id;
        }

        if ($user->role === 'provider') {
            $provider = DataProvider::where('user_id', $user->id)->first();
            return $provider && $dataPoint->data_provider_id === $provider->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'provider';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DataPoint $dataPoint): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider') {
            $provider = DataProvider::where('user_id', $user->id)->first();
            return $provider && $dataPoint->data_provider_id === $provider->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DataPoint $dataPoint): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider') {
            $provider = DataProvider::where('user_id', $user->id)->first();
            return $provider && $dataPoint->data_provider_id === $provider->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DataPoint $dataPoint): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DataPoint $dataPoint): bool
    {
        return $user->role === 'admin';
    }
}
