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
echo "COMECOU CABINS<br/>";
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
$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Header>
    <Agency xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Code>' . $code . '</Code>
    </Agency>
    <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
      <Name>' . $agency . '</Name>
      <Password>' . $password . '</Password>
    </Partner>
  </soap:Header>
  <soap:Body>
  <ListAvailableCabins xmlns="http://schemas.costacrociere.com/WebAffiliation">
  <components>
    <Component>
      <Type>Cruise</Type>
      <Code>PA07201001</Code>
      <Fare>
        <Code>BASIC</Code>
        <Description>Basic</Description>
        <IsAllowedToBeChanged></IsAllowedToBeChanged>
        <AdditionalInfoRequired></AdditionalInfoRequired>
        <PromoFlights xsi:nil="true" />
        <PromoBuses xsi:nil="true" />
      </Fare>
      <Category>
        <Code>IV</Code>
        <Name>Internas</Name>
        <ShipCode>PA</ShipCode>
        <Availability>true</Availability>
        <StatusCode>GA</StatusCode>
        <Price xsi:nil="true" />
        <CabinLocation>Inside</CabinLocation>
        <Ship xsi:nil="true" />
        <URL>https://training.costaextra.it</URL>
        <AdditionalInfo xsi:nil="true" />
        <IsSingleCabin>false</IsSingleCabin>
        <MaxOccupancy>4</MaxOccupancy>
        <MinOccupancy>1</MinOccupancy>
        <UpgradeCode></UpgradeCode>
        <Order>166</Order>
        <CabinAvailabilityInformation xsi:nil="true" />
        <AdditionalDescription></AdditionalDescription>
        <SuperCategoryType>1</SuperCategoryType>
        <CurrencyCode>USD</CurrencyCode>
        <Scores xsi:nil="true" />
      </Category>
      <Cabin>
        <Number></Number>
        <Category xsi:nil="true" />
        <Status></Status>
        <MinOccupancy>4</MinOccupancy>
        <MaxOccupancy>1</MaxOccupancy>
        <DeckName>string</DeckName>
        <DeckCode>string</DeckCode>
        <Beds xsi:nil="true" />
        <Facility>true</Facility>
        <DiningPreference>Main</DiningPreference>
        <Cruise xsi:nil="true" />
        <URL>string</URL>
        <RateInformation xsi:nil="true" />
        <DiningWithInformation xsi:nil="true" />
        <AdditionalInfo xsi:nil="true" />
        <GuestsCabinInfo xsi:nil="true" />
        <RestaurantInfo xsi:nil="true" />
        <DiningSatisfaction></DiningSatisfaction>
        <ServiceLevel>string</ServiceLevel>
      </Cabin>
      <Hotels>
        <Hotel xsi:nil="true" />
        <Hotel xsi:nil="true" />
      </Hotels>
      <TransportationDetails>
        <TransportationDetail xsi:nil="true" />
        <TransportationDetail xsi:nil="true" />
      </TransportationDetails>
      <Insurance>false</Insurance>
      <InsuranceAvailableInd>false</InsuranceAvailableInd>
      <Mandatory>false</Mandatory>
      <Direction>None</Direction>
      <StatusCode></StatusCode>
      <ItemId></ItemId>
      <InsuranceType></InsuranceType>
      <ReferenceNumber>AAA0001</ReferenceNumber>
      <IsPromo></IsPromo>
      <FlightClasses>
        <FlightClass xsi:nil="true" />
        <FlightClass xsi:nil="true" />
      </FlightClasses>
    </Component>
  </components>
  <guests>
    <Guest>
      <FirstName>Manuel</FirstName>
      <LastName>Alves</LastName>
      <LocalizedName></LocalizedName>
      <Nationality></Nationality>
      <Gender>Male</Gender>
      <Title>Mr</Title>
      <BirthDate>1986-02-21</BirthDate>
      <PlaceOfBirth></PlaceOfBirth>
      <BirthCountry></BirthCountry>
      <LanguageCode>en</LanguageCode>
      <Residence>
        <Address></Address>
        <City></City>
        <Zipcode></Zipcode>
        <State></State>
        <Country></Country>
      </Residence>
      <CostaClubNumber></CostaClubNumber>
      <Document>
        <Type>DNI</Type>
        <Number>string</Number>
        <ExpirationDate>2022-10-02</ExpirationDate>
        <IssueDate>2019-06-03</IssueDate>
        <IssuedInCountry>ES</IssuedInCountry>
        <IssuedInCity>Madrid</IssuedInCity>
      </Document>
      <AdditionalInfo>
        <WarningMessage></WarningMessage>
        <InfoMessage></InfoMessage>
      </AdditionalInfo>
      <Phone>
        <Type>Mobile</Type>
        <Number>987362514</Number>
      </Phone>
      <GuestType>Adult</GuestType>
      <Documents>
        <GuestDocument xsi:nil="true" />
        <GuestDocument xsi:nil="true" />
      </Documents>
      <MCHotel>
        <MCHotel xsi:nil="true" />
        <MCHotel xsi:nil="true" />
      </MCHotel>
      <MCTransportation>
        <MCTransportation xsi:nil="true" />
        <MCTransportation xsi:nil="true" />
      </MCTransportation>
      <TTGSequence>string</TTGSequence>
      <CabinInfo>
        <GuestCabinInfo xsi:nil="true" />
        <GuestCabinInfo xsi:nil="true" />
      </CabinInfo>
      <SpecialServices>
        <SpecialServices xsi:nil="true" />
        <SpecialServices xsi:nil="true" />
      </SpecialServices>
      <WebCheckIn>
        <SendAdvertising>false</SendAdvertising>
        <EmergencyInfo xsi:nil="true" />
        <AdditionalAdvertisingInfo xsi:nil="true" />
      </WebCheckIn>
      <MobilePhone>9873256410</MobilePhone>
      <SpecialItems>
        <SpecialItem xsi:nil="true" />
        <SpecialItem xsi:nil="true" />
      </SpecialItems>
      <PrivacyInfo>
        <PrivacyAuthorization>false</PrivacyAuthorization>
      </PrivacyInfo>
      <Compensations>
        <Compensation xsi:nil="true" />
        <Compensation xsi:nil="true" />
      </Compensations>
    </Guest>
  </guests>
</ListAvailableCabins>
  </soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ListAvailableCabins",
    "Content-length: ".strlen($raw)
));
$url = "https://training.costaclick.net/WAWS_1_9/Availability.asmx";

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
$ListAvailableCabinsResponse = $Body->item(0)->getElementsByTagName("ListAvailableCabinsResponse");
if ($ListAvailableCabinsResponse->length > 0) {
    $ListAvailableCabinsResult = $ListAvailableCabinsResponse->item(0)->getElementsByTagName("ListAvailableCabinsResult");
    if ($ListAvailableCabinsResult->length > 0) {
        $Cabin = $ListAvailableCabinsResult->item(0)->getElementsByTagName("Cabin");
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
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>