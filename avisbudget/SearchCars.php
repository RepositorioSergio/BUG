<?php
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
//error_log("\r\n COMECOU RIU CARS \r\n", 3, "/srv/www/htdocs/error_log");
$affiliate_id = 0;
$branch_filter = "";
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
//error_log("\r\n riuLoginEmail $riuLoginEmail \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
//error_log("\r\n riuPassword $riuPassword \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
//error_log("\r\n riuServiceURL $riuServiceURL \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select code, name, city, latitude, longitude from carlocation where id=" . $pickup_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $pickup = strtolower($row_settings["code"]);
    $latitude = $row_settings["latitude"];
    $longitude = $row_settings["longitude"];
    $city = $row_settings["city"];
}
//error_log("\r\n city $city \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select code, name, city, latitude, longitude from carlocation where id=" . $dropoff_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $dropoff = strtolower($row_settings["code"]);
    $latitude2 = $row_settings["latitude"];
    $longitude2 = $row_settings["longitude"];
}
//error_log("\r\n latitude $latitude \r\n", 3, "/srv/www/htdocs/error_log");
$db->getDriver()
    ->getConnection()
    ->disconnect();

$dateStart = new DateTime(strftime("%d-%m-%Y", $from));
$dateEnd = new DateTime(strftime("%d-%m-%Y", $to));
$numberofdays = $dateStart->diff($dateEnd)->format('%d');

