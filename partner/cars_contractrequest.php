<?php
require '../vendor/autoload.php';
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Metadata;
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Config;
use Zend\Log\Logger;
use Zend\Log\Writer;
echo "COMECOU";
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}
$config = new \Zend\Config\Config(include '../config/autoload/global.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$url = 'https://api-sandbox.rezserver.com/api/car/getContractRequest?format=json2&refid=10088&api_key=1f72bc370d770f4b7a6aea7758dfa31c&sid=abcdefghijk&ppn_bundle=V2--_eJwB0AEv_fsHQGYer3A0NFgrZpfA6pFGXSCHZreryDJLJePhXVeag1_p91dtSDbnfFQALFRMqsbAoMUhij6zyLzQb31lY7Ac3sGKLRPv85lyOOWrPa99aXZi_p0yzkiOzl0WN7PC_fOm20oRk9vERQ4wn_fwyaylK0dAduLhlH8hXbVIlxgHlcBMtMiPbW6GO08twwfoLqA_fV80_fKdh2NOln37nSMNzndOnl7vXRTSN9w79ftUMElarmaoRx9vDGhBpmTlpdETPhPtkp65lCezZFQkb_pO4e90bhzZkPo5J3tfU0SBIpJei424UWI1Ut1b8MAa13aXy6zub05nZvxtDZCKp4u3dF3JBtTopxxpEqRz37qjEWKVRksRUgm8ROZguUMxam_fNxDXNfjghY_fuv3wFuA3jBZB49OPWIyb_peUM4ytKrmvjYjC4zX1j4SWVXNg2JWQIr51qzE2z4_phlN_p1CNnUIRuJct_fehCPfnB_p46lKCUrkNRfd0zo5aNyweAM9S9riNuvvXnuaQgoVvDLrmX1Zirgb6DOuB378_p3QQQlT8tUrmlw8wzKABer1RWvvun0TUSDcQ4JLlTVugSE0nKOgd2UbLdEoGKP49RJIFUXZRxmqhxXfCJdaGpxTm1Q';
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/json;charset=utf-8'
));
$client->setUri($url);
$client->setMethod('GET');
//$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
    $response = $response->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client->getUri());
    $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
    echo $return;
    echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}

echo $return;
echo $response;
echo $return; 
$response = json_decode($response, true);

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
    
$getCarContractRequest = $response['getCarContractRequest'];
$results = $getCarContractRequest['results'];
$status = $results['status'];
$status_code = $results['status_code'];
$inventory = $results['inventory'];
$contractStatus = $results['contractStatus'];
$partnerLogo = $results['partnerLogo'];
$partnerCode = $results['partnerCode'];
$partnerName = $results['partnerName'];
$pickupCode = $results['pickupCode'];
$dropOffCode = $results['dropOffCode'];
$pickupDateTime = $results['pickupDateTime'];
$dropoffDateTime = $results['dropoffDateTime'];
$newsLetter = $results['newsLetter'];
$parent_contract_archive_id = $results['parent_contract_archive_id'];
$bundle = $results['bundle'];

$pickupAirport = $results['pickupAirport'];
$airportCode = $pickupAirport['airportCode'];
$name = $pickupAirport['name'];
$city = $pickupAirport['city'];
$countryCode = $pickupAirport['countryCode'];
$latitude = $pickupAirport['latitude'];
$longitude = $pickupAirport['longitude'];
$timeZone = $pickupAirport['timeZone'];

$pickupAddress = $results['pickupAddress'];
$address = $pickupAddress['address'];
$city = $pickupAddress['city'];
$stateProvinceCode = $pickupAddress['stateProvinceCode'];
$postalCode = $pickupAddress['postalCode'];
$countryCode = $pickupAddress['countryCode'];
$country = $pickupAddress['country'];
$latitude = $pickupAddress['latitude'];
$longitude = $pickupAddress['longitude'];
$bookingCounterType = $pickupAddress['bookingCounterType'];
$counterType = $pickupAddress['counterType'];
$counterTypeDescription = $pickupAddress['counterTypeDescription'];
$locationId = $pickupAddress['locationId'];

