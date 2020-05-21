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
echo "COMECOU HOTEL LIST<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://sws-crt.cert.havail.sabre.com';

date_default_timezone_set("UTC");
$datetime = date('Y-m-d\TH:i:s');
$CPAId = 'IA8H';
$BinarySecurityToken = 'Shared/IDL:IceSess\/SessMgr:1\.0.IDL/Common/!ICESMS\/ACPCRTD!ICESMSLB\/CRT.LB!1586774481758!4960!17';

$raw = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<SOAP-ENV:Header>
    <eb:MessageHeader eb:version="3.0.0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader">
        <eb:From>
            <eb:PartyId>111</eb:PartyId>
        </eb:From>
        <eb:To>
            <eb:PartyId>222</eb:PartyId>
        </eb:To>
        <eb:CPAId>IA8H</eb:CPAId>
        <eb:ConversationId>1234567890</eb:ConversationId>
        <eb:Action>GetHotelListRQ</eb:Action>
        <eb:MessageData>
            <eb:MessageId>LbQ26Jnofb4Q8f3Pk15Mg5</eb:MessageId>
            <eb:Timestamp>2020-04-13T17:10:50</eb:Timestamp>
        </eb:MessageData>
            </eb:MessageHeader>
        <wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext">
        <wsse:BinarySecurityToken>Shared/IDL:IceSess\/SessMgr:1\.0.IDL/Common/!ICESMS\/ACPCRTC!ICESMSLB\/CRT.LB!1586799706705!5521!17</wsse:BinarySecurityToken>
    </wsse:Security>
</SOAP-ENV:Header>
<SOAP-ENV:Body>
   <GetHotelListRQ xmlns="http://services.sabre.com/hotel/list/v3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="3.0.0" xsi:schemaLocation="http://services.sabre.com/hotel/list/v3 GetHotelListRQ_v3.xsd">
  <POS>
    <Source PseudoCityCode="IA8H"/>
  </POS>
  <HotelRefs>
         <HotelRef HotelCode="100095296" CodeContext="GLOBAL"/>
    <HotelRef HotelCode="100095298" CodeContext="GLOBAL"/>
    <HotelRef HotelCode="100036910" CodeContext="GLOBAL"/>
        </HotelRefs>
        <HotelPref>
            <SabreRating Min="1.0" Max="5.0"/>
        </HotelPref>
        <HotelInfoRef>
            <LocationInfo>true</LocationInfo>
            <Amenities>true</Amenities>
            <PropertyTypeInfo>true</PropertyTypeInfo>
                  <SecurityFeatures>true</SecurityFeatures>
        </HotelInfoRef>
    </GetHotelListRQ>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Content-Type: text/xml;charset=utf-8",
    "Accept-Encoding: gzip",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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
$Header = $Envelope->item(0)->getElementsByTagName("Header");
$MessageHeader = $Header->item(0)->getElementsByTagName("MessageHeader");
if ($MessageHeader->length > 0) {
    $From = $MessageHeader->item(0)->getElementsByTagName("From");
    if ($From->length > 0) {
        $FromPartyId = $From->item(0)->getElementsByTagName("PartyId");
        if ($FromPartyId->length > 0) {
            $type = $FromPartyId->item(0)->getAttribute("type");
            $FromPartyId = $FromPartyId->item(0)->nodeValue;
        } else {
            $FromPartyId = "";
        }
    }
    $To = $MessageHeader->item(0)->getElementsByTagName("To");
    if ($To->length > 0) {
        $ToPartyId = $To->item(0)->getElementsByTagName("PartyId");
        if ($ToPartyId->length > 0) {
            $ToPartyId = $ToPartyId->item(0)->nodeValue;
        } else {
            $ToPartyId = "";
        }
    }
    $CPAId = $MessageHeader->item(0)->getElementsByTagName("CPAId");
    if ($CPAId->length > 0) {
        $CPAId = $CPAId->item(0)->nodeValue;
    } else {
        $CPAId = "";
    }
    $ConversationId = $MessageHeader->item(0)->getElementsByTagName("ConversationId");
    if ($ConversationId->length > 0) {
        $ConversationId = $ConversationId->item(0)->nodeValue;
    } else {
        $ConversationId = "";
    }
    $MessageData = $MessageHeader->item(0)->getElementsByTagName("MessageData");
    if ($MessageData->length > 0) {
        $MessageId = $MessageData->item(0)->getElementsByTagName("MessageId");
        if ($MessageId->length > 0) {
            $MessageId = $MessageId->item(0)->nodeValue;
        } else {
            $MessageId = "";
        }
        $RefToMessageId = $MessageData->item(0)->getElementsByTagName("RefToMessageId");
        if ($RefToMessageId->length > 0) {
            $RefToMessageId = $RefToMessageId->item(0)->nodeValue;
        } else {
            $RefToMessageId = "";
        }
    }
}
$Security = $Header->item(0)->getElementsByTagName("Security");
if ($Security->length > 0) {
    $BinarySecurityToken = $Security->item(0)->getElementsByTagName("BinarySecurityToken");
    if ($BinarySecurityToken->length > 0) {
        $BinarySecurityToken = $BinarySecurityToken->item(0)->nodeValue;
    } else {
        $BinarySecurityToken = "";
    }
}

