<?php
error_log("\r\n TRAVCO - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mtravco where hid=" . $hid;
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
        $hotellist .= '<EdiCode>' . $row->sid . '</EdiCode>';
    }
}
if ($hotellist != "") {
    $affiliate_id_travco = 0;
    $sql = "select value from settings where name='TravcoAgentCode' and affiliate_id=$affiliate_id_travco";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravcoAgentCode = $row_settings['value'];
    }
    $sql = "select value from settings where name='TravcoAgentPassword' and affiliate_id=$affiliate_id_travco";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravcoAgentPassword = base64_decode($row_settings['value']);
    }

    $sql = "select value from settings where name='TravcoMarkup' and affiliate_id=$affiliate_id_travco";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravcoMarkup = (double) $row_settings['value'];
    } else {
        $TravcoMarkup = 0;
    }
    $sql = "select value from settings where name='TravcoServiceURL' and affiliate_id=$affiliate_id_travco";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravcoServiceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='TravcoTimeout' and affiliate_id=$affiliate_id_travco";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravcoTimeout = (int) $row_settings['value'];
    }

    $single_rooms = 0;
    $double_rooms = 0;
    $triple_rooms = 0;
    $quad_rooms = 0;
    $double_extra_beds = 0;
    for ($r = 0; $r < count($adults); $r ++) {
        if ($adults > 4) {
            // Travco does not support more than 3 rooms
            $breakSearch = 1;
        }
        switch ($adults) {
            case 1:
                $single_rooms = 1;
                break;
            case 2:
                $double_rooms = 1;
                if ($adults != 0) {
                    $double_extra_beds = 1;
                }
                break;
            case 3:
                $triple_rooms = 1;
                break;
            case 4:
                $quad_rooms = 1;
                break;
            default:
                $double_rooms = 1;
                if ($adults != 0) {
                    $double_extra_beds = 1;
                }
                break;
        }
    }

    $raw = "XMLString=<?xml version='1.0' encoding='UTF-8'?>
    <BOOKING type='HA' lang='en-GB' returnURLNeed='no' returnURL='http://' AGENTCODE='" . $TravcoAgentCode . "' AGENTPASSWORD='" . $TravcoAgentPassword . "' NeedCancellationDetails='YES' NeedImportantInformation='YES' AVAILABLE_HOTELS_ONLY='NO' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:noNamespaceSchemaLocation='http://’TravcoServerName’/trlink/schema/HotelAvailabilityV6Snd.xsd'>
        <DATA>
            <ROOMS_DATA SINGLE_ROOMS='" . $single_rooms . "' DOUBLE_ROOMS='" . $double_rooms . "' TRIPLE_ROOMS='" . $triple_rooms . "' QUAD_ROOMS='" . $quad_rooms . "' DOUBLE_EXTRA_BEDS='0'/>
            <DATE_DATA CHECK_IN_DATE='" . strftime("%d/%b/%Y", $from) . "' CHECK_OUT_DATE='" . strftime("%d/%b/%Y", $to) . "'/>
            <OPTIONAL_DATA NeedReductionAmount='YES' NeedHotelMessages='YES' NeedFreeNightDetails='YES' SortingOrder='Low'/>
            <ADDITIONAL_DATA PICTURE_NEED='YES' AMENITY_NEED='YES' HOTEL_ADDRESS_NEED='YES' TELEPHONE_NO_NEED='YES' FAX_NO_NEED='YES' EMAIL_NEED='YES' HotelDescription='YES' HotelCity='YES' HotelProperties='YES' HotelArrivalPointOther='YES' HotelArrivalPoint='YES' GeoCodes='YES' Location='YES' CityArea='YES' EnglishTextNeed='YES'/>
            <MultiHotelsRequest>";
    $raw .= $hotellist;    
    $raw .= "</MultiHotelsRequest>
        </DATA>
    </BOOKING>";
    error_log("\r\n TRAVCO RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($TravcoTimeout == 0) {
        $TravcoTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept-Encoding: gzip',
        'Host:xmlv6.travco.co.uk'
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $TravcoServiceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $TravcoTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $TravcoTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'travco';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>