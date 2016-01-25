<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 12/29/2015
 * Time: 9:59 PM
 */
class Question extends AbstractModel{
    static $_rootQuestionID = [1, 2, 3, 4];

    function answers(){
        return $this->hasMany('QuestionAnswer', 'id_question');
    }
}