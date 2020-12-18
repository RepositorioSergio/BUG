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
echo "COMECOU SEARCH<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url_session = 'https://restapidemo.myfarebox.com/api/CreateSession';

$raw_session = '{
    "Password": "TRVL2020@xml",
    "AccountNumber": "MCN001624",
    "UserName": "TravelM_XML"
  }';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept' => 'application/json',
    'Content-type' => 'application/json',
    'Content-Length' => strlen($raw_session)
));
$client->setUri($url_session);
$client->setMethod('POST');
$client->setRawBody($raw_session);
$response2 = $client->send();
if ($response2->isSuccess()) {
    $response2 = $response2->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response2->getStatusCode() . " - " . $response2->getReasonPhrase());
    echo $return;
    echo $response2->getStatusCode() . " - " . $response2->getReasonPhrase();
    echo $return;
    die();
}

echo '<xmp>';
var_dump($response2);
echo '</xmp>';

$response2 = json_decode($response2, true);
$Data = $response2['Data'];
$SessionId = $Data['SessionId'];

$url = 'https://restapidemo.myfarebox.com/api/v2/Search/Flight';

$raw = ' {
    "OriginDestinationInformations": [
      {
        "DepartureDateTime": "2021-02-21T10:00:00",
        "OriginLocationCode": "BLR",
        "DestinationLocationCode": "DXB"
  },
     {
        "DepartureDateTime": "2021-02-22T10:00:00",
        "OriginLocationCode": "DXB",
        "DestinationLocationCode": "BLR"
      }
    ],
    "TravelPreferences": {
      "MaxStopsQuantity": "Direct",
      "VendorPreferenceCodes": [
        "EK"
         ],
      "CabinPreference": "Y",
      "Preferences": {
        "CabinClassPreference": {
          "CabinType": "Y",
          "PreferenceLevel": "Restricted"
        }
      },
      "AirTripType": "Return"
    },
    "PricingSourceType": "Public",
    "IsRefundable": true,
    "PassengerTypeQuantities": [
      {
        "Code": "ADT",
        "Quantity": 1
      }
    ],
    "RequestOptions": "Fifty",
    "NearByAirports": true,
    "Target": "Test",
    "ConversationId": "AAA1"
  }';

