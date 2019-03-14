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
echo $return;
echo "AFFIL: " . $affiliate_id_rts;
echo $return;
$sql = "select value from settings where name='rtsID' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $rtsID = $row_settings['value'];
}
echo $return;
echo "ID: " . $rtsID;
echo $return;
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
echo $return;
echo $rtsRequestType;
echo $return;
$sql = "select value from settings where name='rtsServiceURL' and affiliate_id=$affiliate_id_rts";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $rtsServiceURL = $row['value'];
}
echo $return;
echo $rtsServiceURL;
echo $return;
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
        <SiteCode>" . $rtsSiteCode . "-00</SiteCode>
        <Password>" . $rtsPassword . "</Password>
        <RequestType>NetPartner</RequestType>
    </BaseInfo>
</soap:Header>
<soap:Body>
<GetHotelSearchListForCustomerCount xmlns='http://www.rts.co.kr/'>
    <HotelSearchListNetGuestCount>
        <LanguageCode>AR</LanguageCode>
        <TravelerNationality>AR</TravelerNationality>
        <CityCode>ROM</CityCode>
        <CheckInDate>2019-04-14</CheckInDate>
        <CheckOutDate>2019-04-17</CheckOutDate>
        <StarRating>0</StarRating>
        <LocationCode></LocationCode>
        <SupplierCompCode></SupplierCompCode>
        <AvailableHotelOnly>true</AvailableHotelOnly>
        <RecommendHotelOnly>false</RecommendHotelOnly>
        <ClientCurrencyCode>USD</ClientCurrencyCode>
        <ItemName></ItemName>
        <SellerMarkup>*1</SellerMarkup>
        <CompareYn>false</CompareYn>
        <SortType></SortType>
        <ItemCodeList>
            <ItemCodeInfo>
                <ItemCode></ItemCode>
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
</GetHotelSearchListForCustomerCount>
</soap:Body>
</soap:Envelope>";

$params = array(
    "claveEntidad" => $trapsaturpackagesEntityKey,
    "login" => $trapsaturpackagesLogin,
    "password" => $trapsaturpackagesPassword,
    "idioma" => "es"
);
try {
    $client = new SoapClient($rtsServiceURL . 'WebServiceProjects/NetWebService/WsHotelProducts.asmx', array(
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        "trace" => 1,
        "exceptions" => true,
        'soap_version' => SOAP_1_1,
        "connection_timeout" => 100
    ));
} catch (\Exception $e) {
    var_dump($e);
    die();
}
echo $return;
echo "PASSOU 1";
echo $return;
// var_dump($client);
try {
    $client->__soapCall('GetHotelSearchListForCustomerCount', array(
        $raw
    ));
} catch (\Exception $e) {
    var_dump($e);
    die();
}
echo $return;
echo "PASSOU 2";
echo $return;
$xmlrequest = $client->__getLastRequest();
$xmlresult = $client->__getLastResponse();

echo "RESPONSE";
/* echo $return;
echo $response;
echo $return; */
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
$GetHotelSearchListForCustomerCountResponse = $Body->item(0)->getElementsByTagName("GetHotelSearchListForCustomerCountResponse");
//GetHotelSearchListForCustomerCountResult
$GetHotelSearchListForCustomerCountResult = $GetHotelSearchListForCustomerCountResponse->item(0)->getElementsByTagName("GetHotelSearchListForCustomerCountResult");
$GetHotelSearchListResponse = $GetHotelSearchListForCustomerCountResult->item(0)->getElementsByTagName("GetHotelSearchListResponse");

