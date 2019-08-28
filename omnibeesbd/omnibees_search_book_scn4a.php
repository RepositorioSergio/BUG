<?php
if (! $_SERVER['DOCUMENT_ROOT']) {
    // On Command Line
    $return = "\r\n";
} else {
    // HTTP Browser
    $return = "<br>";
}

echo $return;
echo "Starting OTA Availability...";
echo $return;
$url = "https://pullcert.omnibees.com/PullService.svc?wsdl";
try {
    $client = new SoapClient($url, array(
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        "trace" => 1,
        "exceptions" => true,
        'soap_version' => SOAP_1_1
    ));
} catch (Exception $e) {
    echo $e->getMessage();
}
//
// var_dump($client->__getFunctions());
//
$from = "2020-08-05T00:00:00";
$to = "2020-08-09T00:00:00";
$rooms = 1;

$params = array();
$params['login']['UserName'] = "BugSoftware";
$params['login']['Password'] = "WO5bYE2A";
$params['ota_HotelAvailRQ']['PrimaryLangID'] = "en";
$params['ota_HotelAvailRQ']['EchoToken'] = "1154782d-ea51-478e-a2c2-02b66b5339c2";
$params['ota_HotelAvailRQ']['TimeStamp'] = strftime("%Y-%m-%dT%H:%m:%S", time());
$params['ota_HotelAvailRQ']['Target'] = "Test";
$params['ota_HotelAvailRQ']['Version'] = "2.6";
$params['ota_HotelAvailRQ']['BestOnly'] = false;
$params['ota_HotelAvailRQ']['IsModify'] = false;
$params['ota_HotelAvailRQ']['HotelSearchCriteria']['Criterion']['HotelRefs']['HotelRef']['HotelCode'] = 1053;
// Adult
$params['ota_HotelAvailRQ']['HotelSearchCriteria']['Criterion']['RoomStayCandidatesType']['RoomStayCandidates']['RoomStayCandidate'][0]['GuestCountsType']['GuestCounts']['GuestCount'][0]['AgeQualifyCode'] = "Adult";
$params['ota_HotelAvailRQ']['HotelSearchCriteria']['Criterion']['RoomStayCandidatesType']['RoomStayCandidates']['RoomStayCandidate'][0]['GuestCountsType']['GuestCounts']['GuestCount'][0]['Count'] = 2;
$params['ota_HotelAvailRQ']['HotelSearchCriteria']['Criterion']['StayDateRange']['End'] = $to;
$params['ota_HotelAvailRQ']['HotelSearchCriteria']['Criterion']['StayDateRange']['Start'] = $from;
// if (! file_exists('omnibees_search_debug_book.php')) {
try {
    $client->__soapCall('GetHotelAvail', array(
        $params
    ));
} catch (Exception $e) {
    echo $e->getMessage();
}
$xmlrequest = $client->__getLastRequest();
// echo $return;
// echo $return;
$xmlresult = $client->__getLastResponse();
// } else {
// include "omnibees_search_debug_book.php";
// }
/* echo $xmlrequest;
echo $return;
echo $return;
echo $xmlresult;
echo $return;
echo $return; */
echo "<xmp>";
echo $xmlrequest;
echo "</xmp>";

echo "<xmp>";
echo $xmlresult;
echo "</xmp>";

