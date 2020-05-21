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
echo "COMECOU VIEW<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$access_token = "T1RLAQJZi0nR/Q9+880Jq2UK1v76ggsAJRCmheZlZJqS6TNvR47IjuUQAADAigAfu4oZE3tYVejeO+/R7aqJUVjlRus3tvBKeFxOiHu/YvNNMlm/10mWVUhLrFowve8+CnRmXV7zcSokvmmlyqd//2OLVlD84CUnn5Sqit/TGgKDOaY0mnv/aM86UPnQ0O5BaQwuiZG6qh6PDBgXi7zcGfN8xEfeXlOex3a2a8o/l+4TgB2RSmQW0/gCRU8+eMHT1KfObFk94Bngt6/b3PqoCU9L2u5AS/N0kXsbp2yRhyvNRqss8AgMfxwoZqSG";

$url = 'https://api-crt.cert.havail.sabre.com/v1/cruise/orders/create';

$raw = '{
    "agencyPOS": {
        "pcc": "IA8H",
        "branchPcc": "IA8H",
        "branchPhoneNum": "999999999",
        "currencyCode": "USD"
    },
    "retrieveResByResID": {
      "vendorCode": "RC",
      "reservationId": "H8737TWI",
      "agencyGroupId": "43562",
      "lockReservation": true
    },
    "retrieveResByGuestName": {
      "firstName": "Jhon",
      "lastName": "Doe",
      "vendorCode": "RC",
      "departureDate": "2020-06-01",
      "shipCode": "ID",
      "agencyGroupInfos": [
        {
          "groupId": "43562",
          "groupName": "Test Group"
        }
      ]
    }
  }';

echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Accept: application/json",
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    'Authorization: Bearer ' . $access_token,
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_USERPWD, $ipcc . ":" . $password);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$response = json_decode($response, true);

$reservationInfo = $response['reservationInfo'];
$reservationIdInfo = $reservationInfo['reservationIdInfo'];
$reservationId = $reservationIdInfo['reservationId'];
$agencyGroupId = $reservationIdInfo['agencyGroupId'];
$resCreationDate = $reservationIdInfo['resCreationDate'];
$resExtensionDate = $reservationIdInfo['resExtensionDate'];
$insuranceRevisionInd = $reservationIdInfo['insuranceRevisionInd'];
$reactivateCancelledRes = $reservationIdInfo['reactivateCancelledRes'];

$sailOption = $reservationInfo['sailOption'];
$voyageId = $sailOption['voyageId'];
$shipCode = $sailOption['shipCode'];
$regionCode = $sailOption['regionCode'];
$departureDate = $sailOption['departureDate'];
$duration = $sailOption['duration'];
$portsOfCallQty = $sailOption['portsOfCallQty'];
$sailingDesc = $sailOption['sailingDesc'];
$portCharges = $sailOption['portCharges'];
$surCharges = $sailOption['surCharges'];
$fareCityCode = $sailOption['fareCityCode'];
$modificationAllowedInd = $sailOption['modificationAllowedInd'];
$themeCode = $sailOption['themeCode'];
$transportationMode = $sailOption['transportationMode'];
$transportationCityCode = $sailOption['transportationCityCode'];
$currencyCode = $sailOption['currencyCode'];
$status = $sailOption['status'];
$insuranceInfo = $sailOption['insuranceInfo'];
$type = $insuranceInfo['type'];
$amount = $insuranceInfo['amount'];
$portsInfo = $sailOption['portsInfo'];
if (count($portsInfo) > 0) {
    $ports = "";
    for ($i=0; $i < count($portsInfo); $i++) { 
        $ports = $portsInfo[$i];
    }
}

$selectedFareCodes = $reservationInfo['selectedFareCodes'];
if (count($selectedFareCodes) > 0) {
    for ($j=0; $j < count($selectedFareCodes); $j++) { 
        $fareCode = $selectedFareCodes[$j]['fareCode'];
        $fareCodeName = $selectedFareCodes[$j]['fareCodeName'];
        $agencyGroupId = $selectedFareCodes[$j]['agencyGroupId'];
        $minOccupancy = $selectedFareCodes[$j]['minOccupancy'];
        $maxOccupancy = $selectedFareCodes[$j]['maxOccupancy'];
        $insuranceAvailableInd = $selectedFareCodes[$j]['insuranceAvailableInd'];
        $ageRestrictionInfos = $selectedFareCodes[$j]['ageRestrictionInfos'];
        if (count($ageRestrictionInfos) > 0) {
            $ageRestriction = "";
            for ($jAux=0; $jAux < count($ageRestrictionInfos); $jAux++) { 
                $ageRestriction = $ageRestrictionInfos[$jAux];
            }
        }
    }
}

