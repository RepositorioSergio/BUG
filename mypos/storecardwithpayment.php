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
 
#Create Purchase object using Config object and set required params
$purchase = new \Mypos\IPC\Purchase($cnf);
$purchase->setUrlCancel('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchasecancel.php'); //User comes here after purchase cancelation
$purchase->setUrlOk('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchaseok.php'); //User comes here after purchase success
$purchase->setUrlNotify('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchasenotify.php'); //IPC sends POST request to this address with purchase status
$purchase->setOrderID(uniqid()); //Some unique ID
$purchase->setCurrency('EUR');
$purchase->setNote('Some note'); //Not required
 
 
#Create Cart object and set cart items
$cart = new \Mypos\IPC\Cart;
$cart->add('Some Book', 1, 9.99); //name, quantity, price
$cart->add('Some other book', 1, 4.56);
$cart->add('Discount', 1, -2.05);
$purchase->setCart($cart);
 
$purchase->setCardTokenRequest(\Mypos\IPC\Purchase::CARD_TOKEN_REQUEST_PAY_AND_STORE);
$purchase->setPaymentParametersRequired(\Mypos\IPC\Purchase::PURCHASE_TYPE_SIMPLIFIED_PAYMENT_PAGE);

try{
    $purchase->process();
}catch(\Mypos\IPC\IPC_Exception $ex){
    echo $ex->getMessage();
}
 