<?php

namespace NewsBot;

class SlackService extends Service {

  protected $subdomain;
  protected $api_token;
  protected $channel;

  public function __construct(&$config) {
    parent::__construct($config);
    $this->subdomain = (string)$config->subdomain;
    $this->api_token = (string)$config->api_token;
    $this->channel   = (string)$config->channel;
  }

  public function dispatch(&$new_articles) {
    if (!$this->active) return;
    foreach ($new_articles as $article) {
      if ($this->filter_by_keywords && !$this->matchKeywords($article)) continue;
      $message = $this->prepareMessage($article);
      if ($message) $this->sendMessage($message);
      if ($this->filter_by_top_article) break;
    }
  }

  protected function getUrl() {
    return "https://" . $this->subdomain . ".slack.com"
      . "/services/hooks/slackbot"
      . "?token=" . urlencode($this->api_token)
      . "&channel=" . urlencode($this->channel);
  }

  public function prepareMessage(Article &$article) {
    $definition = $this->getMessageDefinition();
    if (is_null($definition)) return false;
    
    $message          = new Message();
    $message->message = $definition->format;
    
    $message->message = str_replace("{feed.label}"         , $article->feed->getLabel()                         , $message->message);
    $message->message = str_replace("{article.title}"      , $article->title                                    , $message->message);
    $message->message = str_replace("{article.description}", $article->description                              , $message->message);
    $message->message = str_replace("{article.link}"       , $article->link                                     , $message->message);
    $message->message = str_replace("{article.guid}"       , $article->guid                                     , $message->message);
    $message->message = str_replace("{article.pubDate}"    , $article->pubDate->format($definition->date_format), $message->message);

    return $message;
  }

  public function sendMessage(Message &$message) {
    $response = Common::curlRequest($this->getUrl(), $message->message);
    Common::$logger->writeLine("\033[1;32mSent Slack notification.\033[0;0m");
    return ($response && $response->code == 200);
  }

}
