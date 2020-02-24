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
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_gta where session_id='$session_id'";
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
    $rooms = $searchsettings['rooms'];
    $adt = $searchsettings['adt'];
    $chd = $searchsettings['chd'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enablegta' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_gta = $affiliate_id;
} else {
    $affiliate_id_gta = 0;
}
$sql = "select value from settings where name='gtalogin' and affiliate_id=$affiliate_id_gta";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtalogin = $row_settings["value"];
}
$sql = "select value from settings where name='gtaemail' and affiliate_id=$affiliate_id_gta";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtaemail = $row_settings["value"];
}
$sql = "select value from settings where name='gtapassword' and affiliate_id=$affiliate_id_gta";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtapassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='gtacurrency' and affiliate_id=$affiliate_id_gta";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtacurrency = $row_settings["value"];
}
$sql = "select value from settings where name='gtasubmissionurl' and affiliate_id=$affiliate_id_gta";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtasubmissionurl = $row_settings["value"];
}
$sql = "select value from settings where name='gtatesting' and affiliate_id=$affiliate_id_gta";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $gtatesting = $row_settings["value"];
}
if ($gtatesting == 1) {
    $gtasubmissionurl = "https://interface.demo.gta-travel.com/rbsusapi/RequestListenerServlet";
}
$languageGTA = substr($lang, 0, 2);
if ($languageGTA == "") {
    $languageGTA = "en";
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
$fromGTA = DateTime::createFromFormat("d-m-Y", $from);
$toGTA = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromGTA->diff($toGTA);
$nights = $nights->format('%a');
$fromGTA = $fromGTA->getTimestamp();
$toGTA = $toGTA->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $item = array();
        $cancelation_string = $value['cancelpolicy'];
        // Check pricing & availability
        //
        // Get Cancellation Policies
        //
        if ($value['countrytag'] != "") :
            $countrytag = $value['countrytag'];
        else :
            $countrytag = '';
        endif;
        $xmlrequestcancel = '<?xml version="1.0" encoding="UTF-8" ?><Request><Source><RequestorID Client="' . $gtalogin . '" EMailAddress="' . $gtaemail . '" Password="' . $gtapassword . '"/>';
        // Add Requestor Preferences data
        if ($gtacurrency != "") {
            $Currency = $gtacurrency;
            $xmlrequestcancel .= '<RequestorPreferences Language="' . $languageGTA . '" Currency="' . $gtacurrency . '"' . $countrytag . '>';
        } else {
            $xmlrequestcancel .= '<RequestorPreferences Language="' . $languageGTA . '"' . $countrytag . '>';
        }
        $xmlrequestcancel .= '<RequestMode>SYNCHRONOUS</RequestMode></RequestorPreferences></Source><RequestDetails><SearchHotelPricePaxRequest><ItemDestination DestinationCode="' . $value['ccd'] . '" DestinationType="city"/><ItemCode>' . $value['shid'] . '</ItemCode><PeriodOfStay><CheckInDate>' . strftime("%Y-%m-%d", $fromGTA) . '</CheckInDate><Duration>' . $nights . '</Duration></PeriodOfStay><IncludePriceBreakdown/><IncludeChargeConditions/><PaxRooms>';
        $RoomIndex = 1;
        for ($r = 0; $r < count($selectedAdults); $r ++) {
            $xmlrequestcancel .= '<PaxRoom Adults="' . $selectedAdults[$r] . '" Id="' . $value['offerid'] . '" Cots="0" RoomIndex="' . $RoomIndex . '" />';
            $RoomIndex = $RoomIndex + 1;
        }
        $xmlrequestcancel .= '</PaxRooms></SearchHotelPricePaxRequest></RequestDetails></Request>';
        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100
        ));
        $client->setHeaders(array(
            "Accept-Encoding" => "gzip, deflate",
            "User-Agent" => "curl/7.37.0"
        ));
        $client->setUri($gtasubmissionurl);
        $client->setRawBody($xmlrequestcancel);
        $client->setMethod('POST');
        $responsegta = $client->send();
        if ($responsegta->isSuccess()) {
            $xmlresult = $responsegta->getBody();
        } else {
            error_log("\r\n\r\n" . $responsegta->getStatusCode() . "/" . $responsegta->getReasonPhrase() . "\r\n", 3, "/srv/www/htdocs/error_log");
            echo $responsegta->getStatusCode() . " - " . $responsegta->getReasonPhrase();
            die();
        }
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_gta');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => 0,
                'errormessage' => $xmlrequestcancel,
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
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresult);
        $cancellationpolicy = "";
        $cancelation_details = array();
        $cancelation_deadline = 0;
        $ChargeConditions = $inputDoc->getElementsByTagName('ChargeConditions');
        if ($ChargeConditions->length > 0) {
            $ChargeCondition = $ChargeConditions->item(0)->getElementsByTagName("ChargeCondition");
            for ($xChargeCondition = 0; $xChargeCondition < $ChargeCondition->length; $xChargeCondition ++) {
                if ($cancellationpolicy != "") {
                    $cancellationpolicy .= "<br/><br/>";
                }
                $ctype = $ChargeCondition->item($xChargeCondition)->getAttribute('Type');
                $cancellationpolicy .= ucwords($ChargeCondition->item($xChargeCondition)->getAttribute('Type'));
                if ($ChargeCondition->item($xChargeCondition)->getAttribute('Allowable') == 'false') {
                    $cancellationpolicy .= "<br/>" . $translator->translate("Not allowable") . "<br/><br/>";
                } else {
                    $Condition = $ChargeCondition->item($xChargeCondition)->getElementsByTagName("Condition");
                    for ($xCondition = 0; $xCondition < $Condition->length; $xCondition ++) {
                        if ($Condition->item($xCondition)->getAttribute('Charge') == 'false') {
                            $Amount = 0;
                            $fromDate = mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $fromGTA));
                            $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $fromGTA))) . " " . $translator->translate("or earlier") . " " . $translator->translate("you will not be charged");
                        } else {
                            $Amount = $Condition->item($xCondition)->getAttribute('ChargeAmount');
                            $ccurrency = $Condition->item($xCondition)->getAttribute('Currency');
                            if ($Condition->item($xCondition)->getAttribute('FromDay') == 0) {
                                if ($Condition->item($xCondition)->getAttribute('ToDay') == "") {
                                    if ($Condition->item($xCondition)->getAttribute('Allowable') == "false") {
                                        $cancellationpolicy .= "<br/>" . $translator->translate("Not allowable");
                                    } else {
                                        $fromDate = time();
                                        $cancellationpolicy .= "<br/>" . $translator->translate("Effective today you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                    }
                                } else {
                                    $fromDate = mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $fromGTA));
                                    $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $fromGTA))) . " " . $translator->translate("onwards") . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                }
                            } else {
                                if ($Condition->item($xCondition)->getAttribute('ToDay') == $Condition->item($xCondition)->getAttribute('FromDay')) {
                                    $fromDate = mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $fromGTA));
                                    $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $fromGTA))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                } else {
                                    if ($Condition->item($xCondition)->getAttribute('Type') == "cancellation") {
                                        $FromDay = $Condition->item($xCondition)->getAttribute('FromDay');
                                        if ($FromDay == "") {
                                            $FromDay = 0;
                                        }
                                        if (! is_numeric($FromDay)) {
                                            $FromDay = 0;
                                        }
                                        $ToDay = $Condition->item($xCondition)->getAttribute('ToDay');
                                        if ($ToDay == "") {
                                            $ToDay = 0;
                                        }
                                        if (! is_numeric($ToDay)) {
                                            $ToDay = 0;
                                        }
                                        $fromDate = time();
                                        if ($ToDay == 0) {
                                            if ($cancellationpolicy != "") {
                                                $cancellationpolicy .= "<br/>";
                                            }
                                            $cancellationpolicy .= $translator->translate("From today to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $FromDay, date("Y", $fromGTA))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                        } else {
                                            $a = mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $ToDay, date("Y", $fromGTA));
                                            $b = mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $FromDay, date("Y", $fromGTA));
                                            if ($a > b) {
                                                $cancellationpolicy .= $FromDay . "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $FromDay, date("Y", $fromGTA))) . " " . $translator->translate("to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $fromGTA))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                            } else {
                                                $cancellationpolicy .= $FromDay . "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('ToDay'), date("Y", $fromGTA))) . " " . $translator->translate("to") . " " . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $FromDay, date("Y", $fromGTA))) . " " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                            }
                                        }
                                    } else {
                                        $fromDate = mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $fromGTA));
                                        $cancellationpolicy .= "<br/>" . strftime("%d %b %Y", mktime(0, 0, 0, date("m", $fromGTA), date("d", $fromGTA) - $Condition->item($xCondition)->getAttribute('FromDay'), date("Y", $fromGTA))) . " or earlier " . $translator->translate("you will be charged") . " " . $Condition->item($xCondition)->getAttribute('Currency') . " " . $Condition->item($xCondition)->getAttribute('ChargeAmount');
                                    }
                                }
                            }
                        }
                        if ($ctype == "cancellation") {
                            $cancelitem = array();
                            $date = new DateTime();
                            $date->setTimestamp($fromDate);
                            if ($cancelation_deadline == 0) {
                                $cancelation_deadline = $date->getTimestamp();
                            } else {
                                if ($date->getTimestamp() < $cancelation_deadline) {
                                    $cancelation_deadline = $date->getTimestamp();
                                }
                            }
                            $cancelitem['date'] = $date->format('d-m-Y');
                            $cancelitem['date_timestamp'] = $date->getTimestamp();
                            $cancelitem['nights'] = 0;
                            $cancelitem['percentage'] = 0;
                            $cancelitem['fullstay'] = 0;
                            $cancelitem['currency'] = $ccurrency;
                            $cancelitem['amount'] = $Amount;
                            array_push($cancelation_details, $cancelitem);
                        }
                    }
                }
            }
        }
        if ($cancellationpolicy != "") {
            $cancelation_string = $cancellationpolicy;
        }
        //
        // EOF
        //
        $item['cancelpolicy'] = $cancelation_string;
        $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $cancelation_deadline);
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelation_details;
        // EOF Policies
        // EOF Check prices & availability
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
        $item['total'] = $filter->filter($tot);
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        array_push($roombreakdown, $item);
    }
    $c ++;
}
try {
    $hotel = array();
    $sql = "select sid from xmlhotels_mgta where sid='" . $shid . "' and hid=" . $hid;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_hotel = $statement->execute();
    $row_hotel->buffer();
    if (! $row_hotel->valid()) {
        $response['error'] = "Unable to handle request #5";
        return false;
    }
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
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
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>