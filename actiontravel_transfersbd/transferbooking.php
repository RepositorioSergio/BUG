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
echo "COMECOU BOOKING<br/>";
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

$url = 'http://xml-uat.bookingengine.es/webservice/JP/Operations/BookTransactions.asmx';

$email = 'paulo@corp.bug-software.com';
$password = 'xA2d@a1X';
$bookingcode = '/nV50gUcfbZueTEQ6G+mlLHe789HfjGBrvy80C+CoZqENuyY5wDRR/9dQxAViyROF9y68EKwK2UhDGwLAcg/ZFr4r+/oJGfqJRaAh7sTkbNuorx1d/g/t/SG5b15wN67PXSogT2hLNIQrCeCnZV3onmWHwsJL8LgeV1JFwZ6iGALhd5U2CpUTKKfDuzyXizsYF7if55umIu2/p3IHVpq5RFviFotICCxKiu4KyL5OepnWFsMnTLsNnXdIgr7mDSBaFP6k1obsVDeyXFMPxDBT1DCtkl6LqXKRnrep+zUpIISxnmXow87I+Voj08tcVnk';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
   <TransferBooking xmlns="http://www.juniper.es/webservice/2007/">
      <TransferBookingRQ Version="1.1" Language="en">
         <Login Password="' . $password . '" Email="' . $email . '"/>
         <Paxes>
            <Pax IdPax="1">
                <Name>Josue</Name>
                <Surname>García Perez</Surname>
                <Email>josue@mydomain.com</Email>
                <PhoneNumbers>
                    <PhoneNumber Type="GEN">+34 617884410</PhoneNumber>
                </PhoneNumbers>
                <BornDate>1977-05-18</BornDate>
                <Age>42</Age>
                <Document Type="DNI">24516757Y</Document>
                <Address>Calle Aragón 232</Address>
                <City>Palma de Mallorca</City>
                <Country>Spain</Country>
                <PostalCode>00000</PostalCode>
                <Nationality>ES</Nationality>
            </Pax>
            <Pax IdPax="2">
                <Name>Lurdes</Name>
                <Surname>Martínez López</Surname>
                <Email>lurdes@mydomain.com</Email>
                <PhoneNumbers>
                    <PhoneNumber Type="GEN">+34 615007414</PhoneNumber>
                </PhoneNumbers>
                <BornDate>1979-09-18</BornDate>
                <Age>39</Age>
                <Document Type="DNI">24519759Z</Document>
                <Nationality>ES</Nationality>
            </Pax>
         </Paxes>
         <Holder>
            <RelPax IdPax="1"/>
         </Holder>
         <Elements>
            <TransferElement>
               <BookingCode>' . $bookingcode . '</BookingCode>
                <RelPaxesDist>
                    <RelPaxDist>
                        <RelPaxes>
                            <RelPax IdPax="1"/>
                            <RelPax IdPax="2"/>
                        </RelPaxes>
                    </RelPaxDist>
                </RelPaxesDist>
               <TransfersBookingInfo>
                  <TransferBookingInfo>
                     <Origin Type="ALO">
                        <MeetingPointInfo Code="000" MeetingTime="2020-04-01T09:55:28.8018173+02:00">
                           <Name>MEETING POINT NAME</Name>
                           <Address></Address>
                           <PostalCode></PostalCode>
                        </MeetingPointInfo>
                     </Origin>
                     <Destination Type="ALO">
                     </Destination>
                  </TransferBookingInfo>
               </TransfersBookingInfo>
            </TransferElement>
         </Elements>
         <AdvancedOptions>
            <UseCurrency>USD</UseCurrency>
            <ShowBreakdownPrice>true</ShowBreakdownPrice>
         </AdvancedOptions>
      </TransferBookingRQ>
   </TransferBooking>
</soapenv:Body>
</soapenv:Envelope>';
echo "<xmp>";
var_dump($raw);
echo "</xmp>"; 

