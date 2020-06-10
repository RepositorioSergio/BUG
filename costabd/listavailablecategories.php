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
echo "COMECOU CATEGORIES<br/>";
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
    <ListAvailableCategories xmlns="http://schemas.costacrociere.com/WebAffiliation">
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
            <TransportationDetails>
                <TransportationDetail xsi:nil="true" />
                <TransportationDetail xsi:nil="true" />
            </TransportationDetails>
            <Insurance>false</Insurance>
            <InsuranceAvailableInd>false</InsuranceAvailableInd>
            <Mandatory>false</Mandatory>
            <Direction>None</Direction>
            <ReferenceNumber>A0001</ReferenceNumber>
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
            <Nationality>ES</Nationality>
            <Gender>Male</Gender>
            <Title>Mr</Title>
            <BirthDate>1986-02-13</BirthDate>
            <PlaceOfBirth></PlaceOfBirth>
            <BirthCountry></BirthCountry>
            <LanguageCode>en</LanguageCode>
            <Residence>
                <Address></Address>
                <City></City>
                <Zipcode></Zipcode>
                <State></State>
                <Country>ES</Country>
            </Residence>
            <CostaClubNumber></CostaClubNumber>
            <Document>
                <Type>DNI</Type>
                <Number>654123987</Number>
                <ExpirationDate>2022-03-16</ExpirationDate>
                <IssueDate>2019-06-02</IssueDate>
                <IssuedInCountry>ES</IssuedInCountry>
                <IssuedInCity>Madrid</IssuedInCity>
            </Document>
            <RevisionControl>
                <RateChange>true</RateChange>
                <SpecialServicesChanged>true</SpecialServicesChanged>
                <HotelChange>true</HotelChange>
                <TransportationChange>true</TransportationChange>
                <NameChange>true</NameChange>
            </RevisionControl>
            <Phone>
                <Type>Mobile</Type>
                <Number>9987521300</Number>
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
            <CabinInfo>
                <GuestCabinInfo xsi:nil="true" />
                <GuestCabinInfo xsi:nil="true" />
            </CabinInfo>
            <MultiCruiseInsurance>
                <Insurance>true</Insurance>
                <InsuranceType></InsuranceType>
            </MultiCruiseInsurance>
            <SpecialServices>
                <SpecialServices xsi:nil="true" />
                <SpecialServices xsi:nil="true" />
            </SpecialServices>
            </Guest>
        </guests>
    </ListAvailableCategories>
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
    "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ListAvailableCategories",
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
die();
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
$ListAvailableCategoriesResponse = $Body->item(0)->getElementsByTagName("ListAvailableCategoriesResponse");
if ($ListAvailableCategoriesResponse->length > 0) {
    $ListAvailableCategoriesResult = $ListAvailableCategoriesResponse->item(0)->getElementsByTagName("ListAvailableCategoriesResult");
    if ($ListAvailableCategoriesResult->length > 0) {
        $Category = $ListAvailableCategoriesResult->item(0)->getElementsByTagName("Category");
        if ($Category->length > 0) {
            for ($i=0; $i < $Category->length; $i++) { 
                $Code = $Category->item($i)->getElementsByTagName("Code");
                if ($Code->length > 0) {
                    $Code = $Code->item(0)->nodeValue;
                } else {
                    $Code = "";
                }
                $Name = $Category->item($i)->getElementsByTagName("Name");
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
                $ShipCode = $Category->item($i)->getElementsByTagName("ShipCode");
                if ($ShipCode->length > 0) {
                    $ShipCode = $ShipCode->item(0)->nodeValue;
                } else {
                    $ShipCode = "";
                }
                $Availability = $Category->item($i)->getElementsByTagName("Availability");
                if ($Availability->length > 0) {
                    $Availability = $Availability->item(0)->nodeValue;
                } else {
                    $Availability = "";
                }
                $StatusCode = $Category->item($i)->getElementsByTagName("StatusCode");
                if ($StatusCode->length > 0) {
                    $StatusCode = $StatusCode->item(0)->nodeValue;
                } else {
                    $StatusCode = "";
                }
                $CabinLocation = $Category->item($i)->getElementsByTagName("CabinLocation");
                if ($CabinLocation->length > 0) {
                    $CabinLocation = $CabinLocation->item(0)->nodeValue;
                } else {
                    $CabinLocation = "";
                }
                $URL = $Category->item($i)->getElementsByTagName("URL");
                if ($URL->length > 0) {
                    $URL = $URL->item(0)->nodeValue;
                } else {
                    $URL = "";
                }
                $IsSingleCabin = $Category->item($i)->getElementsByTagName("IsSingleCabin");
                if ($IsSingleCabin->length > 0) {
                    $IsSingleCabin = $IsSingleCabin->item(0)->nodeValue;
                } else {
                    $IsSingleCabin = "";
                }
                $MaxOccupancy = $Category->item($i)->getElementsByTagName("MaxOccupancy");
                if ($MaxOccupancy->length > 0) {
                    $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
                } else {
                    $MaxOccupancy = "";
                }
                $MinOccupancy = $Category->item($i)->getElementsByTagName("MinOccupancy");
                if ($MinOccupancy->length > 0) {
                    $MinOccupancy = $MinOccupancy->item(0)->nodeValue;
                } else {
                    $MinOccupancy = "";
                }
                $Order = $Category->item($i)->getElementsByTagName("Order");
                if ($Order->length > 0) {
                    $Order = $Order->item(0)->nodeValue;
                } else {
                    $Order = "";
                }
                $AdditionalDescription = $Category->item($i)->getElementsByTagName("AdditionalDescription");
                if ($AdditionalDescription->length > 0) {
                    $AdditionalDescription = $Order->item(0)->nodeValue;
                } else {
                    $AdditionalDescription = "";
                }
                $SuperCategoryType = $Category->item($i)->getElementsByTagName("SuperCategoryType");
                if ($SuperCategoryType->length > 0) {
                    $SuperCategoryType = $SuperCategoryType->item(0)->nodeValue;
                } else {
                    $SuperCategoryType = "";
                }
                $CurrencyCode = $Category->item($i)->getElementsByTagName("CurrencyCode");
                if ($CurrencyCode->length > 0) {
                    $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                } else {
                    $CurrencyCode = "";
                }
                $Price = $Category->item($i)->getElementsByTagName("Price");
                if ($Price->length > 0) {
                    $GuestPrice = $Price->item(0)->getElementsByTagName("GuestPrice");
                    if ($GuestPrice->length > 0) {
                        for ($iAux=0; $iAux < $GuestPrice->length; $iAux++) { 
                            $GuestPriceCode = $GuestPrice->item($iAux)->getElementsByTagName("Code");
                            if ($GuestPriceCode->length > 0) {
                                $GuestPriceCode = $GuestPriceCode->item(0)->nodeValue;
                            } else {
                                $GuestPriceCode = "";
                            }
                            $GuestPriceDescription = $GuestPrice->item($iAux)->getElementsByTagName("Description");
                            if ($GuestPriceDescription->length > 0) {
                                $GuestPriceDescription = $GuestPriceDescription->item(0)->nodeValue;
                            } else {
                                $GuestPriceDescription = "";
                            }
                            $GuestPriceAmount = $GuestPrice->item($iAux)->getElementsByTagName("Amount");
                            if ($GuestPriceAmount->length > 0) {
                                $GuestPriceAmount = $GuestPriceAmount->item(0)->nodeValue;
                            } else {
                                $GuestPriceAmount = "";
                            }
                            $PortChgAmount = $GuestPrice->item($iAux)->getElementsByTagName("PortChgAmount");
                            if ($PortChgAmount->length > 0) {
                                $PortChgAmount = $PortChgAmount->item(0)->nodeValue;
                            } else {
                                $PortChgAmount = "";
                            }
                            $AirFuelSupAmount = $GuestPrice->item($iAux)->getElementsByTagName("AirFuelSupAmount");
                            if ($AirFuelSupAmount->length > 0) {
                                $AirFuelSupAmount = $AirFuelSupAmount->item(0)->nodeValue;
                            } else {
                                $AirFuelSupAmount = "";
                            }
                            $CruiseFuelSupAmount = $GuestPrice->item($iAux)->getElementsByTagName("CruiseFuelSupAmount");
                            if ($CruiseFuelSupAmount->length > 0) {
                                $CruiseFuelSupAmount = $CruiseFuelSupAmount->item(0)->nodeValue;
                            } else {
                                $CruiseFuelSupAmount = "";
                            }
                            $GuestPriceDiscounts = $GuestPrice->item($iAux)->getElementsByTagName("Discounts");
                            if ($GuestPriceDiscounts->length > 0) {
                                $GuestPriceDiscounts = $GuestPriceDiscounts->item(0)->nodeValue;
                            } else {
                                $GuestPriceDiscounts = "";
                            }
                        }
                    }
                }
                $Ship = $Category->item($i)->getElementsByTagName("Ship");
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
                $AdditionalInfo = $Category->item($i)->getElementsByTagName("AdditionalInfo");
                if ($AdditionalInfo->length > 0) {
                    $WarningMessage = $AdditionalInfo->item(0)->getElementsByTagName("WarningMessage");
                    if ($WarningMessage->length > 0) {
                        $WarningMessage = $WarningMessage->item(0)->nodeValue;
                    } else {
                        $WarningMessage = "";
                    }
                    $InfoMessage = $AdditionalInfo->item(0)->getElementsByTagName("InfoMessage");
                    if ($InfoMessage->length > 0) {
                        $InfoMessage = $InfoMessage->item(0)->nodeValue;
                    } else {
                        $InfoMessage = "";
                    }
                }
                $CabinAvailabilityInformation = $Category->item($i)->getElementsByTagName("CabinAvailabilityInformation");
                if ($CabinAvailabilityInformation->length > 0) {
                    $NumberOfCabins = $CabinAvailabilityInformation->item(0)->getElementsByTagName("NumberOfCabins");
                    if ($NumberOfCabins->length > 0) {
                        $NumberOfCabins = $NumberOfCabins->item(0)->nodeValue;
                    } else {
                        $NumberOfCabins = "";
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