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
echo "COMECOU AVAILABLE<br/>";
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

$user = 'wingstest';
$pass = 'Win@59491374';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
<hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
</hot:Credentials>
<wsa:Action>http://TekTravel/HotelBookingApi/AvailabilityAndPricing</wsa:Action>
<wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:AvailabilityAndPricingRequest>
        <hot:ResultIndex>4</hot:ResultIndex>
        <hot:HotelCode></hot:HotelCode>
        <hot:SessionId>9616db93-3fdf-40e4-9118-63441e133ccd</hot:SessionId>
        <hot:OptionsForBooking>
            <hot:FixedFormat>true</hot:FixedFormat>
            <hot:RoomCombination>
                <hot:RoomIndex>1</hot:RoomIndex>
                <hot:RoomIndex>2</hot:RoomIndex>
            </hot:RoomCombination>
        </hot:OptionsForBooking>
    </hot:AvailabilityAndPricingRequest>
</soap:Body>
</soap:Envelope>';

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
$AvailabilityAndPricingResponse = $Body->item(0)->getElementsByTagName("AvailabilityAndPricingResponse");

$ResultIndex = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("ResultIndex");
if ($ResultIndex->length > 0) {
    $ResultIndex = $ResultIndex->item(0)->nodeValue;
} else {
    $ResultIndex = "";
}
$AvailableForBook = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("AvailableForBook");
if ($AvailableForBook->length > 0) {
    $AvailableForBook = $AvailableForBook->item(0)->nodeValue;
} else {
    $AvailableForBook = "";
}
$AvailableForConfirmBook = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("AvailableForConfirmBook");
if ($AvailableForConfirmBook->length > 0) {
    $AvailableForConfirmBook = $AvailableForConfirmBook->item(0)->nodeValue;
} else {
    $AvailableForConfirmBook = "";
}
$CancellationPoliciesAvailable = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("CancellationPoliciesAvailable");
if ($CancellationPoliciesAvailable->length > 0) {
    $CancellationPoliciesAvailable = $CancellationPoliciesAvailable->item(0)->nodeValue;
} else {
    $CancellationPoliciesAvailable = "";
}
$AccountInfo = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("AccountInfo");
if ($AccountInfo->length > 0) {
    $AgencyBlocked = $AccountInfo->item(0)->getAttribute("AgencyBlocked");
    $AgencyBalance = $AccountInfo->item(0)->getAttribute("AgencyBalance");
} else {
    $AgencyBlocked = "";
    $AgencyBalance = "";
}
$HotelDetailsVerification = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("HotelDetailsVerification");
if ($HotelDetailsVerification->length > 0) {
    $Status = $HotelDetailsVerification->item(0)->getAttribute("Status");
    $Remarks = $HotelDetailsVerification->item(0)->getAttribute("Remarks");
} else {
    $Status = "";
    $Remarks = "";
}
$HotelDetails = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("HotelDetails");
if ($HotelDetails->length > 0) {
    $HotelName = $HotelDetails->item(0)->getAttribute("HotelName");
    $HotelRating = $HotelDetails->item(0)->getAttribute("HotelRating");
    $Address = $HotelDetails->item(0)->getElementsByTagName("Address");
    if ($Address->length > 0) {
        $Address = $Address->item(0)->nodeValue;
    } else {
        $Address = "";
    }
    $FaxNumber = $HotelDetails->item(0)->getElementsByTagName("FaxNumber");
    if ($FaxNumber->length > 0) {
        $FaxNumber = $FaxNumber->item(0)->nodeValue;
    } else {
        $FaxNumber = "";
    }
    $Map = $HotelDetails->item(0)->getElementsByTagName("Map");
    if ($Map->length > 0) {
        $Map = $Map->item(0)->nodeValue;
    } else {
        $Map = "";
    }
    $PhoneNumber = $HotelDetails->item(0)->getElementsByTagName("PhoneNumber");
    if ($PhoneNumber->length > 0) {
        $PhoneNumber = $PhoneNumber->item(0)->nodeValue;
    } else {
        $PhoneNumber = "";
    }
} else {
    $HotelName = "";
    $HotelRating = "";
}

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('AvailabilityAndPricing');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'ResultIndex' => $ResultIndex,
        'AvailableForBook' => $AvailableForBook,
        'AvailableForConfirmBook' => $AvailableForConfirmBook,
        'CancellationPoliciesAvailable' => $CancellationPoliciesAvailable,
        'AgencyBlocked' => $AgencyBlocked,
        'AgencyBalance' => $AgencyBalance,
        'Status' => $Status,
        'Remarks' => $Remarks,
        'HotelName' => $HotelName,
        'HotelRating' => $HotelRating,
        'Address' => $Address,
        'FaxNumber' => $FaxNumber,
        'Map' => $Map,
        'PhoneNumber' => $PhoneNumber
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
    ->getConnection()
    ->disconnect();

} catch (\Exception $e) {
    echo $return;
    echo "ERRO 1: " . $e;
    echo $return;
}


