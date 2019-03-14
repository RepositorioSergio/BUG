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
echo "COMECOU CRUZEIROS<br/>";
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
echo $return;
echo "AFFIL: " . $affiliate_id_mundocruceros;
echo $return;
$sql = "select value from settings where name='mundocrucerosusername' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerosusername = $row_settings['value'];
}
echo $return;
echo "USER: " . $mundocrucerosusername;
echo $return;
$sql = "select value from settings where name='mundocrucerospassword' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $mundocrucerospassword = base64_decode($row_settings['value']);
}
echo $return;
echo $mundocrucerospassword;
echo $return;
$sql = "select value from settings where name='mundocrucerosServiceURL' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosServiceURL = $row['value'];
}
echo $return;
echo $mundocrucerosServiceURL;
echo $return;
$sql = "select value from settings where name='mundocrucerosSID' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosSID = $row['value'];
}
echo $return;
echo $mundocrucerosSID;
echo $return;
$sql = "select value from settings where name='mundocrucerosWebsite' and affiliate_id=$affiliate_id_mundocruceros";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $mundocrucerosWebsite = $row['value'];
}
echo $return;
echo $mundocrucerosWebsite;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();


$config = new \Zend\Config\Config(include '../config/autoload/global.mundocruceros.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT id FROM ships_linecontent";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

/* $url = 'https://supply.integration2.testaroom.com/';
$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $codetocruiseid = $row->id; */
        $codetocruiseid = 130;
        echo $codetocruiseid . "<br/>";

        $raw = 'xml=<?xml version="1.0"?>
        <request>
            <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
            <method action="getcruisecontent" codetocruiseid="' . $codetocruiseid . '" sitename="' . $mundocrucerosWebsite . '" />
        </request>';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
        if ($inputDoc != null) {
            echo "NAO NULO ";
        }
        $results = $inputDoc->getElementsByTagName("results");
        $errors = $results->item(0)->getElementsByTagName("errors");
        if ($errors->length == 0) {
            $cruise = $results->item(0)->getElementsByTagName("cruise");
            $description = $cruise->item(0)->getAttribute("description");
            $codetocruiseid = $cruise->item(0)->getAttribute("codetocruiseid");
            $voyagecode = $cruise->item(0)->getAttribute("voyagecode");
            $ukdeparture = $cruise->item(0)->getAttribute("ukdeparture");
            $stoplive = $cruise->item(0)->getAttribute("stoplive");
            $startdate = $cruise->item(0)->getAttribute("startdate");
            $starrating = $cruise->item(0)->getAttribute("starrating");
            $shipname = $cruise->item(0)->getAttribute("shipname");
            $shipid = $cruise->item(0)->getAttribute("shipid");
            $sailnights = $cruise->item(0)->getAttribute("sailnights");
            $saildate = $cruise->item(0)->getAttribute("saildate");
            $returndate = $cruise->item(0)->getAttribute("returndate");
            $nofly = $cruise->item(0)->getAttribute("nofly");
            $nights = $cruise->item(0)->getAttribute("nights");
            $name = $cruise->item(0)->getAttribute("name");
            $linelogo = $cruise->item(0)->getAttribute("linelogo");
            $lineid = $cruise->item(0)->getAttribute("lineid");
            $enddate = $cruise->item(0)->getAttribute("enddate");
            $departuk = $cruise->item(0)->getAttribute("departuk");
            $cruiseline = $cruise->item(0)->getAttribute("cruiseline");
            $cruiseid = $cruise->item(0)->getAttribute("cruiseid");

            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('content');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'description' => $description,
                'codetocruiseid' => $codetocruiseid,
                'voyagecode' => $voyagecode,
                'ukdeparture' => $ukdeparture,
                'stoplive' => $stoplive,
                'startdate' => $startdate,
                'starrating' => $starrating,
                'shipname' => $shipname,
                'shipid' => $shipid,
                'sailnights' => $sailnights,
                'saildate' => $saildate,
                'returndate' => $returndate,
                'nofly' => $nofly,
                'nights' => $nights,
                'name' => $name,
                'linelogo' => $linelogo,
                'lineid' => $lineid,
                'enddate' => $enddate,
                'departuk' => $departuk,
                'cruiseline' => $cruiseline,
                'cruiseid' => $cruiseid,
                'codetocruiseid' => $codetocruiseid
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();


            $flights = $cruise->item(0)->getElementsByTagName("flights");
            $flight = $flights->item(0)->getElementsByTagName("flight");
            for ($i=0; $i < $flight->length; $i++) { 
                $name = $flight->item($i)->getAttribute("name");
                $iata = $flight->item($i)->getAttribute("iata");

                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('content_flights');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'name' => $name,
                    'iata' => $iata
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();

            }

            $itinerary = $cruise->item(0)->getElementsByTagName("itinerary");
            $item = $itinerary->item(0)->getElementsByTagName("item");
            for ($j=0; $j < $item->length; $j++) { 
                $description = $item->item($j)->getAttribute("description");
                $type = $item->item($j)->getAttribute("type");
                $name = $item->item($j)->getAttribute("name");
                $portid = $item->item($j)->getAttribute("portid");
                $ownerid = $item->item($j)->getAttribute("ownerid");
                $orderid = $item->item($j)->getAttribute("orderid");
                $longitude = $item->item($j)->getAttribute("longitude");
                $latitude = $item->item($j)->getAttribute("latitude");
                $departtime = $item->item($j)->getAttribute("departtime");
                $departdate = $item->item($j)->getAttribute("departdate");
                $day = $item->item($j)->getAttribute("day");
                $arrivetime = $item->item($j)->getAttribute("arrivetime");
                $arrivedate = $item->item($j)->getAttribute("arrivedate");

                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('content_flights');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'description' => $description,
                    'type' => $type,
                    'name' => $name,
                    'portid' => $portid,
                    'ownerid' => $ownerid,
                    'orderid' => $orderid,
                    'longitude' => $longitude,
                    'latitude' => $latitude,
                    'departtime' => $departtime,
                    'departdate' => $departdate,
                    'day' => $day,
                    'arrivetime' => $arrivetime,
                    'arrivedate' => $arrivedate
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();

            }


            $regions = $cruise->item(0)->getElementsByTagName("regions");
            $region = $regions->item(0)->getElementsByTagName("region");
            for ($l=0; $l < $region->length; $l++) { 
                $name = $sailing->item($l)->getAttribute("name");
                $regionid = $sailing->item($l)->getAttribute("regionid");

                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('content_regions');
                $insert->values(array(
                    'regionid' => $regionid,
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'name' => $name
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();

            }



            $sailings = $cruise->item(0)->getElementsByTagName("sailings");
            $sailing = $sailings->item(0)->getElementsByTagName("sailing");
            for ($k=0; $k < $sailing->length; $k++) { 
                $name = $sailing->item($k)->getAttribute("name");
                $startdate = $sailing->item($k)->getAttribute("startdate");
                $shipname = $sailing->item($k)->getAttribute("shipname");
                $shipid = $sailing->item($k)->getAttribute("shipid");
                $saildate = $sailing->item($k)->getAttribute("saildate");
                $ownerid = $sailing->item($k)->getAttribute("ownerid");
                $code = $sailing->item($k)->getAttribute("code");

                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('content_sailings');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'name' => $name,
                    'startdate' => $startdate,
                    'shipname' => $shipname,
                    'shipid' => $shipid,
                    'saildate' => $saildate,
                    'ownerid' => $ownerid,
                    'code' => $code
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();

            }
        }

    //}
//}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