$Body = $Envelope->item(0)->getElementsByTagName("Body");
$GetHotelListRS = $Body->item(0)->getElementsByTagName("GetHotelListRS");
if ($GetHotelListRS->length > 0) {
    $HotelInfos = $GetHotelListRS->item(0)->getElementsByTagName("HotelInfos");
    if ($HotelInfos->length > 0) {
        $HotelInfo = $HotelDetailsInfo->item(0)->getElementsByTagName("HotelInfo");
        if ($HotelInfo->length > 0) {
            for ($i=0; $i < $HotelInfo->length; $i++) { 
                $HotelCode = $HotelInfo->item($i)->getAttribute("HotelCode");
                $HotelName = $HotelInfo->item($i)->getAttribute("HotelName");
                $SabreHotelCode = $HotelInfo->item($i)->getAttribute("SabreHotelCode");
                $BrandCode = $HotelInfo->item($i)->getAttribute("BrandCode");
                $CodeContext = $HotelInfo->item($i)->getAttribute("CodeContext");
                $BrandName = $HotelInfo->item($i)->getAttribute("BrandName");
                $Status = $HotelInfo->item($i)->getAttribute("RPH");
                $ChainCode = $HotelInfo->item($i)->getAttribute("Status");
                $ChainName = $HotelInfo->item($i)->getAttribute("ChainName");

                $LocationInfo = $HotelInfo->item($i)->getElementsByTagName("LocationInfo");
                if ($LocationInfo->length > 0) {
                    $Longitude = $LocationInfo->item(0)->getAttribute("Longitude");
                    $Latitude = $LocationInfo->item(0)->getAttribute("Latitude");
                    $Address = $LocationInfo->item(0)->getElementsByTagName("Address");
                    if ($Address->length > 0) {
                        $AddressLine1 = $Address->item(0)->getElementsByTagName("AddressLine1");
                        if ($AddressLine1->length > 0) {
                            $AddressLine1 = $AddressLine1->item(0)->nodeValue;
                        } else {
                            $AddressLine1 = "";
                        }
                        $CityName = $Address->item(0)->getElementsByTagName("CityName");
                        if ($CityName->length > 0) {
                            $CityCode = $CityName->item(0)->getAttribute("CityCode");
                            $CityName = $CityName->item(0)->nodeValue;
                        } else {
                            $CityName = "";
                        }
                        $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
                        if ($StateProv->length > 0) {
                            $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                            $StateProv = $StateProv->item(0)->nodeValue;
                        } else {
                            $StateProv = "";
                        }
                        $PostalCode = $Address->item(0)->getElementsByTagName("PostalCode");
                        if ($PostalCode->length > 0) {
                            $PostalCode = $PostalCode->item(0)->nodeValue;
                        } else {
                            $PostalCode = "";
                        }
                        $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
                        if ($CountryName->length > 0) {
                            $CountryNameCode = $CountryName->item(0)->getAttribute("Code");
                            $CountryName = $CountryName->item(0)->nodeValue;
                        } else {
                            $CountryName = "";
                        }
                    }
                    $Neighborhoods = $LocationInfo->item(0)->getElementsByTagName("Neighborhoods");
                    if ($Neighborhoods->length > 0) {
                        $Neighborhood = $Neighborhoods->item(0)->getElementsByTagName("Neighborhood");
                        if ($Neighborhood->length > 0) {
                            $Neighborhood = $Neighborhood->item(0)->nodeValue;
                        } else {
                            $Neighborhood = "";
                        }
                    }
                    $Contact = $LocationInfo->item(0)->getElementsByTagName("Contact");
                    if ($Contact->length > 0) {
                        $Fax = $Contact->item(0)->getAttribute("Fax");
                        $Phone = $Contact->item(0)->getAttribute("Phone");
                    }
                }
                $Amenities = $HotelInfo->item($i)->getElementsByTagName("Amenities");
                if ($Amenities->length > 0) {
                    $Amenity = $Amenities->item(0)->getElementsByTagName("Amenity");
                    if ($Amenity->length > 0) {
                        for ($iAux=0; $iAux < $Amenity->length; $iAux++) { 
                            $Code = $Amenity->item($iAux)->getAttribute("Code");
                            $Description = $Amenity->item($iAux)->getAttribute("Description");
                            $Amenity = $Amenity->item($iAux)->nodeValue;
                        }
                    }
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
