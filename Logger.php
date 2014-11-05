<?php

namespace NewsBot;

class Logger {

  protected $hStdOut;

  public function __construct() {
    $this->hStdOut = null;
  }

  public function close() {
    if (is_resource($this->hStdOut)) fclose($this->hStdOut);
  }

  public function open() {
    $this->close();
    $this->hStdOut = fopen("php://stdout", "w");
  }

  public function rotateLogs() {
    $this->writeLine("rotateLogs()");
  }

  public function write($text, $timestamp = true) {
    if (!is_resource($this->hStdOut)) $this->open();
    $bytes_written = 0;
    if ($timestamp) {
      $dt = new \DateTime("now", Common::$timezone);
      $bytes_written += fwrite($this->hStdOut, "\033[1;37m[" . $dt->format("m/d/Y g:i:s A T") . "]\033[0;0m ");
    }
    $bytes_written += fwrite($this->hStdOut, $text);
    return $bytes_written;
  }

  public function writeLine($text, $timestamp = true) {
    return $this->write($text . "\033[0;0m" . PHP_EOL, $timestamp);
  }

}
