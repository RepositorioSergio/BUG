<?php
require_once './IPC/Loader.php';
$cnf = new \Mypos\IPC\Config(); 
$cnf->setIpcURL('https://www.mypos.eu/vmp/checkout-test/'); 
$cnf->setLang('en'); 
echo "CUSTOMER: 1";
$cnf->setPrivateKeyPath(dirname(__FILE__) . '/keys/store_private_key.pem'); 
$cnf->setAPIPublicKeyPath(dirname(__FILE__) . '/keys/api_public_key.pem'); echo "CUSTOMER: 2";
$cnf->setEncryptPublicKeyPath(dirname(__FILE__) . '/keys/encrypt_key.pem'); 
echo "CUSTOMER: 3";
$cnf->setKeyIndex(1); 
$cnf->setSid('000000000000010'); 
$cnf->setVersion('1.4'); 
$cnf->setWallet('61938166610');
$cart = new \Mypos\IPC\Cart; 
$cart->add('Some Book', 1, 9.99); //name, quantity, price 
$cart->add('Some other book', 1, 4.56); 
$cart->add('Discount', 1, -2.05);
$card = new \Mypos\IPC\Card(); 
$card->setCardType(\Mypos\IPC\Card::CARD_TYPE_VISA); 
$card->setCardNumber('4929131949828217'); 
$card->setExpMM('12'); 
$card->setExpYY('21'); 
$card->setCvc('111'); 
$card->setCardHolder('John Doe'); 
$card->setEci(6); 
$card->setAvv(''); 
$card->setXid('');
$purchase = new \Mypos\IPC\IAPurchase($cnf); 
$purchase->setOrderID(uniqid());
$purchase->setCurrency('EUR'); 
$purchase->setNote('Some note');
$purchase->setCard($card); 
$purchase->setAccountSettlement('11111111119');
$purchase->setCart($cart); 
$purchase->setOutputFormat(Mypos\IPC\Defines::COMMUNICATION_FORMAT_JSON);

$result = $purchase->process(); 
if ($result->getStatus() == \Mypos\IPC\Defines::STATUS_SUCCESS) { 
    echo 'success'; 
} else { 
    echo 'No success'; 
}
