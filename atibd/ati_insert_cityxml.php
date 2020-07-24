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
echo "COMECOU INSERT<br/>";
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

$sql = "SELECT id, name, state FROM ati_area";
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
        $id = $row->id;
        $name = $row->name;
        $state = $row->state;
        $name = addslashes($name);

        // State id
        $sql = "select id from zones where code='$state' and country_id=243";
        $statement = $db->createStatement($sql);
        $row_settings = $statement->prepare();
        try {
            $row_settings = $statement->execute();
        } catch (\Exception $e) {
            echo $return;
            echo "Error: " . $e;
            echo $return;
        }
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $id_zone = $row_settings["id"];
        } else {
            $sql = "select id from zones where code='$state'";
            $statement = $db->createStatement($sql);
            $row_settings = $statement->prepare();
            try {
                $row_settings = $statement->execute();
            } catch (\Exception $e) {
                echo $return;
                echo "Error: " . $e;
                echo $return;
            }
            $row_settings->buffer();
            if ($row_settings->valid()) {
                $row_settings = $row_settings->current();
                $id_zone = $row_settings["id"];
            } else {
                //
                // Something is wrong
                //
                echo "<br/>Zone id does not exist " . $state;
            }
        }

        //Update city_xml40
        $sql = "select id, name from cities where name='$name' and zone_id=" . $id_zone;
        $statement = $db->createStatement($sql);
        $row_settings = $statement->prepare();
        try {
            $row_settings = $statement->execute();
        } catch (\Exception $e) {
            echo $return;
            echo "Error: " . $e;
            echo $return;
        }
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $id_city = $row_settings["id"];
            //
            // Found
            //
            $time = time();
            $sql = "update cities set city_xml40='$id', datetime_updated=$time where id=" . $id_city;
            $statement = $db->createStatement($sql);
            $statement->prepare();
            try {
                $row_settings = $statement->execute();
            } catch (\Exception $e) {
                echo $return;
                echo "Error: " . $e;
                echo $return;
                die();
            }
            $time = time();
            $sql = "update ati_area set mapped=$id_city, datetime_updated=$time where id=" . $id;
            $statement = $db->createStatement($sql);
            $statement->prepare();
            try {
                $row_settings = $statement->execute();
            } catch (\Exception $e) {
                echo $return;
                echo "Error: " . $e;
                echo $return;
                die();
            }
        } else {
            //
            // Something is wrong
            //
            echo "<br/>City id does not exist " . $id_city;
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';

?>