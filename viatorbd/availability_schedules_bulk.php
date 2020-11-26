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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'https://api.viator.com/partner/availability/schedules/bulk';

$raw = '{
    "productCodes": [
      "265910P1",
      "5010SYDNEY"
    ]
  }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'exp-api-key' => '5364bbaf-e4f7-4727-9e91-317e794dfbaa',
    'Accept-Language' => 'en-US',
    'Accept' => 'application/json;version=2.0'
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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$availabilitySchedules = $response['availabilitySchedules'];
if (count($availabilitySchedules) > 0) {
   for ($j=0; $j < count($availabilitySchedules); $j++) { 
        $productCode = $availabilitySchedules[$j]['productCode'];
        $currency = $availabilitySchedules[$j]['currency'];
        $bookableItems = $availabilitySchedules[$j]['bookableItems'];
        if (count($bookableItems) > 0) {
            for ($i=0; $i < count($bookableItems); $i++) { 
                $productOptionCode = $bookableItems[$i]['productOptionCode'];
                $seasons = $bookableItems[$i]['seasons'];
                if (count($seasons) > 0) {
                    for ($iAux=0; $iAux < count($seasons); $iAux++) { 
                        $startDate = $seasons[$iAux]['startDate'];
                        $endDate = $seasons[$iAux]['endDate'];
                        $pricingRecords = $seasons[$iAux]['pricingRecords'];
                        if (count($pricingRecords) > 0) {
                            for ($iAux2=0; $iAux2 < count($pricingRecords); $iAux2++) { 
                                    $daysOfWeek = $pricingRecords[$iAux2]['daysOfWeek'];
                                    if (count($daysOfWeek) > 0) {
                                        $dayOfWeek = "";
                                        for ($iAux3=0; $iAux3 < count($daysOfWeek); $iAux3++) { 
                                            $dayOfWeek = $daysOfWeek[$iAux3];
                                        }
                                    }
                                    $pricingDetails = $pricingRecords[$iAux2]['pricingDetails'];
                                    if (count($pricingDetails) > 0) {
                                        for ($iAux4=0; $iAux4 < count($pricingDetails); $iAux4++) { 
                                            $pricingPackageType = $pricingDetails[$iAux4]['pricingPackageType'];
                                            $minTravelers = $pricingDetails[$iAux4]['minTravelers'];
                                            $ageBand = $pricingDetails[$iAux4]['ageBand'];
                                            $price = $pricingDetails[$iAux4]['price'];
                                            $original = $price['original'];
                                            $recommendedRetailPrice = $original['recommendedRetailPrice'];
                                            $partnerNetPrice = $original['partnerNetPrice'];
                                            $bookingFee = $original['bookingFee'];
                                            $partnerTotalPrice = $original['partnerTotalPrice'];
                                        }
                                    }
                                    $unavailableDates = $pricingRecords[$iAux2]['unavailableDates'];
                                    if (count($unavailableDates) > 0) {
                                        for ($iAux5=0; $iAux5 < count($unavailableDates); $iAux5++) { 
                                            $date = $unavailableDates[$iAux5]['date'];
                                            $reason = $unavailableDates[$iAux5]['reason'];
                                        }
                                    }
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
echo 'Done';
?>