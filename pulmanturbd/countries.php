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
echo "COMECOU COUNTRIES<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$line = 0;
$filename = 'countries.csv';

$file = fopen($filename, 'r');
while ($data = fgetcsv($file, 0, ";")) {

    if ($line >= 1) {
        $id = $data[1];
        $name = $data[0];
        $citizenshipcode = $data[2];

        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('countries');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'countryid' => $id,
            'countryname' => $name,
            'citizenshipcode' => $citizenshipcode
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
    $line = $line +1;
}
fclose($file);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>