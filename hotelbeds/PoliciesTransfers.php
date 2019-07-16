<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$db = new \Zend\Db\Adapter\Adapter($config);
error_log("\r\n COMECOU POLICIES TERCA TARDE \r\n", 3, "/srv/www/htdocs/error_log");
/*
 * $affiliate_id = 0;
 * $sql = "select value from settings where name='enablehotelbedsTransfers' and affiliate_id=$affiliate_id" . $branch_filter;
 * $statement = $db->createStatement($sql);
 * $statement->prepare();
 * $row_settings = $statement->execute();
 * if ($row_settings->valid()) {
 * $affiliate_id_hotelbeds = $affiliate_id;
 * } else {
 * $affiliate_id_hotelbeds = 0;
 * }
 */
$affiliate_id_hotelbeds = 0;
$sql = "select value from settings where name='hotelbedsTransfersuser' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersuser = $row_settings['value'];
}
error_log("\r\n hotelbedsTransfersuser  $hotelbedsTransfersuser \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='hotelbedsTransferspassword' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransferspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='hotelbedsTransfersMarkup' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersMarkup = (double) $row_settings['value'];
} else {
    $hotelbedsTransfersMarkup = 0;
}
// URL
$sql = "select value from settings where name='hotelbedsTransfersserviceURL' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersserviceURL = $row_settings['value'];
}
error_log("\r\n hotelbedsTransfersserviceURL  $hotelbedsTransfersserviceURL \r\n", 3, "/srv/www/htdocs/error_log");
// Quote
try {
    $sql = "select data, searchsettings from quote_session_hotelbedstransfers where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $data = unserialize(base64_decode($row_settings["data"]));
    $adults = $searchsettings['adults'];
    $children = $searchsettings['children'];
    $infants = $searchsettings['infants'];
    $retdate = $searchsettings['retdate'];
    $rettime = $searchsettings['rettime'];
    $arrtime = $searchsettings['arrtime'];
    $d1 = DateTime::createFromFormat("d-m-Y", $searchsettings['from']);
    $d2 = DateTime::createFromFormat("d-m-Y", $searchsettings['to']);
    $nights = $d1->diff($d2);
    $nights = $nights->format('%a');
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
foreach ($data as $key => $value) {
    if ($value['id'] == $quote_id) {
        $id = $key;
        $availToken = $value['availToken'];
        $echoToken = $value['echoToken'];
        $transfertype = $value['transfertype2'];
        $transferInfoCode = $value['transferInfoCode'];
        $typeTransferInfo = $value['typeTransferInfo'];
        $vehiclecode = $value['vehiclecode'];
        $codeIncomingOffice = $value['codeIncomingOffice'];
        $CodePickupLocation = $value['CodePickupLocation'];
        $CodeDestinationLocation = $value['CodeDestinationLocation'];
        $NameContract = $value['NameContract'];
        $codeType = $value['codeType'];
        $dateFrom = $value['dateFrom'];
        $TotalPrice = $value['transferprice'];
        $codeCurrency = $value['currencycode'];
        $factsheetId = $value['factsheetId'];
        break;
    }
}
$rettime2 = str_replace(":", "", $rettime);
$arrtime2 = str_replace(":", "", $arrtime);
error_log("\r\n ANTES IF \r\n", 3, "/srv/www/htdocs/error_log");


if ($TotalPrice >= $data[$id]['transferprice']) {
    // Price Change
    $tmp = $TotalPrice;
    if ($hotelbedsTransfersMarkup != "") {
        if (is_numeric($hotelbedsTransfersMarkup)) {
            $tmp = $tmp + (($tmp * $hotelbedsTransfersMarkup) / 100);
            $tmp = number_format($tmp, 2);
        }
    }
    $data[$id]['transferprice'] = $tmp;
}
error_log("\r\n PASSOU AQUI \r\n", 3, "/srv/www/htdocs/error_log");

$data[$id]['transacno'] = $factsheetId;
$data[$id]['holidayvalue'] = $TotalPrice;
$data[$id]['currencycode'] = $codeCurrency;
try {
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_hotelbedstransfers');
    $delete->where(array(
        'session_id' => $session_id . "-totals"
    ));
    error_log("\r\n PASSOU AQUI 2 \r\n", 3, "/srv/www/htdocs/error_log");
    $statement = $sql->prepareStatementForSqlObject($delete);
    $results = $statement->execute();
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('quote_session_hotelbedstransfers');
    $insert->values(array(
        'session_id' => $session_id . "-totals",
        'xmlrequest' => (string) $xmlrequest,
        'xmlresult' => '',
        'data' => base64_encode(serialize($data[$id])),
        'searchsettings' => ""
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    error_log("\r\n PASSOU AQUI 3 \r\n", 3, "/srv/www/htdocs/error_log");
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}

$transfers = $data[$id];
$db->getDriver()
    ->getConnection()
    ->disconnect();
    error_log("\r\n EOF \r\n", 3, "/srv/www/htdocs/error_log");
?>