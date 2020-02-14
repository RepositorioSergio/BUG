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

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/checktransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';
$RatePlanCode = "/bFyf0Mia38YuvTiH/2eBfB6NpD/0n6R1pdpNJ6oXkinqqFuwdNxTtNVSpS7VmQGr8gnQe5f6LU/IdBJFBqsntDDwJCAkuK26SwNOpjz2i1HPUPOmiJVZYj4iDe6ie0106WDUZPMuY8nncABzmllSlS6bxxo+6I4UyK32KeykkN1iueHlJrefdH4hYA/TRDZ";

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
            }
        }
        $Results = $BookingRulesRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $ServiceResult = $Results->item(0)->getElementsByTagName("ServiceResult");
            if ($ServiceResult->length > 0) {
                $Status = $ServiceResult->item(0)->getAttribute("Status");
                $DestinationZone = $ServiceResult->item(0)->getAttribute("DestinationZone");
                $End = $ServiceResult->item(0)->getAttribute("End");
                $Start = $ServiceResult->item(0)->getAttribute("Start");
                $Code = $ServiceResult->item(0)->getAttribute("Code");
                //BookingCode
                $BookingCode = $ServiceResult->item(0)->getElementsByTagName("BookingCode");
                if ($BookingCode->length > 0) {
                    $ExpirationDate = $BookingCode->item(0)->getAttribute("ExpirationDate");
                    $BookingCode = $BookingCode->item(0)->nodeValue;
                } else {
                    $BookingCode = "";
                    $ExpirationDate = "";
                }
                //ServiceRequiredFields
                $ServiceRequiredFields = $ServiceResult->item(0)->getElementsByTagName("ServiceRequiredFields");
                if ($ServiceRequiredFields->length > 0) {
                    $ServiceBooking = $ServiceRequiredFields->item(0)->getElementsByTagName("ServiceBooking");
                    if ($ServiceBooking->length > 0) {
                        $Holder = $ServiceBooking->item(0)->getElementsByTagName("Holder");
                        if ($Holder->length > 0) {
                            $RelPax = $Holder->item(0)->getElementsByTagName("RelPax");
                            if ($RelPax->length > 0) {
                                $IdPax = $RelPax->item(0)->getAttribute("IdPax");
                            }
                        }
                        //Elements
                        $Elements = $ServiceBooking->item(0)->getElementsByTagName("Elements");
                        if ($Elements->length > 0) {
                            $ServiceElement = $Elements->item(0)->getElementsByTagName("ServiceElement");
                            if ($ServiceElement->length > 0) {
                                $BookingCode = $ServiceElement->item(0)->getElementsByTagName("BookingCode");
                                if ($BookingCode->length > 0) {
                                    $BookingCode = $BookingCode->item(0)->nodeValue;
                                } else {
                                    $BookingCode = "";
                                }
                                $RelPaxesDist = $ServiceElement->item(0)->getElementsByTagName("RelPaxesDist");
                                if ($RelPaxesDist->length > 0) {
                                    $RelPaxDist = $RelPaxesDist->item(0)->getElementsByTagName("RelPaxDist");
                                    if ($RelPaxDist->length > 0) {
                                        $RelPaxes = $RelPaxDist->item(0)->getElementsByTagName("RelPaxes");
                                        if ($RelPaxes->length > 0) {
                                            $RelPax = $RelPaxes->item(0)->getElementsByTagName("RelPax");
                                            if ($RelPax->length > 0) {
                                                for ($iAux=0; $iAux < $RelPax->length; $iAux++) { 
                                                    $IdPax = $RelPax->item($iAux)->getAttribute("IdPax");
                                                }
                                            }
                                        }
                                    }
                                }
                                $ServiceBookingInfo = $ServiceElement->item(0)->getElementsByTagName("ServiceBookingInfo");
                                if ($ServiceBookingInfo->length > 0) {
                                    $MeetingPointInfo = $ServiceBookingInfo->item(0)->getElementsByTagName("MeetingPointInfo");
                                    if ($MeetingPointInfo->length > 0) {
                                        $MeetingTime = $MeetingPointInfo->item(0)->getAttribute("MeetingTime");
                                        $MeetingCode = $MeetingPointInfo->item(0)->getAttribute("Code");
                                        $MeetingName = $Pax->item(0)->getElementsByTagName("Name");
                                        if ($MeetingName->length > 0) {
                                            $MeetingName = $MeetingName->item(0)->nodeValue;
                                        } else {
                                            $MeetingName = "";
                                        }
                                        $MeetingAddress = $Pax->item(0)->getElementsByTagName("Address");
                                        if ($MeetingAddress->length > 0) {
                                            $MeetingAddress = $MeetingAddress->item(0)->nodeValue;
                                        } else {
                                            $MeetingAddress = "";
                                        }
                                        $MeetingPostalCode = $Pax->item(0)->getElementsByTagName("PostalCode");
                                        if ($MeetingPostalCode->length > 0) {
                                            $MeetingPostalCode = $MeetingPostalCode->item(0)->nodeValue;
                                        } else {
                                            $MeetingPostalCode = "";
                                        }
                                    } 
                                }
                            }
                        }

                        $Paxes = $ServiceBooking->item(0)->getElementsByTagName("Paxes");
                        if ($Paxes->length > 0) {
                            $Pax = $Paxes->item(0)->getElementsByTagName("Pax");
                            if ($Pax->length > 0) {
                                for ($i=0; $i < $Pax->length; $i++) { 
                                    $IdPax = $Pax->item($i)->getAttribute("IdPax");
                                    $Name = $Pax->item($i)->getElementsByTagName("Name");
                                    if ($Name->length > 0) {
                                        $Name = $Name->item(0)->nodeValue;
                                    } else {
                                        $Name = "";
                                    }
                                    $Surname = $Pax->item($i)->getElementsByTagName("Surname");
                                    if ($Surname->length > 0) {
                                        $Surname = $Surname->item(0)->nodeValue;
                                    } else {
                                        $Surname = "";
                                    }
                                    $Email = $Pax->item($i)->getElementsByTagName("Email");
                                    if ($Email->length > 0) {
                                        $Email = $Email->item(0)->nodeValue;
                                    } else {
                                        $Email = "";
                                    }
                                    $Address = $Pax->item($i)->getElementsByTagName("Address");
                                    if ($Address->length > 0) {
                                        $Address = $Address->item(0)->nodeValue;
                                    } else {
                                        $Address = "";
                                    }
                                    $City = $Pax->item($i)->getElementsByTagName("City");
                                    if ($City->length > 0) {
                                        $City = $City->item(0)->nodeValue;
                                    } else {
                                        $City = "";
                                    }
                                    $Country = $Pax->item($i)->getElementsByTagName("Country");
                                    if ($Country->length > 0) {
                                        $Country = $Country->item(0)->nodeValue;
                                    } else {
                                        $Country = "";
                                    }
                                    $PostalCode = $Pax->item($i)->getElementsByTagName("PostalCode");
                                    if ($PostalCode->length > 0) {
                                        $PostalCode = $PostalCode->item(0)->nodeValue;
                                    } else {
                                        $PostalCode = "";
                                    }
                                    $Age = $Pax->item($i)->getElementsByTagName("Age");
                                    if ($Age->length > 0) {
                                        $Age = $Age->item(0)->nodeValue;
                                    } else {
                                        $Age = "";
                                    }
                                    $PhoneNumbers = $Pax->item($i)->getElementsByTagName("PhoneNumbers");
                                    if ($PhoneNumbers->length > 0) {
                                        $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                                        if ($PhoneNumber->length > 0) {
                                            $PhoneNumber = $PhoneNumber->item(0)->nodeValue;
                                        } else {
                                            $PhoneNumber = "";
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //CancellationPolicy
                $CancellationPolicy = $ServiceResult->item(0)->getElementsByTagName("CancellationPolicy");
                if ($CancellationPolicy->length > 0) {
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
                        $Hour = "";
                        $FirstDayCostCancellation = "";
                    } 
                    $PolicyRules = $CancellationPolicy->item(0)->getElementsByTagName("PolicyRules");
                    if ($PolicyRules->length > 0) {
                        $Rule = $PolicyRules->item(0)->getElementsByTagName("Rule");
                        if ($Rule->length > 0) {
                            $ApplicationTypeNights = $Rule->item(0)->getAttribute("ApplicationTypeNights");
                            $Nights = $Rule->item(0)->getAttribute("Nights");
                            $PercentPrice = $Rule->item(0)->getAttribute("PercentPrice");
                            $FixedPrice = $Rule->item(0)->getAttribute("FixedPrice");
                            $Type = $Rule->item(0)->getAttribute("Type");
                            $DateFromHour = $Rule->item(0)->getAttribute("DateFromHour");
                            $DateFrom = $Rule->item(0)->getAttribute("DateFrom");
                        }
                    }                   
                }

                //PriceInformation
                $PriceInformation = $ServiceResult->item(0)->getElementsByTagName("PriceInformation");
                if ($PriceInformation->length > 0) {
                    $ServiceInfo = $PriceInformation->item(0)->getElementsByTagName("ServiceInfo");
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
                                $Image = $Image->item(0)->nodeValue;
                            } else {
                                $Image = "";
                            }
                        }
                    }
                    $ServiceOptions = $PriceInformation->item(0)->getElementsByTagName("ServiceOptions");
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
                                $OccupancyAllowed = $ServiceOption->item($iAux)->getElementsByTagName("OccupancyAllowed");
                                if ($OccupancyAllowed->length > 0) {
                                    $Children = $OccupancyAllowed->item(0)->getAttribute("Children");
                                } else {
                                    $Children = "";
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
                                                $Breakdown = $Price->item(0)->getElementsByTagName("Breakdown");
                                                if ($Breakdown->length > 0) {
                                                    $Concepts = $Breakdown->item(0)->getElementsByTagName("Concepts");
                                                    if ($Concepts->length > 0) {
                                                        $Concept = $Concepts->item(0)->getElementsByTagName("Concept");
                                                        if ($Concept->length > 0) {
                                                            $ConceptType = $Concept->item(0)->getAttribute("Type");
                                                            $ConceptName = $Concept->item(0)->getAttribute("Name");
                                                            $Items = $Concept->item(0)->getElementsByTagName("Items");
                                                            if ($Items->length > 0) {
                                                                $Item = $Items->item(0)->getElementsByTagName("Item");
                                                                if ($Item->length > 0) {
                                                                    for ($iAux2=0; $iAux2 < $Item->length; $iAux2++) { 
                                                                        $Amount = $Item->item($iAux2)->getAttribute("Amount");
                                                                        $TtaCode = $Item->item($iAux2)->getAttribute("TtaCode");
                                                                        $Days = $Item->item($iAux2)->getAttribute("Days");
                                                                        $Quantity = $Item->item($iAux2)->getAttribute("Quantity");
                                                                        $Date = $Item->item($iAux2)->getAttribute("Date");
                                                                        $PaxType = $Item->item($iAux2)->getAttribute("PaxType");
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
        }
    }
}


try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('allowedCardsData');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'card_type' => $card_type,
        'name' => $name
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
