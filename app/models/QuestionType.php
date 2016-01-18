<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/16/2016
 * Time: 9:48 AM
 */

class QuestionType extends \Illuminate\Database\Eloquent\Model{
    protected $table = 'question_types';

    const TYPE_YES_NO = 1;
    const TYPE_MULTI_CHOICE = 2;
    const TYPE_TEXT = 3;
    const TYPE_FACTOR = 4;
    const TYPE_FILTER = 5;
}