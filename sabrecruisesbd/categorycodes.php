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
echo "COMECOU CATEGORY<br/>";
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

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/offers/getCategoryCodes';

$raw = '{
    "agencyPOS": {
      "pcc": "BJ1G",
      "currencyCode": "USD"
    },
    "sailingInfo": {
      "vendorCode": "RC",
      "voyageId": "MA01201002MA03S135",
      "includeNonRefundablePromos": true,
      "categoryCodeQualifier": {
        "categoryMediaFlag": true,
        "selectedFareCodes": {
          "fareCodes": [
            "BVL"
          ],
          "cityCode": "PCV"
        }
      }
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

echo '<br/><br/>RESPONSE: ' . $response;

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

$fareCodeOptions = $response['fareCodeOptions'];
if (count($fareCodeOptions) > 0) {
    for ($i=0; $i < count($fareCodeOptions); $i++) { 
        $fareCode = $fareCodeOptions[$i]['fareCode'];
        $fareCodeName = $fareCodeOptions[$i]['fareCodeName'];
        $fareCodeType = $fareCodeOptions[$i]['fareCodeType'];
        $transportationMode = $fareCodeOptions[$i]['transportationMode'];
        $transportationCityCode = $fareCodeOptions[$i]['transportationCityCode'];
        $minOccupancy = $fareCodeOptions[$i]['agencyGroupId'];
        $currencyCode = $fareCodeOptions[$i]['currencyCode'];
        $categoryOptions = $fareCodeOptions[$i]['categoryOptions'];
        if (count($categoryOptions) > 0) {
            for ($iAux=0; $iAux < count($categoryOptions); $iAux++) { 
                $pricedCategoryCode = $categoryOptions[$iAux]['pricedCategoryCode'];
                $berthedCategoryCode = $categoryOptions[$iAux]['berthedCategoryCode'];
                $categoryName = $categoryOptions[$iAux]['categoryName'];
                $categoryLocation = $categoryOptions[$iAux]['categoryLocation'];
                $deckName = $categoryOptions[$iAux]['deckName'];
                $groupSeqNo = $categoryOptions[$iAux]['groupSeqNo'];
                $agencyGroupId = $categoryOptions[$iAux]['agencyGroupId'];
                $shareType = $categoryOptions[$iAux]['shareType'];
                $minOccupancy = $categoryOptions[$iAux]['minOccupancy'];
                $maxOccupancy = $categoryOptions[$iAux]['maxOccupancy'];
                $gtyCategory = $categoryOptions[$iAux]['gtyCategory'];
                $categoryEligibility = $categoryOptions[$iAux]['categoryEligibility'];
                $categoryHref = $categoryOptions[$iAux]['categoryHref'];
                $status = $categoryOptions[$iAux]['status'];
                $airInfo = $categoryOptions[$iAux]['airInfo'];
                $cityName = $airInfo['cityName'];
                $availabilityInd = $airInfo['availabilityInd'];
                $airClass = $airInfo['airClass'];
                if (count($airClass) > 0) {
                    for ($iAux2=0; $iAux2 < count($airClass); $iAux2++) { 
                        $amount = $airClass[$iAux2]['amount'];
                        $classCode = $airClass[$iAux2]['classCode'];
                        $journeyType = $airClass[$iAux2]['journeyType'];
                    }
                }
                $mediaContentInfo = $categoryOptions[$iAux]['mediaContentInfo'];
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

                $priceInfos = $categoryOptions[$iAux]['priceInfos'];
                if (count($priceInfos) > 0) {
                    for ($iAux3=0; $iAux3 < count($priceInfos); $iAux3++) { 
                        $amount = $priceInfos[$iAux3]['amount'];
                        $currencyCode = $priceInfos[$iAux3]['currencyCode'];
                        $breakDownType = $priceInfos[$iAux3]['breakDownType'];
                        $fareCode = $priceInfos[$iAux3]['fareCode'];
                        $fareCodeName = $priceInfos[$iAux3]['fareCodeName'];
                        $fareCodeType = $priceInfos[$iAux3]['fareCodeType'];
                        $nonRefundableType = $priceInfos[$iAux3]['nonRefundableType'];
                        $status = $priceInfos[$iAux3]['status'];
                        $promoInfos = $priceInfos[$iAux3]['promoInfos'];
                        if (count($promotionInfos) > 0) {
                            for ($iAux2=0; $iAux2 < count($promotionInfos); $iAux2++) { 
                                $promoType = $promotionInfos[$iAux2]['promoType'];
                                $promoValue = $promotionInfos[$iAux2]['promoValue'];
                            }
                        }
                        $priceInfoTransportationOverrides = $priceInfos[$iAux3]['priceInfoTransportationOverrides'];
                        if (count($priceInfoTransportationOverrides) > 0) {
                            for ($iAux4=0; $iAux4 < count($priceInfoTransportationOverrides); $iAux4++) { 
                                $transportationMode = $priceInfoTransportationOverrides[$iAux4]['transportationMode'];
                                $amount = $priceInfoTransportationOverrides[$iAux4]['amount'];
                                $gtyCategory = $priceInfoTransportationOverrides[$iAux4]['gtyCategory'];
                            }
                        }
                    }
                }
                $promotionalAmenityInfos = $categoryOptions[$iAux]['promotionalAmenityInfos'];
                if (count($promotionalAmenityInfos) > 0) {
                    for ($iAux5=0; $iAux5 < count($promotionalAmenityInfos); $iAux5++) { 
                        $autoIncluded = $promotionalAmenityInfos[$iAux5]['autoIncluded'];
                        $amenityCode = $promotionalAmenityInfos[$iAux5]['amenityCode'];
                        $amenityDesc = $promotionalAmenityInfos[$iAux5]['amenityDesc'];
                        $amount = $promotionalAmenityInfos[$iAux5]['amount'];
                        $voyageId = $promotionalAmenityInfos[$iAux5]['voyageId'];
                    }
                }
                $categoryAmenityInfos = $categoryOptions[$iAux]['categoryAmenityInfos'];
                if (count($categoryAmenityInfos) > 0) {
                    $categoryAmenity = "";
                    for ($iAUX6=0; $iAUX6 < count($categoryAmenityInfos); $iAUX6++) { 
                        $categoryAmenity = $categoryAmenityInfos[$iAUX6];
                    }
                }
                $transportationOverrideInfos = $categoryOptions[$iAux]['transportationOverrideInfos'];
                if (count($transportationOverrideInfos) > 0) {
                    for ($iAux7=0; $iAux7 < count($transportationOverrideInfos); $iAux7++) { 
                        $minOccupancy = $transportationOverrideInfos[$iAux7]['minOccupancy'];
                        $maxOccupancy = $transportationOverrideInfos[$iAux7]['maxOccupancy'];
                        $pricedCategoryCode = $transportationOverrideInfos[$iAux7]['pricedCategoryCode'];
                        $agencyGroupId = $transportationOverrideInfos[$iAux7]['agencyGroupId'];
                        $fareCode = $transportationOverrideInfos[$iAux7]['fareCode'];
                        $transportationMode = $transportationOverrideInfos[$iAux7]['transportationMode'];
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
