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
echo "COMECOU MODIFY<br/>";
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
$access_token = "T1RLAQJZi0nR/Q9+880Jq2UK1v76ggsAJRCmheZlZJqS6TNvR47IjuUQAADAigAfu4oZE3tYVejeO+/R7aqJUVjlRus3tvBKeFxOiHu/YvNNMlm/10mWVUhLrFowve8+CnRmXV7zcSokvmmlyqd//2OLVlD84CUnn5Sqit/TGgKDOaY0mnv/aM86UPnQ0O5BaQwuiZG6qh6PDBgXi7zcGfN8xEfeXlOex3a2a8o/l+4TgB2RSmQW0/gCRU8+eMHT1KfObFk94Bngt6/b3PqoCU9L2u5AS/N0kXsbp2yRhyvNRqss8AgMfxwoZqSG";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/orders/modify';

$raw = '{
    "agencyPOS": {
        "pcc": "IA8H",
        "branchPcc": "IA8H",
        "branchPhoneNum": "999999999",
        "currencyCode": "USD"
    },
    "sailingInfo": {
      "vendorCode": "RC",
      "voyageId": "20200913RD07",
      "agencyGroupId": "43562",
      "selectedFareCodes": {
        "fareCodes": [
          "BESTFARE"
        ],
        "cityCode": "DFW"
      },
      "selectedCategories": [
        {
          "pricedCategoryCode": "B1",
          "berthedCategoryCode": "B1",
          "groupSeqNo": "1",
          "categoryGroupId": "43562",
          "includeNonRefundablePromos": true,
          "status": "11",
          "selectedCabins": [
            {
              "cabinNum": "101",
              "cabinBedConfigCode": "4",
              "status": "11"
            }
          ]
        }
      ],
      "transportationMode": "O"
    },
    "reservationInfo": {
      "reservationId": "H8737TWI",
      "resExtensionDate": "2019-12-01",
      "coupon": "CASINO",
      "selectedDining": "8",
      "guestDetails": [
        {
          "guestNum": "1",
          "gender": "M",
          "age": "27",
          "personBirthDate": "1992-10-01",
          "email": "johndoe@gmail.com",
          "loyaltyMembershipId": "MJ735S",
          "guestRefNum": "839571047",
          "occupationCode": "GVT",
          "coupon": "CASINO",
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
              "sitting": "8",
              "sittingInstance": "MY TIME",
              "sittingType": "Open",
              "smokingAllowed": false,
              "crossReferencingAllowed": true,
              "familyTimeIndicator": true,
              "prepaidGratuityRequired": true,
              "status": "11"
            }
          ],
          "selectedTransportationInfos": [
            {
              "mode": "O",
              "segmentDirection": "R",
              "fareCityCode": "DFW",
              "cityCode": "DFW",
              "transportGroupSeqNo": "1",
              "status": "11",
              "airAccommodation": {
                "airlineCabinClass": "Y",
                "departureCity": "DFW",
                "arrivalCity": "DFW"
              }
            }
          ],
          "selectedSpecialServices": [
            {
              "code": "Y",
              "type": "6",
              "date": "2019-08-31",
              "numOfYears": "10"
            }
          ],
          "selectedPackages": [
            {
              "code": "NOXFR",
              "type": "3",
              "duration": "2",
              "roomType": "1"
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
        "diningTableCode": "8",
        "smokingAllowed": false
      },
      "travelAgencyInfo": {
        "agentName": "John Doe",
        "branchPhoneNum": "1234567890",
        "phoneNumForCredit": "1234567890",
        "email": "johndoe@gmail.com"
      },
      "paymentInfo": {
        "creditCardInfos": [
          {
            "cardType": "VI",
            "cardNum": "4111111111111111",
            "expireYear": "22",
            "expireMonth": "03",
            "CVV": "000",
            "amount": "500.00",
            "currencyCode": "USD",
            "cardHolderInfo": {
              "name": "John Doe",
              "addressLine1": "123 Victoria street",
              "addressLine2": "123 Victoria street",
              "postalCode": "76890",
              "cityName": "Dallas",
              "stateProvCode": "AL",
              "countryCode": "US"
            },
            "guestLevelAllocatePayment": [
              {
                "guestNum": "1",
                "guestRefNum": "839571047",
                "amount": "1"
              }
            ]
          }
        ],
        "checkInfos": [
          {
            "checkNum": "839571047",
            "amount": "500.00",
            "currencyCode": "USD",
            "guestLevelAllocatePayment": [
              {
                "guestNum": "1",
                "guestRefNum": "839571047",
                "amount": "1"
              }
            ]
          }
        ],
        "groupDepositInfos": [
          {
            "paymentRefNum": "8354103",
            "paymentDistributionFlag": true,
            "guestLevelAllocatePayment": [
              {
                "guestNum": "1",
                "guestRefNum": "839571047",
                "amount": "1"
              }
            ]
          }
        ]
      }
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
$reservationPaymentInfos = $response['reservationPaymentInfos'];
$Description = $reservationPaymentInfos['Description'];
$paymentRefNum = $reservationPaymentInfos['paymentRefNum'];
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
