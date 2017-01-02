<?php

/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 10/16/2016
 * Time: 8:48 AM
 */
class OntologyController extends BaseController
{
    private $n, //số đỉnh của đồ thị
        $path = [], //mảng đánh dấu đường đi.
        $Matrix = [], //ma trận trọng số hay ma trận kề của đồ thị
        $xet = []; //mảng đánh dấu đỉnh đã được gán nhãn.

    const VOCUNG = 1000000;

    function index()
    {
        $this->readFile();
        echo '<pre>';
//        print_r($this->Matrix);
        print_r($this->OnSimConcept(3,5));
        echo '</pre>';
        exit;
    }

    private function readFile()
    {
        $handle = fopen(public_path('DIJKSTRA.IN'), "r");
        if ($handle) {
            $firstLine = true;
            while (($line = fgets($handle)) !== false) {
                if ($firstLine) {
                    $firstLine = false;
                    $this->n = (int)$line;
                }
                else {
                    $arrVal = explode(' ', $line);
                    $this->Matrix[] = $arrVal;
                }
            }

            fclose($handle);
        }
        else {
            // error opening the file.
        }
    }

    private function Dijkstra($a, $b)
    {//tính khoảng cách từ đỉnh a đến  đỉnh b
        $u = null;
        $minp = null;
        //khởi tạo nhãn tạm thời cho các đỉnh.
        for ($v = 0; $v < $this->n; $v++) {
            $d[$v] = $this->Matrix[$a][$v];
            $this->path[$v] = $a;
            $this->xet[$v] = false;
        }
        $this->path[$a] = 0;
        $d[$a] = 0;
        $this->xet[$a] = true;
        //bươc lặp
        while (!$this->xet[$b]) {
            $minp = self::VOCUNG;
            //tìm đỉnh u sao cho d[u] là nhỏ nhất
            for ($v = 1; $v < $this->n; $v++) {
                if ((!$this->xet[$v]) && ($minp > $d[$v])) {
                    $u = $v;
                    $minp = $d[$v];
                }
            }
            $this->xet[$u] = true;// u la dinh co nhan tam thoi nho nhat
            if (!$this->xet[$b]) {
                //gán nhãn lại cho các đỉnh.
                for ($v = 1; $v < $this->n; $v++) {
                    if ((!$this->xet[$v]) && ($d[$u] + $this->Matrix[$u][$v] < $d[$v])) {
                        $d[$v] = $d[$u] + $this->Matrix[$u][$v];
                        $this->path[$v] = $u;
                    }
                }
            }
        }
    }

    private function OnSimConcept($a, $b)
    {
        $ama = [];
        $bma = [];// Ma tran chua duong di tu a den 0, b den 0;
        $j = 0;
        $k = 0;//dem so dinh từ 0 đến a và từ 0 đến b
        $dems = 0;
        $dema = 0;
        $demb = 0;
        $this->Dijkstra(0, $a);
        $i = $this->path[0];
        echo '<pre>';
        print_r($i); echo '<br>';
        print_r($a);
        print_r($this->path);
        echo '</pre>';
        exit;
        while ($i != $a) {
            $ama[$j] = $i;
            $i = $this->path[$i];
            $j++;

        }
        echo 'asd';
        $this->Dijkstra(0, $b);
        $i = $this->path[0];
        while ($i != $b) {
            $ama[$k] = $i;
            $i = $this->path[$i];
            $k++;
        }

        while ($ama[$j] == $bma[$k] && $j != 0 && $k != 0) {

            $dem++;
            $j--;
            $k--;
        }

        $dema = $j;
        $demb = $k;echo '123';
        return $dems / ($dems + $dema + $demb);// độ tương tự
    }
}