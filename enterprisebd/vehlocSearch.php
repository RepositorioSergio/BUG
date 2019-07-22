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
echo "COMECOU VEHLOC";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.enterprise.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$username = "OTA_APTMSTST1";
$password = "fWQBzb4L";
$host = 'https://cis1-xmldirect.ehi.com/services30/OTA30SOAP';

$raw = '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.opentravel.org/OTA/2003/05">
​<soapenv:Header/>
​<soapenv:Body>
<OTA_VehLocSearchRQ TimeStamp="2019-07-10T09:54:48" Target="Production" Version="3.0" TransactionIdentifier="100000001" SequenceNmbr="1" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 file:///C:/Users/Documents/XML/2012b%20Updated/OTA_VehLocSearchRQ.xsd">
	<POS>
		<Source>
			<RequestorID Type="4" ID="XMLRTA">
				<CompanyName Code="EX" CompanyShortName="EHIXMLTEST"/>
			</RequestorID>
		</Source>
		<Source>
			<RequestorID Type="4" ID="00000000" ID_Context="IATA"/>
		</Source>
	</POS>
	<VehLocSearchCriterion>
		<RefPoint CountryCode="US">Disney</RefPoint>
	</VehLocSearchCriterion>
	<Vendor Code="ET"/>
</OTA_VehLocSearchRQ>
​</soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init($host);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/xml;charset=UTF-8',
    'Accept-Encoding: gzip,deflate',
    'SOAPAction: "OTA_VehLocSearchRQ"',
    'Host: cis1-xmldirect.ehi.com',
    'User-Agent: Jakarta Commons-HttpClient/3.1',
    'Content-Length: ' . strlen($raw)
));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
//echo $response;

// echo "<xmp>";
// var_dump($response);
// echo "</xmp>";
// die();
$config = new \Zend\Config\Config(include '../config/autoload/global.enterprise.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$refp = "";
$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);

$responseElement = $inputDoc->documentElement;
$xpath = new DOMXPath($inputDoc);
$search = "";
$search = $xpath->query('/env:Envelope/env:Body', $responseElement);

$OTA_VehLocSearchRS = $search->item(0)->getElementsByTagName("OTA_VehLocSearchRS");
$VehMatchedLocs = $OTA_VehLocSearchRS->item(0)->getElementsByTagName("VehMatchedLocs");
$VehMatchedLoc = $VehMatchedLocs->item(0)->getElementsByTagName("VehMatchedLoc");
if ($VehMatchedLoc->length > 0) {
    for ($i=0; $i < $VehMatchedLoc->length; $i++) { 
        $VehLocSearchCriterion = $VehMatchedLoc->item($i)->getElementsByTagName("VehLocSearchCriterion");
        if ($VehLocSearchCriterion->length > 0) {
            $RefPoint = $VehLocSearchCriterion->item(0)->getElementsByTagName("RefPoint");
            if ($RefPoint->length > 0) {
                $CountryCode = $RefPoint->item(0)->getAttribute("CountryCode");
                $refp = $RefPoint->item(0)->nodeValue;
            } else {
                $CountryCode = "";
                $refp = "";
            }
        }


        $LocationDetail = $VehMatchedLoc->item($i)->getElementsByTagName("LocationDetail");
        if ($LocationDetail->length > 0) {
            $Code = $LocationDetail->item(0)->getAttribute("Code");
            $Name = $LocationDetail->item(0)->getAttribute("Name");
            $AtAirport = $LocationDetail->item(0)->getAttribute("AtAirport");

            $Address = $LocationDetail->item(0)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                if ($AddressLine->length > 0) {
                    $AddressLine = $AddressLine->item(0)->nodeValue;
                } else {
                    $AddressLine = "";
                }
                $CityName = $Address->item(0)->getElementsByTagName("CityName");
                if ($CityName->length > 0) {
                    $CityName = $CityName->item(0)->nodeValue;
                } else {
                    $CityName = "";
                }
                $PostalCode = $Address->item(0)->getElementsByTagName("PostalCode");
                if ($PostalCode->length > 0) {
                    $PostalCode = $PostalCode->item(0)->nodeValue;
                } else {
                    $PostalCode = "";
                }
                $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
                if ($StateProv->length > 0) {
                    $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                } else {
                    $StateCode = "";
                }
                $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
                if ($CountryName->length > 0) {
                    $CodeCountryName = $CountryName->item(0)->getAttribute("Code");
                } else {
                    $CodeCountryName = "";
                }
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('vehlocSearch');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Code' => $Code,
                    'Name' => $Name,
                    'AtAirport' => $AtAirport,
                    'AddressLine' => $AddressLine,
                    'CityName' => $CityName,
                    'PostalCode' => $PostalCode,
                    'StateCode' => $StateCode,
                    'CodeCountryName' => $CodeCountryName,
                    'CountryCode' => $CountryCode,
                    'RefPoint' => $refp
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO SEARCH: " . $e;
                echo $return;
            }

            $Telephone = $LocationDetail->item(0)->getElementsByTagName("Telephone");
            if ($Telephone->length > 0) {
                for ($k=0; $k < $Telephone->length; $k++) { 
                    $PhoneTechType = $Telephone->item($k)->getAttribute("PhoneTechType");
                    $AreaCityCode = $Telephone->item($k)->getAttribute("AreaCityCode");
                    $PhoneNumber = $Telephone->item($k)->getAttribute("PhoneNumber");

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('telephone_vehlocSearch');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'PhoneTechType' => $PhoneTechType,
                            'AreaCityCode' => $AreaCityCode,
                            'PhoneNumber' => $PhoneNumber
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
        }        
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>