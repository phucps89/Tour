<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/16/2016
 * Time: 9:27 AM
 */

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FactorDB extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:factor-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cronjob run to update level total for category';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        ini_set('max_execution_time', 0);

        $factorQuestions = Question::on('main')
            ->where('id_question_type', QuestionType::TYPE_FACTOR)
            ->get();
        $listIDAnswerFactor = [];
        foreach($factorQuestions as $q){
            $listIDAnswerFactor[] = QuestionAnswer
                ::on('main')
                ->where('id_question', $q->id)
                ->lists('id_answer');
        }

        $listIDAnswerNormal = Question::on('main')
            ->where('id_question_type', '!=', QuestionType::TYPE_FACTOR)
            ->join('question_answer', 'question_answer.id_question', '=', 'questions.id')
            ->distinct('id_answer')
            ->orderBy('id_answer')
            ->lists('id_answer');

        $keyGroup = [];
        foreach($listIDAnswerFactor[0] as $fKey0){
            foreach($listIDAnswerFactor[1] as $fKey1){
                foreach($listIDAnswerFactor[2] as $fKey2){
                    $arrTemp = [$fKey0, $fKey1, $fKey2];
                    sort($arrTemp);
                    $keyGroup[] = $arrTemp;
                }
            }
        }

        $insert = [];
        foreach($keyGroup as $key){
            $keyString = implode(',', $key);
            foreach($listIDAnswerNormal as $idAnswer) {
                $answerRoot = DB::select("select * from option_ans where id='{$idAnswer}'");
                $idAttribute = $answerRoot[0]->attribute_id;
                $factor = DB::select("select * from factor where user_detail = '{$keyString}' and attribute_id='{$idAttribute}'");
                if(count($factor) > 0){
                    $insert[] = [
                        'key_group' => $keyString,
                        'id_answer' => $idAnswer,
                        'factor'    => $factor[0]->factor
                    ];
                }
                else{
                    $insert[] = [
                        'key_group' => $keyString,
                        'id_answer' => $idAnswer,
                        'factor'    => rand(1,3)
                    ];
                }
            }
        }
        Factor::insert($insert);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }

}