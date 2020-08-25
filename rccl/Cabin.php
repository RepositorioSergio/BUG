<?php
// Cruises RCCL
error_log("\r\nRCCL Cabin\r\n", 3, "/srv/www/htdocs/error_log");
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
$sql = "select value from settings where name='enablecruisesroyalcaribbean' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_cruisesroyalcaribbean = $affiliate_id;
} else {
    $affiliate_id_cruisesroyalcaribbean = 0;
}
$sql = "select value from settings where name='cruisesroyalcaribbeanusername' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisesroyalcaribbeanusername = $row_settings['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanpassword' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisesroyalcaribbeanpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisesroyalcaribbeanServiceURL' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanServiceURL = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanCompanyShortName' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanCompanyShortName = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanRequestorId' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanRequestorId = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanTerminalID' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanTerminalID = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanSearchSortorder' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanbranchs_id' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanbranchs_id = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanmarkup' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanmarkup = (double) $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanb2cmarkup' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanb2cmarkup = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanaffiliates_id' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanConnetionTimeout' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanConnetionTimeout = (int) $row['value'];
}
$sql = "select value from settings where name='cruisesroyalcaribbeanCurrency' and affiliate_id=$affiliate_id_cruisesroyalcaribbean";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisesroyalcaribbeanCurrency = $row['value'];
}
foreach ($data as $key => $value) {
    if ($quote == $value['quote_id']) {
        $cruise_line_id = $value['cruise_line_id'];
        $cruise_destination_id = $value['cruise_destination_id'];
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
$categorylocation = $selectedcabin['cabin']['categorylocation'];
$groupcode = $selectedcabin['cabin']['groupcode'];
$pricedcategorycode = $selectedcabin['cabin']['pricedcategorycode'];
$statuscabin = $selectedcabin['cabin']['status'];
$farecode = $selectedcabin['cabin']['farecode'];
if ($cruise_line_id != "") {
    $raw = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cab="http://services.rccl.com/Interfaces/CabinList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha"><soapenv:Header/><soapenv:Body><cab:getCabinList><OTA_CruiseCabinAvailRQ MaxResponses="50" MoreDataEchoToken="01" Target="Test" RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="' . strftime("%Y-%m-%dT%H:%M:%S", time()) . '" TransactionIdentifier="106597" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
                <POS>
                    <Source ISOCurrency="' . $cruisesroyalcaribbeanCurrency . '" TerminalID="' . $cruisesroyalcaribbeanTerminalID . '">
                        <RequestorID ID="' . $cruisesroyalcaribbeanRequestorId . '" Type="11" ID_Context="AGENCY1"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="' . $cruisesroyalcaribbeanCompanyShortName . '"/>
                        </BookingChannel>
                    </Source>
                    <Source ISOCurrency="' . $cruisesroyalcaribbeanCurrency . '" TerminalID="' . $cruisesroyalcaribbeanTerminalID . '">
                        <RequestorID ID="' . $cruisesroyalcaribbeanRequestorId . '" Type="11" ID_Context="AGENCY2"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="' . $cruisesroyalcaribbeanCompanyShortName . '"/>
                        </BookingChannel>
                    </Source>
                    <Source ISOCurrency="' . $cruisesroyalcaribbeanCurrency . '" TerminalID="' . $cruisesroyalcaribbeanTerminalID . '">
                        <RequestorID ID="' . $cruisesroyalcaribbeanRequestorId . '" Type="11" ID_Context="AGENT1"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="' . $cruisesroyalcaribbeanCompanyShortName . '"/>
                        </BookingChannel>
                    </Source>
                </POS>
                <Guest><GuestTransportation Mode="29" Status="36"/></Guest><GuestCounts>';
    $raw .= '<GuestCount Age="30" Quantity="' . $adults . '"/>';
    if ($children > 0) {
        for ($z = 0; $z < $children; $z ++) {
            error_log("\r\nTODO - Children Ages\r\n", 3, "/srv/www/htdocs/error_log");
            $raw .= '<GuestCount Age="15" Quantity="1"/>';
        }
    }
    $raw .= '</GuestCounts><SailingInfo>
                    <SelectedSailing ListOfSailingDescriptionCode="' . $listofsailingdescriptioncode . '" Start="' . $start . '" Duration="' . $duration . '" Status="' . $status . '" PortsOfCallQuantity="' . $portsofcallquantity . '">
                        <CruiseLine VendorCode="' . $vendorcode . '" ShipCode="' . $shipcode . '"/>
                        <!--Optional:-->
                        <Region RegionCode="' . $regioncode . '" SubRegionCode="' . $subregioncode . '"/>
                        <!--Optional:-->
                        <DeparturePort LocationCode="' . $departureportlocationcode . '"/>
                        <!--Optional:-->
                        <ArrivalPort LocationCode="' . $arrivalportlocationcode . '"/>
                    </SelectedSailing>
                    <InclusivePackageOption CruisePackageCode="' . $cruisepackagecode . '"/>
                    <Currency CurrencyCode="' . $cruisesroyalcaribbeanCurrency . '" DecimalPlaces="2"/>
                    <SelectedCategory BerthedCategoryCode="' . $pricedcategorycode . '" PricedCategoryCode="' . $pricedcategorycode . '" WaitlistIndicator="false">
                    </SelectedCategory>
                </SailingInfo>
                <SearchQualifiers BerthedCategoryCode="' . $pricedcategorycode . '" FareCode="' . $farecode . '" GroupCode="' . $groupcode . '" CategoryLocation="' . $categorylocation . '">
                    <Status Status="' . $status . '"/>
                </SearchQualifiers>
                <SelectedFare FareCode="' . $farecode . '" GroupCode="' . $groupcode . '"/>
            </OTA_CruiseCabinAvailRQ></cab:getCabinList></soapenv:Body></soapenv:Envelope>';
    error_log("\r\nRCC Response - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cruisesroyalcaribbeanServiceURL . 'Reservation_FITWeb/sca/CabinList');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_USERPWD, $cruisesroyalcaribbeanusername . ":" . $cruisesroyalcaribbeanpassword);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisesroyalcaribbeanConnetionTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    error_log("\r\nRCC Response - $response\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_rccl');
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
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if ($response != "") {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $getCabinListResponse = $Body->item(0)->getElementsByTagName("getCabinListResponse");
        if ($getCabinListResponse->length > 0) {
            $OTA_CruiseCabinAvailRS = $getCabinListResponse->item(0)->getElementsByTagName("OTA_CruiseCabinAvailRS");
            if ($OTA_CruiseCabinAvailRS->length > 0) {
                $SailingInfo = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("SailingInfo");
                if ($SailingInfo->length > 0) {
                    $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
                    if ($SelectedSailing->length > 0) {
                        $Start = $SelectedSailing->item(0)->getAttribute("Start");
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
                // SelectedFare
                $SelectedFare = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("SelectedFare");
                if ($SelectedFare->length > 0) {
                    $FareCode = $SelectedFare->item(0)->getAttribute("FareCode");
                }
                // CabinOptions
                $CabinOptions = $OTA_CruiseCabinAvailRS->item(0)->getElementsByTagName("CabinOptions");
                if ($CabinOptions->length > 0) {
                    $CabinOption = $CabinOptions->item(0)->getElementsByTagName("CabinOption");
                    if ($CabinOption->length > 0) {
                        for ($i = 0; $i < $CabinOption->length; $i ++) {
                            $CabinCategoryCode = $CabinOption->item($i)->getAttribute("CabinCategoryCode");
                            $decks[$i]['cabinnumber'] = $CabinOption->item($i)->getAttribute("CabinNumber");
                            $CabinRanking = $CabinOption->item($i)->getAttribute("CabinRanking");
                            $decks[$i]['deckname'] = $CabinOption->item($i)->getAttribute("DeckName");
                            $decks[$i]['decknumber'] = $CabinOption->item($i)->getAttribute("DeckNumber");
                            $MaxOccupancy = $CabinOption->item($i)->getAttribute("MaxOccupancy");
                            $PositionInShip = $CabinOption->item($i)->getAttribute("PositionInShip");
                            $Status = $CabinOption->item($i)->getAttribute("Status");
                            $Remark = $CabinOption->item($i)->getElementsByTagName("Remark");
                            if ($Remark->length > 0) {
                                $Remark = $Remark->item(0)->nodeValue;
                            } else {
                                $Remark = "";
                            }
                            $sql = "select image from ships_decksimages where ship_id=$ship_id and categorycode='" . $selectedcabin['code'] . "'";
                            $statement = $db->createStatement($sql);
                            $statement->prepare();
                            $row_settings = $statement->execute();
                            $row_settings->buffer();
                            if ($row_settings->valid()) {
                                $row_settings = $row_settings->current();
                                $decks[$i]['deckimg'] = $row_settings['image'];
                            }
                            $CabinConfiguration = $CabinOption->item($i)->getElementsByTagName("CabinConfiguration");
                            if ($CabinConfiguration->length > 0) {
                                for ($iAux3 = 0; $iAux3 < $CabinConfiguration->length; $iAux3 ++) {
                                    $BedConfigurationCode = $CabinConfiguration->item($iAux3)->getAttribute("BedConfigurationCode");
                                    if ($iAux3 == 1) {
                                        $TPA_ViewObstruction = $CabinConfiguration->item($iAux3)->getAttribute("TPA_ViewObstruction");
                                    }
                                }
                            }
                            $MeasurementInfo = $CabinOption->item($i)->getElementsByTagName("MeasurementInfo");
                            if ($MeasurementInfo->length > 0) {
                                for ($iAux = 0; $iAux < $MeasurementInfo->length; $iAux ++) {
                                    $Name = $MeasurementInfo->item($iAux)->getAttribute("Name");
                                    $UnitOfMeasure = $MeasurementInfo->item($iAux)->getAttribute("UnitOfMeasure");
                                    $UnitOfMeasureCode = $MeasurementInfo->item($iAux)->getAttribute("UnitOfMeasureCode");
                                    $UnitOfMeasureQuantity = $MeasurementInfo->item($iAux)->getAttribute("UnitOfMeasureQuantity");
                                }
                            }
                            $CabinFilters = $CabinOption->item($i)->getElementsByTagName("CabinFilters");
                            if ($CabinFilters->length > 0) {
                                $CabinFilter = $CabinFilters->item(0)->getElementsByTagName("CabinFilter");
                                if ($CabinFilter->length > 0) {
                                    for ($iAux2 = 0; $iAux2 < $CabinFilter->length; $iAux2 ++) {
                                        $CabinFilterCode = $CabinFilter->item($iAux2)->getAttribute("CabinFilterCode");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //
        // Dining
        //
        $raw2 = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:din="http://services.rccl.com/Interfaces/DiningList" xmlns:alp="http://www.opentravel.org/OTA/2003/05/alpha"><soapenv:Header/><soapenv:Body><din:getDiningList><OTA_CruiseDiningAvailRQ RetransmissionIndicator="false" SequenceNmbr="1" TimeStamp="2008-12-29T18:25:50.1Z" TransactionIdentifier="106597" Version="1.0" Target="Test" xmlns="http://www.opentravel.org/OTA/2003/05/alpha">
                <POS>
                    <Source ISOCurrency="' . $cruisesroyalcaribbeanCurrency . '" TerminalID="' . $cruisesroyalcaribbeanTerminalID . '">
                        <RequestorID ID="' . $cruisesroyalcaribbeanRequestorId . '" Type="11" ID_Context="AGENCY1"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="' . $cruisesroyalcaribbeanCompanyShortName . '"/>
                        </BookingChannel>
                    </Source>
                    <Source ISOCurrency="' . $cruisesroyalcaribbeanCurrency . '" TerminalID="' . $cruisesroyalcaribbeanTerminalID . '">
                        <RequestorID ID="' . $cruisesroyalcaribbeanRequestorId . '" Type="11" ID_Context="AGENCY2"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="' . $cruisesroyalcaribbeanCompanyShortName . '"/>
                        </BookingChannel>
                    </Source>
                    <Source ISOCurrency="' . $cruisesroyalcaribbeanCurrency . '" TerminalID="' . $cruisesroyalcaribbeanTerminalID . '">
                        <RequestorID ID="' . $cruisesroyalcaribbeanRequestorId . '" Type="11" ID_Context="AGENT1"/>
                        <BookingChannel Type="7">
                            <CompanyName CompanyShortName="' . $cruisesroyalcaribbeanCompanyShortName . '"/>
                        </BookingChannel>
                    </Source>
                </POS>
                <Guest>
                    <GuestTransportation Mode="29" Status="36"/>
                </Guest>';
        for ($r = 0; $r < count($adults); $r ++) {
            $raw2 .= '<alp:GuestCounts>
                    <alp:GuestCount Age="30" Quantity="' . $adults . '"/>';
            if ($children > 0) {
                for ($z = 0; $z < $children; $z ++) {
                    $raw2 .= '<alp:GuestCount Age="' . $children_ages[$r][$z] . '" Quantity="1"/>';
                }
            }
            $raw2 .= '</alp:GuestCounts>';
        }
        $raw2 .= '<SailingInfo>
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
                    <alp:InclusivePackageOption CruisePackageCode="' . $cruisepackagecode . '" InclusiveIndicator="false"/>
                    <!--Optional:-->
                    <Currency CurrencyCode="' . $cruisesroyalcaribbeanCurrency . '" DecimalPlaces="2"/>
                    <SelectedCategory BerthedCategoryCode="' . $pricedcategorycode . '" PricedCategoryCode="' . $pricedcategorycode . '"/>
                </SailingInfo>
                <SelectedFare FareCode="' . $farecode . '" GroupCode="' . $groupcode . '"/>
                <TPA_ReservationId Type="14" ID="0"/>
            </OTA_CruiseDiningAvailRQ>
        </din:getDiningList></soapenv:Body></soapenv:Envelope>';
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $cruisesroyalcaribbeanServiceURL . 'Reservation_FITWeb/sca/DiningList');
        curl_setopt($ch2, CURLOPT_HEADER, false);
        curl_setopt($ch2, CURLOPT_VERBOSE, false);
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch2, CURLOPT_POST, true);
        curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
        curl_setopt($ch2, CURLOPT_USERPWD, $cruisesroyalcaribbeanusername . ":" . $cruisesroyalcaribbeanpassword);
        curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, $cruisesroyalcaribbeanConnetionTimeout);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_ENCODING, 'gzip');
        $response2 = curl_exec($ch2);
        $error = curl_error($ch2);
        $headers = curl_getinfo($ch2);
        curl_close($ch2);
        if ($response2 != "") {
            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($response2);
            $Envelope = $inputDoc->getElementsByTagName("Envelope");
            $Body = $Envelope->item(0)->getElementsByTagName("Body");
            $getDiningListResponse = $Body->item(0)->getElementsByTagName("getDiningListResponse");
            if ($getDiningListResponse->length > 0) {
                $OTA_CruiseDiningAvailRS = $getDiningListResponse->item(0)->getElementsByTagName("OTA_CruiseDiningAvailRS");
                if ($OTA_CruiseDiningAvailRS->length > 0) {
                    $SailingInfo = $OTA_CruiseDiningAvailRS->item(0)->getElementsByTagName("SailingInfo");
                    if ($SailingInfo->length > 0) {
                        $SelectedSailing = $SailingInfo->item(0)->getElementsByTagName("SelectedSailing");
                        if ($SelectedSailing->length > 0) {
                            $Start = $SelectedSailing->item(0)->getAttribute("Start");
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
                    $hasdining = false;
                    $DiningOptions = $OTA_CruiseDiningAvailRS->item(0)->getElementsByTagName("DiningOptions");
                    if ($DiningOptions->length > 0) {
                        $DiningOption = $DiningOptions->item(0)->getElementsByTagName("DiningOption");
                        if ($DiningOption->length > 0) {
                            for ($i = 0; $i < $DiningOption->length; $i ++) {
                                $hasdining = true;
                                $CrossReferencingAllowed = $DiningOption->item($i)->getAttribute("CrossReferencingAllowed");
                                $FamilyTimeIndicator = $DiningOption->item($i)->getAttribute("FamilyTimeIndicator");
                                $PrepaidGratuityRequired = $DiningOption->item($i)->getAttribute("PrepaidGratuityRequired");
                                $dining[$i]['diningcode'] = $DiningOption->item($i)->getAttribute("Sitting");
                                $dining[$i]['diningname'] = $DiningOption->item($i)->getAttribute("SittingInstance");
                                $dining[$i]['status'] = $DiningOption->item($i)->getAttribute("SittingStatus");
                                $SittingType = $DiningOption->item($i)->getAttribute("SittingType");
                                $SmokingAllowed = $DiningOption->item($i)->getAttribute("SmokingAllowed");
                            }
                        }
                        $TPA_DiningProfileInfo = $DiningOptions->item(0)->getElementsByTagName("TPA_DiningProfileInfo");
                        if ($TPA_DiningProfileInfo->length > 0) {
                            $RuleLevel = $TPA_DiningProfileInfo->item(0)->getAttribute("RuleLevel");
                            $RuleName = $TPA_DiningProfileInfo->item(0)->getAttribute("RuleName");
                            $DiningProfile = $TPA_DiningProfileInfo->item(0)->getElementsByTagName("DiningProfile");
                            if ($DiningProfile->length > 0) {
                                $Code = $DiningProfile->item(0)->getAttribute("Code");
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
error_log("\r\nEOF RCCL CABIN\r\n", 3, "/srv/www/htdocs/error_log");
?>