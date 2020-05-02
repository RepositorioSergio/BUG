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
echo "COMECOU CABIN HOLD<br/>";
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

$username = 'CONCTMM';
$password = 'u73ecKBu73ecKB!';

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/HoldCabin";

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:hol="http://services.rccl.com/Interfaces/HoldCabin" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
   <soapenv:Header/>
   <soapenv:Body>
      <hol:holdCabin>
      <OTA_CruiseCabinHoldRQ Version="1.0" SequenceNmbr="1" TimeStamp="2008-12-30T18:30:42.720+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
         <POS>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
               <RequestorID ID="279796" ID_Context="AGENCY1" Type="5"/>
               <BookingChannel Type="7">
                  <CompanyName CompanyShortName="PULLMANTUR"/>
               </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
               <RequestorID ID="279796" ID_Context="AGENCY2" Type="5"/>
               <BookingChannel Type="7">
                  <CompanyName CompanyShortName="PULLMANTUR"/>
               </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
               <RequestorID ID="279796" ID_Context="AGENT1" Type="5"/>
               <BookingChannel Type="7">
                  <CompanyName CompanyShortName="PULLMANTUR"/>
               </BookingChannel>
            </Source>
         </POS>
         <GuestCounts>
            <GuestCount Quantity="2"/>
         </GuestCounts>
         <SelectedSailing Start="2020-08-08" ShipCode="SO" VendorCode="PUL">
            <SelectedFare GroupCode="25"/>
            <SelectedCategory BerthedCategoryCode="JT" PricedCategoryCode="JT">
                <!--Optional:-->
                <CabinAttributes>
                    <!--1 to 99 repetitions:-->
                    <CabinAttribute CabinAttributeCode="86"/>
                </CabinAttributes>
                <!--1 to 4 repetitions:-->
               <SelectedCabin CabinNumber="1550" MaxOccupancy="2"/>
            </SelectedCategory>
            <InclusivePackageOption CruisePackageCode="SOPD0745" InclusiveIndicator="false"/>
         </SelectedSailing>
         <!--0 to 9 repetitions:-->
         <Guest LoyaltyMembershipID="8888888"/>
         <Guest LoyaltyMembershipID="7777777"/>
         <!--Optional:-->
        <alp:SearchQualifiers BerthedCategoryCode="JT" PricedCategoryCode="JT" CabinNumber="1550" GroupCode="25" MaxOccupancy="2" DeckNumber="10" DeckName="CUBIERTA 10">
            <!--0 to 5 repetitions:-->
            <alp:Status Status="36"/>
            <!--Optional:-->
            <alp:Dining Sitting="M"/>
        </alp:SearchQualifiers>
      </OTA_CruiseCabinHoldRQ>
      </hol:holdCabin>
   </soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
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