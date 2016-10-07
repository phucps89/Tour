<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/16/2016
 * Time: 10:11 AM
 */

class Tour extends AbstractModel{

    function location(){
        return $this->belongsTo('Location', 'start_loc', 'id');
    }
}