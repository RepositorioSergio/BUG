<?php
// error_log("\r\nPolicies Panamericana\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
if ($lang != "" and $lang != "en") {
    if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    }
}
$valid = 0;
$hid = 0;
$shid = 0;
$warning = "";
$dbPanamericana = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_panamericana where session_id='$session_id'";
    $statement = $dbPanamericana->createStatement($sql);
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
    if ((int) $nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
        $statement2 = $dbPanamericana->createStatement($sql);
        $statement2->prepare();
        $row_settings = $statement2->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        } else {
            $sql = "select value from settings where name='panamericanaNationalityIsoCode2' and affiliate_id=0";
            $statement = $dbPanamericana->createStatement($sql);
            $statement->prepare();
            $row_settings = $statement->execute();
            $row_settings->buffer();
            if ($row_settings->valid()) {
                $row_settings = $row_settings->current();
                $sourceMarket = $row_settings['value'];
            } else {
                $sourceMarket = "";
            }
        }
    } else {
        $sql = "select value from settings where name='panamericanaNationalityIsoCode2' and affiliate_id=0";
        $statement = $dbPanamericana->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        } else {
            $sourceMarket = "";
        }
    }
    $residency = $searchsettings['residency'];
    $rooms = $searchsettings['rooms'];
    $adt = $searchsettings['adt'];
    $chd = $searchsettings['chd'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
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
$sql = "select value from settings where name='enablepanamericana' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $dbPanamericana->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_panamericana = $affiliate_id;
} else {
    $affiliate_id_panamericana = 0;
}
$sql = "select value from settings where name='panamericanaServiceURL' and affiliate_id=$affiliate_id_panamericana";
$statement = $dbPanamericana->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='panamericanaID' and affiliate_id=$affiliate_id_panamericana";
$statement = $dbPanamericana->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaID = $row_settings["value"];
}
$sql = "select value from settings where name='panamericanaPassword' and affiliate_id=$affiliate_id_panamericana";
$statement = $dbPanamericana->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaPassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='panamericanaCurrencyCode' and affiliate_id=$affiliate_id_panamericana";
$statement = $dbPanamericana->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panamericanaCurrencyCode = $row_settings["value"];
} else {
    $panamericanaCurrencyCode = "";
}
$fromPanamericana = DateTime::createFromFormat("d-m-Y", $from);
$toPanamericana = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromPanamericana->diff($toPanamericana);
$nights = $nights->format('%R%a');
$fromPanamericana = $fromPanamericana->getTimestamp();
$toPanamericana = $toPanamericana->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $rid = $value['rid'];
            $smbr = $value['smbr'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $cancelation_deadline = 0;
        if ($rooms == 1) {
            // Check pricing, availability & cancellation policies
            $xmlrequest = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><OTA_HotelBookingRuleService xmlns="http://www.opentravel.org/OTA/2003/05"><OTA_HotelBookingRuleRQ PrimaryLangID="en" SequenceNmbr="' . $smbr . '"><POS><Source AgentDutyCode="' . $panamericanaID . '"><RequestorID MessagePassword="' . $panamericanaPassword . '" /></Source></POS><RuleMessage HotelCode="' . $shid . '"><StatusApplication RatePlanCode="' . $rid . '" End="' . strftime("%Y-%m-%d", $toPanamericana) . '" Start="' . strftime("%Y-%m-%d", $fromPanamericana) . '" /><TPA_Extensions><ShowSupplements>0</ShowSupplements>';
            if ($panamericanaCurrencyCode != "") {
                $xmlrequest .= '<ForceCurrency>' . $panamericanaCurrencyCode . '</ForceCurrency>';
            } else {
                $xmlrequest .= '<ForceCurrency>USD</ForceCurrency>';
            }
            if ($sourceMarket != "") {
                $xmlrequest .= '<PaxCountry>' . $sourceMarket . '</PaxCountry>';
            }
            $xmlrequest .= '</TPA_Extensions></RuleMessage></OTA_HotelBookingRuleRQ></OTA_HotelBookingRuleService></soap:Body></soap:Envelope>';
            $ch = curl_init();
            // error_log("\r\n\r\nPanamericana " . $panamericanaServiceURL . "OTA_HotelBookingRule.asmx \n", 3, "/srv/www/htdocs/error_log");
            curl_setopt($ch, CURLOPT_URL, $panamericanaServiceURL . "OTA_HotelBookingRule.asmx");
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
            curl_setopt($ch, CURLOPT_VERBOSE, 0);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Accept: application/xml",
                "Content-type: text/xml",
                "Accept-Encoding: gzip, deflate",
                "Content-length: " . strlen($xmlrequest)
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $xmlresult = curl_exec($ch);
            $error = curl_error($ch);
            $headers = curl_getinfo($ch);
            curl_close($ch);
            $xmlresult = gzdecode($xmlresult);
            try {
                $sql = new Sql($dbPanamericana);
                $insert = $sql->insert();
                $insert->into('log_panamericana');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'Policies.php',
                    'errorline' => 0,
                    'errormessage' => $xmlrequest,
                    'sqlcontext' => $xmlresult,
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
            $CCDesc = "";
            if ($xmlresult != "") {
                $inputDoc = new DOMDocument();
                $inputDoc->loadXML($xmlresult);
                error_log("\r\n\r\nPanamericana - $xmlresult - \n", 3, "/srv/www/htdocs/error_log");
                $Errors = $inputDoc->getElementsByTagName("Errors");
                if ($Errors->length > 0) {
                    $Errors = $Errors->item(0)->getElementsByTagName("ErrorType");
                    if ($Errors->length > 0) {
                        $policy_error = true;
                        $ShortText = trim($Errors->item(0)->getAttribute("ShortText"));
                        error_log("\r\n\r\nPanamericana Policies Error\n", 3, "/srv/www/htdocs/error_log");
                        error_log("\r\n\r\n$xmlrequest\n", 3, "/srv/www/htdocs/error_log");
                        error_log("\r\n\r\n$xmlresult\n", 3, "/srv/www/htdocs/error_log");
                        error_log("\r\n\r\nPanamericana Policies Error - $ShortText\n", 3, "/srv/www/htdocs/error_log");
                        $policy_message = $ShortText;
                    }
                } else {
                    $RuleMessage = $inputDoc->getElementsByTagName("RuleMessage");
                    if ($RuleMessage->length > 0) {
                        $HotelName = $RuleMessage->item(0)->getAttribute("HotelName");
                    } else {
                        $HotelName = "";
                    }
                    $cancelation_details = array();
                    $StatusApplication = $inputDoc->getElementsByTagName("StatusApplication");
                    if ($StatusApplication->length > 0) {
                        $RatePlanCode = $StatusApplication->item(0)->getAttribute("RatePlanCode");
                        $node = $inputDoc->getElementsByTagName("BookingRules");
                        if ($node->length > 0) {
                            $BookingRule = $node->item(0)->getElementsByTagName("BookingRule");
                            if ($BookingRule->length > 0) {
                                $CancellationPolicy = $node->item(0)->getElementsByTagName("CancelPenalty");
                                if ($CancellationPolicy->length > 0) {
                                    $Description = $CancellationPolicy->item(0)->getElementsByTagName("PenaltyDescription");
                                    if ($Description->length > 0) {
                                        $Description = $Description->item(0)->getElementsByTagName("Text");
                                        if ($Description->length > 0) {
                                            $CancellationPolicy = $Description->item(0)->nodeValue;
                                        } else {
                                            $CancellationPolicy = "";
                                        }
                                    } else {
                                        $CancellationPolicy = "";
                                    }
                                } else {
                                    $CancellationPolicy = "";
                                }
                                $Description = $node->item(0)->getElementsByTagName("Description");
                                if ($Description->length > 0) {
                                    $Description = $Description->item(0)->getElementsByTagName("Text");
                                    if ($Description->length > 0) {
                                        $Description = $Description->item(0)->nodeValue;
                                    } else {
                                        $Description = "";
                                    }
                                } else {
                                    $Description = "";
                                }
                                if ($Description != "") {
                                    if ($CancellationPolicy != "") {
                                        $CancellationPolicy = $CancellationPolicy . "<br/><br/>";
                                    }
                                    $CancellationPolicy = $CancellationPolicy . $Description;
                                }
                                $HotelContent = $inputDoc->getElementsByTagName("HotelInfo");
                                if ($HotelContent->length > 0) {
                                    $HotelCategory = $HotelContent->item(0)->getElementsByTagName("Category");
                                    if ($HotelCategory->length > 0) {
                                        $HotelCategoryCode = strtoupper($HotelCategory->item(0)->getAttribute("Code"));
                                    } else {
                                        $HotelCategoryCode = "";
                                    }
                                    $Address = $HotelContent->item(0)->getElementsByTagName("Address");
                                    if ($Address->length > 0) {
                                        $Address = $Address->item(0)->nodeValue;
                                    } else {
                                        $Address = "";
                                    }
                                    $Zone = $HotelContent->item(0)->getElementsByTagName("Zone");
                                    if ($Zone->length > 0) {
                                        $Zone = $Zone->item(0)->nodeValue;
                                    } else {
                                        $Zone = "";
                                    }
                                } else {
                                    $Zone = "";
                                    $HotelName = "";
                                    $Address = "";
                                    $HotelCategoryCode = "";
                                }
                                $TPA_Extensions = $node->item(0)->getElementsByTagName("TPA_Extensions");
                                if ($TPA_Extensions->length > 0) {
                                    $CCDesc = "";
                                    $CancellationPolicyRules = $TPA_Extensions->item(0)->getElementsByTagName("CancellationPolicyRules");
                                    if ($CancellationPolicyRules->length > 0) {
                                        $CCurrencyCode = $CancellationPolicyRules->item(0)->getAttribute("CurrencyCode");
                                        $nonRefundable = $CancellationPolicyRules->item(0)->getAttribute("nonRefundable");
                                        if ($nonRefundable == "True") {
                                            $nonRefundable = "true";
                                        }
                                        $Rule = $CancellationPolicyRules->item(0)->getElementsByTagName("Rule");
                                        for ($z = 0; $z < $Rule->length; $z ++) {
                                            $cancelitem = array();
                                            $CCurrencyCodeTmp = $CCurrencyCode;
                                            $DateFrom = $Rule->item($z)->getAttribute("DateFrom");
                                            $DateTo = $Rule->item($z)->getAttribute("DateTo");
                                            $Type = $Rule->item($z)->getAttribute("Type");
                                            $FixedPrice = $Rule->item($z)->getAttribute("FixedPrice");
                                            $PercentPrice = $Rule->item($z)->getAttribute("PercentPrice");
                                            $Nights = $Rule->item($z)->getAttribute("Nights");
                                            $ApplicationTypeNights = $Rule->item($z)->getAttribute("ApplicationTypeNights");
                                            if ($CCDesc != "") {
                                                $CCDesc = $CCDesc . "<br/><br/>";
                                            }
                                            if ($DateTo == "") {
                                                $CCDesc = $CCDesc . $translator->translate("Cancelling from") . " " . $DateFrom . " " . $translator->translate("until the start date of travel");
                                            } else {
                                                $CCDesc = $CCDesc . $translator->translate("Cancelling from") . " " . $DateFrom . " " . $translator->translate("to") . " " . $DateTo;
                                            }
                                            if ($PercentPrice != "0.00") {
                                                $Amount = 0;
                                                $percentage = $PercentPrice;
                                                $CCDesc = $CCDesc . " : " . $PercentPrice . "% " . $translator->translate("of expenses");
                                            } else {
                                                $Amount = $FixedPrice;
                                                $percentage = 0;
                                                $CCDesc = $CCDesc . " : " . $FixedPrice . " " . $CCurrencyCodeTmp;
                                            }
                                            if ($Nights != 0) {
                                                $CCDesc = $CCDesc . " + " . $Nights . " " . $ApplicationTypeNights;
                                            }
                                            $date = new DateTime();
                                            // Fix: Added $Nights == 0
                                            if ($Amount == 0 and $PercentPrice == "0.00" and $Nights == 0) {
                                                // Fix: Added if ($DateTo == "") { (When there is no Date To assume check in date
                                                if ($DateTo == "") {
                                                    // Set to check out
                                                    $cdate[0] = date("Y", $fromPanamericana);
                                                    $cdate[1] = date("m", $fromPanamericana);
                                                    $cdate[2] = date("d", $fromPanamericana);
                                                } else {
                                                    $cdate = explode("-", $DateTo);
                                                }
                                            } else {
                                                $cdate = explode("-", $DateFrom);
                                            }
                                            $date->setDate($cdate[0], $cdate[1], $cdate[2]);
                                            if ($cancelation_deadline == 0) {
                                                $cancelation_deadline = $date->getTimestamp();
                                            } else {
                                                if ($date->getTimestamp() < $cancelation_deadline) {
                                                    $cancelation_deadline = $date->getTimestamp();
                                                }
                                            }
                                            $cancelitem['date'] = $date->format('d-m-Y');
                                            $cancelitem['date_timestamp'] = $date->getTimestamp();
                                            $cancelitem['nights'] = $Nights;
                                            $cancelitem['percentage'] = $percentage;
                                            $cancelitem['fullstay'] = 0;
                                            $cancelitem['currency'] = $CCurrencyCodeTmp;
                                            $cancelitem['amount'] = $Amount;
                                            array_push($cancelation_details, $cancelitem);
                                        }
                                    }
                                    if ($CCDesc != "") {
                                        $CancellationPolicy = $CCDesc;
                                        if ($Description != "") {
                                            if ($CancellationPolicy != "") {
                                                $CancellationPolicy = $CancellationPolicy . "<br/><br/>";
                                            }
                                            $CancellationPolicy = $CancellationPolicy . $Description;
                                        }
                                    }
                                    $CancellationPolicy = str_replace("<![CDATA[", "", $CancellationPolicy);
                                    $CancellationPolicy = str_replace("]]>", "", $CancellationPolicy);
                                    $TotalPrice = $TPA_Extensions->item(0)->getElementsByTagName("TotalPrice");
                                    if ($TotalPrice->length > 0) {
                                        $Nett = $TotalPrice->item(0)->nodeValue;
                                        $NettCurrencyCode = $TotalPrice->item(0)->getAttribute("CurrencyCode");
                                    } else {
                                        $Nett = 0;
                                        $NettCurrencyCode = "";
                                    }
                                } else {
                                    $Nett = 0;
                                    $NettCurrencyCode = "";
                                }
                                if ($NettCurrencyCode != "" and $scurrency != $NettCurrencyCode and $NettCurrencyCode != "" and $scurrency != "") {
                                    $Nett = $CurrencyConverter->convert($Nett, $NettCurrencyCode, $scurrency);
                                }
                                if ((int) $Nett > (int) $value['nettotal']) {
                                    $oldtotal = $value['total'];
                                    $oldnettotal = $value['nettotal'];
                                    //
                                    // Price Changed
                                    //
                                    $value['nettotal'] = $Nett;
                                    if ($panamericanaMarkup != 0) {
                                        $Nett = $Nett + (($Nett * $panamericanaMarkup) / 100);
                                    }
                                    // Geo target markup
                                    if ($internalmarkup != 0) {
                                        $Nett = $Nett + (($Nett * $internalmarkup) / 100);
                                    }
                                    // Agent markup
                                    if ($agent_markup != 0) {
                                        $Nett = $Nett + (($Nett * $agent_markup) / 100);
                                    }
                                    // Fallback Markup
                                    if ($panamericanaMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                        $Nett = $Nett + (($Nett * $HotelsMarkupFallback) / 100);
                                    }
                                    // Agent discount
                                    if ($agent_discount != 0) {
                                        $Nett = $Nett - (($Nett * $agent_discount) / 100);
                                    }
                                    $value['total'] = $Nett;
                                    $pricechanged = true;
                                    $sql = new Sql($dbPanamericana);
                                    $sql = "delete from dp_hotels_pricechange where session_id='" . $session_id . "' and quoteid='" . (string) $value['quoteid'] . "'";
                                    try {
                                        $statement = $dbPanamericana->createStatement($sql);
                                        $statement->prepare();
                                        $results = $statement->execute();
                                    } catch (\Exception $e) {
                                        $logger = new Logger();
                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                        $logger->addWriter($writer);
                                        $logger->info($e->getMessage());
                                    }
                                    try {
                                        $sql = new Sql($dbPanamericana);
                                        $insert = $sql->insert();
                                        $insert->into('dp_hotels_pricechange');
                                        $insert->values(array(
                                            'datetime_created' => time(),
                                            'session_id' => (string) $session_id,
                                            'total' => (string) $value['total'],
                                            'nettotal' => (string) $value['nettotal'],
                                            'oldtotal' => (string) $oldtotal,
                                            'oldnettotal' => (string) $oldnettotal,
                                            'quoteid' => (string) $value['quoteid']
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                    } catch (\Exception $e) {
                                        $logger = new Logger();
                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                        $logger->addWriter($writer);
                                        $logger->info($e->getMessage());
                                    }
                                    $warning = $translator->translate("Price has changed. Please review booking.");
                                }
                            }
                        }
                    }
                }
                if ($CCDesc != "") {
                    $cancelation_string = $CCDesc;
                } else {
                    $cancelation_string = "";
                }
            } else {
                $policy_error = true;
            }
        }
        // EOF Check prices & availability
        $item = array();
        $total = $total + $value['total'];
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        if ($tax > 0) {
            $tot = $value['total'] - floatval($tax);
            $item['subtotal'] = $filter->filter(floatval($tot));
            $item['tax'] = $filter->filter(floatval($tax));
        } else {
            $item['tax'] = "";
            $tot = $value['total'];
            $item['subtotal'] = $filter->filter(floatval($tot));
        }
        $item['nonrefundable'] = $value['nonrefundable'];
        $item['recommended'] = $value['recommended'];
        $item['total'] = $filter->filter($tot);
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $item['cancelpolicy'] = $cancelation_string;
        if ($cancelation_deadline != 0) {
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $cancelation_deadline);
            $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
            $item['cancelpolicy_details'] = $cancelation_details;
        } else {
            $item['cancelpolicy_deadline'] = "";
            $item['cancelpolicy_deadlinetimestamp'] = 0;
            $item['cancelpolicy_details'] = "";
        }
        $response['warning'] = $warning;
        // EOF Policies
        array_push($roombreakdown, $item);
    }
    $c ++;
}
try {
    $hotel = array();
    $sql = "select sid from xmlhotels_mpanamericana where sid='" . $shid . "' and hid=" . $hid;
    $statement = $dbPanamericana->createStatement($sql);
    $statement->prepare();
    $row_hotel = $statement->execute();
    $row_hotel->buffer();
    if (! $row_hotel->valid()) {
        $response['error'] = "Unable to handle request #6";
        return false;
    }
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $dbPanamericana->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
    $row_hotel->buffer();
} catch (\Exception $e) {
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
    $statement2 = $dbPanamericana->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
        $row_country->buffer();
    } catch (\Exception $e) {
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
    $response['error'] = "Unable to handle request #7";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $dbPanamericana->createStatement($sql);
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
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$dbPanamericana->getDriver()
    ->getConnection()
    ->disconnect();
// error_log("\r\nPolicies Panamericana - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>