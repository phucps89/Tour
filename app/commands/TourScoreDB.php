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

class TourScoreDB extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:tourscore-db';

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
        $data = DB::select('select * from tour_score');
        foreach ($data as $d) {
            $ans = DB::select("select * from option_ans where attribute_id = '{$d->attribute_id}'");
            $ans = $ans[0];

            if($d->tour_id == null || $ans->id == null) continue;

            $exist = TourScore::on('main')
                ->where('id_tour', $d->tour_id)
                ->where('id_answer', $ans->id)
                ->first();
            if($exist) continue;

            $t = Tour::on('main')
                ->find($d->tour_id);
            if(!$t) continue;

            $a = Answer::on('main')
                ->find($ans->id);
            if(!$a) continue;

            $tc = new TourScore();
            $tc->id_tour = $t->id;
            $tc->id_answer = $a->id;
            $tc->score = $d->score;
            try{
                DB::beginTransaction();
                $tc->save();
                DB::commit();
            }
            catch(ErrorException $e){
                DB::rollback();
                echo $e->getMessage()."\n";
            }
            catch(PDOException $e){
                DB::rollback();
                echo $e->getMessage()."\n";
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