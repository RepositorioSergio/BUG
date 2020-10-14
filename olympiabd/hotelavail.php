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

$url = 'http://parsystest.olympia.it/NewAvailabilityServlet/hotelavail/OTA2014Compact';

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
		<OTA_HotelAvailRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact" >
			<HotelSearch>
				<Currency Code="USD"/>
				<HotelLocation CityCode="7"/>
				<DateRange Start="2021-04-12" End="2021-04-14"/>
				<RoomCandidates>  
					<RoomCandidate RPH="1">
						<Guests>
                            <Guest AgeCode="A" Count="2" />
						</Guests>
                    </RoomCandidate>
                    <RoomCandidate RPH="2">
						<Guests>
                            <Guest AgeCode="A" Count="3" />
						</Guests>
                    </RoomCandidate>
                    <RoomCandidate RPH="3">
						<Guests>
                            <Guest AgeCode="A" Count="3" />
						</Guests>
                    </RoomCandidate>
				</RoomCandidates>
			</HotelSearch>
		</OTA_HotelAvailRQ>
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
$OTA_HotelAvailRS = $inputDoc->getElementsByTagName('OTA_HotelAvailRS');
$Hotels = $OTA_HotelAvailRS->item(0)->getElementsByTagName('Hotels');
if ($Hotels->length > 0) {
    $DateRange = $Hotels->item(0)->getElementsByTagName('DateRange');
    if ($DateRange->length > 0) {
        $Start = $DateRange->item(0)->getAttribute('Start');
        $End = $DateRange->item(0)->getAttribute('End');
    }
    $RoomCandidates = $Hotels->item(0)->getElementsByTagName('RoomCandidates');
    if ($RoomCandidates->length > 0) {
        $RoomCandidate = $RoomCandidates->item(0)->getElementsByTagName('RoomCandidate');
        if ($RoomCandidate->length > 0) {
            $RPH = $RoomCandidate->item(0)->getAttribute('RPH');
            $Guests = $RoomCandidate->item(0)->getElementsByTagName('Guests');
            if ($Guests->length > 0) {
                $Guest = $Guests->item(0)->getElementsByTagName('Guest');
                if ($Guest->length > 0) {
                    $AgeCode = $Guest->item(0)->getAttribute('AgeCode');
                    $Count = $Guest->item(0)->getAttribute('Count');
                }
            }
        }
    }
    $Hotel = $Hotels->item(0)->getElementsByTagName('Hotel');
    if ($Hotel->length > 0) {
        for ($i=0; $i < $Hotel->length; $i++) { 
            $Info = $Hotel->item($i)->getElementsByTagName('Info');
            if ($Info->length > 0) {
                $HotelCode = $Info->item(0)->getAttribute('HotelCode');
                $HotelName = $Info->item(0)->getAttribute('HotelName');
                $HotelCityCode = $Info->item(0)->getAttribute('HotelCityCode');
                $Rating = $Info->item(0)->getAttribute('Rating');
                $MasterCode = $Info->item(0)->getAttribute('MasterCode');
                $Recommended = $Info->item(0)->getAttribute('Recommended');
                $HotelProvider = $Info->item(0)->getElementsByTagName('HotelProvider');
                if ($HotelProvider->length > 0) {
                    $HotelProvider = $HotelProvider->item(0)->nodeValue;
                } else {
                    $HotelProvider = "";
                }
                $HotelIdent = $Info->item(0)->getElementsByTagName('HotelIdent');
                if ($HotelIdent->length > 0) {
                    $HotelIdent = $HotelIdent->item(0)->nodeValue;
                } else {
                    $HotelIdent = "";
                }
            }
            $BestPrice = $Hotel->item($i)->getElementsByTagName('BestPrice');
            if ($BestPrice->length > 0) {
                $Amount = $BestPrice->item(0)->getAttribute('Amount');
                $Currency = $BestPrice->item(0)->getAttribute('Currency');
            }
            $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
            if ($Rooms->length > 0) {
                $Room = $Rooms->item(0)->getElementsByTagName('Room');
                if ($Room->length > 0) {
                    for ($iAux=0; $iAux < $Room->length; $iAux++) { 
                        $RPH = $Room->item($iAux)->getAttribute('RPH');
                        $Best = $Room->item($iAux)->getAttribute('Best');
                        $Status = $Room->item($iAux)->getAttribute('Status');
                        $RoomType = $Room->item($iAux)->getElementsByTagName('RoomType');
                        if ($RoomType->length > 0) {
                            $RoomTypeCode = $RoomType->item(0)->getAttribute('Code');
                            $RoomTypeName = $RoomType->item(0)->getAttribute('Name');
                        }
                        $RoomRates = $Room->item($iAux)->getElementsByTagName('RoomRates');
                        if ($RoomRates->length > 0) {
                            $RoomRate = $RoomRates->item(0)->getElementsByTagName('RoomRate');
                            if ($RoomRate->length > 0) {
                                $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');
                                $BookingCode = $RoomRate->item(0)->getAttribute('BookingCode');
                                $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                                if ($Total->length > 0) {
                                    $Amount = $Total->item(0)->getAttribute('Amount');
                                    $Commission = $Total->item(0)->getAttribute('Commission');
                                    $Currency = $Total->item(0)->getAttribute('Currency');
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