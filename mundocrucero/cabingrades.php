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
echo "COMECOU CABIN GRADES<br/>";
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

$sessionkey = '0E2D63E0~6BE1n4F1A-89B6-2C804B5F5B8C';
$resultno = '302_78.0';

$raw = 'xml=<?xml version="1.0"?>
<request>
    <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
    <method action="getcabingrades" sessionkey="' . $sessionkey . '" resultno="' . $resultno . '" status="Live" />
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
        }
    }
    $results = $response->item(0)->getElementsByTagName("results");
    if ($results->length > 0) {
        $grades = $results->item(0)->getElementsByTagName("grades");
        if ($grades->length > 0) {
            $grade = $grades->item(0)->getElementsByTagName("grade");
            if ($grade->length > 0) {
                for ($i=0; $i < $grade->length; $i++) { 
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

                    $sdecks = $grade->item($i)->getElementsByTagName("sdecks");
                    if ($sdecks->length > 0) {
                        $sdecks = $sdecks->item(0)->nodeValue;
                    } else {
                        $sdecks = "";
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
                    $childreninfo = $grade->item($i)->getElementsByTagName("childreninfo");
                    if ($childreninfo->length > 0) {
                        for ($iAux2=0; $iAux2 < $childreninfo->length; $iAux2++) { 
                            $header = $childreninfo->item($iAux2)->getAttribute("header");
                            $text = $childreninfo->item($iAux2)->getAttribute("text");
                        }
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
                            for ($iAux3=0; $iAux3 < $itinerary->length; $iAux3++) { 
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
                    $prices = $grade->item($i)->getElementsByTagName("prices");
                    if ($prices->length > 0) {
                        $price = $prices->item(0)->getElementsByTagName("price");
                        if ($price->length > 0) {
                            for ($iAux=0; $iAux < $price->length; $iAux++) { 
                                $appliesto = $price->item($iAux)->getAttribute("appliesto");
                                $marker = $price->item($iAux)->getAttribute("marker");
                                $name = $price->item($iAux)->getAttribute("name");
                                $runorder = $price->item($iAux)->getAttribute("runorder");
                                $type = $price->item($iAux)->getAttribute("type");
                                $value = $price->item($iAux)->getAttribute("value");
                            }
                        }
                    }
                }
            }
        }
        $itinerary = $results->item(0)->getElementsByTagName("itinerary");
        if ($itinerary->length > 0) {
            $item = $itinerary->item(0)->getElementsByTagName("item");
            if ($item->length > 0) {
                for ($j=0; $j < $item->length; $j++) { 
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
