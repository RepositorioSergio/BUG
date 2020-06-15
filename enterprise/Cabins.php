<?php
error_log("\r\n Start Costa - Cabins Cruises\r\n", 3, "/srv/www/htdocs/error_log");
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
$sql = "select cruises_xml13 from cruises_regions where seo='" . $destination . "'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisedestinationid = $row_settings["cruises_xml13"];
}
foreach ($data as $key => $value) {
    if ($quote == $value['quote_id']) {
        $cruise_line_id = $value['cruise_line_id'];
        $cruise_destination_id = $value['cruise_line_id'];
        $ship_id = $value['ship']['id'];
        foreach ($value['product_id'] as $productkey => $productvalue) {
            if ($productvalue == $product) {
                $sailing_id = $value['sailingid'][$productkey];
            }
        }
        break;
    }
}
if ($cruise_line_id != "") {
    $isstate = $tmpstate === 'true' ? true : false;
    $issenior = $senior === 'true' ? true : false;
    $isinterline = $interline === 'true' ? true : false;
    $ismilitary = $military === 'true' ? true : false;
    $ispassengernumber = $tmppassengernumber === 'true' ? true : false;
    //
    // Raw, Request
    //
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
        <ListAvailableCategories xmlns="http://schemas.costacrociere.com/WebAffiliation">
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
        "SOAPAction: http://schemas.costacrociere.com/WebAffiliation/ListAvailableCategories",
        "Content-length: ".strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    //error_log("\r\n Cabins Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $db = new \Laminas\Db\Adapter\Adapter($config);
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
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
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
                        $AdditionalDescription = $AdditionalDescription->item(0)->nodeValue;
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
                    //
                    try {
                        $db = new \Laminas\Db\Adapter\Adapter($config);
                        $sql = "select name, image, description, stateroom_area, veranda_area, color from ships_cabincategory where ship_id=" . $ship_id . " and categorycode='" . $Code . "'";
                        $statement2 = $db->createStatement($sql);
                        $statement2->prepare();
                        $row_cabincategory = $statement2->execute();
                        if ($row_cabincategory->valid()) {
                            $row_cabincategory = $row_cabincategory->current();
                            $Name = $row_cabincategory["name"];
                            $img = $row_cabincategory["image"];
                            $stateroom_area = $row_cabincategory["stateroom_area"];
                            $veranda_area = $row_cabincategory["veranda_area"];
                            $color = $row_cabincategory["color"];
                            $description = $row_cabincategory["description"];
                        }
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $cabins[$cabinscount]['code'] = $Code;
                    $cabins[$cabinscount]['name'] = $Name;
                    $cabins[$cabinscount]['type'] = $type;
                    $cabins[$cabinscount]['description'] = $description;
                    $cabins[$cabinscount]['deckname'] = $decks;
                    $cabins[$cabinscount]['img'] = $img;
                    $cabins[$cabinscount]['isguaranteed'] = "";
                    $cabins[$cabinscount]['clxpolicy'] = "";
                    $cabins[$cabinscount]['dining'] = "";
                    $cabins[$cabinscount]['stateroom_area'] = $stateroom_area;
                    $cabins[$cabinscount]['veranda_area'] = $veranda_area;
                    $cabins[$cabinscount]['color'] = $color;

                    $cabincountprice = 0;
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
                                $price = $GuestPriceAmount;
                                $nettprice = $GuestPriceAmount;
                                $tax = $PortChgAmount;
                                $taxnet = $PortChgAmount;
                                if ($cruisescostamarkup > 0) {
                                    $price = number_format($price + (($price * $cruisescostamarkup) / 100), 2, '.', '');
                                }
                                if ($agent_markup > 0) {
                                    $price = number_format($price + (($price * $agent_markup) / 100), 2, '.', '');
                                }
                                if ($cruisescostamarkup > 0) {
                                    $tax = number_format($tax + (($tax * $cruisescostamarkup) / 100), 2, '.', '');
                                }
                                if ($agent_markup > 0) {
                                    $tax = number_format($tax + (($tax * $agent_markup) / 100), 2, '.', '');
                                }
                                if ($cruisescostaCurrency != $scurrency) {
                                    $price = $CurrencyConverter->convert($price, $cruisescostaCurrency, $scurrency);
                                    $tax = $CurrencyConverter->convert($tax, $cruisescostaCurrency, $scurrency);
                                    $pricepublish = $CurrencyConverter->convert($pricepublish, $cruisescostaCurrency, $scurrency);
                                }
                                if ($tax > 0) {
                                    $taxesincluded = 1;
                                } else {
                                    $taxesincluded = 0;
                                }                           
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['pricetitle'] = $Name;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['taxesincluded'] = $taxesincluded;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['pricepublish'] = $filter->filter($price);
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['price'] = $filter->filter($price);
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['pricenet'] = $nettprice;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['upgradetocategorycode'] = $upgradetocategorycode;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['cabinproductid'] = base64_encode($GuestPriceCode);
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['nonrefundable'] = $nonrefundable;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['tax'] = $tax;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['taxnet'] = $taxnet;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['currencynet'] = $scurrency;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['currency'] = $scurrency;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['categorycode'] = $Code;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['categoryname'] = $Name;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['shipcode'] = $ShipCode;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['statuscode'] = $StatusCode;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['availability'] = $Availability;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['cabinlocation'] = $CabinLocation;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['issinglecabin'] = $IsSingleCabin;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['maxoccupancy'] = $MaxOccupancy;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['minoccupancy'] = $MinOccupancy;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['order'] = $Order;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['supercategorytype'] = $SuperCategoryType;
                                $cabins[$cabinscount]['cabin'][$cabincountprice]['currencycode'] = $CurrencyCode;
                                $cabincountprice ++;
                            }
                        }
                    }
                    $cabinscount ++;
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
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\nEOF CABINS COSTA \r\n", 3, "/srv/www/htdocs/error_log");
?>