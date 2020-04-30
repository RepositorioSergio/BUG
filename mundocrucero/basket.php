<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
echo "COMECOU BASKET<br/>";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
// Start
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

$sessionkey = '0DE96AB9~F7F1i4C40-A6B9-3DB46BA547AA';

$raw = 'xml=<?xml version="1.0"?>
<request>
  <auth password="' . $mundocrucerospassword . '" username="' . $mundocrucerosusername . '" />
  <method action="getbasket" sessionkey="' . $sessionkey . '" status="Live" />
</request>';

echo "<xmp>";
echo $raw;
echo "</xmp>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
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

echo "<xmp>";
echo $response;
echo "</xmp>";


$config = new \Zend\Config\Config(include '../config/autoload/global.mundocruceros.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

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
            $sessionkey = $method->item(0)->getAttribute("sessionkey");
        }
    }
    $results = $response->item(0)->getElementsByTagName("results");
    if ($results->length > 0) {
        $basketitem = $results->item(0)->getElementsByTagName("basketitem");
        if ($basketitem->length > 0) {
            $basketcode = $basketitem->item(0)->getAttribute("basketcode");
            $addedtime = $basketitem->item(0)->getAttribute("addedtime");
            $currency = $basketitem->item(0)->getAttribute("currency");
            $enginename = $basketitem->item(0)->getAttribute("enginename");
            $itemkey = $basketitem->item(0)->getAttribute("itemkey");
            $paymentoption = $basketitem->item(0)->getAttribute("paymentoption");
            $price = $basketitem->item(0)->getAttribute("price");
            $scurrency = $basketitem->item(0)->getAttribute("scurrency");
            $searchno = $basketitem->item(0)->getAttribute("searchno");
            $sprice = $basketitem->item(0)->getAttribute("sprice");
            $startdate = $basketitem->item(0)->getAttribute("startdate");
            $type = $basketitem->item(0)->getAttribute("type");

            $prices = $basketitem->item(0)->getElementsByTagName("prices");
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
            $cardcharges = $basketitem->item(0)->getElementsByTagName("cardcharges");
            if ($cardcharges->length > 0) {
                $cardcharge = $cardcharges->item(0)->getElementsByTagName("cardcharge");
                if ($cardcharge->length > 0) {
                    for ($j=0; $j < $cardcharge->length; $j++) { 
                        $cardname = $cardcharge->item($j)->getAttribute("cardname");
                        $cardtype = $cardcharge->item($j)->getAttribute("cardtype");
                        $chargetype = $cardcharge->item($j)->getAttribute("chargetype");
                        $chargevalue = $cardcharge->item($j)->getAttribute("chargevalue");
                    }
                }
            }
            $deposits = $basketitem->item(0)->getElementsByTagName("deposits");
            if ($deposits->length > 0) {
                $duedate = $deposits->item(0)->getAttribute("duedate");
                $price = $deposits->item(0)->getAttribute("price");
            }
            $item = $basketitem->item(0)->getElementsByTagName("item");
            if ($item->length > 0) {
                $cruiseid = $item->item(0)->getAttribute("cruiseid");
                $codetocruiseid = $item->item(0)->getAttribute("codetocruiseid");
                $airbalcony = $item->item(0)->getAttribute("airbalcony");
                $airbalconypricecode = $item->item(0)->getAttribute("airbalconypricecode");
                $airinside = $item->item(0)->getAttribute("airinside");
                $airinsidepricecode = $item->item(0)->getAttribute("airinsidepricecode");
                $airoutside = $item->item(0)->getAttribute("airoutside");
                $airoutsidepricecode = $item->item(0)->getAttribute("airoutsidepricecode");
                $airport = $item->item(0)->getAttribute("airport");
                $airportname = $item->item(0)->getAttribute("airportname");
                $airsuite = $item->item(0)->getAttribute("airsuite");
                $airsuitepricecode = $item->item(0)->getAttribute("airsuitepricecode");
                $altvoyagecode = $item->item(0)->getAttribute("altvoyagecode");
                $copyandmedia = $item->item(0)->getAttribute("copyandmedia");
                $cruisebalcony = $item->item(0)->getAttribute("cruisebalcony");
                $cruisebalconypricecode = $item->item(0)->getAttribute("cruisebalconypricecode");
                $cruiseinside = $item->item(0)->getAttribute("cruiseinside");
                $cruiseinsidepricecode = $item->item(0)->getAttribute("cruiseinsidepricecode");
                $cruiseoutside = $item->item(0)->getAttribute("cruiseoutside");
                $cruiseoutsidepricecode = $item->item(0)->getAttribute("cruiseoutsidepricecode");
                $cruisesuite = $item->item(0)->getAttribute("cruisesuite");
                $cruisesuitepricecode = $item->item(0)->getAttribute("cruisesuitepricecode");
                $currency = $item->item(0)->getAttribute("currency");
                $departuk = $item->item(0)->getAttribute("departuk");
                $description = $item->item(0)->getAttribute("description");
                $displaycruiseonly = $item->item(0)->getAttribute("displaycruiseonly");
                $domesticdeparture = $item->item(0)->getAttribute("domesticdeparture");
                $enddate = $item->item(0)->getAttribute("enddate");
                $engine = $item->item(0)->getAttribute("engine");
                $enginesource = $item->item(0)->getAttribute("enginesource");
                $groupids = $item->item(0)->getAttribute("groupids");
                $hascruiseonly = $item->item(0)->getAttribute("hascruiseonly");
                $hasflights = $item->item(0)->getAttribute("hasflights");
                $hidden = $item->item(0)->getAttribute("hidden");
                $iata = $item->item(0)->getAttribute("iata");
                $localpricing = $item->item(0)->getAttribute("localpricing");
                $marketid = $item->item(0)->getAttribute("marketid");
                $name = $item->item(0)->getAttribute("name");
                $ncf = $item->item(0)->getAttribute("ncf");
                $nettprice = $item->item(0)->getAttribute("nettprice");
                $price = $item->item(0)->getAttribute("price");
                $nights = $item->item(0)->getAttribute("nights");
                $nofly = $item->item(0)->getAttribute("nofly");
                $nofuelsupplement = $item->item(0)->getAttribute("nofuelsupplement");
                $ownerid = $item->item(0)->getAttribute("ownerid");
                $priority = $item->item(0)->getAttribute("priority");
                $privatenotes = $item->item(0)->getAttribute("privatenotes");
                $ratecode = $item->item(0)->getAttribute("ratecode");
                $ratecodedescription = $item->item(0)->getAttribute("ratecodedescription");
                $ratecodeiconurl = $item->item(0)->getAttribute("ratecodeiconurl");
                $resultiscruiseonly = $item->item(0)->getAttribute("resultiscruiseonly");
                $resultkey = $item->item(0)->getAttribute("resultkey");
                $resultno = $item->item(0)->getAttribute("resultno");
                $resultweight = $item->item(0)->getAttribute("resultweight");
                $returndate = $item->item(0)->getAttribute("returndate");
                $returnuk = $item->item(0)->getAttribute("returnuk");
                $roundtrip = $item->item(0)->getAttribute("roundtrip");
                $saildate = $item->item(0)->getAttribute("saildate");
                $sailnights = $item->item(0)->getAttribute("sailnights");
                $scurrency = $item->item(0)->getAttribute("scurrency");
                $seadays = $item->item(0)->getAttribute("seadays");
                $searchdetail = $item->item(0)->getAttribute("searchdetail");
                $searchno = $item->item(0)->getAttribute("searchno");
                $senior = $item->item(0)->getAttribute("senior");
                $soldout = $item->item(0)->getAttribute("soldout");
                $special = $item->item(0)->getAttribute("special");
                $specialsgroup = $item->item(0)->getAttribute("specialsgroup");
                $sprice = $item->item(0)->getAttribute("sprice");
                $startdate = $item->item(0)->getAttribute("startdate");
                $startprice = $item->item(0)->getAttribute("startprice");
                $stoplive = $item->item(0)->getAttribute("stoplive");
                $systemgroup = $item->item(0)->getAttribute("systemgroup");
                $taxes = $item->item(0)->getAttribute("taxes");
                $type = $item->item(0)->getAttribute("type");
                $voyagecode = $item->item(0)->getAttribute("voyagecode");
                $whatsincluded = $item->item(0)->getAttribute("whatsincluded");
                $zoneid = $item->item(0)->getAttribute("zoneid");

                $cruisebalconyprices = $item->item(0)->getElementsByTagName("cruisebalconyprices");
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
                $cruiseinsideprices = $item->item(0)->getElementsByTagName("cruiseinsideprices");
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
                $cruiseoutsideprices = $item->item(0)->getElementsByTagName("cruiseoutsideprices");
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
                $cruisesuiteprices = $item->item(0)->getElementsByTagName("cruisesuiteprices");
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
                $grade = $item->item(0)->getElementsByTagName("grade");
                if ($grade->length > 0) {
                    $cabin = $grade->item(0)->getAttribute("cabin");
                    $rate = $grade->item(0)->getAttribute("rate");
                }
                $line = $item->item(0)->getElementsByTagName("line");
                if ($line->length > 0) {
                    $lineid = $line->item(0)->getAttribute("id");
                    $linecode = $line->item(0)->getAttribute("code");
                    $linename = $line->item(0)->getAttribute("name");
                    $lineengine = $line->item(0)->getAttribute("engine");
                    $linelogourl = $line->item(0)->getAttribute("logourl");
                    $lineniceurl = $line->item(0)->getAttribute("niceurl");
                }
                $ports = $item->item(0)->getElementsByTagName("ports");
                if ($ports->length > 0) {
                    $port = $ports->item(0)->getElementsByTagName("port");
                    if ($port->length > 0) {
                        for ($j=0; $j < $port->length; $j++) { 
                            $id = $port->item($j)->getAttribute("id");
                            $name = $port->item($j)->getAttribute("name");
                        }
                    }
                }
                $prices = $item->item(0)->getElementsByTagName("prices");
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
                $regions = $item->item(0)->getElementsByTagName("regions");
                if ($regions->length > 0) {
                    $region = $regions->item(0)->getElementsByTagName("region");
                    if ($region->length > 0) {
                        $regionid = $region->item(0)->getAttribute("regionid");
                        $regionname = $region->item(0)->getAttribute("name");
                    }
                }
                $ship = $item->item(0)->getElementsByTagName("ship");
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
                $uniqueportids = $item->item(0)->getElementsByTagName("uniqueportids");
                if ($uniqueportids->length > 0) {
                    $portids = "";
                    for ($k=0; $k < $uniqueportids->length; $k++) { 
                        $portids = $uniqueportids[$k];
                    }
                }
                $uniqueportnames = $item->item(0)->getElementsByTagName("uniqueportnames");
                if ($uniqueportnames->length > 0) {
                    $portnames = "";
                    for ($y=0; $y < $uniqueportnames->length ; $y++) { 
                        $portnames = $uniqueportnames[$y];
                    }
                }
            }
            $prices = $basketitem->item(0)->getElementsByTagName("prices");
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
            //itemdetail
            $itemdetail = $basketitem->item(0)->getElementsByTagName("itemdetail");
            if ($itemdetail->length > 0) {
                $cruiseid = $itemdetail->item(0)->getAttribute("cruiseid");
                $codetocruiseid = $itemdetail->item(0)->getAttribute("codetocruiseid");
                $adults = $itemdetail->item(0)->getAttribute("adults");
                $children = $itemdetail->item(0)->getAttribute("children");
                $airport = $itemdetail->item(0)->getAttribute("airport");
                $airportname = $itemdetail->item(0)->getAttribute("airportname");
                $currency = $itemdetail->item(0)->getAttribute("currency");
                $departuk = $itemdetail->item(0)->getAttribute("departuk");
                $description = $itemdetail->item(0)->getAttribute("description");
                $enddate = $itemdetail->item(0)->getAttribute("enddate");
                $engine = $itemdetail->item(0)->getAttribute("engine");
                $hascruiseonly = $itemdetail->item(0)->getAttribute("hascruiseonly");
                $hasflights = $itemdetail->item(0)->getAttribute("hasflights");
                $hidden = $itemdetail->item(0)->getAttribute("hidden");
                $iata = $itemdetail->item(0)->getAttribute("iata");
                $infants = $itemdetail->item(0)->getAttribute("infants");
                $name = $itemdetail->item(0)->getAttribute("name");
                $nettprice = $itemdetail->item(0)->getAttribute("nettprice");
                $price = $itemdetail->item(0)->getAttribute("price");
                $nights = $itemdetail->item(0)->getAttribute("nights");
                $nofly = $itemdetail->item(0)->getAttribute("nofly");
                $nofuelsupplement = $itemdetail->item(0)->getAttribute("nofuelsupplement");
                $ownerid = $itemdetail->item(0)->getAttribute("ownerid");
                $paymentoption = $itemdetail->item(0)->getAttribute("paymentoption");
                $priority = $itemdetail->item(0)->getAttribute("priority");
                $resultno = $itemdetail->item(0)->getAttribute("resultno");
                $returndate = $itemdetail->item(0)->getAttribute("returndate");
                $saildate = $itemdetail->item(0)->getAttribute("saildate");
                $sailnights = $itemdetail->item(0)->getAttribute("sailnights");
                $special = $itemdetail->item(0)->getAttribute("special");
                $startdate = $itemdetail->item(0)->getAttribute("startdate");
                $stoplive = $itemdetail->item(0)->getAttribute("stoplive");
                $type = $itemdetail->item(0)->getAttribute("type");
                $voyagecode = $itemdetail->item(0)->getAttribute("voyagecode");
                $zoneid = $itemdetail->item(0)->getAttribute("zoneid");

                $cabin = $itemdetail->item(0)->getElementsByTagName("cabin");
                if ($cabin->length > 0) {
                    $deckcode = $cabin->item(0)->getAttribute("deckcode");
                }
                $grade = $itemdetail->item(0)->getElementsByTagName("grade");
                if ($grade->length > 0) {
                    $iata = $grade->item(0)->getAttribute("iata");
                }
                $line = $itemdetail->item(0)->getElementsByTagName("line");
                if ($line->length > 0) {
                    $lineid = $line->item(0)->getAttribute("id");
                    $linecode = $line->item(0)->getAttribute("code");
                    $linename = $line->item(0)->getAttribute("name");
                    $lineengine = $line->item(0)->getAttribute("engine");
                    $linelogourl = $line->item(0)->getAttribute("logourl");
                    $lineniceurl = $line->item(0)->getAttribute("niceurl");
                }
                $ports = $itemdetail->item(0)->getElementsByTagName("ports");
                if ($ports->length > 0) {
                    $port = $ports->item(0)->getElementsByTagName("port");
                    if ($port->length > 0) {
                        for ($j=0; $j < $port->length; $j++) { 
                            $id = $port->item($j)->getAttribute("id");
                            $name = $port->item($j)->getAttribute("name");
                        }
                    }
                }
                $regions = $itemdetail->item(0)->getElementsByTagName("regions");
                if ($regions->length > 0) {
                    $region = $regions->item(0)->getElementsByTagName("region");
                    if ($region->length > 0) {
                        $regionid = $region->item(0)->getAttribute("regionid");
                        $regionname = $region->item(0)->getAttribute("name");
                    }
                }
                $ship = $itemdetail->item(0)->getElementsByTagName("ship");
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
            //reservation
            $reservation = $basketitem->item(0)->getElementsByTagName("reservation");
            if ($reservation->length > 0) {
                $itemdetail = $reservation->item(0)->getElementsByTagName("itemdetail");
                if ($itemdetail->length > 0) {
                    $bookedsectors = $itemdetail->item(0)->getElementsByTagName("bookedsectors");
                    if ($bookedsectors->length > 0) {
                        $bookedsectors = $bookedsectors->item(0)->nodeValue;
                    } else {
                        $bookedsectors = "";
                    }
                }
            }
            //searchdetail
            $searchdetail = $basketitem->item(0)->getElementsByTagName("searchdetail");
            if ($searchdetail->length > 0) {
                $codetocruiseid = $searchdetail->item(0)->getAttribute("codetocruiseid");
                $addins = $searchdetail->item(0)->getAttribute("addins");
                $adults = $searchdetail->item(0)->getAttribute("adults");
                $children = $searchdetail->item(0)->getAttribute("children");
                $infants = $searchdetail->item(0)->getAttribute("infants");
                $allowalldates = $searchdetail->item(0)->getAttribute("allowalldates");
                $classificationid = $searchdetail->item(0)->getAttribute("classificationid");
                $cruiseandflyuk = $searchdetail->item(0)->getAttribute("cruiseandflyuk");
                $cruiseonly = $searchdetail->item(0)->getAttribute("cruiseonly");
                $cruiseonly = $searchdetail->item(0)->getAttribute("cruiseonly");
                $cruiseonlynouk = $searchdetail->item(0)->getAttribute("cruiseonlynouk");
                $cruisepax = $searchdetail->item(0)->getAttribute("cruisepax");
                $cruisetype = $searchdetail->item(0)->getAttribute("cruisetype");
                $cruiseversion = $searchdetail->item(0)->getAttribute("cruiseversion");
                $ctcownerid = $searchdetail->item(0)->getAttribute("ctcownerid");
                $defaulthomecity = $searchdetail->item(0)->getAttribute("defaulthomecity");
                $departuk = $searchdetail->item(0)->getAttribute("departuk");
                $enddate = $searchdetail->item(0)->getAttribute("enddate");
                $endportid = $searchdetail->item(0)->getAttribute("endportid");
                $flycruise = $searchdetail->item(0)->getAttribute("flycruise");
                $forceallocations = $searchdetail->item(0)->getAttribute("forceallocations");
                $groupid = $searchdetail->item(0)->getAttribute("groupid");
                $homecity = $searchdetail->item(0)->getAttribute("homecity");
                $includesenior = $searchdetail->item(0)->getAttribute("includesenior");
                $language = $searchdetail->item(0)->getAttribute("language");
                $lineid = $searchdetail->item(0)->getAttribute("lineid");
                $lowestavailablefare = $searchdetail->item(0)->getAttribute("lowestavailablefare");
                $maxchildage = $searchdetail->item(0)->getAttribute("maxchildage");
                $maxinfantage = $searchdetail->item(0)->getAttribute("maxinfantage");
                $maxresults = $searchdetail->item(0)->getAttribute("maxresults");
                $nights = $searchdetail->item(0)->getAttribute("nights");
                $nofly = $searchdetail->item(0)->getAttribute("nofly");
                $nxcacher = $searchdetail->item(0)->getAttribute("nxcacher");
                $nxfromquote = $searchdetail->item(0)->getAttribute("nxfromquote");
                $parentsid = $searchdetail->item(0)->getAttribute("parentsid");
                $parentsite = $searchdetail->item(0)->getAttribute("parentsite");
                $pastpassenger = $searchdetail->item(0)->getAttribute("pastpassenger");
                $port = $searchdetail->item(0)->getAttribute("port");
                $portid = $searchdetail->item(0)->getAttribute("portid");
                $pricerange = $searchdetail->item(0)->getAttribute("pricerange");
                $processed = $searchdetail->item(0)->getAttribute("processed");
                $promocode = $searchdetail->item(0)->getAttribute("promocode");
                $regionid = $searchdetail->item(0)->getAttribute("regionid");
                $resultkey = $searchdetail->item(0)->getAttribute("resultkey");
                $searchcreatedat = $searchdetail->item(0)->getAttribute("searchcreatedat");
                $searchedon = $searchdetail->item(0)->getAttribute("searchedon");
                $searchno = $searchdetail->item(0)->getAttribute("searchno");
                $shipid = $searchdetail->item(0)->getAttribute("shipid");
                $shipimages = $searchdetail->item(0)->getAttribute("shipimages");
                $sid = $searchdetail->item(0)->getAttribute("sid");
                $sidtype = $searchdetail->item(0)->getAttribute("sidtype");
                $sitename = $searchdetail->item(0)->getAttribute("sitename");
                $source = $searchdetail->item(0)->getAttribute("source");
                $startdate = $searchdetail->item(0)->getAttribute("startdate");
                $startport = $searchdetail->item(0)->getAttribute("startport");
                $startportid = $searchdetail->item(0)->getAttribute("startportid");
                $supplierpromocode = $searchdetail->item(0)->getAttribute("supplierpromocode");
                $systemgroup = $searchdetail->item(0)->getAttribute("systemgroup");
                $tailormadeid = $searchdetail->item(0)->getAttribute("tailormadeid");
                $totalresults = $searchdetail->item(0)->getAttribute("totalresults");
                $type = $searchdetail->item(0)->getAttribute("type");
                $voyagecode = $searchdetail->item(0)->getAttribute("voyagecode");
                $withprices = $searchdetail->item(0)->getAttribute("withprices");
                $withpricesonly = $searchdetail->item(0)->getAttribute("withpricesonly");

                $marketid = $searchdetail->item(0)->getElementsByTagName("marketid");
                if ($marketid->length > 0) {
                    $marketid = $marketid->item(0)->nodeValue;
                } else {
                    $marketid = "";
                }
                $resultcounts = $searchdetail->item(0)->getElementsByTagName("resultcounts");
                if ($resultcounts->length > 0) {
                    $CruiseSearch = $resultcounts->item(0)->getAttribute("CruiseSearch");
                }
                $timings = $searchdetail->item(0)->getElementsByTagName("timings");
                if ($timings->length > 0) {
                    $CruiseSearch = $timings->item(0)->getAttribute("CruiseSearch");
                }
                $workertimes = $searchdetail->item(0)->getElementsByTagName("workertimes");
                if ($workertimes->length > 0) {
                    $CruiseSearch = $workertimes->item(0)->getAttribute("CruiseSearch");
                }
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
