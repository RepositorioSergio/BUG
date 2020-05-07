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

$username = 'CONCTMM';
$password = 'u73ecKBu73ecKB!';

$url = 'https://stage.services.rccl.com/Reservation_FITWeb/sca/ConfirmBooking';

$raw ='<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:conf="http://services.rccl.com/Interfaces/ConfirmBooking" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
<soap:Body>
    <conf:confirmBooking>
    <OTA_CruiseBookRQ Version="1.0" SequenceNmbr="1" TimeStamp="2008-12-30T18:30:42.720+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
    <POS>
        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
            <RequestorID ID="279796" ID_Context="AGENCY1" Type="5"/>
            <BookingChannel Type="7">
            <CompanyName CompanyShortName="PULLMANTUR"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
            <RequestorID ID="279796" ID_Context="AGENCY2" Type="5"/>
            <BookingChannel Type="7">
            <CompanyName CompanyShortName="PULLMANTUR"/>
            </BookingChannel>
        </Source>
        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
            <RequestorID ID="279796" ID_Context="AGENT1" Type="5"/>
            <BookingChannel Type="7">
            <CompanyName CompanyShortName="PULLMANTUR"/>
            </BookingChannel>
        </Source>
    </POS>
    <AgentInfo Contact="CMOTEST" ContactID="CMOtest_MI"/>
    <SailingInfo>
        <SelectedSailing Start="2020-08-15">
            <CruiseLine ShipCode="SO"/>
        </SelectedSailing>
        <InclusivePackageOption CruisePackageCode="SOPD0745"/>
        <SelectedCategory PricedCategoryCode="RS" BerthedCategoryCode="RS" FareCode="BESTRATE" DeckNumber="10" DeckName="CUBIERTA 10" PromotionDescription="STD 1 .1">
            <!--Optional:-->
            <alp:CabinAttributes>
                <!--1 to 99 repetitions:-->
                <alp:CabinAttribute CabinAttributeCode="99"/>
            </alp:CabinAttributes>
            <SelectedCabin Status="48" CabinNumber="1010" MaxOccupancy="4"/>
        </SelectedCategory>
    </SailingInfo>
    <ReservationInfo>
        <ReservationID  ID="0" Type="14"/>
        <GuestDetails>
            <GuestDetail>
                <SelectedFareCode FareCode="BESTRATE"/>
                <ContactInfo BirthDate="1984-12-12Z" Nationality="ES" Age="35" ContactType="CNT">
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
                <SelectedDining Status="36" Sitting="M"/>
                <CruiseDocument DocumentLanguage="ES"/>
            </GuestDetail>
            <GuestDetail>
                <SelectedFareCode FareCode="BESTRATE"/>
                <ContactInfo BirthDate="1987-12-12Z" Nationality="ES" Age="32" ContactType="CNT">
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
                    <Telephone PhoneNumber=""/>
                </ContactInfo>
                <GuestTransportation Status="36" Mode="29">
                <GatewayCity LocationCode="C/O"/>
                </GuestTransportation>
                <SelectedDining Status="36" Sitting="M" Language="ES"/>
                <CruiseDocument DocumentLanguage="ES"/>
            </GuestDetail>
        </GuestDetails>
    </ReservationInfo>
    </OTA_CruiseBookRQ>
    </conf:confirmBooking>
</soap:Body>
</soap:Envelope>';

