<?php
error_log("\r\n Policies ITERPEC - Cars\r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
$db = new \Laminas\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_iterpec where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $data = unserialize(base64_decode($row_settings["data"]));
    $xmlrequest = $row_settings["xmlrequest"];
    $xmlresult = $row_settings["xmlresult"];
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $from = $searchsettings['pickup_from'];
    $to = $searchsettings['dropoff_to'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $response['result'] = $data[$row];
    $total = $total + $response['result']['total'];
    error_log("\r\n total $total \r\n", 3, "/srv/www/htdocs/error_log");
    $carid = $response['result']['carid']; 
    $token = $response['result']['token']; 
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
error_log("\r\n PASSOU 1 \r\n", 3, "/srv/www/htdocs/error_log");
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
error_log("\r\n PASSOU 2 \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='iterpecCarsLogin' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsLogin = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarspassword' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='iterpecCarswebservicesURL' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $iterpecCarswebservicesURL = $row['value'];
}
error_log("\r\n PASSOU 3 \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='iterpecCarsmarkup' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
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
$sql = "select value from settings where name='iterpecCarsaffiliates_id' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsaffiliates_id = $row_settings['value'];
}
error_log("\r\n PASSOU 4 \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='iterpecCarsb2cmarkup' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsb2cmarkup = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarsbranchs_id' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsbranchs_id = $row_settings['value'];
}
error_log("\r\n PASSOU 5 \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='iterpecCarsSortorder' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsSortorder = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecCarsTimeout' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecCarsTimeout = (int)$row_settings['value'];
}
error_log("\r\n PASSOU 8 \r\n", 3, "/srv/www/htdocs/error_log");
$item = array();
$cancelation_string = "";
$cancelation_deadline = 0;

$raw = '{
    "Credential": {
        "Username": "' . $iterpecCarsLogin . '",
        "Password": "' . $iterpecCarspassword . '"
    },
    "Token": "' . $token . '",
    "CarId": ' . $carid . '
    }';
    error_log("\r\n RAW: $raw \r\n", 3, "/srv/www/htdocs/error_log");
$headers = array(
    "Content-type: application/json",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $iterpecCarswebservicesURL . 'ws/Rest/RentACar.svc/GetPaymentPolicies');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, $iterpecCarsTimeout);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response2 = curl_exec($ch);
