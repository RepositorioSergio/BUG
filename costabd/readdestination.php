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
echo "COMECOU DESTINATIONS<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'destinations.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$CostaCatalog = $inputDoc->getElementsByTagName("CostaCatalog");
$CostaItineraryCatalog = $CostaCatalog->item(0)->getElementsByTagName("CostaItineraryCatalog");
if ($CostaItineraryCatalog->length > 0) {
    $Itineraries = $CostaItineraryCatalog->item(0)->getElementsByTagName("Itineraries");
    if ($Itineraries->length > 0) {
        $Itinerary = $Itineraries->item(0)->getElementsByTagName("Itinerary");
        if ($Itinerary->length > 0) {
            for ($i=0; $i < $Itinerary->length; $i++) { 
                $Code = $Itinerary->item($i)->getAttribute("Code");
                $Name = $Itinerary->item($i)->getAttribute("Name");
                $Url = $Itinerary->item($i)->getAttribute("Url");

                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('itineraries');
                    $select->where(array(
                        'id' => $Code
                    ));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    try {
                        $result = $statement->execute();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error: " . $e;
                        echo $return;
                        die();
                    }
                    $result->buffer();
                    $customers = array();
                    if ($result->valid()) {
                        $data = $result->current();
                        $idTmp = (string) $data['id'];
                        if ($idTmp > 0) {
                            $sql = new Sql($db);
                            $select = $sql->update();
                            $select->table('itineraries');
                            $select->where(array(
                                'id' => $idTmp
                            ));
                            $select->set(array(
                                'datetime_updated' => time(),
                                'datetime_updated' => 1,
                                'name' => $Name,
                                'imageurl' => $Url
                            ));
                            $statement = $sql->prepareStatementForSqlObject($select);
                            try {
                                $results = $statement->execute();
                            } catch (\Exception $e) {
                                $console->writeLine('');
                                $console->writeLine($e);
                                $console->writeLine('');
                                die();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('itineraries');
                            $insert->values(array(
                                'id' => $Code,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'imageurl' => $Url
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
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('itineraries');
                        $insert->values(array(
                            'id' => $Code,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'name' => $Name,
                            'imageurl' => $Url
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
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 1: " . $e;
                    echo $return;
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