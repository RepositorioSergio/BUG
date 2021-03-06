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

$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

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

$username = 'CONSTGCOSTAMAR';
$password = '3MDQV5F5BzdvcX9';

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/HoldCabin";

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:hol="http://services.rccl.com/Interfaces/HoldCabin" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
   <soapenv:Header/>
   <soapenv:Body>
      <hol:holdCabin>
      <OTA_CruiseCabinHoldRQ Version="1.0" Target="Test" SequenceNmbr="1" TimeStamp="2008-12-30T18:30:42.720+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
        <POS>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="369567" ID_Context="AGENCY1" Type="11"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="COSTAMAR"/>
                </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="369567" ID_Context="AGENCY2" Type="11"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="COSTAMAR"/>
                </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="369567" ID_Context="AGENT1" Type="11"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="COSTAMAR"/>
                </BookingChannel>
            </Source>
        </POS>
         <GuestCounts>
            <GuestCount Age="30" Quantity="1"/>
         </GuestCounts>
         <SelectedSailing Start="2021-02-08" ShipCode="' . $ShipCode . '" VendorCode="' . $VendorCode . '">
            <SelectedFare FareCode="BESTRATE" GroupCode="1"/>
            <SelectedCategory BerthedCategoryCode="RS" PricedCategoryCode="RS">
                <!--Optional:-->
                <CabinAttributes>
                    <!--1 to 99 repetitions:-->
                    <CabinAttribute CabinAttributeCode="99"/>
                </CabinAttributes>
                <!--1 to 4 repetitions:-->
               <SelectedCabin CabinNumber="1620" MaxOccupancy="5"/>
            </SelectedCategory>
            <InclusivePackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="false"/>
         </SelectedSailing>
         <!--0 to 9 repetitions:-->
         <Guest Code="10" Age="30"/>
         <!--Optional:-->
            <alp:Currency CurrencyCode="USD" DecimalPlaces="2"/>
         <!--Optional:-->
        <SearchQualifiers BerthedCategoryCode="RS" PricedCategoryCode="RS" CabinNumber="1620" GroupCode="1" MaxOccupancy="5" DeckNumber="10" DeckName="CUBIERTA 10">
            <!--0 to 5 repetitions:-->
            <Status Status="36"/>
            <!--Optional:-->
            <Dining Sitting="M"/>
        </SearchQualifiers>
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
$holdCabinResponse = $Body->item(0)->getElementsByTagName("holdCabinResponse");
if ($holdCabinResponse->length > 0) {
    $OTA_CruiseCabinHoldRS = $holdCabinResponse->item(0)->getElementsByTagName("OTA_CruiseCabinHoldRS");
    if ($OTA_CruiseCabinHoldRS->length > 0) {
        $SelectedSailing = $OTA_CruiseCabinHoldRS->item(0)->getElementsByTagName("SelectedSailing");
        if ($SelectedSailing->length > 0) {
            $SelectedCabin = $SelectedSailing->item(0)->getElementsByTagName("SelectedCabin");
            if ($SelectedCabin->length > 0) {
                $CabinNumber = $SelectedCabin->item(0)->getAttribute("CabinNumber");
                $HeldIndicator = $SelectedCabin->item(0)->getAttribute("HeldIndicator");
                $ReleaseDateTime = $SelectedCabin->item(0)->getAttribute("ReleaseDateTime");
                $Status = $SelectedCabin->item(0)->getAttribute("Status");
                $CabinFilters = $SelectedCabin->item(0)->getElementsByTagName("CabinFilters");
                if ($CabinFilters->length > 0) {
                    $CabinFilter = $CabinFilters->item(0)->getElementsByTagName("CabinFilter");
                    if ($CabinFilter->length > 0) {
                        for ($i=0; $i < $CabinFilter->length; $i++) { 
                            $CabinFilterCode = $CabinFilter->item($i)->getAttribute("CabinFilterCode");
                        }
                    }
                }
            }
            $Insurance = $SelectedSailing->item(0)->getElementsByTagName("Insurance");
            if ($Insurance->length > 0) {
                $InsuranceCode = $Insurance->item(0)->getAttribute("InsuranceCode");
                $InsuranceStatus = $Insurance->item(0)->getAttribute("Status");
            }
            $Information = $SelectedSailing->item(0)->getElementsByTagName("Information");
            if ($Information->length > 0) {
                $Name = $Information->item(0)->getAttribute("Name");
                $Text = $Information->item(0)->getElementsByTagName("Text");
                if ($Text->length > 0) {
                    $Text = $Text->item(0)->nodeValue;
                } else {
                    $Text = "";
                }
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>