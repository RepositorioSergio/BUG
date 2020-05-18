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
echo "COMECOU REVALIDATE<br/>";
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

$url = 'https://restapidemo.myfarebox.com/api/v1/Revalidate/Flight';
$session_id = 'C65B61B4-1CDE-4512-A7BF-E02DB2D40AB8-365';
$FareSourceCode = 'dEpQUmwrZ2UzOUhzWjIya1pEVk5LMWN1TlJRMWl0Tmh5Vk14cEZKU3RtbHVKazdweDZyN0VqaTIyQmI1WExlMFpUMTl6Mjd3aFBLWkF0QVJqeG9SSVF3RFFiS0FRK3k4d3NvaW5XY2dZSm12L1ZaeDJMcUJ5QkxSNjBDcVFLTklBT1llNVZaUGRpOWRwOE5obC9EaFlBPT0=';

$raw = '{
    "FareSourceCode": "' . $FareSourceCode . '",
    "Target": "Test",
    "ConversationId": "AAA2"
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
    'Authorization' => 'Bearer ' . $session_id,
    'Content-Length' => strlen($raw)
));
$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}
echo "Response- " . $response;

$response = json_decode($response, true);

$Success = $response['Success'];
if ($Success === "true") {
    $Data = $response['Data'];
    $ConversationId = $Data['ConversationId'];
    $IsValid = $Data['IsValid'];
    $Success = $Data['Success'];
    $Target = $Data['Target'];
    $PricedItineraries = $Data['PricedItineraries'];
    if (count($PricedItineraries) > 0) {
        for ($i=0; $i < count($PricedItineraries); $i++) { 
            $DirectionInd = $PricedItineraries[$i]['DirectionInd'];
            $IsPassportMandatory = $PricedItineraries[$i]['IsPassportMandatory'];
            $PaxNameCharacterLimit = $PricedItineraries[$i]['PaxNameCharacterLimit'];
            $SequenceNumber = $PricedItineraries[$i]['SequenceNumber'];
            $TicketType = $PricedItineraries[$i]['TicketType'];
            $ValidatingAirlineCode = $PricedItineraries[$i]['ValidatingAirlineCode'];
            $AirItineraryPricingInfo = $PricedItineraries[$i]['AirItineraryPricingInfo'];
            $DivideInPartyIndicator = $AirItineraryPricingInfo['DivideInPartyIndicator'];
            $FareSourceCode = $AirItineraryPricingInfo['FareSourceCode'];
            $FareType = $AirItineraryPricingInfo['FareType'];
            $IsRefundable = $AirItineraryPricingInfo['IsRefundable'];
            $ItinTotalFare = $AirItineraryPricingInfo['ItinTotalFare'];
            $ActualFare = $ItinTotalFare['ActualFare'];
            $ActualFareAmount = $ActualFare['Amount'];
            $ActualFareCurrencyCode = $ActualFare['CurrencyCode'];
            $ActualFareDecimalPlaces = $ActualFare['DecimalPlaces'];
            $BaseFare = $ItinTotalFare['BaseFare'];
            $BaseFareAmount = $BaseFare['Amount'];
            $BaseFareCurrencyCode = $BaseFare['CurrencyCode'];
            $BaseFareDecimalPlaces = $BaseFare['DecimalPlaces'];
            $EquivFare = $ItinTotalFare['EquivFare'];
            $EquivFareAmount = $EquivFare['Amount'];
            $EquivFareCurrencyCode = $EquivFare['CurrencyCode'];
            $EquivFareDecimalPlaces = $EquivFare['DecimalPlaces'];
            $TotalFare = $ItinTotalFare['TotalFare'];
            $TotalFareAmount = $TotalFare['Amount'];
            $TotalFareCurrencyCode = $TotalFare['CurrencyCode'];
            $TotalFareDecimalPlaces = $TotalFare['DecimalPlaces'];
            $TotalTax = $ItinTotalFare['TotalTax'];
            $TotalTaxAmount = $TotalTax['Amount'];
            $TotalTaxCurrencyCode = $TotalTax['CurrencyCode'];
            $TotalTaxDecimalPlaces = $TotalTax['DecimalPlaces'];

            $FareInfos = $AirItineraryPricingInfo['FareInfos'];
            if (count($FareInfos) > 0) {
                for ($iAux=0; $iAux < count($FareInfos); $iAux++) { 
                    $FareReference = $FareInfos[$iAux]['FareReference'];
                }
            }
            $PTC_FareBreakdowns = $AirItineraryPricingInfo['PTC_FareBreakdowns'];
            if (count($PTC_FareBreakdowns) > 0) {
                for ($iAux2=0; $iAux2 < count($PTC_FareBreakdowns); $iAux2++) { 
                    $BaggageInfo = $PTC_FareBreakdowns[$iAux2]['BaggageInfo'];
                    if (count($BaggageInfo) > 0) {
                        $baggage = "";
                        for ($iAux3=0; $iAux3 < count($BaggageInfo); $iAux3++) { 
                            $baggage = $BaggageInfo[$iAux3];
                        }
                    }
                    $CabinBaggageInfo = $PTC_FareBreakdowns[$iAux2]['CabinBaggageInfo'];
                    if (count($CabinBaggageInfo) > 0) {
                        $cabinbaggage = "";
                        for ($iAux4=0; $iAux4 < count($CabinBaggageInfo); $iAux4++) { 
                            $cabinbaggage = $CabinBaggageInfo[$iAux4];
                        }
                    }
                    $FareBasisCodes = $PTC_FareBreakdowns[$iAux2]['FareBasisCodes'];
                    if (count($FareBasisCodes) > 0) {
                        $farebasis = "";
                        for ($iAux5=0; $iAux5 < count($FareBasisCodes); $iAux5++) { 
                            $farebasis = $FareBasisCodes[$iAux5];
                        }
                    }
                    $PenaltiesInfo = $PTC_FareBreakdowns[$iAux2]['PenaltiesInfo'];
                    if (count($PenaltiesInfo) > 0) {
                        for ($iAux6=0; $iAux6 < count($PenaltiesInfo); $iAux6++) { 
                            $Allowed = $PenaltiesInfo[$iAux6]['Allowed'];
                            $Amount = $PenaltiesInfo[$iAux6]['Amount'];
                            $CurrencyCode = $PenaltiesInfo[$iAux6]['CurrencyCode'];
                            $PenaltyType = $PenaltiesInfo[$iAux6]['PenaltyType'];
                        }
                    }
                    $PassengerFare = $PTC_FareBreakdowns[$iAux2]['PassengerFare'];
                    $BaseFare = $PassengerFare['BaseFare'];
                    $BaseFareAmount = $BaseFare['Amount'];
                    $BaseFareCurrencyCode = $BaseFare['CurrencyCode'];
                    $BaseFareDecimalPlaces = $BaseFare['DecimalPlaces'];
                    $EquivFare = $PassengerFare['EquivFare'];
                    $EquivFareAmount = $EquivFare['Amount'];
                    $EquivFareCurrencyCode = $EquivFare['CurrencyCode'];
                    $EquivFareDecimalPlaces = $EquivFare['DecimalPlaces'];
                    $TotalFare = $PassengerFare['TotalFare'];
                    $TotalFareAmount = $TotalFare['Amount'];
                    $TotalFareCurrencyCode = $TotalFare['CurrencyCode'];
                    $TotalFareDecimalPlaces = $TotalFare['DecimalPlaces'];
                    $Taxes = $PassengerFare['Taxes'];
                    if (count($Taxes) > 0) {
                        for ($iAux7=0; $iAux7 < count($Taxes); $iAux7++) { 
                            $Amount = $Taxes[$iAux7]['Amount'];
                            $CurrencyCode = $Taxes[$iAux7]['CurrencyCode'];
                            $DecimalPlaces = $Taxes[$iAux7]['DecimalPlaces'];
                            $TaxCode = $Taxes[$iAux7]['TaxCode'];
                        }
                    }
                    $PassengerTypeQuantity = $PTC_FareBreakdowns[$iAux2]['PassengerTypeQuantity'];
                    $Code = $PassengerTypeQuantity['Code'];
                    $Quantity = $PassengerTypeQuantity['Quantity'];
                }
            }

            $OriginDestinationOptions = $PricedItineraries[$i]['OriginDestinationOptions'];
            if (count($OriginDestinationOptions) > 0) {
                for ($iAux8=0; $iAux8 < count($OriginDestinationOptions); $iAux8++) { 
                    $FlightSegments = $OriginDestinationOptions[$iAux8]['FlightSegments'];
                    if (count($FlightSegments) > 0) {
                        for ($iAux9=0; $iAux9 < count($FlightSegments); $iAux9++) { 
                            $ArrivalAirportLocationCode = $FlightSegments[$iAux9]['ArrivalAirportLocationCode'];
                            $ArrivalDateTime = $FlightSegments[$iAux9]['ArrivalDateTime'];
                            $CabinClassCode = $FlightSegments[$iAux9]['CabinClassCode'];
                            $CabinClassText = $FlightSegments[$iAux9]['CabinClassText'];
                            $DepartureAirportLocationCode = $FlightSegments[$iAux9]['DepartureAirportLocationCode'];
                            $DepartureDateTime = $FlightSegments[$iAux9]['DepartureDateTime'];
                            $Eticket = $FlightSegments[$iAux9]['Eticket'];
                            $FlightNumber = $FlightSegments[$iAux9]['FlightNumber'];
                            $JourneyDuration = $FlightSegments[$iAux9]['JourneyDuration'];
                            $MarketingAirlineCode = $FlightSegments[$iAux9]['MarketingAirlineCode'];
                            $MarriageGroup = $FlightSegments[$iAux9]['MarriageGroup'];
                            $MealCode = $FlightSegments[$iAux9]['MealCode'];
                            $ResBookDesigCode = $FlightSegments[$iAux9]['ResBookDesigCode'];
                            $ResBookDesigText = $FlightSegments[$iAux9]['ResBookDesigText'];
                            $StopQuantity = $FlightSegments[$iAux9]['StopQuantity'];
                            $OperatingAirline = $FlightSegments[$iAux9]['OperatingAirline'];
                            $Code = $OperatingAirline['Code'];
                            $Equipment = $OperatingAirline['Equipment'];
                            $FlightNumber = $OperatingAirline['FlightNumber'];
                            $SeatsRemaining = $FlightSegments[$iAux9]['SeatsRemaining'];
                            $BelowMinimum = $SeatsRemaining['BelowMinimum'];
                            $Number = $SeatsRemaining['Number'];
                            $StopQuantityInfo = $FlightSegments[$iAux9]['StopQuantityInfo'];
                            $SQIArrivalDateTime = $StopQuantityInfo['ArrivalDateTime'];
                            $SQIDepartureDateTime = $StopQuantityInfo['DepartureDateTime'];
                            $SQIDuration = $StopQuantityInfo['Duration'];
                            $SQILocationCode = $StopQuantityInfo['LocationCode'];
                        }
                    }
                }
            }
            $RequiredFieldsToBook = $PricedItineraries[$i]['RequiredFieldsToBook'];
            if (count($RequiredFieldsToBook) > 0) {
                $requiredfields = "";
                for ($iAux10=0; $iAux10 < count($RequiredFieldsToBook); $iAux10++) { 
                    $requiredfields = $RequiredFieldsToBook[$iAux10];
                }
            }
        }
    }
}

?>