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
echo "COMECOU AVAILABLE HOTELS<br/>";
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

$url = 'https://test.xtravelsystem.com/public/v1_0rc1/hotelBookingHandler';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/hotel/types">
    <soapenv:Header/>
    <soapenv:Body>
        <typ:availableHotelsByMultiQueryV22>
            <AvailableHotelsByMultiQueryRQV22_1>
                <agencyCode>613</agencyCode>
                <brandCode>1</brandCode>
                <pointOfSaleId>1</pointOfSaleId>
                <checkin>2018-04-22T00:00:00.000Z</checkin>
                <checkout>2018-04-27T00:00:00.000Z</checkout>
                <fromPrice>0</fromPrice>
                <fromRow>0</fromRow>
                <includeEstablishmentData>false</includeEstablishmentData>
                <language>en</language>
                <maxRoomCombinationsPerEstablishment>30</maxRoomCombinationsPerEstablishment>
                <numRows>100</numRows>
                <occupancies>
                    <adults>2</adults>
                    <children>0</children>
                    <numberOfRooms>1</numberOfRooms>
                </occupancies>
                <onlyOnline>true</onlyOnline>
                <orderBy/>
                <productCode/>
                <toPrice>999999</toPrice>
                <establishmentId>245479</establishmentId>
                <extendedLogin>
                    <channel>B2C</channel>
                    <loginCountry>ES</loginCountry>
                    <mainNationality>spain</mainNationality>
                </extendedLogin>
                <coordinates>
                    <latitude>39.55608</latitude>
                    <longitude>2.6221652</longitude>
                    <radius>500</radius>
                </coordinates>
                <coordinates>
                    <latitude>39.555206</latitude>
                    <longitude>2.6202126</longitude>
                    <radius>500</radius>
                </coordinates>
                <coordinates>
                    <latitude>39.79546</latitude>
                    <longitude>2.6973152</longitude>
                    <radius>500</radius>
                </coordinates>
                <paxNationalities>
                    <nationality/>
                </paxNationalities>
            </AvailableHotelsByMultiQueryRQV22_1>
        </typ:availableHotelsByMultiQueryV22>
    </soapenv:Body>
