<?php
error_log("\r\n COMECOU SEARCHCARS \r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
$vehicle = array();
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$db = new \Laminas\Db\Adapter\Adapter($config);
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enableiterpecCars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_iterpec = $affiliate_id;
} else {
    $affiliate_id_iterpec = 0;
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement2 = $db->createStatement($sql);
    $statement2->prepare();
    $row_settings = $statement2->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='iterpecDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_iterpec";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings['value'];
    }
}
$sql = "select value from settings where name='iterpecCarsLogin' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsLogin = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarspassword' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='iterpecCarswebservicesURL' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $iterpecCarswebservicesURL = $row['value'];
}
$sql = "select value from settings where name='iterpecCarsmarkup' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsmarkup = (double) $row_settings['value'];
} else {
    $iterpecCarsmarkup = 0;
}
$sql = "select value from settings where name='iterpecCarsaffiliates_id' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsaffiliates_id = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarsb2cmarkup' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsb2cmarkup = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarsbranchs_id' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsbranchs_id = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarsSortorder' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsSortorder = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarsTimeout' and affiliate_id=$affiliate_id_iterpec";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsTimeout = (int)$row_settings['value'];
}
$sql = "select code, airport_code, name, city, latitude, longitude from carlocation where id=" . $pickup_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $pickup = strtolower($row_settings["airport_code"]);
    $pickup_name = $row_settings["name"];
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
    $city = $row_settings["city"];
}
$sql = "select code, airport_code, name, city, latitude, longitude from carlocation where id=" . $dropoff_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $dropoff = strtolower($row_settings["airport_code"]);
    $dropoff_name = $row_settings["name"];
    $latitude2 = $row_settings["latitude"];
    $longitude2 = $row_settings["longitude"];
}
if ($dropoff == "") {
    $dropoff = $pickup;
}
error_log("\r\n$pickup -> $dropoff\r\n", 3, "/srv/www/htdocs/error_log");

$pickup_name = str_replace("-", "", $pickup_name);
$dropoff_name = str_replace("-", "", $dropoff_name);

