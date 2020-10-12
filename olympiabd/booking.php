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

$config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://parsystest.olympia.it/NewAvailabilityServlet/hotelres/OTA2014Compact';
$bookingcode = "Hh0nLR0Dxlf0HBrmWq4cRQ+E+jygn8k97GpsxHwOOmHWA0UzgCrkLD0rU53hh2P/JM6M3cRZOq+op0Jmmev4/gEXMninHh+Ftks6iWcx/bhRVDLc7j+WH3pZCxNPnKcj2Fp+6FQxXQCO/wX2+zkIHWydCvlsyqFrgz11Rchaon58fyqIKuefpCPZ8RDUNmDAWeKjRK1aBaMIho1RpKAAtK9gnBOLzMEfhRTbp2MYoziJQJai1roExlFaxCoVwvdU";
$bookingcode2 = "Hh0nLR0Dxlf0HBrmWq4cRaqAPPJqa0KIk6LePy4ByaHWA0UzgCrkLD0rU53hh2P/NAXklT4VPTGlHNtULc8PPnGkXLuibq9N4U36WDsUWjN63OPvZji9S9AVA9tG5lNrhSpmhtiJ27PjM00iEoUIAQ5lOYtm03IdVpNG9wECUPU6G/YYEr5Ny2gwqYsMASQm9Umt/tVcZePN7oNFuYQf02Z6sNdvWyWRSXgOKc65a2nnPTf3MHaXHmp9kDtFqJ4Y";

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/">
	<soap-env:Header>
		<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			<wsse:Username>628347</wsse:Username>
			<wsse:Password>clubtest</wsse:Password>
			<Context>olympia_europe_ts</Context>
		</wsse:Security>
	</soap-env:Header>
	<soap-env:Body>
        <OTA_HotelResRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact" Transaction="Booking" DetailLevel="2" RateDetails="1">
            <UniqueID Type="ClientReference" ID="ABC1252"/>
            <HotelRes>
                <Rooms>
                    <Room>
                        <RoomRate BookingCode="' . $bookingcode . '"/>
                        <Guests>
                            <Guest AgeCode="A" LeadGuest="1">
                                <PersonName>
                                    <NamePrefix>Mr.</NamePrefix>
                                    <GivenName>Test</GivenName>
                                    <Surname>Test</Surname>
                                </PersonName>
                            </Guest>
                        </Guests>
                    </Room>
                    <Room>
                        <RoomRate BookingCode="' . $bookingcode2 . '"/>
                        <Guests>
                            <Guest AgeCode="A">
                                <PersonName>
                                    <NamePrefix>Mr.</NamePrefix>
                                    <GivenName>TestB</GivenName>
                                    <Surname>Test</Surname>
                                </PersonName>
                            </Guest>
                            <Guest AgeCode="A">
                                <PersonName>
                                    <NamePrefix>Mr.</NamePrefix>
                                    <GivenName>Test</GivenName>
                                    <Surname>Test</Surname>
                                </PersonName>
                            </Guest>
                            <Guest AgeCode="C" Age="3">
                                <PersonName>
                                    <NamePrefix>Mr.</NamePrefix>
                                    <GivenName>Test</GivenName>
                                    <Surname>Test</Surname>
                                </PersonName>
                            </Guest>
                        </Guests>
                    </Room>
                </Rooms>
            </HotelRes>
        </OTA_HotelResRQ>
	</soap-env:Body>
