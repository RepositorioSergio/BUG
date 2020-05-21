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
echo "COMECOU CSV HOTELS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.sabre.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

function readCSV(string $file) {
    $config = new \Zend\Config\Config(include '../config/autoload/global.sabre.php');
    $config = [
        'driver' => $config->db->driver,
        'database' => $config->db->database,
        'username' => $config->db->username,
        'password' => $config->db->password,
        'hostname' => $config->db->hostname
    ];
    $db = new \Zend\Db\Adapter\Adapter($config);
    
    $line = 0;
    $object = fopen($file, 'r');

    while (($data = fgetcsv($object, 0, "|")) !== false) {
        if ($line > 0) {
            $globalpropertyid = $data[0];
            $sabreid = $data[1];
            $globalpropertyname = $data[2];
            $addressline1 = $data[3];
            $addressline2 = $data[4];
            $city = $data[5];
            $state = $data[6];
            $zip = $data[7];
            $countryname = $data[8];
            $countrycode = $data[9];
            $airportcode = $data[10];
            $latitude = $data[11];
            $longitude = $data[12];
            $chain = $data[13];
            $source = $data[14];
            $propertystatus = $data[15];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotels');
                $insert->values(array(
                    'globalpropertyid' => $globalpropertyid,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'sabreid' => $sabreid,
                    'globalpropertyname' => $globalpropertyname,
                    'addressline1' => $addressline1,
                    'addressline2' => $addressline2,
                    'city' => $city,
                    'state' => $state,
                    'zip' => $zip,
                    'countryname' => $countryname,
                    'countrycode' => $countrycode,
                    'airportcode' => $airportcode,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'chain' => $chain,
                    'source' => $source,
                    'propertystatus' => $propertystatus
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERROR 1: " . $e;
                echo $return;
            }
        }
        $line = $line + 1;
    }
    fclose($object);
}
echo $return;
echo "ERROR 2";
echo $return;
readCSV("GlobalPropertyReport1.csv");
readCSV("GlobalPropertyReport2.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
