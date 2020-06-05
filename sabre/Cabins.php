<?php
error_log("\r\nStart - Cabins - Cruises - Sabre\r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat);
$db = new \Laminas\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='cruisessabreuserID1'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabreuserID1 = $row_settings['value'];
}
$sql = "select value from settings where name='cruisessabreuserID2'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabreuserID2 = $row_settings['value'];
}
$sql = "select value from settings where name='cruisessabrepassword1'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabrepassword1 = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisessabrepassword2'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabrepassword2 = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='cruisessabreIPCC'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabreIPCC = $row_settings['value'];
}
$sql = "select value from settings where name='enablecruisessabre'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $enablecruisessabre = $row_settings['value'];
}
$sql = "select value from settings where name='cruisessabrewebservicesURL'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabrewebservicesURL = $row['value'];
}
$sql = "select value from settings where name='cruisessabremarkup'";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $cruisessabremarkup = (double) $row_settings['value'];
} else {
    $cruisessabremarkup = 0;
}
$sql = "select value from settings where name='cruisessabreb2cmarkup'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreb2cmarkup = $row['value'];
}
$sql = "select value from settings where name='cruisessabrePartyIDFrom'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabrePartyIDFrom = $row['value'];
}
$sql = "select value from settings where name='cruisessabrePartyIDTo'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabrePartyIDTo = $row['value'];
}
$sql = "select value from settings where name='cruisessabreaffiliates_id'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='cruisessabreParallelSearch'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreParallelSearch = $row['value'];
}
$sql = "select value from settings where name='cruisessabreClientID'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreClientID = $row['value'];
}
$sql = "select value from settings where name='cruisessabreClientSecret'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreClientSecret = $row['value'];
}
$sql = "select value from settings where name='cruisessabreConversationID'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreConversationID = $row['value'];
}
$sql = "select value from settings where name='cruisessabreCredentials'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreCredentials = $row['value'];
}
$sql = "select value from settings where name='cruisessabreIPCC'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreIPCC = $row['value'];
}
$sql = "select value from settings where name='cruisessabreTimeout'";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $cruisessabreTimeout = (int) $row['value'];
}
foreach ($data as $key => $value) {
    if ($quote == $value['quote_id']) {
        $cruise_line_id = $value['cruise_line_id'];
        error_log("\r\n cruise_line_id $cruise_line_id \r\n", 3, "/srv/www/htdocs/error_log");
        $cruise_destination_id = $value['cruise_line_id'];
        $ship_id = $value['ship']['id'];
        $vendorCode = $value['vendorCode'];
        $citycode = $value['citycode'];
        $token = $value['token'];
        foreach ($value['product_id'] as $productkey => $productvalue) {
            if ($productvalue == $product) {
                $sailing_id = $value['sailingid'][$productkey];
            }
        }
        break;
    }
}
if ($cruise_line_id != "") {
    $isstate = $tmpstate === 'true' ? true : false;
    $issenior = $senior === 'true' ? true : false;
    $isinterline = $interline === 'true' ? true : false;
    $ismilitary = $military === 'true' ? true : false;
    $ispassengernumber = $tmppassengernumber === 'true' ? true : false;

    $raw2 = '{
        "agencyPOS": {
          "pcc": "' . $cruisessabreIPCC . '",
          "currencyCode": "USD"
        },
        "sailingInfo": {
          "vendorCode": "' . $vendorCode . '",
          "voyageId": "' . $cruise_line_id . '",
          "agencyGroupId": "43562",
          "includeNonRefundablePromos": true,
          "fareCodeQualifier": {
            "cityCode": "' . $citycode . '",
            "fareCodeRulesFlag": true
          }
        },
        "reservationInfo": {
          "guestCount": 1
        }
      }';
    
    $headers2 = array(
        "Accept: application/json",
        "Content-Type: application/json",
        "Accept-Encoding: gzip",
        'Authorization: Bearer ' . $token,
    );
    
    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $cruisessabrewebservicesURL . "v1/cruise/offers/getFareCodes");
    curl_setopt($ch2, CURLOPT_HEADER, false);
    curl_setopt($ch2, CURLOPT_VERBOSE, false);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
    curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, $cruisessabreTimeout);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    $response2 = curl_exec($ch2);
    $error = curl_error($ch2);
    $headers = curl_getinfo($ch2);
    curl_close($ch2);
    $response2 = json_decode($response2, true);

    $fareCodeOptions = $response2['fareCodeOptions'];
    $additionalInfo = $fareCodeOptions['additionalInfo'];
    $portCharges = $additionalInfo['portCharges'];
    $surCharges = $additionalInfo['surCharges'];
    $transportationMode = $additionalInfo['transportationMode'];
    $currencyCode = $additionalInfo['currencyCode'];
    if ($surCharges != "") {
        $tax = $surCharges;
    } else {
        $tax = 0;
    }

    $raw = '{
        "agencyPOS": {
        "pcc": "' . $cruisessabreIPCC . '",
        "currencyCode": "USD"
        },
        "sailingInfo": {
        "vendorCode": "' . $vendorCode . '",
        "voyageId": "' . $cruise_line_id . '",
        "includeNonRefundablePromos": true,
        "categoryCodeQualifier": {
            "categoryMediaFlag": true,
            "selectedFareCodes": {
            "fareCodes": [
                "BVL"
            ],
            "cityCode": "' . $citycode . '"
            }
        }
        },
        "reservationInfo": {
            "guestCount": 1
        }
    }';

    $headers = array(
        "Accept: application/json",
        "Content-Type: application/json",
        "Accept-Encoding: gzip",
        'Authorization: Bearer ' . $token,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $cruisessabrewebservicesURL . "v1/cruise/offers/getCategoryCodes");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $cruisessabreTimeout);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    error_log("\r\n Response - $response \r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    try {
        $db = new \Laminas\Db\Adapter\Adapter($config);
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_sabre');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'Cabins.php',
            'errorline' => 0,
            'errormessage' => $raw,
            'sqlcontext' => $response,
            'errcontext' => ''
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    
    $fareCodeOptions = $response['fareCodeOptions'];
    if (count($fareCodeOptions) > 0) {
        for ($i=0; $i < count($fareCodeOptions); $i++) { 
            $fareCode = $fareCodeOptions[$i]['fareCode'];
            $fareCodeName = $fareCodeOptions[$i]['fareCodeName'];
            $fareCodeType = $fareCodeOptions[$i]['fareCodeType'];
            $transportationMode = $fareCodeOptions[$i]['transportationMode'];
            $transportationCityCode = $fareCodeOptions[$i]['transportationCityCode'];
            $minOccupancy = $fareCodeOptions[$i]['agencyGroupId'];
            $currencyCode = $fareCodeOptions[$i]['currencyCode'];
            $categoryOptions = $fareCodeOptions[$i]['categoryOptions'];
            if (count($categoryOptions) > 0) {
                for ($iAux=0; $iAux < count($categoryOptions); $iAux++) { 
                    $pricedCategoryCode = $categoryOptions[$iAux]['pricedCategoryCode'];
                    $berthedCategoryCode = $categoryOptions[$iAux]['berthedCategoryCode'];
                    $categoryName = $categoryOptions[$iAux]['categoryName'];
                    $categoryLocation = $categoryOptions[$iAux]['categoryLocation'];
                    $deckName = $categoryOptions[$iAux]['deckName'];
                    $groupSeqNo = $categoryOptions[$iAux]['groupSeqNo'];
                    $agencyGroupId = $categoryOptions[$iAux]['agencyGroupId'];
                    $shareType = $categoryOptions[$iAux]['shareType'];
                    $minOccupancy = $categoryOptions[$iAux]['minOccupancy'];
                    $maxOccupancy = $categoryOptions[$iAux]['maxOccupancy'];
                    $gtyCategory = $categoryOptions[$iAux]['gtyCategory'];
                    $categoryEligibility = $categoryOptions[$iAux]['categoryEligibility'];
                    $categoryHref = $categoryOptions[$iAux]['categoryHref'];
                    $status = $categoryOptions[$iAux]['status'];
                    $airInfo = $categoryOptions[$iAux]['airInfo'];
                    $cityName = $airInfo['cityName'];
                    $availabilityInd = $airInfo['availabilityInd'];
                    $airClass = $airInfo['airClass'];
                    if (count($airClass) > 0) {
                        for ($iAux2=0; $iAux2 < count($airClass); $iAux2++) { 
                            $amount = $airClass[$iAux2]['amount'];
                            $classCode = $airClass[$iAux2]['classCode'];
                            $journeyType = $airClass[$iAux2]['journeyType'];
                        }
                    }
                    $mediaContentInfo = $categoryOptions[$iAux]['mediaContentInfo'];
                    $deckMediaInfo = $mediaContentInfo['deckMediaInfo'];
                    $sideView = $deckMediaInfo['sideView'];
                    $topView = $deckMediaInfo['topView'];
                    $categoryMediaInfo = $deckMediaInfo['categoryMediaInfo'];
                    $name = $categoryMediaInfo['name'];
                    $image = $categoryMediaInfo['image'];
                    $floorPlan = $categoryMediaInfo['floorPlan'];
                    $shortDesc = $categoryMediaInfo['shortDesc'];
                    $fullDesc = $categoryMediaInfo['fullDesc'];
                    $icon = $categoryMediaInfo['icon'];
                    $iconColor = $categoryMediaInfo['iconColor'];
                    $minBalconyArea = $categoryMediaInfo['minBalconyArea'];
                    $maxBalconyArea = $categoryMediaInfo['maxBalconyArea'];
                    $relatedCategories = $categoryMediaInfo['relatedCategories'];
                    $groupName = $categoryMediaInfo['groupName'];

                    try {
                        $db = new \Laminas\Db\Adapter\Adapter($config);
                        $sql = "select name, image, description, stateroom_area, veranda_area, color from ships_cabincategory where ship_id=" . $ship_id . " and categorycode='" . $pricedCategoryCode . "'";
                        $statement2 = $db->createStatement($sql);
                        $statement2->prepare();
                        $row_cabincategory = $statement2->execute();
                        if ($row_cabincategory->valid()) {
                            $row_cabincategory = $row_cabincategory->current();
                            $Name = $row_cabincategory["name"];
                            $img = $row_cabincategory["image"];
                            $stateroom_area = $row_cabincategory["stateroom_area"];
                            $veranda_area = $row_cabincategory["veranda_area"];
                            $color = $row_cabincategory["color"];
                            $description = $row_cabincategory["description"];
                        }
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $cabins[$cabinscount]['code'] = $pricedCategoryCode;
                    $cabins[$cabinscount]['name'] = $Name;
                    $cabins[$cabinscount]['type'] = $type;
                    $cabins[$cabinscount]['description'] = $description;
                    $cabins[$cabinscount]['deckname'] = $deckName;
                    $cabins[$cabinscount]['img'] = $img;
                    $cabins[$cabinscount]['isguaranteed'] = "";
                    $cabins[$cabinscount]['clxpolicy'] = "";
                    $cabins[$cabinscount]['stateroom_area'] = $stateroom_area;
                    $cabins[$cabinscount]['veranda_area'] = $veranda_area;
                    $cabins[$cabinscount]['color'] = $color;
                    $cabincountprice = 0;

                    $priceInfos = $categoryOptions[$iAux]['priceInfos'];
                    if (count($priceInfos) > 0) {
                        for ($iAux3=0; $iAux3 < count($priceInfos); $iAux3++) { 
                            $amount = $priceInfos[$iAux3]['amount'];
                            $breakDownType = $priceInfos[$iAux3]['breakDownType'];
                            $fareCode = $priceInfos[$iAux3]['fareCode'];
                            $fareCodeName = $priceInfos[$iAux3]['fareCodeName'];
                            $fareCodeType = $priceInfos[$iAux3]['fareCodeType'];
                            $nonRefundableType = $priceInfos[$iAux3]['nonRefundableType'];
                            $status = $priceInfos[$iAux3]['status'];
                            $pricenet = $amount;
                            $currencyCode = "USD";
                            $taxnet = $tax;

                            if ($cruisessabremarkup > 0) {
                                $amount = number_format($amount + (($amount * $cruisessabremarkup) / 100), 2, '.', '');
                            }
                            if ($agent_markup > 0) {
                                $amount = number_format($amount + (($amount * $agent_markup) / 100), 2, '.', '');
                            }
                            if ($cruisessabremarkup > 0) {
                                $tax = number_format($tax + (($tax * $cruisessabremarkup) / 100), 2, '.', '');
                            }
                            if ($agent_markup > 0) {
                                $tax = number_format($tax + (($tax * $agent_markup) / 100), 2, '.', '');
                            }
                            if ($currencyCode != $scurrency) {
                                $amount = $CurrencyConverter->convert($amount, $currencyCode, $scurrency);
                                $tax = $CurrencyConverter->convert($tax, $currencyCode, $scurrency);
                                $pricepublish = $CurrencyConverter->convert($pricepublish, $currencyCode, $scurrency);
                            }

                            $promoInfos = $priceInfos[$iAux3]['promoInfos'];
                            if (count($promotionInfos) > 0) {
                                for ($iAux2=0; $iAux2 < count($promotionInfos); $iAux2++) { 
                                    $promoType = $promotionInfos[$iAux2]['promoType'];
                                    $promoValue = $promotionInfos[$iAux2]['promoValue'];
                                }
                            }
                            $priceInfoTransportationOverrides = $priceInfos[$iAux3]['priceInfoTransportationOverrides'];
                            if (count($priceInfoTransportationOverrides) > 0) {
                                for ($iAux4=0; $iAux4 < count($priceInfoTransportationOverrides); $iAux4++) { 
                                    $transportationMode = $priceInfoTransportationOverrides[$iAux4]['transportationMode'];
                                    $priceInfosamount = $priceInfoTransportationOverrides[$iAux4]['amount'];
                                    $gtyCategory = $priceInfoTransportationOverrides[$iAux4]['gtyCategory'];
                                }
                            }
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['pricetitle'] = $pricetitle;
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['pricepublish'] = $filter->filter($amount);
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['price'] = $filter->filter($amount);
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['pricenet'] = $pricenet;
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['upgradetocategorycode'] = $upgradetocategorycode;
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['cabinproductid'] = base64_encode($pricedCategoryCode);
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['nonrefundable'] = $nonRefundableType;
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['tax'] = $tax;
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['taxnet'] = $taxnet;
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['currencynet'] = $currencyCode;
                            $cabins[$cabinscount]['cabin'][$cabincountprice]['currency'] = $scurrency;
                            $cabincountprice ++;
                        }
                    }
                    $promotionalAmenityInfos = $categoryOptions[$iAux]['promotionalAmenityInfos'];
                    if (count($promotionalAmenityInfos) > 0) {
                        for ($iAux5=0; $iAux5 < count($promotionalAmenityInfos); $iAux5++) { 
                            $autoIncluded = $promotionalAmenityInfos[$iAux5]['autoIncluded'];
                            $amenityCode = $promotionalAmenityInfos[$iAux5]['amenityCode'];
                            $amenityDesc = $promotionalAmenityInfos[$iAux5]['amenityDesc'];
                            $amount = $promotionalAmenityInfos[$iAux5]['amount'];
                            $voyageId = $promotionalAmenityInfos[$iAux5]['voyageId'];
                        }
                    }
                    $categoryAmenityInfos = $categoryOptions[$iAux]['categoryAmenityInfos'];
                    if (count($categoryAmenityInfos) > 0) {
                        $categoryAmenity = "";
                        for ($iAUX6=0; $iAUX6 < count($categoryAmenityInfos); $iAUX6++) { 
                            $categoryAmenity = $categoryAmenityInfos[$iAUX6];
                        }
                    }
                    $transportationOverrideInfos = $categoryOptions[$iAux]['transportationOverrideInfos'];
                    if (count($transportationOverrideInfos) > 0) {
                        for ($iAux7=0; $iAux7 < count($transportationOverrideInfos); $iAux7++) { 
                            $minOccupancy = $transportationOverrideInfos[$iAux7]['minOccupancy'];
                            $maxOccupancy = $transportationOverrideInfos[$iAux7]['maxOccupancy'];
                            $pricedCategoryCode = $transportationOverrideInfos[$iAux7]['pricedCategoryCode'];
                            $agencyGroupId = $transportationOverrideInfos[$iAux7]['agencyGroupId'];
                            $fareCode = $transportationOverrideInfos[$iAux7]['fareCode'];
                            $transportationMode = $transportationOverrideInfos[$iAux7]['transportationMode'];
                        }
                    }
                    $cabinscount ++;
                }
            }
        }
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>