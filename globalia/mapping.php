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

$starRatingArray = array();
$starRatingArray[0]['stars'] = 0;
    
$config = new \Zend\Config\Config(include '../config/autoload/global.globalia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "select id, name_en, countryid_en, countryname_en, provinceid_en from globalia_destinations where mapped=0";
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
        $destination = $row["name_en"];
        $cityid = (string) $row["id"];
        $id = (int) $row["id"];
        $country_id = $row["countryid_en"];
        $country = $row["countryname_en"];
        $provinceid = $row["provinceid_en"];
        $city_xml50 = "";
        echo addslashes($destination) . "<br/>";
        if ($provinceid === "") {
            $city_xml50 = $country_id . ":" . $cityid;
        } elseif ($country_id === "") {
            $city_xml50 = $provinceid . ":" . $cityid;
        } elseif ($country_id === "" and $provinceid === "") {
            $city_xml50 = $cityid;
        } else {
            $city_xml50 = $country_id . ":" . $provinceid . ":" . $cityid;
        }
        echo $city_xml50 .  "<br/>";
        $sql = "select id, name from cities where name='" . addslashes($destination) . "' and city_xml50=0";
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
            $sql = "update cities set city_xml50='" . $city_xml50 . "' where id=" . $row['id'];
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
            $sql = "update globalia_destinations set mapped=1 where id=" . $id;
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