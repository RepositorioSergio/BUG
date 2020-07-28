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
echo "COMECOU COUNTRIES<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$nrooms = 2;
$n = 6;

$user = 'clubonehotelsTest';
$pass = 'Clu@28527768';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing" >
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
    </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelSearch</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelSearchRequest>
        <hot:CheckInDate>2020-11-03</hot:CheckInDate>
        <hot:CheckOutDate>2020-11-07</hot:CheckOutDate>
        <hot:CountryName>United Arab Emirates</hot:CountryName>
        <hot:CityName>Dubai</hot:CityName>
        <hot:CityId>115936</hot:CityId>
        <hot:IsNearBySearchAllowed>false</hot:IsNearBySearchAllowed>
        <hot:NoOfRooms>' . $nrooms . '</hot:NoOfRooms>
        <hot:GuestNationality>AE</hot:GuestNationality>
        <hot:IsRoomInfoRequired>true</hot:IsRoomInfoRequired>
        <hot:RoomGuests>';

        switch ($n) {
            case 1:
                $raw = $raw . '<hot:RoomGuest AdultCount="1" ChildCount="0"/>';
                break;
            case 2:
                $raw = $raw . '<hot:RoomGuest AdultCount="1" ChildCount="1">
                    <hot:ChildAge>
                        <hot:int>5</hot:int>
                    </hot:ChildAge>
                </hot:RoomGuest>';
                break;
            case 3:
                $raw = $raw . '<hot:RoomGuest AdultCount="2" ChildCount="2">
                    <hot:ChildAge>
                        <hot:int>3</hot:int>
                        <hot:int>5</hot:int>
                    </hot:ChildAge>
                </hot:RoomGuest>';
                break;
            case 4:
                $raw = $raw . '<hot:RoomGuest AdultCount="1" ChildCount="0"/>
                <hot:RoomGuest AdultCount="1" ChildCount="0"/>';
                break;
            case 5:
                $raw = $raw . '<hot:RoomGuest AdultCount="1" ChildCount="1">
                    <hot:ChildAge>
                        <hot:int>5</hot:int>
                    </hot:ChildAge>
                </hot:RoomGuest>
                <hot:RoomGuest AdultCount="1" ChildCount="0"/>';
                break;
            case 6:
                $raw = $raw . '<hot:RoomGuest AdultCount="1" ChildCount="2">
                    <hot:ChildAge>
                        <hot:int>3</hot:int>
                        <hot:int>5</hot:int>
                    </hot:ChildAge>
                </hot:RoomGuest>
                <hot:RoomGuest AdultCount="2" ChildCount="0"/>';
                break;
            case 7:
                $raw = $raw . '<hot:RoomGuest AdultCount="1" ChildCount="2">
                    <hot:ChildAge>
                        <hot:int>3</hot:int>
                        <hot:int>5</hot:int>
                    </hot:ChildAge>
                </hot:RoomGuest>
                <hot:RoomGuest AdultCount="2" ChildCount="0"/>';
                break;
            
            default:
                echo "<br/>ERRO.";
                break;
        }

    $raw = $raw . '</hot:RoomGuests>
        <hot:ResultCount>0</hot:ResultCount>
        <hot:Filters>
        <hot:StarRating>All</hot:StarRating>
        <hot:OrderBy>PriceAsc</hot:OrderBy>
        </hot:Filters>
        <hot:GeoCodes>
            <hot:Latitude>25.26899</hot:Latitude>
            <hot:Longitude>55.37896</hot:Longitude>
            <hot:SearchRadius>10</hot:SearchRadius>
            <hot:CountryCode>AE</hot:CountryCode>
        </hot:GeoCodes>
        <hot:ResponseTime>23</hot:ResponseTime>
    </hot:HotelSearchRequest>
</soap:Body>
</soap:Envelope>';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: ".strlen($raw)
));
$url =  "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

$client->setUri($url);
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
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
$HotelSearchResponse = $Body->item(0)->getElementsByTagName("HotelSearchResponse");

$SessionId = $HotelSearchResponse->item(0)->getElementsByTagName("SessionId");
if ($SessionId->length > 0) {
    $SessionId = $SessionId->item(0)->nodeValue;
} else {
    $SessionId = "";
}
$CityId = $HotelSearchResponse->item(0)->getElementsByTagName("CityId");
if ($CityId->length > 0) {
    $CityId = $CityId->item(0)->nodeValue;
} else {
    $CityId = "";
}
$CheckInDate = $HotelSearchResponse->item(0)->getElementsByTagName("CheckInDate");
if ($CheckInDate->length > 0) {
    $CheckInDate = $CheckInDate->item(0)->nodeValue;
} else {
    $CheckInDate = "";
}
$CheckOutDate = $HotelSearchResponse->item(0)->getElementsByTagName("CheckOutDate");
if ($CheckOutDate->length > 0) {
    $CheckOutDate = $CheckOutDate->item(0)->nodeValue;
} else {
    $CheckOutDate = "";
}

