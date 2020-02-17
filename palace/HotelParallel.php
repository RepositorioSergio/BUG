<?php
error_log("\r\nPalace - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mpalace where hid=" . $hid;
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
    $affiliate_id_palace = 0;
    $sql = "select value from settings where name='palaceresortslogin' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortslogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='palaceresortsCancellationPolicy' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsCancellationPolicy = (int) $row_settings['value'];
    } else {
        $palaceresortsCancellationPolicy = 15;
    }
    $sql = "select value from settings where name='palaceresortspassword' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortspassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='palaceresortsMarkup' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsMarkup = (double) $row_settings['value'];
    } else {
        $palaceresortsMarkup = 0;
    }
    $sql = "select value from settings where name='palaceresortswebserviceurl' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortswebserviceurl = $row_settings['value'];
    }
    $sql = "select value from settings where name='palaceresortsAgencyCode' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsAgencyCode = $row_settings['value'];
    }
    $sql = "select value from settings where name='palaceresortsSecurityCode' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsSecurityCode = $row_settings['value'];
    }
    $sql = "select value from settings where name='palaceresortsTimeout' and affiliate_id=$affiliate_id_palace";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $palaceresortsTimeout = (int) $row_settings['value'];
    }
    if ($palaceresortsTimeout == 0) {
        $palaceresortsTimeout = 120;
    }
    $arrival_date = strftime("%Y-%m-%d", $from);
    $departure_date = strftime("%Y-%m-%d", $to);
    $channelsParallelPalace = array();
    $channelsParallelPalaceRoomDescription = array();
    $channelsParallelPalaceBedDescription = array();
    $channelsParallelPalaceRoomType = array();
    $channelsParallelPalaceBedType = array();
    $multiParallelPalace = curl_multi_init();
    $sql = "select description, roomtype, bed from palace_roomtypes where hotelcode='" . $hotellist . "'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $sql = "select maxpax, allowchildren, maxchild, maxadults from palace_rooms where hotelcode='" . $hotellist . "' and roomtype='" . $row['roomtype'] . "'";
            // error_log("\r\n$sql\r\n", 3, "/srv/www/htdocs/error_log");
            $statement2 = $db->createStatement($sql);
            $statement2->prepare();
            $row_palace_rooms = $statement2->execute();
            $row_palace_rooms->buffer();
            if ($row_palace_rooms->valid()) {
                $row_palace_rooms = $row_palace_rooms->current();
                $valid = true;
                if (($adults + $children) > $row_palace_rooms['maxpax']) {
                    $valid = false;
                }
                if ($adults > $row_palace_rooms['maxadults']) {
                    $valid = false;
                }
                if ($children > $row_palace_rooms['maxchild']) {
                    $valid = false;
                }
                if ($children > 0) {
                    if ($row_palace_rooms['allowchildren'] == "NO") {
                        $valid = false;
                    }
                }
            } else {
                $valid = false;
            }
            if ($valid == true) {
                $raw = '<?xml version="1.0" encoding="UTF-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://localhost/pr_xmlschemas/hotel/01-03-2006/availability.xsd" xmlns:ns2="http://localhost/pr_xmlschemas/hotel/01-03-2006/availabilityRequest.xsd" xmlns:ns3="http://localhost/pr_xmlschemas/hotel/01-03-2006/authInfo.xsd" xmlns:ns4="http://localhost/xmlschemas/enterpriseservice/16-07-2009/"><SOAP-ENV:Body><ns4:GetAvailability><ns2:availabilityRequest><ns2:data><ns1:hotel>' . $hotellist . '</ns1:hotel><ns1:room_type>' . $row['roomtype'] . '</ns1:room_type><ns1:bed_type>' . $row['bed'] . '</ns1:bed_type><ns1:arrival_date>' . $arrival_date . '</ns1:arrival_date><ns1:departure_date>' . $departure_date . '</ns1:departure_date>
                    <ns1:adultos>' . $adults . '</ns1:adultos>
                    <ns1:menores>' . $children . '</ns1:menores>
                    <ns1:baby>0</ns1:baby>
                    <ns1:child>0</ns1:child>
                    <ns1:kid>0</ns1:kid><ns1:rate_plan></ns1:rate_plan><ns1:group_code></ns1:group_code><ns1:promotion_code></ns1:promotion_code><ns1:idioma></ns1:idioma><ns1:agency_cd>' . $palaceresortsAgencyCode . '</ns1:agency_cd></ns2:data><ns2:Tag></ns2:Tag><ns2:AuthInfo><ns3:Recnum>0</ns3:Recnum><ns3:Ent_User>' . $palaceresortslogin . '</ns3:Ent_User><ns3:Ent_Pass>' . $palaceresortspassword . '</ns3:Ent_Pass><ns3:Ent_Term>' . $palaceresortsSecurityCode . '</ns3:Ent_Term></ns2:AuthInfo></ns2:availabilityRequest></ns4:GetAvailability></SOAP-ENV:Body></SOAP-ENV:Envelope>';
                // error_log("\r\nPalace RAW Request - $raw\r\n", 3, "/srv/www/htdocs/error_log");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    "Content-type: text/xml",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "Host: api.palaceresorts.com",
                    "Content-length: " . strlen($raw)
                ));
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);
                curl_setopt($ch, CURLOPT_URL, $palaceresortswebserviceurl);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                curl_setopt($ch, CURLOPT_VERBOSE, false);
                curl_setopt($ch, CURLOPT_POST, true);
                // curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $palaceresortsTimeout);
                curl_setopt($ch, CURLOPT_TIMEOUT, $palaceresortsTimeout);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($multiParallel, $ch);
                $requestsParallel[$nC] = 'palace';
                $channelsParallel[$nC] = $ch;
                $channelsParallelRequest[$nC] = $raw;
                $channelsParallelPalaceRoomType[$nC] = $row['roomtype'];
                $channelsParallelPalaceBedType[$nC] = $row['bed'];
                $channelsParallelPalaceRoomDescription[$nC] = $row['description'];
                if ($row['bed'] == 'K') {
                    $channelsParallelPalaceBedDescription[$nC] = $translator->translate("1 King Bed");
                } elseif ($row['bed'] == 'D') {
                    $channelsParallelPalaceBedDescription[$nC] = $translator->translate("2 Double Beds");
                } else {
                    $channelsParallelPalaceBedDescription[$nC] = "";
                }
                $nC ++;
            }
        }
    }
}
error_log("\r\nPalace - Hotel Parallel Search - EOF\r\n", 3, "/srv/www/htdocs/error_log");
?>