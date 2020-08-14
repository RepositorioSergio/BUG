<?php
// Jumbo Tours Group
error_log("\r\nStart Jumbo Tours Group Search\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$jumbo = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
$sql = "select name, country_id, zone_id,city_xml73, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml73 = $row_settings["city_xml73"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml73 = "";
}
$affiliate_id = 0;
$sql = "select value from settings where name='enablejumbotoursgroupHotels' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_jtg = $affiliate_id;
} else {
    $affiliate_id_jtg = 0;
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='jtgDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='jumbotoursgroupHotelslogin' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelslogin = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelspassword' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='jumbotoursgroupHotelsagencycode' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelsagencycode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsbrandcode' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelsbrandcode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsServiceURL' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsServiceURL = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsMarkup' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelsMarkup = (double) $row_settings['value'];
} else {
    $jumbotoursgroupHotelsMarkup = 0;
}
$sql = "select value from settings where name='jumbotoursgroupHotelspointofsale' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelspointofsale = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsParallelSearch' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsParallelSearch = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsCompany' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsCompany = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsSearchSortorder' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsb2cMarkup' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsb2cMarkup = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsTimeout' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsTimeout = (int)$row['value'];
} else {
    $jumbotoursgroupHotelsTimeout = 0;
}
$sql = "select value from settings where name='jumbotoursgroupHotelspointofsale' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelspointofsale = (int)$row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsaffiliates_id' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsbranches_id' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsbranches_id = $row['value'];
}

