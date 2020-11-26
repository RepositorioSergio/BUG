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

$config=new \Zend\Config\Config(include '../config/autoload/global.viator.php' );
$config=[ 'driver'=> $config->db->driver,
'database' => $config->db->database,
'username' => $config->db->username,
'password' => $config->db->password,
'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = "vapDests-es.xml";
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Destinations = $inputDoc->getElementsByTagName("Destinations");
$Destination = $Destinations->item(0)->getElementsByTagName("Destination");
if ($Destination->length > 0) {
    for ($i=0; $i < $Destination->length; $i++) { 
        $DestinationID = $Destination->item($i)->getElementsByTagName("DestinationID");
        if ($DestinationID->length > 0) {
            $DestinationID = $DestinationID->item(0)->nodeValue;
        } else {
            $DestinationID = "";
        }
        $DestinationName = $Destination->item($i)->getElementsByTagName("DestinationName");
        if ($DestinationName->length > 0) {
            $DestinationName = $DestinationName->item(0)->nodeValue;
        } else {
            $DestinationName = "";
        }
        $Type = $Destination->item($i)->getElementsByTagName("Type");
        if ($Type->length > 0) {
            $Type = $Type->item(0)->nodeValue;
        } else {
            $Type = "";
        }
        $ParentID = $Destination->item($i)->getElementsByTagName("ParentID");
        if ($ParentID->length > 0) {
            $ParentID = $ParentID->item(0)->nodeValue;
        } else {
            $ParentID = "";
        }
        $ParentName = $Destination->item($i)->getElementsByTagName("ParentName");
        if ($ParentName->length > 0) {
            $ParentName = $ParentName->item(0)->nodeValue;
        } else {
            $ParentName = "";
        }
        $DestinationURLs = $Destination->item($i)->getElementsByTagName("DestinationURLs");
        if ($DestinationURLs->length > 0) {
            $DestinationURL = $DestinationURLs->item(0)->getElementsByTagName("DestinationURL");
            if ($DestinationURL->length > 0) {
                $DestinationURL = $DestinationURL->item(0)->nodeValue;
            } else {
                $DestinationURL = "";
            }
            $ThingsToDoURL = $DestinationURLs->item(0)->getElementsByTagName("ThingsToDoURL");
            if ($ThingsToDoURL->length > 0) {
                $ThingsToDoURL = $ThingsToDoURL->item(0)->nodeValue;
            } else {
                $ThingsToDoURL = "";
            }
        }

        try {
            $db = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('destinations');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'destinationid' => $DestinationID,
                'destinationname' => $DestinationName,
                'type' => $Type,
                'parentid' => $ParentID,
                'parentname' => $ParentName,
                'destinationurl' => $DestinationURL,
                'thingstodourl' => $ThingsToDoURL
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

        $DestinationGroups = $Destination->item($i)->getElementsByTagName("DestinationGroups");
        if ($DestinationGroups->length > 0) {
            $Group = $DestinationGroups->item(0)->getElementsByTagName("Group");
            if ($Group->length > 0) {
                for ($iAux=0; $iAux < $Group->length; $iAux++) { 
                    $ID = $Group->item($iAux)->getElementsByTagName("ID");
                    if ($ID->length > 0) {
                        $ID = $ID->item(0)->nodeValue;
                    } else {
                        $ID = "";
                    }
                    $Name = $Group->item($iAux)->getElementsByTagName("Name");
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $URL = $Group->item($iAux)->getElementsByTagName("URL");
                    if ($URL->length > 0) {
                        $URL = $URL->item(0)->nodeValue;
                    } else {
                        $URL = "";
                    }

                    try {
                        $db = new \Zend\Db\Adapter\Adapter($config);
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('destinations_destinationgroups');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'groupid' => $ID,
                            'name' => $Name,
                            'url' => $URL
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
        $HotsellerProducts = $Destination->item($i)->getElementsByTagName("HotsellerProducts");
        if ($HotsellerProducts->length > 0) {
            $Product = $HotsellerProducts->item(0)->getElementsByTagName("Product");
            if ($Product->length > 0) {
                for ($iAux2=0; $iAux2 < $Product->length; $iAux2++) { 
                    $Rank = $Product->item($iAux2)->getElementsByTagName("Rank");
                    if ($Rank->length > 0) {
                        $Rank = $Rank->item(0)->nodeValue;
                    } else {
                        $Rank = "";
                    }
                    $ProductType = $Product->item($iAux2)->getElementsByTagName("ProductType");
                    if ($ProductType->length > 0) {
                        $ProductType = $ProductType->item(0)->nodeValue;
                    } else {
                        $ProductType = "";
                    }
                    $ProductCode = $Product->item($iAux2)->getElementsByTagName("ProductCode");
                    if ($ProductCode->length > 0) {
                        $ProductCode = $ProductCode->item(0)->nodeValue;
                    } else {
                        $ProductCode = "";
                    }
                    $ProductName = $Product->item($iAux2)->getElementsByTagName("ProductName");
                    if ($ProductName->length > 0) {
                        $ProductName = $ProductName->item(0)->nodeValue;
                    } else {
                        $ProductName = "";
                    }
                    $LanguageCode = $Product->item($iAux2)->getElementsByTagName("LanguageCode");
                    if ($LanguageCode->length > 0) {
                        $LanguageCode = $LanguageCode->item(0)->nodeValue;
                    } else {
                        $LanguageCode = "";
                    }
                    $TranslationLevel = $Product->item($iAux2)->getElementsByTagName("TranslationLevel");
                    if ($TranslationLevel->length > 0) {
                        $TranslationLevel = $TranslationLevel->item(0)->nodeValue;
                    } else {
                        $TranslationLevel = "";
                    }
                    $Introduction = $Product->item($iAux2)->getElementsByTagName("Introduction");
                    if ($Introduction->length > 0) {
                        $Introduction = $Introduction->item(0)->nodeValue;
                    } else {
                        $Introduction = "";
                    }
                    $ProductImageURL = $Product->item($iAux2)->getElementsByTagName("ProductImageURL");
                    if ($ProductImageURL->length > 0) {
                        $ProductImageURL = $ProductImageURL->item(0)->nodeValue;
                    } else {
                        $ProductImageURL = "";
                    }
                    $ProductURL = $Product->item($iAux2)->getElementsByTagName("ProductURL");
                    if ($ProductURL->length > 0) {
                        $ProductURL = $ProductURL->item(0)->nodeValue;
                    } else {
                        $ProductURL = "";
                    }

                    try {
                        $db = new \Zend\Db\Adapter\Adapter($config);
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('destinations_products');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'rank' => $Rank,
                            'producttype' => $ProductType,
                            'productcode' => $ProductCode,
                            'productname' => $ProductName,
                            'languagecode' => $LanguageCode,
                            'translationlevel' => $TranslationLevel,
                            'introduction' => $Introduction,
                            'productimageurl' => $ProductImageURL,
                            'producturl' => $ProductURL
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

// EOF
$db->getDriver()
->getConnection()
->disconnect();
echo '<br />Done';
?>