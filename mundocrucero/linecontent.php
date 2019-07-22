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

$sql = "SELECT id FROM cruzeiros_linhas";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$url = 'https://supply.integration2.testaroom.com/';
$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $lineid = $row->id;

        $raw = 'xml=<?xml version="1.0"?>
        <request>
            <auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" />
            <method action="getlinecontent" lineid="' . $lineid . '" />
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
        echo "<xmp>";
        var_dump($response);
        echo "</xmp>";
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

die();

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
        $line = $results->item(0)->getElementsByTagName("line");
        $id = $line->item(0)->getAttribute("id");
        $smalllogourl = $line->item(0)->getAttribute("smalllogourl");
        $niceurl = $line->item(0)->getAttribute("niceurl");
        $name = $line->item(0)->getAttribute("name");
        $logourl = $line->item(0)->getAttribute("logourl");
        $lineimage = $line->item(0)->getAttribute("lineimage");
        $linedescription = $line->item(0)->getAttribute("linedescription");
        $code = $line->item(0)->getAttribute("code");
        $atolnumber = $line->item(0)->getAttribute("atolnumber");
        $atolname = $line->item(0)->getAttribute("atolname");

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('linecontent');
            $select->where(array(
            'id' => $id
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int)$data['id'];
                if ($id > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'smalllogourl' => $smalllogourl,
                        'niceurl' => $niceurl,
                        'name' => $name,
                        'logourl' => $logourl,
                        'lineimage' => $lineimage,
                        'linedescription' => $linedescription,
                        'code' => $code,
                        'atolnumber' => $atolnumber,
                        'atolname' => $atolname,
                        'lineid' => $lineid
                    );
                    $where['id = ?'] = $id;
                    $update = $sql->update('linecontent', $data, $where);
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('linecontent');
                    $insert->values(array(
                        'id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'smalllogourl' => $smalllogourl,
                        'niceurl' => $niceurl,
                        'name' => $name,
                        'logourl' => $logourl,
                        'lineimage' => $lineimage,
                        'linedescription' => $linedescription,
                        'code' => $code,
                        'atolnumber' => $atolnumber,
                        'atolname' => $atolname,
                        'lineid' => $lineid
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('linecontent');
                $insert->values(array(
                    'id' => $id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'smalllogourl' => $smalllogourl,
                    'niceurl' => $niceurl,
                    'name' => $name,
                    'logourl' => $logourl,
                    'lineimage' => $lineimage,
                    'linedescription' => $linedescription,
                    'code' => $code,
                    'atolnumber' => $atolnumber,
                    'atolname' => $atolname,
                    'lineid' => $lineid
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
           }
        } catch (\Exception $e) {
            echo $return;
            echo "Error: " . $e;
            echo $return;
        }


        $description = $line->item(0)->getElementsByTagName("description");
        $item = $description->item(0)->getElementsByTagName("item");
        for ($i=0; $i < $item->length; $i++) { 
            $iditem = $item->item($i)->getAttribute("id");
            $description = $item->item($i)->getAttribute("description");
            $hidden = $item->item($i)->getAttribute("hidden");
            $supercedes = $item->item($i)->getAttribute("supercedes");
            $ownerid = $item->item($i)->getAttribute("ownerid");
            $name = $item->item($i)->getAttribute("name");

            try {
                /* $sql = new Sql($db);
                $select = $sql->select();
                $select->from('description_linecontent');
                $select->where(array(
                'id' => $id
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = $data['id'];
                    if (strlen($id) > 0) {
                        $sql = new Sql($db);
                        $data = array(
                            'id' => $id,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'description' => $description,
                            'name' => $name
                        );
                        $where['id = ?'] = $id;
                        $update = $sql->update('description_linecontent', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('description_linecontent');
                        $insert->values(array(
                            'id' => $id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'description' => $description,
                            'name' => $name
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    }
                } else { */
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('description_linecontent');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'description' => $description,
                        'hidden' => $hidden,
                        'supercedes' => $supercedes,
                        'ownerid' => $ownerid,
                        'name' => $name,
                        'idlinecontent' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
               //}
            } catch (\Exception $e) {
                echo $return;
                echo "Error2: " . $e;
                echo $return;
            }
        }

        $ships = $line->item(0)->getElementsByTagName("ships");
        $ship = $ships->item(0)->getElementsByTagName("ship");
        for ($k=0; $k < $ship->length; $k++) { 
            $idship = $ship->item($k)->getAttribute("id");
            $description = $ship->item($k)->getAttribute("description");
            $ownerid = $ship->item($k)->getAttribute("ownerid");
            $niceurl = $ship->item($k)->getAttribute("niceurl");
            $name = $ship->item($k)->getAttribute("name");
            $totalcrew = $ship->item($k)->getAttribute("totalcrew");
            $tonnage = $ship->item($k)->getAttribute("tonnage");
            $starrating = $ship->item($k)->getAttribute("starrating");
            $rating = $ship->item($k)->getAttribute("rating");
            $occupancy = $ship->item($k)->getAttribute("occupancy");
            $length = $ship->item($k)->getAttribute("length");
            $launched = $ship->item($k)->getAttribute("launched");
            $adultsonly = $ship->item($k)->getAttribute("adultsonly");


            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('ships_linecontent');
                $select->where(array(
                'id' => $idship
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (int)$data['id'];
                    if ($id > 0) {
                        $sql = new Sql($db);
                        $data = array(
                            'id' => $idship,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'description' => $description,
                            'ownerid' => $ownerid,
                            'niceurl' => $niceurl,
                            'name' => $name,
                            'totalcrew' => $totalcrew,
                            'tonnage' => $tonnage,
                            'starrating' => $starrating,
                            'rating' => $rating,
                            'occupancy' => $occupancy,
                            'length' => $length,
                            'launched' => $launched,
                            'adultsonly' => $adultsonly,
                            'idlinecontent' => $id
                        );
                        $where['id = ?'] = $idship;
                        $update = $sql->update('ships_linecontent', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('ships_linecontent');
                        $insert->values(array(
                            'id' => $idship,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'description' => $description,
                            'ownerid' => $ownerid,
                            'niceurl' => $niceurl,
                            'name' => $name,
                            'totalcrew' => $totalcrew,
                            'tonnage' => $tonnage,
                            'starrating' => $starrating,
                            'rating' => $rating,
                            'occupancy' => $occupancy,
                            'length' => $length,
                            'launched' => $launched,
                            'adultsonly' => $adultsonly,
                            'idlinecontent' => $id
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    }
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('ships_linecontent');
                    $insert->values(array(
                        'id' => $idship,
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'description' => $description,
                        'ownerid' => $ownerid,
                        'niceurl' => $niceurl,
                        'name' => $name,
                        'totalcrew' => $totalcrew,
                        'tonnage' => $tonnage,
                        'starrating' => $starrating,
                        'rating' => $rating,
                        'occupancy' => $occupancy,
                        'length' => $length,
                        'launched' => $launched,
                        'adultsonly' => $adultsonly,
                        'idlinecontent' => $id
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
               }
            } catch (\Exception $e) {
                echo $return;
                echo "Error3: " . $e;
                echo $return;
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
