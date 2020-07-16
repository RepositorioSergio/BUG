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
echo "COMECOU SHIPS<br/>";
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
// Start
$affiliate_id = 0;
$branch_filter = "";

$config = new \Zend\Config\Config(include '../config/autoload/global.costa.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'shipsandcategories.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$CostaShipCatalog = $inputDoc->getElementsByTagName("CostaShipCatalog");
if ($CostaShipCatalog->length > 0) {
    $Ships = $CostaShipCatalog->item(0)->getElementsByTagName("Ships");
    if ($Ships->length > 0) {
        $Ship = $Ships->item(0)->getElementsByTagName("Ship");
        if ($Ship->length > 0) {
            for ($i=0; $i < $Ship->length; $i++) { 
                $Code = $Ship->item($i)->getAttribute("CCN_COD_NAVE");
                $Name = $Ship->item($i)->getAttribute("DisplayName");
                $Description = $Ship->item($i)->getAttribute("Description");
                $ImgUrl = $Ship->item($i)->getAttribute("ImgUrl");
                $URL = $Ship->item($i)->getAttribute("Url");
                $Cabins = $Ship->item($i)->getElementsByTagName("Cabins");
                if ($Cabins->length > 0) {
                    $Cabins = $Cabins->item(0)->nodeValue;
                } else {
                    $Cabins = 0;
                }
                $Crew = $Ship->item($i)->getElementsByTagName("Crew");
                if ($Crew->length > 0) {
                    $Crew = $Crew->item(0)->nodeValue;
                } else {
                    $Crew = 0;
                }
                $Guests = $Ship->item($i)->getElementsByTagName("Guests");
                if ($Guests->length > 0) {
                    $Guests = $Guests->item(0)->nodeValue;
                } else {
                    $Guests = 0;
                }
                $Width = $Ship->item($i)->getElementsByTagName("Width");
                if ($Width->length > 0) {
                    $Width = $Width->item(0)->nodeValue;
                } else {
                    $Width = 0;
                }
                $Length = $Ship->item($i)->getElementsByTagName("Length");
                if ($Length->length > 0) {
                    $Length = $Length->item(0)->nodeValue;
                } else {
                    $Length = 0;
                }
                $Tonnage = $Ship->item($i)->getElementsByTagName("Tonnage");
                if ($Tonnage->length > 0) {
                    $Tonnage = $Tonnage->item(0)->nodeValue;
                } else {
                    $Tonnage = 0;
                }
                $MaxSpeed = $Ship->item($i)->getElementsByTagName("MaxSpeed");
                if ($MaxSpeed->length > 0) {
                    $MaxSpeed = $MaxSpeed->item(0)->nodeValue;
                } else {
                    $MaxSpeed = 0;
                }
                $YearOfLaunch = $Ship->item($i)->getElementsByTagName("YearOfLaunch");
                if ($YearOfLaunch->length > 0) {
                    $YearOfLaunch = $YearOfLaunch->item(0)->nodeValue;
                } else {
                    $YearOfLaunch = 0;
                }
                $MonthOfLaunch = $Ship->item($i)->getElementsByTagName("MonthOfLaunch");
                if ($MonthOfLaunch->length > 0) {
                    $MonthOfLaunch = $MonthOfLaunch->item(0)->nodeValue;
                } else {
                    $MonthOfLaunch = 0;
                }
                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('ships');
                    $select->where(array(
                        'id' => $Code
                    ));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $result->buffer();
                    $customers = array();
                    if ($result->valid()) {
                        $data = $result->current();
                        $id = (string) $data['id'];
                        if ($id != "") {
                            $sql = new Sql($db);
                            $data = array(
                                'id' => $Code,
                                'datetime_updated' => time(),
                                'name' => $Name,
                                'description' => $Description,
                                'imgurl' => $ImgUrl,
                                'url' => $URL,
                                'cabins' => $Cabins,
                                'crew' => $Crew,
                                'guests' => $Guests,
                                'width' => $Width,
                                'length' => $Length,
                                'tonnage' => $Tonnage,
                                'maxspeed' => $MaxSpeed,
                                'yearoflaunch' => $YearOfLaunch,
                                'monthoflaunch' => $MonthOfLaunch
                            );
                            $where['id = ?'] = $Code;
                            $update = $sql->update('ships', $data, $where);
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('ships');
                            $insert->values(array(
                                'id' => $Code,
                                'datetime_updated' => time(),
                                'name' => $Name,
                                'description' => $Description,
                                'imgurl' => $ImgUrl,
                                'url' => $URL,
                                'cabins' => $Cabins,
                                'crew' => $Crew,
                                'guests' => $Guests,
                                'width' => $Width,
                                'length' => $Length,
                                'tonnage' => $Tonnage,
                                'maxspeed' => $MaxSpeed,
                                'yearoflaunch' => $YearOfLaunch,
                                'monthoflaunch' => $MonthOfLaunch
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        }
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('ships');
                        $insert->values(array(
                            'id' => $Code,
                            'datetime_updated' => time(),
                            'name' => $Name,
                            'description' => $Description,
                            'imgurl' => $ImgUrl,
                            'url' => $URL,
                            'cabins' => $Cabins,
                            'crew' => $Crew,
                            'guests' => $Guests,
                            'width' => $Width,
                            'length' => $Length,
                            'tonnage' => $Tonnage,
                            'maxspeed' => $MaxSpeed,
                            'yearoflaunch' => $YearOfLaunch,
                            'monthoflaunch' => $MonthOfLaunch
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    }
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 1: " . $e;
                    echo $return;
                }

                $Categories = $Ship->item($i)->getElementsByTagName("Categories");
                if ($Categories->length > 0) {
                    $Category = $Categories->item(0)->getElementsByTagName("Category");
                    if ($Category->length > 0) {
                        for ($iAux=0; $iAux < $Category->length; $iAux++) { 
                            $CategoryCode = $Category->item($iAux)->getAttribute("CategoryCode");
                            $CategoryName = $Category->item($iAux)->getAttribute("CategoryName");
                            $CategoryDescription = $Category->item($iAux)->getAttribute("CategoryDescription");
                            $QuickTimeUrl = $Category->item($iAux)->getAttribute("QuickTimeUrl");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('ships_categories');
                                $insert->values(array(
                                    'datetime_updated' => time(),
                                    'categorycode' => $CategoryCode,
                                    'categoryname' => $CategoryName,
                                    'categorydescription' => $CategoryDescription,
                                    'quicktimeurl' => $QuickTimeUrl
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 2: " . $e;
                                echo $return;
                            }
                        }
                    }
                }

                $Decks = $Ship->item($i)->getElementsByTagName("Decks");
                if ($Decks->length > 0) {
                    $Deck = $Decks->item(0)->getElementsByTagName("Deck");
                    if ($Deck->length > 0) {
                        for ($iAux2=0; $iAux2 < $Deck->length; $iAux2++) { 
                            $DeckCode = $Deck->item($iAux2)->getAttribute("DeckCode");
                            $DeckDescription = $Deck->item($iAux2)->getAttribute("DeckDescription");

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('ships_decks');
                                $insert->values(array(
                                    'deckcode' => $DeckCode,
                                    'datetime_updated' => time(),
                                    'deckdescription' => $DeckDescription,
                                    'shipcode' => $Code
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 3: " . $e;
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