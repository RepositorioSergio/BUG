<?php
error_log("\r\n COMECOU POLICIES \r\n", 3, "/srv/www/htdocs/error_log");
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\I18n\Translator\Translator;
use Zend\Http\Client;
use Zend\Http\Request;
$translator = new Translator();
$valid = 0;
$hid = 0;
$shid = 0;
$total = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
try {
    $sql = "select data, searchsettings, xmlrequest, xmlresult from quote_session_hoteldo where session_id='$session_id'";
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
    $lang = $searchsettings['lang'];
    $currency = $searchsettings['currency'];
    $from = $searchsettings['from'];
    $to = $searchsettings['to'];
    $destination = $searchsettings['destination'];
    $affiliate_id = $searchsettings['affiliate_id'];
    $agent_id = $searchsettings['agent_id'];
    $index = $searchsettings['index'];
    $ipaddress = $searchsettings['ipaddress'];
    $nationality = $searchsettings['nationality'];
    $residency = $searchsettings['residency'];
    $room_type = $searchsettings['room'];
    $adt = $searchsettings['adults'];
    $chd = $searchsettings['children'];
    $children_ages = $searchsettings['children_ages'];
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
$sql = "select value from settings where name='enablehoteldo' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_hoteldo = $affiliate_id;
} else {
    $affiliate_id_hoteldo = 0;
}
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
$sql = "select value from settings where name='HotelDocurrency' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDocurrency = strtoupper($row_settings['value']);
}
$sql = "select value from settings where name='HotelDoTimeout' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDoTimeout = (int) $row_settings['value'];
}
$sql = "select value from settings where name='HotelDocurrency' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDocurrency = strtoupper($row_settings['value']);
}
$sql = "select value from settings where name='HotelDocountrycode' and affiliate_id=$affiliate_id_hoteldo";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $HotelDocountrycode = strtoupper($row_settings['value']);
}
if ((int) $nationality > 0) {
    $sql = "select iso_code_2 from countries where id=" . (int) $nationality;
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
    if ($row_settings->valid()) {
        $row_settings = $row_settings->current();
        $sourceMarket = $row_settings["iso_code_2"];
    } else {
        $sourceMarket = "";
    }
} else {
    $sql = "select value from settings where name='HotelDoDefaultNationalityCountryCode' and affiliate_id=$affiliate_id_hoteldo";
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
$fromHotelsPRO = $fromHotelsPRO->getTimestamp();
$toHotelsPro = $toHotelsPro->getTimestamp();
$c = 0;
$response = array();
$roombreakdown = array();
foreach ($breakdown as $k => $v) {
    foreach ($v as $key => $value) {
        if ($shid == 0) {
            $shid = $value['shid'];
            $code = $value['code'];
            $scode = $value['scode'];
            $HotelId = $value['shid'];
            $room_code = $value['roomid'];
        } else {
            if ($shid != $value['shid']) {
                // We can't book two rooms from two suppliers
                $response['error'] = "Unable to handle request #4";
                return false;
            }
        }
        $from_date = date('Ymd', strtotime($from));
        $to_date = date('Ymd', strtotime($to));
        $item = array();
        $ratekey = $value['ratekey'];
        $mealplan = $value['mealplan'];
        $marketid = $value['marketid'];
        $contractid = $value['contractid'];
        $IdInterfaceInfo = $value['IdInterfaceInfo'];
        $adults = $value['adults'];
        $children = $value['children'];

        $ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP']) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if ($_SERVER['HTTP_X_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if ($_SERVER['HTTP_FORWARDED_FOR']) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if ($_SERVER['HTTP_FORWARDED']) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if ($_SERVER['REMOTE_ADDR']) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
            $ipaddress = "142.44.216.144";
        }

        if ($lang == "es") {
            $l = "esp";
        } elseif ($lang = "pt") {
            $l = "por";
        } else {
            $l = "ing";
        }
        //
        // EOF Policies
        //
        // EOF Check prices & availability
        //
        $raw = '/GetHotelRateRules?a=' . $HotelDouser . '&ip=' . $ipaddress . '&co=' . $sourceMarket . '&c=' . $HotelDocurrency . '&d=&l=' . $l . '&rk=' . $ratekey . '&sd=' . $from_date . '&ed=' . $to_date . '&h=' . $HotelId . '&ci=' . $contractid . '&mi=' . $marketid . '&it=' . $IdInterfaceInfo . '&ri=' . $room_code . '&mp=' . $mealplan . '&r1a=' . $adults . '&r1k=' . $children . '';
        if ($children > 0) {
            $children_ages = explode(",", $children_ages);
            for ($w = 0; $w < $children; $w ++) {
                $raw .= '&r1k' . ($w + 1) . 'a=' . $children_ages[$w] . '';
            }
        }
        $url = $HotelDoserviceURL . $raw;
        error_log("\r\n URL $url \r\n", 3, "/srv/www/htdocs/error_log");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $HotelDoserviceURL . $raw);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response2 = curl_exec($ch);
        curl_close($ch);
        error_log("\r\n RESPONSE $response2 \r\n", 3, "/srv/www/htdocs/error_log");

        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response2);
        $HotelRateRules = $inputDoc->getElementsByTagName("HotelRateRules");
        if ($HotelRateRules->length > 0) {
            $HotelRate = $HotelRateRules->item(0)->getElementsByTagName("HotelRate");
            if ($HotelRate->length > 0) {
                $Id = $HotelRate->item(0)->getElementsByTagName('Id');
                if ($Id->length > 0) {
                    $Id = $Id->item(0)->nodeValue;
                } else {
                    $Id = "";
                }
                $Name = $HotelRate->item(0)->getElementsByTagName('Name');
                if ($Name->length > 0) {
                    $Name = $Name->item(0)->nodeValue;
                } else {
                    $Name = "";
                }
                $Description = $HotelRate->item(0)->getElementsByTagName('Description');
                if ($Description->length > 0) {
                    $Description = $Description->item(0)->nodeValue;
                } else {
                    $Description = "";
                }
                $HotelPath = $HotelRate->item(0)->getElementsByTagName('HotelPath');
                if ($HotelPath->length > 0) {
                    $HotelPath = $HotelPath->item(0)->nodeValue;
                } else {
                    $HotelPath = "";
                }
                $CheckIn = $HotelRate->item(0)->getElementsByTagName('CheckIn');
                if ($CheckIn->length > 0) {
                    $CheckIn = $CheckIn->item(0)->nodeValue;
                } else {
                    $CheckIn = "";
                }
                $CheckOut = $HotelRate->item(0)->getElementsByTagName('CheckOut');
                if ($CheckOut->length > 0) {
                    $CheckOut = $CheckOut->item(0)->nodeValue;
                } else {
                    $CheckOut = "";
                }
                $TotalRooms = $HotelRate->item(0)->getElementsByTagName('TotalRooms');
                if ($TotalRooms->length > 0) {
                    $TotalRooms = $TotalRooms->item(0)->nodeValue;
                } else {
                    $TotalRooms = "";
                }
                $Image = $HotelRate->item(0)->getElementsByTagName('Image');
                if ($Image->length > 0) {
                    $Image = $Image->item(0)->nodeValue;
                } else {
                    $Image = "";
                }
                $AdultOnly = $HotelRate->item(0)->getElementsByTagName('AdultOnly');
                if ($AdultOnly->length > 0) {
                    $AdultOnly = $AdultOnly->item(0)->nodeValue;
                } else {
                    $AdultOnly = "";
                }
                $AdditionalCharges = $HotelRate->item(0)->getElementsByTagName('AdditionalCharges');
                if ($AdditionalCharges->length > 0) {
                    $AdditionalCharges = $AdditionalCharges->item(0)->nodeValue;
                } else {
                    $AdditionalCharges = "";
                }
                $Category = $HotelRate->item(0)->getElementsByTagName('Category');
                if ($Category->length > 0) {
                    $Category = $Category->item(0)->nodeValue;
                } else {
                    $Category = "";
                }
                $DestinationId = $HotelRate->item(0)->getElementsByTagName('DestinationId');
                if ($DestinationId->length > 0) {
                    $DestinationId = $DestinationId->item(0)->nodeValue;
                } else {
                    $DestinationId = "";
                }
                //Themes
                $Themes = $HotelRate->item(0)->getElementsByTagName('Themes');
                if ($Themes->length > 0) {
                    $Theme = $Themes->item(0)->getElementsByTagName('Theme');
                    if ($Theme->length > 0) {
                        $ThemeId = $Theme->item(0)->getElementsByTagName('Id');
                        if ($ThemeId->length > 0) {
                            $ThemeId = $ThemeId->item(0)->nodeValue;
                        } else {
                            $ThemeId = "";
                        }
                    }
                }
                //AirportReference
                $AirportReference = $HotelRate->item(0)->getElementsByTagName('AirportReference');
                if ($AirportReference->length > 0) {
                    $DistanceTo = $AirportReference->item(0)->getElementsByTagName('DistanceTo');
                    if ($DistanceTo->length > 0) {
                        $DistanceTo = $DistanceTo->item(0)->nodeValue;
                    } else {
                        $DistanceTo = "";
                    }
                    $MinutesTo = $AirportReference->item(0)->getElementsByTagName('MinutesTo');
                    if ($MinutesTo->length > 0) {
                        $MinutesTo = $MinutesTo->item(0)->nodeValue;
                    } else {
                        $MinutesTo = "";
                    }
                }
                //Address
                $Address = $HotelRate->item(0)->getElementsByTagName('Address');
                if ($Address->length > 0) {
                    $Neighborhood = $Address->item(0)->getElementsByTagName('Neighborhood');
                    if ($Neighborhood->length > 0) {
                        $Neighborhood = $Neighborhood->item(0)->nodeValue;
                    } else {
                        $Neighborhood = "";
                    }
                    $Street = $Address->item(0)->getElementsByTagName('Street');
                    if ($Street->length > 0) {
                        $Street = $Street->item(0)->nodeValue;
                    } else {
                        $Street = "";
                    }
                    $ZipCode = $Address->item(0)->getElementsByTagName('ZipCode');
                    if ($ZipCode->length > 0) {
                        $ZipCode = $ZipCode->item(0)->nodeValue;
                    } else {
                        $ZipCode = "";
                    }
                    //City
                    $City = $Address->item(0)->getElementsByTagName('City');
                    if ($City->length > 0) {
                        $CityId = $City->item(0)->getElementsByTagName('Id');
                        if ($CityId->length > 0) {
                            $CityId = $CityId->item(0)->nodeValue;
                        } else {
                            $CityId = "";
                        }
                        $CityName = $City->item(0)->getElementsByTagName('Name');
                        if ($CityName->length > 0) {
                            $CityName = $CityName->item(0)->nodeValue;
                        } else {
                            $CityName = "";
                        }
                    }
                    //Country
                    $Country = $Address->item(0)->getElementsByTagName('Country');
                    if ($Country->length > 0) {
                        $CountryId = $Country->item(0)->getElementsByTagName('Id');
                        if ($CountryId->length > 0) {
                            $CountryId = $CountryId->item(0)->nodeValue;
                        } else {
                            $CountryId = "";
                        }
                        $CountryName = $Country->item(0)->getElementsByTagName('Name');
                        if ($CountryName->length > 0) {
                            $CountryName = $CountryName->item(0)->nodeValue;
                        } else {
                            $CountryName = "";
                        }
                    }
                    //GeoLocation
                    $GeoLocation = $Address->item(0)->getElementsByTagName('GeoLocation');
                    if ($GeoLocation->length > 0) {
                        $Distance = $GeoLocation->item(0)->getElementsByTagName('Distance');
                        if ($Distance->length > 0) {
                            $Distance = $Distance->item(0)->nodeValue;
                        } else {
                            $Distance = "";
                        }
                        $DistanceUnit = $GeoLocation->item(0)->getElementsByTagName('DistanceUnit');
                        if ($DistanceUnit->length > 0) {
                            $DistanceUnit = $DistanceUnit->item(0)->nodeValue;
                        } else {
                            $DistanceUnit = "";
                        }
                        $Latitude = $GeoLocation->item(0)->getElementsByTagName('Latitude');
                        if ($Latitude->length > 0) {
                            $Latitude = $Latitude->item(0)->nodeValue;
                        } else {
                            $Latitude = "";
                        }
                        $Longitude = $GeoLocation->item(0)->getElementsByTagName('Longitude');
                        if ($Longitude->length > 0) {
                            $Longitude = $Longitude->item(0)->nodeValue;
                        } else {
                            $Longitude = "";
                        }
                    }
                    //State
                    $State = $Address->item(0)->getElementsByTagName('State');
                    if ($State->length > 0) {
                        $StateId = $State->item(0)->getElementsByTagName('Id');
                        if ($StateId->length > 0) {
                            $StateId = $StateId->item(0)->nodeValue;
                        } else {
                            $StateId = "";
                        }
                        $StateName = $State->item(0)->getElementsByTagName('Name');
                        if ($StateName->length > 0) {
                            $StateName = $StateName->item(0)->nodeValue;
                        } else {
                            $StateName = "";
                        }
                    }
                }
                //Available
                $Available = $HotelRate->item(0)->getElementsByTagName('Available');
                if ($Available->length > 0) {
                    $AvailableId = $Available->item(0)->getElementsByTagName('Id');
                    if ($AvailableId->length > 0) {
                        $AvailableId = $AvailableId->item(0)->nodeValue;
                    } else {
                        $AvailableId = "";
                    }
                    $Information = $Available->item(0)->getElementsByTagName('Information');
                    if ($Information->length > 0) {
                        $Information = $Information->item(0)->nodeValue;
                    } else {
                        $Information = "";
                    }
                    $Status = $Available->item(0)->getElementsByTagName('Status');
                    if ($Status->length > 0) {
                        $Status = $Status->item(0)->nodeValue;
                    } else {
                        $Status = "";
                    }
                }
                //Contact
                $Contact = $HotelRate->item(0)->getElementsByTagName('Contact');
                if ($Contact->length > 0) {
                    $Contact = $Contact->item(0)->getElementsByTagName('Contact');
                    if ($Contact->length > 0) {
                        $ContactId = $Contact->item(0)->getAttribute('Id');
                        $ContactEmail = $Contact->item(0)->getAttribute('Email');
                        $ContactAge = $Contact->item(0)->getAttribute('Age');
                    }
                }
                //Rooms
                $Rooms = $HotelRate->item(0)->getElementsByTagName('Rooms');
                if ($Rooms->length > 0) {
                    $Room = $Rooms->item(0)->getElementsByTagName('Room');
                    if ($Room->length > 0) {
                        $IdRoom = $Room->item(0)->getElementsByTagName('Id');
                        if ($IdRoom->length > 0) {
                            $IdRoom = $IdRoom->item(0)->nodeValue;
                        } else {
                            $IdRoom = "";
                        }
                        $NameRoom = $Room->item(0)->getElementsByTagName('Name');
                        if ($NameRoom->length > 0) {
                            $NameRoom = $NameRoom->item(0)->nodeValue;
                        } else {
                            $NameRoom = "";
                        }
                        $MealPlans = $Room->item(0)->getElementsByTagName('MealPlans');
                        if ($MealPlans->length > 0) {
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
                                    if ($Available->length > 0) {
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
                                    if ($NightsDetail->length > 0) {
                                        $NightlyRate = $NightsDetail->item(0)->getElementsByTagName('NightlyRate');
                                        if ($NightlyRate->length > 0) {
                                            for ($Auxjj = 0; $Auxjj < $NightlyRate->length; $Auxjj ++) {
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
                                    $RateDetails = $MealPlan->item($Auxkk)->getElementsByTagName('RateDetails');
                                    if ($RateDetails->length > 0) {
                                        $RateDetail = $RateDetails->item(0)->getElementsByTagName('RateDetail');
                                        if ($RateDetail->length > 0) {
                                            $IdRateDetail = $RateDetail->item(0)->getElementsByTagName('Id');
                                            if ($IdRateDetail->length > 0) {
                                                $IdRateDetail = $IdRateDetail->item(0)->nodeValue;
                                            } else {
                                                $IdRateDetail = "";
                                            }
                                            $AgencyPublicRateDetail = $RateDetail->item(0)->getElementsByTagName('AgencyPublic');
                                            if ($AgencyPublicRateDetail->length > 0) {
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
                                            if ($AvailableRateDetail->length > 0) {
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
                    }
                }
            }
        }
        //
        // Policies
        //
        $item['code'] = $value['shid'];
        $item['Name'] = $value['Name'];
        $item['total'] = $value['total'];
        $item['nett'] = $value['nett'];
        $total = $total + $value['total'];
        $tot = $value['total'];
        $item['room'] = $value['room'];
        $item['room_description'] = $value['room_description'];
        $item['room_type'] = $value['room_type'];
        $item['meal'] = $value['meal'];
        $item['total'] = $value['total'];
        $item['totalplain'] = number_format($tot, 2, '.', '');
        $avg = $tot / $nights;
        $item['avgnight'] = $filter->filter($avg);
        $item['avgplain'] = number_format($avg, 2, '.', '');
        $item['adults'] = $selectedAdults[$c];
        $item['children'] = $selectedChildren[$c];
        $item['children_ages'] = json_decode(json_encode($selectedChildrenAges[$c]), false);
        $item['cancelpolicy'] = str_replace("<p><br /></p>", "", $value['cancelpolicy']);
        $item['cancelpolicy_deadline'] = strftime("%a, %e %b %Y", $value['cancelpolicy_deadline']);
        $item['cancelpolicy_deadlinetimestamp'] = $value['cancelpolicy_deadline'];
        $item['cancelpolicy_details'] = $value['cancelpolicy'];
        $item['nonrefundable'] = $value['nonrefundable'];
        if ($item['nonrefundable'] == true) {
            $item['cancelpolicy_deadline'] = 0;
            $item['cancelpolicy'] = $translator->translate("This booking is non-refundable and cannot be amended or modified. Failure to arrive at your hotel will be treated as a No-Show and no refund will be given.");
        }
        array_push($roombreakdown, $item);
    }
    $c ++;
}
$hotel = array();
$sql = "select sid from xmlhotels_mhoteldo where sid='" . $shid . "' and hid=" . $hid;
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$row_hotel = $statement->execute();
$row_hotel->buffer();
if (! $row_hotel->valid()) {
    $response['error'] = "Unable to handle request #5";
    return false;
}
$sql = "select description as name, stars, hotel_info, address_1, address_2, address_3, address_4, latitude, longitude, city, city_name, seo, zipcode, country from xmlhotels where id=" . $hid;
$statement = $db->createStatement($sql);
$statement->prepare();
try {
    $row_hotel = $statement->execute();
    $row_hotel->buffer();
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_hotel->valid()) {
    $row_hotel = $row_hotel->current();
    if ($starsArray[$row_hotel['stars']]['stars']) {
        $row_hotel['stars'] = $starsArray[$row_hotel['stars']]['stars'];
    } else {
        $row_hotel['stars'] = 0;
    }
    $sql = "select name from countries where id=" . (int) $row_hotel['country'];
    $statement = $db->createStatement($sql);
    $statement->prepare();
    try {
        $row_country = $statement->execute();
        $row_country->buffer();
    } catch (Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
    if ($row_country->valid()) {
        $row_country = $row_country->current();
        $row_hotel['country_name'] = $row_country['name'];
    } else {
        $row_hotel['country_name'] = "";
    }
    $hotel = $row_hotel;
} else {
    $response['error'] = "Unable to handle request #6";
    return false;
}
$images = array();
try {
    $sql = "select url, description from xmlhotels_images where hotel_id=" . $hid . " order by sortorder";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $result = $statement->execute();
    $result->buffer();
    if ($result instanceof ResultInterface && $result->isQueryResult()) {
        $resultSet = new ResultSet();
        $resultSet->initialize($result);
        foreach ($resultSet as $row) {
            $item = array();
            $item['url'] = "//world-wide-web-servers.com/static/hotels/" . $row->url;
            $item['description'] = $row->description;
            array_push($images, $item);
        }
    }
    ;
} catch (Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
$response['hotel'] = $hotel;
$response['hotel']['images'] = $images;
$response['breakdown'] = $roombreakdown;
$response['total'] = $filter->filter($total);
$response['totalplain'] = number_format($total, 2, '.', '');
$response['searchsettings'] = $searchsettings;
$response['code'] = $vector['code'];
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>