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
echo "COMECOU CREATE BOOK<br/>";
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
$access_token = "T1RLAQJFOmb05syXtpjxkLPaNMg8lpHpaxDA81vlNKc/p5iMxCHCeDsaAADARtmnPm+swb77ak1F5zIwqauBJZcR9oJxqiMe+MhmFWhP0yw8wJa5AwZpKMykWyI10aaicWXOzeZLFsdeghlL12saLzu2aSuUzUlyYPPvT1k9H1Im4SLgIp5GyIJLyxr4deYYyrlZr0MSgUiSxmUMPIQ2knWf21YJ8LWodOPKgRN7dCA+JOOgP2rysy2+4k9moOUKDhKMdASaJ+R73tJ2ASnJl3W8hly3V2LxsecvKVk2ajNopzizQlnzvIPlTSF/";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/orders/create';

$raw = '{
    "agencyPOS": {
      "pcc": "IA8H",
      "currencyCode": "USD"
    },
    "sailingInfo": {
      "vendorCode": "RC",
      "voyageId": "SC01200701SC04I497",
      "agencyGroupId": "43562",
      "selectedFareCodes": {
        "fareCodes": [
          "G0734489"
        ],
        "cityCode": "SHA"
      },
      "selectedCategories": [
        {
          "pricedCategoryCode": "OS",
          "berthedCategoryCode": "OS",
          "groupSeqNo": "1",
          "categoryGroupId": "43562",
          "includeNonRefundablePromos": true,
          "status": "1",
          "selectedCabins": [
            {
              "cabinNum": "15544",
              "cabinBedConfigCode": "4",
              "status": "1"
            }
          ]
        }
      ],
      "transportationMode": "O"
    },
    "reservationInfo": {
      "selectedDining": "11",
      "guestDetails": [
        {
          "guestNum": "1",
          "gender": "M",
          "age": "27",
          "personBirthDate": "1992-10-01",
          "email": "johndoe@gmail.com",
          "loyaltyMembershipId": "MJ735S",
          "guestRefNum": "839571047",
          "selectedInsurance": "Y",
          "guestActionCode": "A",
          "personName": {
            "namePrefix": "MR",
            "firstName": "Jhon",
            "middleName": "William",
            "lastName": "Doe",
            "nameSuffix": "Sr."
          },
          "contactInfo": {
            "guestContact": {
              "type": "7",
              "number": "999999999"
            },
            "emergencyContact": {
              "name": "John Doe",
              "number": "999999999"
            }
          },
          "nationality": {
            "countryCode": "US",
            "stateProvCode": "AL"
          },
          "selectedDiningInfos": [
            {
              "sitting": "11",
              "sittingInstance": "05:30 PM",
              "sittingType": "Traditional",
              "smokingAllowed": false,
              "crossReferencingAllowed": true,
              "familyTimeIndicator": true,
              "prepaidGratuityRequired": false,
              "status": "1"
            }
          ],
          "selectedTransportationInfos": [
            {
              "mode": "O",
              "segmentDirection": "R",
              "fareCityCode": "CLP",
              "cityCode": "CLP",
              "transportGroupSeqNo": "1",
              "status": "1",
              "airAccommodation": {
                "airlineCabinClass": "Y",
                "departureCity": "CLP",
                "arrivalCity": "CLP"
              }
            }
          ],
          "address": {
            "addressLine1": "123 Victoria street",
            "addressLine2": "123 Victoria street",
            "postalCode": "76890",
            "cityName": "Dallas",
            "stateProvCode": "AL",
            "countryCode": "US"
          },
          "immigrationDocument": {
            "placeOfBithCountryCode": "US",
            "documentIssueCountryCode": "US",
            "documentNum": "J12H272",
            "issueDate": "2010-12-01",
            "expireDate": "2030-12-01",
            "socialSecurityNum": "23473246"
          }
        }
      ],
      "travelWithResId": [
        "H8737TWI"
      ],
      "diningTableInfo": {
        "smokingAllowed": false
      },
      "travelAgencyInfo": {
        "agentName": "John Doe",
        "branchPhoneNum": "1234567890",
        "phoneNumForCredit": "1234567890",
        "email": "johndoe@gmail.com"
      }
    }
  }';

echo '<br/> RAW: ' . $raw;

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

echo '<xmp>';
var_dump($response);
echo '</xmp>';

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

$reservationConfirmation = $response['reservationConfirmation'];
$reservationId = $reservationConfirmation['reservationId'];
$status = $reservationConfirmation['status'];
$agencyGroupId = $reservationConfirmation['agencyGroupId'];
$nonRefundableType = $reservationConfirmation['nonRefundableType'];
$reservationNonRefundableType = $reservationConfirmation['reservationNonRefundableType'];
$bookedGuests = $reservationConfirmation['bookedGuests'];
if (count($bookedGuests) > 0) {
    for ($i=0; $i < count($bookedGuests); $i++) { 
        $guestNum = $bookedGuests[$i]['guestNum'];
        $guestRefNum = $bookedGuests[$i]['guestRefNum'];
        $status = $bookedGuests[$i]['status'];
        $reservationId = $bookedGuests[$i]['reservationId'];
        $fareCode = $bookedGuests[$i]['fareCode'];
        $agencyGroupId = $bookedGuests[$i]['agencyGroupId'];
    }
}

$policyInfos = $response['policyInfos'];
if (count($policyInfos) > 0) {
    for ($j=0; $j < count($policyInfos); $j++) { 
        $code = $policyInfos[$j]['code'];
        $text = $policyInfos[$j]['text'];
    }
}
$paymentInfo = $response['paymentInfo'];
$Description = $paymentInfo['Description'];
$paymentRefNum = $paymentInfo['paymentRefNum'];
if (oount($paymentRefNum) > 0) {
    $paymentRef = "";
    for ($k=0; $k < oount($paymentRefNum); $k++) { 
        $paymentRef = $paymentRefNum[$k];
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
