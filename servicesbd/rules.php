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
echo "COMECOU SERVICE BOOKING RULES<br/>";
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

$sql = "SELECT RatePlanCode FROM services_plancode";
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
        $RatePlanCode = $row->RatePlanCode;

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/checktransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';
//$RatePlanCode = "j/aXsLb+uVj8oX1dYcGvV7TM9Qb3UCrMDvMvcMdZTJtNREbuYCg58ElmwbcDIv6bxvnXcw2qa+xMJZDgm0M1eQzFX5BZnLfgX49pXqNocJpxAsT2dBvhD5ge7Oo4DoMdWn8dYbG95b2LN0RibfkjvBEwfOhteDpEVlzERr4chY/eQWCrb9JuYYB17nokWoO9cS/s228YyhbzCkI8YDC9/Jsc+w1cd/OMHG1kZgRGx6A=";

$raw= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
    <ServiceBookingRules xmlns="http://www.juniper.es/webservice/2007/">
        <ServiceBookingRulesRQ Version="1.1" Language="en">
            <Login Password="' . $password . '" Email="' . $email . '"/>
            <ServiceBookingRuleRequest>
                <ServiceRuleOption RatePlanCode="' . $RatePlanCode . '"/>
            </ServiceBookingRuleRequest>
            <AdvancedOptions>
                <ShowBreakdownPrice>true</ShowBreakdownPrice>
            </AdvancedOptions>
        </ServiceBookingRulesRQ>
    </ServiceBookingRules>
    <ns:ServiceBookingRules/>
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
    "SOAPAction: http://www.juniper.es/webservice/2007/ServiceBookingRules",
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
$valido = true;
$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$ServiceBookingRulesResponse = $Body->item(0)->getElementsByTagName("ServiceBookingRulesResponse");
if ($ServiceBookingRulesResponse->length > 0) {
    $BookingRulesRS = $ServiceBookingRulesResponse->item(0)->getElementsByTagName("BookingRulesRS");
    if ($BookingRulesRS->length > 0) {
        $IntCode = $BookingRulesRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $BookingRulesRS->item(0)->getAttribute("TimeStamp");
        $Url = $BookingRulesRS->item(0)->getAttribute("Url");
        $Warnings = $BookingRulesRS->item(0)->getElementsByTagName("Warnings");
        if ($Warnings->length > 0) {
            $Warning = $Warnings->item(0)->getElementsByTagName("Warning");
            if ($Warning->length > 0) {
                $Text = $Warning->item(0)->getAttribute("Text");
                $WarningCode = $Warning->item(0)->getAttribute("Code");
                $valido = false;
            }
        }
        $Results = $BookingRulesRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $ServiceResult = $Results->item(0)->getElementsByTagName("ServiceResult");
            if ($ServiceResult->length > 0) {
                $Service = $ServiceResult->item(0)->getElementsByTagName("Service");
                if ($Service->length > 0) {
                    $Status = $Service->item(0)->getAttribute("Status");
                    //BookingCode
                    $BookingCode = $Service->item(0)->getElementsByTagName("BookingCode");
                    if ($BookingCode->length > 0) {
                        $ExpirationDate = $BookingCode->item(0)->getAttribute("ExpirationDate");
                        $BookingCode = $BookingCode->item(0)->nodeValue;
                    } else {
                        $BookingCode = "";
                        $ExpirationDate = "";
                    }
                    if ($valido == true) {
                        echo $return;
                        echo $BookingCode;
                        echo $return;
                        die();
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
