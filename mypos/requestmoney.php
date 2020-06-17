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
$rm = new \Mypos\IPC\RequestMoney($cnf);
$rm->setAmount(10); 
$rm->setCurrency('EUR'); 
$rm->setOrderID(uniqid()); 
$rm->setMandateReferece('126ca831-93d2-4dfc-ab1f-0cce1d0abe9e'); 
$rm->setCustomerWalletNumber('61938166612'); 
$rm->setReason('Here comes the reason'); 
$rm->setOutputFormat(Mypos\IPC\Defines::COMMUNICATION_FORMAT_XML);
$result = $rm->process(); 
 
if ($result->getStatus() == \Mypos\IPC\Defines::STATUS_SUCCESS) { 
    echo $result->getData()['IPC_Trnref']; 
} else { 
    echo "Erro";
}