</soapenv:Envelope>';

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
$availableHotelsByMultiQueryV22Response = $Body->item(0)->getElementsByTagName("availableHotelsByMultiQueryV22Response");
if ($availableHotelsByMultiQueryV22Response->length > 0) {
    $result = $availableHotelsByMultiQueryV22Response->item(0)->getElementsByTagName("result");
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
        $pricingAgencyCode = $result->item(0)->getElementsByTagName("pricingAgencyCode");
        if ($pricingAgencyCode->length > 0) {
            $pricingAgencyCode = $pricingAgencyCode->item(0)->nodeValue;
        } else {
            $pricingAgencyCode = "";
        }
        $availableHotels = $result->item(0)->getElementsByTagName("availableHotels");
        if ($availableHotels->length > 0) {
            $moreCombinations = $availableHotels->item(0)->getElementsByTagName("moreCombinations");
            if ($moreCombinations->length > 0) {
                $moreCombinations = $moreCombinations->item(0)->nodeValue;
            } else {
                $moreCombinations = "";
            }
            $establishment = $availableHotels->item(0)->getElementsByTagName("establishment");
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
            $roomCombinations = $availableHotels->item(0)->getElementsByTagName("roomCombinations");
            if ($roomCombinations->length > 0) {
                for ($j=0; $j < $roomCombinations->length; $j++) { 
                    $rooms = $roomCombinations->item($j)->getElementsByTagName("rooms");
                    if ($rooms->length > 0) {
                        $adults = $rooms->item(0)->getElementsByTagName("adults");
                        if ($adults->length > 0) {
                            $adults = $adults->item(0)->nodeValue;
                        } else {
                            $adults = "";
                        }
                        $children = $rooms->item(0)->getElementsByTagName("children");
                        if ($children->length > 0) {
                            $children = $children->item(0)->nodeValue;
                        } else {
                            $children = "";
                        }
                        $priceOfFirstNight = $rooms->item(0)->getElementsByTagName("priceOfFirstNight");
                        if ($priceOfFirstNight->length > 0) {
                            $priceOfFirstNight = $priceOfFirstNight->item(0)->nodeValue;
                        } else {
                            $priceOfFirstNight = "";
                        }
                        $quantity = $rooms->item(0)->getElementsByTagName("quantity");
                        if ($quantity->length > 0) {
                            $quantity = $quantity->item(0)->nodeValue;
                        } else {
                            $quantity = "";
                        }
                        $typeCode = $rooms->item(0)->getElementsByTagName("typeCode");
                        if ($typeCode->length > 0) {
                            $typeCode = $typeCode->item(0)->nodeValue;
                        } else {
                            $typeCode = "";
                        }
                        $typeName = $rooms->item(0)->getElementsByTagName("typeName");
                        if ($typeName->length > 0) {
                            $typeName = $typeName->item(0)->nodeValue;
                        } else {
                            $typeName = "";
                        }
                        $typeCategoryCode = $rooms->item(0)->getElementsByTagName("typeCategoryCode");
                        if ($typeCategoryCode->length > 0) {
                            $typeCategoryCode = $typeCategoryCode->item(0)->nodeValue;
                        } else {
                            $typeCategoryCode = "";
                        }
                        $typeCategoryName = $rooms->item(0)->getElementsByTagName("typeCategoryName");
                        if ($typeCategoryName->length > 0) {
                            $typeCategoryName = $typeCategoryName->item(0)->nodeValue;
                        } else {
                            $typeCategoryName = "";
                        }
                    }
                    $prices = $roomCombinations->item($j)->getElementsByTagName("prices");
                    if ($prices->length > 0) {
                        for ($jAux=0; $jAux < $prices->length; $jAux++) { 
                            $boardCategoryCode = $prices->item($jAux)->getElementsByTagName("boardCategoryCode");
                            if ($boardCategoryCode->length > 0) {
                                $boardCategoryCode = $boardCategoryCode->item(0)->nodeValue;
                            } else {
                                $boardCategoryCode = "";
                            }
                            $boardTypeCode = $prices->item($jAux)->getElementsByTagName("boardTypeCode");
                            if ($boardTypeCode->length > 0) {
                                $boardTypeCode = $boardTypeCode->item(0)->nodeValue;
                            } else {
                                $boardTypeCode = "";
                            }
                            $boardTypeName = $prices->item($jAux)->getElementsByTagName("boardTypeName");
                            if ($boardTypeName->length > 0) {
                                $boardTypeName = $boardTypeName->item(0)->nodeValue;
                            } else {
                                $boardTypeName = "";
                            }
                            $offer = $prices->item($jAux)->getElementsByTagName("offer");
                            if ($offer->length > 0) {
                                $offer = $offer->item(0)->nodeValue;
                            } else {
                                $offer = "";
                            }
                            $onRequest = $prices->item($jAux)->getElementsByTagName("onRequest");
                            if ($onRequest->length > 0) {
                                $onRequest = $onRequest->item(0)->nodeValue;
                            } else {
                                $onRequest = "";
                            }
                            $amount = $prices->item($jAux)->getElementsByTagName("amount");
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
                            $roomPrices = $prices->item($jAux)->getElementsByTagName("roomPrices");
                            if ($roomPrices->length > 0) {
                                $paxes = $roomPrices->item(0)->getElementsByTagName("paxes");
                                if ($paxes->length > 0) {
                                    $paxes = $paxes->item(0)->nodeValue;
                                } else {
                                    $paxes = "";
                                }
                                $price = $roomPrices->item(0)->getElementsByTagName("price");
                                if ($price->length > 0) {
                                    $price = $price->item(0)->nodeValue;
                                } else {
                                    $price = "";
                                }
                                $pricePerPaxAndNight = $roomPrices->item(0)->getElementsByTagName("pricePerPaxAndNight");
                                if ($pricePerPaxAndNight->length > 0) {
                                    $pricePerPaxAndNight = $pricePerPaxAndNight->item(0)->nodeValue;
                                } else {
                                    $pricePerPaxAndNight = "";
                                }
                                $pricePerRoomAndNight = $roomPrices->item(0)->getElementsByTagName("pricePerRoomAndNight");
                                if ($pricePerRoomAndNight->length > 0) {
                                    $pricePerRoomAndNight = $pricePerRoomAndNight->item(0)->nodeValue;
                                } else {
                                    $pricePerRoomAndNight = "";
                                }
                                $typeCode = $roomPrices->item(0)->getElementsByTagName("typeCode");
                                if ($typeCode->length > 0) {
                                    $typeCode = $typeCode->item(0)->nodeValue;
                                } else {
                                    $typeCode = "";
                                }
                                $typeName = $roomPrices->item(0)->getElementsByTagName("typeName");
                                if ($typeName->length > 0) {
                                    $typeName = $typeName->item(0)->nodeValue;
                                } else {
                                    $typeName = "";
                                }
                                $description = $roomPrices->item(0)->getElementsByTagName("description");
                                if ($description->length > 0) {
                                    $description = $description->item(0)->nodeValue;
                                } else {
                                    $description = "";
                                }
                                $ratePlanCode = $roomPrices->item(0)->getElementsByTagName("ratePlanCode");
                                if ($ratePlanCode->length > 0) {
                                    $ratePlanCode = $ratePlanCode->item(0)->nodeValue;
                                } else {
                                    $ratePlanCode = "";
                                }
                                $typeCategoryCode = $roomPrices->item(0)->getElementsByTagName("typeCategoryCode");
                                if ($typeCategoryCode->length > 0) {
                                    $typeCategoryCode = $typeCategoryCode->item(0)->nodeValue;
                                } else {
                                    $typeCategoryCode = "";
                                }
                                $typeCategoryName = $roomPrices->item(0)->getElementsByTagName("typeCategoryName");
                                if ($typeCategoryName->length > 0) {
                                    $typeCategoryName = $typeCategoryName->item(0)->nodeValue;
                                } else {
                                    $typeCategoryName = "";
                                }
                                $rates = $roomPrices->item(0)->getElementsByTagName("rates");
                                if ($rates->length > 0) {
                                    $rate = $rates->item(0)->getElementsByTagName("rate");
                                    if ($rate->length > 0) {
                                        $rate = $rate->item(0)->nodeValue;
                                    } else {
                                        $rate = "";
                                    }
                                }
                                $comments = $roomPrices->item(0)->getElementsByTagName("comments");
                                if ($comments->length > 0) {
                                    for ($jAux2=0; $jAux2 < $comments->length; $jAux2++) { 
                                        $from = $comments->item($jAux2)->getElementsByTagName("from");
                                        if ($from->length > 0) {
                                            $from = $from->item(0)->nodeValue;
                                        } else {
                                            $from = "";
                                        }
                                        $to = $comments->item($jAux2)->getElementsByTagName("to");
                                        if ($to->length > 0) {
                                            $to = $to->item(0)->nodeValue;
                                        } else {
                                            $to = "";
                                        }
                                        $text = $comments->item($jAux2)->getElementsByTagName("text");
                                        if ($text->length > 0) {
                                            $text = $text->item(0)->nodeValue;
                                        } else {
                                            $text = "";
                                        }
                                        $type = $comments->item($jAux2)->getElementsByTagName("type");
                                        if ($type->length > 0) {
                                            $type = $type->item(0)->nodeValue;
                                        } else {
                                            $type = "";
                                        }
                                        $conditions = $comments->item($jAux2)->getElementsByTagName("conditions");
                                        if ($conditions->length > 0) {
                                            $conditions = $conditions->item(0)->nodeValue;
                                        } else {
                                            $conditions = "";
                                        }
                                        $errataType = $comments->item($jAux2)->getElementsByTagName("errataType");
                                        if ($errataType->length > 0) {
                                            $errataType = $errataType->item(0)->nodeValue;
                                        } else {
                                            $errataType = "";
                                        }
                                    }
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