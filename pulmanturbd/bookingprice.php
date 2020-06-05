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
echo "COMECOU BOOK PRICE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
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

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:book="http://services.rccl.com/Interfaces/BookingPrice" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
<soapenv:Header/>
<soapenv:Body>
  <book:getBookingPrice>
    <OTA_CruisePriceBookingRQ Version="1.0" SequenceNmbr="1" Target="Test" TimeStamp="2008-12-30T18:30:42.720+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
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
      <SailingInfo>
        <SelectedSailing Start="2021-02-07">
          <CruiseLine ShipCode="HR" VendorCode="PUL"/>
        </SelectedSailing>
        <InclusivePackageOption CruisePackageCode="HRPO0720" InclusiveIndicator="false"/>
        <SelectedCategory BerthedCategoryCode="GS" FareCode="BESTRATE" PricedCategoryCode="GS">
          <SelectedCabin CabinNumber="1071" Status="36"/>
        </SelectedCategory>
      </SailingInfo>
      <ReservationInfo>
        <GuestDetails>
            <GuestDetail>
                <SelectedFareCode FareCode="BESTRATE"/>
                <ContactInfo Nationality="ES" Age="30" BirthDate="1990-05-01" ContactType="CNT" Code="10">
                <PersonName>
                    <GivenName>David</GivenName>
                    <Surname>Sanchez LLorente</Surname>
                    <NameTitle>MR</NameTitle>
                </PersonName>
                </ContactInfo>
                <ContactInfo ContactType="ALT">
                    <PersonName>
                    <GivenName>David</GivenName>
                    <Surname>Sanchez LLorente</Surname>
                    </PersonName>
                    <Address>
                    <AddressLine>MAhonia 2</AddressLine>
                    <CityName>Madrid</CityName>
                    <PostalCode>28043</PostalCode>
                    <CountryName Code="ES"/>
                    </Address>
                    <Email>davizch2004@gmail.com</Email>
                </ContactInfo>
                <ContactInfo ContactType="EMG">
                    <PersonName>
                    <GivenName>David</GivenName>
                    <Surname>Sanchez LLorente</Surname>
                    </PersonName>
                    <Telephone PhoneNumber="9871236540"/>
                </ContactInfo>
                <GuestTransportation Status="36" Mode="29">
                    <GatewayCity LocationCode="C/O"/>
                </GuestTransportation>
                <SelectedDining Status="36" Sitting="M"/>
                <CruiseDocument DocumentLanguage="ES"/>
            </GuestDetail>
        </GuestDetails>
      </ReservationInfo>
    </OTA_CruisePriceBookingRQ>
  </book:getBookingPrice>
</soapenv:Body>
</soapenv:Envelope>';

$url = 'https://stage.services.rccl.com/Reservation_FITWeb/sca/BookingPrice';

$username = 'CONCTMM';
$password = 'u73ecKBu73ecKB!';


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
$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
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
$getBookingPriceResponse = $Body->item(0)->getElementsByTagName("getBookingPriceResponse");
$OTA_CruisePriceBookingRS = $getBookingPriceResponse->item(0)->getElementsByTagName("OTA_CruisePriceBookingRS");
//ReservationID
$ReservationID = $OTA_CruisePriceBookingRS->item(0)->getElementsByTagName("ReservationID");
if ($ReservationID->length > 0) {
    $ID = $ReservationID->item(0)->getAttribute("ID");
    $Type = $ReservationID->item(0)->getAttribute("Type");
}

//SailingInfo
$SailingInfo = $OTA_CruisePriceBookingRS->item(0)->getElementsByTagName("SailingInfo");
if ($SailingInfo->length > 0) {
    $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
    if ($InclusivePackageOption->length > 0) {
        $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
    }
    $SelectedCategory = $SailingInfo->item(0)->getElementsByTagName("SelectedCategory");
    if ($SelectedCategory->length > 0) {
        $PromotionDescription = $SelectedCategory->item(0)->getAttribute("PromotionDescription");
        $FareCode = $SelectedCategory->item(0)->getAttribute("FareCode");
    }
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('booking');
    $insert->values(array(
        'ID' => $ID,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'Type' => $Type,
        'CruisePackageCode' => $CruisePackageCode,
        'PromotionDescription' => $PromotionDescription,
        'FareCode' => $FareCode
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
} catch (\Exception $e) {
    echo $return;
    echo "ERRO B: " . $e;
    echo $return;
}

//BookingPayment
$BookingPayment = $OTA_CruisePriceBookingRS->item(0)->getElementsByTagName("BookingPayment");
if ($BookingPayment->length > 0) {
    $BookingPrices = $BookingPayment->item(0)->getElementsByTagName("BookingPrices");
    if ($BookingPrices->length > 0) {
        $node = $BookingPrices->item(0)->getElementsByTagName("BookingPrice");
        if ($node->length > 0) {
            for ($i=0; $i < $node->length; $i++) { 
                $PriceTypeCode = $node->item($i)->getAttribute("PriceTypeCode");
                $Amount = $node->item($i)->getAttribute("Amount");

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('bookingprice');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'PriceTypeCode' => $PriceTypeCode,
                        'Amount' => $Amount
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO BPR: " . $e;
                    echo $return;
                }
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

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('payment');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Amount' => $Amount,
                        'PaymentNumber' => $PaymentNumber,
                        'DueDate' => $DueDate
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO PS: " . $e;
                    echo $return;
                }
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

                            try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('priceinfo');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'Amount' => $Amount,
                                    'PriceTypeCode' => $PriceTypeCode,
                                    'SelectedOptionsIndicator' => $SelectedOptionsIndicator,
                                    'PricedComponentType' => $PricedComponentType,
                                    'PricedComponentCode' => $PricedComponentCode,
                                    'OptionType' => $OptionType,
                                    'ItemizableIndicator' => $ItemizableIndicator,
                                    'CodeDetail' => $CodeDetail,
                                    'AutoAddedIndicator' => $AutoAddedIndicator
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO PI: " . $e;
                                echo $return;
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
