<?php
// Jumbo Tours Group
error_log("\r\nStart Jumbo Tours Group Hotel Parallel\r\n", 3, "/srv/www/htdocs/error_log");
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
$db = new \Laminas\Db\Adapter\Adapter($config);
unset($tmp);
$sfilter = array();
$failed = false;
$hotellist = "";
$sql = "select sid from xmlhotels_mjtg where hid=" . $hid;
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
        $hotellist .= '<establishmentId>' . $row->sid . '</establishmentId>';
    }
}
if ($hotellist != "") {
    /* if (file_exists("src/App/language/" . $lang . ".mo")) {
        $translator->addTranslationFile("gettext", "src/App/language/" . $lang . ".mo");
    } */
    $affiliate_id = 0;
    $branch_filter = "";
    $sql = "select value from settings where name='enablejumbotoursgroupHotels' and affiliate_id=$affiliate_id" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $affiliate_id_jtg = $affiliate_id;
    } else {
        $affiliate_id_jtg = 0;
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
        $sql = "select value from settings where name='jtgDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_jtg";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings['value'];
        }
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelslogin' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $jumbotoursgroupHotelslogin = $row_settings['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelspassword' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $jumbotoursgroupHotelspassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsagencycode' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $jumbotoursgroupHotelsagencycode = $row_settings['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsbrandcode' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $jumbotoursgroupHotelsbrandcode = $row_settings['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsServiceURL' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsServiceURL = $row['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsMarkup' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $jumbotoursgroupHotelsMarkup = (double) $row_settings['value'];
    } else {
        $jumbotoursgroupHotelsMarkup = 0;
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelspointofsale' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelspointofsale = $row['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsParallelSearch' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsParallelSearch = $row['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsCompany' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsCompany = $row['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsSearchSortorder' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsSearchSortorder = $row['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsb2cMarkup' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsb2cMarkup = $row['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsTimeout' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsTimeout = (int)$row['value'];
    } else {
        $jumbotoursgroupHotelsTimeout = 0;
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsaffiliates_id' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsaffiliates_id = $row['value'];
    }
    $sql = "select value from settings where name='jumbotoursgroupHotelsbranches_id' and affiliate_id=$affiliate_id_jtg";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result->valid()) {
        $row = $result->current();
        $jumbotoursgroupHotelsbranches_id = $row['value'];
    }
    if ($failed == false) {
        if ($jumbotoursgroupHotelsServiceURL != "" and $jumbotoursgroupHotelslogin != "" and $jumbotoursgroupHotelspassword != "") {
            $nC = 0;
            $multiParallelSession = array();
            $numberOfRooms = 1;
            $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/hotel/types">
            <soapenv:Header/>
            <soapenv:Body>
            <typ:availableHotelsByMultiQueryV22>
                <AvailableHotelsByMultiQueryRQV22_1>
                    <agencyCode>' . $jumbotoursgroupHotelsagencycode . '</agencyCode>
                    <brandCode>' . $jumbotoursgroupHotelsbrandcode . '</brandCode>
                    <pointOfSaleId>' . $jumbotoursgroupHotelspointofsale . '</pointOfSaleId>
                    <checkin>' . strftime("%Y-%m-%d", $from) . 'T10:00:00.000Z</checkin>
                    <checkout>' . strftime("%Y-%m-%d", $to) . 'T10:00:00.000Z</checkout>
                    <fromPrice>0</fromPrice>
                    <fromRow>0</fromRow>
                    <includeEstablishmentData>false</includeEstablishmentData>
                    <language>en</language>
                    <maxRoomCombinationsPerEstablishment>30</maxRoomCombinationsPerEstablishment>
                    <numRows>100</numRows>';
                    $raw = $raw . '<occupancies>
                        <adults>' . $adults . '</adults>
                        <children>' . $children . '</children>';
                    if ($selectedChildren[$r] > 0) {
                        for ($z=0; $z < $children; $z++) { 
                            $raw = $raw . '<childrenAges>' . $children_ages[$z] . '</childrenAges>';
                        }
                    }
                    $raw = $raw . '<numberOfRooms>' . $numberOfRooms . '</numberOfRooms>
                    </occupancies>';
            $raw .= '<onlyOnline>true</onlyOnline>
                    <orderBy/>
                    <productCode/>
                    <toPrice>999999</toPrice>
                    ' . $hotellist . '
                    <extendedLogin>
                        <channel>B2C</channel>
                        <loginCountry>' . $sourceMarket . '</loginCountry>
                        <mainNationality>' . $sourceMarket . '</mainNationality>
                    </extendedLogin>
                    <paxNationalities>
                        <nationality/>
                    </paxNationalities>
                </AvailableHotelsByMultiQueryRQV22_1>
            </typ:availableHotelsByMultiQueryV22>
            </soapenv:Body>
            </soapenv:Envelope>';
            if ($jumbotoursgroupHotelsTimeout == 0) {
                $jumbotoursgroupHotelsTimeout = 120;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-type: text/xml",
                "Accept-Encoding: gzip, deflate",
                "Content-length: " . strlen($raw)
            ));
            curl_setopt($ch, CURLINFO_HEADER_OUT, true);
            curl_setopt($ch, CURLOPT_URL, $jumbotoursgroupHotelsServiceURL . 'public/v1_0rc1/hotelBookingHandler');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $jumbotoursgroupHotelsTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT, $jumbotoursgroupHotelsTimeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($multiParallel, $ch);
            $requestsParallel[$nC] = 'jumbotoursgroup';
            $channelsParallel[$nC] = $ch;
            $channelsParallelRequest[$nC] = $raw;
            $nC ++;
        }
    }
}
?>