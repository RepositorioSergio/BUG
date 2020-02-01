<?php
error_log("\r\nTotalStay - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$totalstayshid = 0;
$count = 0;
$sql = "select sid from xmlhotels_mexclusivelyhotels where hid=" . $hid;
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
        $totalstayshid = $row->sid;
        $hotellist .= '<Resort><ResortID>' . $row->sid . '</ResortID></Resort>';
    }
}
if ($hotellist != "") {
    $affiliate_id_totalstay = 0;
    $sql = "select value from settings where name='totalstayuser' and affiliate_id=$affiliate_id_totalstay";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $totalstayuser = $row_settings['value'];
    }
    $sql = "select value from settings where name='totalstaypassword' and affiliate_id=$affiliate_id_totalstay";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $totalstaypassword = base64_decode($row_settings['value']);
    }
    $sql = "select value from settings where name='totalstayMarkup' and affiliate_id=$affiliate_id_totalstay";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $totalstayMarkup = (double) $row_settings['value'];
    } else {
        $totalstayMarkup = 0;
    }
    $sql = "select value from settings where name='totalstayserviceURL' and affiliate_id=$affiliate_id_totalstay";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $totalstayserviceURL = $row_settings['value'];
    }
    $sql = "select value from settings where name='totalstayTimeout' and affiliate_id=$affiliate_id_totalstay";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $totalstayTimeout = (int) $row_settings['value'];
    }
    $raw = 'Data=<SearchRequest><LoginDetails>
        <Login>' . $totalstayuser . '</Login>
        <Password>' . $totalstaypassword . '</Password>
        <CurrencyID>2</CurrencyID></LoginDetails>
    <SearchDetails>
        <ArrivalDate>' . strftime("%Y-%m-%d", $from) . '</ArrivalDate>
        <Duration>' . $noOfNights . '</Duration>
        <Resorts>' . $hotellist . '</Resorts>
        <MealBasisID>0</MealBasisID>
        <MinStarRating>0</MinStarRating>
        <RoomRequests>
            <RoomRequest>
                <Adults>' . $adults . '</Adults>
                <Children>' . $children . '</Children>
                <Infants>0</Infants>';
    if ($children > 0) {
        $raw .= '<ChildAges>';
        for ($z = 0; $z < $children; $z ++) {
            $raw .= '<ChildAge><Age>' . $children_ages[$z] . '</Age></ChildAge>';
        }
        $raw .= '</ChildAges>';
    }
    $raw .= '</RoomRequest></RoomRequests></SearchDetails></SearchRequest>';
    error_log("\r\nTotalStay RAW - $raw\r\n", 3, "/srv/www/htdocs/error_log");
    if ($totalstayTimeout == 0) {
        $totalstayTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $totalstayserviceURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $totalstayTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $totalstayTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'totalstay';
    $channelsParallel[$nC] = $ch;
    $channelsParallelRequest[$nC] = $raw;
    $nC ++;
}
?>