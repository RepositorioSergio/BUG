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
echo "COMECOU CART INQUIRE<br/>";
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

$raw = 'requestXml=<VAXXML xmlns="http://www.triseptsolutions.com/Cart/Inquire/Request/11.0">
<Header AgencyNumber="T140" Contact="Paulo Andrade" Login="Bug Software" Password="St89vxBs" Vendor="MIT" DynamicPackageId="H11" Culture="en-us"  SessionId="9096290594591558497" />
</VAXXML>';

$url = $url . "/CartInquireRequest";
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

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$string = $inputDoc->getElementsByTagName("string");

$VAXXML = $string->item(0)->getElementsByTagName("VAXXML");
if ($VAXXML->length > 0) {
    $Header = $VAXXML->item(0)->getElementsByTagName("Header");
    if ($Header->length > 0) {
        $SessionId = $Header->item(0)->getAttribute("SessionId");
        $Site = $Header->item(0)->getAttribute("Site");
        $DynamicPackageId = $Header->item(0)->getAttribute("DynamicPackageId");
        $Vendor = $Header->item(0)->getAttribute("Vendor");
        $Password = $Header->item(0)->getAttribute("Password");
        $Login = $Header->item(0)->getAttribute("Login");
        $Contact = $Header->item(0)->getAttribute("Contact");
        $AgencyNumber = $Header->item(0)->getAttribute("AgencyNumber");
    }

    $Response = $VAXXML->item(0)->getElementsByTagName("Response");
    if ($Response->length > 0) {
        $Cart = $Response->item(0)->getElementsByTagName("Cart");
        if ($Cart->length > 0) {
            //TravelerAvail
            $TravelerAvail = $Cart->item(0)->getElementsByTagName("TravelerAvail");
            if ($TravelerAvail->length > 0) {
                $PassengerTypeQuantity = $TravelerAvail->item(0)->getElementsByTagName("PassengerTypeQuantity");
                if ($PassengerTypeQuantity->length > 0) {
                    for ($i=0; $i < $PassengerTypeQuantity->length; $i++) { 
                        $PassengerTypeQuantityAge = $PassengerTypeQuantity->item($i)->getAttribute("Age");
                        $PassengerTypeQuantityType = $PassengerTypeQuantity->item($i)->getAttribute("Type");
                        $PassengerTypeQuantitySeq = $PassengerTypeQuantity->item($i)->getAttribute("Seq");
                    }
                }
            }
            //TravelerAvailSets
            $TravelerAvailSets = $Cart->item(0)->getElementsByTagName("TravelerAvailSets");
            if ($TravelerAvailSets->length > 0) {
                $TravelerAvailSet = $TravelerAvailSets->item(0)->getElementsByTagName("TravelerAvailSet");
                if ($TravelerAvailSet->length > 0) {
                    for ($j=0; $j < $TravelerAvailSet->length; $j++) { 
                        $Seq = $TravelerAvailSet->item($j)->getAttribute("Seq");

                        $PassengerTypeQuantityRef = $TravelerAvailSet->item($j)->getElementsByTagName("PassengerTypeQuantityRef");
                        if ($PassengerTypeQuantityRef->length > 0) {
                            $PassengerTypeQuantityRefSeq = $PassengerTypeQuantityRef->item(0)->getAttribute("Seq");
                            $PassengerTypeQuantityRefLead = $PassengerTypeQuantityRef->item(0)->getAttribute("Lead");
                        }
                    }
                }
            }
            //Descriptions
            $Descriptions = $Cart->item(0)->getElementsByTagName("Descriptions");
            if ($Descriptions->length > 0) {
                $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    for ($k=0; $k < $Description->length; $k++) { 
                        $Key = $Description->item($k)->getAttribute("Key");
                    }
                }
            }
            //MediaLinks
            $mediaLinks = "";
            $MediaLinks = $Cart->item(0)->getElementsByTagName("MediaLinks");
            if ($MediaLinks->length > 0) {
                $Media = $MediaLinks->item(0)->getElementsByTagName("Media");
                if ($Media->length > 0) {
                    for ($x=0; $x < $Media->length; $x++) { 
                        $Description = $Media->item($x)->getAttribute("Description");
                        $RefId = $Media->item($x)->getAttribute("RefId");
                        $MediaKey = $Media->item($x)->getAttribute("MediaKey");

                        $mediaLinks = $Media->item($x)->nodeValue;
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