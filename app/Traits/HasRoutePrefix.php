<?php

namespace App\Traits;

trait HasRoutePrefix
{
    /**
     * Deteksi route prefix secara dinamis.
     * Return 'admin-umum' jika request dari /admin-umum, 'superadmin' jika dari /superadmin.
     */
    protected function routePrefix(): string
    {
        $prefix = request()->route()?->getPrefix() ?? '';
        return str_contains($prefix, 'admin-umum') ? 'admin-umum' : 'superadmin';
    }

    /**
     * Shortcut: return route name dengan prefix dinamis.
     * Contoh: $this->prefixRoute('data-entries.data') => 'admin-umum.data-entries.data'
     */
    protected function prefixRoute(string $name, mixed $params = []): string
    {
        return route($this->routePrefix() . '.' . $name, $params);
    }
}
