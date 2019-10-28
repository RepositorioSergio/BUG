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
echo "COMECOU CANCEL POLICIES BY DATE<br/>";
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

$raw = 'requestXml=<VAXXML xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.triseptsolutions.com/CancelPoliciesByDate/Request/11.0">
<Header Vendor="MIT" Site="VAXXML" AgencyNumber="T140" Contact="Paulo Andrade" Login="Bug Software" Password="St89vxBs" DynamicPackageId="H11" />
<Request>
  <HotelCodes>
    <Hotel Code="75447" />
  </HotelCodes>
  <OriginDestinationInformation Type="Checkin" LocationCode="MIA" DateTime="2019-11-10T10:40:48" />
  <OriginDestinationInformation Type="Checkout" LocationCode="MIA" DateTime="2019-11-17T10:40:48" />
</Request>
</VAXXML>';

$url = $url . "/CancelPoliciesByDateRequest";
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
    $Descriptions = $VAXXML->item(0)->getElementsByTagName("Descriptions");
    if ($Descriptions->length > 0) {
        $Description = $Descriptions->item(0)->getElementsByTagName("Description");
        if ($Description->length > 0) {
            for ($j=0; $j < $Description->length; $j++) { 
                $Key = $Description->item($j)->getAttribute("Key");
            }
        }
    }

    $Hotels = $VAXXML->item(0)->getElementsByTagName("Hotels");
    if ($Hotels->length > 0) {
        $Hotel = $Hotels->item(0)->getElementsByTagName("Hotel");
        if ($Hotel->length > 0) {
            for ($i=0; $i < $Hotel->length; $i++) { 
                $HotelCode = $Hotel->item($i)->getAttribute("Code");
                $RollingCancelPolicy = $Hotel->item($i)->getElementsByTagName("RollingCancelPolicy");
                if ($RollingCancelPolicy->length > 0) {
                    $CutOffUnit = $RollingCancelPolicy->item(0)->getAttribute("CutOffUnit");
                    $CutOff = $RollingCancelPolicy->item(0)->getAttribute("CutOff");
                    $CancellationDeadline = $RollingCancelPolicy->item(0)->getAttribute("CancellationDeadline");
                    $DescriptionKey = $RollingCancelPolicy->item(0)->getAttribute("DescriptionKey");
                    $DaysOfWeek = $RollingCancelPolicy->item(0)->getAttribute("DaysOfWeek");
                    $HighDate = $RollingCancelPolicy->item(0)->getAttribute("HighDate");
                    $LowDate = $RollingCancelPolicy->item(0)->getAttribute("LowDate");

                    $NumberOfNightsPenalty = $RollingCancelPolicy->item(0)->getElementsByTagName("NumberOfNightsPenalty");
                    if ($NumberOfNightsPenalty->length > 0) {
                        $NumberOfNights = $NumberOfNightsPenalty->item(0)->getAttribute("NumberOfNights");
                    } else {
                        $NumberOfNights = "";
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