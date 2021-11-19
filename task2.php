<?php
/*
 *  Задание: из исходного квадратного изображения получить новое с размерами 200x100 (WxH) и с сохранением пропорции исходного изображения.
 * */
namespace Tools\PrepareParams {

  function getSizeSide($newWidth, $newHeight){
    return $newWidth >= $newHeight ? $newHeight : $newWidth;
  }

  function getSquareSizePosParams($origWidth, $origHeight, $newWidth, $newHeight){
    $retParams = [];
    $sqSide = getSizeSide($newWidth, $newHeight);
    $ratio = max($sqSide/$origWidth, $sqSide/$origHeight);
    $h = ceil($newHeight / $ratio);
    $x = ($newHeight/2);
    $w = ceil($newHeight / $ratio);  
    return [
      "width" => $sqSide,
      "height" => $sqSide,
      "x" => $x
    ];
  }
}

namespace Tools\PNGBanner{

  function getImageInfo($fnOrigImg){
    if (!file_exists($fnOrigImg))
      return null;
    $params = [];
    $params['image'] = imagecreatefrompng($fnOrigImg);
    $params['size'] = getimagesize($fnOrigImg);
    return $params;
  }

  function createBlank($newWidth, $newHeight){
    $blank = imagecreatetruecolor($newWidth, $newHeight); 
    imagealphablending($blank, false);
    imagesavealpha($blank, true);
    return $blank;
  }

  function create($inputFilename, $outFilename, $newWidth, $newHeight ){
    $origImgInfo = getImageInfo($inputFilename);
    if (!$origImgInfo){
      echo "\nfile $inputFilename not found.\n";
      exit();
    }

    $squareSizeSide = \Tools\PrepareParams\getSizeSide($newWidth, $newHeight);
    $smallImg = imagescale($origImgInfo['image'], $squareSizeSide, -1);
    $sizePosSmall = \Tools\PrepareParams\getSquareSizePosParams(
      $origImgInfo['size'][0],
      $origImgInfo['size'][1],
      $squareSizeSide, 
      $squareSizeSide
    );

    $blankImg = createBlank($newWidth, $newHeight);
    imagecopy(
      $blankImg,
      $smallImg,
      $sizePosSmall['x'],0,
      0, 0,
      $squareSizeSide, $squareSizeSide, 
    );
    $result = imagepng($blankImg, $outFilename, 0); 
    imagedestroy($origImgInfo['image']);
    imagedestroy($smallImg);
    imagedestroy($blankImg);
    return $result;
  }
}

namespace {
  $ret = \Tools\PNGBanner\create("doge.png", "banner-200x100.png", 200, 100);
  var_dump($ret);
}
