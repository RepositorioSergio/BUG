<?php
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
$majestic = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml47, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml47 = $row_settings["city_xml47"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml47 = "";
}
$sql = "select value from settings where name='enablemajesticusa' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_majestic = $affiliate_id;
} else {
    $affiliate_id_majestic = 0;
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
    $sql = "select value from settings where name='majesticusaDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_majestic";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='majesticusaLoginEmail' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='majesticusaPassword' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='majesticusaMarkup' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaMarkup = (double) $row_settings['value'];
} else {
    $majesticusaMarkup = 0;
}
$sql = "select value from settings where name='majesticusaServiceURL' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaServiceURL = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');
$time = date('H:i:s', time());
$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Header><AuthHeader xmlns="http://www.majesticusa.com/majesticweb_xml/"><Username>' . $majesticusaLoginEmail . '</Username><Password>' . $majesticusaPassword . '</Password></AuthHeader></soap:Header>
  <soap:Body>
    <SearchHotelAdvancedV1 xmlns="http://www.majesticusa.com/majesticweb_xml/">
      <hoteltypeid>1</hoteltypeid>
      <cityid>598</cityid><arrival>' . strftime("%Y-%m-%d", $from) . 'T11:00:00.0Z</arrival><departure>' . strftime("%Y-%m-%d", $to) . 'T11:00:00.0Z</departure><qty>' . $rooms . '</qty><paxlist>';
for ($r = 0; $r < count($selectedAdults); $r ++) {
    $raw = $raw . '<adults>' . $selectedAdults[$r] . '</adults>';
    if ($selectedChildren[$r] > 0) {
        $raw = $raw . '<child>' . $selectedChildren[$r] . '</child>';
        for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
            $raw = $raw . '<childage><int>' . $selectedChildrenAges[$r][$z] . '</int></childage>';
        }
    } else {
        $raw = $raw . '<child>' . $selectedChildren[$r] . '</child>';
    }
}
$raw = $raw . '</paxlist>
      <pagesize>10</pagesize>
      <targetpage>1</targetpage>
      <sort>0</sort>
    </SearchHotelAdvancedV1>
  </soap:Body>
