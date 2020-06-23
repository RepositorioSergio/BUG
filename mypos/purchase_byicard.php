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
$cart = new \Mypos\IPC\Cart; 
$cart->add('Some Book', 1, 9.99);
$cart->add('Some other book', 1, 4.56); 
$cart->add('Discount', 1, -2.05);
$purchase = new \Mypos\IPC\PurchaseByIcard($cnf); 
$purchase->setUrlCancel('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchasecancel.php');
$purchase->setUrlOk('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchaseok.php');
$purchase->setUrlNotify('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchasenotify.php');
$purchase->setOrderID(uniqid());
$purchase->setCurrency('EUR'); 
$purchase->setEmail('demo@demo.demo'); 
$purchase->setCart($cart); 
 
try{ 
    $purchase->process(); 
}catch(\Mypos\IPC\IPC_Exception $ex){ 
    echo $ex->getMessage(); 
}
