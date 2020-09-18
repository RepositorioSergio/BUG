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
echo "COMECOU READ CSV<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.rakuten.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "zumata_pois.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, ",")) {
    if ($line > 0) {
        $name = $data[0];
        $name_long = $data[1];
        $latitude = $data[2];
        $longitude = $data[3];

        $name = utf8_encode($name);
        $name_long = utf8_encode($name_long);

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hotels_csv');
            $insert->values(array(
                'datetime_updated' => time(),
                'name' => $name, 
                'name_long' => $name_long, 
                'latitude' => $latitude, 
                'longitude' => $longitude 
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
    $line = $line + 1;
}
fclose($filename);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>