$dropoffAirport = $results['dropoffAirport'];
$airportCode = $dropoffAirport['airportCode'];
$name = $dropoffAirport['name'];
$city = $dropoffAirport['city'];
$countryCode = $dropoffAirport['countryCode'];
$latitude = $dropoffAirport['latitude'];
$longitude = $dropoffAirport['longitude'];
$timeZone = $dropoffAirport['timeZone'];

$dropoffAddress = $results['dropoffAddress'];
$address = $dropoffAddress['address'];
$city = $dropoffAddress['city'];
$stateProvinceCode = $dropoffAddress['stateProvinceCode'];
$postalCode = $dropoffAddress['postalCode'];
$countryCode = $dropoffAddress['countryCode'];
$country = $dropoffAddress['country'];
$latitude = $dropoffAddress['latitude'];
$longitude = $dropoffAddress['longitude'];
$bookingCounterType = $dropoffAddress['bookingCounterType'];
$counterType = $dropoffAddress['counterType'];
$counterTypeDescription = $dropoffAddress['counterTypeDescription'];
$locationId = $dropoffAddress['locationId'];

$pickupOperationHours = $results['pickupOperationHours'];
if (count($pickupOperationHours) > 0) {
   for ($i=0; $i < count($pickupOperationHours); $i++) { 
        $open = $pickupOperationHours[$i]['open'];
        $close = $pickupOperationHours[$i]['close'];
   }
}

$dropoffOperationHours = $results['dropoffOperationHours'];
if (count($dropoffOperationHours) > 0) {
   for ($i=0; $i < count($dropoffOperationHours); $i++) { 
        $open = $dropoffOperationHours[$i]['open'];
        $close = $dropoffOperationHours[$i]['close'];
   }
}

$carInfo = $results['carInfo'];
$carType = $results['carType'];
$example = $results['example'];
$description = $results['description'];
$imageURL = $results['imageURL'];
$passengers = $results['passengers'];
$doors = $results['doors'];
$bags = $results['bags'];
$transmission = $results['transmission'];
$airConditioning = $results['airConditioning'];
$mileage = $results['mileage'];
$images = $results['images'];
$image = "";
foreach ($images as $key => $value) {
    $image = $value;
}

$pricingInfo = $results['pricingInfo'];
$freeCancellation = $pricingInfo['freeCancellation'];
$creditCardRequired = $pricingInfo['creditCardRequired'];
$rateType = $pricingInfo['rateType'];
$ratePlan = $pricingInfo['ratePlan'];
$subtotal = $pricingInfo['subtotal'];
$total = $pricingInfo['total'];
$source = $pricingInfo['source'];
$currency = $source['currency'];
$subtotal = $source['subtotal'];
$totalTaxesAndFees = $source['totalTaxesAndFees'];
$total = $source['total'];
$payAtCounterAmount = $source['payAtCounterAmount'];
$payAtBookingAmount = $source['payAtBookingAmount'];
$base = $source['base'];
$rate = $base['rate'];
$unitAmount = $base['unitAmount'];
$units = $base['units'];
$total = $base['total'];
$subtotal = $base['subtotal'];

$insurance = $results['insurance'];
$text = $insurance['text'];
$html = $insurance['html'];
$text_block = $insurance['text_block'];
$activated = $insurance['activated'];
$provider = $insurance['provider'];
$newyork = $insurance['newyork'];
$source = $newyork['source'];
$converted_amount = $source['converted_amount'];
$currency = $source['currency'];
$symbol = $source['symbol'];
$converted_total = $source['converted_total'];
$other = $insurance['other'];
$source = $other['source'];
$converted_amount = $source['converted_amount'];
$currency = $source['currency'];
$symbol = $source['symbol'];
$converted_total = $source['converted_total'];

$details = $insurance['details'];
$changeReservation = $details['changeReservation'];
$detailsCacheKey = $details['detailsCacheKey'];
$bookingValues = $details['bookingValues'];
$googleWalletSupported = $details['googleWalletSupported'];

