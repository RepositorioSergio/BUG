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
error_log("\r\nStart RCCL\r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\ncruisesroyalcaribbeanServiceURL : $cruisesroyalcaribbeanServiceURL\r\n", 3, "/srv/www/htdocs/error_log");
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
$sql = "select cruises_xml11 from cruises_regions where seo='" . $destination . "'";
error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisedestinationid = $row_settings["cruises_xml11"];
} else {
    $cruisedestinationid = 0;
}
error_log("\r\nRCCL - TODO - TODO - TODO - cruises_xml11 - mapping, then remove cruisedestinationid = 18\r\n", 3, "/srv/www/htdocs/error_log");
$cruisedestinationid = 18;
if ($cruiseline != "all") {
    $sql = "select cruises_xml11 from cruises_lines where seo='" . $cruiseline . "'";
    error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $CruiseLineID = $row_settings["cruises_xml11"];
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
$MinDuration = "";
$MaxDuration = "";
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
    $MinDuration = 'P' . $MinCruiseLength . 'N';
    $MaxDuration = 'P' . $MaxCruiseLength . 'N';
}
if ($cruiseship != "" and $cruiseship != "all") {
    $sql = "select cruises_xml11 from ships where seo='" . $cruiseship . "'";
    error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $ShipID = $row_settings["cruises_xml11"];
    } else {
        $ShipID = 0;
    }
} else {
    $ShipID = 0;
}
if ($departureport != "" and $departureport != "all") {
    $sql = "select cruises_xml11 from cruises_ports where seo='" . $departureport . "'";
    error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $PortID = $row_settings["cruises_xml11"];
    } else {
        $PortID = 0;
    }
} else {
    $PortID = 0;
}
if ($cruisedestinationid > 0) {
    $cruiseinitfilter = '<alp:CruiseLinePrefs>';
    $cruiseendfilter = '</alp:CruiseLinePrefs>';
    // Cruise Line
    if ((int) $CruiseLineID > 0) {
        $cruiselinefilter = '
        <alp:CruiseLinePref>
           <alp:InclusivePackageOption CruisePackageCode="' . $CruiseLineID . '" InclusiveIndicator="false"/>
        </alp:CruiseLinePref>';
    } else {
        $cruiselinefilter = "";
    }
    // error_log("\r\n CruiseLineID : $cruiselinefilter \r\n", 3, "/srv/www/htdocs/error_log");
    // Departure Port
    if ($departureport != "" and $departureport != "all") {
        if ((int) $PortID > 0) {
            $cruisedepartureportfilter = '
            <alp:CruiseLinePref>
               <alp:SearchQualifiers>
                  <alp:Port PortCode="' . $PortID . '"/>
               </alp:SearchQualifiers>
            </alp:CruiseLinePref>';
        } else {
            $cruisedepartureportfilter = "";
        }
    } else {
        $cruisedepartureportfilter = "";
    }
    // error_log("\r\nPort Id : $cruisedepartureportfilter\r\n", 3, "/srv/www/htdocs/error_log");
    // Shipid
    if ($ShipID > 0 or $ShipID != "") {
        $cruiseshipidfilter = '
        <alp:CruiseLinePref ShipCode="' . $ShipID . '"></alp:CruiseLinePref>';
    } else {
        $cruiseshipidfilter = "";
    }
    // error_log("\r\n Ship Id : $cruiseshipidfilter \r\n", 3, "/srv/www/htdocs/error_log");
    // Region id
    if ($destination != "") {
        if ((int) $cruisedestinationid > 0) {
            $cruisedestinationfilter = '<alp:RegionPref RegionCode="' . $cruisedestinationid . '"/>';
            $cruisedestinationfilter = "";
        } else {
            $cruisedestinationfilter = "";
        }
    } else {
        $cruisedestinationfilter = "";
    }
    // error_log("\r\nCruisedestinationfilter : $cruisedestinationfilter\r\n", 3, "/srv/www/htdocs/error_log");
    $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sail="http://services.rccl.com/Interfaces/SailingList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha"><soapenv:Header/><soapenv:Body><sail:getSailingList>
        <alp:OTA_CruiseSailAvailRQ TimeStamp="2008-07-17T12:44:44.866-04:00" Target="Test" Version="1.0" SequenceNmbr="1" PrimaryLangID="en" RetransmissionIndicator="false" MoreIndicator="true" MaxResponses="50">
            <alp:POS>
                <!--1 to 10 repetitions:-->
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="369567" ID_Context="AGENCY1" Type="11"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="COSTAMAR"/>
                    </alp:BookingChannel>
                </alp:Source>
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="369567" ID_Context="AGENCY2" Type="11"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="COSTAMAR"/>
                    </alp:BookingChannel>
                </alp:Source>
                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                    <alp:RequestorID ID="369567" ID_Context="AGENT1" Type="11"/>
                    <alp:BookingChannel Type="7">
                        <alp:CompanyName CompanyShortName="COSTAMAR"/>
                    </alp:BookingChannel>
                </alp:Source>
            </alp:POS>
            <!--Optional:-->
            <alp:GuestCounts>
                <alp:GuestCount Age="30" Quantity="1"/>
                <alp:GuestCount Age="5" Quantity="1"/>
            </alp:GuestCounts>
            <alp:SailingDateRange Start="' . $departureFrom . '" End="' . $departureTo . '" ';
    if ($MinDuration !== "") {
        $raw .= 'MinDuration="' . $MinDuration . '" MaxDuration="' . $MaxDuration . '"';
    }
    if ($cruiselinefilter === "" and $cruisedepartureportfilter === "" and $cruisedestinationfilter === "" and $cruiseshipidfilter === "") {
        $raw .= '/></alp:OTA_CruiseSailAvailRQ></sail:getSailingList></soapenv:Body></soapenv:Envelope>';
    } else {
        $raw .= '/>' . $cruiseinitfilter . '' . $cruiselinefilter . '' . $cruisedepartureportfilter . '' . $cruiseshipidfilter . '' . $cruisedestinationfilter . '' . $cruiseendfilter . '</alp:OTA_CruiseSailAvailRQ></sail:getSailingList></soapenv:Body></soapenv:Envelope>';
    }
    error_log("\r\n Request RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cruisesroyalcaribbeanServiceURL . 'Reservation_FITWeb/sca/SailingList');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_USERPWD, $cruisesroyalcaribbeanusername . ":" . $cruisesroyalcaribbeanpassword);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisesroyalcaribbeanConnetionTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    $xmlresult = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    error_log("\r\nRCCL Response - $xmlresult\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_rccl');
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
                    for ($i = 0; $i < $SailingOption->length; $i ++) {
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
                        $sql = "select name, logo, seo from cruises_lines where cruises_xml11='" . $VendorCode . "'";
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
                            error_log("\r\nRCCL - Unable to find Cruise Line - $VendorCode - $sql\r\n", 3, "/srv/www/htdocs/error_log");
                            // Unable to find cruise line $cruiselineid
                            $cruiseline_name = "";
                            $cruiseline_logo = "";
                            $cruiseline_seo = "";
                        }
                        $sql = "select id, name, seo, shiprating from ships where cruises_xml11='" . $ShipCode . "'";
                        error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
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
                        // error_log("\r\nUnable to find Pullmantur cruise line TODO - Check - $cruiselineid - alterar para id, db cruises_lines \r\n", 3, "/srv/www/htdocs/error_log");
                        $cruises[$counter]["cruise_line_id"] = $ShipCode;
                        $cruises[$counter]["quote_id"] = md5(uniqid($session_id, true)) . "-11-" . $counter;
                        $cruises[$counter]["ship"]["id"] = $ship_id;
                        $cruises[$counter]["ship"]["seo"] = $ship_seo;
                        $cruises[$counter]['ship']["name"] = utf8_encode(htmlentities($shipname, ENT_QUOTES));
                        $cruises[$counter]["ship"]["rating"] = $ship_rating;
                        $cruises[$counter]["cruiseline"]["logo"] = $cruiseline_logo;
                        $cruises[$counter]["cruiseline"]["name"] = utf8_encode(htmlentities($cruiseline_name, ENT_QUOTES));
                        $cruises[$counter]["cruiseline"]["seo"] = $cruiseline_seo;
                        $cruises[$counter]["listofsailingdescriptioncode"] = $ListOfSailingDescriptionCode;
                        $cruises[$counter]["duration"] = $Duration;
                        $cruises[$counter]["portsofcallquantity"] = $PortsOfCallQuantity;
                        $cruises[$counter]["start"] = $Start;
                        $cruises[$counter]["status"] = $Status;
                        $cruises[$counter]["shipcode"] = $ShipCode;
                        $cruises[$counter]["vendorcode"] = $VendorCode;
                        $cruises[$counter]["regioncode"] = $RegionCode;
                        $cruises[$counter]["subregioncode"] = $SubRegionCode;
                        $cruises[$counter]["departureportlocationcode"] = $DeparturePortLocationCode;
                        $cruises[$counter]["arrivalportlocationcode"] = $ArrivalPortLocationCode;
                        $cruises[$counter]["cruisepackagecode"] = $CruisePackageCode;
                        $cruises[$counter]["inclusiveindicator"] = $InclusiveIndicator;
                        //
                        // Itinerary
                        //
                        $raw4 = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:itin="http://services.rccl.com/Interfaces/ItineraryDetail" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha"><soapenv:Header/><soapenv:Body><itin:getItineraryDetail><OTA_CruiseItineraryDescRQ RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-12-29T18:25:50.1Z" TransactionIdentifier="106597" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
                                    <POS>
                                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                                            <RequestorID ID="369567" ID_Context="AGENCY1" Type="11"/>
                                            <BookingChannel Type="7">
                                                <CompanyName CompanyShortName="COSTAMAR"/>
                                            </BookingChannel>
                                        </Source>
                                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                                            <RequestorID ID="369567" ID_Context="AGENCY2" Type="11"/>
                                            <BookingChannel Type="7">
                                                <CompanyName CompanyShortName="COSTAMAR"/>
                                            </BookingChannel>
                                        </Source>
                                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                                            <RequestorID ID="369567" ID_Context="AGENT1" Type="11"/>
                                            <BookingChannel Type="7">
                                                <CompanyName CompanyShortName="COSTAMAR"/>
                                            </BookingChannel>
                                        </Source>
                                    </POS>
                                    <!--Optional:-->
                                    <SelectedSailing Start="' . $Start . '" Duration="' . $Duration . '" VendorCode="' . $VendorCode . '" ShipCode="' . $ShipCode . '" Status="' . $Status . '"/>
                                    <!--Optional:-->
                                    <PackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="false"/>
                                </OTA_CruiseItineraryDescRQ></itin:getItineraryDetail></soapenv:Body></soapenv:Envelope>';
                        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $cruisesroyalcaribbeanServiceURL . 'Reservation_FITWeb/sca/ItineraryDetail');
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_VERBOSE, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw4);
                        curl_setopt($ch, CURLOPT_USERPWD, $cruisesroyalcaribbeanusername . ":" . $cruisesroyalcaribbeanpassword);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisesroyalcaribbeanConnetionTimeout);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
                        $response4 = curl_exec($ch);
                        $error = curl_error($ch);
                        $headers = curl_getinfo($ch);
                        curl_close($ch);
                        
                        $segments = array();
                        
                        $inputDoc = new DOMDocument();
                        $inputDoc->loadXML($response4);
                        $Envelope = $inputDoc->getElementsByTagName("Envelope");
                        $Body = $Envelope->item(0)->getElementsByTagName("Body");
                        $getItineraryDetailResponse = $Body->item(0)->getElementsByTagName("getItineraryDetailResponse");
                        if ($getItineraryDetailResponse->length > 0) {
                            $CruiseItinInfos = $getItineraryDetailResponse->item(0)->getElementsByTagName("CruiseItinInfos");
                            if ($CruiseItinInfos->length > 0) {
                                $CruiseItinInfo = $CruiseItinInfos->item(0)->getElementsByTagName("CruiseItinInfo");
                                if ($CruiseItinInfo->length > 0) {
                                    for ($iTmp = 0; $iTmp < $CruiseItinInfo->length; $iTmp ++) {
                                        $segments[$iTmp]['portid'] = $CruiseItinInfo->item($iTmp)->getAttribute("PortCode");
                                        $segments[$iTmp]['portname'] = $CruiseItinInfo->item($iTmp)->getAttribute("PortName");
                                        $iTmpnformation = $CruiseItinInfo->item($iTmp)->getElementsByTagName("Information");
                                        if ($Information->length > 0) {
                                            $Text = $Information->item(0)->getElementsByTagName("Text");
                                            if ($Text->length > 0) {
                                                $Text = $Text->item(0)->nodeValue;
                                            } else {
                                                $Text = "";
                                            }
                                        }
                                        $DateTimeDescription = $CruiseItinInfo->item($iTmp)->getElementsByTagName("DateTimeDescription");
                                        if ($DateTimeDescription->length > 0) {
                                            for ($iAux = 0; $iAux < $DateTimeDescription->length; $iAux ++) {
                                                $DateTimeDetails = $DateTimeDescription->item($iAux)->getAttribute("DateTimeDetails");
                                                $DateTimeQualifier = $DateTimeDescription->item($iAux)->getAttribute("DateTimeQualifier");
                                                $DayOfWeek = $DateTimeDescription->item($iAux)->getAttribute("DayOfWeek");
                                                /*
                                                 * if ($DateTimeQualifier === "departure") {
                                                 * $segments[$i]['departure'] = $DateTimeDetails;
                                                 * } else {
                                                 * $segments[$i]['arrival'] = $DateTimeDetails;
                                                 * }
                                                 */
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $cruisesfrom = 0;
                        $cruisesfrom_publish = 0;
                        //
                        // Sailling Dates
                        //
                        $duration = 0;
                        $raw2 = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cat="http://services.rccl.com/Interfaces/CategoryList" xmlns:m0="http://www.opentravel.org/OTA/2003/05/alpha"><soapenv:Header/><soapenv:Body><cat:getCategoryList>
                            <OTA_CruiseCategoryAvailRQ Target="Test" MaxResponses="50" MoreIndicator="true" Version="2.0" SequenceNmbr="1" TimeStamp="2008-11-05T19:15:56.692+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
                                <POS>
                                    <Source ISOCurrency="USD" TerminalID="12502LDJW6">
                                        <RequestorID ID="369567" Type="11" ID_Context="AGENCY1"/>
                                        <BookingChannel Type="7">
                                            <CompanyName CompanyShortName="COSTAMAR"/>
                                        </BookingChannel>
                                    </Source>
                                    <Source ISOCurrency="USD" TerminalID="12502LDJW6">
                                        <RequestorID ID="369567" Type="11" ID_Context="AGENCY2"/>
                                        <BookingChannel Type="7">
                                            <CompanyName CompanyShortName="COSTAMAR"/>
                                        </BookingChannel>
                                    </Source>
                                    <Source ISOCurrency="USD" TerminalID="12502LDJW6">
                                        <RequestorID ID="369567" Type="11" ID_Context="AGENT1"/>
                                        <BookingChannel Type="7">
                                            <CompanyName CompanyShortName="COSTAMAR"/>
                                        </BookingChannel>
                                    </Source>
                                </POS>
                                    <Guest>
                                    <GuestTransportation Mode="29" Status="36"/>
                                    </Guest>
                                    <GuestCounts>
                                        <GuestCount Age="30" Quantity="1"/>
                                        <GuestCount Age="5" Quantity="1"/>         
                                    </GuestCounts>
                                    <SailingInfo>
                                    <SelectedSailing ListOfSailingDescriptionCode="' . $ListOfSailingDescriptionCode . '" Start="' . $Start . '" Duration="' . $Duration . '" Status="' . $Status . '" PortsOfCallQuantity="' . $PortsOfCallQuantity . '">
                                        <CruiseLine VendorCode="' . $VendorCode . '" ShipCode="' . $ShipCode . '"/>
                                        <!--Optional:-->
                                        <Region RegionCode="' . $RegionCode . '" SubRegionCode="' . $SubRegionCode . '"/>
                                        <!--Optional:-->
                                        <DeparturePort LocationCode="' . $DeparturePortLocationCode . '"/>
                                        <!--Optional:-->
                                        <ArrivalPort LocationCode="' . $ArrivalPortLocationCode . '"/>
                                    </SelectedSailing>
                                    <!--Optional:-->
                                    <InclusivePackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="' . $InclusiveIndicator . '"/>
                                    <!--Optional:-->
                                    <Currency CurrencyCode="USD" DecimalPlaces="2"/>
                                    </SailingInfo>
                                    <SelectedFare FareCode="BESTRATE"/>
                                </OTA_CruiseCategoryAvailRQ>
                            </cat:getCategoryList></soapenv:Body></soapenv:Envelope>';
                        //
                        // error_log("\r\nRaw Response - $raw2\r\n", 3, "/srv/www/htdocs/error_log");
                        //
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $cruisesroyalcaribbeanServiceURL . 'Reservation_FITWeb/sca/CategoryList');
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_VERBOSE, false);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
                        curl_setopt($ch, CURLOPT_USERPWD, $cruisesroyalcaribbeanusername . ":" . $cruisesroyalcaribbeanpassword);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisesroyalcaribbeanConnetionTimeout);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
                        $response2 = curl_exec($ch);
                        $error = curl_error($ch);
                        $headers = curl_getinfo($ch);
                        curl_close($ch);
                        // error_log("\r\nRCCL Response2 - $response2 \r\n", 3, "/srv/www/htdocs/error_log");
                        $inputDoc = new DOMDocument();
                        $inputDoc->loadXML($response2);
                        $Envelope = $inputDoc->getElementsByTagName("Envelope");
                        $Body = $Envelope->item(0)->getElementsByTagName("Body");
                        $getCategoryListResponse = $Body->item(0)->getElementsByTagName("getCategoryListResponse");
                        if ($getCategoryListResponse->length > 0) {
                            $OTA_CruiseCategoryAvailRS = $getCategoryListResponse->item(0)->getElementsByTagName("OTA_CruiseCategoryAvailRS");
                            if ($OTA_CruiseCategoryAvailRS->length > 0) {
                                $SailingInfo = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("SailingInfo");
                                if ($SailingInfo->length > 0) {
                                    $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
                                    if ($SelectedSailing->length > 0) {
                                        $ListOfSailingDescriptionCode2 = $SelectedSailing->item(0)->getAttribute("ListOfSailingDescriptionCode");
                                        $Duration2 = $SelectedSailing->item(0)->getAttribute("Duration");
                                        $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
                                        if ($CruiseLine->length > 0) {
                                            $ShipCode2 = $CruiseLine->item(0)->getAttribute("ShipCode");
                                            $VendorCode2 = $CruiseLine->item(0)->getAttribute("VendorCode");
                                        }
                                        $Region = $SelectedSailing->item(0)->getElementsByTagName("Region");
                                        if ($Region->length > 0) {
                                            $RegionCode2 = $Region->item(0)->getAttribute("RegionCode");
                                            $SubRegionCode2 = $Region->item(0)->getAttribute("SubRegionCode");
                                        }
                                    }
                                    $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
                                    if ($InclusivePackageOption->length > 0) {
                                        $CruisePackageCode2 = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                                        $InclusiveIndicator2 = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
                                    }
                                }
                                $FareOption = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("FareOption");
                                if ($FareOption->length > 0) {
                                    $CategoryOptions = $FareOption->item(0)->getElementsByTagName("CategoryOptions");
                                    if ($CategoryOptions->length > 0) {
                                        $CategoryOption = $CategoryOptions->item(0)->getElementsByTagName("CategoryOption");
                                        if ($CategoryOption->length > 0) {
                                            for ($x = 0; $x < $CategoryOption->length; $x ++) {
                                                $AvailableGroupAllocationQty = $CategoryOption->item($x)->getAttribute("AvailableGroupAllocationQty");
                                                $AvailableRegularCabins = $CategoryOption->item($x)->getAttribute("AvailableRegularCabins");
                                                $CategoryLocation = $CategoryOption->item($x)->getAttribute("CategoryLocation");
                                                $GroupCode = $CategoryOption->item($x)->getAttribute("GroupCode");
                                                $ListOfCategoryQualifierCodes = $CategoryOption->item($x)->getAttribute("ListOfCategoryQualifierCodes");
                                                $PricedCategoryCode = $CategoryOption->item($x)->getAttribute("PricedCategoryCode");
                                                $Status = $CategoryOption->item($x)->getAttribute("Status");
                                                $PriceInfos = $CategoryOption->item($x)->getElementsByTagName("PriceInfos");
                                                if ($PriceInfos->length > 0) {
                                                    $PriceInfo = $PriceInfos->item(0)->getElementsByTagName("PriceInfo");
                                                    if ($PriceInfo->length > 0) {
                                                        $Amount = $PriceInfo->item(0)->getAttribute("Amount");
                                                        $AppliedPromotionsQuantity = $PriceInfo->item(0)->getAttribute("AppliedPromotionsQuantity");
                                                        $NetAmount = $PriceInfo->item(0)->getAttribute("NetAmount");
                                                        $NonRefundableType = $PriceInfo->item(0)->getAttribute("NonRefundableType");
                                                        $PriceId = $PriceInfo->item(0)->getAttribute("PriceId");
                                                        $PriceIdType = $PriceInfo->item(0)->getAttribute("PriceIdType");
                                                        $PromotionClass = $PriceInfo->item(0)->getAttribute("PromotionClass");
                                                        $PromotionDescription = $PriceInfo->item(0)->getAttribute("PromotionDescription");
                                                        $PromotionTypes = $PriceInfo->item(0)->getAttribute("PromotionTypes");
                                                        $FareCode = $PriceInfo->item(0)->getAttribute("FareCode");
                                                        
                                                        $PriceDescription = $PriceInfo->item(0)->getElementsByTagName("PriceDescription");
                                                        if ($PriceDescription->length > 0) {
                                                            $PriceDescription = $PriceDescription->item(0)->nodeValue;
                                                        } else {
                                                            $PriceDescription = "";
                                                        }
                                                        $PriceBreakDowns = $PriceInfo->item(0)->getElementsByTagName("PriceBreakDowns");
                                                        if ($PriceBreakDowns->length > 0) {
                                                            $Occupancy = $PriceBreakDowns->item(0)->getAttribute("Occupancy");
                                                            $PriceBreakDownsStatus = $PriceBreakDowns->item(0)->getAttribute("Status");
                                                            $PriceBreakDown = $PriceBreakDowns->item(0)->getElementsByTagName("PriceBreakDown");
                                                            if ($PriceBreakDown->length > 0) {
                                                                $AgeQualifyingCode = $PriceBreakDown->item(0)->getAttribute("AgeQualifyingCode");
                                                                $PriceBreakDownAmount = $PriceBreakDown->item(0)->getAttribute("Amount");
                                                                $NCCFAmount = $PriceBreakDown->item(0)->getAttribute("NCCFAmount");
                                                                $PriceBreakDownNetAmount = $PriceBreakDown->item(0)->getAttribute("NetAmount");
                                                                $RPH = $PriceBreakDown->item(0)->getAttribute("RPH");
                                                            }
                                                        }
                                                    }
                                                    
                                                    if ($CategoryLocation === "Inside") {
                                                        $IN_PricePublish = $Amount; // Displays the Inside cabin publish price.
                                                        $IN_Price = $Amount; // Displays the Inside cabin price.
                                                    } elseif ($CategoryLocation === "Outside") {
                                                        $ST_PricePublish = $Amount; // Displays the suite cabin publish price.
                                                        $ST_Price = $Amount; // Displays the suite cabin price.
                                                    } elseif ($CategoryLocation === "Balcony") {
                                                        $BL_PricePublish = $Amount; // Displays the balcony cabin publish price.
                                                        $BL_Price = $Amount; // Displays the balcony cabin price.
                                                    } elseif ($CategoryLocation === "Deluxe") {
                                                        $OV_PricePublish = $Amount; // Displays the ocean view cabin publish price.
                                                        $OV_Price = $Amount; // Displays the ocean view cabin publish price.
                                                    }
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
                                                    $days = str_replace('P', '', $Duration);
                                                    $days = str_replace('N', '', $days);
                                                    $aditionaldays = '+' . $days . ' days';
                                                    $date = date('Y-m-d', strtotime($aditionaldays, strtotime($Start)));
                                                    $from_date = explode("-", $Start);
                                                    $to_date = explode("-", $date);
                                                    $cruises[$counter]['product_id'][$x] = md5(uniqid($session_id, true)) . "-" . $counter . "-" . $x;
                                                    $cruises[$counter]['sailingid'][$x] = $CruisePackageCode;
                                                    $cruises[$counter]['departure'][$x] = mktime(0, 0, 0, $from_date[1], $from_date[2], $from_date[0]);
                                                    $cruises[$counter]['arrival'][$x] = mktime(0, 0, 0, $to_date[1], $to_date[2], $to_date[0]);
                                                    // $cruises[$counter]['Incentive'][$i] = $Incentives;
                                                    // $cruises[$counter]['Incentives'][$i] = $yesIncentives;
                                                    if ($IN_Price == 0 or $IN_Price == - 1) {
                                                        if ($IN_Price > 0) {
                                                            if ($currency != $scurrency) {
                                                                $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currency, $scurrency);
                                                                $IN_Price = $CurrencyConverter->convert($IN_Price, $currency, $scurrency);
                                                            }
                                                        }
                                                        $cruises[$counter]['IN_PricePublish'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]["IN_PricePublish_plain"][$x] = 0;
                                                        $cruises[$counter]['insidecabin'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]["insidecabin_plain"][$x] = 0;
                                                        $cruises[$counter]["IN_Price_plain"][$x] = 0;
                                                    } else {
                                                        if ($IN_Price > 0) {
                                                            if ($currency != $scurrency) {
                                                                $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currency, $scurrency);
                                                                $IN_Price = $CurrencyConverter->convert($IN_Price, $currency, $scurrency);
                                                            }
                                                        }
                                                        $cruises[$counter]['IN_PricePublish'][$x] = $filter->filter($IN_PricePublish);
                                                        $cruises[$counter]["IN_PricePublish_plain"][$x] = $IN_PricePublish;
                                                        $cruises[$counter]['insidecabin'][$x] = $filter->filter($IN_Price);
                                                        $cruises[$counter]['insidecabin_plain'][$x] = $IN_Price;
                                                        $cruises[$counter]["IN_Price_plain"][$x] = $IN_Price;
                                                    }
                                                    if ($OV_Price == 0 or $OV_Price == - 1) {
                                                        if ($OV_Price > 0) {
                                                            if ($currency != $scurrency) {
                                                                $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currency, $scurrency);
                                                                $OV_Price = $CurrencyConverter->convert($OV_Price, $currency, $scurrency);
                                                            }
                                                        }
                                                        $cruises[$counter]['OV_PricePublish'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]["OV_PricePublish_plain"][$x] = $OV_PricePublish;
                                                        $cruises[$counter]['oceanview'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]["oceanview_plain"][$x] = 0;
                                                        $cruises[$counter]["OV_Price_plain"][$x] = 0;
                                                    } else {
                                                        if ($OV_Price > 0) {
                                                            if ($currency != $scurrency) {
                                                                $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currency, $scurrency);
                                                                $OV_Price = $CurrencyConverter->convert($OV_Price, $currency, $scurrency);
                                                            }
                                                        }
                                                        $cruises[$counter]['OV_PricePublish'][$x] = $filter->filter($OV_PricePublish);
                                                        $cruises[$counter]["OV_PricePublish_plain"][$x] = $OV_PricePublish;
                                                        $cruises[$counter]['oceanview'][$x] = $filter->filter($OV_Price);
                                                        $cruises[$counter]["oceanview_plain"][$x] = $OV_Price;
                                                        $cruises[$counter]["OV_Price_plain"][$x] = $OV_Price;
                                                    }
                                                    if ($BL_Price == 0 or $BL_Price == - 1) {
                                                        if ($BL_Price > 0) {
                                                            if ($currency != $scurrency) {
                                                                $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currency, $scurrency);
                                                                $BL_Price = $CurrencyConverter->convert($BL_Price, $currency, $scurrency);
                                                            }
                                                        }
                                                        $cruises[$counter]['BL_PricePublish'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]["BL_PricePublish_plain"][$x] = 0;
                                                        $cruises[$counter]['balcony'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]["balcony_plain"][$x] = 0;
                                                        $cruises[$counter]["BL_Price_plain"][$x] = 0;
                                                    } else {
                                                        if ($BL_Price > 0) {
                                                            if ($currency != $scurrency) {
                                                                $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currency, $scurrency);
                                                                $BL_Price = $CurrencyConverter->convert($BL_Price, $currency, $scurrency);
                                                            }
                                                        }
                                                        $cruises[$counter]['BL_PricePublish'][$x] = $filter->filter($BL_PricePublish);
                                                        $cruises[$counter]["BL_PricePublish_plain"][$x] = $BL_PricePublish;
                                                        $cruises[$counter]['balcony'][$x] = $filter->filter($BL_Price);
                                                        $cruises[$counter]["balcony_plain"][$x] = $BL_Price;
                                                        $cruises[$counter]["BL_Price_plain"][$x] = $BL_Price;
                                                    }
                                                    if ($ST_Price == 0 or $ST_Price == - 1) {
                                                        if ($ST_Price > 0) {
                                                            if ($currency != $scurrency) {
                                                                $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currency, $scurrency);
                                                                $ST_Price = $CurrencyConverter->convert($ST_Price, $currency, $scurrency);
                                                            }
                                                        }
                                                        $cruises[$counter]['ST_PricePublish'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]["ST_PricePublish_plain"][$x] = 0;
                                                        $cruises[$counter]['suite'][$x] = $translator->translate("N/A");
                                                        $cruises[$counter]['suite_plain'][$x] = 0;
                                                        $cruises[$counter]["ST_Price_plain"][$x] = 0;
                                                    } else {
                                                        if ($ST_Price > 0) {
                                                            $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currency, $scurrency);
                                                            $ST_Price = $CurrencyConverter->convert($ST_Price, $currency, $scurrency);
                                                        }
                                                        $cruises[$counter]['ST_PricePublish'][$x] = $filter->filter($ST_PricePublish);
                                                        $cruises[$counter]["ST_PricePublish_plain"][$x] = $ST_PricePublish;
                                                        $cruises[$counter]['suite'][$x] = $filter->filter($ST_Price);
                                                        $cruises[$counter]['suite_plain'][$x] = $ST_Price;
                                                        $cruises[$counter]["ST_Price_plain"][$x] = $ST_Price;
                                                    }
                                                }
                                            }
                                        }
                                        if ($IN_Price > 0) {
                                            if ($currency != $scurrency) {
                                                $cruisesfrom = $CurrencyConverter->convert($cruisesfrom, $currency, $scurrency);
                                                $cruisesfrom_publish = $CurrencyConverter->convert($cruisesfrom_publish, $currency, $scurrency);
                                            }
                                        }
                                        //
                                        // Package List
                                        //
                                        $raw3 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pac="http://services.rccl.com/Interfaces/PackageList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha"><soapenv:Header/><soapenv:Body><pac:getPackageList>
                                          <alp:OTA_CruisePkgAvailRQ TimeStamp="2008-07-17T12:44:44.866-04:00" Target="Test" Version="1.0" SequenceNmbr="1" PrimaryLangID="en" RetransmissionIndicator="false" MoreIndicator="true" MaxResponses="50">
                                             <alp:POS>
                                                <!--1 to 10 repetitions:-->
                                                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                                                    <alp:RequestorID ID="369567" ID_Context="AGENCY1" Type="11"/>
                                                    <alp:BookingChannel Type="7">
                                                        <alp:CompanyName CompanyShortName="COSTAMAR"/>
                                                    </alp:BookingChannel>
                                                </alp:Source>
                                                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                                                    <alp:RequestorID ID="369567" ID_Context="AGENCY2" Type="11"/>
                                                    <alp:BookingChannel Type="7">
                                                        <alp:CompanyName CompanyShortName="COSTAMAR"/>
                                                    </alp:BookingChannel>
                                                </alp:Source>
                                                <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                                                    <alp:RequestorID ID="369567" ID_Context="AGENT1" Type="11"/>
                                                    <alp:BookingChannel Type="7">
                                                        <alp:CompanyName CompanyShortName="COSTAMAR"/>
                                                    </alp:BookingChannel>
                                                </alp:Source>
                                             </alp:POS>
                                             <!--Optional:-->
                                             <alp:GuestCounts>
                                                <!--1 to 9 repetitions:-->
                                                <alp:GuestCount Age="30" Quantity="1"/>
                                                <alp:GuestCount Age="5" Quantity="1"/>
                                             </alp:GuestCounts>
                                             <!--Optional:-->
                                             <alp:SailingInfo>
                                                <!--Optional:-->
                                                <alp:SelectedSailing ListOfSailingDescriptionCode="' . $ListOfSailingDescriptionCode . '" Start="' . $departureFrom . '" Duration="' . $Duration . '" Status="' . $Status . '" PortsOfCallQuantity="' . $PortsOfCallQuantity . '">
                                                   <alp:CruiseLine VendorCode="' . $VendorCode . '" ShipCode="' . $ShipCode . '"/>
                                                   <!--Optional:-->
                                                   <alp:Region RegionCode="' . $RegionCode . '" SubRegionCode="' . $SubRegionCode . '"/>
                                                   <!--Optional:-->
                                                   <alp:DeparturePort LocationCode="' . $DeparturePortLocationCode . '"/>
                                                   <!--Optional:-->
                                                   <alp:ArrivalPort LocationCode="' . $ArrivalPortLocationCode . '"/>
                                                </alp:SelectedSailing>
                                                <!--Optional:-->
                                                <alp:InclusivePackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="' . $InclusiveIndicator . '"/>
                                             </alp:SailingInfo>
                                             <!--1 to 8 repetitions:-->
                                             <alp:PackageOption PackageTypeCode="0" CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="' . $InclusiveIndicator . '"/>
                                          </alp:OTA_CruisePkgAvailRQ>
                                       </pac:getPackageList>
                                    </soapenv:Body>
                                 </soapenv:Envelope>';
                                        
                                        $ch3 = curl_init();
                                        curl_setopt($ch3, CURLOPT_URL, $cruisesroyalcaribbeanServiceURL . 'Reservation_FITWeb/sca/PackageList');
                                        curl_setopt($ch3, CURLOPT_HEADER, false);
                                        curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
                                        curl_setopt($ch3, CURLOPT_VERBOSE, false);
                                        curl_setopt($ch3, CURLOPT_POST, true);
                                        curl_setopt($ch3, CURLOPT_POSTFIELDS, $raw3);
                                        curl_setopt($ch3, CURLOPT_USERPWD, $cruisesroyalcaribbeanusername . ":" . $cruisesroyalcaribbeanpassword);
                                        curl_setopt($ch3, CURLOPT_CONNECTTIMEOUT, $cruisesroyalcaribbeanConnetionTimeout);
                                        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch3, CURLOPT_ENCODING, 'gzip');
                                        $response3 = curl_exec($ch3);
                                        $error = curl_error($ch3);
                                        $headers = curl_getinfo($ch3);
                                        curl_close($ch3);
                                        error_log("\r\nRCCL Response3 - $response3 \r\n", 3, "/srv/www/htdocs/error_log");
                                        $inputDoc = new DOMDocument();
                                        $inputDoc->loadXML($response3);
                                        $Envelope = $inputDoc->getElementsByTagName("Envelope");
                                        $Body = $Envelope->item(0)->getElementsByTagName("Body");
                                        $getPackageListResponse = $Body->item(0)->getElementsByTagName("getPackageListResponse");
                                        if ($getPackageListResponse->length > 0) {
                                            $OTA_CruisePkgAvailRS = $getPackageListResponse->item(0)->getElementsByTagName("OTA_CruisePkgAvailRS");
                                            if ($OTA_CruisePkgAvailRS->length > 0) {
                                                $TPA_Extensions = $OTA_CruisePkgAvailRS->item(0)->getElementsByTagName("TPA_Extensions");
                                                if ($TPA_Extensions->length > 0) {
                                                    $SailingInfos = $TPA_Extensions->item(0)->getElementsByTagName("SailingInfos");
                                                    if ($SailingInfos->length > 0) {
                                                        $SailingInfo = $SailingInfos->item(0)->getElementsByTagName("SailingInfo");
                                                        if ($SailingInfo->length > 0) {
                                                            for ($xSailingInfo = 0; $xSailingInfo < $SailingInfo->length; $xSailingInfo ++) {
                                                                $CruisePackages = $SailingInfo->item($xSailingInfo)->getElementsByTagName("CruisePackages");
                                                                if ($CruisePackages->length > 0) {
                                                                    $CruisePackage = $CruisePackages->item(0)->getElementsByTagName("CruisePackage");
                                                                    if ($CruisePackage->length > 0) {
                                                                        $CruisePackageCode3 = $CruisePackage->item(0)->getAttribute("CruisePackageCode");
                                                                        $Duration3 = $CruisePackage->item(0)->getAttribute("Duration");
                                                                        $End3 = $CruisePackage->item(0)->getAttribute("End");
                                                                        $PackageTypeCode3 = $CruisePackage->item(0)->getAttribute("PackageTypeCode");
                                                                        $Start3 = $CruisePackage->item(0)->getAttribute("Start");
                                                                        $Description = $CruisePackage->item(0)->getAttribute("Description");
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $nights = str_replace('P', '', $Duration);
                                        $nights = str_replace('N', '', $nights);
                                        $cruises[$counter]['from'] = $filter->filter($cruisesfrom);
                                        $cruises[$counter]["from_plain"] = $cruisesfrom;
                                        $cruises[$counter]['from_publish'] = $filter->filter($cruisesfrom_publish);
                                        $cruises[$counter]["from_plain_publish"] = $cruisesfrom_publish;
                                        $cruises[$counter]['name'] = $Description;
                                        $cruises[$counter]['currency'] = $scurrency;
                                        $cruises[$counter]['length'] = $nights;
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
                        }
                    }
                }
            }
        }
    }
}
error_log("\r\nEOF RCCL\r\n", 3, "/srv/www/htdocs/error_log");
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>