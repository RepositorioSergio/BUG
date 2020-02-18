<?php
$scurrency = strtoupper($currency);
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$safe_agent_markup = $agent_markup;
if (intval($affiliates_settings['markup_restel']) != 0) {
    $agent_markup = (int) $affiliates_settings['markup_restel'];
}
if (intval($agents_settings['markup_restel']) != 0) {
    $agent_markup = (int) $agents_settings['markup_restel'];
}
if ($hasChildren == false) {
    $translator = new Translator();
    $filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
    unset($tmp);
    $sfilter = array();
    $restel = false;
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
    $sql = "select value from settings where name='RestelHotUSATimeout' and affiliate_id=$affiliate_id_restel" . $branch_filter;
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSATimeout = (int) $row_settings["value"];
    } else {
        $RestelHotUSATimeout = 0;
    }
    $sql = "select value from settings where name='RestelHotUSACustomerID' and affiliate_id=$affiliate_id_restel" . $branch_filter;
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RestelHotUSACustomerID = $row_settings["value"];
    }
    if ($nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . $nationality;
        $statement = $db->query($sql);
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        } else {
            $sourceMarket = "";
        }
    } else {
        $sourceMarket = "";
    }
    $sql = "select city_xml39 from cities where id=" . $destination;
    $statement = $db->query($sql);
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $city_xml39 = $row_settings["city_xml39"];
        if ($city_xml39 != "") {
            $city_xml39 = explode(":", $city_xml39);
            $pais = $city_xml39[0];
            $provincia = $city_xml39[1];
        } else {
            $pais = "";
            $provincia = "";
        }
    } else {
        $pais = "";
        $provincia = "";
    }
    $startTime = microtime();
    if ($pais != "" and $provincia != "" and count($selectedAdults) <= 2) {
        $translator = new Translator();
        if (file_exists("src/App/language/" . $lang . ".mo")) {
            $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
        }
        $xmlrequest = 'xml=<?xml version="1.0" encoding="UTF-8"?><peticion><tipo>110</tipo><nombre>Availability</nombre><agencia>BUG</agencia><parametros><pais>' . $pais . '</pais><provincia>' . $provincia . '</provincia><pais_cliente>' . $sourceMarket . '</pais_cliente><radio>9</radio><fechaentrada>' . strftime("%m/%d/%Y", $from) . '</fechaentrada><fechasalida>' . strftime("%m/%d/%Y", $to) . '</fechasalida><afiliacion>' . $RestelAffiliate . '</afiliacion><usuario>' . $RestelHotUSACustomerID . '</usuario><numhab1>1</numhab1><paxes1>' . (int) $selectedAdults[0] . '-' . (int) $selectedChildren[0] . '</paxes1>';
        if ((int) $selectedChildren[0] > 0) {
            $xmlrequest .= '<edades1>';
            for ($z = 0; $z < (int) $selectedChildren[0]; $z ++) {
                if ($z > 0) {
                    $xmlrequest .= ',';
                }
                $xmlrequest .= $selectedChildrenAges[0][$z];
            }
            $xmlrequest .= '</edades1>';
        } else {
            $xmlrequest .= '<edades1></edades1>';
        }
        if ((int) $selectedAdults[1] > 0) {
            $xmlrequest .= '<numhab2>1</numhab2>';
        } else {
            $xmlrequest .= '<numhab2>0</numhab2>';
        }
        $xmlrequest .= '<paxes2>' . (int) $selectedAdults[1] . '-' . (int) $selectedChildren[1] . '</paxes2>';
        if ((int) $selectedChildren[1] > 0) {
            $xmlrequest .= '<edades2>';
            for ($z = 0; $z < (int) $selectedChildren[1]; $z ++) {
                if ($z > 0) {
                    $xmlrequest .= ',';
                }
                $xmlrequest .= $selectedChildrenAges[1][$z];
            }
            $xmlrequest .= '</edades2>';
        } else {
            $xmlrequest .= '<edades2></edades2>';
        }
        if ((int) $selectedAdults[2] > 0) {
            $xmlrequest .= '<numhab3>1</numhab3>';
        } else {
            $xmlrequest .= '<numhab3>0</numhab3>';
        }
        $xmlrequest .= '<paxes3>' . (int) $selectedAdults[2] . '-' . (int) $selectedChildren[2] . '</paxes3>';
        if ((int) $selectedChildren[2] > 0) {
            $xmlrequest .= '<edades3>';
            for ($z = 0; $z < (int) $selectedChildren[2]; $z ++) {
                if ($z > 0) {
                    $xmlrequest .= ',';
                }
                $xmlrequest .= $selectedChildrenAges[2][$z];
            }
            $xmlrequest .= '</edades3>';
        } else {
            $xmlrequest .= '<edades3></edades3>';
        }
        $xmlrequest .= '<idioma>2</idioma><duplicidad>1</duplicidad><comprimido>2</comprimido><informacion_hotel>0</informacion_hotel></parametros></peticion>';
        if ($RestelHotUSATimeout == 0) {
            $RestelHotUSATimeout = 120;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_URL, $RestelHotUSAServiceURL . "listen_xml.jsp?codigousu=" . $RestelHotUSAUsername . "&clausu=" . $RestelHotUSApassword . "&afiliacio=" . $RestelAffiliate . "&secacc=" . $RestelHotUSAAccessCode);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $RestelHotUSATimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $RestelHotUSATimeout);
        $response = curl_exec($ch);
        curl_close($ch);
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_restel');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $xmlrequest,
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
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $error = $inputDoc->getElementsByTagName("error");
        if ($error->length > 0) {
            error_log("\r\n" . trim($error->item(0)->nodeValue) . "\r\n", 3, "/srv/www/htdocs/error_log");
        }
        $node = $inputDoc->getElementsByTagName("hot");
        $nodeLength = $node->length;
        for ($xHotel = 0; $xHotel < $nodeLength; $xHotel ++) {
            $shid = $node->item($xHotel)->getElementsByTagName("cod");
            if ($shid->length > 0) {
                $shid = $shid->item(0)->nodeValue;
                $city_tax = $node->item($xHotel)->getElementsByTagName("city_tax");
                if ($city_tax->length > 0) {
                    $city_tax = $city_tax->item(0)->nodeValue;
                } else {
                    $city_tax = "";
                }
                $res = $node->item($xHotel)->getElementsByTagName("res");
                if ($res->length > 0) {
                    $sfilter[] = " sid='$shid' ";
                    $pax = $res->item(0)->getElementsByTagName("pax");
                    $length = $pax->length;
                    for ($zRooms = 0; $zRooms < $length; $zRooms ++) {
                        $code = $pax->item(0)->getAttribute("cod");
                        $hab = $pax->item($zRooms)->getElementsByTagName("hab");
                        $hablength = $hab->length;
                        for ($baseCounterDetails = 0; $baseCounterDetails < $hablength; $baseCounterDetails ++) {
                            $cod = $hab->item($baseCounterDetails)->getAttribute("cod");
                            $desc = ucwords(strtolower($hab->item($baseCounterDetails)->getAttribute("desc")), "/+ ");
                            $reg = $hab->item($baseCounterDetails)->getElementsByTagName("reg");
                            $reglength = $reg->length;
                            for ($xReg = 0; $xReg < $reglength; $xReg ++) {
                                $regcod = $reg->item($xReg)->getAttribute("cod");
                                $regprr = $reg->item($xReg)->getAttribute("prr");
                                $regdiv = $reg->item($xReg)->getAttribute("div");
                                $regesr = $reg->item($xReg)->getAttribute("esr");
                                $regnr = $reg->item($xReg)->getAttribute("nr");
                                $slin = array();
                                $lin = $reg->item($xReg)->getElementsByTagName("lin");
                                $linlength = $lin->length;
                                for ($xLin = 0; $xLin < $linlength; $xLin ++) {
                                    array_push($slin, $lin->item($xLin)->nodeValue);
                                }
                                $total = $regprr;
                                // Markup
                                if ($RestelHotUSAMarkup > 0) {
                                    $total = $total + (($total * $RestelHotUSAMarkup) / 100);
                                }
                                if ($internalmarkup > 0) {
                                    $total = $total + (($total * $internalmarkup) / 100);
                                }
                                if ($agent_markup > 0) {
                                    $total = $total + (($total * $agent_markup) / 100);
                                }
                                // Fallback Markup
                                if ($RestelHotUSAMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                    $total = $total + (($total * $HotelsMarkupFallback) / 100);
                                }
                                // Agent discount
                                if ($agent_discount > 0) {
                                    $total = $total - (($total * $agent_discount) / 100);
                                }
                                if ($scurrency != "" and $base_currency != $scurrency) {
                                    $total = $CurrencyConverter->convert($total, $base_currency, $scurrency);
                                }
                                $tmp[$shid]['recommended'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['city_tax'] = $city_tax;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['paxhab'] = $code;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hab'] = $cod;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['regcod'] = $regcod;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-39";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = "";
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $desc;
                                if ($regnr == 1) {
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                                } else {
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                                }
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $base_currency;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $regprr;
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['lin'] = serialize($slin);
                                if ($regesr == "OK") {
                                    // OK = Available
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                } else {
                                    // PT = Availability must be confirmed by phone
                                    // RT = Restrictions apply
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 0;
                                }
                                try {
                                    $sql = "select mapped from board_mapping where description='" . $regcod . "'";
                                    $statement = $db->query($sql);
                                    $row_board_mapping = $statement->execute();
                                    $row_board_mapping->buffer();
                                    if ($row_board_mapping->valid()) {
                                        $row_board_mapping = $row_board_mapping->current();
                                        $regcod = $row_board_mapping["mapped"];
                                    }
                                } catch (Exception $e) {
                                    $logger = new Logger();
                                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                    $logger->addWriter($writer);
                                    $logger->info($e->getMessage());
                                }
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($regcod);
                                $priceBreakdown = array();
                                $priceBreakdownCount = 0;
                                for ($rDays = 0; $rDays < $nights; $rDays ++) {
                                    $rDayAux = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rDays, date("Y", $from)));
                                    $priceBreakdown[$priceBreakdownCount]['price'] = $filter->filter($total / $nights, 2);
                                    $priceBreakdown[$priceBreakdownCount]['priceplain'] = $total / $nights;
                                    $priceBreakdown[$priceBreakdownCount]['date'] = $rDayAux;
                                    $priceBreakdownCount ++;
                                }
                                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $priceBreakdown;
                                // EOF Checked
                            }
                        }
                    }
                    $restel = true;
                }
            }
        }
    }
    if ($restel == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_mrestel where " . $sfilter;
            $statement = $db->query($sql);
            $result = $statement->execute();
            $result->buffer();
            if ($result instanceof ResultInterface && $result->isQueryResult()) {
                $resultSet = new ResultSet();
                $resultSet->initialize($result);
                foreach ($resultSet as $row) {
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
            $supplier = 39;
            // Store Session
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_restel');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_restel');
                $insert->values(array(
                    'session_id' => $session_id,
                    'xmlrequest' => (string) $xmlrequest,
                    'xmlresult' => (string) $response,
                    'data' => base64_encode(serialize($hotels_array)),
                    'searchsettings' => base64_encode(serialize($requestdata))
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
        }
    }
}
$agent_markup = $safe_agent_markup;
?>