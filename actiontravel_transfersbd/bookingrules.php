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
echo "COMECOU BOOKING RULES<br/>";
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

$sql = "SELECT RatePlanCode FROM transferavail";
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

        $url = 'http://xml-uat.bookingengine.es/webservice/JP/Operations/CheckTransactions.asmx';

        $email = 'paulo@corp.bug-software.com';
        $password = 'xA2d@a1X';
        
        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
        <soapenv:Header/>
        <soapenv:Body>
            <TransferBookingRules>
            <TransferBookingRulesRQ Version="1.1" Language="en">
                <Login Password="' . $password . '" Email="' . $email . '"/>
                <TransferBookingRuleRequest>
                    <TransferRuleOption RatePlanCode="' . $RatePlanCode . '"/>
                </TransferBookingRuleRequest>
                <AdvancedOptions>
                    <UseCurrency>USD</UseCurrency>
                    <ShowBreakdownPrice>true</ShowBreakdownPrice>
                    <ShowCompleteInfo>true</ShowCompleteInfo>
                </AdvancedOptions>
            </TransferBookingRulesRQ>
            </TransferBookingRules>
        </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml",
            "Accept-Encoding: gzip, deflate",
            "SOAPAction: http://www.juniper.es/webservice/2007/TransferBookingRules",
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
        $TransferBookingRulesResponse = $Body->item(0)->getElementsByTagName("TransferBookingRulesResponse");
        if ($TransferBookingRulesResponse->length > 0) {
            $BookingRulesRS = $TransferBookingRulesResponse->item(0)->getElementsByTagName("BookingRulesRS");
            if ($BookingRulesRS->length > 0) {
                $IntCode = $BookingRulesRS->item(0)->getAttribute("IntCode");
                $TimeStamp = $BookingRulesRS->item(0)->getAttribute("TimeStamp");
                $Url = $BookingRulesRS->item(0)->getAttribute("Url");

                $Warnings = $BookingRulesRS->item(0)->getElementsByTagName("Warnings");
                if ($Warnings->length > 0) {
                    $Warning = $Warnings->item(0)->getElementsByTagName("Warning");
                    if ($Warning->length > 0) {
                        $WarningText = $Warning->item(0)->getAttribute("Text");
                        $WarningCode = $Warning->item(0)->getAttribute("Code");
                    }
                }

                $Results = $BookingRulesRS->item(0)->getElementsByTagName("Results");
                if ($Results->length > 0) {
                    $TransferResult = $Results->item(0)->getElementsByTagName("TransferResult");
                    if ($TransferResult->length > 0) {
                        $Status = $TransferResult->item(0)->getAttribute("Status");
                        $Code = $TransferResult->item(0)->getAttribute("Code");
                        $End = $TransferResult->item(0)->getAttribute("End");
                        $Start = $TransferResult->item(0)->getAttribute("Start");
                        //BookingCode
                        $BookingCode = $TransferResult->item(0)->getElementsByTagName("BookingCode");
                        if ($BookingCode->length > 0) {
                            $ExpirationDate = $BookingCode->item(0)->getAttribute("ExpirationDate");
                            $BookingCode = $BookingCode->item(0)->nodeValue;
                        }
                        if ($WarningCode != "warnCheckNotPossible") {
                            echo "<br/>WARNING CODE: " . $WarningCode;
                            echo "<br/>BOOKING CODE: " . $BookingCode;
                        }
                        //TransferRequiredFields
                        $TransferRequiredFields = $TransferResult->item(0)->getElementsByTagName("TransferRequiredFields");
                        if ($TransferRequiredFields->length > 0) {
                            $TransferBooking = $TransferRequiredFields->item(0)->getElementsByTagName("TransferBooking");
                            if ($TransferBooking->length > 0) {
                                $Elements = $TransferBooking->item(0)->getElementsByTagName("Elements");
                                if ($Elements->length > 0) {
                                    $TransferElement = $Elements->item(0)->getElementsByTagName("TransferElement");
                                    if ($TransferElement->length > 0) {
                                        $BookingCode = $TransferElement->item(0)->getElementsByTagName("BookingCode");
                                        if ($BookingCode->length > 0) {
                                            $BookingCode = $BookingCode->item(0)->nodeValue;
                                        } else {
                                            $BookingCode = "";
                                        }
                                        $RelPaxesDist = $TransferElement->item(0)->getElementsByTagName("RelPaxesDist");
                                        if ($RelPaxesDist->length > 0) {
                                            $RelPaxDist = $RelPaxesDist->item(0)->getElementsByTagName("RelPaxDist");
                                            if ($RelPaxDist->length > 0) {
                                                $RelPaxes = $RelPaxDist->item(0)->getElementsByTagName("RelPaxes");
                                                if ($RelPaxes->length > 0) {
                                                    $RelPax = $RelPaxes->item(0)->getElementsByTagName("RelPax");
                                                    if ($RelPax->length > 0) {
                                                        for ($i=0; $i < $RelPax->length; $i++) { 
                                                            $IdPax = $RelPax->item($i)->getAttribute("IdPax");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $TransfersBookingInfo = $TransferElement->item(0)->getElementsByTagName("TransfersBookingInfo");
                                        if ($TransfersBookingInfo->length > 0) {
                                            $TransferBookingInfo = $TransfersBookingInfo->item(0)->getElementsByTagName("TransferBookingInfo");
                                            if ($TransferBookingInfo->length > 0) {
                                                $Origin = $TransferBookingInfo->item(0)->getElementsByTagName("Origin");
                                                if ($Origin->length > 0) {
                                                    $OriginType = $Origin->item(0)->getAttribute("Type");
                                                    $MeetingPointInfo = $Origin->item(0)->getElementsByTagName("MeetingPointInfo");
                                                    if ($MeetingPointInfo->length > 0) {
                                                        $Code = $MeetingPointInfo->item(0)->getAttribute("Code");
                                                        $MeetingTime = $MeetingPointInfo->item(0)->getAttribute("MeetingTime");
                                                    }
                                                    $FlightInfo = $Origin->item(0)->getElementsByTagName("FlightInfo");
                                                    if ($FlightInfo->length > 0) {
                                                        $FlightNumber = $FlightInfo->item(0)->getAttribute("FlightNumber");
                                                        $FlightTime = $FlightInfo->item(0)->getAttribute("FlightTime");
                                                    }
                                                }
                                                $Destination = $TransferBookingInfo->item(0)->getElementsByTagName("Destination");
                                                if ($Destination->length > 0) {
                                                    $DestinationType = $Destination->item(0)->getAttribute("Type");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //CancellationPolicy
                        $CancellationPolicy = $TransferResult->item(0)->getElementsByTagName("CancellationPolicy");
                        if ($CancellationPolicy->length > 0) {
                            $CurrencyCode = $CancellationPolicy->item(0)->getAttribute("CurrencyCode");
                            $Description = $CancellationPolicy->item(0)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                $Description = $Description->item(0)->nodeValue;
                            } else {
                                $Description = "";
                            }
                            $FirstDayCostCancellation = $CancellationPolicy->item(0)->getElementsByTagName("FirstDayCostCancellation");
                            if ($FirstDayCostCancellation->length > 0) {
                                $Hour = $FirstDayCostCancellation->item(0)->getAttribute("Hour");
                                $FirstDayCostCancellation = $FirstDayCostCancellation->item(0)->nodeValue;
                            } else {
                                $FirstDayCostCancellation = "";
                            }
                            $PolicyRules = $CancellationPolicy->item(0)->getElementsByTagName("PolicyRules");
                            if ($PolicyRules->length > 0) {
                                $Rule = $PolicyRules->item(0)->getElementsByTagName("Rule");
                                if ($Rule->length > 0) {
                                    for ($j=0; $j < $Rule->length; $j++) { 
                                        $Type = $Rule->item($j)->getAttribute("Type");
                                        $ApplicationTypeNights = $Rule->item($j)->getAttribute("ApplicationTypeNights");
                                        $Nights = $Rule->item($j)->getAttribute("Nights");
                                        $PercentPrice = $Rule->item($j)->getAttribute("PercentPrice");
                                        $FixedPrice = $Rule->item($j)->getAttribute("FixedPrice");
                                        $DateToHour = $Rule->item($j)->getAttribute("DateToHour");
                                        $DateTo = $Rule->item($j)->getAttribute("DateTo");
                                        $DateFromHour = $Rule->item($j)->getAttribute("DateFromHour");
                                        $DateFrom = $Rule->item($j)->getAttribute("DateFrom");
                                        $From = $Rule->item($j)->getAttribute("From");
                                        $To = $Rule->item($j)->getAttribute("To");
                                    }
                                }
                            }
                        }
                        //PriceInformation
                        $PriceInformation = $TransferResult->item(0)->getElementsByTagName("PriceInformation");
                        if ($PriceInformation->length > 0) {
                            //TransferInfo
                            $TransferInfo = $PriceInformation->item(0)->getElementsByTagName("TransferInfo");
                            if ($TransferInfo->length > 0) {
                                $Name = $TransferInfo->item(0)->getElementsByTagName("Name");
                                if ($Name->length > 0) {
                                    $Name = $Name->item(0)->nodeValue;
                                } else {
                                    $Name = "";
                                }
                                $Description = $TransferInfo->item(0)->getElementsByTagName("Description");
                                if ($Description->length > 0) {
                                    $Description = $Description->item(0)->nodeValue;
                                } else {
                                    $Description = "";
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
                                $CancellationPolicies = $TransferInfo->item(0)->getElementsByTagName("CancellationPolicies");
                                if ($CancellationPolicies->length > 0) {
                                    $CancellationPolicy = $CancellationPolicies->item(0)->getElementsByTagName("CancellationPolicy");
                                    if ($CancellationPolicy->length > 0) {
                                        $CurrencyCode = $CancellationPolicy->item(0)->getAttribute("CurrencyCode");
                                        $Description = $CancellationPolicy->item(0)->getElementsByTagName("Description");
                                        if ($Description->length > 0) {
                                            $Description = $Description->item(0)->nodeValue;
                                        } else {
                                            $Description = "";
                                        }
                                        $FirstDayCostCancellation = $CancellationPolicy->item(0)->getElementsByTagName("FirstDayCostCancellation");
                                        if ($FirstDayCostCancellation->length > 0) {
                                            $Hour = $FirstDayCostCancellation->item(0)->getAttribute("Hour");
                                            $FirstDayCostCancellation = $FirstDayCostCancellation->item(0)->nodeValue;
                                        } else {
                                            $FirstDayCostCancellation = "";
                                        }
                                        $PolicyRules = $CancellationPolicy->item(0)->getElementsByTagName("PolicyRules");
                                        if ($PolicyRules->length > 0) {
                                            $Rule = $PolicyRules->item(0)->getElementsByTagName("Rule");
                                            if ($Rule->length > 0) {
                                                for ($j=0; $j < $Rule->length; $j++) { 
                                                    $Type = $Rule->item($j)->getAttribute("Type");
                                                    $ApplicationTypeNights = $Rule->item($j)->getAttribute("ApplicationTypeNights");
                                                    $Nights = $Rule->item($j)->getAttribute("Nights");
                                                    $PercentPrice = $Rule->item($j)->getAttribute("PercentPrice");
                                                    $FixedPrice = $Rule->item($j)->getAttribute("FixedPrice");
                                                    $DateToHour = $Rule->item($j)->getAttribute("DateToHour");
                                                    $DateTo = $Rule->item($j)->getAttribute("DateTo");
                                                    $DateFromHour = $Rule->item($j)->getAttribute("DateFromHour");
                                                    $DateFrom = $Rule->item($j)->getAttribute("DateFrom");
                                                    $From = $Rule->item($j)->getAttribute("From");
                                                    $To = $Rule->item($j)->getAttribute("To");
                                                }
                                            }
                                        }
                                    }
                                }
                                $AdditionalDescriptions = $TransferInfo->item(0)->getElementsByTagName("AdditionalDescriptions");
                                if ($AdditionalDescriptions->length > 0) {
                                    $Description = $AdditionalDescriptions->item(0)->getElementsByTagName("Description");
                                    if ($Description->length > 0) {
                                        for ($iAux3=0; $iAux3 < $Description->length; $iAux3++) { 
                                            $Type = $Description->item($iAux3)->getAttribute("Type");
                                            $Title = $Description->item($iAux3)->getElementsByTagName("Title");
                                            if ($Title->length > 0) {
                                                $Title = $Title->item(0)->nodeValue;
                                            } else {
                                                $Title = "";
                                            }
                                            $Value = $Description->item($iAux3)->getElementsByTagName("Value");
                                            if ($Value->length > 0) {
                                                $Value = $Value->item(0)->nodeValue;
                                            } else {
                                                $Value = "";
                                            }
                                        }
                                    }
                                }
                            }
                            //TransferOptions
                            $TransferOptions = $PriceInformation->item(0)->getElementsByTagName("TransferOptions");
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