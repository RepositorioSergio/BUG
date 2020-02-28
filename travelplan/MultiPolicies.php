<?php
error_log("\r\nMulti Policies TRAVELPLAN \r\n", 3, "/srv/www/htdocs/error_log");
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
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_travelplan where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_travelplan where session_id='$session_id'";
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
$sql = "select value from settings where name='enabletravelplan' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_travelplan = $affiliate_id;
} else {
    $affiliate_id_travelplan = 0;
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
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
if ((int) $residency > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $residency;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings["iso_code_2"];
    } else {
        $residenceMarket = "";
    }
} else {
    $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $residenceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='TravelPlanuser' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanuser = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanTimeout' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanTimeout = (int) $row_settings['value'];
} else {
    $TravelPlanTimeout = 0;
}
$sql = "select value from settings where name='TravelPlanpassword' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='TravelPlanMarkup' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanMarkup = (double) $row_settings['value'];
} else {
    $TravelPlanMarkup = 0;
}
$sql = "select value from settings where name='TravelPlanserviceURL' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanserviceURL = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSystem' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSystem = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanSalesChannel' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanSalesChannel = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanlanguage' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanlanguage = $row_settings['value'];
}
$sql = "select value from settings where name='TravelPlanConnectionString' and affiliate_id=$affiliate_id_travelplan" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $TravelPlanConnectionString = $row_settings['value'];
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
        $x50_0 = $value['x50_0'];
        $x50_1 = $value['x50_1'];
        $x50_2 = $value['x50_2'];
        $RoomTypeCode = $value['RoomTypeCode'];
        $RatePlanCode = $value['RatePlanCode'];
        $Token = $value['Token'];
        $Total = $value['total'];
        $province = $x50_0 . '' . $x50_1;
        
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        // Check pricing & availability      
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
        xmlns:ns0="http://www.opentravel.org/OTA/2003/05">
           <soapenv:Header/>
           <soapenv:Body>
              <ns0:OTA_HotelResRQ Version="2" ResStatus="Initiate">
                 <ns0:HotelReservations>
                    <ns0:HotelReservation>
                       <ns0:RoomStays>
                          <ns0:RoomStay>
                             <ns0:RoomTypes>
                                <ns0:RoomType RoomTypeCode="' . $RoomTypeCode . '"/>
                             </ns0:RoomTypes>
                             <ns0:RatePlans>
                                <ns0:RatePlan RatePlanCode="' . $RatePlanCode . '"/>
                             </ns0:RatePlans>
                             <ns0:RoomRates>
                                <ns0:RoomRate RatePlanCode="' . $RatePlanCode . '" AvailabilityStatus="AvailableForSale" InvBlockCode="1" RoomTypeCode="' . $RoomTypeCode . '">
                                   <ns0:Rates>
                                      <ns0:Rate>
                                         <ns0:Total AmountAfterTax="' . $Total . '" CurrencyCode="' . $currency . '"/>
                                         <ns0:TPA_Extensions>
                                            <ns0:ProviderTokens>';
                                            $TokenCode = 1;
                                            for ($y = 0; $y < $adt; $y ++) {
                                                $xml = $xml . '<ns0:Token TokenCode="' . $TokenCode . '" TokenName="PaxId"/>';
                                                $TokenCode = $TokenCode + 1;
                                            }
                                            if ($chd > 0) {
                                                for ($o = 0; $o < $chd; $o ++) {
                                                    $xml = $xml . '<ns0:Token TokenCode="' . $TokenCode . '" TokenName="PaxId"/>';
                                                    $TokenCode = $TokenCode + 1;
                                                }
                                            }
                                $xml .= '</ns0:ProviderTokens>
                                            <ns0:RoomToken Token="' . $Token . '"/>
                                         </ns0:TPA_Extensions>
                                      </ns0:Rate>
                                   </ns0:Rates>
                                   <ns0:GuestCounts>';
                                   for ($z = 0; $z < $adt; $z ++) {
                                        $xml = $xml . '<ns0:GuestCount Count="1" Age="30"/>';
                                    }
                                    if ($chd > 0) {
                                        for ($o = 0; $o < $chd; $o ++) {
                                            $xml = $xml . '<ns0:GuestCount Count="1" Age="' . $children_ages[$o] . '"/>';
                                        }
                                    }
                            $xml .= '</ns0:GuestCounts>
                                </ns0:RoomRate>
                             </ns0:RoomRates>
                             <ns0:TimeSpan Start="' . $from_date . '" End="' . $to_date . '"/>
                             <ns0:BasicPropertyInfo HotelCode="' . $HotelId . '"/>
                          </ns0:RoomStay>
                       </ns0:RoomStays>
                       <ns0:ResGuests>';
                       $TokenCode2 = 1;
                       for ($r=0; $r < $adt; $r++) { 
                           $xml .= '<ns0:ResGuest Age="30">
                           <ns0:Profiles>
                               <ns0:ProfileInfo>
                               <ns0:Profile>
                                   <ns0:Customer>
                                       <ns0:CitizenCountryName Code="' . $sourceMarket . '"/>
                                       <ns0:TPA_Extensions>
                                           <ns0:ProviderTokens>
                                           <ns0:Token TokenName="PaxResidenceCountry" TokenCode="' . $residenceMarket . '"/>
                                           <ns0:Token TokenName="PaxId" TokenCode="' . $TokenCode2 . '"/>
                                           </ns0:ProviderTokens>
                                       </ns0:TPA_Extensions>
                                   </ns0:Customer>
                               </ns0:Profile>
                               </ns0:ProfileInfo>
                               </ns0:Profiles>
                               </ns0:ResGuest>';
                            $TokenCode2 = $TokenCode2 + 1;
                       }
                       if ($chd > 0) {
                            for ($z=0; $z < $chd; $z++) { 
                                $xml .= '<ns0:ResGuest Age="' . $children_ages[$z] . '">
                                <ns0:Profiles>
                                    <ns0:ProfileInfo>
                                    <ns0:Profile>
                                        <ns0:Customer>
                                            <ns0:CitizenCountryName Code="' . $sourceMarket . '"/>
                                            <ns0:TPA_Extensions>
                                                <ns0:ProviderTokens>
                                                <ns0:Token TokenName="PaxResidenceCountry" TokenCode="' . $residenceMarket . '"/>
                                                <ns0:Token TokenName="PaxId" TokenCode="' . $TokenCode2 . '"/>
                                                </ns0:ProviderTokens>
                                            </ns0:TPA_Extensions>
                                        </ns0:Customer>
                                    </ns0:Profile>
                                    </ns0:ProfileInfo>
                                    </ns0:Profiles>
                                    </ns0:ResGuest>';
                                    $TokenCode2 = $TokenCode2 + 1;
                            }
                       }                       
                        $xml .= '</ns0:ResGuests>      
                            <ns0:TPA_Extensions>
                                <ns0:Providers>
                                    <ns0:Provider Provider="GSI">
                                        <ns0:Credentials>
                                        <ns0:Credential CredentialCode="' . $TravelPlanuser . '" CredentialName="AccountCode"/>
                                        <ns0:Credential CredentialCode="' . $TravelPlanpassword . '" CredentialName="Password"/>
                                        <ns0:Credential CredentialCode="' . $TravelPlanSystem . '" CredentialName="System"/>
                                        <ns0:Credential CredentialCode="' . $TravelPlanSalesChannel . '" CredentialName="SalesChannel"/>
                                        <ns0:Credential CredentialCode="' . $TravelPlanlanguage . '" CredentialName="Language"/>
                                        <ns0:Credential CredentialCode="' . $TravelPlanConnectionString . '" CredentialName="ConnectionString"/>
                                        </ns0:Credentials>
                                        <ns0:ProviderAreas>
                                        <ns0:Area TypeCode="Country" AreaCode="' . $x50_0 . '"/>
                                        <ns0:Area TypeCode="Province" AreaCode="' . $province . '"/>
                                        </ns0:ProviderAreas>
                                    </ns0:Provider>
                                </ns0:Providers>
                                <ns0:ProviderTokens>
                                    <ns0:Token TokenName="ExternalProvider" TokenCode="1"/>
                                </ns0:ProviderTokens>
                                <ns0:ProviderID Provider="GSI"/>
                            </ns0:TPA_Extensions>
                            </ns0:HotelReservation>
                        </ns0:HotelReservations>
                    </ns0:OTA_HotelResRQ>
                </soapenv:Body>
                </soapenv:Envelope>';
                error_log("\r\n RAW - $xml \r\n", 3, "/srv/www/htdocs/error_log");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $TravelPlanserviceURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept-Encoding: gzip, deflate",
            "Accept: application/xml",
            "Content-type: application/xml",
            "Content-length: " . strlen($xml)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $parse = curl_exec($ch);
        $error = curl_error($ch);
        $headers = curl_getinfo($ch);
        $inputDoc = new DOMDocument();
        error_log("\r\nResponse - $parse\r\n", 3, "/srv/www/htdocs/error_log");
        $tmp = gzdecode($parse);
        if ($tmp != "") {
            $parse = $tmp;
        }
        $tmp = $inputDoc->loadXML($parse);
        $ResGlobalInfo = $inputDoc->getElementsByTagName('ResGlobalInfo');
        if ($ResGlobalInfo->length > 0) {
            $HotelReservationID = $ResGlobalInfo->item(0)->getElementsByTagName("HotelReservationID");
            if ($HotelReservationID->length > 0) {
                $HotelReservationID = $HotelReservationID->item(0)->getAttribute("ResID_Value");
            } else {
                $failed = 1;
            }
        } else {
            $failed = 1;
        }
        if ($HotelReservationID == "") {
            $failed = 1;
        }
        $CancelPolicies = "";
        $CancelPenalty = $inputDoc->getElementsByTagName('CancelPenalty');
        for ($w = 0; $w < $CancelPenalty->length; $w ++) {
            if ($CancelPolicies != "") {
                $CancelPolicies = $CancelPolicies . "<br/>";
            }
            $Start = $CancelPenalty->item($w)->getAttribute("Start");
            $End = $CancelPenalty->item($w)->getAttribute("End");
            $NonRefundable = $CancelPenalty->item($w)->getAttribute("NonRefundable");
            if ($NonRefundable == "true") {
                $CancelPolicies = $CancelPolicies . gettext("Non Refundable");
            } else {
                $CancelPolicies = $CancelPolicies . gettext("Cancel from") . " " . $Start . " " . gettext("to") . " " . $End;
                $AmountPercent = $CancelPenalty->item($w)->getElementsByTagName("AmountPercent");
                if ($AmountPercent->length > 0) {
                    $CancelPolicies = $CancelPolicies . " " . gettext("pay") . " " . $AmountPercent->item(0)->getAttribute("Amount") . " " . $AmountPercent->item(0)->getAttribute("CurrencyCode");
                }
            }
        }
        $RoomRateDescription = $inputDoc->getElementsByTagName('RoomRateDescription');
        for ($w = 0; $w < $RoomRateDescription->length; $w ++) {
            if ($RoomRateDescription->item($w)->getAttribute("Name") == "SERVICE#" or $RoomRateDescription->item($w)->getAttribute("Name") == "SYSTEM#CREAC SERVICIO") {
                $aux = $RoomRateDescription->item($w)->getElementsByTagName("Text");
                if ($aux->length > 0) {
                    if ($CancelPolicies != "") {
                        $CancelPolicies = $CancelPolicies . "<br/><br/>";
                    }
                    $CancelPolicies = $CancelPolicies . $aux->item(0)->nodeValue;
                }
            }
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
        

        if ($NonRefundable == "true") {
            $item['nonrefundable'] = true;
            $item['cancelpolicy'] = $translator->translate("This is a non refundable booking"). "<br/>" . $CancelPolicies;
            $item['cancelpolicy_details'] = $translator->translate("This is a non refundable booking"). "<br/>" . $CancelPolicies;
            $item['cancelpolicy_deadline'] = date('D, d M Y', strtotime($End));
            $item['cancelpolicy_deadlinetimestamp'] = time();
        } else {
            $item['nonrefundable'] = false;
            $item['cancelpolicy'] = $CancelPolicies;
            $item['cancelpolicy_details'] = $CancelPolicies;
            $item['cancelpolicy_deadline'] = date('D, d M Y', strtotime($End));
        }
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mglobalia where sid='" . $shid . "' and hid=" . $hid;
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
error_log("\r\n TRAVELPLAN Policies Multi - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>