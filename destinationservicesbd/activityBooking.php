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
echo "COMECOU ACTIVITY BOOKING<br/>";
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
// Start
$affiliate_id = 0;
$branch_filter = "";


$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$signature = "";
$word = "";
date_default_timezone_set('UTC');
//$date = date("Y-m-d H:i:s");
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");
$accessKey = "709cc0c1189a46cca41796193c4f19af";
$secretKey = "7a846c68ec6b4a7ba964d3856307a54f";
$method = "GET";
$path = "/booking.json/activity-booking/MO10-T14631362";

$word = $date . "" . $accessKey . "" . $method . "" . $path;

$signature = hash_hmac("sha1", $word, $secretKey,true);
$signature = base64_encode($signature);


$url = "https://api.bokun.io";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . '/booking.json/activity-booking/MO10-T14631362');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Bokun-Date: ' . $date,
    'X-Bokun-AccessKey: ' . $accessKey,
    'X-Bokun-Signature: ' . $signature,
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8',
    'Content-Length: ' . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);

die();
$config = new \Zend\Config\Config(include '../config/autoload/global.destinationservices.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$id = $response['id'];
$confirmationCode = $response['confirmationCode'];
$status = $response['status'];
$totalPrice = $response['totalPrice'];
$affiliateCommission = $response['affiliateCommission'];
$agentCommission = $response['agentCommission'];
$currency = $response['currency'];
$bookedDropoffPricingType = $response['bookedDropoffPricingType'];
$bookedPickupPricingType = $response['bookedPickupPricingType'];
$boxBooking = $response['boxBooking'];
$cancelNote = $response['cancelNote'];
$cancellationDate = $response['cancellationDate'];
$cancelledBy = $response['cancelledBy'];
$customized = $response['customized'];
$discountAmount = $response['discountAmount'];
$discountPercentage = $response['discountPercentage'];
$dropoff = $response['dropoff'];
$dropoffPlaceDescription = $response['dropoffPlaceDescription'];
$flexible = $response['flexible'];
$includedOnCustomerInvoice = $response['includedOnCustomerInvoice'];
$paidType = $response['paidType'];
$parentBookingId = $response['parentBookingId'];
$pickup = $response['pickup'];
$pickupPlaceDescription = $response['pickupPlaceDescription'];
$pickupPlaceRoomNumber = $response['pickupPlaceRoomNumber'];
$pickupTime = $response['pickupTime'];
$priceWithDiscount = $response['priceWithDiscount'];
$productConfirmationCode = $response['productConfirmationCode'];
$savedAmount = $response['savedAmount'];
$selectedFlexDayOption = $response['selectedFlexDayOption'];
$sellerCommission = $response['sellerCommission'];
$startTime = $response['startTime'];
$startTimeId = $response['startTimeId'];
$totalParticipants = $response['totalParticipants'];

//activity
$activity = $response['activity'];
$activityid = $activity['id'];
$activityexternalId = $activity['externalId'];
$activityproductGroupId = $activity['productGroupId'];
$activityproductCategory = $activity['productCategory'];
$activitybox = $activity['box'];
$activityinventoryLocal = $activity['inventoryLocal'];
$activityinventorySupportsPricing = $activity['inventorySupportsPricing'];
$activityinventorySupportsAvailability = $activity['inventorySupportsAvailability'];
$activitycreationDate = $activity['creationDate'];
$activitylastModified = $activity['lastModified'];
$activitylastPublished = $activity['lastPublished'];
$activitypublished = $activity['published'];
$activitytitle = $activity['title'];
$activitydescription = $activity['description'];
$activityexcerpt = $activity['excerpt'];
$cancellationPolicy = $activity['cancellationPolicy'];
if ($cancellationPolicy != null) {
    $cancellationPolicyid = $cancellationPolicy['id'];
    $cancellationPolicytitle = $cancellationPolicy['title'];
    $defaultPolicy = $cancellationPolicy['defaultPolicy'];
    $tax = $cancellationPolicy['tax'];
    $taxid = $tax['id'];
    $taxincluded = $tax['included'];
    $taxpercentage = $tax['percentage'];
    $taxtitle = $tax['title'];
    $penaltyRules = $cancellationPolicy['penaltyRules'];
    if (count($penaltyRules) > 0) {
        for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
            $id = $penaltyRules[$iAux]['id'];
            $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
            $charge = $penaltyRules[$iAux]['charge'];
            $chargeType = $penaltyRules[$iAux]['chargeType'];
        }
    }
}
$activityoverrideBarcodeFormat = $activity['overrideBarcodeFormat'];
$activitybarcodeType = $activity['barcodeType'];
$activitytimeZone = $activity['timeZone'];
$activityslug = $activity['slug'];
$activitybaseLanguage = $activity['baseLanguage'];
$activityboxedVendor = $activity['boxedVendor'];
$activitystoredExternally = $activity['storedExternally'];
$activitypluginId = $activity['pluginId'];
$activityreviewRating = $activity['reviewRating'];
$activityreviewCount = $activity['reviewCount'];
$activityactivityType = $activity['activityType'];
$activitybookingType = $activity['bookingType'];
$activityscheduleType = $activity['scheduleType'];
$activitycapacityType = $activity['capacityType'];
$activitypassExpiryType = $activity['passExpiryType'];
$activityfixedPassExpiryDate = $activity['fixedPassExpiryDate'];
$activitymeetingType = $activity['meetingType'];
$activityprivateActivity = $activity['privateActivity'];
$activitypassCapacity = $activity['passCapacity'];
$activitypassValidForDays = $activity['passValidForDays'];
$activitypassesAvailable = $activity['passesAvailable'];
$activitydressCode = $activity['dressCode'];
$activitypassportRequired = $activity['passportRequired'];
$activityincluded = $activity['included'];
$activityexcluded = $activity['excluded'];
$activityrequirements = $activity['requirements'];
$activityattention = $activity['attention'];
$activitylocationCode = $activity['locationCode'];
$activitybookingCutoffMinutes = $activity['bookingCutoffMinutes'];
$activitybookingCutoffHours = $activity['bookingCutoffHours'];
$activitybookingCutoffDays = $activity['bookingCutoffDays'];
$activitybookingCutoffWeeks = $activity['bookingCutoffWeeks'];
$activityrequestDeadlineMinutes = $activity['requestDeadlineMinutes'];
$activityrequestDeadlineHours = $activity['requestDeadlineHours'];
$activityrequestDeadlineDays = $activity['requestDeadlineDays'];
$activityrequestDeadlineWeeks = $activity['requestDeadlineWeeks'];
$activityboxedActivityId = $activity['boxedActivityId'];
$activitycomboActivity = $activity['comboActivity'];
$activityticketPerComboComponent = $activity['ticketPerComboComponent'];
$activitypickupActivityId = $activity['pickupActivityId'];
$activityallowCustomizedBookings = $activity['allowCustomizedBookings'];
$activitydayBasedAvailability = $activity['dayBasedAvailability'];
$activityselectFromDayOptions = $activity['selectFromDayOptions'];
$activitydefaultRateId = $activity['defaultRateId'];
$activityticketPerPerson = $activity['ticketPerPerson'];
$activitydurationType = $activity['durationType'];
$activityduration = $activity['duration'];
$activitydurationMinutes = $activity['durationMinutes'];
$activitydurationHours = $activity['durationHours'];
$activitydurationDays = $activity['durationDays'];
$activitydurationWeeks = $activity['durationWeeks'];
$activitydurationText = $activity['durationText'];
$activityminAge = $activity['minAge'];
$activitynextDefaultPrice = $activity['nextDefaultPrice'];
$activitynextDefaultPriceMoney = $activity['nextDefaultPriceMoney'];
$activitypickupService = $activity['pickupService'];
$activitypickupAllotment = $activity['pickupAllotment'];
$activitypickupAllotmentType = $activity['pickupAllotmentType'];
$activityuseComponentPickupAllotments = $activity['useComponentPickupAllotments'];
$activitycustomPickupAllowed = $activity['customPickupAllowed'];
$activitypickupMinutesBefore = $activity['pickupMinutesBefore'];
$activitynoPickupMsg = $activity['noPickupMsg'];
$activityticketMsg = $activity['ticketMsg'];
$activityshowGlobalPickupMsg = $activity['showGlobalPickupMsg'];
$activityshowNoPickupMsg = $activity['showNoPickupMsg'];
$activitydropoffService = $activity['dropoffService'];
$activitycustomDropoffAllowed = $activity['customDropoffAllowed'];
$activityuseSameAsPickUpPlaces = $activity['useSameAsPickUpPlaces'];
$activitydifficultyLevel = $activity['difficultyLevel'];
$activityhasOpeningHours = $activity['hasOpeningHours'];
$activitydefaultOpeningHours = $activity['defaultOpeningHours'];
$activityhasBoxes = $activity['hasBoxes'];
$activityrequestDeadline = $activity['requestDeadline'];
$activitybookingCutoff = $activity['bookingCutoff'];
$activityactualId = $activity['actualId'];
$activitynextDefaultPriceAsText = $activity['nextDefaultPriceAsText'];
//
$mainContactFields = $activity['mainContactFields'];
if (count($mainContactFields) > 0) {
    for ($k=0; $k < count($mainContactFields); $k++) { 
        $field = $mainContactFields[$k]['field'];
        $required = $mainContactFields[$k]['required'];
    }
}
$requiredCustomerFields = $activity['requiredCustomerFields'];
if (count($requiredCustomerFields) > 0) {
    $customerfields = "";
    for ($l=0; $l < count($requiredCustomerFields); $l++) { 
        $customfields = $requiredCustomerFields[$l];
    }
}
$keywords = $activity['keywords'];
$flags = $activity['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
        $flag = $flags[$iAux9];
    }
}
$languages = $activity['languages'];
if (count($languages) > 0) {
    $language = "";
    for ($l=0; $l < count($languages); $l++) { 
        $language = $languages[$l];
    }
}
$paymentCurrencies = $activity['paymentCurrencies'];
if (count($paymentCurrencies) > 0) {
    $payment = "";
    for ($z=0; $z < count($paymentCurrencies); $z++) { 
        $payment = $paymentCurrencies[$z];
    }
}
$customFields = $activity['customFields'];
if (count($customFields) > 0) {
    for ($j=0; $j < count($customFields); $j++) { 
        $type = $customFields[$j]['title'];
        $inputFieldId = $customFields[$j]['inputFieldId'];
        $value = $customFields[$j]['value'];
    }
}
$tagGroups = $activity['tagGroups'];
$categories = $activity['categories'];
$keyPhoto = $activity['keyPhoto'];
$keyPhotoid = $keyPhoto['id'];
$keyPhotooriginalUrl = $keyPhoto['originalUrl'];
$keyPhotodescription = $keyPhoto['description'];
$keyPhotoalternateText = $keyPhoto['alternateText'];
$keyPhotoheight = $keyPhoto['height'];
$keyPhotowidth = $keyPhoto['width'];
$keyPhotofileName = $keyPhoto['fileName'];
$derived = $keyPhoto['derived'];
if (count($derived) > 0) {
    for ($d=0; $d < count($derived); $d++) { 
        $name = $derived[$d]['name'];
        $url = $derived[$d]['url'];
        $cleanUrl = $derived[$d]['cleanUrl'];
    }
}
$photos = $activity['photos'];
if (count($photos) > 0) {
    for ($f=0; $f < count($photos); $f++) { 
        $photosid = $photos[$f]['id'];
        $photosoriginalUrl = $photos[$f]['originalUrl'];
        $photosdescription = $photos[$f]['description'];
        $photosalternateText = $photos[$f]['alternateText'];
        $photosheight = $photos[$f]['height'];
        $photoswidth = $photos[$f]['width'];
        $photosfileName = $photos[$f]['fileName'];
        $derived = $photos[$f]['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }
    }
}
$videos = $activity['videos'];
$vendor = $activity['vendor'];
$vendorid = $vendor['id'];
$vendortitle = $vendor['title'];
$vendorcurrencyCode = $vendor['currencyCode'];
$vendortimeZone = $vendor['timeZone'];
$vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
$vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
$vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
$vendorcompanyEmailIsDefault = $vendor['companyEmailIsDefault'];
$startPoints = $activity['startPoints'];
if (count($startPoints) > 0) {
    for ($c=0; $c < count($startPoints); $c++) { 
        $startpointsid = $startPoints[$c]['id'];
        $type = $startPoints[$c]['type'];
        $title = $startPoints[$c]['title'];
        $code = $startPoints[$c]['code'];
        $pickupTicketDescription = $startPoints[$c]['pickupTicketDescription'];
        $dropoffTicketDescription = $startPoints[$c]['dropoffTicketDescription'];
        $address = $startPoints[$c]['address'];
        $addressid = $address['id'];
        $addressLine1 = $address['addressLine1'];
        $addressLine2 = $address['addressLine2'];
        $addressLine3 = $address['addressLine3'];
        $city = $address['city'];
        $state = $address['state'];
        $postalCode = $address['postalCode'];
        $countryCode = $address['countryCode'];
        $mapZoomLevel = $address['mapZoomLevel'];
        $origin = $address['origin'];
        $originId = $address['originId'];
        $geoPoint = $address['geoPoint'];
        $latitude = $geoPoint['latitude'];
        $longitude = $geoPoint['longitude'];
        $unLocode = $address['unLocode'];
        $unlocodecountry = $unLocode['country'];
        $unlocodecity = $unLocode['city'];
        $created = $address['created'];
        if (count($created) > 0) {
            for ($cAux=0; $cAux < count($created); $cAux++) { 
                $created = $created[$cAux];
            }
        }

        $created = $startPoints[$c]['created'];
        if (count($created) > 0) {
            for ($cAux=0; $cAux < count($created); $cAux++) { 
                $created = $created[$cAux];
            }
        }
    }
}
$bookingQuestions = $activity['bookingQuestions'];
if (count($bookingQuestions) > 0) {
    for ($x=0; $x < count($bookingQuestions); $x++) { 
        $bookingquestionsid = $bookingQuestions[$x]['id'];
        $personalData = $bookingQuestions[$x]['personalData'];
        $questionCode = $bookingQuestions[$x]['questionCode'];
        $label = $bookingQuestions[$x]['label'];
        $help = $bookingQuestions[$x]['help'];
        $placeholder = $bookingQuestions[$x]['placeholder'];
        $required = $bookingQuestions[$x]['required'];
        $defaultValue = $bookingQuestions[$x]['defaultValue'];
        $dataType = $bookingQuestions[$x]['dataType'];
        $selectFromOptions = $bookingQuestions[$x]['selectFromOptions'];
        $selectMultiple = $bookingQuestions[$x]['selectMultiple'];
        $context = $bookingQuestions[$x]['context'];
        $pricingCategoryTriggerSelection = $bookingQuestions[$x]['pricingCategoryTriggerSelection'];
        $rateTriggerSelection = $bookingQuestions[$x]['rateTriggerSelection'];
        $extraTriggerSelection = $bookingQuestions[$x]['extraTriggerSelection'];  
        $options = $bookingQuestions[$x]['options'];
        if (count($options) > 0) {
            for ($xAux=0; $xAux < count($options); $xAux++) { 
                $name = $options[$xAux]['name'];
                $value = $options[$xAux]['value'];
            }
        }
    }
}
$passengerFields = $activity['passengerFields'];
if (count($passengerFields) > 0) {
    for ($y=0; $y < count($passengerFields); $y++) { 
        $field = $passengerFields[$y]['field'];
        $required = $passengerFields[$y]['required'];
    }
}
$inclusions = $activity['inclusions'];
$exclusions = $activity['exclusions'];
$googlePlace = $activity['googlePlace'];
$googlePlacecountry = $googlePlace['country'];
$googlePlacecountryCode = $googlePlace['countryCode'];
$googlePlacecity = $googlePlace['city'];
$googlePlacecityCode = $googlePlace['cityCode'];
$geoLocationCenter = $googlePlace['geoLocationCenter'];
$lat = $geoLocationCenter['lat'];
$lng = $geoLocationCenter['lng'];
$resourceSlots = $activity['resourceSlots'];
$comboParts = $activity['comboParts'];
$ticketComboComponents = $activity['ticketComboComponents'];
$dayOptions = $activity['dayOptions'];
$activityCategories = $activity['activityCategories'];
if (count($activityCategories) > 0) {
    $activity = "";
    for ($w=0; $w < count($activityCategories); $w++) { 
        $activity = $activityCategories[$w];
    }
}
$activityAttributes = $activity['activityAttributes'];
$guidanceTypes = $activity['guidanceTypes'];
if (count($guidanceTypes) > 0) {
    for ($s=0; $s < count($guidanceTypes); $s++) { 
        $guidancetypesid = $guidanceTypes[$s]['id'];
        $guidanceType = $guidanceTypes[$s]['guidanceType'];
        $created = $guidanceTypes[$s]['created'];
        if (count($created) > 0) {
            for ($cAux=0; $cAux < count($created); $cAux++) { 
                $created = $created[$cAux];
            }
        }
        $languages = $guidanceTypes[$s]['languages'];
        if (count($languages) > 0) {
            $language = "";
            for ($cAux=0; $cAux < count($languages); $cAux++) { 
                $language = $languages[$cAux];
            }
        }
    }
}
$rates = $activity['rates'];
if (count($rates) > 0) {
    for ($r=0; $r < count($rates); $r++) { 
        $ratesid = $rates[$r]['id'];
        $title = $rates[$r]['title'];
        $description = $rates[$r]['description'];
        $index = $rates[$r]['index'];
        $rateCode = $rates[$r]['rateCode'];
        $pricedPerPerson = $rates[$r]['pricedPerPerson'];
        $minPerBooking = $rates[$r]['minPerBooking'];
        $maxPerBooking = $rates[$r]['maxPerBooking'];
        $fixedPassExpiryDate = $rates[$r]['fixedPassExpiryDate'];
        $passValidForDays = $rates[$r]['passValidForDays'];
        $pickupSelectionType = $rates[$r]['pickupSelectionType'];
        $pickupPricingType = $rates[$r]['pickupPricingType'];
        $pickupPricedPerPerson = $rates[$r]['pickupPricedPerPerson'];
        $dropoffSelectionType = $rates[$r]['dropoffSelectionType'];
        $dropoffPricingType = $rates[$r]['dropoffPricingType'];
        $dropoffPricedPerPerson = $rates[$r]['dropoffPricedPerPerson'];
        $allStartTimes = $rates[$r]['allStartTimes'];
        $tieredPricingEnabled = $rates[$r]['tieredPricingEnabled'];
        $allPricingCategories = $rates[$r]['allPricingCategories'];
        $cancellationPolicy = $rates[$r]['cancellationPolicy'];
        $cancellationPolicyid = $cancellationPolicy['id'];
        $cancellationPolicytitle = $cancellationPolicy['title'];
        $cancellationPolicytax = $cancellationPolicy['tax'];
        $defaultPolicy = $cancellationPolicy['defaultPolicy'];
        $penaltyRules = $cancellationPolicy['penaltyRules'];
        if (count($penaltyRules) > 0) {
            for ($i=0; $i < count($penaltyRules); $i++) { 
                $penaltyrulesid = $penaltyRules[$i]['id'];
                $chargeType = $penaltyRules[$i]['chargeType'];
                $charge = $penaltyRules[$i]['charge'];
                $cutoffHours = $penaltyRules[$i]['cutoffHours'];
            }
        }
        $extraConfigs = $rates[$r]['extraConfigs'];
        if (count($extraConfigs) > 0) {
            for ($z=0; $z < count($extraConfigs); $z++) { 
                $extraconfigsid = $extraConfigs[$z]['id'];
                $activityExtraId = $extraConfigs[$z]['activityExtraId'];
                $selectionType = $extraConfigs[$z]['selectionType'];
                $pricingType = $extraConfigs[$z]['pricingType'];
                $pricedPerPerson = $extraConfigs[$z]['pricedPerPerson'];       
                $created = $extraConfigs[$z]['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
            }
        }
        $startTimeIds = $rates[$r]['startTimeIds'];
        if (count($startTimeIds) > 0) {
            $start = "";
            for ($st=0; $st < count($startTimeIds); $st++) { 
                $start = $startTimeIds[$st];
            }
        }
        $pricingCategoryIds = $rates[$r]['pricingCategoryIds'];
        if (count($pricingCategoryIds) > 0) {
            $pricing = "";
            for ($p=0; $p < count($pricingCategoryIds) ; $p++) { 
                $pricing = $pricingCategoryIds[$p];
            }
        }
    }
}
$pickupFlags = $activity['pickupFlags'];
$pickupPlaceGroups = $activity['pickupPlaceGroups'];
$dropoffFlags = $activity['dropoffFlags'];
$dropoffPlaceGroups = $activity['dropoffPlaceGroups'];
$pricingCategories = $activity['pricingCategories'];
if (count($pricingCategories) > 0) {
    for ($p=0; $p < count($pricingCategories); $p++) { 
        $pricingcategoriesid = $pricingCategories[$p]['id'];
        $title = $pricingCategories[$p]['title'];
        $ticketCategory = $pricingCategories[$p]['ticketCategory'];
        $occupancy = $pricingCategories[$p]['occupancy'];
        $groupSize = $pricingCategories[$p]['groupSize'];
        $ageQualified = $pricingCategories[$p]['ageQualified'];
        $minAge = $pricingCategories[$p]['minAge'];
        $maxAge = $pricingCategories[$p]['maxAge'];
        $dependent = $pricingCategories[$p]['dependent'];
        $masterCategoryId = $pricingCategories[$p]['masterCategoryId'];
        $maxPerMaster = $pricingCategories[$p]['maxPerMaster'];
        $sumDependentCategories = $pricingCategories[$p]['sumDependentCategories'];
        $maxDependentSum = $pricingCategories[$p]['maxDependentSum'];
        $internalUseOnly = $pricingCategories[$p]['internalUseOnly'];
        $defaultCategory = $pricingCategories[$p]['defaultCategory'];
        $fullTitle = $pricingCategories[$p]['fullTitle'];
    }
}
$agendaItems = $activity['agendaItems'];
if (count($agendaItems)  > 0) {
    for ($a=0; $a < count($agendaItems); $a++) { 
        $agendaitemsid = $agendaItems[$a]['id'];
        $index = $agendaItems[$a]['index'];
        $title = $agendaItems[$a]['title'];
        $excerpt = $agendaItems[$a]['excerpt'];
        $body = $agendaItems[$a]['body'];
        $day = $agendaItems[$a]['day'];
        $address = $agendaItems[$a]['address'];
        $keyPhoto = $agendaItems[$a]['keyPhoto'];
        $location = $agendaItems[$a]['location'];
        $locationaddress = $location['address'];
        $city = $location['city'];
        $countryCode = $location['countryCode'];
        $postCode = $location['postCode'];
        $latitude = $location['latitude'];
        $longitude = $location['longitude'];
        $zoomLevel = $location['zoomLevel'];
        $origin = $location['origin'];
        $originId = $location['originId'];
        $wholeAddress = $location['wholeAddress'];
    }
}
$startTimes = $activity['startTimes'];
if (count($startTimes) > 0) {
    for ($s=0; $s < count($startTimes); $s++) { 
        $starttimesid = $startTimes[$s]['id'];
        $label = $startTimes[$s]['label'];
        $hour = $startTimes[$s]['hour'];
        $minute = $startTimes[$s]['minute'];
        $overrideTimeWhenPickup = $startTimes[$s]['overrideTimeWhenPickup'];
        $pickupHour = $startTimes[$s]['pickupHour'];
        $pickupMinute = $startTimes[$s]['pickupMinute'];
        $durationType = $startTimes[$s]['durationType'];
        $voucherPickupMsg = $startTimes[$s]['voucherPickupMsg'];
        $externalId = $startTimes[$s]['externalId'];
        $duration = $startTimes[$s]['duration'];
        $durationMinutes = $startTimes[$s]['durationMinutes'];
        $durationHours = $startTimes[$s]['durationHours'];
        $durationDays = $startTimes[$s]['durationDays'];
        $durationWeeks = $startTimes[$s]['durationWeeks'];
        $flags = $startTimes[$s]['flags'];
        if (count($flags) > 0) {
            for ($sAux=0; $sAux < count($flags); $sAux++) { 
                $flags = $flags[$sAux];
            }
        }
    }
}
$bookableExtras = $activity['bookableExtras'];
if (count($bookableExtras) > 0) {
    for ($b=0; $b < count($bookableExtras); $b++) { 
        $bookableextrasid = $bookableExtras[$b]['id'];
        $externalId = $bookableExtras[$b]['externalId'];
        $title = $bookableExtras[$b]['title'];
        $information = $bookableExtras[$b]['information'];
        $included = $bookableExtras[$b]['included'];
        $free = $bookableExtras[$b]['free'];
        $productGroupId = $bookableExtras[$b]['productGroupId'];
        $pricingType = $bookableExtras[$b]['pricingType'];
        $pricingTypeLabel = $bookableExtras[$b]['pricingTypeLabel'];
        $price = $bookableExtras[$b]['price'];
        $increasesCapacity = $bookableExtras[$b]['increasesCapacity'];
        $maxPerBooking = $bookableExtras[$b]['maxPerBooking'];
        $limitByPax = $bookableExtras[$b]['limitByPax'];
    }
}
$route = $activity['route'];
$mapZoomLevel = $route['mapZoomLevel'];
$center = $route['center'];
$centerlat = $center['lat'];
$centerlng = $center['lng'];
$start = $route['start'];
$startlat = $start['lat'];
$startlng = $start['lng'];
$end = $route['end'];
$endlat = $end['lat'];
$endlng = $end['lng'];
$seasonalOpeningHours = $activity['seasonalOpeningHours'];
$displaySettings = $activity['displaySettings'];
$showPickupPlaces = $displaySettings['showPickupPlaces'];
$showRouteMap = $displaySettings['showRouteMap'];
$selectRateBasedOnStartTime = $displaySettings['selectRateBasedOnStartTime'];
$customFields = $displaySettings['customFields'];
if (count($customFields) > 0) {
    for ($j=0; $j < count($customFields); $j++) { 
        $type = $customFields[$j]['title'];
        $inputFieldId = $customFields[$j]['inputFieldId'];
        $value = $customFields[$j]['value'];
    }
}
$actualVendor = $activity['actualVendor'];
$actualVendorid = $actualVendor['id'];
$actualVendortitle = $actualVendor['title'];
$actualVendorcurrencyCode = $actualVendor['currencyCode'];
$actualVendortimeZone = $actualVendor['timeZone'];
$showInvoiceIdOnTicket = $actualVendor['showInvoiceIdOnTicket'];
$showAgentDetailsOnTicket = $actualVendor['showAgentDetailsOnTicket'];
$showPaymentsOnInvoice = $actualVendor['showPaymentsOnInvoice'];
$companyEmailIsDefault = $actualVendor['companyEmailIsDefault'];
//answers
$answers = $response['answers'];
if (count($answers) > 0) {
    for ($k=0; $k < count($answers) ; $k++) { 
        $id = $answers[$k]['id'];
        $answer = $answers[$k]['answer'];
        $group = $answers[$k]['group'];
        $question = $answers[$k]['question'];
        $type = $answers[$k]['type'];
    }
}
//agent
$agent = $response['agent'];
$agentid = $agent['id'];
$agentidNumber = $agent['idNumber'];
$agentreferenceCode = $agent['referenceCode'];
$agenttitle = $agent['title'];
$linkedExternalCustomers = $agent['linkedExternalCustomers'];
if (count($linkedExternalCustomers) > 0) {
    for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
        $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
        $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
        $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
        $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
        $systemType = $linkedExternalCustomers[$j]['systemType'];
        $flags = $linkedExternalCustomers[$j]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
    }
}
//barcode
$barcode = $response['barcode'];
$barcodevalue = $barcode['value'];
$barcodeofflineCode = $barcode['offlineCode'];
$barcodeType = $barcode['barcodeType'];
//bookedPricingCategories
$bookedPricingCategories = $response['bookedPricingCategories'];
if (count($bookedPricingCategories) > 0) {
    for ($rAux=0; $rAux < count($bookedPricingCategories); $rAux++) { 
        $id = $bookedPricingCategories[$rAux]['id'];
        $ticketCategory = $bookedPricingCategories[$rAux]['ticketCategory'];
        $ageQualified = $bookedPricingCategories[$rAux]['ageQualified'];
        $defaultCategory = $bookedPricingCategories[$rAux]['defaultCategory'];
        $dependent = $bookedPricingCategories[$rAux]['dependent'];
        $fullTitle = $bookedPricingCategories[$rAux]['fullTitle'];
        $internalUseOnly = $bookedPricingCategories[$rAux]['internalUseOnly'];
        $masterCategoryId = $bookedPricingCategories[$rAux]['masterCategoryId'];
        $maxAge = $bookedPricingCategories[$rAux]['maxAge'];
        $maxDependentSum = $bookedPricingCategories[$rAux]['maxDependentSum'];
        $maxPerMaster = $bookedPricingCategories[$rAux]['maxPerMaster'];
        $minAge = $bookedPricingCategories[$rAux]['minAge'];
        $sumDependentCategories = $bookedPricingCategories[$rAux]['sumDependentCategories'];
        $title = $bookedPricingCategories[$rAux]['title'];
        $flags = $bookedPricingCategories[$rAux]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
                $flag = $flags[$iAux3];
            }
        }
    }
}
//boxProduct
$boxProduct = $response['boxProduct'];
$boxProductid = $boxProduct['id'];
$boxProductexternalId = $boxProduct['externalId'];
$boxProductprice = $boxProduct['price'];
$boxProductslug = $boxProduct['slug'];
$boxProducttitle = $boxProduct['title'];
$vendor = $boxProduct['vendor'];
$vendorid = $vendor['id'];
$vendorcurrencyCode = $vendor['currencyCode'];
$vendoremailAddress = $vendor['emailAddress'];
$vendorinvoiceIdNumber = $vendor['invoiceIdNumber'];
$vendorlogoStyle = $vendor['logoStyle'];
$vendorphoneNumber = $vendor['phoneNumber'];
$vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
$vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
$vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
$vendortitle = $vendor['title'];
$vendorwebsite = $vendor['website'];
$logo = $vendor['logo'];
$logoid = $logo['id'];
$logoalternateText = $logo['alternateText'];
$logodescription = $logo['description'];
$logooriginalUrl = $logo['originalUrl'];
$derived = $logo['derived'];
if (count($derived) > 0) {
    for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
        $cleanUrl = $derived[$iAux4]['cleanUrl'];
        $name = $derived[$iAux4]['name'];
        $url = $derived[$iAux4]['url'];
    }
}
$flags = $logo['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
        $flag = $flags[$iAux3];
    }
}
$flags = $boxProduct['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
//comboParentBooking
$comboParentBooking = $response['comboParentBooking'];
$comboParentBookingid = $comboParentBooking['id'];
$productConfirmationCode = $comboParentBooking['productConfirmationCode'];
$product = $comboParentBooking['product'];
$productid = $product['id'];
$productexternalId = $product['externalId'];
$productprice = $product['price'];
$productslug = $product['slug'];
$producttitle = $product['title'];
$keyPhoto = $product['keyPhoto'];
$keyPhotoid = $keyPhoto['id'];
$keyPhotooriginalUrl = $keyPhoto['originalUrl'];
$keyPhotodescription = $keyPhoto['description'];
$keyPhotoalternateText = $keyPhoto['alternateText'];
$keyPhotoheight = $keyPhoto['height'];
$keyPhotowidth = $keyPhoto['width'];
$keyPhotofileName = $keyPhoto['fileName'];
$derived = $keyPhoto['derived'];
if (count($derived) > 0) {
    for ($d=0; $d < count($derived); $d++) { 
        $name = $derived[$d]['name'];
        $url = $derived[$d]['url'];
        $cleanUrl = $derived[$d]['cleanUrl'];
    }
}
$flags = $product['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
        $flag = $flags[$iAux3];
    }
}
$vendor = $product['vendor'];
$vendorid = $vendor['id'];
$vendorcurrencyCode = $vendor['currencyCode'];
$vendoremailAddress = $vendor['emailAddress'];
$vendorinvoiceIdNumber = $vendor['invoiceIdNumber'];
$vendorlogoStyle = $vendor['logoStyle'];
$vendorphoneNumber = $vendor['phoneNumber'];
$vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
$vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
$vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
$vendortitle = $vendor['title'];
$vendorwebsite = $vendor['website'];
$logo = $vendor['logo'];
$logoid = $logo['id'];
$logoalternateText = $logo['alternateText'];
$logodescription = $logo['description'];
$logooriginalUrl = $logo['originalUrl'];
$derived = $logo['derived'];
if (count($derived) > 0) {
    for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
        $cleanUrl = $derived[$iAux4]['cleanUrl'];
        $name = $derived[$iAux4]['name'];
        $url = $derived[$iAux4]['url'];
    }
}
$flags = $logo['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
        $flag = $flags[$iAux3];
    }
}
//comboChildBookings
$comboChildBookings = $response['comboChildBookings'];
if (count($comboChildBookings) > 0) {
    for ($z=0; $z < count($comboChildBookings); $z++) { 
        $id = $comboChildBookings[$z]['id'];
        $productConfirmationCode = $comboChildBookings[$z]['productConfirmationCode'];
        $product = $comboChildBookings[$z]['product'];
        $productid = $product['id'];
        $productexternalId = $product['externalId'];
        $productprice = $product['price'];
        $productslug = $product['slug'];
        $producttitle = $product['title'];
        $keyPhoto = $product['keyPhoto'];
        $keyPhotoid = $keyPhoto['id'];
        $keyPhotooriginalUrl = $keyPhoto['originalUrl'];
        $keyPhotodescription = $keyPhoto['description'];
        $keyPhotoalternateText = $keyPhoto['alternateText'];
        $keyPhotoheight = $keyPhoto['height'];
        $keyPhotowidth = $keyPhoto['width'];
        $keyPhotofileName = $keyPhoto['fileName'];
        $derived = $keyPhoto['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }
        $flags = $product['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
                $flag = $flags[$iAux3];
            }
        }
        $vendor = $product['vendor'];
        $vendorid = $vendor['id'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendoremailAddress = $vendor['emailAddress'];
        $vendorinvoiceIdNumber = $vendor['invoiceIdNumber'];
        $vendorlogoStyle = $vendor['logoStyle'];
        $vendorphoneNumber = $vendor['phoneNumber'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendortitle = $vendor['title'];
        $vendorwebsite = $vendor['website'];
        $logo = $vendor['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $logo['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
                $flag = $flags[$iAux3];
            }
        }
    }
}
//dropoffPlace
$dropoffPlace = $response['dropoffPlace'];
$dropoffPlaceid = $dropoffPlace['id'];
$dropoffPlaceexternalId = $dropoffPlace['externalId'];
$dropoffPlacetitle = $dropoffPlace['title'];
$dropoffPlacetype = $dropoffPlace['type'];
$dropoffPlaceaskForRoomNumber = $dropoffPlace['askForRoomNumber'];
$location = $dropoffPlace['location'];
$locationaddress = $location['address'];
$city = $location['city'];
$countryCode = $location['countryCode'];
$postCode = $location['postCode'];
$latitude = $location['latitude'];
$longitude = $location['longitude'];
$zoomLevel = $location['zoomLevel'];
$origin = $location['origin'];
$originId = $location['originId'];
$wholeAddress = $location['wholeAddress'];
$flags = $dropoffPlace['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
        $flag = $flags[$iAux3];
    }
}
//extras
$extras = $response['extras'];
if (count($extras) > 0) {
    for ($e=0; $e < count($extras); $e++) { 
        $id = $extras[$e]['id'];
        $unitCount = $extras[$e]['unitCount'];
        $answers = $extras[$e]['answers'];
        if (count($answers) > 0) {
            for ($k=0; $k < count($answers) ; $k++) { 
                $id = $answers[$k]['id'];
                $answer = $answers[$k]['answer'];
                $group = $answers[$k]['group'];
                $question = $answers[$k]['question'];
                $type = $answers[$k]['type'];
            }
        }
        $bookableExtra = $extras[$e]['bookableExtra'];
        if (count($bookableExtras) > 0) {
            for ($iAux19=0; $iAux19 < count($bookableExtras); $iAux19++) { 
                $id = $bookableExtras[$iAux19]['id'];
                $externalId = $bookableExtras[$iAux19]['externalId'];
                $free = $bookableExtras[$iAux19]['free'];
                $included = $bookableExtras[$iAux19]['included'];
                $increasesCapacity = $bookableExtras[$iAux19]['increasesCapacity'];
                $information = $bookableExtras[$iAux19]['information'];
                $maxPerBooking = $bookableExtras[$iAux19]['maxPerBooking'];
                $price = $bookableExtras[$iAux19]['price'];
                $pricingType = $bookableExtras[$iAux19]['pricingType'];
                $pricingTypeLabel = $bookableExtras[$iAux19]['pricingTypeLabel'];
                $title = $bookableExtras[$iAux19]['title'];
                $flags = $bookableExtras[$iAux19]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
                $questions = $bookableExtras[$iAux19]['questions'];
                if (count($questions) > 0) {
                    for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                        $id = $questions[$iAux15]['id'];
                        $active = $questions[$iAux15]['active'];
                        $answerRequired = $questions[$iAux15]['answerRequired'];
                        $label = $questions[$iAux15]['label'];
                        $options = $questions[$iAux15]['options'];
                        $type = $questions[$iAux15]['type'];
                        $flags = $questions[$iAux15]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                $flag = $flags[$iAux9];
                            }
                        }
                    }
                }
            }
        }
        $extra = $extras[$e]['extra'];
        $id = $extra['id'];
        $externalId = $extra['externalId'];
        $free = $extra['free'];
        $included = $extra['included'];
        $increasesCapacity = $extra['increasesCapacity'];
        $information = $extra['information'];
        $maxPerBooking = $extra['maxPerBooking'];
        $price = $extra['price'];
        $pricingType = $extra['pricingType'];
        $pricingTypeLabel = $extra['pricingTypeLabel'];
        $title = $extra['title'];
        $flags = $extra['flags'];
        $questions = $extra['questions'];
        if (count($questions) > 0) {
            for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                $id = $questions[$iAux15]['id'];
                $active = $questions[$iAux15]['active'];
                $answerRequired = $questions[$iAux15]['answerRequired'];
                $label = $questions[$iAux15]['label'];
                $options = $questions[$iAux15]['options'];
                $type = $questions[$iAux15]['type'];
                $flags = $questions[$iAux15]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
            }
        }
        $groupedAnswers = $extras[$e]['groupedAnswers'];
        if (count($groupedAnswers) > 0) {
            for ($eAux=0; $eAux < count($groupedAnswers); $eAux++) { 
                $group = $groupedAnswers[$eAux]['group'];
                $name = $groupedAnswers[$eAux]['name'];
                $answers = $groupedAnswers[$eAux]['answers'];
                if (count($answers) > 0) {
                    for ($k=0; $k < count($answers) ; $k++) { 
                        $id = $answers[$k]['id'];
                        $answer = $answers[$k]['answer'];
                        $group = $answers[$k]['group'];
                        $question = $answers[$k]['question'];
                        $type = $answers[$k]['type'];
                    }
                }
                $qandA = $groupedAnswers[$eAux]['qandA'];
                if (count($qandA) > 0) {
                    for ($eAux2=0; $eAux2 < count($qandA); $eAux2++) { 
                        $answerAsString = $qandA[$eAux2]['answerAsString'];
                        $questionAsString = $qandA[$eAux2]['questionAsString'];
                        $answer = $qandA[$eAux2]['answer'];
                        $answerid = $answer['id'];
                        $answer2 = $answer['answer'];
                        $answergroup = $answer['group'];
                        $answerquestion = $answer['question'];
                        $answerquestionId = $answer['questionId'];
                        $answertype = $answer['type'];
                        $question = $qandA[$eAux2]['question'];
                        $questionid = $question['id'];
                        $active = $question['active'];
                        $questionanswerRequired = $question['answerRequired'];
                        $questionlabel = $question['label'];
                        $questionoptions = $question['options'];
                        $questiontype = $question['type'];
                    }
                }
                $questionsAndAnswers = $groupedAnswers[$eAux]['questionsAndAnswers'];
                if (count($questionsAndAnswers) > 0) {
                    for ($eAux2=0; $eAux2 < count($questionsAndAnswers); $eAux2++) { 
                        $answerAsString = $questionsAndAnswers[$eAux2]['answerAsString'];
                        $questionAsString = $questionsAndAnswers[$eAux2]['questionAsString'];
                        $answer = $questionsAndAnswers[$eAux2]['answer'];
                        $answerid = $answer['id'];
                        $answer2 = $answer['answer'];
                        $answergroup = $answer['group'];
                        $answerquestion = $answer['question'];
                        $answerquestionId = $answer['questionId'];
                        $answertype = $answer['type'];
                        $question = $questionsAndAnswers[$eAux2]['question'];
                        $questionid = $question['id'];
                        $active = $question['active'];
                        $questionanswerRequired = $question['answerRequired'];
                        $questionlabel = $question['label'];
                        $questionoptions = $question['options'];
                        $questiontype = $question['type'];
                    }
                }
            }
        }
    }
}
//supplierContractFlags
$supplierContractFlags = $response['supplierContractFlags'];
if (count($supplierContractFlags) > 0) {
    $contract = ""; 
    for ($m=0; $m < count($supplierContractFlags); $m++) { 
        $contract = $supplierContractFlags[$m];
    }
}
//sellerContractFlags
$sellerContractFlags = $response['sellerContractFlags'];
if (count($sellerContractFlags) > 0) {
    $contract = ""; 
    for ($m=0; $m < count($sellerContractFlags); $m++) { 
        $contract = $sellerContractFlags[$m];
    }
}
//notes
$notes = $response['notes'];
if (count($notes) > 0) {
    for ($n=0; $n < count($notes); $n++) { 
        $author = $notes[$n]['author'];
        $body = $notes[$n]['body'];
        $created = $notes[$n]['created'];
        $ownerId = $notes[$n]['ownerId'];
        $recipient = $notes[$n]['recipient'];
        $sentAsEmail = $notes[$n]['sentAsEmail'];
        $subject = $notes[$n]['subject'];
        $type = $notes[$n]['type'];
        $voucherAttached = $notes[$n]['voucherAttached'];
        $voucherPricesShown = $notes[$n]['voucherPricesShown'];
    }
}
//parentBooking
$parentBooking = $response['parentBooking'];
$confirmationCode = $parentBooking['confirmationCode'];
$creationDate = $parentBooking['creationDate'];
$currency = $parentBooking['currency'];
$externalBookingReference = $parentBooking['externalBookingReference'];
$externalBookingEntityName = $parentBooking['externalBookingEntityName'];
$externalBookingEntityCode = $parentBooking['externalBookingEntityCode'];
$bookingId = $parentBooking['bookingId'];
$language = $parentBooking['language'];
$paymentType = $parentBooking['paymentType'];
$status = $parentBooking['status'];
$totalDue = $parentBooking['totalDue'];
$totalPaid = $parentBooking['totalPaid'];
$totalPrice = $parentBooking['totalPrice'];
$totalPriceConverted = $parentBooking['totalPriceConverted'];
//accommodationBookings
$accommodationBookings = $parentBooking['accommodationBookings'];
if (count($accommodationBookings) > 0) {
    for ($i=0; $i < count($accommodationBookings); $i++) { 
        $id = $accommodationBookings[$i]['id'];
        $confirmationCode = $accommodationBookings[$i]['confirmationCode'];
        $affiliateCommission = $accommodationBookings[$i]['affiliateCommission'];
        $agentCommission = $accommodationBookings[$i]['agentCommission'];
        $boxBooking = $accommodationBookings[$i]['boxBooking'];
        $cancelNote = $accommodationBookings[$i]['cancelNote'];
        $cancellationDate = $accommodationBookings[$i]['cancellationDate'];
        $cancelledBy = $accommodationBookings[$i]['cancelledBy'];
        $discountAmount = $accommodationBookings[$i]['discountAmount'];
        $discountPercentage = $accommodationBookings[$i]['discountPercentage'];
        $endDate = $accommodationBookings[$i]['endDate'];
        $firstStartDate = $accommodationBookings[$i]['firstStartDate'];
        $includedOnCustomerInvoice = $accommodationBookings[$i]['includedOnCustomerInvoice'];
        $lastEndDate = $accommodationBookings[$i]['lastEndDate'];
        $paidType = $accommodationBookings[$i]['paidType'];
        $parentBookingId = $accommodationBookings[$i]['parentBookingId'];
        $priceWithDiscount = $accommodationBookings[$i]['priceWithDiscount'];
        $productConfirmationCode = $accommodationBookings[$i]['productConfirmationCode'];
        $savedAmount = $accommodationBookings[$i]['savedAmount'];
        $sellerCommission = $accommodationBookings[$i]['sellerCommission'];
        $sortDate = $accommodationBookings[$i]['sortDate'];
        $startDate = $accommodationBookings[$i]['startDate'];
        $status = $accommodationBookings[$i]['status'];
        $totalPrice = $accommodationBookings[$i]['totalPrice'];
        //accommodation
        $accommodation = $accommodationBookings[$i]['accommodation'];
        $accommodationid = $accommodation['id'];
        $accommodationactualId = $accommodation['actualId'];
        $accommodationbaseLanguage = $accommodation['baseLanguage'];
        $accommodationbox = $accommodation['box'];
        $accommodationboxedAccommodationId = $accommodation['boxedAccommodationId'];
        $accommodationcheckInHour = $accommodation['checkInHour'];
        $accommodationcheckInMinute = $accommodation['checkInMinute'];
        $accommodationcheckOutHour = $accommodation['checkOutHour'];
        $accommodationcheckOutMinute = $accommodation['checkOutMinute'];
        $accommodationdescription = $accommodation['description'];
        $accommodationexcerpt = $accommodation['excerpt'];
        $accommodationexternalId = $accommodation['externalId'];
        $accommodationlastPublished = $accommodation['lastPublished'];
        $accommodationproductCategory = $accommodation['productCategory'];
        $accommodationproductGroupId = $accommodation['productGroupId'];
        $accommodationrating = $accommodation['rating'];
        $accommodationslug = $accommodation['slug'];
        $accommodationtitle = $accommodation['title'];
        $accommodationbarcodeType = $accommodation['barcodeType'];
        $accommodationtimeZone = $accommodation['timeZone'];
        $accommodationstoredExternally = $accommodation['storedExternally'];
        $actualVendor = $accommodation['actualVendor'];
        $actualVendorid = $actualVendor['id'];
        $actualVendorcurrencyCode = $actualVendor['currencyCode'];
        $actualVendoremailAddress = $actualVendor['emailAddress'];
        $actualVendorinvoiceIdNumber = $actualVendor['invoiceIdNumber'];
        $actualVendorlogoStyle = $actualVendor['logoStyle'];
        $actualVendorphoneNumber = $actualVendor['phoneNumber'];
        $actualVendorshowAgentDetailsOnTicket = $actualVendor['showAgentDetailsOnTicket'];
        $actualVendorshowInvoiceIdOnTicket = $actualVendor['showInvoiceIdOnTicket'];
        $actualVendorshowPaymentsOnInvoice = $actualVendor['showPaymentsOnInvoice'];
        $actualVendortitle = $actualVendor['title'];
        $actualVendorwebsite = $actualVendor['website'];
        $logo = $actualVendor['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $bookableExtras = $accommodation['bookableExtras'];
        if (count($bookableExtras) > 0) {
            for ($iAux19=0; $iAux19 < count($bookableExtras); $iAux19++) { 
                $id = $bookableExtras[$iAux19]['id'];
                $externalId = $bookableExtras[$iAux19]['externalId'];
                $free = $bookableExtras[$iAux19]['free'];
                $included = $bookableExtras[$iAux19]['included'];
                $increasesCapacity = $bookableExtras[$iAux19]['increasesCapacity'];
                $information = $bookableExtras[$iAux19]['information'];
                $maxPerBooking = $bookableExtras[$iAux19]['maxPerBooking'];
                $price = $bookableExtras[$iAux19]['price'];
                $pricingType = $bookableExtras[$iAux19]['pricingType'];
                $pricingTypeLabel = $bookableExtras[$iAux19]['pricingTypeLabel'];
                $title = $bookableExtras[$iAux19]['title'];
                $flags = $bookableExtras[$iAux19]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
                $questions = $bookableExtras[$iAux19]['questions'];
                if (count($questions) > 0) {
                    for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                        $id = $questions[$iAux15]['id'];
                        $active = $questions[$iAux15]['active'];
                        $answerRequired = $questions[$iAux15]['answerRequired'];
                        $label = $questions[$iAux15]['label'];
                        $options = $questions[$iAux15]['options'];
                        $type = $questions[$iAux15]['type'];
                        $flags = $questions[$iAux15]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                $flag = $flags[$iAux9];
                            }
                        }
                    }
                }
            }
        }
        $boxedVendor = $accommodation['boxedVendor'];
        $boxedVendorid = $boxedVendor['id'];
        $boxedVendorcurrencyCode = $boxedVendor['currencyCode'];
        $boxedVendoremailAddress = $boxedVendor['emailAddress'];
        $boxedVendorinvoiceIdNumber = $boxedVendor['invoiceIdNumber'];
        $boxedVendorlogoStyle = $boxedVendor['logoStyle'];
        $boxedVendorphoneNumber = $boxedVendor['phoneNumber'];
        $boxedVendorshowAgentDetailsOnTicket = $boxedVendor['showAgentDetailsOnTicket'];
        $boxedVendorshowInvoiceIdOnTicket = $boxedVendor['showInvoiceIdOnTicket'];
        $boxedVendorshowPaymentsOnInvoice = $boxedVendor['showPaymentsOnInvoice'];
        $boxedVendortitle = $boxedVendor['title'];
        $boxedVendorwebsite = $boxedVendor['website'];
        $logo = $boxedVendor['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $categories = $accommodation['categories'];
        if (count($categories) > 0) {
            for ($iAux16=0; $iAux16 < count($categories); $iAux16++) { 
               
                 $id = $categories[$iAux16]['id'];$title = $categories[$iAux16]['title'];
                $allowsSelectingMultipleChildren = $categories[$iAux16]['allowsSelectingMultipleChildren'];
                $flags = $categories[$iAux16]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
            }
        }
        $paymentCurrencies = $accommodation['paymentCurrencies'];
        if (count($paymentCurrencies) > 0) {
            $paymentCurrency = "";
            for ($iAux17=0; $iAux17 < count($paymentCurrencies); $iAux17++) { 
                $paymentCurrency = $paymentCurrencies[$iAux17];
            }
        }
        $customFields = $accommodation['customFields'];
        if (count($customFields) > 0) {
            for ($iAux18=0; $iAux18 < count($customFields); $iAux18++) { 
                $flags = $customFields[$iAux18]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
            }
        }
        $flags = $accommodation['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                $flag = $flags[$iAux9];
            }
        }
        $keyPhoto = $accommodation['keyPhoto'];
        $keyPhotoid = $keyPhoto['id'];
        $keyPhotooriginalUrl = $keyPhoto['originalUrl'];
        $keyPhotodescription = $keyPhoto['description'];
        $keyPhotoalternateText = $keyPhoto['alternateText'];
        $keyPhotoheight = $keyPhoto['height'];
        $keyPhotowidth = $keyPhoto['width'];
        $keyPhotofileName = $keyPhoto['fileName'];
        $derived = $keyPhoto['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }
        $keywords = $accommodation['keywords'];
        if (count($keywords) > 0) {
            $keyword = "";
            for ($iAux17=0; $iAux17 < count($keywords); $iAux17++) { 
                $keyword = $keywords[$iAux17];
            }
        }
        $languages = $accommodation['languages'];
        if (count($languages) > 0) {
            $language = "";
            for ($iAux17=0; $iAux17 < count($languages); $iAux17++) { 
                $language = $languages[$iAux17];
            }
        }
        $location = $accommodation['location'];
        $photos = $accommodation['photos'];
        if (count($photos) > 0) {
            for ($f=0; $f < count($photos); $f++) { 
                $photosid = $photos[$f]['id'];
                $photosoriginalUrl = $photos[$f]['originalUrl'];
                $photosdescription = $photos[$f]['description'];
                $photosalternateText = $photos[$f]['alternateText'];
                $photosheight = $photos[$f]['height'];
                $photoswidth = $photos[$f]['width'];
                $photosfileName = $photos[$f]['fileName'];
                $derived = $photos[$f]['derived'];
                if (count($derived) > 0) {
                    for ($d=0; $d < count($derived); $d++) { 
                        $name = $derived[$d]['name'];
                        $url = $derived[$d]['url'];
                        $cleanUrl = $derived[$d]['cleanUrl'];
                    }
                } 
                $flags = $photos[$f]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
            }
        }
        $roomTypes = $accommodation['roomTypes'];
        if (count($roomTypes) > 0) {
            for ($iAux10=0; $iAux10 < count($roomTypes); $iAux10++) { 
                $id = $roomTypes[$iAux10]['id'];
                $accommodationId = $roomTypes[$iAux10]['accommodationId'];
                $accommodationTitle = $roomTypes[$iAux10]['accommodationTitle'];
                $bunkBedCount = $roomTypes[$iAux10]['bunkBedCount'];
                $capacity = $roomTypes[$iAux10]['capacity'];
                $code = $roomTypes[$iAux10]['code'];
                $defaultRateId = $roomTypes[$iAux10]['defaultRateId'];
                $description = $roomTypes[$iAux10]['description'];
                $doubleBedCount = $roomTypes[$iAux10]['doubleBedCount'];
                $excerpt = $roomTypes[$iAux10]['excerpt'];
                $externalId = $roomTypes[$iAux10]['externalId'];
                $extraLargeDoubleBedCount = $roomTypes[$iAux10]['extraLargeDoubleBedCount'];
                $futonMatCount = $roomTypes[$iAux10]['futonMatCount'];
                $internalUseOnly = $roomTypes[$iAux10]['internalUseOnly'];
                $largeDoubleBedCount = $roomTypes[$iAux10]['largeDoubleBedCount'];
                $roomCount = $roomTypes[$iAux10]['roomCount'];
                $shared = $roomTypes[$iAux10]['shared'];
                $singleBedCount = $roomTypes[$iAux10]['singleBedCount'];
                $sofaBedCount = $roomTypes[$iAux10]['sofaBedCount'];
                $title = $roomTypes[$iAux10]['title'];
                $vendorId = $roomTypes[$iAux10]['vendorId'];
                $categories = $roomTypes[$iAux10]['categories'];
                if (count($categories) > 0) {
                    for ($iAux16=0; $iAux16 < count($categories); $iAux16++) { 
                        $id = $categories[$iAux16]['id'];
                        $title = $categories[$iAux16]['title'];
                        $allowsSelectingMultipleChildren = $categories[$iAux16]['allowsSelectingMultipleChildren'];
                        $flags = $categories[$iAux16]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                $flag = $flags[$iAux9];
                            }
                        }
                    }
                }
                $extras = $roomTypes[$iAux10]['extras'];
                if (count($extras) > 0) {
                    for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                        $id = $extras[$iAux14]['id'];
                        $externalId = $extras[$iAux14]['externalId'];
                        $free = $extras[$iAux14]['free'];
                        $included = $extras[$iAux14]['included'];
                        $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                        $information = $extras[$iAux14]['information'];
                        $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                        $price = $extras[$iAux14]['price'];
                        $pricingType = $extras[$iAux14]['pricingType'];
                        $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                        $title = $extras[$iAux14]['title'];
                        $flags = $extras[$iAux14]['flags'];
                        $questions = $extras[$iAux14]['questions'];
                        if (count($questions) > 0) {
                            for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                                $id = $questions[$iAux15]['id'];
                                $active = $questions[$iAux15]['active'];
                                $answerRequired = $questions[$iAux15]['answerRequired'];
                                $label = $questions[$iAux15]['label'];
                                $options = $questions[$iAux15]['options'];
                                $type = $questions[$iAux15]['type'];
                                $flags = $questions[$iAux15]['flags'];
                                if (count($flags) > 0) {
                                    $flag = "";
                                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                        $flag = $flags[$iAux9];
                                    }
                                }
                            }
                        }
                    }
                }
                $roomRates = $roomTypes[$iAux10]['roomRates'];
                if (count($roomRates) > 0) {
                    for ($iAux11=0; $iAux11 < count($roomRates); $iAux11++) { 
                        $id = $roomRates[$iAux11]['id'];
                        $maxNightsStay = $roomRates[$iAux11]['maxNightsStay'];
                        $maxOccupants = $roomRates[$iAux11]['maxOccupants'];
                        $minNightsStay = $roomRates[$iAux11]['minNightsStay'];
                        $stayRestrictions = $roomRates[$iAux11]['stayRestrictions'];
                        $stayRestrictionsAllMonths = $roomRates[$iAux11]['stayRestrictionsAllMonths'];
                        $stayRestrictionsAllWeekdays = $roomRates[$iAux11]['stayRestrictionsAllWeekdays'];
                        $title = $roomRates[$iAux11]['title'];
                        $stayRestrictionsMonths = $roomRates[$iAux11]['stayRestrictionsMonths'];
                        if (count($stayRestrictionsMonths) > 0) {
                            $months = "";
                            for ($iAux12=0; $iAux12 < count($stayRestrictionsMonths); $iAux12++) { 
                                $months = $stayRestrictionsAllWeekdays[$iAux12];
                            }
                        }
                        $stayRestrictionsWeekdays = $roomRates[$iAux11]['stayRestrictionsWeekdays'];
                        if (count($stayRestrictionsAllWeekdays) > 0) {
                            $weekdays = "";
                            for ($iAux13=0; $iAux13 < count($stayRestrictionsAllWeekdays); $iAux13++) { 
                                $weekdays = $stayRestrictionsAllWeekdays[$iAux13];
                            }
                        }
                        $cancellationPolicy = $roomRates[$iAux11]['cancellationPolicy'];
                        $cancellationPolicyid = $cancellationPolicy['id'];
                        $cancellationPolicytitle = $cancellationPolicy['title'];
                        $tax = $cancellationPolicy['tax'];
                        $taxid = $tax['id'];
                        $taxincluded = $tax['included'];
                        $taxpercentage = $tax['percentage'];
                        $taxtitle = $tax['title'];
                        $penaltyRules = $cancellationPolicy['penaltyRules'];
                        if (count($penaltyRules) > 0) {
                            for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                                $id = $penaltyRules[$iAux]['id'];
                                $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                                $charge = $penaltyRules[$iAux]['charge'];
                                $chargeType = $penaltyRules[$iAux]['chargeType'];
                            }
                        }
                    }
                }
                $keyPhoto = $roomTypes[$iAux10]['keyPhoto'];
                $keyPhotoid = $keyPhoto['id'];
                $keyPhotooriginalUrl = $keyPhoto['originalUrl'];
                $keyPhotodescription = $keyPhoto['description'];
                $keyPhotoalternateText = $keyPhoto['alternateText'];
                $keyPhotoheight = $keyPhoto['height'];
                $keyPhotowidth = $keyPhoto['width'];
                $keyPhotofileName = $keyPhoto['fileName'];
                $derived = $keyPhoto['derived'];
                if (count($derived) > 0) {
                    for ($d=0; $d < count($derived); $d++) { 
                        $name = $derived[$d]['name'];
                        $url = $derived[$d]['url'];
                        $cleanUrl = $derived[$d]['cleanUrl'];
                    }
                }
                $photos = $roomTypes[$iAux10]['photos'];
                for ($f=0; $f < count($photos); $f++) { 
                    $photosid = $photos[$f]['id'];
                    $photosoriginalUrl = $photos[$f]['originalUrl'];
                    $photosdescription = $photos[$f]['description'];
                    $photosalternateText = $photos[$f]['alternateText'];
                    $photosheight = $photos[$f]['height'];
                    $photoswidth = $photos[$f]['width'];
                    $photosfileName = $photos[$f]['fileName'];
                    $derived = $photos[$f]['derived'];
                    if (count($derived) > 0) {
                        for ($d=0; $d < count($derived); $d++) { 
                            $name = $derived[$d]['name'];
                            $url = $derived[$d]['url'];
                            $cleanUrl = $derived[$d]['cleanUrl'];
                        }
                    }
                }
                $flags = $roomTypes[$iAux10]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
                $tags = $roomTypes[$iAux10]['tags'];
                if (count($tags) > 0) {
                    for ($iAux7=0; $iAux7 < count($tags); $iAux7++) { 
                        $id = $tags[$iAux7]['id'];
                        $exclusive = $tags[$iAux7]['exclusive'];
                        $facetName = $tags[$iAux7]['facetName'];
                        $groupId = $tags[$iAux7]['groupId'];
                        $ownerId = $tags[$iAux7]['ownerId'];
                        $parentTagId = $tags[$iAux7]['parentTagId'];
                        $title = $tags[$iAux7]['title'];
                        $flags = $tags[$iAux7]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux8=0; $iAux8 < count($flags); $iAux8++) { 
                                $flag = $flags[$iAux8];
                            }
                        }
                    }
                }
            }
        }
        $tagGroups = $accommodation['tagGroups'];
        if (count($tagGroups) > 0) {
            for ($iAux5=0; $iAux5 < count($tagGroups); $iAux5++) { 
                $id = $tagGroups[$iAux5]['id'];
                $exclusive = $tagGroups[$iAux5]['exclusive'];
                $externalId = $tagGroups[$iAux5]['externalId'];
                $facetName = $tagGroups[$iAux5]['facetName'];
                $group = $tagGroups[$iAux5]['group'];
                $ownerId = $tagGroups[$iAux5]['ownerId'];
                $productCategory = $tagGroups[$iAux5]['productCategory'];
                $sharedWithSuppliers = $tagGroups[$iAux5]['sharedWithSuppliers'];
                $subCategory = $tagGroups[$iAux5]['subCategory'];
                $title = $tagGroups[$iAux5]['title'];
                $flags = $tagGroups[$iAux5]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
                $tags = $tagGroups[$iAux5]['tags'];
                if (count($tags) > 0) {
                    for ($iAux7=0; $iAux7 < count($tags); $iAux7++) { 
                        $id = $tags[$iAux7]['id'];
                        $exclusive = $tags[$iAux7]['exclusive'];
                        $facetName = $tags[$iAux7]['facetName'];
                        $groupId = $tags[$iAux7]['groupId'];
                        $ownerId = $tags[$iAux7]['ownerId'];
                        $parentTagId = $tags[$iAux7]['parentTagId'];
                        $title = $tags[$iAux7]['title'];
                        $flags = $tags[$iAux7]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux8=0; $iAux8 < count($flags); $iAux8++) { 
                                $flag = $flags[$iAux8];
                            }
                        }
                    }
                }
            }
        }
        $vendor = $accommodation['vendor'];
        $vendorid = $vendor['id'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendoremailAddress = $vendor['emailAddress'];
        $vendorinvoiceIdNumber = $vendor['invoiceIdNumber'];
        $vendorlogoStyle = $vendor['logoStyle'];
        $vendorphoneNumber = $vendor['phoneNumber'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendortitle = $vendor['title'];
        $vendorwebsite = $vendor['website'];
        $logo = $vendor['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $logo['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
                $flag = $flags[$iAux3];
            }
        }
        $videos = $accommodation['videos'];
        if (count($videos) > 0) {
            for ($iAux2=0; $iAux2 < count($videos); $iAux2++) { 
                $id = $videos[$iAux2]['id'];
                $cleanPreviewUrl = $videos[$iAux2]['cleanPreviewUrl'];
                $cleanThumbnailUrl = $videos[$iAux2]['cleanThumbnailUrl'];
                $html = $videos[$iAux2]['html'];
                $previewUrl = $videos[$iAux2]['previewUrl'];
                $providerName = $videos[$iAux2]['providerName'];
                $sourceUrl = $videos[$iAux2]['sourceUrl'];
                $thumbnailUrl = $videos[$iAux2]['thumbnailUrl'];
            }
        }
        $cancellationPolicy = $accommodation['cancellationPolicy'];
        $cancellationPolicyid = $cancellationPolicy['id'];
        $cancellationPolicytitle = $cancellationPolicy['title'];
        $tax = $cancellationPolicy['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $penaltyRules = $cancellationPolicy['penaltyRules'];
        if (count($penaltyRules) > 0) {
            for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                $id = $penaltyRules[$iAux]['id'];
                $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                $charge = $penaltyRules[$iAux]['charge'];
                $chargeType = $penaltyRules[$iAux]['chargeType'];
            }
        }

        //agent
        $agent = $accommodationBookings[$i]['agent'];
        $agentid = $agent['id'];
        $agentidNumber = $agent['idNumber'];
        $agentreferenceCode = $agent['referenceCode'];
        $agenttitle = $agent['title'];
        $linkedExternalCustomers = $agent['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //allotment
        $allotment = $accommodationBookings[$i]['allotment'];
        $allotmentid = $allotment['id'];
        $allotmenttitle = $allotment['title'];
        //answers
        $answers = $accommodationBookings[$i]['answers'];
        if (count($answers) > 0) {
            for ($k=0; $k < count($answers) ; $k++) { 
                $id = $answers[$k]['id'];
                $answer = $answers[$k]['answer'];
                $group = $answers[$k]['group'];
                $question = $answers[$k]['question'];
                $type = $answers[$k]['type'];
            }
        }
        //barcode
        $barcode = $accommodationBookings[$i]['barcode'];
        $barcodevalue = $barcode['value'];
        $barcodeofflineCode = $barcode['offlineCode'];
        $barcodebarcodeType = $barcode['barcodeType'];
        //boxProduct
        $boxProduct = $accommodationBookings[$i]['boxProduct'];
        $boxProductid = $boxProduct['id'];
        $boxProductexternalId = $boxProduct['externalId'];
        $boxProductprice = $boxProduct['price'];
        $boxProductslug = $boxProduct['slug'];
        $boxProducttitle = $boxProduct['title'];
        $flags = $boxProduct['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        $keyPhoto = $boxProduct['keyPhoto'];
        $keyPhotoid = $keyPhoto['id'];
        $keyPhotooriginalUrl = $keyPhoto['originalUrl'];
        $keyPhotodescription = $keyPhoto['description'];
        $keyPhotoalternateText = $keyPhoto['alternateText'];
        $keyPhotoheight = $keyPhoto['height'];
        $keyPhotowidth = $keyPhoto['width'];
        $keyPhotofileName = $keyPhoto['fileName'];
        $derived = $keyPhoto['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }
        $vendor = $boxProduct['vendor'];
        $vendorid = $vendor['id'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendoremailAddress = $vendor['emailAddress'];
        $vendorinvoiceIdNumber = $vendor['invoiceIdNumber'];
        $vendorlogoStyle = $vendor['logoStyle'];
        $vendorphoneNumber = $vendor['phoneNumber'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendortitle = $vendor['title'];
        $vendorwebsite = $vendor['website'];
        $logo = $vendor['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $logo['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux3=0; $iAux3 < count($flags); $iAux3++) { 
                $flag = $flags[$iAux3];
            }
        }
        //linksToExternalProducts
        $linksToExternalProducts = $accommodationBookings[$i]['linksToExternalProducts'];
        if (count($linksToExternalProducts) > 0) {
            for ($l=0; $l < count($linksToExternalProducts); $l++) { 
                $externalProductId = $linksToExternalProducts[$l]['externalProductId'];
                $externalProductTitle = $linksToExternalProducts[$l]['externalProductTitle'];
                $systemConfigId = $linksToExternalProducts[$l]['systemConfigId'];
                $flags = $linksToExternalProducts[$l]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($lAux=0; $lAux < count($flags); $lAux++) { 
                        $flag = $flags[$lAux];
                    }
                }
            }
        }
        //supplierContractFlags
        $supplierContractFlags = $accommodationBookings[$i]['supplierContractFlags'];
        if (count($supplierContractFlags) > 0) {
            $contract = ""; 
            for ($m=0; $m < count($supplierContractFlags); $m++) { 
                $contract = $supplierContractFlags[$m];
            }
        }
        //sellerContractFlags
        $sellerContractFlags = $accommodationBookings[$i]['sellerContractFlags'];
        if (count($sellerContractFlags) > 0) {
            $contract = ""; 
            for ($m=0; $m < count($sellerContractFlags); $m++) { 
                $contract = $sellerContractFlags[$m];
            }
        }
        //invoice
        $invoice = $accommodationBookings[$i]['invoice'];
        $invoiceid = $invoice['id'];
        $invoicecurrency = $invoice['currency'];
        $invoicedates = $invoice['dates'];
        $invoiceexcludedTaxes = $invoice['excludedTaxes'];
        $invoicefree = $invoice['free'];
        $invoiceincludedTaxes = $invoice['includedTaxes'];
        $invoiceissueDate = $invoice['issueDate'];
        $invoiceproductBookingId = $invoice['productBookingId'];
        $invoiceproductCategory = $invoice['productCategory'];
        $invoiceproductConfirmationCode = $invoice['productConfirmationCode'];
        $invoicetotalAsText = $invoice['totalAsText'];
        $invoicetotalDiscountedAsText = $invoice['totalDiscountedAsText'];
        $invoicetotalDueAsText = $invoice['totalDueAsText'];
        $invoicetotalExcludedTaxAsText = $invoice['totalExcludedTaxAsText'];
        $invoicetotalIncludedTaxAsText = $invoice['totalIncludedTaxAsText'];
        $invoicetotalTaxAsText = $invoice['totalTaxAsText'];

        $issuer = $invoice['issuer'];
        $issuerid = $invoice['id'];
        $issuerexternalId = $invoice['externalId'];
        $issuertitle = $invoice['title'];
        $flags = $invoice['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }

        $customLineItems = $invoice['customLineItems'];
        if (count($customLineItems) > 0) {
            for ($c=0; $c < count($customLineItems); $c++) { 
                $id = $customLineItems[$c]['id'];
                $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                $currency = $customLineItems[$c]['currency'];
                $customDiscount = $customLineItems[$c]['customDiscount'];
                $discount = $customLineItems[$c]['discount'];
                $lineItemType = $customLineItems[$c]['lineItemType'];
                $quantity = $customLineItems[$c]['quantity'];
                $taxAmount = $customLineItems[$c]['taxAmount'];
                $taxAsText = $customLineItems[$c]['taxAsText'];
                $title = $customLineItems[$c]['title'];
                $total = $customLineItems[$c]['total'];
                $totalAsText = $customLineItems[$c]['totalAsText'];
                $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                $totalDue = $customLineItems[$c]['totalDue'];
                $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                $unitPrice = $customLineItems[$c]['unitPrice'];
                $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                $tax = $customLineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $excludedAppliedTaxes = $invoice['excludedAppliedTaxes'];
        if (count($excludedAppliedTaxes) > 0) {
            for ($e=0; $e < count($excludedAppliedTaxes); $e++) { 
                $currency = $excludedAppliedTaxes[$e]['currency'];
                $tax = $excludedAppliedTaxes[$e]['tax'];
                $taxAsText = $excludedAppliedTaxes[$e]['taxAsText'];
                $title = $excludedAppliedTaxes[$e]['title'];
                $taxAsMoney = $excludedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $includedAppliedTaxes = $invoice['includedAppliedTaxes'];
        if (count($includedAppliedTaxes) > 0) {
            for ($e=0; $e < count($includedAppliedTaxes); $e++) { 
                $currency = $includedAppliedTaxes[$e]['currency'];
                $tax = $includedAppliedTaxes[$e]['tax'];
                $taxAsText = $includedAppliedTaxes[$e]['taxAsText'];
                $title = $includedAppliedTaxes[$e]['title'];
                $taxAsMoney = $includedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $lineItems = $invoice['lineItems'];
        if (count($lineItems) > 0) {
            for ($c=0; $c < count($lineItems); $c++) { 
                $id = $lineItems[$c]['id'];
                $calculatedDiscount = $lineItems[$c]['calculatedDiscount'];
                $currency = $lineItems[$c]['currency'];
                $customDiscount = $lineItems[$c]['customDiscount'];
                $discount = $lineItems[$c]['discount'];
                $lineItemType = $lineItems[$c]['lineItemType'];
                $quantity = $lineItems[$c]['quantity'];
                $taxAmount = $lineItems[$c]['taxAmount'];
                $taxAsText = $lineItems[$c]['taxAsText'];
                $title = $lineItems[$c]['title'];
                $total = $lineItems[$c]['total'];
                $totalAsText = $lineItems[$c]['totalAsText'];
                $totalDiscounted = $lineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $lineItems[$c]['totalDiscountedAsText'];
                $totalDue = $lineItems[$c]['totalDue'];
                $totalDueAsText = $lineItems[$c]['totalDueAsText'];
                $unitPrice = $lineItems[$c]['unitPrice'];
                $unitPriceAsText = $lineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $lineItems[$c]['unitPriceDate'];
                $tax = $lineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $lineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $lineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $lineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $lineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $lineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $lodgingTaxes = $invoice['lodgingTaxes'];
        if (count($lodgingTaxes)) {
            for ($l=0; $l < count($lodgingTaxes); $l++) { 
                $currency = $lodgingTaxes[$l]['currency'];
                $tax = $lodgingTaxes[$l]['tax'];
                $taxAsText = $lodgingTaxes[$l]['taxAsText'];
                $title = $lodgingTaxes[$l]['title'];
                $taxAsMoney = $lodgingTaxes[$l]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $paidAmountAsMoney = $invoice['paidAmountAsMoney'];
        $amount = $paidAmountAsMoney['amount'];
        $amountMajor = $paidAmountAsMoney['amountMajor'];
        $amountMajorInt = $paidAmountAsMoney['amountMajorInt'];
        $amountMajorLong = $paidAmountAsMoney['amountMajorLong'];
        $amountMinor = $paidAmountAsMoney['amountMinor'];
        $amountMinorInt = $paidAmountAsMoney['amountMinorInt'];
        $amountMinorLong = $paidAmountAsMoney['amountMinorLong'];
        $minorPart = $paidAmountAsMoney['minorPart'];
        $negative = $paidAmountAsMoney['negative'];
        $negativeOrZero = $paidAmountAsMoney['negativeOrZero'];
        $positive = $paidAmountAsMoney['positive'];
        $positiveOrZero = $paidAmountAsMoney['positiveOrZero'];
        $scale = $paidAmountAsMoney['scale'];
        $zero = $paidAmountAsMoney['zero'];
        $currencyUnit = $paidAmountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $productInvoices = $invoice['productInvoices'];
        if (count($productInvoices) > 0) {
            for ($p=0; $p < count($productInvoices); $p++) { 
                $id = $productInvoices['id'];
                $currency = $productInvoices['currency'];
                $dates = $productInvoices['dates'];
                $excludedTaxes = $productInvoices['excludedTaxes'];
                $free = $productInvoices['free'];
                $includedTaxes = $productInvoices['includedTaxes'];
                $issueDate = $productInvoices['issueDate'];
                $productBookingId = $productInvoices['productBookingId'];
                $productCategory = $productInvoices['productCategory'];
                $productConfirmationCode = $productInvoices['productConfirmationCode'];
                $totalAsText = $productInvoices['totalAsText'];
                $totalDiscountedAsText = $productInvoices['totalDiscountedAsText'];
                $totalDueAsText = $productInvoices['totalDueAsText'];
                $totalExcludedTaxAsText = $productInvoices['totalExcludedTaxAsText'];
                $totalIncludedTaxAsText = $productInvoices['totalIncludedTaxAsText'];
                $totalTaxAsText = $productInvoices['totalTaxAsText'];
                $customLineItems = $productInvoices['customLineItems'];
                if (count($customLineItems) > 0) {
                    for ($c=0; $c < count($customLineItems); $c++) { 
                        $id = $customLineItems[$c]['id'];
                        $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                        $currency = $customLineItems[$c]['currency'];
                        $customDiscount = $customLineItems[$c]['customDiscount'];
                        $discount = $customLineItems[$c]['discount'];
                        $lineItemType = $customLineItems[$c]['lineItemType'];
                        $quantity = $customLineItems[$c]['quantity'];
                        $taxAmount = $customLineItems[$c]['taxAmount'];
                        $taxAsText = $customLineItems[$c]['taxAsText'];
                        $title = $customLineItems[$c]['title'];
                        $total = $customLineItems[$c]['total'];
                        $totalAsText = $customLineItems[$c]['totalAsText'];
                        $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                        $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                        $totalDue = $customLineItems[$c]['totalDue'];
                        $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                        $unitPrice = $customLineItems[$c]['unitPrice'];
                        $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                        $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                        $tax = $customLineItems[$c]['tax'];
                        $taxid = $tax['id'];
                        $taxincluded = $tax['included'];
                        $taxpercentage = $tax['percentage'];
                        $taxtitle = $tax['title'];
                        $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                        $amount = $taxAsMoney['amount'];
                        $amountMajor = $taxAsMoney['amountMajor'];
                        $amountMajorInt = $taxAsMoney['amountMajorInt'];
                        $amountMajorLong = $taxAsMoney['amountMajorLong'];
                        $amountMinor = $taxAsMoney['amountMinor'];
                        $amountMinorInt = $taxAsMoney['amountMinorInt'];
                        $amountMinorLong = $taxAsMoney['amountMinorLong'];
                        $minorPart = $taxAsMoney['minorPart'];
                        $negative = $taxAsMoney['negative'];
                        $negativeOrZero = $taxAsMoney['negativeOrZero'];
                        $positive = $taxAsMoney['positive'];
                        $positiveOrZero = $taxAsMoney['positiveOrZero'];
                        $scale = $taxAsMoney['scale'];
                        $zero = $taxAsMoney['zero'];
                        $currencyUnit = $taxAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                        $totalAsMoneyamount = $totalAsMoney['amount'];
                        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                        $totalAsMoneynegative = $totalAsMoney['negative'];
                        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                        $totalAsMoneypositive = $totalAsMoney['positive'];
                        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                        $totalAsMoneyscale = $totalAsMoney['scale'];
                        $totalAsMoneyzero = $totalAsMoney['zero'];
                        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                    }
                }
            }
        }
        $totalAsMoney = $invoice['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountAsMoney = $invoice['totalDiscountAsMoney'];
        $totalDiscountAsMoneyamount = $totalDiscountAsMoney['amount'];
        $totalDiscountAsMoneyamountMajor = $totalDiscountAsMoney['amountMajor'];
        $totalDiscountAsMoneyamountMajorInt = $totalDiscountAsMoney['amountMajorInt'];
        $totalDiscountAsMoneyamountMajorLong = $totalDiscountAsMoney['amountMajorLong'];
        $totalDiscountAsMoneyamountMinor = $totalDiscountAsMoney['amountMinor'];
        $totalDiscountAsMoneyamountMinorInt = $totalDiscountAsMoney['amountMinorInt'];
        $totalDiscountAsMoneyamountMinorLong = $totalDiscountAsMoney['amountMinorLong'];
        $totalDiscountAsMoneyminorPart = $totalDiscountAsMoney['minorPart'];
        $totalDiscountAsMoneynegative = $totalDiscountAsMoney['negative'];
        $totalDiscountAsMoneynegativeOrZero = $totalDiscountAsMoney['negativeOrZero'];
        $totalDiscountAsMoneypositive = $totalDiscountAsMoney['positive'];
        $totalDiscountAsMoneypositiveOrZero = $totalDiscountAsMoney['positiveOrZero'];
        $totalDiscountAsMoneyscale = $totalDiscountAsMoney['scale'];
        $totalDiscountAsMoneyzero = $totalDiscountAsMoney['zero'];
        $totalDiscountAsMoneycurrencyUnit = $totalDiscountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $invoice['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $invoice['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalExcludedTaxAsMoney = $invoice['totalExcludedTaxAsMoney'];
        $totalExcludedTaxAsMoneyamount = $totalExcludedTaxAsMoney['amount'];
        $totalExcludedTaxAsMoneyamountMajor = $totalExcludedTaxAsMoney['amountMajor'];
        $totalExcludedTaxAsMoneyamountMajorInt = $totalExcludedTaxAsMoney['amountMajorInt'];
        $totalExcludedTaxAsMoneyamountMajorLong = $totalExcludedTaxAsMoney['amountMajorLong'];
        $totalExcludedTaxAsMoneyamountMinor = $totalExcludedTaxAsMoney['amountMinor'];
        $totalExcludedTaxAsMoneyamountMinorInt = $totalExcludedTaxAsMoney['amountMinorInt'];
        $totalExcludedTaxAsMoneyamountMinorLong = $totalExcludedTaxAsMoney['amountMinorLong'];
        $totalExcludedTaxAsMoneyminorPart = $totalExcludedTaxAsMoney['minorPart'];
        $totalExcludedTaxAsMoneynegative = $totalExcludedTaxAsMoney['negative'];
        $totalExcludedTaxAsMoneynegativeOrZero = $totalExcludedTaxAsMoney['negativeOrZero'];
        $totalExcludedTaxAsMoneypositive = $totalExcludedTaxAsMoney['positive'];
        $totalExcludedTaxAsMoneypositiveOrZero = $totalExcludedTaxAsMoney['positiveOrZero'];
        $totalExcludedTaxAsMoneyscale = $totalExcludedTaxAsMoney['scale'];
        $totalExcludedTaxAsMoneyzero = $totalExcludedTaxAsMoney['zero'];
        $totalExcludedTaxAsMoneycurrencyUnit = $totalExcludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalIncludedTaxAsMoney = $invoice['totalIncludedTaxAsMoney'];
        $totalIncludedTaxAsMoneyamount = $totalIncludedTaxAsMoney['amount'];
        $totalIncludedTaxAsMoneyamountMajor = $totalIncludedTaxAsMoney['amountMajor'];
        $totalIncludedTaxAsMoneyamountMajorInt = $totalIncludedTaxAsMoney['amountMajorInt'];
        $totalIncludedTaxAsMoneyamountMajorLong = $totalIncludedTaxAsMoney['amountMajorLong'];
        $totalIncludedTaxAsMoneyamountMinor = $totalIncludedTaxAsMoney['amountMinor'];
        $totalIncludedTaxAsMoneyamountMinorInt = $totalIncludedTaxAsMoney['amountMinorInt'];
        $totalIncludedTaxAsMoneyamountMinorLong = $totalIncludedTaxAsMoney['amountMinorLong'];
        $totalIncludedTaxAsMoneyminorPart = $totalIncludedTaxAsMoney['minorPart'];
        $totalIncludedTaxAsMoneynegative = $totalIncludedTaxAsMoney['negative'];
        $totalIncludedTaxAsMoneynegativeOrZero = $totalIncludedTaxAsMoney['negativeOrZero'];
        $totalIncludedTaxAsMoneypositive = $totalIncludedTaxAsMoney['positive'];
        $totalIncludedTaxAsMoneypositiveOrZero = $totalIncludedTaxAsMoney['positiveOrZero'];
        $totalIncludedTaxAsMoneyscale = $totalIncludedTaxAsMoney['scale'];
        $totalIncludedTaxAsMoneyzero = $totalIncludedTaxAsMoney['zero'];
        $totalIncludedTaxAsMoneycurrencyUnit = $totalIncludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalTaxAsMoney = $invoice['totalTaxAsMoney'];
        $totalTaxAsMoneyamount = $totalTaxAsMoney['amount'];
        $totalTaxAsMoneyamountMajor = $totalTaxAsMoney['amountMajor'];
        $totalTaxAsMoneyamountMajorInt = $totalTaxAsMoney['amountMajorInt'];
        $totalTaxAsMoneyamountMajorLong = $totalTaxAsMoney['amountMajorLong'];
        $totalTaxAsMoneyamountMinor = $totalTaxAsMoney['amountMinor'];
        $totalTaxAsMoneyamountMinorInt = $totalTaxAsMoney['amountMinorInt'];
        $totalTaxAsMoneyamountMinorLong = $totalTaxAsMoney['amountMinorLong'];
        $totalTaxAsMoneyminorPart = $totalTaxAsMoney['minorPart'];
        $totalTaxAsMoneynegative = $totalTaxAsMoney['negative'];
        $totalTaxAsMoneynegativeOrZero = $totalTaxAsMoney['negativeOrZero'];
        $totalTaxAsMoneypositive = $totalTaxAsMoney['positive'];
        $totalTaxAsMoneypositiveOrZero = $totalTaxAsMoney['positiveOrZero'];
        $totalTaxAsMoneyscale = $totalTaxAsMoney['scale'];
        $totalTaxAsMoneyzero = $totalTaxAsMoney['zero'];
        $totalTaxAsMoneycurrencyUnit = $totalTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        //lodgingTax
        $lodgingTax = $accommodationBookings[$i]['lodgingTax'];
        $amount = $lodgingTax['amount'];
        $amountMajor = $lodgingTax['amountMajor'];
        $amountMajorInt = $lodgingTax['amountMajorInt'];
        $amountMajorLong = $lodgingTax['amountMajorLong'];
        $amountMinor = $lodgingTax['amountMinor'];
        $amountMinorInt = $lodgingTax['amountMinorInt'];
        $amountMinorLong = $lodgingTax['amountMinorLong'];
        $minorPart = $lodgingTax['minorPart'];
        $negative = $lodgingTax['negative'];
        $negativeOrZero = $lodgingTax['negativeOrZero'];
        $positive = $lodgingTax['positive'];
        $positiveOrZero = $lodgingTax['positiveOrZero'];
        $scale = $lodgingTax['scale'];
        $zero = $lodgingTax['zero'];
        $currencyUnit = $lodgingTax['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        //notes
        $notes = $accommodationBookings[$i]['notes'];
        if (count($notes) > 0) {
            for ($n=0; $n < count($notes); $n++) { 
                $author = $notes[$n]['author'];
                $body = $notes[$n]['body'];
                $created = $notes[$n]['created'];
                $ownerId = $notes[$n]['ownerId'];
                $recipient = $notes[$n]['recipient'];
                $sentAsEmail = $notes[$n]['sentAsEmail'];
                $subject = $notes[$n]['subject'];
                $type = $notes[$n]['type'];
                $voucherAttached = $notes[$n]['voucherAttached'];
                $voucherPricesShown = $notes[$n]['voucherPricesShown'];
            }
        }
        //parentBooking
        $parentBooking = $accommodationBookings[$i]['parentBooking'];
        //product
        $product = $accommodationBookings[$i]['product'];
        $productid = $product['id'];
        $productexternalId = $product['externalId'];
        $productprice = $product['price'];
        $productslug = $product['slug'];
        $producttitle = $product['title'];
        $keyPhoto = $product['keyPhoto'];
        $keyPhotoid = $keyPhoto['id'];
        $keyPhotooriginalUrl = $keyPhoto['originalUrl'];
        $keyPhotodescription = $keyPhoto['description'];
        $keyPhotoalternateText = $keyPhoto['alternateText'];
        $keyPhotoheight = $keyPhoto['height'];
        $keyPhotowidth = $keyPhoto['width'];
        $keyPhotofileName = $keyPhoto['fileName'];
        $derived = $keyPhoto['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }
        $vendor = $product['vendor'];
        $vendorid = $vendor['id'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendoremailAddress = $vendor['emailAddress'];
        $vendorinvoiceIdNumber = $vendor['invoiceIdNumber'];
        $vendorlogoStyle = $vendor['logoStyle'];
        $vendorphoneNumber = $vendor['phoneNumber'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendortitle = $vendor['title'];
        $vendorwebsite = $vendor['website'];
        $logo = $vendor['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $product['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        //rooms
        $rooms = $accommodationBookings[$i]['rooms'];
        if (count($rooms) > 0) {
            for ($r=0; $r < count($rooms); $r++) { 
                $id = $rooms[$r]['id']; 
                $adults = $rooms[$r]['adults']; 
                $children = $rooms[$r]['children']; 
                $endDate = $rooms[$r]['endDate']; 
                $infants = $rooms[$r]['infants']; 
                $nightCount = $rooms[$r]['nightCount']; 
                $startDate = $rooms[$r]['startDate']; 
                $status = $rooms[$r]['status']; 
                $roomRate = $rooms[$r]['roomRate'];
                $id = $roomRate['id'];
                $maxNightsStay = $roomRate['maxNightsStay'];
                $maxOccupants = $roomRate['maxOccupants'];
                $minNightsStay = $roomRate['minNightsStay'];
                $stayRestrictions = $roomRate['stayRestrictions'];
                $stayRestrictionsAllMonths = $roomRate['stayRestrictionsAllMonths'];
                $stayRestrictionsAllWeekdays = $roomRate['stayRestrictionsAllWeekdays'];
                $title = $roomRate['title'];
                $stayRestrictionsMonths = $roomRate['stayRestrictionsMonths'];
                if (count($stayRestrictionsMonths) > 0) {
                    $months = "";
                    for ($iAux12=0; $iAux12 < count($stayRestrictionsMonths); $iAux12++) { 
                        $months = $stayRestrictionsAllWeekdays[$iAux12];
                    }
                }
                $stayRestrictionsWeekdays = $roomRate['stayRestrictionsWeekdays'];
                if (count($stayRestrictionsAllWeekdays) > 0) {
                    $weekdays = "";
                    for ($iAux13=0; $iAux13 < count($stayRestrictionsAllWeekdays); $iAux13++) { 
                        $weekdays = $stayRestrictionsAllWeekdays[$iAux13];
                    }
                }
                $cancellationPolicy = $roomRate['cancellationPolicy'];
                $cancellationPolicyid = $cancellationPolicy['id'];
                $cancellationPolicytitle = $cancellationPolicy['title'];
                $tax = $cancellationPolicy['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $penaltyRules = $cancellationPolicy['penaltyRules'];
                if (count($penaltyRules) > 0) {
                    for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                        $id = $penaltyRules[$iAux]['id'];
                        $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                        $charge = $penaltyRules[$iAux]['charge'];
                        $chargeType = $penaltyRules[$iAux]['chargeType'];
                    }
                }
                $roomType = $rooms[$r]['roomType'];
                $id = $roomType['id'];
                $accommodationId = $roomType['accommodationId'];
                $accommodationTitle = $roomType['accommodationTitle'];
                $bunkBedCount = $roomType['bunkBedCount'];
                $capacity = $roomType['capacity'];
                $code = $roomType['code'];
                $defaultRateId = $roomType['defaultRateId'];
                $description = $roomType['description'];
                $doubleBedCount = $roomType['doubleBedCount'];
                $excerpt = $roomType['excerpt'];
                $externalId = $roomType['externalId'];
                $extraLargeDoubleBedCount = $roomType['extraLargeDoubleBedCount'];
                $futonMatCount = $roomType['futonMatCount'];
                $internalUseOnly = $roomType['internalUseOnly'];
                $largeDoubleBedCount = $roomType['largeDoubleBedCount'];
                $roomCount = $roomType['roomCount'];
                $shared = $roomType['shared'];
                $singleBedCount = $roomType['singleBedCount'];
                $sofaBedCount = $roomType['sofaBedCount'];
                $title = $roomType['title'];
                $vendorId = $roomType['vendorId'];
                $extras = $roomType['extras'];
                if (count($extras) > 0) {
                    for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                        $id = $extras[$iAux14]['id'];
                        $externalId = $extras[$iAux14]['externalId'];
                        $free = $extras[$iAux14]['free'];
                        $included = $extras[$iAux14]['included'];
                        $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                        $information = $extras[$iAux14]['information'];
                        $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                        $price = $extras[$iAux14]['price'];
                        $pricingType = $extras[$iAux14]['pricingType'];
                        $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                        $title = $extras[$iAux14]['title'];
                        $flags = $extras[$iAux14]['flags'];
                        $questions = $extras[$iAux14]['questions'];
                        if (count($questions) > 0) {
                            for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                                $id = $questions[$iAux15]['id'];
                                $active = $questions[$iAux15]['active'];
                                $answerRequired = $questions[$iAux15]['answerRequired'];
                                $label = $questions[$iAux15]['label'];
                                $options = $questions[$iAux15]['options'];
                                $type = $questions[$iAux15]['type'];
                                $flags = $questions[$iAux15]['flags'];
                                if (count($flags) > 0) {
                                    $flag = "";
                                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                        $flag = $flags[$iAux9];
                                    }
                                }
                            }
                        }
                    }
                }
                $roomRates = $roomType['roomRates'];
                if (count($roomRates) > 0) {
                    for ($iAux11=0; $iAux11 < count($roomRates); $iAux11++) { 
                        $id = $roomRates[$iAux11]['id'];
                        $maxNightsStay = $roomRates[$iAux11]['maxNightsStay'];
                        $maxOccupants = $roomRates[$iAux11]['maxOccupants'];
                        $minNightsStay = $roomRates[$iAux11]['minNightsStay'];
                        $stayRestrictions = $roomRates[$iAux11]['stayRestrictions'];
                        $stayRestrictionsAllMonths = $roomRates[$iAux11]['stayRestrictionsAllMonths'];
                        $stayRestrictionsAllWeekdays = $roomRates[$iAux11]['stayRestrictionsAllWeekdays'];
                        $title = $roomRates[$iAux11]['title'];
                        $stayRestrictionsMonths = $roomRates[$iAux11]['stayRestrictionsMonths'];
                        if (count($stayRestrictionsMonths) > 0) {
                            $months = "";
                            for ($iAux12=0; $iAux12 < count($stayRestrictionsMonths); $iAux12++) { 
                                $months = $stayRestrictionsAllWeekdays[$iAux12];
                            }
                        }
                        $stayRestrictionsWeekdays = $roomRates[$iAux11]['stayRestrictionsWeekdays'];
                        if (count($stayRestrictionsAllWeekdays) > 0) {
                            $weekdays = "";
                            for ($iAux13=0; $iAux13 < count($stayRestrictionsAllWeekdays); $iAux13++) { 
                                $weekdays = $stayRestrictionsAllWeekdays[$iAux13];
                            }
                        }
                        $cancellationPolicy = $roomRates[$iAux11]['cancellationPolicy'];
                        $cancellationPolicyid = $cancellationPolicy['id'];
                        $cancellationPolicytitle = $cancellationPolicy['title'];
                        $tax = $cancellationPolicy['tax'];
                        $taxid = $tax['id'];
                        $taxincluded = $tax['included'];
                        $taxpercentage = $tax['percentage'];
                        $taxtitle = $tax['title'];
                        $penaltyRules = $cancellationPolicy['penaltyRules'];
                        if (count($penaltyRules) > 0) {
                            for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                                $id = $penaltyRules[$iAux]['id'];
                                $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                                $charge = $penaltyRules[$iAux]['charge'];
                                $chargeType = $penaltyRules[$iAux]['chargeType'];
                            }
                        }
                    }
                }
                $defaultRate = $roomType['defaultRate'];
                $defaultRateid = $defaultRate['id'];
                $defaultRatemaxNightsStay = $defaultRate['maxNightsStay'];
                $defaultRatemaxOccupants = $defaultRate['maxOccupants'];
                $defaultRateminNightsStay = $defaultRate['minNightsStay'];
                $defaultRatestayRestrictions = $defaultRate['stayRestrictions'];
                $defaultRatestayRestrictionsAllMonths = $defaultRate['stayRestrictionsAllMonths'];
                $defaultRatestayRestrictionsAllWeekdays = $defaultRate['stayRestrictionsAllWeekdays'];
                $defaultRatetitle = $defaultRate['title'];
                $stayRestrictionsMonths = $defaultRate['stayRestrictionsMonths'];
                if (count($stayRestrictionsMonths) > 0) {
                    $months = "";
                    for ($iAux12=0; $iAux12 < count($stayRestrictionsMonths); $iAux12++) { 
                        $months = $stayRestrictionsAllWeekdays[$iAux12];
                    }
                }
                $stayRestrictionsWeekdays = $defaultRate['stayRestrictionsWeekdays'];
                if (count($stayRestrictionsAllWeekdays) > 0) {
                    $weekdays = "";
                    for ($iAux13=0; $iAux13 < count($stayRestrictionsAllWeekdays); $iAux13++) { 
                        $weekdays = $stayRestrictionsAllWeekdays[$iAux13];
                    }
                }
                $cancellationPolicy = $defaultRate['cancellationPolicy'];
                $cancellationPolicyid = $cancellationPolicy['id'];
                $cancellationPolicytitle = $cancellationPolicy['title'];
                $tax = $cancellationPolicy['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $penaltyRules = $cancellationPolicy['penaltyRules'];
                if (count($penaltyRules) > 0) {
                    for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                        $id = $penaltyRules[$iAux]['id'];
                        $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                        $charge = $penaltyRules[$iAux]['charge'];
                        $chargeType = $penaltyRules[$iAux]['chargeType'];
                    }
                }

                $flags = $roomType['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
                $extras = $rooms[$r]['extras'];
                if (count($extras) > 0) {
                    for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                        $id = $extras[$iAux14]['id'];
                        $externalId = $extras[$iAux14]['externalId'];
                        $free = $extras[$iAux14]['free'];
                        $included = $extras[$iAux14]['included'];
                        $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                        $information = $extras[$iAux14]['information'];
                        $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                        $price = $extras[$iAux14]['price'];
                        $pricingType = $extras[$iAux14]['pricingType'];
                        $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                        $title = $extras[$iAux14]['title'];
                        $flags = $extras[$iAux14]['flags'];
                        $questions = $extras[$iAux14]['questions'];
                        if (count($questions) > 0) {
                            for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                                $id = $questions[$iAux15]['id'];
                                $active = $questions[$iAux15]['active'];
                                $answerRequired = $questions[$iAux15]['answerRequired'];
                                $label = $questions[$iAux15]['label'];
                                $options = $questions[$iAux15]['options'];
                                $type = $questions[$iAux15]['type'];
                                $flags = $questions[$iAux15]['flags'];
                                if (count($flags) > 0) {
                                    $flag = "";
                                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                        $flag = $flags[$iAux9];
                                    }
                                }
                            }
                        }
                    }
                }
                $bookingFields = $rooms[$r]['bookingFields'];
                if (count($bookingFields) > 0) {
                    for ($rAux=0; $rAux < count($bookingFields); $rAux++) { 
                        $name = $bookingFields[$rAux]['name'];
                        $value = $bookingFields[$rAux]['value'];
                    }
                }
                $guests = $rooms[$r]['guests'];
                if (count($guests) > 0) {
                    for ($rAux2=0; $rAux2 < count($guests); $rAux2++) { 
                        $id = $guests[$rAux2]['id'];
                        $uuid = $guests[$rAux2]['uuid'];
                        $firstName = $guests[$rAux2]['firstName'];
                        $lastName = $guests[$rAux2]['lastName'];
                        $email = $guests[$rAux2]['email'];
                        $dateOfBirth = $guests[$rAux2]['dateOfBirth'];
                        $address = $guests[$rAux2]['address'];
                        $contactDetailsHidden = $guests[$rAux2]['contactDetailsHidden'];
                        $contactDetailsHiddenUntil = $guests[$rAux2]['contactDetailsHiddenUntil'];
                        $country = $guests[$rAux2]['country'];
                        $created = $guests[$rAux2]['created'];
                        $language = $guests[$rAux2]['language'];
                        $nationality = $guests[$rAux2]['nationality'];
                        $organization = $guests[$rAux2]['organization'];
                        $passportExpMonth = $guests[$rAux2]['passportExpMonth'];
                        $passportExpYear = $guests[$rAux2]['passportExpYear'];
                        $passportId = $guests[$rAux2]['passportId'];
                        $phoneNumber = $guests[$rAux2]['phoneNumber'];
                        $phoneNumberCountryCode = $guests[$rAux2]['phoneNumberCountryCode'];
                        $place = $guests[$rAux2]['place'];
                        $postCode = $guests[$rAux2]['postCode'];
                        $sex = $guests[$rAux2]['sex'];
                        $state = $guests[$rAux2]['state'];
                        $credentials = $guests[$rAux2]['credentials'];
                        $username = $credentials['username'];
                    }
                }
            }
        }
        //seller
        $seller = $accommodationBookings[$i]['seller'];
        $sellerid = $seller['id'];
        $sellercurrencyCode = $seller['currencyCode'];
        $selleremailAddress = $seller['emailAddress'];
        $sellerinvoiceIdNumber = $seller['invoiceIdNumber'];
        $sellerlogoStyle = $seller['logoStyle'];
        $sellerphoneNumber = $seller['phoneNumber'];
        $sellershowAgentDetailsOnTicket = $seller['showAgentDetailsOnTicket'];
        $sellershowInvoiceIdOnTicket = $seller['showInvoiceIdOnTicket'];
        $sellershowPaymentsOnInvoice = $seller['showPaymentsOnInvoice'];
        $sellertitle = $seller['title'];
        $sellerwebsite = $seller['website'];
        $logo = $seller['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $product['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        $linkedExternalCustomers = $seller['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //supplier
        $supplier = $accommodationBookings[$i]['supplier'];
        $supplierid = $supplier['id'];
        $suppliercurrencyCode = $supplier['currencyCode'];
        $supplieremailAddress = $supplier['emailAddress'];
        $supplierinvoiceIdNumber = $supplier['invoiceIdNumber'];
        $supplierlogoStyle = $supplier['logoStyle'];
        $supplierphoneNumber = $supplier['phoneNumber'];
        $suppliershowAgentDetailsOnTicket = $supplier['showAgentDetailsOnTicket'];
        $suppliershowInvoiceIdOnTicket = $supplier['showInvoiceIdOnTicket'];
        $suppliershowPaymentsOnInvoice = $supplier['showPaymentsOnInvoice'];
        $suppliertitle = $supplier['title'];
        $supplierwebsite = $supplier['website'];
        $logo = $supplier['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $logo['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        $linkedExternalCustomers = $supplier['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
    }
}
$activityBookings = $parentBooking['activityBookings'];
if (count($activityBookings) > 0) {
    for ($i=0; $i < count($activityBookings); $i++) { 
        $bookingId = $activityBookings[$i]['bookingId'];
        $confirmationCode = $activityBookings[$i]['confirmationCode'];
        $productConfirmationCode = $activityBookings[$i]['productConfirmationCode'];
        $parentBookingId = $activityBookings[$i]['parentBookingId'];
        $hasTicket = $activityBookings[$i]['hasTicket'];
        $boxBooking = $activityBookings[$i]['boxBooking'];
        $startDateTime = $activityBookings[$i]['startDateTime'];
        $endDateTime = $activityBookings[$i]['endDateTime'];
        $status = $activityBookings[$i]['status'];
        $includedOnCustomerInvoice = $activityBookings[$i]['includedOnCustomerInvoice'];
        $title = $activityBookings[$i]['title'];
        $totalPrice = $activityBookings[$i]['totalPrice'];
        $priceWithDiscount = $activityBookings[$i]['priceWithDiscount'];
        $discountPercentage = $activityBookings[$i]['discountPercentage'];
        $discountAmount = $activityBookings[$i]['discountAmount'];
        $productCategory = $activityBookings[$i]['productCategory'];
        $paidType = $activityBookings[$i]['paidType'];
        $date = $activityBookings[$i]['date'];
        $startTime = $activityBookings[$i]['startTime'];
        $startTimeId = $activityBookings[$i]['startTimeId'];
        $rateId = $activityBookings[$i]['rateId'];
        $rateTitle = $activityBookings[$i]['rateTitle'];
        $flexible = $activityBookings[$i]['flexible'];
        $productId = $activityBookings[$i]['productId'];
        $customized = $activityBookings[$i]['customized'];
        $customizedDurationMinutes = $activityBookings[$i]['customizedDurationMinutes'];
        $customizedDurationHours = $activityBookings[$i]['customizedDurationHours'];
        $customizedDurationDays = $activityBookings[$i]['customizedDurationDays'];
        $customizedDurationWeeks = $activityBookings[$i]['customizedDurationWeeks'];
        $ticketPerPerson = $activityBookings[$i]['ticketPerPerson'];
        $pickup = $activityBookings[$i]['pickup'];
        $dropoff = $activityBookings[$i]['dropoff'];
        $inventoryConfirmFailed = $activityBookings[$i]['inventoryConfirmFailed'];
        $totalParticipants = $activityBookings[$i]['totalParticipants'];
        $savedAmount = $activityBookings[$i]['savedAmount'];
        $barcode = $activityBookings[$i]['barcode'];
        $barcodevalue = $barcode['value'];
        $barcodeType = $barcode['barcodeType'];
        //product
        $product = $activityBookings[$i]['product'];
        $productid = $product['id'];
        $productexternalId = $product['externalId'];
        $productCategory = $product['productCategory'];
        $producttitle = $product['title'];
        $vendor = $product['vendor'];
        $vendorid = $vendor['id'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendoremailAddress = $vendor['emailAddress'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendortitle = $vendor['title'];
        $vendorcompanyEmailIsDefault = $vendor['companyEmailIsDefault'];
        $flags = $product['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        $cancellationPolicy = $product['cancellationPolicy'];
        $cancellationPolicyid = $cancellationPolicy['id'];
        $cancellationPolicytitle = $cancellationPolicy['title'];
        $defaultPolicy = $cancellationPolicy['defaultPolicy'];
        $tax = $cancellationPolicy['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $penaltyRules = $cancellationPolicy['penaltyRules'];
        if (count($penaltyRules) > 0) {
            for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                $id = $penaltyRules[$iAux]['id'];
                $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                $charge = $penaltyRules[$iAux]['charge'];
                $chargeType = $penaltyRules[$iAux]['chargeType'];
            }
        }
        //supplier
        $supplier = $activityBookings[$i]['supplier'];
        $supplierid = $supplier['id'];
        $suppliercurrencyCode = $supplier['currencyCode'];
        $supplieremailAddress = $supplier['emailAddress'];
        $supplierinvoiceIdNumber = $supplier['invoiceIdNumber'];
        $supplierlogoStyle = $supplier['logoStyle'];
        $supplierphoneNumber = $supplier['phoneNumber'];
        $suppliershowAgentDetailsOnTicket = $supplier['showAgentDetailsOnTicket'];
        $suppliershowInvoiceIdOnTicket = $supplier['showInvoiceIdOnTicket'];
        $suppliershowPaymentsOnInvoice = $supplier['showPaymentsOnInvoice'];
        $suppliertitle = $supplier['title'];
        $supplierwebsite = $supplier['website'];
        $supplierdescription = $supplier['description'];
        $suppliercountryCode = $supplier['countryCode'];
        $suppliertimeZone = $supplier['timeZone'];
        $linkedExternalCustomers = $supplier['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //seller
        $seller = $activityBookings[$i]['seller'];
        $sellerid = $seller['id'];
        $sellercurrencyCode = $seller['currencyCode'];
        $selleremailAddress = $seller['emailAddress'];
        $sellerinvoiceIdNumber = $seller['invoiceIdNumber'];
        $sellerlogoStyle = $seller['logoStyle'];
        $sellerphoneNumber = $seller['phoneNumber'];
        $sellershowAgentDetailsOnTicket = $seller['showAgentDetailsOnTicket'];
        $sellershowInvoiceIdOnTicket = $seller['showInvoiceIdOnTicket'];
        $sellershowPaymentsOnInvoice = $seller['showPaymentsOnInvoice'];
        $sellertitle = $seller['title'];
        $sellerwebsite = $seller['website'];
        $logo = $seller['logo'];
        $logoid = $logo['id'];
        $logoalternateText = $logo['alternateText'];
        $logodescription = $logo['description'];
        $logooriginalUrl = $logo['originalUrl'];
        $derived = $logo['derived'];
        if (count($derived) > 0) {
            for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
                $cleanUrl = $derived[$iAux4]['cleanUrl'];
                $name = $derived[$iAux4]['name'];
                $url = $derived[$iAux4]['url'];
            }
        }
        $flags = $logo['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
        $linkedExternalCustomers = $seller['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //agent
        $agent = $activityBookings[$i]['agent'];
        $agentid = $agent['id'];
        $agenttitle = $agent['title'];
        $linkedExternalCustomers = $agent['linkedExternalCustomers'];
        if (count($linkedExternalCustomers) > 0) {
            for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
                $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
                $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
                $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
                $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
                $systemType = $linkedExternalCustomers[$j]['systemType'];
                $flags = $linkedExternalCustomers[$j]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($jAux=0; $jAux < count($flags); $jAux++) { 
                        $flag = $flags[$jAux];
                    }
                }
            }
        }
        //linksToExternalProducts
        $linksToExternalProducts = $activityBookings[$i]['linksToExternalProducts'];
        if (count($linksToExternalProducts) > 0) {
            for ($l=0; $l < count($linksToExternalProducts); $l++) { 
                $externalProductId = $linksToExternalProducts[$l]['externalProductId'];
                $externalProductTitle = $linksToExternalProducts[$l]['externalProductTitle'];
                $systemConfigId = $linksToExternalProducts[$l]['systemConfigId'];
                $flags = $linksToExternalProducts[$l]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($lAux=0; $lAux < count($flags); $lAux++) { 
                        $flag = $flags[$lAux];
                    }
                }
            }
        }
        //answers
        $answers = $activityBookings[$i]['answers'];
        if (count($answers) > 0) {
            for ($k=0; $k < count($answers) ; $k++) { 
                $id = $answers[$k]['id'];
                $answer = $answers[$k]['answer'];
                $group = $answers[$k]['group'];
                $question = $answers[$k]['question'];
                $type = $answers[$k]['type'];
            }
        }
        //invoice
        $invoice = $activityBookings[$i]['invoice'];
        $invoiceid = $invoice['id'];
        $invoicecurrency = $invoice['currency'];
        $invoicedates = $invoice['dates'];
        $invoiceexcludedTaxes = $invoice['excludedTaxes'];
        $invoicefree = $invoice['free'];
        $invoiceincludedTaxes = $invoice['includedTaxes'];
        $invoiceissueDate = $invoice['issueDate'];
        $invoiceproductBookingId = $invoice['productBookingId'];
        $invoiceproductCategory = $invoice['productCategory'];
        $invoiceproductConfirmationCode = $invoice['productConfirmationCode'];
        $invoicetotalAsText = $invoice['totalAsText'];
        $invoicetotalDiscountedAsText = $invoice['totalDiscountedAsText'];
        $invoicetotalDueAsText = $invoice['totalDueAsText'];
        $invoicetotalExcludedTaxAsText = $invoice['totalExcludedTaxAsText'];
        $invoicetotalIncludedTaxAsText = $invoice['totalIncludedTaxAsText'];
        $invoicetotalTaxAsText = $invoice['totalTaxAsText'];

        $issuer = $invoice['issuer'];
        $issuerid = $issuer['id'];
        $issuerexternalId = $issuer['externalId'];
        $issuertitle = $issuer['title'];
        $flags = $issuer['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }

        $customLineItems = $invoice['customLineItems'];
        if (count($customLineItems) > 0) {
            for ($c=0; $c < count($customLineItems); $c++) { 
                $id = $customLineItems[$c]['id'];
                $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                $currency = $customLineItems[$c]['currency'];
                $customDiscount = $customLineItems[$c]['customDiscount'];
                $discount = $customLineItems[$c]['discount'];
                $lineItemType = $customLineItems[$c]['lineItemType'];
                $quantity = $customLineItems[$c]['quantity'];
                $taxAmount = $customLineItems[$c]['taxAmount'];
                $taxAsText = $customLineItems[$c]['taxAsText'];
                $title = $customLineItems[$c]['title'];
                $total = $customLineItems[$c]['total'];
                $totalAsText = $customLineItems[$c]['totalAsText'];
                $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                $totalDue = $customLineItems[$c]['totalDue'];
                $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                $unitPrice = $customLineItems[$c]['unitPrice'];
                $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                $tax = $customLineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $excludedAppliedTaxes = $invoice['excludedAppliedTaxes'];
        if (count($excludedAppliedTaxes) > 0) {
            for ($e=0; $e < count($excludedAppliedTaxes); $e++) { 
                $currency = $excludedAppliedTaxes[$e]['currency'];
                $tax = $excludedAppliedTaxes[$e]['tax'];
                $taxAsText = $excludedAppliedTaxes[$e]['taxAsText'];
                $title = $excludedAppliedTaxes[$e]['title'];
                $taxAsMoney = $excludedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $includedAppliedTaxes = $invoice['includedAppliedTaxes'];
        if (count($includedAppliedTaxes) > 0) {
            for ($e=0; $e < count($includedAppliedTaxes); $e++) { 
                $currency = $includedAppliedTaxes[$e]['currency'];
                $tax = $includedAppliedTaxes[$e]['tax'];
                $taxAsText = $includedAppliedTaxes[$e]['taxAsText'];
                $title = $includedAppliedTaxes[$e]['title'];
                $taxAsMoney = $includedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $lineItems = $invoice['lineItems'];
        if (count($lineItems) > 0) {
            for ($c=0; $c < count($lineItems); $c++) { 
                $id = $lineItems[$c]['id'];
                $calculatedDiscount = $lineItems[$c]['calculatedDiscount'];
                $currency = $lineItems[$c]['currency'];
                $customDiscount = $lineItems[$c]['customDiscount'];
                $discount = $lineItems[$c]['discount'];
                $lineItemType = $lineItems[$c]['lineItemType'];
                $quantity = $lineItems[$c]['quantity'];
                $taxAmount = $lineItems[$c]['taxAmount'];
                $taxAsText = $lineItems[$c]['taxAsText'];
                $title = $lineItems[$c]['title'];
                $total = $lineItems[$c]['total'];
                $totalAsText = $lineItems[$c]['totalAsText'];
                $totalDiscounted = $lineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $lineItems[$c]['totalDiscountedAsText'];
                $totalDue = $lineItems[$c]['totalDue'];
                $totalDueAsText = $lineItems[$c]['totalDueAsText'];
                $unitPrice = $lineItems[$c]['unitPrice'];
                $unitPriceAsText = $lineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $lineItems[$c]['unitPriceDate'];
                $tax = $lineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $lineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $lineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $lineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $lineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $lineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $lodgingTaxes = $invoice['lodgingTaxes'];
        if (count($lodgingTaxes)) {
            for ($l=0; $l < count($lodgingTaxes); $l++) { 
                $currency = $lodgingTaxes[$l]['currency'];
                $tax = $lodgingTaxes[$l]['tax'];
                $taxAsText = $lodgingTaxes[$l]['taxAsText'];
                $title = $lodgingTaxes[$l]['title'];
                $taxAsMoney = $lodgingTaxes[$l]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $paidAmountAsMoney = $invoice['paidAmountAsMoney'];
        $amount = $paidAmountAsMoney['amount'];
        $amountMajor = $paidAmountAsMoney['amountMajor'];
        $amountMajorInt = $paidAmountAsMoney['amountMajorInt'];
        $amountMajorLong = $paidAmountAsMoney['amountMajorLong'];
        $amountMinor = $paidAmountAsMoney['amountMinor'];
        $amountMinorInt = $paidAmountAsMoney['amountMinorInt'];
        $amountMinorLong = $paidAmountAsMoney['amountMinorLong'];
        $minorPart = $paidAmountAsMoney['minorPart'];
        $negative = $paidAmountAsMoney['negative'];
        $negativeOrZero = $paidAmountAsMoney['negativeOrZero'];
        $positive = $paidAmountAsMoney['positive'];
        $positiveOrZero = $paidAmountAsMoney['positiveOrZero'];
        $scale = $paidAmountAsMoney['scale'];
        $zero = $paidAmountAsMoney['zero'];
        $currencyUnit = $paidAmountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $productInvoices = $invoice['productInvoices'];
        if (count($productInvoices) > 0) {
            for ($p=0; $p < count($productInvoices); $p++) { 
                $id = $productInvoices['id'];
                $currency = $productInvoices['currency'];
                $dates = $productInvoices['dates'];
                $excludedTaxes = $productInvoices['excludedTaxes'];
                $free = $productInvoices['free'];
                $includedTaxes = $productInvoices['includedTaxes'];
                $issueDate = $productInvoices['issueDate'];
                $productBookingId = $productInvoices['productBookingId'];
                $productCategory = $productInvoices['productCategory'];
                $productConfirmationCode = $productInvoices['productConfirmationCode'];
                $totalAsText = $productInvoices['totalAsText'];
                $totalDiscountedAsText = $productInvoices['totalDiscountedAsText'];
                $totalDueAsText = $productInvoices['totalDueAsText'];
                $totalExcludedTaxAsText = $productInvoices['totalExcludedTaxAsText'];
                $totalIncludedTaxAsText = $productInvoices['totalIncludedTaxAsText'];
                $totalTaxAsText = $productInvoices['totalTaxAsText'];
                $customLineItems = $productInvoices['customLineItems'];
                if (count($customLineItems) > 0) {
                    for ($c=0; $c < count($customLineItems); $c++) { 
                        $id = $customLineItems[$c]['id'];
                        $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                        $currency = $customLineItems[$c]['currency'];
                        $customDiscount = $customLineItems[$c]['customDiscount'];
                        $discount = $customLineItems[$c]['discount'];
                        $lineItemType = $customLineItems[$c]['lineItemType'];
                        $quantity = $customLineItems[$c]['quantity'];
                        $taxAmount = $customLineItems[$c]['taxAmount'];
                        $taxAsText = $customLineItems[$c]['taxAsText'];
                        $title = $customLineItems[$c]['title'];
                        $total = $customLineItems[$c]['total'];
                        $totalAsText = $customLineItems[$c]['totalAsText'];
                        $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                        $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                        $totalDue = $customLineItems[$c]['totalDue'];
                        $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                        $unitPrice = $customLineItems[$c]['unitPrice'];
                        $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                        $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                        $tax = $customLineItems[$c]['tax'];
                        $taxid = $tax['id'];
                        $taxincluded = $tax['included'];
                        $taxpercentage = $tax['percentage'];
                        $taxtitle = $tax['title'];
                        $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                        $amount = $taxAsMoney['amount'];
                        $amountMajor = $taxAsMoney['amountMajor'];
                        $amountMajorInt = $taxAsMoney['amountMajorInt'];
                        $amountMajorLong = $taxAsMoney['amountMajorLong'];
                        $amountMinor = $taxAsMoney['amountMinor'];
                        $amountMinorInt = $taxAsMoney['amountMinorInt'];
                        $amountMinorLong = $taxAsMoney['amountMinorLong'];
                        $minorPart = $taxAsMoney['minorPart'];
                        $negative = $taxAsMoney['negative'];
                        $negativeOrZero = $taxAsMoney['negativeOrZero'];
                        $positive = $taxAsMoney['positive'];
                        $positiveOrZero = $taxAsMoney['positiveOrZero'];
                        $scale = $taxAsMoney['scale'];
                        $zero = $taxAsMoney['zero'];
                        $currencyUnit = $taxAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                        $totalAsMoneyamount = $totalAsMoney['amount'];
                        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                        $totalAsMoneynegative = $totalAsMoney['negative'];
                        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                        $totalAsMoneypositive = $totalAsMoney['positive'];
                        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                        $totalAsMoneyscale = $totalAsMoney['scale'];
                        $totalAsMoneyzero = $totalAsMoney['zero'];
                        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                    }
                }
            }
        }
        $totalAsMoney = $invoice['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountAsMoney = $invoice['totalDiscountAsMoney'];
        $totalDiscountAsMoneyamount = $totalDiscountAsMoney['amount'];
        $totalDiscountAsMoneyamountMajor = $totalDiscountAsMoney['amountMajor'];
        $totalDiscountAsMoneyamountMajorInt = $totalDiscountAsMoney['amountMajorInt'];
        $totalDiscountAsMoneyamountMajorLong = $totalDiscountAsMoney['amountMajorLong'];
        $totalDiscountAsMoneyamountMinor = $totalDiscountAsMoney['amountMinor'];
        $totalDiscountAsMoneyamountMinorInt = $totalDiscountAsMoney['amountMinorInt'];
        $totalDiscountAsMoneyamountMinorLong = $totalDiscountAsMoney['amountMinorLong'];
        $totalDiscountAsMoneyminorPart = $totalDiscountAsMoney['minorPart'];
        $totalDiscountAsMoneynegative = $totalDiscountAsMoney['negative'];
        $totalDiscountAsMoneynegativeOrZero = $totalDiscountAsMoney['negativeOrZero'];
        $totalDiscountAsMoneypositive = $totalDiscountAsMoney['positive'];
        $totalDiscountAsMoneypositiveOrZero = $totalDiscountAsMoney['positiveOrZero'];
        $totalDiscountAsMoneyscale = $totalDiscountAsMoney['scale'];
        $totalDiscountAsMoneyzero = $totalDiscountAsMoney['zero'];
        $totalDiscountAsMoneycurrencyUnit = $totalDiscountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $invoice['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $invoice['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalExcludedTaxAsMoney = $invoice['totalExcludedTaxAsMoney'];
        $totalExcludedTaxAsMoneyamount = $totalExcludedTaxAsMoney['amount'];
        $totalExcludedTaxAsMoneyamountMajor = $totalExcludedTaxAsMoney['amountMajor'];
        $totalExcludedTaxAsMoneyamountMajorInt = $totalExcludedTaxAsMoney['amountMajorInt'];
        $totalExcludedTaxAsMoneyamountMajorLong = $totalExcludedTaxAsMoney['amountMajorLong'];
        $totalExcludedTaxAsMoneyamountMinor = $totalExcludedTaxAsMoney['amountMinor'];
        $totalExcludedTaxAsMoneyamountMinorInt = $totalExcludedTaxAsMoney['amountMinorInt'];
        $totalExcludedTaxAsMoneyamountMinorLong = $totalExcludedTaxAsMoney['amountMinorLong'];
        $totalExcludedTaxAsMoneyminorPart = $totalExcludedTaxAsMoney['minorPart'];
        $totalExcludedTaxAsMoneynegative = $totalExcludedTaxAsMoney['negative'];
        $totalExcludedTaxAsMoneynegativeOrZero = $totalExcludedTaxAsMoney['negativeOrZero'];
        $totalExcludedTaxAsMoneypositive = $totalExcludedTaxAsMoney['positive'];
        $totalExcludedTaxAsMoneypositiveOrZero = $totalExcludedTaxAsMoney['positiveOrZero'];
        $totalExcludedTaxAsMoneyscale = $totalExcludedTaxAsMoney['scale'];
        $totalExcludedTaxAsMoneyzero = $totalExcludedTaxAsMoney['zero'];
        $totalExcludedTaxAsMoneycurrencyUnit = $totalExcludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalIncludedTaxAsMoney = $invoice['totalIncludedTaxAsMoney'];
        $totalIncludedTaxAsMoneyamount = $totalIncludedTaxAsMoney['amount'];
        $totalIncludedTaxAsMoneyamountMajor = $totalIncludedTaxAsMoney['amountMajor'];
        $totalIncludedTaxAsMoneyamountMajorInt = $totalIncludedTaxAsMoney['amountMajorInt'];
        $totalIncludedTaxAsMoneyamountMajorLong = $totalIncludedTaxAsMoney['amountMajorLong'];
        $totalIncludedTaxAsMoneyamountMinor = $totalIncludedTaxAsMoney['amountMinor'];
        $totalIncludedTaxAsMoneyamountMinorInt = $totalIncludedTaxAsMoney['amountMinorInt'];
        $totalIncludedTaxAsMoneyamountMinorLong = $totalIncludedTaxAsMoney['amountMinorLong'];
        $totalIncludedTaxAsMoneyminorPart = $totalIncludedTaxAsMoney['minorPart'];
        $totalIncludedTaxAsMoneynegative = $totalIncludedTaxAsMoney['negative'];
        $totalIncludedTaxAsMoneynegativeOrZero = $totalIncludedTaxAsMoney['negativeOrZero'];
        $totalIncludedTaxAsMoneypositive = $totalIncludedTaxAsMoney['positive'];
        $totalIncludedTaxAsMoneypositiveOrZero = $totalIncludedTaxAsMoney['positiveOrZero'];
        $totalIncludedTaxAsMoneyscale = $totalIncludedTaxAsMoney['scale'];
        $totalIncludedTaxAsMoneyzero = $totalIncludedTaxAsMoney['zero'];
        $totalIncludedTaxAsMoneycurrencyUnit = $totalIncludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalTaxAsMoney = $invoice['totalTaxAsMoney'];
        $totalTaxAsMoneyamount = $totalTaxAsMoney['amount'];
        $totalTaxAsMoneyamountMajor = $totalTaxAsMoney['amountMajor'];
        $totalTaxAsMoneyamountMajorInt = $totalTaxAsMoney['amountMajorInt'];
        $totalTaxAsMoneyamountMajorLong = $totalTaxAsMoney['amountMajorLong'];
        $totalTaxAsMoneyamountMinor = $totalTaxAsMoney['amountMinor'];
        $totalTaxAsMoneyamountMinorInt = $totalTaxAsMoney['amountMinorInt'];
        $totalTaxAsMoneyamountMinorLong = $totalTaxAsMoney['amountMinorLong'];
        $totalTaxAsMoneyminorPart = $totalTaxAsMoney['minorPart'];
        $totalTaxAsMoneynegative = $totalTaxAsMoney['negative'];
        $totalTaxAsMoneynegativeOrZero = $totalTaxAsMoney['negativeOrZero'];
        $totalTaxAsMoneypositive = $totalTaxAsMoney['positive'];
        $totalTaxAsMoneypositiveOrZero = $totalTaxAsMoney['positiveOrZero'];
        $totalTaxAsMoneyscale = $totalTaxAsMoney['scale'];
        $totalTaxAsMoneyzero = $totalTaxAsMoney['zero'];
        $totalTaxAsMoneycurrencyUnit = $totalTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        //sellerInvoice
        $sellerInvoice = $activityBookings[$i]['sellerInvoice'];
        $sellerInvoiceid = $sellerInvoice['id'];
        $sellerInvoicecurrency = $sellerInvoice['currency'];
        $sellerInvoicedates = $sellerInvoice['dates'];
        $sellerInvoiceexcludedTaxes = $sellerInvoice['excludedTaxes'];
        $sellerInvoicefree = $sellerInvoice['free'];
        $sellerInvoiceincludedTaxes = $sellerInvoice['includedTaxes'];
        $sellerInvoiceissueDate = $sellerInvoice['issueDate'];
        $sellerInvoiceproductBookingId = $sellerInvoice['productBookingId'];
        $sellerInvoiceproductCategory = $sellerInvoice['productCategory'];
        $sellerInvoiceproductConfirmationCode = $sellerInvoice['productConfirmationCode'];
        $sellerInvoicetotalAsText = $sellerInvoice['totalAsText'];
        $sellerInvoicetotalDiscountedAsText = $sellerInvoice['totalDiscountedAsText'];
        $sellerInvoicetotalDueAsText = $sellerInvoice['totalDueAsText'];
        $sellerInvoicetotalExcludedTaxAsText = $sellerInvoice['totalExcludedTaxAsText'];
        $sellerInvoicetotalIncludedTaxAsText = $sellerInvoice['totalIncludedTaxAsText'];
        $sellerInvoicetotalTaxAsText = $sellerInvoice['totalTaxAsText'];

        $issuer = $sellerInvoice['issuer'];
        $issuerid = $issuer['id'];
        $issuerexternalId = $issuer['externalId'];
        $issuertitle = $issuer['title'];
        $flags = $issuer['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }

        $customLineItems = $sellerInvoice['customLineItems'];
        if (count($customLineItems) > 0) {
            for ($c=0; $c < count($customLineItems); $c++) { 
                $id = $customLineItems[$c]['id'];
                $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                $currency = $customLineItems[$c]['currency'];
                $customDiscount = $customLineItems[$c]['customDiscount'];
                $discount = $customLineItems[$c]['discount'];
                $lineItemType = $customLineItems[$c]['lineItemType'];
                $quantity = $customLineItems[$c]['quantity'];
                $taxAmount = $customLineItems[$c]['taxAmount'];
                $taxAsText = $customLineItems[$c]['taxAsText'];
                $title = $customLineItems[$c]['title'];
                $total = $customLineItems[$c]['total'];
                $totalAsText = $customLineItems[$c]['totalAsText'];
                $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                $totalDue = $customLineItems[$c]['totalDue'];
                $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                $unitPrice = $customLineItems[$c]['unitPrice'];
                $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                $tax = $customLineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $excludedAppliedTaxes = $sellerInvoice['excludedAppliedTaxes'];
        if (count($excludedAppliedTaxes) > 0) {
            for ($e=0; $e < count($excludedAppliedTaxes); $e++) { 
                $currency = $excludedAppliedTaxes[$e]['currency'];
                $tax = $excludedAppliedTaxes[$e]['tax'];
                $taxAsText = $excludedAppliedTaxes[$e]['taxAsText'];
                $title = $excludedAppliedTaxes[$e]['title'];
                $taxAsMoney = $excludedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $includedAppliedTaxes = $sellerInvoice['includedAppliedTaxes'];
        if (count($includedAppliedTaxes) > 0) {
            for ($e=0; $e < count($includedAppliedTaxes); $e++) { 
                $currency = $includedAppliedTaxes[$e]['currency'];
                $tax = $includedAppliedTaxes[$e]['tax'];
                $taxAsText = $includedAppliedTaxes[$e]['taxAsText'];
                $title = $includedAppliedTaxes[$e]['title'];
                $taxAsMoney = $includedAppliedTaxes[$e]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                        $country = $countryCodes[$eAux];
                    }
                }
            }
        }
        $lineItems = $sellerInvoice['lineItems'];
        if (count($lineItems) > 0) {
            for ($c=0; $c < count($lineItems); $c++) { 
                $id = $lineItems[$c]['id'];
                $calculatedDiscount = $lineItems[$c]['calculatedDiscount'];
                $currency = $lineItems[$c]['currency'];
                $customDiscount = $lineItems[$c]['customDiscount'];
                $discount = $lineItems[$c]['discount'];
                $lineItemType = $lineItems[$c]['lineItemType'];
                $quantity = $lineItems[$c]['quantity'];
                $taxAmount = $lineItems[$c]['taxAmount'];
                $taxAsText = $lineItems[$c]['taxAsText'];
                $title = $lineItems[$c]['title'];
                $total = $lineItems[$c]['total'];
                $totalAsText = $lineItems[$c]['totalAsText'];
                $totalDiscounted = $lineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $lineItems[$c]['totalDiscountedAsText'];
                $totalDue = $lineItems[$c]['totalDue'];
                $totalDueAsText = $lineItems[$c]['totalDueAsText'];
                $unitPrice = $lineItems[$c]['unitPrice'];
                $unitPriceAsText = $lineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $lineItems[$c]['unitPriceDate'];
                $tax = $lineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $lineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $lineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $lineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $lineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $lineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $lodgingTaxes = $sellerInvoice['lodgingTaxes'];
        if (count($lodgingTaxes)) {
            for ($l=0; $l < count($lodgingTaxes); $l++) { 
                $currency = $lodgingTaxes[$l]['currency'];
                $tax = $lodgingTaxes[$l]['tax'];
                $taxAsText = $lodgingTaxes[$l]['taxAsText'];
                $title = $lodgingTaxes[$l]['title'];
                $taxAsMoney = $lodgingTaxes[$l]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
        $paidAmountAsMoney = $sellerInvoice['paidAmountAsMoney'];
        $amount = $paidAmountAsMoney['amount'];
        $amountMajor = $paidAmountAsMoney['amountMajor'];
        $amountMajorInt = $paidAmountAsMoney['amountMajorInt'];
        $amountMajorLong = $paidAmountAsMoney['amountMajorLong'];
        $amountMinor = $paidAmountAsMoney['amountMinor'];
        $amountMinorInt = $paidAmountAsMoney['amountMinorInt'];
        $amountMinorLong = $paidAmountAsMoney['amountMinorLong'];
        $minorPart = $paidAmountAsMoney['minorPart'];
        $negative = $paidAmountAsMoney['negative'];
        $negativeOrZero = $paidAmountAsMoney['negativeOrZero'];
        $positive = $paidAmountAsMoney['positive'];
        $positiveOrZero = $paidAmountAsMoney['positiveOrZero'];
        $scale = $paidAmountAsMoney['scale'];
        $zero = $paidAmountAsMoney['zero'];
        $currencyUnit = $paidAmountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $productsellerInvoices = $sellerInvoice['productsellerInvoices'];
        if (count($productsellerInvoices) > 0) {
            for ($p=0; $p < count($productsellerInvoices); $p++) { 
                $id = $productsellerInvoices['id'];
                $currency = $productsellerInvoices['currency'];
                $dates = $productsellerInvoices['dates'];
                $excludedTaxes = $productsellerInvoices['excludedTaxes'];
                $free = $productsellerInvoices['free'];
                $includedTaxes = $productsellerInvoices['includedTaxes'];
                $issueDate = $productsellerInvoices['issueDate'];
                $productBookingId = $productsellerInvoices['productBookingId'];
                $productCategory = $productsellerInvoices['productCategory'];
                $productConfirmationCode = $productsellerInvoices['productConfirmationCode'];
                $totalAsText = $productsellerInvoices['totalAsText'];
                $totalDiscountedAsText = $productsellerInvoices['totalDiscountedAsText'];
                $totalDueAsText = $productsellerInvoices['totalDueAsText'];
                $totalExcludedTaxAsText = $productsellerInvoices['totalExcludedTaxAsText'];
                $totalIncludedTaxAsText = $productsellerInvoices['totalIncludedTaxAsText'];
                $totalTaxAsText = $productsellerInvoices['totalTaxAsText'];
                $customLineItems = $productsellerInvoices['customLineItems'];
                if (count($customLineItems) > 0) {
                    for ($c=0; $c < count($customLineItems); $c++) { 
                        $id = $customLineItems[$c]['id'];
                        $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                        $currency = $customLineItems[$c]['currency'];
                        $customDiscount = $customLineItems[$c]['customDiscount'];
                        $discount = $customLineItems[$c]['discount'];
                        $lineItemType = $customLineItems[$c]['lineItemType'];
                        $quantity = $customLineItems[$c]['quantity'];
                        $taxAmount = $customLineItems[$c]['taxAmount'];
                        $taxAsText = $customLineItems[$c]['taxAsText'];
                        $title = $customLineItems[$c]['title'];
                        $total = $customLineItems[$c]['total'];
                        $totalAsText = $customLineItems[$c]['totalAsText'];
                        $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                        $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                        $totalDue = $customLineItems[$c]['totalDue'];
                        $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                        $unitPrice = $customLineItems[$c]['unitPrice'];
                        $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                        $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                        $tax = $customLineItems[$c]['tax'];
                        $taxid = $tax['id'];
                        $taxincluded = $tax['included'];
                        $taxpercentage = $tax['percentage'];
                        $taxtitle = $tax['title'];
                        $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                        $amount = $taxAsMoney['amount'];
                        $amountMajor = $taxAsMoney['amountMajor'];
                        $amountMajorInt = $taxAsMoney['amountMajorInt'];
                        $amountMajorLong = $taxAsMoney['amountMajorLong'];
                        $amountMinor = $taxAsMoney['amountMinor'];
                        $amountMinorInt = $taxAsMoney['amountMinorInt'];
                        $amountMinorLong = $taxAsMoney['amountMinorLong'];
                        $minorPart = $taxAsMoney['minorPart'];
                        $negative = $taxAsMoney['negative'];
                        $negativeOrZero = $taxAsMoney['negativeOrZero'];
                        $positive = $taxAsMoney['positive'];
                        $positiveOrZero = $taxAsMoney['positiveOrZero'];
                        $scale = $taxAsMoney['scale'];
                        $zero = $taxAsMoney['zero'];
                        $currencyUnit = $taxAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                        $totalAsMoneyamount = $totalAsMoney['amount'];
                        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                        $totalAsMoneynegative = $totalAsMoney['negative'];
                        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                        $totalAsMoneypositive = $totalAsMoney['positive'];
                        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                        $totalAsMoneyscale = $totalAsMoney['scale'];
                        $totalAsMoneyzero = $totalAsMoney['zero'];
                        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                        $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                        $currencyUnitcode = $currencyUnit['code'];
                        $currencyCode = $currencyUnit['currencyCode'];
                        $decimalPlaces = $currencyUnit['decimalPlaces'];
                        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                        $numeric3Code = $currencyUnit['numeric3Code'];
                        $numericCode = $currencyUnit['numericCode'];
                        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                        $symbol = $currencyUnit['symbol'];
                        $countryCodes = $currencyUnit['countryCodes'];
                        if (count($countryCodes) > 0) {
                            $country = "";
                            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                                $country = $countryCodes[$cAux];
                            }
                        }
                    }
                }
            }
        }
        $totalAsMoney = $sellerInvoice['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountAsMoney = $sellerInvoice['totalDiscountAsMoney'];
        $totalDiscountAsMoneyamount = $totalDiscountAsMoney['amount'];
        $totalDiscountAsMoneyamountMajor = $totalDiscountAsMoney['amountMajor'];
        $totalDiscountAsMoneyamountMajorInt = $totalDiscountAsMoney['amountMajorInt'];
        $totalDiscountAsMoneyamountMajorLong = $totalDiscountAsMoney['amountMajorLong'];
        $totalDiscountAsMoneyamountMinor = $totalDiscountAsMoney['amountMinor'];
        $totalDiscountAsMoneyamountMinorInt = $totalDiscountAsMoney['amountMinorInt'];
        $totalDiscountAsMoneyamountMinorLong = $totalDiscountAsMoney['amountMinorLong'];
        $totalDiscountAsMoneyminorPart = $totalDiscountAsMoney['minorPart'];
        $totalDiscountAsMoneynegative = $totalDiscountAsMoney['negative'];
        $totalDiscountAsMoneynegativeOrZero = $totalDiscountAsMoney['negativeOrZero'];
        $totalDiscountAsMoneypositive = $totalDiscountAsMoney['positive'];
        $totalDiscountAsMoneypositiveOrZero = $totalDiscountAsMoney['positiveOrZero'];
        $totalDiscountAsMoneyscale = $totalDiscountAsMoney['scale'];
        $totalDiscountAsMoneyzero = $totalDiscountAsMoney['zero'];
        $totalDiscountAsMoneycurrencyUnit = $totalDiscountAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $sellerInvoice['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $sellerInvoice['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalExcludedTaxAsMoney = $sellerInvoice['totalExcludedTaxAsMoney'];
        $totalExcludedTaxAsMoneyamount = $totalExcludedTaxAsMoney['amount'];
        $totalExcludedTaxAsMoneyamountMajor = $totalExcludedTaxAsMoney['amountMajor'];
        $totalExcludedTaxAsMoneyamountMajorInt = $totalExcludedTaxAsMoney['amountMajorInt'];
        $totalExcludedTaxAsMoneyamountMajorLong = $totalExcludedTaxAsMoney['amountMajorLong'];
        $totalExcludedTaxAsMoneyamountMinor = $totalExcludedTaxAsMoney['amountMinor'];
        $totalExcludedTaxAsMoneyamountMinorInt = $totalExcludedTaxAsMoney['amountMinorInt'];
        $totalExcludedTaxAsMoneyamountMinorLong = $totalExcludedTaxAsMoney['amountMinorLong'];
        $totalExcludedTaxAsMoneyminorPart = $totalExcludedTaxAsMoney['minorPart'];
        $totalExcludedTaxAsMoneynegative = $totalExcludedTaxAsMoney['negative'];
        $totalExcludedTaxAsMoneynegativeOrZero = $totalExcludedTaxAsMoney['negativeOrZero'];
        $totalExcludedTaxAsMoneypositive = $totalExcludedTaxAsMoney['positive'];
        $totalExcludedTaxAsMoneypositiveOrZero = $totalExcludedTaxAsMoney['positiveOrZero'];
        $totalExcludedTaxAsMoneyscale = $totalExcludedTaxAsMoney['scale'];
        $totalExcludedTaxAsMoneyzero = $totalExcludedTaxAsMoney['zero'];
        $totalExcludedTaxAsMoneycurrencyUnit = $totalExcludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalIncludedTaxAsMoney = $sellerInvoice['totalIncludedTaxAsMoney'];
        $totalIncludedTaxAsMoneyamount = $totalIncludedTaxAsMoney['amount'];
        $totalIncludedTaxAsMoneyamountMajor = $totalIncludedTaxAsMoney['amountMajor'];
        $totalIncludedTaxAsMoneyamountMajorInt = $totalIncludedTaxAsMoney['amountMajorInt'];
        $totalIncludedTaxAsMoneyamountMajorLong = $totalIncludedTaxAsMoney['amountMajorLong'];
        $totalIncludedTaxAsMoneyamountMinor = $totalIncludedTaxAsMoney['amountMinor'];
        $totalIncludedTaxAsMoneyamountMinorInt = $totalIncludedTaxAsMoney['amountMinorInt'];
        $totalIncludedTaxAsMoneyamountMinorLong = $totalIncludedTaxAsMoney['amountMinorLong'];
        $totalIncludedTaxAsMoneyminorPart = $totalIncludedTaxAsMoney['minorPart'];
        $totalIncludedTaxAsMoneynegative = $totalIncludedTaxAsMoney['negative'];
        $totalIncludedTaxAsMoneynegativeOrZero = $totalIncludedTaxAsMoney['negativeOrZero'];
        $totalIncludedTaxAsMoneypositive = $totalIncludedTaxAsMoney['positive'];
        $totalIncludedTaxAsMoneypositiveOrZero = $totalIncludedTaxAsMoney['positiveOrZero'];
        $totalIncludedTaxAsMoneyscale = $totalIncludedTaxAsMoney['scale'];
        $totalIncludedTaxAsMoneyzero = $totalIncludedTaxAsMoney['zero'];
        $totalIncludedTaxAsMoneycurrencyUnit = $totalIncludedTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalTaxAsMoney = $sellerInvoice['totalTaxAsMoney'];
        $totalTaxAsMoneyamount = $totalTaxAsMoney['amount'];
        $totalTaxAsMoneyamountMajor = $totalTaxAsMoney['amountMajor'];
        $totalTaxAsMoneyamountMajorInt = $totalTaxAsMoney['amountMajorInt'];
        $totalTaxAsMoneyamountMajorLong = $totalTaxAsMoney['amountMajorLong'];
        $totalTaxAsMoneyamountMinor = $totalTaxAsMoney['amountMinor'];
        $totalTaxAsMoneyamountMinorInt = $totalTaxAsMoney['amountMinorInt'];
        $totalTaxAsMoneyamountMinorLong = $totalTaxAsMoney['amountMinorLong'];
        $totalTaxAsMoneyminorPart = $totalTaxAsMoney['minorPart'];
        $totalTaxAsMoneynegative = $totalTaxAsMoney['negative'];
        $totalTaxAsMoneynegativeOrZero = $totalTaxAsMoney['negativeOrZero'];
        $totalTaxAsMoneypositive = $totalTaxAsMoney['positive'];
        $totalTaxAsMoneypositiveOrZero = $totalTaxAsMoney['positiveOrZero'];
        $totalTaxAsMoneyscale = $totalTaxAsMoney['scale'];
        $totalTaxAsMoneyzero = $totalTaxAsMoney['zero'];
        $totalTaxAsMoneycurrencyUnit = $totalTaxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        //notes
        $notes = $activityBookings[$i]['notes'];
        if (count($notes) > 0) {
            for ($n=0; $n < count($notes); $n++) { 
                $author = $notes[$n]['author'];
                $body = $notes[$n]['body'];
                $created = $notes[$n]['created'];
                $ownerId = $notes[$n]['ownerId'];
                $recipient = $notes[$n]['recipient'];
                $sentAsEmail = $notes[$n]['sentAsEmail'];
                $subject = $notes[$n]['subject'];
                $type = $notes[$n]['type'];
                $voucherAttached = $notes[$n]['voucherAttached'];
                $voucherPricesShown = $notes[$n]['voucherPricesShown'];
            }
        }
        //supplierContractFlags
        $supplierContractFlags = $activityBookings[$i]['supplierContractFlags'];
        if (count($supplierContractFlags) > 0) {
            $contract = ""; 
            for ($m=0; $m < count($supplierContractFlags); $m++) { 
                $contract = $supplierContractFlags[$m];
            }
        }
        //sellerContractFlags
        $sellerContractFlags = $activityBookings[$i]['sellerContractFlags'];
        if (count($sellerContractFlags) > 0) {
            $contract = ""; 
            for ($m=0; $m < count($sellerContractFlags); $m++) { 
                $contract = $sellerContractFlags[$m];
            }
        }
        //cancellationPolicy
        $cancellationPolicy = $activityBookings[$i]['cancellationPolicy'];
        $cancellationPolicyid = $cancellationPolicy['id'];
        $cancellationPolicytitle = $cancellationPolicy['title'];
        $defaultPolicy = $cancellationPolicy['defaultPolicy'];
        $tax = $cancellationPolicy['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $penaltyRules = $cancellationPolicy['penaltyRules'];
        if (count($penaltyRules) > 0) {
            for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                $id = $penaltyRules[$iAux]['id'];
                $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                $charge = $penaltyRules[$iAux]['charge'];
                $chargeType = $penaltyRules[$iAux]['chargeType'];
            }
        }
        $bookingRoles = $activityBookings[$i]['bookingRoles'];
        if (count($bookingRoles) > 0) {
            $roles = "";
            for ($iAux2=0; $iAux2 < count($bookingRoles); $iAux2++) { 
                $roles = $bookingRoles[$iAux2];
            }
        }
        //pricingCategoryBookings
        $pricingCategoryBookings = $activityBookings[$i]['pricingCategoryBookings'];
        if (count($pricingCategoryBookings) > 0) {
            for ($iAux3=0; $iAux3 < count($pricingCategoryBookings); $iAux3++) { 
                $id = $pricingCategoryBookings[$iAux3]['id'];
                $pricingCategoryId = $pricingCategoryBookings[$iAux3]['pricingCategoryId'];
                $leadPassenger = $pricingCategoryBookings[$iAux3]['leadPassenger'];
                $age = $pricingCategoryBookings[$iAux3]['age'];
                $bookedTitle = $pricingCategoryBookings[$iAux3]['bookedTitle'];
                $quantity = $pricingCategoryBookings[$iAux3]['quantity'];
                $pricingCategory = $pricingCategoryBookings[$iAux3]['pricingCategory'];
                $pricingCategoryid = $pricingCategory['id'];
                $pricingCategorytitle = $pricingCategory['title'];
                $pricingCategoryticketCategory = $pricingCategory['ticketCategory'];
                $pricingCategoryoccupancy = $pricingCategory['occupancy'];
                $pricingCategorygroupSize = $pricingCategory['groupSize'];
                $pricingCategoryageQualified = $pricingCategory['ageQualified'];
                $pricingCategoryminAge = $pricingCategory['minAge'];
                $pricingCategorymaxAge = $pricingCategory['maxAge'];
                $pricingCategorydependent = $pricingCategory['dependent'];
                $pricingCategorymasterCategoryId = $pricingCategory['masterCategoryId'];
                $pricingCategorymaxPerMaster = $pricingCategory['maxPerMaster'];
                $pricingCategorysumDependentCategories = $pricingCategory['sumDependentCategories'];
                $pricingCategorymaxDependentSum = $pricingCategory['maxDependentSum'];
                $pricingCategoryinternalUseOnly = $pricingCategory['internalUseOnly'];
                $pricingCategorydefaultCategory = $pricingCategory['defaultCategory'];
                $pricingCategoryfullTitle = $pricingCategory['fullTitle'];
                $flags = $pricingCategory['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($lAux=0; $lAux < count($flags); $lAux++) { 
                        $flag = $flags[$lAux];
                    }
                }
                $extras = $pricingCategoryBookings[$iAux3]['extras'];
                if (count($extras) > 0) {
                    for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                        $id = $extras[$iAux14]['id'];
                        $externalId = $extras[$iAux14]['externalId'];
                        $free = $extras[$iAux14]['free'];
                        $included = $extras[$iAux14]['included'];
                        $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                        $information = $extras[$iAux14]['information'];
                        $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                        $price = $extras[$iAux14]['price'];
                        $pricingType = $extras[$iAux14]['pricingType'];
                        $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                        $title = $extras[$iAux14]['title'];
                        $flags = $extras[$iAux14]['flags'];
                        $questions = $extras[$iAux14]['questions'];
                        if (count($questions) > 0) {
                            for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                                $id = $questions[$iAux15]['id'];
                                $active = $questions[$iAux15]['active'];
                                $answerRequired = $questions[$iAux15]['answerRequired'];
                                $label = $questions[$iAux15]['label'];
                                $options = $questions[$iAux15]['options'];
                                $type = $questions[$iAux15]['type'];
                                $flags = $questions[$iAux15]['flags'];
                                if (count($flags) > 0) {
                                    $flag = "";
                                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                        $flag = $flags[$iAux9];
                                    }
                                }
                            }
                        }
                    }
                }
                $answers = $pricingCategoryBookings[$iAux3]['answers'];
                if (count($answers) > 0) {
                    for ($k=0; $k < count($answers) ; $k++) { 
                        $id = $answers[$k]['id'];
                        $answer = $answers[$k]['answer'];
                        $group = $answers[$k]['group'];
                        $question = $answers[$k]['question'];
                        $type = $answers[$k]['type'];
                    }
                }
                $flags = $pricingCategoryBookings[$iAux3]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($lAux=0; $lAux < count($flags); $lAux++) { 
                        $flag = $flags[$lAux];
                    }
                }
            }
        }
        //extras
        $extras = $activityBookings[$i]['extras'];
        if (count($extras) > 0) {
            for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                $id = $extras[$iAux14]['id'];
                $externalId = $extras[$iAux14]['externalId'];
                $free = $extras[$iAux14]['free'];
                $included = $extras[$iAux14]['included'];
                $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                $information = $extras[$iAux14]['information'];
                $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                $price = $extras[$iAux14]['price'];
                $pricingType = $extras[$iAux14]['pricingType'];
                $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                $title = $extras[$iAux14]['title'];
                $flags = $extras[$iAux14]['flags'];
                $questions = $extras[$iAux14]['questions'];
                if (count($questions) > 0) {
                    for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                        $id = $questions[$iAux15]['id'];
                        $active = $questions[$iAux15]['active'];
                        $answerRequired = $questions[$iAux15]['answerRequired'];
                        $label = $questions[$iAux15]['label'];
                        $options = $questions[$iAux15]['options'];
                        $type = $questions[$iAux15]['type'];
                        $flags = $questions[$iAux15]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                $flag = $flags[$iAux9];
                            }
                        }
                    }
                }
            }
        }
        //bookingFields
        $bookingFields = $activityBookings[$i]['bookingFields'];
        if (count($bookingFields) > 0) {
            for ($rAux=0; $rAux < count($bookingFields); $rAux++) { 
                $name = $bookingFields[$rAux]['name'];
                $value = $bookingFields[$rAux]['value'];
            }
        }
        //bookedPricingCategories
        $bookedPricingCategories = $activityBookings[$i]['bookedPricingCategories'];
        if (count($bookedPricingCategories) > 0) {
            for ($iAux5=0; $iAux5 < count($bookedPricingCategories); $iAux5++) { 
                $id = $bookedPricingCategories[$iAux5]['id'];
                $title = $bookedPricingCategories[$iAux5]['title'];
                $ticketCategory = $bookedPricingCategories[$iAux5]['ticketCategory'];
                $occupancy = $bookedPricingCategories[$iAux5]['occupancy'];
                $groupSize = $bookedPricingCategories[$iAux5]['groupSize'];
                $ageQualified = $bookedPricingCategories[$iAux5]['ageQualified'];
                $minAge = $bookedPricingCategories[$iAux5]['minAge'];
                $maxAge = $bookedPricingCategories[$iAux5]['maxAge'];
                $dependent = $bookedPricingCategories[$iAux5]['dependent'];
                $masterCategoryId = $bookedPricingCategories[$iAux5]['masterCategoryId'];
                $maxPerMaster = $bookedPricingCategories[$iAux5]['maxPerMaster'];
                $sumDependentCategories = $bookedPricingCategories[$iAux5]['sumDependentCategories'];
                $maxDependentSum = $bookedPricingCategories[$iAux5]['maxDependentSum'];
                $internalUseOnly = $bookedPricingCategories[$iAux5]['internalUseOnly'];
                $defaultCategory = $bookedPricingCategories[$iAux5]['defaultCategory'];
                $fullTitle = $bookedPricingCategories[$iAux5]['fullTitle'];
                $flags = $bookedPricingCategories[$iAux5]['flags'];
                if (count($flags) > 0) {
                    $flag = "";
                    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                        $flag = $flags[$iAux9];
                    }
                }
            }
        }
        //activity
        $activity = $activityBookings[$i]['activity'];
        $activityid = $activity['id'];
        $activityexternalId = $activity['externalId'];
        $activityproductGroupId = $activity['productGroupId'];
        $activityproductCategory = $activity['productCategory'];
        $activitybox = $activity['box'];
        $activityinventoryLocal = $activity['inventoryLocal'];
        $activityinventorySupportsPricing = $activity['inventorySupportsPricing'];
        $activityinventorySupportsAvailability = $activity['inventorySupportsAvailability'];
        $activitycreationDate = $activity['creationDate'];
        $activitylastModified = $activity['lastModified'];
        $activitylastPublished = $activity['lastPublished'];
        $activitypublished = $activity['published'];
        $activitytitle = $activity['title'];
        $activitydescription = $activity['description'];
        $activityexcerpt = $activity['excerpt'];
        $cancellationPolicy = $activity['cancellationPolicy'];
        if ($cancellationPolicy != null) {
            $cancellationPolicyid = $cancellationPolicy['id'];
            $cancellationPolicytitle = $cancellationPolicy['title'];
            $defaultPolicy = $cancellationPolicy['defaultPolicy'];
            $tax = $cancellationPolicy['tax'];
            $taxid = $tax['id'];
            $taxincluded = $tax['included'];
            $taxpercentage = $tax['percentage'];
            $taxtitle = $tax['title'];
            $penaltyRules = $cancellationPolicy['penaltyRules'];
            if (count($penaltyRules) > 0) {
                for ($iAux=0; $iAux < count($penaltyRules); $iAux++) { 
                    $id = $penaltyRules[$iAux]['id'];
                    $cutoffHours = $penaltyRules[$iAux]['cutoffHours'];
                    $charge = $penaltyRules[$iAux]['charge'];
                    $chargeType = $penaltyRules[$iAux]['chargeType'];
                }
            }
        }
        $activityoverrideBarcodeFormat = $activity['overrideBarcodeFormat'];
        $activitybarcodeType = $activity['barcodeType'];
        $activitytimeZone = $activity['timeZone'];
        $activityslug = $activity['slug'];
        $activitybaseLanguage = $activity['baseLanguage'];
        $activityboxedVendor = $activity['boxedVendor'];
        $activitystoredExternally = $activity['storedExternally'];
        $activitypluginId = $activity['pluginId'];
        $activityreviewRating = $activity['reviewRating'];
        $activityreviewCount = $activity['reviewCount'];
        $activityactivityType = $activity['activityType'];
        $activitybookingType = $activity['bookingType'];
        $activityscheduleType = $activity['scheduleType'];
        $activitycapacityType = $activity['capacityType'];
        $activitypassExpiryType = $activity['passExpiryType'];
        $activityfixedPassExpiryDate = $activity['fixedPassExpiryDate'];
        $activitymeetingType = $activity['meetingType'];
        $activityprivateActivity = $activity['privateActivity'];
        $activitypassCapacity = $activity['passCapacity'];
        $activitypassValidForDays = $activity['passValidForDays'];
        $activitypassesAvailable = $activity['passesAvailable'];
        $activitydressCode = $activity['dressCode'];
        $activitypassportRequired = $activity['passportRequired'];
        $activityincluded = $activity['included'];
        $activityexcluded = $activity['excluded'];
        $activityrequirements = $activity['requirements'];
        $activityattention = $activity['attention'];
        $activitylocationCode = $activity['locationCode'];
        $activitybookingCutoffMinutes = $activity['bookingCutoffMinutes'];
        $activitybookingCutoffHours = $activity['bookingCutoffHours'];
        $activitybookingCutoffDays = $activity['bookingCutoffDays'];
        $activitybookingCutoffWeeks = $activity['bookingCutoffWeeks'];
        $activityrequestDeadlineMinutes = $activity['requestDeadlineMinutes'];
        $activityrequestDeadlineHours = $activity['requestDeadlineHours'];
        $activityrequestDeadlineDays = $activity['requestDeadlineDays'];
        $activityrequestDeadlineWeeks = $activity['requestDeadlineWeeks'];
        $activityboxedActivityId = $activity['boxedActivityId'];
        $activitycomboActivity = $activity['comboActivity'];
        $activityticketPerComboComponent = $activity['ticketPerComboComponent'];
        $activitypickupActivityId = $activity['pickupActivityId'];
        $activityallowCustomizedBookings = $activity['allowCustomizedBookings'];
        $activitydayBasedAvailability = $activity['dayBasedAvailability'];
        $activityselectFromDayOptions = $activity['selectFromDayOptions'];
        $activitydefaultRateId = $activity['defaultRateId'];
        $activityticketPerPerson = $activity['ticketPerPerson'];
        $activitydurationType = $activity['durationType'];
        $activityduration = $activity['duration'];
        $activitydurationMinutes = $activity['durationMinutes'];
        $activitydurationHours = $activity['durationHours'];
        $activitydurationDays = $activity['durationDays'];
        $activitydurationWeeks = $activity['durationWeeks'];
        $activitydurationText = $activity['durationText'];
        $activityminAge = $activity['minAge'];
        $activitynextDefaultPrice = $activity['nextDefaultPrice'];
        $activitynextDefaultPriceMoney = $activity['nextDefaultPriceMoney'];
        $activitypickupService = $activity['pickupService'];
        $activitypickupAllotment = $activity['pickupAllotment'];
        $activitypickupAllotmentType = $activity['pickupAllotmentType'];
        $activityuseComponentPickupAllotments = $activity['useComponentPickupAllotments'];
        $activitycustomPickupAllowed = $activity['customPickupAllowed'];
        $activitypickupMinutesBefore = $activity['pickupMinutesBefore'];
        $activitynoPickupMsg = $activity['noPickupMsg'];
        $activityticketMsg = $activity['ticketMsg'];
        $activityshowGlobalPickupMsg = $activity['showGlobalPickupMsg'];
        $activityshowNoPickupMsg = $activity['showNoPickupMsg'];
        $activitydropoffService = $activity['dropoffService'];
        $activitycustomDropoffAllowed = $activity['customDropoffAllowed'];
        $activityuseSameAsPickUpPlaces = $activity['useSameAsPickUpPlaces'];
        $activitydifficultyLevel = $activity['difficultyLevel'];
        $activityhasOpeningHours = $activity['hasOpeningHours'];
        $activitydefaultOpeningHours = $activity['defaultOpeningHours'];
        $activityhasBoxes = $activity['hasBoxes'];
        $activityrequestDeadline = $activity['requestDeadline'];
        $activitybookingCutoff = $activity['bookingCutoff'];
        $activityactualId = $activity['actualId'];
        $activitynextDefaultPriceAsText = $activity['nextDefaultPriceAsText'];
        //
        $mainContactFields = $activity['mainContactFields'];
        if (count($mainContactFields) > 0) {
            for ($k=0; $k < count($mainContactFields); $k++) { 
                $field = $mainContactFields[$k]['field'];
                $required = $mainContactFields[$k]['required'];
            }
        }
        $requiredCustomerFields = $activity['requiredCustomerFields'];
        if (count($requiredCustomerFields) > 0) {
            $customerfields = "";
            for ($l=0; $l < count($requiredCustomerFields); $l++) { 
                $customfields = $requiredCustomerFields[$l];
            }
        }
        $keywords = $activity['keywords'];
        $flags = $activity['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                $flag = $flags[$iAux9];
            }
        }
        $languages = $activity['languages'];
        if (count($languages) > 0) {
            $language = "";
            for ($l=0; $l < count($languages); $l++) { 
                $language = $languages[$l];
            }
        }
        $paymentCurrencies = $activity['paymentCurrencies'];
        if (count($paymentCurrencies) > 0) {
            $payment = "";
            for ($z=0; $z < count($paymentCurrencies); $z++) { 
                $payment = $paymentCurrencies[$z];
            }
        }
        $customFields = $activity['customFields'];
        if (count($customFields) > 0) {
            for ($j=0; $j < count($customFields); $j++) { 
                $type = $customFields[$j]['title'];
                $inputFieldId = $customFields[$j]['inputFieldId'];
                $value = $customFields[$j]['value'];
            }
        }
        $tagGroups = $activity['tagGroups'];
        $categories = $activity['categories'];
        $keyPhoto = $activity['keyPhoto'];
        $keyPhotoid = $keyPhoto['id'];
        $keyPhotooriginalUrl = $keyPhoto['originalUrl'];
        $keyPhotodescription = $keyPhoto['description'];
        $keyPhotoalternateText = $keyPhoto['alternateText'];
        $keyPhotoheight = $keyPhoto['height'];
        $keyPhotowidth = $keyPhoto['width'];
        $keyPhotofileName = $keyPhoto['fileName'];
        $derived = $keyPhoto['derived'];
        if (count($derived) > 0) {
            for ($d=0; $d < count($derived); $d++) { 
                $name = $derived[$d]['name'];
                $url = $derived[$d]['url'];
                $cleanUrl = $derived[$d]['cleanUrl'];
            }
        }
        $photos = $activity['photos'];
        if (count($photos) > 0) {
            for ($f=0; $f < count($photos); $f++) { 
                $photosid = $photos[$f]['id'];
                $photosoriginalUrl = $photos[$f]['originalUrl'];
                $photosdescription = $photos[$f]['description'];
                $photosalternateText = $photos[$f]['alternateText'];
                $photosheight = $photos[$f]['height'];
                $photoswidth = $photos[$f]['width'];
                $photosfileName = $photos[$f]['fileName'];
                $derived = $photos[$f]['derived'];
                if (count($derived) > 0) {
                    for ($d=0; $d < count($derived); $d++) { 
                        $name = $derived[$d]['name'];
                        $url = $derived[$d]['url'];
                        $cleanUrl = $derived[$d]['cleanUrl'];
                    }
                }
            }
        }
        $videos = $activity['videos'];
        $vendor = $activity['vendor'];
        $vendorid = $vendor['id'];
        $vendortitle = $vendor['title'];
        $vendorcurrencyCode = $vendor['currencyCode'];
        $vendortimeZone = $vendor['timeZone'];
        $vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
        $vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
        $vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
        $vendorcompanyEmailIsDefault = $vendor['companyEmailIsDefault'];
        $startPoints = $activity['startPoints'];
        if (count($startPoints) > 0) {
            for ($c=0; $c < count($startPoints); $c++) { 
                $startpointsid = $startPoints[$c]['id'];
                $type = $startPoints[$c]['type'];
                $title = $startPoints[$c]['title'];
                $code = $startPoints[$c]['code'];
                $pickupTicketDescription = $startPoints[$c]['pickupTicketDescription'];
                $dropoffTicketDescription = $startPoints[$c]['dropoffTicketDescription'];
                $address = $startPoints[$c]['address'];
                $addressid = $address['id'];
                $addressLine1 = $address['addressLine1'];
                $addressLine2 = $address['addressLine2'];
                $addressLine3 = $address['addressLine3'];
                $city = $address['city'];
                $state = $address['state'];
                $postalCode = $address['postalCode'];
                $countryCode = $address['countryCode'];
                $mapZoomLevel = $address['mapZoomLevel'];
                $origin = $address['origin'];
                $originId = $address['originId'];
                $geoPoint = $address['geoPoint'];
                $latitude = $geoPoint['latitude'];
                $longitude = $geoPoint['longitude'];
                $unLocode = $address['unLocode'];
                $unlocodecountry = $unLocode['country'];
                $unlocodecity = $unLocode['city'];
                $created = $address['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
        
                $created = $startPoints[$c]['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
            }
        }
        $bookingQuestions = $activity['bookingQuestions'];
        if (count($bookingQuestions) > 0) {
            for ($x=0; $x < count($bookingQuestions); $x++) { 
                $bookingquestionsid = $bookingQuestions[$x]['id'];
                $personalData = $bookingQuestions[$x]['personalData'];
                $questionCode = $bookingQuestions[$x]['questionCode'];
                $label = $bookingQuestions[$x]['label'];
                $help = $bookingQuestions[$x]['help'];
                $placeholder = $bookingQuestions[$x]['placeholder'];
                $required = $bookingQuestions[$x]['required'];
                $defaultValue = $bookingQuestions[$x]['defaultValue'];
                $dataType = $bookingQuestions[$x]['dataType'];
                $selectFromOptions = $bookingQuestions[$x]['selectFromOptions'];
                $selectMultiple = $bookingQuestions[$x]['selectMultiple'];
                $context = $bookingQuestions[$x]['context'];
                $pricingCategoryTriggerSelection = $bookingQuestions[$x]['pricingCategoryTriggerSelection'];
                $rateTriggerSelection = $bookingQuestions[$x]['rateTriggerSelection'];
                $extraTriggerSelection = $bookingQuestions[$x]['extraTriggerSelection'];  
                $options = $bookingQuestions[$x]['options'];
                if (count($options) > 0) {
                    for ($xAux=0; $xAux < count($options); $xAux++) { 
                        $name = $options[$xAux]['name'];
                        $value = $options[$xAux]['value'];
                    }
                }
            }
        }
        $passengerFields = $activity['passengerFields'];
        if (count($passengerFields) > 0) {
            for ($y=0; $y < count($passengerFields); $y++) { 
                $field = $passengerFields[$y]['field'];
                $required = $passengerFields[$y]['required'];
            }
        }
        $inclusions = $activity['inclusions'];
        $exclusions = $activity['exclusions'];
        $googlePlace = $activity['googlePlace'];
        $googlePlacecountry = $googlePlace['country'];
        $googlePlacecountryCode = $googlePlace['countryCode'];
        $googlePlacecity = $googlePlace['city'];
        $googlePlacecityCode = $googlePlace['cityCode'];
        $geoLocationCenter = $googlePlace['geoLocationCenter'];
        $lat = $geoLocationCenter['lat'];
        $lng = $geoLocationCenter['lng'];
        $resourceSlots = $activity['resourceSlots'];
        $comboParts = $activity['comboParts'];
        $ticketComboComponents = $activity['ticketComboComponents'];
        $dayOptions = $activity['dayOptions'];
        $activityCategories = $activity['activityCategories'];
        if (count($activityCategories) > 0) {
            $activity = "";
            for ($w=0; $w < count($activityCategories); $w++) { 
                $activity = $activityCategories[$w];
            }
        }
        $activityAttributes = $activity['activityAttributes'];
        $guidanceTypes = $activity['guidanceTypes'];
        if (count($guidanceTypes) > 0) {
            for ($s=0; $s < count($guidanceTypes); $s++) { 
                $guidancetypesid = $guidanceTypes[$s]['id'];
                $guidanceType = $guidanceTypes[$s]['guidanceType'];
                $created = $guidanceTypes[$s]['created'];
                if (count($created) > 0) {
                    for ($cAux=0; $cAux < count($created); $cAux++) { 
                        $created = $created[$cAux];
                    }
                }
                $languages = $guidanceTypes[$s]['languages'];
                if (count($languages) > 0) {
                    $language = "";
                    for ($cAux=0; $cAux < count($languages); $cAux++) { 
                        $language = $languages[$cAux];
                    }
                }
            }
        }
        $rates = $activity['rates'];
        if (count($rates) > 0) {
            for ($r=0; $r < count($rates); $r++) { 
                $ratesid = $rates[$r]['id'];
                $title = $rates[$r]['title'];
                $description = $rates[$r]['description'];
                $index = $rates[$r]['index'];
                $rateCode = $rates[$r]['rateCode'];
                $pricedPerPerson = $rates[$r]['pricedPerPerson'];
                $minPerBooking = $rates[$r]['minPerBooking'];
                $maxPerBooking = $rates[$r]['maxPerBooking'];
                $fixedPassExpiryDate = $rates[$r]['fixedPassExpiryDate'];
                $passValidForDays = $rates[$r]['passValidForDays'];
                $pickupSelectionType = $rates[$r]['pickupSelectionType'];
                $pickupPricingType = $rates[$r]['pickupPricingType'];
                $pickupPricedPerPerson = $rates[$r]['pickupPricedPerPerson'];
                $dropoffSelectionType = $rates[$r]['dropoffSelectionType'];
                $dropoffPricingType = $rates[$r]['dropoffPricingType'];
                $dropoffPricedPerPerson = $rates[$r]['dropoffPricedPerPerson'];
                $allStartTimes = $rates[$r]['allStartTimes'];
                $tieredPricingEnabled = $rates[$r]['tieredPricingEnabled'];
                $allPricingCategories = $rates[$r]['allPricingCategories'];
                $cancellationPolicy = $rates[$r]['cancellationPolicy'];
                $cancellationPolicyid = $cancellationPolicy['id'];
                $cancellationPolicytitle = $cancellationPolicy['title'];
                $cancellationPolicytax = $cancellationPolicy['tax'];
                $defaultPolicy = $cancellationPolicy['defaultPolicy'];
                $penaltyRules = $cancellationPolicy['penaltyRules'];
                if (count($penaltyRules) > 0) {
                    for ($i=0; $i < count($penaltyRules); $i++) { 
                        $penaltyrulesid = $penaltyRules[$i]['id'];
                        $chargeType = $penaltyRules[$i]['chargeType'];
                        $charge = $penaltyRules[$i]['charge'];
                        $cutoffHours = $penaltyRules[$i]['cutoffHours'];
                    }
                }
                $extraConfigs = $rates[$r]['extraConfigs'];
                if (count($extraConfigs) > 0) {
                    for ($z=0; $z < count($extraConfigs); $z++) { 
                        $extraconfigsid = $extraConfigs[$z]['id'];
                        $activityExtraId = $extraConfigs[$z]['activityExtraId'];
                        $selectionType = $extraConfigs[$z]['selectionType'];
                        $pricingType = $extraConfigs[$z]['pricingType'];
                        $pricedPerPerson = $extraConfigs[$z]['pricedPerPerson'];       
                        $created = $extraConfigs[$z]['created'];
                        if (count($created) > 0) {
                            for ($cAux=0; $cAux < count($created); $cAux++) { 
                                $created = $created[$cAux];
                            }
                        }
                    }
                }
                $startTimeIds = $rates[$r]['startTimeIds'];
                if (count($startTimeIds) > 0) {
                    $start = "";
                    for ($st=0; $st < count($startTimeIds); $st++) { 
                        $start = $startTimeIds[$st];
                    }
                }
                $pricingCategoryIds = $rates[$r]['pricingCategoryIds'];
                if (count($pricingCategoryIds) > 0) {
                    $pricing = "";
                    for ($p=0; $p < count($pricingCategoryIds) ; $p++) { 
                        $pricing = $pricingCategoryIds[$p];
                    }
                }
            }
        }
        $pickupFlags = $activity['pickupFlags'];
        $pickupPlaceGroups = $activity['pickupPlaceGroups'];
        $dropoffFlags = $activity['dropoffFlags'];
        $dropoffPlaceGroups = $activity['dropoffPlaceGroups'];
        $pricingCategories = $activity['pricingCategories'];
        if (count($pricingCategories) > 0) {
            for ($p=0; $p < count($pricingCategories); $p++) { 
                $pricingcategoriesid = $pricingCategories[$p]['id'];
                $title = $pricingCategories[$p]['title'];
                $ticketCategory = $pricingCategories[$p]['ticketCategory'];
                $occupancy = $pricingCategories[$p]['occupancy'];
                $groupSize = $pricingCategories[$p]['groupSize'];
                $ageQualified = $pricingCategories[$p]['ageQualified'];
                $minAge = $pricingCategories[$p]['minAge'];
                $maxAge = $pricingCategories[$p]['maxAge'];
                $dependent = $pricingCategories[$p]['dependent'];
                $masterCategoryId = $pricingCategories[$p]['masterCategoryId'];
                $maxPerMaster = $pricingCategories[$p]['maxPerMaster'];
                $sumDependentCategories = $pricingCategories[$p]['sumDependentCategories'];
                $maxDependentSum = $pricingCategories[$p]['maxDependentSum'];
                $internalUseOnly = $pricingCategories[$p]['internalUseOnly'];
                $defaultCategory = $pricingCategories[$p]['defaultCategory'];
                $fullTitle = $pricingCategories[$p]['fullTitle'];
            }
        }
        $agendaItems = $activity['agendaItems'];
        if (count($agendaItems)  > 0) {
            for ($a=0; $a < count($agendaItems); $a++) { 
                $agendaitemsid = $agendaItems[$a]['id'];
                $index = $agendaItems[$a]['index'];
                $title = $agendaItems[$a]['title'];
                $excerpt = $agendaItems[$a]['excerpt'];
                $body = $agendaItems[$a]['body'];
                $day = $agendaItems[$a]['day'];
                $address = $agendaItems[$a]['address'];
                $keyPhoto = $agendaItems[$a]['keyPhoto'];
                $location = $agendaItems[$a]['location'];
                $locationaddress = $location['address'];
                $city = $location['city'];
                $countryCode = $location['countryCode'];
                $postCode = $location['postCode'];
                $latitude = $location['latitude'];
                $longitude = $location['longitude'];
                $zoomLevel = $location['zoomLevel'];
                $origin = $location['origin'];
                $originId = $location['originId'];
                $wholeAddress = $location['wholeAddress'];
            }
        }
        $startTimes = $activity['startTimes'];
        if (count($startTimes) > 0) {
            for ($s=0; $s < count($startTimes); $s++) { 
                $starttimesid = $startTimes[$s]['id'];
                $label = $startTimes[$s]['label'];
                $hour = $startTimes[$s]['hour'];
                $minute = $startTimes[$s]['minute'];
                $overrideTimeWhenPickup = $startTimes[$s]['overrideTimeWhenPickup'];
                $pickupHour = $startTimes[$s]['pickupHour'];
                $pickupMinute = $startTimes[$s]['pickupMinute'];
                $durationType = $startTimes[$s]['durationType'];
                $voucherPickupMsg = $startTimes[$s]['voucherPickupMsg'];
                $externalId = $startTimes[$s]['externalId'];
                $duration = $startTimes[$s]['duration'];
                $durationMinutes = $startTimes[$s]['durationMinutes'];
                $durationHours = $startTimes[$s]['durationHours'];
                $durationDays = $startTimes[$s]['durationDays'];
                $durationWeeks = $startTimes[$s]['durationWeeks'];
                $flags = $startTimes[$s]['flags'];
                if (count($flags) > 0) {
                    for ($sAux=0; $sAux < count($flags); $sAux++) { 
                        $flags = $flags[$sAux];
                    }
                }
            }
        }
        $bookableExtras = $activity['bookableExtras'];
        if (count($bookableExtras) > 0) {
            for ($b=0; $b < count($bookableExtras); $b++) { 
                $bookableextrasid = $bookableExtras[$b]['id'];
                $externalId = $bookableExtras[$b]['externalId'];
                $title = $bookableExtras[$b]['title'];
                $information = $bookableExtras[$b]['information'];
                $included = $bookableExtras[$b]['included'];
                $free = $bookableExtras[$b]['free'];
                $productGroupId = $bookableExtras[$b]['productGroupId'];
                $pricingType = $bookableExtras[$b]['pricingType'];
                $pricingTypeLabel = $bookableExtras[$b]['pricingTypeLabel'];
                $price = $bookableExtras[$b]['price'];
                $increasesCapacity = $bookableExtras[$b]['increasesCapacity'];
                $maxPerBooking = $bookableExtras[$b]['maxPerBooking'];
                $limitByPax = $bookableExtras[$b]['limitByPax'];
            }
        }
        $route = $activity['route'];
        $mapZoomLevel = $route['mapZoomLevel'];
        $center = $route['center'];
        $centerlat = $center['lat'];
        $centerlng = $center['lng'];
        $start = $route['start'];
        $startlat = $start['lat'];
        $startlng = $start['lng'];
        $end = $route['end'];
        $endlat = $end['lat'];
        $endlng = $end['lng'];
        $seasonalOpeningHours = $activity['seasonalOpeningHours'];
        $displaySettings = $activity['displaySettings'];
        $showPickupPlaces = $displaySettings['showPickupPlaces'];
        $showRouteMap = $displaySettings['showRouteMap'];
        $selectRateBasedOnStartTime = $displaySettings['selectRateBasedOnStartTime'];
        $customFields = $displaySettings['customFields'];
        if (count($customFields) > 0) {
            for ($j=0; $j < count($customFields); $j++) { 
                $type = $customFields[$j]['title'];
                $inputFieldId = $customFields[$j]['inputFieldId'];
                $value = $customFields[$j]['value'];
            }
        }
        $actualVendor = $activity['actualVendor'];
        $actualVendorid = $actualVendor['id'];
        $actualVendortitle = $actualVendor['title'];
        $actualVendorcurrencyCode = $actualVendor['currencyCode'];
        $actualVendortimeZone = $actualVendor['timeZone'];
        $showInvoiceIdOnTicket = $actualVendor['showInvoiceIdOnTicket'];
        $showAgentDetailsOnTicket = $actualVendor['showAgentDetailsOnTicket'];
        $showPaymentsOnInvoice = $actualVendor['showPaymentsOnInvoice'];
        $companyEmailIsDefault = $actualVendor['companyEmailIsDefault'];
        //quantityByPricingCategory
        $quantityByPricingCategory = $activityBookings[$i]['quantityByPricingCategory'];
        $quantity = $quantityByPricingCategory[$pricingCategoryId];
    }
}
$affiliate = $parentBooking['affiliate'];
$affiliateid = $affiliate['id'];
$affiliateexternalId = $affiliate['externalId'];
$affiliateidNumber = $affiliate['idNumber'];
$affiliatetitle = $affiliate['title'];
$affiliatetrackingCode = $affiliate['trackingCode'];
$flags = $affiliate['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$agent = $parentBooking['agent'];
$agentid = $agent['id'];
$agentidNumber = $agent['idNumber'];
$agentreferenceCode = $agent['referenceCode'];
$agenttitle = $agent['title'];
$linkedExternalCustomers = $agent['linkedExternalCustomers'];
if (count($linkedExternalCustomers) > 0) {
    for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
        $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
        $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
        $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
        $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
        $systemType = $linkedExternalCustomers[$j]['systemType'];
        $flags = $linkedExternalCustomers[$j]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
    }
}
$bookingChannel = $parentBooking['bookingChannel'];
$bookingChannelid = $bookingChannel['id'];
$bookingChanneluuid = $bookingChannel['uuid'];
$bookingChanneltitle = $bookingChannel['title'];
$bookingChannelbackend = $bookingChannel['backend'];
$bookingChanneloverrideVoucherHeader = $bookingChannel['overrideVoucherHeader'];
$bookingChannelvoucherName = $bookingChannel['voucherName'];
$bookingChannelvoucherPhoneNumber = $bookingChannel['voucherPhoneNumber'];
$bookingChannelvoucherEmailAddress = $bookingChannel['voucherEmailAddress'];
$bookingChannelvoucherLogoStyle = $bookingChannel['voucherLogoStyle'];
$bookingChannelvoucherWebsite = $bookingChannel['voucherWebsite'];
$bookingChannelpaymentProviderAdded = $bookingChannel['paymentProviderAdded'];
$bookingChannelshoppingCartPosition = $bookingChannel['shoppingCartPosition'];
$bookingFields = $parentBooking['bookingFields'];
if (count($bookingFields) > 0) {
    for ($rAux=0; $rAux < count($bookingFields); $rAux++) { 
        $name = $bookingFields[$rAux]['name'];
        $value = $bookingFields[$rAux]['value'];
    }
}
$carRentalBookings = $parentBooking['carRentalBookings'];
$customer = $parentBooking['customer'];
$customerid = $customer['id'];
$customeruuid = $customer['uuid'];
$customerfirstName = $customer['firstName'];
$customerlastName = $customer['lastName'];
$customeremail = $customer['email'];
$customeraddress = $customer['address'];
$customercontactDetailsHidden = $customer['contactDetailsHidden'];
$customercontactDetailsHiddenUntil = $customer['contactDetailsHiddenUntil'];
$customercountry = $customer['country'];
$customercreated = $customer['created'];
$customercredentials = $customer['credentials'];
$customerdateOfBirth = $customer['dateOfBirth'];
$customerlanguage = $customer['language'];
$customernationality = $customer['nationality'];
$customerorganization = $customer['organization'];
$customerpassportExpMonth = $customer['passportExpMonth'];
$customerpassportExpYear = $customer['passportExpYear'];
$customerpassportId = $customer['passportId'];
$customerphoneNumber = $customer['phoneNumber'];
$customerphoneNumberCountryCode = $customer['phoneNumberCountryCode'];
$customerplace = $customer['place'];
$customerpostCode = $customer['postCode'];
$customersex = $customer['sex'];
$customerstate = $customer['state'];
$customertitle = $customer['title'];
$customerpersonalIdNumber = $customer['personalIdNumber'];
$customerclcEmail = $customer['clcEmail'];
$customerpassportExpDay = $customer['passportExpDay'];
$customerPayments = $parentBooking['customerPayments'];
if (count($customerPayments) > 0) {
    for ($w=0; $w < count($customerPayments); $w++) { 
        $id = $customerPayments[$w]['id'];
        $amount = $customerPayments[$w]['amount'];
        $currency = $customerPayments[$w]['currency'];
        $comment = $customerPayments[$w]['comment'];
        $transactionDate = $customerPayments[$w]['transactionDate'];
        $authorizationCode = $customerPayments[$w]['authorizationCode'];
        $activeCustomerInvoiceId = $customerPayments[$w]['activeCustomerInvoiceId'];
        $paymentType = $customerPayments[$w]['paymentType'];
        $cardNumber = $customerPayments[$w]['cardNumber'];
        $paymentReferenceId = $customerPayments[$w]['paymentReferenceId'];
        $paymentProviderType = $customerPayments[$w]['paymentProviderType'];
        $isRefundable = $customerPayments[$w]['isRefundable'];
        $totalRefundedAmount = $customerPayments[$w]['totalRefundedAmount'];
        $useDcc = $customerPayments[$w]['useDcc'];
        $maxRefundableAmount = $customerPayments[$w]['maxRefundableAmount'];
        $refundable = $customerPayments[$w]['refundable'];
        $amountAsText = $customerPayments[$w]['amountAsText'];
        $refundedAmountAsText = $customerPayments[$w]['refundedAmountAsText'];
        $remainingRefundableAmount = $customerPayments[$w]['remainingRefundableAmount'];
        $amountAsMoney = $customerPayments[$w]['amountAsMoney'];
        $amountAsMoneyamount = $amountAsMoney['amount'];
        $amountAsMoneycurrency = $amountAsMoney['currency'];
    }
}
$extranetUser = $parentBooking['extranetUser'];
$extranetUserid = $extranetUser['id'];
$extranetUserfirstName = $extranetUser['firstName'];
$extranetUserlastName = $extranetUser['lastName'];
$extranetUserfullNameShort = $extranetUser['fullNameShort'];
$extranetUserusername = $extranetUser['username'];
$extranetUserpassword = $extranetUser['password'];
$extranetUserrole = $extranetUser['role'];
$extranetUsertermsOfServiceAgreedDate = $extranetUser['termsOfServiceAgreedDate'];
$extranetUserlastLoginDate = $extranetUser['lastLoginDate'];
$extranetUsercreationDate = $extranetUser['creationDate'];
$agent = $extranetUser['agent'];
$agentid = $agent['id'];
$agentidNumber = $agent['idNumber'];
$agentreferenceCode = $agent['referenceCode'];
$agenttitle = $agent['title'];
$linkedExternalCustomers = $agent['linkedExternalCustomers'];
if (count($linkedExternalCustomers) > 0) {
    for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
        $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
        $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
        $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
        $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
        $systemType = $linkedExternalCustomers[$j]['systemType'];
        $flags = $linkedExternalCustomers[$j]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
    }
}
$vendorRoles = $extranetUser['vendorRoles'];
if (count($vendorRoles) > 0) {
    $roles = "";
    for ($v=0; $v < count($vendorRoles); $v++) { 
        $roles = $vendorRoles[$v];
    }
}
$harbour = $parentBooking['harbour'];
$harbourid = $harbour['id'];
$harbourproductListId = $harbour['productListId'];
$harbourtitle = $harbour['title'];
$pickupPlace = $harbour['pickupPlace'];
$pickupPlaceid = $pickupPlace['id'];
$pickupPlaceexternalId = $pickupPlace['externalId'];
$pickupPlaceaskForRoomNumber = $pickupPlace['askForRoomNumber'];
$pickupPlacetitle = $pickupPlace['title'];
$pickupPlacetype = $pickupPlace['type'];
$location = $pickupPlace['location'];
$locationaddress = $location['address'];
$locationcity = $location['city'];
$locationcountryCode = $location['countryCode'];
$locationlatitude = $location['latitude'];
$locationlongitude = $location['longitude'];
$locationpostCode = $location['postCode'];
$locationzoomLevel = $location['zoomLevel'];
$locationorigin = $location['origin'];
$locationoriginId = $location['originId'];
$flags = $pickupPlace['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$flags = $harbour['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$invoice = $parentBooking['invoice'];
$invoiceid = $invoice['id'];
$invoicecurrency = $invoice['currency'];
$invoicedates = $invoice['dates'];
$invoiceexcludedTaxes = $invoice['excludedTaxes'];
$invoicefree = $invoice['free'];
$invoiceincludedTaxes = $invoice['includedTaxes'];
$invoiceissueDate = $invoice['issueDate'];
$invoiceproductBookingId = $invoice['productBookingId'];
$invoiceproductCategory = $invoice['productCategory'];
$invoiceproductConfirmationCode = $invoice['productConfirmationCode'];
$invoicetotalAsText = $invoice['totalAsText'];
$invoicetotalDiscountedAsText = $invoice['totalDiscountedAsText'];
$invoicetotalDueAsText = $invoice['totalDueAsText'];
$invoicetotalExcludedTaxAsText = $invoice['totalExcludedTaxAsText'];
$invoicetotalIncludedTaxAsText = $invoice['totalIncludedTaxAsText'];
$invoicetotalTaxAsText = $invoice['totalTaxAsText'];

$issuer = $invoice['issuer'];
$issuerid = $invoice['id'];
$issuerexternalId = $invoice['externalId'];
$issuertitle = $invoice['title'];
$flags = $invoice['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($lAux=0; $lAux < count($flags); $lAux++) { 
        $flag = $flags[$lAux];
    }
}

$customLineItems = $invoice['customLineItems'];
if (count($customLineItems) > 0) {
    for ($c=0; $c < count($customLineItems); $c++) { 
        $id = $customLineItems[$c]['id'];
        $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
        $currency = $customLineItems[$c]['currency'];
        $customDiscount = $customLineItems[$c]['customDiscount'];
        $discount = $customLineItems[$c]['discount'];
        $lineItemType = $customLineItems[$c]['lineItemType'];
        $quantity = $customLineItems[$c]['quantity'];
        $taxAmount = $customLineItems[$c]['taxAmount'];
        $taxAsText = $customLineItems[$c]['taxAsText'];
        $title = $customLineItems[$c]['title'];
        $total = $customLineItems[$c]['total'];
        $totalAsText = $customLineItems[$c]['totalAsText'];
        $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
        $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
        $totalDue = $customLineItems[$c]['totalDue'];
        $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
        $unitPrice = $customLineItems[$c]['unitPrice'];
        $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
        $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
        $tax = $customLineItems[$c]['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
    }
}
$excludedAppliedTaxes = $invoice['excludedAppliedTaxes'];
if (count($excludedAppliedTaxes) > 0) {
    for ($e=0; $e < count($excludedAppliedTaxes); $e++) { 
        $currency = $excludedAppliedTaxes[$e]['currency'];
        $tax = $excludedAppliedTaxes[$e]['tax'];
        $taxAsText = $excludedAppliedTaxes[$e]['taxAsText'];
        $title = $excludedAppliedTaxes[$e]['title'];
        $taxAsMoney = $excludedAppliedTaxes[$e]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                $country = $countryCodes[$eAux];
            }
        }
    }
}
$includedAppliedTaxes = $invoice['includedAppliedTaxes'];
if (count($includedAppliedTaxes) > 0) {
    for ($e=0; $e < count($includedAppliedTaxes); $e++) { 
        $currency = $includedAppliedTaxes[$e]['currency'];
        $tax = $includedAppliedTaxes[$e]['tax'];
        $taxAsText = $includedAppliedTaxes[$e]['taxAsText'];
        $title = $includedAppliedTaxes[$e]['title'];
        $taxAsMoney = $includedAppliedTaxes[$e]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($eAux=0; $eAux < count($countryCodes); $eAux++) { 
                $country = $countryCodes[$eAux];
            }
        }
    }
}
$lineItems = $invoice['lineItems'];
if (count($lineItems) > 0) {
    for ($c=0; $c < count($lineItems); $c++) { 
        $id = $lineItems[$c]['id'];
        $calculatedDiscount = $lineItems[$c]['calculatedDiscount'];
        $currency = $lineItems[$c]['currency'];
        $customDiscount = $lineItems[$c]['customDiscount'];
        $discount = $lineItems[$c]['discount'];
        $lineItemType = $lineItems[$c]['lineItemType'];
        $quantity = $lineItems[$c]['quantity'];
        $taxAmount = $lineItems[$c]['taxAmount'];
        $taxAsText = $lineItems[$c]['taxAsText'];
        $title = $lineItems[$c]['title'];
        $total = $lineItems[$c]['total'];
        $totalAsText = $lineItems[$c]['totalAsText'];
        $totalDiscounted = $lineItems[$c]['totalDiscounted'];
        $totalDiscountedAsText = $lineItems[$c]['totalDiscountedAsText'];
        $totalDue = $lineItems[$c]['totalDue'];
        $totalDueAsText = $lineItems[$c]['totalDueAsText'];
        $unitPrice = $lineItems[$c]['unitPrice'];
        $unitPriceAsText = $lineItems[$c]['unitPriceAsText'];
        $unitPriceDate = $lineItems[$c]['unitPriceDate'];
        $tax = $lineItems[$c]['tax'];
        $taxid = $tax['id'];
        $taxincluded = $tax['included'];
        $taxpercentage = $tax['percentage'];
        $taxtitle = $tax['title'];
        $taxAsMoney = $lineItems[$c]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalAsMoney = $lineItems[$c]['totalAsMoney'];
        $totalAsMoneyamount = $totalAsMoney['amount'];
        $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
        $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
        $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
        $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
        $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
        $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
        $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
        $totalAsMoneynegative = $totalAsMoney['negative'];
        $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
        $totalAsMoneypositive = $totalAsMoney['positive'];
        $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
        $totalAsMoneyscale = $totalAsMoney['scale'];
        $totalAsMoneyzero = $totalAsMoney['zero'];
        $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDiscountedAsMoney = $lineItems[$c]['totalDiscountedAsMoney'];
        $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
        $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
        $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
        $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
        $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
        $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
        $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
        $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
        $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
        $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
        $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
        $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
        $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
        $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
        $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $totalDueAsMoney = $lineItems[$c]['totalDueAsMoney'];
        $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
        $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
        $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
        $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
        $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
        $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
        $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
        $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
        $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
        $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
        $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
        $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
        $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
        $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
        $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
        $unitPriceAsMoney = $lineItems[$c]['unitPriceAsMoney'];
        $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
        $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
        $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
        $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
        $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
        $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
        $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
        $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
        $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
        $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
        $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
        $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
        $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
        $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
        $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
    }
}
$lodgingTaxes = $invoice['lodgingTaxes'];
if (count($lodgingTaxes)) {
    for ($l=0; $l < count($lodgingTaxes); $l++) { 
        $currency = $lodgingTaxes[$l]['currency'];
        $tax = $lodgingTaxes[$l]['tax'];
        $taxAsText = $lodgingTaxes[$l]['taxAsText'];
        $title = $lodgingTaxes[$l]['title'];
        $taxAsMoney = $lodgingTaxes[$l]['taxAsMoney'];
        $amount = $taxAsMoney['amount'];
        $amountMajor = $taxAsMoney['amountMajor'];
        $amountMajorInt = $taxAsMoney['amountMajorInt'];
        $amountMajorLong = $taxAsMoney['amountMajorLong'];
        $amountMinor = $taxAsMoney['amountMinor'];
        $amountMinorInt = $taxAsMoney['amountMinorInt'];
        $amountMinorLong = $taxAsMoney['amountMinorLong'];
        $minorPart = $taxAsMoney['minorPart'];
        $negative = $taxAsMoney['negative'];
        $negativeOrZero = $taxAsMoney['negativeOrZero'];
        $positive = $taxAsMoney['positive'];
        $positiveOrZero = $taxAsMoney['positiveOrZero'];
        $scale = $taxAsMoney['scale'];
        $zero = $taxAsMoney['zero'];
        $currencyUnit = $taxAsMoney['currencyUnit'];
        $currencyUnitcode = $currencyUnit['code'];
        $currencyCode = $currencyUnit['currencyCode'];
        $decimalPlaces = $currencyUnit['decimalPlaces'];
        $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
        $numeric3Code = $currencyUnit['numeric3Code'];
        $numericCode = $currencyUnit['numericCode'];
        $pseudoCurrency = $currencyUnit['pseudoCurrency'];
        $symbol = $currencyUnit['symbol'];
        $countryCodes = $currencyUnit['countryCodes'];
        if (count($countryCodes) > 0) {
            $country = "";
            for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                $country = $countryCodes[$cAux];
            }
        }
    }
}
$paidAmountAsMoney = $invoice['paidAmountAsMoney'];
$amount = $paidAmountAsMoney['amount'];
$amountMajor = $paidAmountAsMoney['amountMajor'];
$amountMajorInt = $paidAmountAsMoney['amountMajorInt'];
$amountMajorLong = $paidAmountAsMoney['amountMajorLong'];
$amountMinor = $paidAmountAsMoney['amountMinor'];
$amountMinorInt = $paidAmountAsMoney['amountMinorInt'];
$amountMinorLong = $paidAmountAsMoney['amountMinorLong'];
$minorPart = $paidAmountAsMoney['minorPart'];
$negative = $paidAmountAsMoney['negative'];
$negativeOrZero = $paidAmountAsMoney['negativeOrZero'];
$positive = $paidAmountAsMoney['positive'];
$positiveOrZero = $paidAmountAsMoney['positiveOrZero'];
$scale = $paidAmountAsMoney['scale'];
$zero = $paidAmountAsMoney['zero'];
$currencyUnit = $paidAmountAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$productInvoices = $invoice['productInvoices'];
if (count($productInvoices) > 0) {
    for ($p=0; $p < count($productInvoices); $p++) { 
        $id = $productInvoices['id'];
        $currency = $productInvoices['currency'];
        $dates = $productInvoices['dates'];
        $excludedTaxes = $productInvoices['excludedTaxes'];
        $free = $productInvoices['free'];
        $includedTaxes = $productInvoices['includedTaxes'];
        $issueDate = $productInvoices['issueDate'];
        $productBookingId = $productInvoices['productBookingId'];
        $productCategory = $productInvoices['productCategory'];
        $productConfirmationCode = $productInvoices['productConfirmationCode'];
        $totalAsText = $productInvoices['totalAsText'];
        $totalDiscountedAsText = $productInvoices['totalDiscountedAsText'];
        $totalDueAsText = $productInvoices['totalDueAsText'];
        $totalExcludedTaxAsText = $productInvoices['totalExcludedTaxAsText'];
        $totalIncludedTaxAsText = $productInvoices['totalIncludedTaxAsText'];
        $totalTaxAsText = $productInvoices['totalTaxAsText'];
        $customLineItems = $productInvoices['customLineItems'];
        if (count($customLineItems) > 0) {
            for ($c=0; $c < count($customLineItems); $c++) { 
                $id = $customLineItems[$c]['id'];
                $calculatedDiscount = $customLineItems[$c]['calculatedDiscount'];
                $currency = $customLineItems[$c]['currency'];
                $customDiscount = $customLineItems[$c]['customDiscount'];
                $discount = $customLineItems[$c]['discount'];
                $lineItemType = $customLineItems[$c]['lineItemType'];
                $quantity = $customLineItems[$c]['quantity'];
                $taxAmount = $customLineItems[$c]['taxAmount'];
                $taxAsText = $customLineItems[$c]['taxAsText'];
                $title = $customLineItems[$c]['title'];
                $total = $customLineItems[$c]['total'];
                $totalAsText = $customLineItems[$c]['totalAsText'];
                $totalDiscounted = $customLineItems[$c]['totalDiscounted'];
                $totalDiscountedAsText = $customLineItems[$c]['totalDiscountedAsText'];
                $totalDue = $customLineItems[$c]['totalDue'];
                $totalDueAsText = $customLineItems[$c]['totalDueAsText'];
                $unitPrice = $customLineItems[$c]['unitPrice'];
                $unitPriceAsText = $customLineItems[$c]['unitPriceAsText'];
                $unitPriceDate = $customLineItems[$c]['unitPriceDate'];
                $tax = $customLineItems[$c]['tax'];
                $taxid = $tax['id'];
                $taxincluded = $tax['included'];
                $taxpercentage = $tax['percentage'];
                $taxtitle = $tax['title'];
                $taxAsMoney = $customLineItems[$c]['taxAsMoney'];
                $amount = $taxAsMoney['amount'];
                $amountMajor = $taxAsMoney['amountMajor'];
                $amountMajorInt = $taxAsMoney['amountMajorInt'];
                $amountMajorLong = $taxAsMoney['amountMajorLong'];
                $amountMinor = $taxAsMoney['amountMinor'];
                $amountMinorInt = $taxAsMoney['amountMinorInt'];
                $amountMinorLong = $taxAsMoney['amountMinorLong'];
                $minorPart = $taxAsMoney['minorPart'];
                $negative = $taxAsMoney['negative'];
                $negativeOrZero = $taxAsMoney['negativeOrZero'];
                $positive = $taxAsMoney['positive'];
                $positiveOrZero = $taxAsMoney['positiveOrZero'];
                $scale = $taxAsMoney['scale'];
                $zero = $taxAsMoney['zero'];
                $currencyUnit = $taxAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalAsMoney = $customLineItems[$c]['totalAsMoney'];
                $totalAsMoneyamount = $totalAsMoney['amount'];
                $totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
                $totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
                $totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
                $totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
                $totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
                $totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
                $totalAsMoneyminorPart = $totalAsMoney['minorPart'];
                $totalAsMoneynegative = $totalAsMoney['negative'];
                $totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
                $totalAsMoneypositive = $totalAsMoney['positive'];
                $totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
                $totalAsMoneyscale = $totalAsMoney['scale'];
                $totalAsMoneyzero = $totalAsMoney['zero'];
                $totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDiscountedAsMoney = $customLineItems[$c]['totalDiscountedAsMoney'];
                $totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
                $totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
                $totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
                $totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
                $totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
                $totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
                $totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
                $totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
                $totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
                $totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
                $totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
                $totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
                $totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
                $totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
                $totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $totalDueAsMoney = $customLineItems[$c]['totalDueAsMoney'];
                $totalDueAsMoneyamount = $totalDueAsMoney['amount'];
                $totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
                $totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
                $totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
                $totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
                $totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
                $totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
                $totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
                $totalDueAsMoneynegative = $totalDueAsMoney['negative'];
                $totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
                $totalDueAsMoneypositive = $totalDueAsMoney['positive'];
                $totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
                $totalDueAsMoneyscale = $totalDueAsMoney['scale'];
                $totalDueAsMoneyzero = $totalDueAsMoney['zero'];
                $totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
                $unitPriceAsMoney = $customLineItems[$c]['unitPriceAsMoney'];
                $unitPriceAsMoneyamount = $unitPriceAsMoney['amount'];
                $unitPriceAsMoneyamountMajor = $unitPriceAsMoney['amountMajor'];
                $unitPriceAsMoneyamountMajorInt = $unitPriceAsMoney['amountMajorInt'];
                $unitPriceAsMoneyamountMajorLong = $unitPriceAsMoney['amountMajorLong'];
                $unitPriceAsMoneyamountMinor = $unitPriceAsMoney['amountMinor'];
                $unitPriceAsMoneyamountMinorInt = $unitPriceAsMoney['amountMinorInt'];
                $unitPriceAsMoneyamountMinorLong = $unitPriceAsMoney['amountMinorLong'];
                $unitPriceAsMoneyminorPart = $unitPriceAsMoney['minorPart'];
                $unitPriceAsMoneynegative = $unitPriceAsMoney['negative'];
                $unitPriceAsMoneynegativeOrZero = $unitPriceAsMoney['negativeOrZero'];
                $unitPriceAsMoneypositive = $unitPriceAsMoney['positive'];
                $unitPriceAsMoneypositiveOrZero = $unitPriceAsMoney['positiveOrZero'];
                $unitPriceAsMoneyscale = $unitPriceAsMoney['scale'];
                $unitPriceAsMoneyzero = $unitPriceAsMoney['zero'];
                $unitPriceAsMoneycurrencyUnit = $unitPriceAsMoney['currencyUnit'];
                $currencyUnitcode = $currencyUnit['code'];
                $currencyCode = $currencyUnit['currencyCode'];
                $decimalPlaces = $currencyUnit['decimalPlaces'];
                $defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
                $numeric3Code = $currencyUnit['numeric3Code'];
                $numericCode = $currencyUnit['numericCode'];
                $pseudoCurrency = $currencyUnit['pseudoCurrency'];
                $symbol = $currencyUnit['symbol'];
                $countryCodes = $currencyUnit['countryCodes'];
                if (count($countryCodes) > 0) {
                    $country = "";
                    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
                        $country = $countryCodes[$cAux];
                    }
                }
            }
        }
    }
}
$totalAsMoney = $invoice['totalAsMoney'];
$totalAsMoneyamount = $totalAsMoney['amount'];
$totalAsMoneyamountMajor = $totalAsMoney['amountMajor'];
$totalAsMoneyamountMajorInt = $totalAsMoney['amountMajorInt'];
$totalAsMoneyamountMajorLong = $totalAsMoney['amountMajorLong'];
$totalAsMoneyamountMinor = $totalAsMoney['amountMinor'];
$totalAsMoneyamountMinorInt = $totalAsMoney['amountMinorInt'];
$totalAsMoneyamountMinorLong = $totalAsMoney['amountMinorLong'];
$totalAsMoneyminorPart = $totalAsMoney['minorPart'];
$totalAsMoneynegative = $totalAsMoney['negative'];
$totalAsMoneynegativeOrZero = $totalAsMoney['negativeOrZero'];
$totalAsMoneypositive = $totalAsMoney['positive'];
$totalAsMoneypositiveOrZero = $totalAsMoney['positiveOrZero'];
$totalAsMoneyscale = $totalAsMoney['scale'];
$totalAsMoneyzero = $totalAsMoney['zero'];
$totalAsMoneycurrencyUnit = $totalAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$totalDiscountAsMoney = $invoice['totalDiscountAsMoney'];
$totalDiscountAsMoneyamount = $totalDiscountAsMoney['amount'];
$totalDiscountAsMoneyamountMajor = $totalDiscountAsMoney['amountMajor'];
$totalDiscountAsMoneyamountMajorInt = $totalDiscountAsMoney['amountMajorInt'];
$totalDiscountAsMoneyamountMajorLong = $totalDiscountAsMoney['amountMajorLong'];
$totalDiscountAsMoneyamountMinor = $totalDiscountAsMoney['amountMinor'];
$totalDiscountAsMoneyamountMinorInt = $totalDiscountAsMoney['amountMinorInt'];
$totalDiscountAsMoneyamountMinorLong = $totalDiscountAsMoney['amountMinorLong'];
$totalDiscountAsMoneyminorPart = $totalDiscountAsMoney['minorPart'];
$totalDiscountAsMoneynegative = $totalDiscountAsMoney['negative'];
$totalDiscountAsMoneynegativeOrZero = $totalDiscountAsMoney['negativeOrZero'];
$totalDiscountAsMoneypositive = $totalDiscountAsMoney['positive'];
$totalDiscountAsMoneypositiveOrZero = $totalDiscountAsMoney['positiveOrZero'];
$totalDiscountAsMoneyscale = $totalDiscountAsMoney['scale'];
$totalDiscountAsMoneyzero = $totalDiscountAsMoney['zero'];
$totalDiscountAsMoneycurrencyUnit = $totalDiscountAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$totalDiscountedAsMoney = $invoice['totalDiscountedAsMoney'];
$totalDiscountedAsMoneyamount = $totalDiscountedAsMoney['amount'];
$totalDiscountedAsMoneyamountMajor = $totalDiscountedAsMoney['amountMajor'];
$totalDiscountedAsMoneyamountMajorInt = $totalDiscountedAsMoney['amountMajorInt'];
$totalDiscountedAsMoneyamountMajorLong = $totalDiscountedAsMoney['amountMajorLong'];
$totalDiscountedAsMoneyamountMinor = $totalDiscountedAsMoney['amountMinor'];
$totalDiscountedAsMoneyamountMinorInt = $totalDiscountedAsMoney['amountMinorInt'];
$totalDiscountedAsMoneyamountMinorLong = $totalDiscountedAsMoney['amountMinorLong'];
$totalDiscountedAsMoneyminorPart = $totalDiscountedAsMoney['minorPart'];
$totalDiscountedAsMoneynegative = $totalDiscountedAsMoney['negative'];
$totalDiscountedAsMoneynegativeOrZero = $totalDiscountedAsMoney['negativeOrZero'];
$totalDiscountedAsMoneypositive = $totalDiscountedAsMoney['positive'];
$totalDiscountedAsMoneypositiveOrZero = $totalDiscountedAsMoney['positiveOrZero'];
$totalDiscountedAsMoneyscale = $totalDiscountedAsMoney['scale'];
$totalDiscountedAsMoneyzero = $totalDiscountedAsMoney['zero'];
$totalDiscountedAsMoneycurrencyUnit = $totalDiscountedAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$totalDueAsMoney = $invoice['totalDueAsMoney'];
$totalDueAsMoneyamount = $totalDueAsMoney['amount'];
$totalDueAsMoneyamountMajor = $totalDueAsMoney['amountMajor'];
$totalDueAsMoneyamountMajorInt = $totalDueAsMoney['amountMajorInt'];
$totalDueAsMoneyamountMajorLong = $totalDueAsMoney['amountMajorLong'];
$totalDueAsMoneyamountMinor = $totalDueAsMoney['amountMinor'];
$totalDueAsMoneyamountMinorInt = $totalDueAsMoney['amountMinorInt'];
$totalDueAsMoneyamountMinorLong = $totalDueAsMoney['amountMinorLong'];
$totalDueAsMoneyminorPart = $totalDueAsMoney['minorPart'];
$totalDueAsMoneynegative = $totalDueAsMoney['negative'];
$totalDueAsMoneynegativeOrZero = $totalDueAsMoney['negativeOrZero'];
$totalDueAsMoneypositive = $totalDueAsMoney['positive'];
$totalDueAsMoneypositiveOrZero = $totalDueAsMoney['positiveOrZero'];
$totalDueAsMoneyscale = $totalDueAsMoney['scale'];
$totalDueAsMoneyzero = $totalDueAsMoney['zero'];
$totalDueAsMoneycurrencyUnit = $totalDueAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$totalExcludedTaxAsMoney = $invoice['totalExcludedTaxAsMoney'];
$totalExcludedTaxAsMoneyamount = $totalExcludedTaxAsMoney['amount'];
$totalExcludedTaxAsMoneyamountMajor = $totalExcludedTaxAsMoney['amountMajor'];
$totalExcludedTaxAsMoneyamountMajorInt = $totalExcludedTaxAsMoney['amountMajorInt'];
$totalExcludedTaxAsMoneyamountMajorLong = $totalExcludedTaxAsMoney['amountMajorLong'];
$totalExcludedTaxAsMoneyamountMinor = $totalExcludedTaxAsMoney['amountMinor'];
$totalExcludedTaxAsMoneyamountMinorInt = $totalExcludedTaxAsMoney['amountMinorInt'];
$totalExcludedTaxAsMoneyamountMinorLong = $totalExcludedTaxAsMoney['amountMinorLong'];
$totalExcludedTaxAsMoneyminorPart = $totalExcludedTaxAsMoney['minorPart'];
$totalExcludedTaxAsMoneynegative = $totalExcludedTaxAsMoney['negative'];
$totalExcludedTaxAsMoneynegativeOrZero = $totalExcludedTaxAsMoney['negativeOrZero'];
$totalExcludedTaxAsMoneypositive = $totalExcludedTaxAsMoney['positive'];
$totalExcludedTaxAsMoneypositiveOrZero = $totalExcludedTaxAsMoney['positiveOrZero'];
$totalExcludedTaxAsMoneyscale = $totalExcludedTaxAsMoney['scale'];
$totalExcludedTaxAsMoneyzero = $totalExcludedTaxAsMoney['zero'];
$totalExcludedTaxAsMoneycurrencyUnit = $totalExcludedTaxAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$totalIncludedTaxAsMoney = $invoice['totalIncludedTaxAsMoney'];
$totalIncludedTaxAsMoneyamount = $totalIncludedTaxAsMoney['amount'];
$totalIncludedTaxAsMoneyamountMajor = $totalIncludedTaxAsMoney['amountMajor'];
$totalIncludedTaxAsMoneyamountMajorInt = $totalIncludedTaxAsMoney['amountMajorInt'];
$totalIncludedTaxAsMoneyamountMajorLong = $totalIncludedTaxAsMoney['amountMajorLong'];
$totalIncludedTaxAsMoneyamountMinor = $totalIncludedTaxAsMoney['amountMinor'];
$totalIncludedTaxAsMoneyamountMinorInt = $totalIncludedTaxAsMoney['amountMinorInt'];
$totalIncludedTaxAsMoneyamountMinorLong = $totalIncludedTaxAsMoney['amountMinorLong'];
$totalIncludedTaxAsMoneyminorPart = $totalIncludedTaxAsMoney['minorPart'];
$totalIncludedTaxAsMoneynegative = $totalIncludedTaxAsMoney['negative'];
$totalIncludedTaxAsMoneynegativeOrZero = $totalIncludedTaxAsMoney['negativeOrZero'];
$totalIncludedTaxAsMoneypositive = $totalIncludedTaxAsMoney['positive'];
$totalIncludedTaxAsMoneypositiveOrZero = $totalIncludedTaxAsMoney['positiveOrZero'];
$totalIncludedTaxAsMoneyscale = $totalIncludedTaxAsMoney['scale'];
$totalIncludedTaxAsMoneyzero = $totalIncludedTaxAsMoney['zero'];
$totalIncludedTaxAsMoneycurrencyUnit = $totalIncludedTaxAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$totalTaxAsMoney = $invoice['totalTaxAsMoney'];
$totalTaxAsMoneyamount = $totalTaxAsMoney['amount'];
$totalTaxAsMoneyamountMajor = $totalTaxAsMoney['amountMajor'];
$totalTaxAsMoneyamountMajorInt = $totalTaxAsMoney['amountMajorInt'];
$totalTaxAsMoneyamountMajorLong = $totalTaxAsMoney['amountMajorLong'];
$totalTaxAsMoneyamountMinor = $totalTaxAsMoney['amountMinor'];
$totalTaxAsMoneyamountMinorInt = $totalTaxAsMoney['amountMinorInt'];
$totalTaxAsMoneyamountMinorLong = $totalTaxAsMoney['amountMinorLong'];
$totalTaxAsMoneyminorPart = $totalTaxAsMoney['minorPart'];
$totalTaxAsMoneynegative = $totalTaxAsMoney['negative'];
$totalTaxAsMoneynegativeOrZero = $totalTaxAsMoney['negativeOrZero'];
$totalTaxAsMoneypositive = $totalTaxAsMoney['positive'];
$totalTaxAsMoneypositiveOrZero = $totalTaxAsMoney['positiveOrZero'];
$totalTaxAsMoneyscale = $totalTaxAsMoney['scale'];
$totalTaxAsMoneyzero = $totalTaxAsMoney['zero'];
$totalTaxAsMoneycurrencyUnit = $totalTaxAsMoney['currencyUnit'];
$currencyUnitcode = $currencyUnit['code'];
$currencyCode = $currencyUnit['currencyCode'];
$decimalPlaces = $currencyUnit['decimalPlaces'];
$defaultFractionDigits = $currencyUnit['defaultFractionDigits'];
$numeric3Code = $currencyUnit['numeric3Code'];
$numericCode = $currencyUnit['numericCode'];
$pseudoCurrency = $currencyUnit['pseudoCurrency'];
$symbol = $currencyUnit['symbol'];
$countryCodes = $currencyUnit['countryCodes'];
if (count($countryCodes) > 0) {
    $country = "";
    for ($cAux=0; $cAux < count($countryCodes); $cAux++) { 
        $country = $countryCodes[$cAux];
    }
}
$routeBookings = $parentBooking['routeBookings'];
$seller = $parentBooking['seller'];
$sellerid = $seller['id'];
$sellercurrencyCode = $seller['currencyCode'];
$selleremailAddress = $seller['emailAddress'];
$sellerinvoiceIdNumber = $seller['invoiceIdNumber'];
$sellerlogoStyle = $seller['logoStyle'];
$sellerphoneNumber = $seller['phoneNumber'];
$sellershowAgentDetailsOnTicket = $seller['showAgentDetailsOnTicket'];
$sellershowInvoiceIdOnTicket = $seller['showInvoiceIdOnTicket'];
$sellershowPaymentsOnInvoice = $seller['showPaymentsOnInvoice'];
$sellertitle = $seller['title'];
$sellerwebsite = $seller['website'];
$logo = $seller['logo'];
$logoid = $logo['id'];
$logoalternateText = $logo['alternateText'];
$logodescription = $logo['description'];
$logooriginalUrl = $logo['originalUrl'];
$derived = $logo['derived'];
if (count($derived) > 0) {
    for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
        $cleanUrl = $derived[$iAux4]['cleanUrl'];
        $name = $derived[$iAux4]['name'];
        $url = $derived[$iAux4]['url'];
    }
}
$flags = $logo['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$linkedExternalCustomers = $seller['linkedExternalCustomers'];
if (count($linkedExternalCustomers) > 0) {
    for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
        $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
        $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
        $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
        $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
        $systemType = $linkedExternalCustomers[$j]['systemType'];
        $flags = $linkedExternalCustomers[$j]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
    }
}
$systemConfig = $parentBooking['systemConfig'];
$systemConfigid = $systemConfig['id'];
$systemConfigsystemType = $systemConfig['systemType'];
$systemConfigtitle = $systemConfig['title'];
$vessel = $parentBooking['vessel'];
$vesselid = $vessel['id'];
$vesseltitle = $vessel['title'];
$affiliate = $vessel['affiliate'];
$affiliateid = $affiliate['id'];
$affiliateexternalId = $affiliate['externalId'];
$affiliateidNumber = $affiliate['idNumber'];
$affiliatetitle = $affiliate['title'];
$affiliatetrackingCode = $affiliate['trackingCode'];
$flags = $affiliate['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$flags = $vessel['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$stopovers = $vessel['stopovers'];
if (count($stopovers) > 0) {
    for ($z=0; $z < count($stopovers); $z++) { 
        $id = $stopovers[$z]['id'];
        $harbourId = $stopovers[$z]['harbourId'];
        $departureDate = $stopovers[$z]['departureDate'];
        $arrivalDate = $stopovers[$z]['arrivalDate'];
    }
}
//pickupPlace
$pickupPlace = $response['pickupPlace'];
$pickupPlaceid = $pickupPlace['id'];
$pickupPlaceexternalId = $pickupPlace['externalId'];
$pickupPlaceaskForRoomNumber = $pickupPlace['askForRoomNumber'];
$pickupPlacetitle = $pickupPlace['title'];
$pickupPlacetype = $pickupPlace['type'];
$location = $pickupPlace['location'];
$locationaddress = $location['address'];
$city = $location['city'];
$countryCode = $location['countryCode'];
$postCode = $location['postCode'];
$latitude = $location['latitude'];
$longitude = $location['longitude'];
$zoomLevel = $location['zoomLevel'];
$origin = $location['origin'];
$originId = $location['originId'];
$wholeAddress = $location['wholeAddress'];
$flags = $pickupPlace['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
        $flag = $flags[$iAux9];
    }
}
//pricingCategoryBookings
$pricingCategoryBookings = $response['pricingCategoryBookings'];
if (count($pricingCategoryBookings) > 0) {
    for ($iAux3=0; $iAux3 < count($pricingCategoryBookings); $iAux3++) { 
        $id = $pricingCategoryBookings[$iAux3]['id'];
        $pricingCategoryId = $pricingCategoryBookings[$iAux3]['pricingCategoryId'];
        $leadPassenger = $pricingCategoryBookings[$iAux3]['leadPassenger'];
        $age = $pricingCategoryBookings[$iAux3]['age'];
        $bookedTitle = $pricingCategoryBookings[$iAux3]['bookedTitle'];
        $quantity = $pricingCategoryBookings[$iAux3]['quantity'];
        $pricingCategory = $pricingCategoryBookings[$iAux3]['pricingCategory'];
        $pricingCategoryid = $pricingCategory['id'];
        $pricingCategorytitle = $pricingCategory['title'];
        $pricingCategoryticketCategory = $pricingCategory['ticketCategory'];
        $pricingCategoryoccupancy = $pricingCategory['occupancy'];
        $pricingCategorygroupSize = $pricingCategory['groupSize'];
        $pricingCategoryageQualified = $pricingCategory['ageQualified'];
        $pricingCategoryminAge = $pricingCategory['minAge'];
        $pricingCategorymaxAge = $pricingCategory['maxAge'];
        $pricingCategorydependent = $pricingCategory['dependent'];
        $pricingCategorymasterCategoryId = $pricingCategory['masterCategoryId'];
        $pricingCategorymaxPerMaster = $pricingCategory['maxPerMaster'];
        $pricingCategorysumDependentCategories = $pricingCategory['sumDependentCategories'];
        $pricingCategorymaxDependentSum = $pricingCategory['maxDependentSum'];
        $pricingCategoryinternalUseOnly = $pricingCategory['internalUseOnly'];
        $pricingCategorydefaultCategory = $pricingCategory['defaultCategory'];
        $pricingCategoryfullTitle = $pricingCategory['fullTitle'];
        $flags = $pricingCategory['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }
        $extras = $pricingCategoryBookings[$iAux3]['extras'];
        if (count($extras) > 0) {
            for ($iAux14=0; $iAux14 < count($extras); $iAux14++) { 
                $id = $extras[$iAux14]['id'];
                $externalId = $extras[$iAux14]['externalId'];
                $free = $extras[$iAux14]['free'];
                $included = $extras[$iAux14]['included'];
                $increasesCapacity = $extras[$iAux14]['increasesCapacity'];
                $information = $extras[$iAux14]['information'];
                $maxPerBooking = $extras[$iAux14]['maxPerBooking'];
                $price = $extras[$iAux14]['price'];
                $pricingType = $extras[$iAux14]['pricingType'];
                $pricingTypeLabel = $extras[$iAux14]['pricingTypeLabel'];
                $title = $extras[$iAux14]['title'];
                $flags = $extras[$iAux14]['flags'];
                $questions = $extras[$iAux14]['questions'];
                if (count($questions) > 0) {
                    for ($iAux15=0; $iAux15 < count($questions); $iAux15++) { 
                        $id = $questions[$iAux15]['id'];
                        $active = $questions[$iAux15]['active'];
                        $answerRequired = $questions[$iAux15]['answerRequired'];
                        $label = $questions[$iAux15]['label'];
                        $options = $questions[$iAux15]['options'];
                        $type = $questions[$iAux15]['type'];
                        $flags = $questions[$iAux15]['flags'];
                        if (count($flags) > 0) {
                            $flag = "";
                            for ($iAux9=0; $iAux9 < count($flags); $iAux9++) { 
                                $flag = $flags[$iAux9];
                            }
                        }
                    }
                }
            }
        }
        $answers = $pricingCategoryBookings[$iAux3]['answers'];
        if (count($answers) > 0) {
            for ($k=0; $k < count($answers) ; $k++) { 
                $id = $answers[$k]['id'];
                $answer = $answers[$k]['answer'];
                $group = $answers[$k]['group'];
                $question = $answers[$k]['question'];
                $type = $answers[$k]['type'];
            }
        }
        $flags = $pricingCategoryBookings[$iAux3]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }
    }
}
//product
$product = $response['product'];
$productid = $product['id'];
$productexternalId = $product['externalId'];
$productprice = $product['price'];
$productslug = $product['slug'];
$producttitle = $product['title'];
$keyPhoto = $product['keyPhoto'];
$keyPhotoid = $keyPhoto['id'];
$keyPhotooriginalUrl = $keyPhoto['originalUrl'];
$keyPhotodescription = $keyPhoto['description'];
$keyPhotoalternateText = $keyPhoto['alternateText'];
$keyPhotoheight = $keyPhoto['height'];
$keyPhotowidth = $keyPhoto['width'];
$keyPhotofileName = $keyPhoto['fileName'];
$derived = $keyPhoto['derived'];
if (count($derived) > 0) {
    for ($d=0; $d < count($derived); $d++) { 
        $name = $derived[$d]['name'];
        $url = $derived[$d]['url'];
        $cleanUrl = $derived[$d]['cleanUrl'];
    }
}
$vendor = $product['vendor'];
$vendorid = $vendor['id'];
$vendorcurrencyCode = $vendor['currencyCode'];
$vendoremailAddress = $vendor['emailAddress'];
$vendorinvoiceIdNumber = $vendor['invoiceIdNumber'];
$vendorlogoStyle = $vendor['logoStyle'];
$vendorphoneNumber = $vendor['phoneNumber'];
$vendorshowAgentDetailsOnTicket = $vendor['showAgentDetailsOnTicket'];
$vendorshowInvoiceIdOnTicket = $vendor['showInvoiceIdOnTicket'];
$vendorshowPaymentsOnInvoice = $vendor['showPaymentsOnInvoice'];
$vendortitle = $vendor['title'];
$vendorwebsite = $vendor['website'];
$logo = $vendor['logo'];
$logoid = $logo['id'];
$logoalternateText = $logo['alternateText'];
$logodescription = $logo['description'];
$logooriginalUrl = $logo['originalUrl'];
$derived = $logo['derived'];
if (count($derived) > 0) {
    for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
        $cleanUrl = $derived[$iAux4]['cleanUrl'];
        $name = $derived[$iAux4]['name'];
        $url = $derived[$iAux4]['url'];
    }
}
$flags = $product['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
//linksToExternalProducts
$linksToExternalProducts = $response['linksToExternalProducts'];
if (count($linksToExternalProducts) > 0) {
    for ($l=0; $l < count($linksToExternalProducts); $l++) { 
        $externalProductId = $linksToExternalProducts[$l]['externalProductId'];
        $externalProductTitle = $linksToExternalProducts[$l]['externalProductTitle'];
        $systemConfigId = $linksToExternalProducts[$l]['systemConfigId'];
        $flags = $linksToExternalProducts[$l]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($lAux=0; $lAux < count($flags); $lAux++) { 
                $flag = $flags[$lAux];
            }
        }
    }
}
//supplier
$supplier = $response['supplier'];
$supplierid = $supplier['id'];
$suppliercurrencyCode = $supplier['currencyCode'];
$supplieremailAddress = $supplier['emailAddress'];
$supplierinvoiceIdNumber = $supplier['invoiceIdNumber'];
$supplierlogoStyle = $supplier['logoStyle'];
$supplierphoneNumber = $supplier['phoneNumber'];
$suppliershowAgentDetailsOnTicket = $supplier['showAgentDetailsOnTicket'];
$suppliershowInvoiceIdOnTicket = $supplier['showInvoiceIdOnTicket'];
$suppliershowPaymentsOnInvoice = $supplier['showPaymentsOnInvoice'];
$suppliertitle = $supplier['title'];
$supplierwebsite = $supplier['website'];
$logo = $supplier['logo'];
$logoid = $logo['id'];
$logoalternateText = $logo['alternateText'];
$logodescription = $logo['description'];
$logooriginalUrl = $logo['originalUrl'];
$derived = $logo['derived'];
if (count($derived) > 0) {
    for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
        $cleanUrl = $derived[$iAux4]['cleanUrl'];
        $name = $derived[$iAux4]['name'];
        $url = $derived[$iAux4]['url'];
    }
}
$flags = $logo['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$linkedExternalCustomers = $supplier['linkedExternalCustomers'];
if (count($linkedExternalCustomers) > 0) {
    for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
        $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
        $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
        $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
        $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
        $systemType = $linkedExternalCustomers[$j]['systemType'];
        $flags = $linkedExternalCustomers[$j]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
            }
        }
    }
}
//seller
$seller = $response['seller'];
$sellerid = $seller['id'];
$sellercurrencyCode = $seller['currencyCode'];
$selleremailAddress = $seller['emailAddress'];
$sellerinvoiceIdNumber = $seller['invoiceIdNumber'];
$sellerlogoStyle = $seller['logoStyle'];
$sellerphoneNumber = $seller['phoneNumber'];
$sellershowAgentDetailsOnTicket = $seller['showAgentDetailsOnTicket'];
$sellershowInvoiceIdOnTicket = $seller['showInvoiceIdOnTicket'];
$sellershowPaymentsOnInvoice = $seller['showPaymentsOnInvoice'];
$sellertitle = $seller['title'];
$sellerwebsite = $seller['website'];
$logo = $seller['logo'];
$logoid = $logo['id'];
$logoalternateText = $logo['alternateText'];
$logodescription = $logo['description'];
$logooriginalUrl = $logo['originalUrl'];
$derived = $logo['derived'];
if (count($derived) > 0) {
    for ($iAux4=0; $iAux4 < count($derived); $iAux4++) { 
        $cleanUrl = $derived[$iAux4]['cleanUrl'];
        $name = $derived[$iAux4]['name'];
        $url = $derived[$iAux4]['url'];
    }
}
$flags = $product['flags'];
if (count($flags) > 0) {
    $flag = "";
    for ($jAux=0; $jAux < count($flags); $jAux++) { 
        $flag = $flags[$jAux];
    }
}
$linkedExternalCustomers = $seller['linkedExternalCustomers'];
if (count($linkedExternalCustomers) > 0) {
    for ($j=0; $j < count($linkedExternalCustomers); $j++) { 
        $externalCustomerId = $linkedExternalCustomers[$j]['externalCustomerId'];
        $externalCustomerTitle = $linkedExternalCustomers[$j]['externalCustomerTitle'];
        $externalDepartmentId = $linkedExternalCustomers[$j]['externalDepartmentId'];
        $systemConfigId = $linkedExternalCustomers[$j]['systemConfigId'];
        $systemType = $linkedExternalCustomers[$j]['systemType'];
        $flags = $linkedExternalCustomers[$j]['flags'];
        if (count($flags) > 0) {
            $flag = "";
            for ($jAux=0; $jAux < count($flags); $jAux++) { 
                $flag = $flags[$jAux];
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