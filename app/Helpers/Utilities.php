<?php

namespace App\Helpers;

class Utilities
{
    public static function getSentenceFromArray($array, $separator = ', ', $lastseparator = ' dan ')
    {
        $last  = array_slice($array, -1);
        $first = join($separator, array_slice($array, 0, -1));
        $both  = array_filter(array_merge(array($first), $last), 'strlen');
        return join($lastseparator, $both);
    }
}