$headers = array(
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "SOAPAction: http://www.juniper.es/webservice/2007/TransferBooking",
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
$TransferBookingResponse = $Body->item(0)->getElementsByTagName("TransferBookingResponse");
if ($TransferBookingResponse->length > 0) {
    $TransferBookingRS = $TransferBookingResponse->item(0)->getElementsByTagName("TransferBookingRS");
    if ($TransferBookingRS->length > 0) {
        $IntCode = $TransferBookingRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $TransferBookingRS->item(0)->getAttribute("TimeStamp");
        $Url = $TransferBookingRS->item(0)->getAttribute("Url");
        $Reservations = $TransferBookingRS->item(0)->getElementsByTagName("Reservations");
        if ($Reservations->length > 0) {
            $Reservation = $Reservations->item(0)->getElementsByTagName("Reservation");
            if ($Reservation->length > 0) {
                $Status = $Reservation->item(0)->getAttribute("Status");
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
                        for ($i=0; $i < $RelPax->length; $i++) { 
                            $IdPax = $RelPax->item($i)->getAttribute("IdPax");
                        }
                    }
                }
                $Paxes = $Reservation->item(0)->getElementsByTagName("Paxes");
                if ($Paxes->length > 0) {
                    $Pax = $Paxes->item(0)->getElementsByTagName("Pax");
                    if ($Pax->length > 0) {
                        for ($j=0; $j < $Pax->length; $j++) { 
                            $IdPax = $Pax->item($j)->getAttribute("IdPax");
                            $Name = $Pax->item($j)->getElementsByTagName("Name");
                            if ($Name->length > 0) {
                                $Name = $Name->item(0)->nodeValue;
                            } else {
                                $Name = "";
                            }
                            $Surname = $Pax->item($j)->getElementsByTagName("Surname");
                            if ($Surname->length > 0) {
                                $Surname = $Surname->item(0)->nodeValue;
                            } else {
                                $Surname = "";
                            }
                            $Email = $Pax->item($j)->getElementsByTagName("Email");
                            if ($Email->length > 0) {
                                $Email = $Email->item(0)->nodeValue;
                            } else {
                                $Email = "";
                            }
                            $BornDate = $Pax->item($j)->getElementsByTagName("BornDate");
                            if ($BornDate->length > 0) {
                                $BornDate = $BornDate->item(0)->nodeValue;
                            } else {
                                $BornDate = "";
                            }
                            $Age = $Pax->item($j)->getElementsByTagName("Age");
                            if ($Age->length > 0) {
                                $Age = $Age->item(0)->nodeValue;
                            } else {
                                $Age = "";
                            }
                            $Address = $Pax->item($j)->getElementsByTagName("Address");
                            if ($Address->length > 0) {
                                $Address = $Address->item(0)->nodeValue;
                            } else {
                                $Address = "";
                            }
                            $City = $Pax->item($j)->getElementsByTagName("City");
                            if ($City->length > 0) {
                                $City = $City->item(0)->nodeValue;
                            } else {
                                $City = "";
                            }
                            $Country = $Pax->item($j)->getElementsByTagName("Country");
                            if ($Country->length > 0) {
                                $Country = $Country->item(0)->nodeValue;
                            } else {
                                $Country = "";
                            }
                            $PostalCode = $Pax->item($j)->getElementsByTagName("PostalCode");
                            if ($PostalCode->length > 0) {
                                $PostalCode = $PostalCode->item(0)->nodeValue;
                            } else {
                                $PostalCode = "";
                            }
                            $Nationality = $Pax->item($j)->getElementsByTagName("Nationality");
                            if ($Nationality->length > 0) {
                                $Nationality = $Nationality->item(0)->nodeValue;
                            } else {
                                $Nationality = "";
                            }
                            $PhoneNumbers = $Pax->item($j)->getElementsByTagName("PhoneNumbers");
                            if ($PhoneNumbers->length > 0) {
                                $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                                if ($PhoneNumber->length > 0) {
                                    $PhoneNumberType = $PhoneNumber->item(0)->getAttribute("Type");
                                    $PhoneNumber = $PhoneNumber->item(0)->nodeValue;
                                } else {
                                    $PhoneNumber = "";
                                }
                            }
                            $Document = $Pax->item($j)->getElementsByTagName("Document");
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
                $Comments = $Reservation->item(0)->getElementsByTagName("Comments");
                if ($Comments->length > 0) {
                    $Comment = $Comments->item(0)->getElementsByTagName("Comment");
                    if ($Comment->length > 0) {
                        $CommentType = $Comment->item(0)->getAttribute("Type");
                        $Comment = $Comment->item(0)->nodeValue;
                    } else {
                        $Comment = "";
                    }
                }
                $Items = $Reservation->item(0)->getElementsByTagName("Items");
                if ($Items->length > 0) {
                    $TransferItem = $Items->item(0)->getElementsByTagName("TransferItem");
                    if ($TransferItem->length > 0) {
                        $Code = $TransferItem->item(0)->getAttribute("Code");
                        $Status = $TransferItem->item(0)->getAttribute("Status");
                        $End = $TransferItem->item(0)->getAttribute("End");
                        $Start = $TransferItem->item(0)->getAttribute("Start");
                        $ItemId = $TransferItem->item(0)->getAttribute("ItemId");
                        $Prices = $TransferItem->item(0)->getElementsByTagName("Prices");
                        if ($Prices->length > 0) {
                            $Price = $Prices->item(0)->getElementsByTagName("Price");
                            if ($Price->length > 0) {
                                $PriceType = $Price->item(0)->getAttribute("Type");
                                $Currency = $Price->item(0)->getAttribute("Currency");
                                $TotalFixAmounts = $Price->item(0)->getElementsByTagName("TotalFixAmounts");
                                if ($TotalFixAmounts->length > 0) {
                                    $Nett = $TotalFixAmounts->item(0)->getAttribute("Nett");
                                    $Gross = $TotalFixAmounts->item(0)->getAttribute("Gross");
                                    $Recommended = $TotalFixAmounts->item(0)->getAttribute("Recommended");
                                    $Service = $TotalFixAmounts->item(0)->getElementsByTagName("Service");
                                    if ($Service->length > 0) {
                                        $ServiceAmount = $Service->item(0)->getAttribute("Amount");
                                    }
                                    $ServiceTaxes = $TotalFixAmounts->item(0)->getElementsByTagName("ServiceTaxes");
                                    if ($ServiceTaxes->length > 0) {
                                        $ServiceTaxesAmount = $ServiceTaxes->item(0)->getAttribute("Amount");
                                        $ServiceTaxesIncluded = $ServiceTaxes->item(0)->getAttribute("Included");
                                    }
                                    $Commissions = $TotalFixAmounts->item(0)->getElementsByTagName("Commissions");
                                    if ($Commissions->length > 0) {
                                        $CommissionsAmount = $Commissions->item(0)->getAttribute("Amount");
                                        $CommissionsIncluded = $Commissions->item(0)->getAttribute("Included");
                                    }
                                    $HandlingFees = $TotalFixAmounts->item(0)->getElementsByTagName("HandlingFees");
                                    if ($HandlingFees->length > 0) {
                                        $HandlingFeesAmount = $HandlingFees->item(0)->getAttribute("Amount");
                                        $HandlingFeesIncluded = $HandlingFees->item(0)->getAttribute("Included");
                                    }
                                }
                            }
                        }
                        $CancellationPolicies = $TransferItem->item(0)->getElementsByTagName("CancellationPolicies");
                        if ($CancellationPolicies->length > 0) {
                            $CancellationPolicy = $CancellationPolicies->item(0)->getElementsByTagName("CancellationPolicy");
                            if ($CancellationPolicy->length > 0) {
                                $CPCode = $CancellationPolicy->item(0)->getAttribute("Code");
                                $CPOnlyEarlyBooking = $CancellationPolicy->item(0)->getAttribute("OnlyEarlyBooking");
                                $CPPriority = $CancellationPolicy->item(0)->getAttribute("Priority");
                                $Name = $CancellationPolicy->item(0)->getElementsByTagName("Name");
                                if ($Name->length > 0) {
                                    $Name = $Name->item(0)->nodeValue;
                                } else {
                                    $Name = "";
                                }
                                $Description = $CancellationPolicy->item(0)->getElementsByTagName("Description");
                                if ($Description->length > 0) {
                                    $Description = $Description->item(0)->nodeValue;
                                } else {
                                    $Description = "";
                                }
                                $BookingDate = $CancellationPolicy->item(0)->getElementsByTagName("BookingDate");
                                if ($BookingDate->length > 0) {
                                    $BookingDate = $BookingDate->item(0)->nodeValue;
                                } else {
                                    $BookingDate = "";
                                }
                                $Rules = $CancellationPolicy->item(0)->getElementsByTagName("Rules");
                                if ($Rules->length > 0) {
                                    $Rule = $Rules->item(0)->getElementsByTagName("Rule");
                                    if ($Rule->length > 0) {
                                        for ($iAux=0; $iAux < $Rule->length; $iAux++) { 
                                            $ApplicationTypeNights = $Rule->item($iAux)->getAttribute("ApplicationTypeNights");
                                            $Type = $Rule->item($iAux)->getAttribute("Type");
                                            $Fixed = $Rule->item($iAux)->getAttribute("Fixed");
                                            $To = $Rule->item($iAux)->getAttribute("To");
                                            $From = $Rule->item($iAux)->getAttribute("From");                                      
                                        }
                                    }
                                }
                            }
                        }
                        $TransferBookingInfo = $TransferItem->item(0)->getElementsByTagName("TransferBookingInfo");
                        if ($TransferBookingInfo->length > 0) {
                            $Origin = $TransferBookingInfo->item(0)->getElementsByTagName("Origin");
                            if ($Origin->length > 0) {
                                $OriginCode = $Origin->item(0)->getAttribute("Code");
                                $OriginType = $Origin->item(0)->getAttribute("Type");
                                $MeetingPointInfo = $Origin->item(0)->getElementsByTagName("MeetingPointInfo");
                                if ($MeetingPointInfo->length > 0) {
                                    $MeetingPointInfoName = $MeetingPointInfo->item(0)->getElementsByTagName("Name");
                                    if ($MeetingPointInfoName->length > 0) {
                                        $MeetingPointInfoName = $MeetingPointInfoName->item(0)->nodeValue;
                                    } else {
                                        $MeetingPointInfoName = "";
                                    }
                                    $Code = $MeetingPointInfo->item(0)->getAttribute("Code");
                                    $MeetingTime = $MeetingPointInfo->item(0)->getAttribute("MeetingTime");
                                }
                                $FlightInfo = $Origin->item(0)->getElementsByTagName("FlightInfo");
                                if ($FlightInfo->length > 0) {
                                    $FlightNumber = $FlightInfo->item(0)->getAttribute("FlightNumber");
                                    $FlightTime = $FlightInfo->item(0)->getAttribute("FlightTime");
                                    $Airport = $FlightInfo->item(0)->getAttribute("Airport");
                                }
                            }
                            $Destination = $TransferBookingInfo->item(0)->getElementsByTagName("Destination");
                            if ($Destination->length > 0) {
                                $DestinationType = $Destination->item(0)->getAttribute("Type");
                                $DestinationCode = $Destination->item(0)->getAttribute("Code");
                                $MeetingPointInfo = $Destination->item(0)->getElementsByTagName("MeetingPointInfo");
                                if ($MeetingPointInfo->length > 0) {
                                    $MeetingPointInfoName = $MeetingPointInfo->item(0)->getElementsByTagName("Name");
                                    if ($MeetingPointInfoName->length > 0) {
                                        $MeetingPointInfoName = $MeetingPointInfoName->item(0)->nodeValue;
                                    } else {
                                        $MeetingPointInfoName = "";
                                    }
                                    $Code = $MeetingPointInfo->item(0)->getAttribute("Code");
                                    $MeetingTime = $MeetingPointInfo->item(0)->getAttribute("MeetingTime");
                                }
                                $HotelService = $Destination->item(0)->getElementsByTagName("HotelService");
                                if ($HotelService->length > 0) {
                                    $HotelServiceName = $HotelService->item(0)->getElementsByTagName("Name");
                                    if ($HotelServiceName->length > 0) {
                                        $HotelServiceName = $HotelServiceName->item(0)->nodeValue;
                                    } else {
                                        $HotelServiceName = "";
                                    }
                                    $HotelServiceCode = $HotelService->item(0)->getElementsByTagName("Code");
                                    if ($HotelServiceCode->length > 0) {
                                        $HotelServiceCode = $HotelServiceCode->item(0)->nodeValue;
                                    } else {
                                        $HotelServiceCode = "";
                                    }
                                    $HotelServiceBlock = $HotelService->item(0)->getElementsByTagName("Block");
                                    if ($HotelServiceBlock->length > 0) {
                                        $HotelServiceBlock = $HotelServiceBlock->item(0)->nodeValue;
                                    } else {
                                        $HotelServiceBlock = "";
                                    }
                                    $HotelServiceBoard = $HotelService->item(0)->getElementsByTagName("Board");
                                    if ($HotelServiceBoard->length > 0) {
                                        $HotelServiceBoard = $HotelServiceBoard->item(0)->nodeValue;
                                    } else {
                                        $HotelServiceBoard = "";
                                    }
                                    $HotelServiceCheckoutDate = $HotelService->item(0)->getElementsByTagName("CheckoutDate");
                                    if ($HotelServiceCheckoutDate->length > 0) {
                                        $HotelServiceCheckoutDate = $HotelServiceCheckoutDate->item(0)->nodeValue;
                                    } else {
                                        $HotelServiceCheckoutDate = "";
                                    }
                                    $Room = $HotelService->item(0)->getElementsByTagName("Room");
                                    if ($Room->length > 0) {
                                        $RoomName = $Room->item(0)->getElementsByTagName("Name");
                                        if ($RoomName->length > 0) {
                                            $RoomName = $RoomName->item(0)->nodeValue;
                                        } else {
                                            $RoomName = "";
                                        }
                                        $RoomCode = $Room->item(0)->getElementsByTagName("Code");
                                        if ($RoomCode->length > 0) {
                                            $RoomCode = $RoomCode->item(0)->nodeValue;
                                        } else {
                                            $RoomCode = "";
                                        }
                                        $RoomNumber = $Room->item(0)->getElementsByTagName("Number");
                                        if ($RoomNumber->length > 0) {
                                            $RoomNumber = $RoomNumber->item(0)->nodeValue;
                                        } else {
                                            $RoomNumber = "";
                                        }
                                    }
                                }
                            }
                        }
                        $RelPaxes = $TransferItem->item(0)->getElementsByTagName("RelPaxes");
                        if ($RelPaxes->length > 0) {
                            $RelPax = $RelPaxes->item(0)->getElementsByTagName("RelPax");
                            if ($RelPax->length > 0) {
                                for ($iAux2=0; $iAux2 < $RelPax->length; $iAux2++) { 
                                    $IdPax = $RelPax->item($iAux2)->getAttribute("IdPax");
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