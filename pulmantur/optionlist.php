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
echo "COMECOU OPTION LIST<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/OptionList";

$raw ='<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ol="http://services.rccl.com/Interfaces/OptionList" xmlns:m0="http://www.opentravel.org/OTA/2003/05/alpha">
<soapenv:Header/>
<soap:Body>
<ol:getOptionList>
<OTA_CruiseSpecialServiceAvailRQ Target="Production" MaxResponses="50" MoreIndicator="true" Version="2.0" SequenceNmbr="1" TimeStamp="2008-11-05T19:15:56.692+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
<POS>
    <Source ISOCurrency="USD" TerminalID="12502LDJW6">
        <RequestorID ID="313917" Type="5" ID_Context="AGENCY1"/>
        <BookingChannel Type="7">
            <CompanyName CompanyShortName="PULLMANTUR"/>
        </BookingChannel>
    </Source>
    <Source ISOCurrency="USD" TerminalID="12502LDJW6">
        <RequestorID ID="313917" Type="5" ID_Context="AGENCY2"/>
        <BookingChannel Type="7">
            <CompanyName CompanyShortName="PULLMANTUR"/>
        </BookingChannel>
    </Source>
    <Source ISOCurrency="USD" TerminalID="12502LDJW6">
        <RequestorID ID="313917" Type="5" ID_Context="AGENT1"/>
        <BookingChannel Type="7">
            <CompanyName CompanyShortName="PULLMANTUR"/>
        </BookingChannel>
    </Source>
</POS>
<SailingInfo>
    <SelectedSailing Start="2019-10-02">
        <CruiseLine ShipCode="MO"/>
    </SelectedSailing>
    <SelectedCategory BerthedCategoryCode="JT"/>
</SailingInfo>
</OTA_CruiseSpecialServiceAvailRQ>
</ol:getOptionList>
</soap:Body>
</soap:Envelope>';

/* $client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
$response = $response->getBody();
} else {
$logger = new Logger();
$writer = new Writer\Stream('/srv/www/htdocs/error_log');
$logger->addWriter($writer);
$logger->info($client->getUri());
$logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
echo $return;
echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
echo $return;
die();
} */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
if ($response === false) {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "Operation completed without any errors";
    echo $return;
}
curl_close($ch);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$getOptionListResponse = $Body->item(0)->getElementsByTagName("getOptionListResponse");
$OTA_CruiseSpecialServiceAvailRS = $getOptionListResponse->item(0)->getElementsByTagName("OTA_CruiseSpecialServiceAvailRS");
$SpecialServices = $OTA_CruiseSpecialServiceAvailRS->item(0)->getElementsByTagName("SpecialServices");
$node = $SpecialServices->item(0)->getElementsByTagName("SpecialService");
for ($i=0; $i < $node->length; $i++) { 
    $Code = $node->item($i)->getAttribute("Code");
    $Description = $node->item($i)->getAttribute("Description");
    $AssociationType = $node->item($i)->getAttribute("AssociationType");

    $PriceInfo = $node->item($i)->getElementsByTagName("PriceInfo");
    if ($PriceInfo->length > 0) {
        $ChargeTypeCode = $PriceInfo->item(0)->getAttribute("ChargeTypeCode");
        $Amount = $PriceInfo->item(0)->getAttribute("Amount");
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('optionList');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Code' => $Code,
            'Description' => $Description,
            'AssociationType' => $AssociationType,
            'ChargeTypeCode' => $ChargeTypeCode,
            'Amount' => $Amount
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO: " . $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>