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
$sql = "select value from settings where name='enablecartrawler' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_cartrawler = $affiliate_id;
} else {
    $affiliate_id_cartrawler = 0;
}
$sql = "select value from settings where name='CarTrawlerTarget' and affiliate_id=$affiliate_id_cartrawler";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerTarget = $row_settings["value"];
} else {
    $CarTrawlerTarget = "Test";
}
$sql = "select value from settings where name='CarTrawlerLanguage' and affiliate_id=$affiliate_id_cartrawler";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerLanguage = $row_settings["value"];
} else {
    $CarTrawlerLanguage = "52";
}
$sql = "select value from settings where name='CarTrawlerClientId' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerClientId = $row_settings["value"];
}
$sql = "select value from settings where name='CarTrawlerCurrency' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerCurrency = $row_settings["value"];
} else {
    $CarTrawlerCurrency = "52";
}
$sql = "select value from settings where name='CarTrawlerServiceURL' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='CarTrawlerSecureServiceURL' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerSecureServiceURL = $row_settings["value"];
}
$sql = "select value from settings where name='CarTrawlerMarkup' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerMarkup = (double) $row_settings["value"];
}
$sql = "select value from settings where name='CarTrawlerIDContext' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerIDContext = $row_settings["value"];
}
$sql = "select value from settings where name='CarTrawlerType' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerType = $row_settings["value"];
}
$sql = "select value from settings where name='CarTrawlerAvailability' and affiliate_id=$affiliate_id_cartrawler";
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $CarTrawlerAvailability = $row_settings["value"];
} else {
    $CarTrawlerAvailability = "All";
}
$sql = "select code, name, city from carlocation where id=" . $pickup_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $pickup = strtolower($row_settings["code"]);
}
$sql = "select code, name, city from carlocation where id=" . $dropoff_id;
$statement2 = $db->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $dropoff = strtolower($row_settings["code"]);
}

$dateStart = new DateTime(strftime("%d-%m-%Y", $from));
$dateEnd = new DateTime(strftime("%d-%m-%Y", $to));
$numberofdays = $dateStart->diff($dateEnd)->format('%d');

