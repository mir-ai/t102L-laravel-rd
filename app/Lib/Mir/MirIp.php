<?php namespace App\Lib\Mir;

use Illuminate\Database\Eloquent\Model;

class MirIp {

    public static function get()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $ip2 = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
        if ($ip2) {
            $ipArray = array_map('trim', explode(',', $ip2));
            $ip = $ipArray[0] ?? '';
        }

        return $ip;
    }
}
