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
echo "COMECOU BOOKING DETAIL";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://sandbox-affiliateapisecure.agoda.com/xmlpartner/xmlbookservice/bookdetail_v2';

$siteid = 1831338;
$apikey = "b57a754c-5e06-4cdd-ac0d-2ea58c48ef74";


$raw = '<?xml version="1.0" encoding="utf-8"?>
<BookingDetailsRequestV2 siteid="' . $siteid . '" apikey="' . $apikey . '" xmlns="http://xml.agoda.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
	<BookingID>3000961</BookingID>
	<BookingID>3000962</BookingID>
	<BookingID>3000963</BookingID>
	<BookingID>3000964</BookingID>
	<BookingID>3000965</BookingID>
</BookingDetailsRequestV2>';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'text/xml;charset=utf-8',
    'Authorization' => '1831338:b57a754c-5e06-4cdd-ac0d-2ea58c48ef74'
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

die();

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
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
$AvailabilityLongResponseV2 = $inputDoc->getElementsByTagName("AvailabilityLongResponseV2");

$Hotels = $AvailabilityLongResponseV2->item(0)->getElementsByTagName('Hotels');
if ($Hotels->length > 0) {
    $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
    if ($Hotel->length > 0) {
        for ($i=0; $i < $Hotel->length; $i++) { 
            $Id = $Hotel->item($i)->getElementsByTagName('Id');
            if ($Id->length > 0) {
                $Id = $Id->item(0)->nodeValue;
            } else {
                $Id = "";
            }
            $CheapestRoom = $Hotel->item($i)->getElementsByTagName('CheapestRoom');
            if ($CheapestRoom->length > 0) {
                $inclusive = $CheapestRoom->item(0)->getAttribute('inclusive');
                $fees = $CheapestRoom->item(0)->getAttribute('fees');
                $tax = $CheapestRoom->item(0)->getAttribute('tax');
                $exclusive = $CheapestRoom->item(0)->getAttribute('exclusive');
            } else {
                $inclusive = "";
                $fees = "";
                $tax = "";
                $exclusive = "";
            }
            $PaxSettings = $Hotel->item($i)->getElementsByTagName('PaxSettings');
            if ($PaxSettings->length > 0) {
                $childage = $PaxSettings->item(0)->getAttribute('childage');
                $infantage = $PaxSettings->item(0)->getAttribute('infantage');
                $submit = $PaxSettings->item(0)->getAttribute('submit');
            } else {
                $childage = "";
                $infantage = "";
                $submit = "";
            }

            $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
            if ($Rooms->length > 0) {
                $Room = $Rooms->item(0)->getElementsByTagName('Room');
                if ($Room->length > 0) {
                    for ($r=0; $r < $Room->length; $r++) { 
                        $Roomid = $Room->item($r)->getAttribute('id');
                        $Roomname = $Room->item($r)->getAttribute('name');
                        $promoeligible = $Room->item($r)->getAttribute('promoeligible');
                        $blockid = $Room->item($r)->getAttribute('blockid');
                        $ratecategoryid = $Room->item($r)->getAttribute('ratecategoryid');
                        $model = $Room->item($r)->getAttribute('model');
                        $currency = $Room->item($r)->getAttribute('currency');
                        $ratetype = $Room->item($r)->getAttribute('ratetype');
                        $rateplan = $Room->item($r)->getAttribute('rateplan');
                        $lineitemid = $Room->item($r)->getAttribute('lineitemid');
                        $promotionid = $Room->item($r)->getAttribute('promotionid');

                        $StandardTranslation = $Room->item($r)->getElementsByTagName('StandardTranslation');
                        if ($StandardTranslation->length > 0) {
                            $StandardTranslation = $StandardTranslation->item(0)->nodeValue;
                        } else {
                            $StandardTranslation = "";
                        }
                        $RemainingRooms = $Room->item($r)->getElementsByTagName('RemainingRooms');
                        if ($RemainingRooms->length > 0) {
                            $RemainingRooms = $RemainingRooms->item(0)->nodeValue;
                        } else {
                            $RemainingRooms = "";
                        }
                        //Benefits
                        $Benefits = $Room->item($r)->getElementsByTagName('Benefits');
                        if ($Benefits->length > 0) {
                            $Benefit = $Benefits->item(0)->getElementsByTagName('Benefit');
                            if ($Benefit->length > 0) {
                                $Benefitid = $Benefit->item(0)->getAttribute('id');
                                $BenefitName = $Benefit->item(0)->getElementsByTagName('Name');
                                if ($BenefitName->length > 0) {
                                    $BenefitName = $BenefitName->item(0)->nodeValue;
                                } else {
                                    $BenefitName = "";
                                }
                                $BenefitTranslation = $Benefit->item(0)->getElementsByTagName('Translation');
                                if ($BenefitTranslation->length > 0) {
                                    $BenefitTranslation = $BenefitTranslation->item(0)->nodeValue;
                                } else {
                                    $BenefitTranslation = "";
                                }
                            }
                        }
                        //ParentRoom
                        $ParentRoom = $Room->item($r)->getElementsByTagName('ParentRoom');
                        if ($ParentRoom->length > 0) {
                            $ParentRoomid = $ParentRoom->item(0)->getAttribute('id');
                            $ParentRoomname = $ParentRoom->item(0)->getAttribute('name');
                            $ParentRoomtranslationname = $ParentRoom->item(0)->getAttribute('translationname');
                        } else {
                            $ParentRoomid = "";
                            $ParentRoomname = "";
                            $ParentRoomtranslationname = "";
                        }
                        //MaxRoomOccupancy
                        $MaxRoomOccupancy = $Room->item($r)->getElementsByTagName('MaxRoomOccupancy');
                        if ($MaxRoomOccupancy->length > 0) {
                            $extrabeds = $MaxRoomOccupancy->item(0)->getAttribute('extrabeds');
                            $normalbedding = $MaxRoomOccupancy->item(0)->getAttribute('normalbedding');
                        } else {
                            $extrabeds = "";
                            $normalbedding = "";
                        }
                        //RateInfo
                        $RateInfo = $Room->item($r)->getElementsByTagName('RateInfo');
                        if ($RateInfo->length > 0) {
                            $Included = $RateInfo->item(0)->getElementsByTagName('Included');
                            if ($Included->length > 0) {
                                $Included = $Included->item(0)->nodeValue;
                            } else {
                                $Included = "";
                            }
                            $Rate = $RateInfo->item(0)->getElementsByTagName('Rate');
                            if ($Rate->length > 0) {
                                $Rateinclusive = $Rate->item(0)->getAttribute('inclusive');
                                $Ratefees = $Rate->item(0)->getAttribute('fees');
                                $Ratetax = $Rate->item(0)->getAttribute('tax');
                                $Rateexclusive = $Rate->item(0)->getAttribute('exclusive');
                            } else {
                                $Rateinclusive = "";
                                $Ratefees = "";
                                $Ratetax = "";
                                $Rateexclusive = "";
                            }
                            $Promotion = $RateInfo->item(0)->getElementsByTagName('Promotion');
                            if ($Promotion->length > 0) {
                                $text = $Promotion->item(0)->getAttribute('text');
                                $savings = $Promotion->item(0)->getAttribute('savings');
                            } else {
                                $text = "";
                                $savings = "";
                            }
                            $TotalPaymentAmount = $RateInfo->item(0)->getElementsByTagName('TotalPaymentAmount');
                            if ($TotalPaymentAmount->length > 0) {
                                $TotalPaymentAmountinclusive = $TotalPaymentAmount->item(0)->getAttribute('inclusive');
                                $TotalPaymentAmountfees = $TotalPaymentAmount->item(0)->getAttribute('fees');
                                $TotalPaymentAmounttax = $TotalPaymentAmount->item(0)->getAttribute('tax');
                                $TotalPaymentAmountexclusive = $TotalPaymentAmount->item(0)->getAttribute('exclusive');
                            } else {
                                $TotalPaymentAmountinclusive = "";
                                $TotalPaymentAmountfees = "";
                                $TotalPaymentAmounttax = "";
                                $TotalPaymentAmountexclusive = "";
                            }
                        }

                        //Cancellation
                        $policy = "";
                        $policyTrans = "";
                        $policyParam = "";
                        $Cancellation = $Room->item($r)->getElementsByTagName('Cancellation');
                        if ($Cancellation->length > 0) {
                            $PolicyText = $Cancellation->item(0)->getElementsByTagName('PolicyText');
                            if ($PolicyText->length > 0) {
                                $language = $PolicyText->item(0)->getAttribute('language');
                                $policy = $PolicyText->item(0)->nodeValue;
                            } else {
                                $policy = "";
                            }
                            $PolicyTranslated = $Cancellation->item(0)->getElementsByTagName('PolicyTranslated');
                            if ($PolicyTranslated->length > 0) {
                                $language = $PolicyTranslated->item(0)->getAttribute('language');
                                $policyTrans = $PolicyTranslated->item(0)->nodeValue;
                            } else {
                                $policyTrans = "";
                            }
                            $PolicyParameters = $Cancellation->item(0)->getElementsByTagName('PolicyParameters');
                            if ($PolicyParameters->length > 0) {
                                $PolicyParameter = $PolicyParameters->item(0)->getElementsByTagName('PolicyParameter');
                                if ($PolicyParameter->length > 0) {
                                    for ($j=0; $j < $PolicyParameter->length; $j++) { 
                                        $PolicyParametercharge = $PolicyParameter->item($j)->getAttribute('charge');
                                        $PolicyParameterdays = $PolicyParameter->item($j)->getAttribute('days');
                                        $policyParam = $PolicyParameter->item($j)->nodeValue;
                                    }
                                }
                            }
                            $PolicyDates = $Cancellation->item(0)->getElementsByTagName('PolicyDates');
                            if ($PolicyDates->length > 0) {
                                $PolicyDate = $PolicyDates->item(0)->getElementsByTagName('PolicyDate');
                                if ($PolicyDate->length > 0) {
                                    $after = $PolicyDate->item(0)->getAttribute('after');
                                    $RatePD = $PolicyDate->item(0)->getElementsByTagName('Rate');
                                    if ($RatePD->length > 0) {
                                        $RatePDinclusive = $RatePD->item(0)->getAttribute('inclusive');
                                        $RatePDfees = $RatePD->item(0)->getAttribute('fees');
                                        $RatePDtax = $RatePD->item(0)->getAttribute('tax');
                                        $RatePDexclusive = $RatePD->item(0)->getAttribute('exclusive');
                                    } else {
                                        $RatePDinclusive = "";
                                        $RatePDfees = "";
                                        $RatePDtax = "";
                                        $RatePDexclusive = "";
                                    }
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
echo 'Done';
?>