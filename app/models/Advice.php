<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/19/2016
 * Time: 8:36 PM
 */

use Illuminate\Database\Eloquent\Collection;

class Advice extends AbstractModel{

    function details(){
        return $this->hasMany('AdviceDetail', 'id_advice');
    }

    /**
     * @return Collection
     */
    function getListQuestion(){
        $details = $this->details;
        $listIDQuestion = $details->lists('id_question');
        $listIDQuestion = array_unique($listIDQuestion);
        $questions = Question::whereIn('id', $listIDQuestion)
            ->get();
        return $questions;
    }
}