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
echo "COMECOU VIEW RESERVATION<br/>";
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
    <ns1:OTA_ReadRQ Version="1.3">
        <ns1:ReadRequests>
            <ns1:ReadRequest>
                <ns1:UniqueID ID="6066762"></ns1:UniqueID>
            </ns1:ReadRequest>
        </ns1:ReadRequests>
    </ns1:OTA_ReadRQ>
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
$OTA_ResRetrieveRS = $Body->item(0)->getElementsByTagName("OTA_ResRetrieveRS");
if ($OTA_ResRetrieveRS->length > 0) {
    $Version = $OTA_ResRetrieveRS->item(0)->getAttribute("Version");
    $TimeStamp = $OTA_ResRetrieveRS->item(0)->getAttribute("TimeStamp");
    $TransactionIdentifier = $OTA_ResRetrieveRS->item(0)->getAttribute("TransactionIdentifier");
    $ReservationsList = $OTA_ResRetrieveRS->item(0)->getElementsByTagName("ReservationsList");
    if ($ReservationsList->length > 0) {
        $HotelReservation = $ReservationsList->item(0)->getElementsByTagName("HotelReservation");
        if ($HotelReservation->length > 0) {
            $ResStatus = $HotelReservation->item(0)->getAttribute("ResStatus");
            $RoomStays = $HotelReservation->item(0)->getElementsByTagName("RoomStays");
            if ($RoomStays->length > 0) {
                $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
                if ($RoomStay->length > 0) {
                    for ($i=0; $i < $RoomStay->length; $i++) { 
                        $RoomStayStatus = $RoomStay->item($i)->getAttribute("RoomStayStatus");
                        $indexNumber = $RoomStay->item($i)->getAttribute("indexNumber");
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
                        $TimeSpan = $RoomStay->item($i)->getElementsByTagName("TimeSpan");
                        if ($TimeSpan->length > 0) {
                            $Start = $TimeSpan->item(0)->getAttribute("Start");
                            $End = $TimeSpan->item(0)->getAttribute("End");
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
                    }
                }
            }
            $ResGuests = $HotelReservation->item(0)->getElementsByTagName("ResGuests");
            if ($ResGuests->length > 0) {
                $ResGuest = $ResGuests->item(0)->getElementsByTagName("ResGuest");
                if ($ResGuest->length > 0) {
                    $Profiles = $ResGuest->item(0)->getElementsByTagName("Profiles");
                    if ($Profiles->length > 0) {
                        $ProfileInfo = $Profiles->item(0)->getElementsByTagName("ProfileInfo");
                        if ($ProfileInfo->length > 0) {
                            $Profile = $ProfileInfo->item(0)->getElementsByTagName("Profile");
                            if ($Profile->length > 0) {
                                for ($iAux3=0; $iAux3 < $Profile->length; $iAux3++) { 
                                    $RPH = $Profile->item($iAux3)->getAttribute("RPH");
                                    $Customer = $Profile->item($iAux3)->getElementsByTagName("Customer");
                                    if ($Customer->length > 0) {
                                        $PersonName = $Customer->item(0)->getElementsByTagName("PersonName");
                                        if ($PersonName->length > 0) {
                                            $GivenName = $PersonName->item(0)->getElementsByTagName("GivenName");
                                            if ($GivenName->length > 0) {
                                                $GivenName = $GivenName->item(0)->nodeValue;
                                            } else {
                                                $GivenName = "";
                                            }
                                            $NamePrefix = $PersonName->item(0)->getElementsByTagName("NamePrefix");
                                            if ($NamePrefix->length > 0) {
                                                $NamePrefix = $NamePrefix->item(0)->nodeValue;
                                            } else {
                                                $NamePrefix = "";
                                            }
                                            $Surname = $PersonName->item(0)->getElementsByTagName("Surname");
                                            if ($Surname->length > 0) {
                                                $Surname = $Surname->item(0)->nodeValue;
                                            } else {
                                                $Surname = "";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $ResGlobalInfo = $HotelReservation->item(0)->getElementsByTagName("ResGlobalInfo");
            if ($ResGlobalInfo->length > 0) {
                $HotelReservationIDs = $ResGlobalInfo->item(0)->getElementsByTagName("HotelReservationIDs");
                if ($HotelReservationIDs->length > 0) {
                    $HotelReservationID = $HotelReservationIDs->item(0)->getElementsByTagName("HotelReservationID");
                    if ($HotelReservationID->length > 0) {
                        $ResID_Value = $HotelReservationID->item(0)->getAttribute("ResID_Value");
                    }
                }
                $Total = $ResGlobalInfo->item(0)->getElementsByTagName("Total");
                if ($Total->length > 0) {
                    $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                    $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                }
            }
        }
    }
    $POS = $OTA_ResRetrieveRS->item(0)->getElementsByTagName("POS");
    if ($POS->length > 0) {
        $Source = $POS->item(0)->getElementsByTagName("Source");
        if ($Source->length > 0) {
            $ERSP_UserID = $Source->item(0)->getAttribute("ERSP_UserID");
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>