<?php

//database
require_once 'cls_mysql.php';
$config_data = parse_ini_file('config/database.ini');
Flight::register('db', 'cls_mysql', array($config_data['host'], $config_data['username'], $config_data['password'], $config_data['database']));
require_once 'cls_basic_data.php';
require_once 'HttpClient.php';
require_once 'config/master_config.php';
Flight::map('checkParamMatchRegex',array("Tools\\ClsUtilsTools","checkStringMatchRegex"));
Flight::map('generateBarCode',array("Tools\\ClsUtilsTools","generateBarCode"));
Flight::map('checkParamNotNull', array("Tools\\ClsUtilsTools", "checkStringNotNull"));
Flight::map('jsonToObject', array("Tools\\ClsUtilsTools", "jsonToObject"));
Flight::set('master_config', $master_config);
?>
