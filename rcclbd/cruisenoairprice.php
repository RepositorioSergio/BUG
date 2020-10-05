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
echo "COMECOU PRICE<br/>";
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

$filename = "cruise_no_air_price.txt";
if (file_exists($filename)) {
    $file = fopen($filename, 'r');
    $line = 0;

    while (!feof($file)) {
        $content = fgets($file);
        $array = explode("|", $content);
        if ($line > 0) {
            list($packageid, $saildate, $farecode, $categorycode, $priceeffectivedate, $priceeffectivetime, $priceenddate, $priceendtime, $brandcode, $shipcode, $departureportcode, $subregioncode, $regioncode, $promotionclasstype, $promotioneligibilitytype, $needsqualifierflag, $listcriteria, $stateroomtypecode, $requiredoccupancyflag, $guaranteecategoryflag, $sailingonlyflag, $packagedescription, $farecodedescription, $priceflag, $guest1priceamount, $guest2priceamount, $guest3priceamount, $guest4priceamount, $childpriceamount, $infantpriceamount, $travelerpriceamount, $guest1_2gratuityamount, $guest3_4gratuityamount, $childgratuityamount, $guest1noncommissionable, $guest2noncommissionable, $guest3noncommissionable, $guest4noncommissionable, $childnoncommissionable, $infantnoncommissionable, $singlenoncommissionable, $taxesandfeesamount, $acessiblecabin, $releaseacessiblecabin, $bestvalue_single, $bestrate_single, $bestvalue_double, $bestrate_double, $bestvalue_triple, $bestrate_triple, $bestvalue_quad, $bestrate_quad, $offertype, $valueaddcurrency, $valueaddsingle, $valueaddguest1, $valueaddguest2, $valueaddguest3, $valueaddguest4, $valueaddchild, $valueaddinfant, $sequencenumber, $numbernights, $nonrefundablepromotions) = $array;

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('pricing');
                $insert->values(array(
                    'packageid' => $packageid,
                    'datetime_updated' => time(),
                    'saildate' => $saildate,
                    'farecode' => $farecode,
                    'categorycode' => $categorycode,
                    'priceeffectivedate' => $priceeffectivedate,
                    'priceeffectivetime' => $priceeffectivetime,
                    'priceenddate' => $priceenddate,
                    'priceendtime' => $priceendtime,
                    'brandcode' => $brandcode,
                    'shipcode' => $shipcode,
                    'departureportcode' => $departureportcode,
                    'subregioncode' => $subregioncode,
                    'regioncode' => $regioncode,
                    'promotionclasstype' => $promotionclasstype,
                    'promotioneligibilitytype' => $promotioneligibilitytype,
                    'needsqualifierflag' => $needsqualifierflag,
                    'listcriteria' => $listcriteria,
                    'stateroomtypecode' => $stateroomtypecode,
                    'requiredoccupancyflag' => $requiredoccupancyflag,
                    'guaranteecategoryflag' => $guaranteecategoryflag,
                    'sailingonlyflag' => $sailingonlyflag,
                    'packagedescription' => $packagedescription,
                    'farecodedescription' => $farecodedescription,
                    'priceflag' => $priceflag,
                    'guest1priceamount' => $guest1priceamount,
                    'guest2priceamount' => $guest2priceamount,
                    'guest3priceamount' => $guest3priceamount,
                    'guest4priceamount' => $guest4priceamount,
                    'childpriceamount' => $childpriceamount,
                    'infantpriceamount' => $infantpriceamount,
                    'travelerpriceamount' => $travelerpriceamount,
                    'guest1_2gratuityamount' => $guest1_2gratuityamount,
                    'guest3_4gratuityamount' => $guest3_4gratuityamount,
                    'childgratuityamount' => $childgratuityamount,
                    'guest1noncommissionable' => $guest1noncommissionable,
                    'guest2noncommissionable' => $guest2noncommissionable,
                    'guest3noncommissionable' => $guest3noncommissionable,
                    'guest4noncommissionable' => $guest4noncommissionable,
                    'childnoncommissionable' => $childnoncommissionable,
                    'infantnoncommissionable' => $infantnoncommissionable,
                    'singlenoncommissionable' => $singlenoncommissionable,
                    'taxesandfeesamount' => $taxesandfeesamount,
                    'acessiblecabin' => $acessiblecabin,
                    'releaseacessiblecabin' => $releaseacessiblecabin,
                    'bestvalue_single' => $bestvalue_single,
                    'bestrate_single' => $bestrate_single,
                    'bestvalue_double' => $bestvalue_double,
                    'bestrate_double' => $bestrate_double,
                    'bestvalue_triple' => $bestvalue_triple,
                    'bestrate_triple' => $bestrate_triple,
                    'bestvalue_quad' => $bestvalue_quad,
                    'bestrate_quad' => $bestrate_quad,
                    'offertype' => $offertype,
                    'valueaddcurrency' => $valueaddcurrency,
                    'valueaddsingle' => $valueaddsingle,
                    'valueaddguest1' => $valueaddguest1,
                    'valueaddguest2' => $valueaddguest2,
                    'valueaddguest3' => $valueaddguest3,
                    'valueaddguest4' => $valueaddguest4,
                    'valueaddchild' => $valueaddchild,
                    'valueaddinfant' => $valueaddinfant,
                    'sequencenumber' => $sequencenumber,
                    'numbernights' => $numbernights,
                    'nonrefundablepromotions' => $nonrefundablepromotions
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