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
echo "COMECOU LOGIN<br/>";
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
   <pac:availabilityCalendar>
      <arg0>
       <beginDate>2020-03-22</beginDate>
         <!-- El campo "FareCode" es obligatorio-->
         <fareCode>NORMAL</fareCode>
         <ideses>WAW#40292#250941801616500</ideses>
         <!-- El campo "PackageCode" es obligatorio -->
         <packageCode>IT19T-80519</packageCode>
      </arg0>
   </pac:availabilityCalendar>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Content-length: ".strlen($raw)
));


$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}
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
$availabilityCalendarResponse = $Body->item(0)->getElementsByTagName("availabilityCalendarResponse");
if ($availabilityCalendarResponse->length > 0) {
    $return = $availabilityCalendarResponse->item(0)->getElementsByTagName("return");
    if ($return->length > 0) {
        $ideses = $return->item(0)->getElementsByTagName("ideses");
        if ($ideses->length > 0) {
            $ideses = $ideses->item(0)->nodeValue;
        } else {
            $ideses = "";
        }

        //packageCalendar
        $packageCalendar = $return->item(0)->getElementsByTagName("packageCalendar");
        if ($packageCalendar->length > 0) {
            $delayFirstService = $packageCalendar->item(0)->getElementsByTagName("delayFirstService");
            if ($delayFirstService->length > 0) {
                $delayFirstService = $delayFirstService->item(0)->nodeValue;
            } else {
                $delayFirstService = "";
            }
            $fareCode = $packageCalendar->item(0)->getElementsByTagName("fareCode");
            if ($fareCode->length > 0) {
                $fareCode = $fareCode->item(0)->nodeValue;
            } else {
                $fareCode = "";
            }
            $packageCode = $packageCalendar->item(0)->getElementsByTagName("packageCode");
            if ($packageCode->length > 0) {
                $packageCode = $packageCode->item(0)->nodeValue;
            } else {
                $packageCode = "";
            }
            $packageName = $packageCalendar->item(0)->getElementsByTagName("packageName");
            if ($packageName->length > 0) {
                $packageName = $packageName->item(0)->nodeValue;
            } else {
                $packageName = "";
            }
            $postNightsAllowed = $packageCalendar->item(0)->getElementsByTagName("postNightsAllowed");
            if ($postNightsAllowed->length > 0) {
                $postNightsAllowed = $postNightsAllowed->item(0)->nodeValue;
            } else {
                $postNightsAllowed = "";
            }
            $preNightsAllowed = $packageCalendar->item(0)->getElementsByTagName("preNightsAllowed");
            if ($preNightsAllowed->length > 0) {
                $preNightsAllowed = $preNightsAllowed->item(0)->nodeValue;
            } else {
                $preNightsAllowed = "";
            }
            $pricesDay = $packageCalendar->item(0)->getElementsByTagName("pricesDay");
            if ($pricesDay->length > 0) {
                for ($i=0; $i < $pricesDay->length; $i++) { 
                    $dayDate = $pricesDay->item($i)->getElementsByTagName("dayDate");
                    if ($dayDate->length > 0) {
                        $dayDate = $dayDate->item(0)->nodeValue;
                    } else {
                        $dayDate = "";
                    }
                    $pricesOption = $pricesDay->item($i)->getElementsByTagName("pricesOption");
                    if ($pricesOption->length > 0) {
                        $categoryCode = $pricesOption->item(0)->getElementsByTagName("categoryCode");
                        if ($categoryCode->length > 0) {
                            $categoryCode = $categoryCode->item(0)->nodeValue;
                        } else {
                            $categoryCode = "";
                        }
                        $categoryName = $pricesOption->item(0)->getElementsByTagName("categoryName");
                        if ($categoryName->length > 0) {
                            $categoryName = $categoryName->item(0)->nodeValue;
                        } else {
                            $categoryName = "";
                        }
                        $stayCode = $pricesOption->item(0)->getElementsByTagName("stayCode");
                        if ($stayCode->length > 0) {
                            $stayCode = $stayCode->item(0)->nodeValue;
                        } else {
                            $stayCode = "";
                        }
                        $stayName = $pricesOption->item(0)->getElementsByTagName("stayName");
                        if ($stayName->length > 0) {
                            $stayName = $stayName->item(0)->nodeValue;
                        } else {
                            $stayName = "";
                        }
                        $pricesOrigin = $pricesOption->item(0)->getElementsByTagName("pricesOrigin");
                        if ($pricesOrigin->length > 0) {
                            $currency = $pricesOrigin->item(0)->getElementsByTagName("currency");
                            if ($currency->length > 0) {
                                $currency = $currency->item(0)->nodeValue;
                            } else {
                                $currency = "";
                            }
                            $endDate = $pricesOrigin->item(0)->getElementsByTagName("endDate");
                            if ($endDate->length > 0) {
                                $endDate = $endDate->item(0)->nodeValue;
                            } else {
                                $endDate = "";
                            }
                            $price = $pricesOrigin->item(0)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                $price = $price->item(0)->nodeValue;
                            } else {
                                $price = "";
                            }
                        }
                        //specialSupplement
                        $specialSupplement = $pricesOption->item(0)->getElementsByTagName("specialSupplement");
                        if ($specialSupplement->length > 0) {
                            $additionalBedSupplement = $specialSupplement->item(0)->getElementsByTagName("additionalBedSupplement");
                            if ($additionalBedSupplement->length > 0) {
                                $codeABS = $additionalBedSupplement->item(0)->getElementsByTagName("code");
                                if ($codeABS->length > 0) {
                                    $codeABS = $codeABS->item(0)->nodeValue;
                                } else {
                                    $codeABS = "";
                                }
                                $percentageABS = $additionalBedSupplement->item(0)->getElementsByTagName("percentage");
                                if ($percentageABS->length > 0) {
                                    $percentageABS = $percentageABS->item(0)->nodeValue;
                                } else {
                                    $percentageABS = "";
                                }
                            }
                            $childDiscountSupplement = $specialSupplement->item(0)->getElementsByTagName("childDiscountSupplement");
                            if ($childDiscountSupplement->length > 0) {
                                $codeCDS = $childDiscountSupplement->item(0)->getElementsByTagName("code");
                                if ($codeCDS->length > 0) {
                                    $codeCDS = $codeCDS->item(0)->nodeValue;
                                } else {
                                    $codeCDS = "";
                                }
                                $percentageCDS = $childDiscountSupplement->item(0)->getElementsByTagName("percentage");
                                if ($percentageCDS->length > 0) {
                                    $percentageCDS = $percentageCDS->item(0)->nodeValue;
                                } else {
                                    $percentageCDS = "";
                                }
                            }
                            $individualSupplement = $specialSupplement->item(0)->getElementsByTagName("individualSupplement");
                            if ($individualSupplement->length > 0) {
                                $codeIS = $childDiscountSupplement->item(0)->getElementsByTagName("code");
                                if ($codeIS->length > 0) {
                                    $codeIS = $codeIS->item(0)->nodeValue;
                                } else {
                                    $codeIS = "";
                                }
                                $priceIS = $childDiscountSupplement->item(0)->getElementsByTagName("price");
                                if ($priceIS->length > 0) {
                                    $priceIS = $priceIS->item(0)->nodeValue;
                                } else {
                                    $priceIS = "";
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