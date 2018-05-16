<?php

require_once __DIR__ . '/../src/Url.php';


use Publixe\Url;


$url_string = 'https://user:password@domain.ltd:443/folder/script.ext';

$url = new Url($url_string);

var_dump($url);
var_dump($url -> getRelativeUrl());
var_dump($url -> getUrl());
var_dump($url -> getAbsoluteUrl());
var_dump((string) $url);
