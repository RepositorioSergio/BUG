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
    <GetBookingVoucher xmlns='http://www.rts.co.kr/'>
        <BookingVoucher>
            <BookingCode>BUEF212724</BookingCode>
        </BookingVoucher>
    </GetBookingVoucher>
</soap:Body>
</soap:Envelope>";

$soapUrl = 'http://devwsar.rts.net/WebServiceProjects/NetWebService/WsBookings.asmx';

$headers = array(
    "Content-type: text/xml;",
    "SOAPAction: http://www.rts.co.kr/GetBookingVoucher",
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
$GetBookingVoucherResponse = $Body->item(0)->getElementsByTagName("GetBookingVoucherResponse");
// GetBookingVoucherResult
$GetBookingVoucherResult = $GetBookingVoucherResponse->item(0)->getElementsByTagName("GetBookingVoucherResult");
$GetBookingVoucherResponse = $GetBookingVoucherResult->item(0)->getElementsByTagName("GetBookingVoucherResponse");
$GetBookingVoucherResult = $GetBookingVoucherResponse->item(0)->getElementsByTagName("GetBookingVoucherResult");
$BookingVoucherList = $GetBookingVoucherResult->item(0)->getElementsByTagName("BookingVoucherList");

$node = $BookingVoucherList->item(0)->getElementsByTagName("BookingVoucherInfo");
$BookingCode = $node->item(0)->getElementsByTagName("BookingCode");
if ($BookingCode->length > 0) {
    $BookingCode = $BookingCode->item(0)->nodeValue;
} else {
    $BookingCode = "";
}
echo $return;
echo "BookingCode: " . $BookingCode;
echo $return;
$ItemTypeCode = $node->item(0)->getElementsByTagName("ItemTypeCode");
if ($ItemTypeCode->length > 0) {
    $ItemTypeCode = $ItemTypeCode->item(0)->nodeValue;
} else {
    $ItemTypeCode = "";
}
$BookingReference = $node->item(0)->getElementsByTagName("BookingReference");
if ($BookingReference->length > 0) {
    $BookingReference = $BookingReference->item(0)->nodeValue;
} else {
    $BookingReference = "";
}
$CountryCode = $node->item(0)->getElementsByTagName("CountryCode");
if ($CountryCode->length > 0) {
    $CountryCode = $CountryCode->item(0)->nodeValue;
} else {
    $CountryCode = "";
}
$CountryEname = $node->item(0)->getElementsByTagName("CountryEname");
if ($CountryEname->length > 0) {
    $CountryEname = $CountryEname->item(0)->nodeValue;
} else {
    $CountryEname = "";
}
$CityCode = $node->item(0)->getElementsByTagName("CityCode");
if ($CityCode->length > 0) {
    $CityCode = $CityCode->item(0)->nodeValue;
} else {
    $CityCode = "";
}
$CityEname = $node->item(0)->getElementsByTagName("CityEname");
if ($CityEname->length > 0) {
    $CityEname = $CityEname->item(0)->nodeValue;
} else {
    $CityEname = "";
}
$ItemCode = $node->item(0)->getElementsByTagName("ItemCode");
if ($ItemCode->length > 0) {
    $ItemCode = $ItemCode->item(0)->nodeValue;
} else {
    $ItemCode = "";
}
$ItemName = $node->item(0)->getElementsByTagName("ItemName");
if ($ItemName->length > 0) {
    $ItemName = $ItemName->item(0)->nodeValue;
} else {
    $ItemName = "";
}
$Address = $node->item(0)->getElementsByTagName("Address");
if ($Address->length > 0) {
    $Address = $Address->item(0)->nodeValue;
} else {
    $Address = "";
}
$PhoneNo = $node->item(0)->getElementsByTagName("PhoneNo");
if ($PhoneNo->length > 0) {
    $PhoneNo = $PhoneNo->item(0)->nodeValue;
} else {
    $PhoneNo = "";
}
$FaxNo = $node->item(0)->getElementsByTagName("FaxNo");
if ($FaxNo->length > 0) {
    $FaxNo = $FaxNo->item(0)->nodeValue;
} else {
    $FaxNo = "";
}
$Email = $node->item(0)->getElementsByTagName("Email");
if ($Email->length > 0) {
    $Email = $Email->item(0)->nodeValue;
} else {
    $Email = "";
}
$CheckInDate = $node->item(0)->getElementsByTagName("CheckInDate");
if ($CheckInDate->length > 0) {
    $CheckInDate = $CheckInDate->item(0)->nodeValue;
} else {
    $CheckInDate = "";
}
$CheckInWeekday = $node->item(0)->getElementsByTagName("CheckInWeekday");
if ($CheckInWeekday->length > 0) {
    $CheckInWeekday = $CheckInWeekday->item(0)->nodeValue;
} else {
    $CheckInWeekday = "";
}
$CheckOutDate = $node->item(0)->getElementsByTagName("CheckOutDate");
if ($CheckOutDate->length > 0) {
    $CheckOutDate = $CheckOutDate->item(0)->nodeValue;
} else {
    $CheckOutDate = "";
}
$CheckOutWeekday = $node->item(0)->getElementsByTagName("CheckOutWeekday");
if ($CheckOutWeekday->length > 0) {
    $CheckOutWeekday = $CheckOutWeekday->item(0)->nodeValue;
} else {
    $CheckOutWeekday = "";
}
$Duration = $node->item(0)->getElementsByTagName("Duration");
if ($Duration->length > 0) {
    $Duration = $Duration->item(0)->nodeValue;
} else {
    $Duration = "";
}
$FreeBreakfast = $node->item(0)->getElementsByTagName("FreeBreakfast");
if ($FreeBreakfast->length > 0) {
    $FreeBreakfast = $FreeBreakfast->item(0)->nodeValue;
} else {
    $FreeBreakfast = "";
}
$AddBreakfast = $node->item(0)->getElementsByTagName("AddBreakfast");
if ($AddBreakfast->length > 0) {
    $AddBreakfast = $AddBreakfast->item(0)->nodeValue;
} else {
    $AddBreakfast = "";
}
$VoucherReferenceName = $node->item(0)->getElementsByTagName("VoucherReferenceName");
if ($VoucherReferenceName->length > 0) {
    $VoucherReferenceName = $VoucherReferenceName->item(0)->nodeValue;
} else {
    $VoucherReferenceName = "";
}
$SupplierName = $node->item(0)->getElementsByTagName("SupplierName");
if ($SupplierName->length > 0) {
    $SupplierName = $SupplierName->item(0)->nodeValue;
} else {
    $SupplierName = "";
}
$DisplayComment1 = $node->item(0)->getElementsByTagName("DisplayComment1");
if ($DisplayComment1->length > 0) {
    $DisplayComment1 = $DisplayComment1->item(0)->nodeValue;
} else {
    $DisplayComment1 = "";
}
$DisplayComment2 = $node->item(0)->getElementsByTagName("DisplayComment2");
if ($DisplayComment2->length > 0) {
    $DisplayComment2 = $DisplayComment2->item(0)->nodeValue;
} else {
    $DisplayComment2 = "";
}
$DisplayComment3 = $node->item(0)->getElementsByTagName("DisplayComment3");
if ($DisplayComment3->length > 0) {
    $DisplayComment3 = $DisplayComment3->item(0)->nodeValue;
} else {
    $DisplayComment3 = "";
}
$DisplayComment4 = $node->item(0)->getElementsByTagName("DisplayComment4");
if ($DisplayComment4->length > 0) {
    $DisplayComment4 = $DisplayComment4->item(0)->nodeValue;
} else {
    $DisplayComment4 = "";
}
$DisplayComment5 = $node->item(0)->getElementsByTagName("DisplayComment5");
if ($DisplayComment5->length > 0) {
    $DisplayComment5 = $DisplayComment5->item(0)->nodeValue;
} else {
    $DisplayComment5 = "";
}
$IssuedDate = $node->item(0)->getElementsByTagName("IssuedDate");
if ($IssuedDate->length > 0) {
    $IssuedDate = $IssuedDate->item(0)->nodeValue;
} else {
    $IssuedDate = "";
}
$IssuedWeekday = $node->item(0)->getElementsByTagName("IssuedWeekday");
if ($IssuedWeekday->length > 0) {
    $IssuedWeekday = $IssuedWeekday->item(0)->nodeValue;
} else {
    $IssuedWeekday = "";
}
$PartnerImagePath = $node->item(0)->getElementsByTagName("PartnerImagePath");
if ($PartnerImagePath->length > 0) {
    $PartnerImagePath = $PartnerImagePath->item(0)->nodeValue;
} else {
    $PartnerImagePath = "";
}
$MapPointValue = $node->item(0)->getElementsByTagName("MapPointValue");
if ($MapPointValue->length > 0) {
    $MapPointValue = $MapPointValue->item(0)->nodeValue;
} else {
    $MapPointValue = "";
}
$RoomTypeName = $node->item(0)->getElementsByTagName("RoomTypeName");
if ($RoomTypeName->length > 0) {
    $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
} else {
    $RoomTypeName = "";
}
$Latitude = $node->item(0)->getElementsByTagName("Latitude");
if ($Latitude->length > 0) {
    $Latitude = $Latitude->item(0)->nodeValue;
} else {
    $Latitude = "";
}
$Longitude = $node->item(0)->getElementsByTagName("Longitude");
if ($Longitude->length > 0) {
    $Longitude = $Longitude->item(0)->nodeValue;
} else {
    $Longitude = "";
}
$SpecialRemarks = $node->item(0)->getElementsByTagName("SpecialRemarks");
if ($SpecialRemarks->length > 0) {
    $SpecialRemarks = $SpecialRemarks->item(0)->nodeValue;
} else {
    $SpecialRemarks = "";
}

$RoomsAndGuestList = $node->item(0)->getElementsByTagName("RoomsAndGuestList");
if ($RoomsAndGuestList->length > 0) {
    $RoomsAndGuestInfo = $RoomsAndGuestList->item(0)->getElementsByTagName("RoomsAndGuestInfo");
    if ($RoomsAndGuestInfo->length > 0) {
        $RoomCount = $RoomsAndGuestInfo->item(0)->getElementsByTagName("RoomCount");
        if ($RoomCount->length > 0) {
            $RoomCount = $RoomCount->item(0)->nodeValue;
        } else {
            $RoomCount = "";
        }
        $RoomTypeName = $RoomsAndGuestInfo->item(0)->getElementsByTagName("RoomTypeName");
        if ($RoomTypeName->length > 0) {
            $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
        } else {
            $RoomTypeName = "";
        }
        $PassengerName = $RoomsAndGuestInfo->item(0)->getElementsByTagName("PassengerName");
        if ($PassengerName->length > 0) {
            $PassengerName = $PassengerName->item(0)->nodeValue;
        } else {
            $PassengerName = "";
        }
        $VoucherComment = $RoomsAndGuestInfo->item(0)->getElementsByTagName("VoucherComment");
        if ($VoucherComment->length > 0) {
            $VoucherComment = $VoucherComment->item(0)->nodeValue;
        } else {
            $VoucherComment = "";
        }
        $ReservedCode = $RoomsAndGuestInfo->item(0)->getElementsByTagName("ReservedCode");
        if ($ReservedCode->length > 0) {
            $ReservedCode = $ReservedCode->item(0)->nodeValue;
        } else {
            $ReservedCode = "";
        }
    }
}


$sql = new Sql($db);
$insert = $sql->insert();
$insert->into('bookingvoucher');
$insert->values(array(
    'datetime_created' => time(),
    'datetime_updated' => 0,
    'BookingCode' => $BookingCode,
    'ItemTypeCode' => $ItemTypeCode,
    'BookingReference' => $BookingReference,
    'CountryCode' => $CountryCode,
    'CountryEname' => $CountryEname,
    'CityCode' => $CityCode,
    'CityEname' => $CityEname,
    'ItemCode' => $ItemCode,
    'ItemName' => $ItemName,
    'Address' => $Address,
    'PhoneNo' => $PhoneNo,
    'FaxNo' => $FaxNo,
    'Email' => $Email,
    'CheckInDate' => $CheckInDate,
    'CheckInWeekday' => $CheckInWeekday,
    'CheckOutDate' => $CheckOutDate,
    'CheckOutWeekday' => $CheckOutWeekday,
    'Duration' => $Duration,
    'FreeBreakfast' => $FreeBreakfast,
    'AddBreakfast' => $AddBreakfast,
    'VoucherReferenceName' => $VoucherReferenceName,
    'SupplierName' => $SupplierName,
    'DisplayComment1' => $DisplayComment1,
    'DisplayComment2' => $DisplayComment2,
    'DisplayComment3' => $DisplayComment3,
    'DisplayComment4' => $DisplayComment4,
    'DisplayComment5' => $DisplayComment5,
    'IssuedDate' => $IssuedDate,
    'IssuedWeekday' => $IssuedWeekday,
    'PartnerImagePath' => $PartnerImagePath,
    'MapPointValue' => $MapPointValue,
    'RoomTypeName' => $RoomTypeName,
    'Latitude' => $Latitude,
    'Longitude' => $Longitude,
    'SpecialRemarks' => $SpecialRemarks,
    'RoomCount' => $RoomCount,
    'RoomTypeName' => $RoomTypeName,
    'PassengerName' => $PassengerName,
    'VoucherComment' => $VoucherComment,
    'ReservedCode' => $ReservedCode
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