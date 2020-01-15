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
echo "COMECOU AVAILABILITY<br/>";
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
   <pac:availability>
      <arg0>
       <!--El campo "BeginDate" es obligatorio-->
         <beginDate>2020-02-01</beginDate>
       <!--endDate>2019-02-02</endDate-->
         <categoryCode>T</categoryCode>
         <!--El campo "distribution" es obligatorio -->
         <distribution>
            <distributionId>1</distributionId>
            <pax>
               <age>30</age>
               <documentNumber></documentNumber>
               <firstName>NOMBRETESTA1</firstName>
               <lastName>APELLIDOTESTA1</lastName>
               <paxId>1</paxId>
               <phone></phone>
            </pax>
            <pax>
               <age>35</age>
               <documentNumber></documentNumber>
               <firstName>NOMBRETESTA2</firstName>
               <lastName>APELLIDOTESTA2</lastName>
               <paxId>2</paxId>
               <phone></phone>
            </pax>   
         </distribution>
         <!-- El campo "fareCode" es obligatorio -->
         <fareCode>NORMAL</fareCode>
         <!--ideses></ideses-->
         <login>
            <clientId>WWCTM001</clientId>
            <password>CT8954TO</password>
            <system>WAW</system>
            <user>XMLCON</user>
         </login>
         <!-- El campo "packageCode" es obligatorio -->
         <packageCode>EU13992</packageCode>
         <showOptionals>Y</showOptionals>
         <stayCode>3N</stayCode>
      </arg0>
   </pac:availability>
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
$availabilityResponse = $Body->item(0)->getElementsByTagName("availabilityResponse");
if ($availabilityResponse->length > 0) {
    $return = $availabilityResponse->item(0)->getElementsByTagName("return");
    if ($return->length > 0) {
        $ideses = $return->item(0)->getElementsByTagName("ideses");
        if ($ideses->length > 0) {
            $ideses = $ideses->item(0)->nodeValue;
        } else {
            $ideses = "";
        }
        //availablePackage
        $availablePackage = $return->item(0)->getElementsByTagName("availablePackage");
        if ($selectedPackage->length > 0) {
            $beginDate = $availablePackage->item(0)->getElementsByTagName("beginDate");
            if ($beginDate->length > 0) {
                $beginDate = $beginDate->item(0)->nodeValue;
            } else {
                $beginDate = "";
            }
            $categoryCode = $availablePackage->item(0)->getElementsByTagName("categoryCode");
            if ($categoryCode->length > 0) {
                $categoryCode = $categoryCode->item(0)->nodeValue;
            } else {
                $categoryCode = "";
            }
            $categoryName = $availablePackage->item(0)->getElementsByTagName("categoryName");
            if ($categoryName->length > 0) {
                $categoryName = $categoryName->item(0)->nodeValue;
            } else {
                $categoryName = "";
            }
            $delayFirstService = $availablePackage->item(0)->getElementsByTagName("delayFirstService");
            if ($delayFirstService->length > 0) {
                $delayFirstService = $delayFirstService->item(0)->nodeValue;
            } else {
                $delayFirstService = "";
            }
            $endDate = $availablePackage->item(0)->getElementsByTagName("endDate");
            if ($endDate->length > 0) {
                $endDate = $endDate->item(0)->nodeValue;
            } else {
                $endDate = "";
            }
            $fareCode = $availablePackage->item(0)->getElementsByTagName("fareCode");
            if ($fareCode->length > 0) {
                $fareCode = $fareCode->item(0)->nodeValue;
            } else {
                $fareCode = "";
            }
            $fareName = $availablePackage->item(0)->getElementsByTagName("fareName");
            if ($fareName->length > 0) {
                $fareName = $fareName->item(0)->nodeValue;
            } else {
                $fareName = "";
            }
            $packageCode = $availablePackage->item(0)->getElementsByTagName("packageCode");
            if ($packageCode->length > 0) {
                $packageCode = $packageCode->item(0)->nodeValue;
            } else {
                $packageCode = "";
            }
            $packageName = $availablePackage->item(0)->getElementsByTagName("packageName");
            if ($packageName->length > 0) {
                $packageName = $packageName->item(0)->nodeValue;
            } else {
                $packageName = "";
            }
            $postNightsAllowed = $availablePackage->item(0)->getElementsByTagName("postNightsAllowed");
            if ($postNightsAllowed->length > 0) {
                $postNightsAllowed = $postNightsAllowed->item(0)->nodeValue;
            } else {
                $postNightsAllowed = "";
            }
            $preNightsAllowed = $availablePackage->item(0)->getElementsByTagName("preNightsAllowed");
            if ($preNightsAllowed->length > 0) {
                $preNightsAllowed = $preNightsAllowed->item(0)->nodeValue;
            } else {
                $preNightsAllowed = "";
            }
            $stayCode = $availablePackage->item(0)->getElementsByTagName("stayCode");
            if ($stayCode->length > 0) {
                $stayCode = $stayCode->item(0)->nodeValue;
            } else {
                $stayCode = "";
            }
            $stayName = $availablePackage->item(0)->getElementsByTagName("stayName");
            if ($stayName->length > 0) {
                $stayName = $stayName->item(0)->nodeValue;
            } else {
                $stayName = "";
            }
            //destiny
            $destiny = $availablePackage->item(0)->getElementsByTagName("destiny");
            if ($destiny->length > 0) {
                $destinationCode = $destiny->item(0)->getElementsByTagName("destinationCode");
                if ($destinationCode->length > 0) {
                    $destinationCode = $destinationCode->item(0)->nodeValue;
                } else {
                    $destinationCode = "";
                }
                $destinationName = $destiny->item(0)->getElementsByTagName("destinationName");
                if ($destinationName->length > 0) {
                    $destinationName = $destinationName->item(0)->nodeValue;
                } else {
                    $destinationName = "";
                }
            }
            //serviceGroup
            $serviceGroup = $availablePackage->item(0)->getElementsByTagName("serviceGroup");
            if ($serviceGroup->length > 0) {
                $basePaxPrice = $serviceGroup->item(0)->getElementsByTagName("basePaxPrice");
                if ($basePaxPrice->length > 0) {
                    $basePaxPrice = $basePaxPrice->item(0)->nodeValue;
                } else {
                    $basePaxPrice = "";
                }
                $currency = $serviceGroup->item(0)->getElementsByTagName("currency");
                if ($currency->length > 0) {
                    $currency = $currency->item(0)->nodeValue;
                } else {
                    $currency = "";
                }
                $serviceGroupId = $serviceGroup->item(0)->getElementsByTagName("serviceGroupId");
                if ($serviceGroupId->length > 0) {
                    $serviceGroupId = $serviceGroupId->item(0)->nodeValue;
                } else {
                    $serviceGroupId = "";
                }
                $service = $serviceGroup->item(0)->getElementsByTagName("service");
                if ($service->length > 0) {
                    $augmentableQuantity = $service->item(0)->getElementsByTagName("augmentableQuantity");
                    if ($augmentableQuantity->length > 0) {
                        $augmentableQuantity = $augmentableQuantity->item(0)->nodeValue;
                    } else {
                        $augmentableQuantity = "";
                    }
                    $beginDate = $service->item(0)->getElementsByTagName("beginDate");
                    if ($beginDate->length > 0) {
                        $beginDate = $beginDate->item(0)->nodeValue;
                    } else {
                        $beginDate = "";
                    }
                    $augmentableQuantity = $service->item(0)->getElementsByTagName("decrementableQuantity");
                    if ($decrementableQuantity->length > 0) {
                        $decrementableQuantity = $decrementableQuantity->item(0)->nodeValue;
                    } else {
                        $decrementableQuantity = "";
                    }
                    $endDate = $service->item(0)->getElementsByTagName("endDate");
                    if ($endDate->length > 0) {
                        $endDate = $endDate->item(0)->nodeValue;
                    } else {
                        $endDate = "";
                    }
                    $optionalService = $service->item(0)->getElementsByTagName("optionalService");
                    if ($optionalService->length > 0) {
                        $optionalService = $optionalService->item(0)->nodeValue;
                    } else {
                        $optionalService = "";
                    }
                    $quantity = $service->item(0)->getElementsByTagName("quantity");
                    if ($quantity->length > 0) {
                        $quantity = $quantity->item(0)->nodeValue;
                    } else {
                        $quantity = "";
                    }
                    $serviceId = $service->item(0)->getElementsByTagName("serviceId");
                    if ($serviceId->length > 0) {
                        $serviceId = $serviceId->item(0)->nodeValue;
                    } else {
                        $serviceId = "";
                    }
                    $serviceName = $service->item(0)->getElementsByTagName("serviceName");
                    if ($serviceName->length > 0) {
                        $serviceName = $serviceName->item(0)->nodeValue;
                    } else {
                        $serviceName = "";
                    }
                    $serviceTypeCode = $service->item(0)->getElementsByTagName("serviceTypeCode");
                    if ($serviceTypeCode->length > 0) {
                        $serviceTypeCode = $serviceTypeCode->item(0)->nodeValue;
                    } else {
                        $serviceTypeCode = "";
                    }
                    //serviceDetail
                    $serviceDetail = $service->item(0)->getElementsByTagName("serviceDetail");
                    if ($serviceDetail->length > 0) {
                        $categoryCodeSD = $serviceDetail->item(0)->getElementsByTagName("categoryCode");
                        if ($categoryCodeSD->length > 0) {
                            $categoryCodeSD = $categoryCodeSD->item(0)->nodeValue;
                        } else {
                            $categoryCodeSD = "";
                        }
                        $categoryNameSD = $serviceDetail->item(0)->getElementsByTagName("categoryName");
                        if ($categoryNameSD->length > 0) {
                            $categoryNameSD = $categoryNameSD->item(0)->nodeValue;
                        } else {
                            $categoryNameSD = "";
                        }
                        $serviceCodeSD = $serviceDetail->item(0)->getElementsByTagName("serviceCode");
                        if ($serviceCodeSD->length > 0) {
                            $serviceCodeSD = $serviceCodeSD->item(0)->nodeValue;
                        } else {
                            $serviceCodeSD = "";
                        }
                        $serviceIdSD = $serviceDetail->item(0)->getElementsByTagName("serviceId");
                        if ($serviceIdSD->length > 0) {
                            $serviceIdSD = $serviceIdSD->item(0)->nodeValue;
                        } else {
                            $serviceIdSD = "";
                        }
                        $serviceNameSD = $serviceDetail->item(0)->getElementsByTagName("serviceName");
                        if ($serviceNameSD->length > 0) {
                            $serviceNameSD = $serviceNameSD->item(0)->nodeValue;
                        } else {
                            $serviceNameSD = "";
                        }
                        $statusSD = $serviceDetail->item(0)->getElementsByTagName("status");
                        if ($statusSD->length > 0) {
                            $statusSD = $statusSD->item(0)->nodeValue;
                        } else {
                            $statusSD = "";
                        }
                        $supplementSD = $serviceDetail->item(0)->getElementsByTagName("supplement");
                        if ($supplementSD->length > 0) {
                            $supplementSD = $supplementSD->item(0)->nodeValue;
                        } else {
                            $supplementSD = "";
                        }
                        $destiny = $serviceDetail->item(0)->getElementsByTagName("destiny");
                        if ($destiny->length > 0) {
                            $destinationCodeSD = $destiny->item(0)->getElementsByTagName("destinationCode");
                            if ($destinationCodeSD->length > 0) {
                                $destinationCodeSD = $destinationCodeSD->item(0)->nodeValue;
                            } else {
                                $destinationCodeSD = "";
                            }
                            $destinationNameSD = $destiny->item(0)->getElementsByTagName("destinationName");
                            if ($destinationNameSD->length > 0) {
                                $destinationNameSD = $destinationNameSD->item(0)->nodeValue;
                            } else {
                                $destinationNameSD = "";
                            }
                        }
                    }
                }
            }
            //specialSupplement
            $specialSupplement = $availablePackage->item(0)->getElementsByTagName("specialSupplement");
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
            //optionalService
            $optionalService = $availablePackage->item(0)->getElementsByTagName("optionalService");
            if ($optionalService->length > 0) {
                for ($i=0; $i < $optionalService->length; $i++) { 
                    $modalityCode = $optionalService->item($i)->getElementsByTagName("modalityCode");
                    if ($modalityCode->length > 0) {
                        $modalityCode = $modalityCode->item(0)->nodeValue;
                    } else {
                        $modalityCode = "";
                    }
                    $modalityName = $optionalService->item($i)->getElementsByTagName("modalityName");
                    if ($modalityName->length > 0) {
                        $modalityName = $modalityName->item(0)->nodeValue;
                    } else {
                        $modalityName = "";
                    }
                    $optinalServiceId = $optionalService->item($i)->getElementsByTagName("optinalServiceId");
                    if ($optinalServiceId->length > 0) {
                        $optinalServiceId = $optinalServiceId->item(0)->nodeValue;
                    } else {
                        $optinalServiceId = "";
                    }
                    $serviceCode = $optionalService->item($i)->getElementsByTagName("serviceCode");
                    if ($serviceCode->length > 0) {
                        $serviceCode = $serviceCode->item(0)->nodeValue;
                    } else {
                        $serviceCode = "";
                    }
                    $serviceName = $optionalService->item($i)->getElementsByTagName("serviceName");
                    if ($serviceName->length > 0) {
                        $serviceName = $serviceName->item(0)->nodeValue;
                    } else {
                        $serviceName = "";
                    }
                    $status = $optionalService->item($i)->getElementsByTagName("status");
                    if ($status->length > 0) {
                        $status = $status->item(0)->nodeValue;
                    } else {
                        $status = "";
                    }
                    $supplement = $optionalService->item($i)->getElementsByTagName("supplement");
                    if ($supplement->length > 0) {
                        $supplement = $supplement->item(0)->nodeValue;
                    } else {
                        $supplement = "";
                    }
                    $occupancy = $optionalService->item($i)->getElementsByTagName("occupancy");
                    if ($occupancy->length > 0) {
                        $max = $occupancy->item(0)->getElementsByTagName("max");
                        if ($max->length > 0) {
                            $max = $max->item(0)->nodeValue;
                        } else {
                            $max = "";
                        }
                        $maxAdult = $occupancy->item(0)->getElementsByTagName("maxAdult");
                        if ($maxAdult->length > 0) {
                            $maxAdult = $maxAdult->item(0)->nodeValue;
                        } else {
                            $maxAdult = "";
                        }
                        $maxNin = $occupancy->item(0)->getElementsByTagName("maxNin");
                        if ($maxNin->length > 0) {
                            $maxNin = $maxNin->item(0)->nodeValue;
                        } else {
                            $maxNin = "";
                        }
                        $min = $occupancy->item(0)->getElementsByTagName("min");
                        if ($min->length > 0) {
                            $min = $min->item(0)->nodeValue;
                        } else {
                            $min = "";
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