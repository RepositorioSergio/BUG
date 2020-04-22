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
echo "COMECOU RESERVE<br/>";
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
$date = new DateTime();
$date = $date->format("Y-m-d H:i:s");
$accessKey = "709cc0c1189a46cca41796193c4f19af";
$secretKey = "7a846c68ec6b4a7ba964d3856307a54f";
$method = "POST";
$path = "/booking.json/guest/{sessionId}/reserve";

$word = $date . "" . $accessKey . "" . $method . "" . $path;

$signature = hash_hmac("sha1", $word, $secretKey,true);
$signature = base64_encode($signature);


$url = "https://api.bokun.io";

$raw = '{
    "answers": {
        "answers": [{
            "type": "first-name",
            "answer": "TEST"
        }, {
            "type": "last-name",
            "answer": "TEST"
        }, {
            "type": "email",
            "answer": "TEST.TEST@email.com"
        }, {
            "type": "phone-number",
            "answer": "+354 1234567"
        }, {
            "type": "nationality"
            "answer": "UK"
        }, {
            "type": "address",
            "answer": "123 Some St."
        }, {
            "type": "post-code",
            "answer": "101"
        }, {
            "type": "place",
            "answer": "Reykjavik"
        }, {
            "type": "country",
            "answer": "IS"
        }, {
            "type": "organization",
            "answer": "My company"
        }, {
            "type": "email-list-subscription",
            "question": "Yes, I want to subscribe to the email list",
            "answer": "true"
        }],
        "accommodationsBookings": [],
        "carRentalBookings": [],
        "activityBookings": [{
            "bookingId": 443,
            "answerGroups": [{
                "name": "participant-info",
                "answers": [{
                    "type": "name",
                    "question": "Participant name",
                    "answer": "TEST"
                }]
            }, {
                "name": "other",
                "answers": [{
                    "type": "special-requests",
                    "question": "Special requests",
                    "answer": "None."
                }]
            }],
            "extraBookings": [{
                "bookingId": 194,
                "answerGroups": [{
                    "name": "extra-info",
                    "answers": [{
                        "type": "extra-question",
                        "question": "Which size do you use?",
                        "answer": "Large",
                        "questionId": 27
                    }]
                }]
            }],
            "pricingCategoryBookings": [
                {
                  "bookingId": 200
                }
            ],
            "pickupPlaceDescription": "Hotel Reykjavik",
            "pickupPlaceRoomNumber": "110"
        }]
    }
}';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'X-Bokun-Date: ' . $date,
    'X-Bokun-AccessKey: ' . $accessKey,
    'X-Bokun-Signature: ' . $signature,
    'Accept: application/json',
    'Content-Type: application/json;charset=UTF-8',
    'Content-Length: ' . strlen($raw)
));
$client->setUri($url . '/booking.json/guest/{sessionId}/reserve');
$client->setMethod('POST');
$client->setRawBody($raw);
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

$accommodationBookings = $response['accommodationBookings'];
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
        $flags = $product['flags'];
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>