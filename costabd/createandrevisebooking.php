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
echo "COMECOU CREATE BOOKING<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$code = '38290196';
$agency = 'Costamar';
$password = 'C0sT2m2R';
$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://schemas.costacrociere.com/WebAffiliation">
<soapenv:Header>
  <Partner>
    <Name>Costamar</Name>
    <Password>C0sT2m2R</Password>
  </Partner>
  <Agency>
    <Code>38290196</Code>
    <Culture/>
  </Agency> 
</soapenv:Header>
<soapenv:Body>
<CreateAndReviseBookingComplete xmlns="http://schemas.costacrociere.com/WebAffiliation">
<bookingStatus>OPT</bookingStatus>
<components>
  <Component>
    <Type>Cruise</Type>
    <Code>PA07201001</Code>
    <Fare>
      <Code>Basic</Code>
    </Fare>
    <Category>
      <Code>IV</Code>
      <Availability>true</Availability>
      <CabinLocation>Inside</CabinLocation>
      <IsSingleCabin>false</IsSingleCabin>
      <MaxOccupancy>0</MaxOccupancy>
      <MinOccupancy>0</MinOccupancy>
      <Order>106</Order>
    </Category>
    <Cabin>
      <Number>G00000</Number>
      <MinOccupancy>0</MinOccupancy>
      <MaxOccupancy>0</MaxOccupancy>
      <Facility>false</Facility>
      <DiningPreference>Unspecified</DiningPreference>
    </Cabin>
    <Insurance>false</Insurance>
    <InsuranceAvailableInd>false</InsuranceAvailableInd>
    <Mandatory>false</Mandatory>
    <Direction>None</Direction>
  </Component>
</components>
<bookingNumber/>
<guests>
  <Guest>
    <FirstName>aa</FirstName>
    <LastName>bb</LastName>
    <Nationality>DE</Nationality>
    <GuestType>Adult</GuestType>
    <Gender>Female</Gender>
    <BirthDate>2000-01-01T00:00:00.0000000+01:00</BirthDate>
    <CostaClubNumber/>
    <Document>
          <Type>PAS.NO</Type>
          <Number>123456</Number>
          <ExpirationDate>2022-05-23T10:47:44.5058479-03:00</ExpirationDate>
          <IssueDate>2018-05-23T10:47:44.5059378-03:00</IssueDate>
          <IssuedInCountry>USA</IssuedInCountry>
        </Document>
  </Guest>  
</guests>
 <paymentInfo>
</paymentInfo>
<GroupBooking/>      
    <synchronizationID>123</synchronizationID>
    <AgencyInvoiceCode>38290196</AgencyInvoiceCode>
