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
echo "COMECOU AVAILABILITY<br/>";
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

$url = "https://beta.triseptapi.com/11.0/vax.asmx";

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = 'requestXml=<VAXXML xmlns="http://www.triseptsolutions.com/Availability/Request/11.0"><Header AgencyNumber="T140" Contact="Paulo Andrade" Login="Bug Software" Password="St89vxBs" Vendor="MIT" DynamicPackageId="H11" Culture="en-us"  SessionId="' . uniqid() . '" ShowCart="Y" /><Request Type="New" Seq="1" AbsoluteDestinationCode="MIA" AbsoluteOriginCode="MIA"><TravelerAvail><PassengerTypeQuantity Seq="1" Type="ADT" Age="40" /><PassengerTypeQuantity Seq="2" Type="ADT" Age="40" /></TravelerAvail><HotelAvailRQ Start="1" Length="999" SortType="Price"><TravelerAvailSet><PassengerSeq Seq="1"/><PassengerSeq Seq="2"/></TravelerAvailSet><OriginDestinationInformation Type="Checkin" LocationCode="MIA" DateTime="2019-11-10T10:40:48"/><OriginDestinationInformation Type="Checkout" LocationCode="MIA" DateTime="2019-11-17T10:40:48"/></HotelAvailRQ></Request></VAXXML>';


$url = $url . "/AvailabilityRequest";
echo $url . '<br/>';

