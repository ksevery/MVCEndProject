<?php
$cnf['namespaces']['Controllers'] = '../Controllers/';
$cnf['namespaces']['Controllers\Admin'] = '../Controllers/Admin/';
$cnf['namespaces']['Models'] = '../Models/';
$cnf['namespaces']['Views'] = '../Views/';
$cnf['namespaces']['Annotations'] = '../Annotations/';
$cnf['namespaces']['Data'] = '../Data/';

$cnf['views'] = '../Views/';
$cnf['default_controller'] = 'home';
$cnf['default_method'] = 'index';
$cnf['displayExceptions'] = true;

$cnf['session']['autostart'] = true;
$cnf['session']['type'] = 'native';
$cnf['session']['name'] = 'sess';
$cnf['session']['lifetime'] = 3600;
$cnf['session']['path'] = '/';
$cnf['session']['domain'] = '';
$cnf['session']['secure'] = false;

$cnf['identity']['userClass'] = 'Models\ApplicationUser';

return $cnf;