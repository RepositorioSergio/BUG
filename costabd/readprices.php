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
echo "COMECOU PRICES<br/>";
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

$filename = 'cruiseprice_individual.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$CostaCruisePriceCatalog = $inputDoc->getElementsByTagName("CostaCruisePriceCatalog");
if ($CostaCruisePriceCatalog->length > 0) {
    $CruisePriceCatalog = $CostaCruisePriceCatalog->item(0)->getElementsByTagName("CruisePriceCatalog");
    if ($CruisePriceCatalog->length > 0) {
        $Cruises = $CostaCruisePriceCatalog->item(0)->getElementsByTagName("Cruises");
        if ($Cruises->length > 0) {
            $Cruise = $Cruises->item(0)->getElementsByTagName("Cruise");
            if ($Cruise->length > 0) {
                for ($i=0; $i < $Cruise->length; $i++) { 
                    $Code = $Cruise->item($i)->getAttribute("Code");
                    $Name = $Cruise->item($i)->getAttribute("DisplayName");
                    
                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('cruiseprice');
                        $select->where(array(
                            'code' => $Code
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string) $data['Code'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'code' => $Code,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 1,
                                    'name' => $Name
                                );
                                $where['code = ?'] = $Code;
                                $update = $sql->update('cruiseprice', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('cruiseprice');
                                $insert->values(array(
                                    'code' => $Code,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name
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
                            $insert->into('cruiseprice');
                            $insert->values(array(
                                'code' => $Code,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name
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

                    $Categories = $Cruise->item($i)->getElementsByTagName("Categories");
                    if ($Categories->length > 0) {
                        $Category = $Categories->item(0)->getElementsByTagName("Category");
                        if ($Category->length > 0) {
                            for ($iAux=0; $iAux < $Category->length; $iAux++) { 
                                $CategoryCode = $Category->item($iAux)->getAttribute("Code");
                                $CategoryDescription = $Category->item($iAux)->getAttribute("Description");
                                $CategoryDiscount = $Category->item($iAux)->getAttribute("Discount");
                                $BestPrice = $Category->item($iAux)->getAttribute("BestPrice");
                                $ListPrice = $Category->item($iAux)->getAttribute("ListPrice");
                                $CurrencyCode = $Category->item($iAux)->getAttribute("CurrencyCode");
                                $MandatoryFlight = $Category->item($iAux)->getAttribute("MandatoryFlight");
                                $Availability = $Category->item($iAux)->getAttribute("Availability");
                                $HotelMandatory = $Category->item($iAux)->getAttribute("HotelMandatory");

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('cruiseprice_categories');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'categorycode' => $CategoryCode,
                                        'categorydescription' => $CategoryDescription,
                                        'categorydiscount' => $CategoryDiscount,
                                        'bestprice' => $BestPrice,
                                        'listprice' => $ListPrice,
                                        'currencycode' => $CurrencyCode,
                                        'mandatoryflight' => $MandatoryFlight,
                                        'availability' => $Availability,
                                        'hotelmandatory' => $HotelMandatory,
                                        'cruisepricecode' => $Code
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