$selectedCategories = $reservationInfo['selectedCategories'];
if (count($selectedCategories) > 0) {
    for ($k=0; $k < count($selectedCategories); $k++) { 
        $pricedCategoryCode = $selectedCategories[$k]['pricedCategoryCode'];
        $berthedCategoryCode = $selectedCategories[$k]['berthedCategoryCode'];
        $categoryName = $selectedCategories[$k]['categoryName'];
        $fareCode = $selectedCategories[$k]['fareCode'];
        $categoryLocation = $selectedCategories[$k]['categoryLocation'];
        $deckName = $selectedCategories[$k]['deckName'];
        $agencyGroupId = $selectedCategories[$k]['agencyGroupId'];
        $groupSeqNo = $selectedCategories[$k]['groupSeqNo'];
        $categoryGroupId = $selectedCategories[$k]['categoryGroupId'];
        $nonRefundableType = $selectedCategories[$k]['nonRefundableType'];
        $reservationNonRefundableType = $selectedCategories[$k]['reservationNonRefundableType'];
        $minOccupancy = $selectedCategories[$k]['minOccupancy'];
        $maxOccupancy = $selectedCategories[$k]['maxOccupancy'];
        $gtyCategory = $selectedCategories[$k]['gtyCategory'];
        $shareType = $selectedCategories[$k]['shareType'];
        $status = $selectedCategories[$k]['status'];
        $selectedCabins = $selectedCategories[$k]['selectedCabins'];
        if (count($selectedCabins) > 0) {
            for ($kAux=0; $kAux < count($selectedCabins); $kAux++) { 
                $cabinNum = $selectedCabins[$kAux]['cabinNum'];
                $minOccupancy = $selectedCabins[$kAux]['minOccupancy'];
                $maxOccupancy = $selectedCabins[$kAux]['maxOccupancy'];
                $deckName = $selectedCabins[$kAux]['deckName'];
                $connectedCabinNum = $selectedCabins[$kAux]['connectedCabinNum'];
                $smokingAllowed = $selectedCabins[$kAux]['smokingAllowed'];
                $accessibleCabinInd = $selectedCabins[$kAux]['accessibleCabinInd'];
                $bathCode = $selectedCabins[$kAux]['bathCode'];
                $status = $selectedCabins[$kAux]['status'];
                $cabinConfiguration = $selectedCabins[$kAux]['cabinConfiguration'];
                $cabinLocations = $cabinConfiguration['cabinLocations'];
                if (count($cabinLocations) > 0) {
                    $locations = "";
                    for ($kAux2=0; $kAux2 < count($cabinLocations); $kAux2++) { 
                        $locations = $cabinLocations[$kAux2];
                    }
                }
                $cabinAmenities = $cabinConfiguration['cabinAmenities'];
                if (count($cabinAmenities) > 0) {
                    $amenity = "";
                    for ($kAux3=0; $kAux3 < count($cabinAmenities); $kAux3++) { 
                        $amenity = $cabinAmenities[$kAux3];
                    }
                }
                $bedConfigurations = $cabinConfiguration['bedConfigurations'];
                if (count($bedConfigurations) > 0) {
                    for ($kAux4=0; $kAux4 < count($bedConfigurations); $kAux4++) { 
                        $BedCode = $bedConfigurations[$kAux4]['BedCode'];
                        $BedCount = $bedConfigurations[$kAux4]['BedCount'];
                    }
                }
            }
        }
        $transportationInfos = $selectedCategories[$k]['transportationInfos'];
        if (count($transportationInfos) > 0) {
            $transportation = "";
            for ($kAux5=0; $kAux5 < count($transportationInfos); $kAux5++) { 
                $transportation = $transportationInfos[$kAux5];
            }
        }
    }
}

$selectedDiningInfos = $reservationInfo['selectedDiningInfos'];
if (count($selectedDiningInfos) > 0) {
    for ($l=0; $l < count($selectedDiningInfos); $l++) { 
        $sitting = $selectedDiningInfos[$l]['sitting'];
        $status = $selectedDiningInfos[$l]['status'];
    }
}

$diningTableInfo = $reservationInfo['diningTableInfo'];
$diningTableCode = $diningTableInfo['diningTableCode'];
$smokingAllowed = $diningTableInfo['smokingAllowed'];

