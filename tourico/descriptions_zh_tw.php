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

$filename = "HotelDescriptions/THF_Descriptions_zh_TW.csv";
$file = fopen($filename, 'r');
$line = 0;

while (!feof($file)) {
        $content = fgets($file);
        $content = mb_convert_encoding($content, "BIG5", "UTF-8");
        $array = explode('"', $content);
        list($hotelid, $b1, $shortdescription_en_us, $b2, $shortdescription_zh_tw, $b3, $longdescription_en_us, $longdescription_zh_tw, $timestamp, $productstatus) = $array;
        echo "longdescription_zh_tw: ". $array[7] . "<br/>";
    /* if ($line > 0) {
        $hotelid = str_replace('"', '', $hotelid);
        $shortdescription_en_us = str_replace('"', '', $shortdescription_en_us);
        $shortdescription_en_us = mb_convert_encoding($shortdescription_en_us, "UTF-8");
        $shortdescription_zh_tw = str_replace('"', '', $shortdescription_zh_tw);
        $shortdescription_zh_tw = mb_convert_encoding($shortdescription_zh_tw, "UTF-8");
        $longdescription_en_us = str_replace('"', '', $longdescription_en_us);
        $longdescription_en_us = mb_convert_encoding($longdescription_en_us, "UTF-8");
        $longdescription_zh_tw = str_replace('"', '', $longdescription_zh_tw);
        $longdescription_zh_tw = mb_convert_encoding($longdescription_zh_tw, "UTF-8");
        $productstatus = str_replace('"', '', $productstatus);
        $productstatus = mb_convert_encoding($productstatus, "UTF-8");

        if ($hotelid != "") {
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('descriptions_zh_tw');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'shortdescription_en_us' => $shortdescription_en_us, 
                    'shortdescription_zh_tw' => $shortdescription_zh_tw, 
                    'longdescription_en_us' => $longdescription_en_us,
                    'longdescription_zh_tw' => $longdescription_zh_tw, 
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
    } */
    $line = $line + 1;
}
fclose($file);

/* function readCSV(string $filename){
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
    $array = array();
    while ($data = fgetcsv($object, 0, "|")) {
        if ($line > 0) {
            $num = count($data);
            echo "num: ". $num . "<br/>";
            if ($num == 1) {
                $array = explode("|", $data);
                var_dump($data);
                $hotelid = $array[0];
                echo "hotelid: ". $hotelid . "<br/>";
                $shortdescription_en_us = $array[1];
                $shortdescription_zh_tw = $array[2];
                $longdescription_en_us = $array[3];
                $longdescription_zh_tw = $array[4];
                $timestamp = $array[5];
                $productstatus = $array[6];
            }
            if ($num == 5) {
                $id = $data[0];
                $shortdescription_en_us2 = $data[1];
                $shortdescription_zh_tw2 = $data[2];
                $longdescription_en_us2 = $data[4];
                $longdescription_zh_tw2 = $data[5];
            } else {
                if ($num == 8) {
                    $hotelid = $data[0];
                    $shortdescription_en_us = $data[1];
                    $shortdescription_zh_tw = $data[2] . " " . $data[3];
                    $longdescription_en_us = $data[4];
                    $longdescription_zh_tw = $data[5];
                    $timestamp = $data[6];
                    $productstatus = $data[7];
                } elseif ($num == 9) {
                    $hotelid = $data[0];
                    $shortdescription_en_us = $data[1];
                    $shortdescription_zh_tw = $data[2];
                    $longdescription_en_us = $data[3];
                    $longdescription_zh_tw = $data[4] . " " . $data[5] . " " . $data[6];
                    $timestamp = $data[7];
                    $productstatus = $data[8];
                } elseif ($num == 3) {
                    $hotelid = $id;
                    $shortdescription_en_us = $shortdescription_en_us2;
                    $shortdescription_zh_tw = $shortdescription_zh_tw2;
                    $longdescription_en_us = $longdescription_en_us2;
                    $longdescription_zh_tw = $longdescription_zh_tw2 . " " . $data[0];
                    $timestamp = $data[1];
                    $productstatus = $data[2];
                } else {
                    $hotelid = $data[0];
                    $shortdescription_en_us = $data[1];
                    $shortdescription_zh_tw = $data[2];
                    $longdescription_en_us = $data[3];
                    $longdescription_zh_tw = $data[4];
                    $timestamp = $data[5];
                    $productstatus = $data[6];
                }
                
                $shortdescription_en_us = str_replace('"', '', $shortdescription_en_us);
                $shortdescription_en_us = mb_convert_encoding($shortdescription_en_us, "UTF-8");
                $shortdescription_zh_tw = str_replace('"', '', $shortdescription_zh_tw);
                $shortdescription_zh_tw = mb_convert_encoding($shortdescription_zh_tw, "UTF-8");
                $longdescription_en_us = str_replace('"', '', $longdescription_en_us);
                $longdescription_en_us = mb_convert_encoding($longdescription_en_us, "UTF-8");
                $longdescription_zh_tw = str_replace('"', '', $longdescription_zh_tw);
                $longdescription_zh_tw = mb_convert_encoding($longdescription_zh_tw, "UTF-8");
                $productstatus = str_replace('"', '', $productstatus);
                $productstatus = mb_convert_encoding($productstatus, "UTF-8");
    
                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('descriptions_zh_tw');
                    $insert->values(array(
                        'hotelid' => $hotelid,
                        'shortdescription_en_us' => $shortdescription_en_us, 
                        'shortdescription_zh_tw' => $shortdescription_zh_tw, 
                        'longdescription_en_us' => $longdescription_en_us,
                        'longdescription_zh_tw' => $longdescription_zh_tw, 
                        'timestamp' => $timestamp, 
                        'productstatus' => $productstatus 
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo "hotelid: ". $hotelid . "<br/>";
                    echo "data1: ". $data[1] . "<br/>";
                    echo "data2: ". $data[2] . "<br/>";
                    echo "data3: ". $data[3] . "<br/>";
                    echo "data4: ". $data[4] . "<br/>";
                    echo "data5: ". $data[5] . "<br/>";
                    echo "data6: ". $data[6] . "<br/>";
                    echo $return;
                    echo "ERRO: ". $e;
                    echo $return;
                 }
            }
        }
        $line = $line + 1;
    }
    fclose($filename);
}

readCSV("THF_Descriptions_zh_TW2.csv"); */

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>