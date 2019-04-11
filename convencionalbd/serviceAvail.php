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
echo "COMECOU SERVICE SIATAR<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$date = new DateTime("NOW");
$timestamp = $date->format( "Y-m-d\TH:i:s.v" );

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/">
<soap:Header/>
<soap:Body>
    <xnet:getServiceAvail EchoToken="1234" TimeStamp="2019-05-09T18:08:59" Version="1.0">
    <xnet:aRequest>
    <xnet:POS>
        <xnet:Source>
            <xnet:RequestorID ID="a6dge3!tnsf2or" PartnerID="TEST" Username="xnet" Password="pctnx!!!"/>
        </xnet:Source>
    </xnet:POS>
    <xnet:AvailRequest ServiceDay="2019-05-09">
        <xnet:ServiceSearchCriterion ServiceCityCode="RIO">
            <xnet:ServiceType>Both</xnet:ServiceType>
        </xnet:ServiceSearchCriterion>
    </xnet:AvailRequest>
    <xnet:ServicePaxCandidates>
        <xnet:ServicePaxCandidate>
            <xnet:Guest AgeType="ADT" Age="0" Count="2"/>
        </xnet:ServicePaxCandidate>
    </xnet:ServicePaxCandidates>
    </xnet:aRequest>
</xnet:getServiceAvail>
</soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Content-length: ".strlen($raw)
));
$url = "http://xnetinfo.redirectme.net:8080/homologacao_webservice/Integration/ServerIntegration.asmx";

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
$getServiceAvailResponse = $Body->item(0)->getElementsByTagName("getServiceAvailResponse");
$getServiceAvailResult = $getServiceAvailResponse->item(0)->getElementsByTagName("getServiceAvailResult");
$ID = $getServiceAvailResult->item(0)->getAttribute("ID");

