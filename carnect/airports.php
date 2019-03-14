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
$sql = "select value from settings where name='enableCarnectCars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_carnect = $affiliate_id;
} else {
    $affiliate_id_carnect = 0;
}
$sql = "select value from settings where name='CarnectLogin' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectLogin = $row_settings['value'];
}
$sql = "select value from settings where name='CarnectCarspassword' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='CarnectCarsDestinationsServicesURL' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $CarnectCarsDestinationsServicesURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT country_id FROM countries";
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
        $country_id = $row->country_id;
        echo $return;
        echo "country_id: " . $country_id;
        echo $return;


        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
        <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
        <soap12:Body>
            <VehicleAirportRequest xmlns="http://www.opentravel.org/OTA/2003/05">
            <Language>EN</Language>
            <CountryID>' . $country_id . '</CountryID>
            </VehicleAirportRequest>
        </soap12:Body>
        </soap12:Envelope>';
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($xml_post_string)
        );
        //
        // PHP CURL for https connection with auth
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $CarnectCarsDestinationsServicesURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $xmlresult = curl_exec($ch);
        curl_close($ch);
        echo $xmlresult;

        $config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
        $config = [
            'driver' => $config->db->driver,
            'database' => $config->db->database,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'hostname' => $config->db->hostname
        ];
        $db = new \Zend\Db\Adapter\Adapter($config);

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresult);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $VehicleAirportResponse = $Body->item(0)->getElementsByTagName('VehicleAirportResponse');
        $Airports = $VehicleAirportResponse->item(0)->getElementsByTagName('Airports');
        $node = $Airports->item(0)->getElementsByTagName('Airport');
        for ($j=0; $j < $node->length; $j++) { 
            $airport_id = $node->item($j)->getAttribute('id');
            $iata = $node->item($j)->getAttribute('iata');
            $latitude = $node->item($j)->getAttribute('latitude');
            $longitude = $node->item($j)->getAttribute('longitude');
            echo $return;
            echo "airport_id: " . $airport_id;
            echo $return;
            $CityID = $node->item($j)->getElementsByTagName('CityID');
            if ($CityID->length > 0) {
                $CityID = $CityID->item(0)->nodeValue;
            } else {
                $CityID = "";
            }
            $Name = $node->item($j)->getElementsByTagName('Name');
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('airports');
                $select->where(array(
                    'airport_id' => $airport_id
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (int) $data['airport_id'];
                    if ($id > 0) {
                        $sql = new Sql($db);
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'airport_id' => $airport_id,
                            'iata' => $iata,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'CityID' => $CityID,
                            'Name' => $Name
                            );
                            $where['airport_id = ?']  = $airport_id;
                        $update = $sql->update('airports', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('airports');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'airport_id' => $airport_id,
                            'iata' => $iata,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'CityID' => $CityID,
                            'Name' => $Name
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
                    $insert->into('airports');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'airport_id' => $airport_id,
                        'iata' => $iata,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'CityID' => $CityID,
                        'Name' => $Name
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }
            } catch (Exception $e) {
                echo $return;
                echo "Exception: " . $e;
                echo $return;
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