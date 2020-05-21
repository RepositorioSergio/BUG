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
echo "COMECOU FARECODES<br/>";
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
$access_token = "T1RLAQL1K3msyRKOC0Xks0hS2UiK6itTVBDcLx4NU+Nr95TDLBujyxd8AADALo5g0v4rWM9zkO00jU6QQR7ndhm4u1+4myZNxgtOWXFx9lREWWb9H5MOFsM5W4cdqj5pHctWilHDqqbXChGUbZpzV0/dbhN9ZzoWNqjhKV69r67AIi1rud30M45xhcfcssjyJtmT/rbdTqsPxuDTvOflzXTXgXDCBQlPOzxRlUshaEMgvwNrkITuaMEXbswN8ejoky4ci2kQgfGfJrsM73mUPGqrXRUlo6WK4lAAIGVwUd+9zucw4X6Yce9IlEhO";
$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/offers/getFareCodes';

$raw = '{
    "agencyPOS": {
      "pcc": "BJ1G",
      "currencyCode": "USD"
    },
    "sailingInfo": {
      "vendorCode": "HA",
      "voyageId": "V045",
      "agencyGroupId": "43562",
      "includeNonRefundablePromos": true,
      "fareCodeQualifier": {
        "cityCode": "YVR",
        "fareCodeRulesFlag": true
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

echo '<br/>RESPONSE: ' . $response;;

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

$additionalInfo = $response['additionalInfo'];
$portCharges = $additionalInfo['portCharges'];
$surCharges = $additionalInfo['surCharges'];
$transportationMode = $additionalInfo['transportationMode'];
$currencyCode = $additionalInfo['currencyCode'];
$fareCodeOption = $response['fareCodeOption'];
if (count($fareCodeOption) > 0) {
    for ($i=0; $i < count($fareCodeOption); $i++) { 
        $fareCode = $fareCodeOption[$i]['fareCode'];
        $fareCodeName = $fareCodeOption[$i]['fareCodeName'];
        $fareRemark = $fareCodeOption[$i]['fareRemark'];
        $fareCodeType = $fareCodeOption[$i]['fareCodeType'];
        $nonRefundableType = $fareCodeOption[$i]['nonRefundableType'];
        $fareCodeEligibility = $fareCodeOption[$i]['fareCodeEligibility'];
        $minOccupancy = $fareCodeOption[$i]['minOccupancy'];
        $maxOccupancy = $fareCodeOption[$i]['maxOccupancy'];
        $currencyCode = $fareCodeOption[$i]['currencyCode'];
        $status = $fareCodeOption[$i]['status'];
        $fareCodeValidity = $fareCodeOption[$i]['fareCodeValidity'];
        $effectiveDate = $fareCodeValidity['effectiveDate'];
        $discontinueDate = $fareCodeValidity['discontinueDate'];
        $agencyGroupInfos = $fareCodeOption[$i]['agencyGroupInfos'];
        if (count($agencyGroupInfos) > 0) {
            for ($iAux=0; $iAux < count($agencyGroupInfos); $iAux++) { 
                $groupId = $agencyGroupInfos[$iAux]['groupId'];
                $groupName = $agencyGroupInfos[$iAux]['groupName'];
            }
        }
        
        $promotionInfos = $fareCodeOption[$i]['promotionInfos'];
        if (count($promotionInfos) > 0) {
            for ($iAux2=0; $iAux2 < count($promotionInfos); $iAux2++) { 
                $promoType = $promotionInfos[$iAux2]['promoType'];
                $promoValue = $promotionInfos[$iAux2]['promoValue'];
            }
        }
        $combinableFareCodeInfos = $fareCodeOption[$i]['combinableFareCodeInfos'];
        if (count($combinableFareCodeInfos) > 0) {
            for ($iAux3=0; $iAux3 < count($combinableFareCodeInfos); $iAux3++) { 
                $fareCode = $combinableFareCodeInfos[$iAux3]['fareCode'];
                $fareCodeDesc = $combinableFareCodeInfos[$iAux3]['fareCodeDesc'];
                $fareCodeEligibility = $combinableFareCodeInfos[$iAux3]['fareCodeEligibility'];
                $fareCodeType = $combinableFareCodeInfos[$iAux3]['fareCodeType'];
            }
        }
        $transportationInfos = $fareCodeOption[$i]['transportationInfos'];
        if (count($transportationInfos) > 0) {
            $transportation = "";
            for ($iAux4=0; $iAux4 < count($transportationInfos); $iAux4++) { 
                $transportation = $transportationInfos[$iAux4];
            }
        }
        $ageRestrictionInfos = $fareCodeOption[$i]['ageRestrictionInfos'];
        if (count($ageRestrictionInfos) > 0) {
            $ageRestriction = "";
            for ($iAux5=0; $iAux5 < count($ageRestrictionInfos); $iAux5++) { 
                $ageRestriction = $ageRestrictionInfos[$iAux5];
            }
        }
        $fareCodeRules = $fareCodeOption[$i]['fareCodeRules'];
        if (count($fareCodeRules) > 0) {
            for ($iAux6=0; $iAux6 < count($fareCodeRules); $iAux6++) { 
                $type = $fareCodeRules[$iAux6]['type'];
                $text = $fareCodeRules[$iAux6]['text'];
                $restrictedCategories = $fareCodeRules[$iAux6]['restrictedCategories'];
                if (count($restrictedCategories) > 0) {
                    $restricted = "";
                    for ($iAux7=0; $iAux7 < count($restrictedCategories); $iAux7++) { 
                        $restricted = $restrictedCategories[$iAux7];
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