$headers = array(
    "Accept: application/json",
    "Content-type: application/json",
    "Authorization: Bearer " . $SessionId,
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);
echo "Response- " . $response;

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$response = json_decode($response, true);
$Success = $response['Success'];
if ($Success === "true") {
    $Data = $response['Data'];
    $ConversationId = $Data['ConversationId'];
    $FlightSegmentList = $Data['FlightSegmentList'];
    if (count($FlightSegmentList) > 0) {
        for ($i=0; $i < count($FlightSegmentList); $i++) { 
            $DepartureAirportLocationCode = $FlightSegmentList[$i]['DepartureAirportLocationCode'];
            $ArrivalAirportLocationCode = $FlightSegmentList[$i]['ArrivalAirportLocationCode'];
            $DepartureDateTime = $FlightSegmentList[$i]['DepartureDateTime'];
            $ArrivalDateTime = $FlightSegmentList[$i]['ArrivalDateTime'];
            $stops = $FlightSegmentList[$i]['stops'];
            $JourneyDuration = $FlightSegmentList[$i]['JourneyDuration'];
            $Equipment = $FlightSegmentList[$i]['Equipment'];
            $OperatingCarrierCode = $FlightSegmentList[$i]['OperatingCarrierCode'];
            $OperatingFlightNumber = $FlightSegmentList[$i]['OperatingFlightNumber'];
            $MarketingCarriercode = $FlightSegmentList[$i]['MarketingCarriercode'];
            $MarketingFlightNumber = $FlightSegmentList[$i]['MarketingFlightNumber'];
            $SegmentRef = $FlightSegmentList[$i]['SegmentRef'];
        }
    }
    $ItineraryReferenceList = $Data['ItineraryReferenceList'];
    if (count($ItineraryReferenceList) > 0) {
        for ($j=0; $j < count($ItineraryReferenceList); $j++) { 
            $CabinClassCode = $ItineraryReferenceList[$j]['CabinClassCode'];
            $RBD = $ItineraryReferenceList[$j]['RBD'];
            $FareFamily = $ItineraryReferenceList[$j]['FareFamily'];
            $SeatsRemaining = $ItineraryReferenceList[$j]['SeatsRemaining'];
            $FareAttribute = $ItineraryReferenceList[$j]['FareAttribute'];
            $ItineraryRef = $ItineraryReferenceList[$j]['ItineraryRef'];
            $CheckinBaggage = $ItineraryReferenceList[$j]['CheckinBaggage'];
            if (count($CheckinBaggage) > 0) {
                for ($jAux=0; $jAux < count($CheckinBaggage); $jAux++) { 
                    $Type = $CheckinBaggage[$jAux]['Type'];
                    $Value = $CheckinBaggage[$jAux]['Value'];
                }
            }
            $CabinBaggage = $ItineraryReferenceList[$j]['CabinBaggage'];
            if (count($CabinBaggage) > 0) {
                for ($jAux2=0; $jAux2 < count($CabinBaggage); $jAux2++) { 
                    $Type = $CabinBaggage[$jAux2]['Type'];
                    $Value = $CabinBaggage[$jAux2]['Value'];
                }
            }
        }
    }
    $FulfillmentDetailsList = $Data['FulfillmentDetailsList'];
    if (count($FulfillmentDetailsList) > 0) {
        for ($k=0; $k < count($FulfillmentDetailsList); $k++) { 
            $ExchangeTATinMinutes = $FulfillmentDetailsList[$k]['ExchangeTATinMinutes'];
            $RefundTATinMinutes = $FulfillmentDetailsList[$k]['RefundTATinMinutes'];
            $TicketingTATinMinutes = $FulfillmentDetailsList[$k]['TicketingTATinMinutes'];
            $VoidTATinMinutes = $FulfillmentDetailsList[$k]['VoidTATinMinutes'];
            $FulfillmentDetailsRef = $FulfillmentDetailsList[$k]['FulfillmentDetailsRef'];
        }
    }
    $PenaltiesInfoList = $Data['PenaltiesInfoList'];
    if (count($PenaltiesInfoList) > 0) {
        for ($x=0; $x < count($PenaltiesInfoList); $x++) { 
            $PenaltiesInfoRef = $PenaltiesInfoList[$x]['PenaltiesInfoRef'];
            $Penaltydetails = $PenaltiesInfoList[$x]['Penaltydetails'];
            if (count($Penaltydetails) > 0) {
                for ($xAux=0; $xAux < count($Penaltydetails); $xAux++) { 
                    $PaxType = $Penaltydetails[$xAux]['PaxType'];
                    $RefundPenaltyAmount = $Penaltydetails[$xAux]['RefundPenaltyAmount'];
                    $RefundAllowed = $Penaltydetails[$xAux]['RefundAllowed'];
                    $Currency = $Penaltydetails[$xAux]['Currency'];
                    $ChangePenaltyAmount = $Penaltydetails[$xAux]['ChangePenaltyAmount'];
                    $ChangeAllowed = $Penaltydetails[$xAux]['ChangeAllowed'];
                }
            }
        }
    }
    $FlightFaresList = $Data['FlightFaresList'];
    if (count($FlightFaresList) > 0) {
        for ($w=0; $w < count($FlightFaresList); $w++) { 
            $FareType = $FlightFaresList[$w]['FareType'];
            $Currency = $FlightFaresList[$w]['Currency'];
            $FareRef = $FlightFaresList[$w]['FareRef'];
            $PassengerFare = $FlightFaresList[$w]['PassengerFare'];
            if (count($PassengerFare) > 0) {
                for ($wAux=0; $wAux < count($PassengerFare); $wAux++) { 
                    $PaxType = $PassengerFare[$wAux]['PaxType'];
                    $Quantity = $PassengerFare[$wAux]['Quantity'];
                    $BaseFare = $PassengerFare[$wAux]['BaseFare'];
                    $TotalFare = $PassengerFare[$wAux]['TotalFare'];
                    $TaxBreakUp = $PassengerFare[$wAux]['TaxBreakUp'];
                    if (count($TaxBreakUp) > 0) {
                        for ($wAux2=0; $wAux2 < count($TaxBreakUp); $wAux2++) { 
                            $Amount = $TaxBreakUp[$wAux2]['Amount'];
                            $TaxCode = $TaxBreakUp[$wAux2]['TaxCode'];
                        }
                    }
                }
            }
        }
    }
    $PricedItineraries = $Data['PricedItineraries'];
    if (count($PricedItineraries) > 0) {
        for ($y=0; $y < count($PricedItineraries); $y++) { 
            $FareSourceCode = $PricedItineraries[$y]['FareSourceCode'];
            $ValidatingCarrier = $PricedItineraries[$y]['ValidatingCarrier'];
            $FareRef = $PricedItineraries[$y]['FareRef'];
            $PenaltiesInfoRef = $PricedItineraries[$y]['PenaltiesInfoRef'];
            $FulfillmentDetailsRef = $PricedItineraries[$y]['FulfillmentDetailsRef'];
            $OriginDestinations = $PricedItineraries[$y]['OriginDestinations'];
            if (count($OriginDestinations) > 0) {
                for ($yAux=0; $yAux < count($OriginDestinations); $yAux++) { 
                    $SegmentRef = $OriginDestinations[$yAux]['SegmentRef'];
                    $ItineraryRef = $OriginDestinations[$yAux]['ItineraryRef'];
                    $LegIndicator = $OriginDestinations[$yAux]['LegIndicator'];
                }
            }
        }
    }
    $GroupedItems = $Data['GroupedItems'];
    if (count($GroupedItems) > 0) {
        for ($z=0; $z < count($GroupedItems); $z++) { 
            $Itins = $GroupedItems[$z]['Itins'];
            if (count($Itins) > 0) {
                $itin = "";
                for ($zAux=0; $zAux < count($Itins); $zAux++) { 
                    $itin = $Itins[$zAux];
                }
            }
            $Segments = $GroupedItems[$z]['Segments'];
            if (count($Segments) > 0) {
                $segment = "";
                for ($zAux2=0; $zAux2 < count($Segments); $zAux2++) { 
                    $segment = $Segments[$zAux2];
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