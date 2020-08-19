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
echo "COMECOU DINING LIST<br/>";
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

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/DiningList";

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:din="http://services.rccl.com/Interfaces/DiningList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
   <soapenv:Header/>
   <soapenv:Body>
      <din:getDiningList>
        <OTA_CruiseDiningAvailRQ RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-12-29T18:25:50.1Z" TransactionIdentifier="106597" Version="1.0" Target="Test" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
            <POS>
                <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENCY1" Type="11"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENCY2" Type="11"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="3MDQV5F5BzdvcX9" ISOCurrency="USD">
                    <RequestorID ID="313917" ID_Context="AGENT1" Type="11"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="CONSTGCOSTAMAR"/>
                    </BookingChannel>
                </Source>
            </POS>
            <Guest Code="10" Age="30"/>
            <GuestCounts>
                <GuestCount Age="30" Quantity="1"/>
            </GuestCounts>
            <SailingInfo>
                <SelectedSailing ListOfSailingDescriptionCode="6" Start="2021-02-07" Duration="P7N" Status="36" PortsOfCallQuantity="5">
                    <CruiseLine VendorCode="PUL" ShipCode="HR"/>
                    <!--Optional:-->
                    <Region RegionCode="PDYDO" SubRegionCode="PGS"/>
                    <!--Optional:-->
                    <DeparturePort LocationCode="DXB"/>
                    <!--Optional:-->
                    <ArrivalPort LocationCode="DXB"/>
                </SelectedSailing>
                <!--Optional:-->
                <alp:InclusivePackageOption CruisePackageCode="HRPO0720" InclusiveIndicator="false"/>
                <!--Optional:-->
                <Currency CurrencyCode="USD" DecimalPlaces="2"/>
                <SelectedCategory BerthedCategoryCode="GS" PricedCategoryCode="GS"/>
            </SailingInfo>
            <SelectedFare GroupCode="1"/>
            <TPA_ReservationId Type="14" ID="0"/>
        </OTA_CruiseDiningAvailRQ>
    </din:getDiningList>
    </soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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
$getDiningListResponse = $Body->item(0)->getElementsByTagName("getDiningListResponse");
if ($getDiningListResponse->length > 0) {
    $OTA_CruiseDiningAvailRS = $getDiningListResponse->item(0)->getElementsByTagName("OTA_CruiseDiningAvailRS");
    if ($OTA_CruiseDiningAvailRS->length > 0) {
        $SailingInfo = $OTA_CruiseDiningAvailRS->item(0)->getElementsByTagName("SailingInfo");
        if ($SailingInfo->length > 0) {
            $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
            if ($SelectedSailing->length > 0) {
                $Start = $SelectedSailing->item(0)->getAttribute("Start");
                $Duration = $SelectedSailing->item(0)->getAttribute("Duration");

                $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
                if ($CruiseLine->length > 0) {
                    $ShipCode = $CruiseLine->item(0)->getAttribute("ShipCode");
                    $VendorCode = $CruiseLine->item(0)->getAttribute("VendorCode");
                }
                $Region = $SelectedSailing->item(0)->getElementsByTagName("Region");
                if ($Region->length > 0) {
                    $RegionCode = $Region->item(0)->getAttribute("RegionCode");
                    $SubRegionCode = $Region->item(0)->getAttribute("SubRegionCode");
                }
            }
            $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
            if ($InclusivePackageOption->length > 0) {
                $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                $InclusiveIndicator = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
            }
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('dininglist');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'start' => $Start,
                'duration' => $Duration,
                'shipcode' => $ShipCode,
                'vendorcode' => $VendorCode,
                'regioncode' => $RegionCode,
                'subregioncode' => $SubRegionCode,
                'cruisepackagecode' => $CruisePackageCode,
                'inclusiveindicator' => $InclusiveIndicator
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO 1: " . $e;
            echo $return;
        }

        $DiningOptions = $OTA_CruiseDiningAvailRS->item(0)->getElementsByTagName("DiningOptions");
        if ($DiningOptions->length > 0) {
            $DiningOption = $DiningOptions->item(0)->getElementsByTagName("DiningOption");
            if ($DiningOption->length > 0) {
                for ($i=0; $i < $DiningOption->length; $i++) { 
                    $CrossReferencingAllowed = $DiningOption->item($i)->getAttribute("CrossReferencingAllowed");
                    $FamilyTimeIndicator = $DiningOption->item($i)->getAttribute("FamilyTimeIndicator");
                    $PrepaidGratuityRequired = $DiningOption->item($i)->getAttribute("PrepaidGratuityRequired");
                    $Sitting = $DiningOption->item($i)->getAttribute("Sitting");
                    $SittingInstance = $DiningOption->item($i)->getAttribute("SittingInstance");
                    $SittingStatus = $DiningOption->item($i)->getAttribute("SittingStatus");
                    $SittingType = $DiningOption->item($i)->getAttribute("SittingType");
                    $SmokingAllowed = $DiningOption->item($i)->getAttribute("SmokingAllowed");

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('dininglist_diningoptions');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'crossreferencingallowed' => $CrossReferencingAllowed,
                            'familytimeindicator' => $FamilyTimeIndicator,
                            'prepaidgratuityrequired' => $PrepaidGratuityRequired,
                            'sitting' => $Sitting,
                            'sittinginstance' => $SittingInstance,
                            'sittingstatus' => $SittingStatus,
                            'sittingtype' => $SittingType,
                            'smokingallowed' => $SmokingAllowed
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 2: " . $e;
                        echo $return;
                    }
                }
            }
            $TPA_DiningProfileInfo = $DiningOptions->item(0)->getElementsByTagName("TPA_DiningProfileInfo");
            if ($TPA_DiningProfileInfo->length > 0) {
                $RuleLevel = $TPA_DiningProfileInfo->item(0)->getAttribute("RuleLevel");
                $RuleName = $TPA_DiningProfileInfo->item(0)->getAttribute("RuleName");
                $DiningProfile = $TPA_DiningProfileInfo->item(0)->getElementsByTagName("DiningProfile");
                if ($DiningProfile->length > 0) {
                    $Code = $DiningProfile->item(0)->getAttribute("Code");
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('dininglist_tpa_diningprofileinfo');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'code' => $Code,
                        'rulelevel' => $RuleLevel,
                        'rulename' => $RuleName
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 3: " . $e;
                    echo $return;
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