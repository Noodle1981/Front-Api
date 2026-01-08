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
        // Si es Admin o Programador (Ex Analista), ve todo.
        if (auth()->user()->isAdmin() || auth()->user()->hasRole(['Analista', 'Programador'])) {
            return $query;
        }
        // Si es Usuario normal, solo lo suyo
        return $query->where('user_id', auth()->id());
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
        if (!auth()->user()->isAdmin() && !auth()->user()->hasRole(['Analista', 'Programador'])) {
            return $query->where('user_id', auth()->id());
        }
        return $query;
    }
}