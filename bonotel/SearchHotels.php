<?php
// error_log("\r\n start bonotel -sergio \r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$bonotel = false;
$db2 = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml46, latitude, longitude from cities where id=" . $destination;
$statement2 = $db2->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml46 = $row_settings["city_xml46"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml46 = 0;
}
$db2->getDriver()
    ->getConnection()
    ->disconnect();
if ((int) $nationality > 0) {
    $db2 = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db2->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
    $db2->getDriver()
        ->getConnection()
        ->disconnect();
} else {
    $db2 = new \Zend\Db\Adapter\Adapter($config);
    $sql = "select value from settings where name='bonotelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_bonotel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
    $db->getDriver()
        ->getConnection()
        ->disconnect();
}
$sql = "select value from settings where name='bonoteluser' and affiliate_id=$affiliate_id_bonotel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $bonoteluser = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='bonotelpassword' and affiliate_id=$affiliate_id_bonotel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $bonotelpassword = base64_decode($row_settings['value']);
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='bonotelMarkup' and affiliate_id=$affiliate_id_bonotel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $bonotelMarkup = (double) $row_settings['value'];
} else {
    $bonotelMarkup = 0;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='bonotelserviceURL' and affiliate_id=$affiliate_id_bonotel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $bonotelserviceURL = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
// die();
// $city_xml18 = "";
// if ($city_xml46 != "") {
// $raw = 'search/?currency=' . $scurrency . '&client_nationality=' . $sourceMarket . $pax . '&checkin=' . strftime("%Y-%m-%d", $from) . '&checkout=' . strftime("%Y-%m-%d", $to) . '&destination_code=' . $city_xml46;
// } else {
// $raw = 'search/?' . $pax . '&checkout=' . strftime("%Y-%m-%d", $to) . '&checkin=' . strftime("%Y-%m-%d", $from) . '&lat=' . $latitude . '&lon=' . $longitude . '&radius=1000&client_nationality=' . $sourceMarket . '&currency=' . $scurrency;
// }
// echo $raw;
// die();
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$raw = '<?xml version="1.0" encoding="utf-8" ?><availabilityRequest cancelpolicy="Y" hotelfees="Y"><control><userName>' . $bonoteluser . '</userName><passWord>' . $bonotelpassword . '</passWord></control><checkIn>' . strftime("%d-%b-%Y", $from) . '</checkIn><checkOut>' . strftime("%d-%b-%Y", $to) . '</checkOut>
  <noOfRooms>' . $rooms . '</noOfRooms>
  <noOfNights>' . $noOfNights . '</noOfNights>
  <country>' . $sourceMarket . '</country>
  <city>CY191</city>
  <hotelCodes>
    <hotelCode/>
  </hotelCodes><roomsInformation>';
for ($r = 0; $r < count($selectedAdults); $r ++) {
    $raw .= '<roomInfo><roomTypeId>0</roomTypeId><bedTypeId>0</bedTypeId>
      <adultsNum>' . $selectedAdults[$r] . '</adultsNum>
      <childNum>' . $selectedChildren[$r] . '</childNum>';
    if ($selectedChildren[$r] == 0) {
        $raw .= '<childAges/>';
    } else {
        $raw .= '<childAges>';
        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
            $raw .= '<childAge>' . $selectedChildrenAges[$r][$z] . '</childAge>';
        }
        $raw .= '</childAges>';
    }
    $raw .= '</roomInfo>';
}
$raw .= '</roomsInformation></availabilityRequest>';
if ($bonotelserviceURL != "" and $bonoteluser != "" and $bonotelpassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $bonotelserviceURL . "reservation/GetAvailability.do");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml; charset=utf-8',
        'Content-Length: ' . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    /*
     * if ($response === false) {
     * error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
     * } else {
     * error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
     * }
     */
    curl_close($ch);
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    try {
        $db2 = new \Zend\Db\Adapter\Adapter($config);
        $sql = new Sql($db2);
        $insert = $sql->insert();
        $insert->into('log_bonotel');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $bonotelserviceURL . $raw,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db2->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    // echo $response;
    // die();
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("hotel");
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $code = $node->item($rAUX)->getElementsByTagName("hotelCode");
        if ($code->length > 0) {
            $code = $code->item(0)->nodeValue;
        } else {
            $code = "";
        }
        $name = $node->item($rAUX)->getElementsByTagName('name');
        if ($name->length > 0) {
            $name = $name->item(0)->nodeValue;
        } else {
            $name = "";
        }
        // error_log("\r\n Hotel name - : $name \r\n", 3, "/srv/www/htdocs/error_log");
        $address = $node->item($rAUX)->getElementsByTagName('address');
        if ($address->length > 0) {
            $address = $address->item(0)->nodeValue;
        } else {
            $address = "";
        }
        
        $city = $node->item($rAUX)->getElementsByTagName('city');
        if ($city->length > 0) {
            $city = $city->item(0)->nodeValue;
        } else {
            $city = "";
        }
        $stateProvince = $node->item($rAUX)->getElementsByTagName('stateProvince');
        if ($address->length > 0) {
            $stateProvince = $stateProvince->item(0)->nodeValue;
        } else {
            $stateProvince = "";
        }
        $country = $node->item($rAUX)->getElementsByTagName('country');
        if ($country->length > 0) {
            $country = $country->item(0)->nodeValue;
        } else {
            $address = "";
        }
        $postalCode = $node->item($rAUX)->getElementsByTagName('postalCode');
        if ($postalCode->length > 0) {
            $postalCode = $postalCode->item(0)->nodeValue;
        } else {
            $postalCode = "";
        }
        $ratecurrencyCode = $node->item($rAUX)->getElementsByTagName('ratecurrencyCode');
        if ($ratecurrencyCode->length > 0) {
            $ratecurrencyCode = $ratecurrencyCode->item(0)->nodeValue;
        } else {
            $ratecurrencyCode = "";
        }
        $shortDescription = $node->item($rAUX)->getElementsByTagName('shortDescription');
        if ($shortDescription->length > 0) {
            $shortDescription = $shortDescription->item(0)->nodeValue;
        } else {
            $shortDescription = "";
        }
        $startRating = $node->item($rAUX)->getElementsByTagName('startRating');
        if ($startRating->length > 0) {
            $startRating = $startRating->item(0)->nodeValue;
        } else {
            $address = "";
        }
        $thumbNailUrl = $node->item($rAUX)->getElementsByTagName('thumbNailUrl');
        if ($thumbNailUrl->length > 0) {
            $thumbNailUrl = $thumbNailUrl->item(0)->nodeValue;
        } else {
            $thumbNailUrl = "";
        }
        $hotelUrl = $node->item($rAUX)->getElementsByTagName('hotelUrl');
        if ($hotelUrl->length > 0) {
            $hotelUrl = $hotelUrl->item(0)->nodeValue;
        } else {
            $hotelUrl = "";
        }
        $maintenance = $node->item($rAUX)->getElementsByTagName('maintenance');
        if ($maintenance->length > 0) {
            $maintenance = $maintenance->item(0)->nodeValue;
        } else {
            $maintenance = "";
        }
        $bookingPolicy = $node->item($rAUX)->getAttribute('bookingPolicy');
        if ($bookingPolicy->length > 0) {
            $bookingPolicy = $bookingPolicy->item(0)->nodeValue;
        } else {
            $bookingPolicy = "";
        }
        $policyDescription = $node->item($rAUX)->getElementsByTagName('policyDescription');
        if ($policyDescription->length > 0) {
            $policyDescription = $policyDescription->item(0)->nodeValue;
        } else {
            $policyDescription = "";
        }
        $rooms = $node->item($rAUX)->getElementsByTagName("roomInformation");
        if ($rooms->length > 0) {
            $sfilter[] = " sid=$code ";
            for ($k = 0; $k < $rooms->length; $k ++) {
                $roomNo = $rooms->item($k)->getAttribute("roomNo");
                $roomCode = $rooms->item($k)->getAttribute("roomCode");
                $roomTypeCode = $rooms->item($k)->getElementsByTagName("roomTypeCode");
                if ($roomTypeCode->length > 0) {
                    $roomTypeCode = $roomTypeCode->item(0)->nodeValue;
                } else {
                    $roomTypeCode = "";
                }
                $roomType = $rooms->item($k)->getElementsByTagName("roomType");
                if ($roomType->length > 0) {
                    $roomType = $roomType->item(0)->nodeValue;
                } else {
                    $roomType = "";
                }
                $bedTypeCode = $rooms->item($k)->getElementsByTagName("bedTypeCode");
                if ($bedTypeCode->length > 0) {
                    $bedTypeCode = $bedTypeCode->item(0)->nodeValue;
                } else {
                    $bedTypeCode = "";
                }
                $bedType = $rooms->item($k)->getElementsByTagName("bedType");
                if ($bedType->length > 0) {
                    $bedType = $bedType->item(0)->nodeValue;
                } else {
                    $bedType = "";
                }
                $stdAdults = $rooms->item($k)->getElementsByTagName("stdAdults");
                if ($stdAdults->length > 0) {
                    $stdAdults = $stdAdults->item(0)->nodeValue;
                } else {
                    $stdAdults = "";
                }
                $promotionCode = $rooms->item($k)->getElementsByTagName("promotionCode");
                if ($promotionCode->length > 0) {
                    $promotionCode = $promotionCode->item(0)->nodeValue;
                } else {
                    $promotionCode = "";
                }
                $confirmationType = $rooms->item($k)->getElementsByTagName("confirmationType");
                if ($confirmationType->length > 0) {
                    $confirmationType = $confirmationType->item(0)->nodeValue;
                } else {
                    $confirmationType = "";
                }
                $confirmationConditions = $rooms->item($k)->getElementsByTagName("confirmationConditions");
                if ($confirmationConditions->length > 0) {
                    $confirmationConditions = $confirmationConditions->item(0)->nodeValue;
                } else {
                    $confirmationConditions = "";
                }
                $roomBookingPolicy = $rooms->item($k)->getElementsByTagName("roomBookingPolicy");
                if ($roomBookingPolicy->length > 0) {
                    $policyFrom = $roomBookingPolicy->item(0)->getElementsByTagName("policyFrom");
                    if ($policyFrom->length > 0) {
                        $policyFrom = $policyFrom->item(0)->nodeValue;
                    } else {
                        $policyFrom = "";
                    }
                    $policyTo = $roomBookingPolicy->item(0)->getElementsByTagName("policyTo");
                    if ($policyTo->length > 0) {
                        $policyTo = $policyTo->item(0)->nodeValue;
                    } else {
                        $policyTo = "";
                    }
                    $amendmentType = $roomBookingPolicy->item(0)->getElementsByTagName("amendmentType");
                    if ($amendmentType->length > 0) {
                        $amendmentType = $amendmentType->item(0)->nodeValue;
                    } else {
                        $amendmentType = "";
                    }
                    $policyBasedOn = $roomBookingPolicy->item(0)->getElementsByTagName("policyBasedOn");
                    if ($policyBasedOn->length > 0) {
                        $policyBasedOn = $policyBasedOn->item(0)->nodeValue;
                    } else {
                        $policyBasedOn = "";
                    }
                    $policyBasedOnValue = $roomBookingPolicy->item(0)->getElementsByTagName("policyBasedOnValue");
                    if ($policyBasedOnValue->length > 0) {
                        $policyBasedOnValue = $policyBasedOnValue->item(0)->nodeValue;
                    } else {
                        $policyBasedOnValue = "";
                    }
                    $cancellationType = $roomBookingPolicy->item(0)->getElementsByTagName("cancellationType");
                    if ($cancellationType->length > 0) {
                        $cancellationType = $cancellationType->item(0)->nodeValue;
                    } else {
                        $cancellationType = "";
                    }
                    $stayDateRequirement = $roomBookingPolicy->item(0)->getElementsByTagName("stayDateRequirement");
                    if ($stayDateRequirement->length > 0) {
                        $stayDateRequirement = $stayDateRequirement->item(0)->nodeValue;
                    } else {
                        $stayDateRequirement = "";
                    }
                    $arrivalRange = $roomBookingPolicy->item(0)->getElementsByTagName("arrivalRange");
                    if ($arrivalRange->length > 0) {
                        $arrivalRange = $arrivalRange->item(0)->nodeValue;
                    } else {
                        $arrivalRange = "";
                    }
                    $arrivalRangeValue = $roomBookingPolicy->item(0)->getElementsByTagName("arrivalRangeValue");
                    if ($arrivalRangeValue->length > 0) {
                        $arrivalRangeValue = $arrivalRangeValue->item(0)->nodeValue;
                    } else {
                        $arrivalRangeValue = "";
                    }
                    $policyFee = $roomBookingPolicy->item(0)->getElementsByTagName("policyFee");
                    if ($policyFee->length > 0) {
                        $policyFee = $policyFee->item(0)->nodeValue;
                    } else {
                        $policyFee = "";
                    }
                    $noShowBasedOn = $roomBookingPolicy->item(0)->getElementsByTagName("noShowBasedOn");
                    if ($policyBasedOn->length > 0) {
                        $policyBasedOn = $policyBasedOn->item(0)->nodeValue;
                    } else {
                        $policyBasedOn = "";
                    }
                    $noShowBasedOnValue = $roomBookingPolicy->item(0)->getElementsByTagName("noShowBasedOnValue");
                    if ($noShowBasedOnValue->length > 0) {
                        $noShowBasedOnValue = $noShowBasedOnValue->item(0)->nodeValue;
                    } else {
                        $noShowBasedOnValue = "";
                    }
                    $noShowPolicyFee = $roomBookingPolicy->item(0)->getElementsByTagName("noShowPolicyFee");
                    if ($noShowPolicyFee->length > 0) {
                        $noShowPolicyFee = $noShowPolicyFee->item(0)->nodeValue;
                    } else {
                        $noShowPolicyFee = "";
                    }
                }
                // EOF
                $rateInformation = $rooms->item($k)->getElementsByTagName("rateInformation");
                if ($rateInformation->length > 0) {
                    $ratePlanCode = $rateInformation->item(0)->getElementsByTagName("ratePlanCode");
                    if ($ratePlanCode->length > 0) {
                        $ratePlanCode = $ratePlanCode->item(0)->nodeValue;
                    } else {
                        $ratePlanCode = 1;
                    }
                    $ratePlan = $rateInformation->item(0)->getElementsByTagName("ratePlan");
                    if ($ratePlan->length > 0) {
                        $ratePlan = $ratePlan->item(0)->nodeValue;
                    } else {
                        $ratePlan = "";
                    }
                    $averageRate = $rateInformation->item(0)->getElementsByTagName("averageRate");
                    if ($averageRate->length > 0) {
                        $averageRate = $averageRate->item(0)->nodeValue;
                    } else {
                        $averageRate = "";
                    }
                    $totalRate = $rateInformation->item(0)->getElementsByTagName("totalRate");
                    if ($totalRate->length > 0) {
                        $totalRate = $totalRate->item(0)->nodeValue;
                    } else {
                        $totalRate = "";
                    }
                    $nettotal = $totalRate;
                    // error_log("\r\n TOTAL RATE3 $totalRate \r\n", 3, "/srv/www/htdocs/error_log");
                    // $rateInformation = $rateInformation->item(0)->getElementsByTagName("rateInformation");
                    // for ($kkAux = 0; $kkAux < $rateInformation->length; $kkAux ++) {
                    // $ratePlan = $rateInformation->item($kkAux)->getAttribute("ratePlan");
                    // $averageRate = $rateInformation->item($kkAux)->getAttribute("averageRate");
                    // $totalRate = $rateInformation->item($kkAux)->getAttribute("totalRate");
                    // }
                    $dailyRates = $rateInformation->item(0)->getElementsByTagName("dailyRates");
                    if ($dailyRates->length > 0) {
                        // $dailyRates = $rateInformation->item($kkAux)->getAttribute("dailyRates");
                        $nightlyRate = $dailyRates->item(0)->getElementsByTagName("nightlyRate");
                        for ($kkkAux = 0; $kkkAux < $nightlyRate->length; $kkkAux ++) {
                            $stdAdultRate = $nightlyRate->item($kkkAux)->getElementsByTagName("stdAdultRate");
                            if ($stdAdultRate->length > 0) {
                                $stdAdultRate = $stdAdultRate->item(0)->nodeValue;
                            } else {
                                $stdAdultRate = "";
                            }
                            $additionalAdultRate = $nightlyRate->item($kkkAux)->getElementsByTagName("additionalAdultRate");
                            if ($additionalAdultRate->length > 0) {
                                $additionalAdultRate = $additionalAdultRate->item(0)->nodeValue;
                            } else {
                                $additionalAdultRate = "";
                            }
                            $total = $nightlyRate->item($kkkAux)->getElementsByTagName("total");
                            if ($total->length > 0) {
                                $total = $total->item(0)->nodeValue;
                            } else {
                                $total = "";
                            }
                            $rateCode = $nightlyRate->item($kkkAux)->getElementsByTagName("rateCode");
                            if ($rateCode->length > 0) {
                                $rateCode = $rateCode->item(0)->nodeValue;
                            } else {
                                $rateCode = "";
                            }
                        }
                    }
                    $hotelFees = $rateInformation->item(0)->getElementsByTagName("hotelFees");
                    if ($hotelFees->length > 0) {
                        $hotelFee = $hotelFees->item(0)->getElementsByTagName("hotelFee");
                        if ($hotelFee->length > 0) {
                            $feeType = $hotelFee->item(0)->getElementsByTagName("feeType");
                            if ($feeType->length > 0) {
                                $feeType = $feeType->item(0)->nodeValue;
                            } else {
                                $feeType = "";
                            }
                            $feeMethod = $hotelFee->item(0)->getElementsByTagName("feeMethod");
                            if ($feeMethod->length > 0) {
                                $feeMethod = $feeMethod->item(0)->nodeValue;
                            } else {
                                $feeMethod = "";
                            }
                            $requiredFee = $hotelFee->item(0)->getElementsByTagName("requiredFee");
                            if ($requiredFee->length > 0) {
                                $requiredFee = $requiredFee->item(0)->nodeValue;
                            } else {
                                $requiredFee = "";
                            }
                            $feeAssign = $hotelFee->item(0)->getElementsByTagName("feeAssign");
                            if ($feeAssign->length > 0) {
                                $feeAssign = $feeAssign->item(0)->nodeValue;
                            } else {
                                $feeAssign = "";
                            }
                            $feeFrequency = $hotelFee->item(0)->getElementsByTagName("feeFrequency");
                            if ($feeFrequency->length > 0) {
                                $feeFrequency = $feeFrequency->item(0)->nodeValue;
                            } else {
                                $feeFrequency = "";
                            }
                            $feeBasedOn = $hotelFee->item(0)->getElementsByTagName("feeBasedOn");
                            if ($feeBasedOn->length > 0) {
                                $feeBasedOn = $feeBasedOn->item(0)->nodeValue;
                            } else {
                                $feeBasedOn = "";
                            }
                            $feeBasedOnValue = $hotelFee->item(0)->getElementsByTagName("feeBasedOnValue");
                            if ($feeBasedOnValue->length > 0) {
                                $feeBasedOnValue = $feeBasedOnValue->item(0)->nodeValue;
                            } else {
                                $feeBasedOnValue = "";
                            }
                            $salesTax = $hotelFee->item(0)->getElementsByTagName("salesTax");
                            if ($salesTax->length > 0) {
                                $salesTax = $salesTax->item(0)->nodeValue;
                            } else {
                                $salesTax = "";
                            }
                            $conditions = $hotelFee->item(0)->getElementsByTagName("conditions");
                            if ($conditions->length > 0) {
                                $conditions = $conditions->item(0)->nodeValue;
                            } else {
                                $conditions = "";
                            }
                            $feeTotal = $hotelFee->item(0)->getElementsByTagName("feeTotal");
                            if ($feeTotal->length > 0) {
                                $feeTotal = $feeTotal->item(0)->nodeValue;
                            } else {
                                $feeTotal = "";
                            }
                        }
                        // $hotelFee = $hotelFees->item($kkAux)->getAttribute("hotelFee");
                        // for ($kkkAux = 0; $kkkAux < $hotelFee->length; $kkkAux ++) {
                        // $feeType = $hotelFees->item($kkkAux)->getAttribute("feeType");
                        // $feeMethod = $hotelFees->item($kkkAux)->getAttribute("feeMethod");
                        // $requiredFee = $hotelFees->item($kkkAux)->getAttribute("requiredFee");
                        // $feeAssign = $hotelFees->item($kkkAux)->getAttribute("feeAssign");
                        // $feeFrequency = $hotelFees->item($kkkAux)->getAttribute("feeFrequency");
                        // $feeBasedOn = $hotelFees->item($kkkAux)->getAttribute("feeBasedOn");
                        // $feeBasedOnValue = $hotelFees->item($kkkAux)->getAttribute("feeBasedOnValue");
                        // $salesTax = $hotelFees->item($kkkAux)->getAttribute("salesTax");
                        // $conditions = $hotelFees->item($kkkAux)->getAttribute("conditions");
                        // $feeTotal = $hotelFees->item($kkkAux)->getAttribute("feeTotal");
                        // }
                    }
                }
                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                    if ($selectedAdults[$zRooms] == $stdAdults) {
                        // Chidlren ??
                        // if ($selectedChildren[$zRooms] == $children) {
                        if (is_array($tmp[$code])) {
                            $baseCounterDetails = count($tmp[$code]['details'][$zRooms]);
                        } else {
                            $baseCounterDetails = 0;
                        }
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['name'] = $name;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['shid'] = $code;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $cancellationType;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-46";
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['room'] = $roomType;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['adults'] = $adultsNum;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['children'] = $childNum;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
                        if ($promotionCode != "") {
                            $tmp[$code]['special'] = true;
                            $tmp[$code]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                            $tmp[$code]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $promotionCode;
                        } else {
                            $tmp[$code]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                            $tmp[$code]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                        }
                        $db2 = new \Zend\Db\Adapter\Adapter($config);
                        try {
                            $sql = "select rateplan from bonotel_rateplans where rateplancode=" . (int) $ratePlanCode;
                            $statement2 = $db2->createStatement($sql);
                            $statement2->prepare();
                            $row_board_mapping = $statement2->execute();
                            if ($row_board_mapping->valid()) {
                                $row_board_mapping = $row_board_mapping->current();
                                $ratePlanCode = $row_board_mapping["rateplan"];
                            }
                        } catch (Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        try {
                            $sql = "select mapped from board_mapping where description='" . addslashes($ratePlanCode) . "'";
                            $statement2 = $db2->createStatement($sql);
                            $statement2->prepare();
                            $row_board_mapping = $statement2->execute();
                            if ($row_board_mapping->valid()) {
                                $row_board_mapping = $row_board_mapping->current();
                                $ratePlanCode = $row_board_mapping["mapped"];
                            }
                        } catch (Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        $db2->getDriver()
                            ->getConnection()
                            ->disconnect();
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($ratePlanCode);
                        $pricebreakdown = array();
                        $pricebreakdownCount = 0;
                        $Gross = $total / $noOfNights;
                        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                            $amount = $noOfNights * $feeTotal;
                            if ($bonotelMarkup != 0) {
                                $amount = $amount + (($amount * $bonotelMarkup) / 100);
                            }
                            // Geo target markup
                            if ($internalmarkup != 0) {
                                $amount = $amount + (($amount * $internalmarkup) / 100);
                            }
                            // Agent markup
                            if ($agent_markup != 0) {
                                $amount = $amount + (($amount * $agent_markup) / 100);
                            }
                            // Fallback Markup
                            if ($bonotelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                            }
                            // Agent discount
                            if ($agent_discount != 0) {
                                $amount = $amount - (($amount * $agent_discount) / 100);
                            }
                            if ($scurrency != "" and $currency != $scurrency) {
                                $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                            }
                            $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                            $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                            $pricebreakdownCount = $pricebreakdownCount + 1;
                        }
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $base_currency;
                        $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $policyDescription;
                        $vhb = 1;
                        // }
                    }
                }
            }
        }
    }
}
// Paulo
// Tudo para baixo esta bem - nao alterar
if ($vhb == 1) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $db2 = new \Zend\Db\Adapter\Adapter($config);
        $sql = "select hid, sid from xmlhotels_mbonotel where " . $sfilter;
        $statement2 = $db2->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
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
        $db2->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 46;
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $delete = $sql->delete();
            $delete->from('quote_session_bonotel');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('quote_session_bonotel');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $xml,
                'xmlresult' => (string) $xmlresult,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db2->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
?>