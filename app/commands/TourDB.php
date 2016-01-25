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

class TourDB extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:tour-db';

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
        $data = DB::select('select * from tour');
        $insert = [];
        foreach ($data as $d) {
            $insert[] = [
                'code' => $d->id_tour,
                'name' => $d->name,
                'description' => $d->description,
                'day_duration' => $d->day_duration,
                'duration' => $d->duration,
                'start_date' => $d->start_date,
                'start_loc' => $d->starting_gate,
                'adult_price' => $d->adult_price,
                'children_price' => $d->children_price,
            ];
        }
        Tour::insert($insert);
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