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

$breakdown = array();
for ($w = 0; $w < count($quoteid); $w ++) {
    $outputArray = array();
    $arrIt = new RecursiveIteratorIterator(new RecursiveArrayIterator($data));
    foreach ($arrIt as $sub) {
        $subArray = $arrIt->getSubIterator();
        if (isset($quoteid[$w])) {
            if (isset($subArray['quoteid'])) {
                if ($subArray['quoteid'] === $quoteid[$w]) {
                    $outputArray[] = iterator_to_array($subArray);
                    $hid = $arrIt->getSubIterator($arrIt->getDepth() - 4)
                        ->key();
                }
            }
        }
    }
    if (! is_array($outputArray)) {
        $response['error'] = "Unable to handle request #3";
        return false;
    } else {
        array_push($breakdown, $outputArray);
    }
}

$fromHotelsPRO = DateTime::createFromFormat("d-m-Y", $from);
$toHotelsPro = DateTime::createFromFormat("d-m-Y", $to);
$nights = $fromHotelsPRO->diff($toHotelsPro);
$nights = $nights->format('%a');

$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($id == 0) {
            $id = $value['id'];
        } else {
            if ($id != $value['id']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $from_date = date('Y-m-d', strtotime($from));
        $to_date = date('Y-m-d', strtotime($to));
        $cancelpolicy_deadline = 0;
        $cancelpolicy = "";
        $item = array();
        $token = $value['token'];
        $carid = $value['carid'];

        $raw = '{
            "Credential": {
              "Username": "api.xl",
              "Password": "' . $iterpecpassword . '"
            },
            "Token": "' . $token . '",
            "CarId": ' . $carid . '
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
        $response2 = json_decode($response2, true);
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('log_carnect');
            $insert->values(array(
                'datetime_created' => time(),
                'filename' => 'PoliciesCars.php',
                'errorline' => "",
                'errormessage' => $iterpecServiceURL . $raw,
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
        //
        // Policies
        //
        $item['code'] = $value['id'];
        $item['name'] = $value['name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['netprice'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['vendorcode'] = $value['vendorcode'];
        $item['vendor'] = $value['vendor'];
        $item['vendorshortname'] = $value['vendorshortname'];
        $item['doors'] = $value['doors'];
        $item['size'] = $value['size'];
        $item['bags'] = $value['bags'];
        $item['status'] = $value['status'];
        $item['class'] = $value['class'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        
        $from_date = date('Y-m-d',strtotime($StartDate));
        $to_date = date('Y-m-d',strtotime($EndDate));
        $cancelpolicy = "If you cancel booking " . $from_date . " To date " . $to_date . " cost " . $CancellationPolicies_Value. "" . $CancellationPolicies_Currency;
        if ($IsNonRefundable !== false) {
            $item['nonrefundable'] = true;
            $item['cancelpolicy'] = $translator->translate($cancelpolicy);
            $item['cancelpolicy_details'] = $translator->translate($cancelpolicy);
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", strtotime($to_date));
            $item['cancelpolicy_deadlinetimestamp'] = $to_date;
        } else {
            $item['nonrefundable'] = false;
            $item['cancelpolicy'] = $translator->translate($cancelpolicy);
            $item['cancelpolicy_details'] = $translator->translate($cancelpolicy);
            $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", strtotime($to_date));
            $item['cancelpolicy_deadlinetimestamp'] = $to_date;
        }
        
        array_push($roombreakdown, $item);
    }
    $c ++;
}
        
error_log("\r\n FIM \r\n", 3, "/srv/www/htdocs/error_log");     
?>