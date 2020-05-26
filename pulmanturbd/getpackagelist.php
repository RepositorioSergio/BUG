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

$sql = "SELECT id,listofsailingdescriptioncode, duration, portsofcallquantity, start, status, shipcode, vendorcode, regioncode, subregioncode, departureportlocationcode, arrivalportlocationcode, inclusiveindicator  FROM mundocruceros_cruisesailavail";
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

        $username = 'CONCTMM';
        $password = 'u73ecKBu73ecKB!';

        $url = "https://stage.services.rccl.com/Reservation_FITWeb/sca/PackageList";

        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pac="http://services.rccl.com/Interfaces/PackageList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha">
        <soapenv:Header/>
        <soapenv:Body>
           <pac:getPackageList>
              <alp:OTA_CruisePkgAvailRQ TimeStamp="2008-07-17T12:44:44.866-04:00" Target="Test" Version="1.0" SequenceNmbr="1" PrimaryLangID="en" RetransmissionIndicator="false" MoreIndicator="true" MaxResponses="50">
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
                 <!--Optional:-->
                 <alp:SailingInfo>
                    <!--Optional:-->
                    <alp:SelectedSailing ListOfSailingDescriptionCode="' . $ListOfSailingDescriptionCode . '" Start="' . $Start . '" Duration="' . $Duration . '" Status="' . $Status . '" PortsOfCallQuantity="' . $PortsOfCallQuantity . '">
                       <alp:CruiseLine VendorCode="' . $VendorCode . '" ShipCode="' . $ShipCode . '"/>
                       <!--Optional:-->
                       <alp:Region RegionCode="' . $RegionCode . '" SubRegionCode="' . $SubRegionCode . '"/>
                       <!--Optional:-->
                       <alp:DeparturePort LocationCode="' . $DeparturePortLocationCode . '"/>
                       <!--Optional:-->
                       <alp:ArrivalPort LocationCode="' . $ArrivalPortLocationCode . '"/>
                    </alp:SelectedSailing>
                    <!--Optional:-->
                    <alp:InclusivePackageOption CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="' . $InclusiveIndicator . '"/>
                 </alp:SailingInfo>
                 <!--1 to 8 repetitions:-->
                 <alp:PackageOption PackageTypeCode="0" CruisePackageCode="' . $CruisePackageCode . '" InclusiveIndicator="' . $InclusiveIndicator . '"/>
              </alp:OTA_CruisePkgAvailRQ>
           </pac:getPackageList>
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
        $inputDoc->loadXML($response3);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $getPackageListResponse = $Body->item(0)->getElementsByTagName("getPackageListResponse");
        if ($getPackageListResponse->length > 0) {
            $OTA_CruisePkgAvailRS = $getPackageListResponse->item(0)->getElementsByTagName("OTA_CruisePkgAvailRS");
            if ($OTA_CruisePkgAvailRS->length > 0) {
                $TPA_Extensions = $OTA_CruisePkgAvailRS->item(0)->getElementsByTagName("TPA_Extensions");
                if ($TPA_Extensions->length > 0) {
                    $SailingInfos = $TPA_Extensions->item(0)->getElementsByTagName("SailingInfos");
                    if ($SailingInfos->length > 0) {
                        $SailingInfo = $SailingInfos->item(0)->getElementsByTagName("SailingInfo");
                        if ($SailingInfo->length > 0) {
                            for ($xSailingInfo=0; $xSailingInfo < $SailingInfo->length; $xSailingInfo++) { 
                                $CruisePackages = $SailingInfo->item($xSailingInfo)->getElementsByTagName("CruisePackages");
                                if ($CruisePackages->length > 0) {
                                    $CruisePackage = $CruisePackages->item(0)->getElementsByTagName("CruisePackage");
                                    if ($CruisePackage->length > 0) {
                                        $CruisePackageCode = $CruisePackage->item(0)->getAttribute("CruisePackageCode");
                                        $Duration = $CruisePackage->item(0)->getAttribute("Duration");
                                        $End = $CruisePackage->item(0)->getAttribute("End");
                                        $PackageTypeCode = $CruisePackage->item(0)->getAttribute("PackageTypeCode");
                                        $Start = $CruisePackage->item(0)->getAttribute("Start");
                                        $Description = $CruisePackage->item(0)->getAttribute("Description");

                                        $sql = "select id from mundocruceros_packagelist where id='$CruisePackageCode'";
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
                                            $insert->into('mundocruceros_packagelist');
                                            $insert->values(array(
                                                'id' => $CruisePackageCode,
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'duration' => $Duration,
                                                'start' => $Start,
                                                'end' => $End,
                                                'packagetypecode' => $PackageTypeCode,
                                                'description' => $Description,
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
                                            $sql = "update mundocruceros_packagelist set mapped_id='$id_cruises', datetime_updated=$time where id='$CruisePackageCode'";
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
                                            echo "Package does not exist - something is wrong";
                                            die();
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
