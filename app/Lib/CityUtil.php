<?php namespace App\Lib;

class CityUtil {

    public static function city_name()
    {
        return config('_env.CITY_NAME', 'みらい市');
    }

    public static function city_code()
    {
        // SMS配信識別（smsid, post_idなどで使うので)8桁まで
        return config('_env.CITY_CODE', '012345');
    }

    public static function city_long_code()
    {
        return config('_env.CITY_LONG_CODE', '012345_city');
    }

    /**
     * 長い地区コードを5桁の市区町村コードに統一する。
     * 政令市の区は政令市の市区町村コードに統一する。
     *
     * @param string $city_code
     * @return string
     */
    public static function city_code_5(string $city_code): string
    {
        $city_code_5 = substr($city_code, 0, 5);

        if (($ordinance_city_key = config("_const.ordinance_city_codes_5.{$city_code_5}"))) {
            // 政令市の区コードだったら、政令市の市コードに変換する

            $city_code_5 = $ordinance_city_key;
        }

        return $city_code_5;
    }

    /**
     * 市区町村コード（５桁）を市区町村コード（６桁）に変換します。
     *
     * @param string $city_code_5
     * @return string
     */
    public static function city_code_5_to_6(string $city_code_5): string
    {
        return config("_const.city_code_5_to_6.{$city_code_5}");
    }

}
