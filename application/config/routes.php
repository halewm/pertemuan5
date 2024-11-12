<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Route khusus
$route['login/login_act'] = 'login/login_act';
$route['login/lupaPassword'] = 'login/lupaPassword';
$route['login/lupaPassword_act'] = 'login/lupaPassword_act';
