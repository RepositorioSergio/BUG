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
echo "COMECOU GOGLOBAL";
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
$config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:gog="http://www.goglobal.travel/">
   <soapenv:Header/>
   <soapenv:Body>
      <gog:MakeRequest>
         <gog:requestType>11</gog:requestType>
         <gog:xmlRequest><![CDATA[
          <Root>
		    <Header>
                <Agency>1521636</Agency>
                <User>CLUB1XML</User>
                <Password>andrade1998</Password>
                <Operation>HOTEL_SEARCH_REQUEST</Operation>
                <OperationType>Request</OperationType>
		    </Header>
		    <Main Version="2" ResponseFormat="JSON">
                <SortOrder>1</SortOrder>
                <FilterPriceMin>0</FilterPriceMin>
                <FilterPriceMax>10000</FilterPriceMax>
                <MaximumWaitTime>30</MaximumWaitTime>
                <MaxResponses>1000</MaxResponses>
                <FilterRoomBasises>
                        <FilterRoomBasis></FilterRoomBasis>
                </FilterRoomBasises>
                <HotelName></HotelName>
                <Apartments>false</Apartments>
                <CityCode>75</CityCode>
                <ArrivalDate>2019-08-10</ArrivalDate>
                <Nights>3</Nights>
                <Rooms>
                        <Room Adults="2" RoomCount="1"></Room>
                        <Room Adults="2" RoomCount="1" >
                            <ChildAge>7</ChildAge>
                            <ChildAge>5</ChildAge>
                        </Room>
                </Rooms>
		    </Main>
    		</Root>
         ]]></gog:xmlRequest>
      </gog:MakeRequest>
   </soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: text/xml; charset=utf-8",
    "Content-length: " . strlen($raw)
));

$client->setUri('http://xml.qa.goglobal.travel/XMLWebService.asmx');
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
// echo $return;
// echo $response;
// echo $return;

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$MakeRequestResult = $inputDoc->getElementsByTagName("MakeRequestResult");
if ($MakeRequestResult->length > 0) {
    $response = $MakeRequestResult->item(0)->nodeValue;
} else {
    $response = "";
}

$response = json_decode($response, true);
if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}

if (json_last_error() == 0) {
    echo '- Nao houve erro! O parsing foi perfeito';
} else {
    echo 'Erro!<br/>';
    switch (json_last_error()) {
        
        case JSON_ERROR_DEPTH:
            echo ' - profundidade maxima excedida';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - state mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Caracter de controle encontrado';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Erro de sintaxe! String JSON mal-formada!';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Erro na codificação UTF-8';
            break;
        default:
            echo ' – Erro desconhecido';
            break;
    }
}

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.goglobal.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

echo $return;
echo "TAM RESP: " . count($response);
echo $return;
$Hotels = $response['Hotels'];
echo $return;
echo "TAM: " . count($Hotels);
echo $return;

for ($i = 0; $i < count($Hotels); $i ++) {
    $HotelName = $Hotels[$i]['HotelName'];
    $HotelCode = $Hotels[$i]['HotelCode'];
    $CountryId = $Hotels[$i]['CountryId'];
    $CityId = $Hotels[$i]['CityId'];
    $Location = $Hotels[$i]['Location'];
    $Thumbnail = $Hotels[$i]['Thumbnail'];
    $Longitude = $Hotels[$i]['Longitude'];
    $Latitude = $Hotels[$i]['Latitude'];
    echo $return;
    echo "HotelCode: " . $HotelCode;
    echo $return;
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('hotels');
        $insert->values(array(
            'HotelCode' => $HotelCode,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'HotelName' => $HotelName,
            'CountryId' => $CountryId,
            'CityId' => $CityId,
            'Location' => $Location,
            'Thumbnail' => $Thumbnail,
            'Longitude' => $Longitude,
            'Latitude' => $Latitude
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error: " . $e;
        echo $return;
    }
    
    $Offers = $Hotels[$i]['Offers'];
    for ($j = 0; $j < count($Offers); $j ++) {
        $HotelSearchCode = $Offers[$j]['HotelSearchCode'];
        $CxlDeadLine = $Offers[$j]['CxlDeadLine'];
        $NonRef = $Offers[$j]['NonRef'];
        $RoomBasis = $Offers[$j]['RoomBasis'];
        $Availability = $Offers[$j]['Availability'];
        $TotalPrice = $Offers[$j]['TotalPrice'];
        $Currency = $Offers[$j]['Currency'];
        $Category = $Offers[$j]['Category'];
        $Remark = $Offers[$j]['Remark'];
        $Special = $Offers[$j]['Special'];
        $Preferred = $Offers[$j]['Preferred'];
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('offers');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'HotelSearchCode' => $HotelSearchCode,
                'CxlDeadLine' => $CxlDeadLine,
                'NonRef' => $NonRef,
                'RoomBasis' => $RoomBasis,
                'Availability' => $Availability,
                'TotalPrice' => $TotalPrice,
                'Currency' => $Currency,
                'Category' => $Category,
                'Remark' => $Remark,
                'Special' => $Special,
                'Preferred' => $Preferred,
                'HotelCode' => $HotelCode
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO OFFER: " . $e;
            echo $return;
        }
        
        $room = '';
        $Rooms = $Offers[$j]['Rooms'];
        for ($k = 0; $k < count($Rooms); $k ++) {
            $room = $Rooms[$k];
            
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('rooms');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'room' => $room,
                    'HotelSearchCode' => $HotelSearchCode
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO ROOM: " . $e;
                echo $return;
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