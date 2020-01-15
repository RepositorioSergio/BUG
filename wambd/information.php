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
echo "COMECOU INFORMATION<br/>";
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

$url = 'https://xtest.wamos.com/packageTravelXml';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pac="http://packages.servicePackage.dome.com/">
<soapenv:Header/>
<soapenv:Body>
   <pac:information>
      <arg0>
         <ideses></ideses>
         <productCode>EU13992</productCode>
      </arg0>
   </pac:information>
</soapenv:Body>
</soapenv:Envelope>';

/* $client = new Client();
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
} */
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
$informationResponse = $Body->item(0)->getElementsByTagName("informationResponse");
if ($informationResponse->length > 0) {
    $return = $informationResponse->item(0)->getElementsByTagName("return");
    if ($return->length > 0) {
        //infoPackage
        $infoPackage = $return->item(0)->getElementsByTagName("infoPackage");
        if ($infoPackage->length > 0) {
            $cancellationPolicy = $infoPackage->item(0)->getElementsByTagName("cancellationPolicy");
            if ($cancellationPolicy->length > 0) {
                $cancellationPolicy = $cancellationPolicy->item(0)->nodeValue;
            } else {
                $cancellationPolicy = "";
            }
            $destination = $infoPackage->item(0)->getElementsByTagName("destination");
            if ($destination->length > 0) {
                for ($i=0; $i < $destination->length; $i++) { 
                    $destinationCode = $destination->item($i)->getElementsByTagName("destinationCode");
                    if ($destinationCode->length > 0) {
                        $destinationCode = $destinationCode->item(0)->nodeValue;
                    } else {
                        $destinationCode = "";
                    }
                    $destinationName = $destination->item($i)->getElementsByTagName("destinationName");
                    if ($destinationName->length > 0) {
                        $destinationName = $destinationName->item(0)->nodeValue;
                    } else {
                        $destinationName = "";
                    }
                }
            }
            //generalInformation
            $generalInformation = $infoPackage->item(0)->getElementsByTagName("generalInformation");
            if ($generalInformation->length > 0) {
                $packageImage = $generalInformation->item(0)->getElementsByTagName("packageImage");
                if ($packageImage->length > 0) {
                    $packageImage = $packageImage->item(0)->nodeValue;
                } else {
                    $packageImage = "";
                }
                $packageLogo = $generalInformation->item(0)->getElementsByTagName("packageLogo");
                if ($packageLogo->length > 0) {
                    $packageLogo = $packageLogo->item(0)->nodeValue;
                } else {
                    $packageLogo = "";
                }
                $packageRemark = $generalInformation->item(0)->getElementsByTagName("packageRemark");
                if ($packageRemark->length > 0) {
                    $packageRemark = $packageRemark->item(0)->nodeValue;
                } else {
                    $packageRemark = "";
                }
                $shortDescription = $generalInformation->item(0)->getElementsByTagName("shortDescription");
                if ($shortDescription->length > 0) {
                    $shortDescription = $shortDescription->item(0)->nodeValue;
                } else {
                    $shortDescription = "";
                }
                $textDate = $generalInformation->item(0)->getElementsByTagName("textDate");
                if ($textDate->length > 0) {
                    $textDate = $textDate->item(0)->nodeValue;
                } else {
                    $textDate = "";
                }
                $textDestination = $generalInformation->item(0)->getElementsByTagName("textDestination");
                if ($textDestination->length > 0) {
                    $textDestination = $textDestination->item(0)->nodeValue;
                } else {
                    $textDestination = "";
                }
                $ppc = "";
                $plusPackage = $generalInformation->item(0)->getElementsByTagName("plusPackage");
                if ($plusPackage->length > 0) {
                    $ppContent = $plusPackage->item(0)->getElementsByTagName("ppContent");
                    if ($ppContent->length > 0) {
                        for ($j=0; $j < $ppContent->length; $j++) { 
                            $ppc = $ppContent->item($j)->nodeValue;
                        }
                    }
                }
                $property = $generalInformation->item(0)->getElementsByTagName("property");
                if ($property->length > 0) {
                    for ($k=0; $k < $property->length; $k++) { 
                        $propertyImage = $property->item($k)->getElementsByTagName("propertyImage");
                        if ($propertyImage->length > 0) {
                            $propertyImage = $propertyImage->item(0)->nodeValue;
                        } else {
                            $propertyImage = "";
                        }
                        $propertyName = $property->item($k)->getElementsByTagName("propertyName");
                        if ($propertyName->length > 0) {
                            $propertyName = $propertyName->item(0)->nodeValue;
                        } else {
                            $propertyName = "";
                        }
                    }
                }
                $inc = "";
                $notinc = "";
                $services = $generalInformation->item(0)->getElementsByTagName("services");
                if ($services->length > 0) {
                    $included = $services->item(0)->getElementsByTagName("included");
                    if ($included->length > 0) {
                        for ($x=0; $x < $included->length; $x++) { 
                            $inc = $included->item($x)->nodeValue;
                        }
                    }
                    $notIncluded = $services->item(0)->getElementsByTagName("notIncluded");
                    if ($notIncluded->length > 0) {
                        for ($x=0; $x < $notIncluded->length; $x++) { 
                            $notinc = $notIncluded->item($x)->nodeValue;
                        }
                    }
                }
                $url = "";
                $urlDoc = $generalInformation->item(0)->getElementsByTagName("urlDoc");
                if ($urlDoc->length > 0) {
                    for ($x=0; $x < $urlDoc->length; $x++) { 
                        $url = $urlDoc->item($x)->nodeValue;
                    }
                }
            }
            //packageOption
            $packageOption = $infoPackage->item(0)->getElementsByTagName("packageOption");
            if ($packageOption->length > 0) {
                for ($z=0; $z < $packageOption->length; $z++) { 
                    $optionCode = $packageOption->item($z)->getElementsByTagName("optionCode");
                    if ($optionCode->length > 0) {
                        $optionCode = $optionCode->item(0)->nodeValue;
                    } else {
                        $optionCode = "";
                    }
                    $title = $packageOption->item($z)->getElementsByTagName("title");
                    if ($title->length > 0) {
                        $title = $title->item(0)->nodeValue;
                    } else {
                        $title = "";
                    }
                    $destinationOption = $packageOption->item($z)->getElementsByTagName("destinationOption");
                    if ($destinationOption->length > 0) {
                        for ($zAux=0; $zAux < $destinationOption->length; $zAux++) { 
                            $title = $destinationOption->item($zAux)->getElementsByTagName("title");
                            if ($title->length > 0) {
                                $title = $title->item(0)->nodeValue;
                            } else {
                                $title = "";
                            }
                            $destinationService = $destinationOption->item($zAux)->getElementsByTagName("destinationService");
                            if ($destinationService->length > 0) {
                                for ($zAux2=0; $zAux2 < $destinationService->length; $zAux2++) { 
                                    $address = $destinationService->item($zAux2)->getElementsByTagName("address");
                                    if ($address->length > 0) {
                                        $address = $address->item(0)->nodeValue;
                                    } else {
                                        $address = "";
                                    }
                                    $categoryName = $destinationService->item($zAux2)->getElementsByTagName("categoryName");
                                    if ($categoryName->length > 0) {
                                        $categoryName = $categoryName->item(0)->nodeValue;
                                    } else {
                                        $categoryName = "";
                                    }
                                    $description = $destinationService->item($zAux2)->getElementsByTagName("description");
                                    if ($description->length > 0) {
                                        $description = $description->item(0)->nodeValue;
                                    } else {
                                        $description = "";
                                    }
                                    $serviceCode = $destinationService->item($zAux2)->getElementsByTagName("serviceCode");
                                    if ($serviceCode->length > 0) {
                                        $serviceCode = $serviceCode->item(0)->nodeValue;
                                    } else {
                                        $serviceCode = "";
                                    }
                                    $serviceImage = $destinationService->item($zAux2)->getElementsByTagName("serviceImage");
                                    if ($serviceImage->length > 0) {
                                        $serviceImage = $serviceImage->item(0)->nodeValue;
                                    } else {
                                        $serviceImage = "";
                                    }
                                    $serviceName = $destinationService->item($zAux2)->getElementsByTagName("serviceName");
                                    if ($serviceName->length > 0) {
                                        $serviceName = $serviceName->item(0)->nodeValue;
                                    } else {
                                        $serviceName = "";
                                    }
                                }
                            }
                        }
                    }
                }
            }
            //route
            $route = $infoPackage->item(0)->getElementsByTagName("route");
            if ($route->length > 0) {
                $routeImage = $route->item(0)->getElementsByTagName("routeImage");
                if ($routeImage->length > 0) {
                    $routeImage = $routeImage->item(0)->nodeValue;
                } else {
                    $routeImage = "";
                }
                $routeTitle = $route->item(0)->getElementsByTagName("routeTitle");
                if ($routeTitle->length > 0) {
                    $routeTitle = $routeTitle->item(0)->nodeValue;
                } else {
                    $routeTitle = "";
                }
                $routeSegment = $route->item(0)->getElementsByTagName("routeSegment");
                if ($routeSegment->length > 0) {
                    for ($w=0; $w < $routeSegment->length; $w++) { 
                        $beginDay = $routeSegment->item($w)->getElementsByTagName("beginDay");
                        if ($beginDay->length > 0) {
                            $beginDay = $beginDay->item(0)->nodeValue;
                        } else {
                            $beginDay = "";
                        }
                        $endDay = $routeSegment->item($w)->getElementsByTagName("endDay");
                        if ($endDay->length > 0) {
                            $endDay = $endDay->item(0)->nodeValue;
                        } else {
                            $endDay = "";
                        }
                        $title = $routeSegment->item($w)->getElementsByTagName("title");
                        if ($title->length > 0) {
                            $title = $title->item(0)->nodeValue;
                        } else {
                            $title = "";
                        }
                        $zoneCode = $routeSegment->item($w)->getElementsByTagName("zoneCode");
                        if ($zoneCode->length > 0) {
                            $zoneCode = $zoneCode->item(0)->nodeValue;
                        } else {
                            $zoneCode = "";
                        }
                        $description = $routeSegment->item($w)->getElementsByTagName("description");
                        if ($description->length > 0) {
                            $description2 = $description->item(0)->getElementsByTagName("description");
                            if ($description2->length > 0) {
                                $description2 = $description2->item(0)->nodeValue;
                            } else {
                                $description2 = "";
                            }
                            $orderId = $description->item(0)->getElementsByTagName("orderId");
                            if ($orderId->length > 0) {
                                $orderId = $orderId->item(0)->nodeValue;
                            } else {
                                $orderId = "";
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