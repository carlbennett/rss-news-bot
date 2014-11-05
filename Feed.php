<?php

namespace NewsBot;

class Feed implements Updateable {

  protected $label;
  protected $last_response;
  protected $last_updated;
  protected $url;

  public function __construct($label, $url) {
    $this->label         = $label;
    $this->last_response = null;
    $this->last_updated  = 0;
    $this->url           = $url;
  }

  public function getLabel() {
    return $this->label;
  }

  public function getLastResponse() {
    return $this->last_response;
  }

  public function getLastUpdated() {
    return $this->last_updated;
  }

  public function parse(\StdClass &$response) {
    $xml = null;
    try {
      $xml = new \SimpleXMLElement($this->last_response->data);
      if (libxml_get_errors()) throw new \Exception();
    } catch (\Exception $e) {
      throw $e;
      return false;
    }
    if (!$xml) return false;
    $new_articles = [];
    try {
      foreach ($xml->channel->item as $item) {
        $article = new Article($this, $item);
        if (Common::findArticle($article)) continue;
        Common::$articles->attach($article);
        $new_articles[] = $article;
      }
    } catch (\Exception $e) {
      throw $e;
      return false;
    }
    return $new_articles;
  }

  public function setLastUpdated($time) {
    $this->last_updated = $time;
  }

  public function update() {
    $this->setLastUpdated(time());
    $this->last_response = Common::curlRequest($this->url, null);
    return $this->last_response;
  }

}
