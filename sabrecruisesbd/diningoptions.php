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
echo "COMECOU DINING OPTIONS<br/>";
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
$access_token = "T1RLAQL5hLWEM0j35ibfKVCFvEseCXKJoxAy8wigVdi3NB6OkGUcCRg9AADA6731oUy1bncdEOnHYVRaTz8jIdqc+5ub7PYzi36iiTrGc7Qdf1d6HFll+tVPq3C6/pH5ZVCGN67yIaUEnN9csalpwT77r3zgzg5Ye+ggnabt94+o9YDigpalGaYbcGsNhrszNZ1YghdROkPSZk5XIqpoayvEU76G9XEcMSHqoqIstmcwafAdFcVoP0CR92jzYyS91UqMj3sqF5V4W4z/Q56td/EQ23oab407P1D7OQBGYxSRhkPCCbi7FcOQyupw";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/offers/getDiningOptions';

$raw = '{
    "agencyPOS": {
      "pcc": "BJ1G",
      "currencyCode": "USD"
    },
    "sailingInfo": {
      "vendorCode": "RC",
      "voyageId": "QN01200901QN05I658",
      "agencyGroupId": "43562",
      "selectedFareCodes": [
        "BVL"
      ],
      "selectedCategory": {
        "pricedCategoryCode": "RL",
        "berthedCategoryCode": "RL",
        "groupSeqNo": "1",
        "cabinNum": "8320"
      }
    },
    "reservationInfo": {
      "guestCount": 1
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

$reservationInfo = $response['reservationInfo'];
$reservationId = $reservationInfo['reservationId'];
$diningTableInfo = $response['diningTableInfo'];
$smokingAllowed = $diningTableInfo['smokingAllowed'];
$diningTableCodes = $diningTableInfo['diningTableCodes'];
if (count($diningTableInfo) > 0) {
    $diningTable = "";
    for ($i=0; $i < count($diningTableInfo); $i++) { 
        $diningTable = $diningTableInfo[$i];
    }
}
$diningOptions = $response['diningOptions'];
if (count($diningOptions) > 0) {
    for ($j=0; $j < count($diningOptions); $j++) { 
        $sitting = $diningOptions[$j]['sitting'];
        $sittingInstance = $diningOptions[$j]['sittingInstance'];
        $sittingType = $diningOptions[$j]['sittingType'];
        $smokingAllowed = $diningOptions[$j]['smokingAllowed'];
        $crossReferencingAllowed = $diningOptions[$j]['crossReferencingAllowed'];
        $familyTimeIndicator = $diningOptions[$j]['familyTimeIndicator'];
        $prepaidGratuityRequired = $diningOptions[$j]['prepaidGratuityRequired'];
        $status = $diningOptions[$j]['status'];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
