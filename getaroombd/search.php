<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
//echo "COMECOU CITIES";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
// Start
$affiliate_id = 0;
$branch_filter = "";
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
$sql = "select value from settings where name='GetaroomContentServiceURL' and affiliate_id=$affiliate_id_getaroom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $GetaroomContentServiceURL = $row_settings['value'];
}
echo "URL " . $GetaroomContentServiceURL;
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$Token = "742106fd";
$APIKey = "47b668f9-0f5a-5716-ae88-7156b1bf2dc7";
$AuthToken = "f8ce304f-5d0f-5e86-8cfd-f0beeea39d4a";
$raw = 'api/1.1/properties/b41ce02e-69a3-4a28-8bf8-2ab48d0d4135/room_availability?rinfo=%5B%5B18,18%5D%5D&check_in=06/14/2020&check_out=06/21/2020&cancellation_rules=1&room_id&rate_plan_code&api_key=' . $APIKey . '&auth_token=' . $AuthToken;

$url = "https://availability.integration2.testaroom.com/";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . $raw);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";

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
$currency = $requestTmp->item(0)->getElementsByTagName("currency");
if ($currency->length > 0) {
    $currency = $currency->item(0)->nodeValue;
} else {
    $currency = "";
}
$node = $room_stays->item(0)->getElementsByTagName("room-stay");
for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>
