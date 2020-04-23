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
echo "COMECOU CHECKAVAIL<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://xml-uat.bookingengine.es/webservice/JP/Operations/CheckTransactions.asmx';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';
$RatePlanCode = 'AxqgIvnsoMiJdBzhqSMRSAO5yk18UkmhgfO8en0JVGuu0fDtsuRc1ncTV7sz9dnhj5MPmQ9ij8mGvB06KdDi8x7pHVvzSABgulcomXUJniLD+BXkZBizw/uVP7L2KULT4neKWXutPcInZdnB2hFIU3j4nQ9ILRrxT3FGWTfNMxUw/oEkPh0VEL/8dZ1lBpqXk9FLbVBMup1CJ8XZ0Gw8Qg==';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
   <TransferCheckAvail>
      <TransferCheckAvailRQ Version="1.1" Language="en">
         <Login Password="' . $password . '" Email="' . $email . '"/>
         <TransferCheckAvailRequest>
            <TransferCheckOption RatePlanCode="' . $RatePlanCode . '"/>
         </TransferCheckAvailRequest>
         <AdvancedOptions>
            <ShowBreakdownPrice>true</ShowBreakdownPrice>
         </AdvancedOptions>
      </TransferCheckAvailRQ>
   </TransferCheckAvail>
</soapenv:Body>
</soapenv:Envelope>';
echo "<xmp>";
var_dump($raw);
echo "</xmp>"; 

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: http://www.juniper.es/webservice/2007/TransferCheckAvail",
    "Content-length: " . strlen($raw)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.musement.php');
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
$TransferCheckAvailResponse = $Body->item(0)->getElementsByTagName("TransferCheckAvailResponse");
if ($TransferCheckAvailResponse->length > 0) {
    $CheckAvailRS = $TransferCheckAvailResponse->item(0)->getElementsByTagName("CheckAvailRS");
    if ($CheckAvailRS->length > 0) {
        $IntCode = $CheckAvailRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $CheckAvailRS->item(0)->getAttribute("TimeStamp");
        $Url = $CheckAvailRS->item(0)->getAttribute("Url");
        $Results = $CheckAvailRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $TransferResult = $Results->item(0)->getElementsByTagName("TransferResult");
            if ($TransferResult->length > 0) {
                for ($i=0; $i < $TransferResult->length; $i++) { 
                    $Status = $TransferResult->item($i)->getAttribute("Status");
                    $Code = $TransferResult->item($i)->getAttribute("Code");
                    $End = $TransferResult->item($i)->getAttribute("End");
                    $Start = $TransferResult->item($i)->getAttribute("Start");
                    //TransferInfo
                    $TransferInfo = $TransferResult->item($i)->getElementsByTagName("TransferInfo");
                    if ($TransferInfo->length > 0) {
                        $Name = $TransferInfo->item(0)->getElementsByTagName("Name");
                        if ($Name->length > 0) {
                            $Name = $Name->item(0)->nodeValue;
                        } else {
                            $Name = "";
                        }
                        $Images = $TransferInfo->item(0)->getElementsByTagName("Images");
                        if ($Images->length > 0) {
                            $Image = $Images->item(0)->getElementsByTagName("Image");
                            if ($Image->length > 0) {
                                for ($iAux=0; $iAux < $Image->length; $iAux++) { 
                                    $Featured = $Image->item($iAux)->getAttribute("Featured");
                                    $FileName = $Image->item($iAux)->getElementsByTagName("FileName");
                                    if ($FileName->length > 0) {
                                        $FileName = $FileName->item(0)->nodeValue;
                                    } else {
                                        $FileName = "";
                                    }
                                }
                            }
                        }
                    }
                    //TransferOptions
                    $TransferOptions = $TransferResult->item($i)->getElementsByTagName("TransferOptions");
                    if ($TransferOptions->length > 0) {
                        $TransferOption = $TransferOptions->item(0)->getElementsByTagName("TransferOption");
                        if ($TransferOption->length > 0) {
                            $TransferOptionCode = $TransferOption->item(0)->getAttribute("Code");
                            $Duration = $TransferOption->item(0)->getAttribute("Duration");
                            $TransferOptionName = $TransferOption->item(0)->getElementsByTagName("Name");
                            if ($TransferOptionName->length > 0) {
                                $TransferOptionName = $TransferOptionName->item(0)->nodeValue;
                            } else {
                                $TransferOptionName = "";
                            }
                            $Description = $TransferOption->item(0)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                $Description = $Description->item(0)->nodeValue;
                            } else {
                                $Description = "";
                            }
                            $OriginName = $TransferOption->item(0)->getElementsByTagName("OriginName");
                            if ($OriginName->length > 0) {
                                $OriginName = $OriginName->item(0)->nodeValue;
                            } else {
                                $OriginName = "";
                            }
                            $DestinationName = $TransferOption->item(0)->getElementsByTagName("DestinationName");
                            if ($DestinationName->length > 0) {
                                $DestinationName = $DestinationName->item(0)->nodeValue;
                            } else {
                                $DestinationName = "";
                            }
                            $Dates = $TransferOption->item(0)->getElementsByTagName("Dates");
                            if ($Dates->length > 0) {
                                $Date = $Dates->item(0)->getElementsByTagName("Date");
                                if ($Date->length > 0) {
                                    $RatePlanCode = $Date->item(0)->getAttribute("RatePlanCode");
                                    $Start = $Date->item(0)->getAttribute("Start");
                                    $Prices = $Date->item(0)->getElementsByTagName("Prices");
                                    if ($Prices->length > 0) {
                                        $Price = $Prices->item(0)->getElementsByTagName("Price");
                                        if ($Price->length > 0) {
                                            $Currency = $Price->item(0)->getAttribute("Currency");
                                            $Type = $Price->item(0)->getAttribute("Type");
                                            $TotalFixAmounts = $Price->item(0)->getElementsByTagName("TotalFixAmounts");
                                            if ($TotalFixAmounts->length > 0) {
                                                $Nett = $TotalFixAmounts->item(0)->getAttribute("Nett");
                                                $Gross = $TotalFixAmounts->item(0)->getAttribute("Gross");
                                                $Service = $TotalFixAmounts->item(0)->getElementsByTagName("Service");
                                                if ($Service->length > 0) {
                                                    $Amount = $Service->item(0)->getAttribute("Amount");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    //Origins
                    $Origins = $TransferResult->item($i)->getElementsByTagName("Origins");
                    if ($Origins->length > 0) {
                        $Origin = $Origins->item(0)->getElementsByTagName("Origin");
                        if ($Origin->length > 0) {
                            $OriginCode = $Origin->item(0)->getAttribute("Code");
                            $OriginType = $Origin->item(0)->getAttribute("Type");
                            $OriginName2 = $Origin->item(0)->getElementsByTagName("Name");
                            if ($OriginName2->length > 0) {
                                $OriginName2 = $OriginName2->item(0)->nodeValue;
                            } else {
                                $OriginName2 = "";
                            }
                        }
                    }
                    //Destinations
                    $Destinations = $TransferResult->item($i)->getElementsByTagName("Destinations");
                    if ($Destinations->length > 0) {
                        $Destination = $Destinations->item(0)->getElementsByTagName("Destination");
                        if ($Destination->length > 0) {
                            $DestinationCode = $Destination->item(0)->getAttribute("Code");
                            $DestinationType = $Destination->item(0)->getAttribute("Type");
                            $DestinationName = $Destination->item(0)->getElementsByTagName("Name");
                            if ($DestinationName->length > 0) {
                                $DestinationName = $DestinationName->item(0)->nodeValue;
                            } else {
                                $DestinationName = "";
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
echo 'Done';
?>