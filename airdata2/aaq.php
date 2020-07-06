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

$string = file_get_contents("AAQ.json");
$response = json_decode($string, true);
foreach ($response as $key => $value) {
    $airlineIata = $value['airlineIata'];
    $airlineIcao = $value['airlineIcao'];
    $arrivalIata = $value['arrivalIata'];
    $arrivalIcao = $value['arrivalIcao'];
    $arrivalTerminal = $value['arrivalTerminal'];
    $arrivalTime = $value['arrivalTime'];
    $codeshares = $value['codeshares'];
    $departureIata = $value['departureIata'];
    $departureIcao = $value['departureIcao'];
    $departureTerminal = $value['departureTerminal'];
    $departureTime = $value['departureTime'];
    $flightNumber = $value['flightNumber'];
    $regNumber = $value['regNumber'];

    try {               
        $sql = new Sql($db);
        $insert = $sql->insert();
    $insert->into('aaq');
        $insert->values(array(
            'airlineiata' => $airlineIata,
            'airlineicao' => $airlineIcao,
            'arrivaliata' => $arrivalIata,
            'arrivalicao' => $arrivalIcao,
            'arrivalterminal' => $arrivalTerminal,
            'arrivaltime' => $arrivalTime,
            'codeshares' => $codeshares,
            'departureiata' => $departureIata,
            'departureicao' => $departureIcao,
            'departureterminal' => $departureTerminal,
            'departuretime' => $departureTime,
            'flightnumber' => $flightNumber,
            'regnumber' => $regNumber
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