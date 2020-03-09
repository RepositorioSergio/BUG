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

$config = new \Zend\Config\Config(include '../config/autoload/global.nuitee.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://ws.nuitee.com/nuitee/Nuitee?WSDL';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:nuit="http://www.nuitee.ma">
<soapenv:Header/>
<soapenv:Body>
   <nuit:performConfirmPropertyAvailability>
      <confirmPropertyAvailabilityReq>
         <sessionId>20200420|20200427|en|MA|USD|2A0C|1091|9xGqt|1583511845218iiJP</sessionId>
         <hotelCode>215547</hotelCode>
         <rateDetailCode>7-215547|NRFN|20200305|6|57101</rateDetailCode>
         <confirmedRooms>
            <confirmedRooms>
               <boardId>RO</boardId>
               <roomCode>O4bFLOLOlypvzLDwVEgRRKV25TmrSmQ0dUZrGRQIr7knl3XCskLCCiizmmGEA30A|1</roomCode>
            </confirmedRooms>
         </confirmedRooms>
         <timeout>1600</timeout>
      </confirmPropertyAvailabilityReq>
   </nuit:performConfirmPropertyAvailability>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type: text/xml; charset=utf-8',
    'Accept: text/xml',
    'Content-Length: ' . strlen($raw)
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

echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.nuitee.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName('Envelope');
$Body = $Envelope->item(0)->getElementsByTagName('Body');
$performHotelListperformGetPropertyAvailabilityResponseResponse = $Body->item(0)->getElementsByTagName('performGetPropertyAvailabilityResponse');
if ($performHotelListperformGetPropertyAvailabilityResponseResponse->length > 0) {
    $getPropertyAvailabilityRes = $performHotelListperformGetPropertyAvailabilityResponseResponse->item(0)->getElementsByTagName('getPropertyAvailabilityRes');
    if ($getPropertyAvailabilityRes->length > 0) {
        $sessionId = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('sessionId');
        if ($sessionId->length > 0) {
            $sessionId = $sessionId->item(0)->nodeValue;
        } else {
            $sessionId = "";
        }
        $checkInDate = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('checkInDate');
        if ($checkInDate->length > 0) {
            $checkInDate = $checkInDate->item(0)->nodeValue;
        } else {
            $checkInDate = "";
        }
        $checkOutDate = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('checkOutDate');
        if ($checkOutDate->length > 0) {
            $checkOutDate = $checkOutDate->item(0)->nodeValue;
        } else {
            $checkOutDate = "";
        }
        $currency = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('currency');
        if ($currency->length > 0) {
            $currency = $currency->item(0)->nodeValue;
        } else {
            $currency = "";
        }
        $city = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('city');
        if ($city->length > 0) {
            $cityCode = $city->item(0)->getElementsByTagName('cityCode');
            if ($cityCode->length > 0) {
                $cityCode = $cityCode->item(0)->nodeValue;
            } else {
                $cityCode = 0;
            }
            $cityName = $city->item(0)->getElementsByTagName('cityName');
            if ($cityName->length > 0) {
                $cityName = $cityName->item(0)->nodeValue;
            } else {
                $cityName = "";
            }
            $countryName = $city->item(0)->getElementsByTagName('countryName');
            if ($countryName->length > 0) {
                $countryName = $countryName->item(0)->nodeValue;
            } else {
                $countryName = "";
            }
        }
        $roomGuests = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('roomGuests');
        if ($roomGuests->length > 0) {
            $roomGuests2 = $roomGuests->item(0)->getElementsByTagName('roomGuests');
            if ($roomGuests2->length > 0) {
                $adultCount = $roomGuests2->item(0)->getElementsByTagName('adultCount');
                if ($adultCount->length > 0) {
                    $adultCount = $adultCount->item(0)->nodeValue;
                } else {
                    $adultCount = "";
                }
                $childCount = $roomGuests2->item(0)->getElementsByTagName('childCount');
                if ($childCount->length > 0) {
                    $childCount = $childCount->item(0)->nodeValue;
                } else {
                    $childCount = "";
                }
                $agesArray = array();
                $count = 0;
                $childAges = $roomGuests2->item(0)->getElementsByTagName('childAges');
                if ($childAges->length >  0) {
                    for ($j=0; $j < $childAges->length; $j++) { 
                        $childAges = $childAges->item($j)->nodeValue;
                        $agesArray[$count] = $childAges;
                        $count = $count + 1;
                    }
                }
            }
        }
        $hotelInfo = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('hotelInfo');
        if ($hotelInfo->length > 0) {
            $hotelCode = $hotelInfo->item(0)->getElementsByTagName('hotelCode');
            if ($hotelCode->length > 0) {
                $hotelCode = $hotelCode->item(0)->nodeValue;
            } else {
                $hotelCode = "";
            }
            $hotelName = $hotelInfo->item(0)->getElementsByTagName('hotelName');
            if ($hotelName->length > 0) {
                $hotelName = $hotelName->item(0)->nodeValue;
            } else {
                $hotelName = "";
            }
            $hotelAddress = $hotelInfo->item(0)->getElementsByTagName('hotelAddress');
            if ($hotelAddress->length > 0) {
                $hotelAddress = $hotelAddress->item(0)->nodeValue;
            } else {
                $hotelAddress = "";
            }
            $hotelPictureUrl = $hotelInfo->item(0)->getElementsByTagName('hotelPictureUrl');
            if ($hotelPictureUrl->length > 0) {
                $hotelPictureUrl = $hotelPictureUrl->item(0)->nodeValue;
            } else {
                $hotelPictureUrl = "";
            }
            $latitude = $hotelInfo->item(0)->getElementsByTagName('latitude');
            if ($latitude->length > 0) {
                $latitude = $latitude->item(0)->nodeValue;
            } else {
                $latitude = "";
            }
            $longitude = $hotelInfo->item(0)->getElementsByTagName('longitude');
            if ($longitude->length > 0) {
                $longitude = $longitude->item(0)->nodeValue;
            } else {
                $longitude = "";
            }
            $starRating = $hotelInfo->item(0)->getElementsByTagName('starRating');
            if ($starRating->length > 0) {
                $starRating = $starRating->item(0)->nodeValue;
            } else {
                $starRating = "";
            }
        }
        $rateDetails2 = $getPropertyAvailabilityRes->item(0)->getElementsByTagName('rateDetails');
        if ($rateDetails2->length > 0) {
            for ($iAux=0; $iAux < $rateDetails2->length; $iAux++) { 
                $rateDetailCode = $rateDetails2->item($iAux)->getElementsByTagName('rateDetailCode');
                if ($rateDetailCode->length > 0) {
                    $rateDetailCode = $rateDetailCode->item(0)->nodeValue;
                } else {
                    $rateDetailCode = "";
                }
                $remarks = $rateDetails2->item($iAux)->getElementsByTagName('remarks');
                if ($remarks->length > 0) {
                    $remarks = $remarks->item(0)->nodeValue;
                } else {
                    $remarks = "";
                }
                $totalPrice = $rateDetails2->item($iAux)->getElementsByTagName('totalPrice');
                if ($totalPrice->length > 0) {
                    $totalPrice = $totalPrice->item(0)->nodeValue;
                } else {
                    $totalPrice = "";
                }
                $cancelPoliciesInfos = $rateDetails2->item($iAux)->getElementsByTagName('cancelPoliciesInfos');
                if ($cancelPoliciesInfos->length > 0) {
                    $refundableTag = $cancelPoliciesInfos->item(0)->getElementsByTagName('refundableTag');
                    if ($refundableTag->length > 0) {
                        $refundableTag = $refundableTag->item(0)->nodeValue;
                    } else {
                        $refundableTag = "";
                    }
                    $cancelPolicyInfos = $cancelPoliciesInfos->item(0)->getElementsByTagName('cancelPolicyInfos');
                    if ($cancelPolicyInfos->length > 0) {
                        $amount = $cancelPolicyInfos->item(0)->getElementsByTagName('amount');
                        if ($amount->length > 0) {
                            $amount = $amount->item(0)->nodeValue;
                        } else {
                            $amount = "";
                        }
                        $cancelTime = $cancelPolicyInfos->item(0)->getElementsByTagName('cancelTime');
                        if ($amount->length > 0) {
                            $cancelTime = $cancelTime->item(0)->nodeValue;
                        } else {
                            $cancelTime = "";
                        }
                        $type = $cancelPolicyInfos->item(0)->getElementsByTagName('type');
                        if ($type->length > 0) {
                            $type = $type->item(0)->nodeValue;
                        } else {
                            $type = "";
                        }
                    }
                }
                $rooms = $rateDetails2->item($iAux)->getElementsByTagName('rooms');
                if ($rooms->length > 0) {
                    $rooms2 = $rooms->item(0)->getElementsByTagName('rooms');
                    if ($rooms2->length > 0) {
                        $adultCount = $rooms2->item(0)->getElementsByTagName('adultCount');
                        if ($adultCount->length > 0) {
                            $adultCount = $adultCount->item(0)->nodeValue;
                        } else {
                            $adultCount = "";
                        }
                        $childCount = $rooms2->item(0)->getElementsByTagName('childCount');
                        if ($childCount->length > 0) {
                            $childCount = $childCount->item(0)->nodeValue;
                        } else {
                            $childCount = "";
                        }
                        $boards = $rooms2->item(0)->getElementsByTagName('boards');
                        if ($boards->length > 0) {
                            $boards = $boards->item(0)->nodeValue;
                        } else {
                            $boards = "";
                        }
                        $roomCode = $rooms2->item(0)->getElementsByTagName('roomCode');
                        if ($roomCode->length > 0) {
                            $roomCode = $roomCode->item(0)->nodeValue;
                        } else {
                            $roomCode = "";
                        }
                        $roomDescription = $rooms2->item(0)->getElementsByTagName('roomDescription');
                        if ($roomDescription->length > 0) {
                            $roomDescription = $roomDescription->item(0)->nodeValue;
                        } else {
                            $roomDescription = "";
                        }
                        $roomRemarks = $rooms2->item(0)->getElementsByTagName('roomRemarks');
                        if ($roomRemarks->length > 0) {
                            $roomRemarks = $roomRemarks->item(0)->nodeValue;
                        } else {
                            $roomRemarks = "";
                        }
                        $includedBoard = $rooms2->item(0)->getElementsByTagName('includedBoard');
                        if ($includedBoard->length > 0) {
                            $boardDescription = $includedBoard->item(0)->getElementsByTagName('boardDescription');
                            if ($boardDescription->length > 0) {
                                $boardDescription = $boardDescription->item(0)->nodeValue;
                            } else {
                                $boardDescription = "";
                            }
                            $boardId = $includedBoard->item(0)->getElementsByTagName('boardId');
                            if ($boardId->length > 0) {
                                $boardId = $boardId->item(0)->nodeValue;
                            } else {
                                $boardId = "";
                            }
                            $price = $includedBoard->item(0)->getElementsByTagName('price');
                            if ($price->length > 0) {
                                $price = $price->item(0)->nodeValue;
                            } else {
                                $price = "";
                            }
                        }
                        $roomRate = $rooms2->item(0)->getElementsByTagName('roomRate');
                        if ($roomRate->length > 0) {
                            $initialPrice = $roomRate->item(0)->getElementsByTagName('initialPrice');
                            if ($initialPrice->length > 0) {
                                $initialPrice = $initialPrice->item(0)->nodeValue;
                            } else {
                                $initialPrice = "";
                            }
                            $roomRateprice = $roomRate->item(0)->getElementsByTagName('price');
                            if ($roomRateprice->length > 0) {
                                $roomRateprice = $roomRateprice->item(0)->nodeValue;
                            } else {
                                $roomRateprice = "";
                            }
                            $initialPricePerNight = $roomRate->item(0)->getElementsByTagName('initialPricePerNight');
                            if ($initialPricePerNight->length > 0) {
                                for ($iAux2=0; $iAux2 < $initialPricePerNight->length; $iAux2++) { 
                                    $initialPricePerNight = $initialPricePerNight->item($iAux2)->nodeValue;
                                }
                            }
                            $pricePerNight = $roomRate->item(0)->getElementsByTagName('pricePerNight');
                            if ($pricePerNight->length > 0) {
                                for ($iAux3=0; $iAux3 < $pricePerNight->length; $iAux3++) { 
                                    $pricePerNight = $pricePerNight->item($iAux3)->nodeValue;
                                }
                            }
                        }
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