$guestsDetails = $reservationInfo['guestsDetails'];
if (count($guestsDetails) > 0) {
    for ($m=0; $m < count($guestsDetails); $m++) { 
        $guestNum = $guestsDetails[$m]['guestNum'];
        $gender = $guestsDetails[$m]['gender'];
        $age = $guestsDetails[$m]['age'];
        $personBirthDate = $guestsDetails[$m]['personBirthDate'];
        $email = $guestsDetails[$m]['email'];
        $loyaltyMembershipId = $guestsDetails[$m]['loyaltyMembershipId'];
        $guestRefNum = $guestsDetails[$m]['guestRefNum'];
        $occupationCode = $guestsDetails[$m]['occupationCode'];
        $coupon = $guestsDetails[$m]['coupon'];
        $status = $guestsDetails[$m]['status'];
        $personName = $guestsDetails[$m]['personName'];
        $namePrefix = $personName['namePrefix'];
        $firstName = $personName['firstName'];
        $middleName = $personName['middleName'];
        $lastName = $personName['lastName'];
        $nameSuffix = $personName['nameSuffix'];

        $contactInfo = $guestsDetails[$m]['contactInfo'];
        $guestContact = $contactInfo['guestContact'];
        $guestContacttype = $guestContact['type'];
        $guestContactnumber = $guestContact['number'];
        $emergencyContact = $contactInfo['emergencyContact'];
        $emergencyContactname = $emergencyContact['name'];
        $emergencyContactnumber = $emergencyContact['number'];

        $nationality = $guestsDetails[$m]['nationality'];
        $countryCode = $nationality['countryCode'];
        $stateProvCode = $nationality['stateProvCode'];

        $selectedInsurance = $guestsDetails[$m]['selectedInsurance'];
        $selectedInsurancetype = $selectedInsurance['type'];

        $address = $guestsDetails[$m]['address'];
        $addressLine1 = $address['addressLine1'];
        $addressLine2 = $address['addressLine2'];
        $postalCode = $address['postalCode'];
        $cityName = $address['cityName'];
        $stateProvCode = $address['stateProvCode'];

        $immigrationDocument = $guestsDetails[$m]['immigrationDocument'];
        $placeOfBithCountryCode = $immigrationDocument['placeOfBithCountryCode'];
        $documentIssueCountryCode = $immigrationDocument['documentIssueCountryCode'];
        $documentNum = $immigrationDocument['documentNum'];
        $issueDate = $immigrationDocument['issueDate'];
        $expireDate = $immigrationDocument['expireDate'];
        $socialSecurityNum = $immigrationDocument['socialSecurityNum'];

        $selectedDiningInfos = $guestsDetails[$m]['selectedDiningInfos'];
        if (count($selectedDiningInfos) > 0) {
            for ($mAux=0; $mAux < count($selectedDiningInfos); $mAux++) { 
                $sitting = $selectedDiningInfos[$mAux]['sitting'];
                $sittingInstance = $selectedDiningInfos[$mAux]['sittingInstance'];
                $sittingType = $selectedDiningInfos[$mAux]['sittingType'];
                $smokingAllowed = $selectedDiningInfos[$mAux]['smokingAllowed'];
                $crossReferencingAllowed = $selectedDiningInfos[$mAux]['crossReferencingAllowed'];
                $familyTimeIndicator = $selectedDiningInfos[$mAux]['familyTimeIndicator'];
                $prepaidGratuityRequired = $selectedDiningInfos[$mAux]['prepaidGratuityRequired'];
                $status = $selectedDiningInfos[$mAux]['status'];
            }
        }

        $selectedTransportationInfos = $guestsDetails[$m]['selectedTransportationInfos'];
        if (count($selectedTransportationInfos) > 0) {
            for ($mAux2=0; $mAux2 < count($selectedTransportationInfos); $mAux2++) { 
                $mode = $selectedTransportationInfos[$mAux2]['mode'];
                $segmentDirection = $selectedTransportationInfos[$mAux2]['segmentDirection'];
                $cityCode = $selectedTransportationInfos[$mAux2]['cityCode'];
                $transportGroupSeqNo = $selectedTransportationInfos[$mAux2]['transportGroupSeqNo'];
                $airAccommodation = $selectedTransportationInfos[$mAux2]['airAccommodation'];
                $airlineCabinClass = $airAccommodation['airlineCabinClass'];
                $departureCity = $airAccommodation['departureCity'];
                $arrivalCity = $airAccommodation['arrivalCity'];
            }
        }

        $selectedSpecialServices = $guestsDetails[$m]['selectedSpecialServices'];
        if (count($selectedSpecialServices) > 0) {
            for ($mAux3=0; $mAux3 < count($selectedSpecialServices); $mAux3++) { 
                $code = $selectedSpecialServices[$mAux3]['code'];
                $type = $selectedSpecialServices[$mAux3]['type'];
                $date = $selectedSpecialServices[$mAux3]['date'];
                $numOfYears = $selectedSpecialServices[$mAux3]['numOfYears'];
                $comments = $selectedSpecialServices[$mAux3]['comments'];
            }
        }

        $selectedPackages = $guestsDetails[$m]['selectedPackages'];
        if (count($selectedPackages) > 0) {
            for ($mAux4=0; $mAux4 < count($selectedPackages); $mAux4++) { 
                $code = $selectedPackages[$mAux4]['code'];
                $type = $selectedPackages[$mAux4]['type'];
                $duration = $selectedPackages[$mAux4]['duration'];
                $roomType = $selectedPackages[$mAux4]['roomType'];
                $mandatoryPackageInd = $selectedPackages[$mAux4]['mandatoryPackageInd'];
                $complimentaryPackageInd = $selectedPackages[$mAux4]['complimentaryPackageInd'];
                $rateType = $selectedPackages[$mAux4]['rateType'];
                $amount = $selectedPackages[$mAux4]['amount'];
            }
        }
    }
}

