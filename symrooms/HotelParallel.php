<?php
error_log("\r\n Symrooms - Hotel Parallel Search\r\n", 3, "/srv/www/htdocs/error_log");

use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$hotellist = "";
$sql = "select sid from xmlhotels_mhotelbeds where hid=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $result = $statement->execute();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        if ($hotellist == "") {
            $hotellist = $row->sid;
        } else {
            $hotellist .= "," . $row->sid;
        }
    }
}
if ($hotellist != "") {
    $affiliate_id_hotelbeds = 0;
    $sql = "select value from settings where name='hotelbedsserviceURL' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsserviceURL = $row_settings["value"];
    }
    $sql = "select value from settings where name='hotelbedsuser' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsuser = $row_settings["value"];
    }
    $sql = "select value from settings where name='hotelbedspassword' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedspassword = base64_decode($row_settings["value"]);
    }
    $sql = "select value from settings where name='hotelbedsRestFulXMLJsonVersion' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsRestFulXMLJsonVersion = (int) $row_settings["value"];
    } else {
        $hotelbedsRestFulXMLJsonVersion = 0;
    }
    $sql = "select value from settings where name='hotelbedsMaxResultsPerformance' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsMaxResultsPerformance = (int) $row_settings["value"];
    } else {
        $hotelbedsMaxResultsPerformance = 9999;
    }
    $sql = "select value from settings where name='hotelbedsTimeout' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsTimeout = (int) $row_settings["value"];
    } else {
        $hotelbedsTimeout = 120;
    }
    $sql = "select value from settings where name='hotelbedslanguage' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedslanguage = $row_settings["value"];
    } else {
        $hotelbedslanguage = "";
    }
    $sql = "select value from settings where name='hotelbedsEnableLibeRate' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsEnableLibeRate = (int) $row_settings["value"];
    } else {
        $hotelbedsEnableLibeRate = 0;
    }
    $sql = "select value from settings where name='hotelbedsEnableOpaqueProducts' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsEnableOpaqueProducts = (int) $row_settings["value"];
    } else {
        $hotelbedsEnableOpaqueProducts = 0;
    }
    $sql = "select value from settings where name='hotelbedsMarkup' and affiliate_id=$affiliate_id_hotelbeds" . $branch_filter;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $hotelbedsMarkup = (int) $row_settings["value"];
    }
    $sfilter = array();
    $signature = hash("sha256", $hotelbedsuser . $hotelbedspassword . time());
    $endpoint = $hotelbedsserviceURL . "hotel-api/1.0/hotels";
    if ($nationality > 0) {
        $sql = "select iso_code_2 from countries where id=" . $nationality;
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $sourceMarket = $row_settings["iso_code_2"];
        } else {
            $sourceMarket = "";
        }
    } else {
        $sourceMarket = "";
    }

    $pesquisa = "hotel_id";
   
    $url = "https://api.travelgatex.com/";
    if ($pesquisa == "hotel_id") {
        $raw = '{"query":"{\n  hotelX {\n    search( criteria: {\n                checkIn: \"2019-11-19\",\n                checkOut: \"2019-11-20\",\n                hotels: [\"1\"],\n                occupancies: [ {paxes: [{age: 30}, {age: 30}]}]},\n                settings: {\n                      client: \"Demo_Client\",\n                      testMode: true,\n                      context: \"HOTELTEST\"}) {\n      options {\n        id\n        supplierCode\n        hotelCode\n        hotelName\n        boardCode\n    paymentType\n    status\n    rooms {\n    occupancyRefId\n     code\n   description\n    refundable\n    units\n    roomPrice {\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n    }\n    }\n    }\n  beds {\n    type\n    description\n    count\n    shared\n    }\n    ratePlans {\n    code\n    name\n    effectiveDate\n  expireDate\n   }\n    promotions {\n    code\n    name\n    effectiveDate\n    expireDate\n  }\n  }\n  supplements {\n   code\n    name\n    description\n    supplementType\n    chargeType\n    mandatory\n    durationType\n    quantity\n    unit\n    effectiveDate\n    expireDate\n    resort {\n    code\n    name\n    description\n    }\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n   surcharges {\n    chargeType\n    description\n    price {\n    currency\n    binding\n    net\n    gross\n    exchange {\n    currency\n    rate\n  }\n  }\n  }\n    rateRules \n    cancelPolicy {\n    refundable\n    cancelPenalties {\n    hoursBefore\n    penaltyType\n    currency\n   value\n  }\n  }\n      price {\n          net\n          currency\n        }\n    remarks\n    token\n    id\n      }\n      errors {\n        code\n        type\n        description\n      }\n      warnings {\n        code\n        type\n        description\n      }\n    }\n  }\n}"}';
    } else {
        $raw = '{"query":"{\n  hotelX {\n  search(\n  criteria: {\n  checkIn: \"2019-12-23\",\n  checkOut: \"2019-12-24\",\n  occupancies: [{\n paxes: [\n  {age: 30}, {age: 30}]}],\n  language: \"es\",\n  nationality: \"ES\",\n  currency: \"EUR\",\n  market: \"ES\",\n  destinations: [\"1149\", \"AD\", \"31256\"]\n  },\n  settings: {\n  suppliers: {\n code: \"HOTELTEST\"},\n  plugins: {\n  step: REQUEST,\n  pluginsType: [{\n  type: POST_STEP,\n  name: \"search_by_destination\",\n  parameters: [{\n  key: \"accessID\",\n  value: \"422\"}]\n }]\n  },\n  businessRules: null,\n  timeout: 24700,\n  context: \"HOTELTEST\",\n  client: \"Demo_Client\",\n  testMode: true},\n  filter: {\n  access: {\n  includes: []\n  }\n  }) {\n  options {\n  surcharges {\n  chargeType\n  mandatory\n  description\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  accessCode\n  supplierCode\n  market\n  hotelCode\n  hotelName\n  boardCode\n  paymentType\n  status\n  occupancies {\n  id\n  paxes {\n  age\n  }\n  }\n  rooms {\n occupancyRefId\n  code\n  description\n  refundable\n  units\n  roomPrice {\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  breakdown {\n  effectiveDate\n  expireDate\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  beds {\n  type\n  description\n  count\n  shared\n  }\n  ratePlans {\n  code\n  name\n  effectiveDate\n  expireDate\n  }\n  promotions {\n  code\n  name\n  effectiveDate\n  expireDate\n  }\n  }\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  addOns {\n  distribute\n  }\n  supplements {\n  code\n  name\n  description\n  supplementType\n  chargeType\n  mandatory\n  durationType\n  quantity\n  unit\n  effectiveDate\n  expireDate\n  resort {\n  code\n  name\n  description\n  }\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  surcharges {\n  chargeType\n  description\n  price {\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  markups {\n  channel\n  currency\n  binding\n  net\n  gross\n  exchange {\n  currency\n  rate\n  }\n  }\n  }\n  }\n  rateRules\n cancelPolicy {\n  refundable\n  cancelPenalties {\n  hoursBefore\n  penaltyType\n  currency\n  value\n  }\n  }\n  remarks\n  token\n  id\n  }\n  }\n  }\n  }"}';
    }
    if ($hotelbedsTimeout == 0) {
        $hotelbedsTimeout = 120;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Apikey 64780338-49c8-4439-7c7d-d03c2033b145',
        'Accept-Encoding: gzip, deflate, br',
        'Content-Type: application/json',
        'Accept: application/json',
        'Connection: keep-alive',
        'DNT: 1',
        'Origin: https://api.travelgatex.com'
    ));
    
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $hotelbedsTimeout);
    curl_setopt($ch, CURLOPT_TIMEOUT, $hotelbedsTimeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_multi_add_handle($multiParallel, $ch);
    $requestsParallel[$nC] = 'symrooms';
    $channelsParallel[$nC] = $ch;
    $nC ++;
}

?>