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
echo "COMECOU SERVICE AVAILABILITY";
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

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/availtransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.juniper.es/webservice/2007/">
   <soapenv:Header/>
   <soapenv:Body>
      <ns:ServiceAvail>
         <ns:ServiceAvailRQ Version="1.1" Language="es">
            <ns:Login Password="' . $password . '" Email="' . $email . '"/>
            <ns:Paxes>
               <ns:Pax IdPax="1">
                  <ns:Age>30</ns:Age>
               </ns:Pax>
               <ns:Pax IdPax="2">
                  <ns:Age>28</ns:Age>
               </ns:Pax>
               <ns:Pax IdPax="3">
                  <ns:Age>5</ns:Age>
               </ns:Pax>
               <ns:Pax IdPax="4">
                  <ns:Age>15</ns:Age>
               </ns:Pax>
            </ns:Paxes>
            <ns:ServiceRequest>
               <ns:SearchSegmentsServices>
                  <ns:SearchSegmenServices Start="2020-04-03" End="2020-04-08" DestinationZone = "15011"></ns:SearchSegmenServices>
               </ns:SearchSegmentsServices>  
                <ns:RelPaxesDist>
                  <ns:RelPaxDist>
                     <ns:RelPaxes>
                        <ns:RelPax IdPax="1"></ns:RelPax>
                        <ns:RelPax IdPax="2"></ns:RelPax>
                        <ns:RelPax IdPax="3"></ns:RelPax>
                        <ns:RelPax IdPax="4"></ns:RelPax>
                     </ns:RelPaxes>
                  </ns:RelPaxDist>
               </ns:RelPaxesDist>             
            </ns:ServiceRequest>
         </ns:ServiceAvailRQ>
      </ns:ServiceAvail>
   </soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/xml",
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$xmlresult = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($xmlresult);
echo "</xmp>";
die();
/* $url3 = "https://xml-uat.bookingengine.es/WebService/jp/operations/staticdatatransactions.asmx";
$raw3 = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.juniper.es/webservice/2007/">
   <soapenv:Header/>
   <soapenv:Body>
      <ns:ServiceCatalogueData>
         <ns:ServiceCatalogueDataRQ>
            <ns:Login Password="' . $password . '" Email="' . $email . '" />
         </ns:ServiceCatalogueDataRQ>
      </ns:ServiceCatalogueData>
   </soapenv:Body>
</soapenv:Envelope>';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url3);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw3);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/xml",
    "Content-type: text/xml",
    "SOAPAction: http://www.juniper.es/webservice/2007/ServiceCatalogueData",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw3)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$xmlresult = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);
$endTime = microtime();
echo $xmlresult; */

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
$inputDoc->loadXML($xmlresult);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$ServiceAvailResponse = $Body->item(0)->getElementsByTagName("ServiceAvailResponse");
if ($ServiceAvailResponse->length > 0) {
    $AvailabilityRS = $ServiceAvailResponse->item(0)->getElementsByTagName("AvailabilityRS");
    if ($AvailabilityRS->length > 0) {
        $IntCode = $AvailabilityRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $AvailabilityRS->item(0)->getAttribute("TimeStamp");
        $Url = $AvailabilityRS->item(0)->getAttribute("Url");
        $Results = $AvailabilityRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $ServiceResult = $Results->item(0)->getElementsByTagName("ServiceResult");
            if ($ServiceResult->length > 0) {
                for ($i = 0; $i < $ServiceResult->length; $i++) {
                    $Status = $ServiceResult->item($i)->getAttribute("Status");
                    $DestinationZone = $ServiceResult->item($i)->getAttribute("DestinationZone");
                    $End = $ServiceResult->item($i)->getAttribute("End");
                    $Start = $ServiceResult->item($i)->getAttribute("Start");
                    $Code = $ServiceResult->item($i)->getAttribute("Code");
                    //ServiceInfo
                    $ServiceInfo = $ServiceResult->item($i)->getElementsByTagName("ServiceInfo");
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
                    $ServiceOptions = $ServiceResult->item($i)->getElementsByTagName("ServiceOptions");
                    if ($ServiceOptions->length > 0) {
                        $ServiceOption = $ServiceOptions->item(0)->getElementsByTagName("ServiceOption");
                        if ($ServiceOption->length > 0) {
                            for ($iAux=0; $iAux < $ServiceOption->length; $iAux++) { 
                                $Code = $ServiceOption->item($iAux)->getAttribute("Code");
                                $Name = $ServiceOption->item($iAux)->getElementsByTagName("Name");
                                if ($Name->length > 0) {
                                    $Name = $Name->item(0)->nodeValue;
                                } else {
                                    $Name = "";
                                }
                                $Images = $ServiceOption->item($iAux)->getElementsByTagName("Images");
                                if ($Images->length > o) {
                                    $Image = $Images->item(0)->getElementsByTagName("Image");
                                    if ($Image->length > 0) {
                                        $Image = $Image->item(0)->nodeValue;
                                    } else {
                                        $Image = "";
                                    }
                                }
                                $Dates = $ServiceOption->item($iAux)->getElementsByTagName("Dates");
                                if ($Dates->length > 0) {
                                    $Date = $Dates->item(0)->getElementsByTagName("Date");
                                    if ($Date->length > 0) {
                                        for ($iAux2=0; $iAux2 < $Date->length; $iAux2++) { 
                                            $End = $Date->item($iAux2)->getAttribute("End");
                                            $Start = $Date->item($iAux2)->getAttribute("Start");
                                            $RatePlanCode = $Date->item($iAux2)->getAttribute("RatePlanCode");
                                            $Duration = $Date->item($iAux2)->getAttribute("Duration");
                                            $Prices = $Date->item($iAux2)->getElementsByTagName("Prices");
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
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