if ($jumbotoursgroupHotelsServiceURL != "" and $jumbotoursgroupHotelslogin != "" and $jumbotoursgroupHotelspassword != "") {
    $childrenAges = array();
    $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/hotel/types">
    <soapenv:Header/>
    <soapenv:Body>
    <typ:availableHotelsByMultiQueryV22>
        <AvailableHotelsByMultiQueryRQV22_1>
            <agencyCode>' . $jumbotoursgroupHotelsagencycode . '</agencyCode>
            <brandCode>' . $jumbotoursgroupHotelsbrandcode . '</brandCode>
            <pointOfSaleId>' . $jumbotoursgroupHotelspointofsale . '</pointOfSaleId>
            <checkin>' . strftime("%Y-%m-%d", $from) . 'T10:00:00.000Z</checkin>
            <checkout>' . strftime("%Y-%m-%d", $to) . 'T10:00:00.000Z</checkout>
            <fromPrice>0</fromPrice>
            <fromRow>0</fromRow>
            <includeEstablishmentData>false</includeEstablishmentData>
            <language>en</language>
            <maxRoomCombinationsPerEstablishment>30</maxRoomCombinationsPerEstablishment>
            <numRows>100</numRows>';
            for ($r=0; $r < count($selectedAdults); $r++) { 
                $raw = $raw . '<occupancies>
                    <adults>' . $selectedAdults[$r] . '</adults>
                    <children>' . $selectedChildren[$r] . '</children>';
                if ($selectedChildren[$r] > 0) {
                    for ($z=0; $z < $selectedChildren[$r]; $z++) { 
                        $raw = $raw . '<childrenAges>' . $selectedChildrenAges[$r][$z] . '</childrenAges>';
                        array_push($childrenAges, $selectedChildrenAges[$r][$z]);
                    }
                }
                $raw = $raw . '<numberOfRooms>' . $rooms . '</numberOfRooms>
                </occupancies>';
            }
    $raw .= '<onlyOnline>true</onlyOnline>
            <orderBy/>
            <productCode/>
            <toPrice>999999</toPrice>
            <cityCode>' . $city_xml73 . '</cityCode>
            <extendedLogin>
                <channel>B2C</channel>
                <loginCountry>' . $sourceMarket . '</loginCountry>
                <mainNationality>' . $sourceMarket . '</mainNationality>
            </extendedLogin>
            <paxNationalities>
                <nationality>' . $sourceMarket . '</nationality>
            </paxNationalities>
        </AvailableHotelsByMultiQueryRQV22_1>
    </typ:availableHotelsByMultiQueryV22>
    </soapenv:Body>
    </soapenv:Envelope>';
    // error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");

    $headers = array(
        "Content-type: text/xml",
        "Accept-Encoding: gzip, deflate",
        "Content-length: " . strlen($raw)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $jumbotoursgroupHotelsServiceURL . 'public/v1_0rc1/hotelBookingHandler');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, $jumbotoursgroupHotelsTimeout);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    // if ($response === false) {
    // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    // } else {
    // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    // }
    curl_close($ch);
    $endTime = microtime();
    // error_log("\r\n Response Jumbo: $response \r\n", 3, "/srv/www/htdocs/error_log");
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_jtg');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $jumbotoursgroupHotelsServiceURL . $raw,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
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
                for ($k=0; $k < $availableHotels->length; $k++) { 
                    $moreCombinations = $availableHotels->item($k)->getElementsByTagName("moreCombinations");
                    if ($moreCombinations->length > 0) {
                        $moreCombinations = $moreCombinations->item(0)->nodeValue;
                    } else {
                        $moreCombinations = "";
                    }
                    $establishment = $availableHotels->item($k)->getElementsByTagName("establishment");
                    if ($establishment->length > 0) {
                        $id = $establishment->item(0)->getElementsByTagName("id");
                        if ($id->length > 0) {
                            $id = $id->item(0)->nodeValue;
                        } else {
                            $id = "";
                        }
                        $shid = $id;
                        $sfilter[] = " sid='$id' ";
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
                                $commentsfrom = $comments->item($i)->getElementsByTagName("from");
                                if ($commentsfrom->length > 0) {
                                    $commentsfrom = $commentsfrom->item(0)->nodeValue;
                                } else {
                                    $commentsfrom = "";
                                }
                                $commentsto = $comments->item($i)->getElementsByTagName("to");
                                if ($commentsto->length > 0) {
                                    $commentsto = $commentsto->item(0)->nodeValue;
                                } else {
                                    $commentsto = "";
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
                    $roomCombinations = $availableHotels->item($k)->getElementsByTagName("roomCombinations");
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
                                        $cancellationPoliciesArray = array();
                                        $count2 = 0;
                                        $comments = $roomPrices->item(0)->getElementsByTagName("comments");
                                        if ($comments->length > 0) {
                                            for ($jAux2=0; $jAux2 < $comments->length; $jAux2++) { 
                                                $commentsfrom = $comments->item($jAux2)->getElementsByTagName("from");
                                                if ($commentsfrom->length > 0) {
                                                    $commentsfrom = $commentsfrom->item(0)->nodeValue;
                                                } else {
                                                    $commentsfrom = "";
                                                }
                                                $commentsto = $comments->item($jAux2)->getElementsByTagName("to");
                                                if ($commentsto->length > 0) {
                                                    $commentsto = $commentsto->item(0)->nodeValue;
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
                                                $cancellationPoliciesArray[$count2]['from'] = $from;
                                                $cancellationPoliciesArray[$count2]['to'] = $to;
                                                $cancellationPoliciesArray[$count2]['text'] = $text;
                                                $cancellationPoliciesArray[$count2]['type'] = $type;
                                                $count2 = $count2 + 1;
                                            }
                                        }
                                    }

                                    $total = $price;
                                    $nettotal = $total;

                                    $zRooms = 0;
                                    if (is_array($tmp[$shid])) {
                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                    } else {
                                        $baseCounterDetails = 0;
                                    }
                                    
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $Name;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $id;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $typeCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-73";
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $typeName;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $description;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $typeCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ratePlanCode'] = $ratePlanCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['childrenAges'] = $childrenAges;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
                                    if ($jumbotoursgroupHotelsMarkup != 0) {
                                        $total = $total + (($total * $jumbotoursgroupHotelsMarkup) / 100);
                                    }
                                    // Geo target markup
                                    if ($internalmarkup != 0) {
                                        $total = $total + (($total * $internalmarkup) / 100);
                                    }
                                    // Agent markup
                                    if ($agent_markup != 0) {
                                        $total = $total + (($total * $agent_markup) / 100);
                                    }
                                    // Fallback Markup
                                    if ($jumbotoursgroupHotelsMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                        $total = $total + (($total * $HotelsMarkupFallback) / 100);
                                    }
                                    // Agent discount
                                    if ($agent_discount != 0) {
                                        $total = $total - (($total * $agent_discount) / 100);
                                    }
                                    if ($scurrency != "" and $currency != $scurrency) {
                                        $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                                    try {
                                        $sql = "select mapped from board_mapping where description='" . addslashes($boardTypeName) . "'";
                                        $statement = $db->createStatement($sql);
                                        $statement->prepare();
                                        $row_board_mapping = $statement->execute();
                                        $row_board_mapping->buffer();
                                        if ($row_board_mapping->valid()) {
                                            $row_board_mapping = $row_board_mapping->current();
                                            $boardTypeName = $row_board_mapping["mapped"];
                                        }
                                    } catch (\Exception $e) {
                                        $logger = new Logger();
                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                        $logger->addWriter($writer);
                                        $logger->info($e->getMessage());
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($boardTypeName);
                                    $pricebreakdown = array();
                                    $pricebreakdownCount = 0;
                                    $amount = $total / $noOfNights;
                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                        $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                        $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                        $pricebreakdownCount = $pricebreakdownCount + 1;
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currencyCode;
                                    //
                                    // Special
                                    //
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                    //
                                    // Cancel Policies
                                    //
                                    $cancellationPolicy = $cancellationPoliciesArray[2]['text'];
                                    $cancel = explode('-', $cancellationPolicy);
                                    $days = $cancel[0];
                                    $percent = $cancel[1];
                                    $daystext = $days . " days";
                                    $from2 = strftime("%Y-%m-%d", $from);
                                    $Date2 = date('Y-m-d', strtotime("- " . $daystext, strtotime($from2)));
                                    $cancelpolicy_deadline = strftime("%a, %e %b %Y", strtotime($Date2));
                                    $cancelpolicy = 'If you Cancel a booking before ' . $cancelpolicy_deadline . ' has a ' . $percent . ' of total booking amount penalty.';
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy'] = $cancelpolicy;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadline'] = $cancelpolicy_deadline;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails][$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
                                    $count = $count + 1;
                                }
                            }
                        }
                    }
                }
            }
        }
        $jumbo = true;
    }
}
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
if ($jumbo == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mjtg where " . $sfilter;
        // error_log("\r\n SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        $result2->buffer();
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                if (is_array($hotels_array[$row2->hid])) {
                    // Append to original details
                    $tmph = $hotels_array[$row2->hid]['details'];
                    $tmps = $tmp[$row2->sid]['details'];
                    foreach ($tmph as $key => $value) {
                        $last = count($tmph[$key]);
                        foreach ($tmps[$key] as $keyd => $valued) {
                            $tmph[$key][$last] = $valued;
                            $last ++;
                        }
                    }
                    $hotels_array[$row2->hid]['details'] = $tmph;
                } else {
                    $hotels_array[$row2->hid] = $tmp[$row2->sid];
                }
            }
        }
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 73;
        // error_log("\r\n Query: $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_jtg');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_jtg');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
error_log("\r\n End JUMBO \r\n", 3, "/srv/www/htdocs/error_log");
?>