//HotelResultList
$HotelResultList = $HotelSearchResponse->item(0)->getElementsByTagName("HotelResultList");
if ($HotelResultList->length > 0) {
    $HotelResult = $HotelResultList->item(0)->getElementsByTagName("HotelResult");
    if ($HotelResult->length > 0) {
        //HotelInfo
        $HotelInfo = $HotelResult->item(0)->getElementsByTagName("HotelInfo");
        if ($HotelInfo->length > 0) {
            $HotelCode = $HotelInfo->item(0)->getElementsByTagName("HotelCode");
            if ($HotelCode->length > 0) {
                $HotelCode = $HotelCode->item(0)->nodeValue;
            } else {
                $HotelCode = "";
            }
            $HotelName = $HotelInfo->item(0)->getElementsByTagName("HotelName");
            if ($HotelName->length > 0) {
                $HotelName = $HotelName->item(0)->nodeValue;
            } else {
                $HotelName = "";
            }
            $HotelPicture = $HotelInfo->item(0)->getElementsByTagName("HotelPicture");
            if ($HotelPicture->length > 0) {
                $HotelPicture = $HotelPicture->item(0)->nodeValue;
            } else {
                $HotelPicture = "";
            }
            $HotelDescription = $HotelInfo->item(0)->getElementsByTagName("HotelDescription");
            if ($HotelDescription->length > 0) {
                $HotelDescription = $HotelDescription->item(0)->nodeValue;
            } else {
                $HotelDescription = "";
            }
            $Latitude = $HotelInfo->item(0)->getElementsByTagName("Latitude");
            if ($Latitude->length > 0) {
                $Latitude = $Latitude->item(0)->nodeValue;
            } else {
                $Latitude = "";
            }
            $Longitude = $HotelInfo->item(0)->getElementsByTagName("Longitude");
            if ($Longitude->length > 0) {
                $Longitude = $Longitude->item(0)->nodeValue;
            } else {
                $Longitude = "";
            }
            $HotelAddress = $HotelInfo->item(0)->getElementsByTagName("HotelAddress");
            if ($HotelAddress->length > 0) {
                $HotelAddress = $HotelAddress->item(0)->nodeValue;
            } else {
                $HotelAddress = "";
            }
            $Rating = $HotelInfo->item(0)->getElementsByTagName("Rating");
            if ($Rating->length > 0) {
                $Rating = $Rating->item(0)->nodeValue;
            } else {
                $Rating = "";
            }
            $HotelPromotion = $HotelInfo->item(0)->getElementsByTagName("HotelPromotion");
            if ($HotelPromotion->length > 0) {
                $HotelPromotion = $HotelPromotion->item(0)->nodeValue;
            } else {
                $HotelPromotion = "";
            }
            $TripAdvisorRating = $HotelInfo->item(0)->getElementsByTagName("TripAdvisorRating");
            if ($TripAdvisorRating->length > 0) {
                $TripAdvisorRating = $TripAdvisorRating->item(0)->nodeValue;
            } else {
                $TripAdvisorRating = "";
            }
            $TripAdvisorReviewURL = $HotelInfo->item(0)->getElementsByTagName("TripAdvisorReviewURL");
            if ($TripAdvisorReviewURL->length > 0) {
                $TripAdvisorReviewURL = $TripAdvisorReviewURL->item(0)->nodeValue;
            } else {
                $TripAdvisorReviewURL = "";
            }
        }

        //MinHotelPrice
        $MinHotelPrice = $HotelResult->item(0)->getElementsByTagName("MinHotelPrice");
        if ($MinHotelPrice->length > 0) {
            $OriginalPrice = $MinHotelPrice->item(0)->getAttribute("OriginalPrice");
            $B2CRa = $MinHotelPrice->item(0)->getAttribute("B2CRa");
            $Currency = $MinHotelPrice->item(0)->getAttribute("Currency");
            $TotalPrice = $MinHotelPrice->item(0)->getAttribute("TotalPrice");
            $PrefCurrency = $MinHotelPrice->item(0)->getAttribute("PrefCurrency");
            $PrefPrice = $MinHotelPrice->item(0)->getAttribute("PrefPrice");
        }
    }
}

//RoomGuests
$RoomGuests = $HotelSearchResponse->item(0)->getElementsByTagName("RoomGuests");
if ($RoomGuests->length > 0) {
    $node = $RoomGuests->item(0)->getElementsByTagName("RoomGuest");
    for ($i=0; $i < $node->length; $i++) { 
        $ChildCount = $node->item($i)->getAttribute("ChildCount");
        $AdultCount = $node->item($i)->getAttribute("AdultCount");
        $ChildAge = $node->item($i)->getElementsByTagName("ChildAge");
        if ($ChildAge->length > 0) {
            $int = $ChildAge->item(0)->getElementsByTagName("int");
            if ($int->length > 0) {
                $int = $int->item(0)->nodeValue;
            } else {
                $int = "";
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