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
echo "COMECOU READ JSON<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.airdata2.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$string = file_get_contents("airports.json");
$response = json_decode($string, true);
foreach ($response as $key => $value) {
    $airportId = $value['airportId'];
    $GMT = $value['GMT'];
    $codeIataAirport = $value['codeIataAirport'];
    $codeIataCity = $value['codeIataCity'];
    $codeIcaoAirport = $value['codeIcaoAirport'];
    $codeIso2Country = $value['codeIso2Country'];
    $geonameId = $value['geonameId'];
    $latitudeAirport = $value['latitudeAirport'];
    $longitudeAirport = $value['longitudeAirport'];
    $nameAirport = $value['nameAirport'];
    $nameCountry = $value['nameCountry'];
    $phone = $value['phone'];
    $timezone = $value['timezone'];

    try {               
        $sql = new Sql($db);
        $insert = $sql->insert();
    $insert->into('airports');
        $insert->values(array(
            'airportid' => $airportId,
            'codeiataairport' => $codeIataAirport,
            'codeiatacity' => $codeIataCity,
            'codeicaoairport' => $codeIcaoAirport,
            'codeiso2country' => $codeIso2Country,
            'geonameid' => $geonameId,
            'latitudeairport' => $latitudeAirport,
            'longitudeairport' => $longitudeAirport,
            'nameairport' => $nameAirport,
            'namecountry' => $nameCountry,
            'phone' => $phone,
            'timezone' => $timezone,
            'gmt' => $GMT
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO: ". $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>