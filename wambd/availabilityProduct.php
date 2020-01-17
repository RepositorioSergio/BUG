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
echo "COMECOU AVAILABILITY PRODUCT<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$url = 'https://xtest.wamos.com/packageTravel';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pac="http://packages.servicePackage.dome.com/">
<soapenv:Header/>
<soapenv:Body>
   <pac:availabilityProduct>
      <arg0>
         <login>
            <clientId>WWCTM001</clientId>
            <password>CT8954TO</password>
            <system>WAW</system>
            <user>XMLCON</user>
         </login>
       <!--ideses></ideses-->
         <!--beginDate>2019-12-11</beginDate-->
         <!--campaign>4</campaign-->
         <!--destination></destination-->
         <!--monthSearch>2019-12</monthSearch-->
         <!--origin>MAD</origin-->
         <productCode></productCode>
         <!--publishedCode></publishedCode-->
         <searchText>P.68</searchText>
      </arg0>
   </pac:availabilityProduct>
</soapenv:Body>
</soapenv:Envelope>';

$headers = array(
	"Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Content-length: ".strlen($raw)
); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_ENCODING , "gzip");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$availabilityProductResponse = $Body->item(0)->getElementsByTagName("availabilityProductResponse");
if ($availabilityProductResponse->length > 0) {
    $return = $availabilityProductResponse->item(0)->getElementsByTagName("return");
    if ($return->length > 0) {
        $ideses = $return->item(0)->getElementsByTagName("ideses");
        if ($ideses->length > 0) {
            $ideses = $ideses->item(0)->nodeValue;
        } else {
            $ideses = "";
        }
        //filterList
        $filterList = $return->item(0)->getElementsByTagName("filterList");
        if ($filterList->length > 0) {
            for ($i=0; $i < $filterList->length; $i++) { 
                $filterCode = $filterList->item($i)->getElementsByTagName("filterCode");
                if ($filterCode->length > 0) {
                    $filterCode = $filterCode->item(0)->nodeValue;
                } else {
                    $filterCode = "";
                }
                $filterElement = $filterList->item($i)->getElementsByTagName("filterElement");
                if ($filterElement->length > 0) {
                    $filterCodeFE = $filterElement->item(0)->getElementsByTagName("filterCode");
                    if ($filterCodeFE->length > 0) {
                        $filterCodeFE = $filterCodeFE->item(0)->nodeValue;
                    } else {
                        $filterCodeFE = "";
                    }
                    $filterNameFE = $filterElement->item(0)->getElementsByTagName("filterName");
                    if ($filterNameFE->length > 0) {
                        $filterNameFE = $filterNameFE->item(0)->nodeValue;
                    } else {
                        $filterNameFE = "";
                    }
                    $filterResultsFE = $filterElement->item(0)->getElementsByTagName("filterResults");
                    if ($filterResultsFE->length > 0) {
                        $filterResultsFE = $filterResultsFE->item(0)->nodeValue;
                    } else {
                        $filterResultsFE = "";
                    }
                }
            }
        }

        //availableProduct
        $availableProduct = $return->item(0)->getElementsByTagName("availableProduct");
        if ($availableProduct->length > 0) {
            $beginDate = $availableProduct->item(0)->getElementsByTagName("beginDate");
            if ($beginDate->length > 0) {
                $beginDate = $beginDate->item(0)->nodeValue;
            } else {
                $beginDate = "";
            }
            $campaign = $availableProduct->item(0)->getElementsByTagName("campaign");
            if ($campaign->length > 0) {
                $campaign = $campaign->item(0)->nodeValue;
            } else {
                $campaign = "";
            }
            $delayFirstService = $availableProduct->item(0)->getElementsByTagName("delayFirstService");
            if ($delayFirstService->length > 0) {
                $delayFirstService = $delayFirstService->item(0)->nodeValue;
            } else {
                $delayFirstService = "";
            }
            $description = $availableProduct->item(0)->getElementsByTagName("description");
            if ($description->length > 0) {
                $description = $description->item(0)->nodeValue;
            } else {
                $description = "";
            }
            $endDate = $availableProduct->item(0)->getElementsByTagName("endDate");
            if ($endDate->length > 0) {
                $endDate = $endDate->item(0)->nodeValue;
            } else {
                $endDate = "";
            }
            $fareCode = $availableProduct->item(0)->getElementsByTagName("fareCode");
            if ($fareCode->length > 0) {
                $fareCode = $fareCode->item(0)->nodeValue;
            } else {
                $fareCode = "";
            }
            $itineraryCode = $availableProduct->item(0)->getElementsByTagName("itineraryCode");
            if ($itineraryCode->length > 0) {
                $itineraryCode = $itineraryCode->item(0)->nodeValue;
            } else {
                $itineraryCode = "";
            }
            $packageCode = $availableProduct->item(0)->getElementsByTagName("packageCode");
            if ($packageCode->length > 0) {
                $packageCode = $packageCode->item(0)->nodeValue;
            } else {
                $packageCode = "";
            }
            $packageName = $availableProduct->item(0)->getElementsByTagName("packageName");
            if ($packageName->length > 0) {
                $packageName = $packageName->item(0)->nodeValue;
            } else {
                $packageName = "";
            }
            $basePrice = $availableProduct->item(0)->getElementsByTagName("basePrice");
            if ($basePrice->length > 0) {
                $beginDateBP = $availableProduct->item(0)->getElementsByTagName("beginDate");
                if ($beginDateBP->length > 0) {
                    $beginDateBP = $beginDateBP->item(0)->nodeValue;
                } else {
                    $beginDateBP = "";
                }
                $category = $availableProduct->item(0)->getElementsByTagName("category");
                if ($category->length > 0) {
                    $category = $category->item(0)->nodeValue;
                } else {
                    $category = "";
                }
                $categoryName = $availableProduct->item(0)->getElementsByTagName("categoryName");
                if ($categoryName->length > 0) {
                    $categoryName = $categoryName->item(0)->nodeValue;
                } else {
                    $categoryName = "";
                }
                $currency = $availableProduct->item(0)->getElementsByTagName("currency");
                if ($currency->length > 0) {
                    $currency = $currency->item(0)->nodeValue;
                } else {
                    $currency = "";
                }
                $endDate = $availableProduct->item(0)->getElementsByTagName("endDate");
                if ($endDate->length > 0) {
                    $endDate = $endDate->item(0)->nodeValue;
                } else {
                    $endDate = "";
                }
                $price = $availableProduct->item(0)->getElementsByTagName("price");
                if ($price->length > 0) {
                    $price = $price->item(0)->nodeValue;
                } else {
                    $price = "";
                }
                $stayCode = $availableProduct->item(0)->getElementsByTagName("stayCode");
                if ($stayCode->length > 0) {
                    $stayCode = $stayCode->item(0)->nodeValue;
                } else {
                    $stayCode = "";
                }
                $stayName = $availableProduct->item(0)->getElementsByTagName("stayName");
                if ($stayName->length > 0) {
                    $stayName = $stayName->item(0)->nodeValue;
                } else {
                    $stayName = "";
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