$raw2 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:dpconfig="http://www.datapower.com/param/config" xmlns:example="http://www.example.com">
<soapenv:Body>
<a:confirmBooking xmlns:a="http://services.rccl.com/Interfaces/ConfirmBooking" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
    <OTA_CruiseBookRQ TransactionActionCode="Commit" TimeStamp="2013-10-27T05:45:40.200Z" Version="1.0" SequenceNmbr="1" RetransmissionIndicator="false" Target="Production">
        <POS>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
            <RequestorID ID="279796" ID_Context="AGENCY1" Type="5"/>
            <BookingChannel Type="7">
            <CompanyName CompanyShortName="PULLMANTUR"/>
            </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="279796" ID_Context="AGENCY2" Type="5"/>
                <BookingChannel Type="7">
                <CompanyName CompanyShortName="PULLMANTUR"/>
                </BookingChannel>
            </Source>
            <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <RequestorID ID="279796" ID_Context="AGENT1" Type="5"/>
                <BookingChannel Type="7">
                <CompanyName CompanyShortName="PULLMANTUR"/>
                </BookingChannel>
            </Source>
            </POS>
            <AgentInfo Contact="CMOTEST" ContactID="CMOtest_MI"/>
            <SailingInfo>
                <SelectedSailing Start="2020-08-15">
                    <CruiseLine VendorCode="PUL" ShipCode="SO"/>
                </SelectedSailing>
                <InclusivePackageOption CruisePackageCode="SOPD0745"/>
                <Currency CurrencyCode="USD"/>
                <SelectedCategory WaitlistIndicator="false" FareCode="BESTRATE" BerthedCategoryCode="RS" PricedCategoryCode="RS">
                    <CabinAttributes>
                        <CabinAttribute CabinAttributeCode="99"/>
                    </CabinAttributes>
                    <SelectedCabin Status="48" CabinNumber="1010">
                        <CabinConfiguration BedConfigurationCode="SD"/>
                    </SelectedCabin>
                </SelectedCategory>
            </SailingInfo>
            <ReservationInfo>
            <ReservationID Type="14" ID="0"/>
            <GuestDetails>
                <GuestDetail>
                    <SelectedFareCode FareCode="A0741742"/>
                    <ContactInfo GuestRefNumber="606056990" Age="41" Nationality="US" BirthDate="1973-07-12Z"
                    Gender="Male" ContactType="CNT" DefaultInd="false" EmergencyFlag="false">
                    <PersonName>
                        <GivenName>WER</GivenName>
                        <Surname>WER</Surname>
                        <NameTitle>MSTR</NameTitle>
                    </PersonName>
                    <Address>
                    <StateProv StateCode="FL"/>
                    </Address>
                    <FareQualifiers FareRestriction="1"/>
                    </ContactInfo>
                    <ContactInfo ContactType="LNG" >
                        <PersonName>
                            <GivenName>约翰</GivenName>
                            <MiddleName>背风处</MiddleName>
                            <Surname>工匠</Surname>
                        </PersonName>
                    </ContactInfo>
                    <GuestTransportation Mode="29" Status="59">
                        <GatewayCity LocationCode="C/O" CodeContext="IATA">Cruise Only</GatewayCity>
                    </GuestTransportation>
                    <TravelDocument>
                        <DocLimitations>N</DocLimitations>
                    </TravelDocument>
                    <SelectedDining Language="en" Sitting="M" Status="36"/>
                    <SelectedInsurance InsuranceCode="CRCR" SelectedOptionIndicator="true"/>
                    <SelectedPackages/>
                    <SelectedSpecialServices/>
                    <AirAccommodations/>
                    <CruiseDocument DeliveryMethodCode="2" DocumentLanguage="en"/>
                </GuestDetail>
                <GuestDetail>
                    <SelectedFareCode FareCode="A0741742"/>
                    <ContactInfo GuestRefNumber="606057006" Age="41" Nationality="US" BirthDate="1973-07-12Z"
                        Gender="Female" ContactType="CNT" DefaultInd="false" EmergencyFlag="false">
                        <PersonName>
                            <GivenName>WER</GivenName>
                            <Surname>WER</Surname>
                            <NameTitle>MISS</NameTitle>
                        </PersonName>
                        <Address DefaultInd="false" FormattedInd="false"/>
                        <Address DefaultInd="false" FormattedInd="false"/>
                        <Address DefaultInd="false" FormattedInd="false"/>
                    </ContactInfo>
                    <GuestTransportation Mode="29" Status="36">
                        <GatewayCity LocationCode="C/O" CodeContext="IATA">Cruise Only</GatewayCity>
                    </GuestTransportation>
                    <TravelDocument>
                        <DocLimitations>N</DocLimitations>
                    </TravelDocument>
                    <SelectedDining SmokingCode="3" TableSize="S" Language="en" Sitting="O" Status="39"
                    CrossReferencingAllowed="false" FamilyTimeIndicator="false" SittingType="O" SittingInstance="OPEN"/>
                    <SelectedInsurance InsuranceCode="CRCR" SelectedOptionIndicator="true"/>
                    <SelectedPackages/>
                    <SelectedSpecialServices/>
                    <AirAccommodations/>
                    <CruiseDocument DeliveryMethodCode="2" DocumentLanguage="en"/>
                </GuestDetail>
            </GuestDetails>
        </ReservationInfo>
    </OTA_CruiseBookRQ>
</a:confirmBooking>
</soapenv:Body>
</soapenv:Envelope>';

echo $username . ":" . $password;

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