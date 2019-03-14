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
echo "COMECOU SEARCH<br/>";
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
$sql = "select value from settings where name='enablecitytours' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_citytours = $affiliate_id;
} else {
    $affiliate_id_citytours = 0;
}
$sql = "select value from settings where name='citytoursID' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursID = $row_settings['value'];
}
echo "<br/>citytoursID: " . $citytoursID;
$sql = "select value from settings where name='citytoursPassword' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursPassword = base64_decode($row_settings['value']);
}
echo "<br/>citytoursPassword: " . $citytoursPassword;
$sql = "select value from settings where name='citytoursServiceURL' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursServiceURL = $row_settings['value'];
}
echo "<br/>citytoursServiceURL: " . $citytoursServiceURL;
$sql = "select value from settings where name='citytoursAgencyCode' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursAgencyCode = $row_settings['value'];
}
echo "<br/>citytoursAgencyCode: " . $citytoursAgencyCode;
$sql = "select value from settings where name='citytoursSystem' and affiliate_id=$affiliate_id_citytours";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $citytoursSystem = $row_settings['value'];
}
echo "<br/>citytoursSystem: " . $citytoursSystem;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '<?xml version="1.0" encoding="utf-8"?><soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Body>
  <HotelSearch xmlns="http://tempuri.org/">
  <OTA_HotelSearchAvailRQ xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" Version="0" xmlns="http://www.opentravel.org/OTA/2003/05">
  <POS>
    <Source>
      <RequestorID Type="TD" ID="TESTID" />
      <TPA_Extensions>
        <TPA_Extensions xmlns="">
          <Provider>
            <System>' . $citytoursSystem . '</System>
            <Userid>' . $citytoursID . '</Userid>
            <Password>' . $citytoursPassword . '</Password>
            <AgencyCode>' . $citytoursAgencyCode . '</AgencyCode>
          </Provider>
        </TPA_Extensions>
      </TPA_Extensions>
    </Source>
  </POS>
  <Criteria>
    <Criterion>
      <Address>
        <CityName>new york city</CityName>
        <StateProv>new york</StateProv>
        <CountryName>united states</CountryName>
      </Address>
    </Criterion>
  </Criteria>
  <AvailRequestSegments>
    <AvailRequestSegment>
      <StayDateRange Start="2019-05-16" End="2019-05-17"/>
      <RoomStayCandidates>
        <RoomStayCandidate Quantity="2">
          <GuestCounts>
            <GuestCount AgeQualifyingCode="10" Count="2" />
          </GuestCounts>
        </RoomStayCandidate>
        <RoomStayCandidate Quantity="2">
          <GuestCounts>
            <GuestCount AgeQualifyingCode="10" Count="2" />
          </GuestCounts>
        </RoomStayCandidate>
      </RoomStayCandidates>
    </AvailRequestSegment>
  </AvailRequestSegments>
</OTA_HotelSearchAvailRQ>
</HotelSearch>
  </soap:Body>
</soap:Envelope>';
echo "<br/>" . $raw;

$headers = array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "SOAPAction: http://tempuri.org/HotelSearch",
    "Content-length: ".strlen($raw),
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $citytoursServiceURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw); // the SOAP request
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<br/>RESPONSE";
/* echo $return;
echo $response;
echo $return; */
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.citytours.php');
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
$HotelSearchResponse = $Body->item(0)->getElementsByTagName("HotelSearchResponse");
$OTA_HotelSearchAvailRS = $HotelSearchResponse->item(0)->getElementsByTagName("OTA_HotelSearchAvailRS");
$AvailResponseSegment = $OTA_HotelSearchAvailRS->item(0)->getElementsByTagName("AvailResponseSegment");
$BrokerCode = $AvailResponseSegment->item(0)->getAttribute("BrokerCode");

