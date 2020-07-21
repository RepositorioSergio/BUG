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
echo "COMECOU GIATA TRUE<br/>";
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
<wsa:Action>http://TekTravel/HotelBookingApi/GiataHotelCodes</wsa:Action>
<wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
<hot:GiataHotelCodesRequest>
<hot:CityCode>115936</hot:CityCode>
<hot:IsDetailedResponse>true</hot:IsDetailedResponse>
</hot:GiataHotelCodesRequest>
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

$hotelFacil = '';
$Attr = '';

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$GiataHotelCodesResponse = $Body->item(0)->getElementsByTagName("GiataHotelCodesResponse");

$HotelDetails = $GiataHotelCodesResponse->item(0)->getElementsByTagName("HotelDetails");
if ($HotelDetails->length > 0) {
    $Hotel = $HotelDetails->item(0)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        for ($i=0; $i < $Hotel->length; $i++) { 
            $HotelCode = $Hotel->item($i)->getAttribute("HotelCode");
            $HotelName = $Hotel->item($i)->getAttribute("HotelName");
            $HotelRating = $Hotel->item($i)->getAttribute("HotelRating");
            $CityName = $Hotel->item($i)->getElementsByTagName("CityName");
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            $CountryName = $Hotel->item($i)->getElementsByTagName("CountryName");
            if ($CountryName->length > 0) {
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }
            $Address = $Hotel->item($i)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $Address = $Address->item(0)->nodeValue;
            } else {
                $Address = "";
            }
            $HotelLocation = $Hotel->item($i)->getElementsByTagName("HotelLocation");
            if ($HotelLocation->length > 0) {
                $HotelLocation = $HotelLocation->item(0)->nodeValue;
            } else {
                $HotelLocation = "";
            }
            $Description = $Hotel->item($i)->getElementsByTagName("Description");
            if ($Description->length > 0) {
                $Description = $Description->item(0)->nodeValue;
            } else {
                $Description = "";
            }
            $PhoneNumber = $Hotel->item($i)->getElementsByTagName("PhoneNumber");
            if ($PhoneNumber->length > 0) {
                $PhoneNumber = $PhoneNumber->item(0)->nodeValue;
            } else {
                $PhoneNumber = "";
            }
            $FaxNumber = $Hotel->item($i)->getElementsByTagName("FaxNumber");
            if ($FaxNumber->length > 0) {
                $FaxNumber = $FaxNumber->item(0)->nodeValue;
            } else {
                $FaxNumber = "";
            }
            $Map = $Hotel->item($i)->getElementsByTagName("Map");
            if ($Map->length > 0) {
                $Map = $Map->item(0)->nodeValue;
            } else {
                $Map = "";
            }
            $PinCode = $Hotel->item($i)->getElementsByTagName("PinCode");
            if ($PinCode->length > 0) {
                $PinCode = $PinCode->item(0)->nodeValue;
            } else {
                $PinCode = "";
            }
            $HotelWebsiteUrl = $Hotel->item($i)->getElementsByTagName("HotelWebsiteUrl");
            if ($HotelWebsiteUrl->length > 0) {
                $HotelWebsiteUrl = $HotelWebsiteUrl->item(0)->nodeValue;
            } else {
                $HotelWebsiteUrl = "";
            }
            $TripAdvisorRating = $Hotel->item($i)->getElementsByTagName("TripAdvisorRating");
            if ($TripAdvisorRating->length > 0) {
                $TripAdvisorRating = $TripAdvisorRating->item(0)->nodeValue;
            } else {
                $TripAdvisorRating = "";
            }
            $TripAdvisorReviewURL = $Hotel->item($i)->getElementsByTagName("TripAdvisorReviewURL");
            if ($TripAdvisorReviewURL->length > 0) {
                $TripAdvisorReviewURL = $TripAdvisorReviewURL->item(0)->nodeValue;
            } else {
                $TripAdvisorReviewURL = "";
            }


            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('tbo_giatahotelcodes');
                $select->where(array(
                    'id' => $HotelCode
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id =  $data['id'];
                    if ($id->length > 0) {
                        $config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
                        $config = [
                            'driver' => $config->db->driver,
                            'database' => $config->db->database,
                            'username' => $config->db->username,
                            'password' => $config->db->password,
                            'hostname' => $config->db->hostname
                        ];
                        $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'name' => $HotelName,
                            'rating' => $HotelRating,
                            'cityname' => $CityName,
                            'countryname' => $CountryName,
                            'address' => $Address,
                            'hotellocation' => $HotelLocation,
                            'description' => $Description,
                            'phonenumber' => $PhoneNumber,
                            'faxnumber' => $FaxNumber,
                            'map' => $Map,
                            'pincode' => $PinCode,
                            'hotelwebsiteurl' => $HotelWebsiteUrl,
                            'tripadvisorrating' => $TripAdvisorRating,
                            'tripadvisorreviewurl' => $TripAdvisorReviewURL
                            );
                        
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('tbo_giatahotelcodes');
                        $update->set($data);
                        $update->where(array('id' => $HotelCode));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('tbo_giatahotelcodes');
                        $insert->values(array(
                            'id' => $HotelCode,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'name' => $HotelName,
                            'rating' => $HotelRating,
                            'cityname' => $CityName,
                            'countryname' => $CountryName,
                            'address' => $Address,
                            'hotellocation' => $HotelLocation,
                            'description' => $Description,
                            'phonenumber' => $PhoneNumber,
                            'faxnumber' => $FaxNumber,
                            'map' => $Map,
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
                    }
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('tbo_giatahotelcodes');
                    $insert->values(array(
                        'id' => $HotelCode,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $HotelName,
                        'rating' => $HotelRating,
                        'cityname' => $CityName,
                        'countryname' => $CountryName,
                        'address' => $Address,
                        'hotellocation' => $HotelLocation,
                        'description' => $Description,
                        'phonenumber' => $PhoneNumber,
                        'faxnumber' => $FaxNumber,
                        'map' => $Map,
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
                }
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: " . $e;
                echo $return;
            }

            $HotelFacilities = $Hotel->item($i)->getElementsByTagName("HotelFacilities");
            if ($HotelFacilities->length > 0) {
                $HotelFacility = $HotelFacilities->item(0)->getElementsByTagName("HotelFacility");
                if ($HotelFacility->length > 0) {
                    for ($j=0; $j < $HotelFacility->length; $j++) { 
                        $hotelFacil = $HotelFacility->item($j)->nodeValue;
                        
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('tbo_giatahotelcodes_hotelfacilities');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'hotelfacility' => $hotelFacil,
                                'hotelcode' => $HotelCode,
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

            $Attractions = $Hotel->item($i)->getElementsByTagName("Attractions");
            if ($Attractions->length > 0) {
                $Attraction = $Attractions->item(0)->getElementsByTagName("Attraction");
                if ($Attraction->length > 0) {
                    for ($k=0; $k < $Attraction->length; $k++) { 
                        $Attr = $Attraction->item($k)->nodeValue;

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('tbo_giatahotelcodes_attractions');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'attraction' => $Attr,
                                'hotelcode' => $HotelCode,
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
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>