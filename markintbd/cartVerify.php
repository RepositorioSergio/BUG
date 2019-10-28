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
echo "COMECOU CART VERIFY<br/>";
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

$raw = 'requestXml=<VAXXML xmlns="http://www.triseptsolutions.com/Cart/Verify/Request/11.0"> 
<Header AgencyNumber="T140" Contact="Paulo Andrade" Login="Bug Software" Password="St89vxBs" Vendor="MIT" DynamicPackageId="H11" Culture="en-us"  SessionId="6098585180703837028" />
    </VAXXML>';

$url = $url . "/CartVerifyRequest";
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
    $Header = $VAXXML->item(0)->getElementsByTagName("Header");
    if ($Header->length > 0) {
        $SessionId = $Header->item(0)->getAttribute("SessionId");
        $Site = $Header->item(0)->getAttribute("Site");
        $DynamicPackageId = $Header->item(0)->getAttribute("DynamicPackageId");
        $Vendor = $Header->item(0)->getAttribute("Vendor");
        $Password = $Header->item(0)->getAttribute("Password");
        $Login = $Header->item(0)->getAttribute("Login");
        $Contact = $Header->item(0)->getAttribute("Contact");
        $AgencyNumber = $Header->item(0)->getAttribute("AgencyNumber");
    }

    $Response = $VAXXML->item(0)->getElementsByTagName("Response");
    if ($Response->length > 0) {
        $Cart = $Response->item(0)->getElementsByTagName("Cart");
        if ($Cart->length > 0) {
            //TravelerAvail
            $TravelerAvail = $Cart->item(0)->getElementsByTagName("TravelerAvail");
            if ($TravelerAvail->length > 0) {
                $PassengerTypeQuantity = $TravelerAvail->item(0)->getElementsByTagName("PassengerTypeQuantity");
                if ($PassengerTypeQuantity->length > 0) {
                    for ($i=0; $i < $PassengerTypeQuantity->length; $i++) { 
                        $PassengerTypeQuantityAge = $PassengerTypeQuantity->item($i)->getAttribute("Age");
                        $PassengerTypeQuantityType = $PassengerTypeQuantity->item($i)->getAttribute("Type");
                        $PassengerTypeQuantitySeq = $PassengerTypeQuantity->item($i)->getAttribute("Seq");
                    }
                }
            }
            //TravelerAvailSets
            $TravelerAvailSets = $Cart->item(0)->getElementsByTagName("TravelerAvailSets");
            if ($TravelerAvailSets->length > 0) {
                $TravelerAvailSet = $TravelerAvailSets->item(0)->getElementsByTagName("TravelerAvailSet");
                if ($TravelerAvailSet->length > 0) {
                    for ($j=0; $j < $TravelerAvailSet->length; $j++) { 
                        $Seq = $TravelerAvailSet->item($j)->getAttribute("Seq");

                        $PassengerTypeQuantityRef = $TravelerAvailSet->item($j)->getElementsByTagName("PassengerTypeQuantityRef");
                        if ($PassengerTypeQuantityRef->length > 0) {
                            $PassengerTypeQuantityRefSeq = $PassengerTypeQuantityRef->item(0)->getAttribute("Seq");
                            $PassengerTypeQuantityRefLead = $PassengerTypeQuantityRef->item(0)->getAttribute("Lead");
                        }
                    }
                }
            }
            //Descriptions
            $Descriptions = $Cart->item(0)->getElementsByTagName("Descriptions");
            if ($Descriptions->length > 0) {
                $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    for ($k=0; $k < $Description->length; $k++) { 
                        $Key = $Description->item($k)->getAttribute("Key");
                    }
                }
            }
            //MediaLinks
            $mediaLinks = "";
            $MediaLinks = $Cart->item(0)->getElementsByTagName("MediaLinks");
            if ($MediaLinks->length > 0) {
                $Media = $MediaLinks->item(0)->getElementsByTagName("Media");
                if ($Media->length > 0) {
                    for ($x=0; $x < $Media->length; $x++) { 
                        $Description = $Media->item($x)->getAttribute("Description");
                        $RefId = $Media->item($x)->getAttribute("RefId");
                        $MediaKey = $Media->item($x)->getAttribute("MediaKey");

                        $mediaLinks = $Media->item($x)->nodeValue;
                    }
                }
            }
            //Hotel
            $Hotel = $Cart->item(0)->getElementsByTagName("Hotel");
            if ($Hotel->length > 0) {
                $Seq = $Hotel->item(0)->getAttribute("Seq");
                $Status = $Hotel->item(0)->getAttribute("Status");
                $Priority = $Hotel->item(0)->getAttribute("Priority");
                $CartSeq = $Hotel->item(0)->getAttribute("CartSeq");
                $ChainCode = $Hotel->item(0)->getAttribute("ChainCode");
                $HotelCode = $Hotel->item(0)->getAttribute("HotelCode");
                $Name = $Hotel->item(0)->getAttribute("Name");
                $ItemId = $Hotel->item(0)->getAttribute("ItemId");
                $FareType = $Hotel->item(0)->getAttribute("FareType");

                $SmallDescription = $Hotel->item(0)->getElementsByTagName("SmallDescription");
                if ($SmallDescription->length > 0) {
                    $SmallDescription = $SmallDescription->item(0)->nodeValue;
                } else {
                    $SmallDescription = "";
                }
                $LargeDescription = $Hotel->item(0)->getElementsByTagName("LargeDescription");
                if ($LargeDescription->length > 0) {
                    $LargeDescription = $LargeDescription->item(0)->nodeValue;
                } else {
                    $LargeDescription = "";
                }

                //POIProximity
                $POIProximity = $Hotel->item(0)->getElementsByTagName("POIProximity");
                if ($POIProximity->length > 0) {
                    $PointOfInterest = $POIProximity->item(0)->getAttribute("PointOfInterest");
                    $POICode = $POIProximity->item(0)->getAttribute("POICode");
                    $Units = $POIProximity->item(0)->getAttribute("Units");
                    $Direction = $POIProximity->item(0)->getAttribute("Direction");
                    $Distance = $POIProximity->item(0)->getAttribute("Distance");
                }
                //Rating
                $Rating = $Hotel->item(0)->getElementsByTagName("Rating");
                if ($Rating->length > 0) {
                    $Preferred = $Rating->item(0)->getAttribute("Preferred");
                    $Value = $Rating->item(0)->getAttribute("Value");
                }
                //Address
                $Address = $Hotel->item(0)->getElementsByTagName("Address");
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
                $Telephone = $Hotel->item(0)->getElementsByTagName("Telephone");
                if ($Telephone->length > 0) {
                    $Type = $Telephone->item(0)->getAttribute("Type");
                    $tel = $Telephone->item(0)->nodeValue;
                }
                //HotelRatePlan
                $HotelRatePlan = $Hotel->item(0)->getElementsByTagName("HotelRatePlan");
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
                $MediaKey = $Hotel->item(0)->getElementsByTagName("MediaKey");
                if ($MediaKey->length > 0) {
                    for ($y=0; $y < $MediaKey->length; $y++) { 
                        $media = $MediaKey->item($y)->nodeValue;
                    }
                }
                //OriginDestinationInformation
                $OriginDestinationInformation = $Hotel->item(0)->getElementsByTagName("OriginDestinationInformation");
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
                
            }
        }

        $BookingRule = $Response->item(0)->getElementsByTagName("BookingRule");
        if ($BookingRule->length > 0) {
            $RequirePaymentAtBooking = $BookingRule->item(0)->getAttribute("RequirePaymentAtBooking");
            $RequireGuaranteeAtBooking = $BookingRule->item(0)->getAttribute("RequireGuaranteeAtBooking");

            $AcceptableGuarantee = $BookingRule->item(0)->getElementsByTagName("AcceptableGuarantee");
            if ($AcceptableGuarantee->length > 0) {
                $AGAmount = $AcceptableGuarantee->item(0)->getAttribute("Amount");
                $AGAbsoluteDeadline = $AcceptableGuarantee->item(0)->getAttribute("AbsoluteDeadline");
            }
            $RequiredPayment = $BookingRule->item(0)->getElementsByTagName("RequiredPayment");
            if ($RequiredPayment->length > 0) {
                $RPAmount = $AcceptableGuarantee->item(0)->getAttribute("Amount");
                $RPAbsoluteDeadline = $AcceptableGuarantee->item(0)->getAttribute("AbsoluteDeadline");
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