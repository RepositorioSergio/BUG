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

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0" ReqRespVersion="large">
              <POS>
                <Source ISOCountry="US">
                  <RequestorID Type="CarnectMix" ID_Context="6uI52F87" />
                </Source>
              </POS>
              <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>4</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="2019-12-15T10:00:00" ReturnDateTime="2019-12-22T10:00:00">
                  <PickUpLocation LocationCode="MIA" CodeContext="2" />
                  <ReturnLocation LocationCode="MIA" CodeContext="2" />
                </VehRentalCore>
              </VehAvailRQCore>
            </VehAvailRateRQ>
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
die();
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
$VehAvailRateRS = $Body->item(0)->getElementsByTagName("VehAvailRateRS");
$Version = $VehAvailRateRS->item(0)->getAttribute("Version");
$TimeStamp = $VehAvailRateRS->item(0)->getAttribute("TimeStamp");
$EchoToken = $VehAvailRateRS->item(0)->getAttribute("EchoToken");
$VehAvailRSCore = $VehAvailRateRS->item(0)->getElementsByTagName("VehAvailRSCore");

//VehRentalCore
$VehRentalCore = $VehAvailRSCore->item(0)->getElementsByTagName("VehRentalCore");
$Quantity = $VehRentalCore->item(0)->getAttribute("Quantity");
$ReturnDateTime = $VehRentalCore->item(0)->getAttribute("ReturnDateTime");
$PickUpDateTime = $VehRentalCore->item(0)->getAttribute("PickUpDateTime");

//PickUpLocation
$PickUpLocation = $VehRentalCore->item(0)->getElementsByTagName("PickUpLocation");
$LocationCodePL = $PickUpLocation->item(0)->getAttribute("LocationCode");
//ReturnLocation
$ReturnLocation = $VehRentalCore->item(0)->getElementsByTagName("ReturnLocation");
$LocationCodeRL = $ReturnLocation->item(0)->getAttribute("LocationCode");

