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

$raw = '<OTA_HotelResRQ ResStatus="Quote" EchoToken="550e8efd-344e-4f13-9551-d517a9520bbd" Version="1" xmlns="http://www.opentravel.org/OTA/2003/05">
<POS>
  <Source>
    <RequestorID Type="88" ID="TEST" MessagePassword="testpass"/> 
  </Source>
  <Source>
    <RequestorID ID_Context="AxisData" Type="22" ID="TEST"/>
  </Source>
</POS>
<HotelReservations>
  <HotelReservation>
    <RoomStays>
      <RoomStay RPH="1">
        <RoomTypes>
          <RoomType RoomTypeCode="RMSPD20000"/>
        </RoomTypes>
        <TimeSpan End="2020-06-16" Start="2020-06-12"/>
        <Total AmountAfterTax = "1203.31" CurrencyCode ="EUR" />
        <BasicPropertyInfo HotelCode="AUSNYC9WO8"/>
        <ResGuestRPHs>
          <ResGuestRPH RPH="1"/>
          <ResGuestRPH RPH="2"/>
          <ResGuestRPH RPH="3"/>
        </ResGuestRPHs>
        <ServiceRPHs>
          <ServiceRPH RPH="1"/>
        </ServiceRPHs>
      </RoomStay>
      <RoomStay RPH="1">
        <RoomTypes>
          <RoomType RoomTypeCode="RMSPD20000"/>
        </RoomTypes>
        <TimeSpan End="2020-06-16" Start="2020-06-12"/>
        <Total AmountAfterTax = "1203.31" CurrencyCode ="EUR" />
        <BasicPropertyInfo HotelCode="AUSNYC9WO8"/>
        <ResGuestRPHs>
          <ResGuestRPH RPH="4"/>
          <ResGuestRPH RPH="5"/>
        </ResGuestRPHs>
        <ServiceRPHs>
          <ServiceRPH RPH="1"/>
        </ServiceRPHs>
      </RoomStay>
    </RoomStays>
    <Services>
      <Service ServiceInventoryCode="RO" ServiceRPH="1"/>
    </Services>
    <ResGuests>
      <ResGuest AgeQualifyingCode="10" ResGuestRPH="1">
        <GuestCounts>
          <GuestCount Age="32"/>
        </GuestCounts>
      </ResGuest>
      <ResGuest AgeQualifyingCode="10" ResGuestRPH="2">
        <GuestCounts>
          <GuestCount Age="32"/>
        </GuestCounts>
      </ResGuest>
      <ResGuest AgeQualifyingCode="7" ResGuestRPH="3">
        <GuestCounts>
          <GuestCount Age="1"/>
        </GuestCounts>
      </ResGuest>
      <ResGuest AgeQualifyingCode="10" ResGuestRPH="4">
        <GuestCounts>
          <GuestCount Age="30"/>
        </GuestCounts>
      </ResGuest>
      <ResGuest AgeQualifyingCode="10" ResGuestRPH="5">
        <GuestCounts>
          <GuestCount Age="30"/>
        </GuestCounts>
      </ResGuest>
    </ResGuests>
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
$OTA_HotelResRS = $inputDoc->getElementsByTagName("OTA_HotelResRS");

$HotelReservations = $OTA_HotelResRS->item(0)->getElementsByTagName("HotelReservations");
if ($HotelReservations->length > 0) {
  $HotelReservation = $HotelReservations->item(0)->getElementsByTagName("HotelReservation");
  if ($HotelReservation->length > 0) {
    $RoomStays = $HotelReservation->item(0)->getElementsByTagName("RoomStays");
    if ($RoomStays->length > 0) {
      $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
      if ($RoomStay->length > 0) {
        $IndexNumber = $RoomStay->item(0)->getAttribute("IndexNumber");
        $RPH = $RoomStay->item(0)->getAttribute("RPH");
        $Total = $RoomStay->item(0)->getElementsByTagName("Total");
        if ($Total->length > 0) {
          $AmountAfterTax = $Total->item(0)->getElementsByTagName("AmountAfterTax");
          $CurrencyCode = $Total->item(0)->getElementsByTagName("CurrencyCode");
        } else {
          $AmountAfterTax = "";
          $CurrencyCode = "";
        }
      }
    }
    //ResGlobalInfo
    $ResGlobalInfo = $HotelReservation->item(0)->getElementsByTagName("ResGlobalInfo");
    if ($ResGlobalInfo->length > 0) {
      $TotalRGI = $ResGlobalInfo->item(0)->getElementsByTagName("Total");
      if ($TotalRGI->length > 0) {
        $AmountAfterTaxRGI = $TotalRGI->item(0)->getElementsByTagName("AmountAfterTax");
        $CurrencyCodeRGI = $TotTotalRGIal->item(0)->getElementsByTagName("CurrencyCode");
      } else {
        $AmountAfterTaxRGI = "";
        $CurrencyCodeRGI = "";
      }
      $CancelPenalties = $ResGlobalInfo->item(0)->getElementsByTagName("CancelPenalties");
      if ($CancelPenalties->length > 0) {
        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
        if ($CancelPenalty->length > 0) {
          for ($i=0; $i < $CancelPenalty->length; $i++) { 
            $Item_RPH = $CancelPenalty->item($i)->getAttribute("Item_RPH");
            //Deadline
            $Deadline = $CancelPenalty->item($i)->getElementsByTagName("Deadline");
            if ($Deadline->length > 0) {
              $OffsetDropTime = $Deadline->item(0)->getAttribute("OffsetDropTime");
              $OffsetTimeUnit = $Deadline->item(0)->getAttribute("OffsetTimeUnit");
              $OffsetUnitMultiplier = $Deadline->item(0)->getAttribute("OffsetUnitMultiplier");
            }
            //AmountPercent
            $AmountPercent = $CancelPenalty->item($i)->getElementsByTagName("AmountPercent");
            if ($AmountPercent->length > 0) {
              $Percent = $AmountPercent->item(0)->getAttribute("Percent");
              $NmbrOfNights = $AmountPercent->item(0)->getAttribute("NmbrOfNights");
            }
          }
        }
      }
    }
    //TPA_Extensions
    $TPA_Extensions = $HotelReservation->item(0)->getElementsByTagName("TPA_Extensions");
    if ($TPA_Extensions->length > 0) {
      $BookingStatus = $TPA_Extensions->item(0)->getElementsByTagName("BookingStatus");
      if ($BookingStatus->length > 0) {
        $ReservationStatusType = $BookingStatus->item(0)->getAttribute("ReservationStatusType");
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