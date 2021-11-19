<?php
/*
 *  Задание: Есть новость и ссылка на неё, нужно обрезать описание новости до 180
 *  символов, приписать многоточие в конце, а последние два слова и многоточие сделать
 *  ссылкой на полный текст новости.
 *
 * */
  // реализация задания умышленно находится в одном файле для удобства рассмотрения.
  // Определим сущности, действующие лица и инструменты.
  //  сущность над которой работаем.
namespace Domain\Entity {
  class Article {
    public $link;
    public $description;
    public $shortText;
    public function __construct($link, $description){
      $this->link = $link;
      $this->description = $description;
    }
  }
}

// Сначала реализуем интерфейс инструмента обрезателя 
namespace Utility{

  interface ICutter{
    function cropText(string $text): string;
  }

  class DefaultCutter implements ICutter{

    private $letterLimit;

    public function __construct($letterLimit){
      $this->letterLimit = $letterLimit;
    }

    public function cropText(string $text): string{
      $shortText = $this->getShortText($this->letterLimit, $text);
      if ($this->isPartOfWord($this->letterLimit+1, $text))
        $this->setNormalComplected($text, $shortText); 
      return $shortText;
    }

    private function setNormalComplected($text, &$shortText){
      $offset = $this->getOffsetLatestWord($shortText);
      if($offset){
        $letterLimit = $this->letterLimit - (strlen($shortText) - $offset);
        $shortText = $this->getShortText($letterLimit, $text);
      }
    }

    private function getShortText($letterLimit, $text){
      $re = "/^([^|]{0,$letterLimit})[^|]+(.*)$/";
      $matches = [];
      preg_match($re, $text, $matches, PREG_OFFSET_CAPTURE, 0);
      return $matches ? $matches[1][0] : null; 
    }

    private function getOffsetLatestWord($shortText){
      $re = '/(\w+)(\W|)$/'; 
      preg_match($re, $shortText, $matches, PREG_OFFSET_CAPTURE, 0);
      return $matches ? $matches[0][1]: null;
    }

    private function getCharInPosition($position, $text){
      $re = '/^.{' . $position . '}(.)/'; 
      preg_match($re, $text, $matches, PREG_OFFSET_CAPTURE, 0);
      return $matches ? $matches[0][1] : null;
    }

    private function isLetter($char){
      $re = '/(\w)/';
      preg_match($re, $char, $matches, PREG_OFFSET_CAPTURE, 0);
      return $matches ? true : false;
    }

    private function isPartOfWord($position, $text){
      if (strlen($text) <= $position)
        return false;
      $letter = $this->getCharInPosition($position, $text);
      return $this->isLetter($letter); 
    }
  }
}

// действующее лицо  
namespace Domain\Service{

  interface IArticleDesigner{
    function cropDescription(\Domain\Entity\Article $article);
    function addLinkToShortText(\Domain\Entity\Article $article);
  }

  class ArticleDesigner implements IArticleDesigner{
    private $cutter;  

    public function __construct(\Utility\ICutter $cutter){
      $this->cutter = $cutter;
    }
    
    public function cropDescription(\Domain\Entity\Article $article){ 
      $article->shortText = $this->cutter->cropText($article->description);
    }

    private function getLatestTwoWordInfo($shortText){
      $re = '/\w+\W+\w+(\W|)$/'; 
      $matches = [];
      preg_match($re, $shortText, $matches, PREG_OFFSET_CAPTURE, 0);
      return $matches ? ["words" => $matches[0][0], "offset" => $matches[0][1]] : null;
    }

    public function addLinkToShortText(\Domain\Entity\Article $article){
      $wordsInfo = $this->getLatestTwoWordInfo($article->shortText);
      $newLink = '<a href="' . $article->link .'">'. trim($wordsInfo["words"]). '...</a>';  
      $article->shortText = substr_replace($article->shortText, $newLink, $wordsInfo["offset"]);
    }
  };
}

//exit to App/Service
namespace {
  $article = new \Domain\Entity\Article(
    'http://fakenews.com/sport/2021-11-18/42',
    "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."
  );
  $cutter = new \Utility\DefaultCutter(180);
  $artDesigner = new \Domain\Service\ArticleDesigner($cutter);
  $artDesigner->cropDescription($article);
  $artDesigner->addLinkToShortText($article);
  echo $article->shortText;
}
