<?php
// error_log("\r\n SABRE - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_msabre where hid=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $result = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $hotellist .= '' . $row->sid . '';
    }
}
if ($hotellist != "") {
    $affiliate_id_sabre = 0;
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
    
    $raw2 = '<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsd="http://www.w3.org/1999/XMLSchema">
    <soap-env:Header>
    <eb:MessageHeader soap-env:mustUnderstand="1" eb:version="1.0">
        <eb:From>
            <eb:PartyId />
        </eb:From>
        <eb:To>
            <eb:PartyId />
        </eb:To>
        <eb:CPAId>' . $sabretravelnetworktravelportalIPCC . '</eb:CPAId>
        <eb:ConversationId>' . $sabretravelnetworktravelportalConversationID . '</eb:ConversationId>
        <eb:Service>SessionCreateRQ</eb:Service>
        <eb:Action>SessionCreateRQ</eb:Action>
        <eb:MessageData>
            <eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId>
            <eb:Timestamp>' . $datetime . '</eb:Timestamp>
        </eb:MessageData>
    </eb:MessageHeader>
    <wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext" xmlns:wsu="http://schemas.xmlsoap.org/ws/2002/12/utility">
        <wsse:UsernameToken>
            <wsse:Username>' . $sabretravelnetworktravelportaluserID1 . '</wsse:Username>
            <wsse:Password>' . $sabretravelnetworktravelportalpassword1 . '</wsse:Password>
            <Organization>' . $sabretravelnetworktravelportalIPCC . '</Organization>
            <Domain>AA</Domain>
        </wsse:UsernameToken>
    </wsse:Security>
    </soap-env:Header>
    <soap-env:Body>
    <eb:Manifest soap-env:mustUnderstand="1" eb:version="1.0">
        <eb:Reference xlink:href="cid:rootelement" xlink:type="simple" />
    </eb:Manifest>
    <SessionCreateRQ>
        <POS>
            <Source PseudoCityCode="' . $sabretravelnetworktravelportalIPCC . '" />
        </POS>
    </SessionCreateRQ>
    <ns:SessionCreateRQ xmlns:ns="http://www.opentravel.org/OTA/2002/11" />
    </soap-env:Body>
    </soap-env:Envelope>';
    
    $headers2 = array(
        "Content-Type: text/xml;charset=utf-8",
        "Accept-Encoding: gzip",
        "Content-length: " . strlen($raw2)
    );
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
    // error_log("\r\nResponse2: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
    
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
                            <HotelRef HotelCode="' . $hotellist . '"/>
                        </Criterion>
                    </HotelSearchCriteria>
                    <TimeSpan End="' . strftime("%Y-%m-%d", $to) . '" Start="' . strftime("%Y-%m-%d", $from) . '"/>
                </AvailRequestSegment>
            </HotelPropertyDescriptionRQ>
        </soapenv:Body>
     </soapenv:Envelope>';
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: text/xml;charset=utf-8",
        "Accept-Encoding: gzip",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $sabretravelnetworktravelportalwebservicesURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $sabretravelnetworktravelportalTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $sabretravelnetworktravelportalTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'sabre';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>