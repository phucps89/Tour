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

class AnswerMap extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:answer-map';

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
        $answers = Answer::whereNotNull('id_attribute')->get();
        Answer::whereRaw('1=1')->update([
            'id_attr_map' => null
        ]);
        $oldAnswers =  DB::connection('old')->select('select * from option_ans where attribute_id is not null');
        foreach ($answers as $answer) {
            foreach ($oldAnswers as $oldAnswer) {
                if(strcmp($answer->name, $oldAnswer->opt_content) == 0){
                    $data = DB::connection('old')->select('select * from attribute where id = ' . $oldAnswer->attribute_id);
                    $answer->id_attr_map = $data[0]->attribute;
                    $answer->save();
                    echo $answer->name."\n";
                    echo $oldAnswer->opt_content."\n";
                    echo $answer->id_attr_map."\n";
                    echo "---------\n";
                    break;
                }
            }
        }
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