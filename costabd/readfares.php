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
echo "COMECOU FARES<br/>";
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

$filename = 'fares.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$CostaFareExport = $inputDoc->getElementsByTagName("CostaFareExport");
if ($CostaFareExport->length > 0) {
    $FareCatalog = $CostaFareExport->item(0)->getElementsByTagName("FareCatalog");
    if ($FareCatalog->length > 0) {
        $Destination = $FareCatalog->item(0)->getElementsByTagName("Destination");
        if ($Destination->length > 0) {
            for ($i=0; $i < $Destination->length; $i++) { 
                $Code = $Destination->item($i)->getAttribute("Code");
                $DisplayName = $Destination->item($i)->getAttribute("DisplayName");
                $Cruise = $Destination->item($i)->getElementsByTagName("Cruise");
                if ($Cruise->length > 0) {
                    for ($iAux=0; $iAux < $Cruise->length; $iAux++) { 
                        $CruiseCode = $Cruise->item($iAux)->getAttribute("Code");
                        $Fares = $Cruise->item($iAux)->getElementsByTagName("Fares");
                        if ($Fares->length > 0) {
                            $Fare = $Fares->item(0)->getElementsByTagName("Fare");
                            if ($Fare->length > 0) {
                                for ($iAux2=0; $iAux2 < $Fare->length; $iAux2++) { 
                                    $FareCode = $Fare->item($iAux2)->getAttribute("Code");
                                    $FareDescription = $Fare->item($iAux2)->getAttribute("FareDescription");

                                    try {
                                        $sql = new Sql($db);
                                        $select = $sql->select();
                                        $select->from('fares');
                                        $select->where(array(
                                            'id' => $FareCode
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
                                                $select->table('fares');
                                                $select->where(array(
                                                    'id' => $idTmp
                                                ));
                                                $select->set(array(
                                                    'datetime_updated' => time(),
                                                    'datetime_updated' => 1,
                                                    'faredescription' => $FareDescription
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
                                                $insert->into('fares');
                                                $insert->values(array(
                                                    'id' => $FareCode,
                                                    'datetime_created' => time(),
                                                    'datetime_updated' => 0,
                                                    'faredescription' => $FareDescription
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
                                            $insert->into('fares');
                                            $insert->values(array(
                                                'id' => $FareCode,
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'faredescription' => $FareDescription
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