$HotelCancellationPolicies = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("HotelCancellationPolicies");
if ($HotelCancellationPolicies->length > 0) {
    $HotelNorms = $HotelCancellationPolicies->item(0)->getElementsByTagName("HotelNorms");
    if ($HotelNorms->length > 0) {
        $string = $HotelNorms->item(0)->getElementsByTagName("string");
        if ($string->length > 0) {
            $string = $string->item(0)->nodeValue;
        } else {
            $string = "";
        }
    }

    $CancelPolicies = $HotelCancellationPolicies->item(0)->getElementsByTagName("CancelPolicies");
    if ($CancelPolicies->length > 0) {
        $LastCancellationDeadline = $CancelPolicies->item(0)->getElementsByTagName("LastCancellationDeadline");
        if ($LastCancellationDeadline->length > 0) {
            $LastCancellationDeadline = $LastCancellationDeadline->item(0)->nodeValue;
        } else {
            $LastCancellationDeadline = "";
        }
        $DefaultPolicy = $CancelPolicies->item(0)->getElementsByTagName("DefaultPolicy");
        if ($DefaultPolicy->length > 0) {
            $DefaultPolicy = $DefaultPolicy->item(0)->nodeValue;
        } else {
            $DefaultPolicy = "";
        }
        $AutoCancellationText = $CancelPolicies->item(0)->getElementsByTagName("AutoCancellationText");
        if ($AutoCancellationText->length > 0) {
            $AutoCancellationText = $AutoCancellationText->item(0)->nodeValue;
        } else {
            $AutoCancellationText = "";
        }

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('AP_CancelPolicies');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'HotelNorms' => $string,
                'LastCancellationDeadline' => $LastCancellationDeadline,
                'DefaultPolicy' => $DefaultPolicy,
                'AutoCancellationText' => $AutoCancellationText
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

        $CancelPolicy = $CancelPolicies->item(0)->getElementsByTagName("CancelPolicy");
        if ($CancelPolicy->length > 0) {
            for ($i=0; $i < $CancelPolicy->length; $i++) { 
                $RoomIndex = $CancelPolicy->item($i)->getAttribute("RoomIndex");
                $RoomTypeName = $CancelPolicy->item($i)->getAttribute("RoomTypeName");
                $FromDate = $CancelPolicy->item($i)->getAttribute("FromDate");
                $ToDate = $CancelPolicy->item($i)->getAttribute("ToDate");
                $ChargeType = $CancelPolicy->item($i)->getAttribute("ChargeType");
                $CancellationCharge = $CancelPolicy->item($i)->getAttribute("CancellationCharge");
                $Currency = $CancelPolicy->item($i)->getAttribute("Currency");

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('AP_CancelPolicy');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'RoomIndex' => $RoomIndex,
                        'RoomTypeName' => $RoomTypeName,
                        'FromDate' => $FromDate,
                        'ToDate' => $ToDate,
                        'ChargeType' => $ChargeType,
                        'CancellationCharge' => $CancellationCharge,
                        'Currency' => $Currency
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 3: " . $e;
                    echo $return;
                }
            }
        }

        $NoShowPolicy = $CancelPolicies->item(0)->getElementsByTagName("NoShowPolicy");
        if ($NoShowPolicy->length > 0) {
            for ($i=0; $i < $NoShowPolicy->length; $i++) { 
                $RoomIndex = $NoShowPolicy->item($i)->getAttribute("RoomIndex");
                $RoomTypeName = $NoShowPolicy->item($i)->getAttribute("RoomTypeName");
                $FromDate = $NoShowPolicy->item($i)->getAttribute("FromDate");
                $ToDate = $NoShowPolicy->item($i)->getAttribute("ToDate");
                $ChargeType = $NoShowPolicy->item($i)->getAttribute("ChargeType");
                $CancellationCharge = $NoShowPolicy->item($i)->getAttribute("CancellationCharge");
                $Currency = $NoShowPolicy->item($i)->getAttribute("Currency");

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('AP_NoShowPolicy');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'RoomIndex' => $RoomIndex,
                        'RoomTypeName' => $RoomTypeName,
                        'FromDate' => $FromDate,
                        'ToDate' => $ToDate,
                        'ChargeType' => $ChargeType,
                        'CancellationCharge' => $CancellationCharge,
                        'Currency' => $Currency
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 4: " . $e;
                    echo $return;
                }
            }
        }
    }
}

