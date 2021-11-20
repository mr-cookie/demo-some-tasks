<?php
/*  Задание: Из массива из 100 элементов требуется вывести кол-во последовательных пар одинаковых элементов
 * */
function findQuantitySequencePairs(&$inputArr, &$destMapCounterPairs){
  $idx = 0;
  while ($idx < count($inputArr)){
    $next = null; 
    if ($idx + 1 < count($inputArr))
      $next = $inputArr[$idx+1];

    if ($inputArr[$idx] == $next){
      $key = $inputArr[$idx];
      if (!array_key_exists($key, $destMapCounterPairs))
        $destMapCounterPairs[$key] = 0;
      $destMapCounterPairs[$key]++;
      $idx++;
    }
    $idx++;
  }
}

//use
$inputArr = [12,455,34,12,12,5,6,7,8,923,45,45,45,67,878,5,6,7,7,45,34,34,34,34];
$counterPairs = [];
findQuantitySequencePairs($inputArr, $counterPairs);
print_r($counterPairs);
