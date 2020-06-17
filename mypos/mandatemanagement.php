<?php
require_once './IPC/Loader.php';
$cnf = new \Mypos\IPC\Config(); 
$cnf->setIpcURL('https://mypos.eu/vmp/checkout-test/'); 
$cnf->setLang('en'); 
$cnf->setPrivateKeyPath(dirname(__FILE__) . '/keys/store_private_key.pem'); 
$cnf->setAPIPublicKeyPath(dirname(__FILE__) . '/keys/api_public_key.pem'); 
$cnf->setKeyIndex(1); 
$cnf->setSid('000000000000010'); 
$cnf->setVersion('1.3'); 
$cnf->setWallet('61938166610');
$mandateManagement = new \Mypos\IPC\MandateManagement($cnf);
$mandateManagement->setMandateReferece('126ca831-93d2-4dfc-ab1f-0cce1d0abe9e'); 
$mandateManagement->setCustomerWalletNumber('61938166610'); 
$mandateManagement->setAction(\Mypos\IPC\MandateManagement::MANDATE_MANAGEMENT_ACTION_REGISTER); 
$mandateManagement->setMandateText('Here comes the mandate text'); 
$mandateManagement->setOutputFormat(Mypos\IPC\Defines::COMMUNICATION_FORMAT_XML);
$result = $mandateManagement->process(); 
 
if ($result->getStatus() == \Mypos\IPC\Defines::STATUS_SUCCESS) { 
    echo 'success'; 
} else { 
    echo 'No success'; 
}
