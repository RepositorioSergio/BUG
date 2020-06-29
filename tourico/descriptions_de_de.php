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
    
function readCSV(string $filename){
    $config = new \Zend\Config\Config(include '../config/autoload/global.tourico.php');
    $config = [
        'driver' => $config->db->driver,
        'database' => $config->db->database,
        'username' => $config->db->username,
        'password' => $config->db->password,
        'hostname' => $config->db->hostname
    ];
    $db = new \Zend\Db\Adapter\Adapter($config);

    $object = fopen($filename, 'r');
    $line = 0;

    while ($data = fgetcsv($object, 0, "|")) {
        if ($line > 0) {
            $hotelid = $data[0];
            $shortdescription_en_us = $data[1];
            $shortdescription_de_de = $data[2];
            $longdescription_en_us = $data[3];
            $longdescription_de_de = $data[4];
            $timestamp = $data[5];
            $productstatus = $data[6];

            $shortdescription_en_us = str_replace('"', '', $shortdescription_en_us);
            $shortdescription_en_us = mb_convert_encoding($shortdescription_en_us, "UTF-8");
            $shortdescription_de_de = str_replace('"', '', $shortdescription_de_de);
            $shortdescription_de_de = mb_convert_encoding($shortdescription_de_de, "UTF-8");
            $longdescription_en_us = str_replace('"', '', $longdescription_en_us);
            $longdescription_en_us = mb_convert_encoding($longdescription_en_us, "UTF-8");
            $longdescription_de_de = str_replace('"', '', $longdescription_de_de);
            $longdescription_de_de = mb_convert_encoding($longdescription_de_de, "UTF-8");
            $productstatus = str_replace('"', '', $productstatus);

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('descriptions_de_de');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'shortdescription_en_us' => $shortdescription_en_us, 
                    'shortdescription_de_de' => $shortdescription_de_de, 
                    'longdescription_en_us' => $longdescription_en_us,
                    'longdescription_de_de' => $longdescription_de_de, 
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
        $line = $line + 1;
    }
    fclose($filename);
}

readCSV("HotelDescriptions/THF_Descriptions_de_DE.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>