$vehicleRate = $details['vehicleRate'];
$id = $vehicleRate['id'];
$vehicleCode = $vehicleRate['vehicleCode'];
$numRentalDays = $vehicleRate['numRentalDays'];
$pickupDateTime = $vehicleRate['pickupDateTime'];
$returnDateTime = $vehicleRate['returnDateTime'];
$fareType = $vehicleRate['fareType'];
$ratePlan = $vehicleRate['ratePlan'];
$creditCardRequired = $vehicleRate['creditCardRequired'];
$posCurrencyCode = $vehicleRate['posCurrencyCode'];
$transactionCurrencyCode = $vehicleRate['transactionCurrencyCode'];
$payAtCounterCurrencyCode = $vehicleRate['payAtCounterCurrencyCode'];
$freeCancellation = $vehicleRate['freeCancellation'];
$payAtBooking = $vehicleRate['payAtBooking'];
$partnerCode = $vehicleRate['partnerCode'];
$partnerInfo = $vehicleRate['partnerInfo'];
$partnerCode = $partnerInfo['partnerCode'];
$pickupLocationId = $partnerInfo['pickupLocationId'];
$returnLocationId = $partnerInfo['returnLocationId'];
$referenceCode = $partnerInfo['referenceCode'];
$gdsName = $partnerInfo['gdsName'];
$ratePlanName = $partnerInfo['ratePlanName'];
$vehicleExample = $partnerInfo['vehicleExample'];
$vehicleExampleExact = $partnerInfo['vehicleExampleExact'];
$peopleCapacity = $partnerInfo['peopleCapacity'];
$bagCapacity = $partnerInfo['bagCapacity'];
$images = $partnerInfo['images'];
$image = "";
foreach ($images as $key => $value) {
    $image = $value;
}
$vehicleInfo = $vehicleRate['vehicleInfo'];
$numberOfDoors = $vehicleInfo['numberOfDoors'];
$rates = $vehicleRate['rates'];
$USD = $rates['USD'];
$currencyCode = $USD['currencyCode'];
$totalAllInclusivePrice = $USD['totalAllInclusivePrice'];
$payAtBookingAmount = $USD['payAtBookingAmount'];
$basePrices = $USD['basePrices'];
$TOTAL = $basePrices['TOTAL'];
$DAILY = $basePrices['DAILY'];
$summary = $USD['summary'];
$subTotal = $summary['subTotal'];
$totalTaxesAndFees = $summary['totalTaxesAndFees'];
$totalCharges = $summary['totalCharges'];
$basePrice = $summary['basePrice'];
$unitAmount = $basePrice['unitAmount'];
$units = $basePrice['units'];
$totalAmount = $basePrice['totalAmount'];
$taxesAndFees = $summary['taxesAndFees'];
if (count($taxesAndFees) > 0) {
    for ($i=0; $i < count($taxesAndFees); $i++) { 
        $totalAmount = $taxesAndFees[$i]['totalAmount'];
        $description = $taxesAndFees[$i]['description'];
    }
}
$rateDistance = $USD['rateDistance'];
$payAtCounterAmount = $vehicleRate['payAtCounterAmount'];
$USD = $payAtCounterAmount['USD'];

$vehicle = $details['vehicle'];
$vehicleCode = $vehicle['vehicleCode'];
$description = $vehicle['description'];
$vehicleClassRank = $vehicle['vehicleClassRank'];
$driveType = $vehicle['driveType'];
$airConditioning = $vehicle['airConditioning'];
$fuelTypeCode = $vehicle['fuelTypeCode'];
$fuelTypeDescription = $vehicle['fuelTypeDescription'];
$automatic = $vehicle['automatic'];
$manual = $vehicle['manual'];
$vehicleClassCode = $vehicle['vehicleClassCode'];
$vehicleTypeCode = $vehicle['vehicleTypeCode'];

