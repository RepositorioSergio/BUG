<?php
// error_log("\r\nStart - Omnibees - Multi Search\r\n", 3, "/srv/www/htdocs/error_log");
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
if (file_exists("src/App/language/" . $lang . ".mo")) {
    $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
}
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$omnibees = false;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml64, iata_code from cities where id=" . $destination;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml64 = $row_settings["city_xml64"];
    $iata_code = $row_settings["iata_code"];
} else {
    $city_xml64 = "";
    $iata_code = "";
}
$xmlresult = array();
if ($city_xml64 != "" or $iata_code != "") {
    $sql = "select value from settings where name='enableomnibees' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_omnibees = $affiliate_id;
    } else {
        $affiliate_id_omnibees = 0;
    }
    $sql = "select value from settings where name='omnibeesMarkup' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesMarkup = (double) $row_settings['value'];
    } else {
        $omnibeesMarkup = 0;
    }
    $sql = "select value from settings where name='omnibeesLoginEmail' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesLoginEmail = $row_settings['value'];
    }
    $sql = "select value from settings where name='omnibeesPassword' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='omnibeesTimeout' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesTimeout = (int) $row_settings['value'];
    } else {
        $omnibeesTimeout = 0;
    }
    if ($omnibeesTimeout == "") {
        $omnibeesTimeout = 120;
    }
    $sql = "select value from settings where name='omnibeesServiceURL' and affiliate_id=$affiliate_id_omnibees";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $omnibeesServiceURL = $row_settings['value'];
    }
    // error_log("\r\nOmnibees Service URL: $omnibeesServiceURL", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nOmnibees Login: $omnibeesLoginEmail", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nOmnibees Pwd: $omnibeesPassword", 3, "/srv/www/htdocs/error_log");
    //
    // Overwrite (Remove After)
    //
    // $omnibeesServiceURL = 'https://pullcert.omnibees.com/PullService.svc?wsdl';
    // $omnibeesLoginEmail = 'BugSoftware';
    // $omnibeesPassword = 'WO5bYE2A';
    //
    // EOF Overwrite
    //
    if ($omnibeesServiceURL != "") {
        $nC = 0;
        $multiParallel = array();
        $multiParallel = curl_multi_init();
        $Culture = 'en_US';
        $Version = 9;
        for ($r = 0; $r < $rooms; $r ++) {
            // Get Hotel Avail
            $xmlrequest = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://schemas.datacontract.org/2004/07/Pull.BLL.Models" xmlns:ns2="http://tempuri.org/" xmlns:ns3="http://schemas.datacontract.org/2004/07/Pull.BLL.Models.OTA"><SOAP-ENV:Body><ns2:GetHotelAvail><ns2:login><ns1:Password>' . $omnibeesPassword . '</ns1:Password><ns1:UserName>' . $omnibeesLoginEmail . '</ns1:UserName></ns2:login><ns2:ota_HotelAvailRQ><ns3:BestOnly>false</ns3:BestOnly><ns3:EchoToken>' . sha1(mt_rand(1, 90000) . 'SALT') . '</ns3:EchoToken><ns3:HotelSearchCriteria><ns3:Criterion>';
            if ($city_xml64 != "") {
                $xmlrequest .= '<ns3:Address><ns3:CityCode>' . $city_xml64 . '</ns3:CityCode></ns3:Address>';
            } else {
                $xmlrequest .= '<ns3:Location><ns3:CodeContex>IATA</ns3:CodeContex><ns3:LocationCode>' . $iata_code . '</ns3:LocationCode></ns3:Location>';
            }
            $xmlrequest .= '<ns3:RoomStayCandidatesType><ns3:RoomStayCandidates><ns3:RoomStayCandidate><ns3:GuestCountsType><ns3:GuestCounts><ns3:GuestCount><ns3:AgeQualifyCode>Adult</ns3:AgeQualifyCode><ns3:Count>' . $selectedAdults[$r] . '</ns3:Count></ns3:GuestCount>';
            if ($selectedChildren[$r] > 0) {
                for ($z = 0; $z < $selectedChildren[$r]; $z ++) {
                    $xmlrequest .= '<ns3:GuestCount><ns3:Age>' . $selectedChildrenAges[$r][$z] . '</ns3:Age><ns3:AgeQualifyCode>Child</ns3:AgeQualifyCode><ns3:Count>1</ns3:Count></ns3:GuestCount>';
                }
            }
            $xmlrequest .= '</ns3:GuestCounts></ns3:GuestCountsType><ns3:Quantity>1</ns3:Quantity><ns3:RPH>0</ns3:RPH></ns3:RoomStayCandidate></ns3:RoomStayCandidates></ns3:RoomStayCandidatesType><ns3:StayDateRange><ns3:End>' . strftime("%Y-%m-%dT00:00:00", $to) . '</ns3:End><ns3:Start>' . strftime("%Y-%m-%dT00:00:00", $from) . '</ns3:Start></ns3:StayDateRange></ns3:Criterion></ns3:HotelSearchCriteria><ns3:PrimaryLangID>en</ns3:PrimaryLangID><ns3:Target>Production</ns3:Target><ns3:TimeStamp>' . strftime("%Y-%m-%dT%H:%m:%S", time()) . '</ns3:TimeStamp><ns3:Version>2.6</ns3:Version></ns2:ota_HotelAvailRQ></ns2:GetHotelAvail></SOAP-ENV:Body></SOAP-ENV:Envelope>';
            // error_log("\r\nOmnibees Request ($r) : $xmlrequest\r\n", 3, "/srv/www/htdocs/error_log");
            $action = "http://tempuri.org/IPull/GetHotelAvail";
            $headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "Accept: text/xml",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
                "SOAPAction: " . $action,
                "Content-length: " . strlen($xmlrequest)
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $omnibeesServiceURL);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $omnibeesTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $omnibeesTimeout);
            curl_multi_add_handle($multiParallel, $ch);
            $requestsParallel[$nC] = $r;
            $channelsParallel[$nC] = $ch;
            $nC ++;
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($multiParallel, $active);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($multiParallel) == - 1) {
                continue;
            }
            do {
                $mrc = curl_multi_exec($multiParallel, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            if ($mrc != CURLM_OK) {
                error_log("\r\nCurl Multi Exec Error:" . curl_multi_strerror($mrc) . "\r\n", 3, "/srv/www/htdocs/error_log");
            }
        }
        foreach ($channelsParallel as $zRooms => $channel) {
            $response = curl_multi_getcontent($channel);
            $xmlresult[$zRooms] = $response;
            $raw = $requestsParallel[$zRooms];
            //
            // Store Session
            //
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_omnibees_xml');
                $delete->where(array(
                    'session_id' => $session_id,
                    'sindex' => $zRooms
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_omnibees_xml');
                $insert->values(array(
                    'session_id' => $session_id,
                    'sindex' => $zRooms,
                    'xmlresult' => (string) $response
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            // EOF Store Session
            curl_multi_remove_handle($multiParallel, $channel);
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('log_omnibees');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'SearchHotelsMulti.php',
                    'errorline' => $zRooms,
                    'errormessage' => $xmlrequest,
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
                $validSearch = false;
            }
            // error_log("\r\nOmnibees Response - $zRooms ($raw): $response\r\n", 3, "/srv/www/htdocs/error_log");
            $inputDoc = new DOMDocument();
            $inputDoc->loadXML($response);
            if ($response != "") {
                $Envelope = $inputDoc->getElementsByTagName("Envelope");
                if ($Envelope->length > 0) {
                    $Body = $Envelope->item(0)->getElementsByTagName("Body");
                    if ($Body->length > 0) {
                        $GetHotelAvailResponse = $Body->item(0)->getElementsByTagName("GetHotelAvailResponse");
                        if ($GetHotelAvailResponse->length > 0) {
                            $GetHotelAvailResult = $GetHotelAvailResponse->item(0)->getElementsByTagName("GetHotelAvailResult");
                            if ($GetHotelAvailResult->length > 0) {
                                $RoomStaysType = $GetHotelAvailResult->item(0)->getElementsByTagName("RoomStaysType");
                                if ($RoomStaysType->length > 0) {
                                    $RoomStays = $RoomStaysType->item(0)->getElementsByTagName("RoomStays");
                                    if ($RoomStays->length > 0) {
                                        $node = $RoomStays->item(0)->getElementsByTagName("RoomStay");
                                        if ($node->length > 0) {
                                            for ($x = 0; $x < $node->length; $x ++) {
                                                $RatePlans = array();
                                                $Rooms = array();
                                                $RoomRates = array();
                                                $HotelRef = $node->item($x)->getElementsByTagName("HotelRef");
                                                if ($HotelRef->length > 0) {
                                                    $ChainCode = $HotelRef->item(0)->getElementsByTagName("ChainCode");
                                                    if ($ChainCode->length > 0) {
                                                        $ChainCode = $ChainCode->item(0)->nodeValue;
                                                    } else {
                                                        $ChainCode = "";
                                                    }
                                                    $HotelCode = $HotelRef->item(0)->getElementsByTagName("HotelCode");
                                                    if ($HotelCode->length > 0) {
                                                        $HotelCode = $HotelCode->item(0)->nodeValue;
                                                    } else {
                                                        $HotelCode = "";
                                                    }
                                                    $shid = $HotelCode;
                                                    $sfilter[] = " sid='$HotelCode' ";
                                                }
                                                // error_log("\r\nHotelCode: $HotelCode\r\n", 3, "/srv/www/htdocs/error_log");
                                                $aRatePlans = $node->item($x)->getElementsByTagName("RatePlans");
                                                if ($aRatePlans->length > 0) {
                                                    $aRatePlans = $aRatePlans->item(0)->getElementsByTagName("RatePlanType");
                                                    for ($z = 0; $z < $aRatePlans->length; $z ++) {
                                                        $RatePlanID = $aRatePlans->item($z)->getElementsByTagName("RatePlanID");
                                                        if ($RatePlanID->length > 0) {
                                                            $RatePlanID = $RatePlanID->item(0)->nodeValue;
                                                        } else {
                                                            $RatePlanID = "";
                                                        }
                                                        // error_log("\r\nRatePlanID: $RatePlanID \r\n", 3, "/srv/www/htdocs/error_log");
                                                        $RatePlanName = $aRatePlans->item($z)->getElementsByTagName("RatePlanName");
                                                        if ($RatePlanName->length > 0) {
                                                            $RatePlanName = $RatePlanName->item(0)->nodeValue;
                                                        } else {
                                                            $RatePlanName = "";
                                                        }
                                                        $RatePlanTypeCode = $aRatePlans->item($z)->getElementsByTagName("RatePlanTypeCode");
                                                        if ($RatePlanTypeCode->length > 0) {
                                                            $RatePlanTypeCode = $RatePlanTypeCode->item(0)->nodeValue;
                                                        } else {
                                                            $RatePlanTypeCode = "";
                                                        }
                                                        $SortOrder = $aRatePlans->item($z)->getElementsByTagName("SortOrder");
                                                        if ($SortOrder->length > 0) {
                                                            $SortOrder = $SortOrder->item(0)->nodeValue;
                                                        } else {
                                                            $SortOrder = "";
                                                        }
                                                        $RatePlanInclusions = $aRatePlans->item($z)->getElementsByTagName("RatePlanInclusions");
                                                        if ($RatePlanInclusions->length > 0) {
                                                            $RatePlanInclusions = $RatePlanInclusions->item(0)->nodeValue;
                                                        } else {
                                                            $RatePlanInclusions = "";
                                                        }
                                                        $RatePlanDescription = $aRatePlans->item($z)->getElementsByTagName("RatePlanDescription");
                                                        if ($RatePlanDescription->length > 0) {
                                                            $RatePlanDescriptionDescription = $RatePlanDescription->item(0)->getElementsByTagName("Description");
                                                            if ($RatePlanDescriptionDescription->length > 0) {
                                                                $RatePlanDescriptionDescription = $RatePlanDescriptionDescription->item(0)->nodeValue;
                                                            } else {
                                                                $RatePlanDescriptionDescription = "";
                                                            }
                                                            $RatePlanDescriptionLanguage = $RatePlanDescription->item(0)->getElementsByTagName("Language");
                                                            if ($RatePlanDescriptionLanguage->length > 0) {
                                                                $RatePlanDescriptionLanguage = $RatePlanDescriptionLanguage->item(0)->nodeValue;
                                                            } else {
                                                                $RatePlanDescriptionLanguage = "";
                                                            }
                                                        } else {
                                                            $RatePlanDescription = "";
                                                            $RatePlanDescriptionDescription = "";
                                                            $RatePlanDescriptionLanguage = "";
                                                        }
                                                        $RatePlans[$RatePlanID]['RatePlanID'] = $RatePlanID;
                                                        $RatePlans[$RatePlanID]['RatePlanName'] = $RatePlanName;
                                                        $RatePlans[$RatePlanID]['RatePlanTypeCode'] = $RatePlanTypeCode;
                                                        $RatePlans[$RatePlanID]['SortOrder'] = $SortOrder;
                                                        $RatePlans[$RatePlanID]['RatePlanInclusions'] = $RatePlanInclusions;
                                                        $RatePlans[$RatePlanID]['RatePlanDescriptionDescription'] = $RatePlanDescriptionDescription;
                                                        $RatePlans[$RatePlanID]['RatePlanDescriptionLanguage'] = $RatePlanDescriptionLanguage;
                                                        $Aux = array();
                                                        $AdditionalDetailsType = $aRatePlans->item($z)->getElementsByTagName("AdditionalDetailsType");
                                                        if ($AdditionalDetailsType->length > 0) {
                                                            $AdditionalDetails = $AdditionalDetailsType->item(0)->getElementsByTagName("AdditionalDetails");
                                                            if ($AdditionalDetails->length > 0) {
                                                                $AdditionalDetails->item(0)->getElementsByTagName("AdditionalDetail");
                                                                for ($i = 0; $i < $AdditionalDetails->length; $i ++) {
                                                                    $DetailDescription = $AdditionalDetails->item($i)->getElementsByTagName("DetailDescription");
                                                                    if ($DetailDescription->length > 0) {
                                                                        $Description = $DetailDescription->item(0)->getElementsByTagName("Description");
                                                                        if ($Description->length > 0) {
                                                                            $Description = $Description->item(0)->nodeValue;
                                                                        } else {
                                                                            $Description = "";
                                                                        }
                                                                        $Language = $DetailDescription->item(0)->getElementsByTagName("Language");
                                                                        if ($Language->length > 0) {
                                                                            $Language = $Language->item(0)->nodeValue;
                                                                        } else {
                                                                            $Language = "";
                                                                        }
                                                                        $Name = $DetailDescription->item(0)->getElementsByTagName("Name");
                                                                        if ($Name->length > 0) {
                                                                            $Name = $Name->item(0)->nodeValue;
                                                                        } else {
                                                                            $Name = "";
                                                                        }
                                                                        $Inf = array();
                                                                        $Inf['Name'] = $Name;
                                                                        $Inf['Language'] = $Language;
                                                                        $Inf['Description'] = $Description;
                                                                        array_push($Aux, $Inf);
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        $RatePlans[$RatePlanID]['AdditionalDetails'] = $Aux;
                                                        // Payment Policies
                                                        $ppolicies = array();
                                                        $PaymentPolicies = $aRatePlans->item($z)->getElementsByTagName("PaymentPolicies");
                                                        if ($PaymentPolicies->length > 0) {
                                                            $AcceptedPayment = $PaymentPolicies->item(0)->getElementsByTagName("AcceptedPayment");
                                                            for ($wAcceptedPayment = 0; $wAcceptedPayment < $AcceptedPayment->length; $wAcceptedPayment ++) {
                                                                $GuaranteeID = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("GuaranteeID");
                                                                if ($GuaranteeID->length > 0) {
                                                                    $GuaranteeID = $GuaranteeID->item(0)->nodeValue;
                                                                } else {
                                                                    $GuaranteeID = "";
                                                                }
                                                                $ppolicies[$wAcceptedPayment]['GuaranteeID'] = $GuaranteeID;
                                                                $GuaranteeTypeCode = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("GuaranteeTypeCode");
                                                                if ($GuaranteeTypeCode->length > 0) {
                                                                    $GuaranteeTypeCode = $GuaranteeTypeCode->item(0)->nodeValue;
                                                                } else {
                                                                    $GuaranteeTypeCode = "";
                                                                }
                                                                $ppolicies[$wAcceptedPayment]['GuaranteeTypeCode'] = $GuaranteeTypeCode;
                                                                $PaymentCard = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("PaymentCard");
                                                                if ($PaymentCard->length > 0) {
                                                                    $PaymentCard = $PaymentCard->item(0)->nodeValue;
                                                                } else {
                                                                    $PaymentCard = "";
                                                                }
                                                                $ppolicies[$wAcceptedPayment]['PaymentCard'] = $PaymentCard;
                                                                $RPH = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("RPH");
                                                                if ($RPH->length > 0) {
                                                                    $RPH = $RPH->item(0)->nodeValue;
                                                                } else {
                                                                    $RPH = "";
                                                                }
                                                                $ppolicies[$wAcceptedPayment]['RPH'] = $RPH;
                                                            }
                                                        }
                                                        $RatePlans[$RatePlanID]['PaymentPolicies'] = $ppolicies;
                                                        // Guarantees
                                                        $gguarantees = array();
                                                        $Guarantees = $aRatePlans->item($z)->getElementsByTagName("Guarantees");
                                                        if ($Guarantees->length > 0) {
                                                            $Guarantee = $Guarantees->item(0)->getElementsByTagName("Guarantee");
                                                            for ($wGuarantee = 0; $wGuarantee < $Guarantee->length; $wGuarantee ++) {
                                                                $AmountPercent = $Guarantee->item(0)->getElementsByTagName("AmountPercent");
                                                                if ($AmountPercent->length > 0) {
                                                                    $AmountPercent = $AmountPercent->item(0)->nodeValue;
                                                                } else {
                                                                    $AmountPercent = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['AmountPercent'] = $AmountPercent;
                                                                $DeadLine = $Guarantee->item(0)->getElementsByTagName("DeadLine");
                                                                if ($DeadLine->length > 0) {
                                                                    $DeadLine = $DeadLine->item(0)->nodeValue;
                                                                } else {
                                                                    $DeadLine = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['DeadLine'] = $DeadLine;
                                                                $Duration = $Guarantee->item(0)->getElementsByTagName("Duration");
                                                                if ($Duration->length > 0) {
                                                                    $Duration = $Duration->item(0)->nodeValue;
                                                                } else {
                                                                    $Duration = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['Duration'] = $Duration;
                                                                $End = $Guarantee->item(0)->getElementsByTagName("End");
                                                                if ($End->length > 0) {
                                                                    $End = $End->item(0)->nodeValue;
                                                                } else {
                                                                    $End = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['End'] = $End;
                                                                $GuaranteeCode = $Guarantee->item(0)->getElementsByTagName("GuaranteeCode");
                                                                if ($GuaranteeCode->length > 0) {
                                                                    $GuaranteeCode = $GuaranteeCode->item(0)->nodeValue;
                                                                } else {
                                                                    $GuaranteeCode = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['GuaranteeCode'] = $GuaranteeCode;
                                                                $Start = $Guarantee->item(0)->getElementsByTagName("Start");
                                                                if ($Start->length > 0) {
                                                                    $Start = $Start->item(0)->nodeValue;
                                                                } else {
                                                                    $Start = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['Start'] = $Start;
                                                                $GuaranteesAcceptedType = $Guarantee->item(0)->getElementsByTagName("GuaranteesAcceptedType");
                                                                if ($GuaranteesAcceptedType->length > 0) {
                                                                    $GuaranteesAcceptedType = $GuaranteesAcceptedType->item(0)->nodeValue;
                                                                } else {
                                                                    $GuaranteesAcceptedType = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['GuaranteesAcceptedType'] = $GuaranteesAcceptedType;
                                                                $GuaranteeDescription = $Guarantee->item(0)->getElementsByTagName("GuaranteeDescription");
                                                                if ($GuaranteeDescription->length > 0) {
                                                                    $GuaranteeDescriptionDescription = $GuaranteeDescription->item(0)->getElementsByTagName("Description");
                                                                    if ($GuaranteeDescriptionDescription->length > 0) {
                                                                        $GuaranteeDescriptionDescription = $GuaranteeDescriptionDescription->item(0)->nodeValue;
                                                                    } else {
                                                                        $GuaranteeDescriptionDescription = "";
                                                                    }
                                                                    $GuaranteeDescriptionName = $GuaranteeDescription->item(0)->getElementsByTagName("Name");
                                                                    if ($GuaranteeDescriptionName->length > 0) {
                                                                        $GuaranteeDescriptionName = $GuaranteeDescriptionName->item(0)->nodeValue;
                                                                    } else {
                                                                        $GuaranteeDescriptionName = "";
                                                                    }
                                                                    $GuaranteeDescriptionLanguage = $GuaranteeDescription->item(0)->getElementsByTagName("Language");
                                                                    if ($GuaranteeDescriptionLanguage->length > 0) {
                                                                        $GuaranteeDescriptionLanguage = $GuaranteeDescriptionLanguage->item(0)->nodeValue;
                                                                    } else {
                                                                        $GuaranteeDescriptionLanguage = "";
                                                                    }
                                                                } else {
                                                                    $GuaranteeDescriptionDescription = "";
                                                                    $GuaranteeDescriptionLanguage = "";
                                                                    $GuaranteeDescriptionName = "";
                                                                }
                                                                $gguarantees[$wGuarantee]['GuaranteeDescription'] = $GuaranteeDescriptionDescription;
                                                                $gguarantees[$wGuarantee]['GuaranteeDescriptionLanguage'] = $GuaranteeDescriptionLanguage;
                                                                $gguarantees[$wGuarantee]['GuaranteeDescriptionName'] = $GuaranteeDescriptionName;
                                                            }
                                                        }
                                                        $RatePlans[$RatePlanID]['Guarantees2'] = $gguarantees;
                                                        // Offers
                                                        $ooffers = array();
                                                        $Offers = $aRatePlans->item($z)->getElementsByTagName("Offers");
                                                        if ($Offers->length > 0) {
                                                            $Offer = $Offers->item(0)->getElementsByTagName("Offer");
                                                            for ($wOffer = 0; $wOffer < $Offer->length; $wOffer ++) {
                                                                $CompatibleOffer = $Offer->item(0)->getElementsByTagName("CompatibleOffer");
                                                                if ($CompatibleOffer->length > 0) {
                                                                    $IncompatibleOfferIndicator = $CompatibleOffer->item(0)->getElementsByTagName("IncompatibleOfferIndicator");
                                                                    if ($IncompatibleOfferIndicator->length > 0) {
                                                                        $IncompatibleOfferIndicator = $IncompatibleOfferIndicator->item(0)->nodeValue;
                                                                    } else {
                                                                        $IncompatibleOfferIndicator = "";
                                                                    }
                                                                    $CompatibleOffer = "";
                                                                } else {
                                                                    $IncompatibleOfferIndicator = "";
                                                                    $CompatibleOffer = "";
                                                                }
                                                                $ooffers[$wOffer]['CompatibleOffer'] = $CompatibleOffer;
                                                                $ooffers[$wOffer]['IncompatibleOfferIndicator'] = $IncompatibleOfferIndicator;
                                                                $OfferCode = $Offer->item(0)->getElementsByTagName("OfferCode");
                                                                if ($OfferCode->length > 0) {
                                                                    $OfferCode = $OfferCode->item(0)->nodeValue;
                                                                } else {
                                                                    $OfferCode = "";
                                                                }
                                                                $ooffers[$wOffer]['OfferCode'] = $OfferCode;
                                                                $RPH = $Offer->item(0)->getElementsByTagName("RPH");
                                                                if ($RPH->length > 0) {
                                                                    $RPH = $RPH->item(0)->nodeValue;
                                                                } else {
                                                                    $RPH = "";
                                                                }
                                                                $ooffers[$wOffer]['RPH'] = $RPH;
                                                                $End = $Offer->item(0)->getElementsByTagName("End");
                                                                if ($End->length > 0) {
                                                                    $End = $End->item(0)->nodeValue;
                                                                } else {
                                                                    $End = "";
                                                                }
                                                                $OfferDescription = $Offer->item(0)->getElementsByTagName("Description");
                                                                if ($OfferDescription->length > 0) {
                                                                    $OfferDescriptionDescription = $OfferDescription->item(0)->getElementsByTagName("Description");
                                                                    if ($OfferDescriptionDescription->length > 0) {
                                                                        $OfferDescriptionDescription = $OfferDescriptionDescription->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferDescriptionDescription = "";
                                                                    }
                                                                    $OfferDescriptionLanguage = $OfferDescription->item(0)->getElementsByTagName("Language");
                                                                    if ($OfferDescriptionLanguage->length > 0) {
                                                                        $OfferDescriptionLanguage = $OfferDescriptionLanguage->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferDescriptionLanguage = "";
                                                                    }
                                                                } else {
                                                                    $OfferDescriptionDescription = "";
                                                                    $OfferDescriptionLanguage = "";
                                                                }
                                                                $ooffers[$wOffer]['OfferDescription'] = $OfferDescriptionDescription;
                                                                $ooffers[$wOffer]['OfferDescriptionLanguage'] = $OfferDescriptionLanguage;
                                                                $OfferDiscount = $Offer->item(0)->getElementsByTagName("Discount");
                                                                if ($OfferDiscount->length > 0) {
                                                                    $OfferAmountBeforeTax = $OfferDiscount->item(0)->getElementsByTagName("AmountBeforeTax");
                                                                    if ($OfferAmountBeforeTax->length > 0) {
                                                                        $OfferAmountBeforeTax = $OfferAmountBeforeTax->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferAmountBeforeTax = "";
                                                                    }
                                                                    $OfferPercent = $OfferDiscount->item(0)->getElementsByTagName("Percent");
                                                                    if ($OfferPercent->length > 0) {
                                                                        $OfferPercent = $OfferPercent->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferPercent = "";
                                                                    }
                                                                    $OfferChargeUnitCode = $OfferDiscount->item(0)->getElementsByTagName("ChargeUnitCode");
                                                                    if ($OfferChargeUnitCode->length > 0) {
                                                                        $OfferChargeUnitCode = $OfferChargeUnitCode->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferChargeUnitCode = "";
                                                                    }
                                                                    $OfferDiscountCode = $OfferDiscount->item(0)->getElementsByTagName("DiscountCode");
                                                                    if ($OfferDiscountCode->length > 0) {
                                                                        $OfferDiscountCode = $OfferDiscountCode->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferDiscountCode = "";
                                                                    }
                                                                    $OfferDiscountPattern = $OfferDiscount->item(0)->getElementsByTagName("DiscountPattern");
                                                                    if ($OfferDiscountPattern->length > 0) {
                                                                        $OfferDiscountPattern = $OfferDiscountPattern->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferDiscountPattern = "";
                                                                    }
                                                                    $OfferDiscountReason = $OfferDiscount->item(0)->getElementsByTagName("DiscountReason");
                                                                    if ($OfferDiscountReason->length > 0) {
                                                                        $OfferDiscountReason = $OfferDiscountReason->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferDiscountReason = "";
                                                                    }
                                                                    $OfferNightsDiscounted = $OfferDiscount->item(0)->getElementsByTagName("NightsDiscounted");
                                                                    if ($OfferNightsDiscounted->length > 0) {
                                                                        $OfferNightsDiscounted = $OfferNightsDiscounted->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferNightsDiscounted = "";
                                                                    }
                                                                    $OfferNightsRequired = $OfferDiscount->item(0)->getElementsByTagName("NightsRequired");
                                                                    if ($OfferNightsRequired->length > 0) {
                                                                        $OfferNightsRequired = $OfferNightsRequired->item(0)->nodeValue;
                                                                    } else {
                                                                        $OfferNightsRequired = "";
                                                                    }
                                                                } else {
                                                                    $OfferAmountBeforeTax = "";
                                                                    $OfferChargeUnitCode = "";
                                                                    $OfferDiscountCode = "";
                                                                    $OfferDiscountPattern = "";
                                                                    $OfferNightsDiscounted = "";
                                                                    $OfferNightsRequired = "";
                                                                    $OfferPercent = "";
                                                                }
                                                                $ooffers[$wOffer]['AmountBeforeTax'] = $OfferAmountBeforeTax;
                                                                $ooffers[$wOffer]['ChargeUnitCode'] = $OfferChargeUnitCode;
                                                                $ooffers[$wOffer]['DiscountCode'] = $OfferDiscountCode;
                                                                $ooffers[$wOffer]['DiscountPattern'] = $OfferDiscountPattern;
                                                                $ooffers[$wOffer]['DiscountReason'] = $OfferDiscountReason;
                                                                $ooffers[$wOffer]['NightsDiscounted'] = $OfferNightsDiscounted;
                                                                $ooffers[$wOffer]['NightsRequired'] = $OfferNightsRequired;
                                                                $ooffers[$wOffer]['Percent'] = $OfferPercent;
                                                                //
                                                                // Offer Rules
                                                                //
                                                                $DateRestrictionsArray = array();
                                                                $OfferRules = $Offer->item(0)->getElementsByTagName("OfferRules");
                                                                if ($OfferRules->length > 0) {
                                                                    $OfferRule = $OfferRules->item(0)->getElementsByTagName("OfferRule");
                                                                    if ($OfferRule->length > 0) {
                                                                        $DateRestriction = $OfferRule->item(0)->getElementsByTagName("DateRestriction");
                                                                        if ($DateRestriction->length > 0) {
                                                                            $DateRestrictionDuration = $DateRestriction->item(0)->getElementsByTagName("Duration");
                                                                            $DateRestrictionStart = $DateRestriction->item(0)->getElementsByTagName("Start");
                                                                            $DateRestrictionEnd = $DateRestriction->item(0)->getElementsByTagName("End");
                                                                            for ($sDateRestrictionDuration = 0; $sDateRestrictionDuration < $DateRestrictionDuration->length; $sDateRestrictionDuration ++) {
                                                                                $DateRestrictionsArray[$sDateRestrictionDuration]['Duration'] = $DateRestrictionDuration->item($sDateRestrictionDuration)->nodeValue;
                                                                                $DateRestrictionsArray[$sDateRestrictionDuration]['Start'] = $DateRestrictionStart->item($sDateRestrictionDuration)->nodeValue;
                                                                                $DateRestrictionsArray[$sDateRestrictionDuration]['End'] = $DateRestrictionEnd->item($sDateRestrictionDuration)->nodeValue;
                                                                            }
                                                                            $DateRestriction = "";
                                                                        } else {
                                                                            $DateRestriction = "";
                                                                        }
                                                                        $LengthsOfStay = $OfferRule->item(0)->getElementsByTagName("LengthsOfStay");
                                                                        if ($LengthsOfStay->length > 0) {
                                                                            $LengthsOfStay = $LengthsOfStay->item(0)->nodeValue;
                                                                        } else {
                                                                            $LengthsOfStay = "";
                                                                        }
                                                                        $MaxAdvancedBookingOffset = $OfferRule->item(0)->getElementsByTagName("MaxAdvancedBookingOffset");
                                                                        if ($MaxAdvancedBookingOffset->length > 0) {
                                                                            $MaxAdvancedBookingOffset = $MaxAdvancedBookingOffset->item(0)->nodeValue;
                                                                        } else {
                                                                            $MaxAdvancedBookingOffset = "";
                                                                        }
                                                                        $MinAdvancedBookingOffset = $OfferRule->item(0)->getElementsByTagName("MinAdvancedBookingOffset");
                                                                        if ($MinAdvancedBookingOffset->length > 0) {
                                                                            $MinAdvancedBookingOffset = $MinAdvancedBookingOffset->item(0)->nodeValue;
                                                                        } else {
                                                                            $MinAdvancedBookingOffset = "";
                                                                        }
                                                                    } else {
                                                                        $OfferRules = "";
                                                                        $DateRestriction = "";
                                                                        $LengthsOfStay = "";
                                                                        $MinAdvancedBookingOffset = "";
                                                                        $MaxAdvancedBookingOffset = "";
                                                                    }
                                                                } else {
                                                                    $OfferRules = "";
                                                                    $DateRestriction = "";
                                                                    $LengthsOfStay = "";
                                                                    $MinAdvancedBookingOffset = "";
                                                                    $MaxAdvancedBookingOffset = "";
                                                                }
                                                                $ooffers[$wOffer]['OfferRules'] = $OfferRules;
                                                                $ooffers[$wOffer]['DateRestriction'] = $DateRestriction;
                                                                $ooffers[$wOffer]['DateRestrictionsArray'] = $DateRestrictionsArray;
                                                                $ooffers[$wOffer]['LengthsOfStay'] = $LengthsOfStay;
                                                                $ooffers[$wOffer]['MinAdvancedBookingOffset'] = $MinAdvancedBookingOffset;
                                                                $ooffers[$wOffer]['MaxAdvancedBookingOffset'] = $MaxAdvancedBookingOffset;
                                                            }
                                                        }
                                                        $RatePlans[$RatePlanID]['Offers'] = $ooffers;
                                                        // EOF
                                                        $Description = "";
                                                        $Dinner = false;
                                                        $Breakfast = false;
                                                        $ID = "";
                                                        $Lunch = false;
                                                        $MealPlanCode = "";
                                                        $MealPlanIndicator = "";
                                                        $Name = "";
                                                        $MealsIncluded = $aRatePlans->item($z)->getElementsByTagName("MealsIncluded");
                                                        if ($MealsIncluded->length > 0) {
                                                            $Description = $MealsIncluded->item(0)->getElementsByTagName("Description");
                                                            if ($Description->length > 0) {
                                                                $Description = $Description->item(0)->nodeValue;
                                                            } else {
                                                                $Description = "";
                                                            }
                                                            $Dinner = $MealsIncluded->item(0)->getElementsByTagName("Dinner");
                                                            if ($Dinner->length > 0) {
                                                                $Dinner = $Dinner->item(0)->nodeValue;
                                                            } else {
                                                                $Dinner = false;
                                                            }
                                                            $Breakfast = $MealsIncluded->item(0)->getElementsByTagName("Breakfast");
                                                            if ($Breakfast->length > 0) {
                                                                $Breakfast = $Breakfast->item(0)->nodeValue;
                                                            } else {
                                                                $Breakfast = false;
                                                            }
                                                            $ID = $MealsIncluded->item(0)->getElementsByTagName("ID");
                                                            if ($ID->length > 0) {
                                                                $ID = $ID->item(0)->nodeValue;
                                                            } else {
                                                                $ID = "";
                                                            }
                                                            $Lunch = $MealsIncluded->item(0)->getElementsByTagName("Lunch");
                                                            if ($Lunch->length > 0) {
                                                                $Lunch = $Lunch->item(0)->nodeValue;
                                                            } else {
                                                                $Lunch = false;
                                                            }
                                                            $MealPlanCode = $MealsIncluded->item(0)->getElementsByTagName("MealPlanCode");
                                                            if ($MealPlanCode->length > 0) {
                                                                $MealPlanCode = $MealPlanCode->item(0)->nodeValue;
                                                            } else {
                                                                $MealPlanCode = "";
                                                            }
                                                            $MealPlanIndicator = $MealsIncluded->item(0)->getElementsByTagName("MealPlanIndicator");
                                                            if ($MealPlanIndicator->length > 0) {
                                                                $MealPlanIndicator = $MealPlanIndicator->item(0)->nodeValue;
                                                            } else {
                                                                $MealPlanIndicator = false;
                                                            }
                                                            $Name = $MealsIncluded->item(0)->getElementsByTagName("Name");
                                                            if ($Name->length > 0) {
                                                                $Name = $Name->item(0)->nodeValue;
                                                            } else {
                                                                $Name = "";
                                                            }
                                                        }
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['Description'] = $Description;
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['Dinner'] = $Dinner;
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['Breakfast'] = $Breakfast;
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['ID'] = $ID;
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['Lunch'] = $Lunch;
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['MealPlanCode'] = $MealPlanCode;
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['MealPlanIndicator'] = $MealPlanIndicator;
                                                        $RatePlans[$RatePlanID]['MealsIncluded']['Name'] = $Name;
                                                        $Taxes = array();
                                                        $TaxPolicies = $aRatePlans->item($z)->getElementsByTagName("TaxPolicies");
                                                        if ($TaxPolicies->length > 0) {
                                                            $TaxPolicies = $TaxPolicies->item(0)->getElementsByTagName("TaxPolicy");
                                                            for ($i = 0; $i < $TaxPolicies->length; $i ++) {
                                                                $CurrencyCode = $TaxPolicies->item($i)->getElementsByTagName("CurrencyCode");
                                                                if ($CurrencyCode->length > 0) {
                                                                    $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                                                                } else {
                                                                    $CurrencyCode = "";
                                                                }
                                                                $Description = $TaxPolicies->item($i)->getElementsByTagName("Description");
                                                                if ($Description->length > 0) {
                                                                    $Description = $Description->item(0)->nodeValue;
                                                                } else {
                                                                    $Description = "";
                                                                }
                                                                $ID = $TaxPolicies->item($i)->getElementsByTagName("ID");
                                                                if ($ID->length > 0) {
                                                                    $ID = $ID->item(0)->nodeValue;
                                                                } else {
                                                                    $ID = "";
                                                                }
                                                                $IsPerNight = $TaxPolicies->item($i)->getElementsByTagName("IsPerNight");
                                                                if ($IsPerNight->length > 0) {
                                                                    $IsPerNight = $IsPerNight->item(0)->nodeValue;
                                                                } else {
                                                                    $IsPerNight = "";
                                                                }
                                                                $IsPerPerson = $TaxPolicies->item($i)->getElementsByTagName("IsPerPerson");
                                                                if ($IsPerPerson->length > 0) {
                                                                    $IsPerPerson = $IsPerPerson->item(0)->nodeValue;
                                                                } else {
                                                                    $IsPerPerson = "";
                                                                }
                                                                $IsPerRoom = $TaxPolicies->item($i)->getElementsByTagName("IsPerRoom");
                                                                if ($IsPerRoom->length > 0) {
                                                                    $IsPerRoom = $IsPerRoom->item(0)->nodeValue;
                                                                } else {
                                                                    $IsPerRoom = "";
                                                                }
                                                                $IsPerStay = $TaxPolicies->item($i)->getElementsByTagName("IsPerStay");
                                                                if ($IsPerStay->length > 0) {
                                                                    $IsPerStay = $IsPerStay->item(0)->nodeValue;
                                                                } else {
                                                                    $IsPerStay = "";
                                                                }
                                                                $IsValuePercentage = $TaxPolicies->item($i)->getElementsByTagName("IsValuePercentage");
                                                                if ($IsValuePercentage->length > 0) {
                                                                    $IsValuePercentage = $IsValuePercentage->item(0)->nodeValue;
                                                                } else {
                                                                    $IsValuePercentage = "";
                                                                }
                                                                $Name = $TaxPolicies->item($i)->getElementsByTagName("Name");
                                                                if ($Name->length > 0) {
                                                                    $Name = $Name->item(0)->nodeValue;
                                                                } else {
                                                                    $Name = "";
                                                                }
                                                                $Value = $TaxPolicies->item($i)->getElementsByTagName("Value");
                                                                if ($Value->length > 0) {
                                                                    $Value = $Value->item(0)->nodeValue;
                                                                } else {
                                                                    $Value = "";
                                                                }
                                                                $Tax = array();
                                                                $Tax['CurrencyCode'] = $CurrencyCode;
                                                                $Tax['Description'] = $Description;
                                                                $Tax['ID'] = $ID;
                                                                $IsPerNight = ($IsPerNight === 'true');
                                                                $Tax['IsPerNight'] = $IsPerNight;
                                                                $IsPerPerson = ($IsPerPerson === 'true');
                                                                $Tax['IsPerPerson'] = $IsPerPerson;
                                                                $IsPerRoom = ($IsPerRoom === 'true');
                                                                $Tax['IsPerRoom'] = $IsPerRoom;
                                                                $IsPerStay = ($IsPerStay === 'true');
                                                                $Tax['IsPerStay'] = $IsPerStay;
                                                                $IsValuePercentage = ($IsValuePercentage === 'true');
                                                                $Tax['IsValuePercentage'] = $IsValuePercentage;
                                                                $Tax['Name'] = $Name;
                                                                $Tax['Value'] = $Value;
                                                                array_push($Taxes, $Tax);
                                                            }
                                                        }
                                                        $RatePlans[$RatePlanID]['Taxes'] = $Taxes;
                                                        $Guarantees = array();
                                                        $Guarantee = $aRatePlans->item($z)->getElementsByTagName("Guarantee");
                                                        for ($i = 0; $i < $Guarantee->length; $i ++) {
                                                            $Aux = array();
                                                            $AmountPercent = $Guarantee->item($i)->getElementsByTagName("AmountPercent");
                                                            if ($AmountPercent->length > 0) {
                                                                $Aux['AmountPercent'] = $AmountPercent->item(0)->nodeValue;
                                                            }
                                                            $DeadLine = $Guarantee->item($i)->getElementsByTagName("DeadLine");
                                                            if ($DeadLine->length > 0) {
                                                                $Aux['DeadLine'] = $DeadLine->item(0)->nodeValue;
                                                            }
                                                            $Duration = $Guarantee->item($i)->getElementsByTagName("Duration");
                                                            if ($Duration->length > 0) {
                                                                $Aux['Duration'] = $Duration->item(0)->nodeValue;
                                                            }
                                                            $End = $Guarantee->item($i)->getElementsByTagName("End");
                                                            if ($End->length > 0) {
                                                                $Aux['End'] = $End->item(0)->nodeValue;
                                                            }
                                                            $GuaranteeCode = $Guarantee->item($i)->getElementsByTagName("GuaranteeCode");
                                                            if ($GuaranteeCode->length > 0) {
                                                                $Aux['GuaranteeCode'] = $GuaranteeCode->item(0)->nodeValue;
                                                            }
                                                            $GuaranteesAcceptedType = $Guarantee->item($i)->getElementsByTagName("GuaranteesAcceptedType");
                                                            if ($GuaranteesAcceptedType->length > 0) {
                                                                $Aux['GuaranteesAcceptedType'] = $GuaranteesAcceptedType->item(0)->nodeValue;
                                                            }
                                                            $Start = $Guarantee->item($i)->getElementsByTagName("Start");
                                                            if ($Start->length > 0) {
                                                                $Aux['Start'] = $Start->item(0)->nodeValue;
                                                            }
                                                            $GuaranteeDescription = $Guarantee->item($i)->getElementsByTagName("GuaranteeDescription");
                                                            if ($GuaranteeDescription->length > 0) {
                                                                $Description = $GuaranteeDescription->item(0)->getElementsByTagName("Description");
                                                                if ($Description->length > 0) {
                                                                    $Aux['Description'] = $Description->item(0)->nodeValue;
                                                                }
                                                                $Language = $Guarantee->item(0)->getElementsByTagName("Language");
                                                                if ($Language->length > 0) {
                                                                    $Aux['Language'] = $Language->item(0)->nodeValue;
                                                                }
                                                                $Name = $Guarantee->item(0)->getElementsByTagName("Name");
                                                                if ($Name->length > 0) {
                                                                    $Aux['Name'] = $Name->item(0)->nodeValue;
                                                                }
                                                            }
                                                            array_push($Guarantees, $Aux);
                                                        }
                                                        $CurrencyCode = $aRatePlans->item($z)->getElementsByTagName("CurrencyCode");
                                                        if ($CurrencyCode->length > 0) {
                                                            $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                                                        } else {
                                                            $CurrencyCode = "";
                                                        }
                                                        $Cancel = array();
                                                        $CancelPenalties = $aRatePlans->item($z)->getElementsByTagName("CancelPenalties");
                                                        if ($CancelPenalties->length > 0) {
                                                            $CancelPenalties = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                                                            for ($i = 0; $i < $CancelPenalties->length; $i ++) {
                                                                $NonRefundable = $CancelPenalties->item($i)->getElementsByTagName("NonRefundable");
                                                                if ($NonRefundable->length > 0) {
                                                                    $NonRefundable = $NonRefundable->item(0)->nodeValue;
                                                                } else {
                                                                    $NonRefundable = "";
                                                                }
                                                                $Start = $CancelPenalties->item($i)->getElementsByTagName("Start");
                                                                if ($Start->length > 0) {
                                                                    $Start = $Start->item(0)->nodeValue;
                                                                } else {
                                                                    $Start = "";
                                                                }
                                                                $End = $CancelPenalties->item($i)->getElementsByTagName("End");
                                                                if ($End->length > 0) {
                                                                    $End = $End->item(0)->nodeValue;
                                                                } else {
                                                                    $End = "";
                                                                }
                                                                $Duration = $CancelPenalties->item($i)->getElementsByTagName("Duration");
                                                                if ($Duration->length > 0) {
                                                                    $Duration = $Duration->item(0)->nodeValue;
                                                                } else {
                                                                    $Duration = "";
                                                                }
                                                                $PenaltyDescription = $CancelPenalties->item($i)->getElementsByTagName("PenaltyDescription");
                                                                if ($PenaltyDescription->length > 0) {
                                                                    $PenaltyLanguage = $PenaltyDescription->item(0)->getElementsByTagName("Language");
                                                                    if ($PenaltyLanguage->length > 0) {
                                                                        $PenaltyLanguage = $PenaltyLanguage->item(0)->nodeValue;
                                                                    } else {
                                                                        $PenaltyLanguage = "";
                                                                    }
                                                                    $PenaltyName = $PenaltyDescription->item(0)->getElementsByTagName("Name");
                                                                    if ($PenaltyName->length > 0) {
                                                                        $PenaltyName = $PenaltyName->item(0)->nodeValue;
                                                                    } else {
                                                                        $PenaltyName = "";
                                                                    }
                                                                    $PenaltyDescription = $PenaltyDescription->item(0)->getElementsByTagName("Description");
                                                                    if ($PenaltyDescription->length > 0) {
                                                                        $PenaltyDescription = $PenaltyDescription->item(0)->nodeValue;
                                                                    } else {
                                                                        $PenaltyDescription = "";
                                                                    }
                                                                } else {
                                                                    $PenaltyLanguage = "";
                                                                    $PenaltyName = "";
                                                                    $PenaltyDescription = "";
                                                                }
                                                                $IsPerRoom = $CancelPenalties->item($i)->getElementsByTagName("IsPerRoom");
                                                                if ($IsPerRoom->length > 0) {
                                                                    $IsPerRoom = $IsPerRoom->item(0)->nodeValue;
                                                                } else {
                                                                    $IsPerRoom = "";
                                                                }
                                                                $IsPerStay = $CancelPenalties->item($i)->getElementsByTagName("IsPerStay");
                                                                if ($IsPerStay->length > 0) {
                                                                    $IsPerStay = $IsPerStay->item(0)->nodeValue;
                                                                } else {
                                                                    $IsPerStay = "";
                                                                }
                                                                $DeadLine = $CancelPenalties->item($i)->getElementsByTagName("DeadLine");
                                                                if ($DeadLine->length > 0) {
                                                                    $AbsoluteDeadline = $DeadLine->item(0)->getElementsByTagName("AbsoluteDeadline");
                                                                    if ($AbsoluteDeadline->length > 0) {
                                                                        $AbsoluteDeadline = $AbsoluteDeadline->item(0)->nodeValue;
                                                                    } else {
                                                                        $AbsoluteDeadline = "";
                                                                    }
                                                                    $OffsetDropTime = $DeadLine->item(0)->getElementsByTagName("OffsetDropTime");
                                                                    if ($OffsetDropTime->length > 0) {
                                                                        $OffsetDropTime = $OffsetDropTime->item(0)->nodeValue;
                                                                    } else {
                                                                        $OffsetDropTime = "";
                                                                    }
                                                                    $OffsetUnitMultiplier = $DeadLine->item(0)->getElementsByTagName("OffsetUnitMultiplier");
                                                                    if ($OffsetUnitMultiplier->length > 0) {
                                                                        $OffsetUnitMultiplier = $OffsetUnitMultiplier->item(0)->nodeValue;
                                                                    } else {
                                                                        $OffsetUnitMultiplier = "";
                                                                    }
                                                                    $TimeUnitType = $DeadLine->item(0)->getElementsByTagName("TimeUnitType");
                                                                    if ($TimeUnitType->length > 0) {
                                                                        $TimeUnitType = $TimeUnitType->item(0)->nodeValue;
                                                                    } else {
                                                                        $TimeUnitType = "";
                                                                    }
                                                                } else {
                                                                    $AbsoluteDeadline = "";
                                                                    $OffsetDropTime = "";
                                                                    $OffsetUnitMultiplier = "";
                                                                    $TimeUnitType = "";
                                                                }
                                                                $AmountPercent = $CancelPenalties->item($i)->getElementsByTagName("AmountPercent");
                                                                if ($AmountPercent->length > 0) {
                                                                    $Amount = $AmountPercent->item(0)->getElementsByTagName("Amount");
                                                                    if ($Amount->length) {
                                                                        $Amount = $Amount->item(0)->nodeValue;
                                                                    } else {
                                                                        $Amount = 0;
                                                                    }
                                                                    $CurrencyCode = $AmountPercent->item(0)->getElementsByTagName("CurrencyCode");
                                                                    if ($CurrencyCode->length) {
                                                                        $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                                                                    } else {
                                                                        $CurrencyCode = "";
                                                                    }
                                                                    $NmbrOfNights = $AmountPercent->item(0)->getElementsByTagName("NmbrOfNights");
                                                                    if ($NmbrOfNights->length) {
                                                                        $NmbrOfNights = $NmbrOfNights->item(0)->nodeValue;
                                                                    } else {
                                                                        $NmbrOfNights = 0;
                                                                    }
                                                                    $Percent = $AmountPercent->item(0)->getElementsByTagName("Percent");
                                                                    if ($Percent->length) {
                                                                        $Percent = $Percent->item(0)->nodeValue;
                                                                    } else {
                                                                        $Percent = 0;
                                                                    }
                                                                } else {
                                                                    $AmountPercent = "";
                                                                    $Amount = 0;
                                                                    $CurrencyCode = "";
                                                                    $Percent = 0;
                                                                    $NmbrOfNights = 0;
                                                                }
                                                                $Penalty = array();
                                                                $NonRefundable = ($NonRefundable === 'true');
                                                                $Penalty['NonRefundable'] = $NonRefundable;
                                                                $Penalty['Start'] = $Start;
                                                                $Penalty['End'] = $End;
                                                                $Penalty['Duration'] = $Duration;
                                                                $Penalty['PenaltyDescription'] = $PenaltyDescription;
                                                                $Penalty['PenaltyLanguage'] = $PenaltyLanguage;
                                                                $Penalty['PenaltyName'] = $PenaltyName;
                                                                $Penalty['IsPerRoom'] = $IsPerRoom;
                                                                $Penalty['IsPerStay'] = $IsPerStay;
                                                                $Penalty['AbsoluteDeadline'] = $AbsoluteDeadline;
                                                                $Penalty['OffsetDropTime'] = $OffsetDropTime;
                                                                $Penalty['OffsetUnitMultiplier'] = $OffsetUnitMultiplier;
                                                                $Penalty['TimeUnitType'] = $TimeUnitType;
                                                                $Penalty['Amount'] = $Amount;
                                                                $Penalty['CurrencyCode'] = $CurrencyCode;
                                                                $Penalty['NmbrOfNights'] = $NmbrOfNights;
                                                                $Penalty['Percent'] = $Percent;
                                                                if ($NonRefundable == 1) {
                                                                    $Penalty['cancelpolicy_deadline'] = time();
                                                                } else {
                                                                    if ($TimeUnitType == "Day") {
                                                                        if ($OffsetDropTime == "BeforeArrival") {
                                                                            $Penalty['cancelpolicy_deadline'] = mktime(0, 0, 0, date("m", $from), date("d", $from) - $OffsetUnitMultiplier, date("y", $from));
                                                                        } else {
                                                                            error_log("\r\nOmnibees - Unable to handle Offset Drop Time -  $OffsetDropTime", 3, "/srv/www/htdocs/error_log");
                                                                        }
                                                                    } else {
                                                                        error_log("\r\nOmnibees - Unable to handle Time Unit Type -  $TimeUnitType - $NonRefundable", 3, "/srv/www/htdocs/error_log");
                                                                        $Penalty['cancelpolicy_deadline'] = 0;
                                                                    }
                                                                }
                                                                array_push($Cancel, $Penalty);
                                                            }
                                                        }
                                                        $RatePlans[$RatePlanID]['CurrencyCode'] = $CurrencyCode;
                                                        $RatePlans[$RatePlanID]['CancelPenalties'] = $Cancel;
                                                        $RatePlans[$RatePlanID]['Guarantees'] = $Guarantees;
                                                    }
                                                }
                                                $aRoomTypes = $node->item($x)->getElementsByTagName("RoomTypes");
                                                if ($aRoomTypes->length > 0) {
                                                    $aRoomTypes = $aRoomTypes->item(0)->getElementsByTagName("RoomType");
                                                    for ($z = 0; $z < $aRoomTypes->length; $z ++) {
                                                        $RoomID = $aRoomTypes->item($z)->getElementsByTagName("RoomID");
                                                        if ($RoomID->length > 0) {
                                                            $RoomID = $RoomID->item(0)->nodeValue;
                                                        } else {
                                                            $RoomID = "";
                                                        }
                                                        $RoomName = $aRoomTypes->item($z)->getElementsByTagName("RoomName");
                                                        if ($RoomName->length > 0) {
                                                            $RoomName = $RoomName->item(0)->nodeValue;
                                                        } else {
                                                            $RoomName = "";
                                                        }
                                                        $MaxOccupancyAux = $aRoomTypes->item($z)->getElementsByTagName("MaxOccupancy");
                                                        if ($MaxOccupancyAux->length > 0) {
                                                            $MaxOccupancy = $MaxOccupancyAux->item(0)->nodeValue;
                                                        } else {
                                                            $MaxOccupancy = "";
                                                        }
                                                        $NumberOfUnits = $aRoomTypes->item($z)->getElementsByTagName("NumberOfUnits");
                                                        if ($NumberOfUnits->length > 0) {
                                                            $NumberOfUnits = $NumberOfUnits->item(0)->nodeValue;
                                                        } else {
                                                            $NumberOfUnits = "";
                                                        }
                                                        // TODO / AmenitiesType
                                                        $RoomDescription = $aRoomTypes->item($z)->getElementsByTagName("RoomDescription");
                                                        if ($RoomDescription->length > 0) {
                                                            $RoomLanguage = $RoomDescription->item(0)->getElementsByTagName("Language");
                                                            if ($RoomLanguage->length > 0) {
                                                                $RoomLanguage = $RoomLanguage->item(0)->nodeValue;
                                                            } else {
                                                                $RoomLanguage = "";
                                                            }
                                                            $RoomDescription = $RoomDescription->item(0)->getElementsByTagName("Description");
                                                            if ($RoomDescription->length > 0) {
                                                                $RoomDescription = $RoomDescription->item(0)->nodeValue;
                                                            } else {
                                                                $RoomDescription = "";
                                                            }
                                                        } else {
                                                            $RoomDescription = "";
                                                            $RoomLanguage = "";
                                                        }
                                                        $Occupancies = array();
                                                        $Occupancy = $aRoomTypes->item($z)->getElementsByTagName("Occupancy");
                                                        for ($iu = 0; $iu < $Occupancy->length; $iu ++) {
                                                            $Occup = array();
                                                            $AgeQualifyingCode = $Occupancy->item($iu)->getElementsByTagName("AgeQualifyingCode");
                                                            if ($AgeQualifyingCode->length > 0) {
                                                                $Occup['AgeQualifyingCode'] = $AgeQualifyingCode->item(0)->nodeValue;
                                                            }
                                                            $MaxAge = $Occupancy->item($iu)->getElementsByTagName("MaxAge");
                                                            if ($MaxAge->length > 0) {
                                                                $Occup['MaxAge'] = $MaxAge->item(0)->nodeValue;
                                                            }
                                                            $AgeQualifyingCode = $Occupancy->item($iu)->getElementsByTagName("AgeQualifyingCode");
                                                            if ($AgeQualifyingCode->length > 0) {
                                                                $Occup['AgeQualifyingCode'] = $AgeQualifyingCode->item(0)->nodeValue;
                                                            }
                                                            $MaxOccupancyAux = $Occupancy->item($iu)->getElementsByTagName("MaxOccupancy");
                                                            if ($MaxOccupancyAux->length > 0) {
                                                                $Occup['MaxOccupancy'] = $MaxOccupancyAux->item(0)->nodeValue;
                                                            }
                                                            $MinAge = $Occupancy->item($iu)->getElementsByTagName("MinAge");
                                                            if ($MinAge->length > 0) {
                                                                $Occup['MinAge'] = $MinAge->item(0)->nodeValue;
                                                            }
                                                            $MinOccupancy = $Occupancy->item($iu)->getElementsByTagName("MinOccupancy");
                                                            if ($MinOccupancy->length > 0) {
                                                                $Occup['MinOccupancy'] = $MinOccupancy->item(0)->nodeValue;
                                                            }
                                                            array_push($Occupancies, $Occup);
                                                        }
                                                        $Rooms[$RoomID]['RoomID'] = $RoomID;
                                                        $Rooms[$RoomID]['RoomName'] = $RoomName;
                                                        $Rooms[$RoomID]['MaxOccupancy'] = $MaxOccupancy;
                                                        $Rooms[$RoomID]['NumberOfUnits'] = $NumberOfUnits;
                                                        $Rooms[$RoomID]['RoomDescription'] = $RoomDescription;
                                                        $Rooms[$RoomID]['RoomLanguage'] = $RoomLanguage;
                                                        $Rooms[$RoomID]['Occupancies'] = $Occupancies;
                                                    }
                                                }
                                                $RoomRate = $node->item($x)->getElementsByTagName("RoomRate");
                                                for ($z = 0; $z < $RoomRate->length; $z ++) {
                                                    $RatePlanID = $RoomRate->item($z)->getElementsByTagName("RatePlanID");
                                                    if ($RatePlanID->length > 0) {
                                                        $RatePlanID = $RatePlanID->item(0)->nodeValue;
                                                    } else {
                                                        $RatePlanID = "";
                                                    }
                                                    $RoomID = $RoomRate->item($z)->getElementsByTagName("RoomID");
                                                    if ($RoomID->length > 0) {
                                                        $RoomID = $RoomID->item(0)->nodeValue;
                                                    } else {
                                                        $RoomID = "";
                                                    }
                                                    $RoomStayCandidateRPH = $RoomRate->item($z)->getElementsByTagName("RoomStayCandidateRPH");
                                                    if ($RoomStayCandidateRPH->length > 0) {
                                                        $RoomStayCandidateRPH = $RoomStayCandidateRPH->item(0)->nodeValue;
                                                    } else {
                                                        $RoomStayCandidateRPH = "";
                                                    }
                                                    $AdvanceBookingRestriction = $RoomRate->item($z)->getElementsByTagName("AdvanceBookingRestriction");
                                                    if ($AdvanceBookingRestriction->length > 0) {
                                                        $AdvanceBookingRestriction = $AdvanceBookingRestriction->item(0)->nodeValue;
                                                    } else {
                                                        $AdvanceBookingRestriction = "";
                                                    }
                                                    $Discount = $RoomRate->item($z)->getElementsByTagName("Discount");
                                                    if ($Discount->length > 0) {
                                                        $Discount = $Discount->item(0)->nodeValue;
                                                    } else {
                                                        $Discount = "";
                                                    }
                                                    $EffectiveDateAux = $RoomRate->item($z)->getElementsByTagName("EffectiveDate");
                                                    if ($EffectiveDateAux->length > 0) {
                                                        $EffectiveDate = $EffectiveDateAux->item(0)->nodeValue;
                                                    } else {
                                                        $EffectiveDate = "";
                                                    }
                                                    $ExpireDate = $RoomRate->item($z)->getElementsByTagName("ExpireDate");
                                                    if ($ExpireDate->length > 0) {
                                                        $ExpireDate = $ExpireDate->item(0)->nodeValue;
                                                    } else {
                                                        $ExpireDate = "";
                                                    }
                                                    $PromotionCode = $RoomRate->item($z)->getElementsByTagName("PromotionCode");
                                                    if ($PromotionCode->length > 0) {
                                                        $PromotionCode = $PromotionCode->item(0)->nodeValue;
                                                    } else {
                                                        $PromotionCode = "";
                                                    }
                                                    $GroupCode = $RoomRate->item($z)->getElementsByTagName("GroupCode");
                                                    if ($GroupCode->length > 0) {
                                                        $GroupCode = $GroupCode->item(0)->nodeValue;
                                                    } else {
                                                        $GroupCode = "";
                                                    }
                                                    $Rates = array();
                                                    $RatesType = $RoomRate->item($z)->getElementsByTagName("Rate");
                                                    for ($w = 0; $w < $RatesType->length; $w ++) {
                                                        $Rate = array();
                                                        $AgeQualifyingCode = $RatesType->item($w)->getElementsByTagName("AgeQualifyingCode");
                                                        if ($AgeQualifyingCode->length > 0) {
                                                            $Rate['AgeQualifyingCode'] = $AgeQualifyingCode->item(0)->nodeValue;
                                                        }
                                                        $Duration = $RatesType->item($w)->getElementsByTagName("Duration");
                                                        if ($Duration->length > 0) {
                                                            $Rate['Duration'] = $Duration->item(0)->nodeValue;
                                                        }
                                                        $EffectiveDateAux = $RatesType->item($w)->getElementsByTagName("EffectiveDate");
                                                        if ($EffectiveDateAux->length > 0) {
                                                            $Rate['EffectiveDate'] = $EffectiveDateAux->item(0)->nodeValue;
                                                        }
                                                        $MaxAge = $RatesType->item($w)->getElementsByTagName("MaxAge");
                                                        if ($MaxAge->length > 0) {
                                                            $Rate['MaxAge'] = $MaxAge->item(0)->nodeValue;
                                                        }
                                                        $MaxGuestApplicable = $RatesType->item($w)->getElementsByTagName("MaxGuestApplicable");
                                                        if ($MaxGuestApplicable->length > 0) {
                                                            $Rate['MaxGuestApplicable'] = $MaxGuestApplicable->item(0)->nodeValue;
                                                        }
                                                        $MaxLOS = $RatesType->item($w)->getElementsByTagName("MaxLOS ");
                                                        if ($MaxLOS->length > 0) {
                                                            $Rate['MaxLOS '] = $MaxLOS->item(0)->nodeValue;
                                                        }
                                                        $MinAdvancedBookingOffset = $RatesType->item($w)->getElementsByTagName("MinAdvancedBookingOffset");
                                                        if ($MinAdvancedBookingOffset->length > 0) {
                                                            $Rate['MinAdvancedBookingOffset'] = $MinAdvancedBookingOffset->item(0)->nodeValue;
                                                        }
                                                        $MinAge = $RatesType->item($w)->getElementsByTagName("MinAge");
                                                        if ($MinAge->length > 0) {
                                                            $Rate['MinAge'] = $MinAge->item(0)->nodeValue;
                                                        }
                                                        $MinGuestApplicable = $RatesType->item($w)->getElementsByTagName("MinGuestApplicable");
                                                        if ($MinGuestApplicable->length > 0) {
                                                            $Rate['MinGuestApplicable'] = $MinGuestApplicable->item(0)->nodeValue;
                                                        }
                                                        $MinLOS = $RatesType->item($w)->getElementsByTagName("MinLOS");
                                                        if ($MinLOS->length > 0) {
                                                            $Rate['MinLOS'] = $MinLOS->item(0)->nodeValue;
                                                        }
                                                        $MaxLOS = $RatesType->item($w)->getElementsByTagName("MaxLOS");
                                                        if ($MaxLOS->length > 0) {
                                                            $Rate['MaxLOS'] = $MaxLOS->item(0)->nodeValue;
                                                        }
                                                        $NumberOfUnits = $RatesType->item($w)->getElementsByTagName("NumberOfUnits");
                                                        if ($NumberOfUnits->length > 0) {
                                                            $Rate['NumberOfUnits'] = $NumberOfUnits->item(0)->nodeValue;
                                                        }
                                                        $Status = $RatesType->item($w)->getElementsByTagName("Status");
                                                        if ($Status->length > 0) {
                                                            $Rate['Status'] = $Status->item(0)->nodeValue;
                                                        }
                                                        $StayThrough = $RatesType->item($w)->getElementsByTagName("StayThrough");
                                                        if ($StayThrough->length > 0) {
                                                            $Rate['StayThrough'] = $StayThrough->item(0)->nodeValue;
                                                        }
                                                        $Total = $RatesType->item($w)->getElementsByTagName("Total");
                                                        if ($Total->length > 0) {
                                                            $Totals = array();
                                                            $AmountAfterTax = $Total->item(0)->getElementsByTagName("AmountAfterTax");
                                                            if ($AmountAfterTax->length > 0) {
                                                                $Totals['AmountAfterTax'] = $AmountAfterTax->item(0)->nodeValue;
                                                            }
                                                            $AmountBeforeTax = $Total->item(0)->getElementsByTagName("AmountBeforeTax");
                                                            if ($AmountBeforeTax->length > 0) {
                                                                $Totals['AmountBeforeTax'] = $AmountBeforeTax->item(0)->nodeValue;
                                                            }
                                                            $AmountIncludingMarkup = $Total->item(0)->getElementsByTagName("AmountIncludingMarkup");
                                                            if ($AmountIncludingMarkup->length > 0) {
                                                                $Totals['AmountIncludingMarkup'] = $AmountIncludingMarkup->item(0)->nodeValue;
                                                            }
                                                            $AmountIsPackage = $Total->item(0)->getElementsByTagName("AmountIsPackage");
                                                            if ($AmountIsPackage->length > 0) {
                                                                $Totals['AmountIsPackage'] = $AmountIsPackage->item(0)->nodeValue;
                                                            }
                                                            $ChargeType = $Total->item(0)->getElementsByTagName("ChargeType");
                                                            if ($ChargeType->length > 0) {
                                                                $Totals['ChargeType'] = $ChargeType->item(0)->nodeValue;
                                                            }
                                                            $CurrencyCode = $Total->item(0)->getElementsByTagName("CurrencyCode");
                                                            if ($CurrencyCode->length > 0) {
                                                                $Totals['CurrencyCode'] = $CurrencyCode->item(0)->nodeValue;
                                                                $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                                                            } else {
                                                                $CurrencyCode = "";
                                                            }
                                                            $TPA_Extensions = array();
                                                            $TPA = $Total->item(0)->getElementsByTagName("TPA_Extensions");
                                                            if ($TPA->length > 0) {
                                                                $ApprovalInvoiced = $TPA->item(0)->getElementsByTagName("ApprovalInvoiced");
                                                                if ($ApprovalInvoiced->length > 0) {
                                                                    $TPA_Extensions['ApprovalInvoiced'] = $ApprovalInvoiced->item(0)->nodeValue;
                                                                }
                                                                $GuestsTotalRate = $TPA->item(0)->getElementsByTagName("GuestsTotalRate");
                                                                if ($GuestsTotalRate->length > 0) {
                                                                    $TPA_Extensions['GuestsTotalRate'] = $GuestsTotalRate->item(0)->nodeValue;
                                                                }
                                                                $IsPreferredHotel = $TPA->item(0)->getElementsByTagName("IsPreferredHotel");
                                                                if ($IsPreferredHotel->length > 0) {
                                                                    $TPA_Extensions['IsPreferredHotel'] = $IsPreferredHotel->item(0)->nodeValue;
                                                                }
                                                                $RatesAux = $TPA->item(0)->getElementsByTagName("Rates");
                                                                if ($RatesAux->length > 0) {
                                                                    $TPA_Extensions['Rates'] = $RatesAux->item(0)->nodeValue;
                                                                }
                                                                $Services = $TPA->item(0)->getElementsByTagName("Services");
                                                                if ($Services->length > 0) {
                                                                    $TPA_Extensions['Services'] = $Services->item(0)->nodeValue;
                                                                }
                                                                $TotalDiscountValue = $TPA->item(0)->getElementsByTagName("TotalDiscountValue");
                                                                if ($TotalDiscountValue->length > 0) {
                                                                    $TPA_Extensions['TotalDiscountValue'] = $TotalDiscountValue->item(0)->nodeValue;
                                                                }
                                                            }
                                                            $Totals['TPA_Extensions'] = $TPA_Extensions;
                                                            $Rate['Total'] = $Totals;
                                                        }
                                                        array_push($Rates, $Rate);
                                                    }
                                                    $Total = array();
                                                    $Totals = $RoomRate->item($z)->getElementsByTagName("Total");
                                                    if ($Totals->length > 0) {
                                                        $AmountAfterTax = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountAfterTax");
                                                        if ($AmountAfterTax->length > 0) {
                                                            $Total['AmountAfterTax'] = $AmountAfterTax->item(0)->nodeValue;
                                                            $AmountAfterTax = $Total['AmountAfterTax'];
                                                        }
                                                        $AmountBeforeTax = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountBeforeTax");
                                                        if ($AmountBeforeTax->length > 0) {
                                                            $Total['AmountBeforeTax'] = $AmountBeforeTax->item(0)->nodeValue;
                                                        }
                                                        $AmountIncludingMarkup = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountIncludingMarkup");
                                                        if ($AmountIncludingMarkup->length > 0) {
                                                            $Total['AmountIncludingMarkup'] = $AmountIncludingMarkup->item(0)->nodeValue;
                                                        }
                                                        $AmountIsPackage = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountIsPackage");
                                                        if ($AmountIsPackage->length > 0) {
                                                            $Total['AmountIsPackage'] = $AmountIsPackage->item(0)->nodeValue;
                                                        }
                                                        $ChargeType = $Totals->item($Totals->length - 1)->getElementsByTagName("ChargeType");
                                                        if ($ChargeType->length > 0) {
                                                            $Total['ChargeType'] = $ChargeType->item(0)->nodeValue;
                                                        }
                                                        $CurrencyCode = $Totals->item($Totals->length - 1)->getElementsByTagName("CurrencyCode");
                                                        if ($CurrencyCode->length > 0) {
                                                            $Total['CurrencyCode'] = $CurrencyCode->item(0)->nodeValue;
                                                            $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                                                        } else {
                                                            $CurrencyCode = "";
                                                        }
                                                        $TPA_Extensions = array();
                                                        $TPA = $Totals->item($Totals->length - 1)->getElementsByTagName("TPA_Extensions");
                                                        if ($TPA->length > 0) {
                                                            $ApprovalInvoiced = $TPA->item(0)->getElementsByTagName("ApprovalInvoiced");
                                                            if ($ApprovalInvoiced->length > 0) {
                                                                $TPA_Extensions['ApprovalInvoiced'] = $ApprovalInvoiced->item(0)->nodeValue;
                                                            }
                                                            $GuestsTotalRate = $TPA->item(0)->getElementsByTagName("GuestsTotalRate");
                                                            if ($GuestsTotalRate->length > 0) {
                                                                $TPA_Extensions['GuestsTotalRate'] = $GuestsTotalRate->item(0)->nodeValue;
                                                            }
                                                            $IsPreferredHotel = $TPA->item(0)->getElementsByTagName("IsPreferredHotel");
                                                            if ($IsPreferredHotel->length > 0) {
                                                                $TPA_Extensions['IsPreferredHotel'] = $IsPreferredHotel->item(0)->nodeValue;
                                                            }
                                                            $RatesAux = $TPA->item(0)->getElementsByTagName("Rates");
                                                            if ($RatesAux->length > 0) {
                                                                $TPA_Extensions['Rates'] = $RatesAux->item(0)->nodeValue;
                                                            }
                                                            $Services = $TPA->item(0)->getElementsByTagName("Services");
                                                            if ($Services->length > 0) {
                                                                $TPA_Extensions['Services'] = $Services->item(0)->nodeValue;
                                                            }
                                                            $TotalDiscountValue = $TPA->item(0)->getElementsByTagName("TotalDiscountValue");
                                                            if ($TotalDiscountValue->length > 0) {
                                                                $TPA_Extensions['TotalDiscountValue'] = $TotalDiscountValue->item(0)->nodeValue;
                                                            }
                                                        }
                                                        $Total['TPA_Extensions'] = $TPA_Extensions;
                                                    }
                                                    // TODO
                                                    // ServiceRPHs
                                                    // TPA_Extensions
                                                    $Rate = array();
                                                    $Rate['RatePlanID'] = $RatePlanID;
                                                    $Rate['RoomID'] = $RoomID;
                                                    $Rate['Discount'] = $Discount;
                                                    $Rate['EffectiveDate'] = $EffectiveDate;
                                                    $Rate['ExpireDate'] = $ExpireDate;
                                                    $Rate['PromotionCode'] = $PromotionCode;
                                                    $Rate['GroupCode'] = $GroupCode;
                                                    $Rate['RoomStayCandidateRPH'] = $RoomStayCandidateRPH;
                                                    $Rate['AdvanceBookingRestriction'] = $AdvanceBookingRestriction;
                                                    $Rate['Rates'] = $Rates;
                                                    $Rate['Total'] = $Total;
                                                    array_push($RoomRates, $Rate);
                                                    // error_log("\r\nAmount After Tax $AmountAfterTax", 3, "/srv/www/htdocs/error_log");
                                                    $AmountAfterTaxNet = $AmountAfterTax;
                                                    //
                                                    // Markup
                                                    //
                                                    if ($omnibeesMarkup != 0) {
                                                        $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $omnibeesMarkup) / 100);
                                                    }
                                                    // Geo target markup
                                                    if ($internalmarkup != 0) {
                                                        $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $internalmarkup) / 100);
                                                    }
                                                    // Agent markup
                                                    if ($agent_markup != 0) {
                                                        $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $agent_markup) / 100);
                                                    }
                                                    // Fallback Markup
                                                    if ($omnibeesMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                                        $AmountAfterTax = $AmountAfterTax + (($AmountAfterTax * $HotelsMarkupFallback) / 100);
                                                    }
                                                    // Agent discount
                                                    if ($agent_discount != 0) {
                                                        $AmountAfterTax = $AmountAfterTax - (($AmountAfterTax * $agent_discount) / 100);
                                                    }
                                                    if ($CurrencyCode != "") {
                                                        if ($scurrency != "" and $CurrencyCode != $scurrency) {
                                                            $AmountAfterTax = $CurrencyConverter->convert($AmountAfterTax, $CurrencyCode, $scurrency);
                                                        }
                                                    }
                                                    if (is_array($tmp[$shid])) {
                                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                                    } else {
                                                        $baseCounterDetails = 0;
                                                    }
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $HotelCode;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['roomid'] = $RoomID;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateplanid'] = $RatePlanID;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['chaincode'] = $ChainCode;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateplanname'] = $RatePlans[$RatePlanID]['RatePlanName'];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['rateplantypecode'] = $RatePlanTypeCode;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['code'] = $HotelCode;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scode'] = $shid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancellationType'] = $Type;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-64";
                                                    //
                                                    // . " - " . $RoomID . " - " . $RatePlanID
                                                    //
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room'] = $Rooms[$RoomID]['RoomName'] . " - " . $RatePlans[$RatePlanID]['RatePlanName'];
                                                    //
                                                    // $Rooms[$RoomID]['MaxOccupancy'] = $MaxOccupancy;
                                                    // $Rooms[$RoomID]['NumberOfUnits'] = $NumberOfUnits;
                                                    // $Rooms[$RoomID]['RoomLanguage'] = $RoomLanguage;
                                                    // $Rooms[$RoomID]['Occupancies'] = $Occupancies;
                                                    //
                                                    // $RatePlans[$RatePlanID]['RatePlanTypeCode'] = $RatePlanTypeCode;
                                                    // $RatePlans[$RatePlanID]['SortOrder'] = $SortOrder;
                                                    // $RatePlans[$RatePlanID]['RatePlanInclusions'] = $RatePlanInclusions;
                                                    // $RatePlans[$RatePlanID]['RatePlanDescriptionDescription'] = $RatePlanDescriptionDescription;
                                                    // $RatePlans[$RatePlanID]['RatePlanDescriptionLanguage'] = $RatePlanDescriptionLanguage;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['Description'] = $Description;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['Dinner'] = $Dinner;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['Breakfast'] = $Breakfast;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['ID'] = $ID;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['Lunch'] = $Lunch;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['MealPlanCode'] = $MealPlanCode;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['MealPlanIndicator'] = $MealPlanIndicator;
                                                    // $RatePlans[$RatePlanID]['MealsIncluded']['Name'] = $Name;
                                                    //
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['room_description'] = $Rooms[$RoomID]['RoomDescription'];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $AmountAfterTax;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $AmountAfterTaxNet;
                                                    $meal = $RatePlans[$RatePlanID]['MealsIncluded']['Name'];
                                                    if ($meal == "") {
                                                        $meal = "Room Only";
                                                    }
                                                    try {
                                                        $sql = "select mapped from board_mapping where description='" . addslashes($meal) . "'";
                                                        $statement = $db->createStatement($sql);
                                                        $statement->prepare();
                                                        $row_board_mapping = $statement->execute();
                                                        $row_board_mapping->buffer();
                                                        if ($row_board_mapping->valid()) {
                                                            $row_board_mapping = $row_board_mapping->current();
                                                            $meal = $row_board_mapping["mapped"];
                                                        }
                                                    } catch (\Exception $e) {
                                                        $logger = new Logger();
                                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                                        $logger->addWriter($writer);
                                                        $logger->info($e->getMessage());
                                                    }
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($meal);
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['mealinfo'] = $RatePlans[$RatePlanID]['MealsIncluded']['Description'];
                                                    $pricebreakdown = array();
                                                    $pricebreakdownCount = 0;
                                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                                        $amount = $AmountAfterTax / $noOfNights;
                                                        $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                                        $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                                        $pricebreakdownCount = $pricebreakdownCount + 1;
                                                    }
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                                                    if ($RatePlans[$RatePlanID]['Offers'][0]['OfferDescription'] != "") {
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = true;
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = $RatePlans[$RatePlanID]['Offers'][0]['OfferDescription'];
                                                        if ($RatePlans[$RatePlanID]['Offers'][0]['Percent'] != "") {
                                                            $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] .= " - " . $RatePlans[$RatePlanID]['Offers'][0]['Percent'] . "% " . $translator->translate("discount");
                                                        }
                                                    } else {
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                                    }
                                                    // $Penalty['Start'] = $Start;
                                                    // $Penalty['End'] = $End;
                                                    // $Penalty['Duration'] = $Duration;
                                                    // $Penalty['PenaltyLanguage'] = $PenaltyLanguage;
                                                    // $Penalty['PenaltyName'] = $PenaltyName;
                                                    // $Penalty['IsPerRoom'] = $IsPerRoom;
                                                    // $Penalty['IsPerStay'] = $IsPerStay;
                                                    // $Penalty['AbsoluteDeadline'] = $AbsoluteDeadline;
                                                    // $Penalty['OffsetDropTime'] = $OffsetDropTime;
                                                    // $Penalty['OffsetUnitMultiplier'] = $OffsetUnitMultiplier;
                                                    // $Penalty['TimeUnitType'] = $TimeUnitType;
                                                    // $Penalty['Amount'] = $Amount;
                                                    // $Penalty['CurrencyCode'] = $CurrencyCode;
                                                    // $Penalty['NmbrOfNights'] = $NmbrOfNights;
                                                    // $Penalty['Percent'] = $Percent;
                                                    if ($RatePlans[$RatePlanID]['CancelPenalties'][0]['NonRefundable'] == true) {
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = true;
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = time();
                                                    } else {
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nonrefundable'] = false;
                                                        $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy_deadline'] = $RatePlans[$RatePlanID]['CancelPenalties'][0]['cancelpolicy_deadline'];
                                                    }
                                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['cancelpolicy'] = $RatePlans[$RatePlanID]['CancelPenalties'][0]['PenaltyDescription'];
                                                }
                                                $omnibees = true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    if ($omnibees == true) {
        $sfilter = implode(' or ', $sfilter);
        if ($sfilter != "") {
            try {
                $sql = "select hid, sid from xmlhotels_momnibees where " . $sfilter;
                $statement2 = $db->createStatement($sql);
                $statement2->prepare();
                $result2 = $statement2->execute();
                $result2->buffer();
                // error_log("\r\nOmnibees $sql\r\n", 3, "/srv/www/htdocs/error_log");
                if ($result2 instanceof ResultInterface && $result2->isQueryResult()) {
                    $resultSet2 = new ResultSet();
                    $resultSet2->initialize($result2);
                    foreach ($resultSet2 as $row2) {
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
            } catch (\Exception $e) {
                $logger = new Logger();
                $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
            }
            if (is_array($sidfilter)) {
                $supplier = 64;
                $sidfilter = implode(',', $sidfilter);
                $query = 'call xmlhotels("' . $sidfilter . '")';
                // Store Session
                try {
                    $sql = new Sql($db);
                    $delete = $sql->delete();
                    $delete->from('quote_session_omnibees');
                    $delete->where(array(
                        'session_id' => $session_id
                    ));
                    $statement = $sql->prepareStatementForSqlObject($delete);
                    $results = $statement->execute();
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('quote_session_omnibees');
                    $insert->values(array(
                        'session_id' => $session_id,
                        'xmlrequest' => (string) $xmlrequest,
                        'xmlresult' => '',
                        'data' => base64_encode(serialize($hotels_array)),
                        'searchsettings' => base64_encode(serialize($requestdata))
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                } catch (\Exception $e) {
                    $logger = new Logger();
                    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                }
            }
        }
    }
}
// error_log("\r\nEOF - Omnibees - Multi Search\r\n", 3, "/srv/www/htdocs/error_log");
?>