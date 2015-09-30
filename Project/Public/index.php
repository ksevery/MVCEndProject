<?php
include '../../EndF/Application.php';

ini_set('display_errors', 1);

$app = \EndF\Application::getInstance();
$config = \EndF\Config::getInstance();
$config->setConfigFolder('../Config');
$app->run();




