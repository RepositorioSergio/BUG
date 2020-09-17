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
echo "COMECOU HOTEL BY ID";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://data.rakutentravelxchange.com/hotels/en_US/long.json';
$raw = '{
    "hotel_list": [
        "nZol"
    ]
    }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json'
));
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
echo $return;
echo $response;
echo $return; 
$response = json_decode($response, true);

$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
foreach ($response as $key => $value) {
    $hotel = $value;
    for ($i=0; $i < count($hotel); $i++) { 
        $phone = $hotel[$i]['phone'];
        $rating = $hotel[$i]['rating'];
        $fax = $hotel[$i]['fax'];
        $id = $hotel[$i]['id'];
        $trustyou_rating = $hotel[$i]['trustyou_rating'];
        $country = $hotel[$i]['country'];
        $airport_code = $hotel[$i]['airport_code'];
        $policy = $hotel[$i]['policy'];
        $country_code = $hotel[$i]['country_code'];
        $check_out_time = $hotel[$i]['check_out_time'];
        $trustyou_review_count = $hotel[$i]['trustyou_review_count'];
        $address = $hotel[$i]['address'];
        $category_id = $hotel[$i]['category_id'];
        $highest_daily_rate = $hotel[$i]['highest_daily_rate'];
        $state_province = $hotel[$i]['state_province'];
        $approximate_cost = $hotel[$i]['approximate_cost'];
        $trip_advisor_rating = $hotel[$i]['trip_advisor_rating'];
        $trip_advisor_review_count = $hotel[$i]['trip_advisor_review_count'];
        $original_name = $hotel[$i]['original_name'];
        $name = $hotel[$i]['name'];
        $lowest_daily_rate = $hotel[$i]['lowest_daily_rate'];
        $longitude = $hotel[$i]['longitude'];
        $check_in_time = $hotel[$i]['check_in_time'];
        $city = $hotel[$i]['city'];
        $website = $hotel[$i]['website'];
        $description = $hotel[$i]['description'];
        $latitude = $hotel[$i]['latitude'];
        $chain_code = $hotel[$i]['chain_code'];
        $postal_code = $hotel[$i]['postal_code'];
        // amenities
        $amenities = $hotel[$i]['amenities'];
        $minibarinroom = $amenities['miniBarInRoom'];
        $parkinggarage = $amenities['parkingGarage'];
        $exteriorroomentrance = $amenities['exteriorRoomEntrance'];
        $drycleaning = $amenities['dryCleaning'];
        $sauna = $amenities['sauna'];
        $kitchen = $amenities['kitchen'];
        $familyrooms = $amenities['familyRooms'];
        $inroommovies = $amenities['inRoomMovies'];
        $nonsmokingrooms = $amenities['nonSmokingRooms'];
        $meetingrooms = $amenities['meetingRooms'];
        $gameroom = $amenities['gameRoom'];
        $outdoorpool = $amenities['outdoorPool'];
        $dataports = $amenities['dataPorts'];
        $golfcourse = $amenities['golfCourse'];
        $fitnessfacility = $amenities['fitnessFacility'];
        $tvinroom = $amenities['tVInRoom'];
        $petsallowed = $amenities['petsAllowed'];
        $handicapaccessible = $amenities['handicapAccessible'];
        $continentalbreakfast = $amenities['continentalBreakfast'];
        $combination = $amenities['combination'];
        $twentyfourhoursecurity = $amenities['twentyFourHourSecurity'];
        $carrentdesk = $amenities['carRentDesk'];
        $airconditioning = $amenities['airConditioning'];
        $tenniscourt = $amenities['tennisCourt'];
        $videocheckout = $amenities['videoCheckOut'];
        $inhousedining = $amenities['inHouseDining'];
        $voicemail = $amenities['voiceMail'];
        $businesscenter = $amenities['businessCenter'];
        $coffeeteamaker = $amenities['coffeeTeaMaker'];
        $hairdryer = $amenities['hairDryer'];
        $airporttransportation = $amenities['airportTransportation'];
        $valetparking = $amenities['valetParking'];
        $whirpool = $amenities['whirpool'];
        $childrenallowed = $amenities['childrenAllowed'];
        $restrictedaccess = $amenities['restrictedAccess'];
        $interiorroomentrance = $amenities['interiorRoomEntrance'];
        $indoorpool = $amenities['indoorPool'];
        $electronicroomkeys = $amenities['electronicRoomKeys'];
        $wakeupservice = $amenities['wakeUpService'];
        $inhousebar = $amenities['inHouseBar'];
        $safe = $amenities['safe'];
        $map = $amenities['map'];
        $roomservice = $amenities['roomService'];
        $clothingiron = $amenities['clothingIron'];
        // meta image details
        $meta_image_details = $hotel[$i]['meta_image_details'];
        $meta_image_details_count = $meta_image_details['count'];
        $meta_image_details_descs = $meta_image_details['descs'];
        $meta_image_details_prefix = $meta_image_details['prefix'];
        $meta_image_details_suffix = $meta_image_details['suffix'];
        // image details
        $image_details = $hotel[$i]['image_details'];
        $image_details_count = $image_details['count'];
        $image_details_prefix = $image_details['prefix'];
        $image_details_suffix = $image_details['suffix'];
        // regions
        $region_ids = $hotel[$i]['region_ids'];
        if (count($region_ids) > 0) {
            $region_id = "";
            for ($j=0; $j < count($region_ids); $j++) { 
                $region_id = $region_ids[$j];
            }
        }
        // destinations
        $destination_ids = $hotel[$i]['destination_ids'];
        if (count($destination_ids) > 0) {
            $destination_id = "";
            for ($j=0; $j < count($destination_ids); $j++) { 
                $destination_id = $destination_ids[$j];
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>