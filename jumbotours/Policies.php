<?php
// Jumbo Tours Group
error_log("\r\nStart Jumbo Tours Group Policies\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\I18n\Translator\Translator;
use Laminas\Http\Client;
use Laminas\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$total = 0;
error_log("\r\n COMECOU POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
try {
    $db = new \Laminas\Db\Adapter\Adapter($config);
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_jtg where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $lang = $searchsettings['lang'];
    $currency = $searchsettings['currency'];
    $from = $searchsettings['from'];
    $to = $searchsettings['to'];
    $destination = $searchsettings['destination'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $index = $searchsettings['index'];
    $ipaddress = $searchsettings['ipaddress'];
    $nationality = $searchsettings['nationality'];
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$branch_filter = "";
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
    $sql = "select value from settings where name='jtgDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='jumbotoursgroupHotelslogin' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelslogin = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelspassword' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='jumbotoursgroupHotelsagencycode' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelsagencycode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsbrandcode' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupHotelsbrandcode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsServiceURL' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsServiceURL = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsMarkup' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
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
$sql = "select value from settings where name='jumbotoursgroupHotelspointofsale' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelspointofsale = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsParallelSearch' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsParallelSearch = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsCompany' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsCompany = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsSearchSortorder' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsb2cMarkup' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsb2cMarkup = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsTimeout' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
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
$sql = "select value from settings where name='jumbotoursgroupHotelsaffiliates_id' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='jumbotoursgroupHotelsbranches_id' and affiliate_id=$affiliate_id_jtg" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $jumbotoursgroupHotelsbranches_id = $row['value'];
}

$breakdown = array();
for ($w = 0; $w < count($quoteid); $w ++) {
    $outputArray = array();
    $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
    foreach ($arrIt as $sub) {
        $subArray = $arrIt->getSubIterator();
        if (isset($quoteid[$w])) {
            if (isset($subArray['quoteid'])) {
                if ($subArray['quoteid'] === $quoteid[$w]) {
                    $outputArray[] = iterator_to_array($subArray);
                    $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                        ->key();
                }
            }
        }
    }
    if (! is_array($outputArray)) {
        $response['error'] = "Unable to handle request #3";
        return false;
    } else {
        array_push($breakdown, $outputArray);
    }
}

$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');

$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['hotelid'];
            $scode = $value['shid'];
            $hotel_code = $value['shid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        $ratePlanCode = $value['ratePlanCode'];
        $adults = $value['adults'];
        $children = $value['children'];
        $numberOfRooms = 1;

        $raw = '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/hotel/types">
            <soapenv:Header/>
            <soapenv:Body>
                <typ:valuateExtendsV22>
                    <ValuationRQV22_1>
                        <agencyCode>' . $jumbotoursgroupHotelsagencycode . '</agencyCode>
                        <brandCode>' . $jumbotoursgroupHotelsbrandcode . '</brandCode>
                        <pointOfSaleId>1</pointOfSaleId>
                        <checkin>' . $from_date . 'T10:00:00.000Z</checkin>
                        <checkout>' . $to_date . 'T10:00:00.000Z</checkout>
                        <establishmentId>' . $shid . '</establishmentId>
                        <language>en</language>
                        <occupancies>
                            <adults>' . $adults . '</adults>
                            <children>' . $children . '</children>';
                            if ($children > 0) {
                                for ($i=0; $i < $children; $i++) { 
                                    $raw = $raw . '<childrenAges>' . $children_ages[$i] . '</childrenAges>';
                                }
                            }
        $raw = $raw . '<numberOfRooms>' . $numberOfRooms . '</numberOfRooms>
                            <ratePlanCode>' . $ratePlanCode . '</ratePlanCode>
                        </occupancies>
                        <extendedLogin>
                            <channel>B2C</channel>
                            <loginCountry>' . $sourceMarket . '</loginCountry>
                            <mainNationality>' . $sourceMarket . '</mainNationality>
                        </extendedLogin>
                        <paxNationalities>
                            <nationality></nationality>
                        </paxNationalities>
                    </ValuationRQV22_1>
                </typ:valuateExtendsV22>
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
        curl_setopt($ch, CURLOPT_URL, $jumbotoursgroupHotelsServiceURL . 'public/v1_0rc1/hotelBookingHandler');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, $jumbotoursgroupHotelsTimeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        // error_log("\r\n Response: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_jtg');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $jumbotoursgroupHotelsServiceURL,
                'sqlcontext' => $response2,
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
        $inputDoc->loadXML($response2);
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
                        $commentsfrom = $comments->item(0)->getElementsByTagName("from");
                        if ($commentsfrom->length > 0) {
                            $commentsfrom = $commentsfrom->item(0)->nodeValue;
                        } else {
                            $commentsfrom = "";
                        }
                        $commentsto = $comments->item(0)->getElementsByTagName("to");
                        if ($commentsto->length > 0) {
                            $commentsto = $commentsto->item(0)->nodeValue;
                        } else {
                            $commentsto = "";
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
                    $cancellationPoliciesArray = array();
                    $count2 = 0;
                    $cancellationComments = $occupations->item(0)->getElementsByTagName("cancellationComments");
                    if ($cancellationComments->length > 0) {
                        for ($j=0; $j < $cancellationComments->length; $j++) { 
                            $cancellationComments_from = $cancellationComments->item($j)->getElementsByTagName("from");
                            if ($cancellationComments_from->length > 0) {
                                $cancellationComments_from = $cancellationComments_from->item(0)->nodeValue;
                            } else {
                                $cancellationComments_from = "";
                            }
                            $cancellationComments_to = $cancellationComments->item($j)->getElementsByTagName("to");
                            if ($cancellationComments_to->length > 0) {
                                $cancellationComments_to = $cancellationComments_to->item(0)->nodeValue;
                            } else {
                                $cancellationComments_to = "";
                            }
                            $text = $cancellationComments->item($j)->getElementsByTagName("text");
                            if ($text->length > 0) {
                                $text = $text->item(0)->nodeValue;
                            } else {
                                $text = "";
                            }
                            $type = $cancellationComments->item($j)->getElementsByTagName("type");
                            if ($type->length > 0) {
                                $type = $type->item(0)->nodeValue;
                            } else {
                                $type = "";
                            }
                            $cancellationPoliciesArray[$count2]['from'] = $cancellationComments_from;
                            $cancellationPoliciesArray[$count2]['to'] = $cancellationComments_to;
                            $cancellationPoliciesArray[$count2]['text'] = $text;
                            $cancellationPoliciesArray[$count2]['type'] = $type;
                            $count2 = $count2 + 1;
                        }
                    }
                }
            }
        }
        //
        // Policies
        //
        $item['code'] = $value['shid'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nett'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['RoomTypeCode'] = $value['room_type'];
        $item['RoomType'] = $value['room_type'];
        $item['RoomDescription'] = $value['room_description'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        
        $promotion = $value['specialdescription'];
        $cancellationPolicy = $cancellationPoliciesArray[0]['text'];
        $cancel = explode('-', $cancellationPolicy);
        $days = $cancel[0];
        $percent = $cancel[1];
        $daystext = $days . " days";
        $from2 = date('Y-m-d', strtotime($from));
        $Date2 = date('Y-m-d', strtotime("- " . $daystext, strtotime($from2)));
        $cancelpolicy_deadline = strftime("%a, %e %b %Y", strtotime($Date2));
        $cancelpolicy = 'If you Cancel a booking before ' . $cancelpolicy_deadline . ' has a ' . $percent . ' of total booking amount penalty.';

        // $item['nonrefundable'] = true;
        $item['cancelpolicy'] = $translator->translate($cancelpolicy);
        $item['cancelpolicy_details'] = $translator->translate($cancelpolicy);
        $item['cancelpolicy_deadline'] = $cancelpolicy_deadline;
        $item['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$db = new \Laminas\Db\Adapter\Adapter($config);
$hotel = array();
$sql = "select sid from xmlhotels_mjtg where sid='" . $shid . "' and hid=" . $hid;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$db = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $db2 = new \Laminas\Db\Adapter\Adapter($config);
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db2->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $db2->getDriver()
        ->getConnection()
        ->disconnect();
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$images = array();
try {
    $db = new \Laminas\Db\Adapter\Adapter($config);
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $item = array();
            $item['url'] = "//world-wide-web-servers.com/static/hotels/" . $row->url;
            $item['description'] = $row->description;
            array_push($images, $item);
        }
    }
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$response['code'] = $vector['code'];
?>