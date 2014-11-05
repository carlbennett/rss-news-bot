#!/usr/bin/php
<?php

namespace NewsBot;

if (php_sapi_name() != "cli") {
  throw new \Exception("Application must be run via php-cli");
}

function main($argc, $argv) {

  spl_autoload_register(function($className){
    if (substr($className, 0, 8) != "NewsBot\\") return false;
    require_once("./" . str_replace("\\", "/", substr($className, 8)) . ".php");
  });

  pcntl_signal(SIGINT, function($signo){
    Common::$exit = SIGINT;
    Common::$logger->writeLine("\033[0;31mShutting down...\033[0;0m");
  });

  pcntl_signal(SIGUSR1, function($signo){
    Common::$logger->rotateLogs();
  });

  system("stty -echo");

  register_shutdown_function(function(){
    system("stty echo");
  });

  libxml_use_internal_errors(true);

  Common::$articles = new \SplObjectStorage();
  Common::$exit     = 0;
  Common::$feeds    = new \SplObjectStorage();
  Common::$logger   = new Logger();
  Common::$services = new \SplObjectStorage();
  Common::$settings = json_decode(file_get_contents("./Settings.json"));
  Common::$timezone = new \DateTimeZone(Common::$settings->timezone);
  Common::$updater  = new Updater(Common::$settings->update_interval);

  $feeds_sum = Common::createFeedsFromSettings();
  Common::$logger->writeLine("\033[0;34mInitialized " . number_format($feeds_sum) . " feed" . ($feeds_sum == 1 ? "" : "s") . ".");

  $services_sum = Common::createServicesFromSettings();
  Common::$logger->writeLine("\033[0;34mInitialized " . number_format($services_sum) . " service" . ($services_sum == 1 ? "" : "s") . ".");

  Common::$logger->writeLine("\033[0;34mRSS News Bot ready for duty.\033[0;0m");

  do {
    Common::$updater->poll();
    sleep(1);
    if (!pcntl_signal_dispatch()) {
      throw new \Exception("Signal dispatch failed");
    }
  } while (Common::$exit == 0);

  return Common::$exit;

}

exit(main($argc, $argv));
