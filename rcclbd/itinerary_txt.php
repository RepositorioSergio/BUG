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
echo "COMECOU ITINERARY<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "ITINERARY.txt";
if (file_exists($filename)) {
    $file = fopen($filename, 'r');
    $line = 0;

    while (!feof($file)) {
        $content = fgets($file);
        $array = explode("|", $content);
        if ($line > 0) {
            list($packageid, $saildate, $activitydate, $shipcode, $subregioncode, $regioncode, $departureportcode, $itinerarycode, $itineraryeffectivedate, $sailingonlyflag, $vacationportion, $locationcode, $locationname, $activity, $arrivaltime, $departuretime, $brandcode, $numberdays) = $array;
            $locationname = utf8_encode($locationname);
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('itineraries');
                $insert->values(array(
                    'packageid' => $packageid,
                    'datetime_updated' => time(),
                    'saildate' => $saildate,
                    'activitydate' => $activitydate,
                    'shipcode' => $shipcode,
                    'subregioncode' => $subregioncode,
                    'regioncode' => $regioncode,
                    'departureportcode' => $departureportcode,
                    'itinerarycode' => $itinerarycode,
                    'itineraryeffectivedate' => $itineraryeffectivedate,
                    'sailingonlyflag' => $sailingonlyflag,
                    'vacationportion' => $vacationportion,
                    'locationcode' => $locationcode,
                    'locationname' => $locationname,
                    'activity' => $activity,
                    'arrivaltime' => $arrivaltime,
                    'departuretime' => $departuretime,
                    'brandcode' => $brandcode,
                    'numberdays' => $numberdays
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
    fclose($file);
} else {
    echo "File does not exist.";
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>