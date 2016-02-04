#!/usr/bin/php
<?php

$config            = new StdClass();
$config->feeds     = json_decode(file_get_contents("./config/feeds.json"));
$config->main      = json_decode(file_get_contents("./config/main.json"));
$config->services  = json_decode(file_get_contents("./config/services.json"));
$config->templates = json_decode(file_get_contents("./config/templates.json"));


