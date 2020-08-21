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
echo "COMECOU CRUISEFARE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT id,listofsailingdescriptioncode, duration, portsofcallquantity, start, status, shipcode, vendorcode, regioncode, subregioncode, departureportlocationcode, arrivalportlocationcode, inclusiveindicator  FROM cruisesailavail";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $CruisePackageCode = $row->id;
        $ListOfSailingDescriptionCode = $row->listofsailingdescriptioncode;
        $Duration = $row->duration;
        $PortsOfCallQuantity = $row->portsofcallquantity;
        $Start = $row->start;
        $Status = $row->status;
        $ShipCode = $row->shipcode;
        $VendorCode = $row->vendorcode;
        $RegionCode = $row->regioncode;
        $SubRegionCode = $row->subregioncode;
        $DeparturePortLocationCode = $row->departureportlocationcode;
        $ArrivalPortLocationCode = $row->arrivalportlocationcode;
        $InclusiveIndicator = $row->inclusiveindicator;

        $username = 'CONSTGCOSTAMAR';
        $password = '3MDQV5F5BzdvcX9';
        $url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/FareList";

        $raw ='<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <m:getFareList xmlns:m="http://services.rccl.com/Interfaces/FareList">
                <OTA_CruiseFareAvailRQ MaxResponses="150" RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-12-18T12:46:25.861-05:00" TransactionIdentifier="106597" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
                    <POS>
                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                            <RequestorID ID="369567" ID_Context="AGENCY1" Type="11"/>
                            <BookingChannel Type="7">
                                <CompanyName CompanyShortName="COSTAMAR"/>
                            </BookingChannel>
                        </Source>
                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                            <RequestorID ID="369567" ID_Context="AGENCY2" Type="11"/>
                            <BookingChannel Type="7">
                                <CompanyName CompanyShortName="COSTAMAR"/>
                            </BookingChannel>
                        </Source>
                        <Source TerminalID="12502LDJW6" ISOCurrency="USD">
                            <RequestorID ID="369567" ID_Context="AGENT1" Type="11"/>
                            <BookingChannel Type="7">
                                <CompanyName CompanyShortName="COSTAMAR"/>
                            </BookingChannel>
                        </Source>
                    </POS>
                    <Guest>
                        <GuestTransportation Mode="29" Status="36">
                            <GatewayCity LocationCode="BCN"/>
                        </GuestTransportation>
                    </Guest>
                    <Guest>
                        <GuestTransportation Mode="29" Status="36">
                            <GatewayCity LocationCode="BCN"/>
                        </GuestTransportation>
                    </Guest>
                    <GuestCounts>
                        <GuestCount Quantity="1"/>
                        <GuestCount Quantity="1"/>
                    </GuestCounts>
                    <SailingInfo>
                        <SelectedSailing Start="' . $Start . '">
                            <CruiseLine ShipCode="' . $ShipCode . '"/>
                        </SelectedSailing>
                        <InclusivePackageOption CruisePackageCode="' . $CruisePackageCode . '"/>
                    </SailingInfo>
                    <SearchQualifiers>
                        <Status Status="36"/>
                    </SearchQualifiers>
                </OTA_CruiseFareAvailRQ>
            </m:getFareList>
        </soap:Body>
        </soap:Envelope>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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

        $config = new \Zend\Config\Config(include '../config/autoload/global.rcc.php');
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
        $getFareListResponse = $Body->item(0)->getElementsByTagName("getFareListResponse");
        $OTA_CruiseFareAvailRS = $getFareListResponse->item(0)->getElementsByTagName("OTA_CruiseFareAvailRS");
        if ($OTA_CruiseFareAvailRS->length > 0) {
            $SailingInfo = $OTA_CruiseFareAvailRS->item(0)->getElementsByTagName("SailingInfo");
            if ($SailingInfo->length > 0) {
                $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
                if ($SelectedSailing->length > 0) {
                    $ListOfSailingDescriptionCode = $SelectedSailing->item(0)->getAttribute("ListOfSailingDescriptionCode");
                    $Duration = $SelectedSailing->item(0)->getAttribute("Duration");
                    $Start = $SelectedSailing->item(0)->getAttribute("Start");
                    $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
                    if ($CruiseLine->length > 0) {
                        $ShipCode = $CruiseLine->item(0)->getAttribute("ShipCode");
                    }
                }
                $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
                if ($InclusivePackageOption->length > 0) {
                    $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                }
            }
            $FareCodeOptions = $OTA_CruiseFareAvailRS->item(0)->getElementsByTagName("FareCodeOptions");
            if ($FareCodeOptions->length > 0) {
                $FareCodeOption = $FareCodeOptions->item(0)->getElementsByTagName("FareCodeOption");
                if ($FareCodeOption->length > 0) {
                    for ($i=0; $i < $FareCodeOption->length; $i++) { 
                        $DiscountTypes = $FareCodeOption->item($i)->getAttribute("DiscountTypes");
                        $FareCode = $FareCodeOption->item($i)->getAttribute("FareCode");
                        $FareDescription = $FareCodeOption->item($i)->getAttribute("FareDescription");
                        $ListOfFareQualifierCode = $FareCodeOption->item($i)->getAttribute("ListOfFareQualifierCode");
                        $NonRefundableType = $FareCodeOption->item($i)->getAttribute("NonRefundableType");
                        $PromoPercentage = $FareCodeOption->item($i)->getAttribute("PromoPercentage");
                        $PromotionClass = $FareCodeOption->item($i)->getAttribute("PromotionClass");
                        $PromotionEligibility = $FareCodeOption->item($i)->getAttribute("PromotionEligibility");
                        $PromotionTypes = $FareCodeOption->item($i)->getAttribute("PromotionTypes");
                        $Status = $FareCodeOption->item($i)->getAttribute("Status");
                        $FareRemark = $FareCodeOption->item($i)->getElementsByTagName("FareRemark");
                        if ($FareRemark->length > 0) {
                            $FareRemark = $FareRemark->item(0)->nodeValue;
                        } else {
                            $FareRemark = "";
                        }
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('farelist');
                            $insert->values(array(
                               'datetime_updated' => time(),
                               'farecode' => $FareCode,
                               'faredescription' => $FareDescription,
                               'discounttypes' => $DiscountTypes,
                               'listoffarequalifiercode' => $ListOfFareQualifierCode,
                               'nonrefundabletype' => $NonRefundableType,
                               'promopercentage' => $PromoPercentage,
                               'promotionclass' => $PromotionClass,
                               'promotioneligibility' => $PromotionEligibility,
                               'promotiontypes' => $PromotionTypes,
                               'status' => $Status,
                               'fareremark' => $FareRemark,
                               'cruisepackagecode' => $CruisePackageCode
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
                    }
                }
            }
            $TPA_Extensions = $OTA_CruiseFareAvailRS->item(0)->getElementsByTagName("TPA_Extensions");
            if ($TPA_Extensions->length > 0) {
                $Taxes = $TPA_Extensions->item(0)->getElementsByTagName("Taxes");
                if ($Taxes->length > 0) {
                    $Tax = $Taxes->item(0)->getElementsByTagName("Tax");
                    if ($Tax->length > 0) {
                        for ($j=0; $j < $Tax->length; $j++) { 
                            $Amount = $Tax->item($j)->getAttribute("Amount");
                        }
                    }
                }
                $Fee = $TPA_Extensions->item(0)->getElementsByTagName("Fee");
                if ($Fee->length > 0) {
                    $Taxes = $Fee->item(0)->getElementsByTagName("Taxes");
                    if ($Taxes->length > 0) {
                        $Tax = $Taxes->item(0)->getElementsByTagName("Tax");
                        if ($Tax->length > 0) {
                            for ($k=0; $k < $Tax->length; $k++) { 
                                $Amount = $Tax->item($k)->getAttribute("Amount");
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