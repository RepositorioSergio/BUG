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
$access_token = "T1RLAQKTZ6gz/L9XvyVf3594eqV2cYxNiRDFg+aM/TaC/ik+hv83n/HiAADAMSIaX+ZwEYI5a3KjNzFm+l/bxubRBTCcLV+ol2SRZ/74CB5na3cN0iQ9qF85oUYZ5798sHNLP65AxeynE9Y07bUfEMA3/O7MNmGRCTSipPyLvUT6NZ7O97UIZmdNslCn7EkeE0hqMHR+vxcibsOIh6hGzaAmbQ1n37BXTghtesY8fjBBJKyDWFDCxhrnRgF7Vu7U5fblLk57izQARUU/dof5CzFz3vZAgui2rJ+Fn6InHqumtkMDgFfBBHkcDLp+";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/offers/getCabins';

$raw = '{
    "agencyPOS": {
        "pcc": "BJ1G",
        "currencyCode": "USD"
    },
    "sailingInfo": {
      "vendorCode": "RC",
      "voyageId": "MA01201002MA03S135",
      "selectedFareCodes": [
        "BVL"
      ],
      "selectedCategory": {
        "pricedCategoryCode": "GT",
        "berthedCategoryCode": "GT"
      }
    },
    "cabinQualifiers": {
        "cabinMediaFlag": true
    },
    "reservationInfo": {
        "guestCount": 1
    }
  }';

echo '<br/><br/>RAW: ' . $raw;

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
