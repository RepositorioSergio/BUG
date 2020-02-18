<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
use Zend\Http\Request;
$translator = new Translator();
$sindex = $index;
$valid = 0;
$hid = 0;
$shid = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_restel where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_restel where session_id='$session_id'";
}
try {
    $statement = $db->query($sql);
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
$outputArray = array();
$arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
foreach ($arrIt as $sub) {
    $subArray = $arrIt->getSubIterator();
    if (isset($quoteid[$nroom])) {
        if (isset($subArray['quoteid'])) {
            if ($subArray['quoteid'] === $quoteid[$nroom]) {
                $outputArray[] = iterator_to_array($subArray);
                $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                    ->key();
            }
        }
    }
}
$breakdownTmp = array();
if (! is_array($outputArray)) {
    $response['error'] = "Unable to handle request #3";
    return false;
} else {
    array_push($breakdownTmp, $outputArray);
}
$sql = "select value from settings where name='enableRestelHotUSA' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_restel = $affiliate_id;
} else {
    $affiliate_id_restel = 0;
}
$sql = "select value from settings where name='RestelHotUSAUsername' and affiliate_id=$affiliate_id_restel" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $RestelHotUSAUsername = $row_settings["value"];
}
$sql = "select value from settings where name='RestelHotUSApassword' and affiliate_id=$affiliate_id_restel" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $RestelHotUSApassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='RestelHotUSAMarkupd' and affiliate_id=$affiliate_id_restel" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $RestelHotUSAMarkup = $row_settings["value"];
} else {
    $RestelHotUSAMarkup = 0;
}
$sql = "select value from settings where name='RestelHotUSAAccessCode' and affiliate_id=$affiliate_id_restel" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $RestelHotUSAAccessCode = $row_settings["value"];
}
$sql = "select value from settings where name='RestelAffiliate' and affiliate_id=$affiliate_id_restel" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $RestelAffiliate = $row_settings["value"];
}
$sql = "select value from settings where name='RestelHotUSAServiceURL' and affiliate_id=$affiliate_id_restel" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $RestelHotUSAServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='RestelHotUSACustomerID' and affiliate_id=$affiliate_id_restel" . $branch_filter;
$statement = $db->query($sql);
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $RestelHotUSACustomerID = $row_settings["value"];
}
$fromRestel = DateTime::createFromFormat("d-m-Y", $from);
$toRestel = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromRestel->diff($toRestel);
$nights = $nights->format('%a');
$fromRestel = $fromRestel->getTimestamp();
$toRestel = $toRestel->getTimestamp();
$c = $nroom;
$response = array();
$roombreakdown2 = array();
foreach ($breakdownTmp as $k => $v) {
    foreach ($v as $key => $value) {
        $shid = $value['shid'];
        $item = array();
        $cancelation_string = $value['cancelpolicy'];
        $cancelation_deadline = 0;
        $cancelation_details = "";
        //
        // EOF
        //
        $tot = $value['total'];
        $lin = unserialize($value['lin']);
        $xml = 'xml=<?xml version="1.0" encoding="UTF-8"?><peticion><tipo>144</tipo><nombre>Cancel policies</nombre><agencia>BUG</agencia><parametros><datos_reserva><hotel>' . $value['shid'] . '</hotel>';
        $clin = count($lin);
        for ($r = 0; $r < $clin; $r ++) {
            $xml .= '<lin><![CDATA[' . $lin[$r] . ']]></lin>';
        }
        $xml .= '</datos_reserva></parametros></peticion>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_URL, $RestelHotUSAServiceURL . "listen_xml.jsp?codigousu=" . $RestelHotUSAUsername . "&clausu=" . $RestelHotUSApassword . "&afiliacio=" . $RestelAffiliate . "&secacc=" . $RestelHotUSAAccessCode);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responses = curl_exec($ch);
        curl_close($ch);
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_restel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $xml,
                'sqlcontext' => $responses,
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
        $inputDoc->loadXML($responses);
        $politicaCanc = $inputDoc->getElementsByTagName("politicaCanc");
        if ($politicaCanc->length > 0) {
            $fecha = $politicaCanc->item(0)->getAttribute("fecha");
            $dias_antelacion = $politicaCanc->item(0)->getElementsByTagName("dias_antelacion");
            if ($dias_antelacion->length > 0) {
                $dias_antelacion = $dias_antelacion->item(0)->nodeValue;
            } else {
                $dias_antelacion = 0;
            }
            $horas_antelacion = $politicaCanc->item(0)->getElementsByTagName("horas_antelacion");
            if ($horas_antelacion->length > 0) {
                $horas_antelacion = $horas_antelacion->item(0)->nodeValue;
            } else {
                $horas_antelacion = 0;
            }
            $noches_gasto = $politicaCanc->item(0)->getElementsByTagName("noches_gasto");
            if ($noches_gasto->length > 0) {
                $noches_gasto = $noches_gasto->item(0)->nodeValue;
            } else {
                $noches_gasto = 0;
            }
            $estCom_gasto = $politicaCanc->item(0)->getElementsByTagName("estCom_gasto");
            if ($estCom_gasto->length > 0) {
                $estCom_gasto = $estCom_gasto->item(0)->nodeValue;
            } else {
                $estCom_gasto = 0;
            }
            $concepto = $politicaCanc->item(0)->getElementsByTagName("concepto");
            if ($concepto->length > 0) {
                $concepto = $concepto->item(0)->nodeValue;
            } else {
                $concepto = "";
            }
            $entra_en_gastos = $politicaCanc->item(0)->getElementsByTagName("entra_en_gastos");
            if ($entra_en_gastos->length > 0) {
                $entra_en_gastos = $entra_en_gastos->item(0)->nodeValue;
            } else {
                $entra_en_gastos = 0;
            }
            if ($dias_antelacion > 0) {
                $cancelation_deadline = mktime(0, 0, 0, date("m", $fromRestel), date("d", $fromRestel) - $dias_antelacion, date("Y", $fromRestel));
            }
        } else {
            $concepto = "";
        }
        if ($cancelation_string != "") {
            $cancelation_string .= "<br/>";
        }
        if ($concepto != "") {
            $cancelation_string .= $concepto;
        }
        if ($value['city_tax'] != "") {
            if ($cancelation_string != "") {
                $cancelation_string .= "<br/>";
            }
            $cancelation_string .= $value['city_tax'];
        }
        $item['cancelpolicy'] = $cancelation_string;
        if ($cancelation_deadline < time()) {
            $cancelation_deadline = time();
        }
        $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $cancelation_deadline);
        $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        $item['cancelpolicy_details'] = $cancelation_details;
        // EOF Policies
        // EOF Check prices & availability
        $total = $total + $value['total'];
        $item['room'] = $value['room'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $item['subtotal'] = $filter->filter(floatval($tot));
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
try {
    $hotel = array();
    $sql = "select sid from xmlhotels_mrestel where sid=" . $shid . " and hid=" . $hid;
    $statement = $db->query($sql);
    $row_mrestel = $statement->execute();
    $row_mrestel->buffer();
    if (! $row_mrestel->valid()) {
        $response['error'] = "Unable to handle request #6";
        return false;
    }
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
try {
    $sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
    $statement = $db->query($sql);
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
    $statement = $db->query($sql);
    try {
        $row_country = $statement->execute();
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
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->query($sql);
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
$response['breakdown'] = $roombreakdown2;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
// Store Session
$sql = new Sql($db);
$sql = "delete from quote_session_hotel_multipolicies where session_id='" . $session_id . "' and sindex=$sindex";
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('quote_session_hotel_multipolicies');
$insert->values(array(
    'session_id' => $session_id,
    'sindex' => $sindex,
    'data' => base64_encode(serialize($response)),
    'searchsettings' => base64_encode(serialize($searchsettings))
), $insert::VALUES_MERGE);
try {
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['breakdown'] = $roombreakdown;
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>