<?php

namespace NewsBot;

interface Updateable {
  public function getLastUpdated();
  public function setLastUpdated($time);
}
