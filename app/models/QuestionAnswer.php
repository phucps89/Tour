<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/16/2016
 * Time: 10:05 AM
 */

class QuestionAnswer extends AbstractModel{
    protected $table = 'question_answer';

    function detail(){
        return $this->belongsTo('Answer', 'id_answer');
    }
}