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

                            //FALTA COMPLETAR
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