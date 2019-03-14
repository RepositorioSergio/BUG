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
$sql = "select value from settings where name='enableCarnectCars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_carnect = $affiliate_id;
} else {
    $affiliate_id_carnect = 0;
}
$sql = "select value from settings where name='CarnectLogin' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectLogin = $row_settings['value'];
}
$sql = "select value from settings where name='CarnectCarspassword' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='CarnectCarswebservicesURL' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $CarnectCarswebservicesURL = $row['value'];
}
echo $return;
echo "CarnectCarsDestinationsServicesURL: " . $CarnectCarswebservicesURL;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT city_id FROM cities";
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
        $city_id = $row->city_id;
        echo $return;
        echo "city_id: " . $city_id;
        echo $return;

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:ns="http://www.opentravel.org/OTA/2003/05">
          <soapenv:Header />
          <soapenv:Body>
          <VehLocationSearchRQ xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns="http://www.opentravel.org/OTA/2003/05">
            <POS>
              <Source ISOCountry="EN">
                <RequestorID Type="' . $CarnectLogin . '" ID_Context="' . $CarnectCarspassword . '" />
              </Source>
              <Source ISOCountry="GB" />
            </POS>
            <VehLocSearchCriterion Ranking="100">
              <RefPoint RefPointType="1" CityName="' . $city_id . '" />
            </VehLocSearchCriterion>
          </VehLocationSearchRQ>
          </soapenv:Body>
        </soapenv:Envelope>';
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($xml_post_string)
        );
