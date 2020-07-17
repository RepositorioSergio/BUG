<?php
error_log("\r\Start Costa - Cabin Cruises\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat);
$db = new \Laminas\Db\Adapter\Adapter($config);
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablecruisescosta' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_costa = $affiliate_id;
} else {
    $affiliate_id_costa = 0;
}
$sql = "select value from settings where name='cruisescostausername' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisescostausername = $row_settings['value'];
}
$sql = "select value from settings where name='cruisescostapassword' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisescostapassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisescostaServiceURL' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaServiceURL = $row['value'];
}
$sql = "select value from settings where name='cruisescostaAgency' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaAgency = $row['value'];
}
$sql = "select value from settings where name='cruisescostaAgency' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaAgency = $row['value'];
}
$sql = "select value from settings where name='cruisescostaSearchSortorder' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='cruisescostabranchs_id' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostabranchs_id = $row['value'];
}
$sql = "select value from settings where name='cruisescostamarkup' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostamarkup = (double) $row['value'];
}
$sql = "select value from settings where name='cruisescostab2cmarkup' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostab2cmarkup = $row['value'];
}
$sql = "select value from settings where name='cruisescostaaffiliates_id' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='cruisescostaConnetionTimeout' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaConnetionTimeout = (int) $row['value'];
}
$sql = "select value from settings where name='cruisescostaCurrency' and affiliate_id=$affiliate_id_costa";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisescostaCurrency = $row['value'];
}
foreach ($data as $key => $value) {
    if ($quote == $value['quote_id']) {
        $cruise_line_id = $value['cruise_line_id'];
        $cruise_destination_id = $value['cruise_destination_id'];
        $ship_id = $value['ship']['id'];
        foreach ($value['product_id'] as $productkey => $productvalue) {
            if ($productvalue == $product) {
                $sailing_id = $value['sailingid'][$productkey];
            }
        }
        break;
    }
}
$categorycode = $selectedcabin['cabin']['categorycode'];
$categoryname = $selectedcabin['cabin']['categoryname'];
$shipcode = $selectedcabin['cabin']['shipcode'];
$statuscode = $selectedcabin['cabin']['statuscode'];
$availability = $selectedcabin['cabin']['availability'];
$cabinlocation = $selectedcabin['cabin']['cabinlocation'];
$issinglecabin = $selectedcabin['cabin']['issinglecabin'];
$maxoccupancy = $selectedcabin['cabin']['maxoccupancy'];
$minoccupancy = $selectedcabin['cabin']['minoccupancy'];
$order = $selectedcabin['cabin']['order'];
$supercategorytype = $selectedcabin['cabin']['supercategorytype'];
$currencycode = $selectedcabin['cabin']['currencycode'];
if ($cruise_line_id != "") {
    $hasstate = $cabinsearchsettings['state'] === 'true' ? true : false;
    $issenior = $cabinsearchsettings['senior'] === 'true' ? true : false;
    $isinterline = $cabinsearchsettings['interline'] === 'true' ? true : false;
    $ismilitary = $cabinsearchsettings['military'] === 'true' ? true : false;
    $raw = '<?xml version="1.0" encoding="utf-8"?>
    <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Header>
        <Agency xmlns="http://schemas.costacrociere.com/WebAffiliation">
        <Code>' . $cruisescostaAgency . '</Code>
        </Agency>
        <Partner xmlns="http://schemas.costacrociere.com/WebAffiliation">
        <Name>' . $cruisescostausername . '</Name>
        <Password>' . $cruisescostapassword . '</Password>
        </Partner>
    </soap:Header>
    <soap:Body>
        <ListAvailableCabins xmlns="http://schemas.costacrociere.com/WebAffiliation">
        <components>
            <Component>
            <Type>Cruise</Type>
            <Code>' . $cruise_line_id . '</Code>
            <Fare>
                <Code>BASIC</Code>
                <Description>Basic</Description>
                <IsAllowedToBeChanged></IsAllowedToBeChanged>
                <AdditionalInfoRequired></AdditionalInfoRequired>
                <PromoFlights xsi:nil="true" />
                <PromoBuses xsi:nil="true" />
            </Fare>
            <Category>
                <Code>' . $categorycode . '</Code>
                <Name>' . $categoryname . '</Name>
                <ShipCode>' . $shipcode . '</ShipCode>
                <Availability>' . $availability . '</Availability>
                <StatusCode>' . $statuscode . '</StatusCode>
                <Price xsi:nil="true" />
                <CabinLocation>' . $cabinlocation . '</CabinLocation>
                <Ship xsi:nil="true" />
                <URL>https://training.costaextra.it</URL>
                <AdditionalInfo xsi:nil="true" />
                <IsSingleCabin>' . $issinglecabin . '</IsSingleCabin>
                <MaxOccupancy>' . $maxoccupancy . '</MaxOccupancy>
                <MinOccupancy>' . $minoccupancy . '</MinOccupancy>
                <UpgradeCode></UpgradeCode>
                <Order>' . $order . '</Order>
                <CabinAvailabilityInformation xsi:nil="true" />
                <AdditionalDescription></AdditionalDescription>
                <SuperCategoryType>' . $supercategorytype . '</SuperCategoryType>
                <CurrencyCode>' . $currencycode . '</CurrencyCode>
                <Scores xsi:nil="true" />
            </Category>
            <Cabin>
                <Number></Number>
                <Category xsi:nil="true" />
                <Status></Status>
                <MinOccupancy>4</MinOccupancy>
                <MaxOccupancy>1</MaxOccupancy>
                <DeckName></DeckName>
                <DeckCode></DeckCode>
                <Beds xsi:nil="true" />
                <Facility>true</Facility>
                <DiningPreference>Main</DiningPreference>
                <Cruise xsi:nil="true" />
                <URL></URL>
                <RateInformation xsi:nil="true" />
                <DiningWithInformation xsi:nil="true" />
                <AdditionalInfo xsi:nil="true" />
                <GuestsCabinInfo xsi:nil="true" />
                <RestaurantInfo xsi:nil="true" />
                <DiningSatisfaction></DiningSatisfaction>
                <ServiceLevel></ServiceLevel>
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
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cruisescostaServiceURL . 'Availability.asmx');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate");
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisescostaConnetionTimeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: text/xml;charset=\"utf-8\"",
        "Accept: text/xml",
        "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ListAvailableCabins",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    // error_log("\r\n Cabin Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('log_costa');
    $insert->values(array(
        'datetime_created' => time(),
        'filename' => 'Cabins.php',
        'errorline' => 0,
        'errormessage' => $raw,
        'sqlcontext' => $response,
        'errcontext' => ''
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }

    $hasdining = false;
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
                for ($z = 0; $z < $Cabin->length; $z ++) {
                    $Number = $Cabin->item($z)->getElementsByTagName("Number");
                    if ($Number->length > 0) {
                        $Number = $Number->item(0)->nodeValue;
                    } else {
                        $Number = "";
                    }
                    $Status = $Cabin->item($z)->getElementsByTagName("Status");
                    if ($Status->length > 0) {
                        $Status = $Status->item(0)->nodeValue;
                    } else {
                        $Status = "";
                    }
                    $MinOccupancy = $Cabin->item($z)->getElementsByTagName("MinOccupancy");
                    if ($MinOccupancy->length > 0) {
                        $MinOccupancy = $MinOccupancy->item(0)->nodeValue;
                    } else {
                        $MinOccupancy = "";
                    }
                    $MaxOccupancy = $Cabin->item($z)->getElementsByTagName("MaxOccupancy");
                    if ($MaxOccupancy->length > 0) {
                        $MaxOccupancy = $MaxOccupancy->item(0)->nodeValue;
                    } else {
                        $MaxOccupancy = "";
                    }
                    $DeckName = $Cabin->item($z)->getElementsByTagName("DeckName");
                    if ($DeckName->length > 0) {
                        $DeckName = $DeckName->item(0)->nodeValue;
                    } else {
                        $DeckName = "";
                    }
                    $DeckCode = $Cabin->item($z)->getElementsByTagName("DeckCode");
                    if ($DeckCode->length > 0) {
                        $DeckCode = $DeckCode->item(0)->nodeValue;
                    } else {
                        $DeckCode = "";
                    }
                    $decks[$z]['cabinnumber'] = $Number;
                    $decks[$z]['deckname'] = $DeckName;
                    $decks[$z]['decknumber'] = $DeckCode;
                    $sql = "select image from ships_decksimages where ship_id=$ship_id and categorycode='" . $selectedcabin['code'] . "'";
                    $statement = $db->createStatement($sql);
                    $statement->prepare();
                    $row_settings = $statement->execute();
                    $row_settings->buffer();
                    if ($row_settings->valid()) {
                        $row_settings = $row_settings->current();
                        $decks[$z]['deckimg'] = $row_settings['image'];
                    }
                    $Facility = $Cabin->item($z)->getElementsByTagName("Facility");
                    if ($Facility->length > 0) {
                        $Facility = $Facility->item(0)->nodeValue;
                    } else {
                        $Facility = "";
                    }
                    $DiningPreference = $Cabin->item($z)->getElementsByTagName("DiningPreference");
                    if ($DiningPreference->length > 0) {
                        $DiningPreference = $DiningPreference->item(0)->nodeValue;
                        $hasdining = true;
                        $dining[$z]['diningname'] = $DiningPreference;
                    } else {
                        $DiningPreference = "";
                    }
                    $URL = $Cabin->item($z)->getElementsByTagName("URL");
                    if ($URL->length > 0) {
                        $URL = $URL->item(0)->nodeValue;
                    } else {
                        $URL = "";
                    }
                    $Category = $Cabin->item($z)->getElementsByTagName("Category");
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
                    $Cruise = $Cabin->item($z)->getElementsByTagName("Cruise");
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
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>