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

$sql = "SELECT id FROM cities";
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
        $citycode = $row->id;

        $url = 'http://parsystest.olympia.it/NewAvailabilityServlet/staticdata/OTA2014A';

        $raw = '<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/">
            <soap-env:Header>
                <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
                    <wsse:Username>628347</wsse:Username>
                    <wsse:Password>clubtest</wsse:Password>
                    <Context>olympia_europe_ts</Context>
                </wsse:Security>
            </soap-env:Header>
            <soap-env:Body>
                <OTA_ReadRQ xmlns:ns="http://www.opentravel.org/OTA/2003/05/common" xmlns="http://www.opentravel.org/OTA/2003/05" TimeStamp="2020-07-16T06:38:10.60">
                    <ReadRequests>
                        <HotelReadRequest>
                            <TPA_Extensions>
                                <RequestType>GetLocations</RequestType>
                                <CityCode>' . $citycode . '</CityCode>
                            </TPA_Extensions>
                        </HotelReadRequest>
                    </ReadRequests>
                </OTA_ReadRQ>
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
        $OTA_ReadRS = $inputDoc->getElementsByTagName('OTA_ReadRS');
        $ReadResponse = $OTA_ReadRS->item(0)->getElementsByTagName('ReadResponse');
        $Locations = $ReadResponse->item(0)->getElementsByTagName('Locations');
        if ($Locations->length > 0) {
            $Location = $Locations->item(0)->getElementsByTagName('Location');
            if ($Location->length > 0) {
                for ($i=0; $i < $Location->length; $i++) { 
                    $LocationCode = $Location->item($i)->getAttribute('LocationCode');
                    $LocationName = $Location->item($i)->getElementsByTagName('LocationName');
                    if ($LocationName->length > 0) {
                        $LocationName = $LocationName->item(0)->nodeValue;
                    } else {
                        $LocationName = "";
                    }
                    $CityCode = $Location->item($i)->getElementsByTagName('CityCode');
                    if ($CityCode->length > 0) {
                        $CityCode = $CityCode->item(0)->nodeValue;
                    } else {
                        $CityCode = "";
                    }
                    $CityName = $Location->item($i)->getElementsByTagName('CityName');
                    if ($CityName->length > 0) {
                        $CityName = $CityName->item(0)->nodeValue;
                    } else {
                        $CityName = "";
                    }
                    $CountryName = $Location->item($i)->getElementsByTagName('CountryName');
                    if ($CountryName->length > 0) {
                        $CountryName = $CountryName->item(0)->nodeValue;
                    } else {
                        $CountryName = "";
                    }
                    $CountryCode = $Location->item($i)->getElementsByTagName('CountryCode');
                    if ($CountryCode->length > 0) {
                        $CountryCode = $CountryCode->item(0)->nodeValue;
                    } else {
                        $CountryCode = "";
                    }
                    $CountryISO = $Location->item($i)->getElementsByTagName('CountryISO');
                    if ($CountryISO->length > 0) {
                        $CountryISO = $CountryISO->item(0)->nodeValue;
                    } else {
                        $CountryISO = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('olympia_locations');
                        $select->where(array(
                            'id' => $LocationCode
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (int)$data['id'];
                            if ($id > 0) {
                                $config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
                                $config = [
                                    'driver' => $config->db->driver,
                                    'database' => $config->db->database,
                                    'username' => $config->db->username,
                                    'password' => $config->db->password,
                                    'hostname' => $config->db->hostname
                                ];
                                $dbUpdate = new \Zend\Db\Adapter\Adapter($config);

                                $data = array(
                                    'datetime_updated' => time(),
                                    'name' => $LocationName,
                                    'citycode' => $CityCode,
                                    'cityname' => $CityName,
                                    'countrycode' => $CountryCode,
                                    'countryname' => $CountryName, 
                                    'countryiso' => $CountryISO
                                );
            
                                $sql    = new Sql($dbUpdate);
                                $update = $sql->update();
                                $update->table('olympia_locations');
                                $update->set($data);
                                $update->where(array('id' => $LocationCode));

                                $statement = $sql->prepareStatementForSqlObject($update);
                                $results = $statement->execute();
                                $dbUpdate->getDriver()
                                ->getConnection()
                                ->disconnect(); 
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('olympia_locations');
                                $insert->values(array(
                                    'id' => $LocationCode,
                                    'datetime_updated' => time(),
                                    'name' => $LocationName,
                                    'citycode' => $CityCode,
                                    'cityname' => $CityName,
                                    'countrycode' => $CountryCode,
                                    'countryname' => $CountryName, 
                                    'countryiso' => $CountryISO
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('olympia_locations');
                            $insert->values(array(
                                'id' => $LocationCode,
                                'datetime_updated' => time(),
                                'name' => $LocationName,
                                'citycode' => $CityCode,
                                'cityname' => $CityName,
                                'countrycode' => $CountryCode,
                                'countryname' => $CountryName, 
                                'countryiso' => $CountryISO
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO: ". $e;
                        echo $return;
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