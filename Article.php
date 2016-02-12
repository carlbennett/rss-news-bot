<?php

namespace NewsBot;

use \DateTime;
use \NewsBot\Common;
use \NewsBot\Feed;
use \SimpleXMLElement;

class Article {

  public $feed;
  public $title;
  public $link;
  public $pubDate;
  public $description;
  public $guid;

  public function __construct(Feed &$feed, SimpleXMLElement &$item) {
    $this->feed    = $feed;
    $this->title   = (string)$item->title;
    $this->link    = (string)$item->link;
    
    if (isset($item->description)) {
      $this->description = (string)$item->description;
    } else if (isset($item->content)) {
      $this->description = (string)$item->content;
    } else {
      $this->description = null;
    }
    
    if (isset($item->guid)) {
      $this->guid = (string)$item->guid;
    } else if (isset($item->id)) {
      $this->guid = (string)$item->id;
    } else {
      $this->guid = null;
    }

    if (isset($item->pubDate)) {
      $this->pubDate = (string)$item->pubDate;
    } else if (isset($item->updated)) {
      $this->pubDate = (string)$item->updated;
    } else {
      $this->pubDate = null;
    }

    $this->pubDate = new DateTime(
      (string)$this->pubDate, Common::$timezone
    );
  }

  public function compare(self &$target) {
    if (stripos($this->title, $target->title) !== false) return true;
    if (stripos($target->title, $this->title) !== false) return true;
    if (stripos($this->description, $target->description) !== false) return true;
    if (stripos($target->description, $this->description) !== false) return true;
    if (strtolower($this->link) == strtolower($target->link)) return true;
    return false;
  }

  public function matchKeyword($keyword) {
    if (stripos($this->title, $keyword) !== false) return true;
    if (stripos($this->description, $keyword) !== false) return true;
    if (stripos($this->link, $keyword) !== false) return true;
    return false;
  }

}
