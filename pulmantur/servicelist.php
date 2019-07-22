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
echo "COMECOU SERVICE LIST<br/>";
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

$url = "https://stage.services.rccl.com/";

$raw ='<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<m:getGuestServiceList xmlns:m="http://services.rccl.com/Interfaces/GuestServiceList">
    <OTA_CruiseSpecialServiceAvailRQ RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-12-10T08:41:06.699-05:00" TransactionIdentifier="106597" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
    <POS>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="u73ecKBu73ecKB!" ID_Context="CONCTMM" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
    </POS>
    <GuestCounts>
        <GuestCount Age="0" Quantity="1"/>
    </GuestCounts>
    <SailingInfo>
        <SelectedSailing Start="2019-08-02">
        <CruiseLine ShipCode="FR"/>
        </SelectedSailing>
    </SailingInfo>
    </OTA_CruiseSpecialServiceAvailRQ>
</m:getGuestServiceList>
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
$getGuestServiceListResponse = $Body->item(0)->getElementsByTagName("getGuestServiceListResponse");
$OTA_CruiseSpecialServiceAvailRS = $getGuestServiceListResponse->item(0)->getElementsByTagName("OTA_CruiseSpecialServiceAvailRS");
$SpecialServices = $OTA_CruiseSpecialServiceAvailRS->item(0)->getElementsByTagName("SpecialServices");
$node = $SpecialServices->item(0)->getElementsByTagName("SpecialService");
for ($i=0; $i < $node->length; $i++) { 
    $Code = $node->item($i)->getAttribute("Code");
    $Description = $node->item($i)->getAttribute("Description");
    $MinGuestsRequired = $node->item($i)->getAttribute("MinGuestsRequired");
    $NbrOfYearsRequiredInd = $node->item($i)->getAttribute("NbrOfYearsRequiredInd");
    $ServiceDateRequiredInd = $node->item($i)->getAttribute("ServiceDateRequiredInd");
    $UserRemarkRequiredInd = $node->item($i)->getAttribute("UserRemarkRequiredInd");


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('serviceList');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Code' => $Code,
            'Description' => $Description,
            'MinGuestsRequired' => $MinGuestsRequired,
            'NbrOfYearsRequiredInd' => $NbrOfYearsRequiredInd,
            'ServiceDateRequiredInd' => $ServiceDateRequiredInd,
            'UserRemarkRequiredInd' => $UserRemarkRequiredInd
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