$partner = $details['partner'];
$partnerName = $partner['partnerName'];
$partnerCode = $partner['partnerCode'];
$partnerNameShort = $partner['partnerNameShort'];
$phoneNumber = $partner['phoneNumber'];
$isOpaqueParticipant = $partner['isOpaqueParticipant'];
$isRetailParticipant = $partner['isRetailParticipant'];
$isRccOnlyParticipant = $partner['isRccOnlyParticipant'];
$partnerPrograms = $partner['partnerPrograms'];
$partnerCorpDiscountCode = $partnerPrograms['partnerCorpDiscountCode'];
$partnerRateCode = $partnerPrograms['partnerRateCode'];
$partnerPromotionCode = $partnerPrograms['partnerPromotionCode'];
$partnerLoyaltyMembershipId = $partnerPrograms['partnerLoyaltyMembershipId'];
$images = $partner['images'];
$image = "";
foreach ($images as $key => $value) {
    $image = $value;
}

$insurance = $details['insurance'];
$posCurrencyCode = $insurance['posCurrencyCode'];
$transactionCurrencyCode = $insurance['transactionCurrencyCode'];
$rates = $insurance['rates'];
$USD = $rates['USD'];
$currencyCode = $USD['currencyCode'];
$dailyPrice = $USD['dailyPrice'];
$totalPrice = $USD['totalPrice'];

$specialEquipmentGroups = $details['specialEquipmentGroups'];
$CHILD = $specialEquipmentGroups['CHILD'];
$id = $CHILD['id'];
$maxAllowed = $CHILD['maxAllowed'];
$extras = $CHILD['extras'];
if (count($extras) > 0) {
   for ($j=0; $j < count($extras); $j++) { 
        $id = $extras[$j]['id'];
        $name = $extras[$j]['name'];
        $description = $extras[$j]['description'];
        $quantityAvailable = $extras[$j]['quantityAvailable'];
   }
}

$GENERAL = $specialEquipmentGroups['GENERAL'];
$id = $GENERAL['id'];
$extras = $GENERAL['extras'];
if (count($extras) > 0) {
    for ($k=0; $k < count($extras); $k++) { 
        $id = $extras[$k]['id'];
        $name = $extras[$k]['name'];
        $quantityAvailable = $extras[$k]['quantityAvailable'];
    }
}
$HANDICAP = $specialEquipmentGroups['HANDICAP'];
$id = $HANDICAP['id'];
$maxAllowed = $HANDICAP['maxAllowed'];
$extras = $HANDICAP['extras'];
if (count($extras) > 0) {
    for ($l=0; $l < count($extras); $l++) { 
        $id = $extras[$l]['id'];
        $name = $extras[$l]['name'];
        $quantityAvailable = $extras[$l]['quantityAvailable'];
    }
}

$importantInformation = $details['importantInformation'];
$DEFAULT = $importantInformation['DEFAULT'];
if (count($DEFAULT) > 0) {
    $def = "";
    for ($m=0; $m < count($DEFAULT); $m++) { 
        $def = $DEFAULT[$m];
    }
}
$policyGroups = $details['policyGroups'];
if (count($policyGroups) > 0) {
   for ($n=0; $n < count($policyGroups); $n++) { 
        $policies = $policyGroups[$n]['policies'];
        if (count($policies) > 0) {
            for ($nAux=0; $nAux < count($policies); $nAux++) { 
                $description = $policies[$nAux]['description'];
                $code = $policies[$nAux]['code'];
                $items = $policies[$nAux]['items'];
                if (count($items) > 0) {
                    $item = "";
                    for ($nAux2=0; $nAux2 < count($items); $nAux2++) { 
                        $item = $items[$nAux2];
                    }
                }
            }
        }
   }
}
$airportCounterTypes = $details['airportCounterTypes'];
$SHUTTLE = $airportCounterTypes['SHUTTLE'];
$id = $SHUTTLE['id'];
$displayName = $SHUTTLE['displayName'];

