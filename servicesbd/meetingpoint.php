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
echo "COMECOU MEETINGPOINT";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.services.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/StaticDataTransactions.asmx';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';

$raw= '<soapenv:Envelope
xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
    <MeetingPointList>
        <MeetingPointListRQ Version="1.1" Language="en">
            <Login Password="' . $password . '" Email="' . $email . '"/>
            <MeetingPointListRequest ZoneCode="15011" ProductType="TKT"/>
        </MeetingPointListRQ>
    </MeetingPointList>
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
    "SOAPAction: http://www.juniper.es/webservice/2007/MeetingPointList",
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

$config = new \Zend\Config\Config(include '../config/autoload/global.services.php');
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
$MeetingPointListResponse = $Body->item(0)->getElementsByTagName("MeetingPointListResponse");
if ($MeetingPointListResponse->length > 0) {
    $MeetingPointListRS = $MeetingPointListResponse->item(0)->getElementsByTagName("MeetingPointListRS");
    if ($MeetingPointListRS->length > 0) {
        $MeetingPointList = $MeetingPointListRS->item(0)->getElementsByTagName("MeetingPointList");
        if ($MeetingPointList->length > 0) {
            $IntCode = $MeetingPointList->item(0)->getAttribute("IntCode");
            $TimeStamp = $MeetingPointList->item(0)->getAttribute("TimeStamp");
            $Url = $MeetingPointList->item(0)->getAttribute("Url");
            $MeetingPointZones = $MeetingPointList->item(0)->getElementsByTagName("MeetingPointZones");
            if ($MeetingPointZones->length > 0) {
                $MeetingPointZone = $MeetingPointZones->item(0)->getElementsByTagName("MeetingPointZone");
                if ($MeetingPointZone->length > 0) {
                    for ($i=0; $i < $MeetingPointZone->length; $i++) { 
                        $Name = $MeetingPointZone->item($i)->getAttribute("Name");
                        $MeetingPoint = $MeetingPointZone->item($i)->getElementsByTagName("MeetingPoint");
                        if ($MeetingPoint->length > 0) {
                            $Code = $MeetingPoint->item(0)->getAttribute("Code");
                            $DestinationCode = $MeetingPoint->item(0)->getAttribute("DestinationCode");
                            $Latitude = $MeetingPoint->item(0)->getAttribute("Latitude");
                            $Longitude = $MeetingPoint->item(0)->getAttribute("Longitude");
                            $MeetingPointName = $MeetingPoint->item(0)->getElementsByTagName("Name");
                            if ($MeetingPointName->length > 0) {
                                $MeetingPointName = $MeetingPointName->item(0)->nodeValue;
                            } else {
                                $MeetingPointName = "";
                            }
                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('meetingpoint');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'code' => $Code,
                                    'destinationcode' => $DestinationCode,
                                    'latitude' => $Latitude,
                                    'longitude' => $Longitude,
                                    'meetingpointname' => $MeetingPointName
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 1: ". $e;
                                echo $return;
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
