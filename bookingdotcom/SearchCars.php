<?php
error_log("\r\n COMECOU TARDE \r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
$vehicle = array();
use Laminas\Db\Adapter\Adapter;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Log\Logger;
use Laminas\Log\Writer;
use Laminas\Filter\AbstractFilter;
use Laminas\I18n\Translator\Translator;
$filter = new \Laminas\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$db = new \Laminas\Db\Adapter\Adapter($config);
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
$sql = "select value from settings where name='carsbookinggousername' and affiliate_id=$affiliate_id_bookingdotcom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $carsbookinggousername = $row_settings['value'];
}
$sql = "select value from settings where name='carsbookinggopassword' and affiliate_id=$affiliate_id_bookingdotcom";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $carsbookinggopassword = $row_settings['value'];
}
$sql = "select value from settings where name='carsbookinggoerviceurl' and affiliate_id=$affiliate_id_bookingdotcom";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoerviceurl = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoMarkup' and affiliate_id=$affiliate_id_bookingdotcom";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $carsbookinggoMarkup = (double) $row_settings["value"];
}
$sql = "select value from settings where name='carsbookinggob2cMarkup' and affiliate_id=$affiliate_id_bookingdotcom";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggob2cMarkup = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoSearchSortorder' and affiliate_id=$affiliate_id_bookingdotcom";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoSearchSortorder = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoaffiliates_id' and affiliate_id=$affiliate_id_bookingdotcom";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoaffiliates_id = $row['value'];
}
$sql = "select value from settings where name='carsbookinggoTimeout' and affiliate_id=$affiliate_id_bookingdotcom";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $carsbookinggoTimeout = (int)$row['value'];
}
$sql = "select code, airport_code, name, city, latitude, longitude from carlocation where id=" . $pickup_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $pickup = strtolower($row_settings["airport_code"]);
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
    $city = $row_settings["city"];
}
$sql = "select code, airport_code, name, city, latitude, longitude from carlocation where id=" . $dropoff_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $dropoff = strtolower($row_settings["airport_code"]);
    $latitude2 = $row_settings["latitude"];
    $longitude2 = $row_settings["longitude"];
}
if ($dropoff == "") {
    $dropoff = $pickup;
}
error_log("\r\n$pickup -> $dropoff\r\n", 3, "/srv/www/htdocs/error_log");