curl_close($ch);
error_log("\r\n RESPONSE: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
$response2 = json_decode($response2, true);
try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('log_iterpec');
    $insert->values(array(
        'datetime_created' => time(),
        'filename' => 'PoliciesCars.php',
        'errorline' => "",
        'errormessage' => $iterpecCarswebservicesURL . $raw,
        'sqlcontext' => $response2,
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

$TimeSpan = $response2['TimeSpan'];
$TotalTime = $response2['TotalTime'];
$Car = $response2['Car'];
if ($Car != null) {
    $AirConditioning = $Car['AirConditioning'];
    $BaggageQuantity = $Car['BaggageQuantity'];
    $CarModel = $Car['CarModel'];
    $Currency = $Car['Currency'];
    $IsAvailable = $Car['IsAvailable'];
    $NumberOfDoors = $Car['NumberOfDoors'];
    $PassengerQuantity = $Car['PassengerQuantity'];
    $ResponseId = $Car['ResponseId'];
    $TransmissionType = $Car['TransmissionType'];
    $DropOffLocationDetail = $Car['DropOffLocationDetail'];
    $DropOffLocationDetail_Address = $DropOffLocationDetail['Address'];
    $DropOffLocationDetail_Code = $DropOffLocationDetail['Code'];
    $DropOffLocationDetail_Latitude = $DropOffLocationDetail['Latitude'];
    $DropOffLocationDetail_Longitude = $DropOffLocationDetail['Longitude'];
    $DropOffLocationDetail_Phone = $DropOffLocationDetail['Phone'];
    $DropOffLocationDetail_ProviderId = $DropOffLocationDetail['ProviderId'];
    $DropOffLocationDetail_StoreId = $DropOffLocationDetail['StoreId'];
    $Integration = $Car['Integration'];
    $IntegrationId = $Integration['IntegrationId'];
    $IntegrationName = $Integration['IntegrationName'];
    $SippCode = $Integration['SippCode'];
    $PickUpLocationDetail = $Car['PickUpLocationDetail'];
    $PickUpLocationDetail_Address = $PickUpLocationDetail['Address'];
    $PickUpLocationDetail_Code = $PickUpLocationDetail['Code'];
    $PickUpLocationDetail_Latitude = $PickUpLocationDetail['Latitude'];
    $PickUpLocationDetail_Longitude = $PickUpLocationDetail['Longitude'];
    $PickUpLocationDetail_Phone = $PickUpLocationDetail['Phone'];
    $PickUpLocationDetail_ProviderId = $PickUpLocationDetail['ProviderId'];
    $PickUpLocationDetail_StoreId = $PickUpLocationDetail['StoreId'];
    $PriceInformation = $Car['PriceInformation'];
    $PaymentAtDestination = $PriceInformation['PaymentAtDestination'];
    $PaymentAtDestinationCurrency = $PaymentAtDestination['Currency'];
    $PaymentAtDestinationValue = $PaymentAtDestination['Value'];
    $PrePayment = $PriceInformation['PrePayment'];
    $PrePaymentCurrency = $PrePayment['Currency'];
    $PrePaymentValue = $PrePayment['Value'];
    $TotalPrice = $PriceInformation['TotalPrice'];
    $Currency = $TotalPrice['Currency'];
    $Value = $TotalPrice['Value'];
    $Rental = $Car['Rental'];
    $GroupName = $Rental['GroupName'];
    $IdGroup = $Rental['IdGroup'];
    $ProviderGroup = $Rental['ProviderGroup'];
    $RateCode = $Rental['RateCode'];
    $RentalCode = $Rental['RentalCode'];
    $RentalLogoUrl = $Rental['RentalLogoUrl'];
    $RentalName = $Rental['RentalName'];
    $Images = $Car['Images'];
    if (count($Images) > 0) {
        $image = "";
        for ($j=0; $j < count($Images); $j++) { 
            $image = $Images[$j];
        }
    }

    $tmpaux = array();
    $tmpaux['address'] = $PickUpLocationDetail_Address;
    $tmpaux2 = array();
    $tmpaux2['address'] = $DropOffLocationDetail_Address;
    array_push($rentallocation, $tmpaux);
    array_push($rentallocation, $tmpaux2);
    $operation = array();

    $CancellationPolicies = $Car['CancellationPolicies'];
    if (count($CancellationPolicies) > 0) {
        for ($k=0; $k < count($CancellationPolicies); $k++) { 
            $EndDate = $CancellationPolicies[$k]['EndDate'];
            $StartDate = $CancellationPolicies[$k]['StartDate'];
            $Value = $CancellationPolicies[$k]['Value'];
            $CancellationPolicies_Currency = $Value['Currency'];
            $CancellationPolicies_Value = $Value['Value'];
        }
    }
    $date = date('D, d M Y', strtotime($to));
    $cancelation_string = "If you cancel this vehicle before " . $date . " cost " . $CancellationPolicies_Currency . " " . $CancellationPolicies_Value;
    $cancelation_deadline = $date;
    $response['result']['cancelpolicy'] = $cancelation_string;
    $response['result']['cancelpolicy_details'] = $cancelation_string;
    $response['result']['cancelpolicy_deadline'] = $cancelation_deadline;
    $response['result']['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
    $Features = $Car['Features'];
    if (count($Features) > 0) {
        for ($x=0; $x < count($Features); $x++) { 
            $EnglishDescription = $Features[$x]['EnglishDescription'];
            $PortugueseDescription = $Features[$x]['PortugueseDescription'];
            $SpanishDescription = $Features[$x]['SpanishDescription'];
        }
    }
}
        
error_log("\r\n FIM \r\n", 3, "/srv/www/htdocs/error_log");     
?>