$pickups = explode(":", $pickup_time);
$pickuphour = $pickups[0];
$pickupminutes = $pickups[1];
$dropoffs = explode(":", $dropoff_time);
$dropoffhour = $dropoffs[0];
$dropoffminutes = $dropoffs[1];
if ($iterpecCarsLogin != "" and $iterpecCarspassword != "") {
    
    $raw = '{
      "Credential": {
        "Username": "' . $iterpecCarsLogin . '",
        "Password": "' . $iterpecCarspassword . '"
       },
      "Criteria":{
        "Pickup": {
        "Date": "' . strftime("%Y-%m-%d", $from) . '",
        "Hour": ' . $pickuphour . ',
        "Minutes": ' . $pickupminutes . ',
        "LocationCode": "' . strtoupper($pickup) . '",
        "LocationType": "Airport"
       },
       "Dropoff": {
        "Date": "' . strftime("%Y-%m-%d", $to) . '",
        "Hour": ' . $dropoffhour . ',
        "Minutes": ' . $dropoffminutes . ',
        "LocationCode": "' . strtoupper($dropoff) . '",
        "LocationType": "Airport"
       }
      }
     }';
     error_log("\r\n Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
     $headers = array(
      "Content-type: application/json",
      "Content-length: " . strlen($raw)
    );
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $iterpecCarswebservicesURL . 'ws/Rest/RentACar.svc/Search');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, $iterpecCarsTimeout);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    error_log("\r\n Response: $response \r\n", 3, "/srv/www/htdocs/error_log");
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_iterpec');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
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

    $response = json_decode($response, true);
    $TimeSpan = $response['TimeSpan'];
    $Token = $response['Token'];
    $TotalTime = $response['TotalTime'];
    $TotalCarResults = $response['TotalCarResults'];
    $Cars = $response['Cars'];
    if (count($Cars) > 0) {
        for ($i=0; $i < count($Cars); $i++) { 
            $AirConditioning = $Cars[$i]['AirConditioning'];
            $BaggageQuantity = $Cars[$i]['BaggageQuantity'];
            $CarModel = $Cars[$i]['CarModel'];
            $Currency = $Cars[$i]['Currency'];
            $IsAvailable = $Cars[$i]['IsAvailable'];
            $NumberOfDoors = $Cars[$i]['NumberOfDoors'];
            $PassengerQuantity = $Cars[$i]['PassengerQuantity'];
            $ResponseId = $Cars[$i]['ResponseId'];
            $TransmissionType = $Cars[$i]['TransmissionType'];
            $DropOffLocationDetail = $Cars[$i]['DropOffLocationDetail'];
            $DropOffLocationDetail_Address = $DropOffLocationDetail['Address'];
            $DropOffLocationDetail_Code = $DropOffLocationDetail['Code'];
            $DropOffLocationDetail_Latitude = $DropOffLocationDetail['Latitude'];
            $DropOffLocationDetail_Longitude = $DropOffLocationDetail['Longitude'];
            $DropOffLocationDetail_Phone = $DropOffLocationDetail['Phone'];
            $DropOffLocationDetail_ProviderId = $DropOffLocationDetail['ProviderId'];
            $DropOffLocationDetail_StoreId = $DropOffLocationDetail['StoreId'];
            $Integration = $Cars[$i]['Integration'];
            $IntegrationId = $Integration['IntegrationId'];
            $IntegrationName = $Integration['IntegrationName'];
            $SippCode = $Integration['SippCode'];
            $PickUpLocationDetail = $Cars[$i]['PickUpLocationDetail'];
            $PickUpLocationDetail_Address = $PickUpLocationDetail['Address'];
            $PickUpLocationDetail_Code = $PickUpLocationDetail['Code'];
            $PickUpLocationDetail_Latitude = $PickUpLocationDetail['Latitude'];
            $PickUpLocationDetail_Longitude = $PickUpLocationDetail['Longitude'];
            $PickUpLocationDetail_Phone = $PickUpLocationDetail['Phone'];
            $PickUpLocationDetail_ProviderId = $PickUpLocationDetail['ProviderId'];
            $PickUpLocationDetail_StoreId = $PickUpLocationDetail['StoreId'];
            $PriceInformation = $Cars[$i]['PriceInformation'];
            $TotalPrice = $PriceInformation['TotalPrice'];
            $Currency = $TotalPrice['Currency'];
            $Value = $TotalPrice['Value'];
            $Rental = $Cars[$i]['Rental'];
            $GroupName = $Rental['GroupName'];
            $IdGroup = $Rental['IdGroup'];
            $ProviderGroup = $Rental['ProviderGroup'];
            $RateCode = $Rental['RateCode'];
            $RentalCode = $Rental['RentalCode'];
            $RentalLogoUrl = $Rental['RentalLogoUrl'];
            $RentalName = $Rental['RentalName'];

            if ($IsAvailable == true) {
                $status = 'Available';
            } else {
                $status = 'No Available';
            }
            

            $total = $Value;
            $nettotal = $total;

            $Images = $Cars[$i]['Images'];
            if (count($Images) > 0) {
                $image = "";
                for ($j=0; $j < count($Images); $j++) { 
                    $image = $Images[$j];
                }
            }
            $CancellationPolicies = $Cars[$i]['CancellationPolicies'];
            if (count($CancellationPolicies) > 0) {
                for ($k=0; $k < count($CancellationPolicies); $k++) { 
                    $EndDate = $CancellationPolicies[$k]['EndDate'];
                    $StartDate = $CancellationPolicies[$k]['StartDate'];
                    $Value = $CancellationPolicies[$k]['Value'];
                    $CancellationPolicies_Currency = $Value['Currency'];
                    $CancellationPolicies_Value = $Value['Value'];
                }
            }
            $Features = $Cars[$i]['Features'];
            if (count($Features) > 0) {
                for ($x=0; $x < count($Features); $x++) { 
                    $EnglishDescription = $Features[$x]['EnglishDescription'];
                    $PortugueseDescription = $Features[$x]['PortugueseDescription'];
                    $SpanishDescription = $Features[$x]['SpanishDescription'];
                }
            }

            if ($pickup_name === "") {
                $pickup_name = $PickUpLocationDetail_Code;
            }

            if ($dropoff_name === "") {
                $dropoff_name = $DropOffLocationDetail_Code;
            }
            
                
            $cars[$counter]['id'] = $counter;
            $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-3-" . $counter;
            $cars[$counter]['vendorpicture'] = $image;
            $cars[$counter]['vendorcode'] = $ResponseId;
            $cars[$counter]['vendor'] = $CarModel;
            $cars[$counter]['vendorshortname'] = $RentalName;
            $cars[$counter]['size'] = $PassengerQuantity;
            $cars[$counter]['doors'] = $NumberOfDoors;
            $cars[$counter]['aircondition'] = $AirConditioning;
            $cars[$counter]['transmission'] = $TransmissionType;
            $cars[$counter]['bags'] = $BaggageQuantity;
            $cars[$counter]['status'] = $status;
            $cars[$counter]['from'] = $from;
            $cars[$counter]['to'] = $to;
            $cars[$counter]['pickup'] = ucwords(strtolower($pickup_name));
            $cars[$counter]['dropoff'] = ucwords(strtolower($dropoff_name));
            $cars[$counter]['class'] = $SippCode;
            $cars[$counter]['currency'] = $Currency;
            $cars[$counter]['productId'] = $productId;
            $cars[$counter]['programId'] = $CarProgramId;
            $cars[$counter]['name'] = $CarModel;
            $cars[$counter]['picture'] = $RentalLogoUrl;
            $cars[$counter]['programname'] = $GroupName;//supplier
            $cars[$counter]['coverage'] = $coverage;
            $cars[$counter]['ID_Context'] = $ID_Context;
            $cars[$counter]['netcurrency'] = $Currency;
            $cars[$counter]['netprice'] = $nettotal;
            $cars[$counter]['token'] = $Token;
            $cars[$counter]['carid'] = $ResponseId;
            // Total including VAT in renting country currency
            /*
            * if ($minPrice < $CarProgramPrice) {
            * $minPrice = $CarProgramPrice;
            * }
            * $minPrice = number_format($minPrice, 2, ".", "");
            * if ($carstouricoholidaysMarkup != 0) {
            * $minPrice = $minPrice + (($minPrice * $carstouricoholidaysMarkup) / 100);
            * }
            * if ($agent_markup != 0) {
            * $minPrice = $minPrice + (($minPrice * $agent_markup) / 100);
            * }
            * if ($CarProgramCurrency != "") {
            * if ($CarProgramCurrency != $scurrency) {
            * $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
            * }
            * } else {
            * if ($currencyBase != "") {
            * if ($currencyBase != $scurrency) {
            * $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
            * }
            * }
            * }
            */
            $dailytotal = $total / $nights;
            $dailytotal = number_format($dailytotal, 2, ".", "");
            // $minPrice = number_format($minPrice, 2, ".", "");
            $cars[$counter]['currency'] = $CurrencyCode;
            $cars[$counter]['total'] = $filter->filter($total);
            $cars[$counter]['dailytotal'] = $filter->filter($dailytotal);
            $cars[$counter]['dueatpickupplain'] = $DueAtPickup;
            $cars[$counter]['dueatpickup'] = $filter->filter($DueAtPickup);
            $cars[$counter]['dueatpickupcurrency'] = $filter->filter($Currency);
            // Location
            // $cars[$counter]['special'] = 1;
            // $cars[$counter]['recommended'] = 1;
            $counter = $counter + 1;

        }
    }
    //
    // Store Session
    //
    try {
        $sql = new Sql($db);
        $delete = $sql->delete();
        $delete->from('quote_session_iterpec');
        $delete->where(array(
            'session_id' => $session_id
        ));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('quote_session_iterpec');
        $insert->values(array(
            'session_id' => $session_id,
            'xmlrequest' => (string) $raw,
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
?>