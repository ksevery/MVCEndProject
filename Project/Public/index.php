<?php
include '../../EndF/Application.php';

ini_set('display_errors', 1);

//$app = new MvcProject\Application();
//$app->run();
$app = \EndF\Application::getInstance();
$app->run();
