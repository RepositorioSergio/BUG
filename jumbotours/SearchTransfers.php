<?php
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
$db = new \Laminas\Db\Adapter\Adapter($config);
$sorign = $request->getAttribute('origin');
$sorign = explode(":", $sorign);
if ($sorign[1] == "ATLAS") {
    $packages_xml04_origin_transfer_type = "ATLAS";
    $packages_xml04_origin = $sorign[0];
    $direction = "DEPARTURE";
} else {
    $sql = "select airport_code, transfer_type from transfers where id=" . (int) $request->getAttribute('origin');
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    $transfertype = "";
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $packages_xml04_origin = $row_settings["airport_code"];
        $packages_xml04_origin_transfer_type = $row_settings["transfer_type"];
        if ($packages_xml04_origin_transfer_type == "AP") {
            $packages_xml04_origin_transfer_type = "IATA";
            $transfertype = "FLIGHT";
        } elseif ($packages_xml04_origin_transfer_type == "TRAIN") {
            $packages_xml04_origin_transfer_type = "STATION";
            $transfertype = "STATION";
        } elseif ($packages_xml04_origin_transfer_type == "PORT") {
            $packages_xml04_origin_transfer_type = "PORT";
            $transfertype = "PORT";
        }
    }
    $direction = "ARRIVAL";
}
$destination = $request->getAttribute('destination');
if (! is_numeric($destination)) {
    $tmp = explode(":", $destination);
    switch ($tmp[1]) {
        case "ATLAS":
            $packages_xml04_destination = $tmp[0];
            $packages_xml04_destination_transfer_type = "ATLAS";
            break;
        default:
            break;
    }
} else {
    $sql = "select airport_code, transfer_type from transfers where id=" . (int) $request->getAttribute('destination');
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $packages_xml04_destination = $row_settings["airport_code"];
        $packages_xml04_destination_transfer_type = $row_settings["transfer_type"];
        if ($packages_xml04_destination_transfer_type == "AP") {
            if ($transfertype == "") {
                $transfertype = "FLIGHT";
            }
            $packages_xml04_destination_transfer_type = "IATA";
        } elseif ($packages_xml04_destination_transfer_type == "TRAIN") {
            $packages_xml04_destination_transfer_type = "STATION";
            if ($transfertype == "") {
                $transfertype = "STATION";
            }
        } elseif ($packages_xml04_destination_transfer_type == "PORT") {
            $packages_xml04_destination_transfer_type = "PORT";
            if ($transfertype == "") {
                $transfertype = "PORT";
            }
        }
    }
}
$affiliate_id = 0;
$sql = "select value from settings where name='enablejumbotoursgroup' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_jtg = $affiliate_id;
} else {
    $affiliate_id_jtg = 0;
}
$sql = "select value from settings where name='jumbotoursgrouplogin' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgrouplogin = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupPassword' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='jumbotoursgroupmarkup' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupmarkup = (double) $row_settings['value'];
} else {
    $jumbotoursgroupmarkup = 0;
}
// URL
$sql = "select value from settings where name='jumbotoursgroupserviceurl' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupserviceurl = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupagencycode' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupagencycode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupbrandcode' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupbrandcode = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgrouppointofsale' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgrouppointofsale = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupSearchSortOrder' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupSearchSortOrder = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupemail' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupemail = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupb2cmarkup' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupb2cmarkup = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupTimeout' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupTimeout = (int)$row_settings['value'];
} else {
    $jumbotoursgroupTimeout = 0;
}
$sql = "select value from settings where name='jumbotoursgroupaffiliates_id' and affiliate_id=$affiliate_id_jtg";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $jumbotoursgroupaffiliates_id = $row_settings['value'];
}
$sql = "select value from settings where name='jumbotoursgroupPassword' and affiliate_id=$affiliate_id_jtg";
$statement_hotelbeds = $db->createStatement($sql);
$statement_hotelbeds->prepare();
try {
    $statement_hotelbeds = $statement_hotelbeds->execute();
    $statement_hotelbeds->buffer();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($statement_hotelbeds->valid()) {
    $rs_tmp = $statement_hotelbeds->current();
    $sharedSecret = base64_decode($rs_tmp['value']);
} else {
    $sharedSecret = "";
}
if ($jumbotoursgroupserviceurl != "" and $jumbotoursgrouplogin != "" and $jumbotoursgroupPassword != "") {
    //
    // GIATA
    // ATLAS
    // IATA
    // PORT
    // STATION
    //
    // Adults / Child / Infants
    //
    // Return:
    // $url = $hotelbedsTransfersserviceURL . '/availability/en/from/IATA/' . $packages_xml04_origin . '/to/GIATA/' . $packages_xml04_destination . '/' . strftime("%Y-%m-%d", $from) . 'T' . $arrtime . ':00/' . strftime("%Y-%m-%d", $to) . 'T' . $rettime . ':00/' . $adults . '/' . $children . '/0';
    //
    // One way:
    //
    $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/transfer/types">
        <soapenv:Header/>
        <soapenv:Body>
        <typ:availableTransfersV14>
            <AvailabilityRQV13_1>
                <agencyCode>' . $jumbotoursgroupagencycode . '</agencyCode>
                <brandCode>' . $jumbotoursgroupbrandcode . '</brandCode>
                <pointOfSaleId>' . $jumbotoursgrouppointofsale . '</pointOfSaleId>
                <adults>' . $adults . '</adults>
                <children>' . $children . '</children>';
                for ($z=0; $z < $children; $z++) { 
                    $raw .= '<childrenAges>' . $children_ages[$z] . '</childrenAges>';
                }
    $raw .=    '<fromAirToHotelJourney>
                    <airportId>3057</airportId>
                    <establishmentId>4704</establishmentId>
                    <flightInfo>
                    <date>' . strftime("%Y-%m-%d", $from) . 'T' . $arrtime . ':00.000Z</date>
                    <flightNumber>
                        <company>UX</company>
                        <fullNumber>2525</fullNumber>
                        <number>2525</number>
                    </flightNumber>
                    <from>BCN</from>
                    <localTime>' . $arrtime . '</localTime>
                    <to>PMI</to>
                    </flightInfo>
                </fromAirToHotelJourney>
                <fromRow>0</fromRow>
                <journeyDirection>Going</journeyDirection>
                <language>en</language>
                <numRows>100</numRows>
            </AvailabilityRQV13_1>
        </typ:availableTransfersV14>
        </soapenv:Body>
    </soapenv:Envelope>';
    error_log("\r\n Transfers Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    $url_in = $raw;

    $startTime = microtime();
    $headers = array(
        "Content-type: text/xml",
        "Accept-Encoding: gzip, deflate",
        "Content-length: " . strlen($raw)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_URL, $jumbotoursgroupserviceurl . 'public/v1_0rc1/transferBookingHandler');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, $jumbotoursgroupTimeout);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $response_in = $response;
    $error = curl_error($ch);
    curl_close($ch);
    $endTime = microtime();
    error_log("\r\n Transfers Response: $response\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_jtgtransfers');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchTransfers.php',
            'errorline' => 0,
            'errormessage' => $jumbotoursgroupserviceurl,
            'sqlcontext' => (string) $response,
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
    if ($response != "") {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $availableTransfersV14Response = $Body->item(0)->getElementsByTagName("availableTransfersV14Response");
        if ($availableTransfersV14Response->length > 0) {
            $result = $availableTransfersV14Response->item(0)->getElementsByTagName("result");
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
                if ($availableTransfers->length > 0) {
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
                        $fromAir_airportId = $fromAirToHotelJourney->item(0)->getElementsByTagName("airportId");
                        if ($fromAir_airportId->length > 0) {
                            $fromAir_airportId = $fromAir_airportId->item(0)->nodeValue;
                        } else {
                            $fromAir_airportId = "";
                        }
                        $fromAir_establishmentId = $fromAirToHotelJourney->item(0)->getElementsByTagName("establishmentId");
                        if ($fromAir_establishmentId->length > 0) {
                            $fromAir_establishmentId = $fromAir_establishmentId->item(0)->nodeValue;
                        } else {
                            $fromAir_establishmentId = "";
                        }
                        $flightInfo = $fromAirToHotelJourney->item(0)->getElementsByTagName("flightInfo");
                        if ($flightInfo->length > 0) {
                            $fromAir_date = $flightInfo->item(0)->getElementsByTagName("date");
                            if ($fromAir_date->length > 0) {
                                $fromAir_date = $fromAir_date->item(0)->nodeValue;
                            } else {
                                $fromAir_date = "";
                            }
                            $date = str_replace('-', '/', $fromAir_date);
                            $dateDep = date("d/m/Y", strtotime($date));
                            $timeDep = date("H:i", strtotime($date));
                            $fromAir_from = $flightInfo->item(0)->getElementsByTagName("from");
                            if ($fromAir_from->length > 0) {
                                $fromAir_from = $fromAir_from->item(0)->nodeValue;
                            } else {
                                $fromAir_from = "";
                            }
                            $fromAir_to = $flightInfo->item(0)->getElementsByTagName("to");
                            if ($fromAir_to->length > 0) {
                                $fromAir_to = $fromAir_to->item(0)->nodeValue;
                            } else {
                                $fromAir_to = "";
                            }
                            $flightNumber = $flightInfo->item(0)->getElementsByTagName("flightNumber");
                            if ($flightNumber->length > 0) {
                                $fromAir_company = $flightNumber->item(0)->getElementsByTagName("company");
                                if ($fromAir_company->length > 0) {
                                    $fromAir_company = $fromAir_company->item(0)->nodeValue;
                                } else {
                                    $fromAir_company = "";
                                }
                                $fromAir_fullNumber = $flightNumber->item(0)->getElementsByTagName("fullNumber");
                                if ($fromAir_fullNumber->length > 0) {
                                    $fromAir_fullNumber = $fromAir_fullNumber->item(0)->nodeValue;
                                } else {
                                    $fromAir_fullNumber = "";
                                }
                                $fromAir_number = $flightNumber->item(0)->getElementsByTagName("number");
                                if ($fromAir_number->length > 0) {
                                    $fromAir_number = $fromAir_number->item(0)->nodeValue;
                                } else {
                                    $fromAir_number = "";
                                }
                            }
                        }
                    }
                    $fromHotelToAirJourney = $availableTransfers->item(0)->getElementsByTagName("fromHotelToAirJourney");
                    if ($fromHotelToAirJourney->length > 0) {
                        $fromHotel_airportId = $fromHotelToAirJourney->item(0)->getElementsByTagName("airportId");
                        if ($fromHotel_airportId->length > 0) {
                            $fromHotel_airportId = $fromHotel_airportId->item(0)->nodeValue;
                        } else {
                            $fromHotel_airportId = "";
                        }
                        $fromHotel_establishmentId = $fromHotelToAirJourney->item(0)->getElementsByTagName("establishmentId");
                        if ($fromHotel_establishmentId->length > 0) {
                            $fromHotel_establishmentId = $fromHotel_establishmentId->item(0)->nodeValue;
                        } else {
                            $fromHotel_establishmentId = "";
                        }
                        $flightInfo = $fromHotelToAirJourney->item(0)->getElementsByTagName("flightInfo");
                        if ($flightInfo->length > 0) {
                            $fromHotel_date = $flightInfo->item(0)->getElementsByTagName("date");
                            if ($fromHotel_date->length > 0) {
                                $fromHotel_date = $fromHotel_date->item(0)->nodeValue;
                            } else {
                                $fromHotel_date = "";
                            }
                            $date2 = str_replace('-', '/', $fromHotel_date);
                            $dateComeBack = date("d/m/Y", strtotime($date2));
                            $timeComeBack = date("H:i", strtotime($date2));
                            $fromHotel_from = $flightInfo->item(0)->getElementsByTagName("from");
                            if ($fromHotel_from->length > 0) {
                                $fromHotel_from = $fromHotel_from->item(0)->nodeValue;
                            } else {
                                $fromHotel_from = "";
                            }
                            $fromHotel_to = $flightInfo->item(0)->getElementsByTagName("to");
                            if ($fromHotel_to->length > 0) {
                                $fromHotel_to = $fromHotel_to->item(0)->nodeValue;
                            } else {
                                $fromHotel_to = "";
                            }
                            $flightNumber = $flightInfo->item(0)->getElementsByTagName("flightNumber");
                            if ($flightNumber->length > 0) {
                                $fromHotel_company = $flightNumber->item(0)->getElementsByTagName("company");
                                if ($fromHotel_company->length > 0) {
                                    $fromHotel_company = $fromHotel_company->item(0)->nodeValue;
                                } else {
                                    $fromHotel_company = "";
                                }
                                $fromHotel_fullNumber = $flightNumber->item(0)->getElementsByTagName("fullNumber");
                                if ($fromHotel_fullNumber->length > 0) {
                                    $fromHotel_fullNumber = $fromHotel_fullNumber->item(0)->nodeValue;
                                } else {
                                    $fromHotel_fullNumber = "";
                                }
                                $fromHotel_number = $flightNumber->item(0)->getElementsByTagName("number");
                                if ($fromHotel_number->length > 0) {
                                    $fromHotel_number = $fromHotel_number->item(0)->nodeValue;
                                } else {
                                    $fromHotel_number = "";
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
                                if ($scurrency != "" and $totalcurrencyCode != strtoupper($scurrency)) {
                                    $totalvalue = $CurrencyConverter->convert($totalvalue, $totalcurrencyCode, $scurrency);
                                }
                                if ($jumbotoursgroupmarkup != 0) {
                                    $totalvalue = $totalvalue + (($totalvalue * $jumbotoursgroupmarkup) / 100);
                                }
                                if ($internalmarkup != 0) {
                                    $totalvalue = $totalvalue + (($totalvalue * $internalmarkup) / 100);
                                }
                                // Agent markup
                                if ($agent_markup > 0) {
                                    $totalvalue = $totalvalue + (($totalvalue * $agent_markup) / 100);
                                }
                                // Fallback Markup
                                if ($jumbotoursgroupmarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                    $totalvalue = $totalvalue + (($totalvalue * $TransfersMarkupFallback) / 100);
                                }
                                // Agent discount
                                if ($agent_discount != 0) {
                                    $totalvalue = $totalvalue - (($totalvalue * $agent_discount) / 100);
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
                            $nettotal = $unitPricevalue;

                            $cancellationPoliciesArray = array();
                            $count2 = 0;
                            $comments = $transferOption->item($i)->getElementsByTagName("comments");
                            if ($comments->length > 0) {
                                for ($j=0; $j < $comments->length; $j++) { 
                                    $text = $comments->item($j)->getElementsByTagName("text");
                                    if ($text->length > 0) {
                                        $text = $text->item(0)->nodeValue;
                                    } else {
                                        $text = "";
                                    }
                                    $type = $comments->item($j)->getElementsByTagName("type");
                                    if ($type->length > 0) {
                                        $type = $type->item(0)->nodeValue;
                                    } else {
                                        $type = "";
                                    }
                                    $cancellationPoliciesArray[$count2]['text'] = $text;
                                    $cancellationPoliciesArray[$count2]['type'] = $type;
                                    $count2 = $count2 + 1;
                                }
                            }
                            //
                            // CancellationPolicies
                            //
                            $cancelpolicy = "";
                            $cancelpolicy_deadline = "";
                            for ($c=0; $c < count($cancellationPoliciesArray); $c++) { 
                                $type2 = $cancellationPoliciesArray[$c]['type'];
                                if ($type2 === "Cancellation term") {
                                    $cancellationPolicy = $cancellationPoliciesArray[$c]['text'];
                                    $cancel = explode('-', $cancellationPolicy);
                                    $days = $cancel[0];
                                    $percent = $cancel[1];
                                    $daystext = $days . " days";
                                    $from2 = strftime("%Y-%m-%d", $from);
                                    $Date2 = date('Y-m-d', strtotime("- " . $daystext, strtotime($from2)));
                                    $cancelpolicy_deadline = strftime("%a, %e %b %Y", strtotime($Date2));
                                    $cancelpolicy = $translator->translate("If you Cancel a booking before") . " " . $cancelpolicy_deadline . " " . $translator->translate("has a") . " " . $percent . $translator->translate("of total booking amount penalty.");
                                }
                            }
                            //
                            // Formato correcto
                            //
                            // error_log("\r\nNamecategory: $namecategory\r\n", 3, "/srv/www/htdocs/error_log");
                            // error_log("\r\nCancel Policies: $cancelpolicies\r\n", 3, "/srv/www/htdocs/error_log");
                            // error_log("\r\nTransfer Type: $transferType\r\n", 3, "/srv/www/htdocs/error_log");
                            //
                            $transfers[$transfer_count]['id'] = md5(uniqid($session_id, true)) . "-" . $transfer_count . "-5";
                            $transfers[$transfer_count]['adults'] = $adults;
                            $transfers[$transfer_count]['children'] = $children;
                            $transfers[$transfer_count]['infants'] = $infants;
                            $transfers[$transfer_count]['arrdate'] = $dateDep;
                            $transfers[$transfer_count]['arrtime'] = $timeDep;
                            $transfers[$transfer_count]['retdate'] = $dateComeBack;
                            $transfers[$transfer_count]['rettime'] = $timeComeBack;
                            $transfers[$transfer_count]['departurepointcode'] = $fromHotel_airportId;
                            $transfers[$transfer_count]['arrivalpointcode'] = $fromAir_airportId;
                            $transfers[$transfer_count]['transfercode'] = $fromHotel_establishmentId;
                            $transfers[$transfer_count]['transferprice'] = $totalvalue;
                            $transfers[$transfer_count]['nettotal'] = $nettotal;
                            $transfers[$transfer_count]['transferprice_net'] = $nettotal;
                            $transfers[$transfer_count]['departurepointtype'] = $fromAir_from;
                            $transfers[$transfer_count]['arrivalpointtype'] = $fromAir_to;
                            $transfers[$transfer_count]['discount'] = "0";
                            $transfers[$transfer_count]['discountpercent'] = "0";
                            $transfers[$transfer_count]['disclaimer'] = "0";
                            $transfers[$transfer_count]['cancelpolicy'] = $cancelpolicy;
                            $transfers[$transfer_count]['cancelpolicy_details'] = $cancelpolicy;
                            $transfers[$transfer_count]['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
                            $transfers[$transfer_count]['cancelpolicy_deadline'] = $cancelpolicy_deadline;

                            $transfers[$transfer_count]['detailedinfo'] = $locationTextGoing;

                            $transferdescription = $translator->translate($serviceType);

                            $transfers[$transfer_count]['image'] = $imageUrl;
                            $transfers[$transfer_count]['transfertype'] = $serviceType;
                            
                            $transfers[$transfer_count]['transferdescription'] = $description;
                            $transfers[$transfer_count]['transfertype2'] = $serviceType;
                            $transfers[$transfer_count]['transferInfoCode'] = $serviceType;
                            $transfers[$transfer_count]['outboundorigin'] = $fromAir_from;
                            $transfers[$transfer_count]['outbounddestination'] = $fromAir_to;

                            $transfers[$transfer_count]['outboundjourneytime'] = "";
                            $transfers[$transfer_count]['outboundarrivaldate'] = $dateDep;
                            $transfers[$transfer_count]['outboundarrivaltime'] = $timeDep;
                            $transfers[$transfer_count]['outboundpickupdate'] = "";
                            $transfers[$transfer_count]['outboundpickuptime'] = "";
                            // $transfers[$transfer_count]['distance'] = $distance;
                            $transfers[$transfer_count]['duration'] = "";
                            $transfers[$transfer_count]['numberofvehicles'] = $numVehicles;
                            $transfers[$transfer_count]['numberofbags'] = "1";
                            // $transfers[$transfer_count]['maxstops'] = $maxstops;
                            $transfers[$transfer_count]['minstops'] = "0";
                            $transfers[$transfer_count]['maxcapacity'] = $numPassenger;
                            $transfers[$transfer_count]['mincapacity'] = "0";
                            if ($stype == 1) {
                                $transfers[$transfer_count]['sectortype'] = "RETURN";
                            } else {
                                $transfers[$transfer_count]['sectortype'] = "SINGLE";
                            }
                            // 1=Shuttle, 2=Private
                            $transfers[$transfer_count]['vehicletype'] = $carId;
                            $transfers[$transfer_count]['vehicle'] = $carName;
                            $transfers[$transfer_count]['vehicleid'] = $carId;
                            $transfers[$transfer_count]['vehiclecode'] = $carId;
                            $transfers[$transfer_count]['numtransfers'] = ($adults + $children);
                            $transfers[$transfer_count]['PRID'] = $serviceType;
                            $transfers[$transfer_count]['CodePickupLocation'] = $fromAir_from;
                            $transfers[$transfer_count]['CodeDestinationLocation'] = $fromAir_to;
                            $transfers[$transfer_count]['dateFrom'] = $date;
                            $transfers[$transfer_count]['dateTo'] = $date;
                            // $transfers[$transfer_count]['duration_desc'] = convertToHoursMinsA2B($Duration, '%2d hour(s) and %02d minutes');
                            $transfers[$transfer_count]['returnorigin'] = $fromHotel_from;
                            $transfers[$transfer_count]['currency'] = $totalcurrencyCode;
                            $transfers[$transfer_count]['currencycode'] = $scurrency;
                            $transfers[$transfer_count]['currencyid'] = $currencyCode;
                            $transfers[$transfer_count]['returndestination'] = $fromHotel_to;
                            $transfers[$transfer_count]['returnpickuptime'] = $timeDep;
                            $transfers[$transfer_count]['returndeparturedate'] = $dateComeBack;
                            $transfers[$transfer_count]['returndeparturetime'] = $timeComeBack;
                            $transfers[$transfer_count]['returnpickupdate'] = $dateDep;
                            $transfers[$transfer_count]['returnjourneytime'] = "";
                            // $transfers[$transfer_count]['factsheetId'] = $factsheetId;
                            // $transfers[$transfer_count]['rateKey'] = $rateKey;
                            $transfers[$transfer_count]['direction'] = $direction;
                            $transfer_count ++;
                        }
                    }
                }
            }
        }
        if ($stype == 1) {
            $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/transfer/types">
                <soapenv:Header/>
                <soapenv:Body>
                <typ:availableTransfersV14>
                    <AvailabilityRQV13_1>
                        <agencyCode>' . $jumbotoursgroupagencycode . '</agencyCode>
                        <brandCode>' . $jumbotoursgroupbrandcode . '</brandCode>
                        <pointOfSaleId>' . $jumbotoursgrouppointofsale . '</pointOfSaleId>
                        <adults>' . $adults . '</adults>
                        <children>' . $children . '</children>';
                        for ($z=0; $z < $children; $z++) { 
                            $raw .= '<childrenAges>' . $children_ages[$z] . '</childrenAges>';
                        }
                $raw .= '<fromHotelToAirJourney>
                            <airportId>3057</airportId>
                            <establishmentId>4704</establishmentId>
                            <flightInfo>
                            <date>' . strftime("%Y-%m-%d", $to) . 'T' . $rettime . ':00.000Z</date>
                            <flightNumber>
                                <company>JK</company>
                                <fullNumber>1212</fullNumber>
                                <number>1212</number>
                            </flightNumber>
                            <from>PMI</from>
                            <localTime>' . $rettime . '</localTime>
                            <to>BCN</to>
                            </flightInfo>
                        </fromHotelToAirJourney>
                        <fromRow>0</fromRow>
                        <journeyDirection>Return</journeyDirection>
                        <language>en</language>
                        <numRows>100</numRows>
                    </AvailabilityRQV13_1>
                </typ:availableTransfersV14>
                </soapenv:Body>
            </soapenv:Envelope>';
            // error_log("\r\n Transfers Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
            $url_out = $raw;

            $startTime = microtime();
            $headers = array(
                "Content-type: text/xml",
                "Accept-Encoding: gzip, deflate",
                "Content-length: " . strlen($raw)
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_URL, $jumbotoursgroupserviceurl . 'public/v1_0rc1/transferBookingHandler');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, $jumbotoursgroupTimeout);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
            curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            $response_out = $response;
            $error = curl_error($ch);
            curl_close($ch);
            $endTime = microtime();
            error_log("\r\n Transfers Response: $response\r\n", 3, "/srv/www/htdocs/error_log");
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('log_jtgtransfers');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'SearchTransfers.php',
                    'errorline' => 0,
                    'errormessage' => $jumbotoursgroupserviceurl,
                    'sqlcontext' => (string) $response,
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
            if ($response != "") {
                $inputDoc = new DOMDocument();
                $inputDoc->loadXML($response);
                $Envelope = $inputDoc->getElementsByTagName("Envelope");
                $Body = $Envelope->item(0)->getElementsByTagName("Body");
                $availableTransfersV14Response = $Body->item(0)->getElementsByTagName("availableTransfersV14Response");
                if ($availableTransfersV14Response->length > 0) {
                    $result = $availableTransfersV14Response->item(0)->getElementsByTagName("result");
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
                        if ($availableTransfers->length > 0) {
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
                                $fromAir_airportId = $fromAirToHotelJourney->item(0)->getElementsByTagName("airportId");
                                if ($fromAir_airportId->length > 0) {
                                    $fromAir_airportId = $fromAir_airportId->item(0)->nodeValue;
                                } else {
                                    $fromAir_airportId = "";
                                }
                                $fromAir_establishmentId = $fromAirToHotelJourney->item(0)->getElementsByTagName("establishmentId");
                                if ($fromAir_establishmentId->length > 0) {
                                    $fromAir_establishmentId = $fromAir_establishmentId->item(0)->nodeValue;
                                } else {
                                    $fromAir_establishmentId = "";
                                }
                                $flightInfo = $fromAirToHotelJourney->item(0)->getElementsByTagName("flightInfo");
                                if ($flightInfo->length > 0) {
                                    $fromAir_date = $flightInfo->item(0)->getElementsByTagName("date");
                                    if ($fromAir_date->length > 0) {
                                        $fromAir_date = $fromAir_date->item(0)->nodeValue;
                                    } else {
                                        $fromAir_date = "";
                                    }
                                    $date = str_replace('-', '/', $fromAir_date);
                                    $dateDep = date("d/m/Y", strtotime($date));
                                    $timeDep = date("H:i", strtotime($date));
                                    $fromAir_from = $flightInfo->item(0)->getElementsByTagName("from");
                                    if ($fromAir_from->length > 0) {
                                        $fromAir_from = $fromAir_from->item(0)->nodeValue;
                                    } else {
                                        $fromAir_from = "";
                                    }
                                    $fromAir_to = $flightInfo->item(0)->getElementsByTagName("to");
                                    if ($fromAir_to->length > 0) {
                                        $fromAir_to = $fromAir_to->item(0)->nodeValue;
                                    } else {
                                        $fromAir_to = "";
                                    }
                                    $flightNumber = $flightInfo->item(0)->getElementsByTagName("flightNumber");
                                    if ($flightNumber->length > 0) {
                                        $fromAir_company = $flightNumber->item(0)->getElementsByTagName("company");
                                        if ($fromAir_company->length > 0) {
                                            $fromAir_company = $fromAir_company->item(0)->nodeValue;
                                        } else {
                                            $fromAir_company = "";
                                        }
                                        $fromAir_fullNumber = $flightNumber->item(0)->getElementsByTagName("fullNumber");
                                        if ($fromAir_fullNumber->length > 0) {
                                            $fromAir_fullNumber = $fromAir_fullNumber->item(0)->nodeValue;
                                        } else {
                                            $fromAir_fullNumber = "";
                                        }
                                        $fromAir_number = $flightNumber->item(0)->getElementsByTagName("number");
                                        if ($fromAir_number->length > 0) {
                                            $fromAir_number = $fromAir_number->item(0)->nodeValue;
                                        } else {
                                            $fromAir_number = "";
                                        }
                                    }
                                }
                            }
                            $fromHotelToAirJourney = $availableTransfers->item(0)->getElementsByTagName("fromHotelToAirJourney");
                            if ($fromHotelToAirJourney->length > 0) {
                                $fromHotel_airportId = $fromHotelToAirJourney->item(0)->getElementsByTagName("airportId");
                                if ($fromHotel_airportId->length > 0) {
                                    $fromHotel_airportId = $fromHotel_airportId->item(0)->nodeValue;
                                } else {
                                    $fromHotel_airportId = "";
                                }
                                $fromHotel_establishmentId = $fromHotelToAirJourney->item(0)->getElementsByTagName("establishmentId");
                                if ($fromHotel_establishmentId->length > 0) {
                                    $fromHotel_establishmentId = $fromHotel_establishmentId->item(0)->nodeValue;
                                } else {
                                    $fromHotel_establishmentId = "";
                                }
                                $flightInfo = $fromHotelToAirJourney->item(0)->getElementsByTagName("flightInfo");
                                if ($flightInfo->length > 0) {
                                    $fromHotel_date = $flightInfo->item(0)->getElementsByTagName("date");
                                    if ($fromHotel_date->length > 0) {
                                        $fromHotel_date = $fromHotel_date->item(0)->nodeValue;
                                    } else {
                                        $fromHotel_date = "";
                                    }
                                    $date2 = str_replace('-', '/', $fromHotel_date);
                                    $dateComeBack = date("d/m/Y", strtotime($date2));
                                    $timeComeBack = date("H:i", strtotime($date2));
                                    $fromHotel_from = $flightInfo->item(0)->getElementsByTagName("from");
                                    if ($fromHotel_from->length > 0) {
                                        $fromHotel_from = $fromHotel_from->item(0)->nodeValue;
                                    } else {
                                        $fromHotel_from = "";
                                    }
                                    $fromHotel_to = $flightInfo->item(0)->getElementsByTagName("to");
                                    if ($fromHotel_to->length > 0) {
                                        $fromHotel_to = $fromHotel_to->item(0)->nodeValue;
                                    } else {
                                        $fromHotel_to = "";
                                    }
                                    $flightNumber = $flightInfo->item(0)->getElementsByTagName("flightNumber");
                                    if ($flightNumber->length > 0) {
                                        $fromHotel_company = $flightNumber->item(0)->getElementsByTagName("company");
                                        if ($fromHotel_company->length > 0) {
                                            $fromHotel_company = $fromHotel_company->item(0)->nodeValue;
                                        } else {
                                            $fromHotel_company = "";
                                        }
                                        $fromHotel_fullNumber = $flightNumber->item(0)->getElementsByTagName("fullNumber");
                                        if ($fromHotel_fullNumber->length > 0) {
                                            $fromHotel_fullNumber = $fromHotel_fullNumber->item(0)->nodeValue;
                                        } else {
                                            $fromHotel_fullNumber = "";
                                        }
                                        $fromHotel_number = $flightNumber->item(0)->getElementsByTagName("number");
                                        if ($fromHotel_number->length > 0) {
                                            $fromHotel_number = $fromHotel_number->item(0)->nodeValue;
                                        } else {
                                            $fromHotel_number = "";
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
                                        if ($scurrency != "" and $totalcurrencyCode != strtoupper($scurrency)) {
                                            $totalvalue = $CurrencyConverter->convert($totalvalue, $totalcurrencyCode, $scurrency);
                                        }
                                        if ($jumbotoursgroupmarkup != 0) {
                                            $totalvalue = $totalvalue + (($totalvalue * $jumbotoursgroupmarkup) / 100);
                                        }
                                        if ($internalmarkup != 0) {
                                            $totalvalue = $totalvalue + (($totalvalue * $internalmarkup) / 100);
                                        }
                                        // Agent markup
                                        if ($agent_markup > 0) {
                                            $totalvalue = $totalvalue + (($totalvalue * $agent_markup) / 100);
                                        }
                                        // Fallback Markup
                                        if ($jumbotoursgroupmarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                            $totalvalue = $totalvalue + (($totalvalue * $TransfersMarkupFallback) / 100);
                                        }
                                        // Agent discount
                                        if ($agent_discount != 0) {
                                            $totalvalue = $totalvalue - (($totalvalue * $agent_discount) / 100);
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
                                    $nettotal = $unitPricevalue;

                                    $cancellationPoliciesArray = array();
                                    $count2 = 0;
                                    $comments = $transferOption->item($i)->getElementsByTagName("comments");
                                    if ($comments->length > 0) {
                                        for ($j=0; $j < $comments->length; $j++) { 
                                            $text = $comments->item($j)->getElementsByTagName("text");
                                            if ($text->length > 0) {
                                                $text = $text->item(0)->nodeValue;
                                            } else {
                                                $text = "";
                                            }
                                            $type = $comments->item($j)->getElementsByTagName("type");
                                            if ($type->length > 0) {
                                                $type = $type->item(0)->nodeValue;
                                            } else {
                                                $type = "";
                                            }
                                            $cancellationPoliciesArray[$count2]['text'] = $text;
                                            $cancellationPoliciesArray[$count2]['type'] = $type;
                                            $count2 = $count2 + 1;
                                        }
                                    }
                                    //
                                    // CancellationPolicies
                                    //
                                    $cancelpolicy = "";
                                    $cancelpolicy_deadline = "";
                                    for ($c=0; $c < count($cancellationPoliciesArray); $c++) { 
                                        $type2 = $cancellationPoliciesArray[$c]['type'];
                                        if ($type2 === "Cancellation term") {
                                            $cancellationPolicy = $cancellationPoliciesArray[$c]['text'];
                                            $cancel = explode('-', $cancellationPolicy);
                                            $days = $cancel[0];
                                            $percent = $cancel[1];
                                            $daystext = $days . " days";
                                            $from2 = strftime("%Y-%m-%d", $from);
                                            $Date2 = date('Y-m-d', strtotime("- " . $daystext, strtotime($from2)));
                                            $cancelpolicy_deadline = strftime("%a, %e %b %Y", strtotime($Date2));
                                            $cancelpolicy = $translator->translate("If you Cancel a booking before") . " " . $cancelpolicy_deadline . " " . $translator->translate("has a") . " " . $percent . $translator->translate("of total booking amount penalty.");
                                        }
                                    }
                                    //
                                    // Formato correcto
                                    //
                                    // error_log("\r\nNamecategory: $namecategory\r\n", 3, "/srv/www/htdocs/error_log");
                                    // error_log("\r\nCancel Policies: $cancelpolicies\r\n", 3, "/srv/www/htdocs/error_log");
                                    // error_log("\r\nTransfer Type: $transferType\r\n", 3, "/srv/www/htdocs/error_log");
                                    //
                                    $transfersout[$transfer_count_out]['id'] = md5(uniqid($session_id, true)) . "-" . $transfer_count_out . "-5";
                                    $transfersout[$transfer_count_out]['adults'] = $adults;
                                    $transfersout[$transfer_count_out]['children'] = $children;
                                    $transfersout[$transfer_count_out]['infants'] = $infants;
                                    $transfersout[$transfer_count_out]['arrdate'] = $dateDep;
                                    $transfersout[$transfer_count_out]['arrtime'] = $timeDep;
                                    $transfersout[$transfer_count_out]['retdate'] = $dateComeBack;
                                    $transfersout[$transfer_count_out]['rettime'] = $timeComeBack;
                                    $transfersout[$transfer_count_out]['departurepointcode'] = $fromHotel_airportId;
                                    $transfersout[$transfer_count_out]['arrivalpointcode'] = $fromAir_airportId;
                                    $transfersout[$transfer_count_out]['transfercode'] = $fromHotel_establishmentId;
                                    $transfersout[$transfer_count_out]['transferprice'] = $totalvalue;
                                    $transfersout[$transfer_count_out]['nettotal'] = $nettotal;
                                    $transfersout[$transfer_count_out]['transferprice_net'] = $nettotal;
                                    $transfersout[$transfer_count_out]['departurepointtype'] = $fromAir_from;
                                    $transfersout[$transfer_count_out]['arrivalpointtype'] = $fromAir_to;
                                    $transfersout[$transfer_count_out]['discount'] = "0";
                                    $transfersout[$transfer_count_out]['discountpercent'] = "0";
                                    $transfersout[$transfer_count_out]['disclaimer'] = "0";
                                    $transfersout[$transfer_count_out]['cancelpolicy'] = $cancelpolicy;
                                    $transfersout[$transfer_count_out]['cancelpolicy_details'] = $cancelpolicy;
                                    $transfersout[$transfer_count_out]['cancelpolicy_deadlinetimestamp'] = $cancelpolicy_deadline;
                                    $transfersout[$transfer_count_out]['cancelpolicy_deadline'] = $cancelpolicy_deadline;

                                    $transfersout[$transfer_count_out]['detailedinfo'] = $locationTextGoing;

                                    $transferdescription = $translator->translate($serviceType);

                                    $transfersout[$transfer_count_out]['image'] = $imageUrl;
                                    $transfersout[$transfer_count_out]['transfertype'] = $serviceType;
                                    
                                    $transfersout[$transfer_count_out]['transferdescription'] = $description;
                                    $transfersout[$transfer_count_out]['transfertype2'] = $serviceType;
                                    $transfersout[$transfer_count_out]['transferInfoCode'] = $serviceType;
                                    $transfersout[$transfer_count_out]['outboundorigin'] = $fromAir_from;
                                    $transfersout[$transfer_count_out]['outbounddestination'] = $fromAir_to;

                                    $transfersout[$transfer_count_out]['outboundjourneytime'] = "";
                                    $transfersout[$transfer_count_out]['outboundarrivaldate'] = $dateDep;
                                    $transfersout[$transfer_count_out]['outboundarrivaltime'] = $timeDep;
                                    $transfersout[$transfer_count_out]['outboundpickupdate'] = "";
                                    $transfersout[$transfer_count_out]['outboundpickuptime'] = "";
                                    // $transfersout[$transfer_count_out]['distance'] = $distance;
                                    $transfersout[$transfer_count_out]['duration'] = "";
                                    $transfersout[$transfer_count_out]['numberofvehicles'] = $numVehicles;
                                    $transfersout[$transfer_count_out]['numberofbags'] = "1";
                                    // $transfersout[$transfer_count_out]['maxstops'] = $maxstops;
                                    $transfersout[$transfer_count_out]['minstops'] = "0";
                                    $transfersout[$transfer_count_out]['maxcapacity'] = $numPassenger;
                                    $transfersout[$transfer_count_out]['mincapacity'] = "0";
                                    if ($stype == 1) {
                                        $transfersout[$transfer_count_out]['sectortype'] = "RETURN";
                                    } else {
                                        $transfersout[$transfer_count_out]['sectortype'] = "SINGLE";
                                    }
                                    // 1=Shuttle, 2=Private
                                    $transfersout[$transfer_count_out]['vehicletype'] = $carId;
                                    $transfersout[$transfer_count_out]['vehicle'] = $carName;
                                    $transfersout[$transfer_count_out]['vehicleid'] = $carId;
                                    $transfersout[$transfer_count_out]['vehiclecode'] = $carId;
                                    $transfersout[$transfer_count_out]['numtransfersout'] = ($adults + $children);
                                    $transfersout[$transfer_count_out]['PRID'] = $serviceType;
                                    $transfersout[$transfer_count_out]['CodePickupLocation'] = $fromAir_from;
                                    $transfersout[$transfer_count_out]['CodeDestinationLocation'] = $fromAir_to;
                                    $transfersout[$transfer_count_out]['dateFrom'] = $date;
                                    $transfersout[$transfer_count_out]['dateTo'] = $date;
                                    // $transfersout[$transfer_count_out]['duration_desc'] = convertToHoursMinsA2B($Duration, '%2d hour(s) and %02d minutes');
                                    $transfersout[$transfer_count_out]['returnorigin'] = $fromHotel_from;
                                    $transfersout[$transfer_count_out]['currency'] = $totalcurrencyCode;
                                    $transfersout[$transfer_count_out]['currencycode'] = $scurrency;
                                    $transfersout[$transfer_count_out]['currencyid'] = $currencyCode;
                                    $transfersout[$transfer_count_out]['returndestination'] = $fromHotel_to;
                                    $transfersout[$transfer_count_out]['returnpickuptime'] = $timeDep;
                                    $transfersout[$transfer_count_out]['returndeparturedate'] = $dateComeBack;
                                    $transfersout[$transfer_count_out]['returndeparturetime'] = $timeComeBack;
                                    $transfersout[$transfer_count_out]['returnpickupdate'] = $dateDep;
                                    $transfersout[$transfer_count_out]['returnjourneytime'] = "";
                                    // $transfersout[$transfer_count_out]['factsheetId'] = $factsheetId;
                                    // $transfersout[$transfer_count_out]['rateKey'] = $rateKey;
                                    $transfersout[$transfer_count_out]['direction'] = $direction;
                                    $transfer_count_out ++;
                                }
                            }
                        }
                    }
                }
            }
            //
            // Outbound
            //
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_jtgtransfers_out');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_jtgtransfers_out');
                $insert->values(array(
                    'session_id' => $session_id,
                    'xmlrequest' => (string) $url_out,
                    'xmlresult' => (string) $response_out,
                    'data' => base64_encode(serialize($transfersout)),
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
        //
        // Inbound
        //
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_jtgtransfers');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_jtgtransfers');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $url_in,
                'xmlresult' => (string) $response_in,
                'data' => base64_encode(serialize($transfers)),
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\n JumboTours transfers eof\r\n", 3, "/srv/www/htdocs/error_log");
?>