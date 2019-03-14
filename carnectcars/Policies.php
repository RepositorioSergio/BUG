<?php
error_log("\r\nCarnect - Policies Cars\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_carnect where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    error_log("\r\n PASSA 1 \r\n", 3, "/srv/www/htdocs/error_log");
    $row_settings->buffer();
    error_log("\r\n PASSA 2 \r\n", 3, "/srv/www/htdocs/error_log");
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
error_log("\r\n ANTES IF \r\n", 3, "/srv/www/htdocs/error_log");
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $from = $searchsettings['pickup_from'];
    $to = $searchsettings['dropoff_to'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $response['result'] = $data[$row];
    $total = $total + $response['result']['total'];
    error_log("\r\n total $total \r\n", 3, "/srv/www/htdocs/error_log");
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>