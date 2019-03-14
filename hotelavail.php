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
echo "COMECOU RIU<br/>";
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
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
echo $return;
echo $riuServiceURL;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Header/>
    <soapenv:Body>
        <ns6:HotelAvail xmlns:ns6="http://services.enginexml.rumbonet.riu.com">
            <ns6:in0>
                <ns1:AdultsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">2</ns1:AdultsCount>
                <ns1:ChildCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">0</ns1:ChildCount>
                <ns1:CountryCode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">ES</ns1:CountryCode>
                <HotelList xmlns="http://dtos.enginexml.rumbonet.riu.com">
                    <HotelsList>
                        <ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">216</ns2:int>
                        <ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">4401</ns2:int>
                    </HotelsList>
                </HotelList>
                <ns1:InfantsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">0</ns1:InfantsCount>
                <ns1:Language xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">E</ns1:Language>
                <ns1:MealPlan xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/>
                <ns1:promocode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com"/>
                <ns1:rateReference xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/>
                <RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com">
                    <RoomConfig>
                        <RoomStayCandidate>
                            <AdultsCount>2</AdultsCount>
                            <ChildCount>0</ChildCount>
                            <InfantsCount>0</InfantsCount>
                        </RoomStayCandidate>
                    </RoomConfig>
                </RoomList>
                <ns1:RoomsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">1</ns1:RoomsCount>
                <ns1:StayDateEnd xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">20190629</ns1:StayDateEnd>
                <ns1:StayDateStart xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">20190620</ns1:StayDateStart>
            </ns6:in0>
        </ns6:HotelAvail>
    </soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));


$client->setUri($riuServiceURL);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
$response = $response->getBody();
} else {
$logger = new Logger();
$writer = new Writer\Stream('/srv/www/htdocs/error_log');
$logger->addWriter($writer);
$logger->info($client->getUri());
$logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
echo $return;
echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
echo $return;
die();
}
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.RIU.php');
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
$getHotelAvailResponse = $Body->item(0)->getElementsByTagName("getHotelAvailResponse");
$getHotelAvailResult = $getHotelAvailResponse->item(0)->getElementsByTagName("getHotelAvailResult");
$ID = $getHotelAvailResult->item(0)->getAttribute("ID");
$RoomStays = $getHotelAvailResult->item(0)->getElementsByTagName("RoomStays");
$node = $RoomStays->item(0)->getElementsByTagName("RoomStay");
echo $return;
echo "TAM: " . $node->length;
echo $return;
for ($i=0; $i < $node->length; $i++) {       
    //Hotel
    $Hotel = $node->item($i)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        $Code = $Hotel->item(0)->getAttribute("Code");
        $Name = $Hotel->item(0)->getAttribute("Name");
        $StarRating = $Hotel->item(0)->getAttribute("StarRating");
        $SubCategory = $Hotel->item(0)->getAttribute("SubCategory");
        $Email = $Hotel->item(0)->getAttribute("Email");
        $Url = $Hotel->item(0)->getAttribute("Url");
        $UrlVirtualTour = $Hotel->item(0)->getAttribute("UrlVirtualTour");
        $MinAccommodationRate = $Hotel->item(0)->getAttribute("MinAccommodationRate");
        $MaxAccommodationRate = $Hotel->item(0)->getAttribute("MaxAccommodationRate");
        echo $return;
        echo "Code: " . $Code;
        echo $return;

        $Description = $Hotel->item(0)->getElementsByTagName("Description");
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
        $Comments = $Hotel->item(0)->getElementsByTagName("Comments");
        if ($Comments->length > 0) {
            $Comments = $Comments->item(0)->nodeValue;
        } else {
            $Comments = "";
        }

        $Address2 = "";
        $Address = $Hotel->item(0)->getElementsByTagName("Address");
        if ($Address->length > 0) {
            $Latitude = $Address->item(0)->getAttribute("Latitude");
            $Longitude = $Address->item(0)->getAttribute("Longitude");
            $Address2 = $Address->item(0)->nodeValue;
            echo $return;
            echo "Address2: " . $Address2;
            echo $return;
            $City = $Address->item(0)->getElementsByTagName("City");
            if ($City->length > 0) {
                $CityCode = $City->item(0)->getAttribute("Code");
                $CityName = $City->item(0)->getAttribute("Name");
            }
        }

        $PhoneNumbers = $Hotel->item(0)->getElementsByTagName("PhoneNumbers");
        if ($PhoneNumbers->length > 0) {
            $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
            if ($PhoneNumber->length > 0) {
                $LineNumber = $PhoneNumber->item(0)->getAttribute("LineNumber");
                $Prefix = $PhoneNumber->item(0)->getAttribute("Prefix");
                $CountryAccessCode = $PhoneNumber->item(0)->getAttribute("CountryAccessCode");
                $AreaCityCode = $PhoneNumber->item(0)->getAttribute("AreaCityCode");
            } else {
                $LineNumber = "";
                $Prefix = "";
                $CountryAccessCode = "";
                $AreaCityCode = "";
            }
        }

        $MainPhoto = $Hotel->item(0)->getElementsByTagName("MainPhoto");
        if ($MainPhoto->length > 0) {
            $MainPhoto = $MainPhoto->item(0)->nodeValue;
        } else {
            $MainPhoto = "";
        }
        $MinAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MinAccommodationRateCurrency");
        if ($MinAccommodationRateCurrency->length > 0) {
            $MinAccCode = $MinAccommodationRateCurrency->item(0)->getAttribute("Code");
        } else {
            $MinAccCode = "";
        }
        $MaxAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MaxAccommodationRateCurrency");
        if ($MaxAccommodationRateCurrency->length > 0) {
            $MaxAccCode = $MaxAccommodationRateCurrency->item(0)->getAttribute("Code");
        } else {
            $MaxAccCode = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hotelavail');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Code' => $Code,
                'Name' => $Name,
                'StarRating' => $StarRating,
                'SubCategory' => $SubCategory,
                'Email' => $Email,
                'Url' => $Url,
                'UrlVirtualTour' => $UrlVirtualTour,
                'MaxAccommodationRate' => $MaxAccommodationRate,
                'MinAccommodationRate' => $MinAccommodationRate,
                'Description' => $Description,
                'Comments' => $Comments,
                'CityCode' => $CityCode,
                'CityName' => $CityName,
                'Latitude' => $Latitude,
                'Longitude' => $Longitude,
                'Address' => $Address2,
                'AreaCityCode' => $AreaCityCode,
                'CountryAccessCode' => $CountryAccessCode,
                'Prefix' => $Prefix,
                'LineNumber' => $LineNumber,
                'MainPhoto' => $MainPhoto,
                'MinAccCode' => $MinAccCode,
                'MaxAccCode' => $MaxAccCode,
                'IDRoomstay' => $ID
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO HOTEL: " . $e;
            echo $return;
        }

    }
} 

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>