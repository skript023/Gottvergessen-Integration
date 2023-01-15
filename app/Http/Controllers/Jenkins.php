<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Jenkins extends Controller
{
    public static function UInt32($num)
    {
        return $num & 0xFFFFFFFF;
    }

    public static function hash($name)
    {
        $hash = 0;
        $name = strtolower($name);
        foreach(str_split($name) as $letter)
        {
            $hash += ord($letter);
            $hash = Jenkins::UInt32($hash);
            $hash += $hash << 10;
            $hash ^= Jenkins::UInt32($hash) >> 6;
        }

        $hash += $hash << 3;
        $hash ^= Jenkins::UInt32($hash) >> 11;
        $hash += $hash << 15;
        $hash = Jenkins::UInt32($hash);

        return $hash;
    }
}
