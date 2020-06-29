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
            $imageurl = $data[2];
            $imagetypeid = $data[3];
            $imagetypename = $data[4];
            $isthumbnail = $data[5];
            $bigthumbnail = $data[6];
            $timestamp = $data[7];
            $productstatus = $data[8];

            $hotelname = str_replace('"', '', $hotelname);
            $hotelname = mb_convert_encoding($hotelname, "UTF-8");
            $imageurl = str_replace('"', '', $imageurl);
            $imageurl = mb_convert_encoding($imageurl, "UTF-8");
            $imagetypename = str_replace('"', '', $imagetypename);
            $imagetypename = mb_convert_encoding($imagetypename, "UTF-8");

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelimages');
                $insert->values(array(
                    'hotelid' => $hotelid,
                    'hotelname' => $hotelname,
                    'imageurl' => $imageurl,
                    'imagetypeid' => $imagetypeid,
                    'imagetypename' => $imagetypename,
                    'isthumbnail' => $isthumbnail,
                    'bigthumbnail' => $bigthumbnail,
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

readCSV("PDS2_HotelImages_THF.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>