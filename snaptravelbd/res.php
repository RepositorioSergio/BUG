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
echo "COMECOU BOOKING<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://b2b-api-staging.snaptravel.com/res';

$raw = '{
    "hotelId": 118903,
    "sessionId": "Yk9O-nRmdIBbByBdd8%27mNr-0cIqYR8-B2DcX1I-Acr%3AbdncB%7DYIf%7DI-yi1vY-nLB2N93N0ABcrv1Z8AU%7Dy%7DmN%3A-BiYqYNnLT%7D%3AI%3ANrcPuYvn-ncj-Ph8NM%3AMjIvCuQq7d1%7DNN8yJ1%3AyMMIZ1-M-O%7DI%7DMM131-ncZ%7Dy%7DmM%7Dc%22jI31%7DNyB-%3A%7DrNIcZBY%3AviMLj-Y%3AvNy-Uu1vn-N%7DU%7DP%27bNn4ZB1vNiE%27U%7DPi%3ANr-pcrvYZC47j0gAdn%27AB%7DByU%7D%7DJ%7Dn%2771L%7DJ%7DY31-D4BjI%3ACNL-Ju131RCLZ2DkrNIcAiI%3ANiMLBjL1QNycyi%3AIQ%7DNBZ2I%7DmML-giYv4iE%27U%7DPi%3ANr-pcrvYZC47j0gAdIAAB%7D%7DPJI4-iyZB1%3A1%3A-I3n%7Dncj%7DYvkMD%3AAiI3n-Dkj-%3AyrNI-BiY%3A4iM%27T%7Drv%7BNy-A2I%3AnNnLZ2D%27bM%7D--iIv7iE%27U%7DPi%3ANr-pcrvYZCz%3D",
    "room1": "2,8",
    "locale": "en_US",
    "countryCode": "US",
    "arrivalDate": "05/28/2020",
    "departureDate": "05/29/2020",
    "rateKey": "al%7B2%7B%7Btzp_tclSGlYk9O-nRmdIBbByBdd8%27mNr-0cIqYR8-B2DcX1I-Acr%3AbdncB%7DYIf%7DI-yi1vY-nLB2N93N0ABcrv1Z8AU%7Dy%7DmN%3A-BiYqYNnLT%7D%3AI%3ANrcPuYvn-ncj-Ph8NM%3AMjIvCuQq7d1%7DNN8yJ1%3AyMMIZ1-M-O%7DI%7DMM131-ncZ%7Dy%7DmM%7Dc%22jI31%7DNyB-%3A%7DrNIcZBY%3AviMLj-Y%3AvNy-Uu1vn-N%7DU%7DP%27bNn4ZB1vNiE%27U%7DPi%3ANr-pcrvYZC47j0gAdn%27AB%7DByU%7D%7DJ%7Dn%2771L%7DJ%7DY31-D4BjI%3ACNL-Ju131RCLZ2DkrNIcAiI%3ANiMLBjL1QNycyi%3AIQ%7DNBZ2I%7DmML-giYv4iE%27U%7DPi%3ANr-pcrvYZC47j0gAdIAAB%7D%7DPJI4-iyZB1%3A1%3A-I3n%7Dncj%7DYvkMD%3AAiI3n-Dkj-%3AyrNI-BiY%3A4iM%27T%7Drv%7BNy-A2I%3AnNnLZ2D%27bM%7D--iIv7iE%27U%7DPi%3ANr-pcrvYZCz%3Dl%2CGlKjk2_4KzkzlSGlO0R%227p-QRnZFuE%7DrjMq+RnAvYt-0i8d%3AUM1mZO%3ALBMd86NdmU7%2FkR0irBQ%3ADBX%2FCR8Iri0BIU7IJJ9z+Ot-DU7ICiM%7DDBtr%3AB7YX6Ndfi01kRDdmBOrvUNcDiNI8iP1CUMdJg0d8B7I3Bg%2FPcEA4hP3LP9vy2P%7D0cn-4cD191DqFuJzH%2F91%2FH%27%2FgOOzyO9BzR0%2FQAdz7%7D%7DBYY9m%22gd%3AWyUgOY91Bz7kUd-zUO91B23N9d%27%2Fy7jHWNIYsEJ%2FuO%27IQi7%2Ff6NH%3A6NYr%7D7%2F8U01vU0Hf%228HfU0Hf1z9g-MLJjI%3As1NyT%7D%3A1rMIi3uYvn1n4i%7DyYrNr-AcyLbNNgU2NykN0AAi%3ALsj8gBjIvKNr-KR%7Dg7UE%27T-PBXNr-0BY%3AsZCZJ-YLCNM4gcr%3A1JNgM-Y13NI-y21%3AvU7yUiI%3ACNL-piIv1%7Dn%27T%7DYrfNr-B21qnMN%7DT-rZ%7BNLcyc%3A4YZ8BBu%7DZbNphqTd%3D%3Dl%2CGlx22lSGp+LL5",
    "rateCode": "xwqW%3A2xsovm%3AsomrXvoucwmoqqWx%3AqCr2Wqc",
    "specialInformation": "",
    "affiliateConfirmationId": "AAA404",
    "AddressInfo": {
      "address1": "15 Test Dr."
    },
    "city": "Testville",
    "stateProvinceCode": "TV",
    "postalCode": "12345",
    "customerIpAddress": "193.12.12.130",
    "customerUserAgent": "Mozilla/5.0 (X11; Linux x86_64)",
    "ReservationInfo": {
      "email": "emailtom@gmail.com",
      "firstName": "Tom",
      "lastName": "Hanks",
      "homePhone": "123-123-1234",
      "creditCardToken": "450002DWIODQWIDJ3928",
      "creditCardIdentifier": "123",
      "creditCardExpirationMonth": "12",
      "creditCardExpirationYear": "2022"
    }
  }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "x-api-key: 1Yr3v5xEXGqwB8MD2g1n3oma0r3blov3Exgo0r86",
    "Content-Type: application/json",
    "version: 3",
    "Content-Length: " . strlen($raw)
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

echo $response;

$response = json_decode($response, true); 
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$itineraryId = $response['itineraryId'];
$ValueAdds = $response['ValueAdds'];
$arrivalDate = $response['arrivalDate'];
$departureDate = $response['departureDate'];
$errorText = $response['errorText'];
$hotelAddress = $response['hotelAddress'];
$hotelCity = $response['hotelCity'];
$hotelCountryCode = $response['hotelCountryCode'];
$hotelName = $response['hotelName'];
$hotelPostalCode = $response['hotelPostalCode'];
$hotelStateProvinceCode = $response['hotelStateProvinceCode'];
$rateOccupancyPerRoom = $response['rateOccupancyPerRoom'];
$reservationStatusCode = $response['reservationStatusCode'];
$roomDescription = $response['roomDescription'];
$RateInfos = $response['RateInfos'];
$RateInfo = $RateInfos['RateInfo'];
$nonRefundable = $RateInfo['nonRefundable'];
$ChargeableRateInfo = $RateInfo['ChargeableRateInfo'];
$currencyCode = $ChargeableRateInfo['currencyCode'];
$total = $ChargeableRateInfo['total'];

$confirmationNumber = "";
$confirmationNumbers = $response['confirmationNumbers'];
if (count($confirmationNumbers) > 0) {
    for ($i=0; $i < count($confirmationNumbers); $i++) { 
        $confirmationNumber = $confirmationNumbers[$i];
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>