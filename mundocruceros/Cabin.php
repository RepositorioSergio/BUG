<?php
// Cruises Tourico
$scurrency = strtoupper($currency);
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat);
$db = new \Laminas\Db\Adapter\Adapter($config);
error_log("\r\nStart Mundo Cruceros Cabin\r\n", 3, "/srv/www/htdocs/error_log");
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
$sql = "select value from settings where name='mundocrucerosuserid' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosuserid = $row['value'];
}
$sql = "select value from settings where name='mundocrucerosStatusLiveTest' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosStatusLiveTest = $row['value'];
}
$sql = "select value from settings where name='mundocruceroslineid' and affiliate_id=$affiliate_id_mundocruceros";
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
$sql = "select value from settings where name='mundocrucerosCurrencyCode' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosCurrencyCode = $row['value'];
}
$sql = "select cruises_xml13 from cruises_regions where seo='" . $destination . "'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisedestinationid = $row_settings["cruises_xml13"];
}
foreach ($data as $key => $value) {
    if ($quote == $value['quote_id']) {
        $cruise_line_id = $value['cruise_line_id'];
        $cruise_destination_id = $value['cruise_destination_id'];
        $ship_id = $value['ship']['id'];
        $sessionkey = $value['sessionkey'];
        $resultno_parent_search = $value['resultno'];
        foreach ($value['product_id'] as $productkey => $productvalue) {
            // error_log("\r\n$productvalue = $product\r\n", 3, "/srv/www/htdocs/error_log");
            if ($productvalue == $product) {
                $sailing_id = $value['sailingid'][$productkey];
                // error_log("\r\nMundo Cruceros Data:" . print_r($data, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
            }
        }
        break;
    }
}
$resultno = $selectedcabin['cabin']['resultno'];
$gradeno = $selectedcabin['cabin']['gradeno'];
if ($cruise_line_id > 0) {
    $hasstate = $cabinsearchsettings['state'] === 'true' ? true : false;
    $issenior = $cabinsearchsettings['senior'] === 'true' ? true : false;
    $isinterline = $cabinsearchsettings['interline'] === 'true' ? true : false;
    $ismilitary = $cabinsearchsettings['military'] === 'true' ? true : false;
    //
    // RAW, Request
    //
    $raw = 'xml=<?xml version="1.0"?><request><auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" /><method action="getcabins" sessionkey="' . $sessionkey . '" resultno="' . $resultno . '" gradeno="' . $gradeno . '" status="' . $mundocrucerosStatusLiveTest . '" /></request>';

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
    //error_log("\r\nMundo Cruceros Cabin Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_mundocruceros');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'Cabins.php',
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
                $gradeno = $method->item(0)->getAttribute("gradeno");
                $resultno = $method->item(0)->getAttribute("resultno");
                $sessionkey = $method->item(0)->getAttribute("sessionkey");
            }
        }
        $results = $response->item(0)->getElementsByTagName("results");
        if ($results->length > 0) {
            $cabin = $results->item(0)->getElementsByTagName("cabin");
            if ($cabin->length > 0) {
                for ($i = 0; $i < $cabin->length; $i ++) {
                    $bathdescription = $cabin->item($i)->getAttribute("bathdescription");
                    $bedcode = $cabin->item($i)->getAttribute("bedcode");
                    $beddescription = $cabin->item($i)->getAttribute("beddescription");
                    $cabingrade = $cabin->item($i)->getAttribute("cabingrade");
                    $cabinid = $cabin->item($i)->getAttribute("cabinid");
                    $decks[$i]['cabinnumber'] = $cabin->item($i)->getAttribute("cabinno");
                    $deckcode = $cabin->item($i)->getAttribute("deckcode");
                    $deckname = $cabin->item($i)->getAttribute("deckname");
                    $farecode = $cabin->item($i)->getAttribute("farecode");
                    $guaranteed = $cabin->item($i)->getAttribute("guaranteed");
                    $location = $cabin->item($i)->getAttribute("location");
                    $maxguests = $cabin->item($i)->getAttribute("maxguests");
                    $minguests = $cabin->item($i)->getAttribute("minguests");
                    $modified = $cabin->item($i)->getAttribute("modified");
                    $resultno = $cabin->item($i)->getAttribute("resultno");
                    $shipside = $cabin->item($i)->getAttribute("shipside");
                    $x1 = $cabin->item($i)->getAttribute("x1");
                    $x2 = $cabin->item($i)->getAttribute("x2");
                    $y1 = $cabin->item($i)->getAttribute("y1");
                    $y2 = $cabin->item($i)->getAttribute("y2");
                    $deck = $cabin->item($i)->getElementsByTagName("deck");
                    if ($deck->length > 0) {
                        $id = $deck->item(0)->getAttribute("id");
                        $name = $deck->item(0)->getAttribute("name");
                        $imageid = $deck->item(0)->getAttribute("imageid");
                        $imageurl = $deck->item(0)->getAttribute("imageurl");
                        $sortorder = $deck->item(0)->getAttribute("sortorder");
                        $decks[$i]['deckname'] = $name;
                        $decks[$i]['decknumber'] = $id;
                        $sql = "select image from ships_decksimages where ship_id=$ship_id and categorycode='" . $selectedcabin['code'] . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_settings = $statement->execute();
                        $row_settings->buffer();
                        if ($row_settings->valid()) {
                            $row_settings = $row_settings->current();
                            $decks[0]['deckimg'] = $row_settings['image'];
                        }
                    }
                    $bedconfig = $cabin->item($i)->getElementsByTagName("bedconfig");
                    if ($bedconfig->length > 0) {
                        for ($iAux = 0; $iAux < $bedconfig->length; $iAux ++) {
                            $code = $bedconfig->item($iAux)->getAttribute("code");
                            $description = $bedconfig->item($iAux)->getAttribute("description");
                        }
                    }
                }
            }
            $essential = $results->item(0)->getElementsByTagName("essential");
            if ($essential->length > 0) {
                $errataitem = $essential->item(0)->getElementsByTagName("errataitem");
                if ($errataitem->length > 0) {
                    for ($j = 0; $j < $errataitem->length; $j ++) {
                        $type = $errataitem->item($j)->getAttribute("type");
                        $value = $errataitem->item($j)->getAttribute("value");
                    }
                }
            }
        }
    }
}
//
//Dining Options
//
$cabinresult = $resultno;
$raw3 = 'xml=<?xml version="1.0"?>
<request>
  <auth password="' . $mundocrucerospassword . '" username="' . $mundocrucerosusername . '" />
  <method action="getdetail" type="cruise" resultno="' . $resultno_parent_search . '" sessionkey="' . $sessionkey . '" status="' . $mundocrucerosStatusLiveTest . '" resultkey="default">
    <gradelist>
        <grade gradeno="' . $gradeno . '" cabinresult="' . $cabinresult . '" />
    </gradelist>
  </method>