$headers = array(
    "Accept: application/xml",
    "Content-type: application/x-www-form-urlencoded",
    "Content-Encoding: UTF-8",
    "Accept-Encoding: gzip,deflate",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$response = str_replace("&amp;", "&", str_replace("&nbsp;", "", $response));
$response = str_replace('&amp;', '&', $response);
$response = str_replace('&nbsp;', '', $response);
$response = str_replace("&lt;","<",$response);
$response = str_replace("&gt;",">",$response);
$response = str_replace("&quot;","",$response);
$response = str_replace("&#43;","",$response);
$response = str_replace("<br>","<br/>",$response);
$response = str_replace('&', 'and', $response);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
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
$string = $inputDoc->getElementsByTagName("string");

$VAXXML = $string->item(0)->getElementsByTagName("VAXXML");
if ($VAXXML->length > 0) {
    $Response = $VAXXML->item(0)->getElementsByTagName("Response");
    if ($Response->length > 0) {
        //TravelerAvail
        $TravelerAvail = $Response->item(0)->getElementsByTagName("TravelerAvail");
        if ($TravelerAvail->length > 0) {
            $PassengerTypeQuantity = $TravelerAvail->item(0)->getElementsByTagName("PassengerTypeQuantity");
            if ($PassengerTypeQuantity->length > 0) {
                for ($i=0; $i < $PassengerTypeQuantity->length; $i++) { 
                    $Age = $PassengerTypeQuantity->item($i)->getAttribute("Age");
                    $Type = $PassengerTypeQuantity->item($i)->getAttribute("Type");
                    $Seq = $PassengerTypeQuantity->item($i)->getAttribute("Seq");
                }
            }
        }
        //TravelerAvailSets
        $TravelerAvailSets = $Response->item(0)->getElementsByTagName("TravelerAvailSets");
        if ($TravelerAvailSets->length > 0) {
            $TravelerAvailSet = $TravelerAvailSets->item(0)->getElementsByTagName("TravelerAvailSet");
            if ($TravelerAvailSet->length > 0) {
                for ($j=0; $j < $TravelerAvailSet->length; $j++) { 
                    $Seq = $TravelerAvailSet->item($j)->getAttribute("Seq");
                    $PassengerTypeQuantityRef = $TravelerAvailSet->item($j)->getElementsByTagName("PassengerTypeQuantityRef");
                    if ($PassengerTypeQuantityRef->length > 0) {
                        for ($jAux=0; $jAux < $PassengerTypeQuantityRef->length; $jAux++) { 
                            $Seq2 = $PassengerTypeQuantityRef->item($jAux)->getAttribute("Seq");
                            $Lead = $PassengerTypeQuantityRef->item($jAux)->getAttribute("Lead");
                        }
                    }
                }
            }
        }
        //CityLookUp
        $CityLookUp = $Response->item(0)->getElementsByTagName("CityLookUp");
        if ($CityLookUp->length > 0) {
            $City = $CityLookUp->item(0)->getElementsByTagName("City");
            if ($City->length > 0) {
                $State = $City->item(0)->getAttribute("State");
                $Name = $City->item(0)->getAttribute("Name");
                $LocationCode = $City->item(0)->getAttribute("LocationCode");
            }
        }
        //Availability
        $Availability = $Response->item(0)->getElementsByTagName("Availability");
        if ($Availability->length > 0) {
            $Results = $Availability->item(0)->getElementsByTagName("Results");
            if ($Results->length > 0) {
                $Seq = $Results->item(0)->getAttribute("Seq");
                $HighDate = $Results->item(0)->getAttribute("HighDate");
                $LowDate = $Results->item(0)->getAttribute("LowDate");
                $Car = $Results->item(0)->getAttribute("Car");
                $Feature = $Results->item(0)->getAttribute("Feature");
                $Hotel = $Results->item(0)->getAttribute("Hotel");
                $Air = $Results->item(0)->getAttribute("Air");

                $HotelAvailRS = $Results->item(0)->getElementsByTagName("HotelAvailRS");
                if ($HotelAvailRS->length > 0) {
                    $Hotel = $HotelAvailRS->item(0)->getElementsByTagName("Hotel");
                    if ($Hotel->length > 0) {
                        for ($k=0; $k < $Hotel->length; $k++) { 
                            $Name = $Hotel->item($k)->getAttribute("Name");
                            $Priority = $Hotel->item($k)->getAttribute("Priority");
                            $ChainCode = $Hotel->item($k)->getAttribute("ChainCode");
                            $HotelCode = $Hotel->item($k)->getAttribute("HotelCode");
                            $ItemId = $Hotel->item($k)->getAttribute("ItemId");
                            $FareType = $Hotel->item($k)->getAttribute("FareType");

                            $SmallDescription = $Hotel->item($k)->getElementsByTagName('SmallDescription');
                            if ($SmallDescription->length > 0) {
                                $SmallDescription = $SmallDescription->item(0)->nodeValue;
                            } else {
                                $SmallDescription = "";
                            }
                            $LargeDescription = $Hotel->item($k)->getElementsByTagName('LargeDescription');
                            if ($LargeDescription->length > 0) {
                                $LargeDescription = $LargeDescription->item(0)->nodeValue;
                            } else {
                                $LargeDescription = "";
                            }

                            //POIProximity
                            $POIProximity = $Hotel->item($k)->getElementsByTagName("POIProximity");
                            if ($POIProximity->length > 0) {
                                $PointOfInterest = $POIProximity->item(0)->getAttribute("PointOfInterest");
                                $POICode = $POIProximity->item(0)->getAttribute("POICode");
                                $Units = $POIProximity->item(0)->getAttribute("Units");
                                $Direction = $POIProximity->item(0)->getAttribute("Direction");
                                $Distance = $POIProximity->item(0)->getAttribute("Distance");
                            }
                            //Rating
                            $Rating = $Hotel->item($k)->getElementsByTagName("Rating");
                            if ($Rating->length > 0) {
                                $Preferred = $Rating->item(0)->getAttribute("Preferred");
                                $Value = $Rating->item(0)->getAttribute("Value");
                            }
                            //Address
                            $Address = $Hotel->item($k)->getElementsByTagName("Address");
                            if ($Address->length > 0) {
                                $Description = $Address->item(0)->getAttribute("Description");
                                $CountryCode = $Address->item(0)->getAttribute("CountryCode");
                                $PostalCode = $Address->item(0)->getAttribute("PostalCode");
                                $State = $Address->item(0)->getAttribute("State");
                                $City = $Address->item(0)->getAttribute("City");
                                $AddressLine2 = $Address->item(0)->getAttribute("AddressLine2");
                                $AddressLine1 = $Address->item(0)->getAttribute("AddressLine1");
                            }
                            //Telephone
                            $tel = "";
                            $Telephone = $Hotel->item($k)->getElementsByTagName("Telephone");
                            if ($Telephone->length > 0) {
                                $Type = $Telephone->item(0)->getAttribute("Type");
                                $tel = $Telephone->item(0)->nodeValue;
                            }
                            //HotelRatePlan
                            $HotelRatePlan = $Hotel->item($k)->getElementsByTagName("HotelRatePlan");
                            if ($HotelRatePlan->length > 0) {
                                $RoomDescription = $HotelRatePlan->item(0)->getAttribute("RoomDescription");
                                $PlanDescription = $HotelRatePlan->item(0)->getAttribute("PlanDescription");
                                $PlanCode = $HotelRatePlan->item(0)->getAttribute("PlanCode");
                                $RoomCode = $HotelRatePlan->item(0)->getAttribute("RoomCode");
                                $RatePlanId = $HotelRatePlan->item(0)->getAttribute("RatePlanId");
                                $TravelerAvailSet = $HotelRatePlan->item(0)->getAttribute("TravelerAvailSet");

                                //IncludedItemIds
                                $IncludedItemIds = $HotelRatePlan->item(0)->getElementsByTagName("IncludedItemIds");
                                if ($IncludedItemIds->length > 0) {
                                    $IncludedItemIds = $IncludedItemIds->item(0)->nodeValue;
                                } else {
                                    $IncludedItemIds = "";
                                }
                                //ModifiableItemIds
                                $ModifiableItemIds = $HotelRatePlan->item(0)->getElementsByTagName("ModifiableItemIds");
                                if ($ModifiableItemIds->length > 0) {
                                    $ModifiableItemIds = $ModifiableItemIds->item(0)->nodeValue;
                                } else {
                                    $ModifiableItemIds = "";
                                }
                                //OptionalItemIds
                                $OptionalItemIds = $HotelRatePlan->item(0)->getElementsByTagName("OptionalItemIds");
                                if ($OptionalItemIds->length > 0) {
                                    $OptionalItemIds = $OptionalItemIds->item(0)->nodeValue;
                                } else {
                                    $OptionalItemIds = "";
                                }

                                //PricingInfo
                                $PricingInfo = $HotelRatePlan->item(0)->getElementsByTagName("PricingInfo");
                                if ($PricingInfo->length > 0) {
                                    $Currency = $PricingInfo->item(0)->getAttribute("Currency");
                                    $Total = $PricingInfo->item(0)->getAttribute("Total");
                                    $Markups = $PricingInfo->item(0)->getAttribute("Markups");
                                    $Fees = $PricingInfo->item(0)->getAttribute("Fees");
                                    $Taxes = $PricingInfo->item(0)->getAttribute("Taxes");
                                    $Base = $PricingInfo->item(0)->getAttribute("Base");

                                    $Price = $PricingInfo->item(0)->getElementsByTagName("Price");
                                    if ($Price->length > 0) {
                                        $PriceType = $Price->item(0)->getAttribute("Type");
                                        $PriceTotal = $Price->item(0)->getAttribute("Total");
                                        $PriceMarkups = $Price->item(0)->getAttribute("Markups");
                                        $PriceFees = $Price->item(0)->getAttribute("Fees");
                                        $PriceTaxes = $Price->item(0)->getAttribute("Taxes");
                                        $PriceBase = $Price->item(0)->getAttribute("Base");
                                        $PriceQuantity = $Price->item(0)->getAttribute("Quantity");
                                        $PriceQuantityMaximum = $Price->item(0)->getAttribute("QuantityMaximum");
                                        $PriceQuantityMinimum = $Price->item(0)->getAttribute("QuantityMinimum");
                                        $PriceHighAge = $Price->item(0)->getAttribute("HighAge");
                                        $PriceLowAge = $Price->item(0)->getAttribute("LowAge");

                                        $Fee = $Price->item(0)->getElementsByTagName("Fee");
                                        if ($Fee->length > 0) {
                                            $FeeType = $Fee->item(0)->getAttribute("Type");
                                            $FeeTotal = $Fee->item(0)->getAttribute("Total");
                                            $FeeDescription = $Fee->item(0)->getAttribute("Description");
                                            $FeeRate = $Fee->item(0)->getAttribute("Rate");
                                            $FeeCode = $Fee->item(0)->getAttribute("FeeCode");
                                        }
                                    }
                                }
                                
                            }
                            //MediaKey
                            $media = "";
                            $MediaKey = $Hotel->item($k)->getElementsByTagName("MediaKey");
                            if ($MediaKey->length > 0) {
                                for ($y=0; $y < $MediaKey->length; $y++) { 
                                    $media = $MediaKey->item($y)->nodeValue;
                                }
                            }
                            //OriginDestinationInformation
                            $OriginDestinationInformation = $Hotel->item($k)->getElementsByTagName("OriginDestinationInformation");
                            if ($OriginDestinationInformation->length > 0) {
                                for ($z=0; $z < $OriginDestinationInformation->length; $z++) { 
                                    $Type = $OriginDestinationInformation->item($z)->getAttribute("Type");
                                    $DateTime = $OriginDestinationInformation->item($z)->getAttribute("DateTime");
                                    $LocationCode = $OriginDestinationInformation->item($z)->getAttribute("LocationCode");
                                    $Position = $OriginDestinationInformation->item($z)->getElementsByTagName("Position");
                                    if ($Position->length > 0) {
                                        $LocationCode2 = $Position->item(0)->getAttribute("LocationCode");
                                        $Longitude = $Position->item(0)->getAttribute("Longitude");
                                        $Latitude = $Position->item(0)->getAttribute("Latitude");
                                    }
                                }
                            }
                            //SalesAdvisory
                            $sales = "";
                            $SalesAdvisory = $Hotel->item($k)->getElementsByTagName("SalesAdvisory");
                            if ($SalesAdvisory->length > 0) {
                                $EndDate = $SalesAdvisory->item(0)->getAttribute("EndDate");
                                $BeginDate = $SalesAdvisory->item(0)->getAttribute("BeginDate");
                                $sales = $SalesAdvisory->item(0)->nodeValue;
                            } else {
                                $EndDate = "";
                                $BeginDate = "";
                                $sales = "";
                            }
                            //AddedValue
                            $AddedValue = $Hotel->item($k)->getElementsByTagName("AddedValue");
                            if ($AddedValue->length > 0) {
                                $Desc = $AddedValue->item(0)->getAttribute("Desc");
                                $ShortDesc = $AddedValue->item(0)->getAttribute("ShortDesc");
                                $Rank = $AddedValue->item(0)->getAttribute("Rank");
                                $Code = $AddedValue->item(0)->getAttribute("Code");
                            }
                            
                        }
                    }
                }
            }
        }

        $description = "";
        //Descriptions
        $Descriptions = $Response->item(0)->getElementsByTagName("Descriptions");
        if ($Descriptions->length > 0) {
            $Description = $Descriptions->item(0)->getElementsByTagName("Description");
            if ($Description->length > 0) {
                for ($l=0; $l < $Description->length; $l++) { 
                    $Key = $Description->item($l)->getAttribute("Key");
                    $description = $Description->item($l)->nodeValue;
                }
            }
        }

        $media = "";
        //MediaLinks
        $MediaLinks = $Response->item(0)->getElementsByTagName("MediaLinks");
        if ($MediaLinks->length > 0) {
            $Media = $MediaLinks->item(0)->getElementsByTagName("Media");
            if ($Media->length > 0) {
                $Description = $Media->item(0)->getAttribute("Description");
                $RefId = $Media->item(0)->getAttribute("RefId");
                $MediaKey = $Media->item(0)->getAttribute("MediaKey");
                $media = $Media->item(0)->nodeValue;
            }
        }
        //Cart
        $Cart = $Response->item(0)->getElementsByTagName("Cart");
        if ($Cart->length > 0) {
            $TravelerAvail = $Cart->item(0)->getElementsByTagName("TravelerAvail");
            if ($TravelerAvail->length > 0) {
                $PassengerTypeQuantity = $TravelerAvail->item(0)->getElementsByTagName("PassengerTypeQuantity");
                if ($PassengerTypeQuantity->length > 0) {
                    for ($x=0; $x < $PassengerTypeQuantity->length; $x++) { 
                        $Age = $PassengerTypeQuantity->item($x)->getAttribute("Age");
                        $Type = $PassengerTypeQuantity->item($x)->getAttribute("Type");
                        $Seq = $PassengerTypeQuantity->item($x)->getAttribute("Seq");
                    }
                }
            }
            $TravelerAvailSets = $Cart->item(0)->getElementsByTagName("TravelerAvailSets");
            if ($TravelerAvailSets->length > 0) {
                $TravelerAvailSet = $TravelerAvailSets->item(0)->getElementsByTagName("TravelerAvailSet");
                if ($TravelerAvailSet->length > 0) {
                    for ($j=0; $j < $TravelerAvailSet->length; $j++) { 
                        $Seq = $TravelerAvailSet->item($j)->getAttribute("Seq");
                        $PassengerTypeQuantityRef = $TravelerAvailSet->item($j)->getElementsByTagName("PassengerTypeQuantityRef");
                        if ($PassengerTypeQuantityRef->length > 0) {
                            for ($jAux=0; $jAux < $PassengerTypeQuantityRef->length; $jAux++) { 
                                $Seq2 = $PassengerTypeQuantityRef->item($jAux)->getAttribute("Seq");
                                $Lead = $PassengerTypeQuantityRef->item($jAux)->getAttribute("Lead");
                            }
                        }
                    }
                }
            }
            $Descriptions = $Cart->item(0)->getElementsByTagName('Descriptions');
            if ($Descriptions->length > 0) {
                $Descriptions = $Descriptions->item(0)->nodeValue;
            } else {
                $Descriptions = "";
            }
            $MediaLinks = $Cart->item(0)->getElementsByTagName('MediaLinks');
            if ($MediaLinks->length > 0) {
                $MediaLinks = $MediaLinks->item(0)->nodeValue;
            } else {
                $MediaLinks = "";
            }
            $Comments = $Cart->item(0)->getElementsByTagName('Comments');
            if ($Comments->length > 0) {
                $Comments = $Comments->item(0)->nodeValue;
            } else {
                $Comments = "";
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