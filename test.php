<?php 
//冒泡排序
$b = array(22,77,44,55,33,11,66);
//统计长度
$len = count($b);
//循环取出每一个
for ($i=1; $i < $len; $i++) { 
    for ($j=0; $j < $len-$i; $j++) { 
        if ($b[$j]>$b[$j+1]) {
            $arr = $b[$j+1];
            $b[$j+1] = $b[$j];
            $b[$j] = $arr;
        }
    }
}
var_dump($b);






















 ?>