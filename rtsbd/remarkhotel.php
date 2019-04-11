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
echo $return;
echo $rtsPassword;
echo $return;
$sql = "select value from settings where name='rtsSiteCode' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsSiteCode = $row_settings['value'];
}
echo $return;
echo $rtsSiteCode;
echo $return;
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

$raw = '<?xml version="1.0"?>
<soap:Envelope xmlns:rts="http://www.rts.co.kr/" xmlns:soap="http://www.w3.org/2003/05/soap-envelope">
<soap:Header>
<BaseInfo>
<SiteCode>' . $rtsSiteCode . '</SiteCode>
<Password>' . $rtsPassword . '</Password>
<RequestType>NetPartner</RequestType>
</BaseInfo>
</soap:Header>
<soap:Body>
    <GetRemarkHotelInformationForCustomerCount>
        <HotelSearchListNetGuestCount>
            <LanguageCode>AR</LanguageCode>
            <TravelerNationality>AR</TravelerNationality>
            <CityCode>HKG</CityCode>
            <CheckInDate>2019-06-14</CheckInDate>
            <CheckOutDate>2019-06-17</CheckOutDate>
            <StarRating>0</StarRating>
            <LocationCode/>
            <SupplierCompCode/>
            <AvailableHotelOnly>true</AvailableHotelOnly>
            <RecommendHotelOnly>false</RecommendHotelOnly>
            <ClientCurrencyCode>USD</ClientCurrencyCode>
            <ItemName/>
            <SellerMarkup>*1</SellerMarkup>
            <CompareYn>false</CompareYn>
            <SortType/>
            <ItemCodeList>
                <ItemCodeInfo>
                    <ItemCode>HKG0001</ItemCode>
                    <ItemNo>0</ItemNo>
                </ItemCodeInfo>
            </ItemCodeList>
            <GuestList>
                <GuestsInfo>
                    <AdultCount>2</AdultCount>
                    <ChildCount>0</ChildCount>
                    <RoomCount>1</RoomCount>
                    <ChildAge1>0</ChildAge1>
                    <ChildAge2>0</ChildAge2>
                </GuestsInfo>
                <GuestsInfo>
                    <AdultCount>2</AdultCount>
                    <ChildCount>1</ChildCount>
                    <RoomCount>1</RoomCount>
                    <ChildAge1>5</ChildAge1>
                    <ChildAge2>0</ChildAge2>
                </GuestsInfo>
            </GuestList>
        </HotelSearchListNetGuestCount>
        <RoomTypeCode>HK|007:BIS:267405:SGLySTzNRFvBARvDISzROz:N:524539|STANDARD SINGLE|SB*1#|HKG||EZZ.ZC|USD|JDDRBFGK|~None</RoomTypeCode>
    </GetRemarkHotelInformationForCustomerCount>
</soap:Body>
</soap:Envelope>';



