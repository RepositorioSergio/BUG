<?php
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
error_log("\r\n Start RCCL \r\n", 3, "/srv/www/htdocs/error_log");
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablecruisesroyalcaribbean' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_cruisesroyalcaribbean = $affiliate_id;
} else {
    $affiliate_id_cruisesroyalcaribbean = 0;
}
$sql = "select value from settings where name='cruisesroyalcaribbeanusername' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisesroyalcaribbeanusername = $row_settings['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanpassword' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisesroyalcaribbeanpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisesroyalcaribbeanServiceURL' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanServiceURL = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosuserid' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosuserid = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosStatusLiveTest' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosStatusLiveTest = $row['value'];
}
$sql = "select value from settings where name='mundocruceroslineid' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocruceroslineid = $row['value'];
} else {
    $mundocruceroslineid = "";
}
$sql = "select value from settings where name='mundocrucerosServiceURLBook' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURLBook = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosSID' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosSID = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosWebsite' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosWebsite = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanSearchSortorder' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanbranchs_id' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanbranchs_id = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanmarkup' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanmarkup = (double) $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanb2cmarkup' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanb2cmarkup = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanaffiliates_id' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanConnetionTimeout' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanConnetionTimeout = (int) $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanCurrency' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanCurrency = $row['value'];
}

