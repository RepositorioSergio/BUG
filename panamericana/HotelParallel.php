<?php
error_log("\r\n PANAMERICANA - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$hotellist = "";
$sql = "select sid from xmlhotels_mpanamericana where hid=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $result = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $hotellist .= '' . $row->sid . '';
    }
}
if ($hotellist != "") {
    $affiliate_id_panamericana = 0;
    if (! function_exists('xml2array')) {

        function xml2array($contents, $get_attributes = 1)
        {
            if (! $contents)
                return array();
            // if (! function_exists('xml_parser_create')) {
            // return array();
            // }
            $parser = xml_parser_create();
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
            xml_parse_into_struct($parser, $contents, $xml_values);
            xml_parser_free($parser);
            if (! $xml_values)
                return;
            // Initializations
            $xml_array = array();
            $parents = array();
            $opened_tags = array();
            $arr = array();
            $current = &$xml_array;
            foreach ($xml_values as $data) {
                unset($attributes, $value);
                extract($data);
                $result = '';
                if ($get_attributes) {
                    $result = array();
                    if (isset($value))
                        $result["value"] = $value;
                    if (isset($attributes)) {
                        foreach ($attributes as $attr => $val) {
                            if ($get_attributes == 1)
                                $result["attr"][$attr] = $val;
                        }
                    }
                } elseif (isset($value)) {
                    $result = $value;
                }
                if ($type == "open") {
                    $parent[$level - 1] = &$current;
                    if (! is_array($current) or (! in_array($tag, array_keys($current)))) {
                        $current[$tag] = $result;
                        $current = &$current[$tag];
                    } else {
                        if (isset($current[$tag][0])) {
                            array_push($current[$tag], $result);
                        } else {
                            $current[$tag] = array(
                                $current[$tag],
                                $result
                            );
                        }
                        $last = count($current[$tag]) - 1;
                        $current = &$current[$tag][$last];
                    }
                } elseif ($type == "complete") {
                    if (! isset($current[$tag])) {
                        $current[$tag] = $result;
                    } else {
                        if ((is_array($current[$tag]) and $get_attributes == 0) or (isset($current[$tag][0]) and is_array($current[$tag][0]) and $get_attributes == 1)) {
                            array_push($current[$tag], $result);
                        } else {
                            $current[$tag] = array(
                                $current[$tag],
                                $result
                            );
                        }
                    }
                } elseif ($type == 'close') {
                    $current = &$parent[$level - 1];
                }
            }
            return ($xml_array);
        }
    }
    
    $sql = "select value from settings where name='panamericanaServiceURL' and affiliate_id=$affiliate_id_panamericana";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $panamericanaServiceURL = $row_settings["value"];
    }
    $sql = "select value from settings where name='panamericanaID' and affiliate_id=$affiliate_id_panamericana";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $panamericanaID = $row_settings["value"];
    }
    $sql = "select value from settings where name='panamericanaPassword' and affiliate_id=$affiliate_id_panamericana";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $panamericanaPassword = base64_decode($row_settings["value"]);
    }
    $sql = "select value from settings where name='panamericanaMarkup' and affiliate_id=$affiliate_id_panamericana";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $panamericanaMarkup = (int) $row_settings["value"];
    }
    $sql = "select value from settings where name='panamericanaCurrencyCode' and affiliate_id=$affiliate_id_panamericana";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $panamericanaCurrencyCode = $row_settings["value"];
    } else {
        $panamericanaCurrencyCode = "";
    }
    $sql = "select value from settings where name='panamericanaTimeout' and affiliate_id=$affiliate_id_panamericana";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $panamericanaTimeout = (int) $row_settings["value"];
    } else {
        $panamericanaTimeout = 0;
    }
    $sql = "select name, country_id, zone_id, city_xml69, code from cities where id=" . $destination;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $country_selected = (int) $row_settings["country_id"];
        $state_selected = (int) $row_settings["zone_id"];
        $geo_location_name = $row_settings["name"];
        $DestinationZone = (int) $row_settings["city_xml69"];
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
            $sql = "select value from settings where name='panamericanaNationalityIsoCode2' and affiliate_id=$affiliate_id_panamericana";
            $statement = $db->createStatement($sql);
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
        $sql = "select value from settings where name='panamericanaNationalityIsoCode2' and affiliate_id=$affiliate_id_panamericana";
        $statement = $db->createStatement($sql);
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
    
    $xmlrequest = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema"><soap:Body><OTA_HotelAvailService xmlns="http://www.opentravel.org/OTA/2003/05"><OTA_HotelAvailRQ PrimaryLangID="en"><POS><Source AgentDutyCode="' . $panamericanaID . '"><RequestorID Type="1" MessagePassword="' . $panamericanaPassword . '"/></Source></POS><AvailRequestSegments><AvailRequestSegment><StayDateRange End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '" /><RoomStayCandidates>';
    $xmlrequest .= '<RoomStayCandidate Quantity="1"><GuestCounts><GuestCount Count="' . $adults . '" />';
    if ($children > 0) {
        for ($z = 0; $z < $children; $z ++) {
            $xmlrequest .= '<GuestCount Age="' . $children_ages[$z] . '" Count="1" />';
        }
    }
    $xmlrequest .= '</GuestCounts></RoomStayCandidate>';
    $xmlrequest .= '</RoomStayCandidates><HotelSearchCriteria><Criterion><HotelRef HotelCode="' . $hotellist . '" HotelCityCode="" /><TPA_Extensions><ShowBasicInfo>0</ShowBasicInfo><ShowCatalogueData>0</ShowCatalogueData><ShowNettPrice>1</ShowNettPrice><ShowOnlyAvailable>1</ShowOnlyAvailable><ShowPromotions>1</ShowPromotions><ShowCancellationPolicy>0</ShowCancellationPolicy><ShowDailyAvailabilityBreakdown>0</ShowDailyAvailabilityBreakdown>';
    if ($panamericanaCurrencyCode != "") {
        $xmlrequest .= '<ForceCurrency>USD</ForceCurrency>';
    }
    if ($sourceMarket != "") {
        $xmlrequest .= '<PaxCountry>' . $sourceMarket . '</PaxCountry>';
    }
    $xmlrequest .= '</TPA_Extensions></Criterion></HotelSearchCriteria></AvailRequestSegment></AvailRequestSegments></OTA_HotelAvailRQ></OTA_HotelAvailService></soap:Body></soap:Envelope>';
    error_log("\r\n RAW - $xmlrequest\r\n", 3, "/srv/www/htdocs/error_log");
    if ($panamericanaTimeout == 0) {
        $panamericanaTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: application/xml",
        "Content-type: text/xml",
        "Accept-Encoding: gzip, deflate",
        "Content-length: " . strlen($xmlrequest)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $panamericanaServiceURL . "OTA_HotelAvail.asmx");
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $panamericanaTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $panamericanaTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'panamericana';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>