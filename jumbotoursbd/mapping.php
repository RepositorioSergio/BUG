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
echo "START MAPPING...<br/>";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "select id, name, countrycode from jumbo_cities where mapped=0";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
try {
    $results = $statement->execute();
    $results->buffer();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}
if ($results instanceof ResultInterface && $results->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($results);
    foreach ($resultSet as $row) {
        $id = $row["id"];
        $name = $row["name"];
        $countrycode = $row["countrycode"];
        $city_xml73 = "";
        $name = addslashes($name);
        
        $sql = "select id, name from cities where name='" . $name . "' and city_xml73='" . $city_xml73. "'";
        $statement = $db->createStatement($sql);
        try {
            $statement->prepare();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        try {
            $statement = $statement->execute();
            $statement->buffer();
        } catch (\Exception $e) {
            echo $return;
            echo $e->getMessage();
            echo $return;
            die();
        }
        if ($statement->valid()) {
            $row = $statement->current();
            $sql = "update cities set city_xml73=" . $id . " where id=" . $row['id'];
            $statement = $db->createStatement($sql);
            try {
                $statement->prepare();
            } catch (\Exception $e) {
                echo $return;
                echo $e->getMessage();
                echo $return;
                die();
            }
            $row_settings = $statement->execute();
            $sql = "update jumbo_cities set mapped=" . $row['id']. " where id=" . $id;
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_settings = $statement->execute();
        } else {
            echo $return;
            echo "Not Found.<br/>";
            echo $return;
        }
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
    echo "END MAPPING...<br/>";
?>