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

$sql = "SELECT ISO FROM countries";
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
        $ISO = $row->ISO;
        echo $return;
        echo "ISO: " . $ISO;
        echo $return;

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>
        <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema"
        xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
        <soap12:Body>
            <VehicleCityRequest xmlns="http://www.opentravel.org/OTA/2003/05">
            <Language>EN</Language>
            <CountryISO>' . $ISO . '</CountryISO>
            </VehicleCityRequest>
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
        $VehicleCityResponse = $Body->item(0)->getElementsByTagName('VehicleCityResponse');
        $Cities = $VehicleCityResponse->item(0)->getElementsByTagName('Cities');
        $node = $Cities->item(0)->getElementsByTagName('City');
        for ($j=0; $j < $node->length; $j++) { 
            $city_id = $node->item($j)->getAttribute('id');
            $iata = $node->item($j)->getAttribute('iata');
            $latitude = $node->item($j)->getAttribute('latitude');
            $longitude = $node->item($j)->getAttribute('longitude');
            echo $return;
            echo "city_id: " . $city_id;
            echo $return;
            $Name = $node->item($j)->getElementsByTagName('Name');
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
            $RegionID = $node->item($j)->getElementsByTagName('RegionID');
            if ($RegionID->length > 0) {
                $RegionID = $RegionID->item(0)->nodeValue;
            } else {
                $RegionID = "";
            }

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('cities');
                $select->where(array(
                    'city_id' => $city_id
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (int) $data['city_id'];
                    if ($id > 0) {
                        $sql = new Sql($db);
                        $data = array(
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'city_id' => $city_id,
                            'iata' => $iata,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'Name' => $Name,
                            'RegionID' => $RegionID
                            );
                            $where['city_id = ?']  = $city_id;
                        $update = $sql->update('cities', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('cities');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'city_id' => $city_id,
                            'iata' => $iata,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'Name' => $Name,
                            'RegionID' => $RegionID
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
                    $insert->into('cities');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'city_id' => $city_id,
                        'iata' => $iata,
                        'latitude' => $latitude,
                        'longitude' => $longitude,
                        'Name' => $Name,
                        'RegionID' => $RegionID
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