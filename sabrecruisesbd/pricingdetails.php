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
echo "COMECOU PRICING DETAILS<br/>";
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
$access_token = "T1RLAQKTZ6gz/L9XvyVf3594eqV2cYxNiRDFg+aM/TaC/ik+hv83n/HiAADAMSIaX+ZwEYI5a3KjNzFm+l/bxubRBTCcLV+ol2SRZ/74CB5na3cN0iQ9qF85oUYZ5798sHNLP65AxeynE9Y07bUfEMA3/O7MNmGRCTSipPyLvUT6NZ7O97UIZmdNslCn7EkeE0hqMHR+vxcibsOIh6hGzaAmbQ1n37BXTghtesY8fjBBJKyDWFDCxhrnRgF7Vu7U5fblLk57izQARUU/dof5CzFz3vZAgui2rJ+Fn6InHqumtkMDgFfBBHkcDLp+";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/orders/getPricingDetails';

$raw = '{
  "agencyPOS": {
    "pcc": "BJ1G",
    "currencyCode": "USD"
  },
  "sailingInfo": {
    "vendorCode": "RC",
    "voyageId": "MA01201002MA03S135",
    "agencyGroupId": "43562",
    "includeNonRefundablePromos": true,
    "selectedFareCodes": [
      "BVL"
    ],
    "selectedCategory": {
      "pricedCategoryCode": "GT",
      "berthedCategoryCode": "GT",
      "groupSeqNo": "1",
      "cabinNum": "8394",
      "swapWithCabinNum": "8394"
    }
  },
  "reservationInfo": {
    "guestsDetails": [
      {
        "guestNum": "1",
        "gender": "M",
        "age": "27",
        "personBirthDate": "1992-10-01",
        "loyaltyMembershipId": "MJ735S",
        "guestRefNum": "839571048",
        "guestActionCode": "A",
        "personName": {
          "namePrefix": "MR",
          "firstName": "Jhon",
          "middleName": "William",
          "lastName": "Doe",
          "nameSuffix": "Sr."
        },
        "nationality": {
          "countryCode": "US",
          "stateProvCode": "AL"
        },
        "selectedInsurance": {
          "type": "Y"
        },
        "selectedTransportation": {
          "mode": "O",
          "transportGroupSeqNo": "1",
          "cityCode": "PCV",
          "segmentDirection": "R",
          "fareCityCode": "PCV",
          "airAcommodation": {
            "airlineCabinClass": "Y",
            "departureCity": "PCV",
            "arrivalCity": "PCV"
          }
        }
      }
    ]
  }
}';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    'Authorization: Bearer ' . $access_token,
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $ipcc . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<br/>RESPONSE: ' . $response;

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$response = json_decode($response, true);

$otherPricingDetails = $response['otherPricingDetails'];
$bookingNonRefundableType = $otherPricingDetails['bookingNonRefundableType'];
$nonRefundableType = $otherPricingDetails['nonRefundableType'];
$bookingPrices = $response['bookingPrices'];
$currencyCode = $bookingPrices['currencyCode'];
$bookingPrice = $bookingPrices['bookingPrice'];
if (count($bookingPrice) > 0) {
    for ($i=0; $i < count($bookingPrice); $i++) { 
        $amount = $bookingPrice[$i]['amount'];
        $priceTypeCode = $bookingPrice[$i]['priceTypeCode'];
    }
}
$guestPrices = $response['guestPrices'];
if (count($guestPrices) > 0) {
    for ($j=0; $j < count($guestPrices); $j++) { 
        $guestNum = $guestPrices[$j]['guestNum'];
        $priceInfos = $guestPrices[$j]['priceInfos'];
        if (count($priceInfos) > 0) {
            for ($jAux=0; $jAux < count($priceInfos); $jAux++) { 
                $amount = $priceInfos[$jAux]['amount'];
                $priceTypeCode = $priceInfos[$jAux]['priceTypeCode'];
                $nonRefundableType = $priceInfos[$jAux]['nonRefundableType'];
            }
        }
        $appliedFareCodes = $guestPrices[$j]['appliedFareCodes'];
        if (count($appliedFareCodes) > 0) {
            for ($jAux2=0; $jAux2 < count($appliedFareCodes); $jAux2++) { 
                $fareCode = $appliedFareCodes[$jAux2]['fareCode'];
                $agencyGroupId = $appliedFareCodes[$jAux2]['agencyGroupId'];
            }
        }
    }
}
$paymentSchedules = $response['paymentSchedules'];
if (count($paymentSchedules) > 0) {
    for ($k=0; $k < count($paymentSchedules); $k++) { 
        $amount = $paymentSchedules[$k]['amount'];
        $dueDate = $paymentSchedules[$k]['dueDate'];
        $scheduleCode = $paymentSchedules[$k]['scheduleCode'];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
