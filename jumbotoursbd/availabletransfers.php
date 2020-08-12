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
echo "COMECOU AVAILABLE TRANSFERS<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://test.xtravelsystem.com/public/v1_0rc1/transferBookingHandler';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/transfer/types">
<soapenv:Header/>
<soapenv:Body>
   <typ:availableTransfers>
      <AvailabilityRQ_1>
         <agencyCode>266333</agencyCode>
         <brandCode>1</brandCode>
         <pointOfSaleId>1</pointOfSaleId>
         <adults>2</adults>
         <children>0</children>
         <!--
         Zero or more repetitions:
         -->
         <fromAirToHotelJourney>
            <airportId>3057</airportId>
            <establishmentId>4704</establishmentId>
            <flightInfo>
               <date>2021-02-01T08:30:00.000Z</date>
               <flightNumber>
                  <company>UX</company>
                  <fullNumber>2525</fullNumber>
                  <number>2525</number>
               </flightNumber>
               <from>BCN</from>
               <to>PMI</to>
            </flightInfo>
         </fromAirToHotelJourney>
         <fromHotelToAirJourney>
            <airportId>3057</airportId>
            <establishmentId>4704</establishmentId>
            <flightInfo>
               <date>2021-02-02T09:30:00.000Z</date>
               <flightNumber>
                  <company>JK</company>
                  <fullNumber>1212</fullNumber>
                  <number>1212</number>
               </flightNumber>
               <from>PMI</from>
               <to>BCN</to>
            </flightInfo>
         </fromHotelToAirJourney>
         <fromRow>0</fromRow>
         <journeyDirection>Roundtrip</journeyDirection>
         <language>en</language>
         <numRows>100</numRows>
         </AvailabilityRQ_1>
      </typ:availableTransfers>
   </soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

