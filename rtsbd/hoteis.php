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
        <SiteCode>" . $rtsSiteCode . "</SiteCode>
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
        <CheckInDate>2019-06-14</CheckInDate>
        <CheckOutDate>2019-06-17</CheckOutDate>
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

$BaseInfo = array(
    "SiteCode" => $rtsSiteCode,
    "Password" => $rtsPassword,
    "Password" => "NetPartner"
);

$Header = array("BaseInfo" => $BaseInfo);

$GuestsInfo2 = array(
    'AdultCount' => 2, 
    'ChildCount' => 1,
    'RoomCount' => 1, 
    'ChildAge1' => 5,
    'ChildAge2' => 0
);

$GuestsInfo1 = array(
    'AdultCount' => 2, 
    'ChildCount' => 0,
    'RoomCount' => 1, 
    'ChildAge1' => 0,
    'ChildAge2' => 0
);

$GuestList = array(
    'GuestsInfo' => $GuestsInfo1, 
    'GuestsInfo' => $GuestsInfo2 
);

$ItemCodeInfo = array(
    'ItemCode' => '', 
    'ItemNo' => 0 
);

$ItemCodeList = array(
    'ItemCodeInfo' =>  $ItemCodeInfo
);

$HotelSearchListNetGuestCount = array(
    'LanguageCode' => 'AR', 
    'TravelerNationality' => 'AR',
    'LanguageCode' => 'AR',
    'CityCode' => 'ROM',
    'CheckInDate' => '2019-10-14',
    'CheckOutDate' => '2019-10-17',
    'StarRating' => 0,
    'LocationCode' => '',
    'SupplierCompCode' => '',
    'AvailableHotelOnly' => true,
    'RecommendHotelOnly' => false,
    'ClientCurrencyCode' => 'USD',
    'ItemName' => '',
    'SellerMarkup' => '*1',
    'CompareYn' => false,
    'SortType' => '',
    'ItemCodeList' => $ItemCodeList,
    'GuestList' => $GuestList
);

$GetHotelSearchListForCustomerCount = array(
    "HotelSearchListNetGuestCount" => $HotelSearchListNetGuestCount
);

$Body = array("GetHotelSearchListForCustomerCount" => $GetHotelSearchListForCustomerCount);

$params = array(
    "Header" => $Header,
    "Body" => $Body
);

