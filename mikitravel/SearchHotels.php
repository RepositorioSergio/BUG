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
$mikitravel = false;
$sql = "select city_xml16, latitude, longitude from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml16 = $row_settings["city_xml16"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml16 = 0;
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='mikitravelDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_mikitravel";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='mikitravelagentcode' and affiliate_id=$affiliate_id_mikitravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitravelagentcode = $row_settings['value'];
}
$sql = "select value from settings where name='mikitravelpassword' and affiliate_id=$affiliate_id_mikitravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitravelpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='mikitravelMarkup' and affiliate_id=$affiliate_id_mikitravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitravelMarkup = (double) $row_settings['value'];
} else {
    $mikitravelMarkup = 0;
}
$sql = "select value from settings where name='mikitravelserviceurl' and affiliate_id=$affiliate_id_mikitravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mikitravelserviceurl = $row_settings['value'];
}
$pax = "";
for ($i = 0; $i < count($selectedAdults); $i ++) {
    $pax .= '&pax=' . $selectedAdults[$i];
    for ($z = 0; $z < $selectedChildren[$i]; $z ++) {
        $pax .= ',' . $selectedChildrenAges[$i][$z];
    }
}

$dateStart = new DateTime(strftime("%d-%m-%Y", $from));
$dateEnd = new DateTime(strftime("%d-%m-%Y", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
// echo $pax;
// die();
// $city_xml16 = "";
// if ($city_xml16 != "") {
// $raw = 'hotelSearch/?currencyCode=' . $scurrency . '&languageCode=' . $sourceMarket . $pax . '&checkinDate=' . strftime("%d-%m-%Y", $from) . '&checkoutDate=' . strftime("%d-%m-%Y", $to) . '&destination_code=' . $city_xml16;
// } else {
// $raw = 'hotelSearch/?' . $pax . '&checkoutDate=' . strftime("%d-%m-%Y", $to) . '&checkinDate=' . strftime("%d-%m-%Y", $from) . '&lat=' . $latitude . '&lon=' . $longitude . '&radius=1000&languageCode=' . $sourceMarket . '&currencyCode=' . $scurrency;
// }
// echo $raw;
// die();
$city_xml16 = "20142";
$raw = '{
 "requestAuditInfo": {
  "agentCode": "' . $mikitravelagentcode . '",
  "requestPassword": "' . $mikitravelpassword . '",
  "requestDateTime": "' . strftime("%Y-%m-%dT%H:%m:%S.000Z", time()) . '",
  "requestID": 111111111
 },
 "hotelSearchCriteria": {
  "destination": {
   "cityNumbers": [
    ' . $city_xml16 . '
   ]
  },
  "stayPeriod": {
   "checkinDate":"' . strftime("%Y-%m-%d", $from) . '",
   "numberOfNights": ' . $noOfNights . '
  },
  "rooms": [
   {
    "roomNo": 1,
    "guests": [
     {
      "type": "ADT"
     }
    ]
   }
  ],
  "availabilityCriteria": {
   "availabilityIndicator": 1
  },
  "priceCriteria": {
   "returnBestPriceIndicator": true
  },
  "languageCode":"en",
  "currencyCode":"' . $scurrency . '",
  "paxNationality":"' . $sourceMarket . '"
 },
 "versionNumber": "7.0"
}';

if ($mikitravelserviceurl != "" and $mikitravelagentcode != "" and $mikitravelpassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    //error_log("\r\n $mikitravelserviceurl" . "hotelSearch\r\n", 3, "/srv/www/htdocs/error_log");
    //error_log("\r\n $raw \r\n", 3, "/srv/www/htdocs/error_log");
    curl_setopt($ch, CURLOPT_URL, $mikitravelserviceurl . "hotelSearch");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept-Encoding: gzip',
        'Content-Type: application/json',
        'User-Agent:Apache-HttpClient/4.1.1 (java 1.5)',
        'Host:test.mikinet.co.uk'
    ));
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    /* if ($response === false) {
        error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
    } else {
        error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
    } */
    curl_close($ch);
     //error_log("\r\n $response \r\n", 3, "/srv/www/htdocs/error_log");
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_mikitravel');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $mikitravelserviceurl . $raw,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    // echo $response;
    // die();
    error_log("\r\n  ANTES DE LER AGORA 1406 \r\n", 3, "/srv/www/htdocs/error_log");
    $array = json_decode($response, true);
    // Descomentar para ver o vector
    // Nao esquecer de alterar o session id para testar por causa de cache
    // Echo para ver o array completro
    // echo "<xmp>";
    // var_dump($array);
    // echo "</xmp>";
    // die();
     //error_log("\r\nVECTOR " . print_r($array, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    $result = $array['hotels'];
    // Hotels
    foreach ($result as $key => $valueHotels) {
        $currencyCode = $valueHotels['currencyCode'];
        //error_log("\r\n  currencyCode   $currencyCode \r\n", 3, "/srv/www/htdocs/error_log");
        $giataCode = $valueHotels['giataCode'];
        $productCode = $valueHotels['productCode'];
        $shid = $productCode;
        $sfilter[] = " sid='$shid' ";
        //ROOMOPTIONS
        $roomOptions = $valueHotels['roomOptions'];
        $allowModificattions = $roomOptions[0]['allowModificattions'];
        $roomTypeCode = $roomOptions[0]['roomTypeCode'];
        $availalityStatus = $roomOptions[0]['availalityStatus'];
        
        // CANCELLATION POLICIES
        $cancellationPolicies = $roomOptions[0]['cancellationPolicies'];
        $appliesFrom = $cancellationPolicies[0]['appliesFrom'];
        $cancellationCharge = $cancellationPolicies[0]['cancellationCharge'];
        $percentage = $cancellationCharge['percentage'];
        $fullStay = $cancellationPolicies[0]['fullStay'];
        
        $refundable = $roomOptions[0]['refundable'];
        //error_log("\r\n  availalityStatus2   $refundable \r\n", 3, "/srv/www/htdocs/error_log");
        // MEALBASIS
        $mealBasis = $roomOptions[0]['mealBasis'];
        $mealBasisCode = $mealBasis['mealBasisCode'];
        $multipleMealBasis = $mealBasis['multipleMealBasis'];
        $includedMeals = $mealBasis['includedMeals'];
        $description = $includedMeals[0]['description'];
        $mealID = $includedMeals[0]['mealID'];
        $mealType = $includedMeals[0]['mealType'];
        // error_log("\r\n  availalityStatus3   $mealType \r\n", 3, "/srv/www/htdocs/error_log");
        
        $rateIdentifier = $roomOptions[0]['rateIdentifier'];
        $roomCategory = $roomOptions[0]['roomCategory'];
        $roomDescription = $roomOptions[0]['roomDescription'];
        $roomNumbers = $roomOptions[0]['roomNumbers'];
        //error_log("\r\n  availalityStatus4   $roomNumbers \r\n", 3, "/srv/www/htdocs/error_log");
        //ROOM RESTRITIONS
        $roomRestrictions = $roomOptions[0]['roomRestrictions'];
        $typeId = $roomRestrictions[0]['typeId'];
        $values = $roomRestrictions[0]['values'];
        $valueRoom = $values[0]['value'];
        $idRoom = $values[0]['id'];
        // TOTAL PRICE
        $roomTotalPrice = $roomOptions[0]['roomTotalPrice'];
        $priceType = $roomTotalPrice['priceType'];
        $price = $roomTotalPrice['price'];
        $valuePrice = $price['value'];
        $specialOffers = $roomTotalPrice['specialOffers'];
        $offersPresent = $specialsOffers['offersPresent'];
        $supplierName = $roomTotalPrice['supplierName'];
         error_log("\r\n  valuePrice   $valuePrice \r\n", 3, "/srv/www/htdocs/error_log");
        // $valueProducts['minimum_selling_price']
        // $valueProducts['view']
        // echo "<xmp>";
        // var_dump($valueProducts);
        // echo "</xmp>";
        // Rooms
        // Markup
        if ($mikitravelMarkup != 0) {
            $valuePrice = $valuePrice + (($valuePrice * $mikitravelMarkup) / 100);
        }
        // Geo target markup
        if ($internalmarkup != 0) {
            $valuePrice = $valuePrice + (($valuePrice * $internalmarkup) / 100);
        }
        // Agent markup
        if ($agent_markup != 0) {
            $valuePrice = $valuePrice + (($valuePrice * $agent_markup) / 100);
        }
        // Fallback Markup
        if ($mikitravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
            $valuePrice = $valuePrice + (($valuePrice * $HotelsMarkupFallback) / 100);
        }
        // Agent discount
        if ($agent_discount != 0) {
            $valuePrice = $valuePrice - (($valuePrice * $agent_discount) / 100);
        }
        if ($scurrency != "" and $currency != $scurrency) {
            $valuePrice = $CurrencyConverter->convert($valuePrice, $currency, $scurrency);
        }
        error_log("\r\n  CHEGUEI MARKUP \r\n", 3, "/srv/www/htdocs/error_log");
        $pricebreakdown = array();
        $pricebreakdownCount = 0;
        for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
            $amount = $noOfNights * $valuePrice;
            if ($mikitravelMarkup != 0) {
                $amount = $amount + (($amount * $mikitravelMarkup) / 100);
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
            if ($mikitravelMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
            // if (($selectedAdults[$zRooms] <= $adults and $selectedChildren[$zRooms] <= $children) or ($selectedAdults[$zRooms] <= $adults and (($selectedAdults[$zRooms] + $selectedChildren[$zRooms]) <= ($adults + $children)))) {
            if (is_array($tmp[$shid]['details'][$zRooms])) {
                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
            } else {
                $baseCounterDetails = 0;
            }
            
            if ($roomCategory != "") {
                $roomDescription2 = $roomCategory . " - " . $roomDescription;
            } else {
                $roomDescription2 = $roomDescription;
            }
            
            
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $productCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $productCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $roomDescription2;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-16";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $valuePrice;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $productCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['price'] = $valuePrice;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomCategory'] = $roomCategory;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomDescription'] = $roomDescription;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomTypeCode'] = $roomTypeCode;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['refundable'] = $refundable;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateIdentifier'] = $rateIdentifier;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $valuePrice;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['boardtype'] = $mealBasisCode;
            $t = $meal_type;
            try {
                $sql = "select mapped from board_mapping where description='" . addslashes($t) . "'";
                $statement = $db->createStatement($sql);
                $statement->prepare();
                $row_board_mapping = $statement->execute();
                $row_board_mapping->buffer();
                if ($row_board_mapping->valid()) {
                    $row_board_mapping = $row_board_mapping->current();
                    $t = $row_board_mapping["mapped"];
                }
            } catch (Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($mealBasisCode);
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $scurrency;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
            if ($offersPresent == true) {
                $tmp[$shid]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $translator->translate("Special Offer");
            } else {
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            }
            // }
        }
       // error_log("\r\n  CHEGUEI FIM \r\n", 3, "/srv/www/htdocs/error_log");
        // ksort($tmp[$shid]['details']);
        // }
        
        $mikitravel = true;
    }
    //error_log("\r\nVECTOR TMP " . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    // echo "<xmp>";
    // var_dump($tmp);
    // echo "</xmp>";
    // echo "<xmp>";
    // var_dump($sfilter);
    // echo "</xmp>";
    if ($mikitravel == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mmikitravel where " . $sfilter;
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $result = $statement->execute();
            $result->buffer();
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                foreach ($resultSet as $row) {
                    // $sidfilter[] = "id=" . $row2->hid;
                    $sidfilter[] = $row->hid;
                    if (is_array($hotels_array[$row->hid])) {
                        // Append to original details
                        $tmph = $hotels_array[$row->hid]['details'];
                        $tmps = $tmp[$row->sid]['details'];
                        foreach ($tmph as $key => $value) {
                            $last = count($tmph[$key]);
                            foreach ($tmps[$key] as $keyd => $valued) {
                                $tmph[$key][$last] = $valued;
                                $last ++;
                            }
                        }
                        $hotels_array[$row->hid]['details'] = $tmph;
                    } else {
                        $hotels_array[$row->hid] = $tmp[$row->sid];
                    }
                }
            }
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if (is_array($sidfilter)) {
            $sidfilter = implode(',', $sidfilter);
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 16;
            // Store Session
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_mikitravel');
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
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_mikitravel');
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
        }
    }
}
?>