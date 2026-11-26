<?php

namespace App\Support;

final class LikeEscaper
{
    public static function contains(string $term): string
    {
        return '%'.addcslashes(mb_strtolower($term), '%_\\').'%';
    }
}
