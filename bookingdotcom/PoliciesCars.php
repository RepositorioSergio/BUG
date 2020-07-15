<?php
error_log("\r\n COMECOU POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
$db = new \Laminas\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_bookingdotcom where session_id='$session_id'";
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
    error_log("\r\n from : " . $from . "\r\n", 3, "/srv/www/htdocs/error_log");
    $to = $searchsettings['dropoff_to'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    error_log("\r\n agent_id : " . $agent_id . "\r\n", 3, "/srv/www/htdocs/error_log");
    $response['result'] = $data[$row];
    $vendor = $total + $response['result']['vendor']; 
    error_log("\r\n vendor : " . $vendor . "\r\n", 3, "/srv/www/htdocs/error_log");
    $total = $total + $response['result']['total']; 
    error_log("\r\n total : " . $total . "\r\n", 3, "/srv/www/htdocs/error_log");
    $vehicleid = $response['result']['vendorcode']; 
    $pickuplocation_id = $response['result']['pickuplocation_id']; 
    $dropofflocation_id = $response['result']['dropofflocation_id'];  
    $address = $response['result']['address']; 
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}

$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enablecarsbookinggo' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_bookingdotcom = $affiliate_id;
} else {
    $affiliate_id_bookingdotcom = 0;
}
$sql = "select value from settings where name='carsbookinggousername' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $carsbookinggousername = $row_settings['value'];
}
$sql = "select value from settings where name='carsbookinggopassword' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $carsbookinggopassword = $row_settings['value'];
}
$sql = "select value from settings where name='carsbookinggoerviceurl' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoerviceurl = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoMarkup' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $carsbookinggoMarkup = (double) $row_settings["value"];
}
$sql = "select value from settings where name='carsbookinggob2cMarkup' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggob2cMarkup = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoSearchSortorder' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoaffiliates_id' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoTimeout' and affiliate_id=$affiliate_id_bookingdotcom" . $branch_filter;;
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoTimeout = (int)$row['value'];
}

$fromyear = date("Y", strtotime($from));
$frommonth = date("m", strtotime($from));
$fromday = date("d", strtotime($from));
$fromhours = date("H", strtotime($from));
$fromminutes = date("i", strtotime($from));
$fromday_text = date("D", strtotime($from));
$toyear = date("Y", strtotime($to));
$tomonth = date("m", strtotime($to));
$today = date("d", strtotime($to));
$tohours = date("H", strtotime($to));
$tominutes = date("i", strtotime($to));
$today_text = date("D", strtotime($to));
/* $pickups = explode(":", $pickup_time);
$pickuphour = $pickups[0];
$pickupminutes = $pickups[1];
$dropoffs = explode(":", $dropoff_time);
$dropoffhour = $dropoffs[0];
$dropoffminutes = $dropoffs[1]; */

$item = array();
$cancelation_string = "";
$cancelation_deadline = 0;


$raw = '<ExtrasListRQ version="1.1" insuranceVersion="2.0">
    <Credentials username="' . $carsbookinggousername . '" password="' . $carsbookinggopassword . '" remoteIp="' . $ipaddress . '"/> 
    <Vehicle id="' . $vehicleid . '"/>
    <PickUp>
        <Date year="' . $fromyear . '" month="' . $frommonth . '" day="' . $fromday . '" hour="' . $fromhours . '" minute="' . $fromminutes . '"/> 
    </PickUp>
    <DropOff>
        <Date year="' . $toyear . '" month="' . $tomonth . '" day="' . $today . '" hour="' . $tohours . '" minute="' . $tominutes . '"/>
    </DropOff>
    <Price>' . $total . '</Price> 
</ExtrasListRQ>';

