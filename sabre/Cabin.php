<?php
error_log("\r\nStart - Cabin - Cruises - Sabre\r\n", 3, "/srv/www/htdocs/error_log");
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
        $cruise_destination_id = $value['cruise_destination_id'];
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
    $raw = '{
        "agencyPOS": {
            "pcc": "' . $cruisessabreIPCC . '",
            "currencyCode": "USD"
        },
        "sailingInfo": {
        "vendorCode": "' . $vendorCode . '",
        "voyageId": "' . $cruise_line_id . '",
        "selectedFareCodes": [
            "BVL"
        ],
        "selectedCategory": {
            "pricedCategoryCode": "' . $selectedcabin['code'] . '",
            "berthedCategoryCode": "' . $selectedcabin['code'] . '"
        }
        },
        "cabinQualifiers": {
            "cabinMediaFlag": true
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
    curl_setopt($ch, CURLOPT_URL, $cruisessabrewebservicesURL . "v1/cruise/offers/getCabins");
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
    //error_log("\r\n Response - $response \r\n", 3, "/srv/www/htdocs/error_log");
    $response = json_decode($response, true);
    try {
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
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }

    $reservationInfo = $response['reservationInfo'];
    $reservationId = $reservationInfo['reservationId'];
    $cabinOptions = $response['cabinOptions'];
    if (count($cabinOptions) > 0) {
        for ($i=0; $i < count($cabinOptions); $i++) { 
            $cabinCategoryCode = $cabinOptions[$i]['cabinCategoryCode'];
            $cabinNum = $cabinOptions[$i]['cabinNum'];
            $decks[$i]['cabinnumber'] = $cabinNum;
            $decks[$i]['deckname'] = $cabinOptions[$i]['deckName'];
            $decks[$i]['decknumber'] = $cabinOptions[$i]['deckNum'];
            $sql = "select image from ships_decksimages where ship_id=$ship_id and categorycode='" . $selectedcabin['code'] . "'";
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_settings = $statement->execute();
            $row_settings->buffer();
            if ($row_settings->valid()) {
                $row_settings = $row_settings->current();
                $decks[$i]['deckimg'] = $row_settings['image'];
            }
            $groupCabinInd = $cabinOptions[$i]['groupCabinInd'];
            $minOccupancy = $cabinOptions[$i]['minOccupancy'];
            $maxOccupancy = $cabinOptions[$i]['maxOccupancy'];
            $remark = $cabinOptions[$i]['remark'];
            $status = $cabinOptions[$i]['status'];
            $measurementInfo = $cabinOptions[$i]['measurementInfo'];
            $dimensionInfo = $measurementInfo['dimensionInfo'];
            $unitOfMeasure = $measurementInfo['unitOfMeasure'];
            $unitOfMeasureCode = $measurementInfo['unitOfMeasureCode'];
            $unitOfMeasureQuantity = $measurementInfo['unitOfMeasureQuantity'];
            $cabinConfiguration = $cabinOptions[$i]['cabinConfiguration'];
            $accessibleCabinInd = $cabinConfiguration['accessibleCabinInd'];
            $bathCode = $cabinConfiguration['bathCode'];
            $bedCode = $cabinConfiguration['bedCode'];
            $connectedCabinNum = $cabinConfiguration['connectedCabinNum'];
            $smokingAllowed = $cabinConfiguration['smokingAllowed'];
            $bedConfiguration = $cabinConfiguration['bedConfiguration'];
            if (count($bedConfiguration) > 0) {
                for ($iAux=0; $iAux < count($bedConfiguration); $iAux++) { 
                    $bedCode = $bedConfiguration[$iAux]['bedCode'];
                    $bedCount = $bedConfiguration[$iAux]['bedCount'];
                }
            }
            $cabinAmenity = $cabinConfiguration['cabinAmenity'];
            if (count($cabinAmenity) > 0) {
                $amenity = "";
                for ($iAux2=0; $iAux2 < count($cabinAmenity); $iAux2++) { 
                    $amenity = $cabinAmenity[$iAux2];
                }
            }
            $cabinLocation = $cabinConfiguration['cabinLocation'];
            if (count($cabinLocation) > 0) {
                $location = "";
                for ($iAux3=0; $iAux3 < count($cabinLocation); $iAux3++) { 
                    $location = $cabinLocation[$iAux3];
                }
            }

            $mediaContentInfo = $cabinOptions[$i]['mediaContentInfo'];
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

            $cabinMediaInfo = $categoryMediaInfo['cabinMediaInfo'];
            $image = $cabinMediaInfo['image'];
            $floorPlan = $cabinMediaInfo['floorPlan'];
            $totalArea = $cabinMediaInfo['totalArea'];
            $imageType = $cabinMediaInfo['imageType'];
            $imageCoordinates = $cabinMediaInfo['imageCoordinates'];
            $iconCoordinates = $cabinMediaInfo['iconCoordinates'];
        }
    }
    //
    // Dining Options
    //
    $raw2 = '{
        "agencyPOS": {
        "pcc": "' . $cruisessabreIPCC . '",
        "currencyCode": "USD"
        },
        "sailingInfo": {
        "vendorCode": "' . $vendorCode . '",
        "voyageId": "' . $cruise_line_id . '",
        "agencyGroupId": "43562",
        "selectedFareCodes": [
            "BVL"
        ],
        "selectedCategory": {
            "pricedCategoryCode": "' . $selectedcabin['code'] . '",
            "berthedCategoryCode": "' . $selectedcabin['code'] . '",
            "groupSeqNo": "1",
            "cabinNum": "' . $cabinNum . '"
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

    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $cruisessabrewebservicesURL . "v1/cruise/offers/getDiningOptions");
    curl_setopt($ch2, CURLOPT_HEADER, false);
    curl_setopt($ch2, CURLOPT_VERBOSE, false);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $raw2);
    curl_setopt($ch2, CURLOPT_CONNECTTIMEOUT, $cruisessabreTimeout);
    curl_setopt($ch2, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    $response2 = curl_exec($ch2);
    $error = curl_error($ch2);
    $headers = curl_getinfo($ch2);
    curl_close($ch2);

    $response2 = json_decode($response2, true);

    $reservationInfo = $response2['reservationInfo'];
    $reservationId = $reservationInfo['reservationId'];
    $diningTableInfo = $response2['diningTableInfo'];
    $smokingAllowed = $diningTableInfo['smokingAllowed'];
    $diningTableCodes = $diningTableInfo['diningTableCodes'];
    if (count($diningTableInfo) > 0) {
        $diningTable = "";
        for ($i=0; $i < count($diningTableInfo); $i++) { 
            $diningTable = $diningTableInfo[$i];
        }
    }
    $hasdining = false;
    $diningOptions = $response2['diningOptions'];
    if (count($diningOptions) > 0) {
        for ($j=0; $j < count($diningOptions); $j++) { 
            $hasdining = true;
            $dining[$j]['diningcode'] = $diningOptions[$j]['sitting'];
            $dining[$j]['diningname'] = $diningOptions[$j]['sittingInstance'];
            $sittingType = $diningOptions[$j]['sittingType'];
            $smokingAllowed = $diningOptions[$j]['smokingAllowed'];
            $crossReferencingAllowed = $diningOptions[$j]['crossReferencingAllowed'];
            $familyTimeIndicator = $diningOptions[$j]['familyTimeIndicator'];
            $prepaidGratuityRequired = $diningOptions[$j]['prepaidGratuityRequired'];
            $dining[$j]['status'] = $diningOptions[$j]['status'];
        }
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>