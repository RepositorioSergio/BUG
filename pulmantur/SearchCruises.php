<?php
// Cruises PULLMANTUR
error_log("\r\n COMECOU PULLMANTUR  \r\n", 3, "/srv/www/htdocs/error_log");
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
$dbPullmantur = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='cruisespullmanturusername'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturusername = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturpassword'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturpassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='cruisespullmanturmarkup'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturmarkup = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturServiceURL'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturCurrency'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturCurrency = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturb2cmarkup'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturb2cmarkup = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturSearchSortorder'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturSearchSortorder = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturaffiliates_id'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturaffiliates_id = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturbranchs_id'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturbranchs_id = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturConnetionTimeout'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturConnetionTimeout = (int) $row_settings["value"];
}
$sql = "select cruises_xml08 from cruises_regions where seo='" . $destination . "'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisedestinationid = $row_settings["cruises_xml08"];
}
if ($cruiseline != "all") {
    $sql = "select cruises_xml08 from cruises_lines where seo='" . $cruiseline . "'";
    $statement = $dbPullmantur->createStatement($sql);
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
    $MinCruiseLength = 0;
    $MaxCruiseLength = 9999;
} else {
    $length = explode("-", $length);
    $MinCruiseLength = (int) $length[0];
    $MaxCruiseLength = (int) $length[1];
    if ($MaxCruiseLength == 0) {
        $MaxCruiseLength = 9999;
    }
}
if ($cruiseship != "" and $cruiseship != "all") {
    $sql = "select cruises_xml08 from ships where seo='" . $cruiseship . "'";
    $statement = $dbPullmantur->createStatement($sql);
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
    $statement = $dbPullmantur->createStatement($sql);
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

    $username = 'CONCTMM';
    $password = 'u73ecKBu73ecKB!';

    $url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/SailingList";
    $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sail="http://services.rccl.com/Interfaces/SailingList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
    <soapenv:Header/>
    <soapenv:Body>
    <sail:getSailingList>
        <alp:OTA_CruiseSailAvailRQ TimeStamp="2008-07-17T12:44:44.866-04:00" Target="Test" Version="1.0" SequenceNmbr="1" PrimaryLangID="en" RetransmissionIndicator="false" MoreIndicator="true" MaxResponses="50">
            <alp:POS>
                <!--1 to 10 repetitions:-->
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="313917" ID_Context="AGENCY1" Type="5"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                    </alp:BookingChannel>
                </alp:Source>
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="313917" ID_Context="AGENCY2" Type="5"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                    </alp:BookingChannel>
                </alp:Source>
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="313917" ID_Context="AGENT1" Type="5"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                    </alp:BookingChannel>
                </alp:Source>
            </alp:POS>
            <!--Optional:-->
            <alp:GuestCounts>
                <alp:GuestCount Age="30" Quantity="1"/>
                <alp:GuestCount Age="5" Quantity="1"/>
            </alp:GuestCounts>
            <alp:SailingDateRange Start="2020-09-08" End="2020-09-15" MinDuration="P6N" MaxDuration="P8N"/>
        </alp:OTA_CruiseSailAvailRQ>
    </sail:getSailingList>
    </soapenv:Body>
    </soapenv:Envelope>';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    $xmlresult = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    error_log("\r\n Response - $error \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($dbPullmantur);
        $insert = $sql->insert();
        $insert->into('log_pullmantur');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCruises.php',
            'errorline' => 0,
            'errormessage' => $raw,
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
    if ($xmlresult != "") {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresult);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $getSailingListResponse = $Body->item(0)->getElementsByTagName("getSailingListResponse");
        $OTA_CruiseSailAvailRS = $getSailingListResponse->item(0)->getElementsByTagName("OTA_CruiseSailAvailRS");
        if ($OTA_CruiseSailAvailRS->length > 0) {
            $SailingOptions = $OTA_CruiseSailAvailRS->item(0)->getElementsByTagName("SailingOptions");
            if ($SailingOptions->length > 0) {
                $SailingOption = $SailingOptions->item(0)->getElementsByTagName("SailingOption");
                if ($SailingOption->length > 0) {
                    for ($i=0; $i < $SailingOption->length; $i++) { 
                        $SelectedSailing = $SailingOption->item($i)->getElementsByTagName("SelectedSailing");
                        if ($SelectedSailing->length > 0) {
                            $ListOfSailingDescriptionCode = $SelectedSailing->item(0)->getAttribute("ListOfSailingDescriptionCode");
                            $Duration = $SelectedSailing->item(0)->getAttribute("Duration");
                            $PortsOfCallQuantity = $SelectedSailing->item(0)->getAttribute("PortsOfCallQuantity");
                            $Start = $SelectedSailing->item(0)->getAttribute("Start");
                            $Status = $SelectedSailing->item(0)->getAttribute("Status");

                            $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
                            if ($CruiseLine->length > 0) {
                                $ShipCode = $CruiseLine->item(0)->getAttribute("ShipCode");
                                $VendorCode = $CruiseLine->item(0)->getAttribute("VendorCode");
                            }
                            $Region = $SelectedSailing->item(0)->getElementsByTagName("Region");
                            if ($Region->length > 0) {
                                $RegionCode = $Region->item(0)->getAttribute("RegionCode");
                                $SubRegionCode = $Region->item(0)->getAttribute("SubRegionCode");
                            }
                            $DeparturePort = $SelectedSailing->item(0)->getElementsByTagName("DeparturePort");
                            if ($DeparturePort->length > 0) {
                                $DeparturePortLocationCode = $DeparturePort->item(0)->getAttribute("LocationCode");
                            }
                            $ArrivalPort = $SelectedSailing->item(0)->getElementsByTagName("ArrivalPort");
                            if ($ArrivalPort->length > 0) {
                                $ArrivalPortLocationCode = $ArrivalPort->item(0)->getAttribute("LocationCode");
                            }
                        }
                        $InclusivePackageOption = $SailingOption->item($i)->getElementsByTagName("InclusivePackageOption");
                        if ($InclusivePackageOption->length > 0) {
                            $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                            $InclusiveIndicator = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
                        }
                        $sql = "select name, logo, seo from cruises_lines where cruises_xml08='" . $ShipCode . "'";
                        $statement = $dbPullmantur->createStatement($sql);
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
                            $cruiseline_name = "";
                            $cruiseline_logo = "";
                            $cruiseline_seo = "";
                        }
                        $sql = "select id, name, seo, shiprating from ships where cruises_xml08='" . $ShipCode . "'";
                        $statement = $dbPullmantur->createStatement($sql);
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
                            $adaptor = new Sql($dbPullmantur);
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
                        // error_log("\r\nUnable to find tourico cruise line TODO - Check - $cruiselineid - alterar para id, db cruises_lines \r\n", 3, "/srv/www/htdocs/error_log");
                        $cruises[$counter]["cruise_line_id"] = $cruiselineid;
                        $cruises[$counter]["quote_id"] = md5(uniqid($session_id, true)) . "-4-" . $counter;
                        $cruises[$counter]["ship"]["id"] = $ship_id;
                        $cruises[$counter]["ship"]["seo"] = $ship_seo;
                        $cruises[$counter]['ship']["name"] = utf8_encode(htmlentities($shipname, ENT_QUOTES));
                        $cruises[$counter]["ship"]["rating"] = $ship_rating;
                        $cruises[$counter]["cruiseline"]["logo"] = $cruiseline_logo;
                        $cruises[$counter]["cruiseline"]["name"] = utf8_encode(htmlentities($cruiseline_name, ENT_QUOTES));
                        $cruises[$counter]["cruiseline"]["seo"] = $cruiseline_seo;
                        $cruisesfrom = 0;
                        $cruisesfrom_publish = 0;
                        // Itinerary
                        $sql = "select id, name, latitude, longitude, image, description from cruises_ports where cruises_xml08='" . $DeparturePortLocationCode . "'";
                        $statement = $dbPullmantur->createStatement($sql);
                        $statement->prepare();
                        $row = $statement->execute();
                        $row->buffer();
                        if ($row->valid()) {
                            $row = $row->current();
                            $segments[$xSegment]['port_id'] = $row["id"];
                            $segments[$xSegment]['name'] = $row["name"];
                            $segments[$xSegment]['latitude'] = $row["latitude"];
                            $segments[$xSegment]['longitude'] = $row["longitude"];
                            $segments[$xSegment]['image'] = $row["image"];
                            $segments[$xSegment]['description'] = $row["description"];
                        } else {
                            $segments[$xSegment]['port_id'] = 0;
                            $segments[$xSegment]['name'] = "";
                            $segments[$xSegment]['latitude'] = 0;
                            $segments[$xSegment]['longitude'] = 0;
                            $segments[$xSegment]['image'] = "";
                            $segments[$xSegment]['description'] = "";
                        }
                        // Sailling Dates
                        $duration = 0;                        
                        if ($cruisespullmanturmarkup > 0) {
                            if ((int) $IN_Price > 0) {
                                $IN_Price = number_format($IN_Price + (($IN_Price * $cruisespullmanturmarkup) / 100), 2, '.', '');
                            }
                            if ((int) $ST_Price > 0) {
                                $ST_Price = number_format($ST_Price + (($ST_Price * $cruisespullmanturmarkup) / 100), 2, '.', '');
                            }
                            if ((int) $BL_Price > 0) {
                                $BL_Price = number_format($BL_Price + (($BL_Price * $cruisespullmanturmarkup) / 100), 2, '.', '');
                            }
                            if ((int) $OV_Price > 0) {
                                $OV_Price = number_format($OV_Price + (($OV_Price * $cruisespullmanturmarkup) / 100), 2, '.', '');
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
                        $cruises[$counter]['product_id'][$i] = md5(uniqid($session_id, true)) . "-" . $counter . "-" . $i;
                        $cruises[$counter]['sailingid'][$i] = $sailingid;
                        $cruises[$counter]['departure'][$i] = mktime(0, 0, 0, $departure[0], $departure[1], $departure[2]);
                        $cruises[$counter]['arrival'][$i] = mktime(0, 0, 0, $arrival[0], $arrival[1], $arrival[2]);
                        $cruises[$counter]['Incentive'][$i] = $Incentives;
                        $cruises[$counter]['Incentives'][$i] = $yesIncentives;
                        if ($IN_Price == 0 or $IN_Price == - 1) {
                            if ($IN_Price > 0) {
                                if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                }
                            }
                            $cruises[$counter]['IN_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["IN_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['insidecabin'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["insidecabin_plain"][$i] = 0;
                            $cruises[$counter]["IN_Price_plain"][$i] = 0;
                        } else {
                            if ($IN_Price > 0) {
                                if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
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
                                if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                }
                            }
                            $cruises[$counter]['OV_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["OV_PricePublish_plain"][$i] = $OV_PricePublish;
                            $cruises[$counter]['oceanview'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["oceanview_plain"][$i] = 0;
                            $cruises[$counter]["OV_Price_plain"][$i] = 0;
                        } else {
                            if ($OV_Price > 0) {
                                if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
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
                                if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                }
                            }
                            $cruises[$counter]['BL_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["BL_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['balcony'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["balcony_plain"][$i] = 0;
                            $cruises[$counter]["BL_Price_plain"][$i] = 0;
                        } else {
                            if ($BL_Price > 0) {
                                if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
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
                                if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                    $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                    $ST_Price = $CurrencyConverter->convert($ST_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                }
                            }
                            $cruises[$counter]['ST_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["ST_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['suite'][$i] = $translator->translate("N/A");
                            $cruises[$counter]['suite_plain'][$i] = 0;
                            $cruises[$counter]["ST_Price_plain"][$i] = 0;
                        } else {
                            if ($ST_Price > 0) {
                                $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                $ST_Price = $CurrencyConverter->convert($ST_Price, $cruisestouricoholidaysCurrencyCode, $scurrency);
                            }
                            $cruises[$counter]['ST_PricePublish'][$i] = $filter->filter($ST_PricePublish);
                            $cruises[$counter]["ST_PricePublish_plain"][$i] = $ST_PricePublish;
                            $cruises[$counter]['suite'][$i] = $filter->filter($ST_Price);
                            $cruises[$counter]['suite_plain'][$i] = $ST_Price;
                            $cruises[$counter]["ST_Price_plain"][$i] = $ST_Price;
                        }

                        if ($IN_Price > 0) {
                            if ($cruisestouricoholidaysCurrencyCode != $scurrency) {
                                $cruisesfrom = $CurrencyConverter->convert($cruisesfrom, $cruisestouricoholidaysCurrencyCode, $scurrency);
                                $cruisesfrom_publish = $CurrencyConverter->convert($cruisesfrom_publish, $cruisestouricoholidaysCurrencyCode, $scurrency);
                            }
                        }
                        $cruises[$counter]['from'] = $filter->filter($cruisesfrom);
                        $cruises[$counter]["from_plain"] = $cruisesfrom;
                        $cruises[$counter]['from_publish'] = $filter->filter($cruisesfrom_publish);
                        $cruises[$counter]["from_plain_publish"] = $cruisesfrom_publish;
                        $cruises[$counter]['name'] = $name;
                        $cruises[$counter]['currency'] = $scurrency;
                        $cruises[$counter]['length'] = $cruiselength;
                        $cruises[$counter]['cruise_destination_id'] = $ShipCode;
                        $cruises[$counter]['ItineraryId'] = $ItineraryId;
                        $cruises[$counter]['ShipRating'] = $ShipRating;
                        $cruises[$counter]['MapImg'] = $MapImg;
                        $cruises[$counter]['departure']['portid'] = $DeparturePortLocationCode;
                        $cruises[$counter]['departure']['portname'] = $DeparturePortLocationCode;
                        $cruises[$counter]['segments'] = $segments;
                        // Amenities
                        $amenities = array();
                        $tmp = array();
                        $sql = "select distinct(name), ico from ships_amenities where ship_id=" . $ShipCode;
                        $statement2 = $dbPullmantur->createStatement($sql);
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
                        $sql = "select deck_number, name, image from ships_decks where ship_id=" . $ShipCode . " order by deck_number desc";
                        $statement2 = $dbPullmantur->createStatement($sql);
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
                        $sql = "select decknumber from ships_publicareas where ship_id=" . $ShipCode . " group by decknumber order by decknumber desc";
                        $statement2 = $dbPullmantur->createStatement($sql);
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
                        $sql = "select decknumber, name, image from ships_publicareas where ship_id=" . $ShipCode . " order by decknumber desc";
                        $statement2 = $dbPullmantur->createStatement($sql);
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
        }
    }
}
$dbPullmantur->getDriver()
    ->getConnection()
    ->disconnect();
?>