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

    const VC = 100;

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
                    $arrAnsPoint = $this->scoreAnswer($listAnsOfNotFactor->lists('id_answer'));
                    $arrAnsPoint = $this->interaction($arrAnsPoint);
                    echo '<pre>';
                    print_r($arrAnsPoint);
                    echo '</pre>';
                    exit;
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

    private function scoreAnswer($listAnswer){
        $listAns = Answer::whereNotNull('id_attribute')->get();
        $arrAnsPoint = [];
        foreach($listAns as $ans){
            $arrAnsPoint[$ans->id] = [
                'point' => null,
                'level' => null,
                'interactions' => [],
                'attribute' => $ans->id_attribute
            ];
        }
        foreach($listAnswer as $ans){
            $arrAnsPoint[$ans]['point'] = 100;
            $arrAnsPoint[$ans]['level'] = 1;
        }
        return $arrAnsPoint;
    }

    private function interaction($listAnsPoint){
        foreach($listAnsPoint as $idAns=>$ansPoint){
            if($ansPoint['level']){
                $listAnsPoint = $this->interactionLoop($listAnsPoint, $idAns, $ansPoint['point']);
            }
        }
        $idAttrForNext = $this->getAttributeForNext($listAnsPoint);
        echo '<pre>';
        print_r($listAnsPoint);
        echo '</pre>';
    }

    private function interactionLoop($listAnsPoint, $idAnswer, $point, $level = 2){
        $interactions = Interaction::where('id_answer_from', $idAnswer)->get();
        foreach($interactions as $in){
            $idAnsTo = $in->id_answer_to;
            $ansPointTo = &$listAnsPoint[$idAnsTo];
            $hasUpdatePoint = false;
            try {
                if (count($ansPointTo['interactions']) == 0) {
                    if (!$ansPointTo['level'] || $ansPointTo['level'] > $level) {
                        jumpLevel:
                        $ansPointTo['point'] = $point * $in->point;
                        $ansPointTo['level'] = $level;
                        $ansPointTo['interactions'] = [$point, $in->point];
                        $hasUpdatePoint = true;
                    }
                } else {
                    if ($ansPointTo['level'] == $level) {
                        $ansPointTo['interactions'][] = [$point, $in->point];
                        $sumPoint = 0;
                        $sumInter = 0;
                        foreach ($ansPointTo['interactions'] as $interPoint) {
                            $sumPoint += $interPoint[0] * $interPoint[1];
                            $sumInter += $interPoint[1];
                        }
                        $ansPointTo['point'] = $sumPoint / $sumInter;
                        $hasUpdatePoint = true;
                    } else if ($ansPointTo['level'] > $level) {
                        goto jumpLevel;
                    }
                }
            }
            catch(Exception $e){

            }
            if($hasUpdatePoint) {
                $listAnsPoint = $this->interactionLoop($listAnsPoint, $idAnsTo, $ansPointTo['point'], $level + 1);
            }
        }
        return $listAnsPoint;
    }

    private function getAttributeForNext($listAnsPoint){
        $getInteractionCount =  function($idAttribute) {
            $interactionTable = Interaction::getTableName();
            return Answer::where('id_attribute', $idAttribute)
                ->join(
                    $interactionTable,
                    $interactionTable.'.id_answer_from', '=',
                    Answer::getTableName().'.id'
                )
                ->count(
                    DB::raw('DISTINCT '.$interactionTable.'.id_answer_to')
                );
        };

        $tempScoreAttribute = [];
        foreach($listAnsPoint as $ansPoint){
            if (empty($tempScoreAttribute[$ansPoint['attribute']])) {
                $tempScoreAttribute[$ansPoint['attribute']] = [
                    'hasScore' => 0,
                    'numAns' => 0,
                    'levelMin' => self::VC,
                    'numInter' => $getInteractionCount($ansPoint['attribute']),
                    'id' => $ansPoint['attribute'],
                ];
            }
            $tempScoreAttribute[$ansPoint['attribute']]['numAns']++;
            if($ansPoint['point'] !== null){
                $tempScoreAttribute[$ansPoint['attribute']]['hasScore']++;
                $tempScoreAttribute[$ansPoint['attribute']]['levelMin'] =
                    min($tempScoreAttribute[$ansPoint['attribute']]['levelMin'], $ansPoint['level']);
            }
        }

        usort($tempScoreAttribute, function($a, $b) use ($getInteractionCount){
            $conA = $a['numAns'] - $a['hasScore'];
            $conB = $b['numAns'] - $b['hasScore'];

            return $conB > $conA ||
                ($conB == $conA && $b['numInter'] > $a['numInter']) ||
                ($conB == $conA && $b['numInter'] == $a['numInter'] && $b['levelMin'] > $a['levelMin']);
        });

        foreach($tempScoreAttribute as &$a){
            $a['numInter'] = $getInteractionCount($a['id']);
        }
        echo '<pre>';
        print_r($tempScoreAttribute);
        echo '</pre>';
        exit;
    }
}