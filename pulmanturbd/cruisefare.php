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

$config = new \Zend\Config\Config(include '../config/autoload/global.pulmantur.php');
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
        $getOptionListResponse = $Body->item(0)->getElementsByTagName("getOptionListResponse");
        $OTA_CruiseSpecialServiceAvailRS = $getOptionListResponse->item(0)->getElementsByTagName("OTA_CruiseSpecialServiceAvailRS");
        $SpecialServices = $OTA_CruiseSpecialServiceAvailRS->item(0)->getElementsByTagName("SpecialServices");
        $node = $SpecialServices->item(0)->getElementsByTagName("SpecialService");
        for ($i=0; $i < $node->length; $i++) { 
            $Code = $node->item($i)->getAttribute("Code");
            $Description = $node->item($i)->getAttribute("Description");
            $AssociationType = $node->item($i)->getAttribute("AssociationType");

            $PriceInfo = $node->item($i)->getElementsByTagName("PriceInfo");
            if ($PriceInfo->length > 0) {
                $ChargeTypeCode = $PriceInfo->item(0)->getAttribute("ChargeTypeCode");
                $Amount = $PriceInfo->item(0)->getAttribute("Amount");
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('optionList');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Code' => $Code,
                    'Description' => $Description,
                    'AssociationType' => $AssociationType,
                    'ChargeTypeCode' => $ChargeTypeCode,
                    'Amount' => $Amount
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: " . $e;
                echo $return;
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