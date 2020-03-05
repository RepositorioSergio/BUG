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

$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url_login = 'http://test-api-zneith.zdev.tech/zauth/auth?username=clickandbook&password=Wz!8dYuXa';
   
$headers = array(
  'Content-Type' => 'application/json'
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url_login);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$token = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $return;
echo $token;

$url = 'http://test-api-zneith.zdev.tech/search/lowFares';

$raw = '{
    "pos": {
      "source": [
        {
          "requestorID": {
            "id": "2113855"
          }
        }
      ]
    },
    "originDestinationInformation": [
      {
        "departureDateTime": {
          "value": "20200610"
        },
        "originLocation": {
          "locationCode": "LIM"
        },
        "destinationLocation": {
          "locationCode": "CUZ"
        }
      },
      {
        "departureDateTime": {
          "value": "20200620"
        },
        "originLocation": {
          "locationCode": "CUZ"
        },
        "destinationLocation": {
          "locationCode": "LIM"
        }
      }
    ],
    "travelerInfoSummary": {
      "airTravelerAvail": [
        {
          "airTraveler": {
            "passengerTypeQuantity": {
              "code": "ADT",
              "quantity": 1
            }
          }
        }
      ]
    },
    "travelPreferences": [
      {
        "vendorPref": [
          {
            "code": "LA",
            "allianceAllowedInd": true
          }
        ],
        "cabinPref": [
          {
            "cabin": "Y"
          }
        ]
      }
    ]
  }';

$headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token,
    'Content-Length: ' . strlen($raw)
); 
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$response = json_decode($response, true);

