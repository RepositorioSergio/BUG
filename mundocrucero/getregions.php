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
$raw2 = 'xml=<?xml version="1.0"?><request><auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" /><method action="createsession" sitename="' . $mundocrucerosWebsite . '" currency="USD" status="Live" /></request>';
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $mundocrucerosServiceURL);
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
$raw = 'xml=<?xml version="1.0"?><request><auth username="' . $mundocrucerosusername . '" password="' . $mundocrucerospassword . '" /><method action="getcruiseregions" /></request>';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $mundocrucerosServiceURL);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
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
$success = $response->item(0)->getAttribute("success");
if ($success == 'Y') {
    $results = $response->item(0)->getElementsByTagName("results");
    if ($results->length > 0) {
        $region = $results->item(0)->getElementsByTagName("region");
        if ($region->length > 0) {
            for ($i = 0; $i < $region->length; $i ++) {
                $id = $region->item($i)->getAttribute("id");
                $name = $region->item($i)->getAttribute("name");
                echo $return;
                echo $name;
                $sql = "select id, name from mundocruceros_cruiseregions where id=" . $id;
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
                    // All good
                } else {
                    // Insert in mundocruceros_cruiseregions
                    //
                    // TODO
                    //
                    echo $return;
                    echo "Not found in mundocruceros_cruiseregions - insert";
                    echo $return;
                    die();
                }
                $sql = "select id, cruises_xml13 from cruises_regions where (name='$name' or cruises_xml13=" . $id . ")";
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
                    $id_cruises_regions = $row_settings["id"];
                    // Found
                    $sql = "update cruises_regions set cruises_xml13='$id' where id=" . $id_cruises_regions;
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    try {
                        $row_settings = $statement->execute();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error3: " . $e;
                        echo $return;
                        die();
                    }
                    $sql = "update mundocruceros_cruiseregions set mapped_id='$id_cruises_regions' where id=" . $id;
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    try {
                        $row_settings = $statement->execute();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error3: " . $e;
                        echo $return;
                        die();
                    }
                } else {
                    //
                    // Not found - Map manually
                    //
                    echo " - Manually map - not found in db";
                }
            }
        }
    }
}
echo $return;
echo "Done";
echo $return;
//
// EOF
//
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
