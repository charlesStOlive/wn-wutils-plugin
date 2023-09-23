<?php

namespace Waka\Wutils\Classes;

class TinyUuid
{
    public static function generate(int $length = 5)
    {
        $str = "";
        for ($x = 0; $x < $length; $x++) $str .= substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"), 0, 1);
        return $str;
    }

    public static function simple(int $length = 5)
    {
        $str = "";
        for ($x = 0; $x < $length; $x++) $str .= substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, 1);
        return $str;
    }

    public static function generateFromDate($separator = true) {
        if($separator) {
            return  \Carbon\Carbon::now()->format('y_n_j-G_i_s');
        } else {
            return  \Carbon\Carbon::now()->format('ymd-His');
        }
        
    }

    public static function uuid(string $prefix = '', bool $more_entropy = false)
    {
        return uniqid($prefix, $more_entropy);
    }

    public static function readable(array $strings)
    {
        return implode('_', $strings);
    }
}
