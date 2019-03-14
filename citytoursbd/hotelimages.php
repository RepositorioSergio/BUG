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
echo "COMECOU IMAGES<br/>";
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
$sql = "select value from settings where name='enablecitytours' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_citytours = $affiliate_id;
} else {
    $affiliate_id_citytours = 0;
}
$sql = "select value from settings where name='citytoursID' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursID = $row_settings['value'];
}
echo "<br/>citytoursID: " . $citytoursID;
$sql = "select value from settings where name='citytoursPassword' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursPassword = base64_decode($row_settings['value']);
}
echo "<br/>citytoursPassword: " . $citytoursPassword;
$sql = "select value from settings where name='citytoursServiceURL' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursServiceURL = $row_settings['value'];
}
echo "<br/>citytoursServiceURL: " . $citytoursServiceURL;
$sql = "select value from settings where name='citytoursAgencyCode' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursAgencyCode = $row_settings['value'];
}
echo "<br/>citytoursAgencyCode: " . $citytoursAgencyCode;
$sql = "select value from settings where name='citytoursSystem' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursSystem = $row_settings['value'];
}
echo "<br/>citytoursSystem: " . $citytoursSystem;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
  <HotelImages xmlns="http://tempuri.org/">
  <OTA_ReadRQ PrimaryLangID="en-us" Target="Test" TimeStamp="' . $timestamp . '" Version="3.0" xmlns="http://www.opentravel.org/OTA/2003/05">
  <POS>
    <Source PseudoCityCode="NONE">
      <RequestorID ID="TESTID" Type="TD"/>
      <TPA_Extensions>
        <Provider>
          <System>' . $citytoursSystem . '</System>
          <Userid>' . $citytoursID . '</Userid>
          <Password>' . $citytoursPassword . '</Password>          
        </Provider>
      </TPA_Extensions>
    </Source>
  </POS>
  <ReadRequests>
    <ReadRequest>
      <UniqueID ID="533" Type="14" BrokerCode="7" />
    </ReadRequest>
  </ReadRequests>
</OTA_ReadRQ>
</HotelImages>
  </soap:Body>
</soap:Envelope>';
echo "<br/>" . $raw;

$headers = array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "SOAPAction: http://tempuri.org/HotelImages",
    "Content-length: ".strlen($raw),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $citytoursServiceURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw); // the SOAP request
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<br/>RESPONSE";
/* echo $return;
echo $response;
echo $return; */
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 

$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
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
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$HotelImagesResponse = $Body->item(0)->getElementsByTagName("HotelImagesResponse");
$ImageUrls = $HotelImagesResponse->item(0)->getElementsByTagName("ImageUrls");
$ImageUrls2 = $ImageUrls->item(0)->getElementsByTagName("ImageUrls");
$node = $ImageUrls2->item(0)->getElementsByTagName("ImageUrl");
echo $return;
echo "TAM: " . $node->length;
echo $return;
for ($i = 0; $i < $node->length; $i++) {
    $Type = $node->item($i)->getAttribute("Type");
    $Extension = $node->item($i)->getAttribute("Extension");
    echo $return;
    echo "EXT: " . $Extension;
    echo $return;
    $Caption = $node->item($i)->getAttribute("Caption");
    $ImageUrl = $node->item($i)->nodeValue;
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>