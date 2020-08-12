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
echo "COMECOU TRANSFER VALUATE<br/>";
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
      <typ:valuate>
         <ValuationRQ_1>
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
                  <localTime>2021-02-01T09:30:00</localTime>
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
                  <localTime>2021-02-02T10:30:00</localTime>
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
               <!--<childrenAges>8</childrenAges>-->
               <journeyDirection>Roundtrip</journeyDirection>
               <!--Zero or more repetitions:-->
               <names>nombre1</names>
               <names>nombre2</names>
               <numberOfVehicles>1</numberOfVehicles>
               <!--Zero or more repetitions:-->
               <optionalSupplement>
                  <quantity>0</quantity>
                  <suplementCode></suplementCode>
               </optionalSupplement>
               <serviceType>Private</serviceType>
               <vehicleTypeId>1383</vehicleTypeId>
            </vehicleOccupancy>
         </ValuationRQ_1>
      </typ:valuate>
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
$valuateExtendsV22Response = $Body->item(0)->getElementsByTagName("valuateExtendsV22Response");
if ($valuateExtendsV22Response->length > 0) {
    $result = $valuateExtendsV22Response->item(0)->getElementsByTagName("result");
    if ($result->length > 0) {
        $date = $result->item(0)->getElementsByTagName("date");
        if ($date->length > 0) {
            $date = $date->item(0)->nodeValue;
        } else {
            $date = "";
        }
        $checkIn = $result->item(0)->getElementsByTagName("checkIn");
        if ($checkIn->length > 0) {
            $checkIn = $checkIn->item(0)->nodeValue;
        } else {
            $checkIn = "";
        }
        $checkOut = $result->item(0)->getElementsByTagName("checkOut");
        if ($checkOut->length > 0) {
            $checkOut = $checkOut->item(0)->nodeValue;
        } else {
            $checkOut = "";
        }
        $pricingAgencyCode = $result->item(0)->getElementsByTagName("pricingAgencyCode");
        if ($pricingAgencyCode->length > 0) {
            $pricingAgencyCode = $pricingAgencyCode->item(0)->nodeValue;
        } else {
            $pricingAgencyCode = "";
        }
        $status = $result->item(0)->getElementsByTagName("status");
        if ($status->length > 0) {
            $status = $status->item(0)->nodeValue;
        } else {
            $status = "";
        }
        $commision = $result->item(0)->getElementsByTagName("commision");
        if ($commision->length > 0) {
            $commision = $commision->item(0)->nodeValue;
        } else {
            $commision = "";
        }
        $amount = $result->item(0)->getElementsByTagName("amount");
        if ($amount->length > 0) {
            $currencyCode = $amount->item(0)->getElementsByTagName("currencyCode");
            if ($currencyCode->length > 0) {
                $currencyCode = $currencyCode->item(0)->nodeValue;
            } else {
                $currencyCode = "";
            }
            $value = $amount->item(0)->getElementsByTagName("value");
            if ($value->length > 0) {
                $value = $value->item(0)->nodeValue;
            } else {
                $value = "";
            }
        }
        $lines = $result->item(0)->getElementsByTagName("lines");
        if ($lines->length > 0) {
            $code = $lines->item(0)->getElementsByTagName("code");
            if ($code->length > 0) {
                $code = $code->item(0)->nodeValue;
            } else {
                $code = "";
            }
            $description = $lines->item(0)->getElementsByTagName("description");
            if ($description->length > 0) {
                $description = $description->item(0)->nodeValue;
            } else {
                $description = "";
            }
            $priceType = $lines->item(0)->getElementsByTagName("priceType");
            if ($priceType->length > 0) {
                $priceType = $priceType->item(0)->nodeValue;
            } else {
                $priceType = "";
            }
            $quantity = $lines->item(0)->getElementsByTagName("quantity");
            if ($quantity->length > 0) {
                $quantity = $quantity->item(0)->nodeValue;
            } else {
                $quantity = "";
            }
            $status = $lines->item(0)->getElementsByTagName("status");
            if ($status->length > 0) {
                $status = $status->item(0)->nodeValue;
            } else {
                $status = "";
            }
            $from = $lines->item(0)->getElementsByTagName("from");
            if ($from->length > 0) {
                $from = $from->item(0)->nodeValue;
            } else {
                $from = "";
            }
            $to = $lines->item(0)->getElementsByTagName("to");
            if ($to->length > 0) {
                $to = $to->item(0)->nodeValue;
            } else {
                $to = "";
            }
            $price = $lines->item(0)->getElementsByTagName("price");
            if ($price->length > 0) {
                $price_currencyCode = $price->item(0)->getElementsByTagName("currencyCode");
                if ($price_currencyCode->length > 0) {
                    $price_currencyCode = $price_currencyCode->item(0)->nodeValue;
                } else {
                    $price_currencyCode = "";
                }
                $price_value = $price->item(0)->getElementsByTagName("value");
                if ($price_value->length > 0) {
                    $price_value = $price_value->item(0)->nodeValue;
                } else {
                    $price_value = "";
                }
            }
            $total = $lines->item(0)->getElementsByTagName("total");
            if ($total->length > 0) {
                $total_currencyCode = $total->item(0)->getElementsByTagName("currencyCode");
                if ($total_currencyCode->length > 0) {
                    $total_currencyCode = $total_currencyCode->item(0)->nodeValue;
                } else {
                    $total_currencyCode = "";
                }
                $total_value = $total->item(0)->getElementsByTagName("value");
                if ($total_value->length > 0) {
                    $total_value = $total_value->item(0)->nodeValue;
                } else {
                    $total_value = "";
                }
            }
        }
        $remarks = $result->item(0)->getElementsByTagName("remarks");
        if ($remarks->length > 0) {
            for ($i=0; $i < $remarks->length; $i++) { 
                $text = $remarks->item($i)->getElementsByTagName("text");
                if ($text->length > 0) {
                    $text = $text->item(0)->nodeValue;
                } else {
                    $text = "";
                }
                $type = $remarks->item($i)->getElementsByTagName("type");
                if ($type->length > 0) {
                    $type = $type->item(0)->nodeValue;
                } else {
                    $type = "";
                }
            }
        }
        $establishment = $result->item(0)->getElementsByTagName("establishment");
        if ($establishment->length > 0) {
            $id = $establishment->item(0)->getElementsByTagName("id");
            if ($id->length > 0) {
                $id = $id->item(0)->nodeValue;
            } else {
                $id = "";
            }
            $name = $establishment->item(0)->getElementsByTagName("name");
            if ($name->length > 0) {
                $name = $name->item(0)->nodeValue;
            } else {
                $name = "";
            }
            $categoryCode = $establishment->item(0)->getElementsByTagName("categoryCode");
            if ($categoryCode->length > 0) {
                $categoryCode = $categoryCode->item(0)->nodeValue;
            } else {
                $categoryCode = "";
            }
            $categoryName = $establishment->item(0)->getElementsByTagName("categoryName");
            if ($categoryName->length > 0) {
                $categoryName = $categoryName->item(0)->nodeValue;
            } else {
                $categoryName = "";
            }
            $imageUrl = $establishment->item(0)->getElementsByTagName("imageUrl");
            if ($imageUrl->length > 0) {
                $imageUrl = $imageUrl->item(0)->nodeValue;
            } else {
                $imageUrl = "";
            }
            $latitude = $establishment->item(0)->getElementsByTagName("latitude");
            if ($latitude->length > 0) {
                $latitude = $latitude->item(0)->nodeValue;
            } else {
                $latitude = "";
            }
            $longitude = $establishment->item(0)->getElementsByTagName("longitude");
            if ($longitude->length > 0) {
                $longitude = $longitude->item(0)->nodeValue;
            } else {
                $longitude = "";
            }
            $shortDescription = $establishment->item(0)->getElementsByTagName("shortDescription");
            if ($shortDescription->length > 0) {
                $shortDescription = $shortDescription->item(0)->nodeValue;
            } else {
                $shortDescription = "";
            }
            $weight = $establishment->item(0)->getElementsByTagName("weight");
            if ($weight->length > 0) {
                $weight = $weight->item(0)->nodeValue;
            } else {
                $weight = "";
            }
            $address = $establishment->item(0)->getElementsByTagName("address");
            if ($address->length > 0) {
                $address2 = $address->item(0)->getElementsByTagName("address");
                if ($address2->length > 0) {
                    $address2 = $address2->item(0)->nodeValue;
                } else {
                    $address2 = "";
                }
                $cityCode = $address->item(0)->getElementsByTagName("cityCode");
                if ($cityCode->length > 0) {
                    $cityCode = $cityCode->item(0)->nodeValue;
                } else {
                    $cityCode = "";
                }
                $cityName = $address->item(0)->getElementsByTagName("cityName");
                if ($cityName->length > 0) {
                    $cityName = $cityName->item(0)->nodeValue;
                } else {
                    $cityName = "";
                }
                $countryCode = $address->item(0)->getElementsByTagName("countryCode");
                if ($countryCode->length > 0) {
                    $countryCode = $countryCode->item(0)->nodeValue;
                } else {
                    $countryCode = "";
                }
                $countryName = $address->item(0)->getElementsByTagName("countryName");
                if ($countryName->length > 0) {
                    $countryName = $countryName->item(0)->nodeValue;
                } else {
                    $countryName = "";
                }
                $email = $address->item(0)->getElementsByTagName("email");
                if ($email->length > 0) {
                    $email = $email->item(0)->nodeValue;
                } else {
                    $email = "";
                }
                $fax = $address->item(0)->getElementsByTagName("fax");
                if ($fax->length > 0) {
                    $fax = $fax->item(0)->nodeValue;
                } else {
                    $fax = "";
                }
                $addressname = $address->item(0)->getElementsByTagName("name");
                if ($addressname->length > 0) {
                    $addressname = $addressname->item(0)->nodeValue;
                } else {
                    $addressname = "";
                }
                $stateCode = $address->item(0)->getElementsByTagName("stateCode");
                if ($stateCode->length > 0) {
                    $stateCode = $stateCode->item(0)->nodeValue;
                } else {
                    $stateCode = "";
                }
                $stateName = $address->item(0)->getElementsByTagName("stateName");
                if ($stateName->length > 0) {
                    $stateName = $stateName->item(0)->nodeValue;
                } else {
                    $stateName = "";
                }
                $telephone = $address->item(0)->getElementsByTagName("telephone");
                if ($telephone->length > 0) {
                    $telephone = $telephone->item(0)->nodeValue;
                } else {
                    $telephone = "";
                }
                $zipCode = $address->item(0)->getElementsByTagName("zipCode");
                if ($zipCode->length > 0) {
                    $zipCode = $zipCode->item(0)->nodeValue;
                } else {
                    $zipCode = "";
                }
            }
            $comments = $establishment->item(0)->getElementsByTagName("comments");
            if ($comments->length > 0) {
                for ($i=0; $i < $comments->length; $i++) { 
                    $from = $comments->item($i)->getElementsByTagName("from");
                    if ($from->length > 0) {
                        $from = $from->item(0)->nodeValue;
                    } else {
                        $from = "";
                    }
                    $to = $comments->item($i)->getElementsByTagName("to");
                    if ($to->length > 0) {
                        $to = $to->item(0)->nodeValue;
                    } else {
                        $to = "";
                    }
                    $text = $comments->item($i)->getElementsByTagName("text");
                    if ($text->length > 0) {
                        $text = $text->item(0)->nodeValue;
                    } else {
                        $text = "";
                    }
                    $type = $comments->item($i)->getElementsByTagName("type");
                    if ($type->length > 0) {
                        $type = $type->item(0)->nodeValue;
                    } else {
                        $type = "";
                    }
                    $conditions = $comments->item($i)->getElementsByTagName("conditions");
                    if ($conditions->length > 0) {
                        $conditions = $conditions->item(0)->nodeValue;
                    } else {
                        $conditions = "";
                    }
                    $errataType = $comments->item($i)->getElementsByTagName("errataType");
                    if ($errataType->length > 0) {
                        $errataType = $errataType->item(0)->nodeValue;
                    } else {
                        $errataType = "";
                    }
                }
            }
        }
        $occupations = $result->item(0)->getElementsByTagName("occupations");
        if ($occupations->length > 0) {
            $adults = $occupations->item(0)->getElementsByTagName("adults");
            if ($adults->length > 0) {
                $adults = $adults->item(0)->nodeValue;
            } else {
                $adults = "";
            }
            $children = $occupations->item(0)->getElementsByTagName("children");
            if ($children->length > 0) {
                $children = $children->item(0)->nodeValue;
            } else {
                $children = "";
            }
            $boardTypeCode = $occupations->item(0)->getElementsByTagName("boardTypeCode");
            if ($boardTypeCode->length > 0) {
                $boardTypeCode = $boardTypeCode->item(0)->nodeValue;
            } else {
                $boardTypeCode = "";
            }
            $boardTypeName = $occupations->item(0)->getElementsByTagName("boardTypeName");
            if ($boardTypeName->length > 0) {
                $boardTypeName = $boardTypeName->item(0)->nodeValue;
            } else {
                $boardTypeName = "";
            }
            $numberOfDays = $occupations->item(0)->getElementsByTagName("numberOfDays");
            if ($numberOfDays->length > 0) {
                $numberOfDays = $numberOfDays->item(0)->nodeValue;
            } else {
                $numberOfDays = "";
            }
            $numberOfRooms = $occupations->item(0)->getElementsByTagName("numberOfRooms");
            if ($numberOfRooms->length > 0) {
                $numberOfRooms = $numberOfRooms->item(0)->nodeValue;
            } else {
                $numberOfRooms = "";
            }
            $roomTypeCode = $occupations->item(0)->getElementsByTagName("roomTypeCode");
            if ($roomTypeCode->length > 0) {
                $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
            } else {
                $roomTypeCode = "";
            }
            $roomTypeName = $occupations->item(0)->getElementsByTagName("roomTypeName");
            if ($roomTypeName->length > 0) {
                $roomTypeName = $roomTypeName->item(0)->nodeValue;
            } else {
                $roomTypeName = "";
            }
            $fromDate = $occupations->item(0)->getElementsByTagName("fromDate");
            if ($fromDate->length > 0) {
                $fromDate = $fromDate->item(0)->nodeValue;
            } else {
                $fromDate = "";
            }
            $toDate = $occupations->item(0)->getElementsByTagName("toDate");
            if ($toDate->length > 0) {
                $toDate = $toDate->item(0)->nodeValue;
            } else {
                $toDate = "";
            }
            $onRequest = $occupations->item(0)->getElementsByTagName("onRequest");
            if ($onRequest->length > 0) {
                $onRequest = $onRequest->item(0)->nodeValue;
            } else {
                $onRequest = "";
            }
            $description = $occupations->item(0)->getElementsByTagName("description");
            if ($description->length > 0) {
                $description = $description->item(0)->nodeValue;
            } else {
                $description = "";
            }
            $ratePlanCode = $occupations->item(0)->getElementsByTagName("ratePlanCode");
            if ($ratePlanCode->length > 0) {
                $ratePlanCode = $ratePlanCode->item(0)->nodeValue;
            } else {
                $ratePlanCode = "";
            }
            $amount = $occupations->item(0)->getElementsByTagName("amount");
            if ($amount->length > 0) {
                $currencyCode = $amount->item(0)->getElementsByTagName("currencyCode");
                if ($currencyCode->length > 0) {
                    $currencyCode = $currencyCode->item(0)->nodeValue;
                } else {
                    $currencyCode = "";
                }
                $value = $amount->item(0)->getElementsByTagName("value");
                if ($value->length > 0) {
                    $value = $value->item(0)->nodeValue;
                } else {
                    $value = "";
                }
            }
            $rates = $occupations->item(0)->getElementsByTagName("rates");
            if ($rates->length > 0) {
                $rate = $rates->item(0)->getElementsByTagName("rate");
                if ($rate->length > 0) {
                    $rate = $rate->item(0)->nodeValue;
                } else {
                    $rate = "";
                }
            }
            $comments = $occupations->item(0)->getElementsByTagName("comments");
            if ($comments->length > 0) {
                $from = $comments->item(0)->getElementsByTagName("from");
                if ($from->length > 0) {
                    $from = $from->item(0)->nodeValue;
                } else {
                    $from = "";
                }
                $to = $comments->item(0)->getElementsByTagName("to");
                if ($to->length > 0) {
                    $to = $to->item(0)->nodeValue;
                } else {
                    $to = "";
                }
                $text = $comments->item(0)->getElementsByTagName("text");
                if ($text->length > 0) {
                    $text = $text->item(0)->nodeValue;
                } else {
                    $text = "";
                }
                $type = $comments->item(0)->getElementsByTagName("type");
                if ($type->length > 0) {
                    $type = $type->item(0)->nodeValue;
                } else {
                    $type = "";
                }
                $conditions = $comments->item(0)->getElementsByTagName("conditions");
                if ($conditions->length > 0) {
                    $conditions = $conditions->item(0)->nodeValue;
                } else {
                    $conditions = "";
                }
                $errataType = $comments->item(0)->getElementsByTagName("errataType");
                if ($errataType->length > 0) {
                    $errataType = $errataType->item(0)->nodeValue;
                } else {
                    $errataType = "";
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