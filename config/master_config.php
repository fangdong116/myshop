<?php
$master_config = array(
    "wms_config" => array(
        "wms_key" => "yqphhkey",
        "wms_cancel_order_url" => "http://testopenwms.yqphh.com/order/%s/cancel",
        "wms_recover_order_url" => "http://testopenwms.yqphh.com/order/%s/recover",
        "wms_update_order_address_url" => "http://testopenwms.yqphh.com/order/%s/update_address",
        "wms_get_order_url" => "http://testopenwms.yqphh.com/order/%s/shipment_info",
    ),
    "ti_supplier" =>array(
        "product_supplier_id" => "1000",
        "product_supplier_name" => "嘉兴速达",
    ),
    "inventory_config" => array(
        "inventory_key" => "ibenbenkey",
        "inventory_url" => "http://testwmsinventoryapi.yqphh.com/inventory/create_inventory_transaction",
        "inventory_transaction_collection_url" => "http://testwmsinventoryapi.yqphh.com/inventory/create_inventory_transaction_collection",
        "get_inventory_product_url" => "http://testwmsinventoryapi.yqphh.com/inventory/%facility_id/%inventory_status/%product_type/%inventory_type/product_list/",
        "get_inventory_movement_url" => "http://testwmsinventoryapi.yqphh.com/inventory/movement_inventory_list",
        "create_return_in_inventory_transaction_collection_url" => "http://testwmsinventoryapi.yqphh.com/inventory/create_return_in_inventory_transaction_collection",
        "inventory_reserve_url" => "http://testwmsinventoryapi.yqphh.com/inventory/reserve",
        "inventory_cancel_reserve_url" => "http://testwmsinventoryapi.yqphh.com/inventory/cancel_reserve",
        "get_product_list_by_secret_key_url" => "http://testwmsinventoryapi.yqphh.com/inventory/%secret_key/product_list",
        "get_inventory_reserve_product_url" => "http://testwmsinventoryapi.yqphh.com/inventory/%facility_id/%inventory_status/%product_type/%inventory_type/locked_product_list/",
        "get_inventory_batch_product_url" => "http://testwmsinventoryapi.yqphh.com/inventory/%facility_id/%inventory_status/%product_type/%inventory_type/batch_product_list/",
        "get_inventory_container_url" => "http://testwmsinventoryapi.yqphh.com/inventory/containerOrBatchSn/list/",
        "get_create_variance_inventory_url" => "http://testwmsinventoryapi.yqphh.com/inventory/create_variance_inventory_transaction_collection",
        "create_inventory_batch_url" => "http://testwmsinventoryapi.yqphh.com/inventory/create_inventory_transaction_collection_batch",
        "package_to_raw_material_url" => "http://testwmsinventoryapi.yqphh.com/inventory/create_inventory_transaction_collection_batch",
        "get_inventory_time_product_url" => "http://testwmsinventoryapi.yqphh.com/inventory/%facility_id/%inventory_status/%product_type/%inventory_type/batch_container_product_list",
        "get_in_finished_reserve_inventory_url" => "http://testwmsinventoryapi.yqphh.com/inventory/%secret_key/inventory_finished_list",
        "create_supplier_sale_return_url" => "http://testwmsinventoryapi.yqphh.com/inventory/create_supplier_sale_return_in_inventory_transaction_collection",
        "inventory_transaction_product_list"=>"http://testwmsinventoryapi.yqphh.com/inventory/inventory_transaction_product_list",     //获取库存
        "inventory_transaction_product_container_list"=>"http://testwmsinventoryapi.yqphh.com/inventory/inventory_transaction_product_container_list",     //获取库存明细
        "locked_product_list"=>"http://testwmsinventoryapi.yqphh.com/inventory/%facility_id/%inventory_status/goods/%inventory_type/locked_product_list",     //获取库存预定明细
        "inventory_transaction_product_facility_batch"=>"http://testwmsinventoryapi.yqphh.com/inventory/inventory_transaction_product_facility_batch",     //获取库存批次明细
        "inventory_transaction_product_detail_list"=>"http://testwmsinventoryapi.yqphh.com/inventory/inventory_transaction_product_detail_list",     //获取库存流水
    ),
    'service_config' => array(
        "route"=>array(
            "service_url"=>"testroute.service.yqphh.com",
            "service_key"=>"xpq9SkMt8bzCuOqNH6U1wXkgJAubIbLO",
        ),
    ),
    "express_service_config" => array(
        "host" => "http://testexpress.yqphh.com",
        "appId" => "WMS",
        "appSecret" => "wmsexpressKey",
        "recover_url" => "http://testexpress.yqphh.com/express/order/api/recover",
        "cancel_url" => "http://testexpress.yqphh.com/express/order/api/cancel",
        "change_shipping_facility_url" => "http://testexpress.yqphh.com/express/order/api/changeShippingFacility",
        "add_url" => "http://testexpress.yqphh.com/express/order/api/add",
        "leave_center_url" => "http://testexpress.yqphh.com/express/order/api/leaveCenter",
        "print_url" => "http://testexpress.yqphh.com/express/order/out/print",
        "reprint_url" => "http://testexpress.yqphh.com/express/order/api/relprint"
    ),
    "user_account_mail_config" => array(
        'host' => 'smtp.exmail.qq.com',
        'port' => 465,
        'username' => 'verification@ibenben.com',
        'password' => 'Suda630_9UHV67baS',
        'from' => 'verification@ibenben.com',
        'from_name' => '安全验证'
    ),
    "user_verify_url" => "http://testwms.yqphh.com/login/linkVerify",
    "shipment_service_config"=>array(
        "api_key" => 'EE04F3E904F533F44A97337390553F7F',
        "shipment_base_url"=>"http://testshipment.service.yqphh.com/",
        "lock_shipment"=>"lockShipment",
        "shipment_key"=>"movementConstant",
        "update_shipment"=>"updateShipment",
        "ship_shipment"=>"shipShipment",
        "create_print"=>"createPrint",
        "transfer_shipment"=>"transferShipment",
        "request_express" => "requestExpress",
        "send_oms_shipped_info"=>"sendOms",
        "add_async_task" =>"addAsyncTask"

    ),
    "mms_config"=>array(
        "base_url"=>"http://testmms.yqphh.com/mms/",
    ),
    "wms_service_config"=>array(
        "base_url"=>"http://testwmsservice.yqphh.com",
    ),
    "wms_url" => array(
        "verify_url" => "http://testwms.yqphh.com/login/verifyEmail",
    ),
);
?>

