<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/18/2016
 * Time: 10:19 PM
 */

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class AdviceController extends BaseController{

    function index(){
        $adviceDetailTableName = AdviceDetail::getTableName();
        $adviceTableName = Advice::getTableName();
        $advices = Advice
            ::leftJoin(
                $adviceDetailTableName,
                $adviceDetailTableName.'.id_advice',
                '=',
                $adviceTableName.'.id'
            )
            ->groupBy($adviceTableName.'.id')
            ->select([
                $adviceTableName.'.*',
                DB::raw("COUNT(DISTINCT $adviceDetailTableName.id_question) as sumQuestion")
            ])
            ->get();
        $sumQuestion = Question::count();
        return View::make('advice.index', [
            'advices' => $advices,
            'sumQuestion' => $sumQuestion
        ]);
    }

    function adviceFunction(){
        $function = Input::get('function');
        switch($function){
            case 'newActive':
                return $this->addNewAdvice();
            case 'advice':
                return $this->getAdvice();
            default:
                return null;
        }
    }

    private function addNewAdvice(){
        $advice = new Advice();
        $advice->name = Input::get('name');
        $advice->id_user = Auth::user()->id;
        $advice->save();
        return Redirect::route('advice.view', [
            'id' => $advice->id,
            'idQuestion' => 'root'
        ]);
    }

    private function getAdvice(){
        $select = Input::get('select');
        if($select == 'root'){
            $questions = Input::get('question');
            foreach($questions as $id=>$idAns){
                $adviceDetail = new AdviceDetail();
                $adviceDetail->id_advice = Input::get('advice');
                $adviceDetail->id_question = $id;
                $adviceDetail->id_answer = $idAns;
                $adviceDetail->save();
            }
            return Redirect::route('advice.view', ['id'=>Input::get('advice')]);
        }
        else if($select == 'next'){
            $questions = Input::get('question');
            foreach($questions as $id=>$listIDAnswer){
                foreach($listIDAnswer as $idAns) {
                    $adviceDetail = new AdviceDetail();
                    $adviceDetail->id_advice = Input::get('advice');
                    $adviceDetail->id_question = $id;
                    $adviceDetail->id_answer = $idAns;
                    $adviceDetail->save();
                }
            }
            return Redirect::route('advice.view', ['id'=>Input::get('advice')]);
        }
    }

    function view($id, $idQuestion = null){
        if($idQuestion == null){
            $adviceDetails = AdviceDetail::where('id_advice', $id)
                ->get();
            if($adviceDetails->count() == 0){
                return Redirect::route('advice.view', [
                    'id' => $id,
                    'idQuestion' => 'root'
                ]);
            }
            else{
                $adviceDetails = AdviceDetail
                    ::where('id_advice', $id)
                    ->get();
                //=============
                $questionAnswerLoc = AdviceDetail
                    ::where('id_advice', $id)
                    ->where('id_question', 1)
                    ->first();
                $location = Location
                    ::join(
                        Answer::getTableName(),
                        Answer::getTableName().'.name',
                        '=',
                        Location::getTableName().'.code'
                    )
                    ->where(Answer::getTableName().'.id', $questionAnswerLoc->id_answer)
                    ->select(Location::getTableName().'.*')
                    ->first();
                $tours = Tour
                    ::where('start_loc', $location->id)
                    ->get();
                $listAnsOfNotFactor = $adviceDetails->filter(function($row){
                    return !in_array($row->id_question, Question::$_rootQuestionID);
                });
                if($listAnsOfNotFactor->count() > 0){

                }
                else{
                    $question = Question::find(5);
                    $listHistory = Question
                        ::whereIn('id', $adviceDetails->lists('id_question'))
                        ->get();
                }
                return View::make('advice.view', [
                    'select' => 'next',
                    'question' => $question,
                    'listHistory' => $listHistory,
                    'id' => $id,
                    'tours' => $tours
                ]);
            }
        }
        else if ($idQuestion == 'root'){
            $questions = Question::whereIn('id', Question::$_rootQuestionID)
                ->get();
            $listHistory = new Collection();
            return View::make('advice.view', [
                'select' => 'root',
                'questions' => $questions,
                'listHistory' => $listHistory,
                'id' => $id
            ]);
        }
        else{

        }
    }
}