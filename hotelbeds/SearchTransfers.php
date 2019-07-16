<?php
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$abreu = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
error_log("\r\n COMECOU Hotelbeds \r\n", 3, "/srv/www/htdocs/error_log");
$dbHotelbeds = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml02 from cities where id=" . $destination;
$statement2 = $dbHotelbeds->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml02 = $row_settings["city_xml02"];
} else {
    $city_xml02 = "";
}
$transfer_count = 0;
$affiliate_id_hotelbeds = 0;
$sql = "select value from settings where name='hotelbedsTransfersuser' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersuser = $row_settings['value'];
}
$sql = "select value from settings where name='hotelbedsTransferspassword' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransferspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='hotelbedsTransfersMarkup' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersMarkup = (double) $row_settings['value'];
} else {
    $hotelbedsTransfersMarkup = 0;
}
// URL
$sql = "select value from settings where name='hotelbedsTransfersserviceURL' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersserviceURL = $row_settings['value'];
}
error_log("\r\n hotelbedsTransfersserviceURL: $hotelbedsTransfersserviceURL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='hotelbedsTransferslanguage' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransferslanguage = $row_settings['value'];
} else {
    $hotelbedsTransferslanguage = "EN";
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
if ($stype == 1) {
    // Return
    $dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
    $noOfNights = $dateStart->diff($dateEnd)->format('%d');
} else {
    // One Way
    $dateEnd = 0;
    $noOfNights = 0;
}
$date = new Datetime();
$timestamp = $date->format('U');
$token = md5(uniqid(rand(), true));
$rettime2 = str_replace(":", "", $rettime);
$arrtime2 = str_replace(":", "", $arrtime);

// error_log("\r\nHotelbeds Request: $xmlrequest \r\n", 3, "/srv/www/htdocs/error_log");
if ($hotelbedsTransfersserviceURL != "" and $hotelbedsTransfersuser != "" and $hotelbedsTransferspassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.test.hotelbeds.com/transfer-api/1.0/availability/en/from/ATLAS/1523/to/IATA/PMI/2019-10-28T12:15:11/2019-10-30T08:30:52/2/1/0');
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Api-key: qz8j9xgymx97tmd5srx94mru",
        "X-Signature: " . hash("sha256", "qz8j9xgymx97tmd5srx94mru" . time()),
        "Content-Type: application/json",
        "Accept: application/json",
        "Accept-Encoding: gzip"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    error_log("\r\n Hotelbeds Transfers Response: $response \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($dbHotelbeds);
        $insert = $sql->insert();
        $insert->into('log_hotelbedstransfers');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchTransfers.php',
            'errorline' => 0,
            'errormessage' => $hotelbedsTransfersserviceURL,
            'sqlcontext' => '',
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

    $response = json_decode($response, true);

    $search = $response['search'];
    $departure = $search['departure'];
    $departuredate = $departure['date'];
    $departuretime = $departure['time'];

    $date = str_replace('-', '/', $departuredate );
    $dateDep = date("d/m/Y", strtotime($date));

    $comeBack = $search['comeBack'];
    $comeBackdate = $comeBack['date'];
    $comeBacktime = $comeBack['time'];

    $date2 = str_replace('-', '/', $comeBackdate );
    $dateComeBack = date("d/m/Y", strtotime($date2));

    $occupancy = $search['occupancy'];
    $adults = $occupancy['adults'];
    $children = $occupancy['children'];
    $infants = $occupancy['infants'];

    $searchfrom = $search['from'];
    $codeSF = $searchfrom['code'];
    $descriptionSF = $searchfrom['description'];
    $typeSF = $searchfrom['type'];

    $searchto = $search['to'];
    $codeST = $searchto['code'];
    $descriptionST = $searchto['description'];
    $typeST = $searchto['type'];

    $services = $response['services'];

    for ($i = 0; $i < count($services); $i ++) {
        $id = $services[$i]['id'];
        $direction = $services[$i]['direction'];
        $transferType = $services[$i]['transferType'];
        $minPaxCapacity = $services[$i]['minPaxCapacity'];
        $maxPaxCapacity = $services[$i]['maxPaxCapacity'];
        $rateKey = $services[$i]['rateKey'];
        $factsheetId = $services[$i]['factsheetId'];


        $vehicle = $services[$i]['vehicle'];
        $codevehicle = $vehicle['code'];
        $namevehicle = $vehicle['name'];

        $category = $services[$i]['category'];
        $codecategory = $vehicle['code'];
        $namecategory = $vehicle['name'];

        $pickupInformation = $services[$i]['pickupInformation'];
        $date = $pickupInformation['date'];
        $time = $pickupInformation['time'];
        $minPaxCapacityPickup = $pickupInformation['minPaxCapacity'];
        $maxPaxCapacityPickup = $pickupInformation['maxPaxCapacity'];
        //from
        $from = $pickupInformation['from'];
        $codefrom = $from['code'];
        $descriptionfrom = $from['description'];
        $typefrom = $from['type'];
        //to
        $to = $pickupInformation['to'];
        $codeto = $to['code'];
        $descriptionto = $to['description'];
        $typeto = $to['type'];
        //pickup
        $pickup = $pickupInformation['pickup'];
        $address = $pickup['address'];
        $number = $pickup['number'];
        $town = $pickup['town'];
        $zip = $pickup['zip'];
        $description = $pickup['description'];
        $altitude = $pickup['altitude'];
        $latitude = $pickup['latitude'];
        $longitude = $pickup['longitude'];
        $pickupId = $pickup['pickupId'];
        $stopName = $pickup['stopName'];
        $image = $pickup['image'];
        $checkPickup = $pickup['checkPickup'];
        $mustCheckPickupTime = $checkPickup['mustCheckPickupTime'];
        $url = $checkPickup['url'];
        $hoursBeforeConsulting = $checkPickup['hoursBeforeConsulting'];

        $price = $services[$i]['price'];
        $totalAmount = $price['totalAmount'];
        $netAmount = $price['netAmount'];
        $currencyId = $price['currencyId'];

        //content
        $content = $services[$i]['content'];
        //vehicle
        $vehicle = $content['vehicle'];
        $codevehicle = $vehicle['code'];
        $namevehicle = $vehicle['name'];
        //category
        $category = $content['category'];
        $codecategory = $category['code'];
        $namecategory = $category['name'];

        //images
        $images = $content['images'];
        for ($j=0; $j < count($images); $j++) { 
            $url = $images[$j]['url'];
            $type = $images[$j]['type'];
        }
        //transferDetailInfo
        $transferDetailInfo = $content['transferDetailInfo'];
        for ($k=0; $k < count($transferDetailInfo); $k++) { 
            $idtransfer = $transferDetailInfo[$k]['id'];
            $name = $transferDetailInfo[$k]['name'];
            $descriptiontransferDetailInfo = $transferDetailInfo[$k]['description'];
            $typetransferDetailInfo = $transferDetailInfo[$k]['type'];
        }
        //customerTransferTimeInfo
        $customerTransferTimeInfo = $content['customerTransferTimeInfo'];
        for ($l=0; $l < count($customerTransferTimeInfo); $l++) { 
            $valueCustomer = $customerTransferTimeInfo[$l]['value'];
            $typeCustomer = $customerTransferTimeInfo[$l]['type'];
            $metricCustomer = $customerTransferTimeInfo[$l]['metric'];
        }
        //supplierTransferTimeInfo
        $supplierTransferTimeInfo = $content['supplierTransferTimeInfo'];
        for ($m=0; $m < count($supplierTransferTimeInfo); $m++) { 
            $value = $supplierTransferTimeInfo[$m]['value'];
            $type = $supplierTransferTimeInfo[$m]['type'];
            $metric = $supplierTransferTimeInfo[$m]['metric'];
        }
        //transferRemarks
        $transferRemarks = $content['transferRemarks'];
        for ($r=0; $r < count($transferRemarks); $r++) { 
            $typetransferRemarks = $transferRemarks[$r]['type'];
            $descriptiontransferRemarks = $transferRemarks[$r]['description'];
            $mandatory = $transferRemarks[$r]['mandatory'];
        }

        //cancellationPolicies
        $cancellationPolicies = $services[$i]['cancellationPolicies'];
        for ($s=0; $s < count($cancellationPolicies); $s++) { 
            $amount = $cancellationPolicies[$s]['amount'];
            $from = $cancellationPolicies[$s]['from'];
            $currencyId = $cancellationPolicies[$s]['currencyId'];
        }
        //links
        $links = $services[$i]['links'];
        for ($x=0; $x < count($links); $x++) { 
            $rel = $links[$x]['rel'];
            $href = $links[$x]['href'];
            $method = $links[$x]['method'];
        }
        error_log("\r\n namecategory: $namecategory \r\n", 3, "/srv/www/htdocs/error_log");
        
    
            // Formato correcto
            $transfers[$transfer_count]['id'] = md5(uniqid($session_id, true)) . "-" . $transfer_count . "-2";
            $transfers[$transfer_count]['adults'] = $adults;
            $transfers[$transfer_count]['children'] = $children;
            $transfers[$transfer_count]['infants'] = $infants;
            $transfers[$transfer_count]['arrdate'] = $dateDep;
            $transfers[$transfer_count]['arrtime'] = $departuretime;
            $transfers[$transfer_count]['retdate'] = $dateComeBack;
            $transfers[$transfer_count]['rettime'] = $comeBacktime;
            $transfers[$transfer_count]['departurepointcode'] = $codefrom;
            $transfers[$transfer_count]['arrivalpointcode'] = $codeto;
            $transfers[$transfer_count]['transfercode'] = $typetransferRemarks;
            $transfers[$transfer_count]['transferprice'] = $totalAmount;
            $transfers[$transfer_count]['transferprice_net'] = $netAmount;
            $transfers[$transfer_count]['departurepointtype'] = $descriptionto;
            $transfers[$transfer_count]['arrivalpointtype'] = $descriptionfrom;
            $transfers[$transfer_count]['discount'] = "0";
            $transfers[$transfer_count]['discountpercent'] = "0";
            $transfers[$transfer_count]['disclaimer'] = "0";
            if ($codeType == "P") {
                $transferdescription = $translator->translate("Private Transfer");
            } elseif ($codeType == "B") {
                $transferdescription = $translator->translate("Bus Transfer");
            } else {
                $transferdescription = $translator->translate("Unknown Transfer") . " - " . $codeType;
            }
            $transfers[$transfer_count]['image'] = $url;
            $transfers[$transfer_count]['transfertype'] = $transferType;
            if ($transferType == "TER") {
                $t = $translator->translate("Terminal to Terminal");
            } elseif ($transferType == "IN") {
                $t = $translator->translate("Transfer from pickup to destination");
            } elseif ($transferType == "OUT") {
                $t = $translator->translate("Return transfer from the destination to the pickup");
            } else {
                $t = $transferType;
            }
            $transfers[$transfer_count]['transferdescription'] = $descriptiontransferDetailInfo;
            $transfers[$transfer_count]['transfertype2'] = $transferType;
            $transfers[$transfer_count]['transferInfoCode'] = $transferType;
            $transfers[$transfer_count]['typeTransferInfo'] = $typeTransferInfo;
            
            $transfers[$transfer_count]['outboundorigin'] = $descriptionfrom;
            $transfers[$transfer_count]['outbounddestination'] = $descriptionto;
            $transfers[$transfer_count]['outboundjourneytime'] = $valueCustomer . " " . $metricCustomer;
            $transfers[$transfer_count]['outboundarrivaldate'] = $dateDep;
            $transfers[$transfer_count]['outboundarrivaltime'] = $departuretime;
            $transfers[$transfer_count]['outboundpickupdate'] = "";
            $transfers[$transfer_count]['outboundpickuptime'] = "";
            $transfers[$transfer_count]['distance'] = $distance;
            $transfers[$transfer_count]['duration'] = $valueCustomer . " " . $metricCustomer;
            $transfers[$transfer_count]['numberofvehicles'] = "1";
            $transfers[$transfer_count]['numberofbags'] = "1";
            $transfers[$transfer_count]['maxstops'] = $maxstops;
            $transfers[$transfer_count]['minstops'] = "0";
            $transfers[$transfer_count]['maxcapacity'] = $maxPaxCapacity;
            $transfers[$transfer_count]['mincapacity'] = $minPaxCapacity;
            if ($stype == 1) {
                $transfers[$transfer_count]['sectortype'] = "RETURN";
            } else {
                $transfers[$transfer_count]['sectortype'] = "SINGLE";
            }
            // 1=Shuttle, 2=Private
            $transfers[$transfer_count]['vehicletype'] = $codevehicle;
            $transfers[$transfer_count]['vehicle'] = $namevehicle;
            $transfers[$transfer_count]['vehicleid'] = "";
            $transfers[$transfer_count]['vehiclecode'] = $codevehicle;
            $transfers[$transfer_count]['numtransfers'] = ($adults + $children);
            $transfers[$transfer_count]['PRID'] = $transferType;
            $transfers[$transfer_count]['codeIncomingOffice'] = $codeIncomingOffice;
            $transfers[$transfer_count]['CodePickupLocation'] = $codefrom;
            $transfers[$transfer_count]['CodeDestinationLocation'] = $codeto;
            $transfers[$transfer_count]['NameContract'] = $NameContract;
            $transfers[$transfer_count]['codeType'] = $codeType;
            $transfers[$transfer_count]['dateFrom'] = $date;
            $transfers[$transfer_count]['dateTo'] = $date;
            // $transfers[$transfer_count]['duration_desc'] = convertToHoursMinsA2B($Duration, '%2d hour(s) and %02d minutes');
            $transfers[$transfer_count]['availToken'] = $availToken;
            $transfers[$transfer_count]['echoToken'] = $echoToken;
            $transfers[$transfer_count]['returnorigin'] = $descriptionto;
            $transfers[$transfer_count]['currency'] = $currencyId;
            $transfers[$transfer_count]['currencycode'] = $currencyId;
            $transfers[$transfer_count]['returndestination'] = $descriptionfrom;
            $transfers[$transfer_count]['returnpickuptime'] = $time;
            $transfers[$transfer_count]['returndeparturedate'] = $dateComeBack;
            $transfers[$transfer_count]['returndeparturetime'] = $comeBacktime;
            $transfers[$transfer_count]['returnpickupdate'] = $dateDep;
            $transfers[$transfer_count]['returnjourneytime'] = $valueCustomer . " " . $metricCustomer;
            $transfers[$transfer_count]['factsheetId'] = $factsheetId;
            $transfer_count ++;
            // EOF
        }

    try {
        $sql = new Sql($dbHotelbeds);
        $delete = $sql->delete();
        $delete->from('quote_session_hotelbedstransfers');
        $delete->where(array(
            'session_id' => $session_id
        ));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($dbHotelbeds);
        $insert = $sql->insert();
        $insert->into('quote_session_hotelbedstransfers');
        $insert->values(array(
            'session_id' => $session_id,
            'xmlrequest' => (string) $xmlrequest,
            'xmlresult' => (string) $xmlresult,
            'data' => base64_encode(serialize($transfers)),
            'searchsettings' => base64_encode(serialize($requestdata))
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
}
$dbHotelbeds->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\nHotelbeds transfers eof\r\n", 3, "/srv/www/htdocs/error_log");
?>