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
$refund = new \Mypos\IPC\Refund($cnf);
$refund->setAmount(10); 
$refund->setCurrency('EUR'); 
$refund->setOrderID(uniqid()); 
$refund->setTrnref('123456'); 
$refund->setOutputFormat(Mypos\IPC\Defines::COMMUNICATION_FORMAT_XML);
if($refund->process()){ 
    echo "Refund successfull.";
}else{ 
    echo "Refund no successfull.";
}
