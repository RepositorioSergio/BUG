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
echo "COMECOU BOOKING<br/>";
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

$url = "http://xml.sunhotels.net/15/PostGet/NonStaticXMLAPI.asmx/BookV2?userName=BugSofttest&password=bkGk2sWP&currency=EUR&language=en&email=noreply@sunhotels.net&checkInDate=2017-11-23&checkOutDate=2017-11-25&roomId=33513&rooms=1&adults=1&children=1&infant=0&yourRef=Test%20booking&specialrequest=&mealId=1&adultGuest1FirstName=Test&adultGuest1LastName=Adult&adultGuest2FirstName=&
adultGuest2LastName=&adultGuest3FirstName=&adultGuest3LastName=&adultGuest4FirstName=&
adultGuest4LastName=&adultGuest5FirstName=&adultGuest5LastName=&adultGuest6FirstName=&
adultGuest6LastName=&adultGuest7FirstName=&adultGuest7LastName=&adultGuest8FirstName=&
adultGuest8LastName=&adultGuest9FirstName=&adultGuest9LastName=&childrenGuest1FirstName
=Test&childrenGuest1LastName=Child&childrenGuestAge1=8&childrenGuest2FirstName=&childrenG
uest2LastName=&childrenGuestAge2=&childrenGuest3FirstName=&childrenGuest3LastName=&child
renGuestAge3=&childrenGuest4FirstName=&childrenGuest4LastName=&childrenGuestAge4=&childr
enGuest5FirstName=&childrenGuest5LastName=&childrenGuestAge5=&childrenGuest6FirstName=&
childrenGuest6LastName=&childrenGuestAge6=&childrenGuest7FirstName=&childrenGuest7LastNa
me=&childrenGuestAge7=&childrenGuest8FirstName=&childrenGuest8LastName=&childrenGuestAg
e8=&childrenGuest9FirstName=&childrenGuest9LastName=&childrenGuestAge9=&customerEmail=&
paymentMethodId=1&creditCardType=&creditCardNumber=&creditCardHolder=&creditCardCVV2=&c
reditCardExpYear=&creditCardExpMonth=&customerEmail=&invoiceRef=&CustomerCountry=gb&B2
C=0";

$headers = array(
    'Accept-Encoding: gzip,deflate'
); 

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url . '/activity.json/search?currency=USD&lang=EN');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_ENCODING , "gzip,deflate");
//curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
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




// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>