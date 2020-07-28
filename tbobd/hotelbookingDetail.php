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
echo "COMECOU HOTELBOOKINGDETAIL<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$user = 'clubonehotelsTest';
$pass = 'Clu@28527768';

$option = "BookingId";
$booking_id = 139962;

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '"> </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/HotelBookingDetail</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:HotelBookingDetailRequest>';
    if ($option = "BookingId") {
        $raw = $raw . '<hot:BookingId>' . $booking_id . '</hot:BookingId>';
    } elseif($option = "ConfirmationNo") {
        $raw = $raw . '<hot:ConfirmationNo>Z7Y1MW</hot:ConfirmationNo>';
    } elseif($option = "ClientReferenceNumber") {
        $raw = $raw . '<hot:ClientReferenceNumber>210314135855789#gale</hot:ClientReferenceNumber>';
    }
    
 $raw = $raw . '</hot:HotelBookingDetailRequest>
</soap:Body>
</soap:Envelope>';

echo "<br/>REQUEST";
echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: ".strlen($raw)
));
$url =  "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

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
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$hotelFacil = '';
$Attr = '';

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$HotelBookingDetailResponse = $Body->item(0)->getElementsByTagName("HotelBookingDetailResponse");

$BookingDetail = $HotelBookingDetailResponse->item(0)->getElementsByTagName("BookingDetail");
if ($BookingDetail->length > 0) {
    $BookingId = $BookingDetail->item(0)->getAttribute("BookingId");
    $BookingStatus = $BookingDetail->item(0)->getAttribute("BookingStatus");
    $BookingRefNo = $BookingDetail->item(0)->getAttribute("BookingRefNo");
    $ConfirmationNo = $BookingDetail->item(0)->getAttribute("ConfirmationNo");
    $VoucherStatus = $BookingDetail->item(0)->getAttribute("VoucherStatus");

    $HotelName = $BookingDetail->item(0)->getElementsByTagName("HotelName");
    if ($HotelName->length > 0) {
        $HotelName = $HotelName->item(0)->nodeValue;
    } else {
        $HotelName = "";
    }
    $Rating = $BookingDetail->item(0)->getElementsByTagName("Rating");
    if ($Rating->length > 0) {
        $Rating = $Rating->item(0)->nodeValue;
    } else {
        $Rating = "";
    }
    $AddressLine1 = $BookingDetail->item(0)->getElementsByTagName("AddressLine1");
    if ($AddressLine1->length > 0) {
        $AddressLine1 = $AddressLine1->item(0)->nodeValue;
    } else {
        $AddressLine1 = "";
    }
    $AddressLine2 = $BookingDetail->item(0)->getElementsByTagName("AddressLine2");
    if ($AddressLine2->length > 0) {
        $AddressLine2 = $AddressLine2->item(0)->nodeValue;
    } else {
        $AddressLine2 = "";
    }
    $Map = $BookingDetail->item(0)->getElementsByTagName("Map");
    if ($Map->length > 0) {
        $Map = $Map->item(0)->nodeValue;
    } else {
        $Map = "";
    }
    $CityId = $BookingDetail->item(0)->getElementsByTagName("CityId");
    if ($CityId->length > 0) {
        $CityId = $CityId->item(0)->nodeValue;
    } else {
        $CityId = "";
    }
    $City = $BookingDetail->item(0)->getElementsByTagName("City");
    if ($City->length > 0) {
        $City = $City->item(0)->nodeValue;
    } else {
        $City = "";
    }
    $CheckInDate = $BookingDetail->item(0)->getElementsByTagName("CheckInDate");
    if ($CheckInDate->length > 0) {
        $CheckInDate = $CheckInDate->item(0)->nodeValue;
    } else {
        $CheckInDate = "";
    }
    $CheckOutDate = $BookingDetail->item(0)->getElementsByTagName("CheckOutDate");
    if ($CheckOutDate->length > 0) {
        $CheckOutDate = $CheckOutDate->item(0)->nodeValue;
    } else {
        $CheckOutDate = "";
    }
    $BookingDate = $BookingDetail->item(0)->getElementsByTagName("BookingDate");
    if ($BookingDate->length > 0) {
        $BookingDate = $BookingDate->item(0)->nodeValue;
    } else {
        $BookingDate = "";
    }
    $HotelPolicyDetails = $BookingDetail->item(0)->getElementsByTagName("HotelPolicyDetails");
    if ($HotelPolicyDetails->length > 0) {
        $HotelPolicyDetails = $HotelPolicyDetails->item(0)->nodeValue;
    } else {
        $HotelPolicyDetails = "";
    }
    $SpecialRequest = $BookingDetail->item(0)->getElementsByTagName("SpecialRequest");
    if ($SpecialRequest->length > 0) {
        $SpecialRequest = $SpecialRequest->item(0)->nodeValue;
    } else {
        $SpecialRequest = "";
    }
    $Currency = $BookingDetail->item(0)->getElementsByTagName("Currency");
    if ($Currency->length > 0) {
        $Currency = $Currency->item(0)->nodeValue;
    } else {
        $Currency = "";
    }
    $NoOfRooms = $BookingDetail->item(0)->getElementsByTagName("NoOfRooms");
    if ($NoOfRooms->length > 0) {
        $NoOfRooms = $NoOfRooms->item(0)->nodeValue;
    } else {
        $NoOfRooms = "";
    }

    $HotelCancelPolicies = $BookingDetail->item(0)->getElementsByTagName("HotelCancelPolicies");
    if ($HotelCancelPolicies->length > 0) {
        $LastCancellationDeadline = $HotelCancelPolicies->item(0)->getElementsByTagName("LastCancellationDeadline");
        if ($LastCancellationDeadline->length > 0) {
            $LastCancellationDeadline = $LastCancellationDeadline->item(0)->nodeValue;
        } else {
            $LastCancellationDeadline = "";
        }
        $DefaultPolicy = $HotelCancelPolicies->item(0)->getElementsByTagName("DefaultPolicy");
        if ($DefaultPolicy->length > 0) {
            $DefaultPolicy = $DefaultPolicy->item(0)->nodeValue;
        } else {
            $DefaultPolicy = "";
        }
        $AutoCancellationText = $HotelCancelPolicies->item(0)->getElementsByTagName("AutoCancellationText");
        if ($AutoCancellationText->length > 0) {
            $AutoCancellationText = $AutoCancellationText->item(0)->nodeValue;
        } else {
            $AutoCancellationText = "";
        }

        $CancelPolicy = $HotelCancelPolicies->item(0)->getElementsByTagName("CancelPolicy");
        if ($CancelPolicy->length > 0) {
            $RoomIndex = $CancelPolicy->item(0)->getAttribute("RoomIndex");
            $RoomTypeName = $CancelPolicy->item(0)->getAttribute("RoomTypeName");
            $FromDate = $CancelPolicy->item(0)->getAttribute("FromDate");
            $ToDate = $CancelPolicy->item(0)->getAttribute("ToDate");
            $ChargeType = $CancelPolicy->item(0)->getAttribute("ChargeType");
            $CancellationCharge = $CancelPolicy->item(0)->getAttribute("CancellationCharge");
            $CurrencyCancelPolicy = $CancelPolicy->item(0)->getAttribute("Currency");
        }
    }

    $Roomtype = $BookingDetail->item(0)->getElementsByTagName("Roomtype");
    if ($Roomtype->length > 0) {
        $RoomDetails = $Roomtype->item(0)->getElementsByTagName("RoomDetails");
        if ($RoomDetails->length > 0) {
            $RoomName = $RoomDetails->item(0)->getElementsByTagName("RoomName");
            if ($RoomName->length > 0) {
                $RoomName = $RoomName->item(0)->nodeValue;
            } else {
                $RoomName = "";
            }
            $AdultCount = $RoomDetails->item(0)->getElementsByTagName("AdultCount");
            if ($AdultCount->length > 0) {
                $AdultCount = $AdultCount->item(0)->nodeValue;
            } else {
                $AdultCount = "";
            }
            $ChildCount = $RoomDetails->item(0)->getElementsByTagName("ChildCount");
            if ($ChildCount->length > 0) {
                $ChildCount = $ChildCount->item(0)->nodeValue;
            } else {
                $ChildCount = "";
            }
            $Ameneties = $RoomDetails->item(0)->getElementsByTagName("Ameneties");
            if ($Ameneties->length > 0) {
                $Ameneties = $Ameneties->item(0)->nodeValue;
            } else {
                $Ameneties = "";
            }

            $GuestInfo = $RoomDetails->item(0)->getElementsByTagName("GuestInfo");
            if ($GuestInfo->length > 0) {
                $Guest = $GuestInfo->item(0)->getElementsByTagName("Guest");
                if ($Guest->length > 0) {
                    $GuestInRoom = $Guest->item(0)->getAttribute("GuestInRoom");
                    $GuestType = $Guest->item(0)->getAttribute("GuestType");
                    $LeadGuest = $Guest->item(0)->getAttribute("LeadGuest");

                    $Title = $Guest->item(0)->getElementsByTagName("Title");
                    if ($Title->length > 0) {
                        $Title = $Title->item(0)->nodeValue;
                    } else {
                        $Title = "";
                    }
                    $FirstName = $Guest->item(0)->getElementsByTagName("FirstName");
                    if ($FirstName->length > 0) {
                        $FirstName = $FirstName->item(0)->nodeValue;
                    } else {
                        $FirstName = "";
                    }
                    $LastName = $Guest->item(0)->getElementsByTagName("LastName");
                    if ($LastName->length > 0) {
                        $LastName = $LastName->item(0)->nodeValue;
                    } else {
                        $LastName = "";
                    }
                    $Age = $Guest->item(0)->getElementsByTagName("Age");
                    if ($Age->length > 0) {
                        $Age = $Age->item(0)->nodeValue;
                    } else {
                        $Age = "";
                    }
                } else {
                    $GuestInRoom = "";
                    $GuestGuestTypeInRoom = "";
                    $LeadGuest = "";
                }
            }

            $RoomRate = $RoomDetails->item(0)->getElementsByTagName("RoomRate");
            if ($RoomRate->length > 0) {
                $TotalFare = $RoomRate->item(0)->getAttribute("TotalFare");
                $RoomTax = $RoomRate->item(0)->getAttribute("RoomTax");
                $AgentMarkUp = $RoomRate->item(0)->getAttribute("AgentMarkUp");
                $RoomFare = $RoomRate->item(0)->getAttribute("RoomFare");

                $ExtraGuestCharges = $RoomRate->item(0)->getElementsByTagName("ExtraGuestCharges");
                if ($ExtraGuestCharges->length > 0) {
                    $ExtraGuestCharges = $ExtraGuestCharges->item(0)->nodeValue;
                } else {
                    $ExtraGuestCharges = "";
                }
                $ChildCharges = $RoomRate->item(0)->getElementsByTagName("ChildCharges");
                if ($ChildCharges->length > 0) {
                    $ChildCharges = $ChildCharges->item(0)->nodeValue;
                } else {
                    $ChildCharges = "";
                }
                $Discount = $RoomRate->item(0)->getElementsByTagName("Discount");
                if ($Discount->length > 0) {
                    $Discount = $Discount->item(0)->nodeValue;
                } else {
                    $Discount = "";
                }
                $OtherCharges = $RoomRate->item(0)->getElementsByTagName("OtherCharges");
                if ($OtherCharges->length > 0) {
                    $OtherCharges = $OtherCharges->item(0)->nodeValue;
                } else {
                    $OtherCharges = "";
                }
                $ServiceTax = $RoomRate->item(0)->getElementsByTagName("ServiceTax");
                if ($ServiceTax->length > 0) {
                    $ServiceTax = $ServiceTax->item(0)->nodeValue;
                } else {
                    $ServiceTax = "";
                }
            } 
        }
    }

    $AgencyDetails = $BookingDetail->item(0)->getElementsByTagName("AgencyDetails");
    if ($AgencyDetails->length > 0) {
        $NameAgency = $AgencyDetails->item(0)->getElementsByTagName("Name");
        if ($NameAgency->length > 0) {
            $NameAgency = $NameAgency->item(0)->nodeValue;
        } else {
            $NameAgency = "";
        }
        $AddressLine1Agency = $AgencyDetails->item(0)->getElementsByTagName("AddressLine1");
        if ($AddressLine1Agency->length > 0) {
            $AddressLine1Agency = $AddressLine1Agency->item(0)->nodeValue;
        } else {
            $AddressLine1Agency = "";
        }
        $AddressLine2Agency = $AgencyDetails->item(0)->getElementsByTagName("AddressLine2");
        if ($AddressLine2Agency->length > 0) {
            $AddressLine2Agency = $AddressLine2Agency->item(0)->nodeValue;
        } else {
            $AddressLine2Agency = "";
        }
        $PhoneAgency = $AgencyDetails->item(0)->getElementsByTagName("Phone");
        if ($PhoneAgency->length > 0) {
            $PhoneAgency = $PhoneAgency->item(0)->nodeValue;
        } else {
            $PhoneAgency = "";
        }
        $FaxAgency = $AgencyDetails->item(0)->getElementsByTagName("Fax");
        if ($FaxAgency->length > 0) {
            $FaxAgency = $FaxAgency->item(0)->nodeValue;
        } else {
            $FaxAgency = "";
        }
        $CityAgency = $AgencyDetails->item(0)->getElementsByTagName("City");
        if ($CityAgency->length > 0) {
            $CityAgency = $CityAgency->item(0)->nodeValue;
        } else {
            $CityAgency = "";
        }
        $PINAgency = $AgencyDetails->item(0)->getElementsByTagName("PIN");
        if ($PINAgency->length > 0) {
            $PINAgency = $PINAgency->item(0)->nodeValue;
        } else {
            $PINAgency = "";
        }
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('BookingDetail');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'BookingId' => $BookingId,
            'BookingStatus' => $BookingStatus,
            'BookingRefNo' => $BookingRefNo,
            'ConfirmationNo' => $ConfirmationNo,
            'VoucherStatus' => $VoucherStatus,
            'HotelName' => $HotelName,
            'Rating' => $Rating,
            'AddressLine1' => $AddressLine1,
            'AddressLine2' => $AddressLine2,
            'Map' => $Map,
            'CityId' => $CityId,
            'City' => $City,
            'CheckInDate' => $CheckInDate,
            'CheckOutDate' => $CheckOutDate,
            'BookingDate' => $BookingDate,
            'HotelPolicyDetails' => $HotelPolicyDetails,
            'SpecialRequest' => $SpecialRequest,
            'Currency' => $Currency,
            'NoOfRooms' => $NoOfRooms,
            'LastCancellationDeadline' => $LastCancellationDeadline,
            'DefaultPolicy' => $DefaultPolicy,
            'AutoCancellationText' => $AutoCancellationText,
            'CurrencyCancelPolicy' => $CurrencyCancelPolicy,
            'CancellationCharge' => $CancellationCharge,
            'ChargeType' => $ChargeType,
            'ToDate' => $ToDate,
            'FromDate' => $FromDate,
            'RoomIndex' => $RoomIndex,
            'RoomTypeName' => $RoomTypeName,
            'RoomName' => $RoomName,
            'AdultCount' => $AdultCount,
            'ChildCount' => $ChildCount,
            'Ameneties' => $Ameneties,
            'GuestInRoom' => $GuestInRoom,
            'GuestType' => $GuestType,
            'LeadGuest' => $LeadGuest,
            'Title' => $Title,
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'Age' => $Age,
            'TotalFare' => $TotalFare,
            'RoomTax' => $RoomTax,
            'AgentMarkUp' => $AgentMarkUp,
            'RoomFare' => $RoomFare,
            'ExtraGuestCharges' => $ExtraGuestCharges,
            'ChildCharges' => $ChildCharges,
            'Discount' => $Discount,
            'OtherCharges' => $OtherCharges,
            'ServiceTax' => $ServiceTax,
            'NameAgency' => $NameAgency,
            'AddressLine1Agency' => $AddressLine1Agency,
            'AddressLine2Agency' => $AddressLine2Agency,
            'PhoneAgency' => $PhoneAgency,
            'FaxAgency' => $FaxAgency,
            'CityAgency' => $CityAgency,
            'PINAgency' => $PINAgency
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
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>