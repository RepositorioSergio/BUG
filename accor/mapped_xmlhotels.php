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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.accor.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT id, name FROM accor_hotels";
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
        $name = addslashes($name);

        //Update accor_hotels
        $sql = "select id, description from xmlhotels where description='$name'";
        echo "<br/>SQL: " . $sql . "<br/>";
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
            $id_hotel = $row_settings["id"];
            echo $return;
            echo "ID: " . $id;
            echo $return;
            //
            // Found
            //
            $time = time();
            $sql = "update accor_hotels set mapped_id=$id_hotel where id='$id'";
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
            echo "<br/>Hotel id does not exist " . $id;
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';

?>