$headers = array(
    "Content-type: application/xml",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $carsbookinggoerviceurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, $carsbookinggoTimeout);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$xmlresult = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('log_bookingdotcom');
    $insert->values(array(
        'datetime_created' => time(),
        'filename' => 'Policies.php',
        'errorline' => "",
        'errormessage' => $carsbookinggoerviceurl . $raw,
        'sqlcontext' => $xmlresult,
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

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$ExtrasListRS = $inputDoc->getElementsByTagName("ExtrasListRS");
$ExtraInfoList = $ExtrasListRS->item(0)->getElementsByTagName("ExtraInfoList");
if ($ExtraInfoList->length > 0) {
    $ExtraInfo = $ExtraInfoList->item(0)->getElementsByTagName("ExtraInfo");
    if ($ExtraInfo->length > 0) {
        for ($i=0; $i < $ExtraInfo->length; $i++) { 
            $defaultOptIn = $ExtraInfo->item($i)->getAttribute("defaultOptIn");
            $Extra = $ExtraInfo->item($i)->getElementsByTagName("Extra");
            if ($Extra->length > 0) {
                $id = $Extra->item(0)->getAttribute("id");
                $available = $Extra->item(0)->getAttribute("available");
                $product = $Extra->item(0)->getAttribute("product");
                $Name = $Extra->item(0)->getElementsByTagName("Name");
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
                $Comments = $Extra->item(0)->getElementsByTagName("Comments");
                if ($Comments->length > 0) {
                    $Comments = $Comments->item(0)->nodeValue;
                } else {
                    $Comments = "";
                }
            }
            $Price = $ExtraInfo->item($i)->getElementsByTagName("Price");
            if ($Price->length > 0) {
                $currency = $Price->item(0)->getAttribute("currency");
                $baseCurrency = $Price->item(0)->getAttribute("baseCurrency");
                $basePrice = $Price->item(0)->getAttribute("basePrice");
                $prePayable = $Price->item(0)->getAttribute("prePayable");
                $maxPrice = $Price->item(0)->getAttribute("maxPrice");
                $minPrice = $Price->item(0)->getAttribute("minPrice");
                $pricePerWhat = $Price->item(0)->getAttribute("pricePerWhat");
                $pricePerRental = $Price->item(0)->getAttribute("pricePerRental");
                $priceAvailable = $Price->item(0)->getAttribute("priceAvailable");
                $Price = $Price->item(0)->nodeValue;
            } else {
                $Price = "";
            }  
            $PreBookingURIs = $ExtraInfo->item($i)->getElementsByTagName("PreBookingURIs");
            if ($PreBookingURIs->length > 0) {
                $PreBookingKeyFactsURI = $Extra->item(0)->getElementsByTagName("PreBookingKeyFactsURI");
                if ($PreBookingKeyFactsURI->length > 0) {
                    for ($iAux=0; $iAux < $PreBookingKeyFactsURI->length; $iAux++) { 
                        $PreBookingKeyFactsURI = $PreBookingKeyFactsURI->item($iAux)->nodeValue;
                    }
                }
            }  
            $extra = array();
            $extra['name'] = $Name;
            $extra['calculation'] = $pricePerWhat;
            $extra['type'] = $EquipType;
            $extra['currency'] = $currency;
            $extra['charge'] = $Price;
            $extra['included'] = $IncludedInEstTotalInd;
            array_push($extras, $extra);       
        }
    }
}

$address_aux = explode(",", $address);
$StreetNmbr = $address_aux[0] . "," . $address_aux[1];
$CityName = $address_aux[2];
$StateProv = $address_aux[3];
$PostalCode = $address_aux[4];

$tmpaux = array();
$tmpaux['address'] = $StreetNmbr;
$tmpaux['city'] = $CityName;
$tmpaux['zipcode'] = $PostalCode;
$tmpaux['state'] = $StateProv;
$tmpaux['country'] = $CountryName;
$operation = array();
$tmpaux2 = array();
$operation2 = array();

$raw2 = '<PickUpOpenTimeRQ version="1.1">
<Credentials username="' . $carsbookinggousername . '" password="' . $carsbookinggopassword . '"/>
<Location id="' . $pickuplocation_id . '"/>
<Date year="' . $fromyear . '" month="' . $frommonth . '" day="' . $fromday . '"/> 
</PickUpOpenTimeRQ>';
error_log("\r\n RAW $raw2 \r\n", 3, "/srv/www/htdocs/error_log");
$headers = array(
    "Content-type: application/xml",
    "Content-length: " . strlen($raw2)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $carsbookinggoerviceurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, $carsbookinggoTimeout);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw2);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$xmlresult2 = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);
