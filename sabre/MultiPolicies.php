<?php
error_log("\r\nMulti Policies SABRE \r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\I18n\Translator\Translator;
use Laminas\Http\Client;
use Laminas\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$salestaxes = 0;
$salestaxesfees = 0;
$baserate = 0;
$db = new \Laminas\Db\Adapter\Adapter($config);
if ($details == "hoteldetails") {
    // Detail level
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_sabre where session_id='" . $session_id . "-" . $index . "'";
} else {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_sabre where session_id='$session_id'";
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
$sql = "select value from settings where name='enablesabretravelnetworktravelportal' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_sabre = $affiliate_id;
} else {
    $affiliate_id_sabre = 0;
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
    $sql = "select value from settings where name='sabreDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='sabretravelnetworktravelportaluserID1' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworktravelportaluserID1 = $row_settings['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportaluserID2' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworktravelportaluserID2 = $row_settings['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalpassword1' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworktravelportalpassword1 = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='sabretravelnetworktravelportalpassword2' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworktravelportalpassword2 = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='sabretravelnetworktravelportalIPCC' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworktravelportalIPCC = $row_settings['value'];
}
$sql = "select value from settings where name='enablesabretravelnetworktravelportal' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $enablesabretravelnetworktravelportal = $row_settings['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalwebservicesURL' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $sabretravelnetworktravelportalwebservicesURL = $row['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalmarkup' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworktravelportalmarkup = (double) $row_settings['value'];
} else {
    $sabretravelnetworktravelportalmarkup = 0;
}
$sql = "select value from settings where name='sabretravelnetworktravelportalb2cmarkup' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $sabretravelnetworktravelportalb2cmarkup = $row['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalPartyIDFrom' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $sabretravelnetworktravelportalPartyIDFrom = $row['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalPartyIDTo' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $sabretravelnetworktravelportalPartyIDTo = $row['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalConversationID' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $sabretravelnetworktravelportalConversationID = $row['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalParallelSearch' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $sabretravelnetworktravelportalParallelSearch = $row['value'];
}
$sql = "select value from settings where name='sabretravelnetworktravelportalTimeout' and affiliate_id=$affiliate_id_sabre" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $sabretravelnetworktravelportalTimeout = (int) $row['value'];
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
        
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();

        date_default_timezone_set("UTC");
        $datetime = date('Y-m-d\TH:i:s');
        
        $BinarySecurityToken = $value['BinarySecurityToken'];
        $RateAccessCode = $value['RateAccessCode'];
        $adults = $value['adults'];
        $children = $value['children'];

        $count = $adults + $children;

        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
        <soapenv:Header>
           <eb:MessageHeader soapenv:mustUnderstand="0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader">
               <eb:From>
                 <eb:PartyId>' . $sabretravelnetworktravelportalPartyIDFrom . '</eb:PartyId>
              </eb:From>
              <eb:To>
                 <eb:PartyId>' . $sabretravelnetworktravelportalPartyIDTo . '</eb:PartyId>
              </eb:To>
              <eb:CPAId>' . $sabretravelnetworktravelportalIPCC . '</eb:CPAId>
              <eb:ConversationId>' . $sabretravelnetworktravelportalConversationID . '</eb:ConversationId>
              <eb:Service>HotelPropertyDescriptionLLSRQ</eb:Service>
              <eb:Action>HotelPropertyDescriptionLLSRQ</eb:Action>
              <eb:MessageData>
                 <eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId>
                 <eb:Timestamp>' . $datetime . '</eb:Timestamp>
              </eb:MessageData>
           </eb:MessageHeader>
           <eb:Security soapenv:mustUnderstand="0" xmlns:eb="http://schemas.xmlsoap.org/ws/2002/12/secext">
              <eb:BinarySecurityToken>' . $BinarySecurityToken . '</eb:BinarySecurityToken>
           </eb:Security>
        </soapenv:Header>
        <soapenv:Body>
            <HotelPropertyDescriptionRQ Version="2.3.0" xmlns="http://webservices.sabre.com/sabreXML/2011/10" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <AvailRequestSegment>
                    <GuestCounts Count="' . $count . '"/>
                    <HotelSearchCriteria>
                        <Criterion>
                            <HotelRef HotelCode="' . $shid . '"/>
                        </Criterion>
                    </HotelSearchCriteria>
                    <TimeSpan End="' . $to_date . '" Start="' . $from_date . '"/>
                </AvailRequestSegment>
            </HotelPropertyDescriptionRQ>
        </soapenv:Body>
     </soapenv:Envelope>';
     ///////////////////////error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");

        $headers = array(
            "Content-Type: text/xml;charset=utf-8",
            "Accept-Encoding: gzip",
            "Content-length: " . strlen($raw)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sabretravelnetworktravelportalwebservicesURL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_TIMEOUT, $sabretravelnetworktravelportalTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        curl_close($ch);
        error_log("\r\n Response2: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        
        /* try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_sabre');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $sabretravelnetworktravelportalwebservicesURL,
                'sqlcontext' => $response2,
                'errcontext' => ''
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        } */

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Header = $Envelope->item(0)->getElementsByTagName("Header");
        $MessageHeader = $Header->item(0)->getElementsByTagName("MessageHeader");
        if ($MessageHeader->length > 0) {
            $From = $MessageHeader->item(0)->getElementsByTagName("From");
            if ($From->length > 0) {
                $FromPartyId = $From->item(0)->getElementsByTagName("PartyId");
                if ($FromPartyId->length > 0) {
                    $type = $FromPartyId->item(0)->getAttribute("type");
                    $FromPartyId = $FromPartyId->item(0)->nodeValue;
                } else {
                    $FromPartyId = "";
                }
            }
            $To = $MessageHeader->item(0)->getElementsByTagName("To");
            if ($To->length > 0) {
                $ToPartyId = $To->item(0)->getElementsByTagName("PartyId");
                if ($ToPartyId->length > 0) {
                    $ToPartyId = $ToPartyId->item(0)->nodeValue;
                } else {
                    $ToPartyId = "";
                }
            }
            $CPAId = $MessageHeader->item(0)->getElementsByTagName("CPAId");
            if ($CPAId->length > 0) {
                $CPAId = $CPAId->item(0)->nodeValue;
            } else {
                $CPAId = "";
            }
            $ConversationId = $MessageHeader->item(0)->getElementsByTagName("ConversationId");
            if ($ConversationId->length > 0) {
                $ConversationId = $ConversationId->item(0)->nodeValue;
            } else {
                $ConversationId = "";
            }
            $MessageData = $MessageHeader->item(0)->getElementsByTagName("MessageData");
            if ($MessageData->length > 0) {
                $MessageId = $MessageData->item(0)->getElementsByTagName("MessageId");
                if ($MessageId->length > 0) {
                    $MessageId = $MessageId->item(0)->nodeValue;
                } else {
                    $MessageId = "";
                }
                $RefToMessageId = $MessageData->item(0)->getElementsByTagName("RefToMessageId");
                if ($RefToMessageId->length > 0) {
                    $RefToMessageId = $RefToMessageId->item(0)->nodeValue;
                } else {
                    $RefToMessageId = "";
                }
            }
        }
        $Security = $Header->item(0)->getElementsByTagName("Security");
        if ($Security->length > 0) {
            $BinarySecurityToken = $Security->item(0)->getElementsByTagName("BinarySecurityToken");
            if ($BinarySecurityToken->length > 0) {
                $BinarySecurityToken = $BinarySecurityToken->item(0)->nodeValue;
            } else {
                $BinarySecurityToken = "";
            }
        }

        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $HotelPropertyDescriptionRS = $Body->item(0)->getElementsByTagName("HotelPropertyDescriptionRS");
        if ($HotelPropertyDescriptionRS->length > 0) {
            $RoomStay = $HotelPropertyDescriptionRS->item(0)->getElementsByTagName("RoomStay");
            if ($RoomStay->length > 0) {
                $BasicPropertyInfo = $RoomStay->item(0)->getElementsByTagName("BasicPropertyInfo");
                if ($BasicPropertyInfo->length > 0) {
                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                    $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                    $HotelCityCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCityCode");
                    $Latitude = $BasicPropertyInfo->item(0)->getAttribute("Latitude");
                    $Longitude = $BasicPropertyInfo->item(0)->getAttribute("Longitude");
                    $GeoConfidenceLevel = $BasicPropertyInfo->item(0)->getAttribute("GeoConfidenceLevel");
                    $RPH = $BasicPropertyInfo->item(0)->getAttribute("RPH");
                    $ChainCode = $BasicPropertyInfo->item(0)->getAttribute("ChainCode");
                    $NumFloors = $BasicPropertyInfo->item(0)->getAttribute("NumFloors");

                    $CheckInTime = $BasicPropertyInfo->item(0)->getElementsByTagName("CheckInTime");
                    if ($CheckInTime->length > 0) {
                        $CheckInTime = $CheckInTime->item(0)->nodeValue;
                    } else {
                        $CheckInTime = "";
                    }
                    $CheckOutTime = $BasicPropertyInfo->item(0)->getElementsByTagName("CheckOutTime");
                    if ($CheckOutTime->length > 0) {
                        $CheckOutTime = $CheckOutTime->item(0)->nodeValue;
                    } else {
                        $CheckOutTime = "";
                    }
                    //Awards
                    $Awards = $BasicPropertyInfo->item(0)->getElementsByTagName("Awards");
                    if ($Awards->length > 0) {
                        $AwardProvider = $Awards->item(0)->getElementsByTagName("AwardProvider");
                        if ($AwardProvider->length > 0) {
                            $AwardProvider = $AwardProvider->item(0)->nodeValue;
                        } else {
                            $AwardProvider = "";
                        }
                    }
                    //Address
                    $Address = $BasicPropertyInfo->item(0)->getElementsByTagName("Address");
                    if ($Address->length > 0) {
                        $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                        if ($AddressLine->length > 0) {
                            for ($iAux=0; $iAux < $AddressLine->length; $iAux++) { 
                                $AddressLine = $AddressLine->item($iAux)->nodeValue;
                            }
                        }
                    }
                    //ContactNumbers
                    $ContactNumbers = $BasicPropertyInfo->item(0)->getElementsByTagName("ContactNumbers");
                    if ($ContactNumbers->length > 0) {
                        $ContactNumber = $ContactNumbers->item(0)->getElementsByTagName("ContactNumber");
                        if ($ContactNumber->length > 0) {
                            $Phone = $ContactNumber->item(0)->getAttribute("Phone");
                            $Fax = $ContactNumber->item(0)->getAttribute("Fax");
                        }
                    }
                    //DirectConnect
                    $DirectConnect = $BasicPropertyInfo->item(0)->getElementsByTagName("DirectConnect");
                    if ($DirectConnect->length > 0) {
                        $Alt_Avail = $DirectConnect->item(0)->getElementsByTagName("Alt_Avail");
                        if ($Alt_Avail->length > 0) {
                            $Alt_Avail_Ind = $Alt_Avail->item(0)->getAttribute("Ind");
                        }
                        $DC_AvailParticipant = $DirectConnect->item(0)->getElementsByTagName("DC_AvailParticipant");
                        if ($DC_AvailParticipant->length > 0) {
                            $DC_AvailParticipant_Ind = $DC_AvailParticipant->item(0)->getAttribute("Ind");
                        }
                        $DC_SellParticipant = $DirectConnect->item(0)->getElementsByTagName("DC_SellParticipant");
                        if ($DC_SellParticipant->length > 0) {
                            $DC_SellParticipant_Ind = $DC_SellParticipant->item(0)->getAttribute("Ind");
                        }
                        $RatesExceedMax = $DirectConnect->item(0)->getElementsByTagName("RatesExceedMax");
                        if ($RatesExceedMax->length > 0) {
                            $RatesExceedMax_Ind = $RatesExceedMax->item(0)->getAttribute("Ind");
                        }
                        $UnAvail = $DirectConnect->item(0)->getElementsByTagName("UnAvail");
                        if ($UnAvail->length > 0) {
                            $UnAvail_Ind = $UnAvail->item(0)->getAttribute("Ind");
                        }
                    }
                    //LocationDescription
                    $IndexData = $BasicPropertyInfo->item(0)->getElementsByTagName("IndexData");
                    if ($IndexData->length > 0) {
                        $Index = $IndexData->item(0)->getElementsByTagName("Index");
                        if ($Index->length > 0) {
                            for ($i=0; $i < $Index->length; $i++) { 
                                $TransportationCode = $Index->item($i)->getAttribute("TransportationCode");
                                $Point = $Index->item($i)->getAttribute("Point");
                                $LocationCode = $Index->item($i)->getAttribute("LocationCode");
                                $DistanceDirection = $Index->item($i)->getAttribute("DistanceDirection");
                                $CountryState = $Index->item($i)->getAttribute("CountryState");
                            }
                        }
                    }
                    //PropertyOptionInfo
                    $PropertyOptionInfo = $BasicPropertyInfo->item(0)->getElementsByTagName("PropertyOptionInfo");
                    if ($PropertyOptionInfo->length > 0) {
                        $ADA_Accessible = $PropertyOptionInfo->item(0)->getElementsByTagName("ADA_Accessible");
                        if ($ADA_Accessible->length > 0) {
                            $ADA_Accessible_Ind = $ADA_Accessible->item(0)->getAttribute("Ind");
                        }
                        $AdultsOnly = $PropertyOptionInfo->item(0)->getElementsByTagName("AdultsOnly");
                        if ($AdultsOnly->length > 0) {
                            $AdultsOnly_Ind = $AdultsOnly->item(0)->getAttribute("Ind");
                        }
                        $BeachFront = $PropertyOptionInfo->item(0)->getElementsByTagName("BeachFront");
                        if ($BeachFront->length > 0) {
                            $BeachFront_Ind = $BeachFront->item(0)->getAttribute("Ind");
                        }
                        $Breakfast = $PropertyOptionInfo->item(0)->getElementsByTagName("Breakfast");
                        if ($Breakfast->length > 0) {
                            $Breakfast_Ind = $Breakfast->item(0)->getAttribute("Ind");
                        }
                        $BusinessCenter = $PropertyOptionInfo->item(0)->getElementsByTagName("BusinessCenter");
                        if ($BusinessCenter->length > 0) {
                            $BusinessCenter_Ind = $BusinessCenter->item(0)->getAttribute("Ind");
                        }
                        $BusinessReady = $PropertyOptionInfo->item(0)->getElementsByTagName("BusinessReady");
                        if ($BusinessReady->length > 0) {
                            $BusinessReady_Ind = $BusinessReady->item(0)->getAttribute("Ind");
                        }
                        $Conventions = $PropertyOptionInfo->item(0)->getElementsByTagName("Conventions");
                        if ($Conventions->length > 0) {
                            $Conventions_Ind = $Conventions->item(0)->getAttribute("Ind");
                        }
                        $Dataport = $PropertyOptionInfo->item(0)->getElementsByTagName("Dataport");
                        if ($Dataport->length > 0) {
                            $Dataport_Ind = $Dataport->item(0)->getAttribute("Ind");
                        }
                        $Dining = $PropertyOptionInfo->item(0)->getElementsByTagName("Dining");
                        if ($Dining->length > 0) {
                            $Dining_Ind = $Dining->item(0)->getAttribute("Ind");
                        }
                        $DryClean = $PropertyOptionInfo->item(0)->getElementsByTagName("DryClean");
                        if ($DryClean->length > 0) {
                            $DryClean_Ind = $DryClean->item(0)->getAttribute("Ind");
                        }
                        $EcoCertified = $PropertyOptionInfo->item(0)->getElementsByTagName("EcoCertified");
                        if ($EcoCertified->length > 0) {
                            $EcoCertified_Ind = $EcoCertified->item(0)->getAttribute("Ind");
                        }
                        $ExecutiveFloors = $PropertyOptionInfo->item(0)->getElementsByTagName("ExecutiveFloors");
                        if ($ExecutiveFloors->length > 0) {
                            $ExecutiveFloors_Ind = $ExecutiveFloors->item(0)->getAttribute("Ind");
                        }
                        $FitnessCenter = $PropertyOptionInfo->item(0)->getElementsByTagName("FitnessCenter");
                        if ($FitnessCenter->length > 0) {
                            $FitnessCenter_Ind = $FitnessCenter->item(0)->getAttribute("Ind");
                        }
                        $FreeLocalCalls = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeLocalCalls");
                        if ($FreeLocalCalls->length > 0) {
                            $FreeLocalCalls_Ind = $FreeLocalCalls->item(0)->getAttribute("Ind");
                        }
                        $FreeParking = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeParking");
                        if ($FreeParking->length > 0) {
                            $FreeParking_Ind = $FreeParking->item(0)->getAttribute("Ind");
                        }
                        $FreeShuttle = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeShuttle");
                        if ($FreeShuttle->length > 0) {
                            $FreeShuttle_Ind = $FreeShuttle->item(0)->getAttribute("Ind");
                        }
                        $FreeWifiInMeetingRooms = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeWifiInMeetingRooms");
                        if ($FreeWifiInMeetingRooms->length > 0) {
                            $FreeWifiInMeetingRooms_Ind = $FreeWifiInMeetingRooms->item(0)->getAttribute("Ind");
                        }
                        $FreeWifiInPublicSpaces = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeWifiInPublicSpaces");
                        if ($FreeWifiInPublicSpaces->length > 0) {
                            $FreeWifiInPublicSpaces_Ind = $FreeWifiInPublicSpaces->item(0)->getAttribute("Ind");
                        }
                        $FreeWifiInRooms = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeWifiInRooms");
                        if ($FreeWifiInRooms->length > 0) {
                            $FreeWifiInRooms_Ind = $FreeWifiInRooms->item(0)->getAttribute("Ind");
                        }
                        $FullServiceSpa = $PropertyOptionInfo->item(0)->getElementsByTagName("FullServiceSpa");
                        if ($FullServiceSpa->length > 0) {
                            $FullServiceSpa_Ind = $FullServiceSpa->item(0)->getAttribute("Ind");
                        }
                        $GameFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("GameFacilities");
                        if ($GameFacilities->length > 0) {
                            $GameFacilities_Ind = $GameFacilities->item(0)->getAttribute("Ind");
                        }
                        $Golf = $PropertyOptionInfo->item(0)->getElementsByTagName("Golf");
                        if ($Golf->length > 0) {
                            $Golf_Ind = $Golf->item(0)->getAttribute("Ind");
                        }
                        $HighSpeedInternet = $PropertyOptionInfo->item(0)->getElementsByTagName("HighSpeedInternet");
                        if ($HighSpeedInternet->length > 0) {
                            $HighSpeedInternet_Ind = $HighSpeedInternet->item(0)->getAttribute("Ind");
                        }
                        $HypoallergenicRooms = $PropertyOptionInfo->item(0)->getElementsByTagName("HypoallergenicRooms");
                        if ($HypoallergenicRooms->length > 0) {
                            $HypoallergenicRooms_Ind = $HypoallergenicRooms->item(0)->getAttribute("Ind");
                        }
                        $IndoorPool = $PropertyOptionInfo->item(0)->getElementsByTagName("IndoorPool");
                        if ($IndoorPool->length > 0) {
                            $IndoorPool_Ind = $IndoorPool->item(0)->getAttribute("Ind");
                        }
                        $InRoomCoffeeTea = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomCoffeeTea");
                        if ($InRoomCoffeeTea->length > 0) {
                            $InRoomCoffeeTea_Ind = $InRoomCoffeeTea->item(0)->getAttribute("Ind");
                        }
                        $InRoomMiniBar = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomMiniBar");
                        if ($InRoomMiniBar->length > 0) {
                            $InRoomMiniBar_Ind = $InRoomMiniBar->item(0)->getAttribute("Ind");
                        }
                        $InRoomRefrigerator = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomRefrigerator");
                        if ($InRoomRefrigerator->length > 0) {
                            $InRoomRefrigerator_Ind = $InRoomRefrigerator->item(0)->getAttribute("Ind");
                        }
                        $InRoomSafe = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomSafe");
                        if ($InRoomSafe->length > 0) {
                            $InRoomSafe_Ind = $InRoomSafe->item(0)->getAttribute("Ind");
                        }
                        $InteriorDoorways = $PropertyOptionInfo->item(0)->getElementsByTagName("InteriorDoorways");
                        if ($InteriorDoorways->length > 0) {
                            $InteriorDoorways_Ind = $InteriorDoorways->item(0)->getAttribute("Ind");
                        }
                        $Jacuzzi = $PropertyOptionInfo->item(0)->getElementsByTagName("Jacuzzi");
                        if ($Jacuzzi->length > 0) {
                            $Jacuzzi_Ind = $Jacuzzi->item(0)->getAttribute("Ind");
                        }
                        $KidsFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("KidsFacilities");
                        if ($KidsFacilities->length > 0) {
                            $KidsFacilities_Ind = $KidsFacilities->item(0)->getAttribute("Ind");
                        }
                        $KitchenFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("KitchenFacilities");
                        if ($KitchenFacilities->length > 0) {
                            $KitchenFacilities_Ind = $KitchenFacilities->item(0)->getAttribute("Ind");
                        }
                        $MealService = $PropertyOptionInfo->item(0)->getElementsByTagName("MealService");
                        if ($MealService->length > 0) {
                            $MealService_Ind = $MealService->item(0)->getAttribute("Ind");
                        }
                        $MeetingFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("MeetingFacilities");
                        if ($MeetingFacilities->length > 0) {
                            $MeetingFacilities_Ind = $MeetingFacilities->item(0)->getAttribute("Ind");
                        }
                        $NoAdultTV = $PropertyOptionInfo->item(0)->getElementsByTagName("NoAdultTV");
                        if ($NoAdultTV->length > 0) {
                            $NoAdultTV_Ind = $NoAdultTV->item(0)->getAttribute("Ind");
                        }
                        $NonSmoking = $PropertyOptionInfo->item(0)->getElementsByTagName("NonSmoking");
                        if ($NonSmoking->length > 0) {
                            $NonSmoking_Ind = $NonSmoking->item(0)->getAttribute("Ind");
                        }
                        $OutdoorPool = $PropertyOptionInfo->item(0)->getElementsByTagName("OutdoorPool");
                        if ($OutdoorPool->length > 0) {
                            $OutdoorPoolJacuzzi_Ind = $OutdoorPool->item(0)->getAttribute("Ind");
                        }
                        $Pets = $PropertyOptionInfo->item(0)->getElementsByTagName("Pets");
                        if ($Pets->length > 0) {
                            $Pets_Ind = $Pets->item(0)->getAttribute("Ind");
                        }
                        $Pool = $PropertyOptionInfo->item(0)->getElementsByTagName("Pool");
                        if ($Pool->length > 0) {
                            $Pool_Ind = $Pool->item(0)->getAttribute("Ind");
                        }
                        $PublicTransportationAdjacent = $PropertyOptionInfo->item(0)->getElementsByTagName("PublicTransportationAdjacent");
                        if ($PublicTransportationAdjacent->length > 0) {
                            $PublicTransportationAdjacent_Ind = $PublicTransportationAdjacent->item(0)->getAttribute("Ind");
                        }
                        $RateAssured = $PropertyOptionInfo->item(0)->getElementsByTagName("RateAssured");
                        if ($RateAssured->length > 0) {
                            $RateAssured_Ind = $RateAssured->item(0)->getAttribute("Ind");
                        }
                        $Recreation = $PropertyOptionInfo->item(0)->getElementsByTagName("Recreation");
                        if ($Recreation->length > 0) {
                            $Recreation_Ind = $Recreation->item(0)->getAttribute("Ind");
                        }
                        $RestrictedRoomAccess = $PropertyOptionInfo->item(0)->getElementsByTagName("RestrictedRoomAccess");
                        if ($RestrictedRoomAccess->length > 0) {
                            $RestrictedRoomAccess_Ind = $RestrictedRoomAccess->item(0)->getAttribute("Ind");
                        }
                        $RoomService = $PropertyOptionInfo->item(0)->getElementsByTagName("RoomService");
                        if ($RoomService->length > 0) {
                            $RoomService_Ind = $RoomService->item(0)->getAttribute("Ind");
                        }
                        $RoomService24Hours = $PropertyOptionInfo->item(0)->getElementsByTagName("RoomService24Hours");
                        if ($RoomService24Hours->length > 0) {
                            $RoomService24Hours_Ind = $RoomService24Hours->item(0)->getAttribute("Ind");
                        }
                        $RoomsWithBalcony = $PropertyOptionInfo->item(0)->getElementsByTagName("RoomsWithBalcony");
                        if ($RoomsWithBalcony->length > 0) {
                            $RoomsWithBalcony_Ind = $RoomsWithBalcony->item(0)->getAttribute("Ind");
                        }
                        $SkiInOutProperty = $PropertyOptionInfo->item(0)->getElementsByTagName("SkiInOutProperty");
                        if ($SkiInOutProperty->length > 0) {
                            $SkiInOutProperty_Ind = $SkiInOutProperty->item(0)->getAttribute("Ind");
                        }
                        $SmokeFree = $PropertyOptionInfo->item(0)->getElementsByTagName("SmokeFree");
                        if ($SmokeFree->length > 0) {
                            $SmokeFree_Ind = $SmokeFree->item(0)->getAttribute("Ind");
                        }
                        $SmokingRoomsAvail = $PropertyOptionInfo->item(0)->getElementsByTagName("SmokingRoomsAvail");
                        if ($SmokingRoomsAvail->length > 0) {
                            $SmokingRoomsAvail_Ind = $SmokingRoomsAvail->item(0)->getAttribute("Ind");
                        }
                        $Tennis = $PropertyOptionInfo->item(0)->getElementsByTagName("Tennis");
                        if ($Tennis->length > 0) {
                            $Tennis_Ind = $Tennis->item(0)->getAttribute("Ind");
                        }
                        $WaterPurificationSystem = $PropertyOptionInfo->item(0)->getElementsByTagName("WaterPurificationSystem");
                        if ($WaterPurificationSystem->length > 0) {
                            $WaterPurificationSystem_Ind = $WaterPurificationSystem->item(0)->getAttribute("Ind");
                        }
                        $Wheelchair = $PropertyOptionInfo->item(0)->getElementsByTagName("Wheelchair");
                        if ($Wheelchair->length > 0) {
                            $Wheelchair_Ind = $Wheelchair->item(0)->getAttribute("Ind");
                        }
                    }
                    //PropertyTypeInfo
                    $PropertyTypeInfo = $BasicPropertyInfo->item(0)->getElementsByTagName("PropertyTypeInfo");
                    if ($PropertyTypeInfo->length > 0) {
                        $AllInclusive = $PropertyTypeInfo->item(0)->getElementsByTagName("AllInclusive");
                        if ($AllInclusive->length > 0) {
                            $AllInclusive_Ind = $AllInclusive->item(0)->getAttribute("Ind");
                        }
                        $Apartments = $PropertyTypeInfo->item(0)->getElementsByTagName("Apartments");
                        if ($Apartments->length > 0) {
                            $Apartments_Ind = $Apartments->item(0)->getAttribute("Ind");
                        }
                        $BedBreakfast = $PropertyTypeInfo->item(0)->getElementsByTagName("BedBreakfast");
                        if ($BedBreakfast->length > 0) {
                            $BedBreakfast_Ind = $BedBreakfast->item(0)->getAttribute("Ind");
                        }
                        $Castle = $PropertyTypeInfo->item(0)->getElementsByTagName("Castle");
                        if ($Castle->length > 0) {
                            $Castle_Ind = $Castle->item(0)->getAttribute("Ind");
                        }
                        $Conventions = $PropertyTypeInfo->item(0)->getElementsByTagName("Conventions");
                        if ($Conventions->length > 0) {
                            $Conventions_Ind = $Conventions->item(0)->getAttribute("Ind");
                        }
                        $Economy = $PropertyTypeInfo->item(0)->getElementsByTagName("Economy");
                        if ($Economy->length > 0) {
                            $Economy_Ind = $Economy->item(0)->getAttribute("Ind");
                        }
                        $ExtendedStay = $PropertyTypeInfo->item(0)->getElementsByTagName("ExtendedStay");
                        if ($ExtendedStay->length > 0) {
                            $ExtendedStay_Ind = $ExtendedStay->item(0)->getAttribute("Ind");
                        }
                        $Farm = $PropertyTypeInfo->item(0)->getElementsByTagName("Farm");
                        if ($Farm->length > 0) {
                            $Farm_Ind = $Farm->item(0)->getAttribute("Ind");
                        }
                        $First = $PropertyTypeInfo->item(0)->getElementsByTagName("First");
                        if ($First->length > 0) {
                            $First_Ind = $First->item(0)->getAttribute("Ind");
                        }
                        $Luxury = $PropertyTypeInfo->item(0)->getElementsByTagName("Luxury");
                        if ($Luxury->length > 0) {
                            $Luxury_Ind = $Luxury->item(0)->getAttribute("Ind");
                        }
                        $Moderate = $PropertyTypeInfo->item(0)->getElementsByTagName("Moderate");
                        if ($Moderate->length > 0) {
                            $Moderate_Ind = $Moderate->item(0)->getAttribute("Ind");
                        }
                        $Motel = $PropertyTypeInfo->item(0)->getElementsByTagName("Motel");
                        if ($Motel->length > 0) {
                            $Motel_Ind = $Motel->item(0)->getAttribute("Ind");
                        }
                        $Resort = $PropertyTypeInfo->item(0)->getElementsByTagName("Resort");
                        if ($Resort->length > 0) {
                            $Resort_Ind = $Resort->item(0)->getAttribute("Ind");
                        }
                        $Suites = $PropertyTypeInfo->item(0)->getElementsByTagName("Suites");
                        if ($Suites->length > 0) {
                            $Suites_Ind = $Suites->item(0)->getAttribute("Ind");
                        }
                    }
                    //SpecialOffers
                    $SpecialOffers = $BasicPropertyInfo->item(0)->getElementsByTagName("SpecialOffers");
                    if ($SpecialOffers->length > 0) {
                        $SpecialOffers_Ind = $SpecialOffers->item(0)->getAttribute("Ind");
                    }
                    //Taxes
                    $Taxes = $BasicPropertyInfo->item(0)->getElementsByTagName("Taxes");
                    if ($Taxes->length > 0) {
                        $TaxesText = $Taxes->item(0)->getElementsByTagName("Text");
                        if ($TaxesText->length > 0) {
                            $TaxesText = $TaxesText->item(0)->nodeValue;
                        } else {
                            $TaxesText = "";
                        }
                    }
                    //VendorMessages
                    $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName("VendorMessages");
                    if ($VendorMessages->length > 0) {
                        $AdditionalAttractions = $VendorMessages->item(0)->getElementsByTagName("AdditionalAttractions");
                        if ($AdditionalAttractions->length > 0) {
                            $Text = $AdditionalAttractions->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Attractions = $VendorMessages->item(0)->getElementsByTagName("Attractions");
                        if ($Attractions->length > 0) {
                            $Text = $Attractions->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $cancel = "";
                        $cancel2 = "";
                        $Cancellation = $VendorMessages->item(0)->getElementsByTagName("Cancellation");
                        if ($Cancellation->length > 0) {
                            $Text = $Cancellation->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $cancel2 = $cancel2 . $Text->item($j)->nodeValue;
                                    if ($cancel2 == "SEE RATE RULES FOR CANCELLATION REQUIREMENTS") {
                                        $cancel2 = "";
                                    }
                                    $cancel = $cancel2 . " ";
                                }
                            }
                        }
                        $cancel = $cancel . ".";
                        $dep = "";
                        $Deposit = $VendorMessages->item(0)->getElementsByTagName("Deposit");
                        if ($Deposit->length > 0) {
                            $Text = $Deposit->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $dep = $dep . $Text->item($j)->nodeValue;
                                    $dep = $dep . " ";
                                }
                            }
                        }
                        $dep = $dep . ".";
                        $Description = $VendorMessages->item(0)->getElementsByTagName("Description");
                        if ($Description->length > 0) {
                            $Text = $Description->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Dining = $VendorMessages->item(0)->getElementsByTagName("Dining");
                        if ($Dining->length > 0) {
                            $Text = $Dining->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Directions = $VendorMessages->item(0)->getElementsByTagName("Directions");
                        if ($Directions->length > 0) {
                            $Text = $Directions->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Facilities = $VendorMessages->item(0)->getElementsByTagName("Facilities");
                        if ($Facilities->length > 0) {
                            $Text = $Facilities->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Guarantee = $VendorMessages->item(0)->getElementsByTagName("Guarantee");
                        if ($Guarantee->length > 0) {
                            $Text = $Guarantee->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Location = $VendorMessages->item(0)->getElementsByTagName("Location");
                        if ($Location->length > 0) {
                            $Text = $Location->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $MiscServices = $VendorMessages->item(0)->getElementsByTagName("MiscServices");
                        if ($MiscServices->length > 0) {
                            $Text = $MiscServices->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $policy = "";
                        $Policies = $VendorMessages->item(0)->getElementsByTagName("Policies");
                        if ($Policies->length > 0) {
                            $Text = $Policies->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $policy = $policy . $Text->item($j)->nodeValue;
                                    $policy = $policy . " ";
                                }
                            }
                        }
                        $policy = $policy . ".";
                        $cancelpolicy = $cancel . "\r\n" . $dep . "\r\n" . $policy;
                        $Recreation = $VendorMessages->item(0)->getElementsByTagName("Recreation");
                        if ($Recreation->length > 0) {
                            $Text = $Recreation->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Rooms = $VendorMessages->item(0)->getElementsByTagName("Rooms");
                        if ($Rooms->length > 0) {
                            $Text = $Rooms->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Safety = $VendorMessages->item(0)->getElementsByTagName("Safety");
                        if ($Safety->length > 0) {
                            $Text = $Safety->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Services = $VendorMessages->item(0)->getElementsByTagName("Services");
                        if ($Services->length > 0) {
                            $Text = $Services->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                        $Transportation = $VendorMessages->item(0)->getElementsByTagName("Transportation");
                        if ($Transportation->length > 0) {
                            $Text = $Transportation->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                for ($j=0; $j < $Text->length; $j++) { 
                                    $Text = $Text->item($j)->nodeValue;
                                }
                            }
                        }
                    }
                }
                $Guarantee = $RoomStay->item(0)->getElementsByTagName("Guarantee");
                if ($Guarantee->length > 0) {
                    $GuaranteesAccepted = $Guarantee->item(0)->getElementsByTagName("GuaranteesAccepted");
                    if ($GuaranteesAccepted->length > 0) {
                        $PaymentCard = $GuaranteesAccepted->item(0)->getElementsByTagName("PaymentCard");
                        if ($PaymentCard->length > 0) {
                            for ($k=0; $k < $PaymentCard->length; $k++) { 
                                $Type = $PaymentCard->item($k)->getAttribute("Type");
                                $Code = $PaymentCard->item($k)->getAttribute("Code");
                            }
                        }
                    }
                }
                $TimeSpan = $RoomStay->item(0)->getElementsByTagName("TimeSpan");
                if ($TimeSpan->length > 0) {
                    $Start = $TimeSpan->item(0)->getAttribute("Start");
                    $End = $TimeSpan->item(0)->getAttribute("End");
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
        
        $cancelpolicy = strtolower($cancelpolicy);
        $cancelpolicy = str_replace(" cancellation policy text -", ".<br/>", $cancelpolicy);
        $cancelpolicy = str_replace("see travelguidance.marriott.com.", "", $cancelpolicy);
        $cancelpolicy = str_replace("exceptions may apply- please see rate rules .", "", $cancelpolicy);
        $cancelpolicy = str_replace("exceptions may apply - please see hp .", "", $cancelpolicy);

        $texto = explode("\n", str_replace("\r", "", $cancelpolicy));
        foreach($texto as &$paragrafo){
            $paragrafo = ucfirst(strtolower($paragrafo));
        }   
        $text1 = $texto;
        $newtext = "";
        foreach($text1 as $value){
            $newtext .= "<p>" . $value . "</p>";
        }
        $cancelpolicy = $newtext;

        for($i=0; $i < 100; $i++) {
            $new = $i . " days";
            if (strpos($cancelpolicy, $new) !== false) {
                $days = '-' . $new;
                break;
            } else {
                $days = '';
            }
        }
        
        $date = date("Y-m-d", strtotime($days, strtotime($from_date)));
        $timestamp = strtotime($date);
        error_log("\r\n date: $date \r\n", 3, "/srv/www/htdocs/error_log");
        //$item['nonrefundable'] = true;
        $item['cancelpolicy'] = $translator->translate($cancelpolicy);
        $item['cancelpolicy_details'] = $translator->translate($cancelpolicy);
        if ($days != '') {
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $timestamp);
            $item['cancelpolicy_deadlinetimestamp'] = $date;  
        } else {
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", time());
            $item['cancelpolicy_deadlinetimestamp'] = time();  
        }
        
        array_push($roombreakdown, $item);
        array_push($roombreakdown2, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_msabre where sid='" . $shid . "' and hid=" . $hid;
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