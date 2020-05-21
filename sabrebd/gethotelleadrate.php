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
echo "COMECOU HOTEL LEAD RATE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://sws-crt.cert.havail.sabre.com';

date_default_timezone_set("UTC");
$datetime = date('Y-m-d\TH:i:s');
$CPAId = 'IA8H';
$BinarySecurityToken = 'Shared/IDL:IceSess\/SessMgr:1\.0.IDL/Common/!ICESMS\/ACPCRTD!ICESMSLB\/CRT.LB!1586774481758!4960!17';

$raw = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<SOAP-ENV:Header>
    <eb:MessageHeader eb:version="3.0.0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader">
        <eb:From>
            <eb:PartyId>111</eb:PartyId>
        </eb:From>
        <eb:To>
            <eb:PartyId>222</eb:PartyId>
        </eb:To>
        <eb:CPAId>IA8H</eb:CPAId>
        <eb:ConversationId>1234567890</eb:ConversationId>
        <eb:Action>GetHotelLeadRateRQ</eb:Action>
        <eb:MessageData>
            <eb:MessageId>LbQ26Jnofb4Q8f3Pk15Mg5</eb:MessageId>
            <eb:Timestamp>2020-04-14T17:30:50</eb:Timestamp>
        </eb:MessageData>
            </eb:MessageHeader>
        <wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext">
        <wsse:BinarySecurityToken>Shared/IDL:IceSess\/SessMgr:1\.0.IDL/Common/!ICESMS\/ACPCRTD!ICESMSLB\/CRT.LB!1586851286066!9014!13</wsse:BinarySecurityToken>
    </wsse:Security>
</SOAP-ENV:Header>
<SOAP-ENV:Body>
   <GetHotelLeadRateRQ xmlns="http://services.sabre.com/hotel/leadrate/v3_0_0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="3.0.0" xsi:schemaLocation="http://services.sabre.com/hotel/leadrate/v3_0_0 GetHotelLeadRateRQ_V3.xsd">
  <HotelRefs>
            <HotelRef HotelCode="100015408" CodeContext="GLOBAL"/>
        </HotelRefs>
        <RateInfoRef CurrencyCode="USD" PrepaidQualifier="IncludePrepaid" BestOnly="1">
            <StayDateRange StartDate="2020-11-22" EndDate="2020-11-24"/>
            <RateRange Min="0.10" Max="5000.0"/>
            <Rooms>
                <Room Index="1" Adults="2" Children="0">
                </Room>
            </Rooms>
            <InfoSource>100,112,113</InfoSource>
        </RateInfoRef>
    </GetHotelLeadRateRQ>
</SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Content-Type: text/xml;charset=utf-8",
    "Accept-Encoding: gzip",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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
$Header = $Envelope->item(0)->getElementsByTagName("Header");
$MessageHeader = $Header->item(0)->getElementsByTagName("MessageHeader");
if ($MessageHeader->length > 0) {
    $From = $MessageHeader->item(0)->getElementsByTagName("From");
    if ($From->length > 0) {
        $FromPartyId = $From->item(0)->getElementsByTagName("PartyId");
        if ($FromPartyId->length > 0) {
            $type = $FromPartyId->item(0)->getAttribute("type");
            $FromPartyId = $FromPartyId->item(0)->nodeValue;
        } else {
            $FromPartyId = "";
        }
    }
    $To = $MessageHeader->item(0)->getElementsByTagName("To");
    if ($To->length > 0) {
        $ToPartyId = $To->item(0)->getElementsByTagName("PartyId");
        if ($ToPartyId->length > 0) {
            $ToPartyId = $ToPartyId->item(0)->nodeValue;
        } else {
            $ToPartyId = "";
        }
    }
    $CPAId = $MessageHeader->item(0)->getElementsByTagName("CPAId");
    if ($CPAId->length > 0) {
        $CPAId = $CPAId->item(0)->nodeValue;
    } else {
        $CPAId = "";
    }
    $ConversationId = $MessageHeader->item(0)->getElementsByTagName("ConversationId");
    if ($ConversationId->length > 0) {
        $ConversationId = $ConversationId->item(0)->nodeValue;
    } else {
        $ConversationId = "";
    }
    $MessageData = $MessageHeader->item(0)->getElementsByTagName("MessageData");
    if ($MessageData->length > 0) {
        $MessageId = $MessageData->item(0)->getElementsByTagName("MessageId");
        if ($MessageId->length > 0) {
            $MessageId = $MessageId->item(0)->nodeValue;
        } else {
            $MessageId = "";
        }
        $RefToMessageId = $MessageData->item(0)->getElementsByTagName("RefToMessageId");
        if ($RefToMessageId->length > 0) {
            $RefToMessageId = $RefToMessageId->item(0)->nodeValue;
        } else {
            $RefToMessageId = "";
        }
    }
}
$Security = $Header->item(0)->getElementsByTagName("Security");
if ($Security->length > 0) {
    $BinarySecurityToken = $Security->item(0)->getElementsByTagName("BinarySecurityToken");
    if ($BinarySecurityToken->length > 0) {
        $BinarySecurityToken = $BinarySecurityToken->item(0)->nodeValue;
    } else {
        $BinarySecurityToken = "";
    }
}

