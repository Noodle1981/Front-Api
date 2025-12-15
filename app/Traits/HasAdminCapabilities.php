<?php

namespace App\Traits;

trait HasAdminCapabilities
{
    /**
     * Determina si el usuario es administrador
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Verifica si el usuario puede ver todos los registros
     *
     * @return bool
     */
    public function canViewAll(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Verifica si el usuario puede ver estadísticas globales
     *
     * @return bool
     */
    public function canViewGlobalStats(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Verifica si el usuario puede gestionar otros usuarios
     *
     * @return bool
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Verifica si el usuario puede exportar datos
     *
     * @return bool
     */
    public function canExportData(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Verifica si el usuario puede ver métricas de rendimiento
     *
     * @return bool
     */
    public function canViewPerformanceMetrics(): bool
    {
        return $this->isAdmin();
    }
}