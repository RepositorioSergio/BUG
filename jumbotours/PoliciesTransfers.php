<?php
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$db = new \Laminas\Db\Adapter\Adapter($config);
$affiliate_id = 0;
$sql = "select value from settings where name='enablejumbotoursgroup' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_jtg = $affiliate_id;
} else {
    $affiliate_id_jtg = 0;
}
$sql = "select value from settings where name='jumbotoursgrouplogin' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgrouplogin = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupPassword' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='jumbotoursgroupmarkup' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupmarkup = (double) $row_settings['value'];
} else {
    $jumbotoursgroupmarkup = 0;
}
// URL
$sql = "select value from settings where name='jumbotoursgroupserviceurl' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupserviceurl = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupagencycode' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupagencycode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupbrandcode' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupbrandcode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgrouppointofsale' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgrouppointofsale = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupSearchSortOrder' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupSearchSortOrder = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupemail' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupemail = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupb2cmarkup' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupb2cmarkup = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupTimeout' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupTimeout = (int)$row_settings['value'];
} else {
    $jumbotoursgroupTimeout = 0;
}
$sql = "select value from settings where name='jumbotoursgroupaffiliates_id' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupaffiliates_id = $row_settings['value'];
}
//
// Quote
//
if ((int) $out == 1) {
    $sql = "select data, searchsettings from quote_session_jtgtransfers_out where session_id='$session_id'";
} else {
    $sql = "select data, searchsettings from quote_session_jtgtransfers where session_id='$session_id'";
}
try {
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
if ($TotalPrice >= $data[$id]['transferprice']) {
    //
    // Price Change
    //
    $tmp = $TotalPrice;
    if ($jumbotoursgroupmarkup != "") {
        if (is_numeric($jumbotoursgroupmarkup)) {
            $tmp = $tmp + (($tmp * $jumbotoursgroupmarkup) / 100);
            $tmp = number_format($tmp, 2);
        }
    }
    $data[$id]['transferprice'] = $tmp;
}
$data[$id]['transacno'] = $factsheetId;
$data[$id]['holidayvalue'] = $TotalPrice;
$data[$id]['currencycode'] = $codeCurrency;
try {
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_jtgtransfers');
    $delete->where(array(
        'session_id' => $session_id . "-totals"
    ));
    $statement = $sql->prepareStatementForSqlObject($delete);
    $results = $statement->execute();
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('quote_session_jtgtransfers');
    $insert->values(array(
        'session_id' => $session_id . "-totals",
        'xmlrequest' => (string) $xmlrequest,
        'xmlresult' => '',
        'data' => base64_encode(serialize($data[$id])),
        'searchsettings' => ""
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
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
?>