<?php
// error_log("\r\nTravelplan / Globalia - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
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
$hotellist = "";
$sql = "select sid from xmlhotels_mglobalia where hid=" . $hid;
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
    $affiliate_id_travelplan = 0;
    $sql = "select city_xml50, latitude, longitude from cities where id=" . $destination;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $city_xml50 = $row_settings["city_xml50"];
        $latitude = $row_settings["latitude"];
        $longitude = $row_settings["longitude"];
    } else {
        $city_xml50 = 0;
    }
    if ($city_xml50 != "") {
        $city_xml50 = explode(":", $city_xml50);
        $x50_0 = $city_xml50[0];
        $x50_1 = $city_xml50[1];
        $x50_2 = $city_xml50[2];
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
        $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
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
        $sql = "select value from settings where name='TravelPlanDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_travelplan";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $residenceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='TravelPlanuser' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravelPlanuser = $row_settings['value'];
    }
    $sql = "select value from settings where name='TravelPlanTimeout' and affiliate_id=$affiliate_id_travelplan";
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
    $sql = "select value from settings where name='TravelPlanpassword' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravelPlanpassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='TravelPlanMarkup' and affiliate_id=$affiliate_id_travelplan";
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
    $sql = "select value from settings where name='TravelPlanserviceURL' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravelPlanserviceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='TravelPlanSystem' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravelPlanSystem = $row_settings['value'];
    }
    $sql = "select value from settings where name='TravelPlanSalesChannel' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravelPlanSalesChannel = $row_settings['value'];
    }
    $sql = "select value from settings where name='TravelPlanlanguage' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravelPlanlanguage = $row_settings['value'];
    }
    $sql = "select value from settings where name='TravelPlanConnectionString' and affiliate_id=$affiliate_id_travelplan";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $TravelPlanConnectionString = $row_settings['value'];
    }
    $province = $x50_0 . '' . $x50_1;
    $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns0="http://www.opentravel.org/OTA/2003/05"><soapenv:Header/><soapenv:Body><ns0:OTA_HotelAvailRQ Version="1"><ns0:AvailRequestSegments><ns0:AvailRequestSegment><ns0:StayDateRange Start="' . strftime("%Y-%m-%d", $from) . '" End="' . strftime("%Y-%m-%d", $to) . '"/>';
    if ($residenceMarket != "" or $sourceMarket != "") {
        $raw .= '<ns0:Profiles>';
        $RPH = 1;
        for ($z = 0; $z < $adults; $z ++) {
            $raw .= '<ns0:ProfileInfo><ns0:Profile RPH="' . $RPH . '"><ns0:Customer>';
            if ($residenceMarket != "") {
                $raw .= '<ns0:Address><ns0:CountryName Code="' . $residenceMarket . '"/></ns0:Address>';
            }
            if ($sourceMarket != "") {
                $raw .= '<ns0:CitizenCountryName Code="' . $sourceMarket . '"/>';
            }
            $raw .= '</ns0:Customer></ns0:Profile></ns0:ProfileInfo>';
            $RPH = $RPH + 1;
        }
        if ($children > 0) {
            for ($z = 0; $z < $children; $z ++) {
                $raw .= '<ns0:ProfileInfo><ns0:Profile RPH="' . $RPH . '"><ns0:Customer>';
                if ($residenceMarket != "") {
                    $raw .= '<ns0:Address><ns0:CountryName Code="' . $residenceMarket . '"/></ns0:Address>';
                }
                if ($sourceMarket != "") {
                    $raw .= '<ns0:CitizenCountryName Code="' . $sourceMarket . '"/>';
                }
                $raw .= '</ns0:Customer></ns0:Profile></ns0:ProfileInfo>';
                $RPH = $RPH + 1;
            }
        }
        $raw .= '</ns0:Profiles>';
    }
    $raw .= '<ns0:RoomStayCandidates><ns0:RoomStayCandidate><ns0:GuestCounts>';
    $RPH = 1;
    for ($z = 0; $z < $adults; $z ++) {
        $raw .= '<ns0:GuestCount Count="1" Age="30" ResGuestRPH="' . $RPH . '" />';
        $RPH = $RPH + 1;
    }
    if ($children > 0) {
        for ($z = 0; $z < $children; $z ++) {
            $raw .= '<ns0:GuestCount Count="1" Age="' . $children_ages[$z] . '" ResGuestRPH="' . $RPH . '" />';
            $RPH = $RPH + 1;
        }
    }
    $raw .= '</ns0:GuestCounts></ns0:RoomStayCandidate></ns0:RoomStayCandidates><ns0:HotelSearchCriteria><ns0:Criterion><ns0:HotelRef HotelCode="' . $hotellist . '"/></ns0:Criterion></ns0:HotelSearchCriteria><ns0:TPA_Extensions><ns0:Providers><ns0:Provider Provider="GSI"><ns0:Credentials><ns0:Credential CredentialCode="' . $TravelPlanuser . '" CredentialName="AccountCode"/><ns0:Credential CredentialCode="' . $TravelPlanpassword . '" CredentialName="Password"/><ns0:Credential CredentialCode="' . $TravelPlanSystem . '" CredentialName="System"/><ns0:Credential CredentialCode="' . $TravelPlanSalesChannel . '" CredentialName="SalesChannel"/><ns0:Credential CredentialCode="' . $TravelPlanlanguage . '" CredentialName="Language"/><ns0:Credential CredentialCode="' . $TravelPlanConnectionString . '" CredentialName="ConnectionString"/></ns0:Credentials><ns0:ProviderAreas><ns0:Area TypeCode="Country" AreaCode="' . $x50_0 . '"/><ns0:Area TypeCode="Province" AreaCode="' . $province . '"/></ns0:ProviderAreas></ns0:Provider></ns0:Providers><ns0:ProviderTokens><ns0:Token TokenName="ResponseMode" TokenCode="4"/></ns0:ProviderTokens></ns0:TPA_Extensions></ns0:AvailRequestSegment></ns0:AvailRequestSegments></ns0:OTA_HotelAvailRQ></soapenv:Body></soapenv:Envelope>';
    // error_log("\r\nTravelplan Globalia Request: - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($TravelPlanTimeout == 0) {
        $TravelPlanTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept-Encoding: gzip, deflate",
        "Accept: application/xml",
        "Content-type: application/xml",
        "Content-length: " . strlen($raw)
    ));
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $TravelPlanserviceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $TravelPlanTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $TravelPlanTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'travelplan';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>