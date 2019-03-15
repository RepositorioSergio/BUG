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
echo "COMECOU RIU<br/>";
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
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
echo $return;
echo $riuServiceURL;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Header/>
    <soapenv:Body>
        <ns6:HotelAvail xmlns:ns6="http://services.enginexml.rumbonet.riu.com">
            <ns6:in0>
                <ns1:AdultsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">2</ns1:AdultsCount>
                <ns1:ChildCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">0</ns1:ChildCount>
                <ns1:CountryCode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">ES</ns1:CountryCode>
                <HotelList xmlns="http://dtos.enginexml.rumbonet.riu.com">
                    <HotelsList>
                        <ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">216</ns2:int>
                        <ns2:int xmlns:ns2="http://services.common.rumbonet.riu.com">4401</ns2:int>
                    </HotelsList>
                </HotelList>
                <ns1:InfantsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">0</ns1:InfantsCount>
                <ns1:Language xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">E</ns1:Language>
                <ns1:MealPlan xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/>
                <ns1:promocode xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com"/>
                <ns1:rateReference xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:nil="1"/>
                <RoomList xmlns="http://dtos.enginexml.rumbonet.riu.com">
                    <RoomConfig>
                        <RoomStayCandidate>
                            <AdultsCount>2</AdultsCount>
                            <ChildCount>0</ChildCount>
                            <InfantsCount>0</InfantsCount>
                        </RoomStayCandidate>
                    </RoomConfig>
                </RoomList>
                <ns1:RoomsCount xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">1</ns1:RoomsCount>
                <ns1:StayDateEnd xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">20190629</ns1:StayDateEnd>
                <ns1:StayDateStart xmlns:ns1="http://dtos.enginexml.rumbonet.riu.com">20190620</ns1:StayDateStart>
            </ns6:in0>
        </ns6:HotelAvail>
    </soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));


