<?php
$scurrency = strtoupper($currency);
use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Json\Json;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$translator = new Translator();
$filter = new \Zend\I18n\Filter\NumberFormat($NumberFormat, 2);
unset($tmp);
$sfilter = array();
$abreu = false;
$totalPages = 0;
$query = "";
$auxArray = array();
$reviewsFilter = "";
$cAuxCounter = 0;
$dbHotelbeds = new \Zend\Db\Adapter\Adapter($config);
$sql = "select city_xml02 from cities where id=" . $destination;
$statement2 = $dbHotelbeds->createStatement($sql);
$statement2->prepare();
$row_settings = $statement2->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $city_xml02 = $row_settings["city_xml02"];
} else {
    $city_xml02 = "";
}
$transfer_count = 0;
$affiliate_id_hotelbeds = 0;
$sql = "select value from settings where name='hotelbedsTransfersuser' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersuser = $row_settings['value'];
}
$sql = "select value from settings where name='hotelbedsTransferspassword' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransferspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='hotelbedsTransfersMarkup' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersMarkup = (double) $row_settings['value'];
} else {
    $hotelbedsTransfersMarkup = 0;
}
// URL
$sql = "select value from settings where name='hotelbedsTransfersserviceURL' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersserviceURL = $row_settings['value'];
}
$sql = "select value from settings where name='hotelbedsTransferslanguage' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $dbHotelbeds->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransferslanguage = $row_settings['value'];
} else {
    $hotelbedsTransferslanguage = "EN";
}
$dateStart = new DateTime(strftime("%Y-%m-%d", $from));
if ($stype == 1) {
    // Return
    $dateEnd = new DateTime(strftime("%Y-%m-%d", $to));
    $noOfNights = $dateStart->diff($dateEnd)->format('%d');
} else {
    // One Way
    $dateEnd = 0;
    $noOfNights = 0;
}
$date = new Datetime();
$timestamp = $date->format('U');
$token = md5(uniqid(rand(), true));
$rettime2 = str_replace(":", "", $rettime);
$arrtime2 = str_replace(":", "", $arrtime);
// if ($city_xml02 != "") {
$xmlrequest = 'xml_request=<TransferValuedAvailRQ echoToken="' . $token . '" sessionId="' . substr($session_id, 0, 25) . '" xmlns="http://www.hotelbeds.com/schemas/2005/06/messages" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.hotelbeds.com/schemas/2005/06/messages TransferValuedAvailRQ.xsd" version="2013/12"><Language>' . $hotelbedsTransferslanguage . '</Language><Credentials><User>' . $hotelbedsTransfersuser . '</User><Password>' . $hotelbedsTransferspassword . '</Password></Credentials><AvailData type="IN">
        <ServiceDate date="' . strftime("%Y%m%d", $from) . '" time="' . $arrtime2 . '" /><Occupancy><AdultCount>' . $adults . '</AdultCount><ChildCount>' . $children . '</ChildCount></Occupancy>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <IATA>BCN</IATA>
            <DateTime date="' . strftime("%Y%m%d", $from) . '" time="' . $arrtime2 . '" />
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferHotel">
            <GIATA>135661</GIATA>
        </DestinationLocation>
    </AvailData><ReturnContents>Y</ReturnContents></TransferValuedAvailRQ>';