$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rts="http://www.rts.co.kr/">
   <soapenv:Header>
      <rts:BaseInfo>
         <!--Optional:-->
         <rts:SiteCode>CTM1X-00</rts:SiteCode>
         <!--Optional:-->
         <rts:Password>test1234</rts:Password>
         <!--Optional:-->
         <rts:RequestType>NetPartner</rts:RequestType>
      </rts:BaseInfo>
   </soapenv:Header>
   <soapenv:Body>
      <rts:GetRemarkHotelInformationForCustomerCount>
        <rts:HotelSearchListNetGuestCount>
            <!--Optional:-->
            <rts:LanguageCode>AR</rts:LanguageCode>
            <!--Optional:-->
            <rts:TravelerNationality>AR</rts:TravelerNationality>
            <!--Optional:-->
            <rts:CityCode>MIA</rts:CityCode>
            <!--Optional:-->
            <rts:CheckInDate>2019-09-17</rts:CheckInDate>
            <!--Optional:-->
            <rts:CheckOutDate>2019-09-24</rts:CheckOutDate>
            <!--Optional:-->
            <rts:StarRating>0</rts:StarRating>
            <!--Optional:-->
            <rts:LocationCode></rts:LocationCode>
            <!--Optional:-->
            <rts:SupplierCompCode></rts:SupplierCompCode>
            <rts:AvailableHotelOnly>true</rts:AvailableHotelOnly>
            <rts:RecommendHotelOnly>false</rts:RecommendHotelOnly>
            <!--Optional:-->
            <rts:ClientCurrencyCode>USD</rts:ClientCurrencyCode>
            <!--Optional:-->
            <rts:ItemName></rts:ItemName>
            <!--Optional:-->
            <rts:SellerMarkup>*1</rts:SellerMarkup>
            <rts:CompareYn>false</rts:CompareYn>
            <!--Optional:-->
            <rts:SortType></rts:SortType>
            <!--Optional:-->
            <rts:ItemCodeList>
               <!--Zero or more repetitions:-->
               <rts:ItemCodeInfo>
                  <!--Optional:-->
                  <rts:ItemCode>MIA0406</rts:ItemCode>
                  <rts:ItemNo>8</rts:ItemNo>
               </rts:ItemCodeInfo>
            </rts:ItemCodeList>
            <!--Optional:-->
            <rts:GuestList>
               <!--Zero or more repetitions:-->
               <rts:GuestsInfo>
                  <rts:AdultCount>1</rts:AdultCount>
                  <rts:ChildCount>0</rts:ChildCount>
                  <rts:RoomCount>1</rts:RoomCount>
                  <rts:ChildAge1>0</rts:ChildAge1>
                  <rts:ChildAge2>0</rts:ChildAge2>
               </rts:GuestsInfo>			   
            </rts:GuestList>
         </rts:HotelSearchListNetGuestCount>
         <!--Optional:-->
         <rts:RoomTypeCode>20190917|20190924|W|235|128528|DBL.GR|ID_B2B_26|BB|DRBL|1~1~0||N@02~null~1A112AD092274DE155499749478802PAAR0000139011300100823d9b9:CEIJ.GB:double guest room:|ZMMJRBBZG|~BED AND BREAKFAST</rts:RoomTypeCode>
      </rts:GetRemarkHotelInformationForCustomerCount>
   </soapenv:Body>
</soapenv:Envelope>';

$soapUrl = 'http://devwsar.rts.net/WebServiceProjects/NetWebService/WsHotelProducts.asmx';

$headers = array(
    "Content-type: text/xml",
    "SOAPAction: http://www.rts.co.kr/GetRemarkHotelInformationForCustomerCount",
    "Content-length: " . strlen($raw)
);

echo $raw;

$url = $soapUrl;

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
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

echo '<xmp>';
var_dump($response);
echo '</xmp>';

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
$GetRemarkHotelInformationForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetRemarkHotelInformationForCustomerCountResponse");
// GetRemarkHotelInformationForCustomerCountResult
$GetRemarkHotelInformationForCustomerCountResult = $GetRemarkHotelInformationForCustomerCountResponse->item(0)->getElementsByTagName("GetRemarkHotelInformationForCustomerCountResult");

// RemarkHotelInformation
$RemarkHotelInformation = $GetRemarkHotelInformationForCustomerCountResult->item(0)->getElementsByTagName("RemarkHotelInformation");

$ItemCode = $RemarkHotelInformation->item(0)->getElementsByTagName("ItemCode");
if ($ItemCode->length > 0) {
    $ItemCode = $ItemCode->item(0)->nodeValue;
} else {
    $ItemCode = "";
}
$ItemNo = $RemarkHotelInformation->item(0)->getElementsByTagName("ItemNo");
if ($ItemNo->length > 0) {
    $ItemNo = $ItemNo->item(0)->nodeValue;
} else {
    $ItemNo = "";
}
$RoomTypeCode = $RemarkHotelInformation->item(0)->getElementsByTagName("RoomTypeCode");
if ($RoomTypeCode->length > 0) {
    $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
} else {
    $RoomTypeCode = "";
}

$Remarks = $RemarkHotelInformation->item(0)->getElementsByTagName("Remarks");
$Remark1 = $Remarks->item(0)->getElementsByTagName("Remark1");
if ($Remark1->length > 0) {
    $Remark1 = $Remark1->item(0)->nodeValue;
} else {
    $Remark1 = "";
}
$Remark2 = $Remarks->item(0)->getElementsByTagName("Remark2");
if ($Remark2->length > 0) {
    $Remark2 = $Remark2->item(0)->nodeValue;
} else {
    $Remark2 = "";
}

$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('remarkhotel');
$insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'ItemCode' => $ItemCode,
    'ItemNo' => $ItemNo,
    'RoomTypeCode' => $RoomTypeCode,
    'Remark1' => $Remark1,
    'Remark2' => $Remark2
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
