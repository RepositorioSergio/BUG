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
 
$purchase = new \Mypos\IPC\Purchase($cnf);
$purchase->setUrlCancel('http://mysite.com/ipc_cancel'); 
$purchase->setUrlOk('http://mysite.com/ipc_ok'); 
$purchase->setUrlNotify('https://mysite.com/ipc_notify'); 
$purchase->setOrderID(uniqid());
$purchase->setCurrency('EUR');
 
$purchase->setCardTokenRequest(\Mypos\IPC\Purchase::CARD_TOKEN_REQUEST_ONLY_STORE);
$purchase->setPaymentParametersRequired(\Mypos\IPC\Purchase::PURCHASE_TYPE_SIMPLIFIED_PAYMENT_PAGE);
 
try{
    $purchase->process();
}catch(\Mypos\IPC\IPC_Exception $ex){    
    echo $ex->getMessage();
}