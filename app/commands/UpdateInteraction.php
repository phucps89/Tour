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

class UpdateInteraction extends Command {

    protected $_owlData;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'command:interaction-up';

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
        $this->collectDataOWL();
//        print_r($this->_owlData);exit;
        $this->info('Getting transaction from api');
        $response = \Httpful\Request::get('http://survey.khachvip.vn/export')->send();


        $listTransactionRaw = $response->body->transaction->old;
        $listTransactionDump = new \Illuminate\Support\Collection();

        $this->info('Number of root transaction: ' . count($listTransactionRaw));
        $this->info('Dump transaction');
        foreach ($listTransactionRaw as $transaction) {
            $arrTrans = [];
            foreach($transaction as $item){
                $data[] = $item;
                foreach ($this->_owlData[$item] as $owlIndex => $owlValue) {
                    if($owlValue > 0){
                        $data[] = $owlIndex;
                    }
                }
                $arrTrans[] = $data;
                $data = [];
            }
            //Get bien the transaction
            $this->dumpTransaction($arrTrans, $transaction, $result);
//            $this->info('So dump: '. count($result));
            foreach($result as $trans){
                $valueTransaction = $this->getValueTransaction($trans, $transaction);
                if($valueTransaction >= 0.5)
                {
                    $listTransactionDump->push(new TransactionModel($trans, $valueTransaction));
//                    echo $listTransactionDump->count().PHP_EOL;
//                    print_r($trans);
//                    sleep(1);
                }
            }
            unset($result);
            unset($arrTrans);

        }

        $this->info('Ket thuc');
        echo $listTransactionDump->count().PHP_EOL;
        $this->info('Quy dong');

        $finalList = $this->quyDongTransaction($listTransactionDump);
        $finalList = $this->applyNewData($finalList);
        $length = $finalList->count();
        $this->info($length);

        $fileInput = public_path('zz1.txt');
        if(file_exists($fileInput)) unlink($fileInput);
        $fileOutput = public_path('zz2.txt');
        foreach($finalList as $index=>$list){
            if($list) {
                if($index < $length - 1)
                    file_put_contents($fileInput, implode(' ', $list) . "\n", FILE_APPEND);
                else
                    file_put_contents($fileInput, implode(' ', $list), FILE_APPEND);
            }
        }
        $cmd = 'java -jar public/spmf.jar run FPGrowth_association_rules '.$fileInput.' '.$fileOutput.' 10% 30%';
        exec($cmd, $output);
        print_r($output);
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

    private function collectDataOWL()
    {
        Excel::load(storage_path('owl.xls'), function(\Maatwebsite\Excel\Readers\LaravelExcelReader $reader) {
//            $num = $reader->get()->count();
//            for($i=1; $i <= $num; $i++){
//
//            }
            $reader->each(function(\Maatwebsite\Excel\Collections\CellCollection $row){
                $indexRow = $row->first();
                $data = [];
                $row->shift();
                foreach($row as $index=>$value){
                    $index = strtoupper($index);
                    if(strcmp($index, $indexRow) != 0) {
                        $data[strtoupper($index)] = $value;
                    }
                }
                $this->_owlData[$indexRow] = $data;
            });
        });
    }

    private function dumpTransaction($list, $rootTransaction, &$result = [], $item = [], $index = 0){
        foreach ($list[$index] as $i => $so){
//            @$this->info($rootTransaction[$index] . ' So voi ' . $so . ' : ' . $this->_owlData[$rootTransaction[$index]][$so]);
            if($i == 0) {
                $item[] = $so;
            }
            else if($this->_owlData[$rootTransaction[$index]][$so] >= 0.5) {
                $item[] = $so;
            }
            else{
                return;
            }

            if($index < count($list) - 1){
                $this->dumpTransaction($list, $rootTransaction, $result, $item, $index + 1);
            }
            else {
                $result[] = $item;
            }
            unset($item[count($item) - 1]);
            $item = array_values($item);
        }
    }

    private function getValueTransaction($transaction, $transactionRoot)
    {
        if($transaction == $transactionRoot)    return 1;
        $value = 1;
        foreach ($transaction as $index=>$item) {
//            print_r($index);echo "\n";
//            print_r($transactionRoot);echo "\n";
//            print_r($this->_owlData[$transactionRoot[$index]]);
            if($item != $transactionRoot[$index]) {
                $value *= $this->_owlData[$transactionRoot[$index]][$item];
            }
        }
        return round($value, 2);
    }

    private function quyDongTransaction($listTransactionDump)
    {
        $finalList = new \Illuminate\Support\Collection();
        foreach ($listTransactionDump as $item) {
            $times = 100 * $item->getValue();
            for($i=0; $i<$times; $i++){
                $finalList->push($item->getTransaction());
            }
        }
        return $finalList;
    }

    private function applyNewData($listTransactionDump)
    {
        $newData = Answer::whereNotNull('id_attr_map')->get();
        $mapData = $newData->lists('id', 'id_attr_map');
        $finalList = new \Illuminate\Support\Collection();
        foreach($listTransactionDump as $trans){
            $mapTrans = [];
            foreach($trans as $item){
                $mapTrans[] = $mapData[$item];
            }
            $finalList->push($mapTrans);
        }

        return $finalList;
    }

}