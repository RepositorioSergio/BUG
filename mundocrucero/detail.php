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
echo "COMECOU DETAIL<br/>";
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

$sessionkey = '73B05D95-87DDp4FD7-90B0-8C0DED0DF5DA';
$resultno = '302_21.0';
$gradeno = '184:17';
$cabinresult = '184:17_0';

$raw = 'xml=<?xml version="1.0"?>
<request>
  <auth password="' . $mundocrucerospassword . '" username="' . $mundocrucerosusername . '" />
  <method action="getdetail" type="cruise" resultno="' . $resultno . '" sessionkey="' . $sessionkey . '" status="Test" resultkey="default">
    <gradelist>
        <grade gradeno="' . $gradeno . '" cabinresult="' . $cabinresult . '" />
    </gradelist>
  </method>
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

            $dining = $cruise->item(0)->getElementsByTagName("dining");
            if ($dining->length > 0) {
                $smoking = $dining->item(0)->getAttribute("smoking");
                $seatings = $dining->item(0)->getElementsByTagName("seatings");
                if ($seatings->length > 0) {
                    $seating = $seatings->item(0)->getElementsByTagName("seating");
                    if ($seating->length > 0) {
                        for ($i=0; $i < $seating->length; $i++) { 
                            $code = $seating->item($i)->getAttribute("code");
                            $description = $seating->item($i)->getAttribute("description");
                            $status = $seating->item($i)->getAttribute("status");
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
