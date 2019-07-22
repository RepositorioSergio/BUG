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
echo "COMECOU VEHCANCEL<br/>";
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
<OTA_VehCancelRQ TimeStamp="2019-07-10T09:54:48" Target="Production" Version="3.0" TransactionIdentifier="100000001" SequenceNmbr="1" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 file:///C:/Users/Documents/XML/2012b%20Updated/OTA_VehCancelRQ.xsd">
	<POS>
		<Source>
			<RequestorID Type="4" ID="00000000" ID_Context="IATA">
				<CompanyName Code="EX" CompanyShortName="EHIXMLTEST"/>
			</RequestorID>
		</Source>
	</POS>
	<VehCancelRQCore CancelType="Cancel">
		<UniqueID Type="14" ID="1702167138COUNT"/>
		<PersonName>
			<GivenName>XML</GivenName>
			<Surname>TEST</Surname>
		</PersonName>
	</VehCancelRQCore>
	<VehCancelRQInfo>
		<Vendor Code="AL"/>
	</VehCancelRQInfo>
</OTA_VehCancelRQ>                   
​</soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init($host);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: text/xml;charset=UTF-8',
    'Accept-Encoding: gzip,deflate',
    'SOAPAction: "OTA_VehCancelRQ"',
    'Host: cis1-xmldirect.ehi.com',
    'User-Agent: Jakarta Commons-HttpClient/3.1',
    'Content-Length: ' . strlen($raw)
));
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";
//die();
$config = new \Zend\Config\Config(include '../config/autoload/global.enterprise.php');
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

$responseElement = $inputDoc->documentElement;
$xpath = new DOMXPath($inputDoc);
$search = "";
$search = $xpath->query('/env:Envelope/env:Body', $responseElement);

$OTA_VehCancelRS = $search->item(0)->getElementsByTagName("OTA_VehCancelRS");
//LocationDetail
$VehCancelRSCore = $OTA_VehCancelRS->item(0)->getElementsByTagName("VehCancelRSCore");
if ($VehCancelRSCore->length > 0) {
    $CancelStatus = $VehCancelRSCore->item(0)->getAttribute("CancelStatus");
    $UniqueID = $VehCancelRSCore->item(0)->getElementsByTagName("UniqueID");
    if ($UniqueID->length > 0) {
        $Type = $UniqueID->item(0)->getAttribute("Type");
        $IDCancel = $UniqueID->item(0)->getAttribute("ID");

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('vehcancel');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'CancelStatus' => $CancelStatus,
                'Type' => $Type,
                'IDCancel' => $IDCancel
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO 0: " . $e;
            echo $return;
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>