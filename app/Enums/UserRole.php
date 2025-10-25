<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case User = 'user';
    case Author = 'author';

    /**
     * Retorna um rótulo legível para a role.
     * Útil para frontends ou views.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Editor => 'Editor',
            self::User => 'Usuário',
            self::Author => 'Autor',
        };
    }
}
