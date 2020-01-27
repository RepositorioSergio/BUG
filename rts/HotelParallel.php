<?php
error_log("\r\n RTS - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mrts where hid=" . $hid;
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
error_log("\r\n HOTEL $hotellist \r\n", 3, "/srv/www/htdocs/error_log");
if ($hotellist != "") {
    $affiliate_id_rts = 0;
    $sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rtsID = $row_settings['value'];
    }
    $sql = "select value from settings where name='rtsPassword' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rtsPassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rtsSiteCode = $row_settings['value'];
    }
    $sql = "select value from settings where name='rtsRequestType' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rtsRequestType = $row_settings['value'];
    }
    $sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $rtsServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='rtsMarkup' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $rtsMarkup = (double) $row_settings['value'];
    } else {
        $rtsMarkup = 0;
    }
    $sql = "select value from settings where name='RTSTimeout' and affiliate_id=$affiliate_id_rts";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $RTSTimeout = (int) $row_settings['value'];
    }
    $city_xml19 = preg_replace("/[^A-Z]/", "", $hotellist);;
    $raw = "<?xml version='1.0' encoding='utf-8'?>
    <soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
    <soap:Header>
        <BaseInfo xmlns='http://www.rts.co.kr/'>
            <SiteCode>" . $rtsSiteCode . "</SiteCode>
            <Password>" . $rtsPassword . "</Password>
            <RequestType>NetPartner</RequestType>
        </BaseInfo>
    </soap:Header>
    <soap:Body>
    <GetHotelSearchListForCustomerCount xmlns='http://www.rts.co.kr/'>
        <HotelSearchListNetGuestCount>
            <LanguageCode>AR</LanguageCode>
            <TravelerNationality>AR</TravelerNationality>
            <CityCode>" . $city_xml19 . "</CityCode>
            <CheckInDate>" . strftime("%Y-%m-%d", $from) . "</CheckInDate>
            <CheckOutDate>" . strftime("%Y-%m-%d", $to) . "</CheckOutDate>
            <StarRating>0</StarRating>
            <LocationCode></LocationCode>
            <SupplierCompCode></SupplierCompCode>
            <AvailableHotelOnly>true</AvailableHotelOnly>
            <RecommendHotelOnly>false</RecommendHotelOnly>
            <ClientCurrencyCode>USD</ClientCurrencyCode>
            <ItemName></ItemName>
            <SellerMarkup>*1</SellerMarkup>
            <CompareYn>false</CompareYn>
            <SortType></SortType>
            <ItemCodeList>
                <ItemCodeInfo>
                    <ItemCode>" . $hotellist . "</ItemCode>
                    <ItemNo>0</ItemNo>
                </ItemCodeInfo>
            </ItemCodeList>
            <GuestList>";
    for ($r = 0; $r < 1; $r ++) {
        $raw = $raw . "<GuestsInfo>
                <AdultCount>" . $adults . "</AdultCount>";
        if ($children > 0 and $children > 1 and $children < 3) {
            $raw = $raw . "<ChildCount>" . $children . "</ChildCount>
                    <RoomCount>1</RoomCount>";
            $count = 0;
            for ($z = 0; $z < $children; $z ++) {
                if ($count == 0) {
                    $raw = $raw . "<ChildAge1>" . $children_ages[$r][$z] . "</ChildAge1>";
                } else {
                    $raw = $raw . "<ChildAge2>" . $children_ages[$r][$z] . "</ChildAge2>";
                }
                $count = $count + 1;
            }
        } else if ($children > 0) {
            $raw = $raw . "<ChildCount>" . $children . "</ChildCount>
                    <RoomCount>1</RoomCount>";
            for ($z = 0; $z < $children; $z ++) {
                $raw = $raw . "<ChildAge1>" . $children_ages[$r][$z] . "</ChildAge1>";
            }
            $raw = $raw . "<ChildAge2>0</ChildAge2>";
        } else {
            $raw = $raw . "<ChildCount>0</ChildCount>
                    <RoomCount>1</RoomCount>
                    <ChildAge1>0</ChildAge1>
                    <ChildAge2>0</ChildAge2>";
        }
        $raw = $raw . "</GuestsInfo>";
    }

    $raw = $raw . "</GuestList>
        </HotelSearchListNetGuestCount>
    </GetHotelSearchListForCustomerCount>
    </soap:Body>
    </soap:Envelope>";
    error_log("\r\n RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    $RTSTimeout = 0;
    if ($RTSTimeout == 0) {
        $RTSTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-type: text/xml;",
        "SOAPAction: http://www.rts.co.kr/GetHotelSearchListForCustomerCount",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $rtsServiceURL . 'WebServiceProjects/NetWebService/WsHotelProducts.asmx');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    //curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $RTSTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $RTSTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'rts';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>