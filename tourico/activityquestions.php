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
            $activityid = $data[0];
            $additiontypeid = $data[1];
            $questiontext = $data[2];
            $answerdatatype = $data[3];
            $integerfrom = $data[4];
            $integerto = $data[5];
            $questiontype = $data[6];

            $additiontypeid = str_replace('"', '', $additiontypeid);
            $additiontypeid = mb_convert_encoding($additiontypeid, "UTF-8");
            $questiontext = str_replace('"', '', $questiontext);
            $questiontext = mb_convert_encoding($questiontext, "UTF-8");
            $answerdatatype = str_replace('"', '', $answerdatatype);
            $answerdatatype = mb_convert_encoding($answerdatatype, "UTF-8");
            $integerfrom = str_replace('"', '', $integerfrom);
            $integerfrom = mb_convert_encoding($integerfrom, "UTF-8");
            $integerto = str_replace('"', '', $integerto);
            $integerto = mb_convert_encoding($integerto, "UTF-8");
            $questiontype = str_replace('"', '', $questiontype);
            $questiontype = mb_convert_encoding($questiontype, "UTF-8");

            try {               
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('activityquestion');
                $insert->values(array(
                    'activityid' => $activityid,
                    'additiontypeid' => $additiontypeid,
                    'questiontext' => $questiontext,
                    'answerdatatype' => $answerdatatype,
                    'integerfrom' => $integerfrom,
                    'integerto' => $integerto,
                    'questiontype' => $questiontype
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

readCSV("ActivityQuestions.csv");

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>