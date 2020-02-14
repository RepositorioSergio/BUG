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
echo "COMECOU CHECK AVAIL";
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

$url = 'http://xml-uat.bookingengine.es/webservice/JP/Operations/AvailTransactions.asmx?WSDL';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';
$hotelcode = '';
$RatePlanCode = '';

$raw= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
  <HotelCheckAvail>
    <HotelCheckAvailRQ Version="1.1" Language="en">
      <Login Email="' . $email . '" Password="' . $password . '"/>
      <HotelCheckAvailRequest>
        <HotelOption RatePlanCode="' . $RatePlanCode . '"/>
        <SearchSegmentsHotels>
          <SearchSegmentHotels Start="2020-06-20" End="2020-06-22"/>
          <HotelCodes>
            <HotelCode>' . $hotelcode . '</HotelCode>
          </HotelCodes>
        </SearchSegmentsHotels>
      </HotelCheckAvailRequest>
    </HotelCheckAvailRQ>
  </HotelCheckAvail>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
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
$HotelCheckAvailResponse = $Body->item(0)->getElementsByTagName("HotelCheckAvailResponse");
if ($HotelCheckAvailResponse->length > 0) {
    $CheckAvailRS = $HotelCheckAvailResponse->item(0)->getElementsByTagName("CheckAvailRS");
    if ($CheckAvailRS->length > 0) {
        $IntCode = $CheckAvailRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $CheckAvailRS->item(0)->getAttribute("TimeStamp");
        $Url = $CheckAvailRS->item(0)->getAttribute("Url");
        $Results = $CheckAvailRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $HotelResult = $Results->item(0)->getElementsByTagName("HotelResult");
            if ($HotelResult->length > 0) {
                $HotelOptions = $HotelResult->item(0)->getElementsByTagName("HotelOptions");
                if ($HotelOptions->length > 0) {
                    $HotelOption = $HotelOptions->item(0)->getElementsByTagName("HotelOption");
                    if ($HotelOption->length > 0) {
                        $NonRefundable = $HotelOption->item(0)->getAttribute("NonRefundable");
                        $Status = $HotelOption->item(0)->getAttribute("Status");
                        $RatePlanCode = $HotelOption->item(0)->getAttribute("RatePlanCode");
                        $Board = $HotelOption->item(0)->getElementsByTagName("Board");
                        if ($Board->length > 0) {
                            $Type = $Board->item(0)->getAttribute("Type");
                            $Board = $Board->item(0)->nodeValue;
                        } else {
                            $Board = "";
                        }
                        //Prices
                        $Prices = $HotelOption->item(0)->getElementsByTagName("Prices");
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
                                    } else {
                                        $Amount = "";
                                    }

                                    $ServiceTaxes = $TotalFixAmounts->item(0)->getElementsByTagName("ServiceTaxes");
                                    if ($ServiceTaxes->length > 0) {
                                        $ServiceTaxesAmount = $ServiceTaxes->item(0)->getAttribute("Amount");
                                        $Included = $ServiceTaxes->item(0)->getAttribute("Included");
                                    } else {
                                        $ServiceTaxesAmount = "";
                                        $Included = "";
                                    }                
                                }
                            }
                        }
                        //HotelRooms
                        $HotelRooms = $HotelOption->item(0)->getElementsByTagName("HotelRooms");
                        if ($HotelRooms->length > 0) {
                            $HotelRoom = $HotelRooms->item(0)->getElementsByTagName("HotelRoom");
                            if ($HotelRoom->length > 0) {
                                $Source = $HotelRoom->item(0)->getAttribute("Source");
                                $Units = $HotelRoom->item(0)->getAttribute("Units");
                                $AvailRooms = $HotelRoom->item(0)->getAttribute("AvailRooms");
                                $Name = $HotelRoom->item(0)->getElementsByTagName("Name");
                                if ($Name->length > 0) {
                                    $Name = $Name->item(0)->nodeValue;
                                } else {
                                    $Name = "";
                                }
                                $RoomCategory = $HotelRoom->item(0)->getElementsByTagName("RoomCategory");
                                if ($RoomCategory->length > 0) {
                                    $RoomCategoryType = $RoomCategory->item(0)->getAttribute("Type");
                                    $RoomCategory = $RoomCategory->item(0)->nodeValue;
                                } else {
                                    $RoomCategory = "";
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
