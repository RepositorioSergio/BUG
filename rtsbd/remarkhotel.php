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
$db = new \Zend\Db\Adapter\Adapter($config);

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
            <rts:CityCode>SEL</rts:CityCode>
            <!--Optional:-->
            <rts:CheckInDate>2020-07-01</rts:CheckInDate>
            <!--Optional:-->
            <rts:CheckOutDate>2020-07-03</rts:CheckOutDate>
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
                  <rts:ItemCode>SEL0001</rts:ItemCode>
                  <rts:ItemNo>30</rts:ItemNo>
               </rts:ItemCodeInfo>
            </rts:ItemCodeList>
            <!--Optional:-->
            <rts:GuestList>
               <!--Zero or more repetitions:-->
               <rts:GuestsInfo>
                  <rts:AdultCount>2</rts:AdultCount>
                  <rts:ChildCount>0</rts:ChildCount>
                  <rts:RoomCount>1</rts:RoomCount>
                  <rts:ChildAge1>0</rts:ChildAge1>
                  <rts:ChildAge2>0</rts:ChildAge2>
               </rts:GuestsInfo>			   
            </rts:GuestList>
         </rts:HotelSearchListNetGuestCount>
         <!--Optional:-->
         <rts:RoomTypeCode>20200701|20200703|W|435|128032|TWN.DX|FIT|RO||1~2~0||N@04~~20c77~-1751002900~N~~9EFDEF54851C4F6158037749818602PAAR0000028002600060820c77:BBJ.BC:twin deluxe::A0#B0#C0#D0#E0|HZGRDKBD|~None</rts:RoomTypeCode>
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

$response = str_replace('&lt;', '<', $response);
$response = str_replace('&gt;', '>', $response);

echo '<xmp>';
var_dump($response);
echo '</xmp>';
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