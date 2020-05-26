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
echo "COMECOU CATEGORIES<br/>";
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

$filename = 'Categorias_SalesForce.csv';

$file = fopen($filename, 'r');
while ($data = fgetcsv($file, 0, ";")) {
    $code = $data[0];
    $description = $data[1];
    $cubierta = $data[2];
    $shipcode = "SO";

    if ($code != "") {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('categories_salesforce');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $code,
            'description' => $description,
            'cubierta' => $cubierta,
            'shipcode' => $shipcode
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
}
fclose($file);

$line2 = 0;
$file = fopen($filename, 'r');
while ($data = fgetcsv($file, 0, ";")) {
    $code = $data[4];
    $description = $data[5];
    $cubierta = $data[6];
    $shipcode = "MO";

    if ($code != "") {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('categories_salesforce');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $code,
            'description' => $description,
            'cubierta' => $cubierta,
            'shipcode' => $shipcode
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
}
fclose($file);

$file = fopen($filename, 'r');
while ($data = fgetcsv($file, 0, ";")) {
    $code = $data[8];
    $description = $data[9];
    $cubierta = $data[10];
    $shipcode = "HN/HR";

    if ($code != "") {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('categories_salesforce');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $code,
            'description' => $description,
            'cubierta' => $cubierta,
            'shipcode' => $shipcode
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
}
fclose($file);

$file = fopen($filename, 'r');
while ($data = fgetcsv($file, 0, ";")) {
    $code = $data[12];
    $description = $data[13];
    $cubierta = $data[14];
    $shipcode = "ZE/ZT";

    if ($code != "") {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('categories_salesforce');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'code' => $code,
            'description' => $description,
            'cubierta' => $cubierta,
            'shipcode' => $shipcode
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
}
fclose($file);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
