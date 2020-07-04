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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.airdata.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$string = file_get_contents("departure.json");
$response = json_decode($string, true);
foreach ($response as $key => $value) {
    $icao24 = $value['icao24'];
    $firstSeen = $value['firstSeen'];
    $estDepartureAirport = $value['estDepartureAirport'];
    $lastSeen = $value['lastSeen'];
    $estArrivalAirport = $value['estArrivalAirport'];
    $callsign = $value['callsign'];
    $estDepartureAirportHorizDistance = $value['estDepartureAirportHorizDistance'];
    $estDepartureAirportVertDistance = $value['estDepartureAirportVertDistance'];
    $estArrivalAirportHorizDistance = $value['estArrivalAirportHorizDistance'];
    $estArrivalAirportVertDistance = $value['estArrivalAirportVertDistance'];
    $departureAirportCandidatesCount = $value['departureAirportCandidatesCount'];
    $arrivalAirportCandidatesCount = $value['arrivalAirportCandidatesCount'];
    //echo $id . "ID<br/>";

    try {               
        $sql = new Sql($db);
        $insert = $sql->insert();
    $insert->into('departure');
        $insert->values(array(
            'icao24' => $icao24,
            'firstseen' => $firstSeen,
            'lastseen' => $lastSeen,
            'estdepartureairport' => $estDepartureAirport,
            'estarrivalairport' => $estArrivalAirport,
            'callsign' => $callsign,
            'estdepartureairporthorizdistance' => $estDepartureAirportHorizDistance,
            'estdepartureairportvertdistance' => $estDepartureAirportVertDistance,
            'estarrivalairporthorizdistance' => $estArrivalAirportHorizDistance,
            'estarrivalairportvertdistance' => $estArrivalAirportVertDistance,
            'departureairportcandidatescount' => $departureAirportCandidatesCount,
            'arrivalairportcandidatescount' => $arrivalAirportCandidatesCount
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