</soap-env:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type: text/xml; charset=utf-8',
    'Accept: application/xml',
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
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
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
$OTA_BookingInfoRS = $inputDoc->getElementsByTagName('OTA_BookingInfoRS');
$HotelResList = $OTA_BookingInfoRS->item(0)->getElementsByTagName('HotelResList');
if ($HotelResList->length > 0) {
    $ResStatus = $HotelResList->item(0)->getAttribute('ResStatus');
    $CreateDateTime = $HotelResList->item(0)->getAttribute('CreateDateTime');
    $LastModifyDateTime = $HotelResList->item(0)->getAttribute('LastModifyDateTime');
    $NewItem = $HotelResList->item(0)->getAttribute('NewItem');
    $Rooms = $HotelResList->item(0)->getElementsByTagName('Rooms');
    if ($Rooms->length > 0) {
        $Room = $Rooms->item(0)->getElementsByTagName('Room');
        if ($Room->length > 0) {
            $RoomType = $Room->item(0)->getElementsByTagName('RoomType');
            if ($RoomType->length > 0) {
                $Code = $RoomType->item(0)->getAttribute('Code');
                $Name = $RoomType->item(0)->getAttribute('Name');
                $Special = $RoomType->item(0)->getElementsByTagName('Special');
                if ($Special->length > 0) {
                    $Special = $Special->item(0)->nodeValue;
                } else {
                    $Special = "";
                }
            }
            $RoomRate = $Room->item(0)->getElementsByTagName('RoomRate');
            if ($RoomRate->length > 0) {
                $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                $Start = $RoomRate->item(0)->getAttribute('Start');
                $End = $RoomRate->item(0)->getAttribute('End');
                $Rates = $RoomRate->item(0)->getElementsByTagName('Rates');
                if ($Rates->length > 0) {
                    $Rate = $Rates->item(0)->getElementsByTagName('Rate');
                    if ($Rate->length > 0) {
                        $EffectiveDate = $Rate->item(0)->getAttribute('EffectiveDate');
                        $ExpireDate = $Rate->item(0)->getAttribute('ExpireDate');
                        $Total = $Rate->item(0)->getElementsByTagName('Total');
                        if ($Total->length > 0) {
                            $Amount = $Rate->item(0)->getAttribute('Amount');
                            $Currency = $Rate->item(0)->getAttribute('Currency');
                        }
                    }
                }
                $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                if ($Total->length > 0) {
                    $Amount = $Total->item(0)->getAttribute('Amount');
                    $Commission = $Total->item(0)->getAttribute('Commission');
                    $Currency = $Total->item(0)->getAttribute('Currency');
                }
                $CancelPenalties = $RoomRate->item(0)->getElementsByTagName('CancelPenalties');
                if ($CancelPenalties->length > 0) {
                    $CancellationCostsToday = $CancelPenalties->item(0)->getAttribute('CancellationCostsToday');
                    $NonRefundable = $CancelPenalties->item(0)->getAttribute('NonRefundable');
                    $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName('CancelPenalty');
                    if ($CancelPenalty->length > 0) {
                        for ($i=0; $i < $CancelPenalty->length; $i++) { 
                            $Deadline = $CancelPenalty->item($i)->getElementsByTagName('Deadline');
                            if ($Deadline->length > 0) {
                                $TimeUnit = $Deadline->item(0)->getAttribute('TimeUnit');
                                $Units = $Deadline->item(0)->getAttribute('Units');
                            }
                            $Charge = $CancelPenalty->item($i)->getElementsByTagName('Charge');
                            if ($Charge->length > 0) {
                                $Amount = $Charge->item(0)->getAttribute('Amount');
                                $Currency = $Charge->item(0)->getAttribute('Currency');
                            }
                        }
                    }
                }
            }
            $Guests = $Room->item(0)->getElementsByTagName('Guests');
            if ($Guests->length > 0) {
                $Guest = $Guests->item(0)->getElementsByTagName('Guest');
                if ($Guest->length > 0) {
                    for ($j=0; $j < $Guest->length; $j++) { 
                        $AgeCode = $Guest->item($j)->getAttribute('AgeCode');
                        $LeadGuest = $Guest->item($j)->getAttribute('LeadGuest');
                        $PersonName = $Guest->item($j)->getElementsByTagName('PersonName');
                        if ($PersonName->length > 0) {
                            $NamePrefix = $PersonName->item(0)->getElementsByTagName('NamePrefix');
                            if ($NamePrefix->length > 0) {
                                $NamePrefix = $NamePrefix->item(0)->nodeValue;
                            } else {
                                $NamePrefix = "";
                            }
                            $GivenName = $PersonName->item(0)->getElementsByTagName('GivenName');
                            if ($GivenName->length > 0) {
                                $GivenName = $GivenName->item(0)->nodeValue;
                            } else {
                                $GivenName = "";
                            }
                            $Surname = $PersonName->item(0)->getElementsByTagName('Surname');
                            if ($Surname->length > 0) {
                                $Surname = $Surname->item(0)->nodeValue;
                            } else {
                                $Surname = "";
                            }
                        }
                    }
                }
            }
        }
    }
    $Info = $HotelResList->item(0)->getElementsByTagName('Info');
    if ($Info->length > 0) {
        $HotelCode = $Info->item(0)->getAttribute('HotelCode');
        $HotelName = $Info->item(0)->getAttribute('HotelName');
        $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
        $Rating = $Info->item(0)->getAttribute('Rating');
        $MasterCode = $Info->item(0)->getAttribute('MasterCode');
        $Recommended = $Info->item(0)->getAttribute('Recommended');
        $Image = $Info->item(0)->getElementsByTagName('Image');
        if ($Image->length > 0) {
            $Image = $Image->item(0)->nodeValue;
        } else {
            $Image = "";
        }
        $Position = $Info->item(0)->getElementsByTagName('Position');
        if ($Position->length > 0) {
            $Lon = $Position->item(0)->getAttribute('Lon');
            $Lat = $Position->item(0)->getAttribute('Lat');
        }
        $Address = $Info->item(0)->getElementsByTagName('Address');
        if ($Address->length > 0) {
            $AddressLine = $Address->item(0)->getElementsByTagName('AddressLine');
            if ($AddressLine->length > 0) {
                $AddressLine = $AddressLine->item(0)->nodeValue;
            } else {
                $AddressLine = "";
            }
            $CityName = $Address->item(0)->getElementsByTagName('CityName');
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            $PostalCode = $Address->item(0)->getElementsByTagName('PostalCode');
            if ($PostalCode->length > 0) {
                $PostalCode = $PostalCode->item(0)->nodeValue;
            } else {
                $PostalCode = "";
            }
            $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
            if ($CountryName->length > 0) {
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }
        }
        $Contacts = $Info->item(0)->getElementsByTagName('Contacts');
        if ($Contacts->length > 0) {
            $Phone = $Contacts->item(0)->getAttribute('Phone');
            $Fax = $Contacts->item(0)->getAttribute('Fax');
            $Website = $Contacts->item(0)->getAttribute('Website');
        }
        $Policy = $Info->item(0)->getElementsByTagName('Policy');
        if ($Policy->length > 0) {
            $CheckInTime = $Policy->item(0)->getAttribute('CheckInTime');
            $CheckOutTime = $Policy->item(0)->getAttribute('CheckOutTime');
        }
        $Warnings = $Info->item(0)->getElementsByTagName('Warnings');
        if ($Warnings->length > 0) {
            $Msg = $Warnings->item(0)->getElementsByTagName('Msg');
            if ($Msg->length > 0) {
                $message = "";
                for ($i=0; $i < $Msg->length; $i++) { 
                    $message = $Msg->item($i)->nodeValue;
                }
            }
        }
    }
    $HotelResInfo = $HotelResList->item(0)->getElementsByTagName('HotelResInfo');
    if ($HotelResInfo->length > 0) {
        $DateRange = $HotelResInfo->item(0)->getElementsByTagName('DateRange');
        if ($DateRange->length > 0) {
            $Start = $DateRange->item(0)->getAttribute('Start');
            $End = $DateRange->item(0)->getAttribute('End');
        }
        $Total = $HotelResInfo->item(0)->getElementsByTagName('Total');
        if ($Total->length > 0) {
            $Amount = $Total->item(0)->getAttribute('Amount');
            $Commission = $Total->item(0)->getAttribute('Commission');
            $Currency = $Total->item(0)->getAttribute('Currency');
        }
        $HotelResIDs = $HotelResInfo->item(0)->getElementsByTagName('HotelResIDs');
        if ($HotelResIDs->length > 0) {
            $HotelResID = $HotelResIDs->item(0)->getElementsByTagName('HotelResID');
            if ($HotelResID->length > 0) {
                for ($j=0; $j < $HotelResID->length; $j++) { 
                    $Type = $HotelResID->item($j)->getAttribute('Type');
                    $ID = $HotelResID->item($j)->getAttribute('ID');
                }
            }
        }
    }
}
$ResGlobalInfo = $OTA_BookingInfoRS->item(0)->getElementsByTagName('ResGlobalInfo');
if ($ResGlobalInfo->length > 0) {
    $DateRange = $ResGlobalInfo->item(0)->getElementsByTagName('DateRange');
    if ($DateRange->length > 0) {
        $Start = $DateRange->item(0)->getAttribute('Start');
        $End = $DateRange->item(0)->getAttribute('End');
    }
    $Total = $ResGlobalInfo->item(0)->getElementsByTagName('Total');
    if ($Total->length > 0) {
        $Amount = $Total->item(0)->getAttribute('Amount');
        $Commission = $Total->item(0)->getAttribute('Commission');
        $Currency = $Total->item(0)->getAttribute('Currency');
    }
    $HotelResIDs = $ResGlobalInfo->item(0)->getElementsByTagName('HotelResIDs');
    if ($HotelResIDs->length > 0) {
        $HotelResID = $HotelResIDs->item(0)->getElementsByTagName('HotelResID');
        if ($HotelResID->length > 0) {
            for ($j=0; $j < $HotelResID->length; $j++) { 
                $Type = $HotelResID->item($j)->getAttribute('Type');
                $ID = $HotelResID->item($j)->getAttribute('ID');
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