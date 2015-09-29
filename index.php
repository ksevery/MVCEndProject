<?php
$uri = $_SERVER['REQUEST_URI'];
$self = $_SERVER['PHP_SELF'];
$index = basename($self);

$directories = str_replace($index, '', $self);

$requestString = str_replace($directories, '', $uri);

var_dump($requestString);

