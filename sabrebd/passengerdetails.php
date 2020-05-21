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
echo "COMECOU PASSENGER<br/>";
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
$BinarySecurityToken = 'Shared/IDL:IceSess\/SessMgr:1\.0.IDL/Common/!ICESMS\/ACPCRTC!ICESMSLB\/CRT.LB!1586334480536!9067!9';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Header>
   <eb:MessageHeader soapenv:mustUnderstand="0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader">
       <eb:From>
         <eb:PartyId></eb:PartyId>
      </eb:From>
      <eb:To>
         <eb:PartyId></eb:PartyId>
      </eb:To>
      <eb:CPAId>' . $CPAId . '</eb:CPAId>
      <eb:ConversationId>V1@0001@client</eb:ConversationId>
      <eb:Service>PassengerDetailsRQ</eb:Service>
      <eb:Action>PassengerDetailsRQ</eb:Action>
      <eb:MessageData>
         <eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId>
         <eb:Timestamp>' . $datetime . '</eb:Timestamp>
      </eb:MessageData>
   </eb:MessageHeader>
   <eb:Security soapenv:mustUnderstand="0" xmlns:eb="http://schemas.xmlsoap.org/ws/2002/12/secext">
      <eb:BinarySecurityToken>' . $BinarySecurityToken . '</eb:BinarySecurityToken>
   </eb:Security>
</soapenv:Header>
<soapenv:Body>
<PassengerDetailsRQ xmlns="http://services.sabre.com/sp/pd/v3_4" version="3.4.0" ignoreOnError="false" haltOnError="false">
<PostProcessing ignoreAfter="false" unmaskCreditCard="true">
    <RedisplayReservation waitInterval="1000"/>
</PostProcessing>
<SpecialReqDetails>
    <SpecialServiceRQ>
        <SpecialServiceInfo>
            <AdvancePassenger SegmentNumber="A">
                <Document ExpirationDate="2023-05-26" Number="1234567890" Type="P">
                    <IssueCountry>FR</IssueCountry>
                    <NationalityCountry>FR</NationalityCountry>
                </Document>
                <PersonName DateOfBirth="1980-12-02" Gender="M" NameNumber="1.1">
                    <GivenName>JAMES</GivenName>
                    <MiddleName>MALCOLM</MiddleName>
                    <Surname>GREEN</Surname>
                </PersonName>
            </AdvancePassenger>
        </SpecialServiceInfo>
    </SpecialServiceRQ>
</SpecialReqDetails>
<TravelItineraryAddInfoRQ>
    <AgencyInfo>
        <Address>
            <AddressLine>SABRE TRAVEL</AddressLine>
            <CityName>SOUTHLAKE</CityName>
            <CountryCode>US</CountryCode>
            <PostalCode>76092</PostalCode>
            <StateCountyProv StateCode="TX"/>
            <StreetNmbr>3150 SABRE DRIVE</StreetNmbr>
            <VendorPrefs>
                <Airline Hosted="true"/>
            </VendorPrefs>
        </Address>
    </AgencyInfo>
    <CustomerInfo>
        <ContactNumbers>
            <ContactNumber NameNumber="1.1" Phone="817-555-1212" PhoneUseType="H"/>
        </ContactNumbers>
        <PersonName NameNumber="1.1" NameReference="ABC123" PassengerType="ADT">
            <GivenName>MALCOLM</GivenName>
            <Surname>GREEN</Surname>
        </PersonName>
    </CustomerInfo>
</TravelItineraryAddInfoRQ>
</PassengerDetailsRQ>
</soapenv:Body>
</soapenv:Envelope>';
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
$inputDoc->loadXML($response2);
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
$PassengerDetailsRS = $Body->item(0)->getElementsByTagName("PassengerDetailsRS");
if ($PassengerDetailsRS->length > 0) {
    $TravelItineraryReadRS = $PassengerDetailsRS->item(0)->getElementsByTagName("TravelItineraryReadRS");
    if ($TravelItineraryReadRS->length > 0) {
        $TravelItinerary = $TravelItineraryReadRS->item(0)->getElementsByTagName("TravelItinerary");
        if ($TravelItinerary->length > 0) {
            //CustomerInfo
            $CustomerInfo = $TravelItinerary->item(0)->getElementsByTagName("CustomerInfo");
            if ($CustomerInfo->length > 0) {
                # code...
            }
            //ItineraryInfo
            $ItineraryInfo = $TravelItinerary->item(0)->getElementsByTagName("ItineraryInfo");
            if ($ItineraryInfo->length > 0) {
                # code...
            }
            //ItineraryRef
            $ItineraryRef = $TravelItinerary->item(0)->getElementsByTagName("ItineraryRef");
            if ($ItineraryRef->length > 0) {
                # code...
            }
            //SpecialServiceInfo
            $SpecialServiceInfo = $TravelItinerary->item(0)->getElementsByTagName("SpecialServiceInfo");
            if ($SpecialServiceInfo->length > 0) {
                # code...
            }
            //OpenReservationElements
            $OpenReservationElements = $TravelItinerary->item(0)->getElementsByTagName("OpenReservationElements");
            if ($OpenReservationElements->length > 0) {
                $OpenReservationElement = $OpenReservationElements->item(0)->getElementsByTagName("OpenReservationElement");
                if ($OpenReservationElement->length > 0) {
                    # code...
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