</soap:Envelope>';
if ($majesticusaServiceURL != "" and $majesticusaLoginEmail != "" and $majesticusaPassword) {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $majesticusaServiceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "Accept: application/xml",
        "Content-type: text/xml",
        "Content-length: " . strlen($raw),
        "SOAPAction: http://www.majesticusa.com/majesticweb_xml/SearchHotelAdvancedV1"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_majestic');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $majesticusaServiceURL . $raw,
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
    $Envelope = $inputDoc->getElementsByTagName('Envelope');
    $Body = $Envelope->item(0)->getElementsByTagName("Body");
    $SearchHotelAdvancedV1Response = $Body->item(0)->getElementsByTagName("SearchHotelAdvancedV1Response");
    $SearchHotelAdvancedV1Result = $SearchHotelAdvancedV1Response->item(0)->getElementsByTagName("SearchHotelAdvancedV1Result");
    $Root = $SearchHotelAdvancedV1Result->item(0)->getElementsByTagName("Root");
    $BookingOptions = $Root->item(0)->getElementsByTagName("BookingOptions");
    $Hotel = $Root->item(0)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        for ($hl = 0; $hl < $Hotel->length; $hl ++) {
            $Id = $Hotel->item($hl)->getElementsByTagName("Id");
            if ($Id->length > 0) {
                $Id = $Id->item(0)->nodeValue;
            } else {
                $Id = "";
            }
            $shid = $Id;
            $sfilter[] = " sid='$Id' ";
            $Name = $Hotel->item($hl)->getElementsByTagName("Name");
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
            $Location = $Hotel->item($hl)->getElementsByTagName("Location");
            if ($Name->length > 0) {
                $Location = $Location->item(0)->nodeValue;
            } else {
                $Location = "";
            }
            $Category = $Hotel->item($hl)->getElementsByTagName("Category");
            if ($Category->length > 0) {
                $Category = $Category->item(0)->nodeValue;
            } else {
                $Category = "";
            }
            $ProductId = $Hotel->item($hl)->getElementsByTagName("ProductId");
            if ($ProductId->length > 0) {
                $ProductId = $ProductId->item(0)->nodeValue;
            } else {
                $ProductId = "";
            }
            // SpecialNotes
            $SpecialNotes = $Hotel->item($hl)->getElementsByTagName("SpecialNotes");
            if ($SpecialNotes->length > 0) {
                for ($i = 0; $i < $SpecialNotes->length; $i ++) {
                    $Note = $SpecialNotes->item($i)->getElementsByTagName("Note");
                    if ($Note->length > 0) {
                        $Note = $Note->item(0)->nodeValue;
                    } else {
                        $Note = "";
                    }
                    $From = $SpecialNotes->item($i)->getElementsByTagName("From");
                    if ($From->length > 0) {
                        $From = $From->item(0)->nodeValue;
                    } else {
                        $From = "";
                    }
                    $To = $SpecialNotes->item($i)->getElementsByTagName("To");
                    if ($To->length > 0) {
                        $To = $To->item(0)->nodeValue;
                    } else {
                        $To = "";
                    }
                }
            }
            // Room
            $Room = $Hotel->item($hl)->getElementsByTagName("Room");
            if ($Room->length > 0) {
                for ($rm = 0; $rm < $Room->length; $rm ++) {
                    $RoomType = $Room->item($rm)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomType = $RoomType->item(0)->nodeValue;
                    } else {
                        $RoomType = "";
                    }
                    $RoomID = $Room->item($rm)->getElementsByTagName("RoomID");
                    if ($RoomID->length > 0) {
                        $RoomID = $RoomID->item(0)->nodeValue;
                    } else {
                        $RoomID = "";
                    }
                    // Option
                    $Option = $Room->item($rm)->getElementsByTagName("Option");
                    if ($Option->length > 0) {
                        $BookParam = $Option->item(0)->getElementsByTagName("BookParam");
                        if ($BookParam->length > 0) {
                            $BookParam = $BookParam->item(0)->nodeValue;
                        } else {
                            $BookParam = "";
                        }
                        $OptionID = $Option->item(0)->getElementsByTagName("OptionID");
                        if ($OptionID->length > 0) {
                            $OptionID = $OptionID->item(0)->nodeValue;
                        } else {
                            $OptionID = "";
                        }
                        $OptionStatus = $Option->item(0)->getElementsByTagName("OptionStatus");
                        if ($OptionStatus->length > 0) {
                            $OptionStatus = $OptionStatus->item(0)->nodeValue;
                        } else {
                            $OptionStatus = "";
                        }
                        $OptionIsOffer = $Option->item(0)->getElementsByTagName("OptionIsOffer");
                        if ($OptionIsOffer->length > 0) {
                            $OptionIsOffer = $OptionIsOffer->item(0)->nodeValue;
                        } else {
                            $OptionIsOffer = "";
                        }
                        $Rate = $Option->item(0)->getElementsByTagName("Rate");
                        if ($Rate->length > 0) {
                            $AccId = $Rate->item(0)->getElementsByTagName("AccId");
                            if ($AccId->length > 0) {
                                $AccId = $AccId->item(0)->nodeValue;
                            } else {
                                $AccId = "";
                            }
                            $Acc = $Rate->item(0)->getElementsByTagName("Acc");
                            if ($Acc->length > 0) {
                                $Acc = $Acc->item(0)->nodeValue;
                            } else {
                                $Acc = "";
                            }
                            $MaxOccup = $Rate->item(0)->getElementsByTagName("MaxOccup");
                            if ($MaxOccup->length > 0) {
                                $MaxOccup = $MaxOccup->item(0)->nodeValue;
                            } else {
                                $MaxOccup = "";
                            }
                            $OptionNightsUnitTotal = $Rate->item(0)->getElementsByTagName("OptionNightsUnitTotal");
                            if ($OptionNightsUnitTotal->length > 0) {
                                $OptionNightsUnitTotal = $OptionNightsUnitTotal->item(0)->nodeValue;
                            } else {
                                $OptionNightsUnitTotal = "";
                            }
                            $OptionMealPlanUnitTotal = $Rate->item(0)->getElementsByTagName("OptionMealPlanUnitTotal");
                            if ($OptionMealPlanUnitTotal->length > 0) {
                                $OptionMealPlanUnitTotal = $OptionMealPlanUnitTotal->item(0)->nodeValue;
                            } else {
                                $OptionMealPlanUnitTotal = "";
                            }
                            $OptionUnit = $Rate->item(0)->getElementsByTagName("OptionUnit");
                            if ($OptionUnit->length > 0) {
                                $OptionUnit = $OptionUnit->item(0)->nodeValue;
                            } else {
                                $OptionUnit = "";
                            }
                            $OptionNightsTotal = $Rate->item(0)->getElementsByTagName("OptionNightsTotal");
                            if ($OptionNightsTotal->length > 0) {
                                $OptionNightsTotal = $OptionNightsTotal->item(0)->nodeValue;
                            } else {
                                $OptionNightsTotal = "";
                            }
                            $OptionFreeNightsTotal = $Rate->item(0)->getElementsByTagName("OptionFreeNightsTotal");
                            if ($OptionFreeNightsTotal->length > 0) {
                                $OptionFreeNightsTotal = $OptionFreeNightsTotal->item(0)->nodeValue;
                            } else {
                                $OptionFreeNightsTotal = "";
                            }
                            $OptionCommissionPercentage = $Rate->item(0)->getElementsByTagName("OptionCommissionPercentage");
                            if ($OptionCommissionPercentage->length > 0) {
                                $OptionCommissionPercentage = $OptionCommissionPercentage->item(0)->nodeValue;
                            } else {
                                $OptionCommissionPercentage = "";
                            }
                            $OptionCommissionValue = $Rate->item(0)->getElementsByTagName("OptionCommissionValue");
                            if ($OptionCommissionValue->length > 0) {
                                $OptionCommissionValue = $OptionCommissionValue->item(0)->nodeValue;
                            } else {
                                $OptionCommissionValue = "";
                            }
                            $OptionNightsNetTotal = $Rate->item(0)->getElementsByTagName("OptionNightsNetTotal");
                            if ($OptionNightsNetTotal->length > 0) {
                                $OptionNightsNetTotal = $OptionNightsNetTotal->item(0)->nodeValue;
                            } else {
                                $OptionNightsNetTotal = "";
                            }
                            $OptionResortFeeTotal = $Rate->item(0)->getElementsByTagName("OptionResortFeeTotal");
                            if ($OptionResortFeeTotal->length > 0) {
                                $OptionResortFeeTotal = $OptionResortFeeTotal->item(0)->nodeValue;
                            } else {
                                $OptionResortFeeTotal = "";
                            }
                            $Price = $Rate->item(0)->getElementsByTagName("Price");
                            if ($Price->length > 0) {
                                $IdP = $Price->item(0)->getElementsByTagName("Id");
                                if ($IdP->length > 0) {
                                    $IdP = $IdP->item(0)->nodeValue;
                                } else {
                                    $IdP = "";
                                }
                                $IsNoRate = $Price->item(0)->getElementsByTagName("IsNoRate");
                                if ($IsNoRate->length > 0) {
                                    $IsNoRate = $IsNoRate->item(0)->nodeValue;
                                } else {
                                    $IsNoRate = "";
                                }
                                $FromP = $Price->item(0)->getElementsByTagName("From");
                                if ($FromP->length > 0) {
                                    $FromP = $FromP->item(0)->nodeValue;
                                } else {
                                    $FromP = "";
                                }
                                $ToP = $Price->item(0)->getElementsByTagName("To");
                                if ($ToP->length > 0) {
                                    $ToP = $ToP->item(0)->nodeValue;
                                } else {
                                    $ToP = "";
                                }
                                $Nights = $Price->item(0)->getElementsByTagName("Nights");
                                if ($Nights->length > 0) {
                                    $Nights = $Nights->item(0)->nodeValue;
                                } else {
                                    $Nights = "";
                                }
                                $NightsNormal = $Price->item(0)->getElementsByTagName("NightsNormal");
                                if ($NightsNormal->length > 0) {
                                    $NightsNormal = $NightsNormal->item(0)->nodeValue;
                                } else {
                                    $NightsNormal = "";
                                }
                                $NightsSpecial = $Price->item(0)->getElementsByTagName("NightsSpecial");
                                if ($NightsSpecial->length > 0) {
                                    $NightsSpecial = $NightsSpecial->item(0)->nodeValue;
                                } else {
                                    $NightsSpecial = "";
                                }
                                $Status = $Price->item(0)->getElementsByTagName("Status");
                                if ($Status->length > 0) {
                                    $Status = $Status->item(0)->nodeValue;
                                } else {
                                    $Status = "";
                                }
                                $RefPrice = $Price->item(0)->getElementsByTagName("RefPrice");
                                if ($RefPrice->length > 0) {
                                    $RefPrice = $RefPrice->item(0)->nodeValue;
                                } else {
                                    $RefPrice = "";
                                }
                                $NetPrice = $Price->item(0)->getElementsByTagName("NetPrice");
                                if ($NetPrice->length > 0) {
                                    $NetPrice = $NetPrice->item(0)->nodeValue;
                                } else {
                                    $NetPrice = "";
                                }
                                $Remarks = $Price->item(0)->getElementsByTagName("Remarks");
                                if ($Remarks->length > 0) {
                                    $Remarks = $Remarks->item(0)->nodeValue;
                                } else {
                                    $Remarks = "";
                                }
                                $NtFreePolicy = $Price->item(0)->getElementsByTagName("NtFreePolicy");
                                if ($NtFreePolicy->length > 0) {
                                    $NtFreePolicy = $NtFreePolicy->item(0)->nodeValue;
                                } else {
                                    $NtFreePolicy = "";
                                }
                                // resortfee
                                $resortfee = $Price->item(0)->getElementsByTagName("resortfee");
                                if ($resortfee->length > 0) {
                                    $fee = $resortfee->item(0)->getElementsByTagName("fee");
                                    if ($fee->length > 0) {
                                        $fee = $fee->item(0)->nodeValue;
                                    } else {
                                        $fee = "";
                                    }
                                    $total = $resortfee->item(0)->getElementsByTagName("total");
                                    if ($total->length > 0) {
                                        $total = $fee->item(0)->nodeValue;
                                    } else {
                                        $total = "";
                                    }
                                    $feeDescription = $resortfee->item(0)->getElementsByTagName("feeDescription");
                                    if ($feeDescription->length > 0) {
                                        $feeDescription = $feeDescription->item(0)->nodeValue;
                                    } else {
                                        $feeDescription = "";
                                    }
                                    $NoteF = $resortfee->item(0)->getElementsByTagName("Note");
                                    if ($NoteF->length > 0) {
                                        $NoteF = $NoteF->item(0)->nodeValue;
                                    } else {
                                        $NoteF = "";
                                    }
                                    $usage = $resortfee->item(0)->getElementsByTagName("usage");
                                    if ($usage->length > 0) {
                                        $usage = $usage->item(0)->nodeValue;
                                    } else {
                                        $usage = "";
                                    }
                                }
                                // MealPlan
                                $MealPlan = $Price->item(0)->getElementsByTagName("MealPlan");
                                if ($MealPlan->length > 0) {
                                    $Meal = $MealPlan->item(0)->getElementsByTagName("Meal");
                                    if ($Meal->length > 0) {
                                        for ($k = 0; $k < $Meal->length; $k ++) {
                                            $IdM = $Meal->item($k)->getElementsByTagName("Id");
                                            if ($IdM->length > 0) {
                                                $IdM = $IdM->item(0)->nodeValue;
                                            } else {
                                                $IdM = "";
                                            }
                                            $CodeM = $Meal->item($k)->getElementsByTagName("Code");
                                            if ($CodeM->length > 0) {
                                                $CodeM = $CodeM->item(0)->nodeValue;
                                            } else {
                                                $CodeM = "";
                                            }
                                            $NameM = $Meal->item($k)->getElementsByTagName("Name");
                                            if ($NameM->length > 0) {
                                                $NameM = $NameM->item(0)->nodeValue;
                                            } else {
                                                $NameM = "";
                                            }
                                            $AgeTypeM = $Meal->item($k)->getElementsByTagName("AgeType");
                                            if ($AgeTypeM->length > 0) {
                                                $AgeTypeM = $AgeTypeM->item(0)->nodeValue;
                                            } else {
                                                $AgeTypeM = "";
                                            }
                                            $GroupNameM = $Meal->item($k)->getElementsByTagName("GroupName");
                                            if ($GroupNameM->length > 0) {
                                                $GroupNameM = $GroupNameM->item(0)->nodeValue;
                                            } else {
                                                $GroupNameM = "";
                                            }
                                            $RefPriceM = $Meal->item($k)->getElementsByTagName("RefPrice");
                                            if ($RefPriceM->length > 0) {
                                                $RefPriceM = $RefPriceM->item(0)->nodeValue;
                                            } else {
                                                $RefPriceM = "";
                                            }
                                            $NetPriceM = $Meal->item($k)->getElementsByTagName("NetPrice");
                                            if ($NetPriceM->length > 0) {
                                                $NetPriceM = $NetPriceM->item(0)->nodeValue;
                                            } else {
                                                $NetPriceM = "";
                                            }
                                            $IsIncludedM = $Meal->item($k)->getElementsByTagName("IsIncluded");
                                            if ($IsIncludedM->length > 0) {
                                                $IsIncludedM = $IsIncludedM->item(0)->nodeValue;
                                            } else {
                                                $IsIncludedM = "";
                                            }
                                        }
                                    }
                                } else {
                                    $CodeM = "";
                                }
                                $RoomType = str_replace("<B>", "-", $RoomType);
                                $RoomType = str_replace("</B>", "", $RoomType);
                                $RoomType = ucwords(strtolower($RoomType));
                                for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                                    if (is_array($tmp[$shid])) {
                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                    } else {
                                        $baseCounterDetails = 0;
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $Id;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-47";
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RoomType;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomID;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomtype'] = $RoomType;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['acc'] = $Acc;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['accid'] = $AccId;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $RefPrice;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $NetPrice;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = $OptionStatus;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['BookParam'] = $BookParam;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['optionid'] = $OptionID;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['maxpersons'] = $MaxOccup;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                    try {
                                        $sql = "select mapped from board_mapping where description='" . addslashes($NameM) . "'";
                                        $statement = $db->createStatement($sql);
                                        $statement->prepare();
                                        $row_board_mapping = $statement->execute();
                                        $row_board_mapping->buffer();
                                        if ($row_board_mapping->valid()) {
                                            $row_board_mapping = $row_board_mapping->current();
                                            $NameM = $row_board_mapping["mapped"];
                                        }
                                    } catch (\Exception $e) {
                                        $logger = new Logger();
                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                        $logger->addWriter($writer);
                                        $logger->info($e->getMessage());
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($NameM);
                                    $pricebreakdown = array();
                                    $pricebreakdownCount = 0;
                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                        $amount = $RefPrice / $noOfNights;
                                        if ($majesticusaMarkup != 0) {
                                            $amount = $amount + (($amount * $majesticusaMarkup) / 100);
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
                                        if ($majesticusaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $scurrency;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $scurrency;
                                }
                            }
                        }
                    }
                    $majestic = true;
                }
            }
        }
    }
}
if ($majestic == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mmajestic where " . $sfilter;
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        $result2->buffer();
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            foreach ($resultSet2 as $row2) {
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
        $supplier = 47;
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_majestic');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_majestic');
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
?>