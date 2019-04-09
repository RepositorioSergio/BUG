<?php
error_log("\r\n COMECOU TARDE \r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
$vehicle = array();
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
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
$sql = "select value from settings where name='CarnectCarsMarkup' and affiliate_id=$affiliate_id_carnect";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarsMarkup = (double) $row_settings["value"];
}
$sql = "select code, name, city, latitude, longitude from carlocation where id=" . $pickup_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $pickup = strtolower($row_settings["code"]);
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
    $city = $row_settings["city"];
}
error_log("\r\n city $city \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select code, name, city, latitude, longitude from carlocation where id=" . $dropoff_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $dropoff = strtolower($row_settings["code"]);
    $latitude2 = $row_settings["latitude"];
    $longitude2 = $row_settings["longitude"];
}
error_log("\r\n latitude $latitude \r\n", 3, "/srv/www/htdocs/error_log");
$db->getDriver()
    ->getConnection()
    ->disconnect();

if ($CarnectCarswebservicesURL != "" and $CarnectLogin != "") {

    $search = "CityID";

    $from = strftime("%Y-%m-%d", $from) . "T" . $pickup_time . ":00";
    $to = strftime("%Y-%m-%d", $to) . "T" . $dropoff_time . ":00";
    if ($search == "CityID") {
        $raw ='<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0" ReqRespVersion="large">
              <POS>
                <Source ISOCountry="US">
                  <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
              </POS>
              <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>2</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                  <PickUpLocation LocationCode="' . $pickup . '" CodeContext="1" />
                  <ReturnLocation LocationCode="' . $pickup . '" CodeContext="1" />
                </VehRentalCore>
              </VehAvailRQCore>
            </VehAvailRateRQ>
          </soapenv:Body>
        </soapenv:Envelope>';
    }elseif ($search == "iata") {
        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
        <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
            ReqRespVersion="large">
            <POS>
                <Source ISOCountry="US">
                <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
            </POS>
            <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>4</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                <PickUpLocation LocationCode="LHR" CodeContext="2" />
                <ReturnLocation LocationCode="LHR" CodeContext="2" />
                </VehRentalCore>
            </VehAvailRQCore>
            </VehAvailRateRQ>
        </soapenv:Body>
        </soapenv:Envelope>';
    }elseif ($search == "coordinates") {
        $raw ='<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
        <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
            ReqRespVersion="large">
            <POS>
                <Source ISOCountry="US">
                <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
            </POS>
            <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>6</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                <PickUpLocation LocationCode="' . $latitude . ',' . $longitude . '" CodeContext="3" />
                <ReturnLocation LocationCode="' . $latitude . ',' . $longitude . '" CodeContext="3" />
                </VehRentalCore>
            </VehAvailRQCore>
            </VehAvailRateRQ>
        </soapenv:Body>
        </soapenv:Envelope>';
    }elseif ($search == "country") {
        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
            ReqRespVersion="large">
              <POS>
                <Source ISOCountry="DE">
                  <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
                <Source ISOCountry="CA" />
              </POS>
              <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>4</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                  <PickUpLocation LocationCode="PMI" CodeContext="2" />
                  <ReturnLocation LocationCode="PMI" CodeContext="2" />
                </VehRentalCore>
              </VehAvailRQCore>
            </VehAvailRateRQ>
          </soapenv:Body>
        </soapenv:Envelope>';
    }elseif ($search == "veiculos") {
        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
            ReqRespVersion="large">
              <POS>
                <Source ISOCountry="US">
                  <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
              </POS>
              <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>4</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                  <PickUpLocation LocationCode="PMI" CodeContext="2" />
                  <ReturnLocation LocationCode="PMI" CodeContext="2" />
                </VehRentalCore>
                <VehPrefs>
                  <VehPref AirConditionInd="true">
                    <VehClass Size="3" />
                  </VehPref>
                </VehPrefs>
              </VehAvailRQCore>
            </VehAvailRateRQ>
          </soapenv:Body>
        </soapenv:Envelope>';
    }elseif ($search == "supplier") {
        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
            ReqRespVersion="large">
              <POS>
                <Source ISOCountry="US">
                  <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
              </POS>
              <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>4</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                  <PickUpLocation LocationCode="PMI" CodeContext="2" />
                  <ReturnLocation LocationCode="PMI" CodeContext="2" />
                </VehRentalCore>
                <VendorPrefs>
                  <VendorPref Code="KY" />
                </VendorPrefs>
              </VehAvailRQCore>
            </VehAvailRateRQ>
          </soapenv:Body>
        </soapenv:Envelope>';
    }elseif ($search == "age") {
        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
            ReqRespVersion="large">
              <POS>
                <Source ISOCountry="DE">
                  <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
              </POS>
              <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>4</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                  <PickUpLocation LocationCode="PMI" CodeContext="2" />
                  <ReturnLocation LocationCode="PMI" CodeContext="2" />
                </VehRentalCore>
                <DriverType Age="21" />
              </VehAvailRQCore>
            </VehAvailRateRQ>
          </soapenv:Body>
        </soapenv:Envelope>';
    } else{
        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Body>
            <VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" EchoToken="1.0" Version="1.0"
            ReqRespVersion="large">
              <POS>
                <Source ISOCountry="DE" ISOCurrency="USD">
                  <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
                </Source>
              </POS>
              <VehAvailRQCore RateQueryType="Live">
                <RateQueryParameterType>4</RateQueryParameterType>
                <VehRentalCore PickUpDateTime="' . $from . '"
                ReturnDateTime="' . $to . '">
                  <PickUpLocation LocationCode="PMI" CodeContext="2" />
                  <ReturnLocation LocationCode="PMI" CodeContext="2" />
                </VehRentalCore>
                <RateQualifier RateQualifier="PromotionCode" />
              </VehAvailRQCore>
            </VehAvailRateRQ>
          </soapenv:Body>
        </soapenv:Envelope>';
    }
    //error_log("\r\n raw $raw \r\n", 3, "/srv/www/htdocs/error_log");
     
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($raw)
        );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $CarnectCarswebservicesURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $xmlresult = curl_exec($ch);
    curl_close($ch);
    //error_log("\r\n RESPONSE $xmlresult \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_carnect');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
            'errorline' => 0,
            'errormessage' => $raw,
            'sqlcontext' => $xmlresult,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    
    if ($xmlresult != "") {
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
          $VehicleChargeArray = array();
          $VehicleCount = 0;
          for ($i=0; $i < $VehicleCharge->length; $i++) { 
              $PurposeVC = $VehicleCharge->item($i)->getAttribute("Purpose");
              $TaxInclusiveVC = $VehicleCharge->item($i)->getAttribute("TaxInclusive");
              $AmountVC = $VehicleCharge->item($i)->getAttribute("Amount");
              $CurrencyCodeVC = $VehicleCharge->item($i)->getAttribute("CurrencyCode");
              $VehicleChargeArray[$VehicleCount]['Purpose'] = $PurposeVC;
              $VehicleChargeArray[$VehicleCount]['TaxInclusive'] = $TaxInclusiveVC;
              $VehicleChargeArray[$VehicleCount]['Amount'] = $AmountVC;
              $VehicleChargeArray[$VehicleCount]['CurrencyCode'] = $CurrencyCodeVC;
              $VehicleCount = $VehicleCount + 1;
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
          error_log("\r\n ID_Context $ID_Context \r\n", 3, "/srv/www/htdocs/error_log");
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
          $PricedCoverageArray = array();
          $count = 0;
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
              $PricedCoverageArray[$count]['Code'] = $CodeDetails;
              $PricedCoverageArray[$count]['CoverageType'] = $CoverageTypeDetails;
              $PricedCoverageArray[$count]['Amount'] = $AmountCD;
              $PricedCoverageArray[$count]['IncludedInRate'] = $IncludedInRateCD;
              $PricedCoverageArray[$count]['TaxInclusive'] = $TaxInclusiveC;
              $PricedCoverageArray[$count]['AmountC'] = $AmountC;
              $PricedCoverageArray[$count]['CurrencyCode'] = $CurrencyCodeC;
              $PricedCoverageArray[$count]['IncludedInEstTotalInd'] = $IncludedInEstTotalIndC;
              $PricedCoverageArray[$count]['IncludedInRateC'] = $IncludedInRateC;
              $PricedCoverageArray[$count]['GuaranteedInd'] = $GuaranteedIndC;
              $count = $count + 1;
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
          

          if ($CodeContext != "") {
              $CodeContext = $CodeContext;
          } else {
              $CodeContext = $Code . "or similar";
          }
          
          
  
              $cars[$counter]['id'] = $counter;
              $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-12-" . $counter;
              $cars[$counter]['vendorpicture'] = $urlSL;
              $cars[$counter]['vendorcode'] = $VendorCode;
              $cars[$counter]['vendor'] = $Vendor;
              $cars[$counter]['vendorshortname'] = $VendorCode;
              $cars[$counter]['size'] = $PassengerQuantity;
              $cars[$counter]['doors'] = $DoorCount;
              $cars[$counter]['aircondition'] = $AirConditionInd;
              $cars[$counter]['transmission'] = $TransmissionType;
              $cars[$counter]['bags'] = $BaggageQuantity;
              $cars[$counter]['status'] = $Status;
              $cars[$counter]['from'] = $from;
              $cars[$counter]['to'] = $to;
              $cars[$counter]['pickup'] = ucwords(strtolower($NameVL));
              $cars[$counter]['dropoff'] = ucwords(strtolower($NameDL));
              $cars[$counter]['class'] = $VendorCarType;
              $cars[$counter]['currency'] = $CurrencyCode;
              $cars[$counter]['productId'] = $productId;
              $cars[$counter]['programId'] = $CarProgramId;
              $cars[$counter]['name'] = $CodeContext;
              $cars[$counter]['picture'] = $PictureURL;
              $cars[$counter]['programname'] = $CarProgramName;
              $cars[$counter]['coverage'] = $coverage;
              $cars[$counter]['ID_Context'] = $ID_Context;
              $cars[$counter]['netcurrency'] = $CurrencyCode;
              $cars[$counter]['netprice'] = $EstimatedTotalAmount;
              // Total including VAT in renting country currency
              /* if ($minPrice < $CarProgramPrice) {
                  $minPrice = $CarProgramPrice;
              }
              $minPrice = number_format($minPrice, 2, ".", "");
              if ($carstouricoholidaysMarkup != 0) {
                  $minPrice = $minPrice + (($minPrice * $carstouricoholidaysMarkup) / 100);
              }
              if ($agent_markup != 0) {
                  $minPrice = $minPrice + (($minPrice * $agent_markup) / 100);
              }
              if ($CarProgramCurrency != "") {
                  if ($CarProgramCurrency != $scurrency) {
                      $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                  }
              } else {
                  if ($currencyBase != "") {
                      if ($currencyBase != $scurrency) {
                          $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                      }
                  }
              } */
              $dailytotal = $EstimatedTotalAmount / $nights;
              $dailytotal = number_format($dailytotal, 2, ".", "");
              //$minPrice = number_format($minPrice, 2, ".", "");
              $cars[$counter]['currency'] = $scurrency;
              $cars[$counter]['total'] = $filter->filter($EstimatedTotalAmount);
              $cars[$counter]['dailytotal'] = $filter->filter($dailytotal);
              $cars[$counter]['dueatpickupplain'] = $DueAtPickup;
              $cars[$counter]['dueatpickup'] = $filter->filter($DueAtPickup);
              $cars[$counter]['dueatpickupcurrency'] = $filter->filter($CurrencyCode);
              // Location
              // $cars[$counter]['special'] = 1;
              // $cars[$counter]['recommended'] = 1;
              $counter = $counter + 1;
          }
        //
        // Store Session
        //
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_carnect');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_carnect');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $xmlresult,
                'data' => base64_encode(serialize($cars)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>