if ($CarTrawlerClientId != "" and $CarTrawlerServiceURL != "" and $pickup != "" and $dropoff != "") {
    $requestData = '<?xml version="1.0" encoding="UTF-8"?>';
    $requestData .= '<OTA_VehAvailRateRQ xmlns="http://www.opentravel.org/OTA/2003/05" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.opentravel.org/OTA/2003/05 OTA_VehAvailRateRQ.xsd" Target="' . $CarTrawlerTarget . '" Version="1.005" PrimaryLangID="' . $CarTrawlerLanguage . '">';
    $requestData .= '<POS>';
    $requestData .= '<Source ISOCurrency="' . $CarTrawlerCurrency . '">';
    $requestData .= '<RequestorID Type="' . $CarTrawlerType . '" ID="' . $CarTrawlerClientId . '" ID_Context="' . $CarTrawlerIDContext . '" />';
    $requestData .= '</Source>';
    $requestData .= '</POS>';
    $requestData .= '<VehAvailRQCore Status="' . $CarTrawlerAvailability . '">';
    $from = strftime("%Y-%m-%d", $from) . "T" . $pickup_time . ":00";
    $to = strftime("%Y-%m-%d", $to) . "T" . $dropoff_time . ":00";
    $requestData .= '<VehRentalCore PickUpDateTime="' . $from . '" ReturnDateTime="' . $to . '">';
    $requestData .= '<PickUpLocation CodeContext="CARTRAWLER" LocationCode="' . $pickup . '" />';
    $requestData .= '<ReturnLocation CodeContext="CARTRAWLER" LocationCode="' . $dropoff . '" />';
    $requestData .= '</VehRentalCore>';
    $requestData .= '<DriverType Age="' . $driversage . '"/>';
    $requestData .= '</VehAvailRQCore>';
    $requestData .= '<VehAvailRQInfo PassengerQty="1">';
    $requestData .= '<Customer>';
    $requestData .= '<Primary>';
    $requestData .= '<CitizenCountryName Code="' . $residence_iso . '" />';
    $requestData .= '</Primary>';
    $requestData .= '</Customer>';
    $requestData .= '<TPA_Extensions>';
    $requestData .= '<ConsumerIP>' . $ipaddress . '</ConsumerIP>';
    $requestData .= '</TPA_Extensions>';
    $requestData .= '</VehAvailRQInfo>';
    $requestData .= '</OTA_VehAvailRateRQ>';
    // error_log("\r\nEnd Point: " . $CarTrawlerServiceURL . "\r\n", 3, "/srv/www/htdocs/error_log");
    // error_log("\r\nXML Request: " . $requestData . "\r\n", 3, "/srv/www/htdocs/error_log");
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $CarTrawlerServiceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Encoding: UTF-8",
        "Accept-Encoding: gzip,deflate"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    curl_close($ch);
    //error_log("\r\nXML Result: " . $response . "\r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('log_cartrawler');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchCars.php',
            'errorline' => 0,
            'errormessage' => $CarTrawlerServiceURL . $requestData,
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
        $OTA_VehAvailRateRS = $inputDoc->getElementsByTagName("OTA_VehAvailRateRS");
        $VehAvailRSCore = $OTA_VehAvailRateRS->item(0)->getElementsByTagName("VehAvailRSCore");
        if ($VehAvailRSCore->length > 0) {
            $dataset = $VehAvailRSCore->item(0)->getElementsByTagName("VehRentalCore");
            if ($dataset->length > 0) {
                $checkout_date = $dataset->item(0)->getAttribute("ReturnDateTime");
                $checkin_date = $dataset->item(0)->getAttribute("PickUpDateTime");
                $checkin = $dataset->item(0)->getElementsByTagName("PickUpLocation");
                if ($checkin->length > 0) {
                    $checkin_CodeContext = $checkin->item(0)->getAttribute("CodeContext");
                    $checkin_LocationCode = $checkin->item(0)->getAttribute("LocationCode");
                    $pickup_at_location = $checkin->item(0)->getAttribute("Name");
                }
                $checkout = $dataset->item(0)->getElementsByTagName("ReturnLocation");
                if ($checkout->length > 0) {
                    $checkout_CodeContext = $checkout->item(0)->getAttribute("CodeContext");
                    $checkout_LocationCode = $checkout->item(0)->getAttribute("LocationCode");
                    $dropoff_at_location = $checkout->item(0)->getAttribute("Name");
                }
            }
            
            $dataset = $VehAvailRSCore->item(0)->getElementsByTagName("VehVendorAvails");
            if ($dataset->length > 0) {
                $dataset = $dataset->item(0)->getElementsByTagName("VehVendorAvail");
                if ($dataset->length > 0) {
                    for ($r = 0; $r < $dataset->length; $r ++) {
                        $Vendor = $dataset->item($r)->getElementsByTagName("Vendor");
                        if ($Vendor->length > 0) {
                            $VendorCode = $Vendor->item(0)->getAttribute("Code");
                            $VendorCodeContext = $Vendor->item(0)->getAttribute("CodeContext");
                            $VendorCompanyShortName = $Vendor->item(0)->getAttribute("CompanyShortName");
                            $VendorDivision = $Vendor->item(0)->getAttribute("Division");
                        } else {
                            $VendorCode = "";
                            $VendorCodeContext = "";
                            $VendorCompanyShortName = "";
                            $VendorDivision = "";
                        }

                        $Info = $dataset->item($r)->getElementsByTagName("Info");
                        if ($Info->length > 0) {
                            $TPA_Extensions_VendorPictureURL = $Info->item(0)->getElementsByTagName("VendorPictureURL");
                            if ($TPA_Extensions_VendorPictureURL->length > 0) {
                                $TPA_Extensions_VendorPictureURLUIToken = $TPA_Extensions_VendorPictureURL->item(0)->getAttribute("UIToken");
                                $TPA_Extensions_VendorPictureURL = $TPA_Extensions_VendorPictureURL->item(0)->nodeValue;
                            } else {
                                $TPA_Extensions_VendorPictureURLUIToken = "";
                                $TPA_Extensions_VendorPictureURL = "";
                            }
                            $TPA_Extensions_Ranking = $Info->item(0)->getElementsByTagName("Ranking");
                            if ($TPA_Extensions_Ranking->length > 0) {
                                $TPA_Extensions_Ranking = $TPA_Extensions_Ranking->item(0)->getAttribute("index");
                            } else {
                                $TPA_Extensions_Ranking = "";
                            }
                            $LocationDetails = $Info->item(0)->getElementsByTagName("LocationDetails");
                            if ($LocationDetails->length > 0) {
                                $LocationDetails_Code = $LocationDetails->item(0)->getAttribute("Code");
                                $LocationDetails_Name = $LocationDetails->item(0)->getAttribute("Name");
                                $LocationDetails_AtAirport = $LocationDetails->item(0)->getAttribute("AtAirport");
                                $Address = $LocationDetails->item(0)->getElementsByTagName("Address");
                                if ($Address->length > 0) {
                                    $Address_Remark = $Address->item(0)->getAttribute("Remark");
                                    $Address_AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                                    if ($Address_AddressLine->length > 0) {
                                        $Address_AddressLine = $Address_AddressLine->item(0)->nodeValue;
                                    } else {
                                        $Address_AddressLine = "";
                                    }
                                    $Address_CountryName = $Address->item(0)->getElementsByTagName("CountryName");
                                    if ($Address_CountryName->length > 0) {
                                        $Address_CountryName = $Address_CountryName->item(0)->getAttribute("Code");
                                    } else {
                                        $Address_CountryName = "";
                                    }
                                } else {
                                    $Address_Remark = "";
                                    $Address_AddressLine = "";
                                    $Address_CountryName = "";
                                }
                                $Telephone = $LocationDetails->item(0)->getElementsByTagName("Telephone");
                                if ($Telephone->length > 0) {
                                    $Telephone = $Telephone->item(0)->getAttribute("PhoneNumber");
                                } else {
                                    $Telephone = "";
                                }
                                $AdditionalInfo = $LocationDetails->item(0)->getElementsByTagName("AdditionalInfo");
                                if ($AdditionalInfo->length > 0) {
                                    $VehRentLocInfos = $AdditionalInfo->item(0)->getElementsByTagName("VehRentLocInfos");
                                    if ($VehRentLocInfos->length > 0) {
                                        $VehRentLocInfo = $VehRentLocInfos->item(0)->getElementsByTagName("VehRentLocInfo");
                                        if ($VehRentLocInfo->length > 0) {
                                            $VehRentLocInfo_Type = $VehRentLocInfo->item(0)->getAttribute("Type");
                                        } else {
                                            $VehRentLocInfo_Type = "";
                                        }
                                    } else {
                                        $VehRentLocInfo_Type = "";
                                    }
                                    $CounterLocation = $AdditionalInfo->item(0)->getElementsByTagName("CounterLocation");
                                    if ($CounterLocation->length > 0) {
                                        $CounterLocation_Location = $CounterLocation->item(0)->getAttribute("Location");
                                    } else {
                                        $CounterLocation = "";
                                        $CounterLocation_Location = "";
                                    }
                                } else {
                                    $AdditionalInfo = "";
                                    $VehRentLocInfos = "";
                                    $VehRentLocInfo_Type = "";
                                    $CounterLocation = "";
                                    $CounterLocation_Location = "";
                                }
                            } else {
                                $LocationDetails_Code = "";
                                $LocationDetails_Name = "";
                                $LocationDetails_AtAirport = "";
                                $Address_Remark = "";
                                $Address_AddressLine = "";
                                $Address_CountryName = "";
                                $Telephone = "";
                                $AdditionalInfo = "";
                                $VehRentLocInfos = "";
                                $VehRentLocInfo_Type = "";
                                $CounterLocation = "";
                                $CounterLocation_Location = "";
                            }
                            if ($LocationDetails->length > 1) {
                                // Return
                                $LocationDetailsReturn_Code = $LocationDetails->item(1)->getAttribute("Code");
                                $LocationDetailsReturn_Name = $LocationDetails->item(1)->getAttribute("Name");
                                $LocationDetailsReturn_AtAirport = $LocationDetails->item(1)->getAttribute("AtAirport");
                                $Address = $LocationDetails->item(1)->getElementsByTagName("Address");
                                if ($Address->length > 0) {
                                    $Address_Return_Remark = $Address->item(0)->getAttribute("Remark");
                                    $Address_Return_AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                                    if ($Address_Return_AddressLine->length > 0) {
                                        $Address_Return_AddressLine = $Address_Return_AddressLine->item(0)->nodeValue;
                                    } else {
                                        $Address_Return_AddressLine = "";
                                    }
                                    $Address_Return_CountryName = $Address->item(0)->getElementsByTagName("CountryName");
                                    if ($Address_Return_CountryName->length > 0) {
                                        $Address_Return_CountryName = $Address_Return_CountryName->item(0)->getAttribute("Code");
                                    } else {
                                        $Address_Return_CountryName = "";
                                    }
                                } else {
                                    $Address_Return_Remark = "";
                                    $Address_Return_AddressLine = "";
                                    $Address_Return_CountryName = "";
                                }
                                $Telephone_Return = $LocationDetails->item(1)->getElementsByTagName("Telephone");
                                if ($Telephone_Return->length > 0) {
                                    $Telephone_Return = $Telephone_Return->item(0)->getAttribute("PhoneNumber");
                                } else {
                                    $Telephone_Return = "";
                                }
                                $AdditionalInfo_Return = $LocationDetails->item(1)->getElementsByTagName("AdditionalInfo");
                                if ($AdditionalInfo_Return->length > 0) {
                                    $VehRentLocInfos_Return = $AdditionalInfo_Return->item(0)->getElementsByTagName("VehRentLocInfos");
                                    if ($VehRentLocInfos_Return->length > 0) {
                                        $VehRentLocInfo = $VehRentLocInfos_Return->item(0)->getElementsByTagName("VehRentLocInfo");
                                        if ($VehRentLocInfo->length > 0) {
                                            $VehRentLocInfo_Return_Type = $VehRentLocInfo->item(0)->getAttribute("Type");
                                        } else {
                                            $VehRentLocInfo_Return_Type = "";
                                        }
                                    } else {
                                        $VehRentLocInfo_Return_Type = "";
                                    }
                                    $CounterLocation_Return = $AdditionalInfo_Return->item(0)->getElementsByTagName("CounterLocation");
                                    if ($CounterLocation_Return->length > 0) {
                                        $CounterLocation_Return_Location = $CounterLocation_Return->item(0)->getAttribute("Location");
                                    } else {
                                        $CounterLocation_Return = "";
                                        $CounterLocation_Return_Location = "";
                                    }
                                } else {
                                    $LocationDetailsReturn_Code = "";
                                    $LocationDetailsReturn_Name = "";
                                    $LocationDetailsReturn_AtAirport = "";
                                    $Address_Return_Remark = "";
                                    $Address_Return_AddressLine = "";
                                    $Address_Return_CountryName = "";
                                    $Telephone_Return = "";
                                    $AdditionalInfo_Return = "";
                                    $VehRentLocInfos_Return = "";
                                    $VehRentLocInfo_Return_Type = "";
                                    $CounterLocation_Return = "";
                                    $CounterLocation_Return_Location = "";
                                }
                            } else {
                                // Pickup & Drop off Same Location
                                $LocationDetailsReturn_Code = $LocationDetails_Code;
                                $LocationDetailsReturn_Name = $LocationDetails_Name;
                                $LocationDetailsReturn_AtAirport = $LocationDetails_AtAirport;
                                $Address_Return_Remark = $Address_Remark;
                                $Address_Return_AddressLine = $Address_AddressLine;
                                $Address_Return_CountryName = $Address_CountryName;
                                $Telephone_Return = $Telephone;
                                $AdditionalInfo_Return = $AdditionalInfo;
                                $VehRentLocInfos_Return = $VehRentLocInfos;
                                $VehRentLocInfo_Return_Type = $VehRentLocInfo_Type;
                                $CounterLocation_Return = $CounterLocation;
                                $CounterLocation_Return_Location = $CounterLocation_Location;
                            }
                        } else {
                            $LocationDetails_Code = "";
                            $LocationDetails_Name = "";
                            $LocationDetails_AtAirport = "";
                            $Address_Remark = "";
                            $Address_AddressLine = "";
                            $Address_CountryName = "";
                            $Telephone = "";
                            $AdditionalInfo = "";
                            $VehRentLocInfos = "";
                            $VehRentLocInfo_Type = "";
                            $CounterLocation = "";
                            $CounterLocation_Location = "";
                            $LocationDetailsReturn_Code = "";
                            $LocationDetailsReturn_Name = "";
                            $LocationDetailsReturn_AtAirport = "";
                            $Address_Return_Remark = "";
                            $Address_Return_AddressLine = "";
                            $Address_Return_CountryName = "";
                            $Telephone_Return = "";
                            $AdditionalInfo_Return = "";
                            $VehRentLocInfos_Return = "";
                            $VehRentLocInfo_Return_Type = "";
                            $CounterLocation_Return = "";
                            $CounterLocation_Return_Location = "";
                        }
                        $VehAvails = $dataset->item($r)->getElementsByTagName("VehAvails");
                        $VehAvail = $VehAvails->item($z)->getElementsByTagName("VehAvail");
                        for ($k = 0; $k < $VehAvail->length; $k ++) {
                            $VehAvailCore = $VehAvail->item($k)->getElementsByTagName("VehAvailCore");
                            if ($VehAvailCore->length > 0) {
                                $Status = $VehAvailCore->item(0)->getAttribute("Status");
                                $Vehicle = $VehAvailCore->item(0)->getElementsByTagName("Vehicle");
                                if ($Vehicle->length > 0) {
                                    $AirConditionInd = $Vehicle->item(0)->getAttribute("AirConditionInd");
                                    $TransmissionType = $Vehicle->item(0)->getAttribute("TransmissionType");
                                    $FuelType = $Vehicle->item(0)->getAttribute("FuelType");
                                    $DriveType = $Vehicle->item(0)->getAttribute("DriveType");
                                    $PassengerQuantity = $Vehicle->item(0)->getAttribute("PassengerQuantity");
                                    $BaggageQuantity = $Vehicle->item(0)->getAttribute("BaggageQuantity");
                                    $Code = $Vehicle->item(0)->getAttribute("Code");
                                    $CodeContext = $Vehicle->item(0)->getAttribute("CodeContext");
                                    $VehType = $Vehicle->item(0)->getElementsByTagName("VehType");
                                    if ($VehType->length > 0) {
                                        $VehicleCategory = $VehType->item(0)->getAttribute("VehicleCategory");
                                        $DoorCount = $VehType->item(0)->getAttribute("DoorCount");
                                    } else {
                                        $VehicleCategory = "";
                                        $DoorCount = "";
                                    }
                                    $VehClass = $Vehicle->item(0)->getElementsByTagName("VehClass");
                                    if ($VehClass->length > 0) {
                                        $Size = $VehClass->item(0)->getAttribute("Size");
                                    } else {
                                        $Size = "";
                                    }
                                    $VehMakeModel = $Vehicle->item(0)->getElementsByTagName("VehMakeModel");
                                    if ($VehMakeModel->length > 0) {
                                        $Name = $VehMakeModel->item(0)->getAttribute("Name");
                                        $VehMakeModelCode = $VehMakeModel->item(0)->getAttribute("Code");
                                    } else {
                                        $Name = "";
                                        $VehMakeModelCode = "";
                                    }
                                    $PictureURL = $Vehicle->item(0)->getElementsByTagName("PictureURL");
                                    if ($PictureURL->length > 0) {
                                        $PictureURL = $PictureURL->item(0)->nodeValue;
                                    } else {
                                        $PictureURL = "";
                                    }
                                    $VehIdentity = $Vehicle->item(0)->getElementsByTagName("VehIdentity");
                                    if ($VehIdentity->length > 0) {
                                        $VehicleAssetNumber = $VehIdentity->item(0)->getAttribute("VehicleAssetNumber");
                                    } else {
                                        $VehicleAssetNumber = "";
                                    }
                                } else {
                                    $AirConditionInd = "";
                                    $TransmissionType = "";
                                    $FuelType = "";
                                    $DriveType = "";
                                    $PassengerQuantity = "";
                                    $BaggageQuantity = "";
                                    $Code = "";
                                    $CodeContext = "";
                                    $VehicleCategory = "";
                                    $DoorCount = "";
                                    $Size = "";
                                    $Name = "";
                                    $VehMakeModelCode = "";
                                    $PictureURL = "";
                                    $VehicleAssetNumber = "";
                                }
                                $TotalCharge = $VehAvailCore->item(0)->getElementsByTagName("TotalCharge");
                                if ($TotalCharge->length > 0) {
                                    $RateTotalAmount = $TotalCharge->item(0)->getAttribute("RateTotalAmount");
                                    $EstimatedTotalAmount = $TotalCharge->item(0)->getAttribute("EstimatedTotalAmount");
                                    $CurrencyCode = $TotalCharge->item(0)->getAttribute("CurrencyCode");
                                } else {
                                    $RateTotalAmount = 0;
                                    $EstimatedTotalAmount = 0;
                                    $CurrencyCode = "";
                                }
                                $FreeGPS = 0;
                                $FreeAdditionalDriver = 0;
                                $PricedEquips = $VehAvailCore->item(0)->getElementsByTagName("PricedEquips");
                                if ($PricedEquips->length > 0) {
                                    $PricedEquip = $PricedEquips->item(0)->getElementsByTagName("PricedEquip");
                                    $PricedEquipsCounter = 0;
                                    for ($kAux = 0; $kAux < $PricedEquip->length; $kAux ++) {
                                        $Equipment = $PricedEquip->item($kAux)->getElementsByTagName("Equipment");
                                        if ($Equipment->length > 0) {
                                            $EquipType = $Equipment->item(0)->getAttribute("EquipType");
                                            $EquipTypeDescription = $Equipment->item(0)->getElementsByTagName("Description");
                                            if ($EquipTypeDescription->length > 0) {
                                                $EquipTypeDescription = $EquipTypeDescription->item(0)->nodeValue;
                                            } else {
                                                $EquipTypeDescription = "";
                                            }
                                        }
                                        $Charge = $PricedEquip->item($kAux)->getElementsByTagName("Charge");
                                        if ($Charge->length > 0) {
                                            $ChargeAmount = $Charge->item(0)->getAttribute("Amount");
                                            $ChargeCurrencyCode = $Charge->item(0)->getAttribute("CurrencyCode");
                                            $ChargeTaxInclusive = $Charge->item(0)->getAttribute("TaxInclusive");
                                            $ChargeIncludedInRate = $Charge->item(0)->getAttribute("IncludedInRate");
                                        } else {
                                            $ChargeAmount = "";
                                            $ChargeCurrencyCode = "";
                                            $ChargeTaxInclusive = "";
                                            $ChargeIncludedInRate = "";
                                        }
                                        if ($EquipType == 13) {
                                            if ($ChargeAmount == "") {
                                                $FreeGPS = 1;
                                            }
                                        }
                                        if ($EquipType == "101.EQP") {
                                            if ($ChargeAmount == "") {
                                                $FreeAdditionalDriver = 1;
                                            }
                                        }
                                        
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['Prepaid'] = "False";
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipType'] = $EquipType;
                                        if ($EquipType == "1") {
                                            // Mobile Phone
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "mobilephone.png";
                                        } elseif ($EquipType == "3") {
                                            // Luggage Rack
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "luggagerack.png";
                                        } elseif ($EquipType == "4") {
                                            // Ski Rack
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "skirack.png";
                                        } elseif ($EquipType == "7") {
                                            // Infant Child Seat
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "infantchildseat.png";
                                        } elseif ($EquipType == "8") {
                                            // Child Toddler Seat
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "childtoddlerseat.png";
                                        } elseif ($EquipType == "9") {
                                            // Booster Seat
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "boosterseat.png";
                                        } elseif ($EquipType == "10") {
                                            // Snow Chains
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "snowchains.png";
                                        } elseif ($EquipType == "14") {
                                            // Snow Tyres
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "snowtyres.png";
                                        } elseif ($EquipType == "13") {
                                            // GPS
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "gpssatellitenavigationalsystem.png";
                                        } elseif ($EquipType == "30") {
                                            // Winter Package
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "winterpackage.png";
                                        } elseif ($EquipType == "34") {
                                            // Navigational Phone
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "navigationalphone.png";
                                        } elseif ($EquipType == "101.EQP") {
                                            // Additional Driver
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "additionaldriver.png";
                                        } elseif ($EquipType == "102.EQP") {
                                            // GPS
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "gpssatellitenavigationalsystem.png";
                                        } else {
                                            $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeImage'] = "";
                                        }
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['EquipTypeName'] = $EquipType;
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['Amount'] = $ChargeAmount;
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['TaxInclusive'] = $ChargeTaxInclusive;
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['Description'] = $EquipTypeDescription;
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['CurrencyCode'] = $ChargeCurrencyCode;
                                        if ($ChargeIncludedInRate == "false") {
                                            $ChargeIncludedInRate = "False";
                                        }
                                        if ($ChargeIncludedInRate == "true") {
                                            $ChargeIncludedInRate = "True";
                                        }
                                        $cars[$counter]['PricedEquips'][$PricedEquipsCounter]['IncludedInRate'] = $ChargeIncludedInRate;
                                        $PricedEquipsCounter = $PricedEquipsCounter + 1;
                                    }
                                }
                                $RentalRate = $VehAvailCore->item(0)->getElementsByTagName("RentalRate");
                                if ($RentalRate->length > 0) {
                                    $VehicleChargesCounter = 0;
                                    $VehicleCharges = $RentalRate->item(0)->getElementsByTagName("VehicleCharges");
                                    for ($kAux = 0; $kAux < $VehicleCharges->length; $kAux ++) {
                                        $VehicleCharge = $VehicleCharges->item($kAux)->getElementsByTagName("VehicleCharge");
                                        if ($VehicleCharge->length > 0) {
                                            $VehicleChargeDescription = $VehicleCharge->item(0)->getAttribute("Description");
                                            $VehicleChargeTaxInclusive = $VehicleCharge->item(0)->getAttribute("TaxInclusive");
                                            $VehicleChargeIncludedInRate = $VehicleCharge->item(0)->getAttribute("IncludedInRate");
                                            $VehicleChargePurpose = $VehicleCharge->item(0)->getAttribute("Purpose");
                                            
                                            $vehicle[$counter]['VehicleCharges'][$VehicleChargesCounter]['Amount'] = 0;
                                            $vehicle[$counter]['VehicleCharges'][$VehicleChargesCounter]['Description'] = $VehicleChargeDescription;
                                            $vehicle[$counter]['VehicleCharges'][$VehicleChargesCounter]['Purpose'] = $VehicleChargePurpose;
                                            $vehicle[$counter]['VehicleCharges'][$VehicleChargesCounter]['CurrencyCode'] = "";
                                            $vehicle[$counter]['VehicleCharges'][$VehicleChargesCounter]['IncludedInRate'] = $VehicleChargeIncludedInRate;
                                            $vehicle[$counter]['VehicleCharges'][$VehicleChargesCounter]['TaxInclusive'] = $VehicleChargeTaxInclusive;
                                            $VehicleChargesCounter = $VehicleChargesCounter + 1;
                                        }
                                    }
                                    $RateQualifier = $RentalRate->item(0)->getElementsByTagName("RateQualifier");
                                    if ($RateQualifier->length > 0) {
                                        $RateQualifier_RateQualifier = $RateQualifier->item(0)->getAttribute("RateQualifier");
                                        $RateQualifier_PromotionCode = $RateQualifier->item(0)->getAttribute("PromotionCode");
                                    } else {
                                        $RateQualifier_RateQualifier = "";
                                        $RateQualifier_PromotionCode = "";
                                    }
                                    $RateDistance = $RentalRate->item(0)->getElementsByTagName("RateDistance");
                                    if ($RateDistance->length > 0) {
                                        $RateDistance_Unlimited = $RateDistance->item(0)->getAttribute("Unlimited");
                                    }
                                    switch ($RateQualifier_PromotionCode) {
                                        case "INCLUSIVE_WITH_GPS":
                                            $cars[$counter]['Requirements'][0]['Title'] = utf8_encode(gettext("Inclusive with GPS"));
                                            $cars[$counter]['Requirements'][0]['Text'] = "";
                                            break;
                                        case "INCLUSIVE_NO_EXCESS":
                                            $cars[$counter]['Requirements'][0]['Title'] = utf8_encode(gettext("Inclusive No Excess"));
                                            $cars[$counter]['Requirements'][0]['Text'] = "";
                                            break;
                                        case "DIAMOND":
                                            $cars[$counter]['Requirements'][0]['Title'] = utf8_encode(gettext("Diamond"));
                                            $cars[$counter]['Requirements'][0]['Text'] = "";
                                            break;
                                        case "PLATINUM":
                                            $cars[$counter]['Requirements'][0]['Title'] = utf8_encode(gettext("Platinum"));
                                            $cars[$counter]['Requirements'][0]['Text'] = "";
                                            break;
                                        case "GOLD":
                                            $cars[$counter]['Requirements'][0]['Title'] = utf8_encode(gettext("Gold"));
                                            $cars[$counter]['Requirements'][0]['Text'] = "";
                                            break;
                                        default:
                                            break;
                                    }
                                    /* error_log("\r\nRateQualifier_RateQualifier : " . $RateQualifier_RateQualifier . "\r\n", 3, "/srv/www/htdocs/error_log");
                                    error_log("\r\nRateQualifier_PromotionCode : " . $RateQualifier_PromotionCode . "\r\n", 3, "/srv/www/htdocs/error_log");
                                    error_log("\r\nRateDistance_Unlimited : " . $RateDistance_Unlimited . "\r\n", 3, "/srv/www/htdocs/error_log"); */
                                    switch ($RateQualifier_RateQualifier) {
                                        case "PREPAID-IN":
                                            if ($cars[$counter]['Requirements'][0]['Text'] == "") {
                                                $cars[$counter]['Requirements'][0]['Text'] = utf8_encode(gettext("Fully Prepaid, Inclusive product. This means the full cost is paid up front (though some fees may be paid on arrival)."));
                                            } else {
                                                $cars[$counter]['Requirements'][0]['Text'] = $cars[$counter]['Requirements'][0]['Text'] . "<br/><br/>" . utf8_encode(gettext("Fully Prepaid, Inclusive product. This means the full cost is paid up front (though some fees may be paid on arrival)."));
                                            }
                                            break;
                                        case "PREPAID-EX":
                                            if ($cars[$counter]['Requirements'][0]['Text'] == "") {
                                                $cars[$counter]['Requirements'][0]['Text'] = utf8_encode(gettext("Fully-prepaid, Exclusive product. This means the full cost is paid up front (though some fees may be paid on arrival)."));
                                            } else {
                                                $cars[$counter]['Requirements'][0]['Text'] = $cars[$counter]['Requirements'][0]['Text'] . "<br/><br/>" . utf8_encode(gettext("Fully-prepaid, Exclusive product. This means the full cost is paid up front (though some fees may be paid on arrival)."));
                                            }
                                            break;
                                        case "POSTPAID-IN":
                                            if ($cars[$counter]['Requirements'][0]['Text'] == "") {
                                                $cars[$counter]['Requirements'][0]['Text'] = utf8_encode(gettext("Fully-postpaid, Inclusive product. This means the full cost is paid when picking up the car."));
                                            } else {
                                                $cars[$counter]['Requirements'][0]['Text'] = $cars[$counter]['Requirements'][0]['Text'] . "<br/><br/>" . utf8_encode(gettext("Fully-postpaid, Inclusive product. This means the full cost is paid when picking up the car."));
                                            }
                                            break;
                                        case "POSTPAID-EX":
                                            if ($cars[$counter]['Requirements'][0]['Text'] == "") {
                                                $cars[$counter]['Requirements'][0]['Text'] = utf8_encode(gettext("Fully-postpaid, Exclusive product. This means the full cost is paid when picking up the car."));
                                            } else {
                                                $cars[$counter]['Requirements'][0]['Text'] = $cars[$counter]['Requirements'][0]['Text'] . "<br/><br/>" . utf8_encode(gettext("Fully-postpaid, Exclusive product. This means the full cost is paid when picking up the car."));
                                            }
                                            break;
                                        case "PARTPAID-IN":
                                            if ($cars[$counter]['Requirements'][0]['Text'] == "") {
                                                $cars[$counter]['Requirements'][0]['Text'] = utf8_encode(gettext("Partpaid (deposit), Inclusive product. This means a deposit is paid up front, with the remainder being paid when pickup up the car."));
                                            } else {
                                                $cars[$counter]['Requirements'][0]['Text'] = $cars[$counter]['Requirements'][0]['Text'] . "<br/><br/>" . utf8_encode(gettext("Partpaid (deposit), Inclusive product. This means a deposit is paid up front, with the remainder being paid when pickup up the car."));
                                            }
                                            break;
                                        case "PARTPAID-EX":
                                            if ($cars[$counter]['Requirements'][0]['Text'] == "") {
                                                $cars[$counter]['Requirements'][0]['Text'] = utf8_encode(gettext("Partpaid (deposit), Exclusive product. This means a deposit is paid up front, with the remainder being paid when pickup up the car."));
                                            } else {
                                                $cars[$counter]['Requirements'][0]['Text'] = $cars[$counter]['Requirements'][0]['Text'] . "<br/><br/>" . utf8_encode(gettext("Partpaid (deposit), Exclusive product. This means a deposit is paid up front, with the remainder being paid when pickup up the car."));
                                            }
                                            break;
                                        default:
                                            break;
                                    }
                                }
                                
                                $FeesArray = array();
                                $Fees = $VehAvailCore->item(0)->getElementsByTagName("Fees");
                                if ($Fees->length > 0) {
                                    $FeeCount = 0;
                                    $Fees = $Fees->item(0)->getElementsByTagName("Fee");
                                    for ($kAux = 0; $kAux < $Fees->length; $kAux ++) {
                                        $FeeAmount = $Fees->item($kAux)->getAttribute("Amount");
                                        $FeeCurrencyCode = $Fees->item($kAux)->getAttribute("CurrencyCode");
                                        $FeePurpose = $Fees->item($kAux)->getAttribute("Purpose");
                                        
                                        /* error_log("\r\nFeeAmount : " . $FeeAmount . "\r\n", 3, "/srv/www/htdocs/error_log");
                                        error_log("\r\nFeeCurrencyCode : " . $FeeCurrencyCode . "\r\n", 3, "/srv/www/htdocs/error_log");
                                        error_log("\r\nFeePurpose : " . $FeePurpose . "\r\n", 3, "/srv/www/htdocs/error_log"); */
                                        
                                        $FeesArray[$FeeCount]['FeeAmount'] = $FeeAmount;
                                        $FeesArray[$FeeCount]['FeeCurrencyCode'] = $FeeCurrencyCode;
                                        $FeesArray[$FeeCount]['FeePurpose'] = $FeePurpose;
                                        $FeeCount = $FeeCount + 1;
                                    }
                                }

                                $Reference = $VehAvailCore->item(0)->getElementsByTagName("Reference");
                                if ($Reference->length > 0) {
                                    $ReferenceType = $Reference->item(0)->getAttribute("Type");
                                    $ReferenceID = $Reference->item(0)->getAttribute("ID");
                                    $ReferenceID_Context = $Reference->item(0)->getAttribute("ID_Context");
                                    $ReferenceDateTime = $Reference->item(0)->getAttribute("DateTime");
                                    $ReferenceURL = $Reference->item(0)->getAttribute("URL");
                                    /* error_log("\r\nReferenceType : " . $ReferenceType . "\r\n", 3, "/srv/www/htdocs/error_log");
                                    error_log("\r\nReferenceID : " . $ReferenceID . "\r\n", 3, "/srv/www/htdocs/error_log");
                                    error_log("\r\nReferenceID_Context : " . $ReferenceID_Context . "\r\n", 3, "/srv/www/htdocs/error_log");
                                    error_log("\r\nReferenceDateTime : " . $ReferenceDateTime . "\r\n", 3, "/srv/www/htdocs/error_log");
                                    error_log("\r\nReferenceURL : " . $ReferenceURL . "\r\n", 3, "/srv/www/htdocs/error_log"); */
                                } else {
                                    $ReferenceType = "";
                                    $ReferenceID = "";
                                    $ReferenceID_Context = "";
                                    $ReferenceDateTime = "";
                                    $ReferenceURL = "";
                                }
                                $TPA_Extensions = $VehAvailCore->item(0)->getElementsByTagName("TPA_Extensions");
                                if ($TPA_Extensions->length > 0) {
                                    $TPA_Extensions_FuelPolicy = $TPA_Extensions->item(0)->getElementsByTagName("FuelPolicy");
                                    if ($TPA_Extensions_FuelPolicy->length > 0) {
                                        $TPA_Extensions_FuelPolicyType = $TPA_Extensions_FuelPolicy->item(0)->getAttribute("Type");
                                        $TPA_Extensions_FuelPolicyDescription = $TPA_Extensions_FuelPolicy->item(0)->getAttribute("Description");
                                    } else {
                                        $TPA_Extensions_FuelPolicyType = "";
                                        $TPA_Extensions_FuelPolicyDescription = "";
                                    }
                                    $TPA_Extensions_Config = $TPA_Extensions->item(0)->getElementsByTagName("Config");
                                    if ($TPA_Extensions_Config->length > 0) {
                                        $TPA_Extensions_ConfigOrderBy = $TPA_Extensions_Config->item(0)->getAttribute("OrderBy");
                                        $TPA_Extensions_ConfigInsurance = $TPA_Extensions_Config->item(0)->getAttribute("Insurance");
                                        $TPA_Extensions_ConfigDuration = $TPA_Extensions_Config->item(0)->getAttribute("Duration");
                                        $TPA_Extensions_ConfigCC_Info = $TPA_Extensions_Config->item(0)->getAttribute("CC_Info");
                                        $TPA_Extensions_ConfigBestPrice = $TPA_Extensions_Config->item(0)->getAttribute("BestPrice");
                                        $TPA_Extensions_ConfigLimited = $TPA_Extensions_Config->item(0)->getAttribute("Limited");
                                        $TPA_Extensions_ConfigGuaranteed = $TPA_Extensions_Config->item(0)->getAttribute("Guaranteed");
                                    } else {
                                        $TPA_Extensions_ConfigOrderBy = "";
                                        $TPA_Extensions_ConfigInsurance = "";
                                        $TPA_Extensions_ConfigDuration = "";
                                        $TPA_Extensions_ConfigCC_Info = "";
                                        $TPA_Extensions_ConfigBestPrice = "";
                                        $TPA_Extensions_ConfigLimited = "";
                                        $TPA_Extensions_ConfigGuaranteed = "";
                                    }
                                    $TPA_Extensions_OrderBy = $TPA_Extensions->item(0)->getElementsByTagName("OrderBy");
                                    if ($TPA_Extensions_OrderBy->length > 0) {
                                        $TPA_Extensions_OrderByIndex = $TPA_Extensions_OrderBy->item(0)->getAttribute("Index");
                                    } else {
                                        $TPA_Extensions_OrderByIndex = "";
                                    }
                                    $TPA_Extensions_CC_Info = $TPA_Extensions->item(0)->getElementsByTagName("CC_Info");
                                    if ($TPA_Extensions_CC_Info->length > 0) {
                                        $TPA_Extensions_CC_InfoRequired = $TPA_Extensions_CC_Info->item(0)->getAttribute("Required");
                                    } else {
                                        $TPA_Extensions_CC_InfoRequired = "";
                                    }
                                    $TPA_Extensions_Duration = $TPA_Extensions->item(0)->getElementsByTagName("Duration");
                                    if ($TPA_Extensions_Duration->length > 0) {
                                        $TPA_Extensions_DurationDays = $TPA_Extensions_Duration->item(0)->getAttribute("Days");
                                    } else {
                                        $TPA_Extensions_DurationDays = "";
                                    }
                                    $TPA_Extensions_Insurance = $TPA_Extensions->item(0)->getElementsByTagName("Insurance");
                                    if ($TPA_Extensions_Insurance->length > 0) {
                                        $TPA_Extensions_Insuranceavail = $TPA_Extensions_Insurance->item(0)->getAttribute("avail");
                                    } else {
                                        $TPA_Extensions_Insuranceavail = "";
                                    }
                                } else {
                                    $TPA_Extensions_FuelPolicyType = "";
                                    $TPA_Extensions_FuelPolicyDescription = "";
                                    $TPA_Extensions_ConfigOrderBy = "";
                                    $TPA_Extensions_ConfigInsurance = "";
                                    $TPA_Extensions_ConfigDuration = "";
                                    $TPA_Extensions_ConfigCC_Info = "";
                                    $TPA_Extensions_OrderByIndex = "";
                                    $TPA_Extensions_CC_InfoRequired = "";
                                    $TPA_Extensions_DurationDays = "";
                                    $TPA_Extensions_Insuranceavail = "";
                                }
                                switch ($TPA_Extensions_FuelPolicyType) {
                                    default:
                                        if ($cars[$counter]['Requirements'][0]['Text'] == "") {
                                            $cars[$counter]['Requirements'][0]['Text'] = utf8_encode(gettext("Fuel:") . $TPA_Extensions_FuelPolicyDescription);
                                        } else {
                                            $cars[$counter]['Requirements'][0]['Text'] = $cars[$counter]['Requirements'][0]['Text'] . "<br/><br/>" . utf8_encode(gettext("Fuel:") . $TPA_Extensions_FuelPolicyDescription);
                                        }
                                        break;
                                }
                                
                                /* error_log("\r\nTPA_Extensions_FuelPolicyType : " . $TPA_Extensions_FuelPolicyType . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_FuelPolicyDescription : " . $TPA_Extensions_FuelPolicyDescription . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_ConfigOrderBy : " . $TPA_Extensions_ConfigOrderBy . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_ConfigInsurance : " . $TPA_Extensions_ConfigInsurance . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_ConfigDuration : " . $TPA_Extensions_ConfigDuration . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_ConfigCC_Info : " . $TPA_Extensions_ConfigCC_Info . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_OrderByIndex : " . $TPA_Extensions_OrderByIndex . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_CC_InfoRequired : " . $TPA_Extensions_CC_InfoRequired . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_DurationDays : " . $TPA_Extensions_DurationDays . "\r\n", 3, "/srv/www/htdocs/error_log");
                                error_log("\r\nTPA_Extensions_Insuranceavail : " . $TPA_Extensions_Insuranceavail . "\r\n", 3, "/srv/www/htdocs/error_log"); */
                                $CoverageArray = "";
                                $CoverageCount = 0;
                                $VehAvailInfo = $VehAvail->item(0)->getElementsByTagName("VehAvailInfo");
                                if ($VehAvailInfo->length > 0) {
                                    $PricedCoverages = $VehAvailInfo->item(0)->getElementsByTagName("PricedCoverages");
                                    if ($PricedCoverages->length > 0) {
                                        $PricedCoverage = $PricedCoverages->item(0)->getElementsByTagName("PricedCoverage");
                                        for ($xY = 0; $xY < $PricedCoverage->length; $xY ++) {
                                            $Coverage = $PricedCoverage->item($xY)->getElementsByTagName("Coverage");
                                            if ($Coverage->length > 0) {
                                                $CoverageType = $Coverage->item(0)->getAttribute("CoverageType");
                                            } else {
                                                $CoverageType = "";
                                            }
                                            $Coverage = $PricedCoverage->item($xY)->getElementsByTagName("Charge");
                                            if ($Coverage->length > 0) {
                                                $CoverageChargeDescription = $Coverage->item(0)->getAttribute("Description");
                                                $CoverageChargeTaxInclusive = $Coverage->item(0)->getAttribute("TaxInclusive");
                                                $CoverageChargeIncludedInRate = $Coverage->item(0)->getAttribute("IncludedInRate");
                                            } else {
                                                $CoverageChargeDescription = "";
                                                $CoverageChargeTaxInclusive = "";
                                                $CoverageChargeIncludedInRate = "";
                                            }
                                            /* error_log("\r\nCoverageType : " . $CoverageType . "\r\n", 3, "/srv/www/htdocs/error_log");
                                            error_log("\r\nCoverageChargeDescription : " . $CoverageChargeDescription . "\r\n", 3, "/srv/www/htdocs/error_log");
                                            error_log("\r\nCoverageChargeTaxInclusive : " . $CoverageChargeTaxInclusive . "\r\n", 3, "/srv/www/htdocs/error_log");
                                            error_log("\r\nCoverageChargeIncludedInRate : " . $CoverageChargeIncludedInRate . "\r\n", 3, "/srv/www/htdocs/error_log"); */
                                            
                                            $CoverageArray[$CoverageCount]['CoverageType'] = $CoverageType;
                                            $CoverageArray[$CoverageCount]['CoverageChargeDescription'] = $CoverageChargeDescription;
                                            $CoverageArray[$CoverageCount]['CoverageChargeTaxInclusive'] = $CoverageChargeTaxInclusive;
                                            $CoverageArray[$CoverageCount]['CoverageChargeIncludedInRate'] = $CoverageChargeIncludedInRate;
                                            $CoverageCount = $CoverageCount + 1;
                                        }
                                    }
                                } else {
                                    $CoverageType = "";
                                    $CoverageChargeDescription = "";
                                    $CoverageChargeTaxInclusive = "";
                                    $CoverageChargeIncludedInRate = "";
                                }

                                $sql = "select name from cartype where code='$Size'";
                                $statement2 = $db->createStatement($sql);
                                $statement2->prepare();
                                $row_settings = $statement2->execute();
                                $row_settings->buffer();
                                if ($row_settings->valid()) {
                                    $row_settings = $row_settings->current();
                                    $VehicleCategory = $row_settings["name"];
                                }
                                
                                $cars[$counter]['id'] = $counter;
                                $cars[$counter]['quoteid'] = md5(uniqid($session_id, true)) . "-" . $index . "-1-" . $counter;
                                $cars[$counter]['vendorpicture'] = $TPA_Extensions_VendorPictureURL;
                                $cars[$counter]['vendorcode'] = $VendorCode;
                                $cars[$counter]['vendor'] = $VendorCompanyShortName;
                                $cars[$counter]['vendorshortname'] = $VendorCompanyShortName;
                                $cars[$counter]['VendorCodeContext'] = $VendorCodeContext;
                                $cars[$counter]['VendorDivision'] = $VendorDivision;
                                $cars[$counter]['size'] = $PassengerQuantity;
                                $cars[$counter]['doors'] = $DoorCount;
                                $cars[$counter]['aircondition'] = $AirConditionInd;
                                $cars[$counter]['transmission'] = $TransmissionType;
                                $cars[$counter]['bags'] = $BaggageQuantity;
                                $cars[$counter]['status'] = $Status;
                                $cars[$counter]['from'] = $from;
                                $cars[$counter]['to'] = $to;
                                $cars[$counter]['pickup'] = ucwords(strtolower($pickup_at_location));
                                $cars[$counter]['dropoff'] = ucwords(strtolower($dropoff_at_location));
                                $cars[$counter]['class'] = $VehicleCategory;
                                $cars[$counter]['currency'] = $scurrency;
                                $cars[$counter]['productId'] = $productId;
                                $cars[$counter]['programId'] = $CarProgramId;
                                $cars[$counter]['name'] = $Name;
                                $cars[$counter]['picture'] = $PictureURL;
                                $cars[$counter]['programname'] = $CompanyShortName;
                                $cars[$counter]['coverage'] = $CoverageType;
                                $cars[$counter]['netcurrency'] = $CurrencyCode;
                                $cars[$counter]['netprice'] = $RateTotalAmount;
                                // Total including VAT in renting country currency
                                if ($minPrice < $CarProgramPrice) {
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
                                }
                                
                                $dailytotal = $RateTotalAmount / $numberofdays;
                                $dailytotal = number_format($dailytotal, 2, ".", "");
                                $RateTotalAmount = number_format($RateTotalAmount, 2, ".", "");
                                $cars[$counter]['currency'] = $scurrency;
                                $cars[$counter]['total'] = $filter->filter($RateTotalAmount);
                                $cars[$counter]['dailytotal'] = $filter->filter($dailytotal);
                                $cars[$counter]['Location'] = $CounterLocation_Location;
                                $cars[$counter]['LocationCode'] = $CounterLocation_LocationCode;
                                $cars[$counter]['VehicleCategory'] = $VehicleCategory;
                                $cars[$counter]['dueatpickupplain'] = $DueAtPickup;
                                $cars[$counter]['dueatpickup'] = $filter->filter($DueAtPickup);
                                $cars[$counter]['dueatpickupcurrency'] = $filter->filter($DueAtPickupCurrency);
                                // Location
                                // $cars[$counter]['special'] = 1;
                                // $cars[$counter]['recommended'] = 1;
                                $counter = $counter + 1;
                            }
                        }
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
        $delete->from('quote_session_cartrawler');
        $delete->where(array(
            'session_id' => $session_id
        ));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('quote_session_cartrawler');
        $insert->values(array(
            'session_id' => $session_id,
            'xmlrequest' => (string) $xmlrequest,
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
// }
$db->getDriver()
    ->getConnection()
    ->disconnect();
?>