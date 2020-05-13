<?php
// Cruises MUNDOCRUCEROS
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
error_log("\r\n START MUNDOCRUCEROS  \r\n", 3, "/srv/www/htdocs/error_log");
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablemundocruceros' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_mundocruceros = $affiliate_id;
} else {
    $affiliate_id_mundocruceros = 0;
}
$sql = "select value from settings where name='mundocrucerosusername' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerosusername = $row_settings['value'];
}
$sql = "select value from settings where name='mundocrucerospassword' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerospassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='mundocrucerosServiceURL' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURL = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosServiceURLBook' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURLBook = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosSID' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosSID = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosWebsite' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosWebsite = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosSearchSortorder' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosbranchs_id' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosbranchs_id = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosmarkup' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosmarkup = (double) $row['value'];
}
$sql = "select value from settings where name='mundocrucerosb2cmarkup' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosb2cmarkup = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosaffiliates_id' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosConnetionTimeout' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosConnetionTimeout = (int) $row['value'];
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
    $raw2 = 'xml=<?xml version="1.0"?>
    <request>
        <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
        <method action="createsession" sitename="' . $mundocrucerosWebsite . '" currency="USD" status="Live" />
    </request>';

    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $mundocrucerosServiceURL);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_VERBOSE, false);
    curl_setopt($ch2, CURLOPT_HEADER, false);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
    curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, $mundocrucerosConnetionTimeout);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
        "Content-type: application/x-www-form-urlencoded",
        "Accept-Encoding: gzip, deflate",
        "Content-length: " . strlen($raw2)
    ));
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    $response2 = curl_exec($ch2);
    $error2 = curl_error($ch2);
    $headers2 = curl_getinfo($ch2);
    curl_close($ch2);

    $inputDoc2 = new DOMDocument();
    $inputDoc2->loadXML($response2);
    $node = $inputDoc2->getElementsByTagName("response");
    $sessionkey = $node->item(0)->getAttribute("sessionkey");

    $raw = 'xml=<?xml version="1.0"?>
    <request>
    <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
    <method action="simplesearch" type="cruise" sessionkey="' . $sessionkey . '" userid="3035567" sitename="' . $mundocrucerosWebsite . '" currency="USD" status="Live">
        <searchdetail type="cruise" startdate="' . $departureFrom . '" enddate="' . $departureTo . '" lineid="20" adults="2" children="0" sid="' . $mundocrucerosSID . '" resultkey="default">
        </searchdetail>
    </method>
    </request>';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $mundocrucerosConnetionTimeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: application/x-www-form-urlencoded",
        "Accept-Encoding: gzip, deflate",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    //error_log("\r\n Response - $response \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_mundocruceros');
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
        $response = $inputDoc->getElementsByTagName("response");
        $sessionkey = $response->item(0)->getAttribute("sessionkey");
        $success = $response->item(0)->getAttribute("success");
        if ($success == 'Y') {
            $request = $response->item(0)->getElementsByTagName("request");
            if ($request->length > 0) {
                $method = $request->item(0)->getElementsByTagName("method");
                if ($method->length > 0) {
                    $currency = $method->item(0)->getAttribute("currency");
                    $sessionkey = $method->item(0)->getAttribute("sessionkey");
                }
            }
            $results = $response->item(0)->getElementsByTagName("results");
            if ($results->length > 0) {
                $cruise = $results->item(0)->getElementsByTagName("cruise");
                if ($cruise->length > 0) {
                    for ($xCruise=0; $xCruise < $cruise->length; $xCruise++) { 
                        $cruiseid = $cruise->item($xCruise)->getAttribute("cruiseid");
                        $codetocruiseid = $cruise->item($xCruise)->getAttribute("codetocruiseid");
                        $airbalcony = $cruise->item($xCruise)->getAttribute("airbalcony");
                        $airbalconypricecode = $cruise->item($xCruise)->getAttribute("airbalconypricecode");
                        $airinside = $cruise->item($xCruise)->getAttribute("airinside");
                        $airinsidepricecode = $cruise->item($xCruise)->getAttribute("airinsidepricecode");
                        $airoutside = $cruise->item($xCruise)->getAttribute("airoutside");
                        $airoutsidepricecode = $cruise->item($xCruise)->getAttribute("airoutsidepricecode");
                        $airport = $cruise->item($xCruise)->getAttribute("airport");
                        $airportname = $cruise->item($xCruise)->getAttribute("airportname");
                        $airsuite = $cruise->item($xCruise)->getAttribute("airsuite");
                        $airsuitepricecode = $cruise->item($xCruise)->getAttribute("airsuitepricecode");
                        $altvoyagecode = $cruise->item($xCruise)->getAttribute("altvoyagecode");
                        $copyandmedia = $cruise->item($xCruise)->getAttribute("copyandmedia");
                        $cruisebalcony = $cruise->item($xCruise)->getAttribute("cruisebalcony");
                        $cruisebalconypricecode = $cruise->item($xCruise)->getAttribute("cruisebalconypricecode");
                        $cruiseinside = $cruise->item($xCruise)->getAttribute("cruiseinside");
                        $cruiseinsidepricecode = $cruise->item($xCruise)->getAttribute("cruiseinsidepricecode");
                        $cruiseoutside = $cruise->item($xCruise)->getAttribute("cruiseoutside");
                        $cruiseoutsidepricecode = $cruise->item($xCruise)->getAttribute("cruiseoutsidepricecode");
                        $cruisesuite = $cruise->item($xCruise)->getAttribute("cruisesuite");
                        $cruisesuitepricecode = $cruise->item($xCruise)->getAttribute("cruisesuitepricecode");
                        $currency = $cruise->item($xCruise)->getAttribute("currency");
                        $departuk = $cruise->item($xCruise)->getAttribute("departuk");
                        $description = $cruise->item($xCruise)->getAttribute("description");
                        $displaycruiseonly = $cruise->item($xCruise)->getAttribute("displaycruiseonly");
                        $domesticdeparture = $cruise->item($xCruise)->getAttribute("domesticdeparture");
                        $enddate = $cruise->item($xCruise)->getAttribute("enddate");
                        $engine = $cruise->item($xCruise)->getAttribute("engine");
                        $enginesource = $cruise->item($xCruise)->getAttribute("enginesource");
                        $groupids = $cruise->item($xCruise)->getAttribute("groupids");
                        $hascruiseonly = $cruise->item($xCruise)->getAttribute("hascruiseonly");
                        $hasflights = $cruise->item($xCruise)->getAttribute("hasflights");
                        $hidden = $cruise->item($xCruise)->getAttribute("hidden");
                        $iata = $cruise->item($xCruise)->getAttribute("iata");
                        $localpricing = $cruise->item($xCruise)->getAttribute("localpricing");
                        $marketid = $cruise->item($xCruise)->getAttribute("marketid");
                        $cruisename = $cruise->item($xCruise)->getAttribute("name");
                        $ncf = $cruise->item($xCruise)->getAttribute("ncf");
                        $nettprice = $cruise->item($xCruise)->getAttribute("nettprice");
                        $price = $cruise->item($xCruise)->getAttribute("price");
                        $nights = $cruise->item($xCruise)->getAttribute("nights");
                        $nofly = $cruise->item($xCruise)->getAttribute("nofly");
                        $nofuelsupplement = $cruise->item($xCruise)->getAttribute("nofuelsupplement");
                        $ownerid = $cruise->item($xCruise)->getAttribute("ownerid");
                        $priority = $cruise->item($xCruise)->getAttribute("priority");
                        $privatenotes = $cruise->item($xCruise)->getAttribute("privatenotes");
                        $ratecode = $cruise->item($xCruise)->getAttribute("ratecode");
                        $ratecodedescription = $cruise->item($xCruise)->getAttribute("ratecodedescription");
                        $ratecodeiconurl = $cruise->item($xCruise)->getAttribute("ratecodeiconurl");
                        $resultiscruiseonly = $cruise->item($xCruise)->getAttribute("resultiscruiseonly");
                        $resultkey = $cruise->item($xCruise)->getAttribute("resultkey");
                        $resultno = $cruise->item($xCruise)->getAttribute("resultno");
                        $resultweight = $cruise->item($xCruise)->getAttribute("resultweight");
                        $returndate = $cruise->item($xCruise)->getAttribute("returndate");
                        $returnuk = $cruise->item($xCruise)->getAttribute("returnuk");
                        $roundtrip = $cruise->item($xCruise)->getAttribute("roundtrip");
                        $saildate = $cruise->item($xCruise)->getAttribute("saildate");
                        $sailnights = $cruise->item($xCruise)->getAttribute("sailnights");
                        $scurrency = $cruise->item($xCruise)->getAttribute("scurrency");
                        $seadays = $cruise->item($xCruise)->getAttribute("seadays");
                        $searchdetail = $cruise->item($xCruise)->getAttribute("searchdetail");
                        $searchno = $cruise->item($xCruise)->getAttribute("searchno");
                        $senior = $cruise->item($xCruise)->getAttribute("senior");
                        $soldout = $cruise->item($xCruise)->getAttribute("soldout");
                        $special = $cruise->item($xCruise)->getAttribute("special");
                        $specialsgroup = $cruise->item($xCruise)->getAttribute("specialsgroup");
                        $sprice = $cruise->item($xCruise)->getAttribute("sprice");
                        $startdate = $cruise->item($xCruise)->getAttribute("startdate");
                        $startprice = $cruise->item($xCruise)->getAttribute("startprice");
                        $stoplive = $cruise->item($xCruise)->getAttribute("stoplive");
                        $systemgroup = $cruise->item($xCruise)->getAttribute("systemgroup");
                        $taxes = $cruise->item($xCruise)->getAttribute("taxes");
                        $type = $cruise->item($xCruise)->getAttribute("type");
                        $voyagecode = $cruise->item($xCruise)->getAttribute("voyagecode");
                        $whatsincluded = $cruise->item($xCruise)->getAttribute("whatsincluded");
                        $zoneid = $cruise->item($xCruise)->getAttribute("zoneid");

                        $cruisebalconyprices = $cruise->item($xCruise)->getElementsByTagName("cruisebalconyprices");
                        if ($cruisebalconyprices->length > 0) {
                            $price = $cruisebalconyprices->item(0)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                for ($i=0; $i < $price->length; $i++) { 
                                    $appliesto = $price->item($i)->getAttribute("appliesto");
                                    $marker = $price->item($i)->getAttribute("marker");
                                    $name = $price->item($i)->getAttribute("name");
                                    $runorder = $price->item($i)->getAttribute("runorder");
                                    $type = $price->item($i)->getAttribute("type");
                                    $value = $price->item($i)->getAttribute("value");
                                }
                            }
                        }
                        $cruiseinsideprices = $cruise->item($xCruise)->getElementsByTagName("cruiseinsideprices");
                        if ($cruiseinsideprices->length > 0) {
                            $price = $cruiseinsideprices->item(0)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                for ($i=0; $i < $price->length; $i++) { 
                                    $appliesto = $price->item($i)->getAttribute("appliesto");
                                    $marker = $price->item($i)->getAttribute("marker");
                                    $name = $price->item($i)->getAttribute("name");
                                    $runorder = $price->item($i)->getAttribute("runorder");
                                    $type = $price->item($i)->getAttribute("type");
                                    $value = $price->item($i)->getAttribute("value");
                                }
                            }
                        }
                        $cruiseoutsideprices = $cruise->item($xCruise)->getElementsByTagName("cruiseoutsideprices");
                        if ($cruiseoutsideprices->length > 0) {
                            $price = $cruiseoutsideprices->item(0)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                for ($i=0; $i < $price->length; $i++) { 
                                    $appliesto = $price->item($i)->getAttribute("appliesto");
                                    $marker = $price->item($i)->getAttribute("marker");
                                    $name = $price->item($i)->getAttribute("name");
                                    $runorder = $price->item($i)->getAttribute("runorder");
                                    $type = $price->item($i)->getAttribute("type");
                                    $value = $price->item($i)->getAttribute("value");
                                }
                            }
                        }
                        $cruisesuiteprices = $cruise->item($xCruise)->getElementsByTagName("cruisesuiteprices");
                        if ($cruisesuiteprices->length > 0) {
                            $price = $cruisesuiteprices->item(0)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                for ($i=0; $i < $price->length; $i++) { 
                                    $appliesto = $price->item($i)->getAttribute("appliesto");
                                    $marker = $price->item($i)->getAttribute("marker");
                                    $name = $price->item($i)->getAttribute("name");
                                    $runorder = $price->item($i)->getAttribute("runorder");
                                    $type = $price->item($i)->getAttribute("type");
                                    $value = $price->item($i)->getAttribute("value");
                                }
                            }
                        }
                        $grade = $cruise->item($xCruise)->getElementsByTagName("grade");
                        if ($grade->length > 0) {
                            $cabin = $grade->item(0)->getAttribute("cabin");
                            $rate = $grade->item(0)->getAttribute("rate");
                        }
                        $line = $cruise->item($xCruise)->getElementsByTagName("line");
                        if ($line->length > 0) {
                            $lineid = $line->item(0)->getAttribute("id");
                            $linecode = $line->item(0)->getAttribute("code");
                            $linename = $line->item(0)->getAttribute("name");
                            $lineengine = $line->item(0)->getAttribute("engine");
                            $linelogourl = $line->item(0)->getAttribute("logourl");
                            $lineniceurl = $line->item(0)->getAttribute("niceurl");
                        }
                        $portsArray = array();
                        $count = 0;
                        $ports = $cruise->item($xCruise)->getElementsByTagName("ports");
                        if ($ports->length > 0) {
                            $port = $ports->item(0)->getElementsByTagName("port");
                            if ($port->length > 0) {
                                for ($j=0; $j < $port->length; $j++) { 
                                    $portsArray[$count]['id'] = $port->item($j)->getAttribute("id");
                                    $portsArray[$count]['name'] = $port->item($j)->getAttribute("name");
                                    $count = $count + 1;
                                }
                            }
                        }
                        $prices = $cruise->item($xCruise)->getElementsByTagName("prices");
                        if ($prices->length > 0) {
                            $price = $prices->item(0)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                for ($i=0; $i < $price->length; $i++) { 
                                    $appliesto = $price->item($i)->getAttribute("appliesto");
                                    $marker = $price->item($i)->getAttribute("marker");
                                    $name = $price->item($i)->getAttribute("name");
                                    $runorder = $price->item($i)->getAttribute("runorder");
                                    $type = $price->item($i)->getAttribute("type");
                                    $value = $price->item($i)->getAttribute("value");
                                }
                            }
                        }
                        $regions = $cruise->item($xCruise)->getElementsByTagName("regions");
                        if ($regions->length > 0) {
                            $region = $regions->item(0)->getElementsByTagName("region");
                            if ($region->length > 0) {
                                $regionid = $region->item(0)->getAttribute("regionid");
                                $regionname = $region->item(0)->getAttribute("name");
                            }
                        }
                        $ship = $cruise->item($xCruise)->getElementsByTagName("ship");
                        if ($ship->length > 0) {
                            $shipid = $ship->item(0)->getAttribute("id");
                            $shipcode = $ship->item(0)->getAttribute("code");
                            $shipname = $ship->item(0)->getAttribute("name");
                            $shipimagecaption = $ship->item(0)->getAttribute("imagecaption");
                            $shipimageurl = $ship->item(0)->getAttribute("imageurl");
                            $shipsmallimageurl = $ship->item(0)->getAttribute("smallimageurl");
                            $shipniceurl = $ship->item(0)->getAttribute("niceurl");
                            $shiprating = $ship->item(0)->getAttribute("rating");
                        }
                        $uniqueportids = $cruise->item($xCruise)->getElementsByTagName("uniqueportids");
                        if ($uniqueportids->length > 0) {
                            $portids = "";
                            for ($k=0; $k < $uniqueportids->length; $k++) { 
                                $portids = $uniqueportids[$k];
                            }
                        }
                        $uniqueportnames = $cruise->item($xCruise)->getElementsByTagName("uniqueportnames");
                        if ($uniqueportnames->length > 0) {
                            $portnames = "";
                            for ($y=0; $y < $uniqueportnames->length ; $y++) { 
                                $portnames = $uniqueportnames[$y];
                            }
                        }
                        $sql = "select name, logo, seo from cruises_lines where cruises_xml08='" . $lineid . "'";
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
                            $cruiseline_name = $linename;
                            $cruiseline_logo = $linelogourl;
                            $cruiseline_seo = "";
                        }
                        $sql = "select id, name, seo, shiprating from ships where cruises_xml08='" . $shipid . "'";
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
                        $IN_PricePublish = $cruiseinside; // Displays the Inside cabin publish price.
                        $ST_PricePublish = $cruisesuite; // Displays the suite cabin publish price.
                        $BL_PricePublish = $cruisebalcony; // Displays the balcony cabin publish price.
                        $OV_PricePublish = $cruiseoutside; // Displays the ocean view cabin publish price.
                                                                                                            // B2B Price
                        $IN_Price = $cruiseinside; // Displays the Inside cabin price.
                        $ST_Price = $cruisesuite; // Displays the suite cabin price.
                        $BL_Price = $cruisebalcony; // Displays the balcony cabin price.
                        $OV_Price = $cruiseoutside; // Displays the ocean view cabin publish price.
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

                        $cruises[$counter]['product_id'][$xCruise] = md5(uniqid($session_id, true)) . "-" . $counter . "-" . $xCruise;
                        $cruises[$counter]['departure'][$xCruise] = $saildate;
                        $cruises[$counter]['arrival'][$xCruise] = $returndate;
                        if ($IN_Price == 0 or $IN_Price == - 1) {
                            if ($IN_Price > 0) {
                                if ($currency != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currency, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['IN_PricePublish'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]["IN_PricePublish_plain"][$xCruise] = 0;
                            $cruises[$counter]['insidecabin'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]["insidecabin_plain"][$xCruise] = 0;
                            $cruises[$counter]["IN_Price_plain"][$xCruise] = 0;
                        } else {
                            if ($IN_Price > 0) {
                                if ($currency != $scurrency) {
                                    $IN_PricePublish = $CurrencyConverter->convert($IN_PricePublish, $currency, $scurrency);
                                    $IN_Price = $CurrencyConverter->convert($IN_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['IN_PricePublish'][$xCruise] = $filter->filter($IN_PricePublish);
                            $cruises[$counter]["IN_PricePublish_plain"][$xCruise] = $IN_PricePublish;
                            $cruises[$counter]['insidecabin'][$xCruise] = $filter->filter($IN_Price);
                            $cruises[$counter]['insidecabin_plain'][$xCruise] = $IN_Price;
                            $cruises[$counter]["IN_Price_plain"][$xCruise] = $IN_Price;
                        }
                        if ($OV_Price == 0 or $OV_Price == - 1) {
                            if ($OV_Price > 0) {
                                if ($currency != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currency, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['OV_PricePublish'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]["OV_PricePublish_plain"][$xCruise] = $OV_PricePublish;
                            $cruises[$counter]['oceanview'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]["oceanview_plain"][$xCruise] = 0;
                            $cruises[$counter]["OV_Price_plain"][$xCruise] = 0;
                        } else {
                            if ($OV_Price > 0) {
                                if ($currency != $scurrency) {
                                    $OV_PricePublish = $CurrencyConverter->convert($OV_PricePublish, $currency, $scurrency);
                                    $OV_Price = $CurrencyConverter->convert($OV_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['OV_PricePublish'][$xCruise] = $filter->filter($OV_PricePublish);
                            $cruises[$counter]["OV_PricePublish_plain"][$xCruise] = $OV_PricePublish;
                            $cruises[$counter]['oceanview'][$xCruise] = $filter->filter($OV_Price);
                            $cruises[$counter]["oceanview_plain"][$xCruise] = $OV_Price;
                            $cruises[$counter]["OV_Price_plain"][$xCruise] = $OV_Price;
                        }
                        if ($BL_Price == 0 or $BL_Price == - 1) {
                            if ($BL_Price > 0) {
                                if ($currency != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currency, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['BL_PricePublish'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]["BL_PricePublish_plain"][$xCruise] = 0;
                            $cruises[$counter]['balcony'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]["balcony_plain"][$xCruise] = 0;
                            $cruises[$counter]["BL_Price_plain"][$xCruise] = 0;
                        } else {
                            if ($BL_Price > 0) {
                                if ($currency != $scurrency) {
                                    $BL_PricePublish = $CurrencyConverter->convert($BL_PricePublish, $currency, $scurrency);
                                    $BL_Price = $CurrencyConverter->convert($BL_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['BL_PricePublish'][$xCruise] = $filter->filter($BL_PricePublish);
                            $cruises[$counter]["BL_PricePublish_plain"][$xCruise] = $BL_PricePublish;
                            $cruises[$counter]['balcony'][$xCruise] = $filter->filter($BL_Price);
                            $cruises[$counter]["balcony_plain"][$xCruise] = $BL_Price;
                            $cruises[$counter]["BL_Price_plain"][$xCruise] = $BL_Price;
                        }
                        if ($ST_Price == 0 or $ST_Price == - 1) {
                            if ($ST_Price > 0) {
                                if ($currency != $scurrency) {
                                    $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currency, $scurrency);
                                    $ST_Price = $CurrencyConverter->convert($ST_Price, $currency, $scurrency);
                                }
                            }
                            $cruises[$counter]['ST_PricePublish'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]["ST_PricePublish_plain"][$xCruise] = 0;
                            $cruises[$counter]['suite'][$xCruise] = $translator->translate("N/A");
                            $cruises[$counter]['suite_plain'][$xCruise] = 0;
                            $cruises[$counter]["ST_Price_plain"][$xCruise] = 0;
                        } else {
                            if ($ST_Price > 0) {
                                $ST_PricePublish = $CurrencyConverter->convert($ST_PricePublish, $currency, $scurrency);
                                $ST_Price = $CurrencyConverter->convert($ST_Price, $currency, $scurrency);
                            }
                            $cruises[$counter]['ST_PricePublish'][$xCruise] = $filter->filter($ST_PricePublish);
                            $cruises[$counter]["ST_PricePublish_plain"][$xCruise] = $ST_PricePublish;
                            $cruises[$counter]['suite'][$xCruise] = $filter->filter($ST_Price);
                            $cruises[$counter]['suite_plain'][$xCruise] = $ST_Price;
                            $cruises[$counter]["ST_Price_plain"][$xCruise] = $ST_Price;
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
                        $cruises[$counter]['MapImg'] = $MapImg;
                        $cruises[$counter]['departure']['portid'] = $portsArray[0]['id'];
                        $cruises[$counter]['departure']['portname'] = $portsArray[0]['name'];
                        // $cruises[$counter]['segments'] = $portsArray;
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>