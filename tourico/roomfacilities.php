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
            $hotelname = $data[1];
            $provider = $data[2];
            $childage = $data[3];
            $hotelroomtypeid = $data[4];
            $roomid = $data[5];
            $roomtypename = $data[6];
            $maxadults = $data[7];
            $maxchild = $data[8];
            $timestamp = $data[9];
            $productstatus = $data[10];

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('roomfacilities');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'hotelname' => $hotelname,
                    'provider' => $provider,
                    'childage' => $childage,
                    'hotelroomtypeid' => $hotelroomtypeid,
                    'roomid' => $roomid,
                    'roomtypename' => $roomtypename,
                    'maxadults' => $maxadults,
                    'maxchild' => $maxchild,
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

readCSV("PDS2_RoomFacilities_THF.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>