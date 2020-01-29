<?php
error_log("\r\nMulti Policies RTS\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$salestaxes = 0;
$salestaxesfees = 0;
$baserate = 0;
$affiliate_id_expedia = 0;
$occupancies = "";
$sindex = $index;
$db = new \Zend\Db\Adapter\Adapter($config);
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_tbo where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_tbo where session_id='$session_id'";
}
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $lang = $searchsettings['lang'];
    $currency = $searchsettings['currency'];
    $from = $searchsettings['from'];
    $to = $searchsettings['to'];
    $destination = $searchsettings['destination'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $index = $searchsettings['index'];
    $ipaddress = $searchsettings['ipaddress'];
    $nationality = $searchsettings['nationality'];
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
    if ($details == "hoteldetails") {
        $selectedAdults = array();
        $selectedAdults[$nroom] = $adt;
        // Children + Ages
        $selectedChildrenAges = array();
        $selectedChildren = array();
        $selectedChildren[$nroom] = $chd;
        if ($chd > 0) {
            $children_ages = explode(",", $children_ages);
            for ($w = 0; $w < count($children_ages); $w ++) {
                $selectedChildrenAges[$nroom][$w] = $children_ages[$w];
            }
        }
    }
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$branch_filter = '';
$sql = "select value from settings where name='enabletbo' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_tbo = $affiliate_id;
} else {
    $affiliate_id_tbo = 0;
}
$sql = "select value from settings where name='tboLoginEmail' and affiliate_id=$affiliate_id_tbo" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tboLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='tboPassword' and affiliate_id=$affiliate_id_tbo" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tboPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='tboServiceURL' and affiliate_id=$affiliate_id_tbo" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $tboServiceURL = $row['value'];
}
$sql = "select value from settings where name='tboMarkup' and affiliate_id=$affiliate_id_tbo" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tboMarkup = (double) $row_settings['value'];
} else {
    $tboMarkup = 0;
}
$sql = "select value from settings where name='tboTimeout' and affiliate_id=$affiliate_id_tbo" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $tboTimeout = (int) $row_settings['value'];
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
    $sql = "select value from settings where name='tboDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}