$PriceVerification = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("PriceVerification");
if ($PriceVerification->length > 0) {
    $AvailbaleOnNewPrice = $PriceVerification->item(0)->getAttribute("AvailbaleOnNewPrice");
    $PriceChanged = $PriceVerification->item(0)->getAttribute("PriceChanged");
    $Status = $PriceVerification->item(0)->getAttribute("Status");

    $HotelRooms = $PriceVerification->item(0)->getElementsByTagName("HotelRooms");
    if ($HotelRooms->length > 0) {
        $HotelRoom = $HotelRooms->item(0)->getElementsByTagName("HotelRoom");
        if ($HotelRoom->length > 0) {
            for ($i=0; $i < $HotelRoom->length; $i++) { 
                $RoomIndex = $HotelRoom->item($i)->getElementsByTagName("RoomIndex");
                if ($RoomIndex->length > 0) {
                    $RoomIndex = $RoomIndex->item(0)->nodeValue;
                } else {
                    $RoomIndex = "";
                }
                $RoomTypeName = $HotelRoom->item($i)->getElementsByTagName("RoomTypeName");
                if ($RoomTypeName->length > 0) {
                    $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
                } else {
                    $RoomTypeName = "";
                }
                $Inclusion = $HotelRoom->item($i)->getElementsByTagName("Inclusion");
                if ($Inclusion->length > 0) {
                    $Inclusion = $Inclusion->item(0)->nodeValue;
                } else {
                    $Inclusion = "";
                }
                $RoomTypeCode = $HotelRoom->item($i)->getElementsByTagName("RoomTypeCode");
                if ($RoomTypeCode->length > 0) {
                    $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
                } else {
                    $RoomTypeCode = "";
                }
                $RatePlanCode = $HotelRoom->item($i)->getElementsByTagName("RatePlanCode");
                if ($RatePlanCode->length > 0) {
                    $RatePlanCode = $RatePlanCode->item(0)->nodeValue;
                } else {
                    $RatePlanCode = "";
                }


                try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('AP_HotelRooms');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'RoomIndex' => $RoomIndex,
                            'RoomTypeName' => $RoomTypeName,
                            'Inclusion' => $Inclusion,
                            'RoomTypeCode' => $RoomTypeCode,
                            'RatePlanCode' => $RatePlanCode
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();

                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO 5: " . $e;
                    echo $return;
                }

                $RoomRate = $HotelRoom->item($i)->getElementsByTagName("RoomRate");
                if ($RoomRate->length > 0) {
                    $B2CRates = $RoomRate->item(0)->getAttribute("B2CRates");
                    $TotalFare = $RoomRate->item(0)->getAttribute("TotalFare");
                    $RoomTax = $RoomRate->item(0)->getAttribute("RoomTax");
                    $AgentMarkUp = $RoomRate->item(0)->getAttribute("AgentMarkUp");
                    $Currency = $RoomRate->item(0)->getAttribute("Currency");
                    $RoomFare = $RoomRate->item(0)->getAttribute("RoomFare");
                    $IsPackageRate = $RoomRate->item(0)->getAttribute("IsPackageRate");

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

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('AP_RoomRate');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'ExtraGuestCharges' => $ExtraGuestCharges,
                            'ChildCharges' => $ChildCharges,
                            'Discount' => $Discount,
                            'OtherCharges' => $OtherCharges,
                            'ServiceTax' => $ServiceTax,
                            'B2CRates' => $B2CRates,
                            'TotalFare' => $TotalFare,
                            'RoomTax' => $RoomTax,
                            'AgentMarkUp' => $AgentMarkUp,
                            'Currency' => $Currency,
                            'RoomFare' => $RoomFare,
                            'IsPackageRate' => $IsPackageRate,
                            'RoomIndex' => $RoomIndex
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();

                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO 6: " . $e;
                        echo $return;
                    }


                    $DayRates = $RoomRate->item(0)->getElementsByTagName("DayRates");
                    if ($DayRates->length > 0) {
                        $DayRate = $DayRates->item(0)->getElementsByTagName("DayRate");
                        if ($DayRate->length > 0) {
                            for ($j=0; $j < $DayRate->length; $j++) { 
                                $BaseFare = $DayRate->item($j)->getAttribute("BaseFare");
                                $Date = $DayRate->item($j)->getAttribute("Date");

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('AP_DayRates');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'BaseFare' => $BaseFare,
                                        'Date' => $Date,
                                        'RoomIndex' => $RoomIndex
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 7: " . $e;
                                    echo $return;
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