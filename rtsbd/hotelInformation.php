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
echo "COMECOU INFORMACAO<br/>";
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

$sql = "SELECT ItemCode FROM hoteis_ItemInfo";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}


$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $ItemCode = $row->ItemCode;

        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Header>
        <BaseInfo xmlns="http://www.rts.co.kr/">
        <SiteCode>' . $rtsSiteCode . '</SiteCode>
        <Password>' . $rtsPassword . '</Password>
        <RequestType>NetPartner</RequestType>
        </BaseInfo>
        </soap:Header>
        <soap:Body>
            <GetHotelInformation xmlns="http://www.rts.co.kr/">
                <HotelInformationRTSWS>
                    <ST_GetHotelInformationRTSWS>
                        <LanguageCode>AR</LanguageCode>
                        <ItemCode>' . $ItemCode . '</ItemCode>
                    </ST_GetHotelInformationRTSWS>
                </HotelInformationRTSWS>
            </GetHotelInformation>
        </soap:Body>
        </soap:Envelope>';

        $soapUrl = 'http://devwsar.rts.net/WebServiceProjects/NetWebService/WsHotelProducts.asmx';

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "SOAPAction: http://www.rts.co.kr/GetHotelInformation",
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
        $GetHotelInformationResponse = $Body->item(0)->getElementsByTagName("GetHotelInformationResponse");
        // GetHotelInformationResult
        $GetHotelInformationResult = $GetHotelInformationResponse->item(0)->getElementsByTagName("GetHotelInformationResult");
        $Return = $GetHotelInformationResult->item(0)->getElementsByTagName("Return");
        $GetHotelInformationResponse = $Return->item(0)->getElementsByTagName("GetHotelInformationResponse");
        // GetHotelInformationResult
        $GetHotelInformationResult = $GetHotelInformationResponse->item(0)->getElementsByTagName("GetHotelInformationResult");

        $LanguageCode = $GetHotelInformationResult->item(0)->getElementsByTagName("LanguageCode");
        if ($LanguageCode->length > 0) {
            $LanguageCode = $LanguageCode->item(0)->nodeValue;
        } else {
            $LanguageCode = "";
        }
        $LanguageName = $GetHotelInformationResult->item(0)->getElementsByTagName("LanguageName");
        if ($LanguageName->length > 0) {
            $LanguageName = $LanguageName->item(0)->nodeValue;
        } else {
            $LanguageName = "";
        }

        $HotelList = $GetHotelInformationResult->item(0)->getElementsByTagName("HotelList");
        $node = $HotelList->item(0)->getElementsByTagName("HotelInformation");

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
        $CurrencyCode = $node->item(0)->getElementsByTagName("CurrencyCode");
        if ($CurrencyCode->length > 0) {
            $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
        } else {
            $CurrencyCode = "";
        }
        $StarRating = $node->item(0)->getElementsByTagName("StarRating");
        if ($StarRating->length > 0) {
            $StarRating = $StarRating->item(0)->nodeValue;
        } else {
            $StarRating = "";
        }
        $CategoryDesc = $node->item(0)->getElementsByTagName("CategoryDesc");
        if ($CategoryDesc->length > 0) {
            $CategoryDesc = $CategoryDesc->item(0)->nodeValue;
        } else {
            $CategoryDesc = "";
        }
        $TotalRoomCount = $node->item(0)->getElementsByTagName("TotalRoomCount");
        if ($TotalRoomCount->length > 0) {
            $TotalRoomCount = $TotalRoomCount->item(0)->nodeValue;
        } else {
            $TotalRoomCount = "";
        }
        $TotalFloorCount = $node->item(0)->getElementsByTagName("TotalFloorCount");
        if ($TotalFloorCount->length > 0) {
            $TotalFloorCount = $TotalFloorCount->item(0)->nodeValue;
        } else {
            $TotalFloorCount = "";
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
        $Address = $node->item(0)->getElementsByTagName("Address");
        if ($Address->length > 0) {
            $Address = $Address->item(0)->nodeValue;
        } else {
            $Address = "";
        }
        $ZipCode = $node->item(0)->getElementsByTagName("ZipCode");
        if ($ZipCode->length > 0) {
            $ZipCode = $ZipCode->item(0)->nodeValue;
        } else {
            $ZipCode = "";
        }
        $HomepageUrl = $node->item(0)->getElementsByTagName("HomepageUrl");
        if ($HomepageUrl->length > 0) {
            $HomepageUrl = $HomepageUrl->item(0)->nodeValue;
        } else {
            $HomepageUrl = "";
        }
        $RecommendYn = $node->item(0)->getElementsByTagName("RecommendYn");
        if ($RecommendYn->length > 0) {
            $RecommendYn = $RecommendYn->item(0)->nodeValue;
        } else {
            $RecommendYn = "";
        }
        $ExpertReportYn = $node->item(0)->getElementsByTagName("ExpertReportYn");
        if ($ExpertReportYn->length > 0) {
            $ExpertReportYn = $ExpertReportYn->item(0)->nodeValue;
        } else {
            $ExpertReportYn = "";
        }
        $BackpackRecommendYn = $node->item(0)->getElementsByTagName("BackpackRecommendYn");
        if ($BackpackRecommendYn->length > 0) {
            $BackpackRecommendYn = $BackpackRecommendYn->item(0)->nodeValue;
        } else {
            $BackpackRecommendYn = "";
        }
        $BusinessRecommendYn = $node->item(0)->getElementsByTagName("BusinessRecommendYn");
        if ($BusinessRecommendYn->length > 0) {
            $BusinessRecommendYn = $BusinessRecommendYn->item(0)->nodeValue;
        } else {
            $BusinessRecommendYn = "";
        }
        $HoneymoonRecommendYn = $node->item(0)->getElementsByTagName("HoneymoonRecommendYn");
        if ($HoneymoonRecommendYn->length > 0) {
            $HoneymoonRecommendYn = $HoneymoonRecommendYn->item(0)->nodeValue;
        } else {
            $HoneymoonRecommendYn = "";
        }
        $FairRecommendYn = $node->item(0)->getElementsByTagName("FairRecommendYn");
        if ($FairRecommendYn->length > 0) {
            $FairRecommendYn = $FairRecommendYn->item(0)->nodeValue;
        } else {
            $FairRecommendYn = "";
        }
        $ResortRecommendYn = $node->item(0)->getElementsByTagName("ResortRecommendYn");
        if ($ResortRecommendYn->length > 0) {
            $ResortRecommendYn = $ResortRecommendYn->item(0)->nodeValue;
        } else {
            $ResortRecommendYn = "";
        }
        $LastWriteTime = $node->item(0)->getElementsByTagName("LastWriteTime");
        if ($LastWriteTime->length > 0) {
            $LastWriteTime = $LastWriteTime->item(0)->nodeValue;
        } else {
            $LastWriteTime = "";
        }
        $UseYn = $node->item(0)->getElementsByTagName("UseYn");
        if ($UseYn->length > 0) {
            $UseYn = $UseYn->item(0)->nodeValue;
        } else {
            $UseYn = "";
        }
        $AdminRemarks = $node->item(0)->getElementsByTagName("AdminRemarks");
        if ($AdminRemarks->length > 0) {
            $AdminRemarks = $AdminRemarks->item(0)->nodeValue;
        } else {
            $AdminRemarks = "";
        }
        $HotelChainCode = $node->item(0)->getElementsByTagName("HotelChainCode");
        if ($HotelChainCode->length > 0) {
            $HotelChainCode = $HotelChainCode->item(0)->nodeValue;
        } else {
            $HotelChainCode = "";
        }
        $HotelTypeCode = $node->item(0)->getElementsByTagName("HotelTypeCode");
        if ($HotelTypeCode->length > 0) {
            $HotelTypeCode = $HotelTypeCode->item(0)->nodeValue;
        } else {
            $HotelTypeCode = "";
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

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hotelInfo');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'LanguageCode' => $LanguageCode,
                'LanguageName' => $LanguageName,
                'CityCode' => $CityCode,
                'CityEname' => $CityEname,
                'CityName' => $CityName,
                'CountryCode' => $CountryCode,
                'CountryEname' => $CountryEname,
                'CountryName' => $CountryName,
                'ItemName' => $ItemName,
                'ItemCode' => $ItemCode,
                'CurrencyCode' => $CurrencyCode,
                'StarRating' => $StarRating,
                'CategoryDesc' => $CategoryDesc,
                'TotalRoomCount' => $TotalRoomCount,
                'TotalFloorCount' => $TotalFloorCount,
                'PhoneNo' => $PhoneNo,
                'FaxNo' => $FaxNo,
                'Email' => $Email,
                'Address' => $Address,
                'ZipCode' => $ZipCode,
                'HomepageUrl' => $HomepageUrl,
                'RecommendYn' => $RecommendYn,
                'ExpertReportYn' => $ExpertReportYn,
                'BackpackRecommendYn' => $BackpackRecommendYn,
                'BusinessRecommendYn' => $BusinessRecommendYn,
                'HoneymoonRecommendYn' => $HoneymoonRecommendYn,
                'FairRecommendYn' => $FairRecommendYn,
                'ResortRecommendYn' => $ResortRecommendYn,
                'LastWriteTime' => $LastWriteTime,
                'UseYn' => $UseYn,
                'AdminRemarks' => $AdminRemarks,
                'HotelChainCode' => $HotelChainCode,
                'HotelTypeCode' => $HotelTypeCode,
                'Latitude' => $Latitude,
                'Longitude' => $Longitude
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO INFO: " . $e;
            echo $return;
        }

        

        //ImagesFileList
        $ImagesFileList = $node->item(0)->getElementsByTagName("ImagesFileList");
        if ($ImagesFileList->length > 0) {
            $ImageFile = $ImagesFileList->item(0)->getElementsByTagName("ImageFile");
            if ($ImageFile->length > 0) {
                for ($x=0; $x < $ImageFile->length; $x++) { 
                    $DisplayOrder = $ImageFile->item($x)->getElementsByTagName("DisplayOrder");
                    if ($DisplayOrder->length > 0) {
                        $DisplayOrder = $DisplayOrder->item(0)->nodeValue;
                    } else {
                        $DisplayOrder = "";
                    }
                    $ImageTypeCode = $ImageFile->item($x)->getElementsByTagName("ImageTypeCode");
                    if ($ImageTypeCode->length > 0) {
                        $ImageTypeCode = $ImageTypeCode->item(0)->nodeValue;
                    } else {
                        $ImageTypeCode = "";
                    }
                    $ImageTitle = $ImageFile->item($x)->getElementsByTagName("ImageTitle");
                    if ($ImageTitle->length > 0) {
                        $ImageTitle = $ImageTitle->item(0)->nodeValue;
                    } else {
                        $ImageTitle = "";
                    }
                    $ImageDesc = $ImageFile->item($x)->getElementsByTagName("ImageDesc");
                    if ($ImageDesc->length > 0) {
                        $ImageDesc = $ImageDesc->item(0)->nodeValue;
                    } else {
                        $ImageDesc = "";
                    }
                    $ImageFileName = $ImageFile->item($x)->getElementsByTagName("ImageFileName");
                    if ($ImageFileName->length > 0) {
                        $ImageFileName = $ImageFileName->item(0)->nodeValue;
                    } else {
                        $ImageFileName = "";
                    }
                    $MiddleSizeFileName = $ImageFile->item($x)->getElementsByTagName("MiddleSizeFileName");
                    if ($MiddleSizeFileName->length > 0) {
                        $MiddleSizeFileName = $MiddleSizeFileName->item(0)->nodeValue;
                    } else {
                        $MiddleSizeFileName = "";
                    }
                    $LargeSizeFileName = $ImageFile->item($x)->getElementsByTagName("LargeSizeFileName");
                    if ($LargeSizeFileName->length > 0) {
                        $LargeSizeFileName = $LargeSizeFileName->item(0)->nodeValue;
                    } else {
                        $LargeSizeFileName = "";
                    }
                    $LastWriteTime = $ImageFile->item($x)->getElementsByTagName("LastWriteTime");
                    if ($LastWriteTime->length > 0) {
                        $LastWriteTime = $LastWriteTime->item(0)->nodeValue;
                    } else {
                        $LastWriteTime = "";
                    }
                    $FileCode = $ImageFile->item(0)->getElementsByTagName("FileCode");
                    if ($FileCode->length > 0) {
                        $FileCode = $FileCode->item(0)->nodeValue;
                    } else {
                        $FileCode = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelInfo_ImageFile');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'DisplayOrder' => $DisplayOrder,
                            'ImageTypeCode' => $ImageTypeCode,
                            'ImageTitle' => $ImageTitle,
                            'ImageDesc' => $ImageDesc,
                            'ImageFileName' => $ImageFileName,
                            'MiddleSizeFileName' => $MiddleSizeFileName,
                            'LargeSizeFileName' => $LargeSizeFileName,
                            'LastWriteTime' => $LastWriteTime,
                            'FileCode' => $FileCode
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO IMG: " . $e;
                            echo $return;
                    }

                }
            }
        }


        //HotelFacilityList
        $HotelFacilityList = $node->item(0)->getElementsByTagName("HotelFacilityList");
        if ($HotelFacilityList->length > 0) {
            $HotelFacility = $HotelFacilityList->item(0)->getElementsByTagName("HotelFacility");
            if ($HotelFacility->length > 0) {
                for ($y=0; $y < $HotelFacility->length; $y++) { 
                    $FacilityTypeCode = $HotelFacility->item($y)->getElementsByTagName("FacilityTypeCode");
                    if ($FacilityTypeCode->length > 0) {
                        $FacilityTypeCode = $FacilityTypeCode->item(0)->nodeValue;
                    } else {
                        $FacilityTypeCode = "";
                    }
                    $FacilityTypeName = $HotelFacility->item($y)->getElementsByTagName("FacilityTypeName");
                    if ($FacilityTypeName->length > 0) {
                        $FacilityTypeName = $FacilityTypeName->item(0)->nodeValue;
                    } else {
                        $FacilityTypeName = "";
                    }
                    $FacilityCode = $HotelFacility->item($y)->getElementsByTagName("FacilityCode");
                    if ($FacilityCode->length > 0) {
                        $FacilityCode = $FacilityCode->item(0)->nodeValue;
                    } else {
                        $FacilityCode = "";
                    }
                    $FacilityName = $HotelFacility->item($y)->getElementsByTagName("FacilityName");
                    if ($FacilityName->length > 0) {
                        $FacilityName = $FacilityName->item(0)->nodeValue;
                    } else {
                        $FacilityName = "";
                    }
                    $LastWriteTime = $HotelFacility->item($y)->getElementsByTagName("LastWriteTime");
                    if ($LastWriteTime->length > 0) {
                        $LastWriteTime = $LastWriteTime->item(0)->nodeValue;
                    } else {
                        $LastWriteTime = "";
                    }
                    $FirstWriter = $HotelFacility->item($y)->getElementsByTagName("FirstWriter");
                    if ($FirstWriter->length > 0) {
                        $FirstWriter = $FirstWriter->item(0)->nodeValue;
                    } else {
                        $FirstWriter = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelInfo_HotelFacility');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'FacilityTypeCode' => $FacilityTypeCode,
                            'FacilityTypeName' => $FacilityTypeName,
                            'FacilityCode' => $FacilityCode,
                            'FacilityName' => $FacilityName,
                            'LastWriteTime' => $LastWriteTime,
                            'FirstWriter' => $FirstWriter
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO FACIL: " . $e;
                        echo $return;
                    }

                }
            }
        }


        //AvailableBedList
        $AvailableBedList = $node->item(0)->getElementsByTagName("AvailableBedList");
        if ($AvailableBedList->length > 0) {
            $AavailableBedInfo = $AvailableBedList->item(0)->getElementsByTagName("AavailableBedInfo");
            if ($AavailableBedInfo->length > 0) {
                $BedTypeCode = $AavailableBedInfo->item(0)->getElementsByTagName("BedTypeCode");
                if ($BedTypeCode->length > 0) {
                    $BedTypeCode = $BedTypeCode->item(0)->nodeValue;
                } else {
                    $BedTypeCode = "";
                }
                $BedTypeName = $AavailableBedInfo->item(0)->getElementsByTagName("BedTypeName");
                if ($BedTypeName->length > 0) {
                    $BedTypeName = $BedTypeName->item(0)->nodeValue;
                } else {
                    $BedTypeName = "";
                }
            }
        }


        //HotelGroup
        $HotelGroup = $node->item(0)->getElementsByTagName("HotelGroup");
        if ($HotelGroup->length > 0) {
            $ChainCode = $HotelGroup->item(0)->getElementsByTagName("ChainCode");
            if ($ChainCode->length > 0) {
                for ($z=0; $z < $ChainCode->length; $z++) { 
                    $ChainCodeBasicCode = $ChainCode->item($z)->getElementsByTagName("BasicCode");
                    if ($ChainCodeBasicCode->length > 0) {
                        $ChainCodeBasicCode = $ChainCodeBasicCode->item(0)->nodeValue;
                    } else {
                        $ChainCodeBasicCode = "";
                    }
                    $ChainCodeBasicName = $ChainCode->item($z)->getElementsByTagName("BasicName");
                    if ($ChainCodeBasicName->length > 0) {
                        $ChainCodeBasicName = $ChainCodeBasicName->item(0)->nodeValue;
                    } else {
                        $ChainCodeBasicName = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelInfo_ChainCode');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'ChainCodeBasicCode' => $ChainCodeBasicCode,
                            'ChainCodeBasicName' => $ChainCodeBasicName
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO CC: " . $e;
                        echo $return;
                    }

                }
            }

            $TypeCode = $HotelGroup->item(0)->getElementsByTagName("TypeCode");
            if ($TypeCode->length > 0) {
                for ($z=0; $z < $TypeCode->length; $z++) { 
                    $TypeCodeBasicCode = $TypeCode->item($z)->getElementsByTagName("BasicCode");
                    if ($TypeCodeBasicCode->length > 0) {
                        $TypeCodeBasicCode = $TypeCodeBasicCode->item(0)->nodeValue;
                    } else {
                        $TypeCodeBasicCode = "";
                    }
                    $TypeCodeBasicName = $TypeCode->item($z)->getElementsByTagName("BasicName");
                    if ($TypeCodeBasicName->length > 0) {
                        $TypeCodeBasicName = $TypeCodeBasicName->item(0)->nodeValue;
                    } else {
                        $TypeCodeBasicName = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelInfo_TypeCode');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'TypeCodeBasicCode' => $TypeCodeBasicCode,
                            'TypeCodeBasicName' => $TypeCodeBasicName
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO TYPE: " . $e;
                        echo $return;
                    }

                }
            }
        }


        //HotelFacilityList
        $AdditionalReportList = $node->item(0)->getElementsByTagName("AdditionalReportList");
        if ($AdditionalReportList->length > 0) {
            $AdditionalReport = $AdditionalReportList->item(0)->getElementsByTagName("AdditionalReport");
            if ($AdditionalReport->length > 0) {
                for ($i=0; $i < $AdditionalReport->length; $i++) { 
                    $ReportTypeCode = $AdditionalReport->item($i)->getElementsByTagName("ReportTypeCode");
                    if ($ReportTypeCode->length > 0) {
                        $ReportTypeCode = $ReportTypeCode->item(0)->nodeValue;
                    } else {
                        $ReportTypeCode = "";
                    }
                    $ReportTypeCodeName = $AdditionalReport->item($i)->getElementsByTagName("ReportTypeCodeName");
                    if ($ReportTypeCodeName->length > 0) {
                        $ReportTypeCodeName = $ReportTypeCodeName->item(0)->nodeValue;
                    } else {
                        $ReportTypeCodeName = "";
                    }
                    $ReportTypeName = $AdditionalReport->item($i)->getElementsByTagName("ReportTypeName");
                    if ($ReportTypeName->length > 0) {
                        $ReportTypeName = $ReportTypeName->item(0)->nodeValue;
                    } else {
                        $ReportTypeName = "";
                    }
                    $ReportTitle = $AdditionalReport->item($i)->getElementsByTagName("ReportTitle");
                    if ($ReportTitle->length > 0) {
                        $ReportTitle = $ReportTitle->item(0)->nodeValue;
                    } else {
                        $ReportTitle = "";
                    }
                    $ReportDetail = $AdditionalReport->item($i)->getElementsByTagName("ReportDetail");
                    if ($ReportDetail->length > 0) {
                        $ReportDetail = $ReportDetail->item(0)->nodeValue;
                    } else {
                        $ReportDetail = "";
                    }
                    $LastWriteTime = $AdditionalReport->item($i)->getElementsByTagName("LastWriteTime");
                    if ($LastWriteTime->length > 0) {
                        $LastWriteTime = $LastWriteTime->item(0)->nodeValue;
                    } else {
                        $LastWriteTime = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hotelInfo_AdditionalReport');
                        $insert->values(array(
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'ReportTypeCode' => $ReportTypeCode,
                            'ReportTypeCodeName' => $ReportTypeCodeName,
                            'ReportTypeName' => $ReportTypeName,
                            'ReportTitle' => $ReportTitle,
                            'ReportDetail' => $ReportDetail,
                            'LastWriteTime' => $LastWriteTime
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (\Exception $e) {
                        echo $return;
                        echo "ERRO AR: " . $e;
                        echo $return;
                    }

                }
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>