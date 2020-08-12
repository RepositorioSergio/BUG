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
echo "COMECOU CONFIRM BOOK<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.rccl.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));

$username = 'CONCTMM';
$password = 'u73ecKBu73ecKB!';

$url = 'https://stage.services.rccl.com/Reservation_FITWeb/sca/RetrieveBooking';

$raw ='<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ret="http://services.rccl.com/Interfaces/RetrieveBooking" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
<soapenv:Header/>
<soapenv:Body>
   <ret:retrieveBooking>
   <OTA_ReadRQ TimeStamp="2013-09-03T18:35:46.317Z" Version="1.0" SequenceNmbr="1" RetransmissionIndicator="false" TransactionActionCode="RetrievePrice" Target="Test" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
        <POS>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="313917" ID_Context="AGENCY1" Type="5"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="PULLMANTUR"/>
                </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="313917" ID_Context="AGENCY2" Type="5"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="PULLMANTUR"/>
                </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="313917" ID_Context="AGENT1" Type="5"/>
                <BookingChannel Type="7">
                    <CompanyName CompanyShortName="PULLMANTUR"/>
                </BookingChannel>
            </Source>
        </POS>
        <UniqueID ID="1921" Type="14"/>
   </OTA_ReadRQ>
   </ret:retrieveBooking>
   </soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.rccl.php');
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
$confirmBookingResponse = $Body->item(0)->getElementsByTagName("confirmBookingResponse");
if ($confirmBookingResponse->length > 0) {
    $OTA_CruiseBookRS = $confirmBookingResponse->item(0)->getElementsByTagName("OTA_CruiseBookRS");
    if ($OTA_CruiseBookRS->length > 0) {
        $ReservationID = $OTA_CruiseBookRS->item(0)->getElementsByTagName("ReservationID");
        if ($ReservationID->length > 0) {
            $ID = $ReservationID->item(0)->getAttribute("ID");
            $Type = $ReservationID->item(0)->getAttribute("Type");
            $StatusCode = $ReservationID->item(0)->getAttribute("StatusCode");
        }
        $SailingInfo = $OTA_CruiseBookRS->item(0)->getElementsByTagName("SailingInfo");
        if ($SailingInfo->length > 0) {
            $SelectedCategory = $SailingInfo->item(0)->getElementsByTagName("SelectedCategory");
            if ($SelectedCategory->length > 0) {
                $PromotionDescription = $SelectedCategory->item(0)->getAttribute("PromotionDescription");
                $FareCode = $SelectedCategory->item(0)->getAttribute("FareCode");
                $BookingNonRefundableType = $SelectedCategory->item(0)->getAttribute("BookingNonRefundableType");
                $NonRefundableType = $SelectedCategory->item(0)->getAttribute("NonRefundableType");
            }
        }
        $BookingPayment = $OTA_CruiseBookRS->item(0)->getElementsByTagName("BookingPayment");
        if ($BookingPayment->length > 0) {
            $BookingPrices = $BookingPayment->item(0)->getElementsByTagName("BookingPrices");
            if ($BookingPrices->length > 0) {
                $node = $BookingPrices->item(0)->getElementsByTagName("BookingPrice");
                if ($node->length > 0) {
                    for ($i=0; $i < $node->length; $i++) { 
                        $PriceTypeCode = $node->item($i)->getAttribute("PriceTypeCode");
                        $Amount = $node->item($i)->getAttribute("Amount");
                    }
                }
            }

            $PaymentSchedule = $BookingPayment->item(0)->getElementsByTagName("PaymentSchedule");
            if ($PaymentSchedule->length > 0) {
                $Payment = $PaymentSchedule->item(0)->getElementsByTagName("Payment");
                if ($Payment->length > 0) {
                    for ($j=0; $j < $Payment->length; $j++) { 
                        $Amount = $Payment->item($j)->getAttribute("Amount");
                        $PaymentNumber = $Payment->item($j)->getAttribute("PaymentNumber");
                        $DueDate = $Payment->item($j)->getAttribute("DueDate");
                    }
                }
            }

            //GuestPrices
            $GuestPrices = $BookingPayment->item(0)->getElementsByTagName("GuestPrices");
            if ($GuestPrices->length > 0) {
                $GuestPrice = $GuestPrices->item(0)->getElementsByTagName("GuestPrice");
                if ($GuestPrice->length > 0) {
                    for ($k=0; $k < $GuestPrice->length; $k++) { 
                        $PriceInfos = $GuestPrice->item($k)->getElementsByTagName("PriceInfos");
                        if ($PriceInfos->length > 0) {
                            $PriceInfo = $PriceInfos->item(0)->getElementsByTagName("PriceInfo");
                            if ($PriceInfo->length > 0) {
                                for ($kAux=0; $kAux < $PriceInfo->length; $kAux++) { 
                                    $Amount = $PriceInfo->item($kAux)->getAttribute("Amount");
                                    $PriceTypeCode = $PriceInfo->item($kAux)->getAttribute("PriceTypeCode");
                                    $SelectedOptionsIndicator = $PriceInfo->item($kAux)->getAttribute("SelectedOptionsIndicator");
                                    $PricedComponentType = $PriceInfo->item($kAux)->getAttribute("PricedComponentType");
                                    $PricedComponentCode = $PriceInfo->item($kAux)->getAttribute("PricedComponentCode");
                                    $OptionType = $PriceInfo->item($kAux)->getAttribute("OptionType");
                                    $ItemizableIndicator = $PriceInfo->item($kAux)->getAttribute("ItemizableIndicator");
                                    $CodeDetail = $PriceInfo->item($kAux)->getAttribute("CodeDetail");
                                    $AutoAddedIndicator = $PriceInfo->item($kAux)->getAttribute("AutoAddedIndicator");
                                }
                            }
                        }
                    }
                }
            }
            //PolicyInfo
            $PolicyInfo = $BookingPayment->item(0)->getElementsByTagName("PolicyInfo");
            if ($PolicyInfo->length > 0) {
                $Text = $PolicyInfo->item(0)->getElementsByTagName("Text");
                if ($Text->length > 0) {
                    $policy = "";
                    for ($i=0; $i < $Text->length; $i++) { 
                        $policy = $Text->item($i)->nodeValue;
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