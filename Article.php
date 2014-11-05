<?php

namespace NewsBot;

class Article {

  public function __construct(Feed &$feed, \SimpleXMLElement &$item) {
    $this->feed        = $feed;
    $this->title       = (string)$item->title;
    $this->description = (string)$item->description;
    $this->link        = (string)$item->link;
    $this->guid        = (string)$item->guid;
    $this->pubDate     = new \DateTime((string)$item->pubDate, Common::$timezone);
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
