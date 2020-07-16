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
echo "COMECOU HOTELS";
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
$affiliate_id_hoteldo = 0;
$branch_filter = "";
$sql = "select value from settings where name='HotelDouser' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDouser = $row_settings['value'];
}
$sql = "select value from settings where name='HotelDoMarkup' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDoMarkup = (double) $row_settings['value'];
} else {
    $HotelDoMarkup = 0;
}
$sql = "select value from settings where name='HotelDoserviceURL ' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDoserviceURL = $row_settings['value'];
}
    
$config = new \Zend\Config\Config(include '../config/autoload/global.getaroom.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];


$raw = '/GetQuoteHotels?a=CLUBHTXML&co=MX&c=US&sd=20210208&ed=20210211&h=&rt=&mp=&r=1&r1a=1&d=2&l=ING&hash=hs:true;hp:true';
echo "<br/>" . $HotelDoserviceURL . $raw ."<br/>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $HotelDoserviceURL . $raw);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_VERBOSE, false);
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.roomer.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$QuoteHotels = $inputDoc->getElementsByTagName("QuoteHotels");
$QuoteId = $Envelope->item(0)->getElementsByTagName("QuoteId");

$Hotels = $Envelope->item(0)->getElementsByTagName("Hotels");
if ($Hotels->length > 0) {
    $Hotel = $Hotels->item(0)->getElementsByTagName("Hotel");
    if ($Hotel->length > 0) {
        for ($i=0; $i < $Hotel->length; $i++) { 
            $Id = $Hotel->item($i)->getElementsByTagName('Id');
            if ($Id->length > 0) {
                $Id = $Id->item(0)->nodeValue;
            } else {
                $Id = "";
            }
            $Name = $Hotel->item($i)->getElementsByTagName('Name');
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
            $Description = $Hotel->item($i)->getElementsByTagName('Description');
            if ($Description->length > 0) {
                $Description = $Description->item(0)->nodeValue;
            } else {
                $Description = "";
            }
            $CityId = $Hotel->item($i)->getElementsByTagName('CityId');
            if ($CityId->length > 0) {
                $CityId = $CityId->item(0)->nodeValue;
            } else {
                $CityId = "";
            }
            $CityName = $Hotel->item($i)->getElementsByTagName('CityName');
            if ($CityName->length > 0) {
                $CityName = $CityName->item(0)->nodeValue;
            } else {
                $CityName = "";
            }
            $CountryId = $Hotel->item($i)->getElementsByTagName('CountryId');
            if ($CountryId->length > 0) {
                $CountryId = $CountryId->item(0)->nodeValue;
            } else {
                $CountryId = "";
            }
            $CountryName = $Hotel->item($i)->getElementsByTagName('CountryName');
            if ($CountryName->length > 0) {
                $CountryName = $CountryName->item(0)->nodeValue;
            } else {
                $CountryName = "";
            }
            $Street = $Hotel->item($i)->getElementsByTagName('Street');
            if ($Street->length > 0) {
                $Street = $Street->item(0)->nodeValue;
            } else {
                $Street = "";
            }
            $ZipCode = $Hotel->item($i)->getElementsByTagName('ZipCode');
            if ($ZipCode->length > 0) {
                $ZipCode = $ZipCode->item(0)->nodeValue;
            } else {
                $ZipCode = "";
            }
            $CategoryId = $Hotel->item($i)->getElementsByTagName('CategoryId');
            if ($CategoryId->length > 0) {
                $CategoryId = $CategoryId->item(0)->nodeValue;
            } else {
                $CategoryId = "";
            }
            $LocationId = $Hotel->item($i)->getElementsByTagName('LocationId');
            if ($LocationId->length > 0) {
                $LocationId = $LocationId->item(0)->nodeValue;
            } else {
                $LocationId = "";
            }
            $LocationName = $Hotel->item($i)->getElementsByTagName('LocationName');
            if ($LocationName->length > 0) {
                $LocationName = $LocationName->item(0)->nodeValue;
            } else {
                $LocationName = "";
            }
            $Image = $Hotel->item($i)->getElementsByTagName('Image');
            if ($Image->length > 0) {
                $Image = $Image->item(0)->nodeValue;
            } else {
                $Image = "";
            }
            $Path = $Hotel->item($i)->getElementsByTagName('Path');
            if ($Path->length > 0) {
                $Path = $Path->item(0)->nodeValue;
            } else {
                $Path = "";
            }
            $Currency = $Hotel->item($i)->getElementsByTagName('Currency');
            if ($Currency->length > 0) {
                $Currency = $Currency->item(0)->nodeValue;
            } else {
                $Currency = "";
            }
            $Status = $Hotel->item($i)->getElementsByTagName('Status');
            if ($Status->length > 0) {
                $Status = $Status->item(0)->nodeValue;
            } else {
                $Status = "";
            }
            $Latitude = $Hotel->item($i)->getElementsByTagName('Latitude');
            if ($Latitude->length > 0) {
                $Latitude = $Latitude->item(0)->nodeValue;
            } else {
                $Latitude = "";
            }
            $Longitude = $Hotel->item($i)->getElementsByTagName('Longitude');
            if ($Longitude->length > 0) {
                $Longitude = $Longitude->item(0)->nodeValue;
            } else {
                $Longitude = "";
            }
            $AdditionalCharges = $Hotel->item($i)->getElementsByTagName('AdditionalCharges');
            if ($AdditionalCharges->length > 0) {
                $AdditionalCharges = $AdditionalCharges->item(0)->nodeValue;
            } else {
                $AdditionalCharges = "";
            }
            $Order = $Hotel->item($i)->getElementsByTagName('Order');
            if ($Order->length > 0) {
                $Order = $Order->item(0)->nodeValue;
            } else {
                $Order = "";
            }
            //Destination
            $Destination = $Hotel->item($i)->getElementsByTagName('Destination');
            if ($Destination->length > 0) {
                $DestinationId = $Destination->item(0)->getElementsByTagName('Id');
                if ($DestinationId->length > 0) {
                    $DestinationId = $DestinationId->item(0)->nodeValue;
                } else {
                    $DestinationId = "";
                }
                $DestinationName = $Destination->item(0)->getElementsByTagName('Name');
                if ($DestinationName->length > 0) {
                    $DestinationName = $DestinationName->item(0)->nodeValue;
                } else {
                    $DestinationName = "";
                }
                $DestinationPath = $Destination->item(0)->getElementsByTagName('Path');
                if ($DestinationPath->length > 0) {
                    $DestinationPath = $DestinationPath->item(0)->nodeValue;
                } else {
                    $DestinationPath = "";
                }
                $DestinationImage = $Destination->item(0)->getElementsByTagName('Image');
                if ($DestinationImage->length > 0) {
                    $ImageId = $DestinationImage->item(0)->getElementsByTagName('Id');
                    if ($ImageId->length > 0) {
                        $ImageId = $ImageId->item(0)->nodeValue;
                    } else {
                        $ImageId = "";
                    }
                    $ImageName = $DestinationImage->item(0)->getElementsByTagName('Name');
                    if ($ImageName->length > 0) {
                        $ImageName = $ImageName->item(0)->nodeValue;
                    } else {
                        $ImageName = "";
                    }
                    $ImageDomain = $DestinationImage->item(0)->getElementsByTagName('Domain');
                    if ($ImageDomain->length > 0) {
                        $ImageDomain = $ImageDomain->item(0)->nodeValue;
                    } else {
                        $ImageDomain = "";
                    }
                    $ImageURL = $DestinationImage->item(0)->getElementsByTagName('URL');
                    if ($ImageURL->length > 0) {
                        $ImageURL = $ImageURL->item(0)->nodeValue;
                    } else {
                        $ImageURL = "";
                    }
                    $ImageType = $DestinationImage->item(0)->getElementsByTagName('Type');
                    if ($ImageType->length > 0) {
                        $ImageType = $ImageType->item(0)->nodeValue;
                    } else {
                        $ImageType = "";
                    }
                }
            }
            //InterfaceInfo
            $InterfaceInfo = $Hotel->item($i)->getElementsByTagName('InterfaceInfo');
            if ($InterfaceInfo->length > 0) {
                $InterfaceInfoId = $InterfaceInfo->item(0)->getElementsByTagName('Id');
                if ($InterfaceInfoId->length > 0) {
                    $InterfaceInfoId = $InterfaceInfoId->item(0)->nodeValue;
                } else {
                    $InterfaceInfoId = "";
                }
            }
            //Services
            $Services = $Hotel->item($i)->getElementsByTagName('Services');
            if ($Services->length > 0) {
                $Service = $Services->item(0)->getElementsByTagName('Service');
                if ($Service->length > 0) {
                    for ($j=0; $j < $Service->length; $j++) { 
                        $Id = $Service->item($j)->getElementsByTagName('Id');
                        if ($Id->length > 0) {
                            $Id = $Id->item(0)->nodeValue;
                        } else {
                            $Id = "";
                        }
                        $Name = $Service->item($j)->getElementsByTagName('Name');
                        if ($Name->length > 0) {
                            $Name = $Name->item(0)->nodeValue;
                        } else {
                            $Name = "";
                        }
                        $Description = $Service->item($j)->getElementsByTagName('Description');
                        if ($Description->length > 0) {
                            $Description = $Description->item(0)->nodeValue;
                        } else {
                            $Description = "";
                        }
                        $ExtraCharge = $Service->item($j)->getElementsByTagName('ExtraCharge');
                        if ($ExtraCharge->length > 0) {
                            $ExtraCharge = $ExtraCharge->item(0)->nodeValue;
                        } else {
                            $ExtraCharge = "";
                        }
                        $Order = $Service->item($j)->getElementsByTagName('Order');
                        if ($Order->length > 0) {
                            $Order = $Order->item(0)->nodeValue;
                        } else {
                            $Order = "";
                        }
                    }
                }
            }
            //Rooms
            $Rooms = $Hotel->item($i)->getElementsByTagName('Rooms');
            if ($Rooms->length > 0) {
                $Room = $Rooms->item(0)->getElementsByTagName('Room');
                if ($Room->length > 0) {
                    for ($k=0; $k < $Room->length; $k++) { 
                        $Id = $Room->item($k)->getElementsByTagName('Id');
                        if ($Id->length > 0) {
                            $Id = $Id->item(0)->nodeValue;
                        } else {
                            $Id = "";
                        }
                        $Name = $Room->item($k)->getElementsByTagName('Name');
                        if ($Name->length > 0) {
                            $Name = $Name->item(0)->nodeValue;
                        } else {
                            $Name = "";
                        }
                        $CapacityAdults = $Room->item($k)->getElementsByTagName('CapacityAdults');
                        if ($CapacityAdults->length > 0) {
                            $CapacityAdults = $CapacityAdults->item(0)->nodeValue;
                        } else {
                            $CapacityAdults = "";
                        }
                        $CapacityKids = $Room->item($k)->getElementsByTagName('CapacityKids');
                        if ($CapacityKids->length > 0) {
                            $CapacityKids = $CapacityKids->item(0)->nodeValue;
                        } else {
                            $CapacityKids = "";
                        }
                        $CapacityExtras = $Room->item($k)->getElementsByTagName('CapacityExtras');
                        if ($CapacityExtras->length > 0) {
                            $CapacityExtras = $CapacityExtras->item(0)->nodeValue;
                        } else {
                            $CapacityExtras = "";
                        }
                        $CapacityTotal = $Room->item($k)->getElementsByTagName('CapacityTotal');
                        if ($CapacityTotal->length > 0) {
                            $CapacityTotal = $CapacityTotal->item(0)->nodeValue;
                        } else {
                            $CapacityTotal = "";
                        }
                        $CapacityChildAgeFrom = $Room->item($k)->getElementsByTagName('CapacityChildAgeFrom');
                        if ($CapacityChildAgeFrom->length > 0) {
                            $CapacityChildAgeFrom = $CapacityChildAgeFrom->item(0)->nodeValue;
                        } else {
                            $CapacityChildAgeFrom = "";
                        }
                        $CapacityChildAgeTo = $Room->item($k)->getElementsByTagName('CapacityChildAgeTo');
                        if ($CapacityChildAgeTo->length > 0) {
                            $CapacityChildAgeTo = $CapacityChildAgeTo->item(0)->nodeValue;
                        } else {
                            $CapacityChildAgeTo = "";
                        }
                        $CapacityJuniorAgeFrom = $Room->item($k)->getElementsByTagName('CapacityJuniorAgeFrom');
                        if ($CapacityJuniorAgeFrom->length > 0) {
                            $CapacityJuniorAgeFrom = $NCapacityJuniorAgeFromme->item(0)->nodeValue;
                        } else {
                            $CapacityJuniorAgeFrom = "";
                        }
                        $CapacityJuniorAgeTo = $Room->item($k)->getElementsByTagName('CapacityJuniorAgeTo');
                        if ($CapacityJuniorAgeTo->length > 0) {
                            $CapacityJuniorAgeTo = $CapacityJuniorAgeTo->item(0)->nodeValue;
                        } else {
                            $CapacityJuniorAgeTo = "";
                        }
                        $RoomView = $Room->item($k)->getElementsByTagName('RoomView');
                        if ($RoomView->length > 0) {
                            $RoomView = $RoomView->item(0)->nodeValue;
                        } else {
                            $RoomView = "";
                        }
                        $Bedding = $Room->item($k)->getElementsByTagName('Bedding');
                        if ($Bedding->length > 0) {
                            $Bedding = $Bedding->item(0)->nodeValue;
                        } else {
                            $Bedding = "";
                        }
                        $ImageURL = $Room->item($k)->getElementsByTagName('ImageURL');
                        if ($ImageURL->length > 0) {
                            $ImageURL = $ImageURL->item(0)->nodeValue;
                        } else {
                            $ImageURL = "";
                        }
                        $MealPlans = $Room->item($k)->getElementsByTagName('MealPlans');
                        if ($MealPlans->length > 0) {
                            $MealPlan = $MealPlans->item(0)->getElementsByTagName('MealPlan');
                            if ($MealPlan->length > 0) {
                                $MealPlanId = $MealPlan->item(0)->getElementsByTagName('Id');
                                if ($MealPlanId->length > 0) {
                                    $MealPlanId = $MealPlanId->item(0)->nodeValue;
                                } else {
                                    $MealPlanId = "";
                                }
                                $MealPlanName = $MealPlan->item(0)->getElementsByTagName('Name');
                                if ($MealPlanName->length > 0) {
                                    $MealPlanName = $MealPlanName->item(0)->nodeValue;
                                } else {
                                    $MealPlanName = "";
                                }
                                $AverageGrossNormal = $MealPlan->item(0)->getElementsByTagName('AverageGrossNormal');
                                if ($AverageGrossNormal->length > 0) {
                                    $AverageGrossNormal = $AverageGrossNormal->item(0)->nodeValue;
                                } else {
                                    $AverageGrossNormal = "";
                                }
                                $AverageGrossTotal = $MealPlan->item(0)->getElementsByTagName('AverageGrossTotal');
                                if ($AverageGrossTotal->length > 0) {
                                    $AverageGrossTotal = $AverageGrossTotal->item(0)->nodeValue;
                                } else {
                                    $AverageGrossTotal = "";
                                }
                                $AverageNormal = $MealPlan->item(0)->getElementsByTagName('AverageNormal');
                                if ($AverageNormal->length > 0) {
                                    $AverageNormal = $AverageNormal->item(0)->nodeValue;
                                } else {
                                    $AverageNormal = "";
                                }
                                $AverageTotal = $MealPlan->item(0)->getElementsByTagName('AverageTotal');
                                if ($AverageTotal->length > 0) {
                                    $AverageTotal = $AverageTotal->item(0)->nodeValue;
                                } else {
                                    $AverageTotal = "";
                                }
                                $DutyAmount = $MealPlan->item(0)->getElementsByTagName('DutyAmount');
                                if ($DutyAmount->length > 0) {
                                    $DutyAmount = $DutyAmount->item(0)->nodeValue;
                                } else {
                                    $DutyAmount = "";
                                }
                                $GrossNormal = $MealPlan->item(0)->getElementsByTagName('GrossNormal');
                                if ($GrossNormal->length > 0) {
                                    $GrossNormal = $GrossNormal->item(0)->nodeValue;
                                } else {
                                    $GrossNormal = "";
                                }
                                $GrossTotal = $MealPlan->item(0)->getElementsByTagName('GrossTotal');
                                if ($GrossTotal->length > 0) {
                                    $GrossTotal = $GrossTotal->item(0)->nodeValue;
                                } else {
                                    $GrossTotal = "";
                                }
                                $Normal = $MealPlan->item(0)->getElementsByTagName('Normal');
                                if ($Normal->length > 0) {
                                    $Normal = $Normal->item(0)->nodeValue;
                                } else {
                                    $Normal = "";
                                }
                                $RateKey = $MealPlan->item(0)->getElementsByTagName('RateKey');
                                if ($RateKey->length > 0) {
                                    $RateKey = $RateKey->item(0)->nodeValue;
                                } else {
                                    $RateKey = "";
                                }
                                $RoomsCount = $MealPlan->item(0)->getElementsByTagName('RoomsCount');
                                if ($RoomsCount->length > 0) {
                                    $RoomsCount = $RoomsCount->item(0)->nodeValue;
                                } else {
                                    $RoomsCount = "";
                                }
                                $Total = $MealPlan->item(0)->getElementsByTagName('Total');
                                if ($Total->length > 0) {
                                    $Total = $Total->item(0)->nodeValue;
                                } else {
                                    $Total = "";
                                }
                                $MarketId = $MealPlan->item(0)->getElementsByTagName('MarketId');
                                if ($MarketId->length > 0) {
                                    $MarketId = $MarketId->item(0)->nodeValue;
                                } else {
                                    $MarketId = "";
                                }
                                $Contract = $MealPlan->item(0)->getElementsByTagName('Contract');
                                if ($Contract->length > 0) {
                                    $Contract = $Contract->item(0)->nodeValue;
                                } else {
                                    $Contract = "";
                                }
                                $Currency = $MealPlan->item(0)->getElementsByTagName('Currency');
                                if ($Currency->length > 0) {
                                    $Currency = $Currency->item(0)->nodeValue;
                                } else {
                                    $Currency = "";
                                }
                                //AgencyPublic
                                $AgencyPublic = $MealPlan->item(0)->getElementsByTagName('AgencyPublic');
                                if ($AgencyPublic->length > 0) {
                                    $AgencyPublic2 = $AgencyPublic->item(0)->getElementsByTagName('AgencyPublic');
                                    if ($AgencyPublic2->length > 0) {
                                        $AgencyPublic2 = $AgencyPublic2->item(0)->nodeValue;
                                    } else {
                                        $AgencyPublic2 = "";
                                    }
                                    $GrossAgencyPublic = $AgencyPublic->item(0)->getElementsByTagName('GrossAgencyPublic');
                                    if ($GrossAgencyPublic->length > 0) {
                                        $GrossAgencyPublic = $GrossAgencyPublic->item(0)->nodeValue;
                                    } else {
                                        $GrossAgencyPublic = "";
                                    }
                                }
                                //Available
                                $Available = $MealPlan->item(0)->getElementsByTagName('Available');
                                if ($Available->length > 0) {
                                    $AvailableId = $Available->item(0)->getElementsByTagName('Id');
                                    if ($AvailableId->length > 0) {
                                        $AvailableId = $AvailableId->item(0)->nodeValue;
                                    } else {
                                        $AvailableId = "";
                                    }
                                    $Status = $Available->item(0)->getElementsByTagName('Status');
                                    if ($Status->length > 0) {
                                        $Status = $Status->item(0)->nodeValue;
                                    } else {
                                        $Status = "";
                                    }
                                }
                                //NightsDetail
                                $NightsDetail = $MealPlan->item(0)->getElementsByTagName('NightsDetail');
                                if ($NightsDetail->length > 0) {
                                    $NightlyRate = $NightsDetail->item(0)->getElementsByTagName('NightlyRate');
                                    if ($NightlyRate->length > 0) {
                                        for ($x=0; $x < $NightlyRate->length; $x++) { 
                                            $Available = $NightlyRate->item($x)->getElementsByTagName('Available');
                                            if ($Available->length > 0) {
                                                $Available = $Available->item(0)->nodeValue;
                                            } else {
                                                $Available = "";
                                            }
                                            $Date = $NightlyRate->item($x)->getElementsByTagName('Date');
                                            if ($Date->length > 0) {
                                                $Date = $Date->item(0)->nodeValue;
                                            } else {
                                                $Date = "";
                                            }
                                            $GrossNormal = $NightlyRate->item($x)->getElementsByTagName('GrossNormal');
                                            if ($GrossNormal->length > 0) {
                                                $GrossNormal = $GrossNormal->item(0)->nodeValue;
                                            } else {
                                                $GrossNormal = "";
                                            }
                                            $GrossTotal = $NightlyRate->item($x)->getElementsByTagName('GrossTotal');
                                            if ($GrossTotal->length > 0) {
                                                $GrossTotal = $GrossTotal->item(0)->nodeValue;
                                            } else {
                                                $GrossTotal = "";
                                            }
                                            $Normal = $NightlyRate->item($x)->getElementsByTagName('Normal');
                                            if ($Normal->length > 0) {
                                                $Normal = $Normal->item(0)->nodeValue;
                                            } else {
                                                $Normal = "";
                                            }
                                            $PromoId = $NightlyRate->item($x)->getElementsByTagName('PromoId');
                                            if ($PromoId->length > 0) {
                                                $PromoId = $PromoId->item(0)->nodeValue;
                                            } else {
                                                $PromoId = "";
                                            }
                                            $Total = $NightlyRate->item($x)->getElementsByTagName('Total');
                                            if ($Total->length > 0) {
                                                $Total = $Total->item(0)->nodeValue;
                                            } else {
                                                $Total = "";
                                            }
                                        }
                                    }
                                }
                                //RateDetails
                                $RateDetails = $MealPlan->item(0)->getElementsByTagName('RateDetails');
                                if ($RateDetails->length > 0) {
                                    $RateDetail = $RateDetails->item(0)->getElementsByTagName('RateDetail');
                                    if ($RateDetail->length > 0) {
                                        $RateDetailId = $RateDetail->item(0)->getElementsByTagName('Id');
                                        if ($RateDetailId->length > 0) {
                                            $RateDetailId = $RateDetailId->item(0)->nodeValue;
                                        } else {
                                            $RateDetailId = "";
                                        }
                                        $AverageGrossNormal = $RateDetail->item(0)->getElementsByTagName('AverageGrossNormal');
                                        if ($AverageGrossNormal->length > 0) {
                                            $AverageGrossNormal = $AverageGrossNormal->item(0)->nodeValue;
                                        } else {
                                            $AverageGrossNormal = "";
                                        }
                                        $AverageGrossTotal = $RateDetail->item(0)->getElementsByTagName('AverageGrossTotal');
                                        if ($AverageGrossTotal->length > 0) {
                                            $AverageGrossTotal = $AverageGrossTotal->item(0)->nodeValue;
                                        } else {
                                            $AverageGrossTotal = "";
                                        }
                                        $AverageNormal = $RateDetail->item(0)->getElementsByTagName('AverageNormal');
                                        if ($AverageNormal->length > 0) {
                                            $AverageNormal = $AverageNormal->item(0)->nodeValue;
                                        } else {
                                            $AverageNormal = "";
                                        }
                                        $AverageTotal = $RateDetail->item(0)->getElementsByTagName('AverageTotal');
                                        if ($AverageTotal->length > 0) {
                                            $AverageTotal = $AverageTotal->item(0)->nodeValue;
                                        } else {
                                            $AverageTotal = "";
                                        }
                                        $DutyAmount = $RateDetail->item(0)->getElementsByTagName('DutyAmount');
                                        if ($DutyAmount->length > 0) {
                                            $DutyAmount = $DutyAmount->item(0)->nodeValue;
                                        } else {
                                            $DutyAmount = "";
                                        }
                                        $GrossNormal = $RateDetail->item(0)->getElementsByTagName('GrossNormal');
                                        if ($GrossNormal->length > 0) {
                                            $GrossNormal = $GrossNormal->item(0)->nodeValue;
                                        } else {
                                            $GrossNormal = "";
                                        }
                                        $GrossTotal = $RateDetail->item(0)->getElementsByTagName('GrossTotal');
                                        if ($GrossTotal->length > 0) {
                                            $GrossTotal = $GrossTotal->item(0)->nodeValue;
                                        } else {
                                            $GrossTotal = "";
                                        }
                                        $Normal = $RateDetail->item(0)->getElementsByTagName('Normal');
                                        if ($Normal->length > 0) {
                                            $Normal = $Normal->item(0)->nodeValue;
                                        } else {
                                            $Normal = "";
                                        }
                                        $PaxCount = $RateDetail->item(0)->getElementsByTagName('PaxCount');
                                        if ($PaxCount->length > 0) {
                                            $PaxCount = $PaxCount->item(0)->nodeValue;
                                        } else {
                                            $PaxCount = "";
                                        }
                                        $RateKey = $RateDetail->item(0)->getElementsByTagName('RateKey');
                                        if ($RateKey->length > 0) {
                                            $RateKey = $RateKey->item(0)->nodeValue;
                                        } else {
                                            $RateKey = "";
                                        }
                                        $RoomsCount = $RateDetail->item(0)->getElementsByTagName('RoomsCount');
                                        if ($RoomsCount->length > 0) {
                                            $RoomsCount = $RoomsCount->item(0)->nodeValue;
                                        } else {
                                            $RoomsCount = "";
                                        }
                                        $Total = $RateDetail->item(0)->getElementsByTagName('Total');
                                        if ($Total->length > 0) {
                                            $Total = $Total->item(0)->nodeValue;
                                        } else {
                                            $Total = "";
                                        }
                                        //AgencyPublic
                                        $AgencyPublic = $RateDetail->item(0)->getElementsByTagName('AgencyPublic');
                                        if ($AgencyPublic->length > 0) {
                                            $AgencyPublic3 = $AgencyPublic->item(0)->getElementsByTagName('AgencyPublic');
                                            if ($AgencyPublic3->length > 0) {
                                                $AgencyPublic3 = $AgencyPublic3->item(0)->nodeValue;
                                            } else {
                                                $AgencyPublic3 = "";
                                            }
                                            $GrossAgencyPublic3 = $AgencyPublic->item(0)->getElementsByTagName('GrossAgencyPublic');
                                            if ($GrossAgencyPublic3->length > 0) {
                                                $GrossAgencyPublic3 = $GrossAgencyPublic3->item(0)->nodeValue;
                                            } else {
                                                $GrossAgencyPublic3 = "";
                                            }
                                        }
                                        //Available
                                        $Available = $RateDetail->item(0)->getElementsByTagName('Available');
                                        if ($RateDetail->length > 0) {
                                            $AvailableId = $RateDetail->item(0)->getElementsByTagName('Id');
                                            if ($AvailableId->length > 0) {
                                                $AvailableId = $AvailableId->item(0)->nodeValue;
                                            } else {
                                                $AvailableId = "";
                                            }
                                            $Status = $RateDetail->item(0)->getElementsByTagName('Status');
                                            if ($Status->length > 0) {
                                                $Status = $Status->item(0)->nodeValue;
                                            } else {
                                                $Status = "";
                                            }
                                        }
                                        //CancellationPolicy
                                        $CancellationPolicy = $RateDetail->item(0)->getElementsByTagName('CancellationPolicy');
                                        if ($CancellationPolicy->length > 0) {
                                            $CPId = $CancellationPolicy->item(0)->getElementsByTagName('Id');
                                            if ($CPId->length > 0) {
                                                $CPId = $CPId->item(0)->nodeValue;
                                            } else {
                                                $CPId = "";
                                            }
                                            $CPDescription = $CancellationPolicy->item(0)->getElementsByTagName('Description');
                                            if ($CPDescription->length > 0) {
                                                $CPDescription = $CPDescription->item(0)->nodeValue;
                                            } else {
                                                $CPDescription = "";
                                            }
                                            $CPAmount = $CancellationPolicy->item(0)->getElementsByTagName('Amount');
                                            if ($CPAmount->length > 0) {
                                                $CPAmount = $CPAmount->item(0)->nodeValue;
                                            } else {
                                                $CPAmount = "";
                                            }
                                            $DaysToApplyCancellation = $CancellationPolicy->item(0)->getElementsByTagName('DaysToApplyCancellation');
                                            if ($DaysToApplyCancellation->length > 0) {
                                                $DaysToApplyCancellation = $DaysToApplyCancellation->item(0)->nodeValue;
                                            } else {
                                                $DaysToApplyCancellation = "";
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
                                            $IsNonRefundable = $CancellationPolicy->item(0)->getElementsByTagName('IsNonRefundable');
                                            if ($CPDescription->length > 0) {
                                                $IsNonRefundable = $IsNonRefundable->item(0)->nodeValue;
                                            } else {
                                                $IsNonRefundable = "";
                                            }
                                            $IdPolicyInterface = $CancellationPolicy->item(0)->getElementsByTagName('IdPolicyInterface');
                                            if ($IdPolicyInterface->length > 0) {
                                                $IdPolicyInterface = $IdPolicyInterface->item(0)->nodeValue;
                                            } else {
                                                $IdPolicyInterface = "";
                                            }
                                            $NoShow = $CancellationPolicy->item(0)->getElementsByTagName('NoShow');
                                            if ($NoShow->length > 0) {
                                                $DateFrom = $NoShow->item(0)->getElementsByTagName('DateFrom');
                                                if ($DateFrom->length > 0) {
                                                    $DateFrom = $DateFrom->item(0)->nodeValue;
                                                } else {
                                                    $DateFrom = "";
                                                }
                                                $Amount = $NoShow->item(0)->getElementsByTagName('Amount');
                                                if ($Amount->length > 0) {
                                                    $Amount = $Amount->item(0)->nodeValue;
                                                } else {
                                                    $Amount = "";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
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