<?php
error_log("\r\nStart Costa - Search Cruises\r\n", 3, "/srv/www/htdocs/error_log");
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
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablecruisescosta' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_costa = $affiliate_id;
} else {
    $affiliate_id_costa = 0;
}
$sql = "select value from settings where name='cruisescostausername' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisescostausername = $row_settings['value'];
}
$sql = "select value from settings where name='cruisescostapassword' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisescostapassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisescostaServiceURL' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaServiceURL = $row['value'];
}
$sql = "select value from settings where name='cruisescostaAgency' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaAgency = $row['value'];
}
$sql = "select value from settings where name='cruisescostaAgency' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaAgency = $row['value'];
}
$sql = "select value from settings where name='cruisescostaSearchSortorder' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='cruisescostabranchs_id' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostabranchs_id = $row['value'];
}
$sql = "select value from settings where name='cruisescostamarkup' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostamarkup = (double) $row['value'];
}
$sql = "select value from settings where name='cruisescostab2cmarkup' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostab2cmarkup = $row['value'];
}
$sql = "select value from settings where name='cruisescostaaffiliates_id' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='cruisescostaConnetionTimeout' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaConnetionTimeout = (int) $row['value'];
}
$sql = "select value from settings where name='cruisescostaCurrency' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaCurrency = $row['value'];
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
$MinDuration = "";
$MaxDuration = "";
if ($length == "all") {
    $MinCruiseLength = 1;
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
    //
    // Paulo
    // TODO: Adults, Children, Currency, Status, Destination ID Filter, Ship Id Filter
    //
    if ((int) $CruiseLineID > 0) {
        $cruiselinefilter = $CruiseLineID;
        $mundocruceroslineid = "";
    } else {
        $cruiselinefilter = "";
    }
    // Departure Port
    if ($departureport != "" and $departureport != "all") {
        if ((int) $PortID > 0) {
            $cruisedepartureportfilter = $departureport;
        } else {
            $cruisedepartureportfilter = "";
        }
    } else {
        $cruisedepartureportfilter = "";
    }
    // Shipid
    if ($ShipID > 0 or $ShipID != "") {
        $cruiseshipidfilter = $ShipID;
    } else {
        $cruiseshipidfilter = "";
    }
    // Region id
    if ($destination != "") {
        if ((int) $cruisedestinationid > 0) {
            $cruisedestinationfilter = $cruisedestinationid;
            // apagar
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
    $raw = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Header>
        <Agency xmlns="http://schemas.costacrociere.com/WebAffiliation">
        <Code>' . $cruisescostaAgency . '</Code>
        </Agency>
        <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
        <Name>' . $cruisescostausername . '</Name>
        <Password>' . $cruisescostapassword . '</Password>
        </Partner>
    </soap:Header>
    <soap:Body>
        <ListAvailableCruises xmlns="http://schemas.costacrociere.com/WebAffiliation">
            <from>' . $departureFrom . '</from>
            <to>' . $departureTo . '</to>
            <destinationCode>' . $cruisedestinationfilter . '</destinationCode>
            <shipCode>' . $cruiseshipidfilter . '</shipCode>
            <portCode>' . $cruisedepartureportfilter . '</portCode>
        </ListAvailableCruises>
    </soap:Body>
    </soap:Envelope>';
    // error_log("\r\n Request - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cruisescostaServiceURL . 'Availability.asmx');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisescostaConnetionTimeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ListAvailableCruises",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    // error_log("\r\nCosta Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_costa');
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
        $ListAvailableCruisesResponse = $Body->item(0)->getElementsByTagName("ListAvailableCruisesResponse");
        if ($ListAvailableCruisesResponse->length > 0) {
            $ListAvailableCruisesResult = $ListAvailableCruisesResponse->item(0)->getElementsByTagName("ListAvailableCruisesResult");
            if ($ListAvailableCruisesResult->length > 0) {
                $Cruise = $ListAvailableCruisesResult->item(0)->getElementsByTagName("Cruise");
                if ($Cruise->length > 0) {
                    for ($i = 0; $i < $Cruise->length; $i ++) {
                        $Code = $Cruise->item($i)->getElementsByTagName("Code");
                        if ($Code->length > 0) {
                            $Code = $Code->item(0)->nodeValue;
                        } else {
                            $Code = "";
                        }
                        $Description = $Cruise->item($i)->getElementsByTagName("Description");
                        if ($Description->length > 0) {
                            $Description = $Description->item(0)->nodeValue;
                        } else {
                            $Description = "";
                        }
                        $Availability = $Cruise->item($i)->getElementsByTagName("Availability");
                        if ($Availability->length > 0) {
                            $Availability = $Availability->item(0)->nodeValue;
                        } else {
                            $Availability = "";
                        }
                        $Sellability = $Cruise->item($i)->getElementsByTagName("Sellability");
                        if ($Sellability->length > 0) {
                            $Sellability = $Sellability->item(0)->nodeValue;
                        } else {
                            $Sellability = "";
                        }
                        $DepartureDate = $Cruise->item($i)->getElementsByTagName("DepartureDate");
                        if ($DepartureDate->length > 0) {
                            $DepartureDate = $DepartureDate->item(0)->nodeValue;
                        } else {
                            $DepartureDate = "";
                        }
                        $Duration = $Cruise->item($i)->getElementsByTagName("Duration");
                        if ($Duration->length > 0) {
                            $Duration = $Duration->item(0)->nodeValue;
                        } else {
                            $Duration = "";
                        }
                        $MaxOccupancy = $Cruise->item($i)->getElementsByTagName("MaxOccupancy");
                        if ($MaxOccupancy->length > 0) {
                            $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
                        } else {
                            $MaxOccupancy = "";
                        }
                        $Destination = $Cruise->item($i)->getElementsByTagName("Destination");
                        if ($Destination->length > 0) {
                            $DestinationCode = $Destination->item(0)->getElementsByTagName("Code");
                            if ($DestinationCode->length > 0) {
                                $DestinationCode = $DestinationCode->item(0)->nodeValue;
                            } else {
                                $DestinationCode = "";
                            }
                            $DestinationDescription = $Destination->item(0)->getElementsByTagName("Description");
                            if ($DestinationDescription->length > 0) {
                                $DestinationDescription = $DestinationDescription->item(0)->nodeValue;
                            } else {
                                $DestinationDescription = "";
                            }
                        }
                        $DeparturePort = $Cruise->item($i)->getElementsByTagName("DeparturePort");
                        if ($DeparturePort->length > 0) {
                            $DeparturePortCode = $DeparturePort->item(0)->getElementsByTagName("Code");
                            if ($DeparturePortCode->length > 0) {
                                $DeparturePortCode = $DeparturePortCode->item(0)->nodeValue;
                            } else {
                                $DeparturePortCode = "";
                            }
                            $DeparturePortDescription = $DeparturePort->item(0)->getElementsByTagName("Description");
                            if ($DeparturePortDescription->length > 0) {
                                $DeparturePortDescription = $DeparturePortDescription->item(0)->nodeValue;
                            } else {
                                $DeparturePortDescription = "";
                            }
                        }
                        $Ship = $Cruise->item($i)->getElementsByTagName("Ship");
                        if ($Ship->length > 0) {
                            $ShipCode = $Ship->item(0)->getElementsByTagName("Code");
                            if ($ShipCode->length > 0) {
                                $ShipCode = $ShipCode->item(0)->nodeValue;
                            } else {
                                $ShipCode = "";
                            }
                            $ShipName = $Ship->item(0)->getElementsByTagName("Name");
                            if ($ShipName->length > 0) {
                                $ShipName = $ShipName->item(0)->nodeValue;
                            } else {
                                $ShipName = "";
                            }
                            $ShipURL = $Ship->item(0)->getElementsByTagName("URL");
                            if ($ShipURL->length > 0) {
                                $ShipURL = $ShipURL->item(0)->nodeValue;
                            } else {
                                $ShipURL = "";
                            }
                            $ShipCabins = $Ship->item(0)->getElementsByTagName("Cabins");
                            if ($ShipCabins->length > 0) {
                                $ShipCabins = $ShipCabins->item(0)->nodeValue;
                            } else {
                                $ShipCabins = "";
                            }
                            $ShipCrew = $Ship->item(0)->getElementsByTagName("Crew");
                            if ($ShipCrew->length > 0) {
                                $ShipCrew = $ShipCrew->item(0)->nodeValue;
                            } else {
                                $ShipCrew = "";
                            }
                            $ShipGuests = $Ship->item(0)->getElementsByTagName("Guests");
                            if ($ShipGuests->length > 0) {
                                $ShipGuests = $ShipGuests->item(0)->nodeValue;
                            } else {
                                $ShipGuests = "";
                            }
                            $ShipWidth = $Ship->item(0)->getElementsByTagName("Width");
                            if ($ShipWidth->length > 0) {
                                $ShipWidth = $ShipWidth->item(0)->nodeValue;
                            } else {
                                $ShipWidth = "";
                            }
                            $ShipLength = $Ship->item(0)->getElementsByTagName("Length");
                            if ($ShipLength->length > 0) {
                                $ShipLength = $ShipLength->item(0)->nodeValue;
                            } else {
                                $ShipLength = "";
                            }
                            $ShipTonnage = $Ship->item(0)->getElementsByTagName("Tonnage");
                            if ($ShipTonnage->length > 0) {
                                $ShipTonnage = $ShipTonnage->item(0)->nodeValue;
                            } else {
                                $ShipTonnage = "";
                            }
                            $ShipMaxSpeed = $Ship->item(0)->getElementsByTagName("MaxSpeed");
                            if ($ShipMaxSpeed->length > 0) {
                                $ShipMaxSpeed = $ShipMaxSpeed->item(0)->nodeValue;
                            } else {
                                $ShipMaxSpeed = "";
                            }
                            $ShipYearOfLaunch = $Ship->item(0)->getElementsByTagName("YearOfLaunch");
                            if ($ShipYearOfLaunch->length > 0) {
                                $ShipYearOfLaunch = $ShipYearOfLaunch->item(0)->nodeValue;
                            } else {
                                $ShipYearOfLaunch = "";
                            }
                            $ShipMonthOfLaunch = $Ship->item(0)->getElementsByTagName("MonthOfLaunch");
                            if ($ShipMonthOfLaunch->length > 0) {
                                $ShipMonthOfLaunch = $ShipMonthOfLaunch->item(0)->nodeValue;
                            } else {
                                $ShipMonthOfLaunch = "";
                            }
                        }
                        $ImmediateConfirm = $Cruise->item($i)->getElementsByTagName("ImmediateConfirm");
                        if ($ImmediateConfirm->length > 0) {
                            $IsImmediateConfirm = $ImmediateConfirm->item(0)->getElementsByTagName("IsImmediateConfirm");
                            if ($IsImmediateConfirm->length > 0) {
                                $IsImmediateConfirm = $IsImmediateConfirm->item(0)->nodeValue;
                            } else {
                                $IsImmediateConfirm = "";
                            }
                        }
                        $sql = "select name, logo, seo from cruises_lines where cruises_xml14=1";
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
                            error_log("\r\nUnable to find costa cruise line\r\n", 3, "/srv/www/htdocs/error_log");
                            error_log("\r\nCruises Line sql - $sql\r\n", 3, "/srv/www/htdocs/error_log");
                            $cruiseline_name = $Description;
                            $cruiseline_logo = "";
                            $cruiseline_seo = "";
                        }
                        $sql = "select id, name, seo, shiprating from ships where cruises_xml14='" . $ShipCode . "'";
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
                            if ($ship_rating == 99) {
                                $ship_rating = 0;
                            }
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
                            error_log("\r\nCosta Cruises Ships - $sql\r\n", 3, "/srv/www/htdocs/error_log");
                            error_log("\r\nUnable to find Costa ship $shipid - $ShipCode - $ShipName\r\n", 3, "/srv/www/htdocs/error_log");
                            $shipname = $ShipName;
                            $cruises[$counter]["images"][0] = "";
                            $ship_id = 0;
                            $ship_rating = "";
                            $ship_seo = "";
                            $ship = 0;
                        }
                        // error_log("\r\nCosta Ship Id - $ship_id\r\n", 3, "/srv/www/htdocs/error_log");
                        $cruises[$counter]["id"] = $counter;
                        $cruises[$counter]["seo"] = $ship_seo;
                        // TODO
                        // error_log("\r\nUnable to find tourico cruise line TODO - Check - $cruiselineid - alterar para id, db cruises_lines \r\n", 3, "/srv/www/htdocs/error_log");
                        $cruises[$counter]["cruise_line_id"] = $Code;
                        $cruises[$counter]["quote_id"] = md5(uniqid($session_id, true)) . "-14-" . $counter;
                        $cruises[$counter]["ship"]["id"] = $ship_id;
                        $cruises[$counter]["ship"]["seo"] = $ship_seo;
                        $cruises[$counter]['ship']["name"] = utf8_encode(htmlentities($shipname, ENT_QUOTES));
                        $cruises[$counter]["ship"]["rating"] = $ship_rating;
                        $cruises[$counter]["cruiseline"]["logo"] = $cruiseline_logo;
                        $cruises[$counter]["cruiseline"]["name"] = utf8_encode(htmlentities($cruiseline_name, ENT_QUOTES));
                        $cruises[$counter]["cruiseline"]["seo"] = $cruiseline_seo;
                        
                        $cruisesfrom = 0;
                        $cruisesfrom_publish = 0;
                        // B2C Price
                        $IN_PricePublish = 1000; // Displays the Inside cabin publish price.
                        $ST_PricePublish = 1300; // Displays the suite cabin publish price.
                        $BL_PricePublish = 1200; // Displays the balcony cabin publish price.
                        $OV_PricePublish = 1150; // Displays the ocean view cabin publish price.
                                                 // B2B Price
                        $IN_Price = 1000; // Displays the Inside cabin price.
                        $ST_Price = 1300; // Displays the suite cabin price.
                        $BL_Price = 1200; // Displays the balcony cabin price.
                        $OV_Price = 1150; // Displays the ocean view cabin publish price.
                        if ($cruisescostamarkup > 0) {
                            if ((int) $IN_Price > 0) {
                                $IN_Price = number_format($IN_Price + (($IN_Price * $cruisescostamarkup) / 100), 2, '.', '');
                            }
                            if ((int) $ST_Price > 0) {
                                $ST_Price = number_format($ST_Price + (($ST_Price * $cruisescostamarkup) / 100), 2, '.', '');
                            }
                            if ((int) $BL_Price > 0) {
                                $BL_Price = number_format($BL_Price + (($BL_Price * $cruisescostamarkup) / 100), 2, '.', '');
                            }
                            if ((int) $OV_Price > 0) {
                                $OV_Price = number_format($OV_Price + (($OV_Price * $cruisescostamarkup) / 100), 2, '.', '');
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
                        $DepartureDate = date('Y-m-d', strtotime($DepartureDate));
                        $aditionaldays = '+' . $Duration . ' days';
                        $date = date('Y-m-d', strtotime($aditionaldays, strtotime($DepartureDate)));
                        $from_date = explode("-", $DepartureDate);
                        $to_date = explode("-", $date);
                        $cruises[$counter]['product_id'][$i] = md5(uniqid($session_id, true)) . "-" . $counter . "-" . $i;
                        $cruises[$counter]['sailingid'][$i] = $Code;
                        $cruises[$counter]['departure'][$i] = mktime(0, 0, 0, $from_date[1], $from_date[2], $from_date[0]);
                        $cruises[$counter]['arrival'][$i] = mktime(0, 0, 0, $to_date[1], $to_date[2], $to_date[0]);
                        if ($IN_Price == 0 or $IN_Price == - 1) {
                            if ($IN_Price > 0) {
                                if ($cruisescostaCurrency != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $cruisescostaCurrency, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $cruisescostaCurrency, $scurrency);
                                }
                            }
                            $cruises[$counter]['IN_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["IN_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['insidecabin'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["insidecabin_plain"][$i] = 0;
                            $cruises[$counter]["IN_Price_plain"][$i] = 0;
                        } else {
                            if ($IN_Price > 0) {
                                if ($cruisescostaCurrency != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $cruisescostaCurrency, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $cruisescostaCurrency, $scurrency);
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
                                if ($cruisescostaCurrency != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $cruisescostaCurrency, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $cruisescostaCurrency, $scurrency);
                                }
                            }
                            $cruises[$counter]['OV_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["OV_PricePublish_plain"][$i] = $OV_PricePublish;
                            $cruises[$counter]['oceanview'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["oceanview_plain"][$i] = 0;
                            $cruises[$counter]["OV_Price_plain"][$i] = 0;
                        } else {
                            if ($OV_Price > 0) {
                                if ($cruisescostaCurrency != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $cruisescostaCurrency, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $cruisescostaCurrency, $scurrency);
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
                                if ($cruisescostaCurrency != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $cruisescostaCurrency, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $cruisescostaCurrency, $scurrency);
                                }
                            }
                            $cruises[$counter]['BL_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["BL_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['balcony'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["balcony_plain"][$i] = 0;
                            $cruises[$counter]["BL_Price_plain"][$i] = 0;
                        } else {
                            if ($BL_Price > 0) {
                                if ($cruisescostaCurrency != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $cruisescostaCurrency, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $cruisescostaCurrency, $scurrency);
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
                                if ($cruisescostaCurrency != $scurrency) {
                                    $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $cruisescostaCurrency, $scurrency);
                                    $ST_Price = $CurrencyConverter->convert($ST_Price, $cruisescostaCurrency, $scurrency);
                                }
                            }
                            $cruises[$counter]['ST_PricePublish'][$i] = $translator->translate("N/A");
                            $cruises[$counter]["ST_PricePublish_plain"][$i] = 0;
                            $cruises[$counter]['suite'][$i] = $translator->translate("N/A");
                            $cruises[$counter]['suite_plain'][$i] = 0;
                            $cruises[$counter]["ST_Price_plain"][$i] = 0;
                        } else {
                            if ($ST_Price > 0) {
                                $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $cruisescostaCurrency, $scurrency);
                                $ST_Price = $CurrencyConverter->convert($ST_Price, $cruisescostaCurrency, $scurrency);
                            }
                            $cruises[$counter]['ST_PricePublish'][$i] = $filter->filter($ST_PricePublish);
                            $cruises[$counter]["ST_PricePublish_plain"][$i] = $ST_PricePublish;
                            $cruises[$counter]['suite'][$i] = $filter->filter($ST_Price);
                            $cruises[$counter]['suite_plain'][$i] = $ST_Price;
                            $cruises[$counter]["ST_Price_plain"][$i] = $ST_Price;
                        }
                        if ($IN_Price > 0) {
                            if ($cruisescostaCurrency != $scurrency) {
                                $cruisesfrom = $CurrencyConverter->convert($cruisesfrom, $cruisescostaCurrency, $scurrency);
                                $cruisesfrom_publish = $CurrencyConverter->convert($cruisesfrom_publish, $cruisescostaCurrency, $scurrency);
                            }
                        }
                        $cruises[$counter]['from'] = $filter->filter($cruisesfrom);
                        $cruises[$counter]["from_plain"] = $cruisesfrom;
                        $cruises[$counter]['from_publish'] = $filter->filter($cruisesfrom_publish);
                        $cruises[$counter]["from_plain_publish"] = $cruisesfrom_publish;
                        $cruises[$counter]['name'] = $Description;
                        $cruises[$counter]['currency'] = $scurrency;
                        $cruises[$counter]['length'] = $Duration;
                        $cruises[$counter]['cruise_destination_id'] = $DestinationCode;
                        $cruises[$counter]['ItineraryId'] = 0;
                        $cruises[$counter]['ShipRating'] = "";
                        $cruises[$counter]['departure']['portid'] = $DeparturePortCode;
                        $cruises[$counter]['departure']['portname'] = $DeparturePortDescription;
                        // $cruises[$counter]['segments'] = $segments;
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
error_log("\r\nEOF Costa \r\n", 3, "/srv/www/htdocs/error_log");
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>