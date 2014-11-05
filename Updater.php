<?php

namespace NewsBot;

class Updater {

  protected $update_interval;

  public function __construct($update_interval) {
    $this->update_interval = ($update_interval < 5 ? 5 : (int)$update_interval);
  }

  public function poll() {
    $now = time();
    $dispatch_articles = [];
    $notify = true;
    foreach (Common::$feeds as $feed) {
      if ($feed->getLastUpdated() + $this->update_interval < $now) {
        if ($feed->getLastUpdated() == 0) $notify = false;
        $response = $feed->update();
        if ($response && $response->code == 200 && !empty($response->data)) {
          Common::$logger->write("\033[0;32mUpdated \033[1;32m" . $feed->getLabel() . "\033[0;32m feed");
          $new_articles = $feed->parse($response);
          if ($new_articles === false) {
            Common::$logger->writeLine("\033[0;31m, but failed to parse it.\033[0;0m", false);
          } else {
            $new_article_count = count($new_articles);
            Common::$logger->writeLine("\033[0;33m, found \033[1;33m" . $new_article_count . "\033[0;33m new article" . ($new_article_count == 1 ? "" : "s") . ".\033[0;0m", false);
            $dispatch_articles = array_merge($dispatch_articles, $new_articles);
          }
        } else {
          Common::$logger->writeLine("Failed to update " . $feed->getLabel() . " feed.");
        }
      }
    }
    if ($notify && $dispatch_articles) Common::dispatchService($dispatch_articles);
  }

}
