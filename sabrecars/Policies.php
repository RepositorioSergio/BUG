<?php
error_log("\r\n COMECOU POLICIES QUARTA \r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$db = new \Zend\Db\Adapter\Adapter($config);
error_log("\r\n COMECOU POLICIES SEGUNDA VEZ \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_sabre_cars where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $from = $searchsettings['pickup_from'];
    error_log("\r\n from : " . $from . "\r\n", 3, "/srv/www/htdocs/error_log");
    $to = $searchsettings['dropoff_to'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    error_log("\r\n agent_id : " . $agent_id . "\r\n", 3, "/srv/www/htdocs/error_log");
    $response['result'] = $data[$row];
    $total = $total + $response['result']['total']; 
    error_log("\r\n total : " . $total . "\r\n", 3, "/srv/www/htdocs/error_log");
    
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>