echo $xml_post_string;

        //
        // PHP CURL for https connection with auth
        //
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $CarnectCarswebservicesURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $xmlresult = curl_exec($ch);
        curl_close($ch);
        echo $xmlresult;

        $config = new \Zend\Config\Config(include '../config/autoload/global.carnect.php');
        $config = [
            'driver' => $config->db->driver,
            'database' => $config->db->database,
            'username' => $config->db->username,
            'password' => $config->db->password,
            'hostname' => $config->db->hostname
        ];
        $db = new \Zend\Db\Adapter\Adapter($config);

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresult);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $VehLocationSearchRS = $Body->item(0)->getElementsByTagName('VehLocationSearchRS');
        $VehMatchedLocs = $VehLocationSearchRS->item(0)->getElementsByTagName('VehMatchedLocs');
        $VehMatchedLoc = $VehMatchedLocs->item(0)->getElementsByTagName('VehMatchedLoc');
        for ($k=0; $k < $VehMatchedLoc->length; $k++) { 
            //VehLocSearchCriterion
            $VehLocSearchCriterion = $VehMatchedLoc->item($k)->getElementsByTagName('VehLocSearchCriterion');
            $Position = $VehLocSearchCriterion->item(0)->getElementsByTagName('Position');
            $Longitude = $Position->item(0)->getAttribute('Longitude');
            $Latitude = $Position->item(0)->getAttribute('Latitude');

            //LocationDetail
            $LocationDetail = $VehMatchedLoc->item($k)->getElementsByTagName('LocationDetail');
            $ExtendedLocationCode = $LocationDetail->item(0)->getAttribute('ExtendedLocationCode');
            $CodeContext = $LocationDetail->item(0)->getAttribute('CodeContext');
            $Name = $LocationDetail->item(0)->getAttribute('Name');
            $Code = $LocationDetail->item(0)->getAttribute('Code');
            $AtAirport = $LocationDetail->item(0)->getAttribute('AtAirport');

            //Address
            $Address = $LocationDetail->item(0)->getElementsByTagName('Address');
            $Type = $Address->item(0)->getAttribute('Type');
            $StreetNmbr = $Address->item(0)->getElementsByTagName('StreetNmbr');
            if ($StreetNmbr->length > 0) {
                $StreetNmbr = $StreetNmbr->item(0)->nodeValue;
            } else {
                $StreetNmbr = "";
            }
            $AddressLine = $Address->item(0)->getElementsByTagName('AddressLine');
            if ($AddressLine->length > 0) {
                $AddressLine = $AddressLine->item(0)->nodeValue;
            } else {
                $AddressLine = "";
            }
            $CityName = $Address->item(0)->getElementsByTagName('CityName');
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            $PostalCode = $Address->item(0)->getElementsByTagName('PostalCode');
            if ($PostalCode->length > 0) {
                $PostalCode = $PostalCode->item(0)->nodeValue;
            } else {
                $PostalCode = "";
            }
            $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
            if ($CountryName->length > 0) {
                $CountryCode = $CountryName->item(0)->getAttribute('Code');
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }

            $AdditionalInfo = $LocationDetail->item(0)->getElementsByTagName('AdditionalInfo');
            //ParkLocation
            $ParkLocation = $AdditionalInfo->item(0)->getElementsByTagName('ParkLocation');
            $Location = $ParkLocation->item(0)->getAttribute('Location');

            //TPA_Extensions
            $TPA_Extensions = $AdditionalInfo->item(0)->getElementsByTagName('TPA_Extensions');
            $Position = $TPA_Extensions->item(0)->getElementsByTagName('Position');
            $LongitudeTPA = $Position->item(0)->getAttribute('Longitude');
            $LatitudeTPA = $Position->item(0)->getAttribute('Latitude');

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('VehLocation');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'ExtendedLocationCode' => $ExtendedLocationCode,
                    'CodeContext' => $CodeContext,
                    'Name' => $Name,
                    'Code' => $Code,
                    'AtAirport' => $AtAirport,
                    'Type' => $Type,
                    'StreetNmbr' => $StreetNmbr,
                    'CityName' => $CityName,
                    'PostalCode' => $PostalCode,
                    'CountryCode' => $CountryCode,
                    'CountryName' => $CountryName,
                    'Location' => $Location,
                    'LongitudeTPA' => $LongitudeTPA,
                    'LatitudeTPA' => $LatitudeTPA,
                    'Longitude' => $Longitude,
                    'Latitude' => $Latitude
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Exception: " . $e;
                echo $return;
            }
            

            //OperationSchedules
            $day = "";
            $OperationSchedules = $AdditionalInfo->item(0)->getElementsByTagName('OperationSchedules');
            $OperationSchedule = $OperationSchedules->item(0)->getElementsByTagName('OperationSchedule');
            $OperationTimes = $OperationSchedule->item(0)->getElementsByTagName('OperationTimes');
            $OperationTime = $OperationTimes->item(0)->getElementsByTagName('OperationTime');
            for ($i=0; $i < $OperationTime->length; $i++) { 
                $End = $OperationTime->item($i)->getAttribute('End');
                echo $return;
                echo "End: " . $End;
                echo $return;
                $Start = $OperationTime->item($i)->getAttribute('Start');
                echo $return;
                echo "Start: " . $Start;
                echo $return;
                if ($OperationTime->item($i)->getAttribute('Mon')) {
                    $day = "Mon";
                    echo $return;
                    echo "day: " . $day;
                    echo $return;
                }elseif ($OperationTime->item($i)->getAttribute('Tue')) {
                    $day = 'Tue';
                    echo $return;
                    echo "day: " . $day;
                    echo $return;
                }elseif ($OperationTime->item($i)->getAttribute('Weds')) {
                    $day = 'Weds';
                    echo $return;
                    echo "day: " . $day;
                    echo $return;
                }elseif ($OperationTime->item($i)->getAttribute('Thur')) {
                    $day = 'Thur';
                    echo $return;
                    echo "day: " . $day;
                    echo $return;
                }elseif ($OperationTime->item($i)->getAttribute('Fri')) {
                    $day = 'Fri';
                    echo $return;
                    echo "day: " . $day;
                    echo $return;
                }elseif ($OperationTime->item($i)->getAttribute('Sat')) {
                    $day = 'Sat';
                    echo $return;
                    echo "day: " . $day;
                    echo $return;
                }else {
                    $day = 'Sun';
                    echo $return;
                    echo "day: " . $day;
                    echo $return;
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('operationtime');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'End' => $End,
                        'Start' => $Start,
                        'day' => $day
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Exception: " . $e;
                    echo $return;
                }
                
            }


            $Telephone = $LocationDetail->item(0)->getElementsByTagName('Telephone');
            for ($j=0; $j < $Telephone->length; $j++) { 
                $PhoneNumber = $Telephone->item($j)->getAttribute('PhoneNumber');
                echo $return;
                echo "PhoneNumber: " . $PhoneNumber;
                echo $return;
                $PhoneTechType = $Telephone->item($j)->getAttribute('PhoneTechType');
                echo $return;
                echo "PhoneTechType: " . $PhoneTechType;
                echo $return;
                
                try {
                    
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('telefone');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'PhoneNumber' => $PhoneNumber,
                        'PhoneTechType' => $PhoneTechType
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (Exception $e) {
                    echo $return;
                    echo "Exception: " . $e;
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