$selectedSpecialServices = $reservationInfo['selectedSpecialServices'];
if (count($selectedSpecialServices) > 0) {
    for ($n=0; $n < count($selectedSpecialServices); $n++) { 
        $code = $selectedSpecialServices[$n]['code'];
        $type = $selectedSpecialServices[$n]['type'];
        $description = $selectedSpecialServices[$n]['description'];
        $effectiveDate = $selectedSpecialServices[$n]['effectiveDate'];
        $discontinueDate = $selectedSpecialServices[$n]['discontinueDate'];
        $minGuestQuantity = $selectedSpecialServices[$n]['minGuestQuantity'];
        $amount = $selectedSpecialServices[$n]['amount'];
        $amountPerGuest = $selectedSpecialServices[$n]['amountPerGuest'];
        $requiresDate = $selectedSpecialServices[$n]['requiresDate'];
        $requiresYear = $selectedSpecialServices[$n]['requiresYear'];
        $status = $selectedSpecialServices[$n]['status'];
    }
}
$selectedPackages = $reservationInfo['selectedPackages'];
if (count($selectedPackages) > 0) {
    for ($p=0; $p < count($selectedPackages); $p++) { 
        $code = $selectedPackages[$p]['code'];
        $type = $selectedPackages[$p]['type'];
        $name = $selectedPackages[$p]['name'];
        $complimentaryInd = $selectedPackages[$p]['complimentaryInd'];
        $startDate = $selectedPackages[$p]['startDate'];
        $endDate = $selectedPackages[$p]['endDate'];
        $duration = $selectedPackages[$p]['duration'];
        $minGuestQuantity = $selectedPackages[$p]['minGuestQuantity'];
        $remarkText = $selectedPackages[$p]['remarkText'];
        $cityCode = $selectedPackages[$p]['cityCode'];
        $transportationGroupSeqNo = $selectedPackages[$p]['transportationGroupSeqNo'];
        $status = $selectedPackages[$p]['status'];
        $packageRate = $selectedPackages[$p]['packageRate'];
        if (count($packageRate) > 0) {
            for ($pAux=0; $pAux < count($packageRate); $pAux++) { 
                $amount = $packageRate[$pAux]['amount'];
                $rateType = $packageRate[$pAux]['rateType'];
            }
        }
    }
}
$travelWithResId = $reservationInfo['travelWithResId'];
if (count($travelWithResId) > 0) {
    $travelWithRes = "";
    for ($r=0; $r < count($travelWithResId); $r++) { 
        $travelWithRes = $travelWithResId[$r];
    }
}
$agencyGroupInfos = $reservationInfo['agencyGroupInfos'];
if (count($agencyGroupInfos) > 0) {
    for ($s=0; $s < count($agencyGroupInfos); $s++) { 
        $groupId = $agencyGroupInfos[$s]['groupId'];
        $groupName = $agencyGroupInfos[$s]['groupName'];
        $groupType = $agencyGroupInfos[$s]['groupType'];
        $agencyPointOfContact = $agencyGroupInfos[$s]['agencyPointOfContact'];
        $cruiseVendorPointOfContact = $agencyGroupInfos[$s]['cruiseVendorPointOfContact'];
        $remarkText = $agencyGroupInfos[$s]['remarkText'];
        $disclaimerText = $agencyGroupInfos[$s]['disclaimerText'];
        $currencyCode = $agencyGroupInfos[$s]['currencyCode'];
        $status = $agencyGroupInfos[$s]['status'];
    }
}
$modificationInfos = $reservationInfo['modificationInfos'];
if (count($modificationInfos) > 0) {
    for ($t=0; $t < count($modificationInfos); $t++) { 
        $guestNum = $modificationInfos[$t]['guestNum'];
        $modificationTypes = $modificationInfos[$t]['modificationTypes'];
        if (count($modificationTypes) > 0) {
            $modification = "";
            for ($tAux=0; $tAux < count($modificationTypes); $tAux++) { 
                $modification = $modificationTypes[$tAux];
            }
        }
    }
}
$bookingPrices = $reservationInfo['bookingPrices'];
$currencyCode = $bookingPrices['currencyCode'];
$bookingPrice = $bookingPrices['bookingPrice'];
if (count($bookingPrice) > 0) {
    for ($i=0; $i < count($bookingPrice); $i++) { 
        $amount = $bookingPrice[$i]['amount'];
        $priceTypeCode = $bookingPrice[$i]['priceTypeCode'];
    }
}
$guestPrices = $reservationInfo['guestPrices'];
if (count($guestPrices) > 0) {
    for ($j=0; $j < count($guestPrices); $j++) { 
        $guestNum = $guestPrices[$j]['guestNum'];
        $priceInfos = $guestPrices[$j]['priceInfos'];
        if (count($priceInfos) > 0) {
            for ($jAux=0; $jAux < count($priceInfos); $jAux++) { 
                $amount = $priceInfos[$jAux]['amount'];
                $priceTypeCode = $priceInfos[$jAux]['priceTypeCode'];
                $nonRefundableType = $priceInfos[$jAux]['nonRefundableType'];
            }
        }
    }
}
$otherPricingDetails = $reservationInfo['otherPricingDetails'];
$reservationNonRefundableType = $otherPricingDetails['reservationNonRefundableType'];
$nonRefundableType = $otherPricingDetails['nonRefundableType'];
$paymentInfo = $reservationInfo['paymentInfo'];
$paymentSummary = $paymentInfo['paymentSummary'];
$receivedFullPayment = $paymentSummary['receivedFullPayment'];
$totalPaymentReceived = $paymentSummary['totalPaymentReceived'];
$paymentSchedules = $paymentInfo['paymentSchedules'];
if (count($paymentSchedules) > 0) {
    for ($k=0; $k < count($paymentSchedules); $k++) { 
        $amount = $paymentSchedules[$k]['amount'];
        $dueDate = $paymentSchedules[$k]['dueDate'];
        $code = $paymentSchedules[$k]['code'];
    }
}
$travelAgencyInfo = $reservationInfo['travelAgencyInfo'];
$pcc = $travelAgencyInfo['pcc'];
$agentName = $travelAgencyInfo['agentName'];
$sabreSineId = $travelAgencyInfo['sabreSineId'];
$branchPhoneNum = $travelAgencyInfo['branchPhoneNum'];
$phoneNumForCredit = $travelAgencyInfo['phoneNumForCredit'];
$email = $travelAgencyInfo['email'];

