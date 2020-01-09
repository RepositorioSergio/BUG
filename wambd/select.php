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
echo "COMECOU SELECT<br/>";
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

$url = 'https://xtest.wamos.com/packageTravel';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pac="http://packages.servicePackage.dome.com/">
<soapenv:Header/>
<soapenv:Body>
   <pac:select>
      <arg0>
         <!--addPostNights>1</addPostNights-->
         <!--addPreNights>2</addPreNights-->
         <!--El campo "distribution" es obligatorio-->
         <distribution>
            <distributionId>1</distributionId>
            <pax>
               <age>30</age>
               <documentNumber></documentNumber>
               <firstName>Juan</firstName>
               <lastName>Pablo</lastName>
               <paxId>1</paxId>
               <phone></phone>
            </pax>
            <pax>
               <age>35</age>
               <documentNumber></documentNumber>
               <firstName>Alexandra</firstName>
               <lastName>Pablo</lastName>
               <paxId>2</paxId>
               <phone></phone>
            </pax>    
        </distribution>

         <!--El campo "ideses" es obligatorio-->
         <ideses>WAW#40292#779824565346450</ideses>
         <optionalServiceId>5:SE</optionalServiceId>
         
         <!--El campo "serviceGroupId" es obligatorio-->
         <serviceGroupId>27819</serviceGroupId>
      </arg0>
   </pac:select>
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
$selectResponse = $Body->item(0)->getElementsByTagName("selectResponse");
if ($selectResponse->length > 0) {
    $return = $selectResponse->item(0)->getElementsByTagName("return");
    if ($return->length > 0) {
        $ideses = $return->item(0)->getElementsByTagName("ideses");
        if ($ideses->length > 0) {
            $ideses = $ideses->item(0)->nodeValue;
        } else {
            $ideses = "";
        }
        //selectedPackage
        $selectedPackage = $return->item(0)->getElementsByTagName("selectedPackage");
        if ($selectedPackage->length > 0) {
            $beginDate = $selectedPackage->item(0)->getElementsByTagName("beginDate");
            if ($beginDate->length > 0) {
                $beginDate = $beginDate->item(0)->nodeValue;
            } else {
                $beginDate = "";
            }
            $categoryCode = $selectedPackage->item(0)->getElementsByTagName("categoryCode");
            if ($categoryCode->length > 0) {
                $categoryCode = $categoryCode->item(0)->nodeValue;
            } else {
                $categoryCode = "";
            }
            $categoryName = $selectedPackage->item(0)->getElementsByTagName("categoryName");
            if ($categoryName->length > 0) {
                $categoryName = $categoryName->item(0)->nodeValue;
            } else {
                $categoryName = "";
            }
            $commissionablePrice = $selectedPackage->item(0)->getElementsByTagName("commissionablePrice");
            if ($commissionablePrice->length > 0) {
                $commissionablePrice = $commissionablePrice->item(0)->nodeValue;
            } else {
                $commissionablePrice = "";
            }
            $currency = $selectedPackage->item(0)->getElementsByTagName("currency");
            if ($currency->length > 0) {
                $currency = $currency->item(0)->nodeValue;
            } else {
                $currency = "";
            }
            $endDate = $selectedPackage->item(0)->getElementsByTagName("endDate");
            if ($endDate->length > 0) {
                $endDate = $endDate->item(0)->nodeValue;
            } else {
                $endDate = "";
            }
            $fareCode = $selectedPackage->item(0)->getElementsByTagName("fareCode");
            if ($fareCode->length > 0) {
                $fareCode = $fareCode->item(0)->nodeValue;
            } else {
                $fareCode = "";
            }
            $fareName = $selectedPackage->item(0)->getElementsByTagName("fareName");
            if ($fareName->length > 0) {
                $fareName = $fareName->item(0)->nodeValue;
            } else {
                $fareName = "";
            }
            $packageCode = $selectedPackage->item(0)->getElementsByTagName("packageCode");
            if ($packageCode->length > 0) {
                $packageCode = $packageCode->item(0)->nodeValue;
            } else {
                $packageCode = "";
            }
            $packageName = $selectedPackage->item(0)->getElementsByTagName("packageName");
            if ($packageName->length > 0) {
                $packageName = $packageName->item(0)->nodeValue;
            } else {
                $packageName = "";
            }
            $price = $selectedPackage->item(0)->getElementsByTagName("price");
            if ($price->length > 0) {
                $price = $price->item(0)->nodeValue;
            } else {
                $price = "";
            }
            $status = $selectedPackage->item(0)->getElementsByTagName("status");
            if ($status->length > 0) {
                $status = $status->item(0)->nodeValue;
            } else {
                $status = "";
            }
            $stayCode = $selectedPackage->item(0)->getElementsByTagName("stayCode");
            if ($stayCode->length > 0) {
                $stayCode = $stayCode->item(0)->nodeValue;
            } else {
                $stayCode = "";
            }
            $stayName = $selectedPackage->item(0)->getElementsByTagName("stayName");
            if ($stayName->length > 0) {
                $stayName = $stayName->item(0)->nodeValue;
            } else {
                $stayName = "";
            }
            $destiny = $selectedPackage->item(0)->getElementsByTagName("destiny");
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
            //service
            $service = $selectedPackage->item(0)->getElementsByTagName("service");
            if ($service->length > 0) {
                $beginDateService = $service->item(0)->getElementsByTagName("beginDate");
                if ($beginDateService->length > 0) {
                    $beginDateService = $beginDateService->item(0)->nodeValue;
                } else {
                    $beginDateService = "";
                }
                $endDateService = $service->item(0)->getElementsByTagName("endDate");
                if ($endDateService->length > 0) {
                    $endDateService = $endDateService->item(0)->nodeValue;
                } else {
                    $endDateService = "";
                }
                $commissionablePrice = $service->item(0)->getElementsByTagName("commissionablePrice");
                if ($commissionablePrice->length > 0) {
                    $commissionablePrice = $commissionablePrice->item(0)->nodeValue;
                } else {
                    $commissionablePrice = "";
                }
                $priceService = $service->item(0)->getElementsByTagName("price");
                if ($priceService->length > 0) {
                    $priceService = $priceService->item(0)->nodeValue;
                } else {
                    $priceService = "";
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
                $serviceDetail = $service->item(0)->getElementsByTagName("servicserviceDetaileTypeCode");
                if ($serviceDetail->length > 0) {
                    $categoryCode = $serviceDetail->item(0)->getElementsByTagName("categoryCode");
                    if ($categoryCode->length > 0) {
                        $categoryCode = $categoryCode->item(0)->nodeValue;
                    } else {
                        $categoryCode = "";
                    }
                    $categoryName = $serviceDetail->item(0)->getElementsByTagName("categoryName");
                    if ($categoryName->length > 0) {
                        $categoryName = $categoryName->item(0)->nodeValue;
                    } else {
                        $categoryName = "";
                    }
                    $characteristicCode = $serviceDetail->item(0)->getElementsByTagName("characteristicCode");
                    if ($characteristicCode->length > 0) {
                        $characteristicCode = $characteristicCode->item(0)->nodeValue;
                    } else {
                        $characteristicCode = "";
                    }
                    $commissionableSupplement = $serviceDetail->item(0)->getElementsByTagName("commissionableSupplement");
                    if ($commissionableSupplement->length > 0) {
                        $commissionableSupplement = $commissionableSupplement->item(0)->nodeValue;
                    } else {
                        $commissionableSupplement = "";
                    }
                    $mealPlanCode = $serviceDetail->item(0)->getElementsByTagName("mealPlanCode");
                    if ($mealPlanCode->length > 0) {
                        $mealPlanCode = $mealPlanCode->item(0)->nodeValue;
                    } else {
                        $mealPlanCode = "";
                    }
                    $modalityCode = $serviceDetail->item(0)->getElementsByTagName("modalityCode");
                    if ($modalityCode->length > 0) {
                        $modalityCode = $modalityCode->item(0)->nodeValue;
                    } else {
                        $modalityCode = "";
                    }
                    $optinalServiceId = $serviceDetail->item(0)->getElementsByTagName("optinalServiceId");
                    if ($optinalServiceId->length > 0) {
                        $optinalServiceId = $optinalServiceId->item(0)->nodeValue;
                    } else {
                        $optinalServiceId = "";
                    }
                    $serviceCode = $serviceDetail->item(0)->getElementsByTagName("serviceCode");
                    if ($serviceCode->length > 0) {
                        $serviceCode = $serviceCode->item(0)->nodeValue;
                    } else {
                        $serviceCode = "";
                    }
                    $serviceId = $serviceDetail->item(0)->getElementsByTagName("serviceId");
                    if ($serviceId->length > 0) {
                        $serviceId = $serviceId->item(0)->nodeValue;
                    } else {
                        $serviceId = "";
                    }
                    $serviceName = $serviceDetail->item(0)->getElementsByTagName("serviceName");
                    if ($serviceName->length > 0) {
                        $serviceName = $serviceName->item(0)->nodeValue;
                    } else {
                        $serviceName = "";
                    }
                    $status = $serviceDetail->item(0)->getElementsByTagName("status");
                    if ($status->length > 0) {
                        $status = $status->item(0)->nodeValue;
                    } else {
                        $status = "";
                    }
                    $supplement = $serviceDetail->item(0)->getElementsByTagName("supplement");
                    if ($supplement->length > 0) {
                        $supplement = $supplement->item(0)->nodeValue;
                    } else {
                        $supplement = "";
                    }
                    $pax = "";
                    $paxId = $serviceDetail->item(0)->getElementsByTagName("paxId");
                    if ($paxId->length > 0) {
                        for ($i=0; $i < $paxId->length; $i++) { 
                            $pax = $paxId->item($i)->nodeValue;
                        }
                    }
                }
            }
            //passage
            $passage = $selectedPackage->item(0)->getElementsByTagName("passage");
            if ($passage->length > 0) {
                $pax = $passage->item(0)->getElementsByTagName("pax");
                if ($pax->length > 0) {
                    for ($j=0; $j < $pax->length; $j++) { 
                        $age = $pax->item($j)->getElementsByTagName("age");
                        if ($age->length > 0) {
                            $age = $age->item(0)->nodeValue;
                        } else {
                            $age = "";
                        }
                        $firstName = $pax->item($j)->getElementsByTagName("firstName");
                        if ($firstName->length > 0) {
                            $firstName = $firstName->item(0)->nodeValue;
                        } else {
                            $firstName = "";
                        }
                        $lastName = $pax->item($j)->getElementsByTagName("lastName");
                        if ($lastName->length > 0) {
                            $lastName = $lastName->item(0)->nodeValue;
                        } else {
                            $lastName = "";
                        }
                        $paxId = $pax->item($j)->getElementsByTagName("paxId");
                        if ($paxId->length > 0) {
                            $paxId = $paxId->item(0)->nodeValue;
                        } else {
                            $paxId = "";
                        }
                    }
                }
            }
            //remark
            $remark = $selectedPackage->item(0)->getElementsByTagName("remark");
            if ($remark->length > 0) {
                for ($k=0; $k < $remark->length; $k++) { 
                    $code = $remark->item($k)->getElementsByTagName("code");
                    if ($code->length > 0) {
                        $code = $code->item(0)->nodeValue;
                    } else {
                        $code = "";
                    }
                    $type = $remark->item($k)->getElementsByTagName("type");
                    if ($type->length > 0) {
                        $type = $type->item(0)->nodeValue;
                    } else {
                        $type = "";
                    }
                    $value = $remark->item($k)->getElementsByTagName("value");
                    if ($value->length > 0) {
                        $value = $value->item(0)->nodeValue;
                    } else {
                        $value = "";
                    }
                }
            }
            //supplement
            $supplement = $selectedPackage->item(0)->getElementsByTagName("supplement");
            if ($supplement->length > 0) {
                for ($x=0; $x < $supplement->length; $x++) { 
                    $amount = $supplement->item($x)->getElementsByTagName("amount");
                    if ($amount->length > 0) {
                        $amount = $amount->item(0)->nodeValue;
                    } else {
                        $amount = "";
                    }
                    $commissionableAmount = $supplement->item($x)->getElementsByTagName("commissionableAmount");
                    if ($commissionableAmount->length > 0) {
                        $commissionableAmount = $commissionableAmount->item(0)->nodeValue;
                    } else {
                        $commissionableAmount = "";
                    }
                    $description = $supplement->item($x)->getElementsByTagName("description");
                    if ($description->length > 0) {
                        $description = $description->item(0)->nodeValue;
                    } else {
                        $description = "";
                    }
                    $supplementId = $supplement->item($x)->getElementsByTagName("supplementId");
                    if ($supplementId->length > 0) {
                        $supplementId = $supplementId->item(0)->nodeValue;
                    } else {
                        $supplementId = "";
                    }
                    $supplementTypeCode = $supplement->item($x)->getElementsByTagName("supplementTypeCode");
                    if ($supplementTypeCode->length > 0) {
                        $supplementTypeCode = $supplementTypeCode->item(0)->nodeValue;
                    } else {
                        $supplementTypeCode = "";
                    }
                    $pax = "";
                    $paxId = $supplement->item($x)->getElementsByTagName("paxId");
                    if ($paxId->length > 0) {
                        for ($i=0; $i < $paxId->length; $i++) { 
                            $pax = $paxId->item($i)->nodeValue;
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