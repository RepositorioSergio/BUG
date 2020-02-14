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
echo "COMECOU READ BOOKING<br/>";
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

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/BookTransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';
$reservationlocator = "NV655K";

$raw= '<soapenv:Envelope xmlns:soapenv = "http://schemas.xmlsoap.org/soap/envelope/" xmlns = "http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
    <ReadBooking>
        <ReadRQ Version = "1.1" Language = "en">
            <Login Email = "' . $email . '" Password = "' . $password . '"/>
            <ReadRequest ReservationLocator = "' . $reservationlocator . '"/>
        </ReadRQ>
    </ReadBooking>
</soapenv:Body>
</soapenv:Envelope>';

/* $client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Accept: application/xml",
    "Content-type: text/xml;charset=UTF-8",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: http://www.juniper.es/webservice/2007/ReadBooking",
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
} */

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
    "SOAPAction: http://www.juniper.es/webservice/2007/ReadBooking",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

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
$ReadBookingResponse = $Body->item(0)->getElementsByTagName("ReadBookingResponse");
if ($ReadBookingResponse->length > 0) {
    $BookingRS = $ReadBookingResponse->item(0)->getElementsByTagName("BookingRS");
    if ($BookingRS->length > 0) {
        $IntCode = $BookingRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $BookingRS->item(0)->getAttribute("TimeStamp");
        $Url = $BookingRS->item(0)->getAttribute("Url");
        $Reservations = $BookingRS->item(0)->getElementsByTagName("Reservations");
        if ($Reservations->length > 0) {
            $Reservation = $Reservations->item(0)->getElementsByTagName("Reservation");
            if ($Reservation->length > 0) {
                $Status = $Reservation->item(0)->getAttribute("Status");
                $Language = $Reservation->item(0)->getAttribute("Language");
                $Locator = $Reservation->item(0)->getAttribute("Locator");
                $ExternalBookingReference = $Reservation->item(0)->getElementsByTagName("ExternalBookingReference");
                if ($ExternalBookingReference->length > 0) {
                    $ExternalBookingReference = $ExternalBookingReference->item(0)->nodeValue;
                } else {
                    $ExternalBookingReference = "";
                }
                $Holder = $Reservation->item(0)->getElementsByTagName("Holder");
                if ($Holder->length > 0) {
                    $RelPax = $Holder->item(0)->getElementsByTagName("RelPax");
                    if ($RelPax->length > 0) {
                        $IdPax = $RelPax->item(0)->getAttribute("IdPax");
                    }
                }
                //Paxes
                $Paxes = $Reservation->item(0)->getElementsByTagName("Paxes");
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
                            $BornDate = $Pax->item($i)->getElementsByTagName("BornDate");
                            if ($BornDate->length > 0) {
                                $BornDate = $BornDate->item(0)->nodeValue;
                            } else {
                                $BornDate = "";
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
                            $Nationality = $Pax->item($i)->getElementsByTagName("Nationality");
                            if ($Nationality->length > 0) {
                                $Nationality = $Nationality->item(0)->nodeValue;
                            } else {
                                $Nationality = "";
                            }
                            $PhoneNumbers = $Pax->item($i)->getElementsByTagName("PhoneNumbers");
                            if ($PhoneNumbers->length > 0) {
                                $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                                if ($PhoneNumber->length > 0) {
                                    $Type = $PhoneNumber->item(0)->getAttribute("Type");
                                    $PhoneNumber = $PhoneNumber->item(0)->nodeValue;
                                } else {
                                    $PhoneNumber = "";
                                }
                            }
                            $Document = $Pax->item($i)->getElementsByTagName("Document");
                            if ($Document->length > 0) {
                                $DocumentType = $Document->item(0)->getAttribute("Type");
                                $DocumentNumber = $Document->item(0)->getAttribute("Number");
                                $DocumentDateExpired = $Document->item(0)->getElementsByTagName("DocumentDateExpired");
                                if ($DocumentDateExpired->length > 0) {
                                    $DocumentDateExpired = $DocumentDateExpired->item(0)->nodeValue;
                                } else {
                                    $DocumentDateExpired = "";
                                }
                                $DocumentNationality = $Document->item(0)->getElementsByTagName("DocumentNationality");
                                if ($DocumentNationality->length > 0) {
                                    $DocumentNationality = $DocumentNationality->item(0)->nodeValue;
                                } else {
                                    $DocumentNationality = "";
                                }
                            }
                        }
                    }
                }
                //Comments
                $Comments = $Reservation->item(0)->getElementsByTagName("Comments");
                if ($Comments->length > 0) {
                    $Comment = $Comments->item(0)->getElementsByTagName("Comment");
                    if ($Comment->length > 0) {
                        $CommentType = $Comment->item(0)->getAttribute("Type");
                        $Comment = $Comment->item(0)->nodeValue;
                    } else {
                        $Comment = "";
                        $CommentType = "";
                    }
                }
                //Items
                $Items = $Reservation->item(0)->getElementsByTagName("Items");
                if ($Items->length > 0) {
                    $ServiceItem = $Items->item(0)->getElementsByTagName("ServiceItem");
                    if ($ServiceItem->length > 0) {
                        $Status = $ServiceItem->item(0)->getAttribute("Status");
                        $End = $ServiceItem->item(0)->getAttribute("End");
                        $Start = $ServiceItem->item(0)->getAttribute("Start");
                        $Code = $ServiceItem->item(0)->getAttribute("Code");
                        $ItemId = $ServiceItem->item(0)->getAttribute("ItemId");
                        //Prices
                        $Prices = $ServiceItem->item(0)->getElementsByTagName("Prices");
                        if ($Prices->length > 0) {
                            $Price = $Prices->item(0)->getElementsByTagName("Price");
                            if ($Price->length > 0) {
                                $PriceType = $Price->item(0)->getAttribute("Type");
                                $PriceCurrency = $Price->item(0)->getAttribute("Currency");
                                $TotalFixAmounts = $Price->item(0)->getElementsByTagName("TotalFixAmounts");
                                if ($TotalFixAmounts->length > 0) {
                                    $Nett = $TotalFixAmounts->item(0)->getAttribute("Nett");
                                    $Gross = $TotalFixAmounts->item(0)->getAttribute("Gross");
                                    $Recommended = $TotalFixAmounts->item(0)->getAttribute("Recommended");
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
                        //CancellationPolicy
                        $CancellationPolicy = $ServiceItem->item(0)->getElementsByTagName("CancellationPolicy");
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
                                $Hour = "";
                                $FirstDayCostCancellation = "";
                            } 
                            $PolicyRules = $CancellationPolicy->item(0)->getElementsByTagName("PolicyRules");
                            if ($PolicyRules->length > 0) {
                                $Rule = $PolicyRules->item(0)->getElementsByTagName("Rule");
                                if ($Rule->length > 0) {
                                    for ($iAux=0; $iAux < $Rule->length; $iAux++) { 
                                        $ApplicationTypeNights = $Rule->item($iAux)->getAttribute("ApplicationTypeNights");
                                        $Nights = $Rule->item($iAux)->getAttribute("Nights");
                                        $PercentPrice = $Rule->item($iAux)->getAttribute("PercentPrice");
                                        $FixedPrice = $Rule->item($iAux)->getAttribute("FixedPrice");
                                        $Type = $Rule->item($iAux)->getAttribute("Type");
                                        $DateToHour = $Rule->item($iAux)->getAttribute("DateToHour");
                                        $DateTo = $Rule->item($iAux)->getAttribute("DateTo");
                                        $DateFromHour = $Rule->item($iAux)->getAttribute("DateFromHour");
                                        $DateFrom = $Rule->item($iAux)->getAttribute("DateFrom");
                                        $To = $Rule->item($iAux)->getAttribute("To");
                                        $From = $Rule->item($iAux)->getAttribute("From");                                      
                                    }
                                }
                            }
                        }

                        $ServiceInfo = $ServiceItem->item(0)->getElementsByTagName("ServiceInfo");
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
                        $ServiceOptions = $ServiceItem->item(0)->getElementsByTagName("ServiceOptions");
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
                                    $Description = $ServiceOption->item($iAux)->getElementsByTagName("Description");
                                    if ($Description->length > 0) {
                                        $Description = $Description->item(0)->nodeValue;
                                    } else {
                                        $Description = "";
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
                                    //ServiceBookingInfo
                                    $ServiceBookingInfo = $ServiceOption->item($iAux)->getElementsByTagName("ServiceBookingInfo");
                                    if ($ServiceBookingInfo->length > 0) {
                                        $MeetingPointInfo = $ServiceBookingInfo->item(0)->getElementsByTagName("MeetingPointInfo");
                                        if ($MeetingPointInfo->length > 0) {
                                            $MeetingTime = $MeetingPointInfo->item(0)->getAttribute("MeetingTime");
                                            $MEName = $MeetingPointInfo->item(0)->getElementsByTagName("Name");
                                            if ($MEName->length > 0) {
                                                $MEName = $MEName->item(0)->nodeValue;
                                            } else {
                                                $MEName = "";
                                            }
                                        }
                                        $HotelService = $ServiceBookingInfo->item(0)->getElementsByTagName("HotelService");
                                        if ($HotelService->length > 0) {
                                            $MECode = $HotelService->item(0)->getElementsByTagName("Code");
                                            if ($MECode->length > 0) {
                                                $MECode = $MECode->item(0)->nodeValue;
                                            } else {
                                                $MECode = "";
                                            }
                                            $MEName = $HotelService->item(0)->getElementsByTagName("Name");
                                            if ($HSName->length > 0) {
                                                $HSName = $HSName->item(0)->nodeValue;
                                            } else {
                                                $HSName = "";
                                            }
                                            $MEBlock = $HotelService->item(0)->getElementsByTagName("Block");
                                            if ($MEBlock->length > 0) {
                                                $MEBlock = $MEBlock->item(0)->nodeValue;
                                            } else {
                                                $MEBlock = "";
                                            }
                                            $MEBoard = $HotelService->item(0)->getElementsByTagName("Board");
                                            if ($MEBoard->length > 0) {
                                                $MEBoard = $MEBoard->item(0)->nodeValue;
                                            } else {
                                                $MEBoard = "";
                                            }
                                            $HSCheckoutDate = $HotelService->item(0)->getElementsByTagName("CheckoutDate");
                                            if ($HSCheckoutDate->length > 0) {
                                                $HSCheckoutDate = $HSCheckoutDate->item(0)->nodeValue;
                                            } else {
                                                $HSCheckoutDate = "";
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        //RelPaxes
                        $RelPaxes = $ServiceItem->item(0)->getElementsByTagName("RelPaxes");
                        if ($RelPaxes->length > 0) {
                            $RelPax = $Holder->item(0)->getElementsByTagName("RelPax");
                            if ($RelPax->length > 0) {
                                for ($j=0; $j < $RelPax->length; $j++) { 
                                    $IdPax = $RelPax->item($j)->getAttribute("IdPax");
                                }
                            }
                        }
                    }
                }
                //AgenciesData
                $AgenciesData = $Reservation->item(0)->getElementsByTagName("AgenciesData");
                if ($AgenciesData->length > 0) {
                    $AgencyData = $AgenciesData->item(0)->getElementsByTagName("AgencyData");
                    if ($AgencyData->length > 0) {
                        for ($j=0; $j < $AgencyData->length; $j++) { 
                            $ReferencedAgency = $AgencyData->item($j)->getElementsByTagName("ReferencedAgency");
                            if ($ReferencedAgency->length > 0) {
                                $ReferencedAgency = $ReferencedAgency->item(0)->nodeValue;
                            } else {
                                $ReferencedAgency = "";
                            }
                            $AgencyCode = $AgencyData->item($j)->getElementsByTagName("AgencyCode");
                            if ($AgencyCode->length > 0) {
                                $AgencyCode = $AgencyCode->item(0)->nodeValue;
                            } else {
                                $AgencyCode = "";
                            }
                            $AgencyName = $AgencyData->item($j)->getElementsByTagName("AgencyName");
                            if ($AgencyName->length > 0) {
                                $AgencyName = $AgencyName->item(0)->nodeValue;
                            } else {
                                $AgencyName = "";
                            }
                            $AgencyHandledBy = $AgencyData->item($j)->getElementsByTagName("AgencyHandledBy");
                            if ($AgencyHandledBy->length > 0) {
                                $AgencyHandledBy = $AgencyHandledBy->item(0)->nodeValue;
                            } else {
                                $AgencyHandledBy = "";
                            }
                            $AgencyEmail = $AgencyData->item($j)->getElementsByTagName("AgencyEmail");
                            if ($AgencyEmail->length > 0) {
                                $AgencyEmail = $AgencyEmail->item(0)->nodeValue;
                            } else {
                                $AgencyEmail = "";
                            }
                            $AgencyReference = $AgencyData->item($j)->getElementsByTagName("AgencyReference");
                            if ($AgencyReference->length > 0) {
                                $AgencyReference = $AgencyReference->item(0)->nodeValue;
                            } else {
                                $AgencyReference = "";
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
