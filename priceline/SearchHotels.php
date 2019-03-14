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
$pricelinenet = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml20, latitude, longitude from cities where id=" . $destination;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml20 = $row_settings["city_xml20"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml20 = 0;
}
$sql = "select value from settings where name='enablepriceline' and affiliate_id=$affiliate_id";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_priceline = $affiliate_id;
} else {
    $affiliate_id_priceline = 0;
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
    $sql = "select value from settings where name='PricelineNetDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_priceline";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='PricelineNetID' and affiliate_id=$affiliate_id_priceline";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelineNetID = $row_settings['value'];
}
$sql = "select value from settings where name='PricelineNetAPIKey' and affiliate_id=$affiliate_id_priceline";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelineNetAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='PricelineNetMarkup' and affiliate_id=$affiliate_id_priceline";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelineNetMarkup = (double) $row_settings['value'];
} else {
    $PricelineNetMarkup = 0;
}
$sql = "select value from settings where name='PricelineNetServiceURL' and affiliate_id=$affiliate_id_priceline";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelineNetServiceURL = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%d-%m-%Y", $from));
$dateEnd = new DateTime(strftime("%d-%m-%Y", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$raw = 'hotel/getResults.Net?refid=' . $PricelineNetID . '&api_key=' . $PricelineNetAPIKey . '&format=json&city_id=800019762&check_in=' . strftime("%Y-%m-%d", $from) . '&check_out=' . strftime("%Y-%m-%d", $to) . '';
if ($PricelineNetServiceURL != "" and $PricelineNetAPIKey != "" and $PricelineNetAPIKey != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $PricelineNetServiceURL . $raw);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_USERPWD, $PricelineNetAPIKey . ":" . $PricelineNetAPIKey);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // Descomentar para ver o resultado do provider
    // Nao esquecer de alterar o session id para testar por causa de cache
    // echo $response;
    // die();
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_priceline');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
            'errormessage' => $PricelineNetServiceURL . $raw,
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
    $array = json_decode($response, true);
    // Descomentar para ver o vector
    // Nao esquecer de alterar o session id para testar por causa de cache
    // Echo para ver o array completro
    // echo "<xmp>";
    // var_dump($array);
    // echo "</xmp>";
    // die();
    $getHotelResults = $array['getHotelResults.Net'];
    $result = $getHotelResults['results'];
    // error_log("\r\nRESULT:" . print_r($result, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    // Results
    $status = $result['status'];
    $status_code = $result['status_code'];
    $results_data = $result['results_data'];
    $hotel_count = $results_data['hotel_count'];
    $hotel_count_total_filtered = $results_data['hotel_count_total_filtered'];
    $hotel_count_filtered = $results_data['hotel_count_filtered'];
    $availability = $results_data['availability'];
    
    $currency_data = $result['currency_data'];
    $currency = $currency_data['currency'];
    $symbol = $currency_data['symbol'];
    $name_en = $currency_data['name_en'];
    
    $sort_data = $result['sort_data'];
    $sort_by = $sort_data['sort_by'];
    $order = $sort_data['order'];
    
    $city_cluster_data = $result['city_cluster_data'];
    foreach ($city_cluster_data as $key => $valueCityCluster) {
        $city_id = $valueCityCluster;
        // error_log("\r\n city_id $i $city_id \r\n", 3, "/srv/www/htdocs/error_log");
    }
    
    $hotel_data = $result['hotel_data'];
    foreach ($hotel_data as $key => $valueHotelData) {
        $id = $valueHotelData['id'];
        $shid = $id;
        $sfilter[] = " sid='$shid' ";
        $id_a = $valueHotelData['id_a'];
        $id_b = $valueHotelData['id_b'];
        $id_t = $valueHotelData['id_t'];
        $inventory = $valueHotelData['inventory'];
        $name = $valueHotelData['name'];
        $star_rating = $valueHotelData['star_rating'];
        $review_rating = $valueHotelData['review_rating'];
        $review_rating_desc = $valueHotelData['review_rating_desc'];
        $review_count = $valueHotelData['review_count'];
        $review_source = $valueHotelData['review_source'];
        $booking_review_url = $valueHotelData['booking_review_url'];
        $agoda_review_url = $valueHotelData['agoda_review_url'];
        $thumbnail = $valueHotelData['thumbnail'];
        
        $thumbnail_hq = $valueHotelData['thumbnail_hq'];
        $hundred_fifty_square = $thumbnail_hq['hundred_fifty_square'];
        $three_hundred_square = $thumbnail_hq['three_hundred_square'];
        
        $city = $valueHotelData['city'];
        $idC = $city['id'];
        $name = $city['name'];
        
        $address = $valueHotelData['address'];
        $city_name = $address['city_name'];
        $address_line_one = $address['address_line_one'];
        $state_code = $address['state_code'];
        $state_name = $address['state_name'];
        $country_code = $address['country_code'];
        $country_name = $address['country_name'];
        $zip = $address['zip'];
        
        $geo = $valueHotelData['geo'];
        $latitude = $geo['latitude'];
        $longitude = $geo['longitude'];
        
        $neighborhood = $valueHotelData['neighborhood'];
        
        $hotel_chain = $valueHotelData['hotel_chain'];
        $idHC = $hotel_chain['id'];
        $name = $hotel_chain['name'];
        $chain_codes_b = $hotel_chain['chain_codes_b'];
        $chain_codes_t = $hotel_chain['chain_codes_t'];
        
        $room_count = $valueHotelData['room_count'];
        $hotel_rank = $valueHotelData['hotel_rank'];
        $rank = $hotel_rank['rank'];
        $reviewed_hotel = $hotel_rank['reviewed_hotel'];
        $hmi_score = $hotel_rank['hmi_score'];
        $rank_score = $hotel_rank['rank_score'];
        
        $amenity_data = $valueHotelData['amenity_data'];
        foreach ($amenity_data as $key => $valueAMData) {
            $idAmenity = $valueAMData['id'];
            $nameAmenity = $valueAMData['name'];
        }
        
        $static_low_rate = $valueHotelData['static_low_rate'];
        $source_currency = $static_low_rate['source_currency'];
        $source_symbol = $static_low_rate['source_symbol'];
        $source_price = $static_low_rate['source_price'];
        $display_currency = $static_low_rate['display_currency'];
        $display_symbol = $static_low_rate['display_symbol'];
        $display_price = $static_low_rate['display_price'];
        // Rates
        $rate_data = $valueHotelData['rate_data'];
        foreach ($rate_data as $key => $valueRate) {
            // error_log("\r\n Rate Key = $key \r\n", 3, "/srv/www/htdocs/error_log");
            
            // error_log("\r\n " . print_r($rate_data[$key], true) . " \r\n", 3, "/srv/www/htdocs/error_log");
            
            $rate_type = $rate_data[$key]['rate_type'];
            $rate_plan_code = $rate_data[$key]['rate_plan_code'];
            $rate_tracking_id = $rate_data[$key]['rate_tracking_id'];
            $rooms_available = $rate_data[$key]['rooms_available'];
            $mobile_exclusive = $rate_data[$key]['mobile_exclusive'];
            $program_names = $rate_data[$key]['program_names'];
            $occupancy_limit = $rate_data[$key]['occupancy_limit'];
            $titleR = $rate_data[$key]['title'];
            $descriptionR = $rate_data[$key]['description'];
            $bedding_data = $rate_data[$key]['bedding_data'];
            
            $details_summary = $rate_data[$key]['details_summary'];
            $book_policy = $details_summary['book_policy'];
            $promo = $details_summary['promo'];
            $refund_policy = $details_summary['refund_policy'];
            $is_cancellable = $details_summary['is_cancellable'];
            $free_cancellation = $details_summary['free_cancellation'];
            
            $pre_paid = $rate_data[$key]['pre_paid'];
            $prepaid_property_fees = $rate_data[$key]['prepaid_property_fees'];
            $all_inclusive = $rate_data[$key]['all_inclusive'];
            $deposit_required = $rate_data[$key]['deposit_required'];
            
            $rate_amenities = $rate_data[$key]['rate_amenities'];
            $free_wifi = $rate_amenities['free_wifi'];
            $free_breakfast = $rate_amenities['free_breakfast'];
            
            $guarantee_required = $rate_data[$key]['guarantee_required'];
            $ppn_bundle = $rate_data[$key]['ppn_bundle'];
            
            $price_details = $rate_data[$key]['price_details'];
            $source_currencyP = $price_details['source_currency'];
            $source_symbolB = $price_details['source_symbol'];
            $source_priceP = $price_details['source_price'];
            $source_processing_fee = $price_details['source_processing_fee'];
            $source_insurance_fee = $price_details['source_insurance_fee'];
            $source_property_fee = $price_details['source_property_fee'];
            $source_taxes = $price_details['source_taxes'];
            $source_sub_totalP = $price_details['source_sub_total'];
            $source_totalP = $price_details['source_currency'];
            $display_currencyP = $price_details['display_currency'];
            $display_symbolP = $price_details['display_symbol'];
            $display_priceP = $price_details['display_price'];
            $display_processing_fee = $price_details['display_processing_fee'];
            $display_insurance_fee = $price_details['display_insurance_fee'];
            $display_property_fee = $price_details['display_property_fee'];
            $display_taxes = $price_details['display_taxes'];
            $display_sub_totalP = $price_details['display_sub_total'];
            $display_totalP = $price_details['display_total'];
            $night_price_data = $price_details['night_price_data'];
            foreach ($night_price_data as $key => $valueNightpricedata) {
                $source_night_price = $valueNightpricedata['source_night_price'];
                $display_night_price = $valueNightpricedata['display_night_price'];
            }
            
            $benchmark_price_details = $rate_data[$key]['benchmark_price_details'];
            $source_currency = $benchmark_price_details['source_currency'];
            $source_priceB = $benchmark_price_details['source_price'];
            $display_currencyB = $benchmark_price_details['display_currency'];
            $display_priceB = $benchmark_price_details['display_price'];
            $saving_percentage = $benchmark_price_details['saving_percentage'];
            
            $mandatory_fee = $rate_data[$key]['mandatory_fee'];
            
            $important_information = $rate_data[$key]['important_information'];
            foreach ($important_information as $key => $valueImportantinformation) {
                $title = $valueImportantinformation['title'];
                $paragraph_data = $valueImportantinformation['paragraph_data'];
                foreach ($paragraph_data as $key => $valueParagraphdata) {
                    $paragraph = $valueParagraphdata;
                }
            }
            $rate_inventory = $rate_data[$key]['rate_inventory'];
            
            for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                if (is_array($tmp[$shid]['details'][$zRooms])) {
                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                } else {
                    $baseCounterDetails = 0;
                }
                if ($titleR == "") {
                    $titleR = $descriptionR;
                }
                if ($titleR == "") {
                    $titleR = $translator->translate("Room Assigned at Check-in - Sleeps") . " " . $occupancy_limit . " " . $translator->translate("Adult(s)");
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $name;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $id;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $titleR;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ppn_bundle'] = $ppn_bundle;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-20";
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $display_totalP;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $id;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['price'] = $display_priceP;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['currency'] = $currency;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_category'] = $star_rating;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $descriptionR;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $rate_type;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['recommended'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = $display_totalP;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($review_source);
                // error_log("\r\nTitle: $titleR \r\n", 3, "/srv/www/htdocs/error_log");
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    // $amount = $display_night_price * $noOfNights;
                    $amount = $display_totalP * $noOfNights;
                    if ($PricelineNetMarkup != 0) {
                        $amount = $amount + (($amount * $PricelineNetMarkup) / 100);
                    }
                    
                    if ($internalmarkup != 0) {
                        $amount = $amount + (($amount * $internalmarkup) / 100);
                    }
                    
                    if ($agent_markup != 0) {
                        $amount = $amount + (($amount * $agent_markup) / 100);
                    }
                    
                    if ($PricelineNetMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                    }
                    
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
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $paragraph;
            }
        }
        $pricelinenet = true;
    }
    // error_log("\r\n Pricelinenet = $pricelinenet \r\n", 3, "/srv/www/htdocs/error_log");
    
    if ($pricelinenet == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mpriceline where " . $sfilter;
            // error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $result = $statement->execute();
            $result->buffer();
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet2 = new ResultSet();
                $resultSet2->initialize($result);
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
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if (is_array($sidfilter)) {
            $sidfilter = implode(',', $sidfilter);
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 20;
            // error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
            // Store Session
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_priceline');
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
            $insert->into('quote_session_priceline');
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>