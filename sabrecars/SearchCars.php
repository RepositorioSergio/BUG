<?php
$scurrency = strtoupper($currency);
$vehicle = array();
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
$sql = "select value from settings where name='enablesabre' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_sabre = $affiliate_id;
} else {
    $affiliate_id_sabre = 0;
}
$sql = "select value from settings where name='sabretravelnetworkcarsIPCC' and affiliate_id=$affiliate_id_sabre";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworkcarsIPCC = $row_settings["value"];
}
$sql = "select value from settings where name='sabretravelnetworkcarspassword1' and affiliate_id=$affiliate_id_sabre";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworkcarspassword1 = base64_decode($row_settings["value"]);
}
$sql = "select value from settings where name='sabretravelnetworkcarsCurrency' and affiliate_id=$affiliate_id_sabre";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworkcarsCurrency = $row_settings["value"];
} else {
    $sabretravelnetworkcarsCurrency = "52";
}
$sql = "select value from settings where name='sabretravelnetworkcarswebservicesURL' and affiliate_id=$affiliate_id_sabre";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworkcarswebservicesURL = $row_settings["value"];
}
$sql = "select value from settings where name='sabretravelnetworkcarsMarkup' and affiliate_id=$affiliate_id_sabre";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $sabretravelnetworkcarsMarkup = (double) $row_settings["value"];
}
$sql = "select code, name, city from carlocation where id=" . $pickup_id;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $pickup = strtolower($row_settings["code"]);
}
$sql = "select code, name, city from carlocation where id=" . $dropoff_id;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $dropoff = strtolower($row_settings["code"]);
}
error_log("\r\nSabre pickup: " . $pickup . "\r\n", 3, "/srv/www/htdocs/error_log");
error_log("\r\nSabre dropoff: " . $dropoff . "\r\n", 3, "/srv/www/htdocs/error_log");
$dateStart = new DateTime(strftime("%d-%m-%Y", $from));
$dateEnd = new DateTime(strftime("%d-%m-%Y", $to));
$numberofdays = $dateStart->diff($dateEnd)->format('%d');

