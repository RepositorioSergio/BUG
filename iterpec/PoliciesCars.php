<?php
error_log("\r\n Policies ITERPEC - Cars\r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_iterpec where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    error_log("\r\n PASSA 1 \r\n", 3, "/srv/www/htdocs/error_log");
    $row_settings->buffer();
    error_log("\r\n PASSA 2 \r\n", 3, "/srv/www/htdocs/error_log");
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
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enableiterpec' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_iterpec = $affiliate_id;
} else {
    $affiliate_id_iterpec = 0;
}
$sql = "select value from settings where name='iterpeclogin' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpeclogin = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecpassword' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecpassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='iterpecServiceURL' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $iterpecServiceURL = $row['value'];
}
$sql = "select value from settings where name='iterpecMarkup' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecMarkup = (double) $row_settings['value'];
} else {
    $iterpecMarkup = 0;
}
$sql = "select value from settings where name='iterpecaffiliates_id' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecaffiliates_id = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecb2cMarkup' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecb2cMarkup = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecbranches_id' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecbranches_id = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecParallelSearch' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecParallelSearch = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecSearchSortorder' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecSearchSortorder = $row_settings['value'];
}
$sql = "select value from settings where name='iterpecTimeout' and affiliate_id=$affiliate_id_iterpec" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $iterpecTimeout = (int)$row_settings['value'];
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

        
        $item = array();
        $cancelation_string = "";
        $cancelation_deadline = 0;
        $cancelation_details = "";

        error_log("\r\n ID_Context  $ID_Context \r\n", 3, "/srv/www/htdocs/error_log");

        $raw = '{
            "Credential": {
              "Username": "api.xl",
              "Password": "JNpWAfo%3d&"
            },
            "Token": "0883bae1-00f3-4215-8eb5-6ab11eaf53b6",
            "CarId": 514
          }';

          $headers = array(
            "Content-type: application/json",
            "Content-length: " . strlen($raw)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, $iterpecServiceURL . 'ws/Rest/RentACar.svc/GetPaymentPolicies');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, $iterpecTimeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response2 = curl_exec($ch);
        curl_close($ch);
        error_log("\r\n RESPONSE: $response2 \r\n", 3, "/srv/www/htdocs/error_log");
        $response = json_decode($response, true);
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_carnect');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'Policies.php',
                'errorline' => "",
                'errormessage' => $iterpecServiceURL . $raw,
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

        $TimeSpan = $response['TimeSpan'];
        $TotalTime = $response['TotalTime'];
        $Car = $response['Car'];
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