$Body = $Envelope->item(0)->getElementsByTagName("Body");
$GetHotelLeadRateRS = $Body->item(0)->getElementsByTagName("GetHotelLeadRateRS");
if ($GetHotelLeadRateRS->length > 0) {
    $HotelRateInfos = $GetHotelLeadRateRS->item(0)->getElementsByTagName("HotelRateInfos");
    if ($HotelRateInfos->length > 0) {
        $HotelRateInfo = $HotelRateInfos->item(0)->getElementsByTagName("HotelRateInfo");
        if ($HotelRateInfo->length > 0) {
            $HotelInfo = $HotelRateInfo->item(0)->getElementsByTagName("HotelInfo");
            if ($HotelInfo->length > 0) {
                $HotelCode = $HotelInfo->item(0)->getAttribute("HotelCode");
                $SabreHotelCode = $HotelInfo->item(0)->getAttribute("SabreHotelCode");
                $CodeContext = $HotelInfo->item(0)->getAttribute("CodeContext");
            }
            $RateInfos = $HotelRateInfo->item(0)->getElementsByTagName("RateInfos");
            if ($RateInfos->length > 0) {
                $ShopKey = $RateInfos->item(0)->getAttribute("ShopKey");
                $RateInfo = $HotelRateInfo->item(0)->getElementsByTagName("RateInfo");
                if ($RateInfo->length > 0) {
                    for ($r=0; $r < $RateInfo->length; $r++) { 
                        $TaxInclusive = $RateInfo->item($r)->getAttribute("TaxInclusive");
                        $EndDate = $RateInfo->item($r)->getAttribute("EndDate");
                        $RateSource = $RateInfo->item($r)->getAttribute("RateSource");
                        $RateKey = $RateInfo->item($r)->getAttribute("RateKey");
                        $AverageNightlyRate = $RateInfo->item($r)->getAttribute("AverageNightlyRate");
                        $IncidentalsInclusive = $RateInfo->item($r)->getAttribute("IncidentalsInclusive");
                        $AmountBeforeTax = $RateInfo->item($r)->getAttribute("AmountBeforeTax");
                        $AdditionalFeesInclusive = $RateInfo->item($r)->getAttribute("AdditionalFeesInclusive");
                        $AmountAfterTax = $RateInfo->item($r)->getAttribute("AmountAfterTax");
                        $CurrencyCode = $RateInfo->item($r)->getAttribute("CurrencyCode");
                        $StartDate = $RateInfo->item($r)->getAttribute("StartDate");
                        $Commission = $RateInfo->item($r)->getElementsByTagName("Commission");
                        if ($Commission->length > 0) {
                            $Type = $Commission->item(0)->getAttribute("Type");
                            $Percent = $Commission->item(0)->getAttribute("Percent");
                            $CommissionDescription = $Commission->item(0)->getElementsByTagName("CommissionDescription");
                            if ($CommissionDescription->length > 0) {
                                $Text = $CommissionDescription->item(0)->getElementsByTagName("Text");
                                if ($Text->length > 0) {
                                    $Text = $Text->item(0)->nodeValue;
                                } else {
                                    $Text = "";
                                }
                            }
                        }
                    }
                }
            }
            $Rooms = $HotelRateInfo->item(0)->getElementsByTagName("Rooms");
            if ($Rooms->length > 0) {
                $Room = $Rooms->item(0)->getElementsByTagName("Room");
                if ($Room->length > 0) {
                    for ($x=0; $x < $Room->length; $x++) { 
                        $RoomIndex = $Room->item($x)->getAttribute("RoomIndex");
                        $RoomTypeCode = $Room->item($x)->getAttribute("RoomTypeCode");
                        $RoomDescription = $Room->item($x)->getElementsByTagName("RoomDescription");
                        if ($RoomDescription->length > 0) {
                            $RoomDescriptionName = $RoomDescription->item(0)->getAttribute("Name");
                            $Text = $RoomDescription->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                $Text = $Text->item(0)->nodeValue;
                            } else {
                                $Text = "";
                            }
                        }
                        $RatePlans = $Room->item($x)->getElementsByTagName("RatePlans");
                        if ($RatePlans->length > 0) {
                            $RatePlan = $RatePlans->item(0)->getElementsByTagName("RatePlan");
                            if ($RatePlan->length > 0) {
                                $RateSource = $RatePlan->item(0)->getAttribute("RateSource");
                                $RateKey = $RatePlan->item(0)->getAttribute("RateKey");
                                $RateSource = $RatePlan->item(0)->getAttribute("RateSource");
                                $RatePlanCode = $RatePlan->item(0)->getAttribute("RatePlanCode");
                                $LimitedAvailability = $RatePlan->item(0)->getAttribute("LimitedAvailability");
                                $PrepaidIndicator = $RatePlan->item(0)->getAttribute("PrepaidIndicator");
                                $RatePlanName = $RatePlan->item(0)->getAttribute("RatePlanName");
                                $ProductCode = $RatePlan->item(0)->getAttribute("ProductCode");
                                $RatePlanType = $RatePlan->item(0)->getAttribute("RatePlanType");
                                $RateInfo = $RatePlan->item(0)->getElementsByTagName("RateInfo");
                                if ($RateInfo->length > 0) {
                                    $TaxInclusive = $RateInfo->item(0)->getAttribute("TaxInclusive");
                                    $EndDate = $RateInfo->item(0)->getAttribute("EndDate");
                                    $AverageNightlyRate = $RateInfo->item(0)->getAttribute("AverageNightlyRate");
                                    $IncidentalsInclusive = $RateInfo->item(0)->getAttribute("IncidentalsInclusive");
                                    $AmountBeforeTax = $RateInfo->item(0)->getAttribute("AmountBeforeTax");
                                    $AdditionalFeesInclusive = $RateInfo->item(0)->getAttribute("AdditionalFeesInclusive");
                                    $AmountAfterTax = $RateInfo->item(0)->getAttribute("AmountAfterTax");
                                    $CurrencyCode = $RateInfo->item(0)->getAttribute("CurrencyCode");
                                    $StartDate = $RateInfo->item(0)->getAttribute("StartDate");
                                    $Taxes = $RateInfo->item(0)->getElementsByTagName("Taxes");
                                    if ($Taxes->length > 0) {
                                        $TaxesCurrencyCode = $Taxes->item(0)->getAttribute("CurrencyCode");
                                        $TaxesAmount = $Taxes->item(0)->getAttribute("Amount");
                                    }
                                    $RoomExtras = $RateInfo->item(0)->getElementsByTagName("RoomExtras");
                                    if ($RoomExtras->length > 0) {
                                        $RoomExtra = $RoomExtras->item(0)->getElementsByTagName("RoomExtra");
                                        if ($RoomExtra->length > 0) {
                                            for ($y=0; $y < $RoomExtra->length; $y++) { 
                                                $Code = $RoomExtra->item($y)->getAttribute("Code");
                                                $Amount = $RoomExtra->item($y)->getAttribute("Amount");
                                                $CurrencyCode = $RoomExtra->item($y)->getAttribute("CurrencyCode");
                                                $Text = $RoomExtra->item($y)->getElementsByTagName("Text");
                                                if ($Text->length > 0) {
                                                    $Text = $Text->item(0)->nodeValue;
                                                } else {
                                                    $Text = "";
                                                }
                                            }
                                        }
                                    }
                                    $CancelPenalties = $RateInfo->item(0)->getElementsByTagName("CancelPenalties");
                                    if ($CancelPenalties->length > 0) {
                                        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                                        if ($CancelPenalty->length > 0) {
                                            $Refundable = $CancelPenalty->item(0)->getAttribute("Refundable");
                                            $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                                            if ($Deadline->length > 0) {
                                                $OffsetUnitMultiplier = $Deadline->item(0)->getAttribute("OffsetUnitMultiplier");
                                                $OffsetTimeUnit = $Deadline->item(0)->getAttribute("OffsetTimeUnit");
                                                $OffsetDropTime = $Deadline->item(0)->getAttribute("OffsetDropTime");
                                            }
                                        }
                                    }
                                    $Guarantee = $RateInfo->item(0)->getElementsByTagName("Guarantee");
                                    if ($Guarantee->length > 0) {
                                        $GuaranteeType = $Guarantee->item(0)->getAttribute("GuaranteeType");
                                        $GuaranteesAccepted = $Guarantee->item(0)->getElementsByTagName("GuaranteesAccepted");
                                        if ($GuaranteesAccepted->length > 0) {
                                            $GuaranteeAccepted = $GuaranteesAccepted->item(0)->getElementsByTagName("GuaranteeAccepted");
                                            if ($GuaranteeAccepted->length > 0) {
                                                for ($z=0; $z < $GuaranteeAccepted->length; $z++) { 
                                                    $GuaranteeTypeCode = $GuaranteeAccepted->item($z)->getAttribute("GuaranteeTypeCode");
                                                    $PaymentCards = $GuaranteeAccepted->item($z)->getElementsByTagName("PaymentCards");
                                                    if ($PaymentCards->length > 0) {
                                                        $PaymentCard = $PaymentCards->item(0)->getElementsByTagName("PaymentCard");
                                                        if ($PaymentCard->length > 0) {
                                                            $payment = "";
                                                            for ($zAux=0; $zAux < $PaymentCard->length; $zAux++) { 
                                                                $CardCode = $PaymentCard->item($zAux)->getAttribute("CardCode");
                                                                $payment = $PaymentCard->item($zAux)->nodeValue;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
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
