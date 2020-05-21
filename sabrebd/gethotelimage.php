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
echo "COMECOU GET HOTEL IMAGE<br/>";
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
        <eb:Action>GetHotelImageRQ</eb:Action>
        <eb:MessageData>
            <eb:MessageId>LbQ26Jnofb4Q8f3Pk15Mg5</eb:MessageId>
            <eb:Timestamp>2020-04-13T17:10:50</eb:Timestamp>
        </eb:MessageData>
            </eb:MessageHeader>
        <wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext">
        <wsse:BinarySecurityToken>Shared/IDL:IceSess\/SessMgr:1\.0.IDL/Common/!ICESMS\/ACPCRTC!ICESMSLB\/CRT.LB!1586797825699!8565!17</wsse:BinarySecurityToken>
    </wsse:Security>
</SOAP-ENV:Header>
<SOAP-ENV:Body>
   <GetHotelImageRQ xmlns="http://services.sabre.com/hotel/image/v1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1.0.0" xsi:schemaLocation="http://services.sabre.com/hotel/image/v1 GetHotelImageRQ.xsd">
        <HotelRefs>
            <HotelRef HotelCode="100164256" CodeContext="GLOBAL"/>
        </HotelRefs>
        <ImageRef Type="THUMBNAIL" CategoryCode="3" LanguageCode="EN"/>
    </GetHotelImageRQ>
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
$GetHotelImageRS = $Body->item(0)->getElementsByTagName("GetHotelImageRS");
if ($GetHotelImageRS->length > 0) {
    $HotelImageInfos = $GetHotelImageRS->item(0)->getElementsByTagName("HotelImageInfos");
    if ($HotelImageInfos->length > 0) {
        $HotelImageInfo = $HotelImageInfos->item(0)->getElementsByTagName("HotelImageInfo");
        if ($HotelImageInfo->length > 0) {
            $HotelInfo = $HotelImageInfo->item(0)->getElementsByTagName("HotelInfo");
            if ($HotelInfo->length > 0) {
                $HotelCode = $HotelInfo->item(0)->getAttribute("HotelCode");
                $ChainCode = $HotelInfo->item(0)->getAttribute("ChainCode");
                $CodeContext = $HotelInfo->item(0)->getAttribute("CodeContext");
                $Marketer = $HotelInfo->item(0)->getAttribute("Marketer");
                $Logo = $HotelInfo->item(0)->getAttribute("Logo");
            }
            $ImageItem = $HotelImageInfo->item(0)->getElementsByTagName("ImageItem");
            if ($ImageItem->length > 0) {
                $Id = $ImageItem->item(0)->getAttribute("Id");
                $Ordinal = $ImageItem->item(0)->getAttribute("Ordinal");
                $Format = $ImageItem->item(0)->getAttribute("Format");
                $LastModifedDate = $ImageItem->item(0)->getAttribute("LastModifedDate");
                $Image = $ImageItem->item(0)->getElementsByTagName("Image");
                if ($Image->length > 0) {
                    $Url = $Image->item(0)->getAttribute("Url");
                    $Type = $Image->item(0)->getAttribute("Type");
                    $Height = $Image->item(0)->getAttribute("Height");
                    $Width = $Image->item(0)->getAttribute("Width");
                }
                $Category = $ImageItem->item(0)->getElementsByTagName("Category");
                if ($Category->length > 0) {
                    $CategoryCode = $Category->item(0)->getAttribute("CategoryCode");
                    $Description = $Category->item(0)->getElementsByTagName("Description");
                    if ($Description->length > 0) {
                        $Text = $Description->item(0)->getElementsByTagName("Text");
                        if ($Text->length > 0) {
                            $Language = $Text->item(0)->getAttribute("Language");
                            $Text = $Text->item(0)->nodeValue;
                        } else {
                            $Text = "";
                        }
                    }
                }
                $AdditionalInfo = $ImageItem->item(0)->getElementsByTagName("AdditionalInfo");
                if ($AdditionalInfo->length > 0) {
                    $Info = $AdditionalInfo->item(0)->getElementsByTagName("Info");
                    if ($Info->length > 0) {
                        $Type = $Info->item(0)->getAttribute("Type");
                        $Description = $Info->item(0)->getElementsByTagName("Description");
                        if ($Description->length > 0) {
                            $Text = $Description->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                $Language = $Text->item(0)->getAttribute("Language");
                                $Text = $Text->item(0)->nodeValue;
                            } else {
                                $Text = "";
                            }
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