echo $return;
echo $error;
echo $return;
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
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
$availableTransfersResponse = $Body->item(0)->getElementsByTagName("availableTransfersResponse");
if ($availableTransfersResponse->length > 0) {
    $result = $availableTransfersResponse->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $fromRow = $result->item(0)->getElementsByTagName("fromRow");
        if ($fromRow->length > 0) {
            $fromRow = $fromRow->item(0)->nodeValue;
        } else {
            $fromRow = "";
        }
        $numRows = $result->item(0)->getElementsByTagName("numRows");
        if ($numRows->length > 0) {
            $numRows = $numRows->item(0)->nodeValue;
        } else {
            $numRows = "";
        }
        $totalRows = $result->item(0)->getElementsByTagName("totalRows");
        if ($totalRows->length > 0) {
            $totalRows = $totalRows->item(0)->nodeValue;
        } else {
            $totalRows = "";
        }
        $availableTransfers = $result->item(0)->getElementsByTagName("availableTransfers");
        if ($availableTransfers->lemgth > 0) {
            $adults = $availableTransfers->item(0)->getElementsByTagName("adults");
            if ($adults->length > 0) {
                $adults = $adults->item(0)->nodeValue;
            } else {
                $adults = "";
            }
            $children = $availableTransfers->item(0)->getElementsByTagName("children");
            if ($children->length > 0) {
                $children = $children->item(0)->nodeValue;
            } else {
                $children = "";
            }
            $journeyDirection = $availableTransfers->item(0)->getElementsByTagName("journeyDirection");
            if ($journeyDirection->length > 0) {
                $journeyDirection = $journeyDirection->item(0)->nodeValue;
            } else {
                $journeyDirection = "";
            }
            $fromAirToHotelJourney = $availableTransfers->item(0)->getElementsByTagName("fromAirToHotelJourney");
            if ($fromAirToHotelJourney->length > 0) {
                $airportId = $fromAirToHotelJourney->item(0)->getElementsByTagName("airportId");
                if ($airportId->length > 0) {
                    $airportId = $airportId->item(0)->nodeValue;
                } else {
                    $airportId = "";
                }
                $establishmentId = $fromAirToHotelJourney->item(0)->getElementsByTagName("establishmentId");
                if ($establishmentId->length > 0) {
                    $establishmentId = $establishmentId->item(0)->nodeValue;
                } else {
                    $establishmentId = "";
                }
                $flightInfo = $fromAirToHotelJourney->item(0)->getElementsByTagName("flightInfo");
                if ($flightInfo->length > 0) {
                    $date = $flightInfo->item(0)->getElementsByTagName("date");
                    if ($date->length > 0) {
                        $date = $date->item(0)->nodeValue;
                    } else {
                        $date = "";
                    }
                    $from = $flightInfo->item(0)->getElementsByTagName("from");
                    if ($from->length > 0) {
                        $from = $from->item(0)->nodeValue;
                    } else {
                        $from = "";
                    }
                    $to = $flightInfo->item(0)->getElementsByTagName("to");
                    if ($to->length > 0) {
                        $to = $to->item(0)->nodeValue;
                    } else {
                        $to = "";
                    }
                    $flightNumber = $flightInfo->item(0)->getElementsByTagName("flightNumber");
                    if ($flightNumber->length > 0) {
                        $company = $flightNumber->item(0)->getElementsByTagName("company");
                        if ($company->length > 0) {
                            $company = $company->item(0)->nodeValue;
                        } else {
                            $company = "";
                        }
                        $fullNumber = $flightNumber->item(0)->getElementsByTagName("fullNumber");
                        if ($fullNumber->length > 0) {
                            $fullNumber = $fullNumber->item(0)->nodeValue;
                        } else {
                            $fullNumber = "";
                        }
                        $number = $flightNumber->item(0)->getElementsByTagName("number");
                        if ($number->length > 0) {
                            $number = $number->item(0)->nodeValue;
                        } else {
                            $number = "";
                        }
                    }
                }
            }
            $fromHotelToAirJourney = $availableTransfers->item(0)->getElementsByTagName("fromHotelToAirJourney");
            if ($fromHotelToAirJourney->length > 0) {
                $airportId = $fromHotelToAirJourney->item(0)->getElementsByTagName("airportId");
                if ($airportId->length > 0) {
                    $airportId = $airportId->item(0)->nodeValue;
                } else {
                    $airportId = "";
                }
                $establishmentId = $fromHotelToAirJourney->item(0)->getElementsByTagName("establishmentId");
                if ($establishmentId->length > 0) {
                    $establishmentId = $establishmentId->item(0)->nodeValue;
                } else {
                    $establishmentId = "";
                }
                $flightInfo = $fromHotelToAirJourney->item(0)->getElementsByTagName("flightInfo");
                if ($flightInfo->length > 0) {
                    $date = $flightInfo->item(0)->getElementsByTagName("date");
                    if ($date->length > 0) {
                        $date = $date->item(0)->nodeValue;
                    } else {
                        $date = "";
                    }
                    $from = $flightInfo->item(0)->getElementsByTagName("from");
                    if ($from->length > 0) {
                        $from = $from->item(0)->nodeValue;
                    } else {
                        $from = "";
                    }
                    $to = $flightInfo->item(0)->getElementsByTagName("to");
                    if ($to->length > 0) {
                        $to = $to->item(0)->nodeValue;
                    } else {
                        $to = "";
                    }
                    $flightNumber = $flightInfo->item(0)->getElementsByTagName("flightNumber");
                    if ($flightNumber->length > 0) {
                        $company = $flightNumber->item(0)->getElementsByTagName("company");
                        if ($company->length > 0) {
                            $company = $company->item(0)->nodeValue;
                        } else {
                            $company = "";
                        }
                        $fullNumber = $flightNumber->item(0)->getElementsByTagName("fullNumber");
                        if ($fullNumber->length > 0) {
                            $fullNumber = $fullNumber->item(0)->nodeValue;
                        } else {
                            $fullNumber = "";
                        }
                        $number = $flightNumber->item(0)->getElementsByTagName("number");
                        if ($number->length > 0) {
                            $number = $number->item(0)->nodeValue;
                        } else {
                            $number = "";
                        }
                    }
                }
            }
            $transferOption = $availableTransfers->item(0)->getElementsByTagName("transferOption");
            if ($transferOption->length > 0) {
                for ($i=0; $i < $transferOption->length; $i++) { 
                    $direction = $transferOption->item($i)->getElementsByTagName("direction");
                    if ($direction->length > 0) {
                        $direction = $direction->item(0)->nodeValue;
                    } else {
                        $direction = "";
                    }
                    $locationTextGoing = $transferOption->item($i)->getElementsByTagName("locationTextGoing");
                    if ($locationTextGoing->length > 0) {
                        $locationTextGoing = $locationTextGoing->item(0)->nodeValue;
                    } else {
                        $locationTextGoing = "";
                    }
                    $locationTextReturn = $transferOption->item($i)->getElementsByTagName("locationTextReturn");
                    if ($locationTextReturn->length > 0) {
                        $locationTextReturn = $locationTextReturn->item(0)->nodeValue;
                    } else {
                        $locationTextReturn = "";
                    }
                    $numVehicles = $transferOption->item($i)->getElementsByTagName("numVehicles");
                    if ($numVehicles->length > 0) {
                        $numVehicles = $numVehicles->item(0)->nodeValue;
                    } else {
                        $numVehicles = "";
                    }
                    $serviceType = $transferOption->item($i)->getElementsByTagName("serviceType");
                    if ($serviceType->length > 0) {
                        $serviceType = $serviceType->item(0)->nodeValue;
                    } else {
                        $serviceType = "";
                    }
                    $price = $transferOption->item($i)->getElementsByTagName("price");
                    if ($price->length > 0) {
                        $priceType = $price->item(0)->getElementsByTagName("priceType");
                        if ($priceType->length > 0) {
                            $priceType = $priceType->item(0)->nodeValue;
                        } else {
                            $priceType = "";
                        }
                        $paxPrice = $price->item(0)->getElementsByTagName("paxPrice");
                        if ($paxPrice->length > 0) {
                            $currencyCode = $paxPrice->item(0)->getElementsByTagName("currencyCode");
                            if ($currencyCode->length > 0) {
                                $currencyCode = $currencyCode->item(0)->nodeValue;
                            } else {
                                $currencyCode = "";
                            }
                            $value = $paxPrice->item(0)->getElementsByTagName("value");
                            if ($value->length > 0) {
                                $value = $value->item(0)->nodeValue;
                            } else {
                                $value = "";
                            }
                        }
                        $total = $price->item(0)->getElementsByTagName("total");
                        if ($total->length > 0) {
                            $totalcurrencyCode = $total->item(0)->getElementsByTagName("currencyCode");
                            if ($totalcurrencyCode->length > 0) {
                                $totalcurrencyCode = $totalcurrencyCode->item(0)->nodeValue;
                            } else {
                                $totalcurrencyCode = "";
                            }
                            $totalvalue = $total->item(0)->getElementsByTagName("value");
                            if ($totalvalue->length > 0) {
                                $totalvalue = $totalvalue->item(0)->nodeValue;
                            } else {
                                $totalvalue = "";
                            }
                        }
                        $unitPrice = $price->item(0)->getElementsByTagName("unitPrice");
                        if ($unitPrice->length > 0) {
                            $unitPricecurrencyCode = $unitPrice->item(0)->getElementsByTagName("currencyCode");
                            if ($unitPricecurrencyCode->length > 0) {
                                $unitPricecurrencyCode = $unitPricecurrencyCode->item(0)->nodeValue;
                            } else {
                                $unitPricecurrencyCode = "";
                            }
                            $unitPricevalue = $unitPrice->item(0)->getElementsByTagName("value");
                            if ($unitPricevalue->length > 0) {
                                $unitPricevalue = $unitPricevalue->item(0)->nodeValue;
                            } else {
                                $unitPricevalue = "";
                            }
                            $paxType = $unitPrice->item(0)->getElementsByTagName("paxType");
                            if ($paxType->length > 0) {
                                $paxType = $paxType->item(0)->nodeValue;
                            } else {
                                $paxType = "";
                            }
                        }
                    }
                    $vehicleInfo = $transferOption->item($i)->getElementsByTagName("vehicleInfo");
                    if ($vehicleInfo->length > 0) {
                        $carId = $vehicleInfo->item(0)->getElementsByTagName("carId");
                        if ($carId->length > 0) {
                            $carId = $carId->item(0)->nodeValue;
                        } else {
                            $carId = "";
                        }
                        $carName = $vehicleInfo->item(0)->getElementsByTagName("carName");
                        if ($carName->length > 0) {
                            $carName = $carName->item(0)->nodeValue;
                        } else {
                            $carName = "";
                        }
                        $description = $vehicleInfo->item(0)->getElementsByTagName("description");
                        if ($description->length > 0) {
                            $description = $description->item(0)->nodeValue;
                        } else {
                            $description = "";
                        }
                        $imageUrl = $vehicleInfo->item(0)->getElementsByTagName("imageUrl");
                        if ($imageUrl->length > 0) {
                            $imageUrl = $imageUrl->item(0)->nodeValue;
                        } else {
                            $imageUrl = "";
                        }
                        $numPassenger = $vehicleInfo->item(0)->getElementsByTagName("numPassenger");
                        if ($numPassenger->length > 0) {
                            $numPassenger = $numPassenger->item(0)->nodeValue;
                        } else {
                            $numPassenger = "";
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