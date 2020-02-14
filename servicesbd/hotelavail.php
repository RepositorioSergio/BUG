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
echo "COMECOU AVAILABILITY";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/availtransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';

$raw= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
  <HotelAvail>
    <HotelAvailRQ Version="1.1" Language="en">
      <Login Email="' . $email . '" Password="' . $password . '"/>
      <Paxes>
        <Pax IdPax="1"/>
        <Pax IdPax="2"/>
      </Paxes>
      <HotelRequest>
        <SearchSegmentsHotels>
          <SearchSegmentHotels Start="2020-06-20" End="2020-06-22" DestinationZone="15011"/>
          <CountryOfResidence>ES</CountryOfResidence>
        </SearchSegmentsHotels>
        <RelPaxesDist>
          <RelPaxDist>
            <RelPaxes>
              <RelPax IdPax="1"/>
              <RelPax IdPax="2"/>
            </RelPaxes>
          </RelPaxDist>
        </RelPaxesDist>
      </HotelRequest>
      <AdvancedOptions>
        <ShowHotelInfo>false</ShowHotelInfo>
        <ShowOnlyBestPriceCombination>true</ShowOnlyBestPriceCombination>
        <TimeOut>8000</TimeOut>
      </AdvancedOptions>
    </HotelAvailRQ>
  </HotelAvail>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=UTF-8",
    "SOAPAction: http://www.juniper.es/webservice/2007/HotelAvail",
    "Content-length: " . strlen($raw)
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

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
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
if ($HotelAvailResponse->length > 0) {
    $AvailabilityRS = $HotelAvailResponse->item(0)->getElementsByTagName("AvailabilityRS");
    if ($AvailabilityRS->length > 0) {
        $IntCode = $AvailabilityRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $AvailabilityRS->item(0)->getAttribute("TimeStamp");
        $Url = $AvailabilityRS->item(0)->getAttribute("Url");
        $Results = $AvailabilityRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $HotelResult = $Results->item(0)->getElementsByTagName("HotelResult");
            if ($HotelResult->length > 0) {
                for ($i=0; $i < $HotelResult->length; $i++) { 
                    $DestinationZone = $HotelResult->item($i)->getAttribute("DestinationZone");
                    $BestDeal = $HotelResult->item($i)->getAttribute("BestDeal");
                    $JPDCode = $HotelResult->item($i)->getAttribute("JPDCode");
                    $JPCode = $HotelResult->item($i)->getAttribute("JPCode");
                    $Code = $HotelResult->item($i)->getAttribute("Code");
                    $HotelOptions = $HotelResult->item($i)->getElementsByTagName("HotelOptions");
                    if ($HotelOptions->length > 0) {
                        $HotelOption = $HotelOptions->item(0)->getElementsByTagName("HotelOption");
                        if ($HotelOption->length > 0) {
                            for ($iAux=0; $iAux < $HotelOption->length; $iAux++) { 
                                $PackageContract = $HotelOption->item($iAux)->getAttribute("PackageContract");
                                $NonRefundable = $HotelOption->item($iAux)->getAttribute("NonRefundable");
                                $Status = $HotelOption->item($iAux)->getAttribute("Status");
                                $RatePlanCode = $HotelOption->item($iAux)->getAttribute("RatePlanCode");
                                $Board = $HotelOption->item($iAux)->getElementsByTagName("Board");
                                if ($Board->length > 0) {
                                    $Type = $Board->item(0)->getAttribute("Type");
                                    $Board = $Board->item(0)->nodeValue;
                                } else {
                                    $Board = "";
                                }
                                //Prices
                                $Prices = $HotelOption->item($iAux)->getElementsByTagName("Prices");
                                if ($Prices->length > 0) {
                                    $Price = $Prices->item(0)->getElementsByTagName("Price");
                                    if ($Price->length > 0) {
                                        $PriceType = $Price->item(0)->getAttribute("Type");
                                        $PriceCurrency = $Price->item(0)->getAttribute("Currency");
                                        $TotalFixAmounts = $Price->item(0)->getElementsByTagName("TotalFixAmounts");
                                        if ($TotalFixAmounts->length > 0) {
                                            $Nett = $TotalFixAmounts->item(0)->getAttribute("Nett");
                                            $Gross = $TotalFixAmounts->item(0)->getAttribute("Gross");
                                            $Service = $TotalFixAmounts->item(0)->getElementsByTagName("Service");
                                            if ($Service->length > 0) {
                                                $Amount = $Service->item(0)->getAttribute("Amount");
                                            }
                                        }
                                    }
                                }
                                //AdditionalElements
                                $AdditionalElements = $HotelOption->item($iAux)->getElementsByTagName("AdditionalElements");
                                if ($AdditionalElements->length > 0) {
                                    $HotelOffers = $AdditionalElements->item(0)->getElementsByTagName("HotelOffers");
                                    if ($HotelOffers->length > 0) {
                                        $HotelOffer = $HotelOffers->item(0)->getElementsByTagName("HotelOffer");
                                        if ($HotelOffer->length > 0) {
                                            $HORoomCategory = $HotelOffer->item(0)->getAttribute("RoomCategory");
                                            $Begin = $HotelOffer->item(0)->getAttribute("Begin");
                                            $End = $HotelOffer->item(0)->getAttribute("End");
                                            $HOName = $HotelOffer->item(0)->getElementsByTagName("Name");
                                            if ($HOName->length > 0) {
                                                $HOName = $HOName->item(0)->nodeValue;
                                            } else {
                                                $HOName = "";
                                            }
                                            $Description = $HotelOffer->item(0)->getElementsByTagName("Description");
                                            if ($Description->length > 0) {
                                                $Description = $Description->item(0)->nodeValue;
                                            } else {
                                                $Description = "";
                                            }
                                        }
                                    }
                                }
                                //HotelRooms
                                $HotelRooms = $HotelOption->item($iAux)->getElementsByTagName("HotelRooms");
                                if ($HotelRooms->length > 0) {
                                    $HotelRoom = $HotelRooms->item(0)->getElementsByTagName("HotelRoom");
                                    if ($HotelRoom->length > 0) {
                                        for ($iAux2=0; $iAux2 < $HotelRoom->length; $iAux2++) { 
                                            $Source = $HotelRoom->item($iAux2)->getAttribute("Source");
                                            $Units = $HotelRoom->item($iAux2)->getAttribute("Units");
                                            $Name = $HotelRoom->item($iAux2)->getElementsByTagName("Name");
                                            if ($Name->length > 0) {
                                                $Name = $Name->item(0)->nodeValue;
                                            } else {
                                                $Name = "";
                                            }
                                            $RoomCategory = $HotelRoom->item($iAux2)->getElementsByTagName("RoomCategory");
                                            if ($RoomCategory->length > 0) {
                                                $RoomCategoryType = $RoomCategory->item(0)->getAttribute("Type");
                                            } else {
                                                $RoomCategoryType = "";
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
    }
}


try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('allowedCardsData');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'card_type' => $card_type,
        'name' => $name
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO 2: " . $e;
    echo $return;
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
