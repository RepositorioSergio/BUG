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

$sql = "SELECT id FROM hotelsbycountries";
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
        $HotelCode = $row->id;

        $url = 'http://parsystest.olympia.it/NewAvailabilityServlet/hotelinfo/OTA2014A';

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
                <OTA_HotelDescriptiveInfoRQ xmlns:ns="http://www.opentravel.org/OTA/2003/05/common" TimeStamp="2020-05-01T01:08:13.88">
                    <HotelDescriptiveInfos LangRequested="en-GB">
                        <HotelDescriptiveInfo HotelCode="' . $HotelCode . '" />
                    </HotelDescriptiveInfos>
                </OTA_HotelDescriptiveInfoRQ>
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
        $OTA_HotelDescriptiveInfoRS = $inputDoc->getElementsByTagName('OTA_HotelDescriptiveInfoRS');
        $HotelDescriptiveContents = $OTA_HotelDescriptiveInfoRS->item(0)->getElementsByTagName('HotelDescriptiveContents');
        if ($HotelDescriptiveContents->length > 0) {
            $HotelDescriptiveContent = $HotelDescriptiveContents->item(0)->getElementsByTagName('HotelDescriptiveContent');
            if ($HotelDescriptiveContent->length > 0) {
                $HotelCode = $HotelDescriptiveContent->item(0)->getAttribute('HotelCode');
                $HotelName = $HotelDescriptiveContent->item(0)->getAttribute('HotelName');
                $HotelCityCode = $HotelDescriptiveContent->item(0)->getAttribute('HotelCityCode');
                // HotelInfo
                $HotelInfo = $HotelDescriptiveContent->item(0)->getElementsByTagName('HotelInfo');
                if ($HotelInfo->length > 0) {
                    $CategoryCodes = $HotelInfo->item(0)->getElementsByTagName('CategoryCodes');
                    if ($CategoryCodes->length > 0) {
                        $HotelCategory = $CategoryCodes->item(0)->getElementsByTagName('HotelCategory');
                        if ($HotelCategory->length > 0) {
                            $HotelCategoryCode = $HotelCategory->item(0)->getAttribute('Code');
                        }
                    }
                    $Descriptions = $HotelInfo->item(0)->getElementsByTagName('Descriptions');
                    if ($Descriptions->length > 0) {
                        $DescriptiveText = $Descriptions->item(0)->getElementsByTagName('DescriptiveText');
                        if ($DescriptiveText->length > 0) {
                            $DescriptiveText2 = "";
                            for ($i=0; $i < $DescriptiveText->length; $i++) { 
                                $Name = $DescriptiveText->item($i)->getAttribute('Name');
                                $DescriptiveText2 = $DescriptiveText->item($i)->nodeValue;

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('olympia_hoteldescriptive_descriptions');
                                    $insert->values(array(
                                        'datetime_updated' => time(),
                                        'name' => $Name, 
                                        'descriptivetext' => $DescriptiveText2,
                                        'hotelcode' => $HotelCode,
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 2: ". $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                    $Position = $HotelInfo->item(0)->getElementsByTagName('Position');
                    if ($Position->length > 0) {
                        $Latitude = $Position->item(0)->getAttribute('Latitude');
                        $Longitude = $Position->item(0)->getAttribute('Longitude');
                        $PositionAccuracyCode = $Position->item(0)->getAttribute('PositionAccuracyCode');
                    }
                }
                // ContactInfos
                $ContactInfos = $HotelDescriptiveContent->item(0)->getElementsByTagName('ContactInfos');
                if ($ContactInfos->length > 0) {
                    $ContactInfo = $ContactInfos->item(0)->getElementsByTagName('ContactInfo');
                    if ($ContactInfo->length > 0) {
                        $Addresses = $ContactInfo->item(0)->getElementsByTagName('Addresses');
                        if ($Addresses->length > 0) {
                            $Address = $Addresses->item(0)->getElementsByTagName('Address');
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
                            }
                        }
                        $Phones = $ContactInfo->item(0)->getElementsByTagName('Phones');
                        if ($Phones->length > 0) {
                            $Phone = $Phones->item(0)->getElementsByTagName('Phone');
                            if ($Phone->length > 0) {
                                $PhoneNumber = $Phone->item(0)->getAttribute('PhoneNumber');
                                $PhoneTechType = $Phone->item(0)->getAttribute('PhoneTechType');
                            }
                        }
                        $URLs = $ContactInfo->item(0)->getElementsByTagName('URLs');
                        if ($URLs->length > 0) {
                            $URLs = $ContactInfo->item(0)->getElementsByTagName('URLs');
                            if ($URLs->length > 0) {
                                $URL = $URLs->item(0)->getElementsByTagName('URL');
                                if ($URL->length > 0) {
                                    $url2 = "";
                                    for ($j=0; $j < $URL->length; $j++) { 
                                        $Type = $URL->item($j)->getAttribute('Type');
                                        $url2 = $URL->item($j)->nodeValue;
                                        
                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('olympia_hoteldescriptive_urls');
                                            $insert->values(array(
                                                'datetime_updated' => time(),
                                                'type' => $Type, 
                                                'url' => $url2,
                                                'hotelcode' => $HotelCode,
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "ERRO 3: ". $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('olympia_hoteldescriptive');
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
                                'hotelcitycode' => $HotelCityCode,
                                'hotelcategorycode' => $HotelCategoryCode,
                                'latitude' => $Latitude, 
                                'longitude' => $Longitude,
                                'positionaccuracycode' => $PositionAccuracyCode,
                                'addressline' => $AddressLine, 
                                'cityname' => $CityName,
                                'postalcode' => $PostalCode, 
                                'countryname' => $CountryName,
                                'phonenumber' => $PhoneNumber, 
                                'phonetechtype' => $PhoneTechType
                            );
        
                            $sql    = new Sql($dbUpdate);
                            $update = $sql->update();
                            $update->table('olympia_hoteldescriptive');
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
                            $insert->into('olympia_hoteldescriptive');
                            $insert->values(array(
                                'id' => $HotelCode,
                                'datetime_updated' => time(),
                                'name' => $HotelName, 
                                'hotelcitycode' => $HotelCityCode,
                                'hotelcategorycode' => $HotelCategoryCode,
                                'latitude' => $Latitude, 
                                'longitude' => $Longitude,
                                'positionaccuracycode' => $PositionAccuracyCode,
                                'addressline' => $AddressLine, 
                                'cityname' => $CityName,
                                'postalcode' => $PostalCode, 
                                'countryname' => $CountryName,
                                'phonenumber' => $PhoneNumber, 
                                'phonetechtype' => $PhoneTechType
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
                        $insert->into('olympia_hoteldescriptive');
                        $insert->values(array(
                            'id' => $HotelCode,
                            'datetime_updated' => time(),
                            'name' => $HotelName, 
                            'hotelcitycode' => $HotelCityCode,
                            'hotelcategorycode' => $HotelCategoryCode,
                            'latitude' => $Latitude, 
                            'longitude' => $Longitude,
                            'positionaccuracycode' => $PositionAccuracyCode,
                            'addressline' => $AddressLine, 
                            'cityname' => $CityName,
                            'postalcode' => $PostalCode, 
                            'countryname' => $CountryName,
                            'phonenumber' => $PhoneNumber, 
                            'phonetechtype' => $PhoneTechType
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>