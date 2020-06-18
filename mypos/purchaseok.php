<?php
require_once './IPC/Loader.php'; 
$cnf = new \Mypos\IPC\Config(); 
$cnf->setIpcURL('https://www.mypos.eu/vmp/checkout-test/'); 
$cnf->setLang('en'); 
$cnf->setPrivateKeyPath(dirname(__FILE__) . '/keys/store_private_key.pem'); 
$cnf->setAPIPublicKeyPath(dirname(__FILE__) . '/keys/api_public_key.pem'); 
$cnf->setKeyIndex(1); 
$cnf->setSid('000000000000010'); 
$cnf->setVersion('1.3'); 
$cnf->setWallet('61938166610');
try{ 
    $responce = \Mypos\IPC\Response::getInstance($cnf, $_POST, \Mypos\IPC\Defines::COMMUNICATION_FORMAT_POST); 
}catch(\Mypos\IPC\IPC_Exception $e){ 
    //Display Some general error or redirect to merchant store home page 
}
$data = $responce->getData(CASE_LOWER); 
$orderid = "";
foreach ($data as $key => $value) {
    if ($key === "orderid") {
        $orderid = $value;
    }
}
if($orderid !== ""){ 
    echo "Your order has been successfully paid."; 
}else{ 
    //Display Some general error or redirect to merchant store home page 
    header('Location: http://linux-vyco.demos.bug-software.com/specialtours/mypos/purchase.php');
    exit();
}