<?php
error_log("\r\nStart Sabre - Search Cruises\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat);
$db = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='cruisessabreuserID1'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabreuserID1 = $row_settings['value'];
}
$sql = "select value from settings where name='cruisessabreuserID2'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabreuserID2 = $row_settings['value'];
}
$sql = "select value from settings where name='cruisessabrepassword1'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabrepassword1 = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisessabrepassword2'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabrepassword2 = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisessabreIPCC'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabreIPCC = $row_settings['value'];
}
$sql = "select value from settings where name='enablecruisessabre'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $enablecruisessabre = $row_settings['value'];
}
$sql = "select value from settings where name='cruisessabrewebservicesURL'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabrewebservicesURL = $row['value'];
}
$sql = "select value from settings where name='cruisessabremarkup'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabremarkup = (double) $row_settings['value'];
} else {
    $cruisessabremarkup = 0;
}
$sql = "select value from settings where name='cruisessabreb2cmarkup'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreb2cmarkup = $row['value'];
}
$sql = "select value from settings where name='cruisessabrePartyIDFrom'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabrePartyIDFrom = $row['value'];
}
$sql = "select value from settings where name='cruisessabrePartyIDTo'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabrePartyIDTo = $row['value'];
}
$sql = "select value from settings where name='cruisessabreaffiliates_id'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='cruisessabreParallelSearch'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreParallelSearch = $row['value'];
}
$sql = "select value from settings where name='cruisessabreClientID'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreClientID = $row['value'];
}
$sql = "select value from settings where name='cruisessabreClientSecret'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreClientSecret = $row['value'];
}
$sql = "select value from settings where name='cruisessabreConversationID'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreConversationID = $row['value'];
}
$sql = "select value from settings where name='cruisessabreCredentials'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreCredentials = $row['value'];
}
$sql = "select value from settings where name='cruisessabreIPCC'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreIPCC = $row['value'];
}
$sql = "select value from settings where name='cruisessabreTimeout'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreTimeout = (int) $row['value'];
}
$sql = "select cruises_xml08 from cruises_regions where seo='" . $destination . "'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisedestinationid = $row_settings["cruises_xml08"];
}
if ($cruiseline != "all") {
    $sql = "select cruises_xml08 from cruises_lines where seo='" . $cruiseline . "'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $CruiseLineID = $row_settings["cruises_xml08"];
    } else {
        $CruiseLineID = 0;
    }
} else {
    $CruiseLineID = 0;
}
if ($from == "all") {
    $d = new DateTime(date("Y") . '-' . date("m") . '-' . date("d"));
    $departureFrom = $d->format("Y-m-d");
    $d = new DateTime(date("Y") + 1 . '-' . date("m") . '-' . date("d"));
    $departureTo = $d->format("Y-m-d");
} else {
    $fromTmp = explode("-", $from);
    $d = new DateTime($fromTmp[1] . '-' . $fromTmp[0] . '-01');
    $departureFrom = $d->format("Y-m-d");
    $departureTo = $d->format("Y-m-t");
}
if ($length == "all") {
    $MinCruiseLength = 1;
    $MaxCruiseLength = 9999;
} else {
    $length = explode("-", $length);
    $MinCruiseLength = (int) $length[0];
    $MaxCruiseLength = (int) $length[1];
    if ($MaxCruiseLength == 1) {
        $MaxCruiseLength = 9999;
    }
}
if ($cruiseship != "" and $cruiseship != "all") {
    $sql = "select cruises_xml08 from ships where seo='" . $cruiseship . "'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ShipID = $row_settings["cruises_xml08"];
    } else {
        $ShipID = 0;
    }
} else {
    $ShipID = 0;
}
if ($departureport != "" and $departureport != "all") {
    $sql = "select cruises_xml08 from cruises_ports where seo='" . $departureport . "'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PortID = $row_settings["cruises_xml08"];
    } else {
        $PortID = 0;
    }
} else {
    $PortID = 0;
}
if ($cruisedestinationid > 0) {
    $secret = base64_encode($cruisessabreClientSecret);
    $cred = base64_encode(base64_encode($cruisessabreClientID) . ":" . $secret);

    $ch = curl_init($cruisessabrewebservicesURL . "v2/auth/token");
    $vars = "grant_type=client_credentials";
    $headers = array(
        'Authorization: Basic ' . $cred,
        'Accept: */*',
        'Content-Type: application/x-www-form-urlencoded'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result, true);
    $access_token = $result['access_token'];
    $raw = '{
        "agencyPOS": {
            "pcc": "' . $cruisessabreIPCC . '",
            "currencyCode": "USD"
        },
        "sailingQualifier": {
            "startDate": "' . $departureFrom . '",
            "endDate": "' . $departureTo . '",
            "minDuration": "' . $MinCruiseLength . '",
            "maxDuration": "' . $MaxCruiseLength . '",
            "agencyGroupInd": true,
            "sailingMediaFlag": true,
            "itineraryInfoFlag": true
        }
    }';
    $headers = array(
        "Accept: application/json",
        "Content-Type: application/json",
        "Accept-Encoding: gzip",
        "Authorization: Bearer " . $access_token,
        "Content-length: " . strlen($raw)
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cruisessabrewebservicesURL . "v1/cruise/offers/searchSailings");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisessabreTimeout);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    //error_log("\r\nSabre Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_sabre');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCruises.php',
            'errorline' => 0,
            'errormessage' => $raw,
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
    
    $sailingInfo = $response['sailingInfo'];
    $totalEntries = $sailingInfo['totalEntries'];
    $totalPages = $sailingInfo['totalPages'];
    $sailOptions = $sailingInfo['sailOptions'];
    if (count($sailOptions) > 0) {
        for ($i = 0; $i < count($sailOptions); $i ++) {
            $vendorCode = $sailOptions[$i]['vendorCode'];
            $voyageId = $sailOptions[$i]['voyageId'];
            $shipCode = $sailOptions[$i]['shipCode'];
            $departureDate = $sailOptions[$i]['departureDate'];
            $arrivalDate = $sailOptions[$i]['arrivalDate'];
            $duration = $sailOptions[$i]['duration'];
            $cruiseTour = $sailOptions[$i]['cruiseTour'];
            $currencyCode = $sailOptions[$i]['currencyCode'];
            $embarkationPort = $sailOptions[$i]['embarkationPort'];
            $disembarkationPort = $sailOptions[$i]['disembarkationPort'];
            $portsOfCallQty = $sailOptions[$i]['portsOfCallQty'];
            $sailingDesc = $sailOptions[$i]['sailingDesc'];
            $shipImage = $sailOptions[$i]['shipImage'];
            $themeCode = $sailOptions[$i]['themeCode'];
            $status = $sailOptions[$i]['status'];
            $itineraryInfo = $sailOptions[$i]['itineraryInfo'];
            $itineraryImage = $itineraryInfo['itineraryImage'];
            $sql = "select name, logo, seo from cruises_lines where cruises_xml08='" . $vendorCode . "'";
            //error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
            $statement = $db->createStatement($sql);
            try {
                $statement->prepare();
                $row = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $row->buffer();
            if ($row->valid()) {
                $row = $row->current();
                $cruiseline_name = $row["name"];
                $cruiseline_logo = "https://world-wide-web-servers.com/cr/" . $row["logo"];
                $cruiseline_seo = $row["seo"];
            } else {
                // Unable to find cruise line $cruiselineid
                $cruiseline_name = $sailingDesc;
                $cruiseline_logo = "";
                $cruiseline_seo = "";
            }
            $sql = "select id, name, seo, shiprating from ships where cruises_xml08='" . $shipCode . "'";
            //error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
            $statement = $db->createStatement($sql);
            try {
                $statement->prepare();
                $row = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $row->buffer();
            if ($row->valid()) {
                $row = $row->current();
                $shipname = $row["name"];
                $ship_seo = $row["seo"];
                $ship_id = $row["id"];
                $ship_rating = $row["shiprating"];
                // Ships Images
                $images = array();
                $adaptor = new Sql($db);
                $select = $adaptor->select();
                $select->from("ships_images");
                $select->where('ship_id=' . $row['id']);
                $select->columns(array(
                    'url',
                    'thumbnail'
                ));
                $select->order('sortorder');
                $statement3 = $adaptor->prepareStatementForSqlObject($select);
                $results3 = $statement3->execute();
                $results3->buffer();
                if ($results3 instanceof ResultInterface && $results3->isQueryResult()) {
                    $resultSet3 = new ResultSet();
                    $resultSet3->initialize($results3);
                    foreach ($resultSet3 as $row3) {
                        array_push($images, $row3);
                    }
                }
                $cruises[$counter]["images"] = $images;
            } else {
                // Unable to find ship $shipid
                $shipname = "";
                $cruises[$counter]["images"][0] = "";
                $ship_id = 0;
                $ship_rating = 0;
                $ship_seo = "";
            }
            $cruises[$counter]["id"] = $counter;
            $cruises[$counter]["seo"] = $ship_seo;
            // TODO
            // error_log("\r\nUnable to find cruise line TODO - Check - $cruiselineid - alterar para id, db cruises_lines \r\n", 3, "/srv/www/htdocs/error_log");
            $cruises[$counter]["cruise_line_id"] = $voyageId;
            $cruises[$counter]["quote_id"] = md5(uniqid($session_id, true)) . "-8-" . $counter;
            $cruises[$counter]["ship"]["id"] = $ship_id;
            $cruises[$counter]["ship"]["seo"] = $ship_seo;
            $cruises[$counter]['ship']["name"] = utf8_encode(htmlentities($shipname, ENT_QUOTES));
            $cruises[$counter]["ship"]["rating"] = $ship_rating;
            $cruises[$counter]["cruiseline"]["logo"] = $cruiseline_logo;
            $cruises[$counter]["cruiseline"]["name"] = utf8_encode(htmlentities($cruiseline_name, ENT_QUOTES));
            $cruises[$counter]["cruiseline"]["seo"] = $cruiseline_seo;
            $cruisesfrom = 0;
            $cruisesfrom_publish = 0;
            //
            // Itinerary
            //
            // B2C Price
            $IN_PricePublish = 1100; // Displays the Inside cabin publish price.
            $ST_PricePublish = 1150; // Displays the suite cabin publish price.
            $BL_PricePublish = 1200; // Displays the balcony cabin publish price.
            $OV_PricePublish = 1250; // Displays the ocean view cabin publish price.
                                  // B2B Price
            $IN_Price = 1100; // Displays the Inside cabin price.
            $ST_Price = 1150; // Displays the suite cabin price.
            $BL_Price = 1200; // Displays the balcony cabin price.
            $OV_Price = 1250; // Displays the ocean view cabin publish price.
            if ($cruisestouricoholidaysMarkup > 0) {
                if ((int) $IN_Price > 0) {
                    $IN_Price = number_format($IN_Price + (($IN_Price * $cruisestouricoholidaysMarkup) / 100), 2, '.', '');
                }
                if ((int) $ST_Price > 0) {
                    $ST_Price = number_format($ST_Price + (($ST_Price * $cruisestouricoholidaysMarkup) / 100), 2, '.', '');
                }
                if ((int) $BL_Price > 0) {
                    $BL_Price = number_format($BL_Price + (($BL_Price * $cruisestouricoholidaysMarkup) / 100), 2, '.', '');
                }
                if ((int) $OV_Price > 0) {
                    $OV_Price = number_format($OV_Price + (($OV_Price * $cruisestouricoholidaysMarkup) / 100), 2, '.', '');
                }
            }
            if ($agent_markup > 0) {
                if ((int) $IN_Price > 0) {
                    $IN_Price = number_format($IN_Price + (($IN_Price * $agent_markup) / 100), 2, '.', '');
                }
                if ((int) $ST_Price > 0) {
                    $ST_Price = number_format($ST_Price + (($ST_Price * $agent_markup) / 100), 2, '.', '');
                }
                if ((int) $BL_Price > 0) {
                    $BL_Price = number_format($BL_Price + (($BL_Price * $agent_markup) / 100), 2, '.', '');
                }
                if ((int) $OV_Price > 0) {
                    $OV_Price = number_format($OV_Price + (($OV_Price * $agent_markup) / 100), 2, '.', '');
                }
            }
            if ($cruisesfrom == 0) {
                $CheapestPricePublish = 0;
                if ($IN_PricePublish > 0) {
                    $CheapestPricePublish = $IN_PricePublish;
                }
                if ($ST_PricePublish > 0) {
                    if ($CheapestPricePublish > $ST_PricePublish or $CheapestPricePublish == 0) {
                        $CheapestPricePublish = $ST_PricePublish;
                    }
                }
                if ($BL_PricePublish > 0) {
                    if ($CheapestPricePublish > $BL_PricePublish or $CheapestPricePublish == 0) {
                        $CheapestPricePublish = $BL_PricePublish;
                    }
                }
                if ($OV_PricePublish > 0) {
                    if ($CheapestPricePublish > $OV_PricePublish or $CheapestPricePublish == 0) {
                        $CheapestPricePublish = $OV_PricePublish;
                    }
                }
                $cruisesfrom_publish = $CheapestPricePublish;
                $CheapestPrice = 0;
                if ($IN_Price > 0) {
                    $CheapestPrice = $IN_Price;
                }
                if ($ST_Price > 0) {
                    if ($CheapestPrice > $ST_Price or $CheapestPrice == 0) {
                        $CheapestPrice = $ST_Price;
                    }
                }
                if ($BL_Price > 0) {
                    if ($CheapestPrice > $BL_Price or $CheapestPrice == 0) {
                        $CheapestPrice = $BL_Price;
                    }
                }
                if ($OV_Price > 0) {
                    if ($CheapestPrice > $OV_Price or $CheapestPrice == 0) {
                        $CheapestPrice = $OV_Price;
                    }
                }
                $cruisesfrom = $CheapestPrice;
            } else {
                if ($IN_PricePublish > 0) {
                    if ($IN_PricePublish < $cruisesfrom) {
                        $cruisesfrom_publish = $IN_PricePublish;
                    }
                }
                if ($ST_PricePublish > 0) {
                    if ($ST_PricePublish < $cruisesfrom) {
                        $cruisesfrom_publish = $ST_PricePublish;
                    }
                }
                if ($BL_PricePublish > 0) {
                    if ($BL_PricePublish < $cruisesfrom) {
                        $cruisesfrom_publish = $BL_PricePublish;
                    }
                }
                if ($OV_PricePublish > 0) {
                    if ($OV_PricePublish < $cruisesfrom_publish) {
                        $cruisesfrom_publish = $OV_PricePublish;
                    }
                }
                if ($IN_Price > 0) {
                    if ($IN_Price < $cruisesfrom) {
                        $cruisesfrom = $IN_Price;
                    }
                }
                if ($ST_Price > 0) {
                    if ($ST_Price < $cruisesfrom) {
                        $cruisesfrom = $ST_Price;
                    }
                }
                if ($BL_Price > 0) {
                    if ($BL_Price < $cruisesfrom) {
                        $cruisesfrom = $BL_Price;
                    }
                }
                if ($OV_Price > 0) {
                    if ($OV_Price < $cruisesfrom) {
                        $cruisesfrom = $OV_Price;
                    }
                }
            }
            $departure = explode("-", $departureDate);
            $arrival = explode("-", $arrivalDate);
            $cruises[$counter]['product_id'][$i] = md5(uniqid($session_id, true)) . "-" . $counter . "-" . $i;
            $cruises[$counter]['sailingid'][$i] = $voyageId;
            $cruises[$counter]['departure'][$i] = mktime(0, 0, 0, $departure[1], $departure[2], $departure[0]);
            $cruises[$counter]['arrival'][$i] = mktime(0, 0, 0, $arrival[1], $arrival[2], $arrival[0]);
            if ($IN_Price == 0 or $IN_Price == - 1) {
                if ($IN_Price > 0) {
                    if ($currencyCode != $scurrency) {
                        $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currencyCode, $scurrency);
                        $IN_Price = $CurrencyConverter->convert($IN_Price, $currencyCode, $scurrency);
                    }
                }
                $cruises[$counter]['IN_PricePublish'][$i] = $translator->translate("N/A");
                $cruises[$counter]["IN_PricePublish_plain"][$i] = 0;
                $cruises[$counter]['insidecabin'][$i] = $translator->translate("N/A");
                $cruises[$counter]["insidecabin_plain"][$i] = 0;
                $cruises[$counter]["IN_Price_plain"][$i] = 0;
            } else {
                if ($IN_Price > 0) {
                    if ($currencyCode != $scurrency) {
                        $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currencyCode, $scurrency);
                        $IN_Price = $CurrencyConverter->convert($IN_Price, $currencyCode, $scurrency);
                    }
                }
                $cruises[$counter]['IN_PricePublish'][$i] = $filter->filter($IN_PricePublish);
                $cruises[$counter]["IN_PricePublish_plain"][$i] = $IN_PricePublish;
                $cruises[$counter]['insidecabin'][$i] = $filter->filter($IN_Price);
                $cruises[$counter]['insidecabin_plain'][$i] = $IN_Price;
                $cruises[$counter]["IN_Price_plain"][$i] = $IN_Price;
            }
            if ($OV_Price == 0 or $OV_Price == - 1) {
                if ($OV_Price > 0) {
                    if ($currencyCode != $scurrency) {
                        $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currencyCode, $scurrency);
                        $OV_Price = $CurrencyConverter->convert($OV_Price, $currencyCode, $scurrency);
                    }
                }
                $cruises[$counter]['OV_PricePublish'][$i] = $translator->translate("N/A");
                $cruises[$counter]["OV_PricePublish_plain"][$i] = $OV_PricePublish;
                $cruises[$counter]['oceanview'][$i] = $translator->translate("N/A");
                $cruises[$counter]["oceanview_plain"][$i] = 0;
                $cruises[$counter]["OV_Price_plain"][$i] = 0;
            } else {
                if ($OV_Price > 0) {
                    if ($currencyCode != $scurrency) {
                        $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currencyCode, $scurrency);
                        $OV_Price = $CurrencyConverter->convert($OV_Price, $currencyCode, $scurrency);
                    }
                }
                $cruises[$counter]['OV_PricePublish'][$i] = $filter->filter($OV_PricePublish);
                $cruises[$counter]["OV_PricePublish_plain"][$i] = $OV_PricePublish;
                $cruises[$counter]['oceanview'][$i] = $filter->filter($OV_Price);
                $cruises[$counter]["oceanview_plain"][$i] = $OV_Price;
                $cruises[$counter]["OV_Price_plain"][$i] = $OV_Price;
            }
            if ($BL_Price == 0 or $BL_Price == - 1) {
                if ($BL_Price > 0) {
                    if ($currencyCode != $scurrency) {
                        $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currencyCode, $scurrency);
                        $BL_Price = $CurrencyConverter->convert($BL_Price, $currencyCode, $scurrency);
                    }
                }
                $cruises[$counter]['BL_PricePublish'][$i] = $translator->translate("N/A");
                $cruises[$counter]["BL_PricePublish_plain"][$i] = 0;
                $cruises[$counter]['balcony'][$i] = $translator->translate("N/A");
                $cruises[$counter]["balcony_plain"][$i] = 0;
                $cruises[$counter]["BL_Price_plain"][$i] = 0;
            } else {
                if ($BL_Price > 0) {
                    if ($currencyCode != $scurrency) {
                        $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currencyCode, $scurrency);
                        $BL_Price = $CurrencyConverter->convert($BL_Price, $currencyCode, $scurrency);
                    }
                }
                $cruises[$counter]['BL_PricePublish'][$i] = $filter->filter($BL_PricePublish);
                $cruises[$counter]["BL_PricePublish_plain"][$i] = $BL_PricePublish;
                $cruises[$counter]['balcony'][$i] = $filter->filter($BL_Price);
                $cruises[$counter]["balcony_plain"][$i] = $BL_Price;
                $cruises[$counter]["BL_Price_plain"][$i] = $BL_Price;
            }
            if ($ST_Price == 0 or $ST_Price == - 1) {
                if ($ST_Price > 0) {
                    if ($currencyCode != $scurrency) {
                        $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currencyCode, $scurrency);
                        $ST_Price = $CurrencyConverter->convert($ST_Price, $currencyCode, $scurrency);
                    }
                }
                $cruises[$counter]['ST_PricePublish'][$i] = $translator->translate("N/A");
                $cruises[$counter]["ST_PricePublish_plain"][$i] = 0;
                $cruises[$counter]['suite'][$i] = $translator->translate("N/A");
                $cruises[$counter]['suite_plain'][$i] = 0;
                $cruises[$counter]["ST_Price_plain"][$i] = 0;
            } else {
                if ($ST_Price > 0) {
                    $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currencyCode, $scurrency);
                    $ST_Price = $CurrencyConverter->convert($ST_Price, $currencyCode, $scurrency);
                }
                $cruises[$counter]['ST_PricePublish'][$i] = $filter->filter($ST_PricePublish);
                $cruises[$counter]["ST_PricePublish_plain"][$i] = $ST_PricePublish;
                $cruises[$counter]['suite'][$i] = $filter->filter($ST_Price);
                $cruises[$counter]['suite_plain'][$i] = $ST_Price;
                $cruises[$counter]["ST_Price_plain"][$i] = $ST_Price;
            }
            if ($IN_Price > 0) {
                if ($currencyCode != $scurrency) {
                    $cruisesfrom = $CurrencyConverter->convert($cruisesfrom, $currencyCode, $scurrency);
                    $cruisesfrom_publish = $CurrencyConverter->convert($cruisesfrom_publish, $currencyCode, $scurrency);
                }
            }
            $cruises[$counter]['from'] = $filter->filter($cruisesfrom);
            $cruises[$counter]["from_plain"] = $cruisesfrom;
            $cruises[$counter]['from_publish'] = $filter->filter($cruisesfrom_publish);
            $cruises[$counter]["from_plain_publish"] = $cruisesfrom_publish;
            $cruises[$counter]['name'] = $sailingDesc;
            $cruises[$counter]['currency'] = $currencyCode;
            $cruises[$counter]['length'] = $duration;
            $cruises[$counter]['cruise_destination_id'] = $shipCode;
            $cruises[$counter]['ItineraryId'] = $ItineraryId;
            $cruises[$counter]['ShipRating'] = $ShipRating;
            $cruises[$counter]['departure']['portid'] = $embarkationPort;
            $cruises[$counter]['departure']['portname'] = $embarkationPort;
            $cruises[$counter]["vendorCode"] = $vendorCode;
            $cruises[$counter]["citycode"] = $embarkationPort;
            $cruises[$counter]["token"] = $access_token;
            // Amenities
            $amenities = array();
            $tmp = array();
            $sql = "select distinct(name), ico from ships_amenities where ship_id=" . $ship_id;
            $statement2 = $db->createStatement($sql);
            try {
                $statement2->prepare();
                $result2 = $statement2->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $result2->buffer();
            if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
                $resultSet = new ResultSet();
                $resultSet->initialize($result2);
                foreach ($resultSet as $row) {
                    $tmp['name'] = $row->name;
                    $tmp['ico'] = $row->ico;
                    array_push($amenities, $tmp);
                }
            }
            $cruises[$counter]['amenities'] = $amenities;
            // Decks
            $decks = array();
            $tmp = array();
            $sql = "select deck_number, name, image from ships_decks where ship_id=" . $ship_id . " order by deck_number desc";
            $statement2 = $db->createStatement($sql);
            $statement2->prepare();
            $result2 = $statement2->execute();
            $result2->buffer();
            if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
                $resultSet = new ResultSet();
                $resultSet->initialize($result2);
                foreach ($resultSet as $row) {
                    $tmp['name'] = $row->name;
                    $tmp['image'] = $row->image;
                    $tmp['deck_number'] = $row->deck_number;
                    array_push($decks, $tmp);
                }
            }
            $cruises[$counter]['decks'] = $decks;
            // Unique Decks
            $unique_decks = array();
            $tmp = array();
            $sql = "select decknumber from ships_publicareas where ship_id=" . $ship_id . " group by decknumber order by decknumber desc";
            $statement2 = $db->createStatement($sql);
            try {
                $statement2->prepare();
                $result2 = $statement2->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $result2->buffer();
            if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
                $resultSet = new ResultSet();
                $resultSet->initialize($result2);
                foreach ($resultSet as $row) {
                    $tmp['deck_number'] = $row->decknumber;
                    array_push($unique_decks, $tmp);
                }
            }
            $cruises[$counter]['unique_decks'] = $unique_decks;
            // Public Areas
            $publicareas = array();
            $tmp = array();
            $sql = "select decknumber, name, image from ships_publicareas where ship_id=" . $ship_id . " order by decknumber desc";
            $statement2 = $db->createStatement($sql);
            try {
                $statement2->prepare();
                $result2 = $statement2->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            $result2->buffer();
            if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
                $resultSet = new ResultSet();
                $resultSet->initialize($result2);
                foreach ($resultSet as $row) {
                    $tmp['deck_number'] = $row->decknumber;
                    $tmp['name'] = $row->name;
                    $tmp['image'] = $row->image;
                    array_push($unique_decks, $tmp);
                }
            }
            $cruises[$counter]['publicareas'] = $publicareas;
            $counter ++;
        }
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\nSabre EOF Search\r\n", 3, "/srv/www/htdocs/error_log");
?>