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
echo "COMECOU SIMPLE SEARCH<br/>";
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


$raw2 = 'xml=<?xml version="1.0"?>
<request>
    <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
    <method action="createsession" sitename="' . $mundocrucerosWebsite . '" currency="USD" status="Test" />
</request>';

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $mundocrucerosServiceURL );
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_HEADER, false);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
curl_setopt($ch2, CURLOPT_VERBOSE, 0);
curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, 65000);
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
  <method action="simplesearch" type="cruise" sessionkey="' . $sessionkey . '" userid="3035567" sitename="' . $mundocrucerosWebsite . '" currency="USD" status="Test">
    <searchdetail type="cruise" startdate="2021-03-20" enddate="2021-03-27" lineid="20" adults="2" children="0" sid="' . $mundocrucerosSID . '" resultkey="default">
    </searchdetail>
  </method>
</request>';

echo "<xmp>";
echo $raw;
echo "</xmp>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL );
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
            $currency = $method->item(0)->getAttribute("currency");
            $sessionkey = $method->item(0)->getAttribute("sessionkey");
        }
    }
    $results = $response->item(0)->getElementsByTagName("results");
    if ($results->length > 0) {
        $cruiseid = $results->item(0)->getAttribute("cruiseid");
        $codetocruiseid = $results->item(0)->getAttribute("codetocruiseid");
        $airbalcony = $results->item(0)->getAttribute("airbalcony");
        $airbalconypricecode = $results->item(0)->getAttribute("airbalconypricecode");
        $airinside = $results->item(0)->getAttribute("airinside");
        $airinsidepricecode = $results->item(0)->getAttribute("airinsidepricecode");
        $airoutside = $results->item(0)->getAttribute("airoutside");
        $airoutsidepricecode = $results->item(0)->getAttribute("airoutsidepricecode");
        $airport = $results->item(0)->getAttribute("airport");
        $airportname = $results->item(0)->getAttribute("airportname");
        $airsuite = $results->item(0)->getAttribute("airsuite");
        $airsuitepricecode = $results->item(0)->getAttribute("airsuitepricecode");
        $altvoyagecode = $results->item(0)->getAttribute("altvoyagecode");
        $copyandmedia = $results->item(0)->getAttribute("copyandmedia");
        $cruisebalcony = $results->item(0)->getAttribute("cruisebalcony");
        $cruisebalconypricecode = $results->item(0)->getAttribute("cruisebalconypricecode");
        $cruiseinside = $results->item(0)->getAttribute("cruiseinside");
        $cruiseinsidepricecode = $results->item(0)->getAttribute("cruiseinsidepricecode");
        $cruiseoutside = $results->item(0)->getAttribute("cruiseoutside");
        $cruiseoutsidepricecode = $results->item(0)->getAttribute("cruiseoutsidepricecode");
        $cruisesuite = $results->item(0)->getAttribute("cruisesuite");
        $cruisesuitepricecode = $results->item(0)->getAttribute("cruisesuitepricecode");
        $currency = $results->item(0)->getAttribute("currency");
        $departuk = $results->item(0)->getAttribute("departuk");
        $description = $results->item(0)->getAttribute("description");
        $displaycruiseonly = $results->item(0)->getAttribute("displaycruiseonly");
        $domesticdeparture = $results->item(0)->getAttribute("domesticdeparture");
        $enddate = $results->item(0)->getAttribute("enddate");
        $engine = $results->item(0)->getAttribute("engine");
        $enginesource = $results->item(0)->getAttribute("enginesource");
        $groupids = $results->item(0)->getAttribute("groupids");
        $hascruiseonly = $results->item(0)->getAttribute("hascruiseonly");
        $hasflights = $results->item(0)->getAttribute("hasflights");
        $hidden = $results->item(0)->getAttribute("hidden");
        $iata = $results->item(0)->getAttribute("iata");
        $localpricing = $results->item(0)->getAttribute("localpricing");
        $marketid = $results->item(0)->getAttribute("marketid");
        $name = $results->item(0)->getAttribute("name");
        $ncf = $results->item(0)->getAttribute("ncf");
        $nettprice = $results->item(0)->getAttribute("nettprice");
        $price = $results->item(0)->getAttribute("price");
        $nights = $results->item(0)->getAttribute("nights");
        $nofly = $results->item(0)->getAttribute("nofly");
        $nofuelsupplement = $results->item(0)->getAttribute("nofuelsupplement");
        $ownerid = $results->item(0)->getAttribute("ownerid");
        $priority = $results->item(0)->getAttribute("priority");
        $privatenotes = $results->item(0)->getAttribute("privatenotes");
        $ratecode = $results->item(0)->getAttribute("ratecode");
        $ratecodedescription = $results->item(0)->getAttribute("ratecodedescription");
        $ratecodeiconurl = $results->item(0)->getAttribute("ratecodeiconurl");
        $resultiscruiseonly = $results->item(0)->getAttribute("resultiscruiseonly");
        $resultkey = $results->item(0)->getAttribute("resultkey");
        $resultno = $results->item(0)->getAttribute("resultno");
        $resultweight = $results->item(0)->getAttribute("resultweight");
        $returndate = $results->item(0)->getAttribute("returndate");
        $returnuk = $results->item(0)->getAttribute("returnuk");
        $roundtrip = $results->item(0)->getAttribute("roundtrip");
        $saildate = $results->item(0)->getAttribute("saildate");
        $sailnights = $results->item(0)->getAttribute("sailnights");
        $scurrency = $results->item(0)->getAttribute("scurrency");
        $seadays = $results->item(0)->getAttribute("seadays");
        $searchdetail = $results->item(0)->getAttribute("searchdetail");
        $searchno = $results->item(0)->getAttribute("searchno");
        $senior = $results->item(0)->getAttribute("senior");
        $soldout = $results->item(0)->getAttribute("soldout");
        $special = $results->item(0)->getAttribute("special");
        $specialsgroup = $results->item(0)->getAttribute("specialsgroup");
        $sprice = $results->item(0)->getAttribute("sprice");
        $startdate = $results->item(0)->getAttribute("startdate");
        $startprice = $results->item(0)->getAttribute("startprice");
        $stoplive = $results->item(0)->getAttribute("stoplive");
        $systemgroup = $results->item(0)->getAttribute("systemgroup");
        $taxes = $results->item(0)->getAttribute("taxes");
        $type = $results->item(0)->getAttribute("type");
        $voyagecode = $results->item(0)->getAttribute("voyagecode");
        $whatsincluded = $results->item(0)->getAttribute("whatsincluded");
        $zoneid = $results->item(0)->getAttribute("zoneid");

        $cruisebalconyprices = $results->item(0)->getElementsByTagName("cruisebalconyprices");
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
        $cruiseinsideprices = $results->item(0)->getElementsByTagName("cruiseinsideprices");
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
        $cruiseoutsideprices = $results->item(0)->getElementsByTagName("cruiseoutsideprices");
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
        $cruisesuiteprices = $results->item(0)->getElementsByTagName("cruisesuiteprices");
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
        $grade = $results->item(0)->getElementsByTagName("grade");
        if ($grade->length > 0) {
            $cabin = $grade->item(0)->getAttribute("cabin");
            $rate = $grade->item(0)->getAttribute("rate");
        }
        $line = $results->item(0)->getElementsByTagName("line");
        if ($line->length > 0) {
            $lineid = $line->item(0)->getAttribute("id");
            $linecode = $line->item(0)->getAttribute("code");
            $linename = $line->item(0)->getAttribute("name");
            $lineengine = $line->item(0)->getAttribute("engine");
            $linelogourl = $line->item(0)->getAttribute("logourl");
            $lineniceurl = $line->item(0)->getAttribute("niceurl");
        }
        $ports = $results->item(0)->getElementsByTagName("ports");
        if ($ports->length > 0) {
            $port = $ports->item(0)->getElementsByTagName("port");
            if ($port->length > 0) {
                for ($j=0; $j < $port->length; $j++) { 
                    $id = $port->item($j)->getAttribute("id");
                    $name = $port->item($j)->getAttribute("name");
                }
            }
        }
        $prices = $results->item(0)->getElementsByTagName("prices");
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
        $regions = $results->item(0)->getElementsByTagName("regions");
        if ($regions->length > 0) {
            $region = $regions->item(0)->getElementsByTagName("region");
            if ($region->length > 0) {
                $regionid = $region->item(0)->getAttribute("regionid");
                $regionname = $region->item(0)->getAttribute("name");
            }
        }
        $ship = $results->item(0)->getElementsByTagName("ship");
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
        $uniqueportids = $results->item(0)->getElementsByTagName("uniqueportids");
        if ($uniqueportids->length > 0) {
            $portids = "";
            for ($k=0; $k < $uniqueportids->length; $k++) { 
                $portids = $uniqueportids[$k];
            }
        }
        $uniqueportnames = $results->item(0)->getElementsByTagName("uniqueportnames");
        if ($uniqueportnames->length > 0) {
            $portnames = "";
            for ($y=0; $y < $uniqueportnames->length ; $y++) { 
                $portnames = $uniqueportnames[$y];
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