</CreateAndReviseBookingComplete>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/CreateAndReviseBookingComplete",
    "Content-length: ".strlen($raw)
));
$url = "https://training.costaclick.net/WAWS_1_9/Booking.asmx";

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
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
$CreateAndReviseBookingCompleteResponse = $Body->item(0)->getElementsByTagName("CreateAndReviseBookingCompleteResponse");
if ($CreateAndReviseBookingCompleteResponse->length > 0) {
  $CreateAndReviseBookingCompleteResult = $CreateAndReviseBookingCompleteResponse->item(0)->getElementsByTagName("CreateAndReviseBookingCompleteResult");
  if ($CreateAndReviseBookingCompleteResult->length > 0) {
    $BookingNumber = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("BookingNumber");
    if ($BookingNumber->length > 0) {
        $BookingNumber = $BookingNumber->item(0)->nodeValue;
    } else {
        $BookingNumber = "";
    }
    $Status = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("Status");
    if ($Status->length > 0) {
        $Status = $Status->item(0)->nodeValue;
    } else {
        $Status = "";
    }
    $BookTime = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("BookTime");
    if ($BookTime->length > 0) {
        $BookTime = $BookTime->item(0)->nodeValue;
    } else {
        $BookTime = "";
    }
    $SynchronizationID = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("SynchronizationID");
    if ($SynchronizationID->length > 0) {
        $SynchronizationID = $SynchronizationID->item(0)->nodeValue;
    } else {
        $SynchronizationID = "";
    }
    $ExpirationDate = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("ExpirationDate");
    if ($ExpirationDate->length > 0) {
        $ExpirationDate = $ExpirationDate->item(0)->nodeValue;
    } else {
        $ExpirationDate = "";
    }
    $DiningPreference = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("DiningPreference");
    if ($DiningPreference->length > 0) {
        $DiningPreference = $DiningPreference->item(0)->nodeValue;
    } else {
        $DiningPreference = "";
    }
    $PaymentReceivedAmount = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("PaymentReceivedAmount");
    if ($PaymentReceivedAmount->length > 0) {
        $PaymentReceivedAmount = $PaymentReceivedAmount->item(0)->nodeValue;
    } else {
        $PaymentReceivedAmount = "";
    }
    $FullPaymentReceivedInd = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("FullPaymentReceivedInd");
    if ($FullPaymentReceivedInd->length > 0) {
        $FullPaymentReceivedInd = $FullPaymentReceivedInd->item(0)->nodeValue;
    } else {
        $FullPaymentReceivedInd = "";
    }
    $MiscChargeAmount = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("MiscChargeAmount");
    if ($MiscChargeAmount->length > 0) {
        $MiscChargeAmount = $MiscChargeAmount->item(0)->nodeValue;
    } else {
        $MiscChargeAmount = "";
    }
    $PortChargeAmount = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("PortChargeAmount");
    if ($PortChargeAmount->length > 0) {
        $PortChargeAmount = $PortChargeAmount->item(0)->nodeValue;
    } else {
        $PortChargeAmount = "";
    }
    $UserId = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("UserId");
    if ($UserId->length > 0) {
        $UserId = $UserId->item(0)->nodeValue;
    } else {
        $UserId = "";
    }
    $Vaucher = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("Vaucher");
    if ($Vaucher->length > 0) {
        $Vaucher = $Vaucher->item(0)->nodeValue;
    } else {
        $Vaucher = "";
    }
    $CurrencyCode = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("CurrencyCode");
    if ($CurrencyCode->length > 0) {
        $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
    } else {
        $CurrencyCode = "";
    }
    $AgencyPaymentType = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("AgencyPaymentType");
    if ($AgencyPaymentType->length > 0) {
        $AgencyPaymentType = $AgencyPaymentType->item(0)->nodeValue;
    } else {
        $AgencyPaymentType = "";
    }
    $Cabin = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("Cabin");
    if ($Cabin->length > 0) {
        $Number = $Cabin->item(0)->getElementsByTagName("Number");
        if ($Number->length > 0) {
            $Number = $Number->item(0)->nodeValue;
        } else {
            $Number = "";
        }
        $Status = $Cabin->item(0)->getElementsByTagName("Status");
        if ($Status->length > 0) {
            $Status = $Status->item(0)->nodeValue;
        } else {
            $Status = "";
        }
        $MinOccupancy = $Cabin->item(0)->getElementsByTagName("MinOccupancy");
        if ($MinOccupancy->length > 0) {
            $MinOccupancy = $MinOccupancy->item(0)->nodeValue;
        } else {
            $MinOccupancy = "";
        }
        $MaxOccupancy = $Cabin->item(0)->getElementsByTagName("MaxOccupancy");
        if ($MaxOccupancy->length > 0) {
            $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
        } else {
            $MaxOccupancy = "";
        }
        $DeckName = $Cabin->item(0)->getElementsByTagName("DeckName");
        if ($DeckName->length > 0) {
            $DeckName = $DeckName->item(0)->nodeValue;
        } else {
            $DeckName = "";
        }
        $DeckCode = $Cabin->item(0)->getElementsByTagName("DeckCode");
        if ($DeckCode->length > 0) {
            $DeckCode = $DeckCode->item(0)->nodeValue;
        } else {
            $DeckCode = "";
        }
        $Beds = $Cabin->item(0)->getElementsByTagName("Beds");
        if ($Beds->length > 0) {
            $Beds = $Beds->item(0)->nodeValue;
        } else {
            $Beds = "";
        }
        $Facility = $Cabin->item(0)->getElementsByTagName("Facility");
        if ($Facility->length > 0) {
            $Facility = $Facility->item(0)->nodeValue;
        } else {
            $Facility = "";
        }
        $DiningPreference = $Cabin->item(0)->getElementsByTagName("DiningPreference");
        if ($DiningPreference->length > 0) {
            $DiningPreference = $DiningPreference->item(0)->nodeValue;
        } else {
            $DiningPreference = "";
        }
        $URL = $Cabin->item(0)->getElementsByTagName("URL");
        if ($URL->length > 0) {
            $URL = $URL->item(0)->nodeValue;
        } else {
            $URL = "";
        }
        $Category = $Cabin->item(0)->getElementsByTagName("Category");
        if ($Category->length > 0) {
            $Code = $Category->item(0)->getElementsByTagName("Code");
            if ($Code->length > 0) {
                $Code = $Code->item(0)->nodeValue;
            } else {
                $Code = "";
            }
            $Name = $Category->item(0)->getElementsByTagName("Name");
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
            $Availability = $Category->item(0)->getElementsByTagName("Availability");
            if ($Availability->length > 0) {
                $Availability = $Availability->item(0)->nodeValue;
            } else {
                $Availability = "";
            }
            $StatusCode = $Category->item(0)->getElementsByTagName("StatusCode");
            if ($StatusCode->length > 0) {
                $StatusCode = $StatusCode->item(0)->nodeValue;
            } else {
                $StatusCode = "";
            }
            $CabinLocation = $Category->item(0)->getElementsByTagName("CabinLocation");
            if ($CabinLocation->length > 0) {
                $CabinLocation = $CabinLocation->item(0)->nodeValue;
            } else {
                $CabinLocation = "";
            }
            $URL = $Category->item(0)->getElementsByTagName("URL");
            if ($URL->length > 0) {
                $URL = $URL->item(0)->nodeValue;
            } else {
                $URL = "";
            }
            $IsSingleCabin = $Category->item(0)->getElementsByTagName("IsSingleCabin");
            if ($IsSingleCabin->length > 0) {
                $IsSingleCabin = $IsSingleCabin->item(0)->nodeValue;
            } else {
                $IsSingleCabin = "";
            }
            $MaxOccupancy = $Category->item(0)->getElementsByTagName("MaxOccupancy");
            if ($MaxOccupancy->length > 0) {
                $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
            } else {
                $MaxOccupancy = "";
            }
            $MinOccupancy = $Category->item(0)->getElementsByTagName("MinOccupancy");
            if ($MinOccupancy->length > 0) {
                $MinOccupancy = $MinOccupancy->item(0)->nodeValue;
            } else {
                $MinOccupancy = "";
            }
            $UpgradeCode = $Category->item(0)->getElementsByTagName("UpgradeCode");
            if ($UpgradeCode->length > 0) {
                $UpgradeCode = $UpgradeCode->item(0)->nodeValue;
            } else {
                $UpgradeCode = "";
            }
            $Order = $Category->item(0)->getElementsByTagName("Order");
            if ($Order->length > 0) {
                $Order = $Order->item(0)->nodeValue;
            } else {
                $Order = "";
            }
            $AdditionalDescription = $Category->item(0)->getElementsByTagName("AdditionalDescription");
            if ($AdditionalDescription->length > 0) {
                $AdditionalDescription = $Order->item(0)->nodeValue;
            } else {
                $AdditionalDescription = "";
            }
            $SuperCategoryType = $Category->item(0)->getElementsByTagName("SuperCategoryType");
            if ($SuperCategoryType->length > 0) {
                $SuperCategoryType = $SuperCategoryType->item(0)->nodeValue;
            } else {
                $SuperCategoryType = "";
            }
            $CurrencyCode = $Category->item(0)->getElementsByTagName("CurrencyCode");
            if ($CurrencyCode->length > 0) {
                $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
            } else {
                $CurrencyCode = "";
            }
            $Ship = $Category->item(0)->getElementsByTagName("Ship");
            if ($Ship->length > 0) {
                $ShipCode = $Ship->item(0)->getElementsByTagName("Code");
                if ($ShipCode->length > 0) {
                    $ShipCode = $ShipCode->item(0)->nodeValue;
                } else {
                    $ShipCode = "";
                }
                $ShipName = $Ship->item(0)->getElementsByTagName("Name");
                if ($ShipName->length > 0) {
                    $ShipName = $ShipName->item(0)->nodeValue;
                } else {
                    $ShipName = "";
                }
                $ShipURL = $Ship->item(0)->getElementsByTagName("URL");
                if ($ShipURL->length > 0) {
                    $ShipURL = $ShipURL->item(0)->nodeValue;
                } else {
                    $ShipURL = "";
                }
                $ShipCabins = $Ship->item(0)->getElementsByTagName("Cabins");
                if ($ShipCabins->length > 0) {
                    $ShipCabins = $ShipCabins->item(0)->nodeValue;
                } else {
                    $ShipCabins = "";
                }
                $ShipCrew = $Ship->item(0)->getElementsByTagName("Crew");
                if ($ShipCrew->length > 0) {
                    $ShipCrew = $ShipCrew->item(0)->nodeValue;
                } else {
                    $ShipCrew = "";
                }
                $ShipGuests = $Ship->item(0)->getElementsByTagName("Guests");
                if ($ShipGuests->length > 0) {
                    $ShipGuests = $ShipGuests->item(0)->nodeValue;
                } else {
                    $ShipGuests = "";
                }
                $ShipWidth = $Ship->item(0)->getElementsByTagName("Width");
                if ($ShipWidth->length > 0) {
                    $ShipWidth = $ShipWidth->item(0)->nodeValue;
                } else {
                    $ShipWidth = "";
                }
                $ShipLength = $Ship->item(0)->getElementsByTagName("Length");
                if ($ShipLength->length > 0) {
                    $ShipLength = $ShipLength->item(0)->nodeValue;
                } else {
                    $ShipLength = "";
                }
                $ShipTonnage = $Ship->item(0)->getElementsByTagName("Tonnage");
                if ($ShipTonnage->length > 0) {
                    $ShipTonnage = $ShipTonnage->item(0)->nodeValue;
                } else {
                    $ShipTonnage = "";
                }
                $ShipMaxSpeed = $Ship->item(0)->getElementsByTagName("MaxSpeed");
                if ($ShipMaxSpeed->length > 0) {
                    $ShipMaxSpeed = $ShipMaxSpeed->item(0)->nodeValue;
                } else {
                    $ShipMaxSpeed = "";
                }
                $ShipYearOfLaunch = $Ship->item(0)->getElementsByTagName("YearOfLaunch");
                if ($ShipYearOfLaunch->length > 0) {
                    $ShipYearOfLaunch = $ShipYearOfLaunch->item(0)->nodeValue;
                } else {
                    $ShipYearOfLaunch = "";
                }
                $ShipMonthOfLaunch = $Ship->item(0)->getElementsByTagName("MonthOfLaunch");
                if ($ShipMonthOfLaunch->length > 0) {
                    $ShipMonthOfLaunch = $ShipMonthOfLaunch->item(0)->nodeValue;
                } else {
                    $ShipMonthOfLaunch = "";
                }
            }
        }
        $Cruise = $Cabin->item(0)->getElementsByTagName("Cruise");
        if ($Cruise->length > 0) {
            $Code = $Cruise->item(0)->getElementsByTagName("Code");
            if ($Code->length > 0) {
                $Code = $Code->item(0)->nodeValue;
            } else {
                $Code = "";
            }
            $Description = $Cruise->item(0)->getElementsByTagName("Description");
            if ($Description->length > 0) {
                $Description = $Description->item(0)->nodeValue;
            } else {
                $Description = "";
            }
            $Availability = $Cruise->item(0)->getElementsByTagName("Availability");
            if ($Availability->length > 0) {
                $Availability = $Availability->item(0)->nodeValue;
            } else {
                $Availability = "";
            }
            $Sellability = $Cruise->item(0)->getElementsByTagName("Sellability");
            if ($Sellability->length > 0) {
                $Sellability = $Sellability->item(0)->nodeValue;
            } else {
                $Sellability = "";
            }
            $DepartureDate = $Cruise->item(0)->getElementsByTagName("DepartureDate");
            if ($DepartureDate->length > 0) {
                $DepartureDate = $DepartureDate->item(0)->nodeValue;
            } else {
                $DepartureDate = "";
            }
            $Duration = $Cruise->item(0)->getElementsByTagName("Duration");
            if ($Duration->length > 0) {
                $Duration = $Duration->item(0)->nodeValue;
            } else {
                $Duration = "";
            }
            $MaxOccupancy = $Cruise->item(0)->getElementsByTagName("MaxOccupancy");
            if ($MaxOccupancy->length > 0) {
                $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
            } else {
                $MaxOccupancy = "";
            }
            $Destination = $Cruise->item(0)->getElementsByTagName("Destination");
            if ($Destination->length > 0) {
                $DestinationCode = $Destination->item(0)->getElementsByTagName("Code");
                if ($DestinationCode->length > 0) {
                    $DestinationCode = $DestinationCode->item(0)->nodeValue;
                } else {
                    $DestinationCode = "";
                }
                $DestinationDescription = $Destination->item(0)->getElementsByTagName("Description");
                if ($DestinationDescription->length > 0) {
                    $DestinationDescription = $DestinationDescription->item(0)->nodeValue;
                } else {
                    $DestinationDescription = "";
                }
            }
            $DeparturePort = $Cruise->item(0)->getElementsByTagName("DeparturePort");
            if ($DeparturePort->length > 0) {
                $DeparturePortCode = $DeparturePort->item(0)->getElementsByTagName("Code");
                if ($DeparturePortCode->length > 0) {
                    $DeparturePortCode = $DeparturePortCode->item(0)->nodeValue;
                } else {
                    $DeparturePortCode = "";
                }
                $DeparturePortDescription = $DeparturePort->item(0)->getElementsByTagName("Description");
                if ($DeparturePortDescription->length > 0) {
                    $DeparturePortDescription = $DeparturePortDescription->item(0)->nodeValue;
                } else {
                    $DeparturePortDescription = "";
                }
            }
            $Ship = $Cruise->item(0)->getElementsByTagName("Ship");
            if ($Ship->length > 0) {
                $ShipCode = $Ship->item(0)->getElementsByTagName("Code");
                if ($ShipCode->length > 0) {
                    $ShipCode = $ShipCode->item(0)->nodeValue;
                } else {
                    $ShipCode = "";
                }
                $ShipName = $Ship->item(0)->getElementsByTagName("Name");
                if ($ShipName->length > 0) {
                    $ShipName = $ShipName->item(0)->nodeValue;
                } else {
                    $ShipName = "";
                }
                $ShipURL = $Ship->item(0)->getElementsByTagName("URL");
                if ($ShipURL->length > 0) {
                    $ShipURL = $ShipURL->item(0)->nodeValue;
                } else {
                    $ShipURL = "";
                }
                $ShipCabins = $Ship->item(0)->getElementsByTagName("Cabins");
                if ($ShipCabins->length > 0) {
                    $ShipCabins = $ShipCabins->item(0)->nodeValue;
                } else {
                    $ShipCabins = "";
                }
                $ShipCrew = $Ship->item(0)->getElementsByTagName("Crew");
                if ($ShipCrew->length > 0) {
                    $ShipCrew = $ShipCrew->item(0)->nodeValue;
                } else {
                    $ShipCrew = "";
                }
                $ShipGuests = $Ship->item(0)->getElementsByTagName("Guests");
                if ($ShipGuests->length > 0) {
                    $ShipGuests = $ShipGuests->item(0)->nodeValue;
                } else {
                    $ShipGuests = "";
                }
                $ShipWidth = $Ship->item(0)->getElementsByTagName("Width");
                if ($ShipWidth->length > 0) {
                    $ShipWidth = $ShipWidth->item(0)->nodeValue;
                } else {
                    $ShipWidth = "";
                }
                $ShipLength = $Ship->item(0)->getElementsByTagName("Length");
                if ($ShipLength->length > 0) {
                    $ShipLength = $ShipLength->item(0)->nodeValue;
                } else {
                    $ShipLength = "";
                }
                $ShipTonnage = $Ship->item(0)->getElementsByTagName("Tonnage");
                if ($ShipTonnage->length > 0) {
                    $ShipTonnage = $ShipTonnage->item(0)->nodeValue;
                } else {
                    $ShipTonnage = "";
                }
                $ShipMaxSpeed = $Ship->item(0)->getElementsByTagName("MaxSpeed");
                if ($ShipMaxSpeed->length > 0) {
                    $ShipMaxSpeed = $ShipMaxSpeed->item(0)->nodeValue;
                } else {
                    $ShipMaxSpeed = "";
                }
                $ShipYearOfLaunch = $Ship->item(0)->getElementsByTagName("YearOfLaunch");
                if ($ShipYearOfLaunch->length > 0) {
                    $ShipYearOfLaunch = $ShipYearOfLaunch->item(0)->nodeValue;
                } else {
                    $ShipYearOfLaunch = "";
                }
                $ShipMonthOfLaunch = $Ship->item(0)->getElementsByTagName("MonthOfLaunch");
                if ($ShipMonthOfLaunch->length > 0) {
                    $ShipMonthOfLaunch = $ShipMonthOfLaunch->item(0)->nodeValue;
                } else {
                    $ShipMonthOfLaunch = "";
                }
            }
            $ImmediateConfirm = $Cruise->item(0)->getElementsByTagName("ImmediateConfirm");
            if ($ImmediateConfirm->length > 0) {
                $IsImmediateConfirm = $ImmediateConfirm->item(0)->getElementsByTagName("IsImmediateConfirm");
                if ($IsImmediateConfirm->length > 0) {
                    $IsImmediateConfirm = $IsImmediateConfirm->item(0)->nodeValue;
                } else {
                    $IsImmediateConfirm = "";
                }
            }
        }
    }
    $Components = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("Components");
    if ($Components->length > 0) {
        $Component = $Components->item(0)->getElementsByTagName("Component");
        if ($Component->length > 0) {
            for ($i=0; $i < $Component->length; $i++) { 
                $ComponentType = $Component->item($i)->getElementsByTagName("Type");
                if ($ComponentType->length > 0) {
                    $ComponentType = $ComponentType->item(0)->nodeValue;
                } else {
                    $ComponentType = "";
                }
                $ComponentCode = $Component->item($i)->getElementsByTagName("Code");
                if ($ComponentCode->length > 0) {
                    $ComponentCode = $ComponentCode->item(0)->nodeValue;
                } else {
                    $ComponentCode = "";
                }
                $Insurance = $Component->item($i)->getElementsByTagName("Insurance");
                if ($Insurance->length > 0) {
                    $Insurance = $Insurance->item(0)->nodeValue;
                } else {
                    $Insurance = "";
                }
                $InsuranceAvailableInd = $Component->item($i)->getElementsByTagName("InsuranceAvailableInd");
                if ($InsuranceAvailableInd->length > 0) {
                    $InsuranceAvailableInd = $InsuranceAvailableInd->item(0)->nodeValue;
                } else {
                    $InsuranceAvailableInd = "";
                }
                $Mandatory = $Component->item($i)->getElementsByTagName("Mandatory");
                if ($Mandatory->length > 0) {
                    $Mandatory = $Mandatory->item(0)->nodeValue;
                } else {
                    $Mandatory = "";
                }
                $Direction = $Component->item($i)->getElementsByTagName("Direction");
                if ($Direction->length > 0) {
                    $Direction = $Direction->item(0)->nodeValue;
                } else {
                    $Direction = "";
                }
                $Cities = $Component->item($i)->getElementsByTagName("Cities");
                if ($Cities->length > 0) {
                    $City = $Cities->item(0)->getElementsByTagName("City");
                    if ($City->length > 0) {
                        $CityCode = $City->item(0)->getElementsByTagName("Code");
                        if ($CityCode->length > 0) {
                            $CityCode = $CityCode->item(0)->nodeValue;
                        } else {
                            $CityCode = "";
                        }
                        $CityDescription = $City->item(0)->getElementsByTagName("Description");
                        if ($CityDescription->length > 0) {
                            $CityDescription = $CityDescription->item(0)->nodeValue;
                        } else {
                            $CityDescription = "";
                        }
                    }
                }
                $Fare = $Component->item($i)->getElementsByTagName("Fare");
                if ($Fare->length > 0) {
                    $FareCode = $Fare->item(0)->getElementsByTagName("Code");
                    if ($FareCode->length > 0) {
                        $FareCode = $FareCode->item(0)->nodeValue;
                    } else {
                        $FareCode = "";
                    }
                    $FareDescription = $Fare->item(0)->getElementsByTagName("Description");
                    if ($FareDescription->length > 0) {
                        $FareDescription = $FareDescription->item(0)->nodeValue;
                    } else {
                        $FareDescription = "";
                    }
                    $PromoFlights = $Fare->item(0)->getElementsByTagName("PromoFlights");
                    if ($PromoFlights->length > 0) {
                        $AvailablePromoFlights = $PromoFlights->item(0)->getElementsByTagName("AvailablePromoFlights");
                        if ($AvailablePromoFlights->length > 0) {
                            $AvailablePromoFlights = $AvailablePromoFlights->item(0)->nodeValue;
                        } else {
                            $AvailablePromoFlights = "";
                        }
                        $PromoFlightsFees = $PromoFlights->item(0)->getElementsByTagName("PromoFlightsFees");
                        if ($PromoFlightsFees->length > 0) {
                            $PromoFlightsFees = $PromoFlightsFees->item(0)->nodeValue;
                        } else {
                            $PromoFlightsFees = "";
                        }
                        $OnlyPromoFlight = $PromoFlights->item(0)->getElementsByTagName("OnlyPromoFlight");
                        if ($OnlyPromoFlight->length > 0) {
                            $OnlyPromoFlight = $OnlyPromoFlight->item(0)->nodeValue;
                        } else {
                            $OnlyPromoFlight = "";
                        }
                    }
                }
            }
        }
    }
    $Guests = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("Guests");
    if ($Guests->length > 0) {
        $Guest = $Guests->item(0)->getElementsByTagName("Guest");
        if ($Guest->length > 0) {
           for ($j=0; $j < $Guest->length; $j++) { 
              $FirstName = $Guest->item($j)->getElementsByTagName("FirstName");
              if ($FirstName->length > 0) {
                  $FirstName = $FirstName->item(0)->nodeValue;
              } else {
                  $FirstName = "";
              }
              $LastName = $Guest->item($j)->getElementsByTagName("LastName");
              if ($LastName->length > 0) {
                  $LastName = $LastName->item(0)->nodeValue;
              } else {
                  $LastName = "";
              }
              $Nationality = $Guest->item($j)->getElementsByTagName("Nationality");
              if ($Nationality->length > 0) {
                  $Nationality = $Nationality->item(0)->nodeValue;
              } else {
                  $Nationality = "";
              }
              $Gender = $Guest->item($j)->getElementsByTagName("Gender");
              if ($Gender->length > 0) {
                  $Gender = $Gender->item(0)->nodeValue;
              } else {
                  $Gender = "";
              }
              $Title = $Guest->item($j)->getElementsByTagName("Title");
              if ($Title->length > 0) {
                  $Title = $Title->item(0)->nodeValue;
              } else {
                  $Title = "";
              }
              $BirthDate = $Guest->item($j)->getElementsByTagName("BirthDate");
              if ($BirthDate->length > 0) {
                  $BirthDate = $BirthDate->item(0)->nodeValue;
              } else {
                  $BirthDate = "";
              }
              $LanguageCode = $Guest->item($j)->getElementsByTagName("LanguageCode");
              if ($LanguageCode->length > 0) {
                  $LanguageCode = $LanguageCode->item(0)->nodeValue;
              } else {
                  $LanguageCode = "";
              }
              $GuestType = $Guest->item($j)->getElementsByTagName("GuestType");
              if ($GuestType->length > 0) {
                  $GuestType = $GuestType->item(0)->nodeValue;
              } else {
                  $GuestType = "";
              }
              $TTGSequence = $Guest->item($j)->getElementsByTagName("TTGSequence");
              if ($TTGSequence->length > 0) {
                  $TTGSequence = $TTGSequence->item(0)->nodeValue;
              } else {
                  $TTGSequence = "";
              }
              $SpecialServices = $Guest->item($j)->getElementsByTagName("SpecialServices");
              if ($SpecialServices->length > 0) {
                  $SpecialServices = $SpecialServices->item(0)->nodeValue;
              } else {
                  $SpecialServices = "";
              }
              $Email = $Guest->item($j)->getElementsByTagName("Email");
              if ($Email->length > 0) {
                  $Email = $Email->item(0)->nodeValue;
              } else {
                  $Email = "";
              }
              $Compensations = $Guest->item($j)->getElementsByTagName("Compensations");
              if ($Compensations->length > 0) {
                  $Compensations = $Compensations->item(0)->nodeValue;
              } else {
                  $Compensations = "";
              }
              $Residence = $Guest->item($j)->getElementsByTagName("Residence");
              if ($Residence->length > 0) {
                  $Address = $Residence->item(0)->getElementsByTagName("Address");
                  if ($Address->length > 0) {
                      $Address = $Address->item(0)->nodeValue;
                  } else {
                      $Address = "";
                  }
                  $City = $Residence->item(0)->getElementsByTagName("City");
                  if ($City->length > 0) {
                      $City = $City->item(0)->nodeValue;
                  } else {
                      $City = "";
                  }
                  $Zipcode = $Residence->item(0)->getElementsByTagName("Zipcode");
                  if ($Zipcode->length > 0) {
                      $Zipcode = $Zipcode->item(0)->nodeValue;
                  } else {
                      $Zipcode = "";
                  }
                  $State = $Residence->item(0)->getElementsByTagName("State");
                  if ($State->length > 0) {
                      $State = $State->item(0)->nodeValue;
                  } else {
                      $State = "";
                  }
              }
              $Document = $Guest->item($j)->getElementsByTagName("Document");
              if ($Document->length > 0) {
                  $DocumentType = $Document->item(0)->getElementsByTagName("Type");
                  if ($DocumentType->length > 0) {
                      $DocumentType = $DocumentType->item(0)->nodeValue;
                  } else {
                      $DocumentType = "";
                  }
                  $DocumentNumber = $Document->item(0)->getElementsByTagName("Number");
                  if ($DocumentNumber->length > 0) {
                      $DocumentNumber = $DocumentNumber->item(0)->nodeValue;
                  } else {
                      $DocumentNumber = "";
                  }
                  $ExpirationDate = $Document->item(0)->getElementsByTagName("ExpirationDate");
                  if ($ExpirationDate->length > 0) {
                      $ExpirationDate = $ExpirationDate->item(0)->nodeValue;
                  } else {
                      $ExpirationDate = "";
                  }
                  $IssueDate = $Document->item(0)->getElementsByTagName("IssueDate");
                  if ($IssueDate->length > 0) {
                      $IssueDate = $IssueDate->item(0)->nodeValue;
                  } else {
                      $IssueDate = "";
                  }
                  $IssuedInCountry = $Document->item(0)->getElementsByTagName("IssuedInCountry");
                  if ($IssuedInCountry->length > 0) {
                      $IssuedInCountry = $IssuedInCountry->item(0)->nodeValue;
                  } else {
                      $IssuedInCountry = "";
                  }
                  $IssuedInCity = $Document->item(0)->getElementsByTagName("IssuedInCity");
                  if ($IssuedInCity->length > 0) {
                      $IssuedInCity = $IssuedInCity->item(0)->nodeValue;
                  } else {
                      $IssuedInCity = "";
                  }
              }
              $RevisionControl = $Guest->item($j)->getElementsByTagName("RevisionControl");
              if ($RevisionControl->length > 0) {
                  $RateChange = $RevisionControl->item(0)->getElementsByTagName("RateChange");
                  if ($RateChange->length > 0) {
                      $RateChange = $RateChange->item(0)->nodeValue;
                  } else {
                      $RateChange = "";
                  }
                  $SpecialServicesChanged = $RevisionControl->item(0)->getElementsByTagName("SpecialServicesChanged");
                  if ($SpecialServicesChanged->length > 0) {
                      $SpecialServicesChanged = $SpecialServicesChanged->item(0)->nodeValue;
                  } else {
                      $SpecialServicesChanged = "";
                  }
                  $HotelChange = $RevisionControl->item(0)->getElementsByTagName("HotelChange");
                  if ($HotelChange->length > 0) {
                      $HotelChange = $HotelChange->item(0)->nodeValue;
                  } else {
                      $HotelChange = "";
                  }
                  $TransportationChange = $RevisionControl->item(0)->getElementsByTagName("TransportationChange");
                  if ($TransportationChange->length > 0) {
                      $TransportationChange = $TransportationChange->item(0)->nodeValue;
                  } else {
                      $TransportationChange = "";
                  }
                  $NameChange = $RevisionControl->item(0)->getElementsByTagName("NameChange");
                  if ($NameChange->length > 0) {
                      $NameChange = $NameChange->item(0)->nodeValue;
                  } else {
                      $NameChange = "";
                  }
              }
              $Documents = $Guest->item($j)->getElementsByTagName("Documents");
              if ($Documents->length > 0) {
                  $GuestDocument = $Documents->item(0)->getElementsByTagName("GuestDocument");
                  if ($GuestDocument->length > 0) {
                     for ($jAux=0; $jAux < $GuestDocument->length; $jAux++) { 
                        $Type = $GuestDocument->item($jAux)->getElementsByTagName("Type");
                        if ($Type->length > 0) {
                            $Type = $Type->item(0)->nodeValue;
                        } else {
                            $Type = "";
                        }
                        $Number = $GuestDocument->item($jAux)->getElementsByTagName("Number");
                        if ($Number->length > 0) {
                            $Number = $Number->item(0)->nodeValue;
                        } else {
                            $Number = "";
                        }
                        $ExpirationDate = $GuestDocument->item($jAux)->getElementsByTagName("ExpirationDate");
                        if ($ExpirationDate->length > 0) {
                            $ExpirationDate = $ExpirationDate->item(0)->nodeValue;
                        } else {
                            $ExpirationDate = "";
                        }
                        $IssueDate = $GuestDocument->item($jAux)->getElementsByTagName("IssueDate");
                        if ($IssueDate->length > 0) {
                            $IssueDate = $IssueDate->item(0)->nodeValue;
                        } else {
                            $IssueDate = "";
                        }
                        $IssuedInCountry = $GuestDocument->item($jAux)->getElementsByTagName("IssuedInCountry");
                        if ($IssuedInCountry->length > 0) {
                            $IssuedInCountry = $IssuedInCountry->item(0)->nodeValue;
                        } else {
                            $IssuedInCountry = "";
                        }
                        $IssuedInCity = $GuestDocument->item($jAux)->getElementsByTagName("IssuedInCity");
                        if ($IssuedInCity->length > 0) {
                            $IssuedInCity = $IssuedInCity->item(0)->nodeValue;
                        } else {
                            $IssuedInCity = "";
                        }
                     }
                  }
              }
           }
        }
    }
    $Prices = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("Prices");
    if ($Prices->length > 0) {
        $Price = $Prices->item(0)->getElementsByTagName("Price");
        if ($Price->length > 0) {
           for ($k=0; $k < $Price->length; $k++) { 
              $Type = $Price->item($k)->getElementsByTagName("Type");
              if ($Type->length > 0) {
                  $Type = $Type->item(0)->nodeValue;
              } else {
                  $Type = "";
              }
              $Code = $Price->item($k)->getElementsByTagName("Code");
              if ($Code->length > 0) {
                  $Code = $Code->item(0)->nodeValue;
              } else {
                  $Code = "";
              }
              $Description = $Price->item($k)->getElementsByTagName("Description");
              if ($Description->length > 0) {
                  $Description = $Description->item(0)->nodeValue;
              } else {
                  $Description = "";
              }
              $Amount = $Price->item($k)->getElementsByTagName("Amount");
              if ($Amount->length > 0) {
                  $Amount = $Amount->item(0)->nodeValue;
              } else {
                  $Amount = "";
              }
              $GuestSequenceNumber = $Price->item($k)->getElementsByTagName("GuestSequenceNumber");
              if ($GuestSequenceNumber->length > 0) {
                  $GuestSequenceNumber = $GuestSequenceNumber->item(0)->nodeValue;
              } else {
                  $GuestSequenceNumber = "";
              }
           }
        }
    }
    $AdditionalEmbarkationInfo = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("AdditionalEmbarkationInfo");
    if ($AdditionalEmbarkationInfo->length > 0) {
        $EmbarkationInfo = $AdditionalEmbarkationInfo->item(0)->getElementsByTagName("EmbarkationInfo");
        if ($EmbarkationInfo->length > 0) {
           for ($x=0; $x < $EmbarkationInfo->length ; $x++) { 
              $Text = $EmbarkationInfo->item($x)->getElementsByTagName("Text");
              if ($Text->length > 0) {
                  $Text = $Text->item(0)->nodeValue;
              } else {
                  $Text = "";
              }
              $Time = $EmbarkationInfo->item($x)->getElementsByTagName("Time");
              if ($Time->length > 0) {
                  $Time = $Time->item(0)->nodeValue;
              } else {
                  $Time = "";
              }
           }
        }
    }
    $PaymentTypes = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("PaymentTypes");
    if ($PaymentTypes->length > 0) {
        $PaymentType = $PaymentTypes->item(0)->getElementsByTagName("PaymentType");
        if ($PaymentType->length > 0) {
            $PaymentType = $PaymentType->item(0)->nodeValue;
        } else {
            $PaymentType = "";
        }
    }
    $PaymentSchedules = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("PaymentSchedules");
    if ($PaymentSchedules->length > 0) {
        $PaymentSchedule = $PaymentSchedules->item(0)->getElementsByTagName("PaymentSchedule");
        if ($PaymentSchedule->length > 0) {
           for ($y=0; $y < $PaymentSchedule->length; $y++) { 
              $Code = $PaymentSchedule->item($y)->getElementsByTagName("Code");
              if ($Code->length > 0) {
                  $Code = $Code->item(0)->nodeValue;
              } else {
                  $Code = "";
              }
              $Description = $PaymentSchedule->item($y)->getElementsByTagName("Description");
              if ($Description->length > 0) {
                  $Description = $Description->item(0)->nodeValue;
              } else {
                  $Description = "";
              }
              $DueDate = $PaymentSchedule->item($y)->getElementsByTagName("DueDate");
              if ($DueDate->length > 0) {
                  $DueDate = $DueDate->item(0)->nodeValue;
              } else {
                  $DueDate = "";
              }
              $Amount = $PaymentSchedule->item($y)->getElementsByTagName("Amount");
              if ($Amount->length > 0) {
                  $Amount = $Amount->item(0)->nodeValue;
              } else {
                  $Amount = "";
              }
           }
        }
    }
    $RevisionControl = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("RevisionControl");
    if ($RevisionControl->length > 0) {
        $ChangeBooking = $RevisionControl->item(0)->getElementsByTagName("ChangeBooking");
        if ($ChangeBooking->length > 0) {
            $ChangeBooking = $ChangeBooking->item(0)->nodeValue;
        } else {
            $ChangeBooking = "";
        }
        $ChangeBookingByElectronicChannel = $RevisionControl->item(0)->getElementsByTagName("ChangeBookingByElectronicChannel");
        if ($ChangeBookingByElectronicChannel->length > 0) {
            $ChangeBookingByElectronicChannel = $ChangeBookingByElectronicChannel->item(0)->nodeValue;
        } else {
            $ChangeBookingByElectronicChannel = "";
        }
        $ChangeSailing = $RevisionControl->item(0)->getElementsByTagName("ChangeSailing");
        if ($ChangeSailing->length > 0) {
            $ChangeSailing = $ChangeSailing->item(0)->nodeValue;
        } else {
            $ChangeSailing = "";
        }
        $ChangeInsurance = $RevisionControl->item(0)->getElementsByTagName("ChangeInsurance");
        if ($ChangeInsurance->length > 0) {
            $ChangeInsurance = $ChangeInsurance->item(0)->nodeValue;
        } else {
            $ChangeInsurance = "";
        }
        $Reactivate = $RevisionControl->item(0)->getElementsByTagName("Reactivate");
        if ($Reactivate->length > 0) {
            $Reactivate = $Reactivate->item(0)->nodeValue;
        } else {
            $Reactivate = "";
        }
        $AddGuest = $RevisionControl->item(0)->getElementsByTagName("AddGuest");
        if ($AddGuest->length > 0) {
            $AddGuest = $AddGuest->item(0)->nodeValue;
        } else {
            $AddGuest = "";
        }
        $CancelGuest = $RevisionControl->item(0)->getElementsByTagName("CancelGuest");
        if ($CancelGuest->length > 0) {
            $CancelGuest = $CancelGuest->item(0)->nodeValue;
        } else {
            $CancelGuest = "";
        }
        $ChangeCategory = $RevisionControl->item(0)->getElementsByTagName("ChangeCategory");
        if ($ChangeCategory->length > 0) {
            $ChangeCategory = $ChangeCategory->item(0)->nodeValue;
        } else {
            $ChangeCategory = "";
        }
        $ChangeCabin = $RevisionControl->item(0)->getElementsByTagName("ChangeCabin");
        if ($ChangeCabin->length > 0) {
            $ChangeCabin = $ChangeCabin->item(0)->nodeValue;
        } else {
            $ChangeCabin = "";
        }
    }
    $CommissionDetails = $CreateAndReviseBookingCompleteResult->item(0)->getElementsByTagName("CommissionDetails");
    if ($CommissionDetails->length > 0) {
        $CommissionDetail = $CommissionDetails->item(0)->getElementsByTagName("CommissionDetail");
        if ($CommissionDetail->length > 0) {
           for ($z=0; $z < $CommissionDetail->length; $z++) { 
              $Code = $CommissionDetail->item($z)->getElementsByTagName("Code");
              if ($Code->length > 0) {
                  $Code = $Code->item(0)->nodeValue;
              } else {
                  $Code = "";
              }
              $Description = $CommissionDetail->item($z)->getElementsByTagName("Description");
              if ($Description->length > 0) {
                  $Description = $Description->item(0)->nodeValue;
              } else {
                  $Description = "";
              }
              $GrossAmount = $CommissionDetail->item($z)->getElementsByTagName("GrossAmount");
              if ($GrossAmount->length > 0) {
                  $GrossAmount = $GrossAmount->item(0)->nodeValue;
              } else {
                  $GrossAmount = "";
              }
              $Percent = $CommissionDetail->item($z)->getElementsByTagName("Percent");
              if ($Percent->length > 0) {
                  $Percent = $Percent->item(0)->nodeValue;
              } else {
                  $Percent = "";
              }
              $Amount = $CommissionDetail->item($z)->getElementsByTagName("Amount");
              if ($Amount->length > 0) {
                  $Amount = $Amount->item(0)->nodeValue;
              } else {
                  $Amount = "";
              }
              $GuestSequenceNumber = $CommissionDetail->item($z)->getElementsByTagName("GuestSequenceNumber");
              if ($GuestSequenceNumber->length > 0) {
                  $GuestSequenceNumber = $GuestSequenceNumber->item(0)->nodeValue;
              } else {
                  $GuestSequenceNumber = "";
              }
              $ItemCode = $CommissionDetail->item($z)->getElementsByTagName("ItemCode");
              if ($ItemCode->length > 0) {
                  $ItemCode = $ItemCode->item(0)->nodeValue;
              } else {
                  $ItemCode = "";
              }
              $ItemDescription = $CommissionDetail->item($z)->getElementsByTagName("ItemDescription");
              if ($ItemDescription->length > 0) {
                  $ItemDescription = $ItemDescription->item(0)->nodeValue;
              } else {
                  $ItemDescription = "";
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