$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$node = $inputDoc->getElementsByTagName("Service");
$Services = array();
for ($x = 0; $x < $node->length; $x ++) {
    $ID = $node->item($x)->getElementsByTagName("ID");
    if ($ID->length > 0) {
        $ID = $ID->item(0)->nodeValue;
    } else {
        $ID = "";
    }
    $Quantity = $node->item($x)->getElementsByTagName("Quantity");
    if ($Quantity->length > 0) {
        $Quantity = $Quantity->item(0)->nodeValue;
    } else {
        $Quantity = "";
    }
    $ServiceRPH = $node->item($x)->getElementsByTagName("ServiceRPH");
    if ($ServiceRPH->length > 0) {
        $ServiceRPH = $ServiceRPH->item(0)->nodeValue;
    } else {
        $ServiceRPH = "";
    }
    $ServicePricingType = $node->item($x)->getElementsByTagName("ServicePricingType");
    if ($ServicePricingType->length > 0) {
        $ServicePricingType = $ServicePricingType->item(0)->nodeValue;
    } else {
        $ServicePricingType = "";
    }
    $RequestIndicator = $node->item($x)->getElementsByTagName("RequestIndicator");
    if ($RequestIndicator->length > 0) {
        $RequestIndicator = $RequestIndicator->item(0)->nodeValue;
    } else {
        $RequestIndicator = "";
    }
    $ServiceDescription = $node->item($x)->getElementsByTagName("ServiceDescription");
    if ($ServiceDescription->length > 0) {
        $Description = $ServiceDescription->item(0)->getElementsByTagName("Description");
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
        $Name = $ServiceDescription->item(0)->getElementsByTagName("Name");
        if ($Name->length > 0) {
            $Name = $Name->item(0)->nodeValue;
        } else {
            $Name = "";
        }
    } else {
        $ServiceDescription = "";
        $Description = "";
        $Name = "";
    }
    $Service["ID"] = $ID;
    $Service["ServiceRPH"] = $ServiceRPH;
    $Service["ServicePricingType"] = $ServicePricingType;
    $Service["RequestIndicator"] = $RequestIndicator;
    $Service["ServiceDescription"] = $Description;
    $Service["Name"] = $Name;
    $Service["Quantity"] = $Quantity;
    array_push($Services, $Service);
}
$node = $inputDoc->getElementsByTagName("RoomStay");
for ($x = 0; $x < $node->length; $x ++) {
    $RatePlans = array();
    $Rooms = array();
    $RoomRates = array();
    $HotelRef = $node->item($x)->getElementsByTagName("HotelRef");
    if ($HotelRef->length > 0) {
        $ChainCode = $HotelRef->item(0)->getElementsByTagName("ChainCode");
        if ($ChainCode->length > 0) {
            $ChainCode = $ChainCode->item(0)->nodeValue;
        } else {
            $ChainCode = "";
        }
        $HotelCode = $HotelRef->item(0)->getElementsByTagName("HotelCode");
        if ($HotelCode->length > 0) {
            $HotelCode = $HotelCode->item(0)->nodeValue;
        } else {
            $HotelCode = "";
        }
        echo "Chain Code:" . $ChainCode;
        echo $return;
        echo "Hotel Code:" . $HotelCode;
    }
    $aRatePlans = $node->item($x)->getElementsByTagName("RatePlans");
    if ($aRatePlans->length > 0) {
        $aRatePlans = $aRatePlans->item(0)->getElementsByTagName("RatePlanType");
        for ($z = 0; $z < $aRatePlans->length; $z ++) {
            $RatePlanID = $aRatePlans->item($z)->getElementsByTagName("RatePlanID");
            if ($RatePlanID->length > 0) {
                $RatePlanID = $RatePlanID->item(0)->nodeValue;
            } else {
                $RatePlanID = "";
            }
            $RatePlanName = $aRatePlans->item($z)->getElementsByTagName("RatePlanName");
            if ($RatePlanName->length > 0) {
                $RatePlanName = $RatePlanName->item(0)->nodeValue;
            } else {
                $RatePlanName = "";
            }
            $RatePlanTypeCode = $aRatePlans->item($z)->getElementsByTagName("RatePlanTypeCode");
            if ($RatePlanTypeCode->length > 0) {
                $RatePlanTypeCode = $RatePlanTypeCode->item(0)->nodeValue;
            } else {
                $RatePlanTypeCode = "";
            }
            $SortOrder = $aRatePlans->item($z)->getElementsByTagName("SortOrder");
            if ($SortOrder->length > 0) {
                $SortOrder = $SortOrder->item(0)->nodeValue;
            } else {
                $SortOrder = "";
            }
            $RatePlanInclusions = $aRatePlans->item($z)->getElementsByTagName("RatePlanInclusions");
            if ($RatePlanInclusions->length > 0) {
                $RatePlanInclusions = $RatePlanInclusions->item(0)->nodeValue;
            } else {
                $RatePlanInclusions = "";
            }
            $RatePlanDescription = $aRatePlans->item($z)->getElementsByTagName("RatePlanDescription");
            if ($RatePlanDescription->length > 0) {
                $RatePlanDescriptionDescription = $RatePlanDescription->item(0)->getElementsByTagName("Description");
                if ($RatePlanDescriptionDescription->length > 0) {
                    $RatePlanDescriptionDescription = $RatePlanDescriptionDescription->item(0)->nodeValue;
                } else {
                    $RatePlanDescriptionDescription = "";
                }
                $RatePlanDescriptionLanguage = $RatePlanDescription->item(0)->getElementsByTagName("Language");
                if ($RatePlanDescriptionLanguage->length > 0) {
                    $RatePlanDescriptionLanguage = $RatePlanDescriptionLanguage->item(0)->nodeValue;
                } else {
                    $RatePlanDescriptionLanguage = "";
                }
            } else {
                $RatePlanDescription = "";
                $RatePlanDescriptionDescription = "";
                $RatePlanDescriptionLanguage = "";
            }
            $RatePlans[$RatePlanID]['RatePlanID'] = $RatePlanID;
            $RatePlans[$RatePlanID]['RatePlanName'] = $RatePlanName;
            $RatePlans[$RatePlanID]['RatePlanTypeCode'] = $RatePlanTypeCode;
            $RatePlans[$RatePlanID]['SortOrder'] = $SortOrder;
            $RatePlans[$RatePlanID]['RatePlanInclusions'] = $RatePlanInclusions;
            $RatePlans[$RatePlanID]['RatePlanDescriptionDescription'] = $RatePlanDescriptionDescription;
            $RatePlans[$RatePlanID]['RatePlanDescriptionDescription'] = $RatePlanDescriptionDescription;
            $RatePlans[$RatePlanID]['RatePlanDescriptionLanguage'] = $RatePlanDescriptionLanguage;
            $Aux = array();
            $AdditionalDetailsType = $aRatePlans->item($z)->getElementsByTagName("AdditionalDetailsType");
            if ($AdditionalDetailsType->length > 0) {
                $AdditionalDetails = $AdditionalDetailsType->item(0)->getElementsByTagName("AdditionalDetails");
                if ($AdditionalDetails->length > 0) {
                    $AdditionalDetails->item(0)->getElementsByTagName("AdditionalDetail");
                    for ($i = 0; $i < $AdditionalDetails->length; $i ++) {
                        $DetailDescription = $AdditionalDetails->item($i)->getElementsByTagName("DetailDescription");
                        if ($DetailDescription->length > 0) {
                            $Description = $DetailDescription->item(0)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                $Description = $Description->item(0)->nodeValue;
                            } else {
                                $Description = "";
                            }
                            $Language = $DetailDescription->item(0)->getElementsByTagName("Language");
                            if ($Language->length > 0) {
                                $Language = $Language->item(0)->nodeValue;
                            } else {
                                $Language = "";
                            }
                            $Name = $DetailDescription->item(0)->getElementsByTagName("Name");
                            if ($Name->length > 0) {
                                $Name = $Name->item(0)->nodeValue;
                            } else {
                                $Name = "";
                            }
                            $Inf = array();
                            $Inf['Name'] = $Name;
                            $Inf['Language'] = $Language;
                            $Inf['Description'] = $Description;
                            array_push($Aux, $Inf);
                        }
                    }
                }
            }
            $RatePlans[$RatePlanID]['AdditionalDetails'] = $Aux;
            // Payment Policies
            $ppolicies = array();
            $PaymentPolicies = $aRatePlans->item($z)->getElementsByTagName("PaymentPolicies");
            if ($PaymentPolicies->length > 0) {
                $AcceptedPayment = $PaymentPolicies->item(0)->getElementsByTagName("AcceptedPayment");
                for ($wAcceptedPayment = 0; $wAcceptedPayment < $AcceptedPayment->length; $wAcceptedPayment ++) {
                    $GuaranteeID = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("GuaranteeID");
                    if ($GuaranteeID->length > 0) {
                        $GuaranteeID = $GuaranteeID->item(0)->nodeValue;
                    } else {
                        $GuaranteeID = "";
                    }
                    $ppolicies[$wAcceptedPayment]['GuaranteeID'] = $GuaranteeID;
                    $GuaranteeTypeCode = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("GuaranteeTypeCode");
                    if ($GuaranteeTypeCode->length > 0) {
                        $GuaranteeTypeCode = $GuaranteeTypeCode->item(0)->nodeValue;
                    } else {
                        $GuaranteeTypeCode = "";
                    }
                    $ppolicies[$wAcceptedPayment]['GuaranteeTypeCode'] = $GuaranteeTypeCode;
                    $PaymentCard = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("PaymentCard");
                    if ($PaymentCard->length > 0) {
                        $PaymentCard = $PaymentCard->item(0)->nodeValue;
                    } else {
                        $PaymentCard = "";
                    }
                    $ppolicies[$wAcceptedPayment]['PaymentCard'] = $PaymentCard;
                    $RPH = $AcceptedPayment->item($wAcceptedPayment)->getElementsByTagName("RPH");
                    if ($RPH->length > 0) {
                        $RPH = $RPH->item(0)->nodeValue;
                    } else {
                        $RPH = "";
                    }
                    $ppolicies[$wAcceptedPayment]['RPH'] = $RPH;
                }
            }
            $RatePlans[$RatePlanID]['PaymentPolicies'] = $ppolicies;
            // Guarantees
            $gguarantees = array();
            $Guarantees = $aRatePlans->item($z)->getElementsByTagName("Guarantees");
            if ($Guarantees->length > 0) {
                $Guarantee = $Guarantees->item(0)->getElementsByTagName("Guarantee");
                for ($wGuarantee = 0; $wGuarantee < $Guarantee->length; $wGuarantee ++) {
                    $AmountPercent = $Guarantee->item(0)->getElementsByTagName("AmountPercent");
                    if ($AmountPercent->length > 0) {
                        $AmountPercent = $AmountPercent->item(0)->nodeValue;
                    } else {
                        $AmountPercent = "";
                    }
                    $gguarantees[$wGuarantee]['AmountPercent'] = $AmountPercent;
                    $DeadLine = $Guarantee->item(0)->getElementsByTagName("DeadLine");
                    if ($DeadLine->length > 0) {
                        $DeadLine = $DeadLine->item(0)->nodeValue;
                    } else {
                        $DeadLine = "";
                    }
                    $gguarantees[$wGuarantee]['DeadLine'] = $DeadLine;
                    $Duration = $Guarantee->item(0)->getElementsByTagName("Duration");
                    if ($Duration->length > 0) {
                        $Duration = $Duration->item(0)->nodeValue;
                    } else {
                        $Duration = "";
                    }
                    $gguarantees[$wGuarantee]['Duration'] = $Duration;
                    $End = $Guarantee->item(0)->getElementsByTagName("End");
                    if ($End->length > 0) {
                        $End = $End->item(0)->nodeValue;
                    } else {
                        $End = "";
                    }
                    $gguarantees[$wGuarantee]['End'] = $End;
                    $GuaranteeCode = $Guarantee->item(0)->getElementsByTagName("GuaranteeCode");
                    if ($GuaranteeCode->length > 0) {
                        $GuaranteeCode = $GuaranteeCode->item(0)->nodeValue;
                    } else {
                        $GuaranteeCode = "";
                    }
                    $gguarantees[$wGuarantee]['GuaranteeCode'] = $GuaranteeCode;
                    $Start = $Guarantee->item(0)->getElementsByTagName("Start");
                    if ($Start->length > 0) {
                        $Start = $Start->item(0)->nodeValue;
                    } else {
                        $Start = "";
                    }
                    $gguarantees[$wGuarantee]['Start'] = $Start;
                    $GuaranteesAcceptedType = $Guarantee->item(0)->getElementsByTagName("GuaranteesAcceptedType");
                    if ($GuaranteesAcceptedType->length > 0) {
                        $GuaranteesAcceptedType = $GuaranteesAcceptedType->item(0)->nodeValue;
                    } else {
                        $GuaranteesAcceptedType = "";
                    }
                    $gguarantees[$wGuarantee]['GuaranteesAcceptedType'] = $GuaranteesAcceptedType;
                    $GuaranteeDescription = $Guarantee->item(0)->getElementsByTagName("GuaranteeDescription");
                    if ($GuaranteeDescription->length > 0) {
                        $GuaranteeDescriptionDescription = $GuaranteeDescription->item(0)->getElementsByTagName("Description");
                        if ($GuaranteeDescriptionDescription->length > 0) {
                            $GuaranteeDescriptionDescription = $GuaranteeDescriptionDescription->item(0)->nodeValue;
                        } else {
                            $GuaranteeDescriptionDescription = "";
                        }
                        $GuaranteeDescriptionName = $GuaranteeDescription->item(0)->getElementsByTagName("Name");
                        if ($GuaranteeDescriptionName->length > 0) {
                            $GuaranteeDescriptionName = $GuaranteeDescriptionName->item(0)->nodeValue;
                        } else {
                            $GuaranteeDescriptionName = "";
                        }
                        $GuaranteeDescriptionLanguage = $GuaranteeDescription->item(0)->getElementsByTagName("Language");
                        if ($GuaranteeDescriptionLanguage->length > 0) {
                            $GuaranteeDescriptionLanguage = $GuaranteeDescriptionLanguage->item(0)->nodeValue;
                        } else {
                            $GuaranteeDescriptionLanguage = "";
                        }
                    } else {
                        $GuaranteeDescriptionDescription = "";
                        $GuaranteeDescriptionLanguage = "";
                        $GuaranteeDescriptionName = "";
                    }
                    $gguarantees[$wGuarantee]['GuaranteeDescription'] = $GuaranteeDescriptionDescription;
                    $gguarantees[$wGuarantee]['GuaranteeDescriptionLanguage'] = $GuaranteeDescriptionLanguage;
                    $gguarantees[$wGuarantee]['GuaranteeDescriptionName'] = $GuaranteeDescriptionName;
                }
            }
            $RatePlans[$RatePlanID]['Guarantees2'] = $gguarantees;
            // Offers
            $ooffers = array();
            $Offers = $aRatePlans->item($z)->getElementsByTagName("Offers");
            if ($Offers->length > 0) {
                $Offer = $Offers->item(0)->getElementsByTagName("Offer");
                for ($wOffer = 0; $wOffer < $Offer->length; $wOffer ++) {
                    $CompatibleOffer = $Offer->item(0)->getElementsByTagName("CompatibleOffer");
                    if ($CompatibleOffer->length > 0) {
                        $IncompatibleOfferIndicator = $CompatibleOffer->item(0)->getElementsByTagName("IncompatibleOfferIndicator");
                        if ($IncompatibleOfferIndicator->length > 0) {
                            $IncompatibleOfferIndicator = $IncompatibleOfferIndicator->item(0)->nodeValue;
                        } else {
                            $IncompatibleOfferIndicator = "";
                        }
                        $CompatibleOffer = "";
                    } else {
                        $IncompatibleOfferIndicator = "";
                        $CompatibleOffer = "";
                    }
                    $ooffers[$wOffer]['CompatibleOffer'] = $CompatibleOffer;
                    $ooffers[$wOffer]['IncompatibleOfferIndicator'] = $IncompatibleOfferIndicator;
                    $OfferCode = $Offer->item(0)->getElementsByTagName("OfferCode");
                    if ($OfferCode->length > 0) {
                        $OfferCode = $OfferCode->item(0)->nodeValue;
                    } else {
                        $OfferCode = "";
                    }
                    $ooffers[$wOffer]['OfferCode'] = $OfferCode;
                    $RPH = $Offer->item(0)->getElementsByTagName("RPH");
                    if ($RPH->length > 0) {
                        $RPH = $RPH->item(0)->nodeValue;
                    } else {
                        $RPH = "";
                    }
                    $ooffers[$wOffer]['RPH'] = $RPH;
                    $End = $Offer->item(0)->getElementsByTagName("End");
                    if ($End->length > 0) {
                        $End = $End->item(0)->nodeValue;
                    } else {
                        $End = "";
                    }
                    $OfferDescription = $Offer->item(0)->getElementsByTagName("Description");
                    if ($OfferDescription->length > 0) {
                        $OfferDescriptionDescription = $OfferDescription->item(0)->getElementsByTagName("Description");
                        if ($OfferDescriptionDescription->length > 0) {
                            $OfferDescriptionDescription = $OfferDescriptionDescription->item(0)->nodeValue;
                        } else {
                            $OfferDescriptionDescription = "";
                        }
                        $OfferDescriptionLanguage = $OfferDescription->item(0)->getElementsByTagName("Language");
                        if ($OfferDescriptionLanguage->length > 0) {
                            $OfferDescriptionLanguage = $OfferDescriptionLanguage->item(0)->nodeValue;
                        } else {
                            $OfferDescriptionLanguage = "";
                        }
                    } else {
                        $OfferDescriptionDescription = "";
                        $OfferDescriptionLanguage = "";
                    }
                    $ooffers[$wOffer]['OfferDescription'] = $OfferDescriptionDescription;
                    $ooffers[$wOffer]['OfferDescriptionLanguage'] = $OfferDescriptionLanguage;
                    $OfferDiscount = $Offer->item(0)->getElementsByTagName("Discount");
                    if ($OfferDiscount->length > 0) {
                        $OfferAmountBeforeTax = $OfferDiscount->item(0)->getElementsByTagName("AmountBeforeTax");
                        if ($OfferAmountBeforeTax->length > 0) {
                            $OfferAmountBeforeTax = $OfferAmountBeforeTax->item(0)->nodeValue;
                        } else {
                            $OfferAmountBeforeTax = "";
                        }
                        $OfferPercent = $OfferDiscount->item(0)->getElementsByTagName("Percent");
                        if ($OfferPercent->length > 0) {
                            $OfferPercent = $OfferPercent->item(0)->nodeValue;
                        } else {
                            $OfferPercent = "";
                        }
                        $OfferChargeUnitCode = $OfferDiscount->item(0)->getElementsByTagName("ChargeUnitCode");
                        if ($OfferChargeUnitCode->length > 0) {
                            $OfferChargeUnitCode = $OfferChargeUnitCode->item(0)->nodeValue;
                        } else {
                            $OfferChargeUnitCode = "";
                        }
                        $OfferDiscountCode = $OfferDiscount->item(0)->getElementsByTagName("DiscountCode");
                        if ($OfferDiscountCode->length > 0) {
                            $OfferDiscountCode = $OfferDiscountCode->item(0)->nodeValue;
                        } else {
                            $OfferDiscountCode = "";
                        }
                        $OfferDiscountPattern = $OfferDiscount->item(0)->getElementsByTagName("DiscountPattern");
                        if ($OfferDiscountPattern->length > 0) {
                            $OfferDiscountPattern = $OfferDiscountPattern->item(0)->nodeValue;
                        } else {
                            $OfferDiscountPattern = "";
                        }
                        $OfferDiscountReason = $OfferDiscount->item(0)->getElementsByTagName("DiscountReason");
                        if ($OfferDiscountReason->length > 0) {
                            $OfferDiscountReason = $OfferDiscountReason->item(0)->nodeValue;
                        } else {
                            $OfferDiscountReason = "";
                        }
                        $OfferNightsDiscounted = $OfferDiscount->item(0)->getElementsByTagName("NightsDiscounted");
                        if ($OfferNightsDiscounted->length > 0) {
                            $OfferNightsDiscounted = $OfferNightsDiscounted->item(0)->nodeValue;
                        } else {
                            $OfferNightsDiscounted = "";
                        }
                        $OfferNightsRequired = $OfferDiscount->item(0)->getElementsByTagName("NightsRequired");
                        if ($OfferNightsRequired->length > 0) {
                            $OfferNightsRequired = $OfferNightsRequired->item(0)->nodeValue;
                        } else {
                            $OfferNightsRequired = "";
                        }
                    } else {
                        $OfferAmountBeforeTax = "";
                        $OfferChargeUnitCode = "";
                        $OfferDiscountCode = "";
                        $OfferDiscountPattern = "";
                        $OfferNightsDiscounted = "";
                        $OfferNightsRequired = "";
                        $OfferPercent = "";
                    }
                    $ooffers[$wOffer]['AmountBeforeTax'] = $OfferAmountBeforeTax;
                    $ooffers[$wOffer]['ChargeUnitCode'] = $OfferChargeUnitCode;
                    $ooffers[$wOffer]['DiscountCode'] = $OfferDiscountCode;
                    $ooffers[$wOffer]['DiscountPattern'] = $OfferDiscountPattern;
                    $ooffers[$wOffer]['DiscountReason'] = $OfferDiscountReason;
                    $ooffers[$wOffer]['NightsDiscounted'] = $OfferNightsDiscounted;
                    $ooffers[$wOffer]['NightsRequired'] = $OfferNightsRequired;
                    $ooffers[$wOffer]['Percent'] = $OfferPercent;
                    //
                    // Offer Rules
                    //
                    $DateRestrictionsArray = array();
                    $OfferRules = $Offer->item(0)->getElementsByTagName("OfferRules");
                    if ($OfferRules->length > 0) {
                        $OfferRule = $OfferRules->item(0)->getElementsByTagName("OfferRule");
                        if ($OfferRule->length > 0) {
                            $DateRestriction = $OfferRule->item(0)->getElementsByTagName("DateRestriction");
                            if ($DateRestriction->length > 0) {
                                $DateRestrictionDuration = $DateRestriction->item(0)->getElementsByTagName("Duration");
                                $DateRestrictionStart = $DateRestriction->item(0)->getElementsByTagName("Start");
                                $DateRestrictionEnd = $DateRestriction->item(0)->getElementsByTagName("End");
                                for ($sDateRestrictionDuration = 0; $sDateRestrictionDuration < $DateRestrictionDuration->length; $sDateRestrictionDuration ++) {
                                    $DateRestrictionsArray[$sDateRestrictionDuration]['Duration'] = $DateRestrictionDuration->item($sDateRestrictionDuration)->nodeValue;
                                    $DateRestrictionsArray[$sDateRestrictionDuration]['Start'] = $DateRestrictionStart->item($sDateRestrictionDuration)->nodeValue;
                                    $DateRestrictionsArray[$sDateRestrictionDuration]['End'] = $DateRestrictionEnd->item($sDateRestrictionDuration)->nodeValue;
                                }
                                $DateRestriction = "";
                            } else {
                                $DateRestriction = "";
                            }
                            $LengthsOfStay = $OfferRule->item(0)->getElementsByTagName("LengthsOfStay");
                            if ($LengthsOfStay->length > 0) {
                                $LengthsOfStay = $LengthsOfStay->item(0)->nodeValue;
                            } else {
                                $LengthsOfStay = "";
                            }
                            $MaxAdvancedBookingOffset = $OfferRule->item(0)->getElementsByTagName("MaxAdvancedBookingOffset");
                            if ($MaxAdvancedBookingOffset->length > 0) {
                                $MaxAdvancedBookingOffset = $MaxAdvancedBookingOffset->item(0)->nodeValue;
                            } else {
                                $MaxAdvancedBookingOffset = "";
                            }
                            $MinAdvancedBookingOffset = $OfferRule->item(0)->getElementsByTagName("MinAdvancedBookingOffset");
                            if ($MinAdvancedBookingOffset->length > 0) {
                                $MinAdvancedBookingOffset = $MinAdvancedBookingOffset->item(0)->nodeValue;
                            } else {
                                $MinAdvancedBookingOffset = "";
                            }
                        } else {
                            $OfferRules = "";
                            $DateRestriction = "";
                            $LengthsOfStay = "";
                            $MinAdvancedBookingOffset = "";
                            $MaxAdvancedBookingOffset = "";
                        }
                    } else {
                        $OfferRules = "";
                        $DateRestriction = "";
                        $LengthsOfStay = "";
                        $MinAdvancedBookingOffset = "";
                        $MaxAdvancedBookingOffset = "";
                    }
                    $ooffers[$wOffer]['OfferRules'] = $OfferRules;
                    $ooffers[$wOffer]['DateRestriction'] = $DateRestriction;
                    $ooffers[$wOffer]['DateRestrictionsArray'] = $DateRestrictionsArray;
                    $ooffers[$wOffer]['LengthsOfStay'] = $LengthsOfStay;
                    $ooffers[$wOffer]['MinAdvancedBookingOffset'] = $MinAdvancedBookingOffset;
                    $ooffers[$wOffer]['MaxAdvancedBookingOffset'] = $MaxAdvancedBookingOffset;
                }
            }
            $RatePlans[$RatePlanID]['Offers'] = $ooffers;
            // EOF
            $Description = "";
            $Dinner = false;
            $Breakfast = false;
            $ID = "";
            $Lunch = false;
            $MealPlanCode = "";
            $MealPlanIndicator = "";
            $Name = "";
            $MealsIncluded = $aRatePlans->item($z)->getElementsByTagName("MealsIncluded");
            if ($MealsIncluded->length > 0) {
                $Description = $MealsIncluded->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    $Description = $Description->item(0)->nodeValue;
                } else {
                    $Description = "";
                }
                $Dinner = $MealsIncluded->item(0)->getElementsByTagName("Dinner");
                if ($Dinner->length > 0) {
                    $Dinner = $Dinner->item(0)->nodeValue;
                } else {
                    $Dinner = false;
                }
                $Breakfast = $MealsIncluded->item(0)->getElementsByTagName("Breakfast");
                if ($Breakfast->length > 0) {
                    $Breakfast = $Breakfast->item(0)->nodeValue;
                } else {
                    $Breakfast = false;
                }
                $ID = $MealsIncluded->item(0)->getElementsByTagName("ID");
                if ($ID->length > 0) {
                    $ID = $ID->item(0)->nodeValue;
                } else {
                    $ID = "";
                }
                $Lunch = $MealsIncluded->item(0)->getElementsByTagName("Lunch");
                if ($Lunch->length > 0) {
                    $Lunch = $Lunch->item(0)->nodeValue;
                } else {
                    $Lunch = false;
                }
                $MealPlanCode = $MealsIncluded->item(0)->getElementsByTagName("MealPlanCode");
                if ($MealPlanCode->length > 0) {
                    $MealPlanCode = $MealPlanCode->item(0)->nodeValue;
                } else {
                    $MealPlanCode = "";
                }
                $MealPlanIndicator = $MealsIncluded->item(0)->getElementsByTagName("MealPlanIndicator");
                if ($MealPlanIndicator->length > 0) {
                    $MealPlanIndicator = $MealPlanIndicator->item(0)->nodeValue;
                } else {
                    $MealPlanIndicator = false;
                }
                $Name = $MealsIncluded->item(0)->getElementsByTagName("Name");
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
            }
            $RatePlans[$RatePlanID]['MealsIncluded']['Description'] = $Description;
            $RatePlans[$RatePlanID]['MealsIncluded']['Dinner'] = $Dinner;
            $RatePlans[$RatePlanID]['MealsIncluded']['Breakfast'] = $Breakfast;
            $RatePlans[$RatePlanID]['MealsIncluded']['ID'] = $ID;
            $RatePlans[$RatePlanID]['MealsIncluded']['Lunch'] = $Lunch;
            $RatePlans[$RatePlanID]['MealsIncluded']['MealPlanCode'] = $MealPlanCode;
            $RatePlans[$RatePlanID]['MealsIncluded']['MealPlanIndicator'] = $MealPlanIndicator;
            $RatePlans[$RatePlanID]['MealsIncluded']['Name'] = $Name;
            $Taxes = array();
            $TaxPolicies = $aRatePlans->item($z)->getElementsByTagName("TaxPolicies");
            if ($TaxPolicies->length > 0) {
                $TaxPolicies = $TaxPolicies->item(0)->getElementsByTagName("TaxPolicy");
                for ($i = 0; $i < $TaxPolicies->length; $i ++) {
                    $CurrencyCode = $TaxPolicies->item($i)->getElementsByTagName("CurrencyCode");
                    if ($CurrencyCode->length > 0) {
                        $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                    } else {
                        $CurrencyCode = "";
                    }
                    $Description = $TaxPolicies->item($i)->getElementsByTagName("Description");
                    if ($Description->length > 0) {
                        $Description = $Description->item(0)->nodeValue;
                    } else {
                        $Description = "";
                    }
                    $ID = $TaxPolicies->item($i)->getElementsByTagName("ID");
                    if ($ID->length > 0) {
                        $ID = $ID->item(0)->nodeValue;
                    } else {
                        $ID = "";
                    }
                    $IsPerNight = $TaxPolicies->item($i)->getElementsByTagName("IsPerNight");
                    if ($IsPerNight->length > 0) {
                        $IsPerNight = $IsPerNight->item(0)->nodeValue;
                    } else {
                        $IsPerNight = "";
                    }
                    $IsPerPerson = $TaxPolicies->item($i)->getElementsByTagName("IsPerPerson");
                    if ($IsPerPerson->length > 0) {
                        $IsPerPerson = $IsPerPerson->item(0)->nodeValue;
                    } else {
                        $IsPerPerson = "";
                    }
                    $IsPerRoom = $TaxPolicies->item($i)->getElementsByTagName("IsPerRoom");
                    if ($IsPerRoom->length > 0) {
                        $IsPerRoom = $IsPerRoom->item(0)->nodeValue;
                    } else {
                        $IsPerRoom = "";
                    }
                    $IsPerStay = $TaxPolicies->item($i)->getElementsByTagName("IsPerStay");
                    if ($IsPerStay->length > 0) {
                        $IsPerStay = $IsPerStay->item(0)->nodeValue;
                    } else {
                        $IsPerStay = "";
                    }
                    $IsValuePercentage = $TaxPolicies->item($i)->getElementsByTagName("IsValuePercentage");
                    if ($IsValuePercentage->length > 0) {
                        $IsValuePercentage = $IsValuePercentage->item(0)->nodeValue;
                    } else {
                        $IsValuePercentage = "";
                    }
                    $Name = $TaxPolicies->item($i)->getElementsByTagName("Name");
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Value = $TaxPolicies->item($i)->getElementsByTagName("Value");
                    if ($Value->length > 0) {
                        $Value = $Value->item(0)->nodeValue;
                    } else {
                        $Value = "";
                    }
                    $Tax = array();
                    $Tax['CurrencyCode'] = $CurrencyCode;
                    $Tax['Description'] = $Description;
                    $Tax['ID'] = $ID;
                    $IsPerNight = ($IsPerNight === 'true');
                    $Tax['IsPerNight'] = $IsPerNight;
                    $IsPerPerson = ($IsPerPerson === 'true');
                    $Tax['IsPerPerson'] = $IsPerPerson;
                    $IsPerRoom = ($IsPerRoom === 'true');
                    $Tax['IsPerRoom'] = $IsPerRoom;
                    $IsPerStay = ($IsPerStay === 'true');
                    $Tax['IsPerStay'] = $IsPerStay;
                    $IsValuePercentage = ($IsValuePercentage === 'true');
                    $Tax['IsValuePercentage'] = $IsValuePercentage;
                    $Tax['Name'] = $Name;
                    $Tax['Value'] = $Value;
                    array_push($Taxes, $Tax);
                }
            }
            $RatePlans[$RatePlanID]['Taxes'] = $Taxes;
            $Guarantees = array();
            $Guarantee = $aRatePlans->item($z)->getElementsByTagName("Guarantee");
            for ($i = 0; $i < $Guarantee->length; $i ++) {
                $Aux = array();
                $AmountPercent = $Guarantee->item($i)->getElementsByTagName("AmountPercent");
                if ($AmountPercent->length > 0) {
                    $Aux['AmountPercent'] = $AmountPercent->item(0)->nodeValue;
                }
                $DeadLine = $Guarantee->item($i)->getElementsByTagName("DeadLine");
                if ($DeadLine->length > 0) {
                    $Aux['DeadLine'] = $DeadLine->item(0)->nodeValue;
                }
                $Duration = $Guarantee->item($i)->getElementsByTagName("Duration");
                if ($Duration->length > 0) {
                    $Aux['Duration'] = $Duration->item(0)->nodeValue;
                }
                $End = $Guarantee->item($i)->getElementsByTagName("End");
                if ($End->length > 0) {
                    $Aux['End'] = $End->item(0)->nodeValue;
                }
                $GuaranteeCode = $Guarantee->item($i)->getElementsByTagName("GuaranteeCode");
                if ($GuaranteeCode->length > 0) {
                    $Aux['GuaranteeCode'] = $GuaranteeCode->item(0)->nodeValue;
                }
                $GuaranteesAcceptedType = $Guarantee->item($i)->getElementsByTagName("GuaranteesAcceptedType");
                if ($GuaranteesAcceptedType->length > 0) {
                    $Aux['GuaranteesAcceptedType'] = $GuaranteesAcceptedType->item(0)->nodeValue;
                }
                $Start = $Guarantee->item($i)->getElementsByTagName("Start");
                if ($Start->length > 0) {
                    $Aux['Start'] = $Start->item(0)->nodeValue;
                }
                $GuaranteeDescription = $Guarantee->item($i)->getElementsByTagName("GuaranteeDescription");
                if ($GuaranteeDescription->length > 0) {
                    $Description = $GuaranteeDescription->item(0)->getElementsByTagName("Description");
                    if ($Description->length > 0) {
                        $Aux['Description'] = $Description->item(0)->nodeValue;
                    }
                    $Language = $Guarantee->item(0)->getElementsByTagName("Language");
                    if ($Language->length > 0) {
                        $Aux['Language'] = $Language->item(0)->nodeValue;
                    }
                    $Name = $Guarantee->item(0)->getElementsByTagName("Name");
                    if ($Name->length > 0) {
                        $Aux['Name'] = $Name->item(0)->nodeValue;
                    }
                }
                array_push($Guarantees, $Aux);
            }
            $CurrencyCode = $aRatePlans->item($z)->getElementsByTagName("CurrencyCode");
            if ($CurrencyCode->length > 0) {
                $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
            } else {
                $CurrencyCode = "";
            }
            $Cancel = array();
            $CancelPenalties = $aRatePlans->item($z)->getElementsByTagName("CancelPenalties");
            if ($CancelPenalties->length > 0) {
                $CancelPenalties = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                for ($i = 0; $i < $CancelPenalties->length; $i ++) {
                    $NonRefundable = $CancelPenalties->item($i)->getElementsByTagName("NonRefundable");
                    if ($NonRefundable->length > 0) {
                        $NonRefundable = $NonRefundable->item(0)->nodeValue;
                    } else {
                        $NonRefundable = "";
                    }
                    $Start = $CancelPenalties->item($i)->getElementsByTagName("Start");
                    if ($Start->length > 0) {
                        $Start = $Start->item(0)->nodeValue;
                    } else {
                        $Start = "";
                    }
                    $End = $CancelPenalties->item($i)->getElementsByTagName("End");
                    if ($End->length > 0) {
                        $End = $End->item(0)->nodeValue;
                    } else {
                        $End = "";
                    }
                    $Duration = $CancelPenalties->item($i)->getElementsByTagName("Duration");
                    if ($Duration->length > 0) {
                        $Duration = $Duration->item(0)->nodeValue;
                    } else {
                        $Duration = "";
                    }
                    $PenaltyDescription = $CancelPenalties->item($i)->getElementsByTagName("PenaltyDescription");
                    if ($PenaltyDescription->length > 0) {
                        $PenaltyLanguage = $PenaltyDescription->item(0)->getElementsByTagName("Language");
                        if ($PenaltyLanguage->length > 0) {
                            $PenaltyLanguage = $PenaltyLanguage->item(0)->nodeValue;
                        } else {
                            $PenaltyLanguage = "";
                        }
                        $PenaltyName = $PenaltyDescription->item(0)->getElementsByTagName("Name");
                        if ($PenaltyName->length > 0) {
                            $PenaltyName = $PenaltyName->item(0)->nodeValue;
                        } else {
                            $PenaltyName = "";
                        }
                        $PenaltyDescription = $PenaltyDescription->item(0)->getElementsByTagName("Description");
                        if ($PenaltyDescription->length > 0) {
                            $PenaltyDescription = $PenaltyDescription->item(0)->nodeValue;
                        } else {
                            $PenaltyDescription = "";
                        }
                    } else {
                        $PenaltyLanguage = "";
                        $PenaltyName = "";
                        $PenaltyDescription = "";
                    }
                    $IsPerRoom = $CancelPenalties->item($i)->getElementsByTagName("IsPerRoom");
                    if ($IsPerRoom->length > 0) {
                        $IsPerRoom = $IsPerRoom->item(0)->nodeValue;
                    } else {
                        $IsPerRoom = "";
                    }
                    $IsPerStay = $CancelPenalties->item($i)->getElementsByTagName("IsPerStay");
                    if ($IsPerStay->length > 0) {
                        $IsPerStay = $IsPerStay->item(0)->nodeValue;
                    } else {
                        $IsPerStay = "";
                    }
                    $DeadLine = $CancelPenalties->item($i)->getElementsByTagName("DeadLine");
                    if ($DeadLine->length > 0) {
                        $AbsoluteDeadline = $DeadLine->item(0)->getElementsByTagName("AbsoluteDeadline");
                        if ($AbsoluteDeadline->length > 0) {
                            $AbsoluteDeadline = $AbsoluteDeadline->item(0)->nodeValue;
                        } else {
                            $AbsoluteDeadline = "";
                        }
                        $OffsetDropTime = $DeadLine->item(0)->getElementsByTagName("OffsetDropTime");
                        if ($OffsetDropTime->length > 0) {
                            $OffsetDropTime = $OffsetDropTime->item(0)->nodeValue;
                        } else {
                            $OffsetDropTime = "";
                        }
                        $OffsetUnitMultiplier = $DeadLine->item(0)->getElementsByTagName("OffsetUnitMultiplier");
                        if ($OffsetUnitMultiplier->length > 0) {
                            $OffsetUnitMultiplier = $OffsetUnitMultiplier->item(0)->nodeValue;
                        } else {
                            $OffsetUnitMultiplier = "";
                        }
                        $TimeUnitType = $DeadLine->item(0)->getElementsByTagName("TimeUnitType");
                        if ($TimeUnitType->length > 0) {
                            $TimeUnitType = $TimeUnitType->item(0)->nodeValue;
                        } else {
                            $TimeUnitType = "";
                        }
                    } else {
                        $AbsoluteDeadline = "";
                        $OffsetDropTime = "";
                        $OffsetUnitMultiplier = "";
                        $TimeUnitType = "";
                    }
                    $AmountPercent = $CancelPenalties->item($i)->getElementsByTagName("AmountPercent");
                    if ($AmountPercent->length > 0) {
                        $Amount = $AmountPercent->item(0)->getElementsByTagName("Amount");
                        if ($Amount->length) {
                            $Amount = $Amount->item(0)->nodeValue;
                        } else {
                            $Amount = 0;
                        }
                        $CurrencyCode = $AmountPercent->item(0)->getElementsByTagName("CurrencyCode");
                        if ($CurrencyCode->length) {
                            $CurrencyCode = $CurrencyCode->item(0)->nodeValue;
                        } else {
                            $CurrencyCode = "";
                        }
                        $NmbrOfNights = $AmountPercent->item(0)->getElementsByTagName("NmbrOfNights");
                        if ($NmbrOfNights->length) {
                            $NmbrOfNights = $NmbrOfNights->item(0)->nodeValue;
                        } else {
                            $NmbrOfNights = 0;
                        }
                        $Percent = $AmountPercent->item(0)->getElementsByTagName("Percent");
                        if ($Percent->length) {
                            $Percent = $Percent->item(0)->nodeValue;
                        } else {
                            $Percent = 0;
                        }
                    } else {
                        $AmountPercent = "";
                        $Amount = 0;
                        $CurrencyCode = "";
                        $Percent = 0;
                        $NmbrOfNights = 0;
                    }
                    $Penalty = array();
                    $NonRefundable = ($NonRefundable === 'true');
                    $Penalty['NonRefundable'] = $NonRefundable;
                    $Penalty['Start'] = $Start;
                    $Penalty['End'] = $End;
                    $Penalty['Duration'] = $Duration;
                    $Penalty['PenaltyDescription'] = $PenaltyDescription;
                    $Penalty['PenaltyLanguage'] = $PenaltyLanguage;
                    $Penalty['PenaltyName'] = $PenaltyName;
                    $Penalty['IsPerRoom'] = $IsPerRoom;
                    $Penalty['IsPerStay'] = $IsPerStay;
                    $Penalty['AbsoluteDeadline'] = $AbsoluteDeadline;
                    $Penalty['OffsetDropTime'] = $OffsetDropTime;
                    $Penalty['OffsetUnitMultiplier'] = $OffsetUnitMultiplier;
                    $Penalty['TimeUnitType'] = $TimeUnitType;
                    $Penalty['Amount'] = $Amount;
                    $Penalty['CurrencyCode'] = $CurrencyCode;
                    $Penalty['NmbrOfNights'] = $NmbrOfNights;
                    $Penalty['Percent'] = $Percent;
                    array_push($Cancel, $Penalty);
                }
            }
            $RatePlans[$RatePlanID]['CurrencyCode'] = $CurrencyCode;
            $RatePlans[$RatePlanID]['CancelPenalties'] = $Cancel;
            $RatePlans[$RatePlanID]['Guarantees'] = $Guarantees;
            echo ".";
        }
    }
    $aRoomTypes = $node->item($x)->getElementsByTagName("RoomTypes");
    if ($aRoomTypes->length > 0) {
        $aRoomTypes = $aRoomTypes->item(0)->getElementsByTagName("RoomType");
        for ($z = 0; $z < $aRoomTypes->length; $z ++) {
            echo ".";
            $RoomID = $aRoomTypes->item($z)->getElementsByTagName("RoomID");
            if ($RoomID->length > 0) {
                $RoomID = $RoomID->item(0)->nodeValue;
            } else {
                $RoomID = "";
            }
            $RoomName = $aRoomTypes->item($z)->getElementsByTagName("RoomName");
            if ($RoomName->length > 0) {
                $RoomName = $RoomName->item(0)->nodeValue;
            } else {
                $RoomName = "";
            }
            $MaxOccupancyAux = $aRoomTypes->item($z)->getElementsByTagName("MaxOccupancy");
            if ($MaxOccupancyAux->length > 0) {
                $MaxOccupancy = $MaxOccupancyAux->item(0)->nodeValue;
            } else {
                $MaxOccupancy = "";
            }
            $NumberOfUnits = $aRoomTypes->item($z)->getElementsByTagName("NumberOfUnits");
            if ($NumberOfUnits->length > 0) {
                $NumberOfUnits = $NumberOfUnits->item(0)->nodeValue;
            } else {
                $NumberOfUnits = "";
            }
            // TODO / AmenitiesType
            $RoomDescription = $aRoomTypes->item($z)->getElementsByTagName("RoomDescription");
            if ($RoomDescription->length > 0) {
                $RoomLanguage = $RoomDescription->item(0)->getElementsByTagName("Language");
                if ($RoomLanguage->length > 0) {
                    $RoomLanguage = $RoomLanguage->item(0)->nodeValue;
                } else {
                    $RoomLanguage = "";
                }
                $RoomDescription = $RoomDescription->item(0)->getElementsByTagName("Description");
                if ($RoomDescription->length > 0) {
                    $RoomDescription = $RoomDescription->item(0)->nodeValue;
                } else {
                    $RoomDescription = "";
                }
            } else {
                $RoomDescription = "";
                $RoomLanguage = "";
            }
            $Occupancies = array();
            $Occupancy = $aRoomTypes->item($z)->getElementsByTagName("Occupancy");
            for ($iu = 0; $iu < $Occupancy->length; $iu ++) {
                $Occup = array();
                $AgeQualifyingCode = $Occupancy->item($iu)->getElementsByTagName("AgeQualifyingCode");
                if ($AgeQualifyingCode->length > 0) {
                    $Occup['AgeQualifyingCode'] = $AgeQualifyingCode->item(0)->nodeValue;
                }
                $MaxAge = $Occupancy->item($iu)->getElementsByTagName("MaxAge");
                if ($MaxAge->length > 0) {
                    $Occup['MaxAge'] = $MaxAge->item(0)->nodeValue;
                }
                $AgeQualifyingCode = $Occupancy->item($iu)->getElementsByTagName("AgeQualifyingCode");
                if ($AgeQualifyingCode->length > 0) {
                    $Occup['AgeQualifyingCode'] = $AgeQualifyingCode->item(0)->nodeValue;
                }
                $MaxOccupancyAux = $Occupancy->item($iu)->getElementsByTagName("MaxOccupancy");
                if ($MaxOccupancyAux->length > 0) {
                    $Occup['MaxOccupancy'] = $MaxOccupancyAux->item(0)->nodeValue;
                }
                $MinAge = $Occupancy->item($iu)->getElementsByTagName("MinAge");
                if ($MinAge->length > 0) {
                    $Occup['MinAge'] = $MinAge->item(0)->nodeValue;
                }
                $MinOccupancy = $Occupancy->item($iu)->getElementsByTagName("MinOccupancy");
                if ($MinOccupancy->length > 0) {
                    $Occup['MinOccupancy'] = $MinOccupancy->item(0)->nodeValue;
                }
                array_push($Occupancies, $Occup);
            }
            $Rooms[$RoomID]['RoomID'] = $RoomID;
            $Rooms[$RoomID]['RoomName'] = $RoomName;
            $Rooms[$RoomID]['MaxOccupancy'] = $MaxOccupancy;
            $Rooms[$RoomID]['NumberOfUnits'] = $NumberOfUnits;
            $Rooms[$RoomID]['RoomDescription'] = $RoomDescription;
            $Rooms[$RoomID]['RoomLanguage'] = $RoomLanguage;
            $Rooms[$RoomID]['Occupancies'] = $Occupancies;
        }
    }
    $RoomRate = $node->item($x)->getElementsByTagName("RoomRate");
    for ($z = 0; $z < $RoomRate->length; $z ++) {
        $RatePlanID = $RoomRate->item($z)->getElementsByTagName("RatePlanID");
        if ($RatePlanID->length > 0) {
            $RatePlanID = $RatePlanID->item(0)->nodeValue;
        } else {
            $RatePlanID = "";
        }
        $RoomID = $RoomRate->item($z)->getElementsByTagName("RoomID");
        if ($RoomID->length > 0) {
            $RoomID = $RoomID->item(0)->nodeValue;
        } else {
            $RoomID = "";
        }
        $RoomStayCandidateRPH = $RoomRate->item($z)->getElementsByTagName("RoomStayCandidateRPH");
        if ($RoomStayCandidateRPH->length > 0) {
            $RoomStayCandidateRPH = $RoomStayCandidateRPH->item(0)->nodeValue;
        } else {
            $RoomStayCandidateRPH = "";
        }
        $AdvanceBookingRestriction = $RoomRate->item($z)->getElementsByTagName("AdvanceBookingRestriction");
        if ($AdvanceBookingRestriction->length > 0) {
            $AdvanceBookingRestriction = $AdvanceBookingRestriction->item(0)->nodeValue;
        } else {
            $AdvanceBookingRestriction = "";
        }
        $Discount = $RoomRate->item($z)->getElementsByTagName("Discount");
        if ($Discount->length > 0) {
            $Discount = $Discount->item(0)->nodeValue;
        } else {
            $Discount = "";
        }
        $EffectiveDateAux = $RoomRate->item($z)->getElementsByTagName("EffectiveDate");
        if ($EffectiveDateAux->length > 0) {
            $EffectiveDate = $EffectiveDateAux->item(0)->nodeValue;
        } else {
            $EffectiveDate = "";
        }
        $ExpireDate = $RoomRate->item($z)->getElementsByTagName("ExpireDate");
        if ($ExpireDate->length > 0) {
            $ExpireDate = $ExpireDate->item(0)->nodeValue;
        } else {
            $ExpireDate = "";
        }
        $PromotionCode = $RoomRate->item($z)->getElementsByTagName("PromotionCode");
        if ($PromotionCode->length > 0) {
            $PromotionCode = $PromotionCode->item(0)->nodeValue;
        } else {
            $PromotionCode = "";
        }
        $GroupCode = $RoomRate->item($z)->getElementsByTagName("GroupCode");
        if ($GroupCode->length > 0) {
            $GroupCode = $GroupCode->item(0)->nodeValue;
        } else {
            $GroupCode = "";
        }
        $Rates = array();
        $RatesType = $RoomRate->item($z)->getElementsByTagName("Rate");
        for ($w = 0; $w < $RatesType->length; $w ++) {
            $Rate = array();
            $AgeQualifyingCode = $RatesType->item($w)->getElementsByTagName("AgeQualifyingCode");
            if ($AgeQualifyingCode->length > 0) {
                $Rate['AgeQualifyingCode'] = $AgeQualifyingCode->item(0)->nodeValue;
            }
            $Duration = $RatesType->item($w)->getElementsByTagName("Duration");
            if ($Duration->length > 0) {
                $Rate['Duration'] = $Duration->item(0)->nodeValue;
            }
            $EffectiveDateAux = $RatesType->item($w)->getElementsByTagName("EffectiveDate");
            if ($EffectiveDateAux->length > 0) {
                $Rate['EffectiveDate'] = $EffectiveDateAux->item(0)->nodeValue;
            }
            $MaxAge = $RatesType->item($w)->getElementsByTagName("MaxAge");
            if ($MaxAge->length > 0) {
                $Rate['MaxAge'] = $MaxAge->item(0)->nodeValue;
            }
            $MaxGuestApplicable = $RatesType->item($w)->getElementsByTagName("MaxGuestApplicable");
            if ($MaxGuestApplicable->length > 0) {
                $Rate['MaxGuestApplicable'] = $MaxGuestApplicable->item(0)->nodeValue;
            }
            $MaxLOS = $RatesType->item($w)->getElementsByTagName("MaxLOS ");
            if ($MaxLOS->length > 0) {
                $Rate['MaxLOS '] = $MaxLOS->item(0)->nodeValue;
            }
            $MinAdvancedBookingOffset = $RatesType->item($w)->getElementsByTagName("MinAdvancedBookingOffset");
            if ($MinAdvancedBookingOffset->length > 0) {
                $Rate['MinAdvancedBookingOffset'] = $MinAdvancedBookingOffset->item(0)->nodeValue;
            }
            $MinAge = $RatesType->item($w)->getElementsByTagName("MinAge");
            if ($MinAge->length > 0) {
                $Rate['MinAge'] = $MinAge->item(0)->nodeValue;
            }
            $MinGuestApplicable = $RatesType->item($w)->getElementsByTagName("MinGuestApplicable");
            if ($MinGuestApplicable->length > 0) {
                $Rate['MinGuestApplicable'] = $MinGuestApplicable->item(0)->nodeValue;
            }
            $MinLOS = $RatesType->item($w)->getElementsByTagName("MinLOS");
            if ($MinLOS->length > 0) {
                $Rate['MinLOS'] = $MinLOS->item(0)->nodeValue;
            }
            $MaxLOS = $RatesType->item($w)->getElementsByTagName("MaxLOS");
            if ($MaxLOS->length > 0) {
                $Rate['MaxLOS'] = $MaxLOS->item(0)->nodeValue;
            }
            $NumberOfUnits = $RatesType->item($w)->getElementsByTagName("NumberOfUnits");
            if ($NumberOfUnits->length > 0) {
                $Rate['NumberOfUnits'] = $NumberOfUnits->item(0)->nodeValue;
            }
            $Status = $RatesType->item($w)->getElementsByTagName("Status");
            if ($Status->length > 0) {
                $Rate['Status'] = $Status->item(0)->nodeValue;
            }
            $StayThrough = $RatesType->item($w)->getElementsByTagName("StayThrough");
            if ($StayThrough->length > 0) {
                $Rate['StayThrough'] = $StayThrough->item(0)->nodeValue;
            }
            $Total = $RatesType->item($w)->getElementsByTagName("Total");
            if ($Total->length > 0) {
                $Totals = array();
                $AmountAfterTax = $Total->item(0)->getElementsByTagName("AmountAfterTax");
                if ($AmountAfterTax->length > 0) {
                    $Totals['AmountAfterTax'] = $AmountAfterTax->item(0)->nodeValue;
                }
                $AmountBeforeTax = $Total->item(0)->getElementsByTagName("AmountBeforeTax");
                if ($AmountBeforeTax->length > 0) {
                    $Totals['AmountBeforeTax'] = $AmountBeforeTax->item(0)->nodeValue;
                }
                $AmountIncludingMarkup = $Total->item(0)->getElementsByTagName("AmountIncludingMarkup");
                if ($AmountIncludingMarkup->length > 0) {
                    $Totals['AmountIncludingMarkup'] = $AmountIncludingMarkup->item(0)->nodeValue;
                }
                $AmountIsPackage = $Total->item(0)->getElementsByTagName("AmountIsPackage");
                if ($AmountIsPackage->length > 0) {
                    $Totals['AmountIsPackage'] = $AmountIsPackage->item(0)->nodeValue;
                }
                $ChargeType = $Total->item(0)->getElementsByTagName("ChargeType");
                if ($ChargeType->length > 0) {
                    $Totals['ChargeType'] = $ChargeType->item(0)->nodeValue;
                }
                $CurrencyCode = $Total->item(0)->getElementsByTagName("CurrencyCode");
                if ($CurrencyCode->length > 0) {
                    $Totals['CurrencyCode'] = $CurrencyCode->item(0)->nodeValue;
                }
                $TPA_Extensions = array();
                $TPA = $Total->item(0)->getElementsByTagName("TPA_Extensions");
                if ($TPA->length > 0) {
                    $ApprovalInvoiced = $TPA->item(0)->getElementsByTagName("ApprovalInvoiced");
                    if ($ApprovalInvoiced->length > 0) {
                        $TPA_Extensions['ApprovalInvoiced'] = $ApprovalInvoiced->item(0)->nodeValue;
                    }
                    $GuestsTotalRate = $TPA->item(0)->getElementsByTagName("GuestsTotalRate");
                    if ($GuestsTotalRate->length > 0) {
                        $TPA_Extensions['GuestsTotalRate'] = $GuestsTotalRate->item(0)->nodeValue;
                    }
                    $IsPreferredHotel = $TPA->item(0)->getElementsByTagName("IsPreferredHotel");
                    if ($IsPreferredHotel->length > 0) {
                        $TPA_Extensions['IsPreferredHotel'] = $IsPreferredHotel->item(0)->nodeValue;
                    }
                    $RatesAux = $TPA->item(0)->getElementsByTagName("Rates");
                    if ($RatesAux->length > 0) {
                        $TPA_Extensions['Rates'] = $RatesAux->item(0)->nodeValue;
                    }
                    $Services = $TPA->item(0)->getElementsByTagName("Services");
                    if ($Services->length > 0) {
                        $TPA_Extensions['Services'] = $Services->item(0)->nodeValue;
                    }
                    $TotalDiscountValue = $TPA->item(0)->getElementsByTagName("TotalDiscountValue");
                    if ($TotalDiscountValue->length > 0) {
                        $TPA_Extensions['TotalDiscountValue'] = $TotalDiscountValue->item(0)->nodeValue;
                    }
                }
                $Totals['TPA_Extensions'] = $TPA_Extensions;
                $Rate['Total'] = $Totals;
            }
            array_push($Rates, $Rate);
        }
        $Total = array();
        $Totals = $RoomRate->item($z)->getElementsByTagName("Total");
        if ($Totals->length > 0) {
            $AmountAfterTax = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountAfterTax");
            if ($AmountAfterTax->length > 0) {
                $Total['AmountAfterTax'] = $AmountAfterTax->item(0)->nodeValue;
            }
            $AmountBeforeTax = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountBeforeTax");
            if ($AmountBeforeTax->length > 0) {
                $Total['AmountBeforeTax'] = $AmountBeforeTax->item(0)->nodeValue;
            }
            $AmountIncludingMarkup = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountIncludingMarkup");
            if ($AmountIncludingMarkup->length > 0) {
                $Total['AmountIncludingMarkup'] = $AmountIncludingMarkup->item(0)->nodeValue;
            }
            $AmountIsPackage = $Totals->item($Totals->length - 1)->getElementsByTagName("AmountIsPackage");
            if ($AmountIsPackage->length > 0) {
                $Total['AmountIsPackage'] = $AmountIsPackage->item(0)->nodeValue;
            }
            $ChargeType = $Totals->item($Totals->length - 1)->getElementsByTagName("ChargeType");
            if ($ChargeType->length > 0) {
                $Total['ChargeType'] = $ChargeType->item(0)->nodeValue;
            }
            $CurrencyCode = $Totals->item($Totals->length - 1)->getElementsByTagName("CurrencyCode");
            if ($CurrencyCode->length > 0) {
                $Total['CurrencyCode'] = $CurrencyCode->item(0)->nodeValue;
            }
            $TPA_Extensions = array();
            $TPA = $Totals->item($Totals->length - 1)->getElementsByTagName("TPA_Extensions");
            if ($TPA->length > 0) {
                $ApprovalInvoiced = $TPA->item(0)->getElementsByTagName("ApprovalInvoiced");
                if ($ApprovalInvoiced->length > 0) {
                    $TPA_Extensions['ApprovalInvoiced'] = $ApprovalInvoiced->item(0)->nodeValue;
                }
                $GuestsTotalRate = $TPA->item(0)->getElementsByTagName("GuestsTotalRate");
                if ($GuestsTotalRate->length > 0) {
                    $TPA_Extensions['GuestsTotalRate'] = $GuestsTotalRate->item(0)->nodeValue;
                }
                $IsPreferredHotel = $TPA->item(0)->getElementsByTagName("IsPreferredHotel");
                if ($IsPreferredHotel->length > 0) {
                    $TPA_Extensions['IsPreferredHotel'] = $IsPreferredHotel->item(0)->nodeValue;
                }
                $RatesAux = $TPA->item(0)->getElementsByTagName("Rates");
                if ($RatesAux->length > 0) {
                    $TPA_Extensions['Rates'] = $RatesAux->item(0)->nodeValue;
                }
                $Services = $TPA->item(0)->getElementsByTagName("Services");
                if ($Services->length > 0) {
                    $TPA_Extensions['Services'] = $Services->item(0)->nodeValue;
                }
                $TotalDiscountValue = $TPA->item(0)->getElementsByTagName("TotalDiscountValue");
                if ($TotalDiscountValue->length > 0) {
                    $TPA_Extensions['TotalDiscountValue'] = $TotalDiscountValue->item(0)->nodeValue;
                }
            }
            $Total['TPA_Extensions'] = $TPA_Extensions;
        }
        // TODO
        // ServiceRPHs
        // TPA_Extensions
        $Rate = array();
        $Rate['RatePlanID'][$RoomStayCandidateRPH] = $RatePlanID;
        $Rate['RoomID'][$RoomStayCandidateRPH] = $RoomID;
        $Rate['Discount'][$RoomStayCandidateRPH] = $Discount;
        $Rate['EffectiveDate'][$RoomStayCandidateRPH] = $EffectiveDate;
        $Rate['ExpireDate'][$RoomStayCandidateRPH] = $ExpireDate;
        $Rate['PromotionCode'][$RoomStayCandidateRPH] = $PromotionCode;
        $Rate['GroupCode'][$RoomStayCandidateRPH] = $GroupCode;
        $Rate['RoomStayCandidateRPH'][$RoomStayCandidateRPH] = $RoomStayCandidateRPH;
        $Rate['AdvanceBookingRestriction'][$RoomStayCandidateRPH] = $AdvanceBookingRestriction;
        $Rate['Rates'][$RoomStayCandidateRPH] = $Rates;
        $Rate['Total'][$RoomStayCandidateRPH] = $Total;
        array_push($RoomRates, $Rate);
    }
}
echo $return;
echo $return;
$HotelCode[0] = "1053";
$ChainCode[0] = "986";
$RoomID = array();
$RatePlanID = array();
$RoomID[0] = "4270";
$RatePlanID[0] = "23554";
//
// $RoomID[1] = "5713";
// $RatePlanID[1] = "13374";
//
$adults[0] = 2;
$children[0] = 0;
$childrenAges[0][0] = 0;
// $adults[1] = 2;
// $children[1] = 1;
// $childrenAges[1][0] = 10;
// $services = array();
// $services[0][0]['RPH'] = "3080"; // Massage
// $services[0][1]['RPH'] = "3081"; // Champa
// $services[1][0]['RPH'] = "3083"; // Flowers
// $services[1][1]['RPH'] = "3069"; // Half Board
// $AmountBeforeTax = 0;
// $AmountAfterTax = 0;
for ($xRooms = 0; $xRooms < $rooms; $xRooms ++) {
    echo $return;
    echo "Room:" . $xRooms;
    echo $return;
    echo "Room Id = " . $RoomID[$xRooms];
    echo $return;
    echo "Rate Plan = " . $RatePlanID[$xRooms];
    echo $return;
    for ($r = 0; $r < count($RoomRates); $r ++) {
        // echo $RoomRates[$r]['RoomID'][$xRooms];
        // echo $return;
        if ($RoomRates[$r]['RoomID'][$xRooms] == $RoomID[$xRooms]) {
            if ($RoomRates[$r]['RatePlanID'][$xRooms] == $RatePlanID[$xRooms]) {
                if ($RoomRates[$r]['RoomStayCandidateRPH'][$xRooms] == $xRooms) {
                    // echo $return;
                    // echo $return;
                    // var_dump($RoomRates[$r]);
                    // echo $return;
                    // echo $return;
                    // var_dump($RatePlans[$RatePlanID[$xRooms]]);
                    // echo $return;
                    // echo $return;
                    // var_dump($Rooms[$RoomID[$xR]]);
                    // echo $return;
                    $AmountAfterTax = $AmountAfterTax + $RoomRates[$r]["Total"][$xRooms]["AmountAfterTax"];
                    echo $return;
                    echo "Amount after tax:" . $RoomRates[$r]["Total"][$xRooms]["AmountAfterTax"];
                    echo $return;
                    echo "Amount after tax (SUM):" . $AmountAfterTax;
                    echo $return;
                    $AmountBeforeTax = $AmountBeforeTax + $RoomRates[$r]["Total"][$xRooms]["AmountBeforeTax"];
                    echo $return;
                    echo "Amount before tax:" . $RoomRates[$r]["Total"][$xRooms]["AmountBeforeTax"];
                    echo $return;
                    echo "Amount before tax (SUM):" . $AmountBeforeTax;
                    echo $return;
                    $AmountIncludingMarkup = $RoomRates[$r]["Total"][$xRooms]["AmountIncludingMarkup"];
                    $AmountIncludingMarkup = ($AmountIncludingMarkup === 'true');
                    $AmountIsPackage = $RoomRates[$r]["Total"][$xRooms]["AmountIsPackage"];
                    $AmountIsPackage = ($AmountIsPackage === 'true');
                    $ChargeType = $RoomRates[$r]["Total"][$xRooms]["ChargeType"];
                    $CurrencyCode = $RoomRates[$r]["Total"][$xRooms]["CurrencyCode"];
                }
            }
        }
    }
}
echo $return;
echo "Amount Before Tax:" . $AmountBeforeTax;
echo $return;
echo $return;
echo "Amount After Tax:" . $AmountAfterTax;
echo $return;
echo $return;
echo "Starting OTA Book...";
echo $return;

