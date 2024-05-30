<?php

namespace App\Helper;

class Utils
{
    public static function convertStatusToBool(string $status): bool
    {
        return strtolower($status) === 'done';
    }
}
