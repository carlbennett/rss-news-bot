<?php

namespace NewsBot;

abstract class Service {

  protected $class;
  protected $label;
  protected $active;
  protected $message_id;
  protected $keywords;
  protected $filter_by_keywords;
  protected $filter_by_top_article;

  public function __construct(&$config) {
    $this->class                 = $config->class;
    $this->label                 = $config->label;
    $this->active                = $config->active;
    $this->message_id            = $config->message_id;
    $this->keywords              = $config->keywords;
    $this->filter_by_keywords    = $config->filter_by_keywords;
    $this->filter_by_top_article = $config->filter_by_top_article;
  }

  public function getClass() {
    return $this->class;
  }

  public function matchKeywords(&$article) {
    foreach ($this->keywords as $keyword) {
      if ($article->matchKeyword($keyword)) return true;
    }
    return false;
  }

  public abstract function dispatch(&$new_articles);
  public abstract function prepareMessage(Article &$article);
  public abstract function sendMessage(Message &$message);

}
