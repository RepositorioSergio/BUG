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


$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

/* $sql = "SELECT id,listofsailingdescriptioncode, duration, portsofcallquantity, start, status, shipcode, vendorcode, regioncode, subregioncode, departureportlocationcode, arrivalportlocationcode, inclusiveindicator  FROM cruisesailavail";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {*/
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

         $url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/CategoryList";

         $raw ='<?xml version="1.0" encoding="UTF-8"?>
         <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cat="http://services.rccl.com/Interfaces/CategoryList" xmlns:m0="http://www.opentravel.org/OTA/2003/05/alpha">
            <soapenv:Header/>
            <soapenv:Body>
               <cat:getCategoryList>
                  <OTA_CruiseCategoryAvailRQ Target="Test" MaxResponses="50" MoreIndicator="true" Version="2.0" SequenceNmbr="1" TimeStamp="2008-11-05T19:15:56.692+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
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
                     <Guest Code="10" Age="30">
                        <GuestTransportation Mode="29" Status="36"/>
                     </Guest>
                     <GuestCounts>
                        <GuestCount Age="30" Quantity="1"/> 
                        <GuestCount Age="5" Quantity="1"/>    
                     </GuestCounts>
                     <SailingInfo>
                        <SelectedSailing ListOfSailingDescriptionCode="' . $ListOfSailingDescriptionCode . '" Start="' . $Start . '" Duration="' . $Duration . '" Status="' . $Status . '" PortsOfCallQuantity="' . $PortsOfCallQuantity . '">
                           <CruiseLine VendorCode="' . $VendorCode . '" ShipCode="' . $ShipCode . '"/>
                           <!--Optional:-->
                           <Region RegionCode="' . $RegionCode . '" SubRegionCode="' . $SubRegionCode . '"/>
                           <!--Optional:-->
                           <DeparturePort LocationCode="' . $DeparturePortLocationCode . '"/>
                           <!--Optional:-->
                           <ArrivalPort LocationCode="' . $ArrivalPortLocationCode . '"/>
                        </SelectedSailing>
                        <!--Optional:-->
                        <InclusivePackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="' . $InclusiveIndicator . '"/>
                        <!--Optional:-->
                        <Currency CurrencyCode="USD" DecimalPlaces="2"/>
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
         curl_setopt($ch, CURLOPT_VERBOSE, false);
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
         $getCategoryListResponse = $Body->item(0)->getElementsByTagName("getCategoryListResponse");
         if ($getCategoryListResponse->length > 0) {
            $OTA_CruiseCategoryAvailRS = $getCategoryListResponse->item(0)->getElementsByTagName("OTA_CruiseCategoryAvailRS");
            if ($OTA_CruiseCategoryAvailRS->length > 0) {
               $SailingInfo = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("SailingInfo");
               if ($SailingInfo->length > 0) {
                  $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
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
                  $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
                  if ($InclusivePackageOption->length > 0) {
                     $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                     $InclusiveIndicator = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
                  }
               }

               try {
                  $sql = new Sql($db);
                  $insert = $sql->insert();
                  $insert->into('categorylist');
                  $insert->values(array(
                     'datetime_updated' => time(),
                     'listofsailingdescriptioncode' => $ListOfSailingDescriptionCode,
                     'duration' => $Duration,
                     'shipcode' => $ShipCode,
                     'vendorcode' => $VendorCode,
                     'regioncode' => $RegionCode,
                     'subregioncode' => $SubRegionCode,
                     'cruisepackagecode' => $CruisePackageCode,
                     'inclusiveindicator' => $InclusiveIndicator
                  ), $insert::VALUES_MERGE);
                  $statement = $sql->prepareStatementForSqlObject($insert);
                  $results = $statement->execute();
                  $db->getDriver()
                     ->getConnection()
                     ->disconnect();
            } catch (\Exception $e) {
                  echo $return;
                  echo "ERRO 1: " . $e;
                  echo $return;
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
                                 $PriceBreakDownsStatus = $PriceBreakDowns->item(0)->getAttribute("Status");
                                 $PriceBreakDown = $PriceBreakDowns->item(0)->getElementsByTagName("PriceBreakDown");
                                 if ($PriceBreakDown->length > 0) {
                                    $AgeQualifyingCode = $PriceBreakDown->item(0)->getAttribute("AgeQualifyingCode");
                                    $PriceBreakDownAmount = $PriceBreakDown->item(0)->getAttribute("Amount");
                                    $NCCFAmount = $PriceBreakDown->item(0)->getAttribute("NCCFAmount");
                                    $PriceBreakDownNetAmount = $PriceBreakDown->item(0)->getAttribute("NetAmount");
                                    $RPH = $PriceBreakDown->item(0)->getAttribute("RPH");
                                 }
                              }
                           }
                        }

                        try {
                           $sql = new Sql($db);
                           $insert = $sql->insert();
                           $insert->into('categorylist_categoryoptions');
                           $insert->values(array(
                              'datetime_updated' => time(),
                              'availablegroupallocationqty' => $AvailableGroupAllocationQty,
                              'availableregularcabins' => $AvailableRegularCabins,
                              'categorylocation' => $CategoryLocation,
                              'groupcode' => $GroupCode,
                              'listofcategoryqualifiercodes' => $ListOfCategoryQualifierCodes,
                              'pricedcategorycode' => $PricedCategoryCode,
                              'status' => $Status,
                              'amount' => $Amount,
                              'netamount' => $NetAmount,
                              'appliedpromotionsquantity' => $AppliedPromotionsQuantity,
                              'nonrefundabletype' => $NonRefundableType,
                              'priceid' => $PriceId,
                              'priceidtype' => $PriceIdType,
                              'promotionclass' => $PromotionClass,
                              'promotiondescription' => $PromotionDescription,
                              'promotiontypes' => $PromotionTypes,
                              'farecode' => $FareCode,
                              'pricedescription' => $PriceDescription,
                              'occupancy' => $Occupancy,
                              'pricebreakdownsstatus' => $PriceBreakDownsStatus,
                              'agequalifyingcode' => $AgeQualifyingCode,
                              'pricebreakdownamount' => $PriceBreakDownAmount,
                              'nccfamount' => $NCCFAmount,
                              'pricebreakdownnetamount' => $PriceBreakDownNetAmount,
                              'rph' => $RPH,
                              'cruisepackagecode' => $CruisePackageCode
                           ), $insert::VALUES_MERGE);
                           $statement = $sql->prepareStatementForSqlObject($insert);
                           $results = $statement->execute();
                           $db->getDriver()
                              ->getConnection()
                              ->disconnect();
                     } catch (\Exception $e) {
                           echo $return;
                           echo "ERRO 2: " . $e;
                           echo $return;
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

                     try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('categorylist_taxes');
                        $insert->values(array(
                           'datetime_updated' => time(),
                           'amount' => $Amount,
                           'cruisepackagecode' => $CruisePackageCode
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                           ->getConnection()
                           ->disconnect();
                  } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 3: " . $e;
                        echo $return;
                  }
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

                        try {
                           $sql = new Sql($db);
                           $insert = $sql->insert();
                           $insert->into('categorylist_fees');
                           $insert->values(array(
                              'datetime_updated' => time(),
                              'amount' => $Amount,
                              'taxinclusive' => $TaxInclusive,
                              'cruisepackagecode' => $CruisePackageCode
                           ), $insert::VALUES_MERGE);
                           $statement = $sql->prepareStatementForSqlObject($insert);
                           $results = $statement->execute();
                           $db->getDriver()
                              ->getConnection()
                              ->disconnect();
                     } catch (\Exception $e) {
                           echo $return;
                           echo "ERRO 4: " . $e;
                           echo $return;
                     }
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

               try {
                  $sql = new Sql($db);
                  $insert = $sql->insert();
                  $insert->into('categorylist_information');
                  $insert->values(array(
                     'datetime_updated' => time(),
                     'name' => $Name,
                     'text' => $Text,
                     'cruisepackagecode' => $CruisePackageCode
                  ), $insert::VALUES_MERGE);
                  $statement = $sql->prepareStatementForSqlObject($insert);
                  $results = $statement->execute();
                  $db->getDriver()
                     ->getConnection()
                     ->disconnect();
               } catch (\Exception $e) {
                     echo $return;
                     echo "ERRO 5: " . $e;
                     echo $return;
               }
            }
         }
      }
   // }
// }


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
