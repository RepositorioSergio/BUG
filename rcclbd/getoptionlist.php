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

$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$username = 'CONSTGCOSTAMAR';
$password = '3MDQV5F5BzdvcX9';

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/OptionList";

$CruisePackageCode = "NV04S238";
$ListOfSailingDescriptionCode = 6;
$Duration = "P4N";
$PortsOfCallQuantity = 3;
$Start = "2021-02-08";
$Status = 36;
$ShipCode = "NV";
$VendorCode = "RCC";
$RegionCode = "BAHAM";
$SubRegionCode = "BAH";
$DeparturePortLocationCode = "MIA";
$ArrivalPortLocationCode = "MIA";
$InclusiveIndicator = false;

$raw ='<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <ol:getOptionList xmlns="http://www.opentravel.org/OTA/2003/05/alpha" xmlns:ol="http://services.rccl.com/Interfaces/OptionList">
            <OTA_CruiseSpecialServiceAvailRQ SequenceNmbr="1" Version="1.0" Target="Test">
                <POS>
                    <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                        <RequestorID ID="313917" ID_Context="AGENCY1" Type="11"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                        </BookingChannel>
                    </Source>
                    <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                        <RequestorID ID="313917" ID_Context="AGENCY2" Type="11"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                        </BookingChannel>
                    </Source>
                    <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                        <RequestorID ID="313917" ID_Context="AGENT1" Type="11"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                        </BookingChannel>
                    </Source>
                </POS>
                <SailingInfo>
                    <SelectedSailing ListOfSailingDescriptionCode="6" Start="2021-02-08" Duration="P4N" Status="36" PortsOfCallQuantity="3">
                        <CruiseLine VendorCode="' . $VendorCode . '" ShipCode="' . $ShipCode . '"/>
                        <!--Optional:-->
                        <Region RegionCode="' . $RegionCode . '" SubRegionCode="' . $SubRegionCode . '"/>
                        <!--Optional:-->
                        <DeparturePort LocationCode="' . $DeparturePortLocationCode . '"/>
                        <!--Optional:-->
                        <ArrivalPort LocationCode="' . $ArrivalPortLocationCode . '"/>
                    </SelectedSailing>
                    <!--Optional:-->
                    <InclusivePackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="false"/>
                    <!--Optional:-->
                    <Currency CurrencyCode="USD" DecimalPlaces="2"/>
                    <SelectedCategory BerthedCategoryCode="RS"/>
                </SailingInfo>
            </OTA_CruiseSpecialServiceAvailRQ>
        </ol:getOptionList>
    </soap:Body>
</soap:Envelope>';

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

echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
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
        $insert->into('optionlist');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $Code,
            'description' => $Description,
            'associationtype' => $AssociationType,
            'chargetypecode' => $ChargeTypeCode,
            'amount' => $Amount
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