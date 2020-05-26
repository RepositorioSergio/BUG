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
echo "COMECOU Monarch<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'Monarch.csv';

$file = fopen($filename, 'r');
while ($data = fgetcsv($file, 0, ";")) {
    $shipname = $data[0];
    $shipcode = $data[1];
    $cabinno = $data[2];
    $categorycode = $data[4];
    $categorydescription = $data[5];
    $capacity = $data[6];
    $cabincategoryclass = $data[8];
    $deckcode = $data[10];
    $mainlowerbedtype = $data[11];
    $additionalbedtype = $data[12];
    $spaceforbaby = $data[13];
    $cabinlocationcode = $data[19];

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('cabinconfiguration_monarch');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'shipcode' => $shipcode,
        'shipname' => $shipname,
        'cabinno' => $cabinno,
        'categorycode' => $categorycode,
        'categorydescription' => $categorydescription,
        'capacity' => $capacity,
        'cabincategoryclass' => $cabincategoryclass,
        'deckcode' => $deckcode,
        'mainlowerbedtype' => $mainlowerbedtype,
        'additionalbedtype' => $additionalbedtype,
        'spaceforbaby' => $spaceforbaby,
        'cabinlocationcode' => $cabinlocationcode
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        echo $return;
        echo "Error: " . $e;
        echo $return;
        die();
    }
}
fclose($file);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
