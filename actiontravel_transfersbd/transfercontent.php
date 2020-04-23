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
echo "COMECOU TRANSFER CONTENT";
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

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/StaticDataTransactions.asmx';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';

$raw= '<soapenv:Envelope
xmlns:soapenv = "http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
    <TransferContent>
        <TransferContentRQ Version = "1.1" Language = "en">
            <Login Password="' . $password . '" Email="' . $email . '"/>
            <TransferContentList>
                <Transfer Code="HmI+0kDMRhu0c9ysAbhvxQ=="/>
                <Transfer Code="w919t5yai/LXJJVDvQKzeg=="/>
            </TransferContentList>
        </TransferContentRQ>
    </TransferContent>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=UTF-8",
    "SOAPAction: http://www.juniper.es/webservice/2007/TransferContent",
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
$TransferContentResponse = $Body->item(0)->getElementsByTagName("TransferContentResponse");
if ($TransferContentResponse->length > 0) {
    $ContentRS = $TransferContentResponse->item(0)->getElementsByTagName("ContentRS");
    if ($ContentRS->length > 0) {
        $IntCode = $ContentRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $ContentRS->item(0)->getAttribute("TimeStamp");
        $Url = $ContentRS->item(0)->getAttribute("Url");
        $Contents = $ContentRS->item(0)->getElementsByTagName("Contents");
        if ($Contents->length > 0) {
            $TransferContent = $Contents->item(0)->getElementsByTagName("TransferContent");
            if ($TransferContent->length > 0) {
                for ($i=0; $i < $TransferContent->length; $i++) { 
                    $Code = $TransferContent->item($i)->getAttribute("Code");
                    $TransferType = $TransferContent->item($i)->getElementsByTagName("TransferType");
                    if ($TransferType->length > 0) {
                        $Code = $TransferType->item(0)->getAttribute("Code");
                        $TransferTypeName = $TransferType->item(0)->getElementsByTagName("Name");
                        if ($TransferTypeName->length > 0) {
                            $TransferTypeName = $TransferTypeName->item(0)->nodeValue;
                        } else {
                            $TransferTypeName = "";
                        }
                    }
                    //TransferContentInfo
                    $TransferContentInfo = $TransferContent->item($i)->getElementsByTagName("TransferContentInfo");
                    if ($TransferContentInfo->length > 0) {
                        $TransferName = $TransferContentInfo->item(0)->getElementsByTagName("TransferName");
                        if ($TransferName->length > 0) {
                            $TransferName = $TransferName->item(0)->nodeValue;
                        } else {
                            $TransferName = "";
                        }
                        $Descriptions = $TransferContentInfo->item(0)->getElementsByTagName("Descriptions");
                        if ($Descriptions->length > 0) {
                            $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                for ($iAux2=0; $iAux2 < $Description->length; $iAux2++) { 
                                    $Type = $Description->item($iAux2)->getAttribute("Type");
                                    $Description = $Description->item($iAux2)->nodeValue;
                                }
                            }
                        }
                        $Images = $TransferContentInfo->item(0)->getElementsByTagName("Images");
                        if ($Images->length > 0) {
                            $Image = $Images->item(0)->getElementsByTagName("Image");
                            if ($Image->length > 0) {
                                for ($iAux3=0; $iAux3 < $Image->length; $iAux3++) { 
                                    $Type = $Image->item($iAux3)->getAttribute("Type");
                                    $FileName = $Image->item($iAux3)->getElementsByTagName("FileName");
                                    if ($FileName->length > 0) {
                                        $FileName = $FileName->item(0)->nodeValue;
                                    } else {
                                        $FileName = "";
                                    }
                                }
                            }
                        }
                    }
                    //TransferZones
                    $TransferZones = $TransferContent->item($i)->getElementsByTagName("TransferZones");
                    if ($TransferZones->length > 0) {
                        $Origins = $TransferZones->item(0)->getElementsByTagName("Origins");
                        if ($Origins->length > 0) {
                            $Origin = $Origins->item(0)->getElementsByTagName("Origin");
                            if ($Origin->length > 0) {
                                for ($iAux=0; $iAux < $Origin->length; $iAux++) { 
                                    $Code = $Origin->item($iAux)->getAttribute("Code");
                                    $JPDCode = $Origin->item($iAux)->getAttribute("JPDCode");
                                    $Name = $Origin->item($iAux)->getElementsByTagName("Name");
                                    if ($Name->length > 0) {
                                        $Name = $Name->item(0)->nodeValue;
                                    } else {
                                        $Name = "";
                                    }
                                }
                            }
                        }
                        $Destinations = $TransferZones->item(0)->getElementsByTagName("Destinations");
                        if ($Destinations->length > 0) {
                            $Destination = $Destinations->item(0)->getElementsByTagName("Destination");
                            if ($Destination->length > 0) {
                                for ($iAux=0; $iAux < $Destination->length; $iAux++) { 
                                    $Code = $Destination->item($iAux)->getAttribute("Code");
                                    $JPDCode = $Destination->item($iAux)->getAttribute("JPDCode");
                                    $Name = $Destination->item($iAux)->getElementsByTagName("Name");
                                    if ($Name->length > 0) {
                                        $Name = $Name->item(0)->nodeValue;
                                    } else {
                                        $Name = "";
                                    }
                                }
                            }
                        }
                    }
                    //TransferOptions
                    $TransferOptions = $TransferContent->item($i)->getElementsByTagName("TransferOptions");
                    if ($TransferOptions->length > 0) {
                        $TransferOptions = $TransferOptions->item(0)->getElementsByTagName("TransferOptions");
                        if ($TransferOptions->length > 0) {
                            for ($iAux4=0; $iAux4 < $TransferOptions->length; $iAux4++) { 
                                $StartTime = $TransferOptions->item($iAux4)->getAttribute("StartTime");
                                $Name = $TransferOptions->item($iAux4)->getElementsByTagName("Name");
                                if ($Name->length > 0) {
                                    $Name = $Name->item(0)->nodeValue;
                                } else {
                                    $Name = "";
                                }
                                $Descriptions = $TransferOptions->item($iAux4)->getElementsByTagName("Descriptions");
                                if ($Descriptions->length > 0) {
                                    $Descriptions = $Descriptions->item(0)->nodeValue;
                                } else {
                                    $Descriptions = "";
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
