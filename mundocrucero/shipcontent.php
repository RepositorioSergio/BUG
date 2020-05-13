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
echo "COMECOU SHIP CONTENT<br/>";
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
$raw = 'xml=<?xml version="1.0"?>
<request> 
    <auth password="' . $mundocrucerospassword . '" username="' . $mundocrucerosusername . '"/> 
    <method action="getshipcontent" shipid="367" sitename="' . $mundocrucerosWebsite . '"/> 
</request> ';


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
        $ship = $results->item(0)->getElementsByTagName("ship");
        if ($ship->length > 0) {
            $shipid = $ship->item(0)->getAttribute("id");
            $name = $ship->item(0)->getAttribute("name");
            $code = $ship->item(0)->getAttribute("code");
            $adultsonly = $ship->item(0)->getAttribute("adultsonly");
            $apigradeorder = $ship->item(0)->getAttribute("apigradeorder");
            $atolnumber = $ship->item(0)->getAttribute("atolnumber");
            $cruiseline = $ship->item(0)->getAttribute("cruiseline");
            $description = $ship->item(0)->getAttribute("description");
            $hidden = $ship->item(0)->getAttribute("hidden");
            $highlights = $ship->item(0)->getAttribute("highlights");
            $launched = $ship->item(0)->getAttribute("launched");
            $length = $ship->item(0)->getAttribute("length");
            $lineid = $ship->item(0)->getAttribute("lineid");
            $niceurl = $ship->item(0)->getAttribute("niceurl");
            $occupancy = $ship->item(0)->getAttribute("occupancy");
            $ownerid = $ship->item(0)->getAttribute("ownerid");
            $rating = $ship->item(0)->getAttribute("rating");
            $shipclass = $ship->item(0)->getAttribute("shipclass");
            $starrating = $ship->item(0)->getAttribute("starrating");
            $supercedes = $ship->item(0)->getAttribute("supercedes");
            $tonnage = $ship->item(0)->getAttribute("tonnage");
            $totalcabins = $ship->item(0)->getAttribute("totalcabins");
            $totalcrew = $ship->item(0)->getAttribute("totalcrew");

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('shipcontent');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'shipid' => $shipid,
                    'name' => $name,
                    'code' => $code,
                    'adultsonly' => $adultsonly,
                    'apigradeorder' => $apigradeorder,
                    'atolnumber' => $atolnumber,
                    'cruiseline' => $cruiseline,
                    'description' => $description,
                    'hidden' => $hidden,
                    'highlights' => $highlights,
                    'launched' => $launched,
                    'length' => $length,
                    'lineid' => $lineid,
                    'niceurl' => $niceurl,
                    'occupancy' => $occupancy,
                    'ownerid' => $ownerid,
                    'rating' => $rating,
                    'shipclass' => $shipclass,
                    'starrating' => $starrating,
                    'supercedes' => $supercedes,
                    'tonnage' => $tonnage,
                    'totalcabins' => $totalcabins,
                    'totalcrew' => $totalcrew
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO 1: " . $e;
                echo $return;
            }

            $cabintypes = $ship->item(0)->getElementsByTagName("cabintypes");
            if ($cabintypes->length > 0) {
                $cabintype = $cabintypes->item(0)->getElementsByTagName("cabintype");
                if ($cabintype->length > 0) {
                    for ($i=0; $i < $cabintype->length; $i++) { 
                        $id = $cabintype->item($i)->getAttribute("id");
                        $name = $cabintype->item($i)->getAttribute("name");
                        $ownerid = $cabintype->item($i)->getAttribute("ownerid");
                        $cabincode = $cabintype->item($i)->getAttribute("cabincode");
                        $cabincode2 = $cabintype->item($i)->getAttribute("cabincode2");
                        $cabintype2 = $cabintype->item($i)->getAttribute("cabintype");
                        $caption = $cabintype->item($i)->getAttribute("caption");
                        $colourcode = $cabintype->item($i)->getAttribute("colourcode");
                        $deckid = $cabintype->item($i)->getAttribute("deckid");
                        $description = $cabintype->item($i)->getAttribute("description");
                        $isdefault = $cabintype->item($i)->getAttribute("isdefault");
                        $imageurl = $cabintype->item($i)->getAttribute("imageurl");
                        $smallimageurl = $cabintype->item($i)->getAttribute("smallimageurl");
                        $sortweight = $cabintype->item($i)->getAttribute("sortweight");
                        $supercedes = $cabintype->item($i)->getAttribute("supercedes");
                        $validfrom = $cabintype->item($i)->getAttribute("validfrom");
                        $validto = $cabintype->item($i)->getAttribute("validto");
                        $position = $cabintype->item($i)->getElementsByTagName('position');
                        if ($position->length > 0) {
                            $position = $position->item(0)->nodeValue;
                        } else {
                            $position = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('shipcontent_cabintypes');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'cabintypeid' => $id,
                                'name' => $name,
                                'ownerid' => $ownerid,
                                'cabincode' => $cabincode,
                                'cabincode2' => $cabincode2,
                                'cabintype' => $cabintype2,
                                'caption' => $caption,
                                'colourcode' => $colourcode,
                                'deckid' => $deckid,
                                'description' => $description,
                                'isdefault' => $isdefault,
                                'imageurl' => $imageurl,
                                'smallimageurl' => $smallimageurl,
                                'sortweight' => $sortweight,
                                'supercedes' => $supercedes,
                                'validfrom' => $validfrom,
                                'validto' => $validto,
                                'position' => $position,
                                'shipid' => $shipid
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 2: " . $e;
                            echo $return;
                        }
                    }
                }
            }
            $decks = $ship->item(0)->getElementsByTagName("decks");
            if ($decks->length > 0) {
                $deck = $decks->item(0)->getElementsByTagName("deck");
                if ($deck->length > 0) {
                    for ($j=0; $j < $deck->length; $j++) { 
                        $id = $deck->item($j)->getAttribute("id");
                        $name = $deck->item($j)->getAttribute("name");
                        $imageid = $deck->item($j)->getAttribute("imageid");
                        $imageurl = $deck->item($j)->getAttribute("imageurl");
                        $caption = $deck->item($j)->getAttribute("caption");
                        $description = $deck->item($j)->getAttribute("description");
                        $sortorder = $deck->item($j)->getAttribute("sortorder");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('shipcontent_decks');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'deckid' => $id,
                                'name' => $name,
                                'imageid' => $imageid,
                                'imageurl' => $imageurl,
                                'caption' => $caption,
                                'description' => $description,
                                'sortorder' => $sortorder,
                                'shipid' => $shipid
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 3: " . $e;
                            echo $return;
                        }
                    }
                }
            }
            $facilities = $ship->item(0)->getElementsByTagName("facilities");
            if ($facilities->length > 0) {
                $facility = $facilities->item(0)->getElementsByTagName("facility");
                if ($facility->length > 0) {
                    for ($k=0; $k < $facility->length; $k++) { 
                        $categoryid = $facility->item($k)->getAttribute("categoryid");
                        $category = $facility->item($k)->getAttribute("category");
                        
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('shipcontent_facilities');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'categoryid' => $categoryid,
                                'category' => $category,
                                'shipid' => $shipid
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 4: " . $e;
                            echo $return;
                        }

                        $item = $facility->item($k)->getElementsByTagName("item");
                        if ($item->length > 0) {
                            for ($kAux=0; $kAux < $item->length; $kAux++) { 
                                $facilityid = $item->item($kAux)->getAttribute("facilityid");
                                $facilitytypeid = $item->item($kAux)->getAttribute("facilitytypeid");
                                $name = $item->item($kAux)->getAttribute("name");
                                $categoryid = $item->item($kAux)->getAttribute("categoryid");
                                $description = $item->item($kAux)->getAttribute("description");
                                $quantity = $item->item($kAux)->getAttribute("quantity");

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('shipcontent_facilities_items');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'facilityid' => $facilityid,
                                        'facilitytypeid' => $facilitytypeid,
                                        'name' => $name,
                                        'categoryid' => $categoryid,
                                        'description' => $description,
                                        'quantity' => $quantity,
                                        'shipid' => $shipid
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 5: " . $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                }
            }
            $images = $ship->item(0)->getElementsByTagName("images");
            if ($images->length > 0) {
                $image = $images->item(0)->getElementsByTagName("image");
                if ($image->length > 0) {
                    for ($w=0; $w < $image->length; $w++) { 
                        $id = $image->item($w)->getAttribute("id");
                        $caption = $image->item($w)->getAttribute("caption");
                        $default = $image->item($w)->getAttribute("default");
                        $ownerid = $image->item($w)->getAttribute("ownerid");
                        $imageurl = $image->item($w)->getAttribute("imageurl");
                        $smallimageurl = $image->item($w)->getAttribute("smallimageurl");
                        $type = $image->item($w)->getAttribute("type");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('shipcontent_images');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'imageid' => $id,
                                'caption' => $caption,
                                'default' => $default,
                                'ownerid' => $ownerid,
                                'imageurl' => $imageurl,
                                'smallimageurl' => $smallimageurl,
                                'type' => $type,
                                'shipid' => $shipid
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 6: " . $e;
                            echo $return;
                        }
                    }
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
