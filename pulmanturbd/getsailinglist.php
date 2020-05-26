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
echo "COMECOU CRUISE SAIL AVAIL<br/>";
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

$url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/SailingList";

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:sail="http://services.rccl.com/Interfaces/SailingList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
<soapenv:Header/>
<soapenv:Body>
   <sail:getSailingList>
      <alp:OTA_CruiseSailAvailRQ TimeStamp="2008-07-17T12:44:44.866-04:00" Target="Test" Version="1.0" SequenceNmbr="1" PrimaryLangID="en" RetransmissionIndicator="false" MoreIndicator="true" MaxResponses="50">
         <alp:POS>
            <!--1 to 10 repetitions:-->
            <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <alp:RequestorID ID="313917" ID_Context="AGENCY1" Type="5"/>
                <alp:BookingChannel Type="7">
                    <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                </alp:BookingChannel>
            </alp:Source>
            <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <alp:RequestorID ID="313917" ID_Context="AGENCY2" Type="5"/>
                <alp:BookingChannel Type="7">
                    <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                </alp:BookingChannel>
            </alp:Source>
            <alp:Source TerminalID="12502LDJW6" ISOCurrency="USD">
                <alp:RequestorID ID="313917" ID_Context="AGENT1" Type="5"/>
                <alp:BookingChannel Type="7">
                    <alp:CompanyName CompanyShortName="PULLMANTUR"/>
                </alp:BookingChannel>
            </alp:Source>
         </alp:POS>
        <alp:SailingDateRange Start="2020-07-08"/>
      </alp:OTA_CruiseSailAvailRQ>
   </sail:getSailingList>
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
$getSailingListResponse = $Body->item(0)->getElementsByTagName("getSailingListResponse");
$OTA_CruiseSailAvailRS = $getSailingListResponse->item(0)->getElementsByTagName("OTA_CruiseSailAvailRS");
if ($OTA_CruiseSailAvailRS->length > 0) {
    $SailingOptions = $OTA_CruiseSailAvailRS->item(0)->getElementsByTagName("SailingOptions");
    if ($SailingOptions->length > 0) {
        $SailingOption = $SailingOptions->item(0)->getElementsByTagName("SailingOption");
        if ($SailingOption->length > 0) {
            for ($i=0; $i < $SailingOption->length; $i++) { 
                $SelectedSailing = $SailingOption->item($i)->getElementsByTagName("SelectedSailing");
                if ($SelectedSailing->length > 0) {
                    $ListOfSailingDescriptionCode = $SelectedSailing->item(0)->getAttribute("ListOfSailingDescriptionCode");
                    $Duration = $SelectedSailing->item(0)->getAttribute("Duration");
                    $PortsOfCallQuantity = $SelectedSailing->item(0)->getAttribute("PortsOfCallQuantity");
                    $Start = $SelectedSailing->item(0)->getAttribute("Start");
                    $Status = $SelectedSailing->item(0)->getAttribute("Status");

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
                    $DeparturePort = $SelectedSailing->item(0)->getElementsByTagName("DeparturePort");
                    if ($DeparturePort->length > 0) {
                        $DeparturePortLocationCode = $DeparturePort->item(0)->getAttribute("LocationCode");
                    }
                    $ArrivalPort = $SelectedSailing->item(0)->getElementsByTagName("ArrivalPort");
                    if ($ArrivalPort->length > 0) {
                        $ArrivalPortLocationCode = $ArrivalPort->item(0)->getAttribute("LocationCode");
                    }
                }
                $InclusivePackageOption = $SailingOption->item($i)->getElementsByTagName("InclusivePackageOption");
                if ($InclusivePackageOption->length > 0) {
                    $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                    $InclusiveIndicator = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
                }

                $sql = "select id from mundocruceros_cruisesailavail where id='$CruisePackageCode'";
                $statement = $db->createStatement($sql);
                $statement->prepare();
                try {
                    $row_settings = $statement->execute();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error2: " . $e;
                    echo $return;
                    die();
                }
                $row_settings->buffer();
                if ($row_settings->valid()) {
                    $row = $row_settings->current();
                } else {
                    //
                    // Insert in mundocruceros_cruisesailavail
                    //
                    $null = 'null';

                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('mundocruceros_cruisesailavail');
                    $insert->values(array(
                        'id' => $CruisePackageCode,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'listofsailingdescriptioncode' => $ListOfSailingDescriptionCode,
                        'duration' => $Duration,
                        'portsofcallquantity' => $PortsOfCallQuantity,
                        'start' => $Start,
                        'status' => $Status,
                        'shipcode' => $ShipCode,
                        'vendorcode' => $VendorCode,
                        'regioncode' => $RegionCode,
                        'subregioncode' => $SubRegionCode,
                        'departureportlocationcode' => $DeparturePortLocationCode,
                        'arrivalportlocationcode' => $ArrivalPortLocationCode,
                        'inclusiveindicator' => $InclusiveIndicator,
                        'mapped_id' => $null
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    try {
                        $results = $statement->execute();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error: " . $e;
                        echo $return;
                        die();
                    }
                }

                $sql = "select id from cruisesailavail where id='$CruisePackageCode'";
                $statement = $db->createStatement($sql);
                $row_settings = $statement->prepare();
                try {
                    $row_settings = $statement->execute();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error: " . $e;
                    echo $return;
                }
                $row_settings->buffer();
                if ($row_settings->valid()) {
                    $row_settings = $row_settings->current();
                    $id_cruises = $row_settings["id"];
                    //
                    // Found
                    // 
                    $time = time();
                    $sql = "update mundocruceros_cruisesailavail set mapped_id='$id_cruises', datetime_updated=$time where id='$CruisePackageCode'";
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    try {
                        $row_settings = $statement->execute();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error: " . $e;
                        echo $return;
                        die();
                    }
                } else {
                    //
                    // Something is wrong
                    //
                    echo "Cruise line does not exist - something is wrong";
                    die();
                }

                /* try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('cruisesailavail');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'listofsailingdescriptioncode' => $ListOfSailingDescriptionCode,
                        'duration' => $Duration,
                        'portsofcallquantity' => $PortsOfCallQuantity,
                        'start' => $Start,
                        'status' => $Status,
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
                }*/
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
