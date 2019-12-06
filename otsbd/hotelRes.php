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
echo "COMECOU HOTEL RES<br/>";
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

$url = "https://sintesb.axisdata.net/apu-test/ota";

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<OTA_HotelResRQ xmlns="http://www.opentravel.org/OTA/2003/05" AvailRatesOnly="true" Version="0.1" ResStatus="Quote">
<POS>
  <Source>
  <RequestorID Type="22" ID="TEST" ID_Context="AxisData"/>
  </Source>
  <Source>
  <RequestorID Type="88" ID="TEST" MessagePassword="testpass"/>
  </Source>
</POS>
<HotelReservations>
  <HotelReservation>
    <RoomStays>
      <RoomStay>
        <RoomTypes>
          <RoomType RoomTypeCode = "RMSDDB000E"></RoomType>
        </RoomTypes>
        <TimeSpan End = "2019-12-09" Start = "2019-12-06"></TimeSpan>
        <Total AmountAfterTax = "87.24" CurrencyCode = "EUR"></Total>
        <BasicPropertyInfo HotelCode = "AMTSPT0019"></BasicPropertyInfo>
      </RoomStay>
    </RoomStays> 
    <Services>
        <Service ServiceCategoryCode="S0000" ServiceRPH="2">
            <Price>
                <Total CurrencyCode="EUR" AmountAfterTax="50.00"/>
            </Price>
        </Service>
    </Services>   
    <ResGuests>
        <ResGuest AgeQualifyingCode="10" ResGuestRPH="1">
          <Profiles>
            <ProfileInfo>
              <Profile>
                <Customer BirthDate="1977-01-27">
                  <PersonName>
                    <NamePrefix>Mr</NamePrefix>
                    <GivenName>Michael</GivenName>
                    <Surname>Smith</Surname>
                  </PersonName>
                  <Telephone PhoneNumber="+34625625625"/>
                  <email>michael.smith@provider.com</Email>
                </Customer>
              </Profile>
            </ProfileInfo>
          </Profiles>
          <GuestCounts>
            <GuestCount Count="1" Age="42"/>
          </GuestCounts>
        </ResGuest>
    </ResGuests>
    <ResGlobalInfo>
        <HotelReservationIDs>
            <HotelReservationID ResID_SourceContext="Client" ResID_Type="36" ResID_Value="1234"/>
            <HotelReservationID ResID_SourceContext="Client" ResID_Type="37" ResID_Value="1234_1" Item_RPH="1"/>
        </HotelReservationIDs>
    </ResGlobalInfo>
    <TPA_Extensions>
        <EBPrepayment Value="87.24" PaymentDueDate="2019-12-06"/>
        <ns1:BookingStatus xmlns:ns1="http://www.opentravel.org/OTA/2003/05/tpa" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:type="ns1:BookingStatus" ReservationStatusType="Reserved"/>
    </TPA_Extensions>
  </HotelReservation>
</HotelReservations>
</OTA_HotelResRQ>';

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

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>