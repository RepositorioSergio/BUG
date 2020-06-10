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

$config = new \Zend\Config\Config(include '../config/autoload/global.mundocruceros.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT id FROM mundocrucero_ships";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $shipid = $row->id;

        $raw = 'xml=<?xml version="1.0"?>
        <request> 
            <auth password="' . $mundocrucerospassword . '" username="' . $mundocrucerosusername . '"/> 
            <method action="getshipcontent" shipid="' . $shipid . '" sitename="' . $mundocrucerosWebsite . '"/> 
        </request> ';


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL );
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
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
                    echo $return;
                    echo "ID: " . $shipid;
                    echo $return;

                    //
                    // Shipcontent
                    //
                    $sql = "select id, name from mundocruceros_shipcontent where id=" . $shipid;
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    try {
                        $row_settings = $statement->execute();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error2: " . $e;
                        echo $return;
                        die();
                    }
                    $row_settings->buffer();
                    if ($row_settings->valid()) {
                        $row = $row_settings->current();
                    } else {
                        //
                        // Insert in mundocruceros_shipcontent
                        //
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('mundocruceros_shipcontent');
                            $insert->values(array(
                                'id' => $shipid,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
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
                                'totalcrew' => $totalcrew,
                                'mapped_id' => 0
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
                    }
                    $sql = "select id from mundocrucero_ships where (name='$name' or id=" . $shipid . ")";
                    $statement = $db->createStatement($sql);
                    $row_settings = $statement->prepare();
                    try {
                        $row_settings = $statement->execute();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error: " . $e;
                        echo $return;
                    }
                    $row_settings->buffer();
                    if ($row_settings->valid()) {
                        $row_settings = $row_settings->current();
                        $ships_id = $row_settings["id"];
                        //
                        // Found
                        //
                        $time = time();
                        $sql = "update mundocrucero_ships set mapped_id='$shipid', datetime_updated=$time where id=" . $ships_id;
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        try {
                            $row_settings = $statement->execute();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error: " . $e;
                            echo $return;
                            die();
                        }
                        echo "PASSOU 3<br/>";
                        $time = time();
                        $sql = "update mundocruceros_shipcontent set mapped_id='$ships_id', datetime_updated=$time where id=" . $shipid;
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        try {
                            $row_settings = $statement->execute();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error: " . $e;
                            echo $return;
                            die();
                        }
                        echo "PASSOU 4<br/>";
                    } else {
                        //
                        // Something is wrong
                        //
                        echo "Shipcontent does not exist - something is wrong";
                        die();
                    }
                    echo "PASSOU 1<br/>";
                    $cabintypes = $ship->item(0)->getElementsByTagName("cabintypes");
                    if ($cabintypes->length > 0) {
                        $cabintype = $cabintypes->item(0)->getElementsByTagName("cabintype");
                        if ($cabintype->length > 0) {
                            for ($i=0; $i < $cabintype->length; $i++) { 
                                $cabintypeid = $cabintype->item($i)->getAttribute("id");
                                $cabintypename = $cabintype->item($i)->getAttribute("name");
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

                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('mundocruceros_cabintypes');
                                $select->where(array(
                                    'id' => $cabintypeid,
                                    'shipid' => $shipid
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                try {
                                    $result = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error: " . $e;
                                    echo $return;
                                    die();
                                }
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $idTmp = (int) $data['id'];
                                    if ($idTmp > 0) {
                                        $sql = new Sql($db);
                                        $select = $sql->update();
                                        $select->table('mundocruceros_cabintypes');
                                        $select->where(array(
                                            'id' => $idTmp
                                        ));
                                        $select->set(array(
                                            'datetime_updated' => time(),
                                            'datetime_updated' => 0,
                                            'name' => $cabintypename,
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
                                            'mapped_id' => 0
                                        ));
                                        $statement = $sql->prepareStatementForSqlObject($select);
                                        try {
                                            $results = $statement->execute();
                                        } catch (\Exception $e) {
                                            $console->writeLine('');
                                            $console->writeLine($e);
                                            $console->writeLine('');
                                            die();
                                        }
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('mundocruceros_cabintypes');
                                        $insert->values(array(
                                            'id' => $cabintypeid,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'name' => $cabintypename,
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
                                            'mapped_id' => 0
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        try {
                                            $results = $statement->execute();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error: " . $e;
                                            echo $return;
                                            die();
                                        }
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('mundocruceros_cabintypes');
                                    $insert->values(array(
                                        'id' => $cabintypeid,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $cabintypename,
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
                                        'mapped_id' => 0
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    try {
                                        $results = $statement->execute();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error: " . $e;
                                        echo $return;
                                        die();
                                    }
                                }
                                //
                                //Mapping Category
                                //
                                $sql = "select id from mundocrucero_ships where (name='$name' or id=" . $shipid . ")";
                                $statement = $db->createStatement($sql);
                                $row_settings = $statement->prepare();
                                try {
                                    $row_settings = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error: " . $e;
                                    echo $return;
                                }
                                $row_settings->buffer();
                                if ($row_settings->valid()) {
                                    $row_settings = $row_settings->current();
                                    $ships_id = $row_settings["id"];
                                    //
                                    // Found
                                    //
                                    $time = time();
                                    $sql = "update mundocruceros_cabintypes set mapped_id='$ships_id', datetime_updated=$time where id=" . $cabintypeid;
                                    $statement = $db->createStatement($sql);
                                    $statement->prepare();
                                    try {
                                        $row_settings = $statement->execute();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error: " . $e;
                                        echo $return;
                                        die();
                                    }
                                } else {
                                    //
                                    // Something is wrong
                                    //
                                    echo "Shipcontent does not exist - something is wrong";
                                    die();
                                }
                            }
                        }
                    }
                    $decks = $ship->item(0)->getElementsByTagName("decks");
                    if ($decks->length > 0) {
                        $deck = $decks->item(0)->getElementsByTagName("deck");
                        if ($deck->length > 0) {
                            for ($j=0; $j < $deck->length; $j++) { 
                                $deckid = $deck->item($j)->getAttribute("id");
                                $deckname = $deck->item($j)->getAttribute("name");
                                $imageid = $deck->item($j)->getAttribute("imageid");
                                $imageurl = $deck->item($j)->getAttribute("imageurl");
                                $caption = $deck->item($j)->getAttribute("caption");
                                $description = $deck->item($j)->getAttribute("description");
                                $sortorder = $deck->item($j)->getAttribute("sortorder");

                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('mundocruceros_decks');
                                $select->where(array(
                                    'id' => $deckid,
                                    'shipid' => $shipid
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                try {
                                    $result = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error: " . $e;
                                    echo $return;
                                    die();
                                }
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $idTmp = (int) $data['id'];
                                    if ($idTmp > 0) {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'id' => $deckid,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'name' => $deckname,
                                            'imageid' => $imageid,
                                            'imageurl' => $imageurl,
                                            'caption' => $caption,
                                            'description' => $description,
                                            'sortorder' => $sortorder,
                                            'shipid' => $shipid,
                                            'mapped_id' => 0
                                        );
                                        $where['id = ?'] = $idTmp;
                                        try {
                                            $update = $sql->update('mundocruceros_decks', $data, $where);
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error: " . $e;
                                            echo $return;
                                            die();
                                        }
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('mundocruceros_decks');
                                        $insert->values(array(
                                            'id' => $deckid,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'name' => $deckname,
                                            'imageid' => $imageid,
                                            'imageurl' => $imageurl,
                                            'caption' => $caption,
                                            'description' => $description,
                                            'sortorder' => $sortorder,
                                            'shipid' => $shipid,
                                            'mapped_id' => 0
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        try {
                                            $results = $statement->execute();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error: " . $e;
                                            echo $return;
                                            die();
                                        }
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('mundocruceros_decks');
                                    $insert->values(array(
                                        'id' => $deckid,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $deckname,
                                        'imageid' => $imageid,
                                        'imageurl' => $imageurl,
                                        'caption' => $caption,
                                        'description' => $description,
                                        'sortorder' => $sortorder,
                                        'shipid' => $shipid,
                                        'mapped_id' => 0
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    try {
                                        $results = $statement->execute();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error: " . $e;
                                        echo $return;
                                        die();
                                    }
                                }
                                //
                                //Mapping Deck
                                //
                                $sql = "select id from mundocrucero_ships where (name='$name' or id=" . $shipid . ")";
                                $statement = $db->createStatement($sql);
                                $row_settings = $statement->prepare();
                                try {
                                    $row_settings = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error: " . $e;
                                    echo $return;
                                }
                                $row_settings->buffer();
                                if ($row_settings->valid()) {
                                    $row_settings = $row_settings->current();
                                    $ships_id = $row_settings["id"];
                                    //
                                    // Found
                                    //
                                    $time = time();
                                    $sql = "update mundocruceros_decks set mapped_id='$ships_id', datetime_updated=$time where id=" . $deckid;
                                    $statement = $db->createStatement($sql);
                                    $statement->prepare();
                                    try {
                                        $row_settings = $statement->execute();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error: " . $e;
                                        echo $return;
                                        die();
                                    }
                                } else {
                                    //
                                    // Something is wrong
                                    //
                                    echo "Deck does not exist - something is wrong";
                                    die();
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

                                $sql = "select id from mundocruceros_facilities where id=" . $categoryid;
                                $statement = $db->createStatement($sql);
                                $statement->prepare();
                                try {
                                    $row_settings = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error2: " . $e;
                                    echo $return;
                                    die();
                                }
                                $row_settings->buffer();
                                if ($row_settings->valid()) {
                                    $row = $row_settings->current();
                                } else {
                                    //
                                    // Insert in mundocruceros_facilities
                                    //
                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('mundocruceros_facilities');
                                        $insert->values(array(
                                            'id' => $categoryid,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'category' => $category,
                                            'shipid' => $shipid,
                                            'mapped_id' => 0
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
                                }
                                //
                                //Mapping Facilities
                                //
                                $sql = "select id from mundocrucero_ships where (name='$name' or id=" . $shipid . ")";
                                $statement = $db->createStatement($sql);
                                $row_settings = $statement->prepare();
                                try {
                                    $row_settings = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error: " . $e;
                                    echo $return;
                                }
                                $row_settings->buffer();
                                if ($row_settings->valid()) {
                                    $row_settings = $row_settings->current();
                                    $ships_id = $row_settings["id"];
                                    //
                                    // Found
                                    //
                                    $time = time();
                                    $sql = "update mundocruceros_facilities set mapped_id='$ships_id', datetime_updated=$time where id=" . $categoryid;
                                    $statement = $db->createStatement($sql);
                                    $statement->prepare();
                                    try {
                                        $row_settings = $statement->execute();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error: " . $e;
                                        echo $return;
                                        die();
                                    }
                                } else {
                                    //
                                    // Something is wrong
                                    //
                                    echo "Facility does not exist - something is wrong";
                                    die();
                                }

                                $item = $facility->item($k)->getElementsByTagName("item");
                                if ($item->length > 0) {
                                    for ($kAux=0; $kAux < $item->length; $kAux++) { 
                                        $facilityid = $item->item($kAux)->getAttribute("facilityid");
                                        $facilitytypeid = $item->item($kAux)->getAttribute("facilitytypeid");
                                        $itemname = $item->item($kAux)->getAttribute("name");
                                        $categoryidf = $item->item($kAux)->getAttribute("categoryid");
                                        $description = $item->item($kAux)->getAttribute("description");
                                        $quantity = $item->item($kAux)->getAttribute("quantity");

                                        $sql = "select id from mundocruceros_facilities_items where id=" . $facilityid;
                                        $statement = $db->createStatement($sql);
                                        $statement->prepare();
                                        try {
                                            $row_settings = $statement->execute();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error2: " . $e;
                                            echo $return;
                                            die();
                                        }
                                        $row_settings->buffer();
                                        if ($row_settings->valid()) {
                                            $row = $row_settings->current();
                                        } else {
                                            //
                                            // Insert in mundocruceros_facilities_items
                                            //
                                            try {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('mundocruceros_facilities_items');
                                                $insert->values(array(
                                                    'id' => $facilityid,
                                                    'datetime_created' => time(),
                                                    'datetime_updated' => 1,
                                                    'facilitytypeid' => $facilitytypeid,
                                                    'name' => $itemname,
                                                    'categoryid' => $categoryidf,
                                                    'description' => $description,
                                                    'quantity' => $quantity,
                                                    'shipid' => $shipid,
                                                    'mapped_id' => 0
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
                                        //
                                        //Mapping Facilities Items
                                        //
                                        $sql = "select id from mundocrucero_ships where (name='$name' or id=" . $shipid . ")";
                                        $statement = $db->createStatement($sql);
                                        $row_settings = $statement->prepare();
                                        try {
                                            $row_settings = $statement->execute();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error: " . $e;
                                            echo $return;
                                        }
                                        $row_settings->buffer();
                                        if ($row_settings->valid()) {
                                            $row_settings = $row_settings->current();
                                            $ships_id = $row_settings["id"];
                                            //
                                            // Found
                                            //
                                            $time = time();
                                            $sql = "update mundocruceros_facilities_items set mapped_id='$ships_id', datetime_updated=$time where id=" . $facilityid;
                                            $statement = $db->createStatement($sql);
                                            $statement->prepare();
                                            try {
                                                $row_settings = $statement->execute();
                                            } catch (\Exception $e) {
                                                echo $return;
                                                echo "Error: " . $e;
                                                echo $return;
                                                die();
                                            }
                                        } else {
                                            //
                                            // Something is wrong
                                            //
                                            echo "Facility Item does not exist - something is wrong";
                                            die();
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
                                $imageid = $image->item($w)->getAttribute("id");
                                $caption = $image->item($w)->getAttribute("caption");
                                $default = $image->item($w)->getAttribute("default");
                                $ownerid = $image->item($w)->getAttribute("ownerid");
                                $imageurl = $image->item($w)->getAttribute("imageurl");
                                $smallimageurl = $image->item($w)->getAttribute("smallimageurl");
                                $type = $image->item($w)->getAttribute("type");

                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('mundocruceros_images');
                                $select->where(array(
                                    'id' => $imageid,
                                    'shipid' => $shipid
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                try {
                                    $result = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error: " . $e;
                                    echo $return;
                                    die();
                                }
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $idTmp = (int) $data['id'];
                                    if ($idTmp > 0) {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'id' => $imageid,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 1,
                                            'caption' => $caption,
                                            'default' => $default,
                                            'ownerid' => $ownerid,
                                            'imageurl' => $imageurl,
                                            'smallimageurl' => $smallimageurl,
                                            'type' => $type,
                                            'shipid' => $shipid,
                                            'mapped_id' => 0
                                        );
                                        $where['id = ?'] = $idTmp;
                                        try {
                                            $update = $sql->update('mundocruceros_images', $data, $where);
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error: " . $e;
                                            echo $return;
                                            die();
                                        }
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('mundocruceros_images');
                                        $insert->values(array(
                                            'id' => $imageid,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'caption' => $caption,
                                            'default' => $default,
                                            'ownerid' => $ownerid,
                                            'imageurl' => $imageurl,
                                            'smallimageurl' => $smallimageurl,
                                            'type' => $type,
                                            'shipid' => $shipid,
                                            'mapped_id' => 0
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        try {
                                            $results = $statement->execute();
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error: " . $e;
                                            echo $return;
                                            die();
                                        }
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('mundocruceros_images');
                                    $insert->values(array(
                                        'id' => $imageid,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'caption' => $caption,
                                        'default' => $default,
                                        'ownerid' => $ownerid,
                                        'imageurl' => $imageurl,
                                        'smallimageurl' => $smallimageurl,
                                        'type' => $type,
                                        'shipid' => $shipid,
                                        'mapped_id' => 0
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    try {
                                        $results = $statement->execute();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error: " . $e;
                                        echo $return;
                                        die();
                                    }
                                }
                                //
                                //Mapping Images
                                //
                                $sql = "select id from mundocrucero_ships where (name='$name' or id=" . $shipid . ")";
                                $statement = $db->createStatement($sql);
                                $row_settings = $statement->prepare();
                                try {
                                    $row_settings = $statement->execute();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error: " . $e;
                                    echo $return;
                                }
                                $row_settings->buffer();
                                if ($row_settings->valid()) {
                                    $row_settings = $row_settings->current();
                                    $ships_id = $row_settings["id"];
                                    //
                                    // Found
                                    //
                                    $time = time();
                                    $sql = "update mundocruceros_images set mapped_id='$ships_id', datetime_updated=$time where id=" . $imageid;
                                    $statement = $db->createStatement($sql);
                                    $statement->prepare();
                                    try {
                                        $row_settings = $statement->execute();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error: " . $e;
                                        echo $return;
                                        die();
                                    }
                                } else {
                                    //
                                    // Something is wrong
                                    //
                                    echo "Image does not exist - something is wrong";
                                    die();
                                }
                            }
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
v