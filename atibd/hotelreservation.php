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
echo "COMECOU HOTEL RESERVATION<br/>";
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
    <ns1:OTA_HotelResRQ Version="1.3" TransactionIdentifier="TEST15">
        <ns1:POS>
            <ns1:Source ERSP_UserID="5m2z3i432"/>
        </ns1:POS>
        <ns1:HotelReservations>
            <ns1:HotelReservation>
                <ns1:RoomStays>
                    <ns1:RoomStay>
                        <ns1:RoomRates>
                            <ns1:RoomRate RatePlanCode="Available">
                                <ns1:Rates>
                                    <ns1:Rate EffectiveDate="2020-12-12">
                                        <ns1:Base AmountAfterTax="10000" CurrencyCode="USD"/>
                                    </ns1:Rate>
                                    <ns1:Rate EffectiveDate="2020-12-13">
                                        <ns1:Base AmountAfterTax="10000" CurrencyCode="USD"/>
                                    </ns1:Rate>
                                    <ns1:Rate EffectiveDate="2020-12-14">
                                        <ns1:Base AmountAfterTax="10000" CurrencyCode="USD"/>
                                    </ns1:Rate>
                                    <ns1:Rate EffectiveDate="2020-12-15">
                                        <ns1:Base AmountAfterTax="10000" CurrencyCode="USD"/>
                                    </ns1:Rate>
                                    <ns1:Rate EffectiveDate="2020-12-16">
                                        <ns1:Base AmountAfterTax="10000" CurrencyCode="USD"/>
                                    </ns1:Rate>
                                    <ns1:Rate EffectiveDate="2020-12-17">
                                        <ns1:Base AmountAfterTax="10000" CurrencyCode="USD"/>
                                    </ns1:Rate>
                                    <ns1:Rate EffectiveDate="2020-12-18">
                                        <ns1:Base AmountAfterTax="0" CurrencyCode="USD"/>
                                    </ns1:Rate>
                                </ns1:Rates>
                            </ns1:RoomRate>
                        </ns1:RoomRates>
                        <ns1:CancelPenalties>
                            <ns1:CancelPenalty>
                                <ns1:Deadline AbsoluteDeadline="This reservation cannot be cancelled." />
                            </ns1:CancelPenalty>
                        </ns1:CancelPenalties>
                        <ns1:GuestCounts>
                            <ns1:GuestCount AgeQualifyingCode="10" Age="24" ResGuestRPH="1"/>
                            <ns1:GuestCount AgeQualifyingCode="10" Age="20" ResGuestRPH="2"/>
                        </ns1:GuestCounts>
                        <ns1:TimeSpan Duration="P0Y0M7D" Start="2020-12-12" End="2020-12-19"/>
                        <ns1:BasicPropertyInfo HotelCode="ZZZHNL-FIT"/>
                    </ns1:RoomStay>
                </ns1:RoomStays>
                <ns1:ResGuests>
                    <ns1:ResGuest>
                        <ns1:Profiles>
                            <ns1:ProfileInfo>
                                <ns1:Profile RPH="1">
                                    <ns1:Customer>
                                        <ns1:PersonName>
                                            <ns1:GivenName>Walter</ns1:GivenName>
                                            <ns1:NamePrefix>Mr</ns1:NamePrefix>
                                            <ns1:Surname>Jenkins</ns1:Surname>
                                        </ns1:PersonName>
                                    </ns1:Customer>
                                </ns1:Profile>
                                <ns1:Profile RPH="2">
                                    <ns1:Customer>
                                        <ns1:PersonName>
                                            <ns1:GivenName>Mary</ns1:GivenName>
                                            <ns1:NamePrefix>Mrs</ns1:NamePrefix>
                                            <ns1:Surname>Jenkins</ns1:Surname>
                                        </ns1:PersonName>
                                    </ns1:Customer>
                                </ns1:Profile>
                            </ns1:ProfileInfo>
                        </ns1:Profiles>
                    </ns1:ResGuest>
                </ns1:ResGuests>
                <ns1:Guarantee>
                    <ns1:GuaranteesAccepted>
                        <ns1:GuaranteeAccepted>
                            <ns1:PaymentCard CardCode="VI" CardNumber="4111111111111111"
                                SeriesCode="123" ExpireDate="0123">
                                <ns1:Address>
                                    <ns1:StateProv>California</ns1:StateProv>
                                    <ns1:AddressLine>653 W Century blvd</ns1:AddressLine>
                                    <ns1:CityName>Los Angeles</ns1:CityName>
                                    <ns1:PostalCode>90210</ns1:PostalCode>
                                    <ns1:CountryName Code="US"/>
                                </ns1:Address>
                                <ns1:CardHolderName>Walter Jenkins</ns1:CardHolderName>
                                <ns1:Telephone PhoneNumber="818-111-2222"/>
                            </ns1:PaymentCard>
                        </ns1:GuaranteeAccepted>
                    </ns1:GuaranteesAccepted>
                </ns1:Guarantee>
            </ns1:HotelReservation>
        </ns1:HotelReservations>
    </ns1:OTA_HotelResRQ>
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
$OTA_HotelResRS = $Body->item(0)->getElementsByTagName("OTA_HotelResRS");
if ($OTA_HotelResRS->length > 0) {
    $Version = $OTA_HotelResRS->item(0)->getAttribute("Version");
    $TimeStamp = $OTA_HotelResRS->item(0)->getAttribute("TimeStamp");
    $HotelReservations = $OTA_HotelResRS->item(0)->getElementsByTagName("HotelReservations");
    if ($HotelReservations->length > 0) {
        $ResStatus = $HotelReservations->item(0)->getAttribute("ResStatus");
        $RoomStays = $HotelReservations->item(0)->getElementsByTagName("RoomStays");
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
        $ResGuests = $HotelReservations->item(0)->getElementsByTagName("ResGuests");
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
        $ResGlobalInfo = $HotelReservations->item(0)->getElementsByTagName("ResGlobalInfo");
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
        $UniqueID = $HotelReservations->item(0)->getElementsByTagName("UniqueID");
        if ($UniqueID->length > 0) {
            $ID = $UniqueID->item(0)->getAttribute("ID");
        }
    }
    $POS = $OTA_HotelResRS->item(0)->getElementsByTagName("POS");
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