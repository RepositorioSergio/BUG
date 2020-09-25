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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.tourism.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "Activities.csv";
$object = fopen($filename, 'r');
$line = 0;

while ($data = fgetcsv($object, 0, ";")) {
    if ($line > 0) {
        $website = $data[0];
        $organisation = $data[1];
        $activities = $data[2];
        $area = $data[3];
        $telephone = $data[4];
        $email = $data[5];
        $op_hrs = $data[6];
        $latitude = $data[7];
        $longitude = $data[8];
        $activity_type = $data[9];
        $facebook = $data[10];
        $instagram = $data[11];
        $twitter = $data[12];
        $youtube = $data[13];
        $pricing = $data[14];
        $n1 = $data[15];
        $n2 = $data[16];
        $n3 = $data[17];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('activities');
            $insert->values(array(
                'datetime_updated' => time(),
                'website' => $website, 
                'organisation' => $organisation, 
                'activities' => $activities, 
                'area' => $area, 
                'telephone' => $telephone, 
                'email' => $email,
                'op_hrs' => $op_hrs,
                'latitude' => $latitude, 
                'longitude' => $longitude, 
                'activity_type' => $activity_type, 
                'facebook' => $facebook, 
                'instagram' => $instagram, 
                'twitter' => $twitter, 
                'youtube' => $youtube, 
                'pricing' => $pricing
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>