$sql = "select cruises_xml13 from cruises_regions where seo='" . $destination . "'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisedestinationid = $row_settings["cruises_xml13"];
} else {
    $cruisedestinationid = 0;
}
if ($cruiseline != "all") {
    $sql = "select cruises_xml13 from cruises_lines where seo='" . $cruiseline . "'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $CruiseLineID = $row_settings["cruises_xml13"];
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
    $MaiLength = 9999;
} else {
    $length = explode("-", $length);
    $MinCruiseLength = (int) $length[0];
    $MaiLength = (int) $length[1];
    if ($MaiLength == 0) {
        $MaiLength = 9999;
    }
}
if ($cruiseship != "" and $cruiseship != "all") {
    $sql = "select cruises_xml13 from ships where seo='" . $cruiseship . "'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ShipID = $row_settings["cruises_xml13"];
    } else {
        $ShipID = 0;
    }
} else {
    $ShipID = 0;
}
if ($departureport != "" and $departureport != "all") {
    $sql = "select cruises_xml13 from cruises_ports where seo='" . $departureport . "'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PortID = $row_settings["cruises_xml13"];
    } else {
        $PortID = 0;
    }
} else {
    $PortID = 0;
}
if ($cruisedestinationid > 0) {
    // Paulo
    // TODO: Adults, Children, Currency, Status, Destination ID Filter, Ship Id Filter
    //
    // regionid = "XX"
    // startport = "XX"
    //
    // Possible length filter - nights
    //
    if ((int) $CruiseLineID > 0) {
        $cruiselinefilter = ' lineid="' . $CruiseLineID . '"';
        $mundocruceroslineid = "";
    } else {
        $cruiselinefilter = "";
    }
    // Departure Port
    if ($departureport != "" and $departureport != "all") {
        if ((int) $PortID > 0) {
            $cruisedepartureportfilter = ' startport="' . $departureport . '"';
        } else {
            $cruisedepartureportfilter = "";
        }
    } else {
        $cruisedepartureportfilter = "";
    }
    // Shipid
    if ($ShipID > 0 or $ShipID != "") {
        $cruiseshipidfilter = ' shipid="' . $ShipID . '"';
    } else {
        $cruiseshipidfilter = "";
    }
    // Region id
    if ($destination != "") {
        if ((int) $cruisedestinationid > 0) {
            $cruisedestinationfilter = ' regionid="' . $cruisedestinationid . '"';
            //apagar
            $cruisedestinationfilter = "";
        } else {
            $cruisedestinationfilter = "";
        }
    } else {
        $cruisedestinationfilter = "";
    }
    // error_log("\r\nCruise Line ID : $CruiseLineID\r\n", 3, "/srv/www/htdocs/error_log");
    error_log("\r\nPort Id : $PortID\r\n", 3, "/srv/www/htdocs/error_log");
    error_log("\r\nDestination Id : $cruisedestinationid\r\n", 3, "/srv/www/htdocs/error_log");
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
                    <alp:RequestorID ID="313917" ID_Context="AGENCY1" Type="11"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                    </alp:BookingChannel>
                </alp:Source>
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="313917" ID_Context="AGENCY2" Type="11"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                    </alp:BookingChannel>
                </alp:Source>
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="313917" ID_Context="AGENT1" Type="11"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                    </alp:BookingChannel>
                </alp:Source>
            </alp:POS>
            <alp:SailingDateRange Start="2020-11-08"/>
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
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    error_log("\r\n RCCL error - $error \r\n", 3, "/srv/www/htdocs/error_log");
    error_log("\r\n RCCL Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_rccl');
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
    if ($response != "") {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
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

                        $sql = "select name, logo, seo from cruises_lines where cruises_xml13='" . $lineid . "'";
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
                            $cruiseline_logo = "https: // world-wide-web-servers.com/cr/" . $row["logo"];
                            $cruiseline_seo = $row["seo"];
                        } else {
                            // Unable to find cruise line $cruiselineid
                            $cruiseline_name = $linename;
                            $cruiseline_logo = $linelogourl;
                            $cruiseline_seo = "";
                        }
                        $sql = "select id, name, seo, shiprating from ships where cruises_xml13='" . $shipid . "'";
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
                            $shipname = $shipname;
                            $cruises[$counter]["images"][0] = $shipimageurl;
                            $ship_id = $shipid;
                            $ship_rating = $shiprating;
                            $ship_seo = "";
                        }
                        $cruises[$counter]["id"] = $counter;
                        $cruises[$counter]["seo"] = $ship_seo;
                        // TODO
                        // error_log("\r\nUnable to find tourico cruise line TODO - Check - $cruiselineid - alterar para id, db cruises_lines \r\n", 3, "/srv/www/htdocs/error_log");
                        $cruises[$counter]["cruise_line_id"] = $lineid;
                        $cruises[$counter]["quote_id"] = md5(uniqid($session_id, true)) . "-13-" . $counter;
                        $cruises[$counter]["ship"]["id"] = $ship_id;
                        $cruises[$counter]["ship"]["seo"] = $ship_seo;
                        $cruises[$counter]['ship']["name"] = utf8_encode(htmlentities($shipname, ENT_QUOTES));
                        $cruises[$counter]["ship"]["rating"] = $ship_rating;
                        $cruises[$counter]["cruiseline"]["logo"] = $cruiseline_logo;
                        $cruises[$counter]["cruiseline"]["name"] = utf8_encode(htmlentities($cruiseline_name, ENT_QUOTES));
                        $cruises[$counter]["cruiseline"]["seo"] = $cruiseline_seo;
                        $cruisesfrom = 0;
                        $cruisesfrom_publish = 0;
                        
                        $duration = 0;
                        
                        // B2C Price
                        $IN_PricePublish = 1000; // Displays the Inside cabin publish price.
                        $ST_PricePublish = 1100; // Displays the suite cabin publish price.
                        $BL_PricePublish = 1200; // Displays the balcony cabin publish price.
                        $OV_PricePublish = 1150; // Displays the ocean view cabin publish price.
                                                            // B2B Price
                        $IN_Price = 1000; // Displays the Inside cabin price.
                        $ST_Price = 1100; // Displays the suite cabin price.
                        $BL_Price = 1200; // Displays the balcony cabin price.
                        $OV_Price = 1150; // Displays the ocean view cabin publish price.
                        if ($mundocrucerosmarkup > 0) {
                            if ((int) $IN_Price > 0) {
                                $IN_Price = number_format($IN_Price + (($IN_Price * $mundocrucerosmarkup) / 100), 2, '.', '');
                            }
                            if ((int) $ST_Price > 0) {
                                $ST_Price = number_format($ST_Price + (($ST_Price * $mundocrucerosmarkup) / 100), 2, '.', '');
                            }
                            if ((int) $BL_Price > 0) {
                                $BL_Price = number_format($BL_Price + (($BL_Price * $mundocrucerosmarkup) / 100), 2, '.', '');
                            }
                            if ((int) $OV_Price > 0) {
                                $OV_Price = number_format($OV_Price + (($OV_Price * $mundocrucerosmarkup) / 100), 2, '.', '');
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
                        $cruises[$counter]['sailingid'][$i] = $voyagecode;
                        $cruises[$counter]['departure'][$i] = mktime(0, 0, 0, $saildate[1], $saildate[2], $saildate[0]);
                        $cruises[$counter]['arrival'][$i] = mktime(0, 0, 0, $returndate[1], $returndate[2], $returndate[0]);
                        if ($IN_Price == 0 or $IN_Price == - 1) {
                            if ($IN_Price > 0) {
                                if ($currency != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currency, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['IN_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["IN_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['insidecabin'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["insidecabin_plain"][$i] = 0;
                            $cruises[$counter]["IN_Price_plain"][$i] = 0;
                        } else {
                            if ($IN_Price > 0) {
                                if ($currency != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currency, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $currency, $scurrency);
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
                                if ($currency != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currency, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['OV_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["OV_PricePublish_plain"][$i] = $OV_PricePublish;
                            $cruises[$counter]['oceanview'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["oceanview_plain"][$i] = 0;
                            $cruises[$counter]["OV_Price_plain"][$i] = 0;
                        } else {
                            if ($OV_Price > 0) {
                                if ($currency != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currency, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $currency, $scurrency);
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
                                if ($currency != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currency, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['BL_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["BL_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['balcony'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["balcony_plain"][$i] = 0;
                            $cruises[$counter]["BL_Price_plain"][$i] = 0;
                        } else {
                            if ($BL_Price > 0) {
                                if ($currency != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currency, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $currency, $scurrency);
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
                                if ($currency != $scurrency) {
                                    $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currency, $scurrency);
                                    $ST_Price = $CurrencyConverter->convert($ST_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['ST_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["ST_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['suite'][$i] = $translator->translate("N/A");
                            $cruises[$counter]['suite_plain'][$i] = 0;
                            $cruises[$counter]["ST_Price_plain"][$i] = 0;
                        } else {
                            if ($ST_Price > 0) {
                                $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currency, $scurrency);
                                $ST_Price = $CurrencyConverter->convert($ST_Price, $currency, $scurrency);
                            }
                            $cruises[$counter]['ST_PricePublish'][$i] = $filter->filter($ST_PricePublish);
                            $cruises[$counter]["ST_PricePublish_plain"][$i] = $ST_PricePublish;
                            $cruises[$counter]['suite'][$i] = $filter->filter($ST_Price);
                            $cruises[$counter]['suite_plain'][$i] = $ST_Price;
                            $cruises[$counter]["ST_Price_plain"][$i] = $ST_Price;
                        }
                        if ($IN_Price > 0) {
                            if ($currency != $scurrency) {
                                $cruisesfrom = $CurrencyConverter->convert($cruisesfrom, $currency, $scurrency);
                                $cruisesfrom_publish = $CurrencyConverter->convert($cruisesfrom_publish, $currency, $scurrency);
                            }
                        }
                        $cruises[$counter]['from'] = $filter->filter($cruisesfrom);
                        $cruises[$counter]["from_plain"] = $cruisesfrom;
                        $cruises[$counter]['from_publish'] = $filter->filter($cruisesfrom_publish);
                        $cruises[$counter]["from_plain_publish"] = $cruisesfrom_publish;
                        $cruises[$counter]['name'] = $cruisename;
                        $cruises[$counter]['currency'] = $scurrency;
                        $cruises[$counter]['length'] = $sailnights;
                        $cruises[$counter]['cruise_destination_id'] = $cruisedestinationid;
                        $cruises[$counter]['ItineraryId'] = $ItineraryId;
                        $cruises[$counter]['ShipRating'] = $shiprating;
                        $cruises[$counter]['sessionkey'] = $sessionkey;
                        $cruises[$counter]['resultno'] = $resultno;
                        $cruises[$counter]['departure']['portid'] = $segments[0]['portid'];
                        $cruises[$counter]['departure']['portname'] = $segments[0]['portname'];
                        $cruises[$counter]['segments'] = $segments;
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
                        //
                        // Public Areas
                        //
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
        }
    }
}
error_log("\r\n EOF RCCL \r\n", 3, "/srv/www/htdocs/error_log");
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>