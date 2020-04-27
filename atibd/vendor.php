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
echo "COMECOU VENDOR<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.ati.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "content/vendor_v3.txt";
$file = fopen($filename, 'r');

while (!feof($file)) {
    $content = fgets($file);
    $array = explode("|", $content);
    list($id, $name, $description, $address1, $address2, $city, $state, $countrycode, $postalcode, $starrating, $rooms, $phonenumber, $latitude, $longitude, $brand_id, $area_id) = $array;

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('vendor');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'description' => $description,
            'address1' => $address1,
            'address2' => $address2,
            'city' => $city,
            'state' => $state,
            'countrycode' => $countrycode,
            'postalcode' => $postalcode,
            'starrating' => $starrating,
            'rooms' => $rooms,
            'phonenumber' => $phonenumber,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'brand_id' => $brand_id,
            'area_id' => $area_id
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO 1: ". $e;
        echo $return;
    }
}
fclose($file);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>