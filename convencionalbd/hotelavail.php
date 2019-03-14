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
echo "COMECOU HOTEIS SIATAR<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT Code FROM cities";
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
        $city_code = $row->Code;
        echo $return;
        echo "Code: " . $city_code;
        echo $return;

        $date = new DateTime("NOW");
        $timestamp = $date->format( "Y-m-d\TH:i:s.v" );

        $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:xnet="http://xnetinfo.org/">
        <soap:Header/>
        <soap:Body>
            <xnet:getHotelAvail>
            <xnet:aRequest EchoToken="123" TimeStamp="2019-02-28T17:43:25.315" Version="1.0">
                <xnet:POS>
                    <xnet:Source>
                        <xnet:RequestorID ID="a6dge3!tnsf2or" PartnerID="TEST" Username="xnet" Password="pctnx!!!"/>
                    </xnet:Source> 
                </xnet:POS>
                <xnet:AvailRequest>
                    <xnet:StayDateRange Start="2019-06-02" End="2019-06-09" />
                    <xnet:HotelSearchCriterion HotelCityCode="' . $city_code . '"/>
                </xnet:AvailRequest>
                <xnet:RoomStayCandidates>
                    <xnet:RoomStayCandidate>
                        <xnet:Guest AgeType="ADT" Age="0" Count="2"/>
                    </xnet:RoomStayCandidate>
                </xnet:RoomStayCandidates>
            </xnet:aRequest>
            </xnet:getHotelAvail>
        </soap:Body>
        </soap:Envelope>';

        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));
        $client->setHeaders(array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Content-length: ".strlen($raw)
        ));
        $url = "http://xnetinfo.redirectme.net:8080/homologacao_webservice/Integration/ServerIntegration.asmx";

        $client->setUri($url);
        $client->setMethod('POST');
        $client->setRawBody($raw);
        $response = $client->send();
        if ($response->isSuccess()) {
        $response = $response->getBody();
        } else {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($client->getUri());
        $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
        echo $return;
        echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
        echo $return;
        die();
        }
        echo "<br/>RESPONSE";
        echo '<xmp>';
        var_dump($response);
        echo '</xmp>';

        $config = new \Zend\Config\Config(include '../config/autoload/global.convencional.php');
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
        $getHotelAvailResponse = $Body->item(0)->getElementsByTagName("getHotelAvailResponse");
        $getHotelAvailResult = $getHotelAvailResponse->item(0)->getElementsByTagName("getHotelAvailResult");
        $ID = $getHotelAvailResult->item(0)->getAttribute("ID");
        $RoomStays = $getHotelAvailResult->item(0)->getElementsByTagName("RoomStays");
        $node = $RoomStays->item(0)->getElementsByTagName("RoomStay");
        echo $return;
        echo "TAM: " . $node->length;
        echo $return;
        for ($i=0; $i < $node->length; $i++) {       
            //Hotel
            $Hotel = $node->item($i)->getElementsByTagName("Hotel");
            if ($Hotel->length > 0) {
                $Code = $Hotel->item(0)->getAttribute("Code");
                $Name = $Hotel->item(0)->getAttribute("Name");
                $StarRating = $Hotel->item(0)->getAttribute("StarRating");
                $SubCategory = $Hotel->item(0)->getAttribute("SubCategory");
                $Email = $Hotel->item(0)->getAttribute("Email");
                $Url = $Hotel->item(0)->getAttribute("Url");
                $UrlVirtualTour = $Hotel->item(0)->getAttribute("UrlVirtualTour");
                $MinAccommodationRate = $Hotel->item(0)->getAttribute("MinAccommodationRate");
                $MaxAccommodationRate = $Hotel->item(0)->getAttribute("MaxAccommodationRate");
                echo $return;
                echo "Code: " . $Code;
                echo $return;

                $Description = $Hotel->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    $Description = $Description->item(0)->nodeValue;
                } else {
                    $Description = "";
                }
                $Comments = $Hotel->item(0)->getElementsByTagName("Comments");
                if ($Comments->length > 0) {
                    $Comments = $Comments->item(0)->nodeValue;
                } else {
                    $Comments = "";
                }

                $Address2 = "";
                $Address = $Hotel->item(0)->getElementsByTagName("Address");
                if ($Address->length > 0) {
                    $Latitude = $Address->item(0)->getAttribute("Latitude");
                    $Longitude = $Address->item(0)->getAttribute("Longitude");
                    $Address2 = $Address->item(0)->nodeValue;
                    echo $return;
                    echo "Address2: " . $Address2;
                    echo $return;
                    $City = $Address->item(0)->getElementsByTagName("City");
                    if ($City->length > 0) {
                        $CityCode = $City->item(0)->getAttribute("Code");
                        $CityName = $City->item(0)->getAttribute("Name");
                    }
                }

                $PhoneNumbers = $Hotel->item(0)->getElementsByTagName("PhoneNumbers");
                if ($PhoneNumbers->length > 0) {
                    $PhoneNumber = $PhoneNumbers->item(0)->getElementsByTagName("PhoneNumber");
                    if ($PhoneNumber->length > 0) {
                        $LineNumber = $PhoneNumber->item(0)->getAttribute("LineNumber");
                        $Prefix = $PhoneNumber->item(0)->getAttribute("Prefix");
                        $CountryAccessCode = $PhoneNumber->item(0)->getAttribute("CountryAccessCode");
                        $AreaCityCode = $PhoneNumber->item(0)->getAttribute("AreaCityCode");
                    } else {
                        $LineNumber = "";
                        $Prefix = "";
                        $CountryAccessCode = "";
                        $AreaCityCode = "";
                    }
                }

                $MainPhoto = $Hotel->item(0)->getElementsByTagName("MainPhoto");
                if ($MainPhoto->length > 0) {
                    $MainPhoto = $MainPhoto->item(0)->nodeValue;
                } else {
                    $MainPhoto = "";
                }
                $MinAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MinAccommodationRateCurrency");
                if ($MinAccommodationRateCurrency->length > 0) {
                    $MinAccCode = $MinAccommodationRateCurrency->item(0)->getAttribute("Code");
                } else {
                    $MinAccCode = "";
                }
                $MaxAccommodationRateCurrency = $Hotel->item(0)->getElementsByTagName("MaxAccommodationRateCurrency");
                if ($MaxAccommodationRateCurrency->length > 0) {
                    $MaxAccCode = $MaxAccommodationRateCurrency->item(0)->getAttribute("Code");
                } else {
                    $MaxAccCode = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotelavail');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Code' => $Code,
                        'Name' => $Name,
                        'StarRating' => $StarRating,
                        'SubCategory' => $SubCategory,
                        'Email' => $Email,
                        'Url' => $Url,
                        'UrlVirtualTour' => $UrlVirtualTour,
                        'MaxAccommodationRate' => $MaxAccommodationRate,
                        'MinAccommodationRate' => $MinAccommodationRate,
                        'Description' => $Description,
                        'Comments' => $Comments,
                        'CityCode' => $CityCode,
                        'CityName' => $CityName,
                        'Latitude' => $Latitude,
                        'Longitude' => $Longitude,
                        'Address' => $Address2,
                        'AreaCityCode' => $AreaCityCode,
                        'CountryAccessCode' => $CountryAccessCode,
                        'Prefix' => $Prefix,
                        'LineNumber' => $LineNumber,
                        'MainPhoto' => $MainPhoto,
                        'MinAccCode' => $MinAccCode,
                        'MaxAccCode' => $MaxAccCode,
                        'IDRoomstay' => $ID
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO HOTEL: " . $e;
                    echo $return;
                }

            }

            //RoomRates
            $RoomRates = $node->item($i)->getElementsByTagName("RoomRates");
            if ($RoomRates->length > 0) {
                $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                echo $return;
                echo "TAM2: " . $RoomRate->length;
                echo $return;
                if ($RoomRate->length > 0) {
                    for ($k=0; $k < $RoomRate->length; $k++) { 
                        $IDRoomRate = $RoomRate->item($k)->getAttribute("ID");
                        echo $return;
                        echo "IDRoomRate: " . $IDRoomRate;
                        echo $return;
                        $FitGroup = $RoomRate->item($k)->getAttribute("FitGroup");
                        $HasAllIncluded = $RoomRate->item($k)->getAttribute("HasAllIncluded");
                        $HasBkftIncluded = $RoomRate->item($k)->getAttribute("HasBkftIncluded");
                        $HasFapIncluded = $RoomRate->item($k)->getAttribute("HasFapIncluded");
                        $HasMapIncluded = $RoomRate->item($k)->getAttribute("HasMapIncluded");
                        $CancelCost = $RoomRate->item($k)->getAttribute("CancelCost");
                        $DailyCostCancel = $RoomRate->item($k)->getAttribute("DailyCostCancel");
                        $DeadLineCancel = $RoomRate->item($k)->getAttribute("DeadLineCancel");
                        $ChargingUnit = $RoomRate->item($k)->getAttribute("ChargingUnit");
                        $TotalValue = $RoomRate->item($k)->getAttribute("TotalValue");

                        $Currency = $RoomRate->item($k)->getElementsByTagName("Currency");
                        if ($Currency->length > 0) {
                            $CurrencyCode = $Currency->item(0)->getAttribute("Code");
                        } else {
                            $CurrencyCode = "";
                        }
                        $Market = $RoomRate->item($k)->getElementsByTagName("Market");
                        if ($Market->length > 0) {
                            $MarketCode = $Market->item(0)->getAttribute("Code");
                        } else {
                            $MarketCode = "";
                        }
                        $Comments = $RoomRate->item($k)->getElementsByTagName("Comments");
                        if ($Comments->length > 0) {
                            $Comments = $Comments->item(0)->nodeValue;
                        } else {
                            $Comments = "";
                        }

                        //RoomType
                        $RoomType = $RoomRate->item($k)->getElementsByTagName("RoomType");
                        if ($RoomType->length > 0) {
                            $RoomTypeCode = $RoomType->item(0)->getAttribute("Code");
                            $RoomTypeName = $RoomType->item(0)->getAttribute("Name");

                            $RoomsOccupants = $RoomType->item(0)->getElementsByTagName("RoomsOccupants");
                            $RoomOccupants = $RoomsOccupants->item(0)->getElementsByTagName("RoomOccupants");
                            $RoomRateOccupants = $RoomOccupants->item(0)->getElementsByTagName("RoomRateOccupants");
                            if ($RoomRateOccupants->length > 0) {
                                $OccupantsID = $RoomRateOccupants->item(0)->getAttribute("OccupantsID");
                                $IsImmediateConfirmation = $RoomRateOccupants->item(0)->getAttribute("IsImmediateConfirmation");
                                $TotalValueRate = $RoomRateOccupants->item(0)->getAttribute("TotalValue");
                                $Guest = $RoomRateOccupants->item(0)->getElementsByTagName("Guest");
                                if ($Guest->length > 0) {
                                    $Count = $Guest->item(0)->getAttribute("Count");
                                    $Age = $Guest->item(0)->getAttribute("Age");
                                    $AgeType = $Guest->item(0)->getAttribute("AgeType");
                                } else {
                                    $Count = "";
                                    $Age = "";
                                    $AgeType = "";
                                }

                                $AccommodationRate = $RoomRateOccupants->item(0)->getElementsByTagName("AccommodationRate");
                                if ($AccommodationRate->length > 0) {
                                    $Occupation = $AccommodationRate->item(0)->getAttribute("Occupation");

                                    $DailyRate = $AccommodationRate->item(0)->getElementsByTagName("DailyRate");
                                    if ($DailyRate->length > 0) {
                                        for ($d=0; $d < $DailyRate->length; $d++) { 
                                            $TotalValueAcc = $DailyRate->item($d)->getAttribute("TotalValue");
                                            $DailyValue = $DailyRate->item($d)->getAttribute("DailyValue");
                                            $End = $DailyRate->item($d)->getAttribute("End");
                                            $Start = $DailyRate->item($d)->getAttribute("Start");
                                            try {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('hotelavail_dailyRate');
                                                $insert->values(array(
                                                    'datetime_created' => time(),
                                                    'datetime_updated' => 0,
                                                    'TotalValue' => $TotalValueAcc,
                                                    'DailyValue' => $DailyValue,
                                                    'End' => $End,
                                                    'Start' => $Start,
                                                    'IDRoomstay' => $ID
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();
                                            } catch (\Exception $e) {
                                                echo $return;
                                                echo "ERRO DAILY: " . $e;
                                                echo $return;
                                            }
                                        }
                                    }

                                    $Guarantee = $AccommodationRate->item(0)->getElementsByTagName("Guarantee");
                                    if ($Guarantee->length > 0) {
                                        $Type = $Guarantee->item(0)->getAttribute("Type");
                                        $Percentage = $Guarantee->item(0)->getAttribute("Percentage");
                                        $Deadline = $Guarantee->item(0)->getAttribute("Deadline");
                                    } else {
                                        $Type = "";
                                        $Percentage = "";
                                        $Deadline = "";
                                    }

                                } else {
                                    $Occupation = "";
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('hotelavail_room');
                                    $insert->values(array(
                                        'IDRoomRate' => $IDRoomRate,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'FitGroup' => $FitGroup,
                                        'HasAllIncluded' => $HasAllIncluded,
                                        'HasBkftIncluded' => $StarRating,
                                        'HasFapIncluded' => $HasFapIncluded,
                                        'HasMapIncluded' => $HasMapIncluded,
                                        'MaxAccommodationRate' => $MaxAccommodationRate,
                                        'MinAccommodationRate' => $MinAccommodationRate,
                                        'CancelCost' => $CancelCost,
                                        'DailyCostCancel' => $DailyCostCancel,
                                        'DeadLineCancel' => $DeadLineCancel,
                                        'ChargingUnit' => $ChargingUnit,
                                        'TotalValue' => $TotalValue,
                                        'CurrencyCode' => $CurrencyCode,
                                        'MarketCode' => $MarketCode,
                                        'Comments' => $Comments,
                                        'RoomTypeCode' => $RoomTypeCode,
                                        'RoomTypeName' => $RoomTypeName,
                                        'OccupantsID' => $OccupantsID,
                                        'IsImmediateConfirmation' => $IsImmediateConfirmation,
                                        'TotalValueRate' => $TotalValueRate,
                                        'Count' => $Count,
                                        'Age' => $Age,
                                        'AgeType' => $AgeType,
                                        'Occupation' => $Occupation,
                                        'Type' => $Type,
                                        'Percentage' => $Percentage,
                                        'Deadline' => $Deadline,
                                        'IDRoomstay' => $ID
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO RATES: " . $e;
                                    echo $return;
                                }

                                echo "<br/>PASSOU ANTES<br/> ";
                                $OptionalServices = $RoomRateOccupants->item(0)->getElementsByTagName("OptionalServices");
                                if ($OptionalServices->length > 0) {
                                    $OptionalService = $OptionalServices->item(0)->getElementsByTagName("OptionalService");
                                    if ($OptionalService->length > 0) {
                                        $OptionalServiceCode = $OptionalService->item(0)->getAttribute("Code");
                                        $OptionalServiceName = $OptionalService->item(0)->getAttribute("Name");
                                        $OptionalServiceOccupation = $OptionalService->item(0)->getAttribute("Occupation");
                                        $OptionalServiceRateID = $OptionalService->item(0)->getAttribute("RateID");
                                        $OptionalServiceValue = $OptionalService->item(0)->getAttribute("Value");
                                        $OptionalServiceChargeUnit = $OptionalService->item(0)->getAttribute("ChargeUnit");
                                        $OptionalServiceCategoryCode = $OptionalService->item(0)->getAttribute("CategoryCode");

                                        $DailyRate = $OptionalService->item(0)->getElementsByTagName("DailyRate");
                                        if ($DailyRate->length > 0) {
                                            for ($op=0; $op < $DailyRate->length; $op++) { 
                                                $DailyRateTotalValue = $DailyRate->item($op)->getAttribute("TotalValue");
                                                $DailyRateDailyValue = $DailyRate->item($op)->getAttribute("DailyValue");
                                                $DailyRateEnd = $DailyRate->item($op)->getAttribute("End");
                                                $DailyRateStart = $DailyRate->item($op)->getAttribute("Start");

                                                try {
                                                    $sql = new Sql($db);
                                                    $insert = $sql->insert();
                                                    $insert->into('hotelavail_dailyRateOptional');
                                                    $insert->values(array(
                                                        'datetime_created' => time(),
                                                        'datetime_updated' => 0,
                                                        'OptionalServiceCode' => $OptionalServiceCode,
                                                        'OptionalServiceName' => $OptionalServiceName,
                                                        'OptionalServiceOccupation' => $OptionalServiceOccupation,
                                                        'OptionalServiceRateID' => $OptionalServiceRateID,
                                                        'OptionalServiceValue' => $OptionalServiceValue,
                                                        'OptionalServiceChargeUnit' => $OptionalServiceChargeUnit,
                                                        'OptionalServiceCategoryCode' => $OptionalServiceCategoryCode,
                                                        'TotalValue' => $DailyRateTotalValue,
                                                        'DailyValue' => $DailyRateDailyValue,
                                                        'End' => $DailyRateEnd,
                                                        'Start' => $DailyRateStart,
                                                        'IDRoomstay' => $ID
                                                    ), $insert::VALUES_MERGE);
                                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                                    $results = $statement->execute();
                                                    $db->getDriver()
                                                        ->getConnection()
                                                        ->disconnect();
                                                } catch (\Exception $e) {
                                                    echo $return;
                                                    echo "ERRO DAILY: " . $e;
                                                    echo $return;
                                                }
                                            }
                                        }

                                    } else {
                                        $OptionalServiceCode = "";
                                        $OptionalServiceName = "";
                                        $OptionalServiceOccupation = "";
                                        $OptionalServiceRateID = "";
                                        $OptionalServiceValue = "";
                                        $OptionalServiceChargeUnit = "";
                                        $OptionalServiceCategoryCode = "";
                                    }
                                }       
                            } else {
                                $OccupantsID = "";
                                $IsImmediateConfirmation = "";
                                $TotalValueRate = "";
                            }
                        } else {
                            $RoomTypeCode = "";
                            $RoomTypeName = "";
                        }

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