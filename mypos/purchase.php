<?php
require_once './IPC/Loader.php';
$cnf = new \Mypos\IPC\Config(); 
$cnf->setIpcURL('https://www.mypos.eu/vmp/checkout-test'); 
$cnf->setLang('en'); 
$cnf->setPrivateKeyPath(dirname(__FILE__) . '/keys/store_private_key.pem'); 
$cnf->setAPIPublicKeyPath(dirname(__FILE__) . '/keys/api_public_key.pem');
$cnf->setKeyIndex(1); 
$cnf->setSid('000000000000010'); 
$cnf->setVersion('1.4'); 
$cnf->setWallet('61938166610');
$customer = new \Mypos\IPC\Customer(); 
$customer->setFirstName('John'); 
$customer->setLastName('Smith'); 
$customer->setEmail('demo@demo.demo'); 
$customer->setPhone('+351962839246'); 
$customer->setCountry('BGR'); 
$customer->setAddress('Business Park Varna'); 
$customer->setCity('Varna'); 
$customer->setZip('9000');
//echo "CUSTOMER: " . $cnf->getSid();
$cart = new \Mypos\IPC\Cart; 
$cart->add('Some Book', 1, 9.99);
$cart->add('Some other book', 1, 4.56); 
$cart->add('Discount', 1, -2.05);
//echo "<br/>TOTAL: " . $cart->getTotal();
$purchase = new \Mypos\IPC\Purchase($cnf); 
$purchase->setUrlCancel('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchasecancel.php');
$purchase->setUrlOk('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchaseok.php'); 
$purchase->setUrlNotify('https://linux-vyco.demos.bug-software.com/specialtours/mypos/purchasenotify.php');
$purchase->setOrderID(uniqid());
$purchase->setCurrency('EUR'); 
$purchase->setNote('Some note');
$purchase->setCustomer($customer); 
$purchase->setCart($cart); 

$purchase->setCardTokenRequest(\Mypos\IPC\Purchase::CARD_TOKEN_REQUEST_PAY_AND_STORE); 
$purchase->setPaymentParametersRequired(\Mypos\IPC\Purchase::PURCHASE_TYPE_FULL);
$purchase->setPaymentMethod(\Mypos\IPC\Purchase::PAYMENT_METHOD_BOTH);
//echo $purchase->validate();
try{ 
    $purchase->process(); 
}catch(\Mypos\IPC\IPC_Exception $ex){ 
    echo $ex->getMessage(); 
}
?>