// error_log("\r\nHotelbeds Request: $xmlrequest \r\n", 3, "/srv/www/htdocs/error_log");
if ($hotelbedsTransfersserviceURL != "" and $hotelbedsTransfersuser != "" and $hotelbedsTransferspassword != "") {
    $startTime = microtime();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $hotelbedsTransfersserviceURL);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlrequest);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
        'Accept-Encoding: gzip',
        'Content-Length: ' . strlen($xmlrequest)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $xmlresult = curl_exec($ch);
    $error = curl_error($ch);
    $headers = curl_getinfo($ch);
    // error_log("\r\nHotelbeds Transfers Response: $xmlresult \r\n", 3, "/srv/www/htdocs/error_log");
    $endTime = microtime();
    try {
        $sql = new Sql($dbHotelbeds);
        $insert = $sql->insert();
        $insert->into('log_hotelbedstransfers');
        $insert->values(array(
            'datetime_created' => time(),
            'filename' => 'SearchTransfers.php',
            'errorline' => 0,
            'errormessage' => $hotelbedsTransfersserviceURL . $xmlrequest,
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
    $inputDoc = new DOMDocument();
    $inputDoc->loadXML($xmlresult);
    $ServiceTransfer = $inputDoc->getElementsByTagName("ServiceTransfer");
    if ($ServiceTransfer->length > 0) {
        $transferType = $ServiceTransfer->item(0)->getAttribute("transferType");
    } else {
        $transferType = "";
    }
    $search = $inputDoc->getElementsByTagName("TransferValuedAvailRS");
    if ($search->length > 0) {
        $AuditData = $search->item(0)->getElementsByTagName('AuditData');
        $ProcessTime = $AuditData->item(0)->getElementsByTagName('ProcessTime');
        if ($ProcessTime->length > 0) {
            $ProcessTime = $ProcessTime->item(0)->nodeValue;
        } else {
            $ProcessTime = "";
        }
        $node = $inputDoc->getElementsByTagName('TransferValuedAvailRS');
        $echoToken = $node->item(0)->getAttribute("echoToken");
        error_log("\r\n echoToken = $echoToken \r\n", 3, "/srv/www/htdocs/error_log");
        $AuditData = $node->item(0)->getElementsByTagName('AuditData');
        $ProcessTime = $AuditData->item(0)->getElementsByTagName('ProcessTime');
        if ($ProcessTime->length > 0) {
            $ProcessTime = $ProcessTime->item(0)->nodeValue;
        } else {
            $ProcessTime = "";
        }
        $Timestamp = $AuditData->item(0)->getElementsByTagName('Timestamp');
        if ($Timestamp->length > 0) {
            $Timestamp = $Timestamp->item(0)->nodeValue;
        } else {
            $Timestamp = "";
        }
        $RequestHost = $AuditData->item(0)->getElementsByTagName('RequestHost');
        if ($RequestHost->length > 0) {
            $RequestHost = $RequestHost->item(0)->nodeValue;
        } else {
            $RequestHost = "";
        }
        $ServerName = $AuditData->item(0)->getElementsByTagName('ServerName');
        if ($ServerName->length > 0) {
            $ServerName = $ServerName->item(0)->nodeValue;
        } else {
            $ServerName = "";
        }
        $ServerId = $AuditData->item(0)->getElementsByTagName('ServerId');
        if ($ServerId->length > 0) {
            $ServerId = $ServerId->item(0)->nodeValue;
        } else {
            $ServerId = "";
        }
        $SchemaRelease = $AuditData->item(0)->getElementsByTagName('SchemaRelease');
        if ($SchemaRelease->length > 0) {
            $SchemaRelease = $SchemaRelease->item(0)->nodeValue;
        } else {
            $SchemaRelease = "";
        }
        $node2 = $node->item(0)->getElementsByTagName('ServiceTransfer');
        for ($rAUX = 0; $rAUX < $node2->length; $rAUX ++) {
            $availToken = $node2->item($rAUX)->getAttribute("availToken");
            $transferType = $node2->item($rAUX)->getAttribute("transferType");
            //error_log("\r\n availToken = $availToken \r\n", 3, "/srv/www/htdocs/error_log");
            // ContractList
            $ContractList = $node2->item($rAUX)->getElementsByTagName('ContractList');
            $Contract = $ContractList->item(0)->getElementsByTagName('Contract');
            $NameContract = $Contract->item(0)->getElementsByTagName('Name');
            if ($NameContract->length > 0) {
                $NameContract = $NameContract->item(0)->nodeValue;
            } else {
                $NameContract = "";
            }
            $IncomingOffice = $Contract->item(0)->getElementsByTagName('IncomingOffice');
            $codeIncomingOffice = $IncomingOffice->item(0)->getAttribute("code");
            
            // DateFrom
            $DateFrom = $node2->item($rAUX)->getElementsByTagName('DateFrom');
            $date = $DateFrom->item(0)->getAttribute("date");
            $time = $DateFrom->item(0)->getAttribute("time");
            
            // Currency
            $Currency = $node2->item($rAUX)->getElementsByTagName('Currency');
            $codeCurrency = $Currency->item(0)->getAttribute("code");
            
            // TotalAmount
            $TotalAmount = $node2->item($rAUX)->getElementsByTagName('TotalAmount');
            if ($TotalAmount->length > 0) {
                $TotalAmount = $TotalAmount->item(0)->nodeValue;
            } else {
                $TotalAmount = "";
            }
            
            // SellingPrice
            $SellingPrice = $node2->item($rAUX)->getElementsByTagName('SellingPrice');
            $mandatory = $SellingPrice->item(0)->getAttribute("mandatory");
            if ($SellingPrice->length > 0) {
                $SellingPrice = $SellingPrice->item(0)->nodeValue;
            } else {
                $SellingPrice = "";
            }
            // Transfer Info
            $TransferInfo = $node2->item($rAUX)->getElementsByTagName('TransferInfo');
            $CodeTransferInfo = $TransferInfo->item(0)->getElementsByTagName('Code');
            if ($CodeTransferInfo->length > 0) {
                $CodeTransferInfo = $CodeTransferInfo->item(0)->nodeValue;
            } else {
                $CodeTransferInfo = "";
            }
            $typeTransferInfo = $TransferInfo->item(0)->getAttribute("type");
            //
            // Description List
            //
            $transfertypeDescription = "";
            $transfertypeVehicle = "";
            $DescriptionList = $TransferInfo->item(0)->getElementsByTagName('DescriptionList');
            if ($DescriptionList->length > 0) {
                $Description = $DescriptionList->item(0)->getElementsByTagName('Description');
                for ($lAux = 0; $lAux < $Description->length; $lAux ++) {
                    $languageCode = $Description->item($lAux)->getAttribute("languageCode");
                    $typeDescription = $Description->item($lAux)->getAttribute("type");
                    if ($typeDescription == "GENERAL") {
                        $transfertypeDescription = $Description->item($lAux)->nodeValue;
                    } elseif ($typeDescription == "VEHICLE") {
                        $transfertypeVehicle = $Description->item($lAux)->nodeValue;
                    }
                }
            } else {
                $transfertypeDescription = "";
                $transfertypeVehicle = "";
                $DescriptionList = "";
            }
            //
            // Image Listing
            // Paulo
            //
            $image = "";
            $ImageArray = array();
            $ImageCount = 0;
            $ImageList = $TransferInfo->item(0)->getElementsByTagName('ImageList');
            $Image = $ImageList->item(0)->getElementsByTagName('Image');
            for ($kAux = 0; $kAux < $Image->length; $kAux ++) {
                $TypeImage = $Image->item($kAux)->getElementsByTagName('Type');
                if ($TypeImage->length > 0) {
                    $TypeImage = $TypeImage->item(0)->nodeValue;
                } else {
                    $TypeImage = "";
                }
                $UrlImage = $Image->item($kAux)->getElementsByTagName('Url');
                if ($UrlImage->length > 0) {
                    $UrlImage = $UrlImage->item(0)->nodeValue;
                } else {
                    $UrlImage = "";
                }
                if ($UrlImage != "") {
                    $ImageArray[$ImageCount]['Type'] = $TypeImage;
                    $ImageArray[$ImageCount]['Url'] = $UrlImage;
                    if ($image == "") {
                        $image = $UrlImage;
                    } elseif ($TypeImage == "XL") {
                        $image = $UrlImage;
                    }
                    $ImageCount = $ImageCount + 1;
                }
            }
            // EOF Image Listing
            //
            // Transfer Type
            // Paulo
            $Type = $TransferInfo->item(0)->getElementsByTagName('Type');
            if ($Type->length > 0) {
                $codeType = $Type->item(($Type->length) - 1)->getAttribute("code");
            } else {
                $codeType = "";
            }
            // EOF Transfer Type
            
            // VehicleType
            $VehicleType = $TransferInfo->item(0)->getElementsByTagName('VehicleType');
            $codeVT = $VehicleType->item(0)->getElementsByTagName('code');
            if ($codeVT->length > 0) {
                $codeVT = $codeVT->item(0)->nodeValue;
            } else {
                $codeVT = "";
            }
            // TransferSpecificContent
            $TransferSpecificContent = $TransferInfo->item(0)->getElementsByTagName('TransferSpecificContent');
            if ($TransferSpecificContent->length > 0) {
                $idTransferSpecificContent = $TransferSpecificContent->item(0)->getAttribute("id");
                
                $MaximumWaitingTime = $TransferSpecificContent->item(0)->getElementsByTagName('MaximumWaitingTime');
                if ($MaximumWaitingTime->length > 0) {
                    $timeMInt = $MaximumWaitingTime->item(0)->getAttribute("time");
                    $MaximumWaitingTime = $MaximumWaitingTime->item(0)->nodeValue;
                } else {
                    $MaximumWaitingTime = "";
                }
                $MaximumWaitingTimeSupplierDomestic = $TransferSpecificContent->item(0)->getElementsByTagName('MaximumWaitingTimeSupplierDomestic');
                if ($MaximumWaitingTimeSupplierDomestic->length > 0) {
                    $timeMDom = $MaximumWaitingTimeSupplierDomestic->item(0)->getAttribute("time");
                    $MaximumWaitingTimeSupplierDomestic = $MaximumWaitingTimeSupplierDomestic->item(0)->nodeValue;
                } else {
                    $MaximumWaitingTimeSupplierDomestic = "";
                }
                $MaximumWaitingTimeSupplierInternational = $TransferSpecificContent->item(0)->getElementsByTagName('MaximumWaitingTimeSupplierInternational');
                if ($MaximumWaitingTimeSupplierInternational->length > 0) {
                    $timeMInt = $MaximumWaitingTimeSupplierInternational->item(0)->getAttribute("time");
                    $MaximumWaitingTimeSupplierInternational = $MaximumWaitingTimeSupplierInternational->item(0)->nodeValue;
                } else {
                    $MaximumWaitingTimeSupplierInternational = "";
                }
                
                $maxstops = 0;
                $MaximumNumberStops = $TransferSpecificContent->item(0)->getElementsByTagName('MaximumNumberStops');
                if ($MaximumNumberStops->length > 0) {
                    $maxstops = $MaximumNumberStops->item(0)->getAttribute("maxstops");
                }
                
                $GenericTransferGuidelinesList = $TransferSpecificContent->item(0)->getElementsByTagName('GenericTransferGuidelinesList');
                $TransferBulletPoint = $GenericTransferGuidelinesList->item(0)->getElementsByTagName('TransferBulletPoint');
                for ($jAux = 0; $jAux < $TransferBulletPoint->length; $jAux ++) {
                    $idTransferBullet = $TransferBulletPoint->item($jAux)->getAttribute("id");
                    $DescriptionTransferBullet = $TransferBulletPoint->item($jAux)->getElementsByTagName('Description');
                    if ($DescriptionTransferBullet->length > 0) {
                        $DescriptionTransferBullet = $DescriptionTransferBullet->item(0)->nodeValue;
                    } else {
                        $DescriptionTransferBullet = "";
                    }
                    $DetailedDescriptionTransferBullet = $TransferBulletPoint->item($jAux)->getElementsByTagName('DetailedDescription');
                    if ($DetailedDescriptionTransferBullet->length > 0) {
                        $DetailedDescriptionTransferBullet = $DetailedDescriptionTransferBullet->item(0)->nodeValue;
                    } else {
                        $DetailedDescriptionTransferBullet = "";
                    }
                }
            }
            // Paxes
            $Paxes = $node2->item($rAUX)->getElementsByTagName('Paxes');
            $AdultCount = $Paxes->item(0)->getElementsByTagName('AdultCount');
            if ($AdultCount->length > 0) {
                $AdultCount = $AdultCount->item(0)->nodeValue;
            } else {
                $AdultCount = "";
            }
            $ChildCount = $Paxes->item(0)->getElementsByTagName('ChildCount');
            if ($ChildCount->length > 0) {
                $ChildCount = $ChildCount->item(0)->nodeValue;
            } else {
                $ChildCount = "";
            }
            // PickupLocation
            $PickupLocation = $node2->item($rAUX)->getElementsByTagName('PickupLocation');
            if ($PickupLocation->length > 0) {
                $CodePL = $PickupLocation->item(0)->getElementsByTagName('Code');
                if ($CodePL->length > 0) {
                    $CodePL = $CodePL->item(0)->nodeValue;
                } else {
                    $CodePL = "";
                }
                $NamePL = $PickupLocation->item(0)->getElementsByTagName('Name');
                if ($NamePL->length > 0) {
                    $NamePL = $NamePL->item(0)->nodeValue;
                } else {
                    $NamePL = "";
                }
                $TransferZonePL = $PickupLocation->item(0)->getElementsByTagName('TransferZone');
                $CodeTZpl = $TransferZonePL->item(0)->getElementsByTagName('Code');
                if ($CodeTZpl->length > 0) {
                    $CodeTZpl = $CodeTZpl->item(0)->nodeValue;
                } else {
                    $CodeTZpl = "";
                }
                $typeTransferZone = $TransferZonePL->item(0)->getAttribute("type");
                $Coordinates = $PickupLocation->item(0)->getElementsByTagName('Coordinates');
                if ($Coordinates->length) {
                    $longitude = $Coordinates->item(0)->getAttribute("longitude");
                    $latitude = $Coordinates->item(0)->getAttribute("latitude");
                }
                //
                $TransferZonePL = $PickupLocation->item(0)->getElementsByTagName('TransferZone');
                $DescriptionPL = $TransferZonePL->item(0)->getElementsByTagName('Description');
                if ($DescriptionPL->length > 0) {
                    $DescriptionPL = $DescriptionPL->item(0)->nodeValue;
                } else {
                    $DescriptionPL = "";
                }
                $Address = $TransferZonePL->item(0)->getElementsByTagName('Address');
                if ($Address->length > 0) {
                    $Address = $Address->item(0)->nodeValue;
                } else {
                    $Address = "";
                }
                $City = $TransferZonePL->item(0)->getElementsByTagName('City');
                if ($City->length > 0) {
                    $City = $City->item(0)->nodeValue;
                } else {
                    $City = "";
                }
                $ZipCode = $TransferZonePL->item(0)->getElementsByTagName('ZipCode');
                if ($ZipCode->length > 0) {
                    $ZipCode = $ZipCode->item(0)->nodeValue;
                } else {
                    $ZipCode = "";
                }
                $CountryPL = $TransferZonePL->item(0)->getElementsByTagName('Country');
                if ($CountryPL->length > 0) {
                    $CountryPL = $CountryPL->item(0)->nodeValue;
                } else {
                    $CountryPL = "";
                }
            }
            
            // DestinationLocation
            $DestinationLocation = $node2->item($rAUX)->getElementsByTagName('DestinationLocation');
            if ($DestinationLocation->length > 0) {
                $typeDL = $DestinationLocation->item(0)->getAttribute("type");
                $CodeDL = $DestinationLocation->item(0)->getElementsByTagName('Code');
                if ($CodeDL->length > 0) {
                    $CodeDL = $CodeDL->item(0)->nodeValue;
                } else {
                    $CodeDL = "";
                }
                $NameDL = $DestinationLocation->item(0)->getElementsByTagName('Name');
                if ($NameDL->length > 0) {
                    $NameDL = $NameDL->item(0)->nodeValue;
                } else {
                    $NameDL = "";
                }
                $TerminalTypeDL = $DestinationLocation->item(0)->getElementsByTagName('TerminalType');
                if ($TerminalType->length > 0) {
                    $TerminalType = $TerminalType->item(0)->nodeValue;
                } else {
                    $TerminalType = "";
                }
                $TransferZoneDL = $DestinationLocation->item(0)->getElementsByTagName('TransferZone');
                $typeTransferZoneDL = $TransferZoneDL->item(0)->getAttribute("type");
                $CodeTransferZoneDL = $TransferZoneDL->item(0)->getElementsByTagName('Code');
                if ($CodeTransferZoneDL->length > 0) {
                    $CodeTransferZoneDL = $CodeTransferZoneDL->item(0)->nodeValue;
                } else {
                    $CodeTransferZoneDL = "";
                }
            }
            
            // DestinationLocationList
            $TransferLocationArray = array();
            $TransferLocationCount = 0;
            $DestinationLocationList = $node2->item($rAUX)->getElementsByTagName('DestinationLocationList');
            if ($DestinationLocationList->length > 0) {
                $TransferLocation = $DestinationLocationList->item(0)->getElementsByTagName('TransferLocation');
                for ($hAux = 0; $hAux < $TransferLocation->length; $hAux ++) {
                    $typeTransferLocation = $TransferLocation->item($hAux)->getAttribute("type");
                    $CodeTransferLocation = $TransferLocation->item($hAux)->getElementsByTagName('Code');
                    if ($CodeTransferLocation->length > 0) {
                        $CodeTransferLocation = $CodeTransferLocation->item(0)->nodeValue;
                    } else {
                        $CodeTransferLocation = "";
                    }
                    $NameTransferLocation = $TransferLocation->item($hAux)->getElementsByTagName('Name');
                    if ($NameTransferLocation->length > 0) {
                        $NameTransferLocation = $NameTransferLocation->item(0)->nodeValue;
                    } else {
                        $NameTransferLocation = "";
                    }
                    $BestMatchOrder = $TransferLocation->item($hAux)->getElementsByTagName('BestMatchOrder');
                    if ($BestMatchOrder->length > 0) {
                        $BestMatchOrder = $BestMatchOrder->item(0)->nodeValue;
                    } else {
                        $BestMatchOrder = "";
                    }
                    $TransferZoneTL = $TransferLocation->item($hAux)->getElementsByTagName('TransferZone');
                    if ($TransferZoneTL->length > 0) {
                        $typeTransferZoneTL = $TransferZoneTL->item(0)->getAttribute("type");
                        $CodeTransferZoneTL = $TransferZoneTL->item(0)->getElementsByTagName('Code');
                        if ($CodeTransferZoneTL->length > 0) {
                            $CodeTransferZoneTL = $CodeTransferZoneTL->item(0)->nodeValue;
                        } else {
                            $CodeTransferZoneTL = "";
                        }
                    }
                    $LocationInformation = $TransferLocation->item($hAux)->getElementsByTagName('LocationInformation');
                    if ($LocationInformation->length > 0) {
                        $distance = $LocationInformation->item(0)->getAttribute("distance");
                        // error_log("\r\n distance = $distance \r\n", 3, "/srv/www/htdocs/error_log");
                        $Address = $LocationInformation->item(0)->getElementsByTagName('Address');
                        if ($Address->length > 0) {
                            $Address = $Address->item(0)->nodeValue;
                        } else {
                            $Address = "";
                        }
                        $Number = $LocationInformation->item(0)->getElementsByTagName('Number');
                        if ($Number->length > 0) {
                            $Number = $Number->item(0)->nodeValue;
                        } else {
                            $Number = "";
                        }
                        $Town = $LocationInformation->item(0)->getElementsByTagName('Town');
                        if ($Town->length > 0) {
                            $Town = $Town->item(0)->nodeValue;
                        } else {
                            $Town = "";
                        }
                        $Zip = $LocationInformation->item(0)->getElementsByTagName('Zip');
                        if ($Zip->length > 0) {
                            $Zip = $Zip->item(0)->nodeValue;
                        } else {
                            $Zip = "";
                        }
                        $Description = $LocationInformation->item(0)->getElementsByTagName('Description');
                        if ($Description->length > 0) {
                            $Description = $Description->item(0)->nodeValue;
                        } else {
                            $Description = "";
                        }
                        $GPSPoint = $LocationInformation->item(0)->getElementsByTagName('GPSPoint');
                        if ($GPSPoint->length > 0) {
                            $longitude = $GPSPoint->item(0)->getAttribute("longitude");
                            $latitude = $GPSPoint->item(0)->getAttribute("latitude");
                        }
                    }
                    $TransferLocationArray[$TransferLocationCount]['distance'] = $distance;
                    $TransferLocationCount = $TransferLocationCount + 1;
                }
            }
            
            // PickupLocationList
            $TransferLocationArray = array();
            $TransferLocationCount = 0;
            $PickupLocationList = $node2->item($rAUX)->getElementsByTagName('PickupLocationList');
            if ($PickupLocationList->length > 0) {
                $TransferLocation = $PickupLocationList->item(0)->getElementsByTagName('TransferLocation');
                for ($hAux = 0; $hAux < $TransferLocation->length; $hAux ++) {
                    $typeTransferLocation = $TransferLocation->item($hAux)->getAttribute("type");
                    $CodeTransferLocation = $TransferLocation->item($hAux)->getElementsByTagName('Code');
                    if ($CodeTransferLocation->length > 0) {
                        $CodeTransferLocation = $CodeTransferLocation->item(0)->nodeValue;
                    } else {
                        $CodeTransferLocation = "";
                    }
                    $NameTransferLocation = $TransferLocation->item($hAux)->getElementsByTagName('Name');
                    if ($NameTransferLocation->length > 0) {
                        $NameTransferLocation = $NameTransferLocation->item(0)->nodeValue;
                    } else {
                        $NameTransferLocation = "";
                    }
                    $BestMatchOrder = $TransferLocation->item($hAux)->getElementsByTagName('BestMatchOrder');
                    if ($BestMatchOrder->length > 0) {
                        $BestMatchOrder = $BestMatchOrder->item(0)->nodeValue;
                    } else {
                        $BestMatchOrder = "";
                    }
                    $TransferZoneTL = $TransferLocation->item($hAux)->getElementsByTagName('TransferZone');
                    if ($TransferZoneTL->length > 0) {
                        $typeTransferZoneTL = $TransferZoneTL->item(0)->getAttribute("type");
                        $CodeTransferZoneTL = $TransferZoneTL->item(0)->getElementsByTagName('Code');
                        if ($CodeTransferZoneTL->length > 0) {
                            $CodeTransferZoneTL = $CodeTransferZoneTL->item(0)->nodeValue;
                        } else {
                            $CodeTransferZoneTL = "";
                        }
                    }
                    $LocationInformation = $TransferLocation->item($hAux)->getElementsByTagName('LocationInformation');
                    if ($LocationInformation->length > 0) {
                        $distance = $LocationInformation->item(0)->getAttribute("distance");
                        // error_log("\r\n distance = $distance \r\n", 3, "/srv/www/htdocs/error_log");
                        $Address = $LocationInformation->item(0)->getElementsByTagName('Address');
                        if ($Address->length > 0) {
                            $Address = $Address->item(0)->nodeValue;
                        } else {
                            $Address = "";
                        }
                        $Number = $LocationInformation->item(0)->getElementsByTagName('Number');
                        if ($Number->length > 0) {
                            $Number = $Number->item(0)->nodeValue;
                        } else {
                            $Number = "";
                        }
                        $Town = $LocationInformation->item(0)->getElementsByTagName('Town');
                        if ($Town->length > 0) {
                            $Town = $Town->item(0)->nodeValue;
                        } else {
                            $Town = "";
                        }
                        $Zip = $LocationInformation->item(0)->getElementsByTagName('Zip');
                        if ($Zip->length > 0) {
                            $Zip = $Zip->item(0)->nodeValue;
                        } else {
                            $Zip = "";
                        }
                        $Description = $LocationInformation->item(0)->getElementsByTagName('Description');
                        if ($Description->length > 0) {
                            $Description = $Description->item(0)->nodeValue;
                        } else {
                            $Description = "";
                        }
                        $GPSPoint = $LocationInformation->item(0)->getElementsByTagName('GPSPoint');
                        if ($GPSPoint->length > 0) {
                            $longitude = $GPSPoint->item(0)->getAttribute("longitude");
                            $latitude = $GPSPoint->item(0)->getAttribute("latitude");
                        }
                    }
                    $TransferLocationArray[$TransferLocationCount]['distance'] = $distance;
                    $TransferLocationCount = $TransferLocationCount + 1;
                }
            }
            
            // RetailPrice
            $RetailPrice = $node2->item($rAUX)->getElementsByTagName('RetailPrice');
            if ($RetailPrice->length > 0) {
                $RetailPrice = $RetailPrice->item(0)->nodeValue;
            } else {
                $RetailPrice = "";
            }
            // EstimatedTransferDuration
            $EstimatedTransferDuration = $node2->item($rAUX)->getElementsByTagName('EstimatedTransferDuration');
            if ($EstimatedTransferDuration->length > 0) {
                $EstimatedTransferDuration = $EstimatedTransferDuration->item(0)->nodeValue;
            } else {
                $EstimatedTransferDuration = "";
            }
            
            // ProductSpecifications
            $ProductSpecifications = $node2->item($rAUX)->getElementsByTagName('ProductSpecifications');
            $MasterServiceType = $ProductSpecifications->item(0)->getElementsByTagName('MasterServiceType');
            $codeMST = $MasterServiceType->item(0)->getAttribute("code");
            $nameMST = $MasterServiceType->item(0)->getAttribute("name");
            $MasterProductType = $ProductSpecifications->item(0)->getElementsByTagName('MasterProductType');
            $codeMPT = $MasterProductType->item(0)->getAttribute("code");
            $nameMPT = $MasterProductType->item(0)->getAttribute("name");
            $MasterVehicleType = $ProductSpecifications->item(0)->getElementsByTagName('MasterVehicleType');
            $codeMVT = $MasterVehicleType->item(0)->getAttribute("code");
            $nameMVT = $MasterVehicleType->item(0)->getAttribute("name");
            $TransferGeneralInfoArray = array();
            $TransferGeneralInfoCount = 0;
            $TransferGeneralInfoList = $ProductSpecifications->item(0)->getElementsByTagName('TransferGeneralInfoList');
            if ($TransferGeneralInfoList->length > 0) {
                $TransferBulletPoint = $TransferGeneralInfoList->item(0)->getElementsByTagName('TransferBulletPoint');
                for ($iAux = 0; $iAux < $TransferBulletPoint->length; $iAux ++) {
                    $id = $TransferBulletPoint->item($iAux)->getAttribute("id");
                    $order = $TransferBulletPoint->item($iAux)->getAttribute("order");
                    $DescriptionTBP = $TransferBulletPoint->item($iAux)->getElementsByTagName('Description');
                    if ($DescriptionTBP->length > 0) {
                        $DescriptionTBP = $DescriptionTBP->item(0)->nodeValue;
                    } else {
                        $DescriptionTBP = "";
                    }
                    $TransferGeneralInfoArray[$TransferGeneralInfoCount]['id'] = $id;
                    $TransferGeneralInfoArray[$TransferGeneralInfoCount]['order'] = $order;
                    $TransferGeneralInfoArray[$TransferGeneralInfoCount]['Description'] = $DescriptionTBP;
                    $TransferGeneralInfoCount = $TransferGeneralInfoCount + 1;
                }
            }
            
            // TransferPickupInformation
            $TransferPickupInformation = $node2->item($rAUX)->getElementsByTagName('TransferPickupInformation');
            if ($TransferPickupInformation->length > 0) {
                $DescriptionTPI = $TransferPickupInformation->item(0)->getElementsByTagName('Description');
                if ($DescriptionTPI->length > 0) {
                    $DescriptionTPI = $DescriptionTPI->item(0)->nodeValue;
                } else {
                    $DescriptionTPI = "";
                }
            }
            
            // DepartureTravelInfo
            $DepartureTravelInfo = $node2->item($rAUX)->getElementsByTagName('DepartureTravelInfo');
            if ($DepartureTravelInfo->length > 0) {
                $DepartInfo = $DepartureTravelInfo->item(0)->getElementsByTagName('DepartInfo');
                $CodeDI = $DepartInfo->item(0)->getElementsByTagName('Code');
                if ($CodeDI->length > 0) {
                    $CodeDI = $CodeDI->item(0)->nodeValue;
                } else {
                    $CodeDI = "";
                }
                $NameDI = $DepartInfo->item(0)->getElementsByTagName('Name');
                if ($NameDI->length > 0) {
                    $NameDI = $NameDI->item(0)->nodeValue;
                } else {
                    $NameDI = "";
                }
                $TransferZoneDI = $DepartInfo->item(0)->getElementsByTagName('TransferZone');
                $typeTransferZoneDI = $TransferZoneDI->item(0)->getAttribute("type");
                $CodeTransferZoneDI = $TransferZoneDI->item(0)->getElementsByTagName('Code');
                if ($CodeTransferZoneDI->length > 0) {
                    $CodeTransferZoneDI = $CodeTransferZoneDI->item(0)->nodeValue;
                } else {
                    $CodeTransferZoneDI = "";
                }
                $DateTime = $DepartInfo->item(0)->getElementsByTagName('DateTime');
                $timeDI = $DateTime->item(0)->getAttribute("time");
                $dateDI = $DateTime->item(0)->getAttribute("date");
                
                $TerminalTypeDI = $DepartInfo->item(0)->getElementsByTagName('TerminalType');
                if ($TerminalTypeDI->length > 0) {
                    $TerminalTypeDI = $TerminalTypeDI->item(0)->nodeValue;
                } else {
                    $TerminalTypeDI = "";
                }
            }
            
            // ArrivalTravelInfo
            $ArrivalTravelInfo = $node2->item($rAUX)->getElementsByTagName('ArrivalTravelInfo');
            if ($ArrivalTravelInfo->length > 0) {
                $ArrivalInfo = $ArrivalTravelInfo->item(0)->getElementsByTagName('ArrivalInfo');
                $CodeDI = $ArrivalInfo->item(0)->getElementsByTagName('Code');
                if ($CodeDI->length > 0) {
                    $CodeDI = $CodeDI->item(0)->nodeValue;
                } else {
                    $CodeDI = "";
                }
                $NameDI = $ArrivalInfo->item(0)->getElementsByTagName('Name');
                if ($NameDI->length > 0) {
                    $NameDI = $NameDI->item(0)->nodeValue;
                } else {
                    $NameDI = "";
                }
                $TransferZoneDI = $ArrivalInfo->item(0)->getElementsByTagName('TransferZone');
                $typeTransferZoneDI = $TransferZoneDI->item(0)->getAttribute("type");
                $CodeTransferZoneDI = $TransferZoneDI->item(0)->getElementsByTagName('Code');
                if ($CodeTransferZoneDI->length > 0) {
                    $CodeTransferZoneDI = $CodeTransferZoneDI->item(0)->nodeValue;
                } else {
                    $CodeTransferZoneDI = "";
                }
                $DateTime = $ArrivalInfo->item(0)->getElementsByTagName('DateTime');
                $timeAI = $DateTime->item(0)->getAttribute("time");
                $dateAI = $DateTime->item(0)->getAttribute("date");
                
                $TerminalTypeDI = $ArrivalInfo->item(0)->getElementsByTagName('TerminalType');
                if ($TerminalTypeDI->length > 0) {
                    $TerminalTypeDI = $TerminalTypeDI->item(0)->nodeValue;
                } else {
                    $TerminalTypeDI = "";
                }
            }
            // Formato correcto
            $transfers[$transfer_count]['id'] = md5(uniqid($session_id, true)) . "-" . $transfer_count . "-2";
            $transfers[$transfer_count]['adults'] = $AdultCount;
            $transfers[$transfer_count]['children'] = $ChildCount;
            $transfers[$transfer_count]['infants'] = $infants;
            $transfers[$transfer_count]['arrdate'] = strftime("%d/%m/%y", $from);
            $transfers[$transfer_count]['arrtime'] = $arrtime;
            $transfers[$transfer_count]['retdate'] = strftime("%d/%m/%y", $to);
            $transfers[$transfer_count]['rettime'] = $rettime;
            $transfers[$transfer_count]['departurepointcode'] = $CodeDI;
            $transfers[$transfer_count]['arrivalpointcode'] = $CodeDI;
            $transfers[$transfer_count]['transfercode'] = $CodeTransferZoneDI;
            $transfers[$transfer_count]['transferprice'] = $TotalAmount;
            $transfers[$transfer_count]['transferprice_net'] = $TotalAmount;
            $transfers[$transfer_count]['departurepointtype'] = $TerminalTypeDI;
            $transfers[$transfer_count]['arrivalpointtype'] = $TerminalTypeDI;
            $transfers[$transfer_count]['discount'] = "0";
            $transfers[$transfer_count]['discountpercent'] = "0";
            $transfers[$transfer_count]['disclaimer'] = "0";
            if ($codeType == "P") {
                $transferdescription = $translator->translate("Private Transfer");
            } elseif ($codeType == "B") {
                $transferdescription = $translator->translate("Bus Transfer");
            } else {
                $transferdescription = $translator->translate("Unknown Transfer") . " - " . $codeType;
            }
            $transfers[$transfer_count]['image'] = $image;
            $transfers[$transfer_count]['transfertype'] = $transfertypeDescription;
            if ($transferType == "TER") {
                $t = $translator->translate("Terminal to Terminal");
            } elseif ($transferType == "IN") {
                $t = $translator->translate("Transfer from pickup to destination");
            } elseif ($transferType == "OUT") {
                $t = $translator->translate("Return transfer from the destination to the pickup");
            } else {
                $t = $transferType;
            }
            $transfers[$transfer_count]['transferdescription'] = $transferdescription . " (" . $t . ") - " . $transfertypeVehicle;
            $transfers[$transfer_count]['transfertype2'] = $transferType;
            $transfers[$transfer_count]['transferInfoCode'] = $CodeTransferInfo;
            $transfers[$transfer_count]['typeTransferInfo'] = $typeTransferInfo;
            
            $transfers[$transfer_count]['outboundorigin'] = $NamePL;
            $transfers[$transfer_count]['outbounddestination'] = $NameDL;
            $transfers[$transfer_count]['outboundjourneytime'] = $timeMDom . " " . $MaximumWaitingTimeSupplierDomestic;
            $transfers[$transfer_count]['outboundarrivaldate'] = strftime("%d/%m/%y", $from);
            $transfers[$transfer_count]['outboundarrivaltime'] = $arrtime2;
            $transfers[$transfer_count]['outboundpickupdate'] = "";
            $transfers[$transfer_count]['outboundpickuptime'] = "";
            $transfers[$transfer_count]['distance'] = $distance;
            $transfers[$transfer_count]['duration'] = $EstimatedTransferDuration;
            $transfers[$transfer_count]['numberofvehicles'] = "1";
            $transfers[$transfer_count]['numberofbags'] = "1";
            $transfers[$transfer_count]['maxstops'] = $maxstops;
            $transfers[$transfer_count]['minstops'] = "0";
            $transfers[$transfer_count]['maxcapacity'] = ($AdultCount + $ChildCount);
            $transfers[$transfer_count]['mincapacity'] = "1";
            if ($stype == 1) {
                $transfers[$transfer_count]['sectortype'] = "RETURN";
            } else {
                $transfers[$transfer_count]['sectortype'] = "SINGLE";
            }
            // 1=Shuttle, 2=Private
            $transfers[$transfer_count]['vehicletype'] = $codeVT;
            $transfers[$transfer_count]['vehicle'] = $transfertypeVehicle;
            $transfers[$transfer_count]['vehicleid'] = "";
            $transfers[$transfer_count]['vehiclecode'] = $codeVT;
            $transfers[$transfer_count]['numtransfers'] = ($AdultCount + $ChildCount);
            $transfers[$transfer_count]['PRID'] = $transferType;
            $transfers[$transfer_count]['codeIncomingOffice'] = $codeIncomingOffice;
            $transfers[$transfer_count]['CodePickupLocation'] = $CodePL;
            $transfers[$transfer_count]['CodeDestinationLocation'] = $CodeDL;
            $transfers[$transfer_count]['NameContract'] = $NameContract;
            $transfers[$transfer_count]['codeType'] = $codeType;
            $transfers[$transfer_count]['dateFrom'] = $date;
            // $transfers[$transfer_count]['duration_desc'] = convertToHoursMinsA2B($Duration, '%2d hour(s) and %02d minutes');
            $transfers[$transfer_count]['availToken'] = $availToken;
            $transfers[$transfer_count]['echoToken'] = $echoToken;
            $transfers[$transfer_count]['returnorigin'] = $NameDL;
            $transfers[$transfer_count]['currency'] = $codeCurrency;
            $transfers[$transfer_count]['currencycode'] = $codeCurrency;
            $transfers[$transfer_count]['returndestination'] = $NamePL;
            $transfers[$transfer_count]['returnpickuptime'] = $rettime;
            $transfers[$transfer_count]['returndeparturedate'] = strftime("%d/%m/%y", $to);
            $transfers[$transfer_count]['returndeparturetime'] = $rettime;
            $transfers[$transfer_count]['returnpickupdate'] = strftime("%d/%m/%y", $to);
            $transfers[$transfer_count]['returnjourneytime'] = $timeMDom . " " . $MaximumWaitingTimeSupplierDomestic;
            $transfer_count ++;
            // EOF
        }
    }
    try {
        $sql = new Sql($dbHotelbeds);
        $delete = $sql->delete();
        $delete->from('quote_session_hotelbedstransfers');
        $delete->where(array(
            'session_id' => $session_id
        ));
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($dbHotelbeds);
        $insert = $sql->insert();
        $insert->into('quote_session_hotelbedstransfers');
        $insert->values(array(
            'session_id' => $session_id,
            'xmlrequest' => (string) $xmlrequest,
            'xmlresult' => (string) $xmlresult,
            'data' => base64_encode(serialize($transfers)),
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
$dbHotelbeds->getDriver()
    ->getConnection()
    ->disconnect();
error_log("\r\nHotelbeds transfers eof\r\n", 3, "/srv/www/htdocs/error_log");
?>