$anyArray = array();
$count = 0;
$any2 = "";
$pricedItineraries = $response['pricedItineraries'];
$pricedItinerary = $pricedItineraries['pricedItinerary'];
if (count($pricedItinerary) > 0) {
  for ($i=0; $i < count($pricedItinerary); $i++) { 
    $airItinerary = $pricedItinerary[$i]['airItinerary'];
    $originDestinationOptions = $airItinerary['originDestinationOptions'];
    $originDestinationOption = $originDestinationOptions['originDestinationOption'];
    if (count($originDestinationOption) > 0) {
      for ($iAux=0; $iAux < count($originDestinationOption); $iAux++) { 
        $refNumber = $originDestinationOption[$iAux]['refNumber'];
        $rph = $originDestinationOption[$iAux]['rph'];
        echo $return;
        echo "RPH " . $rph;
        echo $return;
        $flightSegment = $originDestinationOption[$iAux]['flightSegment'];
        if (count($flightSegment) > 0) {
          foreach ($flightSegment as $key => $value)  { 
            $departureDateTime = $value['departureDateTime'];
            $arrivalDateTime = $value['arrivalDateTime'];
            $flightNumber = $value['flightNumber'];
            $fareBasisCode = $value['fareBasisCode'];
            $connectionType = $value['connectionType'];
            $departureAirport = $value['departureAirport'];
            $locationCodeDA = $departureAirport['locationCode'];
            $codeContextDA = $departureAirport['codeContext'];
            $arrivalAirport = $value['arrivalAirport'];
            $locationCodeAA = $arrivalAirport['locationCode'];
            $codeContextAA = $arrivalAirport['codeContext'];
            $operatingAirline = $value['operatingAirline'];
            $companyShortName = $operatingAirline['companyShortName'];
            $code = $operatingAirline['code'];
            $equipment = $value['equipment'];
            if (count($equipment) > 0) {
              foreach ($equipment as $key => $valueequipment) { 
                $airEquipType = $valueequipment['airEquipType'];
              }
            }
            $marketingAirline = $value['marketingAirline'];
            $companyShortNameMA = $marketingAirline['companyShortName'];
            $codeMA = $marketingAirline['code'];
            $bookingClassAvails = $value['bookingClassAvails'];
            if (count($bookingClassAvails) > 0) {
              foreach ($bookingClassAvails as $key => $valuebookingClassAvails) { 
                $cabinType = $valuebookingClassAvails['cabinType'];
                $bookingClassAvail = $valuebookingClassAvails['bookingClassAvail'];
                if (count($bookingClassAvail) > 0) {
                  foreach ($bookingClassAvail as $key => $valuebookingClassAvail) { 
                    $resBookDesigCode = $valuebookingClassAvail['resBookDesigCode'];
                  }
                }
              }
            }
            $comment = $value['comment'];
            $stopLocation = $value['stopLocation'];
            $anyextensions = "";
            $tpaextensions = $value['tpaextensions'];
            $any = $tpaextensions['any'];       
            if (count($any) > 0) {
              for ($iAux2=0; $iAux2 < count($any); $iAux2++) { 
                $anyextensions = htmlspecialchars($any[$iAux2]);
                echo $return;
                echo "ANY " . $anyextensions;
                echo $return;
                $anyArray[$count] = $anyextensions;
                $count = $count + 1;
              }
            }
          }
          if (count($anyArray) > 0) {
            $tam = count($anyArray);
      
            echo $return;
            echo "ANY2 " . $anyArray[$tam - 1];
            echo $return;
            $xml = $anyArray[$tam - 1];

            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($xml);
            $flightDetails = $inputDoc->getElementsByTagName("flightDetails");
            echo $return;
            echo "ENTROU " . $flightDetails->length;
            echo $return;
            $elapsedTime = $flightDetails->item(0)->getElementsByTagName("elapsedTime");
            if ($elapsedTime->length > 0) {
                $elapsedTime = $elapsedTime->item(0)->nodeValue;
            } else {
                $elapsedTime = "";
            }
            echo $return;
            echo "elapsedTime:" . $elapsedTime;
            echo $return;
            $brandedFare = $flightDetails->item(0)->getElementsByTagName("brandedFare");
            if ($brandedFare ->length > 0) {
              $brandName = $brandedFare->item(0)->getAttribute('brandName');
              $brandID = $brandedFare->item(0)->getAttribute('brandID');
            } else {
              $brandName = "";
              $brandID = "";
            }
            echo $return;
            echo "ID:" . $brandID;
            echo $return;
            $baggageInformationList = $flightDetails->item(0)->getElementsByTagName("baggageInformationList");
            if ($baggageInformationList->length > 0) {
              $baggageInformation = $baggageInformationList->item(0)->getElementsByTagName("baggageInformation");
              if ($baggageInformation->length > 0) {
                $pieces = $brandedFare->item(0)->getAttribute('pieces');
              } else {
                $pieces = "";
              }
            }
            $connectionLocationList = $flightDetails->item(0)->getElementsByTagName("connectionLocationList");
            if ($connectionLocationList->length > 0) {
              $connectionLocation = $connectionLocationList->item(0)->getElementsByTagName("connectionLocation");
              if ($connectionLocation->length > 0) {
                $minChangeTime = $connectionLocation->item(0)->getAttribute('minChangeTime');
                $locationCode = $connectionLocation->item(0)->getAttribute('locationCode');
                $codeContext = $connectionLocation->item(0)->getAttribute('codeContext');
              } else {
                $minChangeTime = "";
                $locationCode = "";
                $codeContext = "";
              }
            }   
          }
        }
      }
    }

    //airItineraryPricingInfo
    $airItineraryPricingInfo = $pricedItinerary[$i]['airItineraryPricingInfo'];
    $pricingSource = $airItineraryPricingInfo['pricingSource'];
    $validatingAirlineCode = $airItineraryPricingInfo['validatingAirlineCode'];
    $itinTotalFare = $airItineraryPricingInfo['itinTotalFare'];
    if (count($itinTotalFare) > 0) {
      foreach ($itinTotalFare as $key => $valueitinTotalFare) { 
        $baseFare = $valueitinTotalFare['baseFare'];
        $amountBF = $valueitinTotalFare['amount'];
        $equivFare = $valueitinTotalFare['equivFare'];
        $taxes = $valueitinTotalFare['taxes'];
        $amounttaxes = $taxes['amount'];
        $tax = $taxes['tax'];
        if (count($tax) > 0) {
          foreach ($tax as $key => $valuetax) { 
            $taxCode = $valuetax['taxCode'];
            $currencyCode = $valuetax['currencyCode'];
            $taxAmount = $valuetax['amount'];
          }
        }
        $totalFare = $valueitinTotalFare['totalFare'];
        $amountTF = $taxes['amount'];
        $fareBaggageAllowance = $valueitinTotalFare['fareBaggageAllowance'];
        $remark = $itinTotalFare[$j]['remark'];
      }
    }
    $priceRequestInformation = $airItineraryPricingInfo['priceRequestInformation'];
    $fareQualifier = $priceRequestInformation['fareQualifier'];
    $negotiatedFareCode = $priceRequestInformation['negotiatedFareCode'];
    $rebookOption = $priceRequestInformation['rebookOption'];
    //notes
    $notes = $pricedItinerary[$i]['notes'];
    //ticketingInfo
    $ticketingInfo = $pricedItinerary[$i]['ticketingInfo'];
    $pseudoCityCode = $ticketingInfo['pseudoCityCode'];
    $pricingSystem = $ticketingInfo['pricingSystem'];
    $pricingSystemCode = $pricingSystem['code'];
    $flightSegmentRefNumber = $ticketingInfo['flightSegmentRefNumber'];
    $travelerRefNumber = $ticketingInfo['travelerRefNumber'];
    $miscTicketingCode = $ticketingInfo['miscTicketingCode'];
    $deliveryInfo = $ticketingInfo['deliveryInfo'];
    $paymentType = $ticketingInfo['paymentType'];
  }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>