try {
    $client = new SoapClient($rtsServiceURL . 'WebServiceProjects/NetWebService/WsHotelProducts.asmx?wsdl', array(
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
echo '<xmp>';
var_dump($client->__getFunctions());
echo '</xmp>';

try {
    $client->__soapCall('GetHotelSearchListForCustomerCount', array(
        $params
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
var_dump($xmlresult);
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


$HotelSearchList = $node->item(0)->getElementsByTagName("HotelSearchList");
if ($HotelSearchList->length > 0) {
    $HotelItemInfo = $HotelSearchList->item(0)->getElementsByTagName("HotelItemInfo");
    if ($HotelItemInfo->length > 0) {
        $ItemCode = $HotelItemInfo->item(0)->getElementsByTagName("ItemCode");
        if ($ItemCode->length > 0) {
            $ItemCode = $ItemCode->item(0)->nodeValue;
        } else {
            $ItemCode = "";
        }
        $ItemName = $HotelItemInfo->item(0)->getElementsByTagName("ItemName");
        if ($ItemName->length > 0) {
            $ItemName = $ItemName->item(0)->nodeValue;
        } else {
            $ItemName = "";
        }
        $StarRating = $HotelItemInfo->item(0)->getElementsByTagName("StarRating");
        if ($StarRating->length > 0) {
            $StarRating = $StarRating->item(0)->nodeValue;
        } else {
            $StarRating = "";
        }
        $RecommendYn = $HotelItemInfo->item(0)->getElementsByTagName("RecommendYn");
        if ($RecommendYn->length > 0) {
            $RecommendYn = $RecommendYn->item(0)->nodeValue;
        } else {
            $RecommendYn = "";
        }
        $ExpertReportYn = $HotelItemInfo->item(0)->getElementsByTagName("ExpertReportYn");
        if ($ExpertReportYn->length > 0) {
            $ExpertReportYn = $ExpertReportYn->item(0)->nodeValue;
        } else {
            $ExpertReportYn = "";
        }
        $FirstImageFileName = $HotelItemInfo->item(0)->getElementsByTagName("FirstImageFileName");
        if ($FirstImageFileName->length > 0) {
            $FirstImageFileName = $FirstImageFileName->item(0)->nodeValue;
        } else {
            $FirstImageFileName = "";
        }
        $HotelDescription = $HotelItemInfo->item(0)->getElementsByTagName("HotelDescription");
        if ($HotelDescription->length > 0) {
            $HotelDescription = $HotelDescription->item(0)->nodeValue;
        } else {
            $HotelDescription = "";
        }
        $BackpackYn = $HotelItemInfo->item(0)->getElementsByTagName("BackpackYn");
        if ($BackpackYn->length > 0) {
            $BackpackYn = $BackpackYn->item(0)->nodeValue;
        } else {
            $BackpackYn = "";
        }
        $BusinessYn = $HotelItemInfo->item(0)->getElementsByTagName("BusinessYn");
        if ($BusinessYn->length > 0) {
            $BusinessYn = $BusinessYn->item(0)->nodeValue;
        } else {
            $BusinessYn = "";
        }
        $HoneymoonYn = $HotelItemInfo->item(0)->getElementsByTagName("HoneymoonYn");
        if ($HoneymoonYn->length > 0) {
            $HoneymoonYn = $HoneymoonYn->item(0)->nodeValue;
        } else {
            $HoneymoonYn = "";
        }
        $FairYn = $HotelItemInfo->item(0)->getElementsByTagName("FairYn");
        if ($FairYn->length > 0) {
            $FairYn = $FairYn->item(0)->nodeValue;
        } else {
            $FairYn = "";
        }
        $AirPackYn = $HotelItemInfo->item(0)->getElementsByTagName("AirPackYn");
        if ($AirPackYn->length > 0) {
            $AirPackYn = $AirPackYn->item(0)->nodeValue;
        } else {
            $AirPackYn = "";
        }
        $BookingCount = $HotelItemInfo->item(0)->getElementsByTagName("BookingCount");
        if ($BookingCount->length > 0) {
            $BookingCount = $BookingCount->item(0)->nodeValue;
        } else {
            $BookingCount = "";
        }
        $LocationList = $HotelItemInfo->item(0)->getElementsByTagName("LocationList");
        if ($LocationList->length > 0) {
            $LocationList = $LocationList->item(0)->nodeValue;
        } else {
            $LocationList = "";
        }

        $GeoCode = $HotelItemInfo->item(0)->getElementsByTagName("GeoCode");
        if ($GeoCode->length > 0) {
            $Latitude = $GeoCode->item(0)->getElementsByTagName("Latitude");
            if ($Latitude->length > 0) {
                $Latitude = $Latitude->item(0)->nodeValue;
            } else {
                $Latitude = "";
            }
            $Longitude = $GeoCode->item(0)->getElementsByTagName("Longitude");
            if ($Longitude->length > 0) {
                $Longitude = $Longitude->item(0)->nodeValue;
            } else {
                $Longitude = "";
            }
        }

        $PriceList = $HotelItemInfo->item(0)->getElementsByTagName("PriceList");
        if ($PriceList->length > 0) {
            $PriceInfo = $PriceList->item(0)->getElementsByTagName("PriceInfo");
            if ($PriceInfo->length > 0) {
                $PriceInfoItemNo = $PriceInfo->item(0)->getElementsByTagName("ItemNo");
                if ($PriceInfoItemNo->length > 0) {
                    $PriceInfoItemNo = $PriceInfoItemNo->item(0)->nodeValue;
                } else {
                    $PriceInfoItemNo = "";
                }
                $PriceInfoItemCode = $PriceInfo->item(0)->getElementsByTagName("ItemCode");
                if ($PriceInfoItemCode->length > 0) {
                    $PriceInfoItemCode = $PriceInfoItemCode->item(0)->nodeValue;
                } else {
                    $PriceInfoItemCode = "";
                }
                $SupplierCompCode = $PriceInfo->item(0)->getElementsByTagName("SupplierCompCode");
                if ($SupplierCompCode->length > 0) {
                    $SupplierCompCode = $SupplierCompCode->item(0)->nodeValue;
                } else {
                    $SupplierCompCode = "";
                }
                $RoomTypeCode = $PriceInfo->item(0)->getElementsByTagName("RoomTypeCode");
                if ($RoomTypeCode->length > 0) {
                    $RoomTypeCode = $RoomTypeCode->item(0)->nodeValue;
                } else {
                    $RoomTypeCode = "";
                }
                $RoomTypeName = $PriceInfo->item(0)->getElementsByTagName("RoomTypeName");
                if ($RoomTypeName->length > 0) {
                    $RoomTypeName = $RoomTypeName->item(0)->nodeValue;
                } else {
                    $RoomTypeName = "";
                }
                $BreakfastTypeName = $PriceInfo->item(0)->getElementsByTagName("BreakfastTypeName");
                if ($BreakfastTypeName->length > 0) {
                    $BreakfastTypeName = $BreakfastTypeName->item(0)->nodeValue;
                } else {
                    $BreakfastTypeName = "";
                }
                $AddBreakfastTypeName = $PriceInfo->item(0)->getElementsByTagName("AddBreakfastTypeName");
                if ($AddBreakfastTypeName->length > 0) {
                    $AddBreakfastTypeName = $AddBreakfastTypeName->item(0)->nodeValue;
                } else {
                    $AddBreakfastTypeName = "";
                }
                $PriceComment = $PriceInfo->item(0)->getElementsByTagName("PriceComment");
                if ($PriceComment->length > 0) {
                    $PriceComment = $PriceComment->item(0)->nodeValue;
                } else {
                    $PriceComment = "";
                }
                $FareRateType = $PriceInfo->item(0)->getElementsByTagName("FareRateType");
                if ($FareRateType->length > 0) {
                    $FareRateType = $FareRateType->item(0)->nodeValue;
                } else {
                    $FareRateType = "";
                }
                $PriceStatus = $PriceInfo->item(0)->getElementsByTagName("PriceStatus");
                if ($PriceStatus->length > 0) {
                    $PriceStatus = $PriceStatus->item(0)->nodeValue;
                } else {
                    $PriceStatus = "";
                }
                $NetCurrencyCode = $PriceInfo->item(0)->getElementsByTagName("NetCurrencyCode");
                if ($NetCurrencyCode->length > 0) {
                    $NetCurrencyCode = $NetCurrencyCode->item(0)->nodeValue;
                } else {
                    $NetCurrencyCode = "";
                }
                $NetConvertRate = $PriceInfo->item(0)->getElementsByTagName("NetConvertRate");
                if ($NetConvertRate->length > 0) {
                    $NetConvertRate = $NetConvertRate->item(0)->nodeValue;
                } else {
                    $NetConvertRate = "";
                }
                $SellerNetPrice = $PriceInfo->item(0)->getElementsByTagName("SellerNetPrice");
                if ($SellerNetPrice->length > 0) {
                    $SellerNetPrice = $SellerNetPrice->item(0)->nodeValue;
                } else {
                    $SellerNetPrice = "";
                }
                $LocalNetPrice = $PriceInfo->item(0)->getElementsByTagName("LocalNetPrice");
                if ($LocalNetPrice->length > 0) {
                    $LocalNetPrice = $LocalNetPrice->item(0)->nodeValue;
                } else {
                    $LocalNetPrice = "";
                }
                $SellerMarkupPrice = $PriceInfo->item(0)->getElementsByTagName("SellerMarkupPrice");
                if ($SellerMarkupPrice->length > 0) {
                    $SellerMarkupPrice = $SellerMarkupPrice->item(0)->nodeValue;
                } else {
                    $SellerMarkupPrice = "";
                }
                $RecommendClientPrice = $PriceInfo->item(0)->getElementsByTagName("RecommendClientPrice");
                if ($RecommendClientPrice->length > 0) {
                    $RecommendClientPrice = $RecommendClientPrice->item(0)->nodeValue;
                } else {
                    $RecommendClientPrice = "";
                }
                $SellerClientPrice = $PriceInfo->item(0)->getElementsByTagName("SellerClientPrice");
                if ($SellerClientPrice->length > 0) {
                    $SellerClientPrice = $SellerClientPrice->item(0)->nodeValue;
                } else {
                    $SellerClientPrice = "";
                }
                $DoubleBedYn = $PriceInfo->item(0)->getElementsByTagName("DoubleBedYn");
                if ($DoubleBedYn->length > 0) {
                    $DoubleBedYn = $DoubleBedYn->item(0)->nodeValue;
                } else {
                    $DoubleBedYn = "";
                }

                $SupplierPromotion = $PriceInfo->item(0)->getElementsByTagName("SupplierPromotion");
                if ($SupplierPromotion->length > 0) {
                    $PromotionName = $SupplierPromotion->item(0)->getElementsByTagName("PromotionName");
                    if ($PromotionName->length > 0) {
                        $PromotionName = $PromotionName->item(0)->nodeValue;
                    } else {
                        $PromotionName = "";
                    }
                    $PromotionDescription = $SupplierPromotion->item(0)->getElementsByTagName("PromotionDescription");
                    if ($PromotionDescription->length > 0) {
                        $PromotionDescription = $PromotionDescription->item(0)->nodeValue;
                    } else {
                        $PromotionDescription = "";
                    }
                }

                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hoteis_ItemInfo');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'ItemCode' => $ItemCode,
                    'ItemName' => $ItemName,
                    'StarRating' => $StarRating,
                    'RecommendYn' => $RecommendYn,
                    'ExpertReportYn' => $ExpertReportYn,
                    'FirstImageFileName' => $FirstImageFileName,
                    'HotelDescription' => $HotelDescription,
                    'BackpackYn' => $BackpackYn,
                    'BusinessYn' => $BusinessYn,
                    'HoneymoonYn' => $HoneymoonYn,
                    'FairYn' => $FairYn,
                    'AirPackYn' => $AirPackYn,
                    'BookingCount' => $BookingCount,
                    'LocationList' => $LocationList,
                    'Latitude' => $Latitude,
                    'Longitude' => $Longitude,
                    'PriceInfoItemNo' => $PriceInfoItemNo,
                    'PriceInfoItemCode' => $PriceInfoItemCode,
                    'SupplierCompCode' => $SupplierCompCode,
                    'RoomTypeCode' => $RoomTypeCode,
                    'RoomTypeName' => $RoomTypeName,
                    'BreakfastTypeName' => $BreakfastTypeName,
                    'AddBreakfastTypeName' => $AddBreakfastTypeName,
                    'PriceComment' => $PriceComment,
                    'FareRateType' => $FareRateType,
                    'PriceStatus' => $PriceStatus,
                    'NetCurrencyCode' => $NetCurrencyCode,
                    'NetConvertRate' => $NetConvertRate,
                    'SellerNetPrice' => $SellerNetPrice,
                    'LocalNetPrice' => $LocalNetPrice,
                    'SellerMarkupPrice' => $SellerMarkupPrice,
                    'RecommendClientPrice' => $RecommendClientPrice,
                    'SellerClientPrice' => $SellerClientPrice,
                    'DoubleBedYn' => $DoubleBedYn,
                    'PromotionName' => $PromotionName,
                    'PromotionDescription' => $PromotionDescription
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();


                $PriceBreakdown = $PriceInfo->item(0)->getElementsByTagName("PriceBreakdown");
                if ($PriceBreakdown->length > 0) {
                    for ($j=0; $j < $PriceBreakdown->length; $j++) { 
                        $Date = $PriceBreakdown->item($j)->getElementsByTagName("Date");
                        if ($Date->length > 0) {
                            $Date = $Date->item(0)->nodeValue;
                        } else {
                            $Date = "";
                        }
                        $Price = $PriceBreakdown->item($j)->getElementsByTagName("Price");
                        if ($Price->length > 0) {
                            $Price = $Price->item(0)->nodeValue;
                        } else {
                            $Price = "";
                        }

                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hoteis_PriceBreakdown');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Date' => $Date,
                            'Price' => $Price,
                            'ItemCode' => $ItemCode
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    }
                }
            }
        }
    }
}


$RoomList = $node->item(0)->getElementsByTagName("RoomList");
if ($RoomList->length > 0) {
    $RoomInfo = $RoomList->item(0)->getElementsByTagName("RoomInfo");
    if ($RoomInfo->length > 0) {
        for ($i = 0; $i < $RoomInfo->length; $i ++) {
            $BedTypeCode = $RoomInfo->item($i)->getElementsByTagName("BedTypeCode");
            if ($BedTypeCode->length > 0) {
                $BedTypeCode = $BedTypeCode->item(0)->nodeValue;
            } else {
                $BedTypeCode = "";
            }
            $RoomCount = $RoomInfo->item($i)->getElementsByTagName("RoomCount");
            if ($RoomCount->length > 0) {
                $RoomCount = $RoomCount->item(0)->nodeValue;
            } else {
                $RoomCount = "";
            }
            $ChlidAge1 = $RoomInfo->item($i)->getElementsByTagName("ChlidAge1");
            if ($ChlidAge1->length > 0) {
                $ChlidAge1 = $ChlidAge1->item(0)->nodeValue;
            } else {
                $ChlidAge1 = "";
            }
            $ChlidAge2 = $ChlidAge2->item($i)->getElementsByTagName("ChlidAge2");
            if ($ChlidAge2->length > 0) {
                $ChlidAge2 = $ChlidAge2->item(0)->nodeValue;
            } else {
                $ChlidAge2 = "";
            }
        
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hoteis_RoomInfo');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'BedTypeCode' => $BedTypeCode,
                'RoomCount' => $RoomCount,
                'ChlidAge1' => $ChlidAge1,
                'ChlidAge2' => $ChlidAge2
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>