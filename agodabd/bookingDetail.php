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
	<BookingID>8283511</BookingID>
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
$BookingDetailsResponseV2 = $inputDoc->getElementsByTagName("BookingDetailsResponseV2");
$status = $BookingDetailsResponseV2->item(0)->getAttribute('status');

$Bookings = $BookingDetailsResponseV2->item(0)->getElementsByTagName('Bookings');
if ($Bookings->length > 0) {
    $Booking = $Bookings->item(0)->getElementsByTagName('Booking');
    if ($Booking->length > 0) {
        for ($j=0; $j < $Booking->length; $j++) { 
            $BookingID = $Booking->item($j)->getElementsByTagName('BookingID');
            if ($BookingID->length > 0) {
                $BookingID = $BookingID->item(0)->nodeValue;
            } else {
                $BookingID = "";
            }
            $Tag = $Booking->item($j)->getElementsByTagName('Tag');
            if ($Tag->length > 0) {
                $Tag = $Tag->item(0)->nodeValue;
            } else {
                $Tag = "";
            }
            $Status = $Booking->item($j)->getElementsByTagName('Status');
            if ($Status->length > 0) {
                $Status = $Status->item(0)->nodeValue;
            } else {
                $Status = "";
            }
            $Country = $Booking->item($j)->getElementsByTagName('Country');
            if ($Country->length > 0) {
                $Country = $Country->item(0)->nodeValue;
            } else {
                $Country = "";
            }
            $City = $Booking->item($j)->getElementsByTagName('City');
            if ($City->length > 0) {
                $City = $City->item(0)->nodeValue;
            } else {
                $City = "";
            }
            $Hotel = $Booking->item($j)->getElementsByTagName('Hotel');
            if ($Hotel->length > 0) {
                $Hotel = $Hotel->item(0)->nodeValue;
            } else {
                $Hotel = "";
            }
            $CheckInDate = $Booking->item($j)->getElementsByTagName('CheckInDate');
            if ($CheckInDate->length > 0) {
                $CheckInDate = $CheckInDate->item(0)->nodeValue;
            } else {
                $CheckInDate = "";
            }
            $CheckOutDate = $Booking->item($j)->getElementsByTagName('CheckOutDate');
            if ($CheckOutDate->length > 0) {
                $CheckOutDate = $CheckOutDate->item(0)->nodeValue;
            } else {
                $CheckOutDate = "";
            }
            $SelfServiceURL = $Booking->item($j)->getElementsByTagName('SelfServiceURL');
            if ($SelfServiceURL->length > 0) {
                $SelfServiceURL = $SelfServiceURL->item(0)->nodeValue;
            } else {
                $SelfServiceURL = "";
            }
            $Source = $Booking->item($j)->getElementsByTagName('Source');
            if ($Source->length > 0) {
                $Source = $Source->item(0)->nodeValue;
            } else {
                $Source = "";
            }
            $SupplierName = $Booking->item($j)->getElementsByTagName('SupplierName');
            if ($SupplierName->length > 0) {
                $SupplierName = $SupplierName->item(0)->nodeValue;
            } else {
                $SupplierName = "";
            }
            $SupplierReference = $Booking->item($j)->getElementsByTagName('SupplierReference');
            if ($SupplierReference->length > 0) {
                $SupplierReference = $SupplierReference->item(0)->nodeValue;
            } else {
                $SupplierReference = "";
            }

            //Room
            $Room = $Booking->item($j)->getElementsByTagName('Room');
            if ($Room->length > 0) {
                $RoomType = $Room->item(0)->getElementsByTagName('RoomType');
                if ($RoomType->length > 0) {
                    $RoomType = $RoomType->item(0)->nodeValue;
                } else {
                    $RoomType = "";
                }
                $RoomsBooked = $Room->item(0)->getElementsByTagName('RoomsBooked');
                if ($RoomsBooked->length > 0) {
                    $RoomsBooked = $RoomsBooked->item(0)->nodeValue;
                } else {
                    $RoomsBooked = "";
                }
            }

            //TotalRateUSD
            $TotalRateUSD = $Booking->item($j)->getElementsByTagName('TotalRateUSD');
            if ($TotalRateUSD->length > 0) {
                $inclusive = $TotalRateUSD->item(0)->getAttribute('inclusive');
                $fees = $TotalRateUSD->item(0)->getAttribute('fees');
                $tax = $TotalRateUSD->item(0)->getAttribute('tax');
                $exclusive = $TotalRateUSD->item(0)->getAttribute('exclusive');
                $ratetype = $TotalRateUSD->item(0)->getAttribute('ratetype');
                $rateplan = $TotalRateUSD->item(0)->getAttribute('rateplan');
            } else {
                $inclusive = "";
                $fees = "";
                $tax = "";
                $exclusive = "";
                $ratetype = "";
                $rateplan = "";
            }

            //Payment
            $pay = "";
            $Payment = $Booking->item($j)->getElementsByTagName('Payment');
            if ($Payment->length > 0){
                $PaymentRateInclusive = $Payment->item(0)->getElementsByTagName('PaymentRateInclusive');
                if ($PaymentRateInclusive->length > 0){
                    $currency = $PaymentRateInclusive->item(0)->getAttribute('currency');
                    $pay = $PaymentRateInclusive->item(0)->nodeValue;
                } else {
                    $currency = "";
                    $pay = ""; 
                }
            }

            //GuestDetails
            $GuestDetails = $Booking->item($j)->getElementsByTagName('GuestDetails');
            if ($GuestDetails->length > 0) {
                $GuestDetail = $GuestDetails->item(0)->getElementsByTagName('GuestDetail');
                if ($GuestDetail->length > 0) {
                    for ($i=0; $i < $GuestDetail->length; $i++) { 
                        $Primary = $GuestDetail->item($i)->getAttribute('Primary');
                        $Title = $GuestDetail->item($i)->getElementsByTagName('Title');
                        if ($Title->length > 0) {
                            $Title = $Title->item(0)->nodeValue;
                        } else {
                            $Title = "";
                        }
                        $FirstName = $GuestDetail->item($i)->getElementsByTagName('FirstName');
                        if ($FirstName->length > 0) {
                            $FirstName = $FirstName->item(0)->nodeValue;
                        } else {
                            $FirstName = "";
                        }
                        $LastName = $GuestDetail->item($i)->getElementsByTagName('LastName');
                        if ($LastName->length > 0) {
                            $LastName = $LastName->item(0)->nodeValue;
                        } else {
                            $LastName = "";
                        }
                        $CountryOfPassport = $GuestDetail->item($i)->getElementsByTagName('CountryOfPassport');
                        if ($CountryOfPassport->length > 0) {
                            $CountryOfPassport = $CountryOfPassport->item(0)->nodeValue;
                        } else {
                            $CountryOfPassport = "";
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