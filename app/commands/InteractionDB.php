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

class InteractionDB extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:interaction-db';

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
        $data = DB::select('select * from interaction');
        $insert = [];
        foreach ($data as $d) {
            $ans = DB::select("select * from option_ans where attribute_id = '{$d->attribute_id}'");
            $ans1 = DB::select("select * from option_ans where attribute_id = '{$d->attribute_id1}'");
            if(isset($ans[0]) && isset($ans1[0])) {
                $insert[] = [
                    'id_answer_from' => $ans[0]->id,
                    'id_answer_to'   => $ans1[0]->id,
                    'point'          => $d->point,
                ];
            }
        }
        Interaction::insert($insert);
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