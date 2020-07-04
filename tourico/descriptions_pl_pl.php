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

$config = new \Zend\Config\Config(include '../config/autoload/global.tourico.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "THF_Descriptions_pl_PL3.csv";
$file = fopen($filename, 'r');
$line = 0;

while (!feof($file)) {
        $content = fgets($file);
        $content = mb_convert_encoding($content, "UTF-8");
        $array = explode('|', $content);
        list($hotelid, $shortdescription_en_us, $shortdescription_pl_pl, $longdescription_en_us, $longdescription_pl_pl, $timestamp, $productstatus) = $array;
    if ($line > 0) {
        $hotelid = str_replace('"', '', $hotelid);
        $shortdescription_en_us = str_replace('"', '', $shortdescription_en_us);
        $shortdescription_pl_pl = str_replace('"', '', $shortdescription_pl_pl);
        $longdescription_en_us = str_replace('"', '', $longdescription_en_us);
        $longdescription_pl_pl = str_replace('"', '', $longdescription_pl_pl);
        $productstatus = str_replace('"', '', $productstatus);

        if ($hotelid != "") {
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('descriptions_pl_pl');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'shortdescription_en_us' => $shortdescription_en_us, 
                    'shortdescription_pl_pl' => $shortdescription_pl_pl, 
                    'longdescription_en_us' => $longdescription_en_us,
                    'longdescription_pl_pl' => $longdescription_pl_pl, 
                    'timestamp' => $timestamp, 
                    'productstatus' => $productstatus 
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
    } 
    $line = $line + 1;
}
fclose($file);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>