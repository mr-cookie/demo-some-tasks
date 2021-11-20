<?php
/*  Задание: Из массива из 100 элементов требуется вывести кол-во последовательных пар одинаковых элементов
 * */
function findQuantitySequencePairs(&$inputArr, &$destMapCounterPairs){
  $idx = 0;
  while ($idx < count($destArr)){
    $next = null; 
    if ($idx + 1 < count($destArr))
      $next = $destArr[$idx+1];

    if ($destArr[$idx] == $next){
      $key = $destArr[$idx];
      if (!array_key_exists($key, $srcMapCounterPairs))
        $srcMapCounterPairs[$key] = 0;
      $srcMapCounterPairs[$key]++;
      //$idx++;
    }
    $idx++;
  }
}

//use
$inputArr = [12,455,34,12,12,5,6,7,8,923,45,45,45,67,878,5,6,7,7,45,34,34,34,34];
$counterPairs = [];
findQuantitySequencePairs($inputArr, $counterPairs);
print_r($counterPairs);
