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
echo "COMECOU READ XML<br/>";
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

$filename = 'codeLookup.xml';
$response = file_get_contents($filename);
if (file_exists($filename)) {
    echo "O arquivo $filename existe<br/>";
} else {
    echo "O arquivo $filename não existe<br/>";
}
$inputDoc = new DOMDocument();
$inputDoc->preserveWhiteSpace = true;
$inputDoc->loadXML($response);
$lookupList = $inputDoc->getElementsByTagName('lookupList');
if ($lookupList->length > 0) {
    $lookup = $lookupList->item(0)->getElementsByTagName("lookup");
    if ($lookup->length > 0) {
        for ($i=0; $i < $lookup->length; $i++) { 
            $id = $lookup->item($i)->getAttribute("id");
            if ($id === "Country_Codes") {
                $mapList = $lookup->item($i)->getElementsByTagName("mapList");
                if ($mapList->length > 0) {
                    $map = $mapList->item(0)->getElementsByTagName("map");
                    if ($map->length > 0) {
                        for ($j=0; $j < $map->length; $j++) { 
                            $rapiCode = $map->item($j)->getAttribute("rapiCode");
                            $otaCode = $map->item($j)->getAttribute("otaCode");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('rcc_countries');
                                $insert->values(array(
                                    'id' => $rapiCode,
                                   'datetime_updated' => time(),
                                   'otacode' => $otaCode
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                   ->getConnection()
                                   ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 1: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
