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
echo "COMECOU BOOKING RULES";
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

$url = 'http://xml-uat.bookingengine.es/webservice/JP/Operations/AvailTransactions.asmx?WSDL';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';
$hotelcode = '';
$RatePlanCode = '';

$raw= '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns="http://www.juniper.es/webservice/2007/">
<soap:Header/>
<soap:Body>
  <HotelBookingRules>
    <HotelBookingRulesRQ Version="1.1" Language="en">
      <Login Email="' . $email . '" Password="' . $password . '"/>
      <HotelBookingRulesRequest>
        <HotelOption RatePlanCode="' . $RatePlanCode . '"/>
        <SearchSegmentsHotels>
          <SearchSegmentHotels Start="2020-06-20" End="2020-06-22"/>
          <HotelCodes>
            <HotelCode>' . $hotelcode . '</HotelCode>
          </HotelCodes>
        </SearchSegmentsHotels>
      </HotelBookingRulesRequest>
    </HotelBookingRulesRQ>
  </HotelBookingRules>
</soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml",
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
$HotelBookingRulesResponse = $Body->item(0)->getElementsByTagName("HotelBookingRulesResponse");
if ($HotelBookingRulesResponse->length > 0) {
    $BookingRulesRS = $HotelBookingRulesResponse->item(0)->getElementsByTagName("BookingRulesRS");
    if ($BookingRulesRS->length > 0) {
        $IntCode = $BookingRulesRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $BookingRulesRS->item(0)->getAttribute("TimeStamp");
        $Url = $BookingRulesRS->item(0)->getAttribute("Url");
        $Results = $BookingRulesRS->item(0)->getElementsByTagName("Results");
        if ($Results->length > 0) {
            $HotelResult = $Results->item(0)->getElementsByTagName("HotelResult");
            if ($HotelResult->length > 0) {
                $HotelOptions = $HotelResult->item(0)->getElementsByTagName("HotelOptions");
                if ($HotelOptions->length > 0) {
                    $HotelOption = $HotelOptions->item(0)->getElementsByTagName("HotelOption");
                    if ($HotelOption->length > 0) {
                        $Status = $HotelOption->item(0)->getAttribute("Status");
                        //BookingCode
                        $BookingCode = $HotelOption->item(0)->getElementsByTagName("BookingCode");
                        if ($BookingCode->length > 0) {
                            $ExpirationDate = $BookingCode->item(0)->getAttribute("ExpirationDate");
                            $BookingCode = $BookingCode->item(0)->nodeValue;
                        } else {
                            $BookingCode = "";
                            $ExpirationDate = "";
                        }
                        //HotelRequiredFields
                        $HotelRequiredFields = $HotelOption->item(0)->getElementsByTagName("HotelRequiredFields");
                        if ($HotelRequiredFields->length > 0) {
                            $HotelBooking = $HotelRequiredFields->item(0)->getElementsByTagName("HotelBooking");
                            if ($HotelBooking->length > 0) {
                                $Holder = $HotelBooking->item(0)->getElementsByTagName("Holder");
                                if ($Holder->length > 0) {
                                    $RelPax = $Holder->item(0)->getElementsByTagName("RelPax");
                                    if ($RelPax->length > 0) {
                                        $IdPax = $RelPax->item(0)->getAttribute("IdPax");
                                    }
                                }
                                //Elements
                                $Elements = $HotelBooking->item(0)->getElementsByTagName("Elements");
                                if ($Elements->length > 0) {
                                    $HotelElement = $Elements->item(0)->getElementsByTagName("HotelElement");
                                    if ($HotelElement->length > 0) {
                                        $BookingCode = $HotelElement->item(0)->getElementsByTagName("BookingCode");
                                        if ($BookingCode->length > 0) {
                                            $BookingCode = $BookingCode->item(0)->nodeValue;
                                        } else {
                                            $BookingCode = "";
                                        }
                                        $HotelBookingInfo = $HotelElement->item(0)->getElementsByTagName("HotelBookingInfo");
                                        if ($HotelBookingInfo->length > 0) {
                                            $HotelBookingInfoEnd = $HotelBookingInfo->item(0)->getAttribute("End");
                                            $HotelBookingInfoStart = $HotelBookingInfo->item(0)->getAttribute("Start");
                                            $HotelCode = $HotelBookingInfo->item(0)->getElementsByTagName("HotelCode");
                                            if ($HotelCode->length > 0) {
                                                $HotelCode = $HotelCode->item(0)->nodeValue;
                                            } else {
                                                $HotelCode = "";
                                            }
                                            $Price = $HotelBookingInfo->item(0)->getElementsByTagName("Price");
                                            if ($Price->length > 0) {
                                                $PriceRange = $Price->item(0)->getElementsByTagName("PriceRange");
                                                if ($PriceRange->length > 0) {
                                                    $Currency = $PriceRange->item(0)->getAttribute("Currency");
                                                    $Maximum = $PriceRange->item(0)->getAttribute("Maximum");
                                                    $Minimum = $PriceRange->item(0)->getAttribute("Minimum");
                                                } 
                                            } 
                                        }
                                    }
                                }

                                $Paxes = $HotelBooking->item(0)->getElementsByTagName("Paxes");
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
                        $CancellationPolicy = $HotelOption->item(0)->getElementsByTagName("CancellationPolicy");
                        if ($CancellationPolicy->length > 0) {
                            $CPCurrencyCode = $CancellationPolicy->item(0)->getAttribute("CurrencyCode");
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

                        //PriceInformation
                        $PriceInformation = $HotelOption->item(0)->getElementsByTagName("PriceInformation");
                        if ($PriceInformation->length > 0) {
                            $Board = $PriceInformation->item(0)->getElementsByTagName("Board");
                            if ($Board->length > 0) {
                                $Type = $Board->item(0)->getAttribute("Type");
                                $Board = $Board->item(0)->nodeValue;
                            } else {
                                $Board = "";
                            }
                            //Prices
                            $Prices = $PriceInformation->item(0)->getElementsByTagName("Prices");
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

                                        $ServiceTaxes = $TotalFixAmounts->item(0)->getElementsByTagName("ServiceTaxes");
                                        if ($ServiceTaxes->length > 0) {
                                            $ServiceTaxesAmount = $ServiceTaxes->item(0)->getAttribute("Amount");
                                            $Included = $ServiceTaxes->item(0)->getAttribute("Included");
                                        } else {
                                            $ServiceTaxesAmount = "";
                                            $Included = "";
                                        }                
                                    }
                                }
                            }
                            //AdditionalElements
                            $AdditionalElements = $PriceInformation->item(0)->getElementsByTagName("AdditionalElements");
                            if ($AdditionalElements->length > 0) {
                                $HotelOffers = $AdditionalElements->item(0)->getElementsByTagName("HotelOffers");
                                if ($HotelOffers->length > 0) {
                                    $HotelOffer = $HotelOffers->item(0)->getElementsByTagName("HotelOffer");
                                    if ($HotelOffer->length > 0) {
                                        $Code = $HotelOffer->item(0)->getAttribute("Code");
                                        $HOName = $HotelOffer->item(0)->getElementsByTagName("Name");
                                        if ($HOName->length > 0) {
                                            $HOName = $HOName->item(0)->nodeValue;
                                        } else {
                                            $HOName = "";
                                        }
                                        $Description = $HotelOffer->item(0)->getElementsByTagName("Description");
                                        if ($Description->length > 0) {
                                            $Description = $Description->item(0)->nodeValue;
                                        } else {
                                            $Description = "";
                                        }
                                    }
                                }
                            }
                            //HotelContent
                            $HotelContent = $PriceInformation->item(0)->getElementsByTagName("HotelContent");
                            if ($HotelContent->length > 0) {
                                $HotelContentCode = $HotelContent->item(0)->getAttribute("Code");
                                $HotelName = $HotelContent->item(0)->getElementsByTagName("HotelName");
                                if ($HotelName->length > 0) {
                                    $HotelName = $HotelName->item(0)->nodeValue;
                                } else {
                                    $HotelName = "";
                                }
                                $Zone = $HotelContent->item(0)->getElementsByTagName("Zone");
                                if ($Zone->length > 0) {
                                    $ZoneJPDCode = $Zone->item(0)->getAttribute("JPDCode");
                                    $ZoneCode = $Zone->item(0)->getAttribute("Code");
                                } else {
                                    $ZoneJPDCode = "";
                                    $ZoneCode = "";
                                }
                                $HotelCategory = $HotelContent->item(0)->getElementsByTagName("HotelCategory");
                                if ($HotelCategory->length > 0) {
                                    $HotelCategoryType = $HotelCategory->item(0)->getAttribute("Type");
                                    $HotelCategory = $HotelCategory->item(0)->nodeValue;
                                } else {
                                    $HotelCategoryType = "";
                                    $HotelCategory = "";
                                }
                                $HotelType = $HotelContent->item(0)->getElementsByTagName("HotelType");
                                if ($HotelType->length > 0) {
                                    $HotelTypeType = $HotelType->item(0)->getAttribute("Type");
                                    $HotelType = $HotelType->item(0)->nodeValue;
                                } else {
                                    $HotelTypeType = "";
                                    $HotelType = "";
                                }
                                $Label = $HotelContent->item(0)->getElementsByTagName("Label");
                                if ($Label->length > 0) {
                                    $SortPriority = $Label->item(0)->getAttribute("SortPriority");
                                    $LabelCode = $Label->item(0)->getAttribute("Code");
                                    $LabelDescription = $Label->item(0)->getElementsByTagName("Description");
                                    if ($LabelDescription->length > 0) {
                                        $LabelDescription = $LabelDescription->item(0)->nodeValue;
                                    } else {
                                        $LabelDescription = "";
                                    }
                                } else {
                                    $SortPriority = "";
                                    $LabelCode = "";
                                }
                                $Address = $HotelContent->item(0)->getElementsByTagName("Address");
                                if ($Address->length > 0) {
                                    $Address2 = $Address->item(0)->getElementsByTagName("Address");
                                    if ($Address2->length > 0) {
                                        $Address2 = $Address2->item(0)->nodeValue;
                                    } else {
                                        $Address2 = "";
                                    }
                                    $Latitude = $Address->item(0)->getElementsByTagName("Latitude");
                                    if ($Latitude->length > 0) {
                                        $Latitude = $Latitude->item(0)->nodeValue;
                                    } else {
                                        $Latitude = "";
                                    }
                                    $Longitude = $Address->item(0)->getElementsByTagName("Longitude");
                                    if ($Longitude->length > 0) {
                                        $Longitude = $Longitude->item(0)->nodeValue;
                                    } else {
                                        $Longitude = "";
                                    }
                                }
                            }
                            //HotelRooms
                            $HotelRooms = $PriceInformation->item(0)->getElementsByTagName("HotelRooms");
                            if ($HotelRooms->length > 0) {
                                $HotelRoom = $HotelRooms->item(0)->getElementsByTagName("HotelRoom");
                                if ($HotelRoom->length > 0) {
                                    for ($iAux2=0; $iAux2 < $HotelRoom->length; $iAux2++) { 
                                        $Source = $HotelRoom->item($iAux2)->getAttribute("Source");
                                        $Units = $HotelRoom->item($iAux2)->getAttribute("Units");
                                        $AvailRooms = $HotelRoom->item($iAux2)->getAttribute("AvailRooms");
                                        $Name = $HotelRoom->item($iAux2)->getElementsByTagName("Name");
                                        if ($Name->length > 0) {
                                            $Name = $Name->item(0)->nodeValue;
                                        } else {
                                            $Name = "";
                                        }
                                        $RoomCategory = $HotelRoom->item($iAux2)->getElementsByTagName("RoomCategory");
                                        if ($RoomCategory->length > 0) {
                                            $RoomCategoryType = $RoomCategory->item(0)->getAttribute("Type");
                                            $RoomCategory = $RoomCategory->item(0)->nodeValue;
                                        } else {
                                            $RoomCategoryType = "";
                                        } 
                                        $RoomOccupancy = $HotelRoom->item($iAux2)->getElementsByTagName("RoomOccupancy");
                                        if ($RoomOccupancy->length > 0) {
                                            $Children = $RoomOccupancy->item(0)->getAttribute("Children");
                                            $Adults = $RoomOccupancy->item(0)->getAttribute("Adults");
                                            $Occupancy = $RoomOccupancy->item(0)->getAttribute("Occupancy");
                                        }                                       
                                    }
                                }
                            }
                        }
                        //OptionalElements
                        $OptionalElements = $HotelOption->item(0)->getElementsByTagName("OptionalElements");
                        if ($OptionalElements->length > 0) {
                            $Comments = $OptionalElements->item(0)->getElementsByTagName("Comments");
                            if ($Comments->length > 0) {
                                $Comment = $Comments->item(0)->getElementsByTagName("Comment");
                                if ($Comment->length > 0) {
                                    $CommentType = $Comment->item(0)->getAttribute("Type");
                                    $Comment = $Comment->item(0)->nodeValue;
                                }
                            }
                            $HotelSupplements = $OptionalElements->item(0)->getElementsByTagName("HotelSupplements");
                            if ($HotelSupplements->length > 0) {
                                $HotelSupplement = $HotelSupplements->item(0)->getElementsByTagName("HotelSupplement");
                                if ($HotelSupplement->length > 0) {
                                    $RatePlanCode = $HotelSupplement->item(0)->getAttribute("RatePlanCode");
                                    $HotelSupplementName = $HotelSupplement->item(0)->getElementsByTagName("Name");
                                    if ($HotelSupplementName->length > 0) {
                                        $HotelSupplementName = $HotelSupplementName->item(0)->nodeValue;
                                    } else {
                                        $HotelSupplementName = "";
                                    }
                                    $Prices = $HotelSupplement->item(0)->getElementsByTagName("Prices");
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

                                                $ServiceTaxes = $TotalFixAmounts->item(0)->getElementsByTagName("ServiceTaxes");
                                                if ($ServiceTaxes->length > 0) {
                                                    $ServiceTaxesAmount = $ServiceTaxes->item(0)->getAttribute("Amount");
                                                    $Included = $ServiceTaxes->item(0)->getAttribute("Included");
                                                } else {
                                                    $ServiceTaxesAmount = "";
                                                    $Included = "";
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
