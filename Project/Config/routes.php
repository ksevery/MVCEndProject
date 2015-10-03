<?php
const NS = 'namespace';
const CONTROLLERS = 'controllers';
const METHODS = 'methods';
const REQUEST_METHOD = 'request_method';


$cnf['admin'][NS] = 'Controllers\Admin';
$cnf['admin'][CONTROLLERS]['users']['to'] = 'users';
$cnf['admin'][CONTROLLERS]['users'][METHODS]['edit'] = 'update';

$cnf['*'][NS] = 'Controllers';
$cnf['*'][CONTROLLERS]['users'][METHODS]['edit'] = 'sell';
$cnf['*'][CONTROLLERS]['users'][REQUEST_METHOD]['edit'] = 'put';
return $cnf;