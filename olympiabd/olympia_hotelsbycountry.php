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

$sql = "SELECT id, countryiso FROM countries";
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
        $countryiso = $row->countryiso;

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
            <OTA_HotelSearchRQ xmlns:ns="http://www.opentravel.org/OTA/2003/05/common" TimeStamp="2020-04-20T11:52:44.81">
                <Criteria>
                    <Criterion>
                        <RefPoint CountryCode="' . $countryiso . '" />
                    </Criterion>
                </Criteria>
            </OTA_HotelSearchRQ>
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
        $OTA_HotelSearchRS = $inputDoc->getElementsByTagName('OTA_HotelSearchRS');
        $Properties = $OTA_HotelSearchRS->item(0)->getElementsByTagName('Properties');
        if ($Properties->length > 0) {
            $Property = $Properties->item(0)->getElementsByTagName('Property');
            if ($Property->length > 0) {
                for ($i=0; $i < $Property->length; $i++) { 
                    $HotelCode = $Property->item($i)->getAttribute('HotelCode');
                    echo $return;
                    echo $HotelCode;
                    echo $return;
                    $HotelName = $Property->item($i)->getAttribute('HotelName');
                    $Position = $Property->item($i)->getElementsByTagName('Position');
                    if ($Position->length > 0) {
                        $Latitude = $Position->item(0)->getAttribute('Latitude');
                        $Longitude = $Position->item(0)->getAttribute('Longitude');
                    }
                    $Address = $Property->item($i)->getElementsByTagName('Address');
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
                        $TPA_Extensions = $Address->item(0)->getElementsByTagName('TPA_Extensions');
                        if ($TPA_Extensions->length > 0) {
                            $CityCode = $TPA_Extensions->item(0)->getElementsByTagName('CityCode');
                            if ($CityCode->length > 0) {
                                $CityCode = $CityCode->item(0)->nodeValue;
                            } else {
                                $CityCode = "";
                            }
                            $CountryCode = $TPA_Extensions->item(0)->getElementsByTagName('CountryCode');
                            if ($CountryCode->length > 0) {
                                $CountryCode = $CountryCode->item(0)->nodeValue;
                            } else {
                                $CountryCode = "";
                            }
                            $CountryISO = $TPA_Extensions->item(0)->getElementsByTagName('CountryISO');
                            if ($CountryISO->length > 0) {
                                $CountryISO = $CountryISO->item(0)->nodeValue;
                            } else {
                                $CountryISO = "";
                            }
                        }
                    }
                    $ContactNumbers = $Property->item($i)->getElementsByTagName('ContactNumbers');
                    if ($ContactNumbers->length > 0) {
                        $ContactNumber = $ContactNumbers->item(0)->getElementsByTagName('ContactNumber');
                        if ($ContactNumber->length > 0) {
                            $PhoneNumber = $ContactNumber->item(0)->getAttribute('PhoneNumber');
                            $PhoneTechType = $ContactNumber->item(0)->getAttribute('PhoneTechType');
                        }
                    }
                    $Award = $Property->item($i)->getElementsByTagName('Award');
                    if ($Award->length > 0) {
                        $Rating = $Award->item(0)->getAttribute('Rating');
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('olympia_hotelsbycountries');
                        $select->where(array(
                            'id' => $HotelCode
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
                                    'name' => $HotelName, 
                                    'latitude' => $Latitude,
                                    'longitude' => $Longitude,
                                    'addressline' => $AddressLine, 
                                    'cityname' => $CityName,
                                    'citycode' => $CityCode,
                                    'postalcode' => $PostalCode, 
                                    'countrycode' => $CountryCode,
                                    'countryname' => $CountryName,
                                    'countryiso' => $CountryISO,
                                    'phonenumber' => $PhoneNumber, 
                                    'phonetechtype' => $PhoneTechType,
                                    'rating' => $Rating
                                );
            
                                $sql    = new Sql($dbUpdate);
                                $update = $sql->update();
                                $update->table('olympia_hotelsbycountries');
                                $update->set($data);
                                $update->where(array('id' => $HotelCode));

                                $statement = $sql->prepareStatementForSqlObject($update);
                                $results = $statement->execute();
                                $dbUpdate->getDriver()
                                ->getConnection()
                                ->disconnect(); 
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('olympia_hotelsbycountries');
                                $insert->values(array(
                                    'id' => $HotelCode,
                                    'datetime_updated' => time(),
                                    'name' => $HotelName, 
                                    'latitude' => $Latitude,
                                    'longitude' => $Longitude,
                                    'addressline' => $AddressLine, 
                                    'cityname' => $CityName,
                                    'citycode' => $CityCode,
                                    'postalcode' => $PostalCode, 
                                    'countrycode' => $CountryCode,
                                    'countryname' => $CountryName,
                                    'countryiso' => $CountryISO,
                                    'phonenumber' => $PhoneNumber, 
                                    'phonetechtype' => $PhoneTechType,
                                    'rating' => $Rating
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
                            $insert->into('olympia_hotelsbycountries');
                            $insert->values(array(
                                'id' => $HotelCode,
                                'datetime_updated' => time(),
                                'name' => $HotelName, 
                                'latitude' => $Latitude,
                                'longitude' => $Longitude,
                                'addressline' => $AddressLine, 
                                'cityname' => $CityName,
                                'citycode' => $CityCode,
                                'postalcode' => $PostalCode, 
                                'countrycode' => $CountryCode,
                                'countryname' => $CountryName,
                                'countryiso' => $CountryISO,
                                'phonenumber' => $PhoneNumber, 
                                'phonetechtype' => $PhoneTechType,
                                'rating' => $Rating
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