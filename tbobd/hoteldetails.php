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
echo "COMECOU AVAILABLE<br/>";
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

$user = 'wingstest';
$pass = 'Win@59491374';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
    </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelDetails</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelDetailsRequest>
        <hot:ResultIndex>2</hot:ResultIndex>
        <hot:SessionId>f5513c90-f9dd-4cb0-9e92-bdeca8a9f5d3</hot:SessionId>
        <hot:HotelCode>1136544</hot:HotelCode>
    </hot:HotelDetailsRequest>
</soap:Body>
</soap:Envelope>';

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
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$Attr = '';
$hotel = "";
$imgs = "";

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$HotelDetailsResponse = $Body->item(0)->getElementsByTagName("HotelDetailsResponse");

$HotelDetails = $HotelDetailsResponse->item(0)->getElementsByTagName("HotelDetails");
if ($HotelDetails->length > 0) {
    $HotelCode = $HotelDetails->item(0)->getAttribute("HotelCode");
    $HotelName = $HotelDetails->item(0)->getAttribute("HotelName");
    $HotelRating = $HotelDetails->item(0)->getAttribute("HotelRating");
    $Address = $HotelDetails->item(0)->getElementsByTagName("Address");
    if ($Address->length > 0) {
        $Address = $Address->item(0)->nodeValue;
    } else {
        $Address = "";
    }
    $Description = $HotelDetails->item(0)->getElementsByTagName("Description");
    if ($Description->length > 0) {
        $Description = $Description->item(0)->nodeValue;
    } else {
        $Description = "";
    }
    $CountryName = $HotelDetails->item(0)->getElementsByTagName("CountryName");
    if ($CountryName->length > 0) {
        $CountryName = $CountryName->item(0)->nodeValue;
    } else {
        $CountryName = "";
    }
    $Email = $HotelDetails->item(0)->getElementsByTagName("Email");
    if ($Email->length > 0) {
        $Email = $Email->item(0)->nodeValue;
    } else {
        $Email = "";
    }
    $FaxNumber = $HotelDetails->item(0)->getElementsByTagName("FaxNumber");
    if ($FaxNumber->length > 0) {
        $FaxNumber = $FaxNumber->item(0)->nodeValue;
    } else {
        $FaxNumber = "";
    }
    $Image = $HotelDetails->item(0)->getElementsByTagName("Image");
    if ($Image->length > 0) {
        $Image = $Image->item(0)->nodeValue;
    } else {
        $Image = "";
    }
    $Map = $HotelDetails->item(0)->getElementsByTagName("Map");
    if ($Map->length > 0) {
        $Map = $Map->item(0)->nodeValue;
    } else {
        $Map = "";
    }
    $PhoneNumber = $HotelDetails->item(0)->getElementsByTagName("PhoneNumber");
    if ($PhoneNumber->length > 0) {
        $PhoneNumber = $PhoneNumber->item(0)->nodeValue;
    } else {
        $PhoneNumber = "";
    }
    $PinCode = $HotelDetails->item(0)->getElementsByTagName("PinCode");
    if ($PinCode->length > 0) {
        $PinCode = $PinCode->item(0)->nodeValue;
    } else {
        $PinCode = "";
    }
    $HotelWebsiteUrl = $HotelDetails->item(0)->getElementsByTagName("HotelWebsiteUrl");
    if ($HotelWebsiteUrl->length > 0) {
        $HotelWebsiteUrl = $HotelWebsiteUrl->item(0)->nodeValue;
    } else {
        $HotelWebsiteUrl = "";
    }
    $TripAdvisorRating = $HotelDetails->item(0)->getElementsByTagName("TripAdvisorRating");
    if ($TripAdvisorRating->length > 0) {
        $TripAdvisorRating = $TripAdvisorRating->item(0)->nodeValue;
    } else {
        $TripAdvisorRating = "";
    }
    $TripAdvisorReviewURL = $HotelDetails->item(0)->getElementsByTagName("TripAdvisorReviewURL");
    if ($TripAdvisorReviewURL->length > 0) {
        $TripAdvisorReviewURL = $TripAdvisorReviewURL->item(0)->nodeValue;
    } else {
        $TripAdvisorReviewURL = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('tbo_hoteldetails');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'hotelcode' => $HotelCode,
            'hotelname' => $HotelName,
            'hotelrating' => $HotelRating,
            'address' => $Address,
            'description' => $Description,
            'countryname' => $CountryName,
            'email' => $Email,
            'faxnumber' => $FaxNumber,
            'image' => $Image,
            'map' => $Map,
            'phonenumber' => $PhoneNumber,
            'pincode' => $PinCode,
            'hotelwebsiteurl' => $HotelWebsiteUrl,
            'tripadvisorrating' => $TripAdvisorRating,
            'tripadvisorreviewurl' => $TripAdvisorReviewURL
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


    $Attractions = $HotelDetails->item(0)->getElementsByTagName("Attractions");
    if ($Attractions->length > 0) {
        $Attraction = $Attractions->item(0)->getElementsByTagName("Attraction");
        if ($Attraction->length > 0) {
            for ($i=0; $i < $Attraction->length; $i++) { 
                $Attr = $Attraction->item($i)->nodeValue;

                try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('tbo_hoteldetails_attractions');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'attraction' => $Attr,
                            'hotelcode' => $HotelCode
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

    $HotelFacilities = $HotelDetails->item(0)->getElementsByTagName("HotelFacilities");
    if ($HotelFacilities->length > 0) {
        $HotelFacility = $HotelFacilities->item(0)->getElementsByTagName("HotelFacility");
        if ($HotelFacility->length > 0) {
            for ($i=0; $i < $HotelFacility->length; $i++) { 
                $hotel = $HotelFacility->item($i)->nodeValue;

                try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('tbo_hoteldetails_hotelfacilities');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'hotelfacility' => $hotel,
                            'hotelcode' => $HotelCode
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

    $ImageUrls = $HotelDetails->item(0)->getElementsByTagName("ImageUrls");
    if ($ImageUrls->length > 0) {
        $Attraction = $ImageUrls->item(0)->getElementsByTagName("ImageUrl");
        if ($ImageUrl->length > 0) {
            for ($i=0; $i < $ImageUrl->length; $i++) { 
                $imgs = $ImageUrl->item($i)->nodeValue;

                try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('tbo_hoteldetails_imageurls');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'imageurl' => $imgs,
                            'hotelcode' => $HotelCode
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>