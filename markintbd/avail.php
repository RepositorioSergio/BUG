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
echo "COMECOU AVAILABILITY<br/>";
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

$url = "https://api.palaceresorts.com/EnterpriseServiceInterface/ServiceInterface.asmx";

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<VAXXML xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.triseptsolutions.com/Availability/Request/11.0"
xmlns:VAXXML111="http://www.triseptsolutions.com/VAXXML/11.1">
  <Header AgencyNumber="T140" Contact="Paulo Andrade" Login="BLUE STAR" Password="blue123" Vendor="MIT" DynamicPackageId="H11" SessionId="3439590611580437421" Site="VAXXML" ShowCart="Y" ShowRequest="N">
    <MediaRequest xmlns="http://www.triseptsolutions.com/Media/Request/11.0">
      <AirMedia Size="S" RefId="AS" />
      <HotelMedia Size="S" RefId="HS" />
    </MediaRequest>
  </Header>
  <Request Type="New" AbsoluteOriginCode="MKE" AbsoluteDestinationCode="LAS">
    <TravelerAvail>
      <PassengerTypeQuantity Seq="1" Type="ADT" Age="40" />
      <PassengerTypeQuantity Seq="2" Type="ADT" Age="40" />
    </TravelerAvail>
    <AirAvailRQ Start="1" Length="3" MaxConnections="3" SortType="Price">
      <Airlines Search="I" />
      <ConnectionInformation CabinType="Y" Direction="O">
        <OriginDestinationInformation Type="Org" LocationCode="MKE" DateTime="2019-10-16T00:00:00" />
        <OriginDestinationInformation Type="Des" LocationCode="LAS" DateTime="2019-10-16T00:00:00" />
      </ConnectionInformation>
 	<ConnectionInformation CabinType="Y" Direction="O">
        <OriginDestinationInformation Type="Org" LocationCode="MKE" DateTime="2019-10-16T00:00:00" />
        <OriginDestinationInformation Type="Des" LocationCode="LAS" DateTime="2019-10-16T00:00:00" />
      </ConnectionInformation>
    </AirAvailRQ>
    <HotelAvailRQ Start="1" Length="3" SortType="Price">
      <OriginDestinationInformation Type="Checkin" LocationCode="LAS" DateTime="2019-10-16T00:00:00" />
      <OriginDestinationInformation Type="Checkout" LocationCode="LAS" DateTime="2019-10-23T00:00:00" />
      <TravelerAvailSet>
        <PassengerSeq Seq="1" />
        <PassengerSeq Seq="2" />
      </TravelerAvailSet>
    </HotelAvailRQ>
    <VehAvailRateRQ Start="1" Length="3" SortType="Price">
      <OriginDestinationInformation Type="Pickup" LocationCode="LAS" DateTime="2019-10-16T00:00:00" />
      <OriginDestinationInformation Type="Dropoff" LocationCode="LAS" DateTime="2019-10-23T00:00:00" />
    </VehAvailRateRQ>
    <FeatureAvailRQ Start="1" Length="3">
      <OriginDestinationInformation Type="UsageStart" LocationCode="LAS" DateTime="2019-10-16T00:00:00" />
      <OriginDestinationInformation Type="UsageEnd" LocationCode="LAS" DateTime="2019-10-23T00:00:00" />
    </FeatureAvailRQ>
  </Request>
</VAXXML>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Host: api.palaceresorts.com",
    "Content-length: " . strlen($raw)
));

$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
$response = $response->getBody();
} else {
$logger = new Logger();
$writer = new Writer\Stream('/srv/www/htdocs/error_log');
$logger->addWriter($writer);
$logger->info($client->getUri());
$logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
echo $return;
echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
echo $return;
die();
} 


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