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
error_log("\r\nSpecial Tours - Start\r\n", 3, "/srv/www/htdocs/error_log");
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$specialtours = false;
$db2 = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml33, latitude, longitude from cities where id=" . $destination;
$statement2 = $db2->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml33 = $row_settings["city_xml33"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml33 = 0;
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
    $sql = "select value from settings where name='SpecialToursDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_specialtours";
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
$sql = "select value from settings where name='SpecialTourslogin' and affiliate_id=$affiliate_id_specialtours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SpecialTourslogin = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='SpecialTourspassword' and affiliate_id=$affiliate_id_specialtours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SpecialTourspassword = base64_decode($row_settings['value']);
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='SpecialToursMarkup' and affiliate_id=$affiliate_id_specialtours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SpecialToursMarkup = (double) $row_settings['value'];
} else {
    $SpecialToursMarkup = 0;
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$sql = "select value from settings where name='SpecialToursServiceURL' and affiliate_id=$affiliate_id_specialtours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $SpecialToursServiceURL = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$dateCheckin = new DateTime(strftime("%d-%m-%Y", $from));
$dayCheckin = $dateCheckin->format('d');
$monthCheckin = $dateCheckin->format('m');
$yearCheckin = $dateCheckin->format('Y');

$dateCheckout = new DateTime(strftime("%d-%m-%Y", $to));
$dayCheckout = $dateCheckout->format('d');
$monthCheckout = $dateCheckout->format('m');
$yearCheckout = $dateCheckout->format('Y');

$raw = 'pXML=<?xml version="1.0" encoding="utf-8" ?> 
<HtlAllocation>
    <Version>1.41</Version> 
    <Agent> 
        <UName>' . $SpecialTourslogin . '</UName> 
        <UPsw>' . $SpecialTourspassword . '</UPsw> 
    </Agent> 
    <ClientCountryID>68</ClientCountryID>
<CoID>68</CoID>
<CiID>11934</CiID>
<HoID>0</HoID>
<RT>DBL</RT>
<Rooms>' . $rooms . '</Rooms>
<inDate>
<Day>' . $dayCheckin . '</Day>
<Month>' . $monthCheckin . '</Month>
<Year>' . $yearCheckin . '</Year>
</inDate>
<outDate>
<Day>' . $dayCheckout . '</Day>
<Month>' . $monthCheckout . '</Month>
<Year>' . $yearCheckout . '</Year>
</outDate>
<PriceCurrency>' . $scurrency . '</PriceCurrency>
<OnlyAvailable>Y</OnlyAvailable>
<IncludeCLXPolicy>Y</IncludeCLXPolicy>
</HtlAllocation>';
// <RT Ages=’N|N|N’>DBL</RT>
// <HStars>
// <HStar ID="N" />
// <HStar ID="N" />
// </HStars>
// <HFacs>
// <HFac ID="NN" />
// <HFac ID="NN" />
// </HFacs>
// <RST>AAA</RST>
// <HtlLocation>AAA...</HtlLocation>
// <PriceFrom>NNNNNNNNNN.NNNN</PriceFrom>
// <PriceTo>NNNNNNNNNN.NNNN</PriceTo>
// <ContractFilter>AAA</ContractFilter>
if ($SpecialToursServiceURL != "" and $SpecialTourslogin != "" and $SpecialTourspassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $SpecialToursServiceURL . "STOLXMLAllocation.asmx/AllocationSearch");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'Accept-Encoding: gzip',
        'Content-Length: ' . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
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
        $insert->into('log_specialtours');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $SpecialToursServiceURL . "STOLXMLAllocation.asmx/AllocationSearch",
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
    $array = json_decode($response, true);
    // Descomentar para ver o vector
    // Nao esquecer de alterar o session id para testar por causa de cache
    // Echo para ver o array completro
    // echo "<xmp>";
    // var_dump($array);
    // echo "</xmp>";
    // die();
    error_log("\r\n" . print_r($array, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("Alloc");
    
    // Results
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $HoID = $node->item($rAUX)->getElementsByTagName("HoID");
        if ($HoID->length > 0) {
            $HoID = $HoID->item(0)->nodeValue;
        } else {
            $HoID = "";
        }
        $shid = $HoID;
        $RST = $node->item($rAUX)->getElementsByTagName("RST");
        if ($RST->length > 0) {
            $RST = $RST->item(0)->nodeValue;
        } else {
            $RST = "";
        }
        $RoomDescr = $valueAlloc['RoomDescr'];
        $RoomDescr = $node->item($rAUX)->getElementsByTagName("RoomDescr");
        if ($RoomDescr->length > 0) {
            $RoomDescr = $RoomDescr->item(0)->nodeValue;
        } else {
            $RoomDescr = "";
        }
        $BFK = $node->item($rAUX)->getElementsByTagName("BFK");
        if ($BFK->length > 0) {
            $BFK = $BFK->item(0)->nodeValue;
        } else {
            $BFK = "";
        }
        // $Pr = $valueAlloc['Pr'];
        // $Pr = $node->item($rAUX)->getElementsByTagName("Pr");
        // if ($Pr->length > 0) {
        // $Pr = $Pr->item(0)->nodeValue;
        // } else {
        // $Pr = "";
        // }
        $PrCur = $node->item($rAUX)->getElementsByTagName("PrCur");
        if ($PrCur->length > 0) {
            $PrCur = $PrCur->item(0)->nodeValue;
        } else {
            $PrCur = "";
        }
        $TotPricePUB = $node->item($rAUX)->getElementsByTagName("TotPricePUB");
        if ($TotPricePUB->length > 0) {
            $TotPricePUB = $TotPricePUB->item(0)->nodeValue;
        } else {
            $TotPricePUB = "";
        }
        $Avail = $node->item($rAUX)->getElementsByTagName("Avail");
        if ($Avail->length > 0) {
            $Avail = $Avail->item(0)->nodeValue;
        } else {
            $Avail = "";
        }
        $PrCD = $node->item($rAUX)->getElementsByTagName("PrCD");
        if ($PrCD->length > 0) {
            $PrCD = $PrCD->item(0)->nodeValue;
        } else {
            $PrCD = "";
        }
        $TotalEUR = $node->item($rAUX)->getElementsByTagName("TotalEUR");
        if ($TotalEUR->length > 0) {
            $TotalEUR = $TotalEUR->item(0)->nodeValue;
        } else {
            $TotalEUR = "";
        }
        $CLXPolicy = $node->item($rAUX)->getElementsByTagName("CLXPolicy");
        if ($CLXPolicy->length > 0) {
            $LastNoChargeCLX = $CLXPolicy->item(0)->getElementsByTagName("LastNoChargeCLX");
            if ($LastNoChargeCLX->length > 0) {
                $LastNoChargeCLX = $LastNoChargeCLX->item(0)->nodeValue;
            } else {
                $LastNoChargeCLX = "";
            }
            $NonShow = $CLXPolicy->item(0)->getElementsByTagName("NonShow ");
            if ($NonShow->length > 0) {
                $chargeN = $NonShow->item(0)->getAttribute("charge");
                $currencyN = $NonShow->item(0)->getAttribute("currency");
                $publicchargeN = $NonShow->item(0)->getAttribute("publiccharge");
            } else {
                $NonShow = "";
            }
            $CLXDetails = $CLXPolicy->item(0)->getElementsByTagName("CLXDetails ");
            if ($CLXDetails->length > 0) {
                $from = $CLXDetails->item(0)->getAttribute("from");
                $chargeCLX = $CLXDetails->item(0)->getAttribute("charge");
                $currencyCLX = $CLXDetails->item(0)->getAttribute("currency");
                $publicchargeCLX = $CLXDetails->item(0)->getAttribute("publiccharge");
            } else {
                $NonShow = "";
            }
        }
        $Remarks = $node->item($rAUX)->getElementsByTagName("Remarks");
        if ($Remarks->length > 0) {
            $Remarks = $Remarks->item(0)->nodeValue;
        } else {
            $Remarks = "";
        }
        
        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
            // if (($selectedAdults[$zRooms] <= $adults and $selectedChildren[$zRooms] <= $children) or ($selectedAdults[$zRooms] <= $adults and (($selectedAdults[$zRooms] + $selectedChildren[$zRooms]) <= ($adults + $children)))) {
            if (is_array($tmp[$shid]['details'][$zRooms])) {
                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
            } else {
                $baseCounterDetails = 0;
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $HoID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $RST . '' . $RoomDescr;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-33";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $Pr;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $HoID;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['price'] = $Pr;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescr'] = $RoomDescr;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
            // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = $nonrefundable;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $TotalEUR;
            $tmp[$code]['details'][$zRooms][$baseCounterDetails]['special'] = false;
            $tmp[$code]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($BFK);
            // $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['boardtype'] = $meal_type;
            // $t = $meal_type;
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $amount = $noOfNights * $TotalEUR;
                if ($SpecialToursMarkup != 0) {
                    $amount = $amount + (($amount * $SpecialToursMarkup) / 100);
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
                if ($SpecialToursMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
            
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
            // }
        }
        $sfilter[] = " sid='$shid' ";
        $specialtours = true;
    }
    // echo "<xmp>";
    // var_dump($sfilter);
    // echo "</xmp>";
    if ($specialtours == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = "select hid, sid from xmlhotels_mspecialtours where " . $sfilter;
            error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
            error_log("\r\n $query \r\n", 3, "/srv/www/htdocs/error_log");
            $supplier = 33;
            // Store Session
            $db2 = new \Zend\Db\Adapter\Adapter($config);
            $sql = new Sql($db2);
            $delete = $sql->delete();
            $delete->from('quote_session_specialtours');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            try {
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $sql = new Sql($db2);
            $insert = $sql->insert();
            $insert->into('quote_session_specialtours');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            try {
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $db2->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }
}
error_log("\r\nSpecial Tours - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>