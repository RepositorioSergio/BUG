<?php
// Cruises Mundocruceros
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
error_log("\r\nStart Mundo Cruceros Cabins\r\n", 3, "/srv/www/htdocs/error_log");
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
        $cruise_destination_id = $value['cruise_line_id'];
        $ship_id = $value['ship']['id'];
        $sessionkey = $value['sessionkey'];
        $resultno = $value['resultno'];
        foreach ($value['product_id'] as $productkey => $productvalue) {
            if ($productvalue == $product) {
                $sailing_id = $value['sailingid'][$productkey];
            }
        }
        break;
    }
}
if ($cruise_line_id > 0) {
    $isstate = $tmpstate === 'true' ? true : false;
    $issenior = $senior === 'true' ? true : false;
    $isinterline = $interline === 'true' ? true : false;
    $ismilitary = $military === 'true' ? true : false;
    $ispassengernumber = $tmppassengernumber === 'true' ? true : false;
    //
    // Raw, Request
    //
    $raw = 'xml=<?xml version="1.0"?><request><auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" /><method action="getcabingrades" sessionkey="' . $sessionkey . '" resultno="' . $resultno . '" status="' . $mundocrucerosStatusLiveTest . '" /></request>';
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
    //error_log("\r\n Cabins Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $db = new \Laminas\Db\Adapter\Adapter($config);
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
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
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
                $resultno = $method->item(0)->getAttribute("resultno");
                $sessionkey = $method->item(0)->getAttribute("sessionkey");
            }
        }
        $results = $response->item(0)->getElementsByTagName("results");
        if ($results->length > 0) {
            $grades = $results->item(0)->getElementsByTagName("grades");
            if ($grades->length > 0) {
                $grade = $grades->item(0)->getElementsByTagName("grade");
                if ($grade->length > 0) {
                    $cabincountprice = 0;
                    for ($i = 0; $i < $grade->length; $i ++) {
                        $cabinid = $grade->item($i)->getAttribute("cabinid");
                        $cabincode = $grade->item($i)->getAttribute("cabincode");
                        $currency = $grade->item($i)->getAttribute("currency");
                        $description = $grade->item($i)->getAttribute("description");
                        $destination = $grade->item($i)->getAttribute("destination");
                        $docabinsrequest = $grade->item($i)->getAttribute("docabinsrequest");
                        $farecode = $grade->item($i)->getAttribute("farecode");
                        $farename = $grade->item($i)->getAttribute("farename");
                        $gradeno = $grade->item($i)->getAttribute("gradeno");
                        $grossprice = $grade->item($i)->getAttribute("grossprice");
                        $groupfarecode = $grade->item($i)->getAttribute("groupfarecode");
                        $groupfarename = $grade->item($i)->getAttribute("groupfarename");
                        $gst = $grade->item($i)->getAttribute("gst");
                        $iata = $grade->item($i)->getAttribute("iata");
                        $nda = $grade->item($i)->getAttribute("nda");
                        $oldgrossprice = $grade->item($i)->getAttribute("oldgrossprice");
                        $pastguestind = $grade->item($i)->getAttribute("pastguestind");
                        $nettprice = $grade->item($i)->getAttribute("nettprice");
                        $price = $grade->item($i)->getAttribute("price");
                        $resultno = $grade->item($i)->getAttribute("resultno");
                        $sailingid = $grade->item($i)->getAttribute("sailingid");
                        $scurrency = $grade->item($i)->getAttribute("scurrency");
                        $senior = $grade->item($i)->getAttribute("senior");
                        $sprice = $grade->item($i)->getAttribute("sprice");
                        $taxes = $grade->item($i)->getAttribute("taxes");
                        $type = $grade->item($i)->getAttribute("type");
                        $wasprice = $grade->item($i)->getAttribute("wasprice");
                        $taxnet = $taxes;
                        $sdecks = $grade->item($i)->getElementsByTagName("sdecks");
                        if ($sdecks->length > 0) {
                            $decks = "";
                            $count = 0;
                            for ($iAux4=0; $iAux4 < $sdecks->length; $iAux4++) { 
                                $decks .= $sdecks->item($iAux4)->nodeValue;
                                if ($count < ($sdecks->length - 1)) {
                                    $decks .= ",";
                                }
                                $count = $count + 1;
                            }
                        }
                        $cabintype = $grade->item($i)->getElementsByTagName("cabintype");
                        if ($cabintype->length > 0) {
                            $cabintype_id = $cabintype->item(0)->getAttribute("id");
                            $cabintype_name = $cabintype->item(0)->getAttribute("name");
                            $cabintype_cabincode = $cabintype->item(0)->getAttribute("cabincode");
                            $cabintype_cabincode2 = $cabintype->item(0)->getAttribute("cabincode2");
                            $cabintype_cabintype = $cabintype->item(0)->getAttribute("cabintype");
                            $cabintype_caption = $cabintype->item(0)->getAttribute("caption");
                            $cabintype_colourcode = $cabintype->item(0)->getAttribute("colourcode");
                            $cabintype_deckid = $cabintype->item(0)->getAttribute("deckid");
                            $cabintype_description = $cabintype->item(0)->getAttribute("description");
                            $cabintype_imageurl = $cabintype->item(0)->getAttribute("imageurl");
                            $cabintype_isdefault = $cabintype->item(0)->getAttribute("isdefault");
                            $cabintype_ownerid = $cabintype->item(0)->getAttribute("ownerid");
                            $cabintype_smallimageurl = $cabintype->item(0)->getAttribute("smallimageurl");
                            $cabintype_sortweight = $cabintype->item(0)->getAttribute("sortweight");
                            $cabintype_supercedes = $cabintype->item(0)->getAttribute("supercedes");
                            $cabintype_validfrom = $cabintype->item(0)->getAttribute("validfrom");
                            $cabintype_validto = $cabintype->item(0)->getAttribute("validto");
                        }
                        //
                        try {
                            $db = new \Laminas\Db\Adapter\Adapter($config);
                            $sql = "select name, image, description, stateroom_area, veranda_area, color from ships_cabincategory where ship_id=" . $ship_id . " and categorycode='" . $cabincode . "'";
                            $statement2 = $db->createStatement($sql);
                            $statement2->prepare();
                            $row_cabincategory = $statement2->execute();
                            if ($row_cabincategory->valid()) {
                                $row_cabincategory = $row_cabincategory->current();
                                $Name = $row_cabincategory["name"];
                                $img = $row_cabincategory["image"];
                                $stateroom_area = $row_cabincategory["stateroom_area"];
                                $veranda_area = $row_cabincategory["veranda_area"];
                                $color = $row_cabincategory["color"];
                                $description = $row_cabincategory["description"];
                            }
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (Exception $e) {
                            $logger = new Logger();
                            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                            $logger->addWriter($writer);
                            $logger->info($e->getMessage());
                        }
                        $cabins[$cabinscount]['code'] = $cabincode;
                        $cabins[$cabinscount]['name'] = $Name;
                        $cabins[$cabinscount]['type'] = $type;
                        $cabins[$cabinscount]['description'] = $description;
                        $cabins[$cabinscount]['deckname'] = $decks;
                        $cabins[$cabinscount]['img'] = $img;
                        $cabins[$cabinscount]['isguaranteed'] = "";
                        $cabins[$cabinscount]['clxpolicy'] = "";
                        $cabins[$cabinscount]['dining'] = "";
                        $cabins[$cabinscount]['stateroom_area'] = $stateroom_area;
                        $cabins[$cabinscount]['veranda_area'] = $veranda_area;
                        $cabins[$cabinscount]['color'] = $color;
                        $cabins[$cabinscount]['sessionkey'] = $sessionkey;
                        $cabins[$cabinscount]['resultno'] = $resultno;
                        $cabins[$cabinscount]['gradeno'] = $gradeno;
                        $childreninfo = $grade->item($i)->getElementsByTagName("childreninfo");
                        if ($childreninfo->length > 0) {
                            for ($iAux2 = 0; $iAux2 < $childreninfo->length; $iAux2 ++) {
                                $header = $childreninfo->item($iAux2)->getAttribute("header");
                                $text = $childreninfo->item($iAux2)->getAttribute("text");
                            }
                        }
                        if ($mundocrucerosmarkup > 0) {
                            $price = number_format($price + (($price * $mundocrucerosmarkup) / 100), 2, '.', '');
                        }
                        if ($agent_markup > 0) {
                            $price = number_format($price + (($price * $agent_markup) / 100), 2, '.', '');
                        }
                        if ($mundocrucerosmarkup > 0) {
                            $taxes = number_format($taxes + (($taxes * $mundocrucerosmarkup) / 100), 2, '.', '');
                        }
                        if ($agent_markup > 0) {
                            $taxes = number_format($taxes + (($taxes * $agent_markup) / 100), 2, '.', '');
                        }
                        $rate = $grade->item($i)->getElementsByTagName("rate");
                        if ($rate->length > 0) {
                            $rate_code = $rate->item(0)->getAttribute("code");
                            $rate_name = $rate->item(0)->getAttribute("name");
                            $rate_airavail = $rate->item(0)->getAttribute("airavail");
                            $rate_faretype = $rate->item(0)->getAttribute("faretype");
                            $rate_insuranceavail = $rate->item(0)->getAttribute("insuranceavail");
                            $rate_misccharges = $rate->item(0)->getAttribute("misccharges");
                            $rate_nett = $rate->item(0)->getAttribute("nett");
                            $rate_nonrefundable = $rate->item(0)->getAttribute("nonrefundable");
                            $rate_pastpassenger = $rate->item(0)->getAttribute("pastpassenger");
                            $rate_portcharges = $rate->item(0)->getAttribute("portcharges");
                            $rate_sortweight = $rate->item(0)->getAttribute("sortweight");
                            
                            $insuranceopts = $rate->item(0)->getElementsByTagName("insuranceopts");
                            if ($insuranceopts->length > 0) {
                                $insuranceopts_name = $insuranceopts->item(0)->getAttribute("name");
                                $insuranceopts_type = $insuranceopts->item(0)->getAttribute("type");
                            }
                            $itinerary = $rate->item(0)->getElementsByTagName("itinerary");
                            if ($itinerary->length > 0) {
                                for ($iAux3 = 0; $iAux3 < $itinerary->length; $iAux3 ++) {
                                    $arrivedate = $itinerary->item($iAux3)->getAttribute("arrivedate");
                                    $arrivetime = $itinerary->item($iAux3)->getAttribute("arrivetime");
                                    $day = $itinerary->item($iAux3)->getAttribute("day");
                                    $departdate = $itinerary->item($iAux3)->getAttribute("departdate");
                                    $departtime = $itinerary->item($iAux3)->getAttribute("departtime");
                                    $extrainfo = $itinerary->item($iAux3)->getAttribute("extrainfo");
                                    $itinerarydescription = $itinerary->item($iAux3)->getAttribute("itinerarydescription");
                                    $itineraryname = $itinerary->item($iAux3)->getAttribute("itineraryname");
                                    $latitude = $itinerary->item($iAux3)->getAttribute("latitude");
                                    $longitude = $itinerary->item($iAux3)->getAttribute("longitude");
                                    $shortdescription = $itinerary->item($iAux3)->getAttribute("shortdescription");
                                    $uniqueportid = $itinerary->item($iAux3)->getAttribute("uniqueportid");
                                }
                            }
                            $logindetails = $rate->item(0)->getElementsByTagName("logindetails");
                            if ($logindetails->length > 0) {
                                $logindetails_agency = $logindetails->item(0)->getAttribute("agency");
                                $logindetails_currency = $logindetails->item(0)->getAttribute("currency");
                                $logindetails_system = $logindetails->item(0)->getAttribute("system");
                                $logindetails_username = $logindetails->item(0)->getAttribute("username");
                            }
                        }
                        
                        if ($taxes > 0) {
                            $taxesincluded = 1;
                        } else {
                            $taxesincluded = 0;
                        }
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['resultno'] = $resultno;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['gradeno'] = $gradeno;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['pricetitle'] = $farename;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['taxesincluded'] = $taxesincluded;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['pricepublish'] = $filter->filter($price);
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['price'] = $filter->filter($price);
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['pricenet'] = $nettprice;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['upgradetocategorycode'] = $upgradetocategorycode;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['cabinproductid'] = base64_encode($cabinid);
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['nonrefundable'] = $rate_nonrefundable;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['tax'] = $taxes;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['taxnet'] = $taxnet;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['currencynet'] = $scurrency;
                        $cabins[$cabinscount]['cabin'][$cabincountprice]['currency'] = $scurrency;
                        // $cabins[$cabinscount]['cabin'][$cabincountprice]['ncf'] = $ncf;
                        $cabincountprice ++;
                        $prices = $grade->item($i)->getElementsByTagName("prices");
                        if ($prices->length > 0) {
                            $price = $prices->item(0)->getElementsByTagName("price");
                            if ($price->length > 0) {
                                for ($iAux = 0; $iAux < $price->length; $iAux ++) {
                                    $appliesto = $price->item($iAux)->getAttribute("appliesto");
                                    $marker = $price->item($iAux)->getAttribute("marker");
                                    $name = $price->item($iAux)->getAttribute("name");
                                    $runorder = $price->item($iAux)->getAttribute("runorder");
                                    $type = $price->item($iAux)->getAttribute("type");
                                    $value = $price->item($iAux)->getAttribute("value");
                                }
                            }
                        }
                        $cabinscount ++;
                    }
                }
            }
            $itinerary = $results->item(0)->getElementsByTagName("itinerary");
            if ($itinerary->length > 0) {
                $item = $itinerary->item(0)->getElementsByTagName("item");
                if ($item->length > 0) {
                    for ($j = 0; $j < $item->length; $j ++) {
                        $arrivedate = $item->item($j)->getAttribute("arrivedate");
                        $arrivetime = $item->item($j)->getAttribute("arrivetime");
                        $day = $item->item($j)->getAttribute("day");
                        $departdate = $item->item($j)->getAttribute("departdate");
                        $departtime = $item->item($j)->getAttribute("departtime");
                        $description = $item->item($j)->getAttribute("description");
                        $idlcrossed = $item->item($j)->getAttribute("idlcrossed");
                        $latitude = $item->item($j)->getAttribute("latitude");
                        $longitude = $item->item($j)->getAttribute("longitude");
                        $name = $item->item($j)->getAttribute("name");
                        $orderid = $item->item($j)->getAttribute("orderid");
                        $originalitineraryname = $item->item($j)->getAttribute("originalitineraryname");
                        $ownerid = $item->item($j)->getAttribute("ownerid");
                        $portid = $item->item($j)->getAttribute("portid");
                        $supered_id = $item->item($j)->getAttribute("supered_id");
                        $type = $item->item($j)->getAttribute("type");
                    }
                }
            }
        }
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\nEOF CABINS Mundo Cruceros\r\n", 3, "/srv/www/htdocs/error_log");
?>