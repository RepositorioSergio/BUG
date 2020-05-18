<?php
$scurrency = strtoupper($currency);
use Laminas\Http\Client;
use Laminas\Http\Request;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$sabre = false;
// error_log("\r\nStart Sabre Hotels\r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select name, country_id, zone_id, city_xml28, latitude, longitude from cities where id=" . $destination;
// error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $name = $row_settings["name"];
    $country_id = $row_settings["country_id"];
    $zone_id = $row_settings["zone_id"];
    $city_xml28 = $row_settings["city_xml28"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
} else {
    $city_xml28 = "";
}
// error_log("\r\nSabre Search Code: $city_xml28\r\n", 3, "/srv/www/htdocs/error_log");
if ($city_xml28 != "") {
    $affiliate_id = 0;
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
        $sql = "select value from settings where name='sabreDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_sabre";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportaluserID1' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sabretravelnetworktravelportaluserID1 = $row_settings['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportaluserID2' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sabretravelnetworktravelportaluserID2 = $row_settings['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalpassword1' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sabretravelnetworktravelportalpassword1 = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalpassword2' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sabretravelnetworktravelportalpassword2 = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalIPCC' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sabretravelnetworktravelportalIPCC = $row_settings['value'];
    }
    $sql = "select value from settings where name='enablesabretravelnetworktravelportal' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $enablesabretravelnetworktravelportal = $row_settings['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalwebservicesURL' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $sabretravelnetworktravelportalwebservicesURL = $row['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalmarkup' and affiliate_id=$affiliate_id_sabre";
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
    $sql = "select value from settings where name='sabretravelnetworktravelportalb2cmarkup' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $sabretravelnetworktravelportalb2cmarkup = $row['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalPartyIDFrom' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $sabretravelnetworktravelportalPartyIDFrom = $row['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalPartyIDTo' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $sabretravelnetworktravelportalPartyIDTo = $row['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalConversationID' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $sabretravelnetworktravelportalConversationID = $row['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalParallelSearch' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $sabretravelnetworktravelportalParallelSearch = $row['value'];
    }
    $sql = "select value from settings where name='sabretravelnetworktravelportalTimeout' and affiliate_id=$affiliate_id_sabre";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $sabretravelnetworktravelportalTimeout = (int) $row['value'];
    }
    date_default_timezone_set("UTC");
    $datetime = date('Y-m-d\TH:i:s');
    $raw2 = '<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/1999/XMLSchema"><soap-env:Header><eb:MessageHeader soap-env:mustUnderstand="1" eb:version="1.0"><eb:From><eb:PartyId /></eb:From><eb:To><eb:PartyId /></eb:To><eb:CPAId>' . $sabretravelnetworktravelportalIPCC . '</eb:CPAId><eb:ConversationId>' . $sabretravelnetworktravelportalConversationID . '</eb:ConversationId><eb:Service>SessionCreateRQ</eb:Service><eb:Action>SessionCreateRQ</eb:Action><eb:MessageData><eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId><eb:Timestamp>' . $datetime . '</eb:Timestamp></eb:MessageData></eb:MessageHeader><wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext" xmlns:wsu="http://schemas.xmlsoap.org/ws/2002/12/utility"><wsse:UsernameToken><wsse:Username>' . $sabretravelnetworktravelportaluserID1 . '</wsse:Username><wsse:Password>' . $sabretravelnetworktravelportalpassword1 . '</wsse:Password><Organization>' . $sabretravelnetworktravelportalIPCC . '</Organization><Domain>AA</Domain></wsse:UsernameToken></wsse:Security></soap-env:Header><soap-env:Body><eb:Manifest soap-env:mustUnderstand="1" eb:version="1.0"><eb:Reference xlink:href="cid:rootelement" xlink:type="simple" /></eb:Manifest><SessionCreateRQ><POS><Source PseudoCityCode="' . $sabretravelnetworktravelportalIPCC . '" /></POS></SessionCreateRQ><ns:SessionCreateRQ xmlns:ns="http://www.opentravel.org/OTA/2002/11" /></soap-env:Body></soap-env:Envelope>';
    $headers2 = array(
        "Content-Type: text/xml;charset=utf-8",
        "Accept-Encoding: gzip",
        "Content-length: " . strlen($raw2)
    );
    // error_log("\r\nSabre Session Request: $raw2\r\n", 3, "/srv/www/htdocs/error_log");
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $sabretravelnetworktravelportalwebservicesURL);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_HEADER, false);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
    curl_setopt($ch2, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch2, CURLOPT_TIMEOUT, $sabretravelnetworktravelportalwebservicesURL);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
    $response2 = curl_exec($ch2);
    curl_close($ch2);
    // error_log("\r\nSabre Session Response: $response2\r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_sabre');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchHotels.php',
            'errorline' => 0,
            'errormessage' => $sabretravelnetworktravelportalwebservicesURL . $raw2,
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
    }
    if ($response2 != "") {
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
        $count = 0;
        for ($r=0; $r < count($selectedAdults); $r++) { 
            $count = $count + $selectedAdults[$r] + $selectedChildren[$r];
        }
        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"><soapenv:Header><eb:MessageHeader soapenv:mustUnderstand="0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader"><eb:From><eb:PartyId>' . $sabretravelnetworktravelportalPartyIDFrom . '</eb:PartyId></eb:From><eb:To><eb:PartyId>' . $sabretravelnetworktravelportalPartyIDTo . '</eb:PartyId></eb:To><eb:CPAId>' . $sabretravelnetworktravelportalIPCC . '</eb:CPAId><eb:ConversationId>' . $sabretravelnetworktravelportalConversationID . '</eb:ConversationId><eb:Service>OTA_HotelAvailLLSRQ</eb:Service><eb:Action>OTA_HotelAvailLLSRQ</eb:Action><eb:MessageData><eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId><eb:Timestamp>' . $datetime . '</eb:Timestamp></eb:MessageData></eb:MessageHeader><eb:Security soapenv:mustUnderstand="0" xmlns:eb="http://schemas.xmlsoap.org/ws/2002/12/secext"><eb:BinarySecurityToken>' . $BinarySecurityToken . '</eb:BinarySecurityToken></eb:Security></soapenv:Header><soapenv:Body><OTA_HotelAvailRQ Version="2.3.0" xmlns="http://webservices.sabre.com/sabreXML/2011/10" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><AvailRequestSegment><Customer><Corporate><ID>' . $sabretravelnetworktravelportalIPCC . '</ID></Corporate></Customer>
     <GuestCounts Count="' . $count . '"/>
     <HotelSearchCriteria><Criterion><HotelRef HotelCityCode="' . $city_xml28 . '" /></Criterion></HotelSearchCriteria><TimeSpan End="' . strftime("%m-%d", $to) . '" Start="' . strftime("%m-%d", $from) . '" /></AvailRequestSegment></OTA_HotelAvailRQ></soapenv:Body></soapenv:Envelope>';
        error_log("\r\nSabre Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
        if ($sabretravelnetworktravelportalwebservicesURL != "" and $sabretravelnetworktravelportaluserID1 != "" and $sabretravelnetworktravelportalpassword1 != "") {
            $headers = array(
                "Content-Type: text/xml;charset=utf-8",
                "Accept-Encoding: gzip",
                "Content-length: " . strlen($raw)
            );
            $startTime = microtime();
            // error_log("\r\nSabre Request: $raw\r\n", 3, "/srv/www/htdocs/error_log");
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sabretravelnetworktravelportalwebservicesURL);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, $sabretravelnetworktravelportalTimeout);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $headers = curl_getinfo($ch);
            curl_close($ch);
            error_log("\r\nSabre Response: $response\r\n", 3, "/srv/www/htdocs/error_log");
            $endTime = microtime();
            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('log_sabre');
                $insert->values(array(
                    'datetime_created' => time(),
                    'filename' => 'SearchHotels.php',
                    'errorline' => $this->microtime_diff($startTime, $endTime),
                    'errormessage' => $sabretravelnetworktravelportalwebservicesURL . $raw,
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
                $OTA_HotelAvailRS = $Body->item(0)->getElementsByTagName("OTA_HotelAvailRS");
                if ($OTA_HotelAvailRS->length > 0) {
                    $AvailabilityOptions = $OTA_HotelAvailRS->item(0)->getElementsByTagName("AvailabilityOptions");
                    if ($AvailabilityOptions->length > 0) {
                        $AvailabilityOption = $AvailabilityOptions->item(0)->getElementsByTagName("AvailabilityOption");
                        if ($AvailabilityOption->length > 0) {
                            for ($i = 0; $i < $AvailabilityOption->length; $i ++) {
                                $RPH = $AvailabilityOption->item($i)->getAttribute("RPH");
                                $BasicPropertyInfo = $AvailabilityOption->item($i)->getElementsByTagName("BasicPropertyInfo");
                                if ($BasicPropertyInfo->length > 0) {
                                    $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                                    $shid = $HotelCode;
                                    $sfilter[] = " sid='$HotelCode' ";
                                    $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                                    $HotelCityCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCityCode");
                                    $Latitude = $BasicPropertyInfo->item(0)->getAttribute("Latitude");
                                    $Longitude = $BasicPropertyInfo->item(0)->getAttribute("Longitude");
                                    $GEO_ConfidenceLevel = $BasicPropertyInfo->item(0)->getAttribute("GEO_ConfidenceLevel");
                                    $Distance = $BasicPropertyInfo->item(0)->getAttribute("Distance");
                                    $ChainCode = $BasicPropertyInfo->item(0)->getAttribute("ChainCode");
                                    $AreaID = $BasicPropertyInfo->item(0)->getAttribute("AreaID");
                                    // Address
                                    $Address = $BasicPropertyInfo->item(0)->getElementsByTagName("Address");
                                    if ($Address->length > 0) {
                                        $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                                        if ($AddressLine->length > 0) {
                                            for ($iAux = 0; $iAux < $AddressLine->length; $iAux ++) {
                                                $AddressLine = $AddressLine->item($iAux)->nodeValue;
                                            }
                                        }
                                    }
                                    // ContactNumbers
                                    $ContactNumbers = $BasicPropertyInfo->item(0)->getElementsByTagName("ContactNumbers");
                                    if ($ContactNumbers->length > 0) {
                                        $ContactNumber = $ContactNumbers->item(0)->getElementsByTagName("ContactNumber");
                                        if ($ContactNumber->length > 0) {
                                            $Phone = $ContactNumber->item(0)->getAttribute("Phone");
                                            $Fax = $ContactNumber->item(0)->getAttribute("Fax");
                                        }
                                    }
                                    // DirectConnect
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
                                    // LocationDescription
                                    $LocationDescription = $BasicPropertyInfo->item(0)->getElementsByTagName("LocationDescription");
                                    if ($LocationDescription->length > 0) {
                                        $LocationDescriptionCode = $LocationDescription->item(0)->getAttribute("Code");
                                        $LocationDescriptionText = $LocationDescription->item(0)->getElementsByTagName("Text");
                                        if ($LocationDescriptionText->length > 0) {
                                            $LocationDescriptionText = $LocationDescriptionText->item(0)->nodeValue;
                                        } else {
                                            $LocationDescriptionText = "";
                                        }
                                    }
                                    // Property
                                    $Property = $BasicPropertyInfo->item(0)->getElementsByTagName("Property");
                                    if ($Property->length > 0) {
                                        $PropertyRating = $Property->item(0)->getAttribute("Rating");
                                        $PropertyText = $Property->item(0)->getElementsByTagName("Text");
                                        if ($PropertyText->length > 0) {
                                            $PropertyText = $PropertyText->item(0)->nodeValue;
                                        } else {
                                            $PropertyText = "";
                                        }
                                    }
                                    // PropertyOptionInfo
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
                                    // RateRange
                                    $RateRange = $BasicPropertyInfo->item(0)->getElementsByTagName("RateRange");
                                    if ($RateRange->length > 0) {
                                        $Min = $RateRange->item(0)->getAttribute("Min");
                                        $Max = $RateRange->item(0)->getAttribute("Max");
                                        $CurrencyCode = $RateRange->item(0)->getAttribute("CurrencyCode");
                                    }
                                    $total = $Max;
                                    $nettotal = $Min;
                                    // RoomRate
                                    $RoomRate = $BasicPropertyInfo->item(0)->getElementsByTagName("RoomRate");
                                    if ($RoomRate->length > 0) {
                                        $RateLevelCode = $RoomRate->item(0)->getAttribute("RateLevelCode");
                                        $HotelRateCode = $RoomRate->item(0)->getElementsByTagName("HotelRateCode");
                                        if ($HotelRateCode->length > 0) {
                                            $HotelRateCode = $HotelRateCode->item(0)->nodeValue;
                                        } else {
                                            $HotelRateCode = "";
                                        }
                                        $AdditionalInfo = $RoomRate->item(0)->getElementsByTagName("AdditionalInfo");
                                        if ($AdditionalInfo->length > 0) {
                                            $CancelPolicy = $AdditionalInfo->item(0)->getElementsByTagName("CancelPolicy");
                                            if ($CancelPolicy->length > 0) {
                                                $Numeric = $CancelPolicy->item(0)->getAttribute("Numeric");
                                            }
                                        }
                                    }
                                    // SpecialOffers
                                    $SpecialOffers = $BasicPropertyInfo->item(0)->getElementsByTagName("SpecialOffers");
                                    if ($SpecialOffers->length > 0) {
                                        $SpecialOffers_Ind = $SpecialOffers->item(0)->getAttribute("Ind");
                                    }
                                    
                                    $zRooms = 0;
                                    if (is_array($tmp[$shid])) {
                                        $baseCounterDetails = count($tmp[$shid]['details'][$zRooms]);
                                    } else {
                                        $baseCounterDetails = 0;
                                    }
                                    
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['name'] = $HotelName;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['hotelid'] = $HotelCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['shid'] = $shid;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['status'] = 1;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-28";
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['BinarySecurityToken'] = $BinarySecurityToken;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['HotelRateCode'] = $HotelRateCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['RPH'] = $RPH;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['ChainCode'] = $ChainCode;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['adults'] = $selectedAdults[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['children'] = $selectedChildren[$zRooms];
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['nettotal'] = $nettotal;
                                    if ($sabretravelnetworktravelportalmarkup != 0) {
                                        $total = $total + (($total * $sabretravelnetworktravelportalmarkup) / 100);
                                    }
                                    // Geo target markup
                                    if ($internalmarkup != 0) {
                                        $total = $total + (($total * $internalmarkup) / 100);
                                    }
                                    // Agent markup
                                    if ($agent_markup != 0) {
                                        $total = $total + (($total * $agent_markup) / 100);
                                    }
                                    // Fallback Markup
                                    if ($sabretravelnetworktravelportalmarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                                        $total = $total + (($total * $HotelsMarkupFallback) / 100);
                                    }
                                    // Agent discount
                                    if ($agent_discount != 0) {
                                        $total = $total - (($total * $agent_discount) / 100);
                                    }
                                    if ($scurrency != "" and $currency != $scurrency) {
                                        $total = $CurrencyConverter->convert($total, $currency, $scurrency);
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['total'] = (double) $total;
                                    try {
                                        $sql = "select mapped from board_mapping where description='" . addslashes($BreakfastTypeName) . "'";
                                        $statement = $db->createStatement($sql);
                                        $statement->prepare();
                                        $row_board_mapping = $statement->execute();
                                        $row_board_mapping->buffer();
                                        if ($row_board_mapping->valid()) {
                                            $row_board_mapping = $row_board_mapping->current();
                                            $BreakfastTypeName = $row_board_mapping["mapped"];
                                        }
                                    } catch (\Exception $e) {
                                        $logger = new Logger();
                                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                                        $logger->addWriter($writer);
                                        $logger->info($e->getMessage());
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['meal'] = $translator->translate($BreakfastTypeName);
                                    $pricebreakdown = array();
                                    $pricebreakdownCount = 0;
                                    $amount = $total / $noOfNights;
                                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                                        $pricebreakdown[$pricebreakdownCount]['price'] = $filter->filter($amount);
                                        $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                                        $pricebreakdownCount = $pricebreakdownCount + 1;
                                    }
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['scurrency'] = $CurrencyCode;
                                    //
                                    // Special
                                    //
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['special'] = false;
                                    $tmp[$shid]['details'][$zRooms][$baseCounterDetails]['specialdescription'] = "";
                                    $count = $count + 1;
                                }
                            }
                        }
                        $sabre = true;
                    }
                }
            }
        }
    }
    // Close Session
    if ($BinarySecurityToken != "") {
        $action = 'SessionCloseRQ';
        $xml = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><soapenv:Header><MessageHeader xmlns="http://www.ebxml.org/namespaces/messageHeader"><From><PartyId type="urn:x12.org:IO5:01">' . $sabretravelnetworktravelportalPartyIDFrom . '</PartyId></From><To><PartyId type="urn:x12.org:IO5:01">' . $sabretravelnetworktravelportalPartyIDTo . '</PartyId></To><CPAId>' . $sabretravelnetworktravelportalIPCC . '</CPAId><ConversationId>' . $ConversationId . '</ConversationId><Service type="OTA">' . $action . '</Service><Action>' . $action . '</Action><MessageData><MessageId>' . $mid . '</MessageId><Timestamp>' . $timestamp . '</Timestamp><TimeToLive>' . $timetolive . '</TimeToLive></MessageData></MessageHeader><wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext"><wsse:BinarySecurityToken valueType="String" EncodingType="wsse:Base64Binary">' . $BinarySecurityToken . '</wsse:BinarySecurityToken></wsse:Security></soapenv:Header><soapenv:Body><SessionCloseRQ xmlns="http://www.opentravel.org/OTA/2002/11"><POS><Source PseudoCityCode="' . $sabretravelnetworktravelportalIPCC . '" /></POS></SessionCloseRQ></soapenv:Body></soapenv:Envelope>';
        // error_log("\r\nClose Sabre Request : $xml\r\n", 3, "/srv/www/htdocs/error_log");
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $sabretravelnetworktravelportalwebservicesURL);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 0);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($xml)
        ));
        $resultCloseSession = curl_exec($soap_do);
        // error_log("\r\nClose Sabre Result : $resultCloseSession\r\n", 3, "/srv/www/htdocs/error_log");
        $err = curl_error($soap_do);
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_sabre');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'SearchHotels.php',
                'errorline' => 0,
                'errormessage' => $sabretravelnetworktravelportalwebservicesURL . $xml,
                'sqlcontext' => $resultCloseSession,
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
    }
    // error_log("\r\nSabre Array:" . print_r($tmp, true) . " \r\n", 3, "/srv/www/htdocs/error_log");
    if ($sabre == true) {
        $sfilter = implode(' or ', $sfilter);
        try {
            $sql = "select hid, sid from xmlhotels_msabre where " . $sfilter;
            // error_log("\r\n SQL: $sql \r\n", 3, "/srv/www/htdocs/error_log");
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
        } catch (\Exception $e) {
            $logger = new Logger();
            $writer = new Writer\Stream('/srv/www/htdocs/error_log');
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        if (is_array($sidfilter)) {
            $sidfilter = implode(',', $sidfilter);
            $query = 'call xmlhotels("' . $sidfilter . '")';
            $supplier = 28;
            // error_log("\r\nSabre Query: $query \r\n", 3, "/srv/www/htdocs/error_log");
            try {
                $sql = new Sql($db);
                $delete = $sql->delete();
                $delete->from('quote_session_sabre');
                $delete->where(array(
                    'session_id' => $session_id
                ));
                $statement = $sql->prepareStatementForSqlObject($delete);
                $results = $statement->execute();
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('quote_session_sabre');
                $insert->values(array(
                    'session_id' => $session_id,
                    'xmlrequest' => (string) $raw,
                    'xmlresult' => (string) $response,
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
// error_log("\r\nEnd Sabre\r\n", 3, "/srv/www/htdocs/error_log");
?>