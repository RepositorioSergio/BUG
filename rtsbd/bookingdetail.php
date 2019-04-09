<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}

$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
// Start
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablerts' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_rts = $affiliate_id;
} else {
    $affiliate_id_rts = 0;
}
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
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.rts.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$raw = "<?xml version='1.0' encoding='utf-8'?>
<soap:Envelope xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'
xmlns:xsd='http://www.w3.org/2001/XMLSchema' xmlns:soap='http://schemas.xmlsoap.org/soap/envelope/'>
<soap:Header>
    <BaseInfo xmlns='http://www.rts.co.kr/'>
        <SiteCode>" . $rtsSiteCode . "</SiteCode>
        <Password>" . $rtsPassword . "</Password>
        <RequestType>NetPartner</RequestType>
    </BaseInfo>
</soap:Header>
<soap:Body>
    <GetBookingDetail xmlns='http://www.rts.co.kr/'>
        <BookingDetail>
            <LanguageCode>AR</LanguageCode>
            <BookingCode>BUEF212724</BookingCode>
        </BookingDetail>
    </GetBookingDetail>
</soap:Body>
</soap:Envelope>";

$soapUrl = 'http://devwsar.rts.net/WebServiceProjects/NetWebService/WsBookings.asmx';

$headers = array(
    "Content-type: text/xml;",
    "SOAPAction: http://www.rts.co.kr/GetBookingDetail",
    "Content-length: " . strlen($raw)
);

$url = $soapUrl;

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

/*
 * echo $return;
 * echo $response;
 * echo $return;
 */
$response = str_replace('&lt;', '<', $response);
$response = str_replace('&gt;', '>', $response);

echo $response;
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.rts.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$GetHotelSearchListForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetHotelSearchListForCustomerCountResponse");
// GetHotelSearchListForCustomerCountResult
$GetHotelSearchListForCustomerCountResult = $GetHotelSearchListForCustomerCountResponse->item(0)->getElementsByTagName("GetHotelSearchListForCustomerCountResult");
$GetHotelSearchListResponse = $GetHotelSearchListForCustomerCountResult->item(0)->getElementsByTagName("GetHotelSearchListResponse");

$node = $GetHotelSearchListResponse->item(0)->getElementsByTagName("GetHotelSearchListResult");
$LanguageCode = $node->item(0)->getElementsByTagName("LanguageCode");
if ($LanguageCode->length > 0) {
    $LanguageCode = $LanguageCode->item(0)->nodeValue;
} else {
    $LanguageCode = "";
}
echo $return;
echo "LanguageCode: " . $LanguageCode;
echo $return;
$LanguageName = $node->item(0)->getElementsByTagName("LanguageName");
if ($LanguageName->length > 0) {
    $LanguageName = $LanguageName->item(0)->nodeValue;
} else {
    $LanguageName = "";
}


$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('hoteis');
$insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'LanguageCode' => $LanguageCode,
    'LanguageName' => $LanguageName,
    'NationalityCode' => $NationalityCode,
    'NationalityName' => $NationalityName,
    'ContinentCode' => $ContinentCode,
    'CityCode' => $CityCode,
    'CityEname' => $CityEname,
    'CityName' => $CityName,
    'CountryCode' => $CountryCode,
    'CountryEname' => $CountryEname,
    'CountryName' => $CountryName,
    'StateCode' => $StateCode,
    'StateEname' => $StateEname,
    'StateName' => $StateName,
    'CheckInDate' => $CheckInDate,
    'CheckInWeekday' => $CheckInWeekday,
    'CheckOutDate' => $CheckOutDate,
    'CheckOutWeekday' => $CheckOutWeekday,
    'Duration' => $Duration,
    'CheckInLeftDays' => $CheckInLeftDays,
    'ItemName' => $ItemName,
    'ItemCode' => $ItemCode,
    'ItemNo' => $ItemNo,
    'StarRating' => $StarRating,
    'LocationCode' => $LocationCode,
    'AvailableHotelOnly' => $AvailableHotelOnly,
    'RecommendHotelOnly' => $RecommendHotelOnly,
    'TotalResultCount' => $TotalResultCount,
    'ExchangeConvertDate' => $ExchangeConvertDate,
    'SellingCurrencyCode' => $SellingCurrencyCode,
    'ClientCurrencyCode' => $ClientCurrencyCode,
    'SellingConvertRate' => $SellingConvertRate,
    'CityEventList' => $CityEventList
), $insert::VALUES_MERGE);
$statement = $sql->prepareStatementForSqlObject($insert);
$results = $statement->execute();
$db->getDriver()
    ->getConnection()
    ->disconnect();


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>