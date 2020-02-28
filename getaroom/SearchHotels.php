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
$getaroom = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select name, country_id, zone_id,city_xml56, latitude, longitude from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml56 = $row_settings["city_xml56"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml56 = "";
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
$sql = "select value from settings where name='GetaroomAuth' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuth = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAPIKey' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAPIKey = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomMarkup' and affiliate_id=$affiliate_id_getaroom";
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
$sql = "select value from settings where name='GetaroomServiceURL' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomServiceURL = $row_settings['value'];
}
error_log("\r\n GetaroomServiceURL: $GetaroomServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='GetaroomAuthorizationToken' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuthorizationToken = $row_settings['value'];
}
$sql = "select value from settings where name='GetaroomAuth' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomAuth = $row_settings['value'];
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');

$date = new Datetime();
$timestamp = $date->format('U');
$raw = 'api/1.1/properties/b41ce02e-69a3-4a28-8bf8-2ab48d0d4135/room_availability?rinfo=%5B%5B18,18%5D%5D&check_in=' . strftime("%m/%d/%Y", $from) . '&check_out=' . strftime("%m/%d/%Y", $to) . '&cancellation_rules=1&room_id&rate_plan_code&api_key=' . $GetaroomAPIKey . '&auth_token=' . $GetaroomAuth;
if ($GetaroomServiceURL != "" and $GetaroomAuth != "" and $GetaroomAPIKey != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $GetaroomServiceURL . $raw);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // error_log("\r\n response: $response \r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_getaroom');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => $this->microtime_diff($startTime, $endTime),
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
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
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
    error_log("\r\n TAM: $node->length \r\n", 3, "/srv/www/htdocs/error_log");
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        error_log("\r\n rAUX: $rAUX \r\n", 3, "/srv/www/htdocs/error_log");
        $room = $node->item($rAUX)->getElementsByTagName("room");
        if ($room->length > 0) {
            $hotel_id = $room->item(0)->getElementsByTagName('hotel-id');
            if ($hotel_id->length > 0) {
                $hotel_id = $hotel_id->item(0)->nodeValue;
            } else {
                $hotel_id = "";
            }
            $shid = $hotel_id;
            $sfilter[] = " sid='$hotel_id' ";
            $room_id = $room->item(0)->getElementsByTagName('room-id');
            if ($room_id->length > 0) {
                $room_id = $room_id->item(0)->nodeValue;
            } else {
                $room_id = "";
            }
            error_log("\r\n room_id: $room_id \r\n", 3, "/srv/www/htdocs/error_log");
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
        $commission_tier = $node->item($rAUX)->getElementsByTagName('commission-tier');
        if ($commission_tier->length > 0) {
            $commission_tier = $commission_tier->item(0)->nodeValue;
        } else {
            $commission_tier = "";
        }
        $refundable = $node->item($rAUX)->getElementsByTagName('refundable');
        if ($refundable->length > 0) {
            $refundable = $refundable->item(0)->nodeValue;
        } else {
            $refundable = "";
        }
        $sale = $node->item($rAUX)->getElementsByTagName('sale');
        if ($sale->length > 0) {
            $sale = $sale->item(0)->nodeValue;
        } else {
            $sale = "";
        }
        $promotional_text = $node->item($rAUX)->getElementsByTagName('promotional-text');
        if ($promotional_text->length > 0) {
            $promotional_text = $promotional_text->item(0)->nodeValue;
        } else {
            $promotional_text = "";
        }
        $promotion_details = $node->item($rAUX)->getElementsByTagName('promotion-details');
        if ($promotion_details->length > 0) {
            $promotion_details = $promotion_details->item(0)->nodeValue;
        } else {
            $promotion_details = "";
        }

        
        $rooms_left = $node->item($rAUX)->getElementsByTagName('rooms-left');
        if ($rooms_left->length > 0) {
            $rooms_left = $rooms_left->item(0)->nodeValue;
        } else {
            $rooms_left = "";
        }
        $timed_pressure_sell = $node->item($rAUX)->getElementsByTagName('timed-pressure-sell');
        if ($timed_pressure_sell->length > 0) {
            $timed_pressure_sell = $timed_pressure_sell->item(0)->nodeValue;
        } else {
            $timed_pressure_sell = "";
        }
        $expires_at = $node->item($rAUX)->getElementsByTagName('expires-at');
        if ($expires_at->length > 0) {
            $expires_at = $expires_at->item(0)->nodeValue;
        } else {
            $expires_at = "";
        }
        $restrictions = $node->item($rAUX)->getElementsByTagName('restrictions');
        if ($restrictions->length > 0) {
            $restrictions = $restrictions->item(0)->nodeValue;
        } else {
            $restrictions = "";
        }
        $geo_restrictions = $node->item($rAUX)->getElementsByTagName('geo-restrictions');
        if ($geo_restrictions->length > 0) {
            $geo_restrictions = $geo_restrictions->item(0)->nodeValue;
        } else {
            $geo_restrictions = "";
        }
        $lowest_average = $node->item($rAUX)->getElementsByTagName('lowest-average');
        if ($lowest_average->length > 0) {
            $lowest_average = $lowest_average->item(0)->nodeValue;
        } else {
            $lowest_average = "";
        }
        $fees_collected = $node->item($rAUX)->getElementsByTagName('fees-collected-at-property');
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
        $display_pricing = $node->item($rAUX)->getElementsByTagName('display-pricing');
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
        $lowest_averageDP = $display_pricing->item(0)->getElementsByTagName('lowest-average');
        if ($lowest_averageDP->length > 0) {
            $reference_amountDP = $lowest_averageDP->item(0)->getAttribute('reference-amount');
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
                    $reference_amountNR = $rateNR->item(0)->getAttribute('reference-amount');
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
        $booking_pricing = $node->item($rAUX)->getElementsByTagName('booking-pricing');
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
            $reference_amountBP = $taxesBP->item(0)->getAttribute('reference-amount');
            $taxesBP = $taxesBP->item(0)->nodeValue;
        } else {
            $taxesBP = "";
        }
        $feesBP = $booking_pricing->item(0)->getElementsByTagName('fees');
        if ($feesBP->length > 0) {
            $allow_markupBP = $feesBP->item(0)->getAttribute('allow-markup');
            $feesBP = $feesBP->item(0)->nodeValue;
        } else {
            $feesBP = "";
        }
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
            $reference_amountBP = $lowest_averageBP->item(0)->getAttribute('reference-amount');
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
                    $reference_amountNRB = $rateNRB->item(0)->getAttribute('reference-amount');
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
        $rate_plan_type = $node->item($rAUX)->getElementsByTagName('rate-plan-type');
        if ($rate_plan_type->length > 0) {
            $rate_plan_type = $rate_plan_type->item(0)->nodeValue;
        } else {
            $rate_plan_type = "";
        }
        $rate_plan_code = $node->item($rAUX)->getElementsByTagName('rate-plan-code');
        if ($rate_plan_code->length > 0) {
            $rate_plan_code = $rate_plan_code->item(0)->nodeValue;
        } else {
            $rate_plan_code = "";
        }
        $policy_description = $node->item($rAUX)->getElementsByTagName('policy-description');
        if ($policy_description->length > 0) {
            $policy_description = $policy_description->item(0)->nodeValue;
        } else {
            $policy_description = "";
        }
        $cancellation_deadline = $node->item($rAUX)->getElementsByTagName('cancellation-deadline');
        if ($cancellation_deadline->length > 0) {
            $cancellation_deadline = $cancellation_deadline->item(0)->nodeValue;
        } else {
            $cancellation_deadline = "";
        }
        $cancellation_policy = $node->item($rAUX)->getElementsByTagName('cancellation-policy');
        if ($cancellation_policy->length > 0) {
            $cancellation_policy = $cancellation_policy->item(0)->nodeValue;
        } else {
            $cancellation_policy = "";
        }
        $terms_acceptance_text = $node->item($rAUX)->getElementsByTagName('terms-acceptance-text');
        if ($terms_acceptance_text->length > 0) {
            $terms_acceptance_text = $terms_acceptance_text->item(0)->nodeValue;
        } else {
            $terms_acceptance_text = "";
        }
        $cancellation_penalties = $node->item($rAUX)->getElementsByTagName('cancellation-penalties');
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
        $landing_url = $node->item($rAUX)->getElementsByTagName('landing-url');
        if ($landing_url->length > 0) {
            $landing_url = $landing_url->item(0)->nodeValue;
        } else {
            $landing_url = "";
        }
        $ratetoken = $node->item($rAUX)->getElementsByTagName('rate-token');
        if ($ratetoken->length > 0) {
            $ratetoken = $ratetoken->item(0)->nodeValue;
        } else {
            $ratetoken = "";
        }

        $count = 0;
        $vaArray = array();
        $vaCount = 0;
        $value_adds = $node->item($rAUX)->getElementsByTagName('value-adds');
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
        
        for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
            if (is_array($tmp[$shid])) {
                $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
            } else {
                $baseCounterDetails = 0;
            }

             $nameVA = $vaArray[$count]['name'];
            error_log("\r\n nameVA: $nameVA \r\n", 3, "/srv/www/htdocs/error_log"); 

            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $title;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $hotel_id;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $room_id;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $rate_plan_code;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
            // cancellationType nao existe
            // $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $c_type;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-56";
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $title;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $description;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_type'] = $room_id;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $adults;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $children;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $totalDP;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $subtotalDP;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['refundable'] = $refundable;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rate_plan_code'] = $rate_plan_code;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($nameVA);
            $pricebreakdown = array();
            $pricebreakdownCount = 0;
            for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                $amount = $totalDP / $noOfNights;
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
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $currency;
            if ($promotional_text != "") {
                $tmp[$shid]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $promotional_text;
            } else {
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
            }
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $cancellation_policy;
            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $cancellation_deadline;
            $count = $count + 1;
        }
    //}
//}
        $getaroom = true;  
    }
    error_log("\r\n" . print_r($tmp, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
    if ($getaroom == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mgetaroom where " . $sfilter;
            $statement2 = $db->createStatement($sql);
            $statement2->prepare();
            $result2 = $statement2->execute();
            $result2->buffer();
            //error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
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
            $supplier = 56;
            // Store Session
            $sql = new Sql($db);
            //error_log("\r\n$query\r\n", 3, "/srv/www/htdocs/error_log");
            $delete = $sql->delete();
            $delete->from('quote_session_getaroom');
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
            $insert->into('quote_session_getaroom');
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