if ($carsbookinggousername != "" and $carsbookinggopassword != "") {
    $fromyear = strftime("%Y", $from);
    $frommonth = strftime("%m", $from);
    $fromday = strftime("%d", $from);
    $toyear = strftime("%Y", $to);
    $tomonth = strftime("%m", $to);
    $today = strftime("%d", $to);
    $pickups = explode(":", $pickup_time);
    $pickuphour = $pickups[0];
    $pickupminutes = $pickups[1];
    $dropoffs = explode(":", $dropoff_time);
    $dropoffhour = $dropoffs[0];
    $dropoffminutes = $dropoffs[1];
    $raw = '<SearchRQ version="1.1" supplierInfo="true" cor="' . $residence_iso . '" prefcurr="USD" returnExtras="true">
        <Credentials username="' . $carsbookinggousername . '" password="' . $carsbookinggopassword . '" remoteIp="' . $ipaddress . '"/> 
        <PickUp>
            <Location id="' . $pickup . '"/>
            <Date year="' . $fromyear .'" month="' . $frommonth . '" day="' . $fromday . '" hour="' . $pickuphour . '" minute="' . $pickupminutes . '"/> 
        </PickUp>
        <DropOff>
            <Location id="' . $dropoff . '"/>
            <Date year="' . $toyear . '" month="' . $tomonth . '" day="' . $today . '" hour="' . $dropoffhour . '" minute="' . $dropoffminutes . '"/>
        </DropOff>
        <DriverAge>' . $driversage . '</DriverAge>
    </SearchRQ>';
    error_log("\r\n raw $raw \r\n", 3, "/srv/www/htdocs/error_log");
     
    $headers = array(
        "Content-type: application/xml",
        "Content-length: " . strlen($raw)
    );
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_URL, $carsbookinggoerviceurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($ch, CURLOPT_TIMEOUT, $carsbookinggoTimeout);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    $endTime = microtime();
    error_log("\r\n RESPONSE $response \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_bookingdotcom');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
            'errorline' => 0,
            'errormessage' => $url . $raw,
            'sqlcontext' => $response,
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
    
    if ($response != "") {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($response);
        $SearchRS = $inputDoc->getElementsByTagName("SearchRS");
        $MatchList = $SearchRS->item(0)->getElementsByTagName("MatchList");
        if ($MatchList->length > 0) {
            $Match = $MatchList->item(0)->getElementsByTagName("Match");
            if ($Match->length > 0) {
                for ($i=0; $i < $Match->length; $i++) { 
                    $ForwardURL = $Match->item($i)->getElementsByTagName("ForwardURL");
                    if ($ForwardURL->length > 0) {
                        $ForwardURL = $ForwardURL->item(0)->nodeValue;
                    } else {
                        $ForwardURL = "";
                    }
                    $Vehicle = $Match->item($i)->getElementsByTagName("Vehicle");
                    if ($Vehicle->length > 0) {
                        $id = $Vehicle->item(0)->getAttribute("id");
                        $propositionType = $Vehicle->item(0)->getAttribute("propositionType");
                        $automatic = $Vehicle->item(0)->getAttribute("automatic");
                        $aircon = $Vehicle->item(0)->getAttribute("aircon");
                        $airbag = $Vehicle->item(0)->getAttribute("airbag");
                        $petrol = $Vehicle->item(0)->getAttribute("petrol");
                        $group = $Vehicle->item(0)->getAttribute("group");
                        $doors = $Vehicle->item(0)->getAttribute("doors");
                        $seats = $Vehicle->item(0)->getAttribute("seats");
                        $bigSuitcase = $Vehicle->item(0)->getAttribute("bigSuitcase");
                        $smallSuitcase = $Vehicle->item(0)->getAttribute("smallSuitcase");
                        $suitcases = $Vehicle->item(0)->getAttribute("suitcases");
                        $fuelPolicy = $Vehicle->item(0)->getAttribute("fuelPolicy");
                        $cmaCompliant = $Vehicle->item(0)->getAttribute("cmaCompliant");
                        $insurancePkg = $Vehicle->item(0)->getAttribute("insurancePkg");
                        $display = $Vehicle->item(0)->getAttribute("display");
                        $order = $Vehicle->item(0)->getAttribute("order");
                        $freeCancellation = $Vehicle->item(0)->getAttribute("freeCancellation");
                        $unlimitedMileage = $Vehicle->item(0)->getAttribute("unlimitedMileage");
                        $Name = $Vehicle->item(0)->getElementsByTagName("Name");
                        if ($Name->length > 0) {
                            $Name = $Name->item(0)->nodeValue;
                        } else {
                            $Name = "";
                        }
                        $ImageURL = $Vehicle->item(0)->getElementsByTagName("ImageURL");
                        if ($ImageURL->length > 0) {
                            $ImageURL = $ImageURL->item(0)->nodeValue;
                        } else {
                            $ImageURL = "";
                        }
                        $LargeImageURL = $Vehicle->item(0)->getElementsByTagName("LargeImageURL");
                        if ($LargeImageURL->length > 0) {
                            $LargeImageURL = $LargeImageURL->item(0)->nodeValue;
                        } else {
                            $LargeImageURL = "";
                        }
                    }
                    $Fees = $Match->item($i)->getElementsByTagName("Fees");
                    if ($Fees->length > 0) {
                        $DepositExcessFees = $Fees->item(0)->getElementsByTagName("DepositExcessFees");
                        if ($DepositExcessFees->length > 0) {
                            $TheftExcess = $DepositExcessFees->item(0)->getElementsByTagName("TheftExcess");
                            if ($TheftExcess->length > 0) {
                                $TheftExcess_amount = $TheftExcess->item(0)->getAttribute("amount");
                                $TheftExcess_currency = $TheftExcess->item(0)->getAttribute("currency");
                                $TheftExcess_taxIncluded = $TheftExcess->item(0)->getAttribute("taxIncluded");
                            }
                            $DamageExcess = $DepositExcessFees->item(0)->getElementsByTagName("DamageExcess");
                            if ($DamageExcess->length > 0) {
                                $DamageExcess_amount = $DamageExcess->item(0)->getAttribute("amount");
                                $DamageExcess_currency = $DamageExcess->item(0)->getAttribute("currency");
                                $DamageExcess_taxIncluded = $DamageExcess->item(0)->getAttribute("taxIncluded");
                            }
                            $Deposit = $DepositExcessFees->item(0)->getElementsByTagName("Deposit");
                            if ($Deposit->length > 0) {
                                $Deposit_amount = $Deposit->item(0)->getAttribute("amount");
                                $Deposit_currency = $Deposit->item(0)->getAttribute("currency");
                                $Deposit_taxIncluded = $Deposit->item(0)->getAttribute("taxIncluded");
                            }
                        }
                        $FuelPolicy = $Fees->item(0)->getElementsByTagName("FuelPolicy");
                        if ($FuelPolicy->length > 0) {
                            $FuelPolicy_type = $FuelPolicy->item(0)->getAttribute("type");
                        }
                        $KnownFees = $Fees->item(0)->getElementsByTagName("KnownFees");
                        if ($KnownFees->length > 0) {
                            $Fee = $KnownFees->item(0)->getElementsByTagName("Fee");
                            if ($Fee->length > 0) {
                                for ($iAux=0; $iAux < $Fee->length; $iAux++) { 
                                    $feeTypeName = $Fee->item($iAux)->getAttribute("feeTypeName");
                                    $alwaysPayable = $Fee->item($iAux)->getAttribute("alwaysPayable");
                                    $minAmount = $Fee->item($iAux)->getAttribute("minAmount");
                                    $maxAmount = $Fee->item($iAux)->getAttribute("maxAmount");
                                    $currency = $Fee->item($iAux)->getAttribute("currency");
                                    $taxIncluded = $Fee->item($iAux)->getAttribute("taxIncluded");
                                    $perDuration = $Fee->item($iAux)->getAttribute("perDuration");
                                    $feeDistance = $Fee->item($iAux)->getAttribute("feeDistance");
                                    $distance = $Fee->item($iAux)->getAttribute("distance");
                                    $iskM = $Fee->item($iAux)->getAttribute("iskM");
                                    $unlimited = $Fee->item($iAux)->getAttribute("unlimited");
                                }
                            }
                        }
                    }
                    $Price = $Match->item($i)->getElementsByTagName("Price");
                    if ($Price->length > 0) {
                        $currency = $Price->item(0)->getAttribute("currency");
                        $baseCurrency = $Price->item(0)->getAttribute("baseCurrency");
                        $basePrice = $Price->item(0)->getAttribute("basePrice");
                        $deposit = $Price->item(0)->getAttribute("deposit");
                        $baseDeposit = $Price->item(0)->getAttribute("baseDeposit");
                        $discount = $Price->item(0)->getAttribute("discount");
                        $driveAwayPrice = $Price->item(0)->getAttribute("driveAwayPrice");
                        $quoteAllowed = $Price->item(0)->getAttribute("quoteAllowed");
                        $Price = $Price->item(0)->nodeValue;
                    } else {
                        $Price = "";
                    }
                    $total = $Price;
                    $nettotal = $basePrice;

                    $ExtraInfoList = $Match->item($i)->getElementsByTagName("ExtraInfoList");
                    if ($ExtraInfoList->length > 0) {
                        $ExtraInfo = $ExtraInfoList->item(0)->getElementsByTagName("ExtraInfo");
                        if ($ExtraInfo->length > 0) {
                            $Extra = $ExtraInfo->item(0)->getElementsByTagName("Extra");
                            if ($Extra->length > 0) {
                                $Extraavailable = $Extra->item(0)->getAttribute("available");
                                $Extraproduct = $Extra->item(0)->getAttribute("product");
                                $ExtraName = $Extra->item(0)->getElementsByTagName("Name");
                                if ($ExtraName->length > 0) {
                                    $ExtraName = $ExtraName->item(0)->nodeValue;
                                } else {
                                    $ExtraName = "";
                                }
                                $ExtraComments = $Extra->item(0)->getElementsByTagName("Comments");
                                if ($ExtraComments->length > 0) {
                                    $ExtraComments = $ExtraComments->item(0)->nodeValue;
                                } else {
                                    $ExtraComments = "";
                                }
                                $PreBookingURIs = $Extra->item(0)->getElementsByTagName("PreBookingURIs");
                                if ($PreBookingURIs->length > 0) {
                                    $PreBookingKeyFactsURI = $PreBookingURIs->item(0)->getElementsByTagName("PreBookingKeyFactsURI");
                                    if ($PreBookingKeyFactsURI->length > 0) {
                                        $PreBookingKeyFactsURI = $PreBookingKeyFactsURI->item(0)->nodeValue;
                                    } else {
                                        $PreBookingKeyFactsURI = "";
                                    }
                                    $PreBookingPolicyWordingURI = $PreBookingURIs->item(0)->getElementsByTagName("PreBookingPolicyWordingURI");
                                    if ($PreBookingPolicyWordingURI->length > 0) {
                                        $PreBookingPolicyWordingURI = $PreBookingPolicyWordingURI->item(0)->nodeValue;
                                    } else {
                                        $PreBookingPolicyWordingURI = "";
                                    }
                                }
                            }
                            $ExtraPrice = $ExtraInfo->item(0)->getElementsByTagName("Price");
                            if ($ExtraPrice->length > 0) {
                                $ExtraPrice_currency = $ExtraPrice->item(0)->getAttribute("currency");
                                $ExtraPrice_baseCurrency = $ExtraPrice->item(0)->getAttribute("baseCurrency");
                                $ExtraPrice_basePrice = $ExtraPrice->item(0)->getAttribute("basePrice");
                                $prePayable = $ExtraPrice->item(0)->getAttribute("prePayable");
                                $maxPrice = $ExtraPrice->item(0)->getAttribute("maxPrice");
                                $minPrice = $ExtraPrice->item(0)->getAttribute("minPrice");
                                $pricePerWhat = $ExtraPrice->item(0)->getAttribute("pricePerWhat");
                                $priceAvailable = $ExtraPrice->item(0)->getAttribute("priceAvailable");
                                $driveAwayPrice = $ExtraPrice->item(0)->getAttribute("driveAwayPrice");
                                $ExtraPrice = $ExtraPrice->item(0)->nodeValue;
                            } else {
                                $ExtraPrice = "";
                            }
                        }
                    }
                    $Route = $Match->item($i)->getElementsByTagName("Route");
                    if ($Route->length > 0) {
                        $PickUp = $Route->item(0)->getElementsByTagName("PickUp");
                        if ($PickUp->length > 0) {
                            $Location = $PickUp->item(0)->getElementsByTagName("Location");
                            if ($Location->length > 0) {
                                $PickUpLocation_id = $Location->item(0)->getAttribute("id");
                                $PickUpLocation_locCode = $Location->item(0)->getAttribute("locCode");
                                $PickUpLocation_locName = $Location->item(0)->getAttribute("locName");
                                $PickUpLocation_onAirport = $Location->item(0)->getAttribute("onAirport");
                            }
                        }
                        $DropOff = $Route->item(0)->getElementsByTagName("DropOff");
                        if ($DropOff->length > 0) {
                            $Location = $DropOff->item(0)->getElementsByTagName("Location");
                            if ($Location->length > 0) {
                                $DropOffLocation_id = $Location->item(0)->getAttribute("id");
                                $DropOffLocation_locCode = $Location->item(0)->getAttribute("locCode");
                                $DropOffLocation_locName = $Location->item(0)->getAttribute("locName");
                                $DropOffLocation_onAirport = $Location->item(0)->getAttribute("onAirport");
                            }
                        }
                    }
                    $Supplier = $Match->item($i)->getElementsByTagName("Supplier");
                    if ($Supplier->length > 0) {
                        $long = $Supplier->item(0)->getAttribute("long");
                        $lat = $Supplier->item(0)->getAttribute("lat");
                        $dropOffLong = $Supplier->item(0)->getAttribute("dropOffLong");
                        $dropOffLat = $Supplier->item(0)->getAttribute("dropOffLat");
                        $address = $Supplier->item(0)->getAttribute("address");
                        $dropOffAddress = $Supplier->item(0)->getAttribute("dropOffAddress");
                        $small_logo = $Supplier->item(0)->getAttribute("small_logo");
                        $locType = $Supplier->item(0)->getAttribute("locType");
                        $supplierName = $Supplier->item(0)->getAttribute("supplierName");
                        $pickUpInstructions = $Supplier->item(0)->getAttribute("pickUpInstructions");
                        $dropOffInstructions = $Supplier->item(0)->getAttribute("dropOffInstructions");
                        $Supplier = $Supplier->item(0)->nodeValue;
                    } else {
                        $Supplier = "";
                    }
                    $Ratings = $Match->item($i)->getElementsByTagName("Ratings");
                    if ($Ratings->length > 0) {
                        $Average = $Ratings->item(0)->getElementsByTagName("Average");
                        if ($Average->length > 0) {
                            $Average = $Average->item(0)->nodeValue;
                        } else {
                            $Average = "";
                        }
                        $AverageText = $Ratings->item(0)->getElementsByTagName("Average");
                        if ($AverageText->length > 0) {
                            $AverageText = $AverageText->item(0)->nodeValue;
                        } else {
                            $AverageText = "";
                        }
                        $NumRatings = $Ratings->item(0)->getElementsByTagName("NumRatings");
                        if ($NumRatings->length > 0) {
                            $NumRatings = $NumRatings->item(0)->nodeValue;
                        } else {
                            $NumRatings = "";
                        }
                        $ValForMoney = $Ratings->item(0)->getElementsByTagName("ValForMoney");
                        if ($ValForMoney->length > 0) {
                            $ValForMoney = $ValForMoney->item(0)->nodeValue;
                        } else {
                            $ValForMoney = "";
                        }
                        $Efficiency = $Ratings->item(0)->getElementsByTagName("Efficiency");
                        if ($Efficiency->length > 0) {
                            $Efficiency = $Efficiency->item(0)->nodeValue;
                        } else {
                            $Efficiency = "";
                        }
                        $CollectTime = $Ratings->item(0)->getElementsByTagName("CollectTime");
                        if ($CollectTime->length > 0) {
                            $CollectTime = $CollectTime->item(0)->nodeValue;
                        } else {
                            $CollectTime = "";
                        }
                        $DropOffTime = $Ratings->item(0)->getElementsByTagName("DropOffTime");
                        if ($DropOffTime->length > 0) {
                            $DropOffTime = $DropOffTime->item(0)->nodeValue;
                        } else {
                            $DropOffTime = "";
                        }
                        $Cleanliness = $Ratings->item(0)->getElementsByTagName("Cleanliness");
                        if ($Cleanliness->length > 0) {
                            $Cleanliness = $Cleanliness->item(0)->nodeValue;
                        } else {
                            $Cleanliness = "";
                        }
                        $Condition = $Ratings->item(0)->getElementsByTagName("Condition");
                        if ($Condition->length > 0) {
                            $Condition = $Condition->item(0)->nodeValue;
                        } else {
                            $Condition = "";
                        }
                        $Locating = $Ratings->item(0)->getElementsByTagName("Locating");
                        if ($Locating->length > 0) {
                            $Locating = $Locating->item(0)->nodeValue;
                        } else {
                            $Locating = "";
                        }
                    }

                    $bags = $bigSuitcase + $smallSuitcase;         
  
                    $cars[$counter]['id'] = $counter;
                    $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-15-" . $counter;
                    $cars[$counter]['vendorpicture'] = $ImageURL;
                    $cars[$counter]['vendorcode'] = $id;
                    $cars[$counter]['vendor'] = $Name;
                    $cars[$counter]['vendorshortname'] = $Name;
                    $cars[$counter]['size'] = $seats;
                    $cars[$counter]['doors'] = $doors;
                    $cars[$counter]['aircondition'] = $aircon;
                    $cars[$counter]['transmission'] = $automatic;
                    $cars[$counter]['bags'] = $bags;
                    $cars[$counter]['status'] = $display;
                    $cars[$counter]['from'] = $from;
                    $cars[$counter]['to'] = $to;
                    $cars[$counter]['pickup'] = ucwords(strtolower($PickUpLocation_locName));
                    $cars[$counter]['dropoff'] = ucwords(strtolower($DropOffLocation_locName));
                    $cars[$counter]['class'] = $propositionType;
                    $cars[$counter]['currency'] = $currency;
                    $cars[$counter]['productId'] = $productId;
                    $cars[$counter]['programId'] = $CarProgramId;
                    $cars[$counter]['name'] = $Name;
                    $cars[$counter]['picture'] = $small_logo;
                    $cars[$counter]['programname'] = $group;
                    $cars[$counter]['coverage'] = $coverage;
                    $cars[$counter]['ID_Context'] = $ID_Context;
                    $cars[$counter]['netcurrency'] = $baseCurrency;
                    $cars[$counter]['netprice'] = $nettotal;
                    $cars[$counter]['cancelpolicy'] = $freeCancellation;
                    $cars[$counter]['pickuplocation_id'] = $PickUpLocation_id;
                    $cars[$counter]['dropofflocation_id'] = $DropOffLocation_id;
                    $cars[$counter]['address'] = $address;
                    // Total including VAT in renting country currency
                    /* if ($minPrice < $CarProgramPrice) {
                        $minPrice = $CarProgramPrice;
                    }
                    $minPrice = number_format($minPrice, 2, ".", "");
                    if ($carstouricoholidaysMarkup != 0) {
                        $minPrice = $minPrice + (($minPrice * $carstouricoholidaysMarkup) / 100);
                    }
                    if ($agent_markup != 0) {
                        $minPrice = $minPrice + (($minPrice * $agent_markup) / 100);
                    }
                    if ($CarProgramCurrency != "") {
                        if ($CarProgramCurrency != $scurrency) {
                            $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                        }
                    } else {
                        if ($currencyBase != "") {
                            if ($currencyBase != $scurrency) {
                                $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                            }
                        }
                    } */
                    $dailytotal = $total / $nights;
                    $dailytotal = number_format($dailytotal, 2, ".", "");
                    //$minPrice = number_format($minPrice, 2, ".", "");
                    $cars[$counter]['currency'] = $scurrency;
                    $cars[$counter]['total'] = $filter->filter($total);
                    $cars[$counter]['dailytotal'] = $filter->filter($dailytotal);
                    $cars[$counter]['dueatpickupplain'] = $DueAtPickup;
                    $cars[$counter]['dueatpickup'] = $filter->filter($DueAtPickup);
                    $cars[$counter]['dueatpickupcurrency'] = $filter->filter($currency);
                    // Location
                    // $cars[$counter]['special'] = 1;
                    // $cars[$counter]['recommended'] = 1;
                    $counter = $counter + 1;
                }
            }
        }
    }
    //
    // Store Session
    //
    try {
        $sql = new Sql($db);
        $delete = $sql->delete();
        $delete->from('quote_session_bookingdotcom');
        $delete->where(array(
            'session_id' => $session_id
        ));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('quote_session_bookingdotcom');
        $insert->values(array(
            'session_id' => $session_id,
            'xmlrequest' => (string) $raw,
            'xmlresult' => (string) $response,
            'data' => base64_encode(serialize($cars)),
            'searchsettings' => base64_encode(serialize($requestdata))
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>