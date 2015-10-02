<?php
$cnf['namespaces']['MvcProject\Controllers'] = 'C:\xampp\htdocs\MVCProject\MVCEndProject\Project\Controllers\\';
$cnf['default_controller'] = 'home';
$cnf['default_method'] = 'index';

$cnf['session']['autostart'] = true;
$cnf['session']['type'] = 'native';
$cnf['session']['name'] = 'sess';
$cnf['session']['lifetime'] = 3600;
$cnf['session']['path'] = '/';
$cnf['session']['domain'] = '';
$cnf['session']['secure'] = false;

return $cnf;