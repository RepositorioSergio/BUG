<?php
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
error_log("\r\nOTS - Hotel Parallel Search - Parse\r\n", 3, "/srv/www/htdocs/error_log");
if ($response != "") {
    error_log("\r\nResponse - $response\r\n", 3, "/srv/www/htdocs/error_log");
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    $node = $inputDoc->getElementsByTagName("Hotel");
    for ($rAUX = 0; $rAUX < $node->length; $rAUX ++) {
        $Id = $node->item($rAUX)->getElementsByTagName("Id");
        if ($Id->length > 0) {
            $Id = $Id->item(0)->nodeValue;
            $shid = $Id;
        } else {
            $Id = "";
        }
        $sfilter[] = " sid='$Id' ";
        $Name = $node->item($rAUX)->getElementsByTagName('Name');
        if ($Name->length > 0) {
            $Name = $Name->item(0)->nodeValue;
        } else {
            $Name = "";
        }
        $Description = $node->item($rAUX)->getElementsByTagName("Description");
        if ($Description->length > 0) {
            $Description = $Description->item(0)->nodeValue;
        } else {
            $Description = "";
        }
        $CityId = $node->item($rAUX)->getElementsByTagName('CityId');
        if ($CityId->length > 0) {
            $CityId = $CityId->item(0)->nodeValue;
        } else {
            $CityId = "";
        }
        $CityName = $node->item($rAUX)->getElementsByTagName("CityName");
        if ($CityName->length > 0) {
            $CityName = $CityName->item(0)->nodeValue;
        } else {
            $CityName = "";
        }
        $CountryId = $node->item($rAUX)->getElementsByTagName('CountryId');
        if ($CountryId->length > 0) {
            $CountryId = $CountryId->item(0)->nodeValue;
        } else {
            $CountryId = "";
        }
        $CountryName = $node->item($rAUX)->getElementsByTagName("CountryName");
        if ($CountryName->length > 0) {
            $CountryName = $CountryName->item(0)->nodeValue;
        } else {
            $CountryName = "";
        }
        $Street = $node->item($rAUX)->getElementsByTagName('Street');
        if ($Street->length > 0) {
            $Street = $Street->item(0)->nodeValue;
        } else {
            $Street = "";
        }
        $ZipCode = $node->item($rAUX)->getElementsByTagName("ZipCode");
        if ($ZipCode->length > 0) {
            $ZipCode = $ZipCode->item(0)->nodeValue;
        } else {
            $ZipCode = "";
        }
        $CategoryId = $node->item($rAUX)->getElementsByTagName('CategoryId');
        if ($CategoryId->length > 0) {
            $CategoryId = $CategoryId->item(0)->nodeValue;
        } else {
            $CategoryId = "";
        }
        $LocationId = $node->item($rAUX)->getElementsByTagName('LocationId');
        if ($LocationId->length > 0) {
            $LocationId = $LocationId->item(0)->nodeValue;
        } else {
            $LocationId = "";
        }
        $LocationName = $node->item($rAUX)->getElementsByTagName("LocationName");
        if ($LocationName->length > 0) {
            $LocationName = $LocationName->item(0)->nodeValue;
        } else {
            $LocationName = "";
        }
        $Image = $node->item($rAUX)->getElementsByTagName('Image');
        if ($Image->length > 0) {
            $Image = $Image->item(0)->nodeValue;
        } else {
            $Image = "";
        }
        $Path = $node->item($rAUX)->getElementsByTagName("Path");
        if ($Path->length > 0) {
            $Path = $Path->item(0)->nodeValue;
        } else {
            $Path = "";
        }
        $Currency = $node->item($rAUX)->getElementsByTagName('Currency');
        if ($Currency->length > 0) {
            $Currency = $Currency->item(0)->nodeValue;
        } else {
            $Currency = "";
        }
        $Status = $node->item($rAUX)->getElementsByTagName('Status');
        if ($Status->length > 0) {
            $Status = $Status->item(0)->nodeValue;
        } else {
            $Status = "";
        }
        $Latitude = $node->item($rAUX)->getElementsByTagName('Latitude');
        if ($Latitude->length > 0) {
            $Latitude = $Latitude->item(0)->nodeValue;
        } else {
            $Latitude = "";
        }
        $Longitude = $node->item($rAUX)->getElementsByTagName("Longitude");
        if ($Longitude->length > 0) {
            $Longitude = $Longitude->item(0)->nodeValue;
        } else {
            $Longitude = "";
        }
        $AdditionalCharges = $node->item($rAUX)->getElementsByTagName('AdditionalCharges');
        if ($AdditionalCharges->length > 0) {
            $AdditionalCharges = $AdditionalCharges->item(0)->nodeValue;
        } else {
            $AdditionalCharges = "";
        }
        $Order = $node->item($rAUX)->getElementsByTagName("Order");
        if ($Order->length > 0) {
            $Order = $Order->item(0)->nodeValue;
        } else {
            $Order = "";
        }
        //CHAIN
        $Chain = $node->item($rAUX)->getElementsByTagName('Chain');
        if ($Chain->length > 0) {
            $IdChain = $Chain->item(0)->getElementsByTagName('Id');
            if ($IdChain->length > 0) {
                $IdChain = $IdChain->item(0)->nodeValue;
            } else {
                $IdChain = "";
            }
            $NameChain = $Chain->item(0)->getElementsByTagName('Name');
            if ($NameChain->length > 0) {
                $NameChain = $NameChain->item(0)->nodeValue;
            } else {
                $NameChain = "";
            }
            $PathChain = $Chain->item(0)->getElementsByTagName('Path');
            if ($PathChain->length > 0) {
                $PathChain = $PathChain->item(0)->nodeValue;
            } else {
                $PathChain = "";
            }
        } 
        $Destination = $node->item($rAUX)->getElementsByTagName("Destination");
        if ($Destination->length > 0) {
            $IdDestination = $Destination->item(0)->getElementsByTagName('Id');
            if ($IdDestination->length > 0) {
                $IdDestination = $IdDestination->item(0)->nodeValue;
            } else {
                $IdDestination = "";
            }
            $NameDestination = $Destination->item(0)->getElementsByTagName('Name');
            if ($NameDestination->length > 0) {
                $NameDestination = $NameDestination->item(0)->nodeValue;
            } else {
                $NameDestination = "";
            }
            $Image = $Destination->item(0)->getElementsByTagName('Image');
            if ($Image->length > 0) {
                $IdImage = $Image->item(0)->getElementsByTagName('Id');
                if ($IdImage->length > 0) {
                    $IdImage = $IdImage->item(0)->nodeValue;
                } else {
                    $IdImage = "";
                }
                $NameImage = $Image->item(0)->getElementsByTagName('Name');
                if ($NameImage->length > 0) {
                    $NameImage = $NameImage->item(0)->nodeValue;
                } else {
                    $NameImage = "";
                }
                $DomainImage = $Image->item(0)->getElementsByTagName('Domain');
                if ($DomainImage->length > 0) {
                    $DomainImage = $DomainImage->item(0)->nodeValue;
                } else {
                    $DomainImage = "";
                }
                $URLImage = $Image->item(0)->getElementsByTagName('URL');
                if ($URLImage->length > 0) {
                    $URLImage = $URLImage->item(0)->nodeValue;
                } else {
                    $URLImage = "";
                }
                $TypeImage = $Image->item(0)->getElementsByTagName('Type');
                if ($TypeImage->length > 0) {
                    $TypeImage = $TypeImage->item(0)->nodeValue;
                } else {
                    $TypeImage = "";
                }
            }
            $PathDestination = $Destination->item(0)->getElementsByTagName('Path');
            if ($PathDestination->length > 0) {
                $PathDestination = $PathDestination->item(0)->nodeValue;
            } else {
                $PathDestination = "";
            }
        } 

        $Services = $node->item($rAUX)->getElementsByTagName('Services');
        if ($Services->length > 0) {
            $Service = $Services->item(0)->getElementsByTagName('Service');
            if ($Service->length > 0) {
                for ($i = 0; $i < $Service->length; $i ++) {
                    $IdService = $Service->item($i)->getElementsByTagName('Id');
                    if ($IdService->length > 0) {
                        $IdService = $IdService->item(0)->nodeValue;
                    } else {
                        $IdService = "";
                    }
                    $NameService = $Service->item($i)->getElementsByTagName('Name');
                    if ($NameService->length > 0) {
                        $NameService = $NameService->item(0)->nodeValue;
                    } else {
                        $NameService = "";
                    }
                    $DescriptionService = $Service->item($i)->getElementsByTagName('Description');
                    if ($DescriptionService->length > 0) {
                        $DescriptionService = $DescriptionService->item(0)->nodeValue;
                    } else {
                        $DescriptionService = "";
                    }
                    $ExtraChargeService = $Service->item($i)->getElementsByTagName('ExtraCharge');
                    if ($ExtraChargeService->length > 0) {
                        $ExtraChargeService = $ExtraChargeService->item(0)->nodeValue;
                    } else {
                        $ExtraChargeService = "";
                    }
                    $OrderService = $Service->item($i)->getElementsByTagName('Order');
                    if ($OrderService->length > 0) {
                        $OrderService = $OrderService->item(0)->nodeValue;
                    } else {
                        $OrderService = "";
                    }
                }
            }
        }  
        $InterfaceInfo = $node->item($rAUX)->getElementsByTagName('InterfaceInfo');
        if ($InterfaceInfo->length > 0) {
            $IdInterfaceInfo = $InterfaceInfo->item(0)->getElementsByTagName('Id');
            $InterfaceType = $InterfaceInfo->item(0)->getAttribute('InterfaceType');
            if ($IdInterfaceInfo->length > 0) {
                $IdInterfaceInfo = $IdInterfaceInfo->item(0)->nodeValue;
            } else {
                $IdInterfaceInfo = "";
            }
        }
        $IsBindingRate = $node->item($rAUX)->getElementsByTagName("IsBindingRate");
        if ($IsBindingRate->length > 0) {
            $IsBindingRate = $IsBindingRate->item(0)->nodeValue;
        } else {
            $IsBindingRate = "";
        }
        $Rooms = $node->item($rAUX)->getElementsByTagName('Rooms');
        if ($Rooms->length > 0) {
            $Room = $Rooms->item(0)->getElementsByTagName('Room');
            if ($Room->length > 0) {
                for ($Auxk = 0; $Auxk < $Room->length; $Auxk ++) {
                    $IdRoom = $Room->item($Auxk)->getElementsByTagName('Id');
                    if ($IdRoom->length > 0) {
                        $IdRoom = $IdRoom->item(0)->nodeValue;
                    } else {
                        $IdRoom = "";
                    }
                    $NameRoom = $Room->item($Auxk)->getElementsByTagName('Name');
                    if ($NameRoom->length > 0) {
                        $NameRoom = $NameRoom->item(0)->nodeValue;
                    } else {
                        $NameRoom = "";
                    }
                    $MealPlans = $Room->item($Auxk)->getElementsByTagName('MealPlans');
                    if($MealPlans->length > 0){
                        $MealPlan = $MealPlans->item(0)->getElementsByTagName('MealPlan');
                        if ($MealPlan->length > 0) {
                            for ($Auxkk = 0; $Auxkk < $MealPlan->length; $Auxkk ++) {
                                $IdMealPlan = $MealPlan->item($Auxkk)->getElementsByTagName('Id');
                                if ($IdMealPlan->length > 0) {
                                    $IdMealPlan = $IdMealPlan->item(0)->nodeValue;
                                } else {
                                    $IdMealPlan = "";
                                }
                                $NameMealPlan = $MealPlan->item($Auxkk)->getElementsByTagName('Name');
                                if ($NameMealPlan->length > 0) {
                                    $NameMealPlan = $NameMealPlan->item(0)->nodeValue;
                                } else {
                                    $NameMealPlan = "";
                                }
                                $AgencyPublic = $MealPlan->item($Auxkk)->getElementsByTagName('AgencyPublic');
                                if ($AgencyPublic->length > 0) {
                                    $AgencyPublic2 = $MealPlan->item($Auxkk)->getElementsByTagName('AgencyPublic');
                                    if ($AgencyPublic2->length > 0) {
                                        $AgencyPublic2 = $AgencyPublic2->item(0)->nodeValue;
                                    } else {
                                        $AgencyPublic2 = "";
                                    }
                                    $GrossAgencyPublic = $MealPlan->item($Auxkk)->getElementsByTagName('GrossAgencyPublic');
                                    if ($GrossAgencyPublic->length > 0) {
                                        $GrossAgencyPublic = $GrossAgencyPublic->item(0)->nodeValue;
                                    } else {
                                        $GrossAgencyPublic = "";
                                    }
                                } 
                                $Available = $MealPlan->item($Auxkk)->getElementsByTagName('Available');
                                if($Available->length > 0){
                                    $IdAvailable = $MealPlan->item(0)->getElementsByTagName('Id');
                                    if ($IdAvailable->length > 0) {
                                        $IdAvailable = $IdAvailable->item(0)->nodeValue;
                                    } else {
                                        $IdAvailable = "";
                                    }
                                    $Status = $MealPlan->item(0)->getElementsByTagName('Status');
                                    if ($Status->length > 0) {
                                        $Status = $Status->item(0)->nodeValue;
                                    } else {
                                        $Status = "";
                                    }
                                }
                                $AverageGrossNormal = $MealPlan->item($Auxkk)->getElementsByTagName("AverageGrossNormal");
                                if ($AverageGrossNormal->length > 0) {
                                    $AverageGrossNormal = $AverageGrossNormal->item(0)->nodeValue;
                                } else {
                                    $AverageGrossNormal = "";
                                }
                                $AverageGrossTotal = $MealPlan->item($Auxkk)->getElementsByTagName("AverageGrossTotal");
                                if ($AverageGrossNormal->length > 0) {
                                    $AverageGrossTotal = $AverageGrossTotal->item(0)->nodeValue;
                                } else {
                                    $AverageGrossTotal = "";
                                }
                                $AverageNormal = $MealPlan->item($Auxkk)->getElementsByTagName("AverageNormal");
                                if ($AverageNormal->length > 0) {
                                    $AverageNormal = $AverageNormal->item(0)->nodeValue;
                                } else {
                                    $AverageNormal = "";
                                }
                                $AverageTotal = $MealPlan->item($Auxkk)->getElementsByTagName("AverageTotal");
                                if ($AverageTotal->length > 0) {
                                    $AverageTotal = $AverageTotal->item(0)->nodeValue;
                                } else {
                                    $AverageTotal = "";
                                }
                                $DutyAmount = $MealPlan->item($Auxkk)->getElementsByTagName("DutyAmount");
                                if ($DutyAmount->length > 0) {
                                    $DutyAmount = $DutyAmount->item(0)->nodeValue;
                                } else {
                                    $DutyAmount = "";
                                }
                                $GrossNormal = $MealPlan->item($Auxkk)->getElementsByTagName("GrossNormal");
                                if ($GrossNormal->length > 0) {
                                    $GrossNormal = $GrossNormal->item(0)->nodeValue;
                                } else {
                                    $GrossNormal = "";
                                }
                                $GrossTotal = $MealPlan->item($Auxkk)->getElementsByTagName("GrossTotal");
                                if ($GrossTotal->length > 0) {
                                    $GrossTotal = $GrossTotal->item(0)->nodeValue;
                                } else {
                                    $GrossTotal = "";
                                }
                                $Normal = $MealPlan->item($Auxkk)->getElementsByTagName("Normal");
                                if ($Normal->length > 0) {
                                    $Normal = $Normal->item(0)->nodeValue;
                                } else {
                                    $Normal = "";
                                }
                                $RateKey = $MealPlan->item($Auxkk)->getElementsByTagName("RateKey");
                                if ($RateKey->length > 0) {
                                    $RateKey = $RateKey->item(0)->nodeValue;
                                } else {
                                    $RateKey = "";
                                }
                                $RoomsCount = $MealPlan->item($Auxkk)->getElementsByTagName("RoomsCount");
                                if ($RoomsCount->length > 0) {
                                    $RoomsCount = $RoomsCount->item(0)->nodeValue;
                                } else {
                                    $RoomsCount = "";
                                }
                                $Total = $MealPlan->item($Auxkk)->getElementsByTagName("Total");
                                if ($Total->length > 0) {
                                    $Total = $Total->item(0)->nodeValue;
                                } else {
                                    $Total = "";
                                }
                                $MarketId = $MealPlan->item($Auxkk)->getElementsByTagName("MarketId");
                                if ($MarketId->length > 0) {
                                    $MarketId = $MarketId->item(0)->nodeValue;
                                } else {
                                    $MarketId = "";
                                }
                                $Contract = $MealPlan->item($Auxkk)->getElementsByTagName("Contract");
                                if ($Contract->length > 0) {
                                    $Contract = $Contract->item(0)->nodeValue;
                                } else {
                                    $Contract = "";
                                }
                                $Currency = $MealPlan->item($Auxkk)->getElementsByTagName("Currency");
                                if ($Currency->length > 0) {
                                    $Currency = $Currency->item(0)->nodeValue;
                                } else {
                                    $Currency = "";
                                }
                                
                                $NightsDetail = $MealPlan->item($Auxkk)->getElementsByTagName('NightsDetail');
                                if($NightsDetail->length > 0){
                                    $NightlyRate = $NightsDetail->item(0)->getElementsByTagName('NightlyRate');
                                    if($NightlyRate->length > 0){
                                        for ($Auxjj=0; $Auxjj < $NightlyRate->length ; $Auxjj++) { 
                                            $Available = $NightlyRate->item($Auxjj)->getElementsByTagName("Available");
                                            if ($Available->length > 0) {
                                                $Available = $Available->item(0)->nodeValue;
                                            } else {
                                                $Available = "";
                                            }
                                            
                                            $DateN = $NightlyRate->item($Auxjj)->getElementsByTagName("Date");
                                            if ($DateN->length > 0) {
                                                $DateN = $DateN->item(0)->nodeValue;
                                            } else {
                                                $DateN = "";
                                            }
                                            $GrossNormalN = $NightlyRate->item($Auxjj)->getElementsByTagName("GrossNormal");
                                            if ($GrossNormalN->length > 0) {
                                                $GrossNormalN = $GrossNormalN->item(0)->nodeValue;
                                            } else {
                                                $GrossNormalN = "";
                                            }
                                            $GrossTotalN = $NightlyRate->item($Auxjj)->getElementsByTagName("GrossTotal");
                                            if ($GrossTotalN->length > 0) {
                                                $GrossTotalN = $GrossTotalN->item(0)->nodeValue;
                                            } else {
                                                $GrossTotalN = "";
                                            }
                                            $NormalN = $NightlyRate->item($Auxjj)->getElementsByTagName("Normal");
                                            if ($NormalN->length > 0) {
                                                $NormalN = $NormalN->item(0)->nodeValue;
                                            } else {
                                                $NormalN = "";
                                            }
                                            $PromoId = $NightlyRate->item($Auxjj)->getElementsByTagName("PromoId");
                                            if ($PromoId->length > 0) {
                                                $PromoId = $PromoId->item(0)->nodeValue;
                                            } else {
                                                $PromoId = "";
                                            }
                                            $TotalN = $NightlyRate->item($Auxjj)->getElementsByTagName("Total");
                                            if ($TotalN->length > 0) {
                                                $TotalN = $TotalN->item(0)->nodeValue;
                                            } else {
                                                $TotalN = "";
                                            }
                                        }
                                    }
                                }
                                $Promotions = $MealPlan->item($Auxkk)->getElementsByTagName('Promotions');
                                if ($Promotions->length > 0) {
                                    $Promotion = $Promotions->item(0)->getElementsByTagName('Promotion');
                                    if($Promotion->length > 0){
                                        $IdPromotion = $Promotion->item(0)->getElementsByTagName('Id');
                                        if ($IdPromotion->length > 0) {
                                            $IdPromotion = $IdPromotion->item(0)->nodeValue;
                                        } else {
                                            $IdPromotion = "";
                                        }
                                        $PromotionName = $Promotion->item(0)->getElementsByTagName('Name');
                                        if ($PromotionName->length > 0) {
                                            $PromotionName = $PromotionName->item(0)->nodeValue;
                                        } else {
                                            $PromotionName = "";
                                        }
                                        $DescriptionPromotion = $Promotion->item(0)->getElementsByTagName('Description');
                                        if ($DescriptionPromotion->length > 0) {
                                            $DescriptionPromotion = $DescriptionPromotion->item(0)->nodeValue;
                                        } else {
                                            $DescriptionPromotion = "";
                                        }
                                        $RatePromotion = $Promotion->item(0)->getElementsByTagName('Rate');
                                        if ($RatePromotion->length > 0) {
                                            $RatePromotion = $RatePromotion->item(0)->nodeValue;
                                        } else {
                                            $RatePromotion = "";
                                        }
                                        $SavingPromotion = $Promotion->item(0)->getElementsByTagName('Saving');
                                        if ($SavingPromotion->length > 0) {
                                            $SavingPromotion = $SavingPromotion->item(0)->nodeValue;
                                        } else {
                                            $SavingPromotion = "";
                                        }
                                        $PromotionTypeId = $Promotion->item(0)->getElementsByTagName('PromotionTypeId');
                                        if ($PromotionTypeId->length > 0) {
                                            $PromotionTypeId = $PromotionTypeId->item(0)->nodeValue;
                                        } else {
                                            $PromotionTypeId = "";
                                        }
                                    }
                                }
                                $RateDetails = $MealPlan->item($Auxkk)->getElementsByTagName('RateDetails');
                                if($RateDetails->length > 0){
                                    $RateDetail = $RateDetails->item(0)->getElementsByTagName('RateDetail');
                                    if($RateDetail->length > 0){
                                        $IdRateDetail = $RateDetail->item(0)->getElementsByTagName('Id');
                                        if ($IdRateDetail->length > 0) {
                                            $IdRateDetail = $IdRateDetail->item(0)->nodeValue;
                                        } else {
                                            $IdRateDetail = "";
                                        }
                                        $AgencyPublicRateDetail = $RateDetail->item(0)->getElementsByTagName('AgencyPublic');
                                        if($AgencyPublicRateDetail->length > 0){
                                            $AgencyPublicRateDetail2 = $AgencyPublicRateDetail->item(0)->getElementsByTagName('AgencyPublic');
                                            if ($AgencyPublicRateDetail2->length > 0) {
                                                $AgencyPublicRateDetail2 = $AgencyPublicRateDetail2->item(0)->nodeValue;
                                            } else {
                                                $AgencyPublicRateDetail2 = "";
                                            }
                                            $GrossAgencyPublicRateDetail = $AgencyPublicRateDetail->item(0)->getElementsByTagName('GrossAgencyPublic');
                                            if ($GrossAgencyPublicRateDetail->length > 0) {
                                                $GrossAgencyPublicRateDetail = $GrossAgencyPublicRateDetail->item(0)->nodeValue;
                                            } else {
                                                $GrossAgencyPublicRateDetail = "";
                                            }
                                        }
                                        $AvailableRateDetail = $RateDetail->item(0)->getElementsByTagName('Available');
                                        if($AvailableRateDetail->length > 0){
                                            $IdAvailableRD = $AvailableRateDetail->item(0)->getElementsByTagName('Id');
                                            if ($IdAvailableRD->length > 0) {
                                                $IdAvailableRD = $IdAvailableRD->item(0)->nodeValue;
                                            } else {
                                                $IdAvailableRD = "";
                                            }
                                            $StatusAvailableRD = $AvailableRateDetail->item(0)->getElementsByTagName('Status');
                                            if ($StatusAvailableRD->length > 0) {
                                                $StatusAvailableRD = $StatusAvailableRD->item(0)->nodeValue;
                                            } else {
                                                $StatusAvailableRD = "";
                                            }
                                        }
                                        $AverageGrossNormalRD = $RateDetail->item(0)->getElementsByTagName('AverageGrossNormal'); 
                                        if ($AverageGrossNormalRD->length > 0) {
                                            $AverageGrossNormalRD = $AverageGrossNormalRD->item(0)->nodeValue;
                                        } else {
                                            $AverageGrossNormalRD = "";
                                        }
                                        $AverageGrossTotalRD = $RateDetail->item(0)->getElementsByTagName('AverageGrossTotal'); 
                                        if ($AverageGrossTotalRD->length > 0) {
                                            $AverageGrossTotalRD = $AverageGrossTotalRD->item(0)->nodeValue;
                                        } else {
                                            $AverageGrossTotalRD = "";
                                        }
                                        $AverageNormalRD = $RateDetail->item(0)->getElementsByTagName('AverageNormal'); 
                                        if ($AverageNormalRD->length > 0) {
                                            $AverageNormalRD = $AverageNormalRD->item(0)->nodeValue;
                                        } else {
                                            $AverageNormalRD = "";
                                        }
                                        $AverageTotalRD = $RateDetail->item(0)->getElementsByTagName('AverageTotal'); 
                                        if ($AverageTotalRD->length > 0) {
                                            $AverageTotalRD = $AverageTotalRD->item(0)->nodeValue;
                                        } else {
                                            $AverageTotalRD = "";
                                        }
                                        $CancellationPolicy = $RateDetail->item(0)->getElementsByTagName('CancellationPolicy'); 
                                        if ($CancellationPolicy->length > 0) {
                                            $IdCP = $CancellationPolicy->item(0)->getElementsByTagName('Id'); 
                                            if ($IdCP->length > 0) {
                                                $IdCP = $IdCP->item(0)->nodeValue;
                                            } else {
                                                $IdCP = "";
                                            }
                                            $DescriptionCP = $CancellationPolicy->item(0)->getElementsByTagName('Description'); 
                                            if ($DescriptionCP->length > 0) {
                                                $DescriptionCP = $DescriptionCP->item(0)->nodeValue;
                                            } else {
                                                $DescriptionCP = "";
                                            }
                                            $DaysToApplyCancellation = $CancellationPolicy->item(0)->getElementsByTagName('DaysToApplyCancellation'); 
                                            if ($DaysToApplyCancellation->length > 0) {
                                                $DaysToApplyCancellation = $DaysToApplyCancellation->item(0)->nodeValue;
                                            } else {
                                                $DaysToApplyCancellation = "";
                                            }
                                            $AmountCP = $CancellationPolicy->item(0)->getElementsByTagName('Amount'); 
                                            if ($AmountCP->length > 0) {
                                                $AmountCP = $AmountCP->item(0)->nodeValue;
                                            } else {
                                                $AmountCP = "";
                                            }
                                            $PaymentLimitDay = $CancellationPolicy->item(0)->getElementsByTagName('PaymentLimitDay'); 
                                            if ($PaymentLimitDay->length > 0) {
                                                $PaymentLimitDay = $PaymentLimitDay->item(0)->nodeValue;
                                            } else {
                                                $PaymentLimitDay = "";
                                            }
                                            $NightsPenalty = $CancellationPolicy->item(0)->getElementsByTagName('NightsPenalty'); 
                                            if ($NightsPenalty->length > 0) {
                                                $NightsPenalty = $NightsPenalty->item(0)->nodeValue;
                                            } else {
                                                $NightsPenalty = "";
                                            }
                                            
                                            $NoShowCP = $CancellationPolicy->item(0)->getElementsByTagName('NoShow'); 
                                            if ($NoShowCP->length > 0) {
                                                $DateFromCP = $NoShowCP->item(0)->getElementsByTagName('DateFrom');
                                                if ($DateFromCP->length > 0) {
                                                    $DateFromCP = $DateFromCP->item(0)->nodeValue;
                                                } else {
                                                    $DateFromCP = "";
                                                }
                                                $AmountCP = $NoShowCP->item(0)->getElementsByTagName('Amount');
                                                if ($AmountCP->length > 0) {
                                                    $AmountCP = $AmountCP->item(0)->nodeValue;
                                                } else {
                                                    $AmountCP = "";
                                                }
                                            } 
                                            $IsNonRefundable = $CancellationPolicy->item(0)->getElementsByTagName('IsNonRefundable');
                                            if ($IsNonRefundable->length > 0) {
                                                $IsNonRefundable = $IsNonRefundable->item(0)->nodeValue;
                                            } else {
                                                $IsNonRefundable = "";
                                            }
                                            $DesciptionPolicy = $CancellationPolicy->item(0)->getElementsByTagName('DesciptionPolicy');
                                            if ($DesciptionPolicy->length > 0) {
                                                $DesciptionPolicy = $DesciptionPolicy->item(0)->nodeValue;
                                            } else {
                                                $DesciptionPolicy = "";
                                            }
                                            $IdPolicyInterface = $CancellationPolicy->item(0)->getElementsByTagName('IdPolicyInterface');
                                            if ($IdPolicyInterface->length > 0) {
                                                $IdPolicyInterface = $IdPolicyInterface->item(0)->nodeValue;
                                            } else {
                                                $IdPolicyInterface = "";
                                            }
                                        } 
                                        $DutyAmount = $RateDetail->item(0)->getElementsByTagName('DutyAmount');
                                        if ($DutyAmount->length > 0) {
                                            $DutyAmount = $DutyAmount->item(0)->nodeValue;
                                        } else {
                                            $DutyAmount = "";
                                        }
                                        $GrossNormalRD = $RateDetail->item(0)->getElementsByTagName('GrossNormal');
                                        if ($GrossNormalRD->length > 0) {
                                            $GrossNormalRD = $GrossNormalRD->item(0)->nodeValue;
                                        } else {
                                            $GrossNormalRD = "";
                                        }
                                        $GrossTotalRD = $RateDetail->item(0)->getElementsByTagName('GrossTotal');
                                        if ($GrossTotalRD->length > 0) {
                                            $GrossTotalRD = $GrossTotalRD->item(0)->nodeValue;
                                        } else {
                                            $GrossTotalRD = "";
                                        }
                                        $NormalRD = $RateDetail->item(0)->getElementsByTagName('Normal');
                                        if ($NormalRD->length > 0) {
                                            $NormalRD = $NormalRD->item(0)->nodeValue;
                                        } else {
                                            $NormalRD = "";
                                        }
                                        $PaxCountRD = $RateDetail->item(0)->getElementsByTagName('PaxCount');
                                        if ($PaxCountRD->length > 0) {
                                            $PaxCountRD = $PaxCountRD->item(0)->nodeValue;
                                        } else {
                                            $PaxCountRD = "";
                                        }
                                        $RateKeyRD = $RateDetail->item(0)->getElementsByTagName('RateKey');
                                        if ($RateKeyRD->length > 0) {
                                            $RateKeyRD = $RateKeyRD->item(0)->nodeValue;
                                        } else {
                                            $RateKeyRD = "";
                                        }
                                        $RoomsCountRD = $RateDetail->item(0)->getElementsByTagName('RoomsCount');
                                        if ($RoomsCountRD->length > 0) {
                                            $RoomsCountRD = $RoomsCountRD->item(0)->nodeValue;
                                        } else {
                                            $RoomsCountRD = "";
                                        }
                                        $TotalRD = $RateDetail->item(0)->getElementsByTagName('Total');
                                        if ($TotalRD->length > 0) {
                                            $TotalRD = $TotalRD->item(0)->nodeValue;
                                        } else {
                                            $TotalRD = "";
                                        }
                                    }
                                }
                            }
                        }

                    }
                    $CapacityAdults = $Room->item($Auxk)->getElementsByTagName('CapacityAdults');
                    if ($CapacityAdults->length > 0) {
                        $CapacityAdults = $CapacityAdults->item(0)->nodeValue;
                    } else {
                        $CapacityAdults = "";
                    }
                    $CapacityKids = $Room->item($Auxk)->getElementsByTagName('CapacityKids');
                    if ($CapacityKids->length > 0) {
                        $CapacityKids = $CapacityKids->item(0)->nodeValue;
                    } else {
                        $CapacityKids = "";
                    } 
                    $CapacityExtras = $Room->item($Auxk)->getElementsByTagName('CapacityExtras');
                    if ($CapacityExtras->length > 0) {
                        $CapacityExtras = $CapacityExtras->item(0)->nodeValue;
                    } else {
                        $CapacityExtras = "";
                    } 
                    $CapacityTotal = $Room->item($Auxk)->getElementsByTagName('CapacityTotal');
                    if ($CapacityTotal->length > 0) {
                        $CapacityTotal = $CapacityTotal->item(0)->nodeValue;
                    } else {
                        $CapacityTotal = "";
                    } 
                    $CapacityChildAgeFrom = $Room->item($Auxk)->getElementsByTagName('CapacityChildAgeFrom');
                    if ($CapacityChildAgeFrom->length > 0) {
                        $CapacityChildAgeFrom = $CapacityChildAgeFrom->item(0)->nodeValue;
                    } else {
                        $CapacityChildAgeFrom = "";
                    } 
                    $CapacityChildAgeTo = $Room->item($Auxk)->getElementsByTagName('CapacityChildAgeTo');
                    if ($CapacityChildAgeTo->length > 0) {
                        $CapacityChildAgeTo = $CapacityChildAgeTo->item(0)->nodeValue;
                    } else {
                        $CapacityChildAgeTo = "";
                    } 
                    $CapacityJuniorAgeFrom = $Room->item($Auxk)->getElementsByTagName('CapacityJuniorAgeFrom');
                    if ($CapacityJuniorAgeFrom->length > 0) {
                        $CapacityJuniorAgeFrom = $CapacityJuniorAgeFrom->item(0)->nodeValue;
                    } else {
                        $CapacityJuniorAgeFrom = "";
                    } 
                    $CapacityJuniorAgeTo = $Room->item($Auxk)->getElementsByTagName('CapacityJuniorAgeTo');
                    if ($CapacityJuniorAgeTo->length > 0) {
                        $CapacityJuniorAgeTo = $CapacityJuniorAgeTo->item(0)->nodeValue;
                    } else {
                        $CapacityJuniorAgeTo = "";
                    } 
                    $RoomView = $Room->item($Auxk)->getElementsByTagName('RoomView');
                    if ($RoomView->length > 0) {
                        $RoomView = $RoomView->item(0)->nodeValue;
                    } else {
                        $RoomView = "";
                    } 
                    $Bedding = $Room->item($Auxk)->getElementsByTagName('Bedding');
                    if ($Bedding->length > 0) {
                        $Bedding = $Bedding->item(0)->nodeValue;
                    } else {
                        $Bedding = "";
                    } 
                    $ImageURL = $Room->item($Auxk)->getElementsByTagName('ImageURL');
                    if ($ImageURL->length > 0) {
                        $ImageURL = $ImageURL->item(0)->nodeValue;
                    } else {
                        $ImageURL = "";
                    } 
                
                    $rooms[$baseCounterDetails]['name'] = $Name;
                    $rooms[$baseCounterDetails]['hotelid'] = $Id;
                    $rooms[$baseCounterDetails]['roomid'] = $IdRoom;
                    $rooms[$baseCounterDetails]['code'] = $Id;
                    $rooms[$baseCounterDetails]['scode'] = $Id;
                    $rooms[$baseCounterDetails]['shid'] = $Id;
                    $rooms[$baseCounterDetails]['status'] = 1;
                    $rooms[$baseCounterDetails]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-49";
                    $rooms[$baseCounterDetails]['room'] = $RoomView;
                    $rooms[$baseCounterDetails]['room_type'] = $IdRoom;
                    $rooms[$baseCounterDetails]['room_description'] = $Bedding;
                    $rooms[$baseCounterDetails]['nonrefundable'] = $IsNonRefundable;
                    $rooms[$baseCounterDetails]['adults'] = $adults;
                    $rooms[$baseCounterDetails]['children'] = $children;
                    $rooms[$baseCounterDetails]['nettotal'] = (double) $TotalN;
                    if ($HotelDoMarkup != 0) {
                        $TotalN = $TotalN + (($TotalN * $HotelDoMarkup) / 100);
                    }
                    // Geo target markup
                    if ($internalmarkup != 0) {
                        $TotalN = $TotalN + (($TotalN * $internalmarkup) / 100);
                    }
                    // Agent markup
                    if ($agent_markup != 0) {
                        $TotalN = $TotalN + (($TotalN * $agent_markup) / 100);
                    }
                    // Fallback Markup
                    if ($HotelDoMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $TotalN = $TotalN + (($TotalN * $HotelsMarkupFallback) / 100);
                    }
                    // Agent discount
                    if ($agent_discount != 0) {
                        $TotalN = $TotalN - (($TotalN * $agent_discount) / 100);
                    }
                    if ($scurrency != "" and $currency != $scurrency) {
                        $TotalN = $CurrencyConverter->convert($TotalN, $currency, $scurrency);
                    }
                    $TotalN = number_format($TotalN, 2, ".", "");
                    $rooms[$baseCounterDetails]['total'] = (double) $TotalN;
                    $rooms[$baseCounterDetails]['totalplain'] = (double) $TotalN;
                    try {
                        $sql = "select mapped from board_mapping where description='" . addslashes($IdMealPlan) . "'";
                        $statement = $db->createStatement($sql);
                        $statement->prepare();
                        $row_board_mapping = $statement->execute();
                        $row_board_mapping->buffer();
                        if ($row_board_mapping->valid()) {
                            $row_board_mapping = $row_board_mapping->current();
                            $Text = $row_board_mapping["mapped"];
                        }
                    } catch (\Exception $e) {
                        $logger = new Logger();
                        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
                        $logger->addWriter($writer);
                        $logger->info($e->getMessage());
                    }
                    $rooms[$baseCounterDetails]['meal'] = $translator->translate($IdMealPlan);
                    $pricebreakdown = array();
                    $pricebreakdownCount = 0;
                    $amount = $TotalN / $noOfNights;
                    if ($HotelDoMarkup != 0) {
                        $amount = $amount + (($amount * $HotelDoMarkup) / 100);
                    }
                    // Geo target markup
                    if ($internalmarkup != 0) {
                        $amount = $amount + (($amount * $internalmarkup) / 100);
                    }
                    // Agent markup
                    if ($agent_markup != 0) {
                        $amount = $amount + (($amount * $agent_markup) / 100);
                    }
                    // Fallback Markup
                    if ($HotelDoMarkup == 0 and $internalmarkup == 0 and $agent_markup == 0) {
                        $amount = $amount + (($amount * $HotelsMarkupFallback) / 100);
                    }
                    // Agent discount
                    if ($agent_discount != 0) {
                        $amount = $amount - (($amount * $agent_discount) / 100);
                    }
                    if ($scurrency != "" and $currency != $scurrency) {
                        $amount = $CurrencyConverter->convert($amount, $currency, $scurrency);
                    }
                    for ($rZZ = 0; $rZZ < $noOfNights; $rZZ ++) {
                        $pricebreakdown[$pricebreakdownCount]['date'] = strftime("%d %b %Y", mktime(0, 0, 0, date("m", $from), date("d", $from) + $rZZ, date("y", $from)));
                        $pricebreakdown[$pricebreakdownCount]['price'] = number_format($amount, 2, ".", "");
                        $pricebreakdown[$pricebreakdownCount]['priceplain'] = $amount;
                        $pricebreakdownCount = $pricebreakdownCount + 1;
                    }
                    $rooms[$baseCounterDetails]['pricebreakdown'] = $pricebreakdown;
                    $rooms[$baseCounterDetails]['scurrency'] = $scurrency;
                    //
                    // TODO - Specials
                    //
                    if ($PromotionName != "") {
                        $rooms[$baseCounterDetails]['special'] = true;
                        $rooms[$baseCounterDetails]['specialdescription'] = $translator->translate("Special Offer");
                    } else {
                        $rooms[$baseCounterDetails]['special'] = false;
                        $rooms[$baseCounterDetails]['specialdescription'] = "";
                    }
                    //
                    // TODO - Cancellation policies
                    //
                    if ($IsNonRefundable == true) {
                        $rooms[$baseCounterDetails]['cancelpolicy'] = $DescriptionCP . '<br/> This booking is Non-Refundable.';
                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = date('d-m-Y');
                        $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                    } else {
                        $rooms[$baseCounterDetails]['cancelpolicy'] = $DescriptionCP;
                        $rooms[$baseCounterDetails]['cancelpolicy_deadline'] = ($DaysToApplyCancellation + 1) . ' days';
                        $rooms[$baseCounterDetails]['cancelpolicy_deadlinetimestamp'] = 0;
                    }
                    
                    $rooms[$baseCounterDetails]['currency'] = strtoupper($scurrency);
                    $baseCounterDetails ++;
                    // $agoda = true;
                }
            }
        }
    }

    //
    // Store Session
    //
    $srooms[$hid]['details'][0] = $rooms;
    $session_id_tmp = $session_id . "-" . $index;
    $sql = new Sql($db);
    $delete = $sql->delete();
    $delete->from('quote_session_hoteldo');
    $delete->where(array(
        'session_id' => $session_id_tmp
    ));
    $statement = $sql->prepareStatementForSqlObject($delete);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('quote_session_hoteldo');
    $insert->values(array(
        'session_id' => $session_id_tmp,
        'xmlrequest' => (string) $request,
        'xmlresult' => (string) $response,
        'data' => base64_encode(serialize($srooms)),
        'searchsettings' => base64_encode(serialize($requestdata))
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    try {
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
}
?>