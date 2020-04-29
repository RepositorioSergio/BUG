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
echo "COMECOU CABINS<br/>";
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

$sessionkey = '61DD81F2-4068r4CDF-910C-2649D2E760E1';
$resultno = '302_18.0';
$gradeno = '184:14';

$raw = 'xml=<?xml version="1.0"?>
<request>
    <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
    <method action="getcabins" sessionkey="' . $sessionkey . '" resultno="' . $resultno . '" gradeno="' . $gradeno . '" status="Live" />
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
            $gradeno = $method->item(0)->getAttribute("gradeno");
            $resultno = $method->item(0)->getAttribute("resultno");
            $sessionkey = $method->item(0)->getAttribute("sessionkey");
        }
    }
    $results = $response->item(0)->getElementsByTagName("results");
    if ($results->length > 0) {
        $cabin = $results->item(0)->getElementsByTagName("cabin");
        if ($cabin->length > 0) {
            for ($i=0; $i < $cabin->length; $i++) { 
                $bathdescription = $cabin->item($i)->getAttribute("bathdescription");
                $bedcode = $cabin->item($i)->getAttribute("bedcode");
                $beddescription = $cabin->item($i)->getAttribute("beddescription");
                $cabingrade = $cabin->item($i)->getAttribute("cabingrade");
                $cabinid = $cabin->item($i)->getAttribute("cabinid");
                $cabinno = $cabin->item($i)->getAttribute("cabinno");
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
                }
                $bedconfig = $cabin->item($i)->getElementsByTagName("bedconfig");
                if ($bedconfig->length > 0) {
                    for ($iAux=0; $iAux < $bedconfig->length; $iAux++) { 
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
                for ($j=0; $j < $errataitem->length; $j++) { 
                    $type = $errataitem->item($j)->getAttribute("type");
                    $value = $errataitem->item($j)->getAttribute("value");
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
