<?php
error_log("\r\n COMECOU SEARCHCARS \r\n", 3, "/srv/www/htdocs/error_log");
$scurrency = strtoupper($currency);
$vehicle = array();
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
$sfilter = array();
$valid = 0;
$db = new \Zend\Db\Adapter\Adapter($config);
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enableCarnectCars' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_carnect = $affiliate_id;
} else {
    $affiliate_id_carnect = 0;
}
$sql = "select value from settings where name='CarnectLogin' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectLogin = $row_settings['value'];
}
$sql = "select value from settings where name='CarnectCarspassword' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='CarnectCarswebservicesURL' and affiliate_id=$affiliate_id_carnect";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $CarnectCarswebservicesURL = $row['value'];
}
$sql = "select value from settings where name='CarnectCarsMarkup' and affiliate_id=$affiliate_id_carnect";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarnectCarsMarkup = (double) $row_settings["value"];
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
$db->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\n$pickup -> $dropoff\r\n", 3, "/srv/www/htdocs/error_log");
$username = "OTA_APTMSTST1";
$password = "fWQBzb4L";
$host = 'https://cis1-xmldirect.ehi.com/services30/OTA30SOAP';
if ($username != "" and $password != "") {
    // MaxResponses="30"
    $raw = '<?xml version="1.0" encoding="UTF-8"?><soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns="http://www.opentravel.org/OTA/2003/05"><soapenv:Header/><soapenv:Body><OTA_VehAvailRateRQ PrimaryLangID="EN" TimeStamp="2010-05-23T09:30:00" Target="Test" Version="3.0" TransactionIdentifier="100000001" SequenceNmbr="1" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05:\Users\e557gc\Documents\XML\2012BU~1\OTA_VehAvailRateRQ.xsd"><POS><Source ISOCountry="US"><RequestorID Type="4" ID="XMLRTA"><CompanyName Code="EX" CompanyShortName="EHIXMLTEST"/></RequestorID></Source></POS><VehAvailRQCore Status="Available"><VehRentalCore PickUpDateTime="' . strftime("%Y-%m-%d", $from) . "T" . $pickup_time . ":00" . '" ReturnDateTime="' . strftime("%Y-%m-%d", $to) . "T" . $dropoff_time . ":00" . '"><PickUpLocation LocationCode="' . $pickup . '" /><ReturnLocation LocationCode="' . $dropoff . '" /></VehRentalCore><VendorPrefs><VendorPref Code="ET"/></VendorPrefs><DriverType Age="' . $driversage . '"/>';
    // ​<RateQualifier RateQualifier="CORP"/>
    $raw .= '<TPA_Extensions><TPA_Extension_Flags EnhancedTotalPrice="true"/></TPA_Extensions></VehAvailRQCore></OTA_VehAvailRateRQ></soapenv:Body></soapenv:Envelope>';
    error_log("\r\nEnterprise Request: $raw \r\n", 3, "/srv/www/htdocs/error_log");
    $ch = curl_init($host);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: text/xml;charset=UTF-8',
        'Accept-Encoding: gzip,deflate',
        'SOAPAction: "OTA_VehAvailRateRQ"',
        'Host: cis1-xmldirect.ehi.com',
        'User-Agent: Jakarta Commons-HttpClient/3.1',
        'Content-Length: ' . strlen($raw)
    ));
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    error_log("\r\nEnterprise Response: $response \r\n", 3, "/srv/www/htdocs/error_log");
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_enterprise');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
            'errorline' => 0,
            'errormessage' => $raw,
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
    
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($response);
    
    $responseElement = $inputDoc->documentElement;
    $xpath = new DOMXPath($inputDoc);
    $search = "";
    $search = $xpath->query('/env:Envelope/env:Body', $responseElement);
    
    $OTA_VehAvailRateRS = $search->item(0)->getElementsByTagName("OTA_VehAvailRateRS");
    $VehAvailRSCore = $OTA_VehAvailRateRS->item(0)->getElementsByTagName("VehAvailRSCore");
    // VehRentalCore
    $VehRentalCore = $VehAvailRSCore->item(0)->getElementsByTagName("VehRentalCore");
    if ($VehRentalCore->length > 0) {
        $PickUpDateTime = $VehRentalCore->item(0)->getAttribute("PickUpDateTime");
        $ReturnDateTime = $VehRentalCore->item(0)->getAttribute("ReturnDateTime");
        $PickUpLocation = $VehRentalCore->item(0)->getElementsByTagName("PickUpLocation");
        if ($PickUpLocation->length > 0) {
            $LocationCodePickup = $PickUpLocation->item(0)->getAttribute("LocationCode");
        }
        $ReturnLocation = $VehRentalCore->item(0)->getElementsByTagName("ReturnLocation");
        if ($ReturnLocation->length > 0) {
            $LocationCodeReturn = $ReturnLocation->item(0)->getAttribute("LocationCode");
        }
    }
    
    // VehVendorAvails
    $VehVendorAvails = $VehAvailRSCore->item(0)->getElementsByTagName("VehVendorAvails");
    if ($VehVendorAvails->length > 0) {
        $VehVendorAvail = $VehVendorAvails->item(0)->getElementsByTagName("VehVendorAvail");
        if ($VehVendorAvail->length > 0) {
            $Vendor = $VehVendorAvail->item(0)->getElementsByTagName("Vendor");
            if ($Vendor->length > 0) {
                $Code = $Vendor->item(0)->getAttribute("Code");
                $CompanyShortName = $Vendor->item(0)->getAttribute("CompanyShortName");
            } else {
                $Code = "";
                $CompanyShortName = "";
            }
            
            // Info
            $Info = $VehVendorAvail->item(0)->getElementsByTagName("Info");
            if ($Info->length > 0) {
                $TPA_Extensions = $Info->item(0)->getElementsByTagName("TPA_Extensions");
                if ($TPA_Extensions->length > 0) {
                    $TPA_Extensions_Inf = $TPA_Extensions->item(0)->getElementsByTagName('TPA_Extensions_Inf');
                    if ($TPA_Extensions_Inf->length > 0) {
                        $RentalDuration = $TPA_Extensions_Inf->item(0)->getAttribute("RentalDuration");
                    } else {
                        $RentalDuration = "";
                    }
                    $TPA_Extension_Flags = $TPA_Extensions->item(0)->getElementsByTagName('TPA_Extension_Flags');
                    if ($TPA_Extension_Flags->length > 0) {
                        $Type = $TPA_Extension_Flags->item(0)->getAttribute("Type");
                        $CustDropOff = $TPA_Extension_Flags->item(0)->getAttribute("CustDropOff");
                        $CustPickUp = $TPA_Extension_Flags->item(0)->getAttribute("CustPickUp");
                    } else {
                        $Type = "";
                        $CustDropOff = "";
                        $CustPickUp = "";
                    }
                }
                
                $LocationDetails = $Info->item(0)->getElementsByTagName("LocationDetails");
                if ($LocationDetails->length > 0) {
                    $AdditionalInfo = $LocationDetails->item(0)->getElementsByTagName("AdditionalInfo");
                    if ($AdditionalInfo->length > 0) {
                        $VehRentLocInfos = $AdditionalInfo->item(0)->getElementsByTagName("VehRentLocInfos");
                        if ($VehRentLocInfos->length > 0) {
                            $VehRentLocInfo = $VehRentLocInfos->item(0)->getElementsByTagName("VehRentLocInfo");
                            if ($VehRentLocInfo->length > 0) {
                                for ($k = 0; $k < $VehRentLocInfo->length; $k ++) {
                                    $Type = $VehRentLocInfo->item($k)->getAttribute("Type");
                                    $Title = $VehRentLocInfo->item($k)->getAttribute("Title");
                                    $SubSection = $VehRentLocInfo->item($k)->getElementsByTagName('SubSection');
                                    if ($SubSection->length > 0) {
                                        $SubCode = $SubSection->item(0)->getAttribute("SubCode");
                                        $Paragraph = $SubSection->item(0)->getElementsByTagName('Paragraph');
                                        if ($Paragraph->length > 0) {
                                            $Text = $Paragraph->item(0)->getElementsByTagName('Text');
                                            if ($Text->length > 0) {
                                                $Text = $Text->item(0)->nodeValue;
                                            } else {
                                                $Text = "";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            // VehAvails
            $VehAvails = $VehVendorAvail->item(0)->getElementsByTagName("VehAvails");
            if ($VehAvails->length > 0) {
                $VehAvail = $VehAvails->item(0)->getElementsByTagName("VehAvail");
                if ($VehAvail->length > 0) {
                    for ($w=0; $w < $VehAvail->length; $w++) { 
                        $VehAvailInfo = $VehAvail->item($w)->getElementsByTagName('VehAvailInfo');
                        if ($VehAvailInfo->length > 0) {
                            $VehAvailInfo = $VehAvailInfo->item(0)->nodeValue;
                        } else {
                            $VehAvailInfo = "";
                        }
                        
                        $VehAvailCore = $VehAvail->item($w)->getElementsByTagName('VehAvailCore');
                        if ($VehAvailCore->length > 0) {
                            $Status = $VehAvailCore->item(0)->getAttribute("Status");
                            // Vehicle
                            $Vehicle = $VehAvailCore->item(0)->getElementsByTagName('Vehicle');
                            if ($Vehicle->length > 0) {
                                $TransmissionType = $Vehicle->item(0)->getAttribute("TransmissionType");
                                $AirConditionInd = $Vehicle->item(0)->getAttribute("AirConditionInd");
                                $BaggageQuantity = $Vehicle->item(0)->getAttribute("BaggageQuantity");
                                $PassengerQuantity = $Vehicle->item(0)->getAttribute("PassengerQuantity");
                                $PictureURL = $Vehicle->item(0)->getElementsByTagName('PictureURL');
                                if ($PictureURL->length > 0) {
                                    $PictureURL = $PictureURL->item(0)->nodeValue;
                                } else {
                                    $PictureURL = "";
                                }
                                $VehType = $Vehicle->item(0)->getElementsByTagName('VehType');
                                if ($VehType->length > 0) {
                                    $VehicleCategory = $VehType->item(0)->getAttribute("VehicleCategory");
                                } else {
                                    $VehicleCategory = "";
                                }
                                $VehClass = $Vehicle->item(0)->getElementsByTagName('VehClass');
                                if ($VehClass->length > 0) {
                                    $Size = $VehClass->item(0)->getAttribute("Size");
                                } else {
                                    $Size = "";
                                }
                                $VehMakeModel = $Vehicle->item(0)->getElementsByTagName('VehMakeModel');
                                if ($VehMakeModel->length > 0) {
                                    $CodeVehMakeModel = $VehMakeModel->item(0)->getAttribute("Code");
                                    $NameVehMakeModel = $VehMakeModel->item(0)->getAttribute("Name");
                                } else {
                                    $CodeVehMakeModel = "";
                                    $NameVehMakeModel = "";
                                }
                            }
                            
                            // TotalCharge
                            $TotalCharge = $VehAvailCore->item(0)->getElementsByTagName('TotalCharge');
                            if ($TotalCharge->length > 0) {
                                $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
                                $RateTotalAmount = $TotalCharge->item(0)->getAttribute("RateTotalAmount");
                                $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
                            } else {
                                $CurrencyCode = "";
                                $RateTotalAmount = "";
                                $EstimatedTotalAmount = "";
                            }
                            
                            // Reference
                            $Reference = $VehAvailCore->item(0)->getElementsByTagName('Reference');
                            if ($Reference->length > 0) {
                                $TypeReference = $Reference->item(0)->getAttribute("Type");
                                $DateTime = $Reference->item(0)->getAttribute("DateTime");
                                $ID = $Reference->item(0)->getAttribute("ID");
                            } else {
                                $Type = "";
                                $DateTime = "";
                                $ID = "";
                            }

                            // PricedEquips
                            $PricedEquips = $VehAvailCore->item(0)->getElementsByTagName('PricedEquips');
                            if ($PricedEquips->length > 0) {
                                $PricedEquip = $PricedEquips->item(0)->getElementsByTagName('PricedEquip');
                                if ($PricedEquip->length > 0) {
                                    $Required = $PricedEquip->item(0)->getAttribute("Required");
                                    $Equipment = $PricedEquip->item(0)->getElementsByTagName('Equipment');
                                    if ($Equipment->length > 0) {
                                        $Quantity = $Equipment->item(0)->getAttribute("Quantity");
                                        $EquipType = $Equipment->item(0)->getAttribute("EquipType");
                                    } else {
                                        $Quantity = "";
                                        $EquipType = "";
                                    }
                                    $Charge = $PricedEquip->item(0)->getElementsByTagName('Charge');
                                    if ($Charge->length > 0) {
                                        $CurrencyCodeCharge = $Charge->item(0)->getAttribute("CurrencyCode");
                                        $AmountCharge = $Charge->item(0)->getAttribute("Amount");
                                        $IncludedInEstTotalIndCharge = $Charge->item(0)->getAttribute("IncludedInEstTotalInd");
                                        $IncludedInRateCharge = $Charge->item(0)->getAttribute("IncludedInRate");
                                        $GuaranteedIndCharge = $Charge->item(0)->getAttribute("GuaranteedInd");
                                    } else {
                                        $CurrencyCode = "";
                                        $Amount = "";
                                    }
                                }
                            }

                            // RentalRate
                            $RentalRate = $VehAvailCore->item(0)->getElementsByTagName('RentalRate');
                            if ($RentalRate->length > 0) {
                                $RateDistance = $RentalRate->item(0)->getElementsByTagName('RateDistance');
                                if ($RateDistance->length > 0) {
                                    $VehiclePeriodUnitName = $RateDistance->item(0)->getAttribute("VehiclePeriodUnitName");
                                    $DistUnitName = $RateDistance->item(0)->getAttribute("DistUnitName");
                                    $Unlimited = $RateDistance->item(0)->getAttribute("Unlimited");
                                } else {
                                    $VehiclePeriodUnitName = "";
                                    $DistUnitName = "";
                                    $Unlimited = "";
                                }
                                $RateQualifier = $RentalRate->item(0)->getElementsByTagName('RateQualifier');
                                if ($RateQualifier->length > 0) {
                                    $CorpDiscountNmbr = $RateQualifier->item(0)->getAttribute("CorpDiscountNmbr");
                                    $RateQualifier2 = $RateQualifier->item(0)->getAttribute("RateQualifier");
                                    $RatePeriod = $RateQualifier->item(0)->getAttribute("RatePeriod");
                                } else {
                                    $CorpDiscountNmbr = "";
                                    $RateQualifier2 = "";
                                    $RatePeriod = "";
                                }

                                $VehicleCharges = $RentalRate->item(0)->getElementsByTagName('VehicleCharges');
                                if ($VehicleCharges->length > 0) {
                                    $VehicleCharge = $VehicleCharges->item(0)->getElementsByTagName('VehicleCharge');
                                    if ($VehicleCharge->length > 0) {
                                        for ($i = 0; $i < $VehicleCharge->length; $i ++) {
                                            $Amount = $VehicleCharge->item($i)->getAttribute("Amount");
                                            $CurrencyCode = $VehicleCharge->item($i)->getAttribute("CurrencyCode");
                                            $TaxInclusive = $VehicleCharge->item($i)->getAttribute("TaxInclusive");
                                            $Purpose = $VehicleCharge->item($i)->getAttribute("Purpose");
                                            $Description = $VehicleCharge->item($i)->getAttribute("Description");
                                            $Calculation = $VehicleCharge->item($i)->getElementsByTagName('Calculation');
                                            if ($Calculation->length > 0) {
                                                $UnitCharge = $Calculation->item(0)->getAttribute("UnitCharge");
                                                $UnitName = $Calculation->item(0)->getAttribute("UnitName");
                                                $Quantity = $Calculation->item(0)->getAttribute("Quantity");
                                            } else {
                                                $UnitCharge = "";
                                                $UnitName = "";
                                                $Quantity = "";
                                            }
                                        }
                                    }
                                }
                            }
                            
                            // Fees
                            $Fees = $VehAvailCore->item(0)->getElementsByTagName('Fees');
                            if ($Fees->length > 0) {
                                $Fee = $Fees->item(0)->getElementsByTagName('Fee');
                                if ($Fee->length > 0) {
                                    for ($j = 0; $j < $Fee->length; $j ++) {
                                        $Amount = $Fee->item($j)->getAttribute("Amount");
                                        $CurrencyCode = $Fee->item($j)->getAttribute("CurrencyCode");
                                        $Purpose = $Fee->item($j)->getAttribute("Purpose");
                                        $Description = $Fee->item($j)->getAttribute("Description");
                                        $IncludedInRate = $Fee->item($j)->getAttribute("IncludedInRate");
                                        $IncludedInEstTotalInd = $Fee->item($j)->getAttribute("IncludedInEstTotalInd");
                                    }
                                }
                            }
                        }

            
                        $cars[$counter]['id'] = $counter;
                        $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-14-" . $counter;
                        $cars[$counter]['vendorpicture'] = "https://world-wide-web-servers.com/car/vendors/" . $Code . ".gif";
                        $cars[$counter]['vendorcode'] = $Code;
                        $cars[$counter]['vendor'] = $CompanyShortName;
                        $cars[$counter]['vendorshortname'] = $CompanyShortName;
                        $cars[$counter]['size'] = $PassengerQuantity;
                        $cars[$counter]['doors'] = $DoorCount;
                        $cars[$counter]['aircondition'] = $AirConditionInd;
                        $cars[$counter]['transmission'] = $TransmissionType;
                        $cars[$counter]['bags'] = $BaggageQuantity;
                        $cars[$counter]['status'] = $Status;
                        $cars[$counter]['from'] = $from;
                        $cars[$counter]['to'] = $to;
                        $cars[$counter]['pickup'] = ucwords(strtolower($LocationCodePickup));
                        $cars[$counter]['dropoff'] = ucwords(strtolower($LocationCodeReturn));
                        $cars[$counter]['class'] = $CodeVehMakeModel;
                        $cars[$counter]['currency'] = $CurrencyCode;
                        $cars[$counter]['productId'] = $productId;
                        $cars[$counter]['programId'] = $CarProgramId;
                        $cars[$counter]['name'] = $NameVehMakeModel;
                        $cars[$counter]['picture'] = $PictureURL;
                        $cars[$counter]['programname'] = $CarProgramName;
                        $cars[$counter]['coverage'] = $coverage;
                        $cars[$counter]['ID_Context'] = $ID_Context;
                        $cars[$counter]['netcurrency'] = $CurrencyCode;
                        $cars[$counter]['netprice'] = $EstimatedTotalAmount;
                        // Total including VAT in renting country currency
                        /*
                        * if ($minPrice < $CarProgramPrice) {
                        * $minPrice = $CarProgramPrice;
                        * }
                        * $minPrice = number_format($minPrice, 2, ".", "");
                        * if ($carstouricoholidaysMarkup != 0) {
                        * $minPrice = $minPrice + (($minPrice * $carstouricoholidaysMarkup) / 100);
                        * }
                        * if ($agent_markup != 0) {
                        * $minPrice = $minPrice + (($minPrice * $agent_markup) / 100);
                        * }
                        * if ($CarProgramCurrency != "") {
                        * if ($CarProgramCurrency != $scurrency) {
                        * $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                        * }
                        * } else {
                        * if ($currencyBase != "") {
                        * if ($currencyBase != $scurrency) {
                        * $minPrice = $CurrencyConverter->convert($minPrice, $CarProgramCurrency, $scurrency);
                        * }
                        * }
                        * }
                        */
                        $dailytotal = $EstimatedTotalAmount / $nights;
                        $dailytotal = number_format($dailytotal, 2, ".", "");
                        // $minPrice = number_format($minPrice, 2, ".", "");
                        $cars[$counter]['currency'] = $CurrencyCode;
                        $cars[$counter]['total'] = $filter->filter($EstimatedTotalAmount);
                        $cars[$counter]['dailytotal'] = $filter->filter($dailytotal);
                        $cars[$counter]['dueatpickupplain'] = $DueAtPickup;
                        $cars[$counter]['dueatpickup'] = $filter->filter($DueAtPickup);
                        $cars[$counter]['dueatpickupcurrency'] = $filter->filter($CurrencyCode);
                        // Location
                        // $cars[$counter]['special'] = 1;
                        // $cars[$counter]['recommended'] = 1;
                        $counter = $counter + 1;

                    }
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
        $delete->from('quote_session_enterprise');
        $delete->where(array(
            'session_id' => $session_id
        ));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('quote_session_enterprise');
        $insert->values(array(
            'session_id' => $session_id,
            'xmlrequest' => (string) $raw,
            'xmlresult' => (string) $xmlresult,
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