//VehVendorAvails
$VehVendorAvails = $VehAvailRSCore->item(0)->getElementsByTagName("VehVendorAvails");
$VehVendorAvail = $VehVendorAvails->item(0)->getElementsByTagName("VehVendorAvail");
$VehAvails = $VehVendorAvail->item(0)->getElementsByTagName("VehAvails");
$VehAvail = $VehAvails->item(0)->getElementsByTagName("VehAvail");
for ($x=0; $x < $VehAvail->length; $x++) { 

    //VehAvailCore
    $VehAvailCore = $VehAvail->item($x)->getElementsByTagName("VehAvailCore");
    $Status = $VehAvailCore->item(0)->getAttribute("Status");
    //Vehicle
    $Vehicle = $VehAvailCore->item(0)->getElementsByTagName("Vehicle");

    //Vehicle
    $Vehicle = $VehAvailCore->item(0)->getElementsByTagName("Vehicle");
    $CodeContext = $Vehicle->item(0)->getAttribute("CodeContext");
    $Code = $Vehicle->item(0)->getAttribute("Code");
    $VendorCarType = $Vehicle->item(0)->getAttribute("VendorCarType");
    $BaggageQuantity = $Vehicle->item(0)->getAttribute("BaggageQuantity");
    $PassengerQuantity = $Vehicle->item(0)->getAttribute("PassengerQuantity");
    $DriveType = $Vehicle->item(0)->getAttribute("DriveType");
    $FuelType = $Vehicle->item(0)->getAttribute("FuelType");
    $TransmissionType = $Vehicle->item(0)->getAttribute("TransmissionType");
    $AirConditionInd = $Vehicle->item(0)->getAttribute("AirConditionInd");
    //VehType
    $VehType = $Vehicle->item(0)->getElementsByTagName("VehType");
    $DoorCount = $VehType->item(0)->getAttribute("DoorCount");
    $VehicleCategory = $VehType->item(0)->getAttribute("VehicleCategory");
    //VehClass
    $VehClass = $Vehicle->item(0)->getElementsByTagName("VehClass");
    $Size = $VehClass->item(0)->getAttribute("Size");
    //VehMakeModel
    $VehMakeModel = $Vehicle->item(0)->getElementsByTagName("VehMakeModel");
    $VehMakeModelCode = $VehMakeModel->item(0)->getAttribute("Code");
    $VehMakeModelName = $VehMakeModel->item(0)->getAttribute("Name");
    //PictureURL
    $PictureURL = $Vehicle->item(0)->getElementsByTagName("PictureURL");
    if ($PictureURL->length > 0) {
        $PictureURL = $PictureURL->item(0)->nodeValue;
    } else {
        $PictureURL = "";
    }

    //RentalRate
    $RentalRate = $VehAvailCore->item(0)->getElementsByTagName("RentalRate");
    $RateDistance = $RentalRate->item(0)->getElementsByTagName("RateDistance");
    $DistUnitName = $RateDistance->item(0)->getAttribute("DistUnitName");
    $Unlimited = $RateDistance->item(0)->getAttribute("Unlimited");
    $VehicleCharges = $RentalRate->item(0)->getElementsByTagName("VehicleCharges");
    $VehicleCharge = $VehicleCharges->item(0)->getElementsByTagName("VehicleCharge");
    for ($i=0; $i < $VehicleCharge->length; $i++) { 
        $PurposeVC = $VehicleCharge->item($i)->getAttribute("Purpose");
        $TaxInclusiveVC = $VehicleCharge->item($i)->getAttribute("TaxInclusive");
        $AmountVC = $VehicleCharge->item($i)->getAttribute("Amount");
        $CurrencyCodeVC = $VehicleCharge->item($i)->getAttribute("CurrencyCode");
    }

    //TotalCharge
    $TotalCharge = $VehAvailCore->item(0)->getElementsByTagName("TotalCharge");
    $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
    $RateConvertInd = $TotalCharge->item(0)->getAttribute("RateConvertInd");
    $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
    //Fees
    $Fees = $VehAvailCore->item(0)->getElementsByTagName("Fees");
    $Fee = $Fees->item(0)->getElementsByTagName("Fee");
    $FeeArray = array();
    $FeeCount = 0;
    for ($j=0; $j < $Fee->length; $j++) { 
        $TaxInclusiveF = $Fee->item($j)->getAttribute("TaxInclusive");
        $AmountF = $Fee->item($j)->getAttribute("Amount");
        $CurrencyCodeF = $Fee->item($j)->getAttribute("CurrencyCode");
        $IncludedInEstTotalIndF = $Fee->item($j)->getAttribute("IncludedInEstTotalInd");
        $IncludedInRateF = $Fee->item($j)->getAttribute("IncludedInRate");
        $DescriptionF = $Fee->item($j)->getAttribute("Description");
        $FeeArray[$FeeCount]['TaxInclusive'] = $TaxInclusiveF;
        $FeeArray[$FeeCount]['Amount'] = $AmountF;
        $FeeArray[$FeeCount]['CurrencyCode'] = $CurrencyCodeF;
        $FeeArray[$FeeCount]['IncludedInEstTotalInd'] = $IncludedInEstTotalIndF;
        $FeeArray[$FeeCount]['IncludedInRate'] = $IncludedInRateF;
        $FeeArray[$FeeCount]['Description'] = $DescriptionF;
        $FeeCount = $FeeCount + 1;
    }

    //Reference
    $Reference = $VehAvailCore->item(0)->getElementsByTagName("Reference");
    $ID_Context = $Reference->item(0)->getAttribute("ID_Context");
    $Type = $Reference->item(0)->getAttribute("Type");
    $URL = $Reference->item(0)->getAttribute("URL");
    //Vendor
    $Vendor = $VehAvailCore->item(0)->getElementsByTagName("Vendor");
    if ($Vendor->length > 0) {
        $VendorCodeContext = $Vendor->item(0)->getAttribute("CodeContext");
        $VendorCode = $Vendor->item(0)->getAttribute("Code");
        $TravelSector = $Vendor->item(0)->getAttribute("TravelSector");
        $Vendor = $Vendor->item(0)->nodeValue;
    } else {
        $Vendor = "";
    }

    //VendorLocation
    $VendorLocation = $VehAvailCore->item(0)->getElementsByTagName("VendorLocation");
    if ($VendorLocation->length > 0) {
        $LocationCodeVL = $VendorLocation->item(0)->getAttribute("LocationCode");
        $CodeContextVL = $VendorLocation->item(0)->getAttribute("CodeContext");
        $NameVL = $VendorLocation->item(0)->getAttribute("Name");
        $CounterLocationVL = $VendorLocation->item(0)->getAttribute("CounterLocation");
        $ExtendedLocationCodeVL = $VendorLocation->item(0)->getAttribute("ExtendedLocationCode");
        $VendorLocation = $VendorLocation->item(0)->nodeValue;
    } else {
        $VendorLocation = "";
    }
    //DropOffLocation
    $DropOffLocation = $VehAvailCore->item(0)->getElementsByTagName("DropOffLocation");
    if ($DropOffLocation->length > 0) {
        $LocationCodeDL = $DropOffLocation->item(0)->getAttribute("LocationCode");
        $CodeContextDL = $DropOffLocation->item(0)->getAttribute("CodeContext");
        $NameDL = $DropOffLocation->item(0)->getAttribute("Name");
        $CounterLocationDL = $DropOffLocation->item(0)->getAttribute("CounterLocation");
        $ExtendedLocationCodeDL = $DropOffLocation->item(0)->getAttribute("ExtendedLocationCode");
        $DropOffLocation = $DropOffLocation->item(0)->nodeValue;
    } else {
        $DropOffLocation = "";
    }
    //AdvanceBooking
    $AdvanceBooking = $VehAvail->item($x)->getElementsByTagName("AdvanceBooking");
    $RulesApplyInd = $AdvanceBooking->item(0)->getAttribute("RulesApplyInd");

    //VehAvailInfo
    $VehAvailInfo = $VehAvail->item($x)->getElementsByTagName("VehAvailInfo");
    $PricedCoverages = $VehAvailInfo->item(0)->getElementsByTagName("PricedCoverages");
    $PricedCoverage = $PricedCoverages->item(0)->getElementsByTagName("PricedCoverage");
    for ($k=0; $k < $PricedCoverage->length; $k++) { 
        $Coverage = $PricedCoverage->item($k)->getElementsByTagName("Coverage");
        $Details = $Coverage->item(0)->getElementsByTagName("Details");
        $CodeDetails = $Details->item(0)->getAttribute("Code");
        $CoverageTypeDetails = $Details->item(0)->getAttribute("CoverageType");
        $ChargeDetails = $Details->item(0)->getElementsByTagName("Charge");
        $AmountCD = $ChargeDetails->item(0)->getAttribute("Amount");
        $IncludedInRateCD = $ChargeDetails->item(0)->getAttribute("IncludedInRate");
        $Charge = $PricedCoverage->item($k)->getElementsByTagName("Charge");
        $TaxInclusiveC = $Charge->item(0)->getAttribute("TaxInclusive");
        $AmountC = $Charge->item(0)->getAttribute("Amount");
        $CurrencyCodeC = $Charge->item(0)->getAttribute("CurrencyCode");
        $IncludedInEstTotalIndC = $Charge->item(0)->getAttribute("IncludedInEstTotalInd");
        $IncludedInRateC = $Charge->item(0)->getAttribute("IncludedInRate");
        $GuaranteedIndC = $Charge->item(0)->getAttribute("GuaranteedInd");
    }

    $PaymentRules = $VehAvailInfo->item(0)->getElementsByTagName("PaymentRules");
    $PaymentRule = $PaymentRules->item(0)->getElementsByTagName("PaymentRule");
    if ($PaymentRule->length > 0) {
        $PaymentType = $PaymentRule->item(0)->getAttribute("PaymentType");
        $PaymentRule = $PaymentRule->item(0)->nodeValue;
    } else {
        $PaymentRule = "";
    }
    $TPA_Extensions = $VehAvailInfo->item(0)->getElementsByTagName("TPA_Extensions");
    $TermsConditions = $TPA_Extensions->item(0)->getElementsByTagName("TermsConditions");
    $urlTC = $TermsConditions->item(0)->getAttribute("url");
    $ProductInformation = $TPA_Extensions->item(0)->getElementsByTagName("ProductInformation");
    $urlPI = $ProductInformation->item(0)->getAttribute("url");
    $tempPI = $ProductInformation->item(0)->getAttribute("temp");
    $SupplierLogo = $TPA_Extensions->item(0)->getElementsByTagName("SupplierLogo");
    $urlSL = $SupplierLogo->item(0)->getAttribute("url");
}


$db->getDriver()
    ->getConnection()
    ->disconnect();
?>