$outputArray = array();
$arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
foreach ($arrIt as $sub) {
    $subArray = $arrIt->getSubIterator();
    if (isset($quoteid[$nroom])) {
        if (isset($subArray['quoteid'])) {
            if ($subArray['quoteid'] === $quoteid[$nroom]) {
                $outputArray[] = iterator_to_array($subArray);
                $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                    ->key();
            }
        }
    }
}
$breakdownTmp = array();
if (! is_array($outputArray)) {
    $response['error'] = "Unable to handle request #3";
    return false;
} else {
    array_push($breakdownTmp, $outputArray);
}
$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');
$c = $nroom;
$response = array();
$roombreakdown2 = array();
foreach ($breakdownTmp as $k => $v) {
    foreach ($v as $key => $value) {
        $shid = $value['shid'];
        $code = $value['hotelid'];
        $scode = $value['shid'];
        $HotelId = $value['hotelid'];
        $room_code = $value['roomid'];
        $SessionId = $value['SessionId'];
        $ResultIndex = $value['ResultIndex'];
        
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        
        // CANCELLATION POLICIES
        
        $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
        <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
            <hot:Credentials UserName="' . $tboLoginEmail . '" Password="' . $tboPassword . '">
            </hot:Credentials>
            <wsa:Action>http://TekTravel/HotelBookingApi/HotelCancellationPolicy</wsa:Action>
            <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
        </soap:Header>
        <soap:Body>
            <hot:HotelCancellationPolicyRequest>
                <hot:ResultIndex>' . $ResultIndex . '</hot:ResultIndex>
                <hot:SessionId>' . $SessionId . '</hot:SessionId>
                <hot:OptionsForBooking>
                    <hot:FixedFormat>false</hot:FixedFormat>
                    <hot:RoomCombination>
                        <hot:RoomIndex>' . $room_code . '</hot:RoomIndex>
                    </hot:RoomCombination>
                </hot:OptionsForBooking>
            </hot:HotelCancellationPolicyRequest>
        </soap:Body>
        </soap:Envelope>';
        // error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");
        
        $headers = array(
            "Content-type: application/soap+xml; charset=utf-8",
            "Content-length: " . strlen($raw)
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $tboServiceURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $xmlresponse = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        // if ($response === false) {
        // error_log("\r\nCurl error: " . curl_error($ch) . "\r\n", 3, "/srv/www/htdocs/error_log");
        // } else {
        // error_log("\r\n Operation completed without any errors \r\n", 3, "/srv/www/htdocs/error_log");
        // }
        error_log("\r\n Response POLICIES: $xmlresponse \r\n", 3, "/srv/www/htdocs/error_log");
        
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresponse);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $HotelCancellationPolicyResponse = $Body->item(0)->getElementsByTagName("HotelCancellationPolicyResponse");
        
        $CancelPolicies = $HotelCancellationPolicyResponse->item(0)->getElementsByTagName("CancelPolicies");
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
            
            $CancelPolicy = $CancelPolicies->item(0)->getElementsByTagName("CancelPolicy");
            if ($CancelPolicy->length > 0) {
                $Currency = $CancelPolicy->item(0)->getAttribute("Currency");
                $CancellationCharge = $CancelPolicy->item(0)->getAttribute("CancellationCharge");
                $ChargeType = $CancelPolicy->item(0)->getAttribute("ChargeType");
                $ToDate = $CancelPolicy->item(0)->getAttribute("ToDate");
                $FromDate = $CancelPolicy->item(0)->getAttribute("FromDate");
                $RoomIndex = $CancelPolicy->item(0)->getAttribute("RoomIndex");
                $RoomTypeName = $CancelPolicy->item(0)->getAttribute("RoomTypeName");
            }
        }
        
        $HotelNorms = $HotelCancellationPolicyResponse->item(0)->getElementsByTagName("HotelNorms");
        if ($HotelNorms->length > 0) {
            $string = $HotelNorms->item(0)->getElementsByTagName("string");
            if ($string->length > 0) {
                $string = $string->item(0)->nodeValue;
            } else {
                $string = "";
            }
        }
        
        // AVAILABLE PRICING
        $raw3 = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
        <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
        <hot:Credentials UserName="' . $tboLoginEmail . '" Password="' . $tboPassword . '">
        </hot:Credentials>
        <wsa:Action>http://TekTravel/HotelBookingApi/AvailabilityAndPricing</wsa:Action>
        <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
        </soap:Header>
        <soap:Body>
            <hot:AvailabilityAndPricingRequest>
                <hot:ResultIndex>' . $ResultIndex . '</hot:ResultIndex>
                <hot:HotelCode></hot:HotelCode>
                <hot:SessionId>' . $SessionId . '</hot:SessionId>
                <hot:OptionsForBooking>
                    <hot:FixedFormat>false</hot:FixedFormat>
                    <hot:RoomCombination>
                        <hot:RoomIndex>' . $RoomIndex . '</hot:RoomIndex>
                    </hot:RoomCombination>
                </hot:OptionsForBooking>
            </hot:AvailabilityAndPricingRequest>
        </soap:Body>
        </soap:Envelope>';
        
        $headers3 = array(
            "Content-type: application/soap+xml; charset=utf-8",
            "Content-length: " . strlen($raw3)
        );
        
        $ch3 = curl_init();
        curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch3, CURLOPT_URL, $tboServiceURL);
        curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch3, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch3, CURLOPT_TIMEOUT, 1000);
        curl_setopt($ch3, CURLOPT_POST, true);
        curl_setopt($ch3, CURLOPT_POSTFIELDS, $raw3);
        curl_setopt($ch3, CURLOPT_HTTPHEADER, $headers3);
        $xmlresponse3 = curl_exec($ch3);
        $error = curl_error($ch3);
        $headers = curl_getinfo($ch3);
        
        error_log("\r\n Response POLICIES3: $xmlresponse3 \r\n", 3, "/srv/www/htdocs/error_log");
        
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresponse3);
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
        
        $HotelCancellationPolicies = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("HotelCancellationPolicies");
        if ($HotelCancellationPolicies->length > 0) {
            $HotelNorms = $HotelCancellationPolicies->item(0)->getElementsByTagName("HotelNorms");
            if ($HotelNorms->length > 0) {
                $string = $HotelNorms->item(0)->getElementsByTagName("string");
                if ($string->length > 0) {
                    for ($s=0; $s < $string->length; $s++) { 
                        $string = $string->item($s)->nodeValue;
                    }
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
                
                $CancelPolicy = $CancelPolicies->item(0)->getElementsByTagName("CancelPolicy");
                if ($CancelPolicy->length > 0) {
                    $RoomIndex = $CancelPolicy->item(0)->getAttribute("RoomIndex");
                    $RoomTypeName = $CancelPolicy->item(0)->getAttribute("RoomTypeName");
                    $FromDate = $CancelPolicy->item(0)->getAttribute("FromDate");
                    $ToDate = $CancelPolicy->item(0)->getAttribute("ToDate");
                    $ChargeType = $CancelPolicy->item(0)->getAttribute("ChargeType");
                    $CancellationCharge = $CancelPolicy->item(0)->getAttribute("CancellationCharge");
                    $Currency = $CancelPolicy->item(0)->getAttribute("Currency");
                }
                
                $NoShowPolicy = $CancelPolicies->item(0)->getElementsByTagName("NoShowPolicy");
                if ($NoShowPolicy->length > 0) {
                    for ($i = 0; $i < $NoShowPolicy->length; $i ++) {
                        $RoomIndex = $NoShowPolicy->item($i)->getAttribute("RoomIndex");
                        $RoomTypeName = $NoShowPolicy->item($i)->getAttribute("RoomTypeName");
                        $FromDate = $NoShowPolicy->item($i)->getAttribute("FromDate");
                        $ToDate = $NoShowPolicy->item($i)->getAttribute("ToDate");
                        $ChargeType = $NoShowPolicy->item($i)->getAttribute("ChargeType");
                        $CancellationCharge = $NoShowPolicy->item($i)->getAttribute("CancellationCharge");
                        $Currency = $NoShowPolicy->item($i)->getAttribute("Currency");
                    }
                }
            }
        }
        
        $PriceVerification = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("PriceVerification");
        if ($PriceVerification->length > 0) {
            $AvailbaleOnNewPrice = $PriceVerification->item(0)->getAttribute("AvailbaleOnNewPrice");
            $PriceChanged = $PriceVerification->item(0)->getAttribute("PriceChanged");
            $Status = $PriceVerification->item(0)->getAttribute("Status");
        }

        $AccountInfo = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("AccountInfo");
        if ($AccountInfo->length > 0) {
            $AgencyBlocked = $AccountInfo->item(0)->getAttribute("AgencyBlocked");
            $AgencyBalance = $AccountInfo->item(0)->getAttribute("AgencyBalance");
        }

        $HotelDetailsVerification = $AvailabilityAndPricingResponse->item(0)->getElementsByTagName("HotelDetailsVerification");
        if ($HotelDetailsVerification->length > 0) {
            $Status = $HotelDetailsVerification->item(0)->getAttribute("Status");
            $Remarks = $HotelDetailsVerification->item(0)->getAttribute("Remarks");
        }
        //
        // EOF Policies
        //
        // EOF Check prices & availability
        //
        $item['code'] = $value['shid'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nett'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['RoomTypeCode'] = $value['room_type'];
        $item['RoomType'] = $value['room_type'];
        $item['RoomDescription'] = $value['room_description'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        
        if ($FromDate == "") {
                $item['nonrefundable'] = true;
                $item['cancelpolicy'] = $translator->translate("This is a non refundable booking");
                $item['cancelpolicy_details'] = $translator->translate("This is a non refundable booking");
                $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
                $item['cancelpolicy_deadlinetimestamp'] = time();
        } else {
            $cancelation_details = "From Date " . $FromDate . " To Date " . $ToDate . " Charge " . $CancellationCharge . "% amount of the booking.";;
            $item['cancelpolicy'] = $cancelation_details;
            $item['cancelpolicy_details'] = $cancelation_details;
            $item['cancelpolicy_deadline'] = date('d-m-Y', strtotime($LastCancellationDeadline));
        }
        // $cancelation_deadline = $value['DailyRateStart'];
        // $cancelation_details = $value['cancelpolicy'];
        // $item['cancelpolicy'] = $cancelpolicy;
        // $item['cancelpolicy_deadline'] = strftime("%d-%m-%Y", $cancelation_deadline);
        // $item['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
        // $item['cancelpolicy_details'] = $cancelation_details;
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mtbo where sid='" . $shid . "' and hid=" . $hid;
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel->buffer();
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    try {
        $row_country = $statement2->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $row_country->buffer();
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $item = array();
            $item['url'] = "//world-wide-web-servers.com/static/hotels/" . $row->url;
            $item['description'] = $row->description;
            array_push($images, $item);
        }
    }
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
// error_log("\r\n" . print_r($responseContent, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
$hotel['checkin'] = $responseContent[$shid]['checkin'];
$hotel['fees'] = $responseContent[$shid]['fees'];
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown2;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['sales_taxes'] = $filter->filter($salestaxes);
$response['sales_taxesplain'] = number_format($salestaxes, 2, '.', '');
$response['taxes'] = $filter->filter($salestaxesfees);
$response['taxesplain'] = number_format($salestaxesfees, 2, '.', '');
$response['base_rate'] = $filter->filter($baserate);
$response['base_rateplain'] = number_format($baserate, 2, '.', '');
$response['occupancies'] = json_encode($occupancies);
$response['searchsettings'] = $searchsettings;
$response['ean'] = 1;
$response['eanbookhref'] = $href;
//
// Store Session
//
$sql = new Sql($db);
$sql = "delete from quote_session_hotel_multipolicies where session_id='" . $session_id . "' and sindex=$sindex";
try {
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('quote_session_hotel_multipolicies');
$insert->values(array(
    'session_id' => $session_id,
    'sindex' => $sindex,
    'data' => base64_encode(serialize($response)),
    'searchsettings' => base64_encode(serialize($searchsettings))
), $insert::VALUES_MERGE);
try {
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['breakdown'] = $roombreakdown;
error_log("\r\nRTS Policies Multi - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>