$node = $GetHotelSearchListResponse->item(0)->getElementsByTagName("GetHotelSearchListResult");
$LanguageCode = $node->item(0)->getElementsByTagName("LanguageCode");
if ($LanguageCode->length > 0) {
    $LanguageCode = $LanguageCode->item(0)->nodeValue;
} else {
    $LanguageCode = "";
}
$LanguageName = $node->item(0)->getElementsByTagName("LanguageName");
if ($LanguageName->length > 0) {
    $LanguageName = $LanguageName->item(0)->nodeValue;
} else {
    $LanguageName = "";
}
$NationalityCode = $node->item(0)->getElementsByTagName("NationalityCode");
if ($NationalityCode->length > 0) {
    $NationalityCode = $NationalityCode->item(0)->nodeValue;
} else {
    $NationalityCode = "";
}
$NationalityName = $node->item(0)->getElementsByTagName("NationalityName");
if ($NationalityName->length > 0) {
    $NationalityName = $NationalityName->item(0)->nodeValue;
} else {
    $NationalityName = "";
}
$ContinentCode = $node->item(0)->getElementsByTagName("ContinentCode");
if ($ContinentCode->length > 0) {
    $ContinentCode = $ContinentCode->item(0)->nodeValue;
} else {
    $ContinentCode = "";
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
$CityName = $node->item(0)->getElementsByTagName("CityName");
if ($CityName->length > 0) {
    $CityName = $CityName->item(0)->nodeValue;
} else {
    $CityName = "";
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
$CountryName = $node->item(0)->getElementsByTagName("CountryName");
if ($CountryName->length > 0) {
    $CountryName = $CountryName->item(0)->nodeValue;
} else {
    $CountryName = "";
}
$StateCode = $node->item(0)->getElementsByTagName("StateCode");
if ($StateCode->length > 0) {
    $StateCode = $StateCode->item(0)->nodeValue;
} else {
    $StateCode = "";
}
$StateEname = $node->item(0)->getElementsByTagName("StateEname");
if ($StateEname->length > 0) {
    $StateEname = $StateEname->item(0)->nodeValue;
} else {
    $StateEname = "";
}
$StateName = $node->item(0)->getElementsByTagName("StateName");
if ($StateName->length > 0) {
    $StateName = $StateName->item(0)->nodeValue;
} else {
    $StateName = "";
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
$CheckInLeftDays = $node->item(0)->getElementsByTagName("CheckInLeftDays");
if ($CheckInLeftDays->length > 0) {
    $CheckInLeftDays = $CheckInLeftDays->item(0)->nodeValue;
} else {
    $CheckInLeftDays = "";
}
$ItemName = $node->item(0)->getElementsByTagName("ItemName");
if ($ItemName->length > 0) {
    $ItemName = $ItemName->item(0)->nodeValue;
} else {
    $ItemName = "";
}
$ItemCode = $node->item(0)->getElementsByTagName("ItemCode");
if ($ItemCode->length > 0) {
    $ItemCode = $ItemCode->item(0)->nodeValue;
} else {
    $ItemCode = "";
}
$ItemNo = $node->item(0)->getElementsByTagName("ItemNo");
if ($ItemNo->length > 0) {
    $ItemNo = $ItemNo->item(0)->nodeValue;
} else {
    $ItemNo = "";
}
$StarRating = $node->item(0)->getElementsByTagName("StarRating");
if ($StarRating->length > 0) {
    $StarRating = $StarRating->item(0)->nodeValue;
} else {
    $StarRating = "";
}
$LocationCode = $node->item(0)->getElementsByTagName("LocationCode");
if ($LocationCode->length > 0) {
    $LocationCode = $LocationCode->item(0)->nodeValue;
} else {
    $LocationCode = "";
}
$AvailableHotelOnly = $node->item(0)->getElementsByTagName("AvailableHotelOnly");
if ($AvailableHotelOnly->length > 0) {
    $AvailableHotelOnly = $AvailableHotelOnly->item(0)->nodeValue;
} else {
    $AvailableHotelOnly = "";
}
$RecommendHotelOnly = $node->item(0)->getElementsByTagName("RecommendHotelOnly");
if ($RecommendHotelOnly->length > 0) {
    $RecommendHotelOnly = $RecommendHotelOnly->item(0)->nodeValue;
} else {
    $RecommendHotelOnly = "";
}
$TotalResultCount = $node->item(0)->getElementsByTagName("TotalResultCount");
if ($TotalResultCount->length > 0) {
    $TotalResultCount = $TotalResultCount->item(0)->nodeValue;
} else {
    $TotalResultCount = "";
}
$ExchangeConvertDate = $node->item(0)->getElementsByTagName("ExchangeConvertDate");
if ($ExchangeConvertDate->length > 0) {
    $ExchangeConvertDate = $ExchangeConvertDate->item(0)->nodeValue;
} else {
    $ExchangeConvertDate = "";
}
$SellingCurrencyCode = $node->item(0)->getElementsByTagName("SellingCurrencyCode");
if ($SellingCurrencyCode->length > 0) {
    $SellingCurrencyCode = $SellingCurrencyCode->item(0)->nodeValue;
} else {
    $SellingCurrencyCode = "";
}
$ClientCurrencyCode = $node->item(0)->getElementsByTagName("ClientCurrencyCode");
if ($ClientCurrencyCode->length > 0) {
    $ClientCurrencyCode = $ClientCurrencyCode->item(0)->nodeValue;
} else {
    $ClientCurrencyCode = "";
}
$SellingConvertRate = $node->item(0)->getElementsByTagName("SellingConvertRate");
if ($SellingConvertRate->length > 0) {
    $SellingConvertRate = $SellingConvertRate->item(0)->nodeValue;
} else {
    $SellingConvertRate = "";
}
$CityEventList = $node->item(0)->getElementsByTagName("CityEventList");
if ($CityEventList->length > 0) {
    $CityEventList = $CityEventList->item(0)->nodeValue;
} else {
    $CityEventList = "";
}



for ($iAux = 0; $iAux < $node->length; $iAux ++) {
    $tipo = $node->item($iAUX)->getElementsByTagName("tipo");
    if ($tipo->length > 0) {
        $tipo = $tipo->item(0)->nodeValue;
    } else {
        $tipo = "";
    }


    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('travelplan_arbolResponseRowTypeCont');
    $select->where(array(
        'codigo' => $codigo
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = $data['codigo'];
        if (count($id) > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'tipo' => $tipo,
                'codigo' => $codigo,
                'descr' => $desc,
                'codZona' => $codZona,
                'descZona' => $descZona,
                'cadenaDestino' => $cadenaDestino,
                'residente' => $residente
            );
            $where['codigo = ?'] = $codigo;
            $update = $sql->update('travelplan_arbolResponseRowTypeCont', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('travelplan_arbolResponseRowTypeCont');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'tipo' => $tipo,
                'codigo' => $codigo,
                'descr' => $desc,
                'codZona' => $codZona,
                'descZona' => $descZona,
                'cadenaDestino' => $cadenaDestino,
                'residente' => $residente
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    } else {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('travelplan_arbolResponseRowTypeCont');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'tipo' => $tipo,
            'codigo' => $codigo,
            'descr' => $desc,
            'codZona' => $codZona,
            'descZona' => $descZona,
            'cadenaDestino' => $cadenaDestino,
            'residente' => $residente
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>