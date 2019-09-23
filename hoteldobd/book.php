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
echo "COMECOU HOTELS";
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
$affiliate_id_hoteldo = 0;
$branch_filter = "";
$sql = "select value from settings where name='HotelDouser' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDouser = $row_settings['value'];
}
$sql = "select value from settings where name='HotelDoMarkup' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDoMarkup = (double) $row_settings['value'];
} else {
    $HotelDoMarkup = 0;
}
$sql = "select value from settings where name='HotelDoserviceURL ' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDoserviceURL = $row_settings['value'];
}
echo $return;
echo $HotelDoserviceURL;
echo $return;
    
$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$ipaddress = '';
if ($_SERVER['HTTP_CLIENT_IP']) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_X_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
} else if ($_SERVER['HTTP_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
} else if ($_SERVER['REMOTE_ADDR']) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
} else {
    $ipaddress = 'UNKNOWN';
    $ipaddress = "142.44.216.144";
}

$ipaddress = "142.44.216.144";


$raw = '<Request Type="Reservation" Version="1.0">
    <affiliateid>DIVISAXML</affiliateid>
    <language>esp</language>
    <currency>PE</currency>
    <ip>' . $ipaddress . '</ip>
    <uid>m2r32b14es10socxtxs4y4ht</uid>
    <firstname>prueba</firstname>
    <lastname>prueba</lastname>
    <emailaddress>prueba@bestday.com</emailaddress>
    <country>MX</country>
    <address>bonampak 4</address>
    <city>cancun</city>
    <state>QROO</state>
    <zip>77500</zip>
    <total>2975.028076171875</total>
    <naturalperson>
        <gender/>
        <nationality/>
        <number/>
        <type/>
    </naturalperson>
    <legalperson>
        <businessname/>
        <number/>
        <type/>
    </legalperson>
    <phones>
        <phone>
            <type>2</type>
            <number>9981454221</number>
        </phone>
    </phones>
    <hotels>
        <hotel>
            <hotelid>450</hotelid>
            <roomtype>STDNR</roomtype>
            <mealplan>ZZ</mealplan>
            <datearrival>20200108</datearrival>
            <datedeparture>20200111</datedeparture>
            <marketid>MAYORIST</marketid>
            <contractid>2</contractid>
            <dutypercent>0</dutypercent>
            <rooms>
                <room>
                    <name>Juan</name>
                    <lastname>Perez</lastname >
                    <amount>2975.028076171875</amount>
                    <status>AV</status>
                    <ratekey>U1RETlItWlpNQVlPUklTVDJaWkF1dG9tYXRpY01haWw=</ratekey>
                    <adults>2</adults>
                    <kids>0</kids>
                    <k1a>0</k1a>
                </room>
            </rooms>
        </hotel>
    </hotels>
<payments>
<agencycreditpayment>
<type></type>
<currency></currency>
<amount></amount>
</agencycreditpayment>
</payments>
</Request>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $HotelDoserviceURL . '/Book');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: text/xml",
    "Content-type: text/xml;charset=\"utf-8\"",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>
