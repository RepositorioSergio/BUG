<?php
require_once './IPC/Loader.php';
$cnf = new \Mypos\IPC\Config(); 
$cnf->setIpcURL('https://mypos.eu/vmp/checkout/'); 
$cnf->setLang('en'); 
$cnf->setPrivateKeyPath(dirname(__FILE__) . '/keys/store_private_key.pem'); 
$cnf->setAPIPublicKeyPath(dirname(__FILE__) . '/keys/api_public_key.pem'); 
$cnf->setEncryptPublicKeyPath(dirname(__FILE__) . '/keys/encrypt_key.pem'); 
$cnf->setKeyIndex(1); 
$cnf->setSid('000000000000010'); 
$cnf->setVersion('1.3'); 
$cnf->setWallet('61938166610');
$card = new \Mypos\IPC\Card(); 
$card->setCardToken('1041333312721BC752C1AB7743D0821AA1C9CA09'); 
$card->setCardHolder('John Doe'); 
$card->setCardType(\Mypos\IPC\Card::CARD_TYPE_MASTERCARD); 
$card->setExpMM('12'); 
$card->setExpYY('21'); 
$card->setCvc('111'); 
$card->setEci(6); 
$card->setAvv(''); 
$card->setXid('');
$store = new \Mypos\IPC\IAStoredCardUpdate($cnf); 
$store->setCardVerification(\Mypos\IPC\IAStoreCard::CARD_VERIFICATION_YES); 
$store->setAmount(1.00); 
$store->setCurrency('EUR'); 
$store->setCard($card); 
$store->setOutputFormat(Mypos\IPC\Defines::COMMUNICATION_FORMAT_JSON);

$result = $store->process(); 
if ($result->getStatus() == \Mypos\IPC\Defines::STATUS_SUCCESS) { 
    //success 
    echo $result->getData(CASE_LOWER)['cardtoken']; 
} else { 
    //Show error. 
}