$pickupDateHoursOfOperation = $details['pickupDateHoursOfOperation'];
if (count($pickupDateHoursOfOperation) > 0) {
    for ($p=0; $p < count($pickupDateHoursOfOperation); $p++) { 
        $openTime = $pickupDateHoursOfOperation[$p]['openTime'];
        $closeTime = $pickupDateHoursOfOperation[$p]['closeTime'];
    }
}
$returnDateHoursOfOperation = $details['returnDateHoursOfOperation'];
if (count($returnDateHoursOfOperation) > 0) {
    for ($r=0; $r < count($returnDateHoursOfOperation); $r++) { 
        $openTime = $returnDateHoursOfOperation[$r]['openTime'];
        $closeTime = $returnDateHoursOfOperation[$r]['closeTime'];
    }
}

$importantInfo = $insurance['importantInfo'];
if (count($importantInfo) > 0) {
    for ($s=0; $s < count($importantInfo); $s++) { 
        $title = $importantInfo[$s]['title'];
        $text = $importantInfo[$s]['text'];
    }
}

$paymentMethods = $results['paymentMethods'];
if (count($paymentMethods) > 0) {
    for ($t=0; $t < count($paymentMethods); $t++) { 
        $name = $paymentMethods[$t]['name'];
        $code = $paymentMethods[$t]['code'];
    }
}

$importantInformation = $results['importantInformation'];
if (count($importantInformation) > 0) {
    for ($x=0; $x < count($importantInformation); $x++) { 
        $title = $importantInformation[$x]['title'];
        $text = $importantInformation[$x]['text'];
        $display = $importantInformation[$x]['display'];
    }
}

$policies = $results['policies'];
if (count($policies) > 0) {
    for ($y=0; $y < count($policies); $y++) { 
        $policyDescription = $policies[$y]['policyDescription'];
        $items = $policies[$y]['items'];
    }
}

$specialEquipments = $results['specialEquipments'];
$child_seats = $specialEquipments['child_seats'];
if (count($child_seats) > 0) {
    for ($i=0; $i < count($child_seats); $i++) { 
        $id = $child_seats[$i]['id'];
        $code = $child_seats[$i]['code'];
        $name = $child_seats[$i]['name'];
        $description = $child_seats[$i]['description'];
        $quantityAvailable = $child_seats[$i]['quantityAvailable'];
    }
}
$other_equip = $specialEquipments['other_equip'];
if (count($other_equip) > 0) {
    for ($i=0; $i < count($other_equip); $i++) { 
        $id = $other_equip[$i]['id'];
        $code = $other_equip[$i]['code'];
        $name = $other_equip[$i]['name'];
        $description = $other_equip[$i]['description'];
        $quantityAvailable = $other_equip[$i]['quantityAvailable'];
    }
}
$vendorProgramData = $results['vendorProgramData'];
if (count($vendorProgramData) > 0) {
    for ($i=0; $i < count($vendorProgramData); $i++) { 
        $name = $vendorProgramData[$i]['name'];
        $code = $vendorProgramData[$i]['code'];
        $value = $vendorProgramData[$i]['value'];
        $disable = $vendorProgramData[$i]['disable'];
    }
}
$partnerProgramData = $results['partnerProgramData'];
if (count($partnerProgramData) > 0) {
    for ($i=0; $i < count($partnerProgramData); $i++) { 
        $name = $partnerProgramData[$i]['name'];
        $code = $partnerProgramData[$i]['code'];
        $value = $partnerProgramData[$i]['value'];
        $disable = $partnerProgramData[$i]['disable'];
    }
}
$customer_locations = $results['customer_locations'];
$user_input = $customer_locations['user_input'];
$country_data = $customer_locations['country_data'];
if (count($country_data) > 0) {
    for ($i=0; $i < count($country_data); $i++) { 
        $name = $country_data[$i]['name'];
        $code = $country_data[$i]['code'];
        $region_data = $country_data[$i]['region_data'];
        if (count($region_data) > 0) {
            for ($iAux=0; $iAux < count($region_data); $iAux++) { 
                $name = $region_data[$iAux]['name'];
                $code = $region_data[$iAux]['code'];
            }
        }
    }
}



// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>