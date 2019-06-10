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

$url = "https://stage.services.rccl.com/Interfaces/OptionList";

$raw ='<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
<ns14:getOptionList xmlns="http://www.opentravel.org/OTA/2003/05/alpha" xmlns:ns2="http://services.rccl.com/Interfaces/PackageList"
xmlns:ns3="http://services.rccl.com/Interfaces/CabinDetail" xmlns:ns4="http://services.rccl.com/Interfaces/FastSell"
xmlns:ns5="http://services.rccl.com/Interfaces/GuestServiceList" xmlns:ns6="http://services.rccl.com/Interfaces/BookingHistory"
xmlns:ns7="http://services.rccl.com/Interfaces/ReleaseCabin" xmlns:ns8="http://services.rccl.com/Interfaces/TourList"
xmlns:ns9="http://services.rccl.com/Interfaces/LookupAgency" xmlns:ns10="http://services.rccl.com/Interfaces/ReleaseBooking"
xmlns:ns11="http://services.rccl.com/Interfaces/PromotionList" xmlns:ns12="http://services.rccl.com/Interfaces/TourDetail"
xmlns:ns13="http://services.rccl.com/Interfaces/AirAvail" xmlns:ns14="http://services.rccl.com/Interfaces/OptionList"
xmlns:ns15="http://services.rccl.com/Interfaces/BookingList" xmlns:ns16="http://services.rccl.com/Interfaces/FareDetail"
xmlns:ns17="http://services.rccl.com/Interfaces/PaymentExtension" xmlns:ns18="http://services.rccl.com/Interfaces/Payment"
xmlns:ns19="http://services.rccl.com/Interfaces/TransferDetail" xmlns:ns20="http://services.rccl.com/Interfaces/DiningList"
xmlns:ns21="http://services.rccl.com/Interfaces/TransferList" xmlns:ns22="http://services.rccl.com/Interfaces/ConfirmAir"
xmlns:ns23="http://services.rccl.com/Interfaces/BookingDocument" xmlns:ns24="http://services.rccl.com/Interfaces/BusDetail"
xmlns:ns25="http://services.rccl.com/Interfaces/CabinList" xmlns:ns26="http://services.rccl.com/Interfaces/Login"
xmlns:ns27="http://services.rccl.com/Interfaces/OptionDetail" xmlns:ns28="http://services.rccl.com/Interfaces/ItineraryDetail"
xmlns:ns29="http://services.rccl.com/Interfaces/SailingList" xmlns:ns30="http://services.rccl.com/Interfaces/ConfirmBooking"
xmlns:ns31="http://services.rccl.com/Interfaces/LinkedBooking" xmlns:ns32="http://services.rccl.com/Interfaces/RetrieveBooking"
xmlns:ns33="http://services.rccl.com/Interfaces/CategoryList" xmlns:ns34="http://services.rccl.com/Interfaces/FareList"
xmlns:ns35="http://services.rccl.com/Interfaces/PackageDetail" xmlns:ns36="http://services.rccl.com/Interfaces/AutoAddChargeDetail"
xmlns:ns37="http://services.rccl.com/Interfaces/Memo" xmlns:ns38="http://services.rccl.com/Interfaces/BookingPrice"
xmlns:ns39="http://services.rccl.com/Interfaces/HoldCabin" xmlns:ns40="http://services.rccl.com/Interfaces/BusList">
<OTA_CruiseSpecialServiceAvailRQ SequenceNmbr="1" Version="1">
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
<SailingInfo>
    <SelectedSailing Start="2019-08-02">
        <CruiseLine ShipCode="MO"/>
    </SelectedSailing>
    <SelectedCategory BerthedCategoryCode="JT"/>
</SailingInfo>
</OTA_CruiseSpecialServiceAvailRQ>
</ns14:getOptionList>
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