$similarNameReservationsInfo = $response['similarNameReservationsInfo'];
$sailingsInfo = $similarNameReservationsInfo['sailingsInfo'];
if (count($sailingsInfo) > 0) {
    for ($x=0; $x < count($sailingsInfo); $x++) { 
        $departureDate = $sailingsInfo[$x]['departureDate'];
        $shipCode = $sailingsInfo[$x]['shipCode'];
        $duration = $sailingsInfo[$x]['duration'];
        $reservationInfo = $sailingsInfo[$x]['reservationInfo'];
        if (count($reservationInfo) > 0) {
            for ($xAux=0; $xAux < count($reservationInfo); $xAux++) { 
                $reservationId = $reservationInfo[$xAux]['reservationId'];
                $agencyGroupId = $reservationInfo[$xAux]['agencyGroupId'];
                $groupName = $reservationInfo[$xAux]['groupName'];
                $fareCode = $reservationInfo[$xAux]['fareCode'];
                $pricedCategoryCode = $reservationInfo[$xAux]['pricedCategoryCode'];
                $categoryGroupSeqNo = $reservationInfo[$xAux]['categoryGroupSeqNo'];
                $cabinNum = $reservationInfo[$xAux]['cabinNum'];
                $firstName = $reservationInfo[$xAux]['firstName'];
                $middleName = $reservationInfo[$xAux]['middleName'];
                $lastName = $reservationInfo[$xAux]['lastName'];
                $guestRefNum = $reservationInfo[$xAux]['guestRefNum'];
                $status = $reservationInfo[$xAux]['status'];
            }
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