</request>';
error_log("\r\nMundo Cruceros Cabin RAW - $raw3 \r\n", 3, "/srv/www/htdocs/error_log");
$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, $mundocrucerosServiceURL);
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch3, CURLOPT_HEADER, false);
curl_setopt($ch3, CURLOPT_VERBOSE, false);
curl_setopt($ch3, CURLOPT_POST, true);
curl_setopt($ch3, CURLOPT_POSTFIELDS, $raw3);
curl_setopt($ch3, CURLOPT_ENCODING, "gzip, deflate");
curl_setopt($ch3, CURLOPT_CONNECTTIMEOUT, $mundocrucerosConnetionTimeout);
curl_setopt($ch3, CURLOPT_HTTPHEADER, array(
    "Content-type: application/x-www-form-urlencoded",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw3)
));
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
$response3 = curl_exec($ch3);
$error = curl_error($ch3);
$headers = curl_getinfo($ch3);
curl_close($ch3);
error_log("\r\nMundo Cruceros Cabin Response3 - $response3 \r\n", 3, "/srv/www/htdocs/error_log");
$inputDoc = new DOMDocument();
$inputDoc->loadXML($response3);
$response = $inputDoc->getElementsByTagName("response");
$sessionkey = $response->item(0)->getAttribute("sessionkey");
$success = $response->item(0)->getAttribute("success");
if ($success == 'Y') {
    $request = $response->item(0)->getElementsByTagName("request");
    if ($request->length > 0) {
        $method = $request->item(0)->getElementsByTagName("method");
        if ($method->length > 0) {
            $resultno = $method->item(0)->getAttribute("resultno");
            $sessionkey = $method->item(0)->getAttribute("sessionkey");
            $gradelist = $method->item(0)->getElementsByTagName("gradelist");
            if ($gradelist->length > 0) {
                $grade = $gradelist->item(0)->getElementsByTagName("grade");
                if ($grade->length > 0) {
                    $cabinresult = $grade->item(0)->getAttribute("cabinresult");
                    $gradeno = $grade->item(0)->getAttribute("gradeno");
                }
            }
        }
    }
    $results = $response->item(0)->getElementsByTagName("results");
    if ($results->length > 0) {
        $cruise = $results->item(0)->getElementsByTagName("cruise");
        if ($cruise->length > 0) {
            $cruiseid = $cruise->item(0)->getAttribute("cruiseid");
            $codetocruiseid = $cruise->item(0)->getAttribute("codetocruiseid");
            $airport = $cruise->item(0)->getAttribute("airport");
            $airportname = $cruise->item(0)->getAttribute("airportname");
            $currency = $cruise->item(0)->getAttribute("currency");
            $departuk = $cruise->item(0)->getAttribute("departuk");
            $description = $cruise->item(0)->getAttribute("description");
            $enddate = $cruise->item(0)->getAttribute("enddate");
            $engine = $cruise->item(0)->getAttribute("engine");
            $hascruiseonly = $cruise->item(0)->getAttribute("hascruiseonly");
            $hasflights = $cruise->item(0)->getAttribute("hasflights");
            $hidden = $cruise->item(0)->getAttribute("hidden");
            $iata = $cruise->item(0)->getAttribute("iata");
            $name = $cruise->item(0)->getAttribute("name");
            $ncf = $cruise->item(0)->getAttribute("ncf");
            $nettprice = $cruise->item(0)->getAttribute("nettprice");
            $price = $cruise->item(0)->getAttribute("price");
            $nights = $cruise->item(0)->getAttribute("nights");
            $nofly = $cruise->item(0)->getAttribute("nofly");
            $nofuelsupplement = $cruise->item(0)->getAttribute("nofuelsupplement");
            $ownerid = $cruise->item(0)->getAttribute("ownerid");
            $priority = $cruise->item(0)->getAttribute("priority");
            $paymentoption = $cruise->item(0)->getAttribute("paymentoption");
            $resultno = $cruise->item(0)->getAttribute("resultno");
            $returndate = $cruise->item(0)->getAttribute("returndate");
            $saildate = $cruise->item(0)->getAttribute("saildate");
            $sailnights = $cruise->item(0)->getAttribute("sailnights");
            $special = $cruise->item(0)->getAttribute("special");
            $startdate = $cruise->item(0)->getAttribute("startdate");
            $startprice = $cruise->item(0)->getAttribute("startprice");
            $stoplive = $cruise->item(0)->getAttribute("stoplive");
            $type = $cruise->item(0)->getAttribute("type");
            $voyagecode = $cruise->item(0)->getAttribute("voyagecode");
            $zoneid = $cruise->item(0)->getAttribute("zoneid");

            $hasdining = false;
            $dining = $cruise->item(0)->getElementsByTagName("dining");
            if ($dining->length > 0) {
                $smoking = $dining->item(0)->getAttribute("smoking");
                $seatings = $dining->item(0)->getElementsByTagName("seatings");
                if ($seatings->length > 0) {
                    $seating = $seatings->item(0)->getElementsByTagName("seating");
                    if ($seating->length > 0) {
                        for ($i=0; $i < $seating->length; $i++) { 
                            $hasdining = true;
                            $dining[$i]['diningcode'] = $seating->item($i)->getAttribute("code");
                            $dining[$z]['diningname'] = $seating->item($i)->getAttribute("description");
                            $dining[$z]['status'] = $seating->item($i)->getAttribute("status");
                        }
                    }
                }
                $tablesizes = $dining->item(0)->getElementsByTagName("tablesizes");
                if ($tablesizes->length > 0) {
                    $tablesize = $tablesizes->item(0)->getElementsByTagName("tablesize");
                    if ($tablesize->length > 0) {
                        $table = "";
                        for ($k=0; $k < $tablesize->length; $k++) { 
                            $table = $tablesize[$k];
                        }
                    }
                }
                if ($hasdining == false) {
                    // Ge dining from previous step
                    
                    // $cabindata -> nao existe - NOTA              
                    foreach ($cabindata as $key => $value) {
                        if ($value['code'] == $cabin) {
                            $dining = $value['dining'];
                            break;
                        }
                    }
                }
            }
            $cabin = $cruise->item(0)->getElementsByTagName("cabin");
            if ($cabin->length > 0) {
                $deckcode = $cabin->item(0)->getAttribute("deckcode");
            }
            $grade = $cruise->item(0)->getElementsByTagName("grade");
            if ($grade->length > 0) {
                $iata = $grade->item(0)->getAttribute("iata");
            }
            $line = $cruise->item(0)->getElementsByTagName("line");
            if ($line->length > 0) {
                $lineid = $line->item(0)->getAttribute("id");
                $linecode = $line->item(0)->getAttribute("code");
                $linename = $line->item(0)->getAttribute("name");
                $lineengine = $line->item(0)->getAttribute("engine");
                $linelogourl = $line->item(0)->getAttribute("logourl");
                $lineniceurl = $line->item(0)->getAttribute("niceurl");
            }
            $ports = $cruise->item(0)->getElementsByTagName("ports");
            if ($ports->length > 0) {
                $port = $ports->item(0)->getElementsByTagName("port");
                if ($port->length > 0) {
                    for ($j=0; $j < $port->length; $j++) { 
                        $id = $port->item($j)->getAttribute("id");
                        $name = $port->item($j)->getAttribute("name");
                    }
                }
            }
            $regions = $cruise->item(0)->getElementsByTagName("regions");
            if ($regions->length > 0) {
                $region = $regions->item(0)->getElementsByTagName("region");
                if ($region->length > 0) {
                    $regionid = $region->item(0)->getAttribute("regionid");
                    $regionname = $region->item(0)->getAttribute("name");
                }
            }
            $ship = $cruise->item(0)->getElementsByTagName("ship");
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
        }
    }
}
error_log("\r\n EOF Mundo Cruceros Cabin \r\n", 3, "/srv/www/htdocs/error_log");
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>