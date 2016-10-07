<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 12/29/2015
 * Time: 9:59 PM
 */
class Question extends AbstractModel{
    static $_rootQuestionID = [1, 2, 3, 4];
    //protected $guarded = array();  // Important
    protected $fillable = array('id', 'name', 'id_question_type', 'created_at', 'updated_at');

    function answers(){
        return $this->hasMany('QuestionAnswer', 'id_question');
    }

    function bestAnswer($except = [])
    {
        $answerHistory = AdviceDetail
            ::whereNotIn('id_question', array_merge(self::$_rootQuestionID, $except))
            ->lists('id_answer');
        $answers = $this->answers;
        $listThisIDAnswer = $answers->lists('id_answer');
        $listIDTourSuggest = TourScore::select('*');
        foreach ($answerHistory as $ans) {
            $listIDTourSuggest->orWhere(function ($query) use ($ans) {
                $query->where('id_answer', $ans)
                    ->where('score', 100);
            });
        }
        $listIDTourSuggest = $listIDTourSuggest->lists('id_tour');
        $listTourScoreOrder = TourScore::where(function ($query) use ($answers, $listThisIDAnswer) {
            foreach ($listThisIDAnswer as $ans) {
                $query->orWhere(function ($query) use ($ans) {
                    $query->where('id_answer', $ans)
                        ->where('score', '>=', 50);
                });
            }
        });

        $listTourScoreOrder->whereIn('id_tour', $listIDTourSuggest);

        $listTourScoreOrder = $listTourScoreOrder->get();

        $listBestIDAnswer = array_values(array_unique($listTourScoreOrder->lists('id_answer')));
        if ($listBestIDAnswer) {
            return Answer::whereIn('id', $listBestIDAnswer)->get();
        } else {
            return Answer::whereIn('id', $listThisIDAnswer)->get();
        }
    }
}