<?php
const NS = 'namespace';
const CONTROLLERS = 'controllers';
const METHODS = 'methods';


$cnf['admin'][NS] = 'MvcProject\Controllers\Admin';
$cnf['admin'][CONTROLLERS]['items']['to'] = 'data';
$cnf['admin'][CONTROLLERS]['items']['methods']['edit'] = 'update';

$cnf['*'][NS] = 'MvcProject\Controllers';
$cnf['*'][CONTROLLERS]['people']['to'] = 'users';
$cnf['*'][CONTROLLERS]['people'][METHODS]['edit'] = 'update';
return $cnf;