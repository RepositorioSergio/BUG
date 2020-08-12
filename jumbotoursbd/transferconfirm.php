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
echo "COMECOU CONFIRM<br/>";
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

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/transfer/types">
   <soapenv:Header/>
   <soapenv:Body>
      <typ:confirm>
         <ConfirmRQ_1>
            <agencyCode>266333</agencyCode>
            <brandCode>1</brandCode>
            <pointOfSaleId>1</pointOfSaleId>
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
            <journeyDirection>Roundtrip</journeyDirection>
            <language>en</language>
            <!--Zero or more repetitions:-->
            <supplements></supplements>
            <!--Zero or more repetitions:-->
            <vehicleOccupancy>
               <adults>2</adults>
               <children>0</children>
               <!--Zero or more repetitions:-->
               <journeyDirection>Roundtrip</journeyDirection>
               <!--Zero or more repetitions:-->
               <names>nombre1</names>
               <names>nombre2</names>
               <numberOfVehicles>1</numberOfVehicles>
               <serviceType>Private</serviceType>
               <vehicleTypeId>1383</vehicleTypeId>
            </vehicleOccupancy>
            <agencyReference>myref</agencyReference>
            <basketId></basketId>
            <closeBasket>true</closeBasket>
            <!--Zero or more repetitions:-->
            <comments>
               <text></text>
               <type></type>
            </comments>
            <sendVoucher>false</sendVoucher>
            <titular>test</titular>
            <userId></userId>
            <voucherEmail>pep.company@jumbotours.es</voucherEmail>
         </ConfirmRQ_1>
      </typ:confirm>
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
$confirmResponse = $Body->item(0)->getElementsByTagName("confirmResponse");
if ($confirmResponse->length > 0) {
    $result = $confirmExtendsV22Response->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $journeyDirection = $result->item(0)->getElementsByTagName("journeyDirection");
        if ($journeyDirection->length > 0) {
            $journeyDirection = $journeyDirection->item(0)->nodeValue;
        } else {
            $journeyDirection = "";
        }
        $status = $result->item(0)->getElementsByTagName("status");
        if ($status->length > 0) {
            $status = $status->item(0)->nodeValue;
        } else {
            $status = "";
        }
        $basket = $result->item(0)->getElementsByTagName("basket");
        if ($basket->length > 0) {
            $basketId = $basket->item(0)->getElementsByTagName("basketId");
            if ($basketId->length > 0) {
                $basketId = $basketId->item(0)->nodeValue;
            } else {
                $basketId = "";
            }
            $cancelled = $basket->item(0)->getElementsByTagName("cancelled");
            if ($cancelled->length > 0) {
                $cancelled = $cancelled->item(0)->nodeValue;
            } else {
                $cancelled = "";
            }
            $closed = $basket->item(0)->getElementsByTagName("closed");
            if ($closed->length > 0) {
                $closed = $closed->item(0)->nodeValue;
            } else {
                $closed = "";
            }
            $opened = $basket->item(0)->getElementsByTagName("opened");
            if ($opened->length > 0) {
                $opened = $opened->item(0)->nodeValue;
            } else {
                $opened = "";
            }
            $titular = $basket->item(0)->getElementsByTagName("titular");
            if ($titular->length > 0) {
                $titular = $titular->item(0)->nodeValue;
            } else {
                $titular = "";
            }
        }
        $confirmService = $result->item(0)->getElementsByTagName("confirmService");
        if ($confirmService->length > 0) {
            for ($i=0; $i < $confirmService->length; $i++) { 
                $journeyDirection = $confirmService->item($i)->getElementsByTagName("journeyDirection");
                if ($journeyDirection->length > 0) {
                    $journeyDirection = $journeyDirection->item(0)->nodeValue;
                } else {
                    $journeyDirection = "";
                }
                $serviceId = $confirmService->item($i)->getElementsByTagName("serviceId");
                if ($serviceId->length > 0) {
                    $serviceId = $serviceId->item(0)->nodeValue;
                } else {
                    $serviceId = "";
                }
                $journeyInfo = $confirmService->item($i)->getElementsByTagName("journeyInfo");
                if ($journeyInfo->length > 0) {
                    $airportId = $journeyInfo->item(0)->getElementsByTagName("airportId");
                    if ($airportId->length > 0) {
                        $airportId = $airportId->item(0)->nodeValue;
                    } else {
                        $airportId = "";
                    }
                    $establishmentId = $journeyInfo->item(0)->getElementsByTagName("establishmentId");
                    if ($establishmentId->length > 0) {
                        $establishmentId = $establishmentId->item(0)->nodeValue;
                    } else {
                        $establishmentId = "";
                    }
                    $flightInfo = $journeyInfo->item(0)->getElementsByTagName("flightInfo");
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
                        $localTime = $flightInfo->item(0)->getElementsByTagName("localTime");
                        if ($localTime->length > 0) {
                            $localTime = $localTime->item(0)->nodeValue;
                        } else {
                            $localTime = "";
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
                $vehicleOccupancy = $confirmService->item($i)->getElementsByTagName("vehicleOccupancy");
                if ($vehicleOccupancy->length > 0) {
                    $adults = $vehicleOccupancy->item(0)->getElementsByTagName("adults");
                    if ($adults->length > 0) {
                        $adults = $adults->item(0)->nodeValue;
                    } else {
                        $adults = "";
                    }
                    $children = $vehicleOccupancy->item(0)->getElementsByTagName("children");
                    if ($children->length > 0) {
                        $children = $children->item(0)->nodeValue;
                    } else {
                        $children = "";
                    }
                    $journeyDirection = $vehicleOccupancy->item(0)->getElementsByTagName("journeyDirection");
                    if ($journeyDirection->length > 0) {
                        $journeyDirection = $journeyDirection->item(0)->nodeValue;
                    } else {
                        $journeyDirection = "";
                    }
                    $numberOfVehicles = $vehicleOccupancy->item(0)->getElementsByTagName("numberOfVehicles");
                    if ($numberOfVehicles->length > 0) {
                        $numberOfVehicles = $numberOfVehicles->item(0)->nodeValue;
                    } else {
                        $numberOfVehicles = "";
                    }
                    $serviceType = $vehicleOccupancy->item(0)->getElementsByTagName("serviceType");
                    if ($serviceType->length > 0) {
                        $serviceType = $serviceType->item(0)->nodeValue;
                    } else {
                        $serviceType = "";
                    }
                    $vehicleTypeId = $vehicleOccupancy->item(0)->getElementsByTagName("vehicleTypeId");
                    if ($vehicleTypeId->length > 0) {
                        $vehicleTypeId = $vehicleTypeId->item(0)->nodeValue;
                    } else {
                        $vehicleTypeId = "";
                    }
                    $locationTextGoing = $vehicleOccupancy->item(0)->getElementsByTagName("locationTextGoing");
                    if ($locationTextGoing->length > 0) {
                        $locationTextGoing = $locationTextGoing->item(0)->nodeValue;
                    } else {
                        $locationTextGoing = "";
                    }
                    $price = $vehicleOccupancy->item(0)->getElementsByTagName("price");
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
                    }
                }
            }
        }
        $totalPrice = $result->item(0)->getElementsByTagName("totalPrice");
        if ($totalPrice->length > 0) {
            $currencyCode = $totalPrice->item(0)->getElementsByTagName("currencyCode");
            if ($currencyCode->length > 0) {
                $currencyCode = $currencyCode->item(0)->nodeValue;
            } else {
                $currencyCode = "";
            }
            $value = $totalPrice->item(0)->getElementsByTagName("value");
            if ($value->length > 0) {
                $value = $value->item(0)->nodeValue;
            } else {
                $value = "";
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