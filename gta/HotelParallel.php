<?php
error_log("\r\nGTA - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
unset($array);
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$hotellist = "";
$sql = "select sid from xmlhotels_mgta where hid=" . $hid;
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
        $hotellist .= '<ItemCode>' . $row->sid . '</ItemCode>';
    }
}
if ($hotellist != "") {
    $affiliate_id_gta = 0;
    $sql = "select value from settings where name='gtalogin' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtalogin = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtaemail' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtaemail = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtapassword' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtapassword = base64_decode($row_settings["value"]);
    }
    $sql = "select value from settings where name='gtacurrency' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtacurrency = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtaEnableTripadvisorRatings' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtaEnableTripadvisorRatings = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtaTripAdvisorPartnerKey' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtaTripAdvisorPartnerKey = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtaTimeout' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtaTimeout = (int) $row_settings["value"];
    } else {
        $gtaTimeout = 0;
    }
    $sql = "select value from settings where name='gtatesting' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtatesting = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtamarkup' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtamarkup = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtaInventory' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtaInventory = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtasubmissionurl' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtasubmissionurl = $row_settings["value"];
    }
    $sql = "select value from settings where name='gtalogin' and affiliate_id=$affiliate_id_gta";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $gtalogin = $row_settings["value"];
    }
    $sql = "select city_xml11 from cities where id=" . $destination;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $city_xml11 = $row_settings["city_xml11"];
    } else {
        $city_xml11 = "";
    }
    if ($nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . $nationality;
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $countrytag = ' Country="' . $row_settings["iso_code_2"] . '"';
        } else {
            $countrytag = "";
        }
    } else {
        $countrytag = "";
    }
    if ($gtatesting == 1) {
        $gtasubmissionurl = "https://interface.demo.gta-travel.com/rbsusapi/RequestListenerServlet";
    }
    $languageGTA = substr($language, 0, 2);
    if ($languageGTA == "") {
        $languageGTA = "en";
    }
    
    $aux = $children + $adults;
    if ($aux <= 9) {
        $numberofcots = "";
        $nrextrabeds = 0;
        $raw = '<?xml version="1.0" encoding="UTF-8" ?><Request><Source><RequestorID Client="' . $gtalogin . '" EMailAddress="' . $gtaemail . '" Password="' . $gtapassword . '"/>';
        if ($gtacurrency != "") {
            $Currency = $gtacurrency;
            $raw .= '<RequestorPreferences Language="' . $languageGTA . '"  Currency="' . $gtacurrency . '"' . $countrytag . '>';
        } else {
            $raw .= '<RequestorPreferences Language="' . $languageGTA . '"' . $countrytag . '>';
        }
        $raw .= '<RequestMode>SYNCHRONOUS</RequestMode></RequestorPreferences></Source><RequestDetails><SearchHotelPricePaxRequest><ItemDestination DestinationType="city" DestinationCode="' . $city_xml11 . '"/><ItemCodes>' . $hotellist . '</ItemCodes>';
        if ($gtaInventory == true) {
            $raw .= '<ImmediateConfirmationOnly />';
        }
        $raw .= '<PeriodOfStay><CheckInDate>' . date(strftime("%Y-%m-%d", $from)) . '</CheckInDate><Duration>' . $nights . '</Duration></PeriodOfStay>';
        $raw .= '<IncludeRecommended /><IncludePriceBreakdown /><IncludeChargeConditions />';
        $numberofcotsString = '';
        $raw .= '<PaxRooms>';
        $cots = 0;
        $r = 0;
        if ($children > 0) {
            for ($z = 0; $z < $children; $z ++) {
                if ($children_ages[$z] < 2) {
                    $cots = $cots + 1;
                }
            }
        }
        $raw .= '<PaxRoom Adults="' . $adults . '" Cots="' . $cots . '" RoomIndex="' . ($r + 1) . '">';
        if ($children > 0) {
            $raw .= '<ChildAges>';
            for ($zK = 0; $zK < $children; $zK ++) {
                if ($children_ages[$zK] > 1) {
                    $raw .= '<Age>' . $children_ages[$zK] . '</Age>';
                }
            }
            $raw .= '</ChildAges>';
        }
        $raw .= '</PaxRoom></PaxRooms>';
        if ((int) $stars > 0) {
            $raw .= '<StarRatingRange><Min>' . (int) $stars . '</Min><Max>5</Max></StarRatingRange>';
        }
        $raw .= '</SearchHotelPricePaxRequest></RequestDetails></Request>';
        error_log("\r\nGTA RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
        if ($gtaTimeout == 0) {
            $gtaTimeout = 120;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Accept-Encoding: gzip, deflate",
            "User-Agent: curl/7.37.0",
            "Content-Encoding: UTF-8",
            "Content-Type: text/xml; charset=UTF-8",
            "Content-length: " . strlen($raw)
        ));
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_URL, $gtasubmissionurl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $gtaTimeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $gtaTimeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_multi_add_handle($multiParallel, $ch);
        $requestsParallel[$nC] = 'gta';
        $channelsParallel[$nC] = $ch;
        $channelsParallelRequest[$nC] = $raw;
        $nC ++;
    }
}
?>