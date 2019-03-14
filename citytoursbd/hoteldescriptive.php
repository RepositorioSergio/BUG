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
echo "COMECOU SEARCH<br/>";
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
  <HotelDescriptiveInfo xmlns="http://tempuri.org/">
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
</HotelDescriptiveInfo>
  </soap:Body>
</soap:Envelope>';
echo "<br/>" . $raw;

$headers = array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "SOAPAction: http://tempuri.org/HotelDescriptiveInfo",
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
die();
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
$HotelDescriptiveInfoResponse = $Body->item(0)->getElementsByTagName("HotelDescriptiveInfoResponse");
$OTA_HotelDescriptiveInfoRS = $HotelDescriptiveInfoResponse->item(0)->getElementsByTagName("OTA_HotelDescriptiveInfoRS");
$HotelDescriptiveContents = $OTA_HotelDescriptiveInfoRS->item(0)->getElementsByTagName("HotelDescriptiveContents");
$HotelDescriptiveContent = $HotelDescriptiveContents->item(0)->getElementsByTagName("HotelDescriptiveContent");
$HotelName = $HotelDescriptiveContent->item(0)->getAttribute("HotelName");
//HotelInfo
$HotelInfo = $HotelDescriptiveContent->item(0)->getElementsByTagName("HotelInfo");
$CategoryCodes = $HotelInfo->item(0)->getElementsByTagName("CategoryCodes");
$SegmentCategory = $CategoryCodes->item(0)->getElementsByTagName("SegmentCategory");
if ($SegmentCategory->length > 0) {
    $TypeHotel = $SegmentCategory->item(0)->getAttribute("TypeHotel");
    $Stars = $SegmentCategory->item(0)->getAttribute("Stars");
} else {
    $TypeHotel = "";
    $Stars = "";
}


$Descriptions = $HotelInfo->item(0)->getElementsByTagName("Descriptions");
if ($Descriptions->length > 0) {
    $DescriptiveText = $Descriptions->item(0)->getElementsByTagName("DescriptiveText");
    if ($DescriptiveText->length > 0) {
        $DescriptiveText = $DescriptiveText->item(0)->nodeValue;
    } else {
        $DescriptiveText = "";
    }
}

//ContactInfos
$ContactInfos = $HotelDescriptiveContent->item(0)->getElementsByTagName("ContactInfos");
$ContactInfo = $ContactInfos->item(0)->getElementsByTagName("ContactInfo");
$Addresses = $ContactInfo->item(0)->getElementsByTagName("Addresses");
$Address = $Addresses->item(0)->getElementsByTagName("Address");
if ($Address->length > 0) {
    $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
    if ($AddressLine->length > 0) {
        $AddressLine = $AddressLine->item(0)->nodeValue;
    } else {
        $AddressLine = "";
    }
    $CityName = $Address->item(0)->getElementsByTagName("CityName");
    if ($CityName->length > 0) {
        $CityName = $CityName->item(0)->nodeValue;
    } else {
        $CityName = "";
    }
    $CityCode = $Address->item(0)->getElementsByTagName("CityCode");
    if ($CityCode->length > 0) {
        $CityCode = $CityCode->item(0)->nodeValue;
    } else {
        $CityCode = "";
    }
    $PostalCode = $Address->item(0)->getElementsByTagName("PostalCode");
    if ($PostalCode->length > 0) {
        $PostalCode = $PostalCode->item(0)->nodeValue;
    } else {
        $PostalCode = "";
    }
    $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
    if ($StateProv->length > 0) {
        $StateCode = $StateProv->item(0)->getAttribute("StateCode");
        $StateProv = $StateProv->item(0)->nodeValue;
    } else {
        $StateProv = "";
    }
    $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
    if ($CountryName->length > 0) {
        $Code = $CountryName->item(0)->getAttribute("Code");
        $CountryName = $CountryName->item(0)->nodeValue;
    } else {
        $CountryName = "";
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>