if ($riuServiceURL != "" and $riuLoginEmail != "") {

    $from = strftime("%Y-%m-%d", $from) . "T" . $pickup_time . ":00";
    $to = strftime("%Y-%m-%d", $to) . "T" . $dropoff_time . ":00";
    
        $raw ='<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"  xmlns:xsi="http://www.w3.org/1999/XMLSchema-instance" xmlns:xsd="http://www.w3.org/1999/XMLSchema">
        <SOAP-ENV:Header>
            <ns:credentials xmlns:ns="http://wsg.avis.com/wsbang/authInAny">
                <ns:userID ns:encodingType="xsd:string">CTMTours</ns:userID>
                <ns:password ns:encodingType="xsd:string">zGkWdCXG8yrw</ns:password>
            </ns:credentials>
            <ns:WSBang-Roadmap xmlns:ns="http://wsg.avis.com/wsbang"/>
        </SOAP-ENV:Header>
        <SOAP-ENV:Body>
            <ns:Request xmlns:ns="http://wsg.avis.com/wsbang">
                <OTA_VehAvailRateRQ MaxResponses="100" ReqRespVersion="small" Version="1.0" xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_VehAvailRateRQ.xsd">
                    <POS>
                        <Source>
                            <RequestorID ID="CTMTours" Type="1"/>
                        </Source>
                        <Source>
                            <RequestorID ID="91502994" Type="5"/>
                        </Source>
                    </POS>
                    <VehAvailRQCore Status="Available">
                        <VehRentalCore PickUpDateTime="2019-04-24T12:00:00" ReturnDateTime="2019-04-25T14:00:00">
                            <PickUpLocation LocationCode="JFK"/>
                            <ReturnLocation LocationCode="JFK"/>
                        </VehRentalCore>
                        <VendorPrefs>
                            <VendorPref CompanyShortName="Avis"/>
                        </VendorPrefs>
                        <VehPrefs>
                        <VehPref AirConditionPref="Preferred" ClassPref="Preferred" TransmissionPref="Preferred" TransmissionType="Automatic" TypePref="Preferred">
                            <VehType VehicleCategory="1" />
                            <VehClass Size="1"/>
                        </VehPref>
                        </VehPrefs>
                    <RateQualifier RateCategory="3" />         
                </VehAvailRQCore>
                <VehAvailRQInfo>
                    <Customer>
                        <Primary>
                            <CitizenCountryName Code="US"/>
                        </Primary>
                    </Customer>
                </VehAvailRQInfo>
            </OTA_VehAvailRateRQ>
        </ns:Request>
        </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>';

        $userpass = 'CTMTours:zGkWdCXG8yrw';
        $login = base64_encode($userpass); 
     
        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "Authorization: Basic " . $login,
            "Content-length: " . strlen($raw)
        );

    $url = 'https://qaservices.carrental.com/wsbang/HTTPSOAPRouter/ws9071';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $xmlresult = curl_exec($ch);
    curl_close($ch);
    //error_log("\r\n RESPONSE $xmlresult \r\n", 3, "/srv/www/htdocs/error_log");

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_avisbudget');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
            'errorline' => 0,
            'errormessage' => $raw,
            'sqlcontext' => $xmlresult,
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
    
    if ($xmlresult != "") {
        $inputDoc = new DOMDocument();
        $inputDoc->loadXML($xmlresult);
        $Envelope = $inputDoc->getElementsByTagName("Envelope");
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $Response = $Body->item(0)->getElementsByTagName("Response");
        $OTA_VehAvailRateRS = $Response->item(0)->getElementsByTagName("OTA_VehAvailRateRS");
        $VehAvailRSCore = $OTA_VehAvailRateRS->item(0)->getElementsByTagName("VehAvailRSCore");
        //VehRentalCore
        $VehRentalCore = $VehAvailRSCore->item(0)->getElementsByTagName("VehRentalCore");
        if ($VehRentalCore->length > 0) {
            $ReturnDateTime = $VehRentalCore->item(0)->getAttribute("ReturnDateTime");
            $PickUpDateTime = $VehRentalCore->item(0)->getAttribute("PickUpDateTime");
        
            $PickUpLocation = $VehRentalCore->item(0)->getElementsByTagName("PickUpLocation");
            if ($PickUpLocation->length > 0) {
                $PickUpLocationCodeContext = $PickUpLocation->item(0)->getAttribute("CodeContext");
                $PickUpLocationLocationCode = $PickUpLocation->item(0)->getAttribute("LocationCode");
            }
        
            $ReturnLocation = $VehRentalCore->item(0)->getElementsByTagName("ReturnLocation");
            if ($ReturnLocation->length > 0) {
                $ReturnLocationCodeContext = $ReturnLocation->item(0)->getAttribute("CodeContext");
                $ReturnLocationLocationCode = $ReturnLocation->item(0)->getAttribute("LocationCode");
            }
        }
        
        //VehVendorAvails
        $VehVendorAvails = $VehAvailRSCore->item(0)->getElementsByTagName("VehVendorAvails");
        $VehVendorAvail = $VehVendorAvails->item(0)->getElementsByTagName("VehVendorAvail");
        if ($VehVendorAvail->length > 0) {
            $Vendor = $VehVendorAvail->item(0)->getElementsByTagName('Vendor');
            if ($Vendor->length > 0) {
                $Vendor = $Vendor->item(0)->nodeValue;
            } else {
                $Vendor = "";
            }

            $Info = $VehVendorAvail->item(0)->getElementsByTagName('Info');
            if ($Info->length > 0) {
                $LocationDetails = $Info->item(0)->getElementsByTagName('LocationDetails');
                if ($LocationDetails->length > 0) {
                    $Code = $LocationDetails->item(0)->getAttribute("Code");
                    $Name = $LocationDetails->item(0)->getAttribute("Name");
                    $CodeContext = $LocationDetails->item(0)->getAttribute("CodeContext");
                    $ExtendedLocationCode = $LocationDetails->item(0)->getAttribute("ExtendedLocationCode");
                    $AtAirport = $LocationDetails->item(0)->getAttribute("AtAirport");

                    $Telephone = $LocationDetails->item(0)->getElementsByTagName('Telephone');
                    if ($Telephone->length > 0) {
                        $PhoneNumber = $Telephone->item(0)->getAttribute("PhoneNumber");
                    } else {
                        $PhoneNumber = "";
                    }

                    $Address = $LocationDetails->item(0)->getElementsByTagName('Address');
                    if ($Address->length > 0) {
                        $StreetNmbr = $Address->item(0)->getElementsByTagName('StreetNmbr');
                        if ($StreetNmbr->length > 0) {
                            $StreetNmbr = $StreetNmbr->item(0)->nodeValue;
                        } else {
                            $StreetNmbr = "";
                        }
                        $CityName = $Address->item(0)->getElementsByTagName('CityName');
                        if ($CityName->length > 0) {
                            $CityName = $CityName->item(0)->nodeValue;
                        } else {
                            $CityName = "";
                        }
                        $PostalCode = $Address->item(0)->getElementsByTagName('PostalCode');
                        if ($PostalCode->length > 0) {
                            $PostalCode = $PostalCode->item(0)->nodeValue;
                        } else {
                            $PostalCode = "";
                        }
                        $StateProv = $Address->item(0)->getElementsByTagName('StateProv');
                        if ($StateProv->length > 0) {
                            $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                            $StateProv = $StateProv->item(0)->nodeValue;
                        } else {
                            $StateCode = "";
                            $StateProv = "";
                        }
                        $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
                        if ($CountryName->length > 0) {
                            $CountryCode = $CountryName->item(0)->getAttribute("Code");
                            $CountryName = $CountryName->item(0)->nodeValue;
                        } else {
                            $CountryCode = "";
                            $CountryName = "";
                        }
                    }
                }
            }
            
            $VehAvails = $VehVendorAvails->item(0)->getElementsByTagName('VehAvails');
            if ($VehAvails->length > 0) {
                $VehAvail = $VehAvails->item(0)->getElementsByTagName('VehAvail');
                if ($VehAvail->length > 0) {
                    for ($i=0; $i < $VehAvail->length; $i++) { 
                        $VehAvailCore = $VehAvail->item($i)->getElementsByTagName('VehAvailCore');
                        if ($VehAvailCore->length > 0) {
                            $Status = $VehAvailCore->item(0)->getAttribute("Status");
                            
                            $Vehicle = $VehAvailCore->item(0)->getElementsByTagName('Vehicle');
                            if ($Vehicle->length > 0) {
                                $TransmissionType = $Vehicle->item(0)->getAttribute("TransmissionType");
                                $AirConditionInd = $Vehicle->item(0)->getAttribute("AirConditionInd");
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
                                    $VehMakeModelCode = $VehMakeModel->item(0)->getAttribute("Code");
                                    $VehMakeModelName = $VehMakeModel->item(0)->getAttribute("Name");
                                } else {
                                    $VehMakeModelCode = "";
                                    $VehMakeModelName = "";
                                }
                            } else {
                                $TransmissionType = "";
                                $AirConditionInd = "";
                            }
                        } else {
                            $Status = "";
                        }

                        $TotalCharge = $VehAvailCore->item(0)->getElementsByTagName('TotalCharge');
                        if ($TotalCharge->length > 0) {
                            $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
                            $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
                            $RateTotalAmount = $TotalCharge->item(0)->getAttribute("RateTotalAmount");
                        } else {
                            $CurrencyCode = "";
                            $EstimatedTotalAmount = "";
                            $RateTotalAmount = "";
                        }
                        
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
                                $RateQualifier2 = $RateQualifier->item(0)->getAttribute("RateQualifier");
                                $RateCategory = $RateQualifier->item(0)->getAttribute("RateCategory");
                            } else {
                                $RateQualifier2 = "";
                                $RateCategory = "";
                            }
                            //error_log("\r\n RateCategory $RateCategory \r\n", 3, "/srv/www/htdocs/error_log");
                            $VehicleCharges = $RentalRate->item(0)->getElementsByTagName('VehicleCharges');
                            if ($VehicleCharges->length > 0) {
                                $VehicleCharge = $VehicleCharges->item(0)->getElementsByTagName('VehicleCharge');
                                if ($VehicleCharge->length > 0) {
                                    for ($j=0; $j < $VehicleCharge->length; $j++) { 
                                        $Purpose = $VehicleCharge->item($j)->getAttribute("Purpose");
                                        $VehicleChargeCurrencyCode = $VehicleCharge->item($j)->getAttribute("CurrencyCode");
                                        $VehicleChargeAmount = $VehicleCharge->item($j)->getAttribute("Amount");
                                        $IncludedInRate = $VehicleCharge->item($j)->getAttribute("IncludedInRate");
                                        $GuaranteedInd = $VehicleCharge->item($j)->getAttribute("GuaranteedInd");
                                        $Description = $VehicleCharge->item($j)->getAttribute("Description");
                                        $TaxInclusive = $VehicleCharge->item($j)->getAttribute("TaxInclusive");

                                        $Calculation = $VehicleCharge->item($j)->getElementsByTagName('Calculation');
                                        if ($Calculation->length > 0) {
                                            $Quantity = $Calculation->item(0)->getAttribute("Quantity");
                                            $UnitName = $Calculation->item(0)->getAttribute("UnitName");
                                        } else {
                                            $Quantity = "";
                                            $UnitName = "";
                                        }

                                        $TaxAmounts = $VehicleCharge->item($j)->getElementsByTagName('TaxAmounts');
                                        if ($TaxAmounts->length > 0) {
                                            $TaxAmount = $TaxAmounts->item(0)->getElementsByTagName('TaxAmount');
                                            if ($TaxAmount->length > 0) {
                                                $TaxAmountCurrencyCode = $TaxAmount->item(0)->getAttribute("CurrencyCode");
                                                $TaxAmountDescription = $TaxAmount->item(0)->getAttribute("Description");
                                                $Total = $TaxAmount->item(0)->getAttribute("Total");
                                            } else {
                                                $TaxAmountCurrencyCode = "";
                                                $TaxAmountDescription = "";
                                                $Total = "";
                                            }
                                        }    
                                    }
                                }
                            }

                            $urlpicture = 'http://www.avis.com/car-rental/images/global/en/rentersguide/vehicle_guide/';
                            
                            $cars[$counter]['id'] = $counter;
                            $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-13-" . $counter;
                            $cars[$counter]['vendorpicture'] = "https://world-wide-web-servers.com/car/vendors/avis.png";
                            $cars[$counter]['vendorcode'] = $Vendor;
                            $cars[$counter]['vendor'] = $Vendor;
                            $cars[$counter]['vendorshortname'] = $Vendor;
                            $cars[$counter]['size'] = $Size;
                            $cars[$counter]['doors'] = $DoorCount;
                            $cars[$counter]['aircondition'] = $AirConditionInd;
                            $cars[$counter]['transmission'] = $TransmissionType;
                            $cars[$counter]['bags'] = $BaggageQuantity;
                            $cars[$counter]['status'] = $Status;
                            $cars[$counter]['from'] = $from;
                            $cars[$counter]['to'] = $to;
                            $cars[$counter]['pickup'] = ucwords(strtolower($PickUpLocationLocationCode));
                            $cars[$counter]['dropoff'] = ucwords(strtolower($NameDL));
                            $cars[$counter]['class'] = $VehMakeModelCode;
                            $cars[$counter]['currency'] = $CurrencyCode;
                            $cars[$counter]['productId'] = $productId;
                            $cars[$counter]['programId'] = $CarProgramId;
                            $cars[$counter]['name'] = $VehMakeModelName;
                            $cars[$counter]['picture'] = $urlpicture . '' .  $PictureURL;
                            $cars[$counter]['programname'] = "";
                            $cars[$counter]['coverage'] = $coverage;
                            $cars[$counter]['netcurrency'] = $CurrencyCode;
                            $cars[$counter]['netprice'] = $EstimatedTotalAmount;
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
                            $dailytotal = $EstimatedTotalAmount / $numberofdays;
                            $dailytotal = number_format($dailytotal, 2, ".", "");
                            //$minPrice = number_format($minPrice, 2, ".", "");
                            $cars[$counter]['currency'] = $scurrency;
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
            $delete->from('quote_session_avisbudget');
            $delete->where(array(
                'session_id' => $session_id
            ));
            $statement = $sql->prepareStatementForSqlObject($delete);
            $results = $statement->execute();
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('quote_session_avisbudget');
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
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>