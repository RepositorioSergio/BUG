<?php
// Cruises Pullmantur
error_log("\r\nPullmantur Cabins\r\n", 3, "/srv/www/htdocs/error_log");
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
$dbPullmantur = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='cruisespullmanturusername'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturusername = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturpassword'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturpassword = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='cruisespullmanturmarkup'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturmarkup = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturServiceURL'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturCurrency'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturCurrency = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturb2cmarkup'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturb2cmarkup = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturSearchSortorder'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturSearchSortorder = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturaffiliates_id'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturaffiliates_id = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturbranchs_id'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturbranchs_id = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturConnetionTimeout'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturConnetionTimeout = (int) $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturRequestorId'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturRequestorId = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturCompanyShortName'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturCompanyShortName = $row_settings["value"];
}
$sql = "select value from settings where name='cruisespullmanturTerminalID'";
$statement = $dbPullmantur->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisespullmanturTerminalID = $row_settings["value"];
}
foreach ($data as $key => $value) {
    if ($quote == $value['quote_id']) {
        $cruise_line_id = $value['cruise_line_id'];
        $cruise_destination_id = $value['cruise_line_id'];
        $ship_id = $value['ship']['id'];
        $listofsailingdescriptioncode = $value['listofsailingdescriptioncode'];
        $duration = $value['duration'];
        $portsofcallquantity = $value['portsofcallquantity'];
        $start = $value['start'];
        $status = $value['status'];
        $shipcode = $value['shipcode'];
        $vendorcode = $value['vendorcode'];
        $regioncode = $value['regioncode'];
        $subregioncode = $value['subregioncode'];
        $departureportlocationcode = $value['departureportlocationcode'];
        $arrivalportlocationcode = $value['arrivalportlocationcode'];
        $cruisepackagecode = $value['cruisepackagecode'];
        $inclusiveindicator = $value['inclusiveindicator'];
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
    
    $raw = '<?xml version="1.0" encoding="UTF-8"?>
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cat="http://services.rccl.com/Interfaces/CategoryList" xmlns:m0="http://www.opentravel.org/OTA/2003/05/alpha">
    <soapenv:Header/>
    <soapenv:Body>
    <cat:getCategoryList>
        <OTA_CruiseCategoryAvailRQ Target="Test" MaxResponses="50" MoreIndicator="true" Version="2.0" SequenceNmbr="1" TimeStamp="2008-11-05T19:15:56.692+05:30" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
            <POS>
                <Source TerminalID="' . $cruisespullmanturTerminalID . '" ISOCurrency="' . $cruisespullmanturCurrency . '">
                    <RequestorID ID="' . $cruisespullmanturRequestorId . '" ID_Context="AGENCY1" Type="5"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="' . $cruisespullmanturCompanyShortName . '"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="' . $cruisespullmanturTerminalID . '" ISOCurrency="' . $cruisespullmanturCurrency . '">
                    <RequestorID ID="' . $cruisespullmanturRequestorId . '" ID_Context="AGENCY2" Type="5"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="' . $cruisespullmanturCompanyShortName . '"/>
                    </BookingChannel>
                </Source>
                <Source TerminalID="' . $cruisespullmanturTerminalID . '" ISOCurrency="' . $cruisespullmanturCurrency . '">
                    <RequestorID ID="' . $cruisespullmanturRequestorId . '" ID_Context="AGENT1" Type="5"/>
                    <BookingChannel Type="7">
                        <CompanyName CompanyShortName="' . $cruisespullmanturCompanyShortName . '"/>
                    </BookingChannel>
                </Source>
            </POS>
                <Guest>
                    <GuestTransportation Mode="29" Status="36"/>
                </Guest>';
                $raw .= '<GuestCount Age="30" Quantity="' . $adults . '"/>';
                if ($children > 0) {
                    for ($z = 0; $z < $children; $z ++) {
                        error_log("\r\nTODO - Children Ages\r\n", 3, "/srv/www/htdocs/error_log");
                        $raw .= '<GuestCount Age="15" Quantity="1"/>';
                    }
                }
                $raw .= '<SailingInfo>
                    <SelectedSailing ListOfSailingDescriptionCode="' . $listofsailingdescriptioncode . '" Start="' . $start . '" Duration="' . $duration . '" Status="' . $status . '" PortsOfCallQuantity="' . $portsofcallquantity . '">
                        <CruiseLine VendorCode="' . $vendorcode . '" ShipCode="' . $shipcode . '"/>
                        <!--Optional:-->
                        <Region RegionCode="' . $regioncode . '" SubRegionCode="' . $subregioncode . '"/>
                        <!--Optional:-->
                        <DeparturePort LocationCode="' . $departureportlocationcode . '"/>
                        <!--Optional:-->
                        <ArrivalPort LocationCode="' . $arrivalportlocationcode . '"/>
                    </SelectedSailing>
                    <!--Optional:-->
                    <InclusivePackageOption CruisePackageCode="' . $cruisepackagecode . '" InclusiveIndicator="' . $inclusiveindicator . '"/>
                    <!--Optional:-->
                    <Currency CurrencyCode="' . $cruisespullmanturCurrency . '" DecimalPlaces="2"/>
                </SailingInfo>
                <SelectedFare FareCode="BESTRATE"/>
            </OTA_CruiseCategoryAvailRQ>
        </cat:getCategoryList>
    </soapenv:Body>
    </soapenv:Envelope>';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cruisespullmanturServiceURL . 'Reservation_FITWeb/sca/CategoryList');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_USERPWD, $cruisespullmanturusername . ":" . $cruisespullmanturpassword);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisespullmanturConnetionTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    // error_log("\r\n Response - $response \r\n", 3, "/srv/www/htdocs/error_log");
    
    try {
        $dbPullmantur = new \Laminas\Db\Adapter\Adapter($config);
        $sql = new Sql($dbPullmantur);
        $insert = $sql->insert();
        $insert->into('log_pullmantur');
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
        $dbPullmantur->getDriver()
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
    $getCategoryListResponse = $Body->item(0)->getElementsByTagName("getCategoryListResponse");
    if ($getCategoryListResponse->length > 0) {
        $OTA_CruiseCategoryAvailRS = $getCategoryListResponse->item(0)->getElementsByTagName("OTA_CruiseCategoryAvailRS");
        if ($OTA_CruiseCategoryAvailRS->length > 0) {
            $SailingInfo = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("SailingInfo");
            if ($SailingInfo->length > 0) {
                $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
                if ($SelectedSailing->length > 0) {
                    $ListOfSailingDescriptionCode = $SelectedSailing->item(0)->getAttribute("ListOfSailingDescriptionCode");
                    $Duration = $SelectedSailing->item(0)->getAttribute("Duration");
                    
                    $CruiseLine = $SelectedSailing->item(0)->getElementsByTagName("CruiseLine");
                    if ($CruiseLine->length > 0) {
                        $ShipCode = $CruiseLine->item(0)->getAttribute("ShipCode");
                        $VendorCode = $CruiseLine->item(0)->getAttribute("VendorCode");
                    }
                    $Region = $SelectedSailing->item(0)->getElementsByTagName("Region");
                    if ($Region->length > 0) {
                        $RegionCode = $Region->item(0)->getAttribute("RegionCode");
                        $SubRegionCode = $Region->item(0)->getAttribute("SubRegionCode");
                    }
                }
                $InclusivePackageOption = $SailingInfo->item(0)->getElementsByTagName("InclusivePackageOption");
                if ($InclusivePackageOption->length > 0) {
                    $CruisePackageCode = $InclusivePackageOption->item(0)->getAttribute("CruisePackageCode");
                    $InclusiveIndicator = $InclusivePackageOption->item(0)->getAttribute("InclusiveIndicator");
                }
            }
            $Taxes = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("Taxes");
            if ($Taxes->length > 0) {
                $Tax = $Taxes->item(0)->getElementsByTagName("Tax");
                if ($Tax->length > 0) {
                    for ($j = 0; $j < $Tax->length; $j ++) {
                        $Amount = $Tax->item($j)->getAttribute("Amount");
                        $tax = $Amount;
                    }
                }
            }
            $FareOption = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("FareOption");
            if ($FareOption->length > 0) {
                $CategoryOptions = $FareOption->item(0)->getElementsByTagName("CategoryOptions");
                if ($CategoryOptions->length > 0) {
                    $CategoryOption = $CategoryOptions->item(0)->getElementsByTagName("CategoryOption");
                    if ($CategoryOption->length > 0) {
                        for ($i = 0; $i < $CategoryOption->length; $i ++) {
                            $AvailableGroupAllocationQty = $CategoryOption->item($i)->getAttribute("AvailableGroupAllocationQty");
                            $AvailableRegularCabins = $CategoryOption->item($i)->getAttribute("AvailableRegularCabins");
                            $CategoryLocation = $CategoryOption->item($i)->getAttribute("CategoryLocation");
                            $GroupCode = $CategoryOption->item($i)->getAttribute("GroupCode");
                            $ListOfCategoryQualifierCodes = $CategoryOption->item($i)->getAttribute("ListOfCategoryQualifierCodes");
                            $PricedCategoryCode = $CategoryOption->item($i)->getAttribute("PricedCategoryCode");
                            $Status = $CategoryOption->item($i)->getAttribute("Status");
                            
                            if ($GroupCode != "") {
                                try {
                                    $dbPullmantur = new \Laminas\Db\Adapter\Adapter($config);
                                    $sql = "select name, image, description, stateroom_area, veranda_area, color from ships_cabincategory where ship_id=" . $ship_id . " and categorycode='" . $PricedCategoryCode . "'";
                                    $statement2 = $dbPullmantur->createStatement($sql);
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
                                    $dbPullmantur->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (Exception $e) {
                                    $logger = new Logger();
                                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                    $logger->addWriter($writer);
                                    $logger->info($e->getMessage());
                                }
                                $cabins[$cabinscount]['code'] = $PricedCategoryCode;
                                $cabins[$cabinscount]['name'] = $Name;
                                $cabins[$cabinscount]['type'] = $type;
                                $cabins[$cabinscount]['description'] = $description;
                                $cabins[$cabinscount]['deckname'] = $deckname;
                                $cabins[$cabinscount]['img'] = $img;
                                $cabins[$cabinscount]['isguaranteed'] = "";
                                $cabins[$cabinscount]['clxpolicy'] = "";
                                $cabins[$cabinscount]['dining'] = "";
                                $cabins[$cabinscount]['stateroom_area'] = $stateroom_area;
                                $cabins[$cabinscount]['veranda_area'] = $veranda_area;
                                $cabins[$cabinscount]['color'] = $color;
                                $cabins[$cabinscount]['categorylocation'] = $CategoryLocation;
                                $cabins[$cabinscount]['groupcode'] = $GroupCode;
                                $cabins[$cabinscount]['pricedcategorycode'] = $PricedCategoryCode;
                                $cabins[$cabinscount]['status'] = $Status;
                                
                                $cabincountprice = 0;
                                $PriceInfos = $CategoryOption->item($i)->getElementsByTagName("PriceInfos");
                                if ($PriceInfos->length > 0) {
                                    $PriceInfo = $PriceInfos->item(0)->getElementsByTagName("PriceInfo");
                                    if ($PriceInfo->length > 0) {
                                        $Amount = $PriceInfo->item(0)->getAttribute("Amount");
                                        $AppliedPromotionsQuantity = $PriceInfo->item(0)->getAttribute("AppliedPromotionsQuantity");
                                        $NetAmount = $PriceInfo->item(0)->getAttribute("NetAmount");
                                        $NonRefundableType = $PriceInfo->item(0)->getAttribute("NonRefundableType");
                                        $PriceId = $PriceInfo->item(0)->getAttribute("PriceId");
                                        $PriceIdType = $PriceInfo->item(0)->getAttribute("PriceIdType");
                                        $PromotionClass = $PriceInfo->item(0)->getAttribute("PromotionClass");
                                        $PromotionDescription = $PriceInfo->item(0)->getAttribute("PromotionDescription");
                                        $PromotionTypes = $PriceInfo->item(0)->getAttribute("PromotionTypes");
                                        $FareCode = $PriceInfo->item(0)->getAttribute("FareCode");
                                        $cabins[$cabinscount]['farecode'] = $FareCode;
                                        
                                        if ($cruisespullmanturmarkup > 0) {
                                            $Amount = number_format($Amount + (($Amount * $cruisespullmanturmarkup) / 100), 2, '.', '');
                                        }
                                        if ($agent_markup > 0) {
                                            $Amount = number_format($Amount + (($Amount * $agent_markup) / 100), 2, '.', '');
                                        }
                                        if ($cruisespullmanturmarkup > 0) {
                                            $tax = number_format($tax + (($tax * $cruisespullmanturmarkup) / 100), 2, '.', '');
                                        }
                                        if ($agent_markup > 0) {
                                            $tax = number_format($tax + (($tax * $agent_markup) / 100), 2, '.', '');
                                        }
                                        $PriceDescription = $PriceInfo->item(0)->getElementsByTagName("PriceDescription");
                                        if ($PriceDescription->length > 0) {
                                            $PriceDescription = $PriceDescription->item(0)->nodeValue;
                                        } else {
                                            $PriceDescription = "";
                                        }
                                        $taxnet = $tax;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['pricetitle'] = $PriceDescription;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['pricepublish'] = $filter->filter($Amount);
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['price'] = $filter->filter($Amount);
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['pricenet'] = $NetAmount;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['upgradetocategorycode'] = $upgradetocategorycode;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['cabinproductid'] = base64_encode($PricedCategoryCode);
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['nonrefundable'] = $NonRefundableType;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['tax'] = $tax;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['taxnet'] = $taxnet;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['currencynet'] = $scurrency;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['currency'] = $scurrency;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['farecode'] = $FareCode;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['categorylocation'] = $CategoryLocation;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['groupcode'] = $GroupCode;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['pricedcategorycode'] = $PricedCategoryCode;
                                        $cabins[$cabinscount]['cabin'][$cabincountprice]['status'] = $Status;
                                        $cabincountprice ++;
                                        $PriceBreakDowns = $PriceInfo->item(0)->getElementsByTagName("PriceBreakDowns");
                                        if ($PriceBreakDowns->length > 0) {
                                            $Occupancy = $PriceBreakDowns->item(0)->getAttribute("Occupancy");
                                            $PriceBreakDownsStatus = $PriceBreakDowns->item(0)->getAttribute("Status");
                                            $PriceBreakDown = $PriceBreakDowns->item(0)->getElementsByTagName("PriceBreakDown");
                                            if ($PriceBreakDown->length > 0) {
                                                for ($iAux = 0; $iAux < $PriceBreakDown->length; $iAux ++) {
                                                    $AgeQualifyingCode = $PriceBreakDown->item($iAux)->getAttribute("AgeQualifyingCode");
                                                    $PriceBreakDownAmount = $PriceBreakDown->item($iAux)->getAttribute("Amount");
                                                    $NCCFAmount = $PriceBreakDown->item($iAux)->getAttribute("NCCFAmount");
                                                    $PriceBreakDownNetAmount = $PriceBreakDown->item($iAux)->getAttribute("NetAmount");
                                                    $RPH = $PriceBreakDown->item($iAux)->getAttribute("RPH");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $cabinscount ++;
                        }
                    }
                }
            }
            $Fee = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("Fee");
            if ($Fee->length > 0) {
                $TaxInclusive = $Fee->item(0)->getAttribute("TaxInclusive");
                $Taxes = $Fee->item(0)->getElementsByTagName("Taxes");
                if ($Taxes->length > 0) {
                    $Tax = $Taxes->item(0)->getElementsByTagName("Tax");
                    if ($Tax->length > 0) {
                        for ($j = 0; $j < $Tax->length; $j ++) {
                            $Amount = $Tax->item($j)->getAttribute("Amount");
                        }
                    }
                }
            }
            $Information = $OTA_CruiseCategoryAvailRS->item(0)->getElementsByTagName("Information");
            if ($Information->length > 0) {
                $Name = $Information->item(0)->getAttribute("Name");
                $Text = $Information->item(0)->getElementsByTagName("Text");
                if ($Text->length > 0) {
                    $Text = $Text->item(0)->nodeValue;
                } else {
                    $Text = "";
                }
            }
        }
    }
}
$dbPullmantur->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\n EOF CABINS  \r\n", 3, "/srv/www/htdocs/error_log");
?>