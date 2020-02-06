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
echo "COMECOU PREBOOKING<br/>";
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


$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

date_default_timezone_set('UTC');
//$date = date("Y-m-d H:i:s");
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <PreBookV2 xmlns="http://xml.sunhotels.net/15/">
      <userName>testagent</userName>
      <password>785623</password>
      <currency>USD</currency>
      <language>en</language>
      <checkInDate>2020-06-12</checkInDate>
      <checkOutDate>2020-06-17</checkOutDate>
      <rooms>1</rooms>
      <adults>2</adults>
      <children>0</children>
      <childrenAges></childrenAges>
      <infant>0</infant>
      <mealId>1</mealId>
      <customerCountry>es</customerCountry>
      <b2c>0</b2c>
      <searchPrice>1644</searchPrice>
      <roomId>14656827</roomId>
      <hotelId></hotelId>
      <roomtypeId></roomtypeId>
      <blockSuperDeal></blockSuperDeal>
      <showPriceBreakdown>true</showPriceBreakdown>
    </PreBookV2>
  </soap:Body>
</soap:Envelope>';

$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx?op=PreBookV2";

$headers = array(
    'Accept-Encoding: gzip,deflate',
    'Host: xml.sunhotels.net',
    'Content-Type: text/xml; charset=utf-8',
    'SOAPAction: http://xml.sunhotels.net/15/PreBookV2',
    'Content-Length: ' . strlen($raw)
); 

$ch = curl_init();
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip,deflate");
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
echo $response;
echo "</xmp>";

die();
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response2);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");

$PreBookV2Response = $Body->item(0)->getElementsByTagName("PreBookV2Response");
if ($PreBookV2Response->length > 0) {
    $preBookResult = $PreBookV2Response->item(0)->getElementsByTagName("preBookResult");
    if ($preBookResult->length > 0) {
        $PreBookCode = $preBookResult->item(0)->getElementsByTagName("PreBookCode");
        if ($PreBookCode->length > 0) {
            $PreBookCode = $PreBookCode->item(0)->nodeValue;
        } else {
            $PreBookCode = "";
        }
        error_log("\r\n PreBookCode: $PreBookCode \r\n", 3, "/srv/www/htdocs/error_log");
        $price2 = 0;
        $Price = $preBookResult->item(0)->getElementsByTagName("Price");
        if ($Price->length > 0) {
            $currency = $Price->item(0)->getAttribute("currency");
            $price2 = $Price->item(0)->nodeValue;
        } else {
            $currency = "";
            $price2 = "";
        }
        //Notes
        $Notes = $preBookResult->item(0)->getElementsByTagName("Notes");
        if ($Notes->length > 0) {
            $Note = $Notes->item(0)->getElementsByTagName("Note");
            if ($Note->length > 0) {
                for ($i=0; $i < $Note->length; $i++) { 
                    $end_date = $Note->item($i)->getAttribute("end_date");
                    $start_date = $Note->item($i)->getAttribute("start_date");
                    $text = $Note->item($i)->getElementsByTagName("text");
                    if ($text->length > 0) {
                        $text = $text->item(0)->nodeValue;
                    } else {
                        $text = "";
                    }
                }
            }
        }
        //PriceBreakdown
        $PriceBreakdown = $preBookResult->item(0)->getElementsByTagName("PriceBreakdown");
        if ($PriceBreakdown->length > 0) {
            $currency = $PriceBreakdown->item(0)->getAttribute("currency");
            $to = $PriceBreakdown->item(0)->getAttribute("to");
            $from = $PriceBreakdown->item(0)->getAttribute("from");
            $total = $PriceBreakdown->item(0)->getAttribute("total");
            $guest = $PriceBreakdown->item(0)->getElementsByTagName("guest");
            if ($guest->length > 0) {
                for ($j=0; $j < $guest->length; $j++) { 
                    $guesttotal = $guest->item($j)->getAttribute("total");
                    $price = $guest->item($j)->getElementsByTagName("price");
                    if ($price->length > 0) {
                        $value = $price->item(0)->getAttribute("value");
                        $type = $price->item(0)->getAttribute("type");
                        $breakdown = $price->item(0)->getAttribute("breakdown");
                    } else {
                        $value = "";
                        $type = "";
                        $breakdown = "";
                    }
                    
                }
            }
        } else {
            $currency = "";
            $total = "";
            $from = "";
            $to = "";
        }
        //CancellationPolicies
        $CancellationPolicies = $preBookResult->item(0)->getElementsByTagName("CancellationPolicies");
        if ($CancellationPolicies->length > 0) {
            $CancellationPolicy = $CancellationPolicies->item(0)->getElementsByTagName("CancellationPolicy");
            if ($CancellationPolicy->length > 0) {
                $deadline = $CancellationPolicy->item(0)->getElementsByTagName("deadline");
                if ($deadline->length > 0) {
                    $deadline = $deadline->item(0)->nodeValue;
                } else {
                    $deadline = "";
                }
                $percentage = $CancellationPolicy->item(0)->getElementsByTagName("percentage");
                if ($percentage->length > 0) {
                    $percentage = $percentage->item(0)->nodeValue;
                } else {
                    $percentage = "";
                }
                $text = $CancellationPolicy->item(0)->getElementsByTagName("text");
                if ($text->length > 0) {
                    $text = $text->item(0)->nodeValue;
                } else {
                    $text = "";
                }
            }
        }
    }
}



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>