$ProvidedServices = $getServiceAvailResult->item(0)->getElementsByTagName("ProvidedServices");
if ($ProvidedServices->length > 0) {
    $ProvidedService = $ProvidedServices->item(0)->getElementsByTagName("ProvidedService");
    if ($ProvidedService->length > 0) {
        for ($i=0; $i < $ProvidedService->length; $i++) { 
            $Service = $ProvidedService->item($i)->getElementsByTagName("Service");
            if ($Service->length > 0) {
                $Code = $Service->item(0)->getAttribute("Code");
                $Name = $Service->item(0)->getAttribute("Name");
                $Duration = $Service->item(0)->getAttribute("Duration");
                $DocInfo = $Service->item(0)->getAttribute("DocInfo");
                $Description = $Service->item(0)->getAttribute("Description");
                $MinRate = $Service->item(0)->getAttribute("MinRate");
                $MaxRate = $Service->item(0)->getAttribute("MaxRate");
                $ProviderCityName = $Service->item(0)->getAttribute("ProviderCityName");
                $ProviderCityCode = $Service->item(0)->getAttribute("ProviderCityCode");
                $ProviderName = $Service->item(0)->getAttribute("ProviderName");
                $ProviderCode = $Service->item(0)->getAttribute("ProviderCode");

                $ServiceClass = $Service->item(0)->getElementsByTagName("ServiceClass");
                if ($ServiceClass->length > 0) {
                    $ServiceClass = $ServiceClass->item(0)->nodeValue;
                } else {
                    $ServiceClass = "";
                }
                $Type = $Service->item(0)->getElementsByTagName("Type");
                if ($Type->length > 0) {
                    $Type = $Type->item(0)->nodeValue;
                } else {
                    $Type = "";
                }
                $photos = $Service->item(0)->getElementsByTagName("photos");
                if ($photos->length > 0) {
                    $photos = $photos->item(0)->nodeValue;
                } else {
                    $photos = "";
                }
                $Comments = $Service->item(0)->getElementsByTagName("Comments");
                if ($Comments->length > 0) {
                    $Comments = $Comments->item(0)->nodeValue;
                } else {
                    $Comments = "";
                }

                $MinRateCurrency = $Service->item(0)->getElementsByTagName("MinRateCurrency");
                if ($MinRateCurrency->length > 0) {
                    $MinRateName = $MinRateCurrency->item(0)->getAttribute("Name");
                    $MinRateCode = $MinRateCurrency->item(0)->getAttribute("Code");
                } else {
                    $MinRateName = "";
                    $MinRateCode = "";
                }
                $MaxRateCurrency = $Service->item(0)->getElementsByTagName("MaxRateCurrency");
                if ($MaxRateCurrency->length > 0) {
                    $MaxRateName = $MaxRateCurrency->item(0)->getAttribute("Name");
                    $MaxRateCode = $MaxRateCurrency->item(0)->getAttribute("Code");
                } else {
                    $MaxRateName = "";
                    $MaxRateCode = "";
                }
                echo "PASTA 0<br/>";

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('services');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Code' => $Code,
                        'Name' => $Name,
                        'Duration' => $Duration,
                        'DocInfo' => $DocInfo,
                        'Description' => $Description,
                        'MinRate' => $MinRate,
                        'MaxRate' => $MaxRate,
                        'ProviderCityName' => $ProviderCityName,
                        'ProviderCityCode' => $ProviderCityCode,
                        'ProviderName' => $ProviderName,
                        'ProviderCode' => $ProviderCode,
                        'ServiceClass' => $ServiceClass,
                        'Type' => $Type,
                        'photos' => $photos,
                        'Comments' => $Comments,
                        'MinRateName' => $MinRateName,
                        'MinRateCode' => $MinRateCode,
                        'MaxRateName' => $MaxRateName,
                        'MaxRateCode' => $MaxRateCode,
                        'IDService' => $ID
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
                echo "PASSA 0<br/>";
            }
            echo "PASSOU 0<br/>";
            //serviceRates
            $serviceRates = $ProvidedService->item($i)->getElementsByTagName("serviceRates");
            echo "PASSOU 1<br/>";
            if ($serviceRates->length > 0) {
                $ServiceRate = $serviceRates->item(0)->getElementsByTagName("ServiceRate");
                echo "PASSOU 2<br/>";
                if ($ServiceRate->length > 0) {
                    echo "PASSOU 3<br/>";
                    $serviceRatesID = $ServiceRate->item(0)->getAttribute("ID");
                    $ChargeUnit = $ServiceRate->item(0)->getAttribute("ChargeUnit");
                    $Total = $ServiceRate->item(0)->getAttribute("Total");
                    $Day = $ServiceRate->item(0)->getAttribute("Day");
                    $Category = $ServiceRate->item(0)->getElementsByTagName("Category");
                    if ($Category->length > 0) {
                        $Category = $Category->item(0)->nodeValue;
                    } else {
                        $Category = "";
                    }
                    $Currency = $ServiceRate->item(0)->getElementsByTagName("Currency");
                    if ($Currency->length > 0) {
                        $CurrencyName = $Currency->item(0)->getAttribute("Name");
                        $CurrencyCode = $Currency->item(0)->getAttribute("Code");
                    } else {
                        $CurrencyName = "";
                        $CurrencyCode = "";
                    }
                    $Market = $ServiceRate->item(0)->getElementsByTagName("Market");
                    if ($Market->length > 0) {
                        $MarketCode = $Market->item(0)->getAttribute("Code");
                    } else {
                        $MarketCode = "";
                    }

                    $PaxRateOccupants = $ServiceRate->item(0)->getElementsByTagName("PaxRateOccupants");
                    if ($PaxRateOccupants->length > 0) {
                        $PaxOccupants = $PaxRateOccupants->item(0)->getElementsByTagName("PaxOccupants");
                        if ($PaxOccupants->length > 0) {
                            $PaxOccupantsTotal = $PaxOccupants->item(0)->getAttribute("Total");
                            
                            $Guest = $PaxOccupants->item(0)->getElementsByTagName("Guest");
                            if ($Guest->length > 0) {
                                $Count = $Guest->item(0)->getAttribute("Count");
                                $Age = $Guest->item(0)->getAttribute("Age");
                                $AgeType = $Guest->item(0)->getAttribute("AgeType");
                            }
                        }
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('servicesRates');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'serviceRatesID' => $serviceRatesID,
                            'ChargeUnit' => $ChargeUnit,
                            'Total' => $Total,
                            'Day' => $Day,
                            'Category' => $Category,
                            'CurrencyName' => $CurrencyName,
                            'CurrencyCode' => $CurrencyCode,
                            'PaxOccupantsTotal' => $PaxOccupantsTotal,
                            'Count' => $Count,
                            'Age' => $Age,
                            'AgeType' => $AgeType,
                            'MarketCode' => $MarketCode,
                            'IDService' => $ID
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO RATE: " . $e;
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
echo '<br/>Done';
?>