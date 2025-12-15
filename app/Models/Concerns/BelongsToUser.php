<?php

namespace App\Models\Concerns;

trait BelongsToUser
{
    /**
     * Scope para filtrar registros por el usuario actual
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForCurrentUser($query)
    {
        if (!auth()->user()->isAdmin()) {
            return $query->where('user_id', auth()->id());
        }
        return $query;
    }

    /**
     * Verifica si el registro pertenece al usuario actual
     *
     * @return bool
     */
    public function belongsToUser($user = null)
    {
        $user = $user ?? auth()->user();
        return $user->id === $this->user_id || $user->isAdmin();
    }

    /**
     * Filtro para estadísticas limitadas al usuario actual
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUserStats($query)
    {
        if (!auth()->user()->isAdmin()) {
            return $query->where('user_id', auth()->id());
        }
        return $query;
    }
}