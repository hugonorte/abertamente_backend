<?php

namespace App\Enums;

enum PostStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHING = 'publishing';
    case PUBLISHED = 'published';
    case UNPUBLISHING = 'unpublishing';
    case UNPUBLISHED = 'unpublished';
    case ERROR = 'error';

    /**
     * Retorna os valores que são considerados visíveis publicamente
     */
    public static function publicStatuses(): array
    {
        return [
            self::PUBLISHED->value,
        ];
    }
}
