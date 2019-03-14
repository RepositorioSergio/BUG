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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_getaroom where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (\Exception $e) {
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
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enableGetaroom' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_getaroom = $affiliate_id;
} else {
    $affiliate_id_getaroom = 0;
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
    $sql = "select value from settings where name='GetaroomDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_getaroom";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='GetaroomAuth' and affiliate_id=$affiliate_id_getaroom" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuth = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAPIKey' and affiliate_id=$affiliate_id_getaroom" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomMarkup' and affiliate_id=$affiliate_id_getaroom" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomMarkup = (double) $row_settings['value'];
} else {
    $GetaroomMarkup = 0;
}
$sql = "select value from settings where name='GetaroomServiceURL' and affiliate_id=$affiliate_id_getaroom" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomServiceURL = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAuthorizationToken' and affiliate_id=$affiliate_id_getaroom" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuthorizationToken = $row_settings['value'];
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
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['code'];
            $scode = $value['scode'];
            $hotel_code = $value['shid'];
            $room_code = $value['roomid'];
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
        $checkin = date('m/d/Y', strtotime($from));
        $checkout = date('m/d/Y', strtotime($to));
        
        $raw = 'api/1.1/properties/b41ce02e-69a3-4a28-8bf8-2ab48d0d4135/room_availability?rinfo=%5B%5B18,18%5D%5D&check_in=' . $checkin . '&check_out=' . $checkout . '&cancellation_rules=1&room_id=' . $room_code . '&rate_plan_code=' . $code . '&api_key=' . $GetaroomAPIKey . '&auth_token=' . $GetaroomAuth;
        
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $GetaroomServiceURL . $raw);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseResult = curl_exec($ch);
        curl_close($ch);
        // error_log("\r\n response: $response \r\n", 3, "/srv/www/htdocs/error_log");
        $endTime = microtime();
        
        $vector = array();
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($responseResult);
        $room_stays = $inputDoc->getElementsByTagName("room-stays");
        $requestTmp = $room_stays->item(0)->getElementsByTagName("request");
        $check_in = $requestTmp->item(0)->getElementsByTagName("check-in");
        if ($check_in->length > 0) {
            $check_in = $check_in->item(0)->nodeValue;
        } else {
            $check_in = "";
        }
        $check_out = $requestTmp->item(0)->getElementsByTagName("check-out");
        if ($check_out->length > 0) {
            $check_out = $check_out->item(0)->nodeValue;
        } else {
            $check_out = "";
        }
        $rooms = $requestTmp->item(0)->getElementsByTagName("rooms");
        if ($rooms->length > 0) {
            $rooms = $rooms->item(0)->nodeValue;
        } else {
            $rooms = "";
        }
        $adults = $requestTmp->item(0)->getElementsByTagName("adults");
        if ($adults->length > 0) {
            $adults = $adults->item(0)->nodeValue;
        } else {
            $adults = "";
        }
        $children = $requestTmp->item(0)->getElementsByTagName("children");
        if ($children->length > 0) {
            $children = $children->item(0)->nodeValue;
        } else {
            $children = "";
        }
        $node = $room_stays->item(0)->getElementsByTagName("room-stay");
        $room = $node->item(0)->getElementsByTagName("room");
        if ($room->length > 0) {
            $hotel_id = $room->item(0)->getElementsByTagName('hotel-id');
            if ($hotel_id->length > 0) {
                $hotel_id = $hotel_id->item(0)->nodeValue;
            } else {
                $hotel_id = "";
            }
            $shid = $hotel_id;
            $sfilter[] = " sid='$hotel_id' ";
            error_log("\r\n hotel_id: $hotel_id \r\n", 3, "/srv/www/htdocs/error_log");
            $room_id = $room->item(0)->getElementsByTagName('room-id');
            if ($room_id->length > 0) {
                $room_id = $room_id->item(0)->nodeValue;
            } else {
                $room_id = "";
            }
            $title = $room->item(0)->getElementsByTagName('title');
            if ($title->length > 0) {
                $title = $title->item(0)->nodeValue;
            } else {
                $title = "";
            }
            $description = $room->item(0)->getElementsByTagName('description');
            if ($description->length > 0) {
                $description = $description->item(0)->nodeValue;
            } else {
                $description = "";
            }
        } else {
            $description = "";
        }
        $commission_tier = $node->item(0)->getElementsByTagName('commission-tier');
        if ($commission_tier->length > 0) {
            $commission_tier = $commission_tier->item(0)->nodeValue;
        } else {
            $commission_tier = "";
        }
        $refundable = $node->item(0)->getElementsByTagName('refundable');
        if ($refundable->length > 0) {
            $refundable = $refundable->item(0)->nodeValue;
        } else {
            $refundable = "";
        }
        $sale = $node->item(0)->getElementsByTagName('sale');
        if ($sale->length > 0) {
            $sale = $sale->item(0)->nodeValue;
        } else {
            $sale = "";
        }
        $promotional_text = $node->item(0)->getElementsByTagName('promotional-text');
        if ($promotional_text->length > 0) {
            $promotional_text = $promotional_text->item(0)->nodeValue;
        } else {
            $promotional_text = "";
        }
        $promotion_details = $node->item(0)->getElementsByTagName('promotion-details');
        if ($promotion_details->length > 0) {
            $promotion_details = $promotion_details->item(0)->nodeValue;
        } else {
            $promotion_details = "";
        }
        
        $vaArray = array();
        $vaCount = 0;
        $value_adds = $node->item(0)->getElementsByTagName('value-adds');
        $value_add = $value_adds->item(0)->getElementsByTagName('value-add');
        if ($value_add->length > 0) {
            for ($i = 0; $i < $value_add->length; $i ++) {
                $codeVA = $value_add->item($i)->getElementsByTagName('code');
                if ($codeVA->length > 0) {
                    $codeVA = $codeVA->item(0)->nodeValue;
                } else {
                    $codeVA = "";
                }
                $nameVA = $value_add->item($i)->getElementsByTagName('name');
                if ($nameVA->length > 0) {
                    $nameVA = $nameVA->item(0)->nodeValue;
                } else {
                    $nameVA = "";
                }
                $vaArray[$vaCount]['code'] = $codeVA;
                $vaArray[$vaCount]['name'] = $nameVA;
                $vaCount = $vaCount + 1;
            }
        }
        $rooms_left = $node->item(0)->getElementsByTagName('rooms-left');
        if ($rooms_left->length > 0) {
            $rooms_left = $rooms_left->item(0)->nodeValue;
        } else {
            $rooms_left = "";
        }
        $timed_pressure_sell = $node->item(0)->getElementsByTagName('timed-pressure-sell');
        if ($timed_pressure_sell->length > 0) {
            $timed_pressure_sell = $timed_pressure_sell->item(0)->nodeValue;
        } else {
            $timed_pressure_sell = "";
        }
        $expires_at = $node->item(0)->getElementsByTagName('expires-at');
        if ($expires_at->length > 0) {
            $expires_at = $expires_at->item(0)->nodeValue;
        } else {
            $expires_at = "";
        }
        $restrictions = $node->item(0)->getElementsByTagName('restrictions');
        if ($restrictions->length > 0) {
            $restrictions = $restrictions->item(0)->nodeValue;
        } else {
            $restrictions = "";
        }
        error_log("\r\n restrictions: $restrictions \r\n", 3, "/srv/www/htdocs/error_log");
        $geo_restrictions = $node->item(0)->getElementsByTagName('geo-restrictions');
        if ($geo_restrictions->length > 0) {
            $geo_restrictions = $geo_restrictions->item(0)->nodeValue;
        } else {
            $geo_restrictions = "";
        }
        $lowest_average = $node->item(0)->getElementsByTagName('lowest-average');
        if ($lowest_average->length > 0) {
            $lowest_average = $lowest_average->item(0)->nodeValue;
        } else {
            $lowest_average = "";
        }
        $fees_collected = $node->item(0)->getElementsByTagName('fees-collected-at-property');
        $fee = $fees_collected->item(0)->getElementsByTagName('fee');
        if ($fee->length > 0) {
            for ($j = 0; $j < $fee->length; $j ++) {
                $nameF = $fee->item($j)->getElementsByTagName('name');
                if ($nameF->length > 0) {
                    $nameF = $nameF->item(0)->nodeValue;
                } else {
                    $nameF = "";
                }
                $amountF = $fee->item($j)->getElementsByTagName('amount');
                if ($amountF->length > 0) {
                    $amountF = $amountF->item(0)->nodeValue;
                } else {
                    $amountF = "";
                }
                $totalF = $fee->item($j)->getElementsByTagName('total');
                if ($totalF->length > 0) {
                    $totalF = $totalF->item(0)->nodeValue;
                } else {
                    $totalF = "";
                }
                $currencyF = $fee->item($j)->getElementsByTagName('currency');
                if ($currencyF->length > 0) {
                    $currencyF = $currencyF->item(0)->nodeValue;
                } else {
                    $currencyF = "";
                }
                $descriptionF = $fee->item($j)->getElementsByTagName('description');
                if ($descriptionF->length > 0) {
                    $descriptionF = $descriptionF->item(0)->nodeValue;
                } else {
                    $descriptionF = "";
                }
            }
        }
        $display_pricing = $node->item(0)->getElementsByTagName('display-pricing');
        $original_average = $display_pricing->item(0)->getElementsByTagName('original-average');
        if ($original_average->length > 0) {
            $original_average = $original_average->item(0)->nodeValue;
        } else {
            $original_average = "";
        }
        $subtotalDP = $display_pricing->item(0)->getElementsByTagName('subtotal');
        if ($subtotalDP->length > 0) {
            $subtotalDP = $subtotalDP->item(0)->nodeValue;
        } else {
            $subtotalDP = "";
        }
        $taxesDP = $display_pricing->item(0)->getElementsByTagName('taxes');
        if ($taxesDP->length > 0) {
            $taxesDP = $taxesDP->item(0)->nodeValue;
        } else {
            $taxesDP = "";
        }
        $feesDP = $display_pricing->item(0)->getElementsByTagName('fees');
        if ($feesDP->length > 0) {
            $feesDP = $feesDP->item(0)->nodeValue;
        } else {
            $feesDP = "";
        }
        error_log("\r\n feesDP: $feesDP \r\n", 3, "/srv/www/htdocs/error_log");
        $line_items = $display_pricing->item(0)->getElementsByTagName('line-items');
        $line_item = $line_items->item(0)->getElementsByTagName('line-item');
        if ($line_item->length > 0) {
            for ($l = 0; $l < $line_item->length; $l ++) {
                $codeLI = $line_item->item($l)->getElementsByTagName('code');
                if ($codeLI->length > 0) {
                    $codeLI = $codeLI->item(0)->nodeValue;
                } else {
                    $codeLI = "";
                }
                $nameLI = $line_item->item($l)->getElementsByTagName('name');
                if ($nameLI->length > 0) {
                    $nameLI = $nameLI->item(0)->nodeValue;
                } else {
                    $nameLI = "";
                }
                $amountLI = $line_item->item($l)->getElementsByTagName('amount');
                if ($amountLI->length > 0) {
                    $amountLI = $amountLI->item(0)->nodeValue;
                } else {
                    $amountLI = "";
                }
            }
        }
        $totalDP = $display_pricing->item(0)->getElementsByTagName('total');
        if ($totalDP->length > 0) {
            $totalDP = $totalDP->item(0)->nodeValue;
        } else {
            $totalDP = "";
        }
        error_log("\r\n totalDP: $totalDP \r\n", 3, "/srv/www/htdocs/error_log");
        $lowest_averageDP = $display_pricing->item(0)->getElementsByTagName('lowest-average');
        if ($lowest_averageDP->length > 0) {
            $lowest_averageDP = $lowest_averageDP->item(0)->nodeValue;
        } else {
            $lowest_averageDP = "";
        }
        $currencyDP = $display_pricing->item(0)->getElementsByTagName('currency');
        if ($currencyDP->length > 0) {
            $currencyDP = $currencyDP->item(0)->nodeValue;
        } else {
            $currencyDP = "";
        }
        error_log("\r\n currencyDP: $currencyDP \r\n", 3, "/srv/www/htdocs/error_log");
        $nightly_rates = $display_pricing->item(0)->getElementsByTagName('nightly-rates');
        $nightly_rate = $nightly_rates->item(0)->getElementsByTagName('nightly-rate');
        if ($nightly_rate->length > 0) {
            for ($K = 0; $K < $nightly_rate->length; $K ++) {
                $dateNR = $nightly_rate->item($K)->getElementsByTagName('date');
                if ($dateNR->length > 0) {
                    $dateNR = $dateNR->item(0)->nodeValue;
                } else {
                    $dateNR = "";
                }
                $rateNR = $nightly_rate->item($K)->getElementsByTagName('rate');
                if ($rateNR->length > 0) {
                    $rateNR = $rateNR->item(0)->nodeValue;
                } else {
                    $rateNR = "";
                }
                $original_rateNR = $nightly_rate->item($K)->getElementsByTagName('original-rate');
                if ($original_rateNR->length > 0) {
                    $original_rateNR = $original_rateNR->item(0)->nodeValue;
                } else {
                    $original_rateNR = "";
                }
            }
        }
        $booking_pricing = $node->item(0)->getElementsByTagName('booking-pricing');
        $original_averageBP = $booking_pricing->item(0)->getElementsByTagName('original-average');
        if ($original_averageBP->length > 0) {
            $original_averageBP = $original_averageBP->item(0)->nodeValue;
        } else {
            $original_averageBP = "";
        }
        $subtotalBP = $booking_pricing->item(0)->getElementsByTagName('subtotal');
        if ($subtotalBP->length > 0) {
            $subtotalBP = $subtotalBP->item(0)->nodeValue;
        } else {
            $subtotalBP = "";
        }
        $taxesBP = $booking_pricing->item(0)->getElementsByTagName('taxes');
        if ($taxesBP->length > 0) {
            $taxesBP = $taxesBP->item(0)->nodeValue;
        } else {
            $taxesBP = "";
        }
        $feesBP = $booking_pricing->item(0)->getElementsByTagName('fees');
        if ($feesBP->length > 0) {
            $feesBP = $feesBP->item(0)->nodeValue;
        } else {
            $feesBP = "";
        }
        error_log("\r\n feesBP: $feesBP \r\n", 3, "/srv/www/htdocs/error_log");
        $line_items = $booking_pricing->item(0)->getElementsByTagName('line-items');
        $line_item = $line_items->item(0)->getElementsByTagName('line-item');
        if ($line_item->length > 0) {
            for ($l = 0; $l < $line_item->length; $l ++) {
                $codeLI = $line_item->item($l)->getElementsByTagName('code');
                if ($codeLI->length > 0) {
                    $codeLI = $codeLI->item(0)->nodeValue;
                } else {
                    $codeLI = "";
                }
                $nameLI = $line_item->item($l)->getElementsByTagName('name');
                if ($nameLI->length > 0) {
                    $nameLI = $nameLI->item(0)->nodeValue;
                } else {
                    $nameLI = "";
                }
                $amountLI = $line_item->item($l)->getElementsByTagName('amount');
                if ($amountLI->length > 0) {
                    $amountLI = $amountLI->item(0)->nodeValue;
                } else {
                    $amountLI = "";
                }
            }
        }
        $totalBP = $booking_pricing->item(0)->getElementsByTagName('total');
        if ($totalBP->length > 0) {
            $totalBP = $totalBP->item(0)->nodeValue;
        } else {
            $totalBP = "";
        }
        $lowest_averageBP = $booking_pricing->item(0)->getElementsByTagName('lowest-average');
        if ($lowest_averageBP->length > 0) {
            $lowest_averageBP = $lowest_averageBP->item(0)->nodeValue;
        } else {
            $lowest_averageBP = "";
        }
        $currencyBP = $booking_pricing->item(0)->getElementsByTagName('currency');
        if ($currencyBP->length > 0) {
            $currencyBP = $currencyBP->item(0)->nodeValue;
        } else {
            $currencyBP = "";
        }
        $nightly_rates = $booking_pricing->item(0)->getElementsByTagName('nightly-rates');
        $nightly_rate = $nightly_rates->item(0)->getElementsByTagName('nightly-rate');
        if ($nightly_rate->length > 0) {
            for ($K = 0; $K < $nightly_rate->length; $K ++) {
                $dateNRB = $nightly_rate->item($K)->getElementsByTagName('date');
                if ($dateNRB->length > 0) {
                    $dateNRB = $dateNRB->item(0)->nodeValue;
                } else {
                    $dateNRB = "";
                }
                $rateNRB = $nightly_rate->item($K)->getElementsByTagName('rate');
                if ($rateNRB->length > 0) {
                    $rateNRB = $rateNRB->item(0)->nodeValue;
                } else {
                    $rateNRB = "";
                }
                $original_rateNRB = $nightly_rate->item($K)->getElementsByTagName('original-rate');
                if ($original_rateNRB->length > 0) {
                    $original_rateNRB = $original_rateNRB->item(0)->nodeValue;
                } else {
                    $original_rateNRB = "";
                }
            }
        }
        $rate_plan_type = $node->item(0)->getElementsByTagName('rate-plan-type');
        if ($rate_plan_type->length > 0) {
            $rate_plan_type = $rate_plan_type->item(0)->nodeValue;
        } else {
            $rate_plan_type = "";
        }
        $rate_plan_code = $node->item(0)->getElementsByTagName('rate-plan-code');
        if ($rate_plan_code->length > 0) {
            $rate_plan_code = $rate_plan_code->item(0)->nodeValue;
        } else {
            $rate_plan_code = "";
        }
        $policy_description = $node->item(0)->getElementsByTagName('policy-description');
        if ($policy_description->length > 0) {
            $policy_description = $policy_description->item(0)->nodeValue;
        } else {
            $policy_description = "";
        }
        $cancellation_deadline = $node->item(0)->getElementsByTagName('cancellation-deadline');
        if ($cancellation_deadline->length > 0) {
            $cancellation_deadline = $cancellation_deadline->item(0)->nodeValue;
        } else {
            $cancellation_deadline = "";
        }
        $cancellation_policy = $node->item(0)->getElementsByTagName('cancellation-policy');
        if ($cancellation_policy->length > 0) {
            $cancellation_policy = $cancellation_policy->item(0)->nodeValue;
        } else {
            $cancellation_policy = "";
        }
        $terms_acceptance_text = $node->item(0)->getElementsByTagName('terms-acceptance-text');
        if ($terms_acceptance_text->length > 0) {
            $terms_acceptance_text = $terms_acceptance_text->item(0)->nodeValue;
        } else {
            $terms_acceptance_text = "";
        }
        error_log("\r\n terms_acceptance_text: $terms_acceptance_text \r\n", 3, "/srv/www/htdocs/error_log");
        $cancellation_penalties = $node->item(0)->getElementsByTagName('cancellation-penalties');
        $cancellation_penalty = $cancellation_penalties->item(0)->getElementsByTagName('cancellation-penalty');
        if ($cancellation_penalty->length > 0) {
            $deadline = $cancellation_penalty->item(0)->getElementsByTagName('deadline');
            if ($deadline->length > 0) {
                $deadline = $deadline->item(0)->nodeValue;
            } else {
                $deadline = "";
            }
            $amountCP = $cancellation_penalty->item(0)->getElementsByTagName('amount');
            if ($amountCP->length > 0) {
                $amountCP = $amountCP->item(0)->nodeValue;
            } else {
                $amountCP = "";
            }
            $currencyCP = $cancellation_penalty->item(0)->getElementsByTagName('currency');
            if ($currencyCP->length > 0) {
                $currencyCP = $currencyCP->item(0)->nodeValue;
            } else {
                $currencyCP = "";
            }
            $basis = $cancellation_penalty->item(0)->getElementsByTagName('basis');
            if ($basis->length > 0) {
                $nights = $basis->item(0)->getElementsByTagName('nights');
                if ($nights->length > 0) {
                    $nights = $nights->item(0)->nodeValue;
                } else {
                    $nights = "";
                }
            }
        }
        $landing_url = $node->item(0)->getElementsByTagName('landing-url');
        if ($landing_url->length > 0) {
            $landing_url = $landing_url->item(0)->nodeValue;
        } else {
            $landing_url = "";
        }
        error_log("\r\n landing_url: $landing_url \r\n", 3, "/srv/www/htdocs/error_log");
        $customer_payment_disclosure = $node->item(0)->getElementsByTagName('customer-payment-disclosure');
        if ($customer_payment_disclosure->length > 0) {
            $booking_message = $customer_payment_disclosure->item(0)->getElementsByTagName('booking-message');
            if ($booking_message->length > 0) {
                $booking_message = $booking_message->item(0)->nodeValue;
            } else {
                $booking_message = "";
            }
            error_log("\r\n booking_message: $booking_message \r\n", 3, "/srv/www/htdocs/error_log");
            $phone_number = $customer_payment_disclosure->item(0)->getElementsByTagName('phone-number');
            if ($phone_number->length > 0) {
                $phone_number = $phone_number->item(0)->nodeValue;
            } else {
                $phone_number = "";
            }
            $billing_descriptor = $customer_payment_disclosure->item(0)->getElementsByTagName('billing-descriptor');
            if ($billing_descriptor->length > 0) {
                $billing_descriptor = $billing_descriptor->item(0)->nodeValue;
            } else {
                $billing_descriptor = "";
            }
            $processed_in_country = $customer_payment_disclosure->item(0)->getElementsByTagName('processed-in-country');
            if ($processed_in_country->length > 0) {
                $processed_in_country = $processed_in_country->item(0)->nodeValue;
            } else {
                $processed_in_country = "";
            }
            $terms_and_conditions_url = $customer_payment_disclosure->item(0)->getElementsByTagName('terms-and-conditions-url');
            if ($terms_and_conditions_url->length > 0) {
                $terms_and_conditions_url = $terms_and_conditions_url->item(0)->nodeValue;
            } else {
                $terms_and_conditions_url = "";
            }
        }
        
        $vector['code'] = $hotel_id;
        
        $pricebreakdown = array();
        $pricebreakdownCount = 0;
        for ($rZZ = 0; $rZZ < $nightsPro; $rZZ ++) {
            $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
            $amount = $totalDP / $nightsPro;
            if ($GetaroomMarkup != 0) {
                $amount = $amount + (($amount * $GetaroomMarkup) / 100);
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
            if ($GetaroomMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
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
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_getaroom');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $GetaroomServiceURL . $raw,
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
        error_log("\r\n DEPOIS TRY \r\n", 3, "/srv/www/htdocs/error_log");
        
        $item['code'] = $shid;
        error_log("\r\n PASSOU AQUI 1 \r\n", 3, "/srv/www/htdocs/error_log");
        $item['name'] = $title;
        error_log("\r\n PASSOU AQUI 2 \r\n", 3, "/srv/www/htdocs/error_log");
        $item['total'] = $totalDP;
        error_log("\r\n PASSOU AQUI 3 \r\n", 3, "/srv/www/htdocs/error_log");
        $item['nett'] = $subtotalDP;
        error_log("\r\n PASSOU AQUI 4 \r\n", 3, "/srv/www/htdocs/error_log");
        $total = $total + $totalDP;
        error_log("\r\n PASSOU AQUI 5 \r\n", 3, "/srv/www/htdocs/error_log");
        $tot = $totalDP;
        $item['room'] = $title;
        $item['room_description'] = $description;
        $item['roomid'] = $room_id;
        error_log("\r\n PASSOU 1 \r\n", 3, "/srv/www/htdocs/error_log");
        $item['meal'] = $value['meal'];
        error_log("\r\n PASSOU 1 \r\n", 3, "/srv/www/htdocs/error_log");
        $item['total'] = $totalDP;
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $adults;
        $item['children'] = $children;
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        error_log("\r\n PASSOU 3 \r\n", 3, "/srv/www/htdocs/error_log");
        $item['cancelpolicy'] = str_replace("<p><br/></p>", "", $cancellation_policy);
        error_log("\r\n PASSOU 4 \r\n", 3, "/srv/www/htdocs/error_log");
        $item['cancelpolicy_deadline'] = $cancellation_deadline;
        error_log("\r\n PASSOU ITEM \r\n", 3, "/srv/www/htdocs/error_log");
        // $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        // $item['cancelpolicy_details'] = $cancelation_details;
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mgetaroom where sid='" . $shid . "' and hid=" . $hid;
error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
error_log("\r\n DEPOIS TRY SQL \r\n", 3, "/srv/www/htdocs/error_log");
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
error_log("\r\n DEPOIS IF TRY \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel->buffer();
error_log("\r\n DEPOIS TRY SQL 2 \r\n", 3, "/srv/www/htdocs/error_log");
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
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $row_country->buffer();
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
    error_log("\r\n $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
$response['hotel'] = $hotel;
error_log("\r\n DEPOIS TRY SQL 5 \r\n", 3, "/srv/www/htdocs/error_log");
$response['hotel']['images'] = $images;
error_log("\r\n DEPOIS TRY SQL 6 \r\n", 3, "/srv/www/htdocs/error_log");
$response['breakdown'] = $roombreakdown;
error_log("\r\n DEPOIS TRY SQL 7 \r\n", 3, "/srv/www/htdocs/error_log");
$response['total'] = $filter->filter($total);
error_log("\r\n DEPOIS TRY SQL 8 \r\n", 3, "/srv/www/htdocs/error_log");
$response['totalplain'] = number_format($total, 2, '.', '');
error_log("\r\n DEPOIS TRY SQL 9 \r\n", 3, "/srv/www/htdocs/error_log");
$response['searchsettings'] = $searchsettings;
error_log("\r\n DEPOIS TRY SQL 10 \r\n", 3, "/srv/www/htdocs/error_log");
$response['code'] = $vector['code'];
error_log("\r\n FIM \r\n", 3, "/srv/www/htdocs/error_log");
?>