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
echo "COMECOU HOTEL SEARCH<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://wsi-test.americantours.com/ati-services/ATIInterface';

$username = '5m2z3i432';
$password = 'bmY9SWPELu';
$encode = $username . ":" . $password;
$auth = base64_encode("$encode");

$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.opentravel.org/OTA/2003/05">
<soap:Header/>
<soap:Body>
    <ns1:OTA_HotelAvailRQ Version="1.3">
        <POS>
            <Source ERSP_UserID="5m2z3i432"/>
        </POS>
        <AvailRequestSegments>
            <AvailRequestSegment AvailReqType="AMENITIES">
                <StayDateRange Start="2020-06-24" Duration="P0Y0M3D"></StayDateRange>
                <RoomStayCandidates>
                    <RoomStayCandidate>
                        <GuestCounts>
                            <GuestCount AgeQualifyingCode="10" Count="1"/>
                        </GuestCounts>
                    </RoomStayCandidate>
                </RoomStayCandidates>
                <HotelSearchCriteria>
                    <Criterion>
                        <HotelRef HotelCityCode="10203"/>
                    </Criterion>
                </HotelSearchCriteria>
            </AvailRequestSegment>
        </AvailRequestSegments>
    </ns1:OTA_HotelAvailRQ>
</soap:Body>
</soap:Envelope>';

$headers = array(
    "Content-type: text/xml;charset=UTF-8",
    "Accept-Encoding: gzip, deflate",
    "Authorization: Basic " . $auth,
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
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
$OTA_HotelAvailRS = $Body->item(0)->getElementsByTagName("OTA_HotelAvailRS");
if ($OTA_HotelAvailRS->length > 0) {
    $RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
    if ($RoomStays->length > 0) {
        $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
        if ($RoomStay->length > 0) {
            for ($i=0; $i < $RoomStay->length; $i++) { 
                $RoomTypes = $RoomStay->item($i)->getElementsByTagName("RoomTypes");
                if ($RoomTypes->length > 0) {
                    $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $IsRoom = $RoomType->item(0)->getAttribute("IsRoom");
                        $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                        $Amenities = $RoomType->item(0)->getElementsByTagName("Amenities");
                        if ($Amenities->length > 0) {
                            $Amenity = $Amenities->item(0)->getElementsByTagName("Amenity");
                            if ($Amenity->length > 0) {
                                $CodeDetail = $Amenity->item(0)->getAttribute("CodeDetail");
                                $RoomAmenityCode = $Amenity->item(0)->getAttribute("RoomAmenityCode");
                            }
                        }
                        $RoomDescription = $RoomType->item(0)->getElementsByTagName("RoomDescription");
                        if ($RoomDescription->length > 0) {
                            $Text = $RoomDescription->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                $Text = $Text->item(0)->nodeValue;
                            } else {
                                $Text = "";
                            }
                        }
                    }
                }
                $RoomRates = $RoomStay->item($i)->getElementsByTagName("RoomRates");
                if ($RoomRates->length > 0) {
                    $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                    if ($RoomRate->length > 0) {
                        $RatePlanCode = $RoomRate->item(0)->getAttribute("RatePlanCode");
                        $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                        if ($Rates->length > 0) {
                            $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                            if ($Rate->length > 0) {
                                for ($iAux=0; $iAux < $Rate->length; $iAux++) { 
                                    $EffectiveDate = $Rate->item($iAux)->getAttribute("EffectiveDate");
                                    $Base = $Rate->item($iAux)->getElementsByTagName("Base");
                                    if ($Base->length > 0) {
                                        $AmountAfterTax = $Base->item(0)->getAttribute("AmountAfterTax");
                                        $CurrencyCode = $Base->item(0)->getAttribute("CurrencyCode");
                                    }
                                }
                            }
                        }
                    }
                }
                $GuestCounts = $RoomStay->item($i)->getElementsByTagName("GuestCounts");
                if ($GuestCounts->length > 0) {
                    $IsPerRoom = $GuestCounts->item(0)->getAttribute("IsPerRoom");
                    $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
                    if ($GuestCount->length > 0) {
                        for ($iAux2=0; $iAux2 < $GuestCount->length; $iAux2++) { 
                            $AgeQualifyingCode = $GuestCount->item($iAux2)->getAttribute("AgeQualifyingCode");
                            $Count = $GuestCount->item($iAux2)->getAttribute("Count");
                            $ResGuestRPH = $GuestCount->item($iAux2)->getAttribute("ResGuestRPH");
                        }
                    }
                }
                $BasicPropertyInfo = $RoomStay->item($i)->getElementsByTagName("BasicPropertyInfo");
                if ($BasicPropertyInfo->length > 0) {
                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                    $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                }
                $CancelPenalties = $RoomStay->item($i)->getElementsByTagName("CancelPenalties");
                if ($CancelPenalties->length > 0) {
                    $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                    if ($CancelPenalty->length > 0) {
                        $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                        if ($Deadline->length > 0) {
                            $AbsoluteDeadline = $Deadline->item(0)->getAttribute("AbsoluteDeadline");
                        }
                    }
                }
                $Total = $RoomStay->item($i)->getElementsByTagName("Total");
                if ($Total->length > 0) {
                    $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                    $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                }
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>