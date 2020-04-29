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

$url = "https://stage.services.rccl.com/Interfaces/OptionList";

$raw ='<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
    <ns30:confirmBooking xmlns="http://www.opentravel.org/OTA/2003/05/alpha"
    xmlns:ns2="http://services.rccl.com/Interfaces/PackageList" xmlns:ns3="http://services.rccl.com/Interfaces/CabinDetail"
    xmlns:ns4="http://services.rccl.com/Interfaces/FastSell" xmlns:ns5="http://services.rccl.com/Interfaces/GuestServiceList"
    xmlns:ns6="http://services.rccl.com/Interfaces/BookingHistory" xmlns:ns7="http://services.rccl.com/Interfaces/ReleaseCabin"
    xmlns:ns8="http://services.rccl.com/Interfaces/TourList" xmlns:ns9="http://services.rccl.com/Interfaces/LookupAgency"
    xmlns:ns10="http://services.rccl.com/Interfaces/ReleaseBooking" xmlns:ns11="http://services.rccl.com/Interfaces/PromotionList"
    xmlns:ns12="http://services.rccl.com/Interfaces/TourDetail" xmlns:ns13="http://services.rccl.com/Interfaces/AirAvail"
    xmlns:ns14="http://services.rccl.com/Interfaces/OptionList" xmlns:ns15="http://services.rccl.com/Interfaces/BookingList"
    xmlns:ns16="http://services.rccl.com/Interfaces/FareDetail" xmlns:ns17="http://services.rccl.com/Interfaces/PaymentExtension"
    xmlns:ns18="http://services.rccl.com/Interfaces/Payment" xmlns:ns19="http://services.rccl.com/Interfaces/TransferDetail"
    xmlns:ns20="http://services.rccl.com/Interfaces/DiningList" xmlns:ns21="http://services.rccl.com/Interfaces/TransferList"
    xmlns:ns22="http://services.rccl.com/Interfaces/ConfirmAir" xmlns:ns23="http://services.rccl.com/Interfaces/BookingDocument"
    xmlns:ns24="http://services.rccl.com/Interfaces/BusDetail" xmlns:ns25="http://services.rccl.com/Interfaces/CabinList"
    xmlns:ns26="http://services.rccl.com/Interfaces/Login" xmlns:ns27="http://services.rccl.com/Interfaces/OptionDetail"
    xmlns:ns28="http://services.rccl.com/Interfaces/ItineraryDetail" xmlns:ns29="http://services.rccl.com/Interfaces/SailingList"
    xmlns:ns30="http://services.rccl.com/Interfaces/ConfirmBooking" xmlns:ns31="http://services.rccl.com/Interfaces/LinkedBooking"
    xmlns:ns32="http://services.rccl.com/Interfaces/RetrieveBooking" xmlns:ns33="http://services.rccl.com/Interfaces/CategoryList"
    xmlns:ns34="http://services.rccl.com/Interfaces/FareList" xmlns:ns35="http://services.rccl.com/Interfaces/PackageDetail"
    xmlns:ns36="http://services.rccl.com/Interfaces/AutoAddChargeDetail" xmlns:ns37="http://services.rccl.com/Interfaces/Memo"
    xmlns:ns38="http://services.rccl.com/Interfaces/BookingPrice" xmlns:ns39="http://services.rccl.com/Interfaces/HoldCabin"
    xmlns:ns40="http://services.rccl.com/Interfaces/BusList">
    <OTA_CruiseBookRQ SequenceNmbr="1" Version="1" TransactionActionCode="Commit">
    <POS>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="279796" ID_Context="AGENCY1" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="279796" ID_Context="AGENCY2" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="EUR">
            <RequestorID ID="279796" ID_Context="AGENT1" Type="5"/>
            <BookingChannel Type="7">
                <CompanyName CompanyShortName="IST"/>
            </BookingChannel>
        </Source>
    </POS>
    <AgentInfo Contact="1234"/>
    <SailingInfo>
    <SelectedSailing Start="2014-08-02">
        <CruiseLine ShipCode="MO"/>
    </SelectedSailing>
    <InclusivePackageOption CruisePackageCode="MOPS0702"/>
    <SelectedCategory PricedCategoryCode="JT" BerthedCategoryCode="JT" FareCode="A0686033" WaitlistIndicator="false">
        <SelectedCabin CabinNumber="1548" Status="39">
            <CabinConfiguration BedConfigurationCode="T"/>
        </SelectedCabin>
    </SelectedCategory>
    </SailingInfo>
    <ReservationInfo>
        <ReservationID LastModifyDateTime="2014-07-18T17:33:07.000Z" ID="0" Type="14"/>
        <GuestDetails>
            <GuestDetail>
                <SelectedFareCode FareCode="A0686033"/>
                <ContactInfo BirthDate="1981-12-12Z" Nationality="ES" Age="32" ContactType="CNT">
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
                    <Telephone PhoneNumber=" "/>
                </ContactInfo>
                <GuestTransportation Status="36" Mode="29">
                    <GatewayCity LocationCode="C/O"/>
                </GuestTransportation>
                <SelectedDining Status="36" Sitting="M" Language="ES" SmokingCode="2" SittingType="Traditional"/>
                <SelectedOptions OptionCode="PSG3"/>
                <SelectedOptions OptionCode="PSG1"/>
                <CruiseDocument DocumentLanguage="ES"/>
            </GuestDetail>
            <GuestDetail>
                <SelectedFareCode FareCode="A0686033"/>
                <ContactInfo BirthDate="1980-12-12Z" Nationality="ES" Age="33" ContactType="CNT">
                    <PersonName>
                        <GivenName>Javier</GivenName>
                        <Surname>Sanchez PEREZ</Surname>
                        <NameTitle>MR</NameTitle>
                    </PersonName>
                </ContactInfo>
                <ContactInfo ContactType="EMG">
                    <PersonName>
                        <GivenName>David</GivenName>
                        <Surname>Sanchez LLorente</Surname>
                    </PersonName>
                    <Telephone PhoneNumber=" "/>
                </ContactInfo>
                <GuestTransportation Status="36" Mode="29">
                <GatewayCity LocationCode="C/O"/>
                </GuestTransportation>
                <SelectedDining Status="36" Sitting="M" Language="ES" SmokingCode="2" SittingType="Traditional"/>
                <SelectedOptions OptionCode="PSG3"/>
                <SelectedOptions OptionCode="PSG1"/>
                <CruiseDocument DocumentLanguage="ES"/>
            </GuestDetail>
        </GuestDetails>
    </ReservationInfo>
    </OTA_CruiseBookRQ>
    </ns30:confirmBooking>
</soap:Body>
</soap:Envelope>';

/* $client->setUri($url);
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
} */
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
if ($response === false) {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "Operation completed without any errors";
    echo $return;
}
curl_close($ch);

echo "<br/>RESPONSE";
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
    $StatusCode = $ReservationID->item(0)->getAttribute("StatusCode");
}

//SailingInfo
$SailingInfo = $OTA_CruisePriceBookingRS->item(0)->getElementsByTagName("SailingInfo");
if ($SailingInfo->length > 0) {
    $SelectedCategory = $SailingInfo->item(0)->getElementsByTagName("SelectedCategory");
    if ($SelectedCategory->length > 0) {
        $PromotionDescription = $SelectedCategory->item(0)->getAttribute("PromotionDescription");
        $FareCode = $SelectedCategory->item(0)->getAttribute("FareCode");
    }
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('confirmbooking');
    $insert->values(array(
        'ID' => $ID,
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'Type' => $Type,
        'StatusCode' => $StatusCode,
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
                    $insert->into('confirmbooking_price');
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
                    $insert->into('confirmbooking_payment');
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
                                $insert->into('confirmbooking_priceinfo');
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