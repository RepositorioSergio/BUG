<?php
require_once './IPC/Loader.php';
$cnf = new \Mypos\IPC\Config(); 
$cnf->setIpcURL('https://mypos.eu/vmp/checkout-test/'); 
$cnf->setLang('en'); 
$cnf->setPrivateKeyPath(dirname(__FILE__) . '/keys/store_private_key.pem'); 
$cnf->setAPIPublicKeyPath(dirname(__FILE__) . '/keys/api_public_key.pem'); 
$cnf->setKeyIndex(1); 
$cnf->setSid('000000000000010'); 
$cnf->setVersion('1.0'); 
$cnf->setWallet('61938166610');
$ipcStatus = new Mypos\IPC\GetPaymentStatus($cnf); 
$ipcStatus->setOrderID('1440'); 
$ipcStatus->setOutputFormat(\Mypos\IPC\Defines::COMMUNICATION_FORMAT_JSON);
$result = $ipcStatus->process();
switch($result->getStatus()){ 
    case \Mypos\IPC\Defines::STATUS_SUCCESS: 
        //Display returned data in the site interfase. 
        //Loop and display in table or just print_r response 
        print_r(\Mypos\IPC\Helper::getArrayVal($result->getData(CASE_LOWER), 'orderstatus')); 
        break; 
    case \Mypos\IPC\Defines::STATUS_INVALID_PARAMS: 
        //Order not found or set params are invalid. 
        //Show error. 
        break; 
}
try{ 
    $ipcStatus = new \Mypos\IPC\GetPaymentStatus($cnf); 
    $ipcStatus->setOrderID('1440'); 
    $ipcStatus->setOutputFormat(\Mypos\IPC\Defines::COMMUNICATION_FORMAT_JSON); 
    $result = $ipcStatus->process(); 
 
    switch($result->getStatus()){ 
        case \Mypos\IPC\Defines::STATUS_SUCCESS: 
            print_r(\Mypos\IPC\Helper::getArrayVal($result->getData(CASE_LOWER), 'orderstatus')); 
            break; 
        case \Mypos\IPC\Defines::STATUS_INVALID_PARAMS: 
            echo 'Not found!'; 
            break; 
    } 
}catch(\Mypos\IPC_Exception $ex){ 
    //Display exception message 
    echo $ex->getMessage(); 
}
