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
$raw = 'xml=<?xml version="1.0"?><request><auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" /><method action="getcruiselines" sitename="' . $mundocrucerosWebsite . '" /></request>';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL);
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
echo $return;
echo "<xmp>";
echo $response;
echo "</xmp>";
echo $return;
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
$results = $inputDoc->getElementsByTagName("results");
$node = $results->item(0)->getElementsByTagName("line");
for ($i = 0; $i < $node->length; $i ++) {
    $id = $node->item($i)->getAttribute("id");
    $name = $node->item($i)->getAttribute("name");
    $smalllogourl = $node->item($i)->getAttribute("smalllogourl");
    $logourl = $node->item($i)->getAttribute("logourl");
    echo $return;
    echo $name;
    echo $return;
    //
    // Cruise Lines
    //
    $sql = "select id, name from mundocruceros_cruiselines where id=" . $id;
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
        // Insert in mundocruceros_cruiselines
        //
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('mundocruceros_cruiselines');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'smalllogourl' => $smalllogourl,
            'logourl' => $logourl,
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
    $sql = "select id, cruises_xml13 from cruises_lines where (name='$name' or cruises_xml13=" . $id . ")";
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
        $id_cruises_lines = $row_settings["id"];
        //
        // Found
        //
        $time = time();
        $sql = "update cruises_lines set cruises_xml13='$id', datetime_updated=$time where id=" . $id_cruises_lines;
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
        $time = time();
        $sql = "update mundocruceros_cruiselines set mapped_id='$id_cruises_lines', datetime_updated=$time where id=" . $id;
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
        echo "Cruise line does not exist - something is wrong";
        die();
    }
    $ships = $node->item($i)->getElementsByTagName("ships");
    if ($ships->length > 0) {
        $ship = $ships->item(0)->getElementsByTagName("ship");
        if ($ship->length > 0) {
            for ($j = 0; $j < $ship->length; $j ++) {
                $id_ship = $ship->item($j)->getAttribute("id");
                $ship_name = $ship->item($j)->getAttribute("name");
                echo $ship_name;
                echo $return;
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('mundocrucero_ships');
                $select->where(array(
                    'id' => $id_ship,
                    'cruiseline_id' => $id
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
                            'datetime_updated' => time(),
                            'name' => $ship_name
                        );
                        $where['id = ?'] = $idTmp;
                        try {
                            $update = $sql->update('mundocrucero_ships', $data, $where);
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error: " . $e;
                            echo $return;
                            die();
                        }
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('mundocrucero_ships');
                        $insert->values(array(
                            'id' => $id_ship,
                            'cruiseline_id' => $id,
                            'datetime_updated' => 0,
                            'name' => $ship_name,
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
                    $insert->into('mundocrucero_ships');
                    $insert->values(array(
                        'id' => $id_ship,
                        'cruiseline_id' => $id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'name' => $ship_name,
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
                // Ship Mapping
                //
                $sql = "select id from ships where (name='$ship_name' or cruises_xml13=" . $id_ship . ")";
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
                    $id_ship_ships = $row_settings["id"];
                    //
                    // Found
                    //
                    $time = time();
                    $sql = "update ships set cruises_xml13='$id_ship', datetime_updated=$time where id=" . $id_ship_ships;
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
                    $time = time();
                    $sql = "update mundocrucero_ships set mapped_id='$id_ship_ships', datetime_updated=$time where id=" . $id_ship;
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
                    echo "Ship does not exist - map manually";
                    echo $return;
                    echo $sql;
                    echo $return;
                }
            }
        }
    }
}
echo $return;
echo "Done";
echo $return;
// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>