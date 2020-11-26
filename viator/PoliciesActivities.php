<?php
error_log("\r\nPolicies Activities - Viator\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
$sql = "select data from quote_session_viatoractivities where session_id='$quoteid'";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
} else {
    $response['error'] = "Unable to handle request #3";
    return false;
}
// Get Activity Availability
$arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
foreach ($arrIt as $sub) {
    $subArray = $arrIt->getSubIterator();
    if ($subArray['quoteid'] === $availabilityid) {
        $availability = $subArray;
        $total = (float) $availability['rates'][0]['totalplain'];
        break;
    }
}
$activity['adults'] = $adults;
$activity['children'] = $children;
$activity['children_ages'] = $children_ages;
$activity['children_ages_array'] = explode("-", $children_ages);
$activity['availability'] = $availability;
?>