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
echo "COMECOU CATEGORY LIST<br/>";
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

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/CategoryList";

$raw ='<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cat="http://services.rccl.com/Interfaces/CategoryList" xmlns:m0="http://www.opentravel.org/OTA/2003/05/alpha">
<soapenv:Header/>
<soapenv:Body>
   <cat:getCategoryList>
      <OTA_CruiseCategoryAvailRQ Target="Production" MaxResponses="50" MoreIndicator="true" Version="2.0" SequenceNmbr="1" TimeStamp="2008-11-05T19:15:56.692+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
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
             <Guest >
               <GuestTransportation Mode="29" Status="36"/>
            </Guest>
            <GuestCounts>
               <GuestCount Age="20" Quantity="1"/> 
               <GuestCount Age="20" Quantity="1"/>        
            </GuestCounts>
            <SailingInfo>
               <SelectedSailing Start="2020-08-08">
                  <CruiseLine ShipCode="MO"/>
               </SelectedSailing>
            </SailingInfo>
            <SelectedFare FareCode="BESTRATE"/>
         </OTA_CruiseCategoryAvailRQ>
      </cat:getCategoryList>
   </soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
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
$getCategoryListResponse = $Body->item(0)->getElementsByTagName("getCategoryListResponse");
if ($getCategoryListResponse->length > 0) {
   $OTA_CruiseCategoryAvailRS = $getCategoryListResponse->item(0)->getElementsByTagName("OTA_CruiseCategoryAvailRS");
   if ($OTA_CruiseCategoryAvailRS->length > 0) {
      $SailingInfo = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("SailingInfo");
      if ($SailingInfo->length > 0) {
         $SelectedSailing = $SailingOption->item(0)->getElementsByTagName("SelectedSailing");
         if ($SelectedSailing->length > 0) {
            $ListOfSailingDescriptionCode = $SelectedSailing->item(0)->getAttribute("ListOfSailingDescriptionCode");
            $Duration = $SelectedSailing->item(0)->getAttribute("Duration");

            $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
            if ($CruiseLine->length > 0) {
               $ShipCode = $CruiseLine->item(0)->getAttribute("ShipCode");
               $VendorCode = $CruiseLine->item(0)->getAttribute("VendorCode");
            }
            $Region = $SelectedSailing->item(0)->getElementsByTagName("Region");
            if ($Region->length > 0) {
               $RegionCode = $Region->item(0)->getAttribute("RegionCode");
               $SubRegionCode = $Region->item(0)->getAttribute("SubRegionCode");
            }
         }
         $InclusivePackageOption = $SailingOption->item($i)->getElementsByTagName("InclusivePackageOption");
         if ($InclusivePackageOption->length > 0) {
            $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
            $InclusiveIndicator = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
         }
      }
      $FareOption = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("FareOption");
      if ($FareOption->length > 0) {
         $CategoryOptions = $FareOption->item(0)->getElementsByTagName("CategoryOptions");
         if ($CategoryOptions->length > 0) {
            $CategoryOption = $CategoryOptions->item(0)->getElementsByTagName("CategoryOption");
            if ($CategoryOption->length > 0) {
               for ($i=0; $i < $CategoryOption->length; $i++) { 
                  $AvailableGroupAllocationQty = $CategoryOption->item($i)->getAttribute("AvailableGroupAllocationQty");
                  $AvailableRegularCabins = $CategoryOption->item($i)->getAttribute("AvailableRegularCabins");
                  $CategoryLocation = $CategoryOption->item($i)->getAttribute("CategoryLocation");
                  $GroupCode = $CategoryOption->item($i)->getAttribute("GroupCode");
                  $ListOfCategoryQualifierCodes = $CategoryOption->item($i)->getAttribute("ListOfCategoryQualifierCodes");
                  $PricedCategoryCode = $CategoryOption->item($i)->getAttribute("PricedCategoryCode");
                  $Status = $CategoryOption->item($i)->getAttribute("Status");

                  $PriceInfos = $CategoryOption->item($i)->getElementsByTagName("PriceInfos");
                  if ($PriceInfos->length > 0) {
                     $PriceInfo = $PriceInfos->item(0)->getElementsByTagName("PriceInfo");
                     if ($PriceInfo->length > 0) {
                        $Amount = $PriceInfo->item(0)->getAttribute("Amount");
                        $AppliedPromotionsQuantity = $PriceInfo->item(0)->getAttribute("AppliedPromotionsQuantity");
                        $NetAmount = $PriceInfo->item(0)->getAttribute("NetAmount");
                        $NonRefundableType = $PriceInfo->item(0)->getAttribute("NonRefundableType");
                        $PriceId = $PriceInfo->item(0)->getAttribute("PriceId");
                        $PriceIdType = $PriceInfo->item(0)->getAttribute("PriceIdType");
                        $PromotionClass = $PriceInfo->item(0)->getAttribute("PromotionClass");
                        $PromotionDescription = $PriceInfo->item(0)->getAttribute("PromotionDescription");
                        $PromotionTypes = $PriceInfo->item(0)->getAttribute("PromotionTypes");
                        $FareCode = $PriceInfo->item(0)->getAttribute("FareCode");

                        $PriceDescription = $PriceInfo->item(0)->getElementsByTagName("PriceDescription");
                        if ($PriceDescription->length > 0) {
                           $PriceDescription = $PriceDescription->item(0)->nodeValue;
                        } else {
                           $PriceDescription = "";
                        }
                        $PriceBreakDowns = $PriceInfo->item(0)->getElementsByTagName("PriceBreakDowns");
                        if ($PriceBreakDowns->length > 0) {
                           $Occupancy = $PriceBreakDowns->item(0)->getAttribute("Occupancy");
                           $Status = $PriceBreakDowns->item(0)->getAttribute("Status");
                           $PriceBreakDown = $PriceBreakDowns->item(0)->getElementsByTagName("PriceBreakDown");
                           if ($PriceBreakDown->length > 0) {
                              $AgeQualifyingCode = $PriceBreakDown->item(0)->getAttribute("AgeQualifyingCode");
                              $Amount = $PriceBreakDown->item(0)->getAttribute("Amount");
                              $NCCFAmount = $PriceBreakDown->item(0)->getAttribute("NCCFAmount");
                              $NetAmount = $PriceBreakDown->item(0)->getAttribute("NetAmount");
                              $RPH = $PriceBreakDown->item(0)->getAttribute("RPH");
                           }
                        }
                     }
                  }
               }
            }
         }
      }
      $Taxes = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("Taxes");
      if ($Taxes->length > 0) {
         $Tax = $Taxes->item(0)->getElementsByTagName("Tax");
         if ($Tax->length > 0) {
            for ($j=0; $j < $Tax->length; $j++) { 
               $Amount = $Tax->item($j)->getAttribute("Amount");
            }
         }
      }
      $Fee = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("Fee");
      if ($Fee->length > 0) {
         $TaxInclusive = $Fee->item(0)->getAttribute("TaxInclusive");
         $Taxes = $Fee->item(0)->getElementsByTagName("Taxes");
         if ($Taxes->length > 0) {
            $Tax = $Taxes->item(0)->getElementsByTagName("Tax");
            if ($Tax->length > 0) {
               for ($j=0; $j < $Tax->length; $j++) { 
                  $Amount = $Tax->item($j)->getAttribute("Amount");
               }
            }
         }
      }
      $Information = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("Information");
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
