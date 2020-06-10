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
echo "COMECOU SHIPS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$code = '38290196';
$agency = 'Costamar';
$password = 'C0sT2m2R';
$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <Agency xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Code>' . $code . '</Code>
    </Agency>
    <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Name>' . $agency . '</Name>
      <Password>' . $password . '</Password>
    </Partner>
  </soap:Header>
  <soap:Body>
    <ListAllShips xmlns="http://schemas.costacrociere.com/WebAffiliation" />
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
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ListAllShips",
    "Content-length: ".strlen($raw)
));
$url = "https://training.costaclick.net/WAWS_1_9/Availability.asmx";

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

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
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
$ListAllShipsResponse = $Body->item(0)->getElementsByTagName("ListAllShipsResponse");
if ($ListAllShipsResponse->length > 0) {
    $ListAllShipsResult = $ListAllShipsResponse->item(0)->getElementsByTagName("ListAllShipsResult");
    if ($ListAllShipsResult->length > 0) {
        $Ship = $ListAllShipsResult->item(0)->getElementsByTagName("Ship");
        if ($Ship->length > 0) {
            for ($i=0; $i < $Ship->length; $i++) { 
                $Code = $Ship->item($i)->getElementsByTagName("Code");
                if ($Code->length > 0) {
                    $Code = $Code->item(0)->nodeValue;
                } else {
                    $Code = "";
                }
                $Name = $Ship->item($i)->getElementsByTagName("Name");
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
                $URL = $Ship->item($i)->getElementsByTagName("URL");
                if ($URL->length > 0) {
                    $URL = $URL->item(0)->nodeValue;
                } else {
                    $URL = "";
                }
                $Cabins = $Ship->item($i)->getElementsByTagName("Cabins");
                if ($Cabins->length > 0) {
                    $Cabins = $Cabins->item(0)->nodeValue;
                } else {
                    $Cabins = "";
                }
                $Crew = $Ship->item($i)->getElementsByTagName("Crew");
                if ($Crew->length > 0) {
                    $Crew = $Crew->item(0)->nodeValue;
                } else {
                    $Crew = "";
                }
                $Guests = $Ship->item($i)->getElementsByTagName("Guests");
                if ($Guests->length > 0) {
                    $Guests = $Guests->item(0)->nodeValue;
                } else {
                    $Guests = "";
                }
                $Width = $Ship->item($i)->getElementsByTagName("Width");
                if ($Width->length > 0) {
                    $Width = $Width->item(0)->nodeValue;
                } else {
                    $Width = "";
                }
                $Length = $Ship->item($i)->getElementsByTagName("Length");
                if ($Length->length > 0) {
                    $Length = $Length->item(0)->nodeValue;
                } else {
                    $Length = "";
                }
                $Tonnage = $Ship->item($i)->getElementsByTagName("Tonnage");
                if ($Tonnage->length > 0) {
                    $Tonnage = $Tonnage->item(0)->nodeValue;
                } else {
                    $Tonnage = "";
                }
                $MaxSpeed = $Ship->item($i)->getElementsByTagName("MaxSpeed");
                if ($MaxSpeed->length > 0) {
                    $MaxSpeed = $MaxSpeed->item(0)->nodeValue;
                } else {
                    $MaxSpeed = "";
                }
                $YearOfLaunch = $Ship->item($i)->getElementsByTagName("YearOfLaunch");
                if ($YearOfLaunch->length > 0) {
                    $YearOfLaunch = $YearOfLaunch->item(0)->nodeValue;
                } else {
                    $YearOfLaunch = "";
                }
                $MonthOfLaunch = $Ship->item($i)->getElementsByTagName("MonthOfLaunch");
                if ($MonthOfLaunch->length > 0) {
                    $MonthOfLaunch = $MonthOfLaunch->item(0)->nodeValue;
                } else {
                    $MonthOfLaunch = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('ships');
                    $insert->values(array(
                        'code' => $Code,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $Name,
                        'url' => $URL,
                        'cabins' => $Cabins,
                        'crew' => $Crew,
                        'guests' => $Guests,
                        'width' => $Width,
                        'length' => $Length,
                        'tonnage' => $Tonnage,
                        'maxspeed' => $MaxSpeed,
                        'yearoflaunch' => $YearOfLaunch,
                        'monthoflaunch' => $MonthOfLaunch
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 1: " . $e;
                    echo $return;
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