error_log("\r\n RESPONSE $xmlresult2 \r\n", 3, "/srv/www/htdocs/error_log");
$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult2);
$PickUpOpenTimeRS = $inputDoc->getElementsByTagName("PickUpOpenTimeRS");
$OpenTime = $PickUpOpenTimeRS->item(0)->getElementsByTagName("OpenTime");
if ($OpenTime->length > 0) {
    $OpenTime = $OpenTime->item(0)->nodeValue;
} else {
    $OpenTime = "";
}

$openingHour = strpos($OpenTime, '1');
$closingHour = strrpos($OpenTime, '1') + 1;
$tmparr = array();
$tmparr['start'] = $openingHour . ":00";
$tmparr['end'] = $closingHour . ":00";
$tmparr['day'] = $fromday_text;
array_push($operation, $tmparr);

// Dropoff Time
$raw3 = '<DropOffOpenTimeRQ version="1.1">
    <Credentials username="' . $carsbookinggousername . '" password="' . $carsbookinggopassword . '"/>
    <Location id="' . $dropofflocation_id . '"/>
    <Date year="' . $toyear . '" month="' . $tomonth . '" day="' . $today . '"/> 
</DropOffOpenTimeRQ>';
error_log("\r\n RAW $raw3 \r\n", 3, "/srv/www/htdocs/error_log");
$headers = array(
    "Content-type: application/xml",
    "Content-length: " . strlen($raw3)
);
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_URL, $carsbookinggoerviceurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($ch, CURLOPT_TIMEOUT, $carsbookinggoTimeout);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw3);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$xmlresult3 = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult3);
$DropOffOpenTimeRS = $inputDoc->getElementsByTagName("DropOffOpenTimeRS");
$OpenTime2 = $DropOffOpenTimeRS->item(0)->getElementsByTagName("OpenTime");
if ($OpenTime2->length > 0) {
    $OpenTime2 = $OpenTime2->item(0)->nodeValue;
} else {
    $OpenTime2 = "";
}

$openingHour2 = strpos($OpenTime2, '1');
$closingHour2 = strrpos($OpenTime2, '1') + 1;
$tmparr2 = array();
$tmparr2['start'] = $openingHour2 . ":00";
$tmparr2['end'] = $closingHour2 . ":00";
$tmparr2['day'] = $today_text;
array_push($operation2, $tmparr2);
$tmpaux['operation'] = $operation2;
for ($i=0; $i < 2; $i++) { 
    array_push($rentallocation, $tmpaux);  
}

//$date = date("D, d M Y", strtotime("-48 hours", $to));
$date = date('D, d M Y', strtotime('-2 days', strtotime($to)));

$cancelpolicy = $response['result']['cancelpolicy'];
if ($cancelpolicy == "true") {
    $cancelation_string = "This vehicle can be cancelled within 48 hours of collection without any charge.";
}
$cancelation_deadline = $date;
$response['result']['cancelpolicy'] = $cancelation_string;
$response['result']['cancelpolicy_details'] = $cancelation_string;
if ($cancelation_deadline != 0) {
    $response['result']['cancelpolicy_deadline'] = $cancelation_deadline;
    $response['result']['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
} else {
    $response['result']['cancelpolicy_deadline'] = $cancelation_deadline;
    $response['result']['cancelpolicy_deadlinetimestamp'] = $cancelation_deadline;
}
// error_log("\r\nCarnect Policies:\r\n\r\n" . print_r($extras, true) . "\r\n", 3, "/srv/www/htdocs/error_log");
?>