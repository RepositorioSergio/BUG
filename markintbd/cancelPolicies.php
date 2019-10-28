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
echo "COMECOU CANCEL POLICIES<br/>";
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

$url = "https://beta.triseptapi.com/11.0/vax.asmx";

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = 'requestXml=<VAXXML xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.triseptsolutions.com/VAXXML/11.0/CancelPoliciesRequest">
<Header Vendor="MIT" Site="VAXXML" AgencyNumber="T140" Contact="Paulo Andrade" Login="Bug Software" Password="St89vxBs" SessionId="6977906677164297060" />
</VAXXML>';

$url = $url . "/CancelPoliciesRequest";
echo $url . '<br/>';

$headers = array(
    "Accept: application/xml",
    "Content-type: application/x-www-form-urlencoded",
    "Content-Encoding: UTF-8",
    "Accept-Encoding: gzip,deflate",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$response = str_replace("&amp;", "&", str_replace("&nbsp;", "", $response));
$response = str_replace('&amp;', '&', $response);
$response = str_replace('&nbsp;', '', $response);
$response = str_replace("&lt;","<",$response);
$response = str_replace("&gt;",">",$response);
$response = str_replace("&quot;","",$response);
$response = str_replace("&#43;","",$response);
$response = str_replace("<br>","<br/>",$response);
$response = str_replace('&', 'and', $response);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>