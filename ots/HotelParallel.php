<?php
error_log("\r\n OTS - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mhotelbeds where hid=" . $hid;
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
        if ($hotellist == "") {
            $hotellist = $row->sid;
        } else {
            $hotellist .= "," . $row->sid;
        }
    }
}
if ($hotellist != "") {
    $affiliate_id_hotelbeds = 0;
    $sql = "select value from settings where name='hotelbedsserviceURL' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsserviceURL = $row_settings["value"];
    }
    $sql = "select value from settings where name='hotelbedsuser' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsuser = $row_settings["value"];
    }
    $sql = "select value from settings where name='hotelbedspassword' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedspassword = base64_decode($row_settings["value"]);
    }
    $sql = "select value from settings where name='hotelbedsRestFulXMLJsonVersion' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsRestFulXMLJsonVersion = (int) $row_settings["value"];
    } else {
        $hotelbedsRestFulXMLJsonVersion = 0;
    }
    $sql = "select value from settings where name='hotelbedsMaxResultsPerformance' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsMaxResultsPerformance = (int) $row_settings["value"];
    } else {
        $hotelbedsMaxResultsPerformance = 9999;
    }
    $sql = "select value from settings where name='hotelbedsTimeout' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsTimeout = (int) $row_settings["value"];
    } else {
        $hotelbedsTimeout = 120;
    }
    $sql = "select value from settings where name='hotelbedslanguage' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedslanguage = $row_settings["value"];
    } else {
        $hotelbedslanguage = "";
    }
    $sql = "select value from settings where name='hotelbedsEnableLibeRate' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsEnableLibeRate = (int) $row_settings["value"];
    } else {
        $hotelbedsEnableLibeRate = 0;
    }
    $sql = "select value from settings where name='hotelbedsEnableOpaqueProducts' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsEnableOpaqueProducts = (int) $row_settings["value"];
    } else {
        $hotelbedsEnableOpaqueProducts = 0;
    }
    $sql = "select value from settings where name='hotelbedsMarkup' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsMarkup = (int) $row_settings["value"];
    }
    $sfilter = array();
    $signature = hash("sha256", $hotelbedsuser . $hotelbedspassword . time());
    $endpoint = $hotelbedsserviceURL . "hotel-api/1.0/hotels";
    if ($nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . $nationality;
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        } else {
            $sourceMarket = "";
        }
    } else {
        $sourceMarket = "";
    }

    $HotelCode = "AMTSPT0012";

    $url = "https://sintesb.axisdata.net/apu-test/ota";
    $raw = '<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="OTA_HotelAvailRQ.xsd" Version="0.1" EchoToken="123322344">
    <POS>
        <Source>
            <RequestorID Instance="MF001" ID_Context="AxisData" ID="TEST" Type="22"/>
        </Source>
        <Source>
            <RequestorID Type="88" ID="TEST" MessagePassword="testpass"/>
        </Source>
    </POS>
    <AvailRequestSegments>
        <AvailRequestSegment>
            <StayDateRange End="2019-12-12" Start="2019-12-10"/>
            <RoomStayCandidates>
                <RoomStayCandidate Quantity="1" RPH="5">
                    <GuestCounts>
                        <GuestCount AgeQualifyingCode="10" Count="2"/>
                    </GuestCounts>
                </RoomStayCandidate>
            </RoomStayCandidates>
            <HotelSearchCriteria>
                <Criterion ExactMatch="true">
                    <HotelRef HotelCode="' . $HotelCode . '"/>
                </Criterion>
            </HotelSearchCriteria>
        </AvailRequestSegment>
    </AvailRequestSegments>
    </OTA_HotelAvailRQ>';
    if ($hotelbedsTimeout == 0) {
        $hotelbedsTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept-Encoding: gzip,deflate',
        'Content-Length:' . strlen($raw),
        'Content-Type: text/xml;charset=utf-8',
        'Authorization: 1831338:b57a754c-5e06-4cdd-ac0d-2ea58c48ef74'
    ));
    
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $hotelbedsTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $hotelbedsTimeout);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'ots';
    $channelsParallel[$nC] = $ch;
    $nC ++;
}

?>