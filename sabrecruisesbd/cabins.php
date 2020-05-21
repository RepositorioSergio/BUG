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
echo "COMECOU CABINS<br/>";
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

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/offers/getCabins';

$raw = '{
    "agencyPOS": {
        "pcc": "BJ1G",
        "currencyCode": "USD"
    },
    "sailingInfo": {
      "vendorCode": "RC",
      "voyageId": "QN01200901QN05I658",
      "selectedFareCodes": [
        "BVL"
      ],
      "selectedCategory": {
        "pricedCategoryCode": "RL",
        "berthedCategoryCode": "RL"
      }
    },
    "cabinQualifiers": {
        "cabinMediaFlag": true
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
$cabinOptions = $response['cabinOptions'];
if (count($cabinOptions) > 0) {
    for ($i=0; $i < count($cabinOptions); $i++) { 
        $cabinCategoryCode = $cabinOptions[$i]['cabinCategoryCode'];
        $cabinNum = $cabinOptions[$i]['cabinNum'];
        $deckName = $cabinOptions[$i]['deckName'];
        $deckNum = $cabinOptions[$i]['deckNum'];
        $groupCabinInd = $cabinOptions[$i]['groupCabinInd'];
        $minOccupancy = $cabinOptions[$i]['minOccupancy'];
        $maxOccupancy = $cabinOptions[$i]['maxOccupancy'];
        $remark = $cabinOptions[$i]['remark'];
        $status = $cabinOptions[$i]['status'];
        $measurementInfo = $cabinOptions[$i]['measurementInfo'];
        $dimensionInfo = $measurementInfo['dimensionInfo'];
        $unitOfMeasure = $measurementInfo['unitOfMeasure'];
        $unitOfMeasureCode = $measurementInfo['unitOfMeasureCode'];
        $unitOfMeasureQuantity = $measurementInfo['unitOfMeasureQuantity'];
        $cabinConfiguration = $cabinOptions[$i]['cabinConfiguration'];
        $accessibleCabinInd = $cabinConfiguration['accessibleCabinInd'];
        $bathCode = $cabinConfiguration['bathCode'];
        $bedCode = $cabinConfiguration['bedCode'];
        $connectedCabinNum = $cabinConfiguration['connectedCabinNum'];
        $smokingAllowed = $cabinConfiguration['smokingAllowed'];
        $bedConfiguration = $cabinConfiguration['bedConfiguration'];
        if (count($bedConfiguration) > 0) {
            for ($iAux=0; $iAux < count($bedConfiguration); $iAux++) { 
                $bedCode = $bedConfiguration[$iAux]['bedCode'];
                $bedCount = $bedConfiguration[$iAux]['bedCount'];
            }
        }
        $cabinAmenity = $cabinConfiguration['cabinAmenity'];
        if (count($cabinAmenity) > 0) {
            $amenity = "";
            for ($iAux2=0; $iAux2 < count($cabinAmenity); $iAux2++) { 
                $amenity = $cabinAmenity[$iAux2];
            }
        }
        $cabinLocation = $cabinConfiguration['cabinLocation'];
        if (count($cabinLocation) > 0) {
            $location = "";
            for ($iAux3=0; $iAux3 < count($cabinLocation); $iAux3++) { 
                $location = $cabinLocation[$iAux3];
            }
        }

        $mediaContentInfo = $cabinOptions[$i]['mediaContentInfo'];
        $deckMediaInfo = $mediaContentInfo['deckMediaInfo'];
        $sideView = $deckMediaInfo['sideView'];
        $topView = $deckMediaInfo['topView'];
        $categoryMediaInfo = $deckMediaInfo['categoryMediaInfo'];
        $name = $categoryMediaInfo['name'];
        $image = $categoryMediaInfo['image'];
        $floorPlan = $categoryMediaInfo['floorPlan'];
        $shortDesc = $categoryMediaInfo['shortDesc'];
        $fullDesc = $categoryMediaInfo['fullDesc'];
        $icon = $categoryMediaInfo['icon'];
        $iconColor = $categoryMediaInfo['iconColor'];
        $minBalconyArea = $categoryMediaInfo['minBalconyArea'];
        $maxBalconyArea = $categoryMediaInfo['maxBalconyArea'];
        $relatedCategories = $categoryMediaInfo['relatedCategories'];
        $groupName = $categoryMediaInfo['groupName'];

        $cabinMediaInfo = $categoryMediaInfo['cabinMediaInfo'];
        $image = $cabinMediaInfo['image'];
        $floorPlan = $cabinMediaInfo['floorPlan'];
        $totalArea = $cabinMediaInfo['totalArea'];
        $imageType = $cabinMediaInfo['imageType'];
        $imageCoordinates = $cabinMediaInfo['imageCoordinates'];
        $iconCoordinates = $cabinMediaInfo['iconCoordinates'];
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