$HotelBlocks = $AvailResponseSegment->item(0)->getElementsByTagName("HotelBlocks");
$node = $HotelBlocks->item(0)->getElementsByTagName("HotelBlock");
echo $return;
echo "TAM: " . $node->length;
echo $return;
for ($i = 0; $i < $node->length; $i++) {
    $BasicPropertyInfo = $node->item($i)->getElementsByTagName("BasicPropertyInfo");
    if ($BasicPropertyInfo->length > 0) {
        $HotelName = $BasicPropertyInfo->item(0)->getElementsByTagName("HotelName");
        if ($HotelName->length > 0) {
            $HotelName = $HotelName->item(0)->nodeValue;
        } else {
            $HotelName = "";
        }
        $HotelCode = $BasicPropertyInfo->item(0)->getElementsByTagName("HotelCode");
        if ($HotelCode->length > 0) {
            $HotelCode = $HotelCode->item(0)->nodeValue;
        } else {
            $HotelCode = "";
        }
        $HotelID = $BasicPropertyInfo->item(0)->getElementsByTagName("HotelID");
        if ($HotelID->length > 0) {
            $HotelID = $HotelID->item(0)->nodeValue;
        } else {
            $HotelID = "";
        }
        $Address = $BasicPropertyInfo->item(0)->getElementsByTagName("Address");
        if ($Address->length > 0) {
            $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
            if ($AddressLine->length > 0) {
                $AddressLine = $AddressLine->item(0)->nodeValue;
            } else {
                $AddressLine = "";
            }
            $CityName = $Address->item(0)->getElementsByTagName("CityName");
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            $PostalCode = $Address->item(0)->getElementsByTagName("PostalCode");
            if ($PostalCode->length > 0) {
                $PostalCode = $PostalCode->item(0)->nodeValue;
            } else {
                $PostalCode = "";
            }
            $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
            if ($StateProv->length > 0) {
                $StateProv = $StateProv->item(0)->nodeValue;
            } else {
                $StateProv = "";
            }
            $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
            if ($CountryName->length > 0) {
                $Code = $CountryName->item(0)->getAttribute("Code");
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }
        } else {
            $CityName = "";
        }

        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('hoteis');
            $select->where(array(
                'HotelID' => $HotelID
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $result = $statement->execute();
            $result->buffer();
            $customers = array();
            if ($result->valid()) {
                $data = $result->current();
                $id = (int) $data['hoteis'];
                if ($id > 0) {
                    $sql = new Sql($db);
                    $data = array(
                        'HotelID' => $HotelID,
                        'datetime_created' => time(),
                        'datetime_updated' => 1,
                        'HotelName' => $HotelName,
                        'HotelCode' => $HotelCode,
                        'AddressLine' => $AddressLine,
                        'CityName' => $CityName,
                        'PostalCode' => $PostalCode,
                        'StateProv' => $StateProv,
                        'CountryName' => $CountryName,
                        'Code' => $Code,
                        'BrokerCode' => $BrokerCode
                        );
                        $where['HotelID = ?']  = $HotelID;
                    $update = $sql->update('hoteis', $data, $where);
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();   
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hoteis');
                    $insert->values(array(
                        'HotelID' => $HotelID,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'HotelName' => $HotelName,
                        'HotelCode' => $HotelCode,
                        'AddressLine' => $AddressLine,
                        'CityName' => $CityName,
                        'PostalCode' => $PostalCode,
                        'StateProv' => $StateProv,
                        'CountryName' => $CountryName,
                        'Code' => $Code,
                        'BrokerCode' => $BrokerCode
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
                $insert->into('hoteis');
                $insert->values(array(
                    'HotelID' => $HotelID,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'HotelName' => $HotelName,
                    'HotelCode' => $HotelCode,
                    'AddressLine' => $AddressLine,
                    'CityName' => $CityName,
                    'PostalCode' => $PostalCode,
                    'StateProv' => $StateProv,
                    'CountryName' => $CountryName,
                    'Code' => $Code,
                    'BrokerCode' => $BrokerCode
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            }
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO: " . $e;
            echo $return;
        }

    }
    //RoomStays
    $RoomStays = $node->item($i)->getElementsByTagName("RoomStays");
    if ($RoomStays->length > 0) {
        $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
        if ($RoomStay->length > 0) {
            for ($iAux=0; $iAux < $RoomStay->length; $iAux++) { 
                $Configuration = $RoomStay->item($iAux)->getAttribute("Configuration");
                $AvailableIndicator = $RoomStay->item($iAux)->getAttribute("AvailableIndicator");
                $AvailabilityID = $RoomStay->item($iAux)->getAttribute("AvailabilityID");

                //RoomTypes
                $RoomTypes = $RoomStay->item($iAux)->getElementsByTagName("RoomTypes");
                $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                if ($RoomType->length > 0) {
                    $RoomID = $RoomType->item(0)->getAttribute("RoomID");
                    $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                    $RoomType = $RoomType->item(0)->getAttribute("RoomType");
                    $NumberOfUnits = $RoomType->item(0)->getAttribute("NumberOfUnits");
                    $RoomDescription = $RoomType->item(0)->getElementsByTagName("RoomDescription");
                    if ($RoomDescription->length > 0) {
                        $RoomDescription = $RoomDescription->item(0)->nodeValue;
                    }else {
                        $RoomDescription = "";
                    }
                }

                //RatePlans
                $RatePlans = $RoomStay->item($iAux)->getElementsByTagName("RatePlans");
                $RatePlan = $RatePlans->item(0)->getElementsByTagName("RatePlan");
                if ($RatePlan->length > 0) {
                    $ExistsMeals = $RatePlan->item(0)->getAttribute("ExistsMeals");
                    $ContractID = $RatePlan->item(0)->getAttribute("ContractID");
                    $RatePlanID = $RatePlan->item(0)->getAttribute("RatePlanID");
                    $DailyPrice = $RatePlan->item(0)->getAttribute("DailyPrice");
                    $Date = $RatePlan->item(0)->getAttribute("Date");
                    $RateResortFee = $RatePlan->item(0)->getAttribute("RateResortFee");
                }

                //RoomRates
                $RoomRates = $RoomStay->item($iAux)->getElementsByTagName("RoomRates");
                $RoomRate = $RatePlans->item(0)->getElementsByTagName("RoomRate");
                if ($RoomRate->length > 0) {
                    $NumberOfUnitsRoomRate = $RoomRate->item(0)->getAttribute("NumberOfUnits");
                    $RatePlanCode = $RoomRate->item(0)->getAttribute("RatePlanCode");
                    $BookingCode = $RoomRate->item(0)->getAttribute("BookingCode");

                    $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                    $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                    if ($Rate->length > 0) {
                        $ExpireDate = $Rate->item(0)->getAttribute("ExpireDate");
                        $EffectiveDate = $Rate->item(0)->getAttribute("EffectiveDate");

                        $Total = $Rate->item(0)->getElementsByTagName("Total");
                        if ($Total->length > 0) {
                            $CurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                            $AmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                            $AmountBeforeTax = $Total->item(0)->getAttribute("AmountBeforeTax");
                        } else {
                            $ExpireDate = "";
                            $ExpireDate = "";
                            $ExpireDate = "";
                        }
                        

                    } else {
                        $ExpireDate = "";
                        $EffectiveDate = "";
                    }

                }else {
                    $NumberOfUnits = "";
                    $RatePlanID = "";
                    $RatePlanID = "";
                }

                //TimeSpan
                $TimeSpan = $RoomStay->item($iAux)->getElementsByTagName("TimeSpan");
                if ($TimeSpan->length > 0) {
                    $IgnorarCutOff = $TimeSpan->item(0)->getAttribute("IgnorarCutOff");
                    $End = $TimeSpan->item(0)->getAttribute("End");
                    $Start = $TimeSpan->item(0)->getAttribute("Start");
                } else {
                    $NumberOfUnits = "";
                    $RatePlanID = "";
                    $RatePlanID = "";
                }
                
                //CancelPenalties
                $CancelPenalties = $RoomStay->item($iAux)->getElementsByTagName("CancelPenalties");
                $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                if ($CancelPenalty->length > 0) {
                    $NonRefundable = $CancelPenalty->item(0)->getAttribute("NonRefundable");
                    $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                    if ($Deadline->length > 0) {
                        $OffsetTimeUnit = $Deadline->item(0)->getAttribute("OffsetTimeUnit");
                        $AbsoluteDeadline = $Deadline->item(0)->getAttribute("AbsoluteDeadline");
                    } else {
                        $OffsetTimeUnit = "";
                        $AbsoluteDeadline = "";
                    }
                    $AmountPercent = $CancelPenalty->item(0)->getElementsByTagName("AmountPercent");
                    if ($AmountPercent->length > 0) {
                        $NmbrOfNights = $AmountPercent->item(0)->getAttribute("NmbrOfNights");
                    } else {
                        $NmbrOfNights = "";
                    }
                    $PenaltyDescription = $CancelPenalty->item(0)->getElementsByTagName("PenaltyDescription");
                    if ($PenaltyDescription->length > 0) {
                        $PenaltyDescription = $PenaltyDescription->item(0)->nodeValue;
                    } else {
                        $PenaltyDescription = "";
                    }
                } else {
                    $NonRefundable = "";
                }

                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('hoteis_rooms');
                    $select->where(array(
                        'RoomID' => $RoomID
                    ));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $result->buffer();
                    $customers = array();
                    if ($result->valid()) {
                        $data = $result->current();
                        $id = (int) $data['hoteis_rooms'];
                        if ($id > 0) {
                            $sql = new Sql($db);
                            $data = array(
                                'RoomID' => $RoomID,
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'RoomDescription' => $RoomDescription,
                                'RoomTypeCode' => $RoomTypeCode,
                                'RoomType' => $RoomType,
                                'NumberOfUnits' => $NumberOfUnits,
                                'ExistsMeals' => $ExistsMeals,
                                'ContractID' => $ContractID,
                                'RatePlanID' => $RatePlanID,
                                'DailyPrice' => $DailyPrice,
                                'Date' => $Date,
                                'RateResortFee' => $RateResortFee,
                                'NumberOfUnitsRoomRate' => $NumberOfUnitsRoomRate,
                                'RatePlanCode' => $RatePlanCode,
                                'BookingCode' => $BookingCode,
                                'CurrencyCode' => $CurrencyCode,
                                'AmountAfterTax' => $AmountAfterTax,
                                'AmountBeforeTax' => $AmountBeforeTax,
                                'IgnorarCutOff' => $IgnorarCutOff,
                                'End' => $End,
                                'Start' => $Start,
                                'NonRefundable' => $NonRefundable,
                                'OffsetTimeUnit' => $OffsetTimeUnit,
                                'AbsoluteDeadline' => $AbsoluteDeadline,
                                'NmbrOfNights' => $NmbrOfNights,
                                'PenaltyDescription' => $PenaltyDescription,
                                'Configuration' => $Configuration,
                                'AvailableIndicator' => $AvailableIndicator,
                                'AvailabilityID' => $AvailabilityID,
                                'HotelID' => $HotelID
                                );
                                $where['RoomID = ?']  = $RoomID;
                            $update = $sql->update('hoteis_rooms', $data, $where);
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();   
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hoteis_rooms');
                            $insert->values(array(
                                'RoomID' => $RoomID,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'RoomDescription' => $RoomDescription,
                                'RoomTypeCode' => $RoomTypeCode,
                                'RoomType' => $RoomType,
                                'NumberOfUnits' => $NumberOfUnits,
                                'ExistsMeals' => $ExistsMeals,
                                'ContractID' => $ContractID,
                                'RatePlanID' => $RatePlanID,
                                'DailyPrice' => $DailyPrice,
                                'Date' => $Date,
                                'RateResortFee' => $RateResortFee,
                                'NumberOfUnitsRoomRate' => $NumberOfUnitsRoomRate,
                                'RatePlanCode' => $RatePlanCode,
                                'BookingCode' => $BookingCode,
                                'CurrencyCode' => $CurrencyCode,
                                'AmountAfterTax' => $AmountAfterTax,
                                'AmountBeforeTax' => $AmountBeforeTax,
                                'IgnorarCutOff' => $IgnorarCutOff,
                                'End' => $End,
                                'Start' => $Start,
                                'NonRefundable' => $NonRefundable,
                                'OffsetTimeUnit' => $OffsetTimeUnit,
                                'AbsoluteDeadline' => $AbsoluteDeadline,
                                'NmbrOfNights' => $NmbrOfNights,
                                'PenaltyDescription' => $PenaltyDescription,
                                'Configuration' => $Configuration,
                                'AvailableIndicator' => $AvailableIndicator,
                                'AvailabilityID' => $AvailabilityID,
                                'HotelID' => $HotelID
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
                        $insert->into('hoteis_rooms');
                        $insert->values(array(
                            'RoomID' => $RoomID,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'RoomDescription' => $RoomDescription,
                            'RoomTypeCode' => $RoomTypeCode,
                            'RoomType' => $RoomType,
                            'NumberOfUnits' => $NumberOfUnits,
                            'ExistsMeals' => $ExistsMeals,
                            'ContractID' => $ContractID,
                            'RatePlanID' => $RatePlanID,
                            'DailyPrice' => $DailyPrice,
                            'Date' => $Date,
                            'RateResortFee' => $RateResortFee,
                            'NumberOfUnitsRoomRate' => $NumberOfUnitsRoomRate,
                            'RatePlanCode' => $RatePlanCode,
                            'BookingCode' => $BookingCode,
                            'CurrencyCode' => $CurrencyCode,
                            'AmountAfterTax' => $AmountAfterTax,
                            'AmountBeforeTax' => $AmountBeforeTax,
                            'IgnorarCutOff' => $IgnorarCutOff,
                            'End' => $End,
                            'Start' => $Start,
                            'NonRefundable' => $NonRefundable,
                            'OffsetTimeUnit' => $OffsetTimeUnit,
                            'AbsoluteDeadline' => $AbsoluteDeadline,
                            'NmbrOfNights' => $NmbrOfNights,
                            'PenaltyDescription' => $PenaltyDescription,
                            'Configuration' => $Configuration,
                            'AvailableIndicator' => $AvailableIndicator,
                            'AvailabilityID' => $AvailabilityID,
                            'HotelID' => $HotelID
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    }
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO ROOM: " . $e;
                    echo $return;
                }

                //Messages
                $Message2 = "";
                $Messages = $RoomStay->item($iAux)->getElementsByTagName("Messages");
                $Message = $Messages->item(0)->getElementsByTagName("Message");
                if ($Message->length > 0) {
                    for ($iAux2=0; $iAux2 < $Message->length; $iAux2++) { 
                        $TypeMessage = $Message->item($iAux2)->getAttribute("TypeMessage");
                        $Message2 = $Message->item(0)->nodeValue;
                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hoteis_rmessages');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'Message' => $Message2,
                                'TypeMessage' => $TypeMessage,
                                'RoomID' => $RoomID
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO MESSAGE: " . $e;
                            echo $return;
                        }
                    }
                } else {
                    $Message = "";
                }
                
                
            }   
        }
    }
}

$RoomStayCandidates = $AvailResponseSegment->item(0)->getElementsByTagName("RoomStayCandidates");
if ($RoomStayCandidates->length > 0) {
    for ($j=0; $j < $RoomStayCandidates->length; $j++) { 
        $Quantity = $RoomStayCandidates->item($j)->getAttribute("Quantity");
        $Configuration = $RoomStayCandidates->item($j)->getAttribute("Configuration");
        $GuestCounts = $RoomStayCandidates->item($j)->getElementsByTagName("GuestCounts");
        if ($GuestCounts->length > 0) {
            $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
            if ($GuestCount->length > 0) {
                $Count = $GuestCount->item(0)->getAttribute("Count");
                $AgeQualifyingCode = $GuestCount->item(0)->getAttribute("AgeQualifyingCode");
            } else {
                $Count = "";
                $AgeQualifyingCode = "";
            }  
        }
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hoteis_candidates');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Quantity' => $Quantity,
                'Configuration' => $Configuration,
                'Count' => $Count,
                'AgeQualifyingCode' => $AgeQualifyingCode,
                'BrokerCode' => $BrokerCode
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO MESSAGE: " . $e;
            echo $return;
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>