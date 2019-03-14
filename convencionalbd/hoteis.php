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
echo "COMECOU HOTEIS SIATAR<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT Code FROM cities";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $city_code = $row->Code;

        $date = new DateTime("NOW");
        $timestamp = $date->format( "Y-m-d\TH:i:s.v" );

        $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/">
        <soap:Header/>
        <soap:Body>
            <xnet:getHotelList>
                <xnet:aRequest EchoToken="123" TimeStamp="' . $timestamp . '" Version="1.0">
                    <xnet:POS>
                        <xnet:Source>
                            <xnet:RequestorID ID="a6dge3!tnsf2or" PartnerID="TEST" Username="xnet" Password="pctnx!!!"/>
                        </xnet:Source> 
                    </xnet:POS>
                    <xnet:HotelSearchCriterion HotelCityCode="' . $city_code . '"/>
                </xnet:aRequest>
            </xnet:getHotelList>
        </soap:Body>
        </soap:Envelope>';

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
        $url = "http://xnetinfo.redirectme.net:8080/homologacao_webservice/Integration/ServerIntegration.asmx";

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
        /* echo "<br/>RESPONSE";
        echo '<xmp>';
        var_dump($response);
        echo '</xmp>'; */

        $config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
        $getHotelListResponse = $Body->item(0)->getElementsByTagName("getHotelListResponse");
        $getHotelListResult = $getHotelListResponse->item(0)->getElementsByTagName("getHotelListResult");
        $Hotels = $getHotelListResult->item(0)->getElementsByTagName("Hotels");
        $node = $Hotels->item(0)->getElementsByTagName("Hotel");
        for ($i=0; $i < $node->length; $i++) { 
            $Code = $node->item($i)->getAttribute("Code");
            $Name = $node->item($i)->getAttribute("Name");
            $StarRating = $node->item($i)->getAttribute("StarRating");
            $Email = $node->item($i)->getAttribute("Email");
            $Url = $node->item($i)->getAttribute("Url");
            $MaxAccommodationRate = $node->item($i)->getAttribute("MaxAccommodationRate");
            $MinAccommodationRate = $node->item($i)->getAttribute("MinAccommodationRate");
            echo $return;
            echo "Code: " . $Code;
            echo $return;

            $Description = $node->item($i)->getElementsByTagName("Description");
            if ($Description->length > 0) {
                $Description = $Description->item(0)->nodeValue;
            } else {
                $Description = "";
            }
            $Comments = $node->item($i)->getElementsByTagName("Comments");
            if ($Comments->length > 0) {
                $Comments = $Comments->item(0)->nodeValue;
            } else {
                $Comments = "";
            }

            $Address2 = "";
            $Address = $node->item($i)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $Latitude = $Address->item(0)->getAttribute("Latitude");
                $Longitude = $Address->item(0)->getAttribute("Longitude");
                $Address2 = $Address->item(0)->nodeValue;
                echo $return;
                echo "Address2: " . $Address2;
                echo $return;
                $City = $Address->item(0)->getElementsByTagName("City");
                if ($City->length > 0) {
                    $CityCode = $City->item(0)->getAttribute("Code");
                    $CityName = $City->item(0)->getAttribute("Name");
                    $CityState = $City->item(0)->getAttribute("State");
                    $CityCountryCode = $City->item(0)->getAttribute("CountryCode");
                }
            }

            $PhoneNumbers = $node->item($i)->getElementsByTagName("PhoneNumbers");
            if ($PhoneNumbers->length > 0) {
                $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                if ($PhoneNumber->length > 0) {
                    $AreaCityCode = $PhoneNumber->item(0)->getAttribute("AreaCityCode");
                    $CountryAccessCode = $PhoneNumber->item(0)->getAttribute("CountryAccessCode");
                    $Prefix = $PhoneNumber->item(0)->getAttribute("Prefix");
                    $LineNumber = $PhoneNumber->item(0)->getAttribute("LineNumber");
                } else {
                    $AreaCityCode = "";
                    $CountryAccessCode = "";
                    $Prefix = "";
                    $LineNumber = "";
                }
            } else {
                $AreaCityCode = "";
                $CountryAccessCode = "";
                $Prefix = "";
                $LineNumber = "";
            }

            $MainPhoto = $node->item($i)->getElementsByTagName("MainPhoto");
            if ($MainPhoto->length > 0) {
                $MainPhoto = $MainPhoto->item(0)->nodeValue;
            } else {
                $MainPhoto = "";
            }
            

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hoteis');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Code' => $Code,
                    'Name' => $Name,
                    'StarRating' => $StarRating,
                    'Email' => $Email,
                    'Description' => $Description,
                    'MaxAccommodationRate' => $MaxAccommodationRate,
                    'MinAccommodationRate' => $MinAccommodationRate,
                    'Comments' => $Comments,
                    'CityCode' => $CityCode,
                    'CityName' => $CityName,
                    'CityState' => $CityState,
                    'CityCountryCode' => $CityCountryCode,
                    'Latitude' => $Latitude,
                    'Longitude' => $Longitude,
                    'Address' => $Address2,
                    'AreaCityCode' => $AreaCityCode,
                    'CountryAccessCode' => $CountryAccessCode,
                    'Prefix' => $Prefix,
                    'LineNumber' => $LineNumber,
                    'MainPhoto' => $MainPhoto
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

            $foto = "";
            $Photo = $node->item($i)->getElementsByTagName("Photo");
            if ($Photo->length > 0) {
                for ($j=0; $j < $Photo->length; $j++) { 
                    $foto = $Photo->item($j)->nodeValue;
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hoteis_fotos');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Code' => $Code,
                        'foto' => $foto
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
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