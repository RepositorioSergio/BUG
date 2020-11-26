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

$url = 'https://api.viator.com/partner/bookings/book';

$raw = '{
    "productCode": "5010SYDNEY",
    "productOptionCode": "48HOUR",
    "currency": "AUD",
    "partnerBookingRef": "BR-581567752",
    "travelDate": "2020-11-30",
    "paxMix": [
      {
        "ageBand": "ADULT",
        "numberOfTravelers": 1
      }
    ],
    "languageGuide": {
      "type": "AUDIO",
      "language": "en",
      "legacyGuide": "en/SERVICE_AUDIO"
    },
    "bookingRef": "BR-581567752",
    "bookerInfo": {
      "firstName": "Simon",
      "lastName": "Nettle"
    },
    "bookingQuestionAnswers": [
      {
        "question": "AGEBAND",
        "answer": "ADULT",
        "travelerNum": 1
      },
      {
        "question": "FULL_NAMES_LAST",
        "answer": "Nettle",
        "travelerNum": 1
      },
      {
        "question": "FULL_NAMES_FIRST",
        "answer": "Simon",
        "travelerNum": 1
      },
      {
        "question": "SPECIAL_REQUIREMENTS",
        "answer": "NO",
        "travelerNum": 1
      }
    ],
    "communication": {
      "email": "snettle@tripadvisor.com",
      "phone": "+61 431532778"
    }
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

$bookingRef = $response['bookingRef'];
$currency = $response['currency'];
$bookingHoldInfo = $response['bookingHoldInfo'];
$availability = $bookingHoldInfo['availability'];
$status = $availability['status'];
$pricing = $bookingHoldInfo['pricing'];
$status = $pricing['status'];
$validUntil = $pricing['validUntil'];

$totalPrice = $response['totalPrice'];
$price = $totalPrice['price'];
$recommendedRetailPrice = $price['recommendedRetailPrice'];
$partnerNetPrice = $price['partnerNetPrice'];
$bookingFee = $price['bookingFee'];
$partnerTotalPrice = $price['partnerTotalPrice'];

$lineItems = $response['lineItems'];
if (count($lineItems) > 0) {
    for ($i=0; $i < count($lineItems); $i++) { 
        $ageBand = $lineItems[$i]['ageBand'];
        $numberOfTravelers = $lineItems[$i]['numberOfTravelers'];
        $subtotalPrice = $lineItems[$i]['subtotalPrice'];
        $price = $subtotalPrice['price'];
        $recommendedRetailPrice = $price['recommendedRetailPrice'];
        $partnerNetPrice = $price['partnerNetPrice'];
    }
}

$cancellationPolicy = $response['cancellationPolicy'];
$type = $cancellationPolicy['type'];
$description = $cancellationPolicy['description'];
$cancelIfBadWeather = $cancellationPolicy['cancelIfBadWeather'];
$cancelIfInsufficientTravelers = $cancellationPolicy['cancelIfInsufficientTravelers'];
$refundEligibility = $cancellationPolicy['refundEligibility'];
if (count($refundEligibility) > 0) {
    for ($j=0; $j < count($refundEligibility); $j++) { 
        $dayRangeMin = $refundEligibility[$j]['dayRangeMin'];
        $dayRangeMax = $refundEligibility[$j]['dayRangeMax'];
        $percentageRefundable = $refundEligibility[$j]['percentageRefundable'];
        $startTimestamp = $refundEligibility[$j]['startTimestamp'];
        $endTimestamp = $refundEligibility[$j]['endTimestamp'];
    }
}

$voucherInfo = $response['voucherInfo'];
$url = $voucherInfo['url'];
$format = $voucherInfo['format'];

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>