if ($sabretravelnetworkcarsIPCC != "" and $sabretravelnetworkcarswebservicesURL != "" and $sabretravelnetworkcarspassword1 != "") {
    $from2 = strftime("%Y-%m-%d", $from) . "T" . $pickup_time;
    $to2 = strftime("%Y-%m-%d", $to) . "T" . $dropoff_time;
    $xmlrequest = '{
        "OTA_VehAvailRateRQ" : {
          "Version" : "2.4.1",
          "VehAvailRQCore" : {
            "QueryType" : "Quote",
            "VehRentalCore" : {
              "PickUpDateTime" : ' . $from2 . ',
              "ReturnDateTime" : ' . $to2 . ',
              "PickUpLocation" : {
                "LocationCode" : ' . $pickup . '
              }
            },
            "VendorPrefs" : {
              "VendorPref" : [ {
                "Code" : "ZE"
              } ]
            }
          }
        }
      }';
    
    error_log("\r\nEnd Point: " . $sabretravelnetworkcarswebservicesURL . "\r\n", 3, "/srv/www/htdocs/error_log");
    error_log("\r\nXML Request: " . $xmlrequest . "\r\n", 3, "/srv/www/htdocs/error_log");
    /*
     * function buildCredentials()
     * {
     * $credentials = $this->config["formatVersion"] . ":" . $this->config["userId"] . ":" . $this->config["group"] . ":" . $this->config["domain"];
     * $credentials = "V1:7971:IA8H:AA";
     * $secret = base64_encode($this->config["clientSecret"]);
     * return base64_encode(base64_encode($credentials) . ":" . $secret);
     * }
     */
    $credentials = $this->config["formatVersion"] . ":" . $this->config["userId"] . ":" . $this->config["group"] . ":" . $this->config["domain"];
    $credentials = "V1:7971:IA8H:AA";
    $secret = base64_encode($this->config["clientSecret"]);
    $cred = base64_encode(base64_encode($credentials) . ":" . $secret);
    error_log("\r\nXML cred: " . $cred . "\r\n", 3, "/srv/www/htdocs/error_log");
    
    // function getToken($config)
    // {
    // if (TokenHolder::$token == null || time() > TokenHolder::$expirationDate) {
    // $authCall = new Auth();
    // $authCall->config = $config;
    // TokenHolder::$token = $authCall->callForToken();
    // TokenHolder::$expirationDate = time() + TokenHolder::$token->expires_in;
    // }
    // return TokenHolder::$token;
    // }
    
    // function buildHeaders()
    // {
    // $headers = array(
    // 'Authorization: Bearer ' . TokenHolder::getToken($this->config)->access_token,
    // 'Accept: */*'
    // );
    // return $headers;
    // }
    
    // $headers1 = $this->buildHeaders();
    // error_log("\r\nXML headers1: " . $headers1 . "\r\n", 3, "/srv/www/htdocs/error_log");
    
    $ch = curl_init($this->config['environment'] . "/v2/auth/token");
    $vars = "grant_type=client_credentials";
    $headers = array(
        'Authorization: Basic ' . $cred,
        'Accept: */*',
        'Content-Type: application/x-www-form-urlencoded'
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    error_log("\r\n Result: " . $result . "\r\n", 3, "/srv/www/htdocs/error_log");
    
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $sabretravelnetworkcarswebservicesURL . 'v2.4.1/shop/cars');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_USERPWD, $sabretravelnetworkcarsIPCC . ":" . $sabretravelnetworkcarspassword1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Accept-Encoding: gzip",
        'Authorization: Bearer ' . $token
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    // error_log("\r\nPaulo - TODO - Autenticacao - Result: " . $response . "\r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_sabre_cars');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
            'errorline' => 0,
            'errormessage' => $sabretravelnetworkcarswebservicesURL . $xmlrequest,
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
    include "/srv/www/htdocs/ages.xml/src/App/Action/Sabre/SearchCarsDebug.php";
    error_log("\r\nResult: " . $response . "\r\n", 3, "/srv/www/htdocs/error_log");
    $array = json_decode($response, true);
    $OTA_VehAvailRateRS = $array['OTA_VehAvailRateRS'];
    $VehAvailRSCore = $OTA_VehAvailRateRS['VehAvailRSCore'];
    // VehRentalCore
    $VehRentalCore = $VehAvailRSCore['VehRentalCore'];
    $NumDays = $VehRentalCore['NumDays'];
    error_log("\r\n NumDays: " . $NumDays . "\r\n", 3, "/srv/www/htdocs/error_log");
    $NumHours = $VehRentalCore['NumHours'];
    $PickUpDateTime = $VehRentalCore['PickUpDateTime'];
    $ReturnDateTime = $VehRentalCore['ReturnDateTime'];
    $DropOffLocationDetails = $VehRentalCore['DropOffLocationDetails'];
    $LocationCodeDLD = $DropOffLocationDetails['LocationCode'];
    error_log("\r\n LocationCode: " . $LocationCode . "\r\n", 3, "/srv/www/htdocs/error_log");
    
    $LocationDetails = $VehRentalCore['LocationDetails'];
    $CounterLocation = $LocationDetails['CounterLocation'];
    $LocationCode = $LocationDetails['LocationCode'];
    $LocationName = $LocationDetails['LocationName'];
    $LocationOwner = $LocationDetails['LocationOwner'];
    error_log("\r\n LocationOwner: " . $LocationOwner . "\r\n", 3, "/srv/www/htdocs/error_log");
    $OperationSchedule = $LocationDetails['OperationSchedule'];
    $OperationTimes = $OperationSchedule['OperationTimes'];
    $OperationTime = $OperationTimes['OperationTime'];
    foreach ($OperationTime as $key => $valueOperationTime) {
        $Start = $valueOperationTime['Start'];
        $End = $valueOperationTime['End'];
        error_log("\r\n End: " . $End . "\r\n", 3, "/srv/www/htdocs/error_log");
    }
    
    // VehVendorAvails
    $VehVendorAvails = $VehAvailRSCore['VehVendorAvails'];
    $VehVendorAvail = $VehVendorAvails['VehVendorAvail'];
    foreach ($VehVendorAvail as $key => $valueVehVendorAvail) {
        $RPH = $valueVehVendorAvail['RPH'];
        $Vendor = $valueVehVendorAvail['Vendor'];
        $VendorCode = $Vendor['Code'];
        $VendorCompanyShortName = $Vendor['CompanyShortName'];
        error_log("\r\n VendorCompanyShortName: " . $VendorCompanyShortName . "\r\n", 3, "/srv/www/htdocs/error_log");
        $VendorParticipationLevel = $Vendor['ParticipationLevel'];
        error_log("\r\n VendorParticipationLevel: " . $VendorParticipationLevel . "\r\n", 3, "/srv/www/htdocs/error_log");
        
        $VehAvailCore = $valueVehVendorAvail['VehAvailCore'];
        $VehicleCharges = $VehAvailCore['VehicleCharges'];
        $VehicleCharge = $VehicleCharges['VehicleCharge'];
        $Amount = $VehicleCharge['Amount'];
        $CurrencyCode = $VehicleCharge['CurrencyCode'];
        $GuaranteeInd = $VehicleCharge['GuaranteeInd'];
        $AdditionalDayHour = $VehicleCharge['AdditionalDayHour'];
        $Day = $AdditionalDayHour['Day'];
        $DayCurrencyCode = $Day['CurrencyCode'];
        $DayMileageAllowance = $Day['MileageAllowance'];
        error_log("\r\n DayMileageAllowance: " . $DayMileageAllowance . "\r\n", 3, "/srv/www/htdocs/error_log");
        $DayRate = $Day['Rate'];
        error_log("\r\n DayRate: " . $DayRate . "\r\n", 3, "/srv/www/htdocs/error_log");
        $Hour = $AdditionalDayHour['Hour'];
        $HourCurrencyCode = $Hour['CurrencyCode'];
        $HourMileageAllowance = $Hour['MileageAllowance'];
        $HourRate = $Hour['Rate'];
        
        $Mileage = $VehicleCharge['Mileage'];
        $Allowance = $Mileage['Allowance'];
        $CurrencyCodeM = $Mileage['CurrencyCode'];
        $ExtraMileageCharge = $Mileage['ExtraMileageCharge'];
        $UnitOfMeasure = $Mileage['UnitOfMeasure'];
        $SpecialEquipTotalCharge = $VehicleCharge['SpecialEquipTotalCharge'];
        $CurrencyCodeSETC = $SpecialEquipTotalCharge['CurrencyCode'];
        $TotalCharge = $VehicleCharge['TotalCharge'];
        $TotalChargeAmount = $TotalCharge['Amount'];
        $TotalChargeCurrencyCode = $TotalCharge['CurrencyCode'];
        error_log("\r\n TotalChargeCurrencyCode: " . $TotalChargeCurrencyCode . "\r\n", 3, "/srv/www/htdocs/error_log");
        
        $RentalRate = $VehAvailCore['RentalRate'];
        $AvailabilityStatus = $RentalRate['AvailabilityStatus'];
        $RateCode = $RentalRate['RateCode'];
        $STM_RatePlan = $RentalRate['STM_RatePlan'];
        error_log("\r\n STM_RatePlan: " . $STM_RatePlan . "\r\n", 3, "/srv/www/htdocs/error_log");
        $Vehicle = $RentalRate['Vehicle'];
        $VehType = $Vehicle['VehType'][0];
        
        if (strlen($VehType) > 2) {
            $category = substr($VehType, - 4, 1);
            $type = substr($VehType, - 3, 1);
            $transmission = substr($VehType, - 2, 1);
            $airfuel = substr($VehType, - 1);
        } else {
            $category = substr($VehType, - 2, 1);
            $type = substr($VehType, - 1);
        }
        error_log("\r\n category: " . $category . "\r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n type: " . $type . "\r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n transmission: " . $transmission . "\r\n", 3, "/srv/www/htdocs/error_log");
        error_log("\r\n airfuel: " . $airfuel . "\r\n", 3, "/srv/www/htdocs/error_log");
        $sql = "select description from sabre_cars_category where code='$category'";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $categoryVeh = $row_settings["description"];
            $Name = $row_settings["description"];
        } else {
            $Name = "";
        }
        $sql = "select description from sabre_cars_type where code='$type'";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $typeVeh = $row_settings["description"];
        }
        try {
            $sql = "select description from sabre_cars_transmission where code='$transmission'";
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_settings = $statement->execute();
            $row_settings->buffer();
            if ($row_settings->valid()) {
                $row_settings = $row_settings->current();
                $transmitionVeh = $row_settings["description"];
            } else {
                $transmitionVeh = "";
            }
        } catch (Exception $e) {
            error_log("\r\n" . $e->getMessage() . "\r\n", 3, "/srv/www/htdocs/error_log");
        }
        $sql = "select description from sabre_cars_airfuel where code='$airfuel'";
        $statement = $db->createStatement($sql);
        $statement->prepare();
        $row_settings = $statement->execute();
        $row_settings->buffer();
        if ($row_settings->valid()) {
            $row_settings = $row_settings->current();
            $airfuelVeh = $row_settings["description"];
        } else {
            $airfuelVeh = "";
        }
        try {
            $sql = "select rentallocation from sabre_status where indicator='$AvailabilityStatus'";
            $statement = $db->createStatement($sql);
            $statement->prepare();
            $row_settings = $statement->execute();
            $row_settings->buffer();
            if ($row_settings->valid()) {
                $row_settings = $row_settings->current();
                $status = $row_settings["rentallocation"];
            } else {
                $status = $AvailabilityStatus;
            }
        } catch (Exception $e) {
            error_log("\r\n" . $e->getMessage() . "\r\n", 3, "/srv/www/htdocs/error_log");
            $status = $AvailabilityStatus;
        }
        $cars[$counter]['id'] = $counter;
        $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-18-" . $counter;
        $cars[$counter]['vendorpicture'] = "https://world-wide-web-servers.com/car/vendors/" . $VendorCode . ".gif";
        $cars[$counter]['vendorcode'] = $VendorCode;
        $cars[$counter]['vendor'] = $VendorCompanyShortName;
        $cars[$counter]['vendorshortname'] = $VendorCompanyShortName;
        $cars[$counter]['size'] = $PassengerQuantity;
        $cars[$counter]['doors'] = $typeVeh;
        $cars[$counter]['aircondition'] = $GuaranteeInd;
        $cars[$counter]['transmission'] = $transmitionVeh;
        $cars[$counter]['bags'] = $BaggageQuantity;
        $cars[$counter]['status'] = $status;
        $cars[$counter]['from'] = $from;
        $cars[$counter]['to'] = $to;
        $cars[$counter]['pickup'] = ucwords(strtolower($LocationName));
        $cars[$counter]['dropoff'] = ucwords(strtolower($LocationCodeDLD));
        $cars[$counter]['class'] = $typeVeh;
        $cars[$counter]['currency'] = $scurrency;
        $cars[$counter]['productId'] = $productId;
        $cars[$counter]['programId'] = $CarProgramId;
        $cars[$counter]['name'] = $Name;
        $cars[$counter]['picture'] = "https://www.world-wide-web-servers.com/static/cars/" . $category . ".png";
        $cars[$counter]['programname'] = $CompanyShortName;
        $cars[$counter]['coverage'] = $CoverageType;
        $cars[$counter]['netcurrency'] = $CurrencyCode;
        $cars[$counter]['netprice'] = $DayRate;
        // Total including VAT in renting country currency
        /*
         * if ($minPrice < $CarProgramPrice) {
         * $minPrice = $CarProgramPrice;
         * }
         * $minPrice = number_format($minPrice, 2, ".", "");
         * if ($sabretravelnetworkcarsMarkup != 0) {
         * $minPrice = $minPrice + (($minPrice * $sabretravelnetworkcarsMarkup) / 100);
         * }
         * if ($agent_markup != 0) {
         * $minPrice = $minPrice + (($minPrice * $agent_markup) / 100);
         * }
         * if ($CurrencyCode != "") {
         * if ($CurrencyCode != $scurrency) {
         * $minPrice = $CurrencyConverter->convert($minPrice, $CurrencyCode, $scurrency);
         * }
         * } else {
         * if ($currencyBase != "") {
         * if ($currencyBase != $scurrency) {
         * $minPrice = $CurrencyConverter->convert($minPrice, $CurrencyCode, $scurrency);
         * }
         * }
         * }
         */
        
        $dailytotal = $Amount / $NumDays;
        $dailytotal = number_format($dailytotal, 2, ".", "");
        $RateTotalAmount = number_format($Amount, 2, ".", "");
        $cars[$counter]['currency'] = $CurrencyCode;
        $cars[$counter]['total'] = $filter->filter($Amount);
        $cars[$counter]['dailytotal'] = $filter->filter($dailytotal);
        $cars[$counter]['Location'] = $LocationName;
        $cars[$counter]['LocationCode'] = $LocationCode;
        $cars[$counter]['VehicleCategory'] = $categoryVeh;
        /*
         * $cars[$counter]['dueatpickupplain'] = $DueAtPickup;
         * $cars[$counter]['dueatpickup'] = $filter->filter($DueAtPickup);
         * $cars[$counter]['dueatpickupcurrency'] = $filter->filter($DueAtPickupCurrency);
         */
        // Location
        // $cars[$counter]['special'] = 1;
        // $cars[$counter]['recommended'] = 1;
        $counter = $counter + 1;
        // }
    }
    //
    // Store Session
    //
    try {
        $sql = new Sql($db);
        $delete = $sql->delete();
        $delete->from('quote_session_sabre_cars');
        $delete->where(array(
            'session_id' => $session_id
        ));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('quote_session_sabre_cars');
        $insert->values(array(
            'session_id' => $session_id,
            'xmlrequest' => (string) $xmlrequest,
            'xmlresult' => (string) $response,
            'data' => base64_encode(serialize($cars)),
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\nDone - EOF Sabre\r\n", 3, "/srv/www/htdocs/error_log");
?>