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
echo "COMECOU SERVICE CHECKAVAIL";
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

    
$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/checktransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';
$RatePlanCode = "/bFyf0Mia38YuvTiH/2eBfB6NpD/0n6R1pdpNJ6oXkinqqFuwdNxTtNVSpS7VmQGr8gnQe5f6LU/IdBJFBqsntDDwJCAkuK26SwNOpjz2i1HPUPOmiJVZYj4iDe6ie0106WDUZPMuY8nncABzmllSlS6bxxo+6I4UyK32KeykkN1iueHlJrefdH4hYA/TRDZ";

$raw= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
    <ServiceCheckAvail xmlns="http://www.juniper.es/webservice/2007/">
        <ServiceCheckAvailRQ Version="1.1" Language="en">
            <Login Password="' . $password . '" Email="' . $email . '"/>
            <ServiceCheckAvailRequest>
                <ServiceCheckOption RatePlanCode="' . $RatePlanCode . '"/>
            </ServiceCheckAvailRequest>
        </ServiceCheckAvailRQ>
    </ServiceCheckAvail>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Accept: application/xml",
    "Content-type: text/xml;charset=UTF-8",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: http://www.juniper.es/webservice/2007/ServiceCheckAvail",
    "Content-length: " . strlen($raw)
)); 
$client->setUri($url);
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
}

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
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
$ServiceCheckAvailResponse = $Body->item(0)->getElementsByTagName("ServiceCheckAvailResponse");
if ($ServiceCheckAvailResponse->length > 0) {
    $CheckAvailRS = $ServiceCheckAvailResponse->item(0)->getElementsByTagName("CheckAvailRS");
    if ($CheckAvailRS->length > 0) {
        $IntCode = $CheckAvailRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $CheckAvailRS->item(0)->getAttribute("TimeStamp");
        $Url = $CheckAvailRS->item(0)->getAttribute("Url");
        $Results = $CheckAvailRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $ServiceResult = $Results->item(0)->getElementsByTagName("ServiceResult");
            if ($ServiceResult->length > 0) {
                $Status = $ServiceResult->item(0)->getAttribute("Status");
                $DestinationZone = $ServiceResult->item(0)->getAttribute("DestinationZone");
                $End = $ServiceResult->item(0)->getAttribute("End");
                $Start = $ServiceResult->item(0)->getAttribute("Start");
                $Code = $ServiceResult->item(0)->getAttribute("Code");
                //ServiceInfo
                $ServiceInfo = $ServiceResult->item(0)->getElementsByTagName("ServiceInfo");
                if ($ServiceInfo->length > 0) {
                    $ServiceInfoName = $ServiceInfo->item(0)->getElementsByTagName("Name");
                    if ($ServiceInfoName->length > 0) {
                        $ServiceInfoName = $ServiceInfoName->item(0)->nodeValue;
                    } else {
                        $ServiceInfoName = "";
                    }
                    $ServiceInfoDescription = $ServiceInfo->item(0)->getElementsByTagName("Description");
                    if ($ServiceInfoDescription->length > 0) {
                        $ServiceInfoDescription = $ServiceInfoDescription->item(0)->nodeValue;
                    } else {
                        $ServiceInfoDescription = "";
                    }
                    $Images = $ServiceInfo->item(0)->getElementsByTagName("Images");
                    if ($Images->length > o) {
                        $Image = $Images->item(0)->getElementsByTagName("Image");
                        if ($Image->length > 0) {
                            $Featured = $Image->item(0)->getAttribute("Featured");
                            $FileName = $Image->item(0)->getElementsByTagName("FileName");
                            if ($FileName->length > 0) {
                                $FileName = $FileName->item(0)->nodeValue;
                            } else {
                                $FileName = "";
                            }
                        }
                    }
                }
                //ServiceOptions
                $ServiceOptions = $ServiceResult->item(0)->getElementsByTagName("ServiceOptions");
                if ($ServiceOptions->length > 0) {
                    $ServiceOption = $ServiceOptions->item(0)->getElementsByTagName("ServiceOption");
                    if ($ServiceOption->length > 0) {
                        $Code = $ServiceOption->item(0)->getAttribute("Code");
                        $Name = $ServiceOption->item(0)->getElementsByTagName("Name");
                        if ($Name->length > 0) {
                            $Name = $Name->item(0)->nodeValue;
                        } else {
                            $Name = "";
                        }
                        $Images = $ServiceOption->item(0)->getElementsByTagName("Images");
                        if ($Images->length > o) {
                            $Image = $Images->item(0)->getElementsByTagName("Image");
                            if ($Image->length > 0) {
                                $Image = $Image->item(0)->nodeValue;
                            } else {
                                $Image = "";
                            }
                        }
                        $Dates = $ServiceOption->item(0)->getElementsByTagName("Dates");
                        if ($Dates->length > 0) {
                            $Date = $Dates->item(0)->getElementsByTagName("Date");
                            if ($Date->length > 0) {
                                $End = $Date->item(0)->getAttribute("End");
                                $Start = $Date->item(0)->getAttribute("Start");
                                $RatePlanCode = $Date->item(0)->getAttribute("RatePlanCode");
                                $Duration = $Date->item(0)->getAttribute("Duration");
                                $Prices = $Date->item(0)->getElementsByTagName("Prices");
                                if ($Prices->length > 0) {
                                    $Price = $Prices->item(0)->getElementsByTagName("Price");
                                    if ($Price->length > 0) {
                                        $PriceType = $Price->item(0)->getAttribute("Type");
                                        $PriceCurrency = $Price->item(0)->getAttribute("Currency");
                                        $TotalFixAmounts = $Price->item(0)->getElementsByTagName("TotalFixAmounts");
                                        if ($TotalFixAmounts->length > 0) {
                                            $Nett = $TotalFixAmounts->item(0)->getAttribute("Nett");
                                            $Gross = $TotalFixAmounts->item(0)->getAttribute("Gross");
                                            $Service = $TotalFixAmounts->item(0)->getElementsByTagName("Service");
                                            if ($Service->length > 0) {
                                                $Amount = $Service->item(0)->getAttribute("Amount");
                                            } else {
                                                $Amount = "";
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
