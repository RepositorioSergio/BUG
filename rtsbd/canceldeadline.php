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
   <rts:GetCancelDeadlineForCustomerCount>
      <rts:GetCancelDeadline>
         <!--Optional:-->
         <rts:ItemCode>SAO0037</rts:ItemCode>
         <rts:ItemNo>16</rts:ItemNo>
         <!--Optional:-->
         <rts:RoomTypeCode>20200416|20200419|W|258|47369|SUI.SU|GAR|BB||1~1~0||N@04~~246c3~-513337453~N~~927FE06EFE78440157909834038802PAAR00000580026000508246c3^20200416|20200419|W|258|47369|SUI.SU|GAR|BB||1~1~1|6|N@04~~246c3~-1117434062~N~~927FE06EFE78440157909834038802PAAR00000580026000508246c3:DJB.EA:suite single twin superior:258|17368|431:A1#B0#C0#D0#E0|DHBRGPB|~Breakfast</rts:RoomTypeCode>
         <!--Optional:-->
         <rts:CheckInDate>2020-04-16</rts:CheckInDate>
         <!--Optional:-->
         <rts:CheckOutDate>2020-04-19</rts:CheckOutDate>
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
            <rts:GuestsInfo>
               <rts:AdultCount>1</rts:AdultCount>
               <rts:ChildCount>1</rts:ChildCount>
               <rts:RoomCount>1</rts:RoomCount>
               <rts:ChildAge1>6</rts:ChildAge1>
               <rts:ChildAge2>0</rts:ChildAge2>
            </rts:GuestsInfo>
         </rts:GuestList>
         <!--Optional:-->
         <rts:LanguageCode>AR</rts:LanguageCode>
         <!--Optional:-->
         <rts:TravelerNationality>AR</rts:TravelerNationality>
      </rts:GetCancelDeadline>
   </rts:GetCancelDeadlineForCustomerCount>
</soapenv:Body>
</soapenv:Envelope>';

$soapUrl = 'http://devwsar.rts.net/WebServiceProjects/NetWebService/WsBookings.asmx';

$headers = array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "SOAPAction: http://www.rts.co.kr/GetCancelDeadlineForCustomerCount",
    "Content-length: " . strlen($raw)
);

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
$GetCancelDeadlineForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetCancelDeadlineForCustomerCountResponse");
// GetCancelDeadlineForCustomerCountResult
$GetCancelDeadlineForCustomerCountResult = $GetCancelDeadlineForCustomerCountResponse->item(0)->getElementsByTagName("GetCancelDeadlineForCustomerCountResult");

// GetCancelDeadlineResponse
$GetCancelDeadlineResponse = $GetCancelDeadlineForCustomerCountResult->item(0)->getElementsByTagName("GetCancelDeadlineResponse");
$GetCancelDeadlineResult = $GetCancelDeadlineResponse->item(0)->getElementsByTagName("GetCancelDeadlineResult");

$CancelDeadlineDate = $GetCancelDeadlineResult->item(0)->getElementsByTagName("CancelDeadlineDate");
if ($CancelDeadlineDate->length > 0) {
    $CancelDeadlineDate = $CancelDeadlineDate->item(0)->nodeValue;
} else {
    $CancelDeadlineDate = "";
}
$TypeCode = $GetCancelDeadlineResult->item(0)->getElementsByTagName("TypeCode");
if ($TypeCode->length > 0) {
    $TypeCode = $TypeCode->item(0)->nodeValue;
} else {
    $TypeCode = "";
}

$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('cancelhotel');
$insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'CancelDeadlineDate' => $CancelDeadlineDate,
    'TypeCode' => $TypeCode
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