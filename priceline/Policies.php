<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$total = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_priceline where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
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
    $rooms = $searchsettings['rooms'];
    $adt = $searchsettings['adt'];
    $chd = $searchsettings['chd'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
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
    $sql = "select value from settings where name='PricelineNetDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='PricelineNetID' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelineNetID = $row_settings['value'];
}
$sql = "select value from settings where name='PricelineNetAPIKey' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelineNetAPIKey = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='PricelineNetMarkup' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
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
$sql = "select value from settings where name='PricelineNetServiceURL' and affiliate_id=$affiliate_id_priceline" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $PricelineNetServiceURL = $row_settings['value'];
}
// error_log("\r\n PricelineNetServiceURL2 = $PricelineNetServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
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
$fromHotelsPRO = $fromHotelsPRO->getTimestamp();
$toHotelsPro = $toHotelsPro->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['code'];
            $ppn_bundle = $value['ppn_bundle'];
            $scode = $value['scode'];
            $hotel_code = $value['shid'];
            // error_log("\r\n" . print_r($value, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";
        //
        // Policies
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $PricelineNetServiceURL . "hotel/getContractRequest?format=json&refid=8303&api_key=aca5194eacf4390a5b000bcf63a27c0d&test_mode=1&ppn_bundle=" . $ppn_bundle);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $ppn_bundle);
        curl_setopt($ch, CURLOPT_USERPWD, $PricelineNetAPIKey . ":" . $PricelineNetAPIKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        // error_log("\r\nResponse Policies: $result . \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\nCode: $code \r\n", 3, "/srv/www/htdocs/error_log");
        $result1 = json_decode($result, true);
        // error_log("\r\nVECTOR RESULT1 " . print_r($result1, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        // $result1 = $arrayResponse['results'];
        $vector = array();
        $getHotelContractRequest = $result1['getHotelContractRequest'];
        $result2 = $getHotelContractRequest['results'];
        $vector['status'] = $result2['status'];
        $vector['status_code'] = $result2['status_code'];
        $status_code = $result2['status_code'];
        $vector['result'] = $result2['result'];
        $resultado = $vector['result'];
        $vector['rate_source'] = $resultado['rate_source'];
        $rate = $vector['rate_source'];
        $vector['rate_type'] = $resultado['rate_type'];
        $vector['ppn_bundle'] = $resultado['ppn_bundle'];
        $vector['check_in'] = $resultado['check_in'];
        $vector['check_out'] = $resultado['check_out'];
        $vector['num_rooms'] = $resultado['num_rooms'];
        $vector['num_adults'] = $resultado['num_adults'];
        $vector['num_children'] = $resultado['num_children'];
        $vector['num_nights'] = $resultado['num_nights'];
        $num_nigths = $vector['num_nights'];
        $vector['allowed_cards_data'] = $resultado['allowed_cards_data'];
        $allowed_cards_data = $vector['allowed_cards_data'];
        $novo_item = array();
        foreach ($allowed_cards_data as $key => $valueAllowedcards) {
            $card_type = $valueAllowedcards['card_type'];
            $name = $valueAllowedcards['name'];
            $novo_item['card_type'] = $card_type;
            $novo_item['name'] = $name;
        }
        $vector['allowed_cards_data'] = $novo_item;
        
        // HOTEL
        $vector_hotel = array();
        $vector['hotel'] = $resultado['hotel'];
        $hotel = $vector['hotel'];
        $hotel_id = $hotel['hotel_id'];
        $hotel_name = $hotel['hotel_name'];
        $property_type = $hotel['property_type'];
        $phone_number = $hotel['phone_number'];
        $star_rating = $hotel['star_rating'];
        $thumbnail = $hotel['thumbnail'];
        $vector_hotel['hotel_id'] = $hotel_id;
        $vector_hotel['hotel_name'] = $hotel_name;
        $vector_hotel['property_type'] = $property_type;
        $vector_hotel['phone_number'] = $phone_number;
        $vector_hotel['star_rating'] = $star_rating;
        $vector_hotel['thumbnail'] = $thumbnail;
        
        $vectorCity = array();
        $city = $hotel['city'];
        $idCity = $city['id'];
        $nameCity = $city['name'];
        $vectorCity['id'] = $idCity;
        $vectorCity['name'] = $nameCity;
        $vector_hotel['city'] = $vectorCity;
        $vector_address = array();
        $address = $hotel['address'];
        $address_line_one = $address['address_line_one'];
        $city_name = $address['city_name'];
        $state_code = $address['state_code'];
        $zip = $address['zip'];
        $country_code = $address['country_code'];
        $vector_address['address'] = $address_line_one;
        $vector_address['address_line_one'] = $address;
        $vector_address['city_name'] = $city_name;
        $vector_address['state_code'] = $state_code;
        $vector_address['zip'] = $zip;
        $vector_address['country_code'] = $country_code;
        $vector_hotel['address'] = $vector_address;
        
        $vector_geo = array();
        $geo = $hotel['geo'];
        $lat = $geo['lat'];
        $lng = $geo['lng'];
        $vector_geo['lat'] = $lat;
        $vector_geo['lng'] = $lng;
        $vector_hotel['geo'] = $vector_geo;
        $vector_hc = array();
        $hotel_chain = $hotel['hotel_chain'];
        $namehc = $hotel_chain['name'];
        $codehc = $hotel_chain['code'];
        $vector_hc['name'] = $namehc;
        $vector_hc['code'] = $codehc;
        $vector_hotel['hotel_chain'] = $vector_hc;
        
        $check_in_time = $hotel['check_in_time'];
        $vector_hotel['check_in_time'] = $check_in_time;
        $check_out_time = $hotel['check_out_time'];
        $vector_hotel['check_out_time'] = $check_out_time;
        
        $vector_hs = array();
        $hotel_score = $hotel['hotel_score'];
        $guest_score = $hotel_score['guest_score'];
        $guest_score_count = $hotel_score['guest_score_count'];
        $hmi_score = $hotel_score['hmi_score'];
        $city_rank = $hotel_score['city_rank'];
        $review_rating_desc = $hotel_score['review_rating_desc'];
        $vector_hs['guest_score'] = $guest_score;
        $vector_hs['guest_score_count'] = $guest_score_count;
        $vector_hs['hmi_score'] = $hmi_score;
        $vector_hs['city_rank'] = $city_rank;
        $vector_hs['review_rating_desc'] = $review_rating_desc;
        $vector_hotel['hotel_score'] = $vector_hs;
        $vector['hotel'] = $vector_hotel;
        $vector_acd = array();
        $vector['allowed_countries_data'] = $resultado['allowed_countries_data'];
        $allowed_countries_data = $vector['allowed_countries_data'];
        foreach ($allowed_countries_data as $key => $valueAllowedcountries) {
            $codeac = $valueAllowedcountries['code'];
            $nameac = $valueAllowedcountries['name'];
            $state_dataac = $valueAllowedcountries['state_data'];
            $vector_acd['code'] = $codeac;
            $vector_acd['name'] = $nameac;
            $vector_acd['state_data'] = $state_dataac;
        }
        $vector['allowed_countries_data'] = $vector_acd;
        $vector['room_info'] = $resultado['room_info'];
        $room_info = $vector['room_info'];
        foreach ($room_info as $key => $valueRoominfo) {
            $rate_plan_code = $valueRoominfo['rate_plan_code'];
            $title = $valueRoominfo['title'];
            $description = $valueRoominfo['description'];
            $bed_type = $valueRoominfo['bed_type'];
            $occupancy_limit = $valueRoominfo['occupancy_limit'];
            $pre_paid = $valueRoominfo['pre_paid'];
            
            $price_details = $valueRoominfo['price_details'];
            $booked_currency = $price_details['booked_currency'];
            $baseline_currency = $price_details['baseline_currency'];
            $baseline_symbol = $price_details['baseline_symbol'];
            $baseline_price = $price_details['baseline_price'];
            $baseline_processing_fee = $price_details['baseline_processing_fee'];
            $baseline_property_fee = $price_details['baseline_property_fee'];
            $baseline_taxes = $price_details['baseline_taxes'];
            $baseline_sub_total = $price_details['baseline_sub_total'];
            $baseline_total = $price_details['baseline_total'];
            $source_symbol = $price_details['source_symbol'];
            $source_price = $price_details['source_price'];
            $source_processing_fee = $price_details['source_processing_fee'];
            $source_property_fee = $price_details['source_property_fee'];
            $source_taxes = $price_details['source_taxes'];
            $source_sub_total = $price_details['source_sub_total'];
            $source_total = $price_details['source_total'];
            $display_currency = $price_details['display_currency'];
            $display_symbol = $price_details['display_symbol'];
            $display_price = $price_details['display_price'];
            $display_processing_fee = $price_details['display_processing_fee'];
            $display_property_fee = $price_details['display_property_fee'];
            $display_taxes = $price_details['display_taxes'];
            $display_sub_total = $price_details['display_sub_total'];
            $display_total = $price_details['display_total'];
            
            $attribute_data = $price_details['attribute_data'];
            $tax_data = $attribute_data['tax_data'];
            foreach ($tax_data as $key => $valueTaxdata) {
                $typetax = $tax_data[$key]['type'];
                $source_currencytax = $tax_data[$key]['source_currency'];
                $source_amount = $tax_data[$key]['source_amount'];
                $display_currencytax = $tax_data[$key]['display_currency'];
                $display_amounttax = $tax_data[$key]['display_amount'];
                $attributetax = $tax_data[$key]['attribute'];
            }
            
            $night_price_data = $price_details['night_price_data'];
            foreach ($night_price_data as $key => $valueNightpricedata) {
                $baseline_night_price = $valueNightpricedata['baseline_night_price'];
                $source_night_price = $valueNightpricedata['source_night_price'];
                $display_night_price = $valueNightpricedata['display_night_price'];
            }
            
            $nightly_rate_changes = $price_details['nightly_rate_changes'];
            $promo = $price_details['promo'];
            $titlepromo = $promo['title'];
            $description = $promo['description'];
            $type = $promo['type'];
            $terms = $promo['terms'];
            $strikeout_price = $promo['strikeout_price'];
            $source_strikeout_price = $promo['source_strikeout_price'];
            $display_strikeout_price = $promo['display_strikeout_price'];
            $required = $promo['required'];
            
            $photos = $valueRoominfo['photos'];
            $hotel_id = $photos['hotel_id'];
            $photo_data = $photos['photo_data'];
            foreach ($photo_data as $key => $valuePhotodata) {
                $photo = $valuePhotodata;
            }
            
            $amenity_data = $valueRoominfo['amenity_data'];
            foreach ($amenity_data as $key => $valueAmenitydata) {
                $idamenity = $valueAmenitydata['id'];
                $nameamenity = $valueAmenitydata['name'];
            }
            
            $marketing = $valueRoominfo['marketing'];
            $rooms_remaining = $marketing['rooms_remaining'];
            $selling_out = $marketing['selling_out'];
            $lowest_rate = $marketing['lowest_rate'];
            
            $plugin_data = $valueRoominfo['plugin_data'];
            $aaa = $plugin_data['aaa'];
            $hot_rate = $aaa['hot_rate'];
            
            $is_cancellable = $valueRoominfo['is_cancellable'];
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $num_nigths; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) + $rZZ, date("y", $fromHotelsPRO)));
                $amount = $num_nigths * $source_price;
                if ($PricelineNetMarkup != 0) {
                    $amount = $amount + (($amount * $PricelineNetMarkup) / 100);
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
                if ($PricelineNetMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
                $pricebreakdownCount ++;
            }
            $novotmp['room'] = $room_category;
            $novotmp['shid'] = $shid;
            $novotmp['code'] = $shid;
            $novotmp['room_category'] = $room_category;
            $novotmp['room_description'] = $room_description;
            $novotmp['room_type'] = $room_type;
            $novotmp['recommended'] = false;
            $novotmp['scurrency'] = $currency;
            $novotmp['currency'] = $currency;
            $novotmp['pricebreakdown'] = $pricebreakdown;
        }
        $vector['important_information'] = $resultado['important_information'];
        $important_information = $vector['important_information'];
        $policy_data = $important_information['policy_data'];
        foreach ($policy_data as $key => $valuePolicydata) {
            $titlepolicy = $valuePolicydata['title'];
            $paragraph_datap = $valuePolicydata['paragraph_data'];
            foreach ($paragraph_datap as $key => $valueParagraphdata) {
                $paragr .= $valueParagraphdata;
                $paragr .= "<br/>";
            }
        }
        $vector['taxes_and_fees_policy'] = $resultado['taxes_and_fees_policy'];
        $taxes_and_fees_policy = $vector['taxes_and_fees_policy'];
        $policy_datatfp = $taxes_and_fees_policy['policy_data'];
        foreach ($policy_datatfp as $key => $valuePolicydatatfp) {
            $titletfp = $valuePolicydata['title'];
            $paragraph_datatfp = $valuePolicydata['paragraph_data'];
            foreach ($paragraph_datatfp as $key => $valueParagraphdatat) {
                $paragr2 .= $valueParagraphdatat;
                $paragr2 .= "<br/>";
            }
        }
        
        $vector['tracking_id'] = $resultado['tracking_id'];
        $vector['plugin_information'] = $resultado['plugin_information'];
        $vector['book_form_details'] = $resultado['book_form_details'];
        $book_form_details = $vector['book_form_details'];
        $name_first = $book_form_details['name_first'];
        $name_last = $book_form_details['name_last'];
        $email = $book_form_details['email'];
        $phone_number = $book_form_details['phone_number'];
        $card_type = $book_form_details['card_type'];
        $card_number = $book_form_details['card_number'];
        $expires = $book_form_details['expires'];
        $cvc_code = $book_form_details['cvc_code'];
        $card_holder = $book_form_details['card_holder'];
        $add_insurance = $book_form_details['add_insurance'];
        $address_line_one = $book_form_details['address_line_one'];
        $address_city = $book_form_details['address_city'];
        $address_postal_code = $book_form_details['address_postal_code'];
        $address_state_code = $book_form_details['address_state_code'];
        $country_code = $book_form_details['country_code'];
        $smoking = $book_form_details['smoking'];
        $comments = $book_form_details['comments'];
        $initials = $book_form_details['initials'];
        $contract_response = $book_form_details['contract_response'];
        $newsletter = $book_form_details['newsletter'];
        $aaa_member_id = $book_form_details['aaa_member_id'];
        $mq_purpose = $book_form_details['mq_purpose'];
        $onstar_advisor_id = $book_form_details['onstar_advisor_id'];
        $pnr = $book_form_details['pnr'];
        $inventory = $book_form_details['inventory'];
        
        $vector['upsell_data'] = $resultado['upsell_data'];
        $upsell_data = $vector['upsell_data'];
        $travel_insurance = $upsell_data['travel_insurance'];
        $default = $travel_insurance['default'];
        $important_information = $travel_insurance['important_information'];
        $ti_html = $travel_insurance['ti_html'];
        $insurance_type = $travel_insurance['insurance_type'];
        $insurance_available = $travel_insurance['insurance_available'];
        $extra_information = $travel_insurance['extra_information'];
        // error_log("\r\nNOVO VECTOR: " . print_r($vector, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_priceline');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $PricelineNetServiceURL . "hotel/getContractRequest?format=json&refid=8303&api_key=aca5194eacf4390a5b000bcf63a27c0d&test_mode=1&ppn_bundle=" . $ppn_bundle,
                'sqlcontext' => $result,
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
        //
        // EOF Policies
        // EOF Check prices & availability
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['adults'] = $value['adults'];
        $item['children'] = $value['children'];
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        // $item['adults'] = $selectedAdults[$c];
        // $item['children'] = $selectedChildren[$c];
        // $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        if ($vector['nonrefundable'] == true) {
            $cancelation_string = $translator->translate("This is a non refundable offer. You will be charged full amount of the booking.");
            $cancelation_deadline = time();
        } else {
            foreach ($vector['policies'] as $key => $val) {
                $days_remaining = $val['days_remaining'];
                $ratio = $val['ratio'];
                if ($cancelation_string != "") {
                    $cancelation_string .= "<br/>";
                }
                $cancelation_string .= $translator->translate("Charge") . " " . number_format($ratio * $value['total'], 2, '.', '') . " " . $translator->translate("if cancelled on and after") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO)));
                if ($cancelation_deadline == 0) {
                    $cancelation_deadline = mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO));
                } else {
                    if (mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO)) < $cancelation_deadline) {
                        $cancelation_deadline = mktime(0, 0, 0, date("m", $fromHotelsPRO), date("d", $fromHotelsPRO) - $days_remaining, date("y", $fromHotelsPRO));
                    }
                }
            }
        }
        $vector['extra_information'] = $extra_information;
        if ($vector['extra_information'] != "") {
            if ($cancelation_string == "") {
                $cancelation_string = $vector['extra_information'];
            } else {
                $cancelation_string .= "<br/><br/>" . $vector['extra_information'];
            }
        }
        $cancelpolicy = "";
        $cancelpolicy .= $paragr;
        $cancelpolicy .= "<br/><br/>";
        $cancelpolicy .= "<b>Mandatories Fees and Taxes</b>";
        $cancelpolicy .= "<br/><br/>" . $paragr2;
        $item['cancelpolicy'] = $cancelpolicy;
        $item['cancelpolicy_deadline'] = strftime("%d-%m-%Y", $cancelation_deadline);
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelation_details;
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mpriceline where sid='" . $shid . "' and hid=" . $hid;
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
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
    $row_hotel->buffer();
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
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
        $row_country->buffer();
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
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>