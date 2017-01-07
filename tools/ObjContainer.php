<?php

//database
require_once 'cls_mysql.php';
$config_data = parse_ini_file('config/database.ini');
Flight::register('db', 'cls_mysql', array($config_data['host'], $config_data['username'], $config_data['password'], $config_data['database']));
Flight::register('slave', 'cls_mysql', array($config_data['slave_host'], $config_data['slave_username'], $config_data['slave_password'], $config_data['slave_database']));
// Flight::register('express', 'cls_mysql', array($config_data['express_host'], $config_data['express_username'], $config_data['express_password'], $config_data['express_database']));
Flight::register('addressdb', 'cls_mysql', array($config_data['address_host'], $config_data['address_username'], $config_data['address_password'], $config_data['address_database']));
Flight::register('dmpdb', 'cls_mysql', array($config_data['dmp_host'], $config_data['dmp_username'], $config_data['dmp_password'], $config_data['dmp_database']));
Flight::register('omsdb', 'cls_mysql', array($config_data['oms_host'], $config_data['oms_username'], $config_data['oms_password'], $config_data['oms_database']));
Flight::register('omsjjwdb', 'cls_mysql', array($config_data['oms_jjw_host'], $config_data['oms_jjw_username'], $config_data['oms_jjw_password'], $config_data['oms_jjw_database']));

require_once 'cls_inventory_tool.php';
require_once 'cls_shipment_service_tool.php';
require_once 'cls_mail_tool.php';
require_once 'cls_route_service_tool.php';
require_once 'cls_mms_tool.php';
require_once 'cls_wms_service_tool.php';
Flight::register('inventory', 'cls_inventory_tool', array());
Flight::register('shipmentService','cls_shipment_service_tool',array());
Flight::register('routeService','cls_route_service_tool',array());
Flight::register('mmsService','cls_mms_tool',array());
Flight::register('wmsService','cls_wms_service_tool',array());


require_once 'cls_basic_data.php';
require_once 'HttpClient.php';
require_once 'config/master_config.php';
require_once 'cls_wmslog_tool.php';
Flight::map('checkParamMatchRegex',array("Tools\ClsUtilsTools","checkStringMatchRegex"));
Flight::map('generateBarCode',array("Tools\ClsUtilsTools","generateBarCode"));
Flight::map('checkParamNotNull', array("Tools\\ClsUtilsTools", "checkStringNotNull"));
Flight::map('jsonToObject', array("Tools\\ClsUtilsTools", "jsonToObject"));
Flight::set('master_config', $master_config);

Flight::map('createRouteLog',array("Tools\\ClsWmsLogTools","cbCreateRouteLog"));
Flight::map('getRouteLogInfo',array("Tools\\ClsWmsLogTools","cbGetRouteLogInfo"));

?>