$client->setUri($riuServiceURL);
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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.RIU.php');
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
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$HotelAvailResponse = $Body->item(0)->getElementsByTagName("HotelAvailResponse");
$HotelAvailResponse2 = $HotelAvailResponse->item(0)->getElementsByTagName("HotelAvailResponse");
$availabilityList = $HotelAvailResponse2->item(0)->getElementsByTagName("availabilityList");
$node = $availabilityList->item(0)->getElementsByTagName("AvailabilityGroup");
for ($i=0; $i < $node->length; $i++) {       
    $amount = $node->item($i)->getElementsByTagName("amount");
    if ($amount->length > 0) {
        $amount = $amount->item(0)->nodeValue;
    } else {
        $amount = "";
    }
    $amountWithoutOffer = $node->item($i)->getElementsByTagName("amountWithoutOffer");
    if ($amountWithoutOffer->length > 0) {
        $amountWithoutOffer = $amountWithoutOffer->item(0)->nodeValue;
    } else {
        $amountWithoutOffer = "";
    }
    $currencyCode = $node->item($i)->getElementsByTagName("currencyCode");
    if ($currencyCode->length > 0) {
        $currencyCode = $currencyCode->item(0)->nodeValue;
    } else {
        $currencyCode = "";
    }
    $hotelID = $node->item($i)->getElementsByTagName("hotelID");
    if ($hotelID->length > 0) {
        $hotelID = $hotelID->item(0)->nodeValue;
    } else {
        $hotelID = "";
    }
    $moroccoTaxes = $node->item($i)->getElementsByTagName("moroccoTaxes");
    if ($moroccoTaxes->length > 0) {
        $moroccoTaxes = $moroccoTaxes->item(0)->nodeValue;
    } else {
        $moroccoTaxes = "";
    }
    $quoteType = $node->item($i)->getElementsByTagName("quoteType");
    if ($quoteType->length > 0) {
        $quoteType = $quoteType->item(0)->nodeValue;
    } else {
        $quoteType = "";
    }
    $rateHotel = $node->item($i)->getElementsByTagName("rateHotel");
    if ($rateHotel->length > 0) {
        $rateHotel = $rateHotel->item(0)->nodeValue;
    } else {
        $rateHotel = "";
    }
    $taxIncluded = $node->item($i)->getElementsByTagName("taxIncluded");
    if ($taxIncluded->length > 0) {
        $taxIncluded = $taxIncluded->item(0)->nodeValue;
    } else {
        $taxIncluded = "";
    }
    $typePrice = $node->item($i)->getElementsByTagName("typePrice");
    if ($typePrice->length > 0) {
        $typePrice = $typePrice->item(0)->nodeValue;
    } else {
        $typePrice = "";
    }
    $uniqueID = $node->item($i)->getElementsByTagName("uniqueID");
    if ($uniqueID->length > 0) {
        $uniqueID = $uniqueID->item(0)->nodeValue;
    } else {
        $uniqueID = "";
    }
    $promocode = $node->item($i)->getElementsByTagName("promocode");
    if ($promocode->length > 0) {
        $promocode = $promocode->item(0)->nodeValue;
    } else {
        $promocode = "";
    }
    $rateReference = $node->item($i)->getElementsByTagName("rateReference");
    if ($rateReference->length > 0) {
        $rateReference = $rateReference->item(0)->nodeValue;
    } else {
        $rateReference = "";
    }

    $translationTHabs = $node->item($i)->getElementsByTagName("translationTHabs");
    if ($translationTHabs->length > 0) {
        $listTHabs = $translationTHabs->item(0)->getElementsByTagName("listTHabs");
        if ($listTHabs->length > 0) {
            $listTHabs = $listTHabs->item(0)->nodeValue;
        } else {
            $listTHabs = "";
        }
    }

    $roomList = $node->item($i)->getElementsByTagName("roomList");
    if ($roomList->length > 0) {
        $RoomStayGroup = $roomList->item(0)->getElementsByTagName("RoomStayGroup");
        if ($RoomStayGroup->length > 0) {
            $RSGamount = $RoomStayGroup->item(0)->getElementsByTagName("amount");
            if ($RSGamount->length > 0) {
                $RSGamount = $RSGamount->item(0)->nodeValue;
            } else {
                $RSGamount = "";
            }
            $mealPlan = $RoomStayGroup->item(0)->getElementsByTagName("mealPlan");
            if ($mealPlan->length > 0) {
                $mealPlan = $mealPlan->item(0)->nodeValue;
            } else {
                $mealPlan = "";
            }
            $roomTypeCode = $RoomStayGroup->item(0)->getElementsByTagName("roomTypeCode");
            if ($roomTypeCode->length > 0) {
                $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
            } else {
                $roomTypeCode = "";
            }

            $roomConfig = $RoomStayGroup->item(0)->getElementsByTagName("roomConfig");
            if ($roomConfig->length > 0) {
                $AdultsCount = $roomConfig->item(0)->getElementsByTagName("AdultsCount");
                if ($AdultsCount->length > 0) {
                    $AdultsCount = $AdultsCount->item(0)->nodeValue;
                } else {
                    $AdultsCount = "";
                }
                $Ages = $roomConfig->item(0)->getElementsByTagName("Ages");
                if ($Ages->length > 0) {
                    $Ages = $Ages->item(0)->nodeValue;
                } else {
                    $Ages = "";
                }
                $ChildCount = $roomConfig->item(0)->getElementsByTagName("ChildCount");
                if ($ChildCount->length > 0) {
                    $ChildCount = $ChildCount->item(0)->nodeValue;
                } else {
                    $ChildCount = "";
                }
                $InfantsCount = $roomConfig->item(0)->getElementsByTagName("InfantsCount");
                if ($InfantsCount->length > 0) {
                    $InfantsCount = $InfantsCount->item(0)->nodeValue;
                } else {
                    $InfantsCount = "";
                }
            }
        }
    }


    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hotelavail');
        $insert->values(array(
            'uniqueID' => $uniqueID,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'hotelID' => $hotelID,
            'amount' => $amount,
            'amountWithoutOffer' => $amountWithoutOffer,
            'currencyCode' => $currencyCode,
            'moroccoTaxes' => $moroccoTaxes,
            'quoteType' => $quoteType,
            'rateHotel' => $rateHotel,
            'taxIncluded' => $taxIncluded,
            'typePrice' => $typePrice,
            'promocode' => $promocode,
            'rateReference' => $rateReference,
            'listTHabs' => $listTHabs,
            'RSGamount' => $RSGamount,
            'mealPlan' => $mealPlan,
            'roomTypeCode' => $roomTypeCode,
            'AdultsCount' => $AdultsCount,
            'Ages' => $Ages,
            'ChildCount' => $ChildCount,
            'InfantsCount' => $InfantsCount
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO HOTEL: " . $e;
        echo $return;
    }

} 

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>