$url = "https://pullcert.omnibees.com/PullService.svc?wsdl";
try {
    $client = new SoapClient($url, array(
        'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        "trace" => 1,
        "exceptions" => true,
        'soap_version' => SOAP_1_1
    ));
} catch (Exception $e) {
    echo $e->getMessage();
}
$params = array();
$params['login']['UserName'] = "BugSoftware";
$params['login']['Password'] = "WO5bYE2A";
$params['ota_HotelResRQ']['PrimaryLangID'] = "en";
$params['ota_HotelResRQ']['EchoToken'] = "1154782d-ea51-478e-a2c2-02b66b5339c2";
$params['ota_HotelResRQ']['TimeStamp'] = strftime("%Y-%m-%dT%H:%m:%S", time());
$params['ota_HotelResRQ']['Target'] = "Test";
$params['ota_HotelResRQ']['Version'] = "2.6";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['CreateDateTime'] = "0001-01-01T00:00:00";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['LastModifyDateTime'] = "0001-01-01T00:00:00";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['CommentsType']['Comments']['Comment']['Description'] = "This is the reservations comments";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['GuaranteeID'] = null;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['GuaranteeTypeCode'] = "DirectBill";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['PaymentCard']['CardCode'] = "Visa";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['PaymentCard']['CardHolderName'] = "Consumer";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['PaymentCard']['CardNumber'] = "4111111111111111";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['PaymentCard']['EffectiveDate'] = strftime("%Y-%m-%dT%H:%m:%S", time());
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['PaymentCard']['ExpireDate'] = "2021-01-01T00:00:00";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['PaymentCard']['SeriesCode'] = "737";
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Guarantee']['GuaranteesAcceptedType']['GuaranteesAccepted']['GuaranteeAccepted']['PaymentCard']['RPH'] = 0;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Total']['AmountAfterTax'] = $AmountAfterTax;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Total']['AmountBeforeTax'] = $AmountBeforeTax;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Total']['AmountIncludingMarkup'] = $AmountIncludingMarkup;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Total']['AmountIsPackage'] = $AmountIsPackage;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Total']['ChargeType'] = 'PerStay';
// $ChargeType;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGlobalInfo']['Total']['CurrencyCode'] = $CurrencyCode;
$ResGuestRPH = 1;
$rS = 0;
for ($r = 0; $r < $rooms; $r ++) {
    echo $return;
    echo "Room Id:" . $RoomID[$r];
    echo $return;
    echo "Rate Plain ID:" . $RatePlanID[$r];
    echo $return;
    //
    // Guests
    //
    $adt = 1;
    for ($w = 0; $w < $adults[$r]; $w ++) {
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Age'] = 0;
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['AgeQualifyingCode'] = "Adult";
        if ($ResGuestRPH == 1) {
            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['PrimaryIndicator'] = true;
        } else {
            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['PrimaryIndicator'] = false;
        }
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['AddressLine'] = "Rua 25";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['CityCode'] = null;
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['CountryCode'] = "0";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['PostalCode'] = "8000 Faro";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['StateProvCode'] = "0";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['BirthDate'] = "1980-05-01T00:00:00";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Email'] = "paulo@corp.bug-software.com";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Gender'] = "Male";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['GivenName'] = "Ob Adult " . $adt;
        $adt ++; // Remove -> Only need for certification
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['MiddleName'] = null;
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['NamePrefix'] = "None";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['Surname'] = "Smoth";
        // $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Telephones'][0]['TelephoneInfo']['PhoneLocationType'] = "Home";
        // $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Telephones'][0]['TelephoneInfo']['PhoneNumber'] = "215 252 252";
        // $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Telephones'][0]['TelephoneInfo']['PhoneTechType'] = "Voice";
        // $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Telephones'][0]['TelephoneInfo']['ShareMarketInd'] = "Yes";
        // $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Telephones'][0]['TelephoneInfo']['ShareSyncInd'] = "Yes";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['ResGuestRPH'] = $ResGuestRPH;
        $ResGuestRPH ++;
        $rS ++;
    }
    // Children
    $chd = 1;
    for ($w = 0; $w < $children[$r]; $w ++) {
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Age'] = (int) $childrenAges[$r][$w];
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['AgeQualifyingCode'] = "Child";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['PrimaryIndicator'] = false;
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['AddressLine'] = "Rua 25";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['CityCode'] = null;
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['CountryCode'] = "0";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['PostalCode'] = "8000 Faro";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Address']['StateProvCode'] = "0";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['BirthDate'] = "1980-05-01T00:00:00";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Email'] = "paulo@corp.bug-software.com";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['Gender'] = "Male";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['GivenName'] = "Ob child " . $chd;
        $chd ++; // Remove -> Only need for certification
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['MiddleName'] = null;
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['NamePrefix'] = "None";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['Profiles']['ProfileInfos']['ProfileInfo']['Profile']['Customer']['PersonName']['Surname'] = "Smoth";
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResGuestsType']['ResGuests']['ResGuest'][$rS]['ResGuestRPH'] = $ResGuestRPH;
        $ResGuestRPH ++;
        $rS ++;
    }
}
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResStatus']['PMS_ResStatusType'] = null;
$params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['ResStatus']['TransactionActionType'] = "Book";
$valid = false;
$ResGuestRPH = 1;
for ($xRooms = 0; $xRooms < $rooms; $xRooms ++) {
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['MoreIndicator'] = false;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomStayLanguage'] = "en";
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['WarningRPH'] = null;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['BasicPropertyInfo']['CurrencyCode'] = null;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['BasicPropertyInfo']['HotelRef']['ChainCode'] = $ChainCode;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['BasicPropertyInfo']['HotelRef']['HotelCode'] = $HotelCode;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['CommentsType']['Comments']['Comment']['Description'] = "This is my room comment";
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['CommentsType']['Comments']['Comment']['Language'] = null;
    // Adults
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][0]['Age'] = null;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][0]['AgeQualifyCode'] = "Adult";
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][0]['Count'] = $adults[$xRooms];
    for ($xY = 0; $xY < $adults[$xRooms]; $xY ++) {
        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][0]['ResGuestRPH'][$xY] = $ResGuestRPH;
        $ResGuestRPH ++;
    }
    if ($children[$xRooms] > 0) {
        for ($zA = 0; $zA < $children[$xRooms]; $zA ++) {
            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][1]['Age'] = $childrenAges[$xRooms][$zA];
            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][1]['AgeQualifyCode'] = "Child";
            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][1]['Count'] = $children[$xRooms];
            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['GuestCountsType']['GuestCounts']['GuestCount'][1]['ResGuestRPH'][0] = $ResGuestRPH;
            $ResGuestRPH ++;
        }
    }
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RPH'] = $xRooms;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['ExtensionData'] = null;
    // $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CurrencyCode'] = "BRL";
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Commission '] = null;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['EffectiveDate'] = null;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['ExpireDate'] = null;
    //
    // Rate Plans
    //
    for ($r = 0; $r < count($RoomRates); $r ++) {
        if ($RoomRates[$r]['RoomID'][$xRooms] == $RoomID[$xRooms]) {
            if ($RoomRates[$r]['RatePlanID'][$xRooms] == $RatePlanID[$xRooms]) {
                if ($RoomRates[$r]['RoomStayCandidateRPH'][$xRooms] == $xRooms) {
                    $valid = true;
                    $ratePlans = $RatePlans[$RatePlanID[$xRooms]];
                    if ($ratePlans["MealsIncluded"]["MealPlanCode"] != "") {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['ExtensionData'] = null;
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['Breakfast'] = $ratePlans["MealsIncluded"]["Breakfast"];
                        $ratePlans["MealsIncluded"]["Dinner"] = ($ratePlans["MealsIncluded"]["Dinner"] === 'true');
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['Dinner'] = $ratePlans["MealsIncluded"]["Dinner"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['ID'] = $ratePlans["MealsIncluded"]["ID"];
                        $ratePlans["MealsIncluded"]["Lunch"] = ($ratePlans["MealsIncluded"]["Lunch"] === 'true');
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['Lunch'] = $ratePlans["MealsIncluded"]["Lunch"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['MealPlanCode'] = $ratePlans["MealsIncluded"]["MealPlanCode"];
                        $ratePlans["MealsIncluded"]["MealPlanIndicator"] = ($ratePlans["MealsIncluded"]["MealPlanIndicator"] === 'true');
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['MealPlanIndicator'] = $ratePlans["MealsIncluded"]["MealPlanIndicator"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['Description'] = $ratePlans["MealsIncluded"]["Description"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['MealsIncluded']['Name'] = $ratePlans["MealsIncluded"]["Name"];
                    }
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['RatePlanDescription']['ExtensionData'] = null;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['RatePlanDescription']['Description'] = $ratePlans["RatePlanDescriptionDescription"];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['RatePlanDescription']['Language'] = $ratePlans["RatePlanDescriptionLanguage"];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['RatePlanID'] = $RatePlanID;
                    if ($ratePlans["RatePlanInclusions"] != "") {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['RatePlanInclusions'] = $ratePlans["RatePlanInclusions"];
                    }
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['RatePlanTypeCode'] = $ratePlans["RatePlanTypeCode"];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['SortOrder'] = $ratePlans["SortOrder"];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['RatePlanName'] = $ratePlans["RatePlanName"];
                    $taxes = $ratePlans["Taxes"];
                    for ($z = 0; $z < count($taxes); $z ++) {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['ExtensionData'] = null;
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['CurrencyCode'] = $taxes[$z]["CurrencyCode"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['Description'] = $taxes[$z]["Description"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['ID'] = $taxes[$z]["ID"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['IsPerNight'] = $taxes[$z]["IsPerNight"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['IsPerPerson'] = $taxes[$z]["IsPerPerson"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['IsPerRoom'] = $taxes[$z]["IsPerRoom"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['IsPerStay'] = $taxes[$z]["IsPerStay"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['IsValuePercentage'] = $taxes[$z]["IsValuePercentage"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['Name'] = $taxes[$z]["Name"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['TaxPolicies']['TaxPolicy'][$z]['Value'] = $taxes[$z]['Value'];
                    }
                    $rates = $RoomRates[$r]["Rates"][$xRooms];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['ExtensionData'] = "";
                    for ($z = 0; $z < count($rates); $z ++) {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['ExtensionData'] = null;
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['EffectiveDate'] = $rates[$z]["EffectiveDate"];
                        if ($rates[$z]['MaxLOS'] != "") {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['MaxLOS'] = $rates[$z]['MaxLOS'];
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['MaxLOS'] = null;
                        }
                        if ($rates[$z]['MinLOS'] != "") {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['MinLOS'] = $rates[$z]['MinLOS'];
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['MinLOS'] = null;
                        }
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['Total']['ExtensionData'] = null;
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['Total']['AmountAfterTax'] = $rates[$z]["Total"]["AmountAfterTax"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['Total']['AmountBeforeTax'] = $rates[$z]["Total"]["AmountBeforeTax"];
                        $rates[$z]["Total"]["AmountIncludingMarkup"] = ($rates[$z]["Total"]["AmountIncludingMarkup"] === 'true');
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['Total']['AmountIncludingMarkup'] = $rates[$z]["Total"]["AmountIncludingMarkup"];
                        $rates[$z]["Total"]["AmountIsPackage"] = ($rates[$z]["Total"]["AmountIsPackage"] === 'true');
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['Total']["AmountIsPackage"] = $rates[$z]["Total"]["AmountIsPackage"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['Total']['ChargeType'] = $rates[$z]["Total"]["ChargeType"];
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatesType']['Rates']['Rate'][$z]['Total']['CurrencyCode'] = $rates[$z]["Total"]["CurrencyCode"];
                    }
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['ExtensionData'] = null;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['EffectiveDate'] = $RoomRates[$r]["EffectiveDate"][$xRooms];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['ExpireDate'] = $RoomRates[$r]["ExpireDate"][$xRooms];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RatePlanID'] = $RoomRates[$r]["RatePlanID"][$xRooms];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RoomID'] = $RoomRates[$r]["RoomID"][$xRooms];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['RoomStayCandidateRPH'] = $RoomRates[$r]["RoomStayCandidateRPH"][$xRooms];
                    if (is_array($services[$xRooms])) {
                        for ($yAz = 0; $yAz < count($services[$xRooms]); $yAz ++) {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['ServiceRPHs']['ServiceRPH'][$yAz]['ExtensionData'] = null;
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['ServiceRPHs']['ServiceRPH'][$yAz]['RPH'] = $services[$xRooms][$yAz]['RPH'];
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['ServiceRPHs']['ServiceRPH'][$yAz]['IsPerRoom'] = false;
                        }
                    }
                    if (is_array($ratePlans['Offers'][0])) {
                        if (isset($ratePlans['Offers'][0]["IncompatibleOfferIndicator"])) {
                            if ($ratePlans['Offers'][0]["IncompatibleOfferIndicator"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['CompatibleOffer']['CompatibleOffer']['IncompatibleOfferIndicator'] = $ratePlans['Offers'][0]["IncompatibleOfferIndicator"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['CompatibleOffer']['CompatibleOffer'] = null;
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['CompatibleOffer']['CompatibleOffer']['IncompatibleOfferIndicator'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['CompatibleOffer']['CompatibleOffer'] = null;
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['CompatibleOffer']['CompatibleOffer']['IncompatibleOfferIndicator'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["OfferDescription"])) {
                            if ($ratePlans['Offers'][0]["OfferDescription"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Description']['Description'] = $ratePlans['Offers'][0]["OfferDescription"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Description']['Description'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Description']['Description'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["OfferDescriptionLanguage"])) {
                            if ($ratePlans['Offers'][0]["OfferDescriptionLanguage"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Description']['Language'] = $ratePlans['Offers'][0]["OfferDescriptionLanguage"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Description']['Language'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Description']['Language'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["AmountBeforeTax"])) {
                            if ($ratePlans['Offers'][0]["AmountBeforeTax"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['AmountBeforeTax'] = $ratePlans['Offers'][0]["AmountBeforeTax"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['AmountBeforeTax'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['AmountBeforeTax'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["ChargeUnitCode"])) {
                            if ($ratePlans['Offers'][0]["ChargeUnitCode"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['ChargeUnitCode'] = $ratePlans['Offers'][0]["ChargeUnitCode"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['ChargeUnitCode'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['ChargeUnitCode'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["DiscountCode"])) {
                            if ($ratePlans['Offers'][0]["DiscountCode"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountCode'] = $ratePlans['Offers'][0]["DiscountCode"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountCode'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountCode'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["DiscountPattern"])) {
                            if ($ratePlans['Offers'][0]["DiscountPattern"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountPattern'] = $ratePlans['Offers'][0]["DiscountPattern"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountPattern'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountPattern'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["DiscountReason"])) {
                            if ($ratePlans['Offers'][0]["DiscountReason"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountReason'] = $ratePlans['Offers'][0]["DiscountReason"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountReason'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['DiscountReason'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["NightsDiscounted"])) {
                            if ($ratePlans['Offers'][0]["NightsDiscounted"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['NightsDiscounted'] = $ratePlans['Offers'][0]["NightsDiscounted"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['NightsDiscounted'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['NightsDiscounted'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["NightsRequired"])) {
                            if ($ratePlans['Offers'][0]["NightsRequired"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['NightsRequired'] = $ratePlans['Offers'][0]["NightsRequired"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['NightsRequired'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['NightsRequired'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["Percent"])) {
                            if ($ratePlans['Offers'][0]["Percent"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['Percent'] = $ratePlans['Offers'][0]["Percent"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['Percent'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['Discount']['Percent'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["OfferCode"])) {
                            if ($ratePlans['Offers'][0]["OfferCode"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferCode'] = $ratePlans['Offers'][0]["OfferCode"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferCode'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferCode'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["RPH"])) {
                            if ($ratePlans['Offers'][0]["RPH"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['RPH'] = $ratePlans['Offers'][0]["RPH"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['RPH'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['RPH'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["DateRestrictionsArray"])) {
                            if ($ratePlans['Offers'][0]["DateRestrictionsArray"] != "") {
                                for ($sDateRestriction = 0; $sDateRestriction < count($ratePlans['Offers'][0]["DateRestrictionsArray"]); $sDateRestriction ++) {
                                    if ($ratePlans['Offers'][0]["DateRestrictionsArray"][$sDateRestriction]['Duration'] != "") {
                                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction']['DateRestriction'][$sDateRestriction]['Duration'] = $ratePlans['Offers'][0]["DateRestrictionsArray"][$sDateRestriction]['Duration'];
                                    } else {
                                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction']['DateRestriction'][$sDateRestriction]['Duration'] = null;
                                    }
                                    if ($ratePlans['Offers'][0]["DateRestrictionsArray"][$sDateRestriction]['Start'] != "") {
                                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction']['DateRestriction'][0]['Start'] = $ratePlans['Offers'][0]["DateRestrictionsArray"][$sDateRestriction]['Start'];
                                    } else {
                                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction']['DateRestriction'][0]['Start'] = null;
                                    }
                                    if ($ratePlans['Offers'][0]["DateRestrictionsArray"][$sDateRestriction]['End'] != "") {
                                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction']['DateRestriction'][0]['End'] = $ratePlans['Offers'][0]["DateRestrictionsArray"][$sDateRestriction]['End'];
                                    } else {
                                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction']['DateRestriction'][0]['End'] = null;
                                    }
                                }
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['DateRestriction'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["LengthsOfStay"])) {
                            if ($ratePlans['Offers'][0]["LengthsOfStay"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['LengthsOfStay'] = $ratePlans['Offers'][0]["LengthsOfStay"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['LengthsOfStay'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['LengthsOfStay'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["MinAdvancedBookingOffset"])) {
                            if ($ratePlans['Offers'][0]["MinAdvancedBookingOffset"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['MaxAdvancedBookingOffset'] = $ratePlans['Offers'][0]["MaxAdvancedBookingOffset"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['MaxAdvancedBookingOffset'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['MaxAdvancedBookingOffset'] = null;
                        }
                        if (isset($ratePlans['Offers'][0]["MinAdvancedBookingOffset"])) {
                            if ($ratePlans['Offers'][0]["MinAdvancedBookingOffset"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['MinAdvancedBookingOffset'] = $ratePlans['Offers'][0]["MinAdvancedBookingOffset"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['MinAdvancedBookingOffset'] = null;
                            }
                        } else {
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers']['Offer'][0]['OfferRules']['OfferRule']['MinAdvancedBookingOffset'] = null;
                        }
                    } else {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Offers'] = null;
                    }
                    $AmountAfterTax = $RoomRates[$r]["Total"][$xRooms]["AmountAfterTax"];
                    $AmountBeforeTax = $RoomRates[$r]["Total"][$xRooms]["AmountBeforeTax"];
                    $AmountIncludingMarkup = $RoomRates[$r]["Total"][$xRooms]["AmountIncludingMarkup"];
                    $AmountIncludingMarkup = ($AmountIncludingMarkup === 'true');
                    $AmountIsPackage = $RoomRates[$r]["Total"][$xRooms]["AmountIsPackage"];
                    $AmountIsPackage = ($AmountIsPackage === 'true');
                    $ChargeType = $RoomRates[$r]["Total"][$xRooms]["ChargeType"];
                    $CurrencyCode = $RoomRates[$r]["Total"][$xRooms]["CurrencyCode"];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['Total']['AmountAfterTax'] = $AmountAfterTax;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['Total']['AmountBeforeTax'] = $AmountBeforeTax;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['Total']['AmountIncludingMarkup'] = $AmountIncludingMarkup;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['Total']['AmountIsPackage'] = $AmountIsPackage;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['Total']['ChargeType'] = $ChargeType;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomRates']['RoomRate'][0]['Total']['CurrencyCode'] = $CurrencyCode;
                }
                // echo $return;
                // var_dump($Rooms[$RoomID[$xRooms]]);
                // echo $return;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['ExtensionData'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['NumberOfUnits'] = "18";
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['RoomDescription']['ExtensionData'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['RoomDescription']['Description'] = $Rooms[$RoomID][$xRooms]["RoomDescription"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['RoomDescription']['Language'] = $Rooms[$RoomID][$xRooms]["RoomLanguage"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['RoomID'] = $RoomID;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['RoomName'] = $Rooms[$RoomID][$xRooms]["RoomName"];
                $policies = $RatePlans[$RatePlanID]['PaymentPolicies'];
                // var_dump($policies);
                for ($z = 0; $z < count($policies); $z ++) {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['PaymentPolicies']['AcceptedPayments']['AcceptedPayment'][$z]['GuaranteeTypeCode'] = $policies[$z]['GuaranteeTypeCode'];
                    if ($policies[$z]['PaymentCard'] != "") {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['PaymentPolicies']['AcceptedPayments']['AcceptedPayment'][$z]['PaymentCard'] = $policies[$z]['PaymentCard'];
                    } else {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['PaymentPolicies']['AcceptedPayments']['AcceptedPayment'][$z]['PaymentCard'] = null;
                    }
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['PaymentPolicies']['AcceptedPayments']['AcceptedPayment'][$z]['RPH'] = $policies[$z]['RPH'];
                    if ($policies[$z]['GuaranteeID'] != "") {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['PaymentPolicies']['AcceptedPayments']['AcceptedPayment'][$z]['GuaranteeID'] = $policies[$z]['GuaranteeID'];
                    } else {
                        $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['PaymentPolicies']['AcceptedPayments']['AcceptedPayment'][$z]['GuaranteeID'] = null;
                    }
                }
                // Guarantees
                $guarantees = $RatePlans[$RatePlanID]['Guarantees2'];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['ExtensionData'] = null;
                if (is_array($guarantees)) {
                    if (isset($guarantees[0])) {
                        if (is_array($guarantees[0])) {
                            // for ($z <= 0; $z < count($guarantees); $z ++) {
                            if ($guarantees[0]["End"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['End'] = $guarantees[0]["End"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['End'] = null;
                            }
                            if ($guarantees[0]["GuaranteeCode"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['GuaranteeCode'] = $guarantees[0]["GuaranteeCode"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['GuaranteeCode'] = null;
                            }
                            if ($guarantees[0]["Duration"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['Duration'] = $guarantees[0]["Duration"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['Duration'] = null;
                            }
                            if ($guarantees[0]["AmountPercent"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['AmountPercent'] = $guarantees[0]["AmountPercent"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['AmountPercent'] = null;
                            }
                            if ($guarantees[0]["DeadLine"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['Deadline'] = $guarantees[0]["DeadLine"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['Deadline'] = null;
                            }
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['GuaranteeDescription']['ExtensionData'] = null;
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['GuaranteeDescription']['Description'] = $guarantees[0]["GuaranteeDescription"];
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['GuaranteeDescription']['Language'] = $guarantees[0]["GuaranteeDescriptionLanguage"];
                            $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['GuaranteeDescription']['Name'] = $guarantees[0]["GuaranteeDescriptionName"];
                            if ($guarantees[0]["Start"] != "") {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['Start'] = $guarantees[0]["Start"];
                            } else {
                                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['Guarantees']['Guarantee']['Start'] = null;
                            }
                            // }
                        }
                    }
                }
                //
                // echo $return;
                // var_dump($RatePlans[$RatePlanID]);
                // echo $return;
                //
                $additionaldetails = $RatePlans[$RatePlanID]["AdditionalDetails"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['AdditionalDetailsType']['ExtensionData'] = null;
                for ($z = 0; $z < count($additionaldetails); $z ++) {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['AdditionalDetailsType']['AdditionalDetails']['AdditionalDetail'][$z]['ExtensionData'] = null;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['AdditionalDetailsType']['AdditionalDetails']['AdditionalDetail'][$z]['DetailDescription']['ExtensionData'] = null;
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['AdditionalDetailsType']['AdditionalDetails']['AdditionalDetail'][$z]['DetailDescription']['Description'] = $additionaldetails[$z]["Description"];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['AdditionalDetailsType']['AdditionalDetails']['AdditionalDetail'][$z]['DetailDescription']['Language'] = $additionaldetails[$z]["Language"];
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['AdditionalDetailsType']['AdditionalDetails']['AdditionalDetail'][$z]['DetailDescription']['Name'] = $additionaldetails[$z]["Name"];
                }
                if ($RatePlans[$RatePlanID]["CurrencyCode"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CurrencyCode'] = $RatePlans[$RatePlanID]["CurrencyCode"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CurrencyCode'] = null;
                }
                $cancelpenalties = $RatePlans[$RatePlanID]["CancelPenalties"];
                // for ($z = 0; $z < count($cancelpenalties); $z ++) {
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['ExtensionData'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['AmountPercent']['ExtensionData'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['AmountPercent']['Amount'] = $cancelpenalties[0]["Amount"];
                if ($cancelpenalties[0]["CurrencyCode"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['AmountPercent']['CurrencyCode'] = $cancelpenalties[0]["CurrencyCode"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['AmountPercent']['CurrencyCode'] = null;
                }
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['AmountPercent']['NmbrOfNights'] = $cancelpenalties[0]["NmbrOfNights"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['AmountPercent']['Percent'] = $cancelpenalties[0]["Percent"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['ExtensionData'] = null;
                if ($cancelpenalties[0]["AbsoluteDeadline"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['AbsoluteDeadline'] = $cancelpenalties[0]["AbsoluteDeadline"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['AbsoluteDeadline'] = null;
                }
                if ($cancelpenalties[0]["OffsetDropTime"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['OffsetDropTime'] = $cancelpenalties[0]["OffsetDropTime"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['OffsetDropTime'] = null;
                }
                if ($cancelpenalties[0]["OffsetUnitMultiplier"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['OffsetUnitMultiplier'] = $cancelpenalties[0]["OffsetUnitMultiplier"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['OffsetUnitMultiplier'] = null;
                }
                if ($cancelpenalties[0]["TimeUnitType"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['TimeUnitType'] = $cancelpenalties[0]["TimeUnitType"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['TimeUnitType'] = null;
                }
                if ($cancelpenalties[0]["Duration"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['Duration'] = $cancelpenalties[0]["Duration"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['DeadLine']['Duration'] = null;
                }
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['NonRefundable'] = $cancelpenalties[0]["NonRefundable"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['PenaltyDescription']['ExtensionData'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['PenaltyDescription']['Description'] = $cancelpenalties[0]["PenaltyDescription"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['PenaltyDescription']['Language'] = $cancelpenalties[0]["PenaltyLanguage"];
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['PenaltyDescription']['Name'] = $cancelpenalties[0]["PenaltyName"];
                if ($cancelpenalties[0]["Start"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['Start'] = $cancelpenalties[0]["Start"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['Start'] = null;
                }
                if ($cancelpenalties[0]["End"] != "") {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['End'] = $cancelpenalties[0]["End"];
                } else {
                    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RatePlans'][0]['CancelPenalties']['CancelPenalty']['End'] = null;
                }
                // }
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][0]['AgeQualifyingCode'] = "Adult";
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][0]['MaxAge'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][0]['MaxOccupancy'] = "2";
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][0]['MinAge'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][0]['MinOccupancy'] = "1";
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][1]['AgeQualifyingCode'] = "Child";
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][1]['MaxAge'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][1]['MaxOccupancy'] = "1";
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][1]['MinAge'] = null;
                $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['RoomTypes']['RoomType'][0]['Occupancies']['Occupancy'][1]['MinOccupancy'] = "1";
            }
        }
    }
    //
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['SpecialRequestsType']['ExtensionData']['SpecialRequests']['SpecialRequest']['ExtensionData'] = null;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['SpecialRequestsType']['SpecialRequests']['SpecialRequest'][0]['Description'] = "This is my room special request";
    //
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['Total']['ExtensionData'] = null;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['Total']['AmountAfterTax'] = $AmountAfterTax;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['Total']['AmountBeforeTax'] = $AmountBeforeTax;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['Total']['AmountIncludingMarkup'] = $AmountIncludingMarkup;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['Total']['AmountIsPackage'] = $AmountIsPackage;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['Total']['ChargeType'] = 'PerStay';
    // $ChargeType;
    $params['ota_HotelResRQ']['HotelReservationsType']['HotelReservations']['HotelReservation']['RoomStaysType']['RoomStays']['RoomStay'][$xRooms]['Total']['CurrencyCode'] = $CurrencyCode;
    //
}
if ($valid == false) {
    echo $return;
    echo "No Room Id / Rate Type combination found";
    echo $return;
    die();
}
try {
    $client->__soapCall('SendHotelRes', array(
        $params
    ));
} catch (Exception $e) {
    echo $e->getMessage();
}
$xmlrequest = $client->__getLastRequest();
echo $return;
echo $return;
$xmlresult = $client->__getLastResponse();
/* echo $xmlrequest;
echo $return;
echo $return;
echo $xmlresult;
echo $return;
echo $return;
echo $return; */
echo "<xmp>";
echo $xmlrequest;
echo "</xmp>";

echo "<xmp>";
echo $xmlresult;
echo "</xmp>";
echo $return;
echo "End";
echo $return;
?>