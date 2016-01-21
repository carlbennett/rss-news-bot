<?php

namespace NewsBot;

class Common {

  private function __construct(){}

  public static $articles;
  public static $exit;
  public static $feeds;
  public static $logger;
  public static $services;
  public static $settings;
  public static $timezone;
  public static $updater;

  public static function createFeedsFromSettings() {
    $i = 0;
    if (!self::$feeds instanceof \SplObjectStorage) self::$feeds = new \SplObjectStorage();
    foreach (self::$settings->feeds as $feed_object) {
      self::$feeds->attach(new Feed($feed_object->label, $feed_object->url));
      ++$i;
    }
    return $i;
  }

  public static function createServicesFromSettings() {
    $i = 0;
    if (!self::$services instanceof \SplObjectStorage) self::$services = new \SplObjectStorage();
    foreach (self::$settings->services as $obj) {
      $class = "NewsBot\\" . (string)$obj->class;
      self::$services->attach(new $class($obj));
      ++$i;
    }
    return $i;
  }
  
  public static function curlRequest($url, $post_content) {
    $curl = curl_init();
    $time = microtime(true);

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_MAXREDIRS, 10);

    curl_setopt($curl, CURLOPT_URL, $url);

    if (!is_null($post_content) && is_array($post_content)) {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_content));
      curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/json;charset=utf-8"]);
    } else if (is_string($post_content)) {
      curl_setopt($curl, CURLOPT_POST, true);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $post_content);
      curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
    }

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = new \StdClass();
    $response->data = curl_exec($curl);
    $response->code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $response->type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
    $response->time = microtime(true) - $time;

    curl_close($curl);
    return $response;
  }

  public static function dispatchService(&$new_articles) {
    Common::$logger->writeLine("\033[0;33mDispatching services...\033[0;0m");
    foreach (self::$services as $service) {
      $service->dispatch($new_articles);
    }
  }

  public static function findArticle(Article &$article) {
    foreach (self::$articles as $source) {
      if ($source->compare($article)) return true;
    }
    return false;
  }

}
