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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "select id from rcc_countries where mapped_id=0";
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
        $country_id = (string) $row["id"];
        $cruises_xml11 = "";
        $sql = "select id, name from cruises_regions where id='" . $country_id . "' and cruises_xml11='" . $cruises_xml11 . "'";
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
            $id_country = $row["id"];
            $time = time();
            $sql = "update cruises_regions set cruises_xml11='" . $ship_id . "', datetime_updated=$time where id=" . $row['id'];
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
            $sql = "update rcc_countries set mapped_id='" . $id_country . "' where id='" . $country_id ."'";
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