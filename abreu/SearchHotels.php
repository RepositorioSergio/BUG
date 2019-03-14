<?php
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$abreu = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
$sql = "select city_xml41 from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml41 = $row_settings["city_xml41"];
} else {
    $city_xml41 = "";
}

if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='AbreuDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_abreu";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='AbreuUsername' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuUsername = $row_settings['value'];
}

$sql = "select value from settings where name='Abreupassword' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreupassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='AbreuMarkup' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuMarkup = (double) $row_settings['value'];
} else {
    $AbreuMarkup = 0;
}
// URL
$sql = "select value from settings where name='AbreuHOTELAVAILABILITY' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuHOTELAVAILABILITY = $row_settings['value'];
}

$sql = "select value from settings where name='AbreuCustomerID' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuCustomerID = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuOnRequest' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuOnRequest = $row_settings['value'];
}

$sql = "select value from settings where name='Abreub2cMarkup' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreub2cMarkup = $row_settings['value'];
}

for ($rAux = 0; $rAux < $rooms; $rAux ++) {
    $auxArray[$rAux]['qty'] = 1;
    $auxArray[$rAux]['selectedAdults'] = $selectedAdults[$rAux];
    $auxArray[$rAux]['selectedChildren'] = $selectedChildren[$rAux];
}
// error_log("\r\n DEPOIS FOR " . count($auxArray) . " \r\n", 3, "/srv/www/htdocs/error_log");

$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
$dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
$noOfNights = $dateStart->diff($dateEnd)->format('%d');
$date = new Datetime();
$timestamp = $date->format('U');
// error_log("\r\n ANTES RAW \r\n", 3, "/srv/www/htdocs/error_log");

