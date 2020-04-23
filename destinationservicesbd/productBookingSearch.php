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
echo "COMECOU PRODUCT BOOKING<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$signature = "";
$word = "";
date_default_timezone_set('UTC');
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");
$accessKey = "709cc0c1189a46cca41796193c4f19af";
$secretKey = "7a846c68ec6b4a7ba964d3856307a54f";
$method = "POST";
$path = "/booking.json/product-booking-search";

$word = $date . "" . $accessKey . "" . $method . "" . $path;

$signature = hash_hmac("sha1", $word, $secretKey,true);
$signature = base64_encode($signature);


$url = "https://api.bokun.io";

$raw = '{
  "confirmationCode": "ABC2234-456",
  "textFilter": "TEST",
  "creationDateRange": {
    "from": "2020-06-01",
    "to": "2020-06-10"
  },
  "startDateRange": {
    "from": "2020-07-01",
    "to": "2020-07-19"
  },
  "productIds": [123,345],
  "page": 1,
  "pageSize": 100
}';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'X-Bokun-Date: ' . $date,
    'X-Bokun-AccessKey: ' . $accessKey,
    'X-Bokun-Signature: ' . $signature,
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8',
    'Content-Length: ' . strlen($raw)
));
$client->setUri($url . '/booking.json/product-booking-search');
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

die();
$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

if (count($response) > 0) {
    for ($i=0; $i < count($response); $i++) { 
        $tookInMillis = $response[$i]['tookInMillis'];
        $totalHits = $response[$i]['totalHits'];
        $query = $response[$i]['query'];
        $confirmationCode = $query['confirmationCode'];
        $bookingRole = $query['bookingRole'];
        $excludeComboBookings = $query['excludeComboBookings'];
        $noteQuery = $query['noteQuery'];
        $page = $query['page'];
        $pageSize = $query['pageSize'];
        $productExternalId = $query['productExternalId'];
        $productTitle = $query['productTitle'];
        $productType = $query['productType'];
        $textFilter = $query['textFilter'];
        $bookedExtrasIds = $query['bookedExtrasIds'];
        if (count($bookedExtrasIds) > 0) {
            $bookedExtrasId = "";
            for ($iAux=0; $iAux < count($bookedExtrasIds); $iAux++) { 
                $bookedExtrasId = $bookedExtrasIds[$iAux];
            }
        }
        $bookingStatuses = $query['bookingStatuses'];
        if (count($bookingStatuses) > 0) {
            $bookingStatus = "";
            for ($iAux=0; $iAux < count($bookingStatuses); $iAux++) { 
                $bookingStatus = $bookingStatuses[$iAux];
            }
        }
        $creationDateRange = $query['creationDateRange'];
        $from = $creationDateRange['from'];
        $to = $creationDateRange['to'];
        $includeLower = $creationDateRange['includeLower'];
        $includeUpper = $creationDateRange['includeUpper'];
        $fields = $query['fields'];
        $productIds = $query['productIds'];
        if (count($productIds) > 0) {
            $productId = "";
            for ($iAux=0; $iAux < count($productIds); $iAux++) { 
                $productId = $productIds[$iAux];
            }
        }
        $sortFields = $query['sortFields'];
        if (count($sortFields) > 0) {
            for ($iAux2=0; $iAux2 < count($sortFields); $iAux2++) { 
                $name = $sortFields[$iAux2]['name'];
                $order = $sortFields[$iAux2]['order'];
            }
        }
        $startDateRange = $query['startDateRange'];
        $startDateRangefrom = $startDateRange['from'];
        $startDateRangeto = $startDateRange['to'];
        $startDateRangeincludeLower = $startDateRange['includeLower'];
        $startDateRangeincludeUpper = $startDateRange['includeUpper'];
        //results
        $results = $response[$i]['results'];
        if (count($results) > 0) {
            for ($iAux3=0; $iAux3 < count($results); $iAux3++) { 
                $id = $results[$iAux3]['id'];
                $confirmationCode = $results[$iAux3]['confirmationCode'];
                $affiliateCommission = $results[$iAux3]['affiliateCommission'];
                $agentCommission = $results[$iAux3]['agentCommission'];
                $boxBooking = $results[$iAux3]['boxBooking'];
                $cancelNote = $results[$iAux3]['cancelNote'];
                $cancellationDate = $results[$iAux3]['cancellationDate'];
                $cancelledBy = $results[$iAux3]['cancelledBy'];
                $contactDetailsHidden = $results[$iAux3]['contactDetailsHidden'];
                $contactDetailsHiddenUntil = $results[$iAux3]['contactDetailsHiddenUntil'];
                $creationDate = $results[$iAux3]['creationDate'];
                $currency = $results[$iAux3]['currency'];
                $discountAmount = $results[$iAux3]['discountAmount'];
                $discountPercentage = $results[$iAux3]['discountPercentage'];
                $startDate = $results[$iAux3]['startDate'];
                $endDate = $results[$iAux3]['endDate'];
                $externalBookingReference = $results[$iAux3]['externalBookingReference'];
                $paidAmount = $results[$iAux3]['paidAmount'];
                $paidType = $results[$iAux3]['paidType'];
                $parentBookingId = $results[$iAux3]['parentBookingId'];
                $prepaid = $results[$iAux3]['prepaid'];
                $productCategory = $results[$iAux3]['productCategory'];
                $productConfirmationCode = $results[$iAux3]['productConfirmationCode'];
                $productExternalId = $results[$iAux3]['productExternalId'];
                $productType = $results[$iAux3]['productType'];
                $resellerPaidType = $results[$iAux3]['resellerPaidType'];
                $resold = $results[$iAux3]['resold'];
                $sellerCommission = $results[$iAux3]['sellerCommission'];
                $status = $results[$iAux3]['status'];
                $totalPrice = $results[$iAux3]['totalPrice'];
                $unconfirmedPayments = $results[$iAux3]['unconfirmedPayments'];
                //agent
                $agent = $results[$iAux3]['agent'];
                $agentid = $agent['id'];
                $agentcontractId = $agent['contractId'];
                $agentcontractType = $agent['contractType'];
                $agenttitle = $agent['title'];
                $agentUser = $results[$iAux3]['agentUser'];
                $agentUserid = $agentUser['id'];
                $agentUsername = $agentUser['name'];
                $assignedResources = $results[$iAux3]['assignedResources'];
                if (count($assignedResources) > 0) {
                    for ($iAux4=0; $iAux4 < count($assignedResources); $iAux4++) { 
                        $id = $assignedResources[$iAux4]['id'];
                        $title = $assignedResources[$iAux4]['title'];
                        $type = $assignedResources[$iAux4]['type'];
                    }
                }
                $boxProduct = $results[$iAux3]['boxProduct'];
                $boxProductid = $boxProduct['id'];
                $boxProductprice = $boxProduct['price'];
                $boxProducttitle = $boxProduct['title'];
                $flags = $boxProduct['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
                $boxSupplier = $results[$iAux3]['boxSupplier'];
                $boxSupplierid = $boxSupplier['id'];
                $boxSupplierprice = $boxSupplier['price'];
                $boxSuppliertitle = $boxSupplier['title'];
                $flags = $boxSupplier['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
                $channel = $results[$iAux3]['channel'];
                $channelid = $channel['id'];
                $channelprice = $channel['price'];
                $channeltitle = $channel['title'];
                $flags = $channel['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
                $id = $results[$iAux3]['id'];
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