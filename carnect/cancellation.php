<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
// Start
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enableCarnectCars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_carnect = $affiliate_id;
} else {
    $affiliate_id_carnect = 0;
}
$sql = "select value from settings where name='CarnectLogin' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectLogin = $row_settings['value'];
}
$sql = "select value from settings where name='CarnectCarspassword' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='CarnectCarswebservicesURL' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $CarnectCarswebservicesURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Header />
          <soapenv:Body>
        <VehCancelResRQ xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" EchoToken="" Version="15" xmlns="http://www.opentravel.org/OTA/2003/05">
          <POS>
            <Source ISOCountry="US">
              <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
            </Source>
          </POS>
          <VehCancelRQCore CancelType="Book">
            <UniqueID ID_Context="TES404842991024" />
            <PersonName>
              <Surname>Andrade</Surname>
            </PersonName>
          </VehCancelRQCore>
        </VehCancelResRQ>
        </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($xml_post_string)
        );

        //
        // PHP CURL for https connection with auth
        //
error_reporting(E_ALL);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $CarnectCarswebservicesURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $xmlresult = curl_exec($ch);
        curl_close($ch);
echo '<xmp>';
        echo $xmlresult;
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$VehCancelResRS = $Body->item(0)->getElementsByTagName('VehCancelResRS');

//VehCancelRSCore
$VehCancelRSCore = $VehCancelResRS->item(0)->getElementsByTagName('VehCancelRSCore');
if ($VehCancelRSCore->length > 0) {
    $CancelStatus = $VehCancelRSCore->item(0)->getAttribute("CancelStatus");
    $UniqueID = $VehResRSCore->item(0)->getElementsByTagName('UniqueID');
    if ($UniqueID->length > 0) {
        $ID_Context = $UniqueID->item(0)->getAttribute("ID_Context");
    }
    $CancelRules = $VehResRSCore->item(0)->getElementsByTagName('CancelRules');
    if ($CancelRules->length > 0) {
        $CancelRule = $CancelRules->item(0)->getElementsByTagName('CancelRule');
        if ($CancelRule->length > 0) {
            $CancelRuleAmount = $CancelRule->item(0)->getAttribute("Amount");
        }
    }
}

//VehCancelRSInfo
$VehCancelRSInfo = $VehCancelResRS->item(0)->getElementsByTagName('VehCancelRSInfo');
if ($VehCancelRSInfo->length > 0) {
    $VehReservation = $VehCancelRSInfo->item(0)->getElementsByTagName('VehReservation');
    if ($VehReservation->length > 0) {
        $ReservationStatus = $VehReservation->item(0)->getAttribute("ReservationStatus");
        $VehSegmentCore = $VehReservation->item(0)->getElementsByTagName('VehSegmentCore');
        if ($VehSegmentCore->length > 0) {
            $Fees = $VehSegmentCore->item(0)->getElementsByTagName('Fees');
            if ($Fees->length > 0) {
                $Fee = $Fees->item(0)->getElementsByTagName('Fee');
                if ($Fee->length > 0) {
                    $TaxInclusive = $Fee->item(0)->getAttribute('TaxInclusive');
                    $Amount = $Fee->item(0)->getAttribute('Amount');
                    $CurrencyCode = $Fee->item(0)->getAttribute('CurrencyCode');
                    $IncludedInEstTotalInd = $Fee->item(0)->getAttribute('IncludedInEstTotalInd');
                    $Description = $Fee->item(0)->getAttribute('Description');
                }
            }
        }
    }
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('cancellation');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'CancelStatus' => $CancelStatus,
        'ID_Context' => $ID_Context,
        'CancelRuleAmount' => $CancelRuleAmount,
        'ReservationStatus' => $ReservationStatus,
        'TaxInclusive' => $TaxInclusive,
        'Amount' => $Amount,
        'CurrencyCode' => $CurrencyCode,
        'IncludedInEstTotalInd' => $IncludedInEstTotalInd,
        'Description' => $Description
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "Exception: " . $e;
    echo $return;
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br />Done';
?>