if ($city_xml41 != "") {
    
    $raw = '<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA ../../../html/OTASchemas/OTA_HotelAvailRQ.xsd" EchoToken="echo" TimeStamp="2008-03-03T09:19:01Z" Target="Production" Version="1" SequenceNmbr="1" AvailRatesOnly="true" BestOnly="true" RateRangeOnly="false" ExactMatchOnly="true" LanguageId="EN"><POS><Source><UniqueId URL="" Type="ParsysOperatorCode" Id="' . $AbreuCustomerID . ':' . $AbreuUsername . ':' . $Abreupassword . '"/></Source></POS><BookingSegments><BookingSegment xmlns="http://www.opentravel.org/OTA" AvailReqType="Room"><HotelReference HotelCityCode="' . $city_xml41 . '"/><SearchCodes><CodeRef CodeContext="CategoryOperator" Code="="/><CodeRef CodeContext="CategoryCode" Code="0"/><CodeRef CodeContext="Name" Code=""/></SearchCodes><StayDateRange StartDate="' . date("Y-m-d", $from) . 'T12:00:00Z" EndDate="' . date("Y-m-d", $to) . 'T12:00:00Z"/><RoomStayCandidates>';
    for ($r = 0; $r < count($auxArray); $r ++) {
        //
        // RoomTypeCode
        //
        // 0: SINGLE ROOM
        // 1: DOUBLE ROOM
        // 2: TWIN BED ROOM
        // 3: TRIPLE ROOM
        // 4: 2 ADULTS, 1 CHILD ROOM
        // 5: QUADRUPLE ROOM
        // 6: 3 ADULT, 1 CHILD
        // 7: 2 ADULT, 2 CHILD
        //
        if ($auxArray[$r]['selectedAdults'] == 1) {
            if ($auxArray[$r]['selectedChildren'] == 0) {
                $RoomTypeCode = 0;
            } elseif ($auxArray[$r]['selectedChildren'] == 1) {
                $RoomTypeCode = 2;
            } elseif ($auxArray[$r]['selectedChildren'] == 2) {
                $RoomTypeCode = 3;
            } elseif ($auxArray[$r]['selectedChildren'] == 3) {
                $RoomTypeCode = 4;
            } elseif ($auxArray[$r]['selectedChildren'] >= 4) {
                $RoomTypeCode = 5;
            } else {
                $RoomTypeCode = 0;
            }
        } elseif ($auxArray[$r]['selectedAdults'] == 2) {
            if ($auxArray[$r]['selectedChildren'] == 0) {
                $RoomTypeCode = 1;
            } elseif ($auxArray[$r]['selectedChildren'] == 1) {
                $RoomTypeCode = 4;
            } elseif ($auxArray[$r]['selectedChildren'] == 2) {
                $RoomTypeCode = 7;
            } elseif ($auxArray[$r]['selectedChildren'] >= 3) {
                $RoomTypeCode = 5;
            } else {
                $RoomTypeCode = 1;
            }
        } elseif ($auxArray[$r]['selectedAdults'] == 3) {
            if ($auxArray[$r]['selectedChildren'] == 0) {
                $RoomTypeCode = 3;
            } elseif ($auxArray[$r]['selectedChildren'] == 1) {
                $RoomTypeCode = 6;
            } elseif ($auxArray[$r]['selectedChildren'] >= 2) {
                $RoomTypeCode = 5;
            } else {
                $RoomTypeCode = 3;
            }
        } elseif ($auxArray[$r]['selectedAdults'] == 4) {
            $RoomTypeCode = 5;
        } else {
            $RoomTypeCode = 2;
        }
        $raw .= '<RoomStayCandidate xmlns="http://www.opentravel.org/OTA" RoomType="" RoomTypeCode="' . $RoomTypeCode . '" Quantity="1">';
        $passengers = $passengers + $auxArray[$r]['selectedAdults'];
        $raw .= '<GuestCount xmlns="http://www.opentravel.org/OTA" AgeQualCode="Adult" Count="' . $auxArray[$r]['selectedAdults'] . '"/>';
        if ($auxArray[$r]['selectedChildren'] > 0) {
            $raw .= '<GuestCount xmlns="http://www.opentravel.org/OTA" AgeQualCode="Child" Count="' . $auxArray[$r]['selectedChildren'] . '"/>';
            $passengers = $passengers + $auxArray[$r]['selectedChildren'];
            for ($k = 0; $k < $auxArray[$r]['selectedChildren']; $k ++) {
                $counterAges = 1;
                foreach ($selectedChildrenAges[$k] as $keyAux => $valueAux) {
                    $raw .= '<GuestAge Age="' . $valueAux . '" ind="' . $counterAges . '"/>';
                }
            }
        }
        $raw .= '</RoomStayCandidate>';
    }
    $raw .= '</RoomStayCandidates><TPA_Extensions><UniqueId URL="" Type="Reservation" Id=""/><OnRequest Id="' . $AbreuOnRequest . '"/></TPA_Extensions></BookingSegment></BookingSegments></OTA_HotelAvailRQ>';
    // error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    if ($AbreuHOTELAVAILABILITY != "" and $AbreuUsername != "" and $Abreupassword != "") {
        $startTime = microtime();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $AbreuHOTELAVAILABILITY);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept: application/xml",
            "Content-type: text/xml",
            "Content-length: " . strlen($raw)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        // error_log("\r\n $PalladiumHotelGroupserviceurl \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\n $raw \r\n", 3, "/srv/www/htdocs/error_log");
        // error_log("\r\n RESPONSE ABREU: $response \r\n", 3, "/srv/www/htdocs/error_log");
        curl_close($ch);
        // Descomentar para ver o resultado do provider
        // Nao esquecer de alterar o session id para testar por causa de cache
        // echo $response;
        // die();
        $endTime = microtime();
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_abreu');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => $this->microtime_diff($startTime, $endTime),
                'errormessage' => $AbreuHOTELAVAILABILITY . $raw,
                'sqlcontext' => $response,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $node = $inputDoc->getElementsByTagName('SummaryResponse');
        for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
            $CurrencyCode = $node->item($rAUX)->getAttribute("CurrencyCode");
            error_log("\r\n CurrencyCode ABREU: $CurrencyCode \r\n", 3, "/srv/www/htdocs/error_log");
            $CurrencyCode_Base = $CurrencyCode;
            $HotelAddress = $node->item($rAUX)->getAttribute("HotelAddress");
            $HotelBabyAge = $node->item($rAUX)->getAttribute("HotelBabyAge");
            $HotelCat = $node->item($rAUX)->getAttribute("HotelCat");
            $HotelChildrenAge = $node->item($rAUX)->getAttribute("HotelChildrenAge");
            $HotelCity = $node->item($rAUX)->getAttribute("HotelCity");
            $HotelCountry = $node->item($rAUX)->getAttribute("HotelCountry");
            $HotelLink = $node->item($rAUX)->getAttribute("HotelLink");
            $HotelLocation = $node->item($rAUX)->getAttribute("HotelLocation");
            $HotelName = $node->item($rAUX)->getAttribute("HotelName");
            error_log("\r\n HotelName ABREU: $HotelName \r\n", 3, "/srv/www/htdocs/error_log");
            $HotelPhone = $node->item($rAUX)->getAttribute("HotelPhone");
            $IsModifiable = $node->item($rAUX)->getAttribute("IsModifiable");
            $InvBlockCode = $node->item($rAUX)->getAttribute("InvBlockCode");
            $IsAlternate = $node->item($rAUX)->getAttribute("IsAlternate");
            $IsRoom = $node->item($rAUX)->getAttribute("IsRoom");
            $MultipleRoom = $node->item($rAUX)->getAttribute("MultipleRoom");
            $HotelLatitude = $node->item($rAUX)->getAttribute("HotelLatitude");
            $HotelLongitude = $node->item($rAUX)->getAttribute("HotelLongitude");
            $Accuracy = $node->item($rAUX)->getAttribute("Accuracy");
            $ParsysRoomTypeDescription = $node->item($rAUX)->getAttribute("ParsysRoomTypeDescription");
            $ParsysRoomTypeName = $node->item($rAUX)->getAttribute("ParsysRoomTypeName");
            $ParsysRoomTypeUse = $node->item($rAUX)->getAttribute("ParsysRoomTypeUse");
            $ParsysRoomTypeUseName = $node->item($rAUX)->getAttribute("ParsysRoomTypeUseName");
            $RoomTypeCode = $node->item($rAUX)->getAttribute("RoomTypeCode");
            $Preference = $node->item($rAUX)->getAttribute("Preference");
            $Recommended = $node->item($rAUX)->getAttribute("Recommended");
            $Mark = $node->item($rAUX)->getAttribute("Mark");
            $CommissionType = $node->item($rAUX)->getAttribute("CommissionType");
            $ParsysRoomTypeUseAdults = $node->item($rAUX)->getAttribute("ParsysRoomTypeUseAdults");
            $ParsysRoomTypeUseChildren = $node->item($rAUX)->getAttribute("ParsysRoomTypeUseChildren");
            $MealPlan = $node->item($rAUX)->getAttribute("MealPlan");
            $MealPlanDescription = $node->item($rAUX)->getAttribute("MealPlanDescription");
            $MealPlanCode = $node->item($rAUX)->getAttribute("MealPlanCode");
            $AssignedRoomUseCode = $node->item($rAUX)->getAttribute("AssignedRoomUseCode");
            $RequestIDs = $node->item($rAUX)->getAttribute("RequestIDs");
            $HotelIdent = $node->item($rAUX)->getAttribute("HotelIdent");
            $NonRefundable = $node->item($rAUX)->getAttribute("NonRefundable");
            $HasCanPenaltyNow = $node->item($rAUX)->getAttribute("HasCanPenaltyNow");
            error_log("\r\n HasCanPenaltyNow ABREU: $HasCanPenaltyNow \r\n", 3, "/srv/www/htdocs/error_log");
            // Hotel Reference
            $HotelReference = $node->item($rAUX)->getElementsByTagName("HotelReference");
            if ($HotelReference->length > 0) {
                $HotelCode = $HotelReference->item(0)->getAttribute("HotelCode");
                $shid = $HotelCode;
                $sfilter[] = " sid='$HotelCode' ";
                $MasterCode = $HotelReference->item(0)->getAttribute("MasterCode");
                error_log("\r\n MasterCode ABREU: $MasterCode \r\n", 3, "/srv/www/htdocs/error_log");
            } else {
                $HotelCode = "";
                $MasterCode = "";
            }
            // RATEQUOTE
            $RateQuote = $node->item($rAUX)->getElementsByTagName("RateQuote");
            if ($RateQuote->length > 0) {
                $MaxOccupancy = $RateQuote->item(0)->getElementsByTagName("MaxOccupancy");
                if ($MaxOccupancy->length > 0) {
                    $Count = $MaxOccupancy->item(0)->getAttribute("Count");
                } else {
                    $Count = 0;
                }
                $DateRange = $RateQuote->item(0)->getElementsByTagName("DateRange");
                if ($DateRange->length > 0) {
                    $EndDate = $DateRange->item(0)->getAttribute("EndDate");
                    $StartDate = $DateRange->item(0)->getAttribute("StartDate");
                } else {
                    $EndDate = "";
                    $StartDate = "";
                }
                $QuotedRate = $RateQuote->item(0)->getElementsByTagName("QuotedRate");
                if ($QuotedRate->length > 0) {
                    $CurrencyCode = $QuotedRate->item(0)->getAttribute("CurrencyCode");
                    error_log("\r\n CurrencyCode ABREU: $CurrencyCode \r\n", 3, "/srv/www/htdocs/error_log");
                    $Commission = $QuotedRate->item(0)->getAttribute("Commission");
                    $StartDate = $QuotedRate->item(0)->getAttribute("StartDate");
                    $DecPart = $QuotedRate->item(0)->getAttribute("DecPart");
                    $IntPart = $QuotedRate->item(0)->getAttribute("IntPart");
                    $RateAmount = $QuotedRate->item(0)->getAttribute("RateAmount");
                    $RateBasisUnitsQty = $QuotedRate->item(0)->getAttribute("RateBasisUnitsQty");
                } else {
                    $EndDate = "";
                    $StartDate = "";
                    $CurrencyCode = "";
                    $Commission = 0;
                    $IntPart = 0;
                    $DecPart = 0;
                    $RateAmount = "";
                    $RateBasisUnitsQty = 0;
                }
            } else {
                $Count = 0;
                $EndDate = "";
                $StartDate = "";
                $CurrencyCode = "";
                $Commission = 0;
                $IntPart = 0;
                $DecPart = 0;
                $RateAmount = "";
                $RateBasisUnitsQty = 0;
            }
            
            $total = $IntPart . "." . $DecPart;
            
            // ADITIONALDETAILS
            $AdditionalDetails = $node->item($rAUX)->getElementsByTagName("AdditionalDetails");
            if ($AdditionalDetails->length > 0) {
                $AdditionalDetail = $AdditionalDetails->item(0)->getElementsByTagName("AdditionalDetail");
                $AdditionalDetails = array();
                for ($xAdditionalDetail = 0; $xAdditionalDetail < $AdditionalDetail->length; $xAdditionalDetail ++) {
                    $AdditionalDetails[$xAdditionalDetail]['Amount'] = $AdditionalDetail->item($xAdditionalDetail)->getAttribute("Amount");
                    $AdditionalDetails[$xAdditionalDetail]['Code'] = $AdditionalDetail->item($xAdditionalDetail)->getAttribute("Code");
                    $AdditionalDetails[$xAdditionalDetail]['CurrencyCode'] = $AdditionalDetail->item($xAdditionalDetail)->getAttribute("CurrencyCode");
                    $AdditionalDetails[$xAdditionalDetail]['DecPart'] = $AdditionalDetail->item($xAdditionalDetail)->getAttribute("DecPart");
                    $AdditionalDetails[$xAdditionalDetail]['IntPart'] = $AdditionalDetail->item($xAdditionalDetail)->getAttribute("IntPart");
                    $AdditionalDetails[$xAdditionalDetail]['Type'] = $AdditionalDetail->item($xAdditionalDetail)->getAttribute("Type");
                    $xAdditionalDetail = $xAdditionalDetail + 1;
                }
            } else {
                $AdditionalDetails = "";
            }
            // ADITIONALDETAILSBYDAYS
            $auxAdditionalDetailDate = array();
            $auxDetailSuppl = array();
            $auxCount = 0;
            $auxCount2 = 0;
            $AdditionalDetailsByDay = $node->item($rAUX)->getElementsByTagName("AdditionalDetailsByDay");
            if ($AdditionalDetailsByDay->length > 0) {
                $AdditionalDetailDate = $AdditionalDetailsByDay->item(0)->getElementsByTagName("AdditionalDetailDate");
                for ($i = 0; $i < $AdditionalDetailDate->length; $i ++) {
                    $Breakdown[$auxCount]['Date'] = $AdditionalDetailDate->item($i)->getAttribute("Date");
                    $Breakdown[$auxCount]['Amount'] = $AdditionalDetailDate->item($i)->getAttribute("Amount");
                    $Breakdown[$auxCount]['CurrencyCode'] = $AdditionalDetailDate->item($i)->getAttribute("CurrencyCode");
                    $Breakdown[$auxCount]['DecPart'] = $AdditionalDetailDate->item($i)->getAttribute("DecPart");
                    $Breakdown[$auxCount]['IntPart'] = $AdditionalDetailDate->item($i)->getAttribute("IntPart");
                    $Breakdown[$auxCount]['Rate'] = $AdditionalDetailDate->item($i)->getAttribute("Rate");
                    $Breakdown[$auxCount]['RateParsys'] = $AdditionalDetailDate->item($i)->getAttribute("RateParsys");
                    $Breakdown[$auxCount]['RateName'] = $AdditionalDetailDate->item($i)->getAttribute("RateName");
                    $Breakdown[$auxCount]['MealPlan'] = $AdditionalDetailDate->item($i)->getAttribute("MealPlan");
                    $Breakdown[$auxCount]['MealPlanDescription'] = $AdditionalDetailDate->item($i)->getAttribute("MealPlanDescription");
                    $auxCount = $auxCount + 1;
                    $AdditionalDetailSuppl = $AdditionalDetailDate->item($i)->getElementsByTagName("AdditionalDetailSuppl");
                    if ($AdditionalDetailSuppl->length > 0) {
                        $AdditionalDetailSupplMeal = $AdditionalDetailSuppl->item(0)->getElementsByTagName("AdditionalDetailSupplMeal");
                        for ($auxMeal = 0; $auxMeal < $AdditionalDetailSupplMeal->length; $auxMeal ++) {
                            $auxDetailSuppl[$auxCount2]['MealPlanDescription'] = $AdditionalDetailSupplMeal->item($auxMeal)->getAttribute("MealPlanDescription");
                            $auxDetailSuppl[$auxCount2]['IntPart'] = $AdditionalDetailSupplMeal->item($auxMeal)->getAttribute("IntPart");
                            $auxDetailSuppl[$auxCount2]['DecPart'] = $AdditionalDetailSupplMeal->item($auxMeal)->getAttribute("DecPart");
                            $auxDetailSuppl[$auxCount2]['Code'] = $AdditionalDetailSupplMeal->item($auxMeal)->getAttribute("Code");
                            $auxDetailSuppl[$auxCount2]['Amount'] = $AdditionalDetailSupplMeal->item($auxMeal)->getAttribute("Amount");
                            $auxCount2 = $auxCount2 + 1;
                        }
                    }
                }
            }
            
            // TPAEXTENSIONS
            $auxWarn = array();
            $auxW = 0;
            $TPA_Extensions = $node->item($rAUX)->getElementsByTagName("TPA_Extensions");
            $UniqueId = $TPA_Extensions->item(0)->getElementsByTagName("UniqueId");
            $Type = $UniqueId->item(0)->getAttribute("Type");
            $URL = $UniqueId->item(0)->getAttribute("URL");
            $Id = $UniqueId->item(0)->getAttribute("Id");
            $ConfirmationStatus = $TPA_Extensions->item(0)->getElementsByTagName("ConfirmationStatus");
            $CodeC = $ConfirmationStatus->item(0)->getAttribute("Code");
            $Reason = $ConfirmationStatus->item(0)->getAttribute("Reason");
            error_log("\r\n Reason ABREU: $Reason \r\n", 3, "/srv/www/htdocs/error_log");
            $Warnings = $TPA_Extensions->item(0)->getElementsByTagName("Warnings");
            if ($Warnings->length > 0) {
                for ($auxWarn = 0; $auxWarn < $Warnings->length; $auxWarn ++) {
                    $auxWarn[$auxW]['Text'] = $Warnings->item($auxWarn)->getAttribute("Text");
                    $auxW = $auxW + 1;
                }
            }
            $CancelPenaltyFromDate = "";
            $CancelPenaltyToDate = "";
            $CancelPenaltyPenalty = "";
            $CancelPolicyInformation = $TPA_Extensions->item(0)->getElementsByTagName("CancelPolicyInformation");
            if ($CancelPolicyInformation->length > 0) {
                $Description = $CancelPolicyInformation->item(0)->getElementsByTagName("Description");
                $IsPerRoom = $CancelPolicyInformation->item(0)->getAttribute("IsPerRoom");
                $HasCancelPolicy = $CancelPolicyInformation->item(0)->getAttribute("HasCancelPolicy");
                error_log("\r\n HasCancelPolicy ABREU: $HasCancelPolicy \r\n", 3, "/srv/www/htdocs/error_log");
                $CancelPenalties = $CancelPolicyInformation->item(0)->getElementsByTagName("CancelPenalties");
                $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                if ($CancelPenalty->length > 0) {
                    for ($p = 0; $p < $CancelPenalty->length; $p ++) {
                        $CancelPenaltyFromDate = $CancelPenalties->item($yCancelPenalties)->getAttribute("FromDate");
                        $CancelPenaltyToDate = $CancelPenalties->item($yCancelPenalties)->getAttribute("ToDate");
                        $CancelPenaltyPenalty = $CancelPenalties->item($yCancelPenalties)->getAttribute("Penalty");
                    }
                }
            }
            
            for ($zRooms = 0; $zRooms < count($selectedAdults); $zRooms ++) {
                if (is_array($tmp[$shid])) {
                    $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                } else {
                    $baseCounterDetails = 0;
                }
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelId'] = $HotelCode;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-41";
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $ParsysRoomTypeName;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomTypeCode'] = $ParsysRoomTypeName;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomType'] = $ParsysRoomTypeName;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RatePlanCode'] = $RateParsys;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RoomDescription'] = $ParsysRoomTypeDescription;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nett'] = $total;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['NonRefundable'] = $NonRefundable;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['Recommended'] = $Recommended;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['boardtype'] = $MealPlanDescription;
                
                /*
                 * if ($PromotionCode != "") {
                 * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                 * $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $PromotionCode;
                 * } else {
                 */
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                // }
                
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($MealPlan);
                $pricebreakdown = array();
                $pricebreakdownCount = 0;
                for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                    $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                    $amount = $noOfNights * $total;
                    if ($AbreuMarkup != 0) {
                        $amount = $amount + (($amount * $AbreuMarkup) / 100);
                    }
                    // Geo target markup
                    if ($internalmarkup != 0) {
                        $amount = $amount + (($amount * $internalmarkup) / 100);
                    }
                    // Agent markup
                    if ($agent_markup != 0) {
                        $amount = $amount + (($amount * $agent_markup) / 100);
                    }
                    // Fallback Markup
                    if ($AbreuMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                    }
                    // Agent discount
                    if ($agent_discount != 0) {
                        $amount = $amount - (($amount * $agent_discount) / 100);
                    }
                    if ($scurrency != "" and $currency != $scurrency) {
                        $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                    }
                    $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                    $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                    $pricebreakdownCount = $pricebreakdownCount + 1;
                }
                
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $Description;
                $tmp[$code]['details'][$zRooms][$baseCounterDetails]['cancelpolicies'] = $Description;
            }
        }
        $abreu = true;
    }
}
// error_log("\r\n palladium: $palladium \r\n", 3, "/srv/www/htdocs/error_log");
// error_log("\r\n TMP:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");

if ($abreu == true) {
    $sfilter = implode(' or ', $sfilter);
    try {
        $sql = "select hid, sid from xmlhotels_mabreu where " . $sfilter;
        $statement2 = $db->createStatement($sql);
        $statement2->prepare();
        $result2 = $statement2->execute();
        $result2->buffer();
        if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
            $resultSet2 = new ResultSet();
            $resultSet2->initialize($result2);
            foreach ($resultSet2 as $row2) {
                // $sidfilter[] = "id=" . $row2->hid;
                $sidfilter[] = $row2->hid;
                if (is_array($hotels_array[$row2->hid])) {
                    // Append to original details
                    $tmph = $hotels_array[$row2->hid]['details'];
                    $tmps = $tmp[$row2->sid]['details'];
                    foreach ($tmph as $key => $value) {
                        $last = count($tmph[$key]);
                        foreach ($tmps[$key] as $keyd => $valued) {
                            $tmph[$key][$last] = $valued;
                            $last ++;
                        }
                    }
                    $hotels_array[$row2->hid]['details'] = $tmph;
                } else {
                    $hotels_array[$row2->hid] = $tmp[$row2->sid];
                }
            }
        }
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if (is_array($sidfilter)) {
        $sidfilter = implode(',', $sidfilter);
        $query = 'call xmlhotels("' . $sidfilter . '")';
        $supplier = 41;
        error_log("\r\n QUERY $query \r\n", 3, "/srv/www/htdocs/error_log");
        try {
            $sql = new Sql($db);
            $delete = $sql->delete();
            $delete->from('quote_session_abreu');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_abreu');
            $insert->values(array(
                'session_id' => $session_id,
                'xmlrequest' => (string) $raw,
                'xmlresult' => (string) $response,
                'data' => base64_encode(serialize($hotels_array)),
                'searchsettings' => base64_encode(serialize($requestdata))
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }
}
// }
?>