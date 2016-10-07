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

class DBTransaction extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:dbt';

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
        $line = $this->argument('line');
        $fileInput = $line.'.inp.txt';
        $r = 1;
        $record = [];
        while($r <= 30){
            $record[] = $r++;
        }
        while($line){
            $num = rand(5, 15);
            $list = [];
            $temp = $record;
            shuffle($temp);
            while($num){
                $k = array_rand($temp);
                unset($temp[$k]);
                $num--;
            }
            file_put_contents(public_path('db/'.$fileInput), implode(' ', $temp) . "\n", FILE_APPEND);
            $line--;
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('line', InputArgument::REQUIRED, 'line', null)
        );
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