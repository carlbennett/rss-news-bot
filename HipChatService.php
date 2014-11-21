<?php

namespace NewsBot;

class HipChatService extends Service {

  protected $api_token;
  protected $room_id;

  public function __construct(&$config) {
    parent::__construct($config);
    $this->api_token = (string)$config->api_token;
    $this->room_id   = (string)$config->room_id;
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
    return "https://api.hipchat.com/v2/room/"
      . urlencode($this->room_id)
      . "/notification?auth_token="
      . urlencode($this->api_token);
  }

  public function prepareMessage(Article &$article) {
    $definition = $this->getMessageDefinition();
    if (is_null($definition)) return false;
    
    $message                 = new Message();
    $message->color          = $definition->color;
    $message->message_format = $definition->format[0];
    $message->message        = $definition->format[1];
    $message->notify         = $definition->notify;
    
    $message->message = str_replace("{feed.label}"         , $article->feed->getLabel()                         , $message->message);
    $message->message = str_replace("{article.title}"      , htmlspecialchars($article->title)                  , $message->message);
    $message->message = str_replace("{article.description}", htmlspecialchars($article->description)            , $message->message);
    $message->message = str_replace("{article.link}"       , $article->link                                     , $message->message);
    $message->message = str_replace("{article.guid}"       , $article->guid                                     , $message->message);
    $message->message = str_replace("{article.pubDate}"    , $article->pubDate->format($definition->date_format), $message->message);

    return $message;
  }

  public function sendMessage(Message &$message) {
    $response = Common::curlRequest($this->getUrl(), [
      "color" => $message->color,
      "message" => $message->message,
      "notify" => $message->notify,
      "message_format" => $message->message_format,
    ]);
    Common::$logger->writeLine("\033[1;32mSent HipChat notification.\033[0;0m");
    return ($response && $response->code == 204);
  }

}
