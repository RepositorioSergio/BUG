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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$file = "vapProducts-es.xml";

$xml = new XMLReader();

if (!$xml->open($file))
{
    die("Failed to open 'vapProducts-es.xml'");
}

while($xml->read() && $xml->name !== 'Product'){continue;}
while ($xml->name === 'Product') {
    $value = new SimpleXMLElement($xml->readOuterXML());

    $rank = $value->Rank;
    $ProductType = $value->ProductType;
    $ProductCode = $value->ProductCode;
    $ProductName = $value->ProductName;
    $Introduction = $value->Introduction;
    $ProductText = $value->ProductText;
    $SpecialDescription	 = $value->SpecialDescription	;
    $Special = $value->Special;
    $Duration = $value->Duration;
    $Commences = $value->Commences;

    $ThumbnailURL = $value->ProductImage->ThumbnailURL;
    $ImageURL = $value->ProductImage->ImageURL;

    $DestinationID = $value->Destination->ID;
    $Continent = $value->Destination->Continent;
    $Country = $value->Destination->Country;
    $Region = $value->Destination->Region;
    $City = $value->Destination->City;
    $IATACode = $value->Destination->IATACode;

    $ProductCategory = $value->ProductCategories->ProductCategory;
    if ($ProductCategory != null) {
        foreach ($ProductCategory as $key => $valueProductCategory) {
            $Group = $valueProductCategory->Group;
            $Category = $valueProductCategory->Category;
            $Subcategory = $valueProductCategory->Subcategory;

            try {
                $db = new \Zend\Db\Adapter\Adapter($config);
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('products_productcategory');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'group' => $Group,
                    'category' => $Category,
                    'subcategory' => $Subcategory
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

    $ProductURL = $value->ProductURLs->ProductURL;

    $PriceAUD = $value->Pricing->PriceAUD;
    $PriceNZD = $value->Pricing->PriceNZD;
    $PriceEUR = $value->Pricing->PriceEUR;
    $PriceGBP = $value->Pricing->PriceGBP;
    $PriceUSD = $value->Pricing->PriceUSD;
    $PriceCAD = $value->Pricing->PriceCAD;
    $PriceCHF = $value->Pricing->PriceCHF;
    $PriceNOK = $value->Pricing->PriceNOK;
    $PriceJPY = $value->Pricing->PriceJPY;
    $PriceSEK = $value->Pricing->PriceSEK;
    $PriceHKD = $value->Pricing->PriceHKD;
    $PriceSGD = $value->Pricing->PriceSGD;
    $PriceZAR = $value->Pricing->PriceZAR;
    $PriceINR = $value->Pricing->PriceINR;
    $PriceTWD = $value->Pricing->PriceTWD;

    $BookingType = $value->BookingType;
    $VoucherOption = $value->VoucherOption;

    try {
        $db = new \Zend\Db\Adapter\Adapter($config);
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('products');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'rank' => $rank,
            'producttype' => $ProductType,
            'productcode' => $ProductCode,
            'productname' => $ProductName,
            'introduction' => $Introduction,
            'producttext' => $ProductText,
            'specialdescription' => $SpecialDescription,
            'special' => $Special,
            'duration' => $Duration,
            'commences' => $Commences,
            'thumbnailurl' => $ThumbnailURL,
            'imageurl' => $ImageURL,
            'destinationid' => $DestinationID,
            'continent' => $Continent,
            'country' => $Country,
            'region' => $Region,
            'city' => $City,
            'iatacode' => $IATACode,
            'producturl' => $ProductURL,
            'priceaud' => $PriceAUD,
            'pricenzd' => $PriceNZD,
            'priceeur' => $PriceEUR,
            'pricegbp' => $PriceGBP,
            'priceusd' => $PriceUSD,
            'pricecad' => $PriceCAD,
            'pricechf' => $PriceCHF,
            'pricenok' => $PriceNOK,
            'pricejpy' => $PriceJPY,
            'pricesek' => $PriceSEK,
            'pricehkd' => $PriceHKD,
            'pricesgd' => $PriceSGD,
            'pricezar' => $PriceZAR,
            'priceinr' => $PriceINR,
            'pricetwd' => $PriceTWD,
            'bookingtype' => $BookingType,
            'voucheroption' => $VoucherOption
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

    // move ponteiro  
    $xml->next('Product');
    // apaga este elemento
    unset($value);
}
$xml->close();

// EOF
$db->getDriver()
->getConnection()
->disconnect();
echo '<br />Done';
?>