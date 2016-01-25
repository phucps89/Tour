<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/16/2016
 * Time: 10:19 AM
 */

class Location extends AbstractModel{

    public static function getLocation($code){
        static $data;
        if(empty($data)){
            $data = self::all();
        }
        $location = $data->filter(function($item) use ($code){
            return $item->code == $code;
        })->first();
        return $location;
    }
}