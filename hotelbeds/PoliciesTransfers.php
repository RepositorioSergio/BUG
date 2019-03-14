<?php
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Driver\ResultInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Sql;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Filter\AbstractFilter;
use Zend\I18n\Translator\Translator;
$db = new \Zend\Db\Adapter\Adapter($config);
error_log("\r\n COMECOU POLICIES TERCA TARDE \r\n", 3, "/srv/www/htdocs/error_log");
/*
 * $affiliate_id = 0;
 * $sql = "select value from settings where name='enablehotelbedsTransfers' and affiliate_id=$affiliate_id" . $branch_filter;
 * $statement = $db->createStatement($sql);
 * $statement->prepare();
 * $row_settings = $statement->execute();
 * if ($row_settings->valid()) {
 * $affiliate_id_hotelbeds = $affiliate_id;
 * } else {
 * $affiliate_id_hotelbeds = 0;
 * }
 */
$affiliate_id_hotelbeds = 0;
$sql = "select value from settings where name='hotelbedsTransfersuser' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersuser = $row_settings['value'];
}
error_log("\r\n hotelbedsTransfersuser  $hotelbedsTransfersuser \r\n", 3, "/srv/www/htdocs/error_log");
$sql = "select value from settings where name='hotelbedsTransferspassword' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransferspassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='hotelbedsTransfersMarkup' and affiliate_id=$affiliate_id_hotelbeds";
$statement = $db->createStatement($sql);
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
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $hotelbedsTransfersserviceURL = $row_settings['value'];
}
error_log("\r\n hotelbedsTransfersserviceURL  $hotelbedsTransfersserviceURL \r\n", 3, "/srv/www/htdocs/error_log");
// Quote
try {
    $sql = "select data, searchsettings from quote_session_hotelbedstransfers where session_id='$session_id'";
    $statement = $db->createStatement($sql);
    $statement->prepare();
    $row_settings = $statement->execute();
    $row_settings->buffer();
} catch (\Exception $e) {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($e->getMessage());
}
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $searchsettings = unserialize(base64_decode($row_settings["searchsettings"]));
    $data = unserialize(base64_decode($row_settings["data"]));
    $adults = $searchsettings['adults'];
    $children = $searchsettings['children'];
    $infants = $searchsettings['infants'];
    $retdate = $searchsettings['retdate'];
    $rettime = $searchsettings['rettime'];
    $arrtime = $searchsettings['arrtime'];
    $d1 = DateTime::createFromFormat("d-m-Y", $searchsettings['from']);
    $d2 = DateTime::createFromFormat("d-m-Y", $searchsettings['to']);
    $nights = $d1->diff($d2);
    $nights = $nights->format('%a');
} else {
    $response['error'] = "Unable to handle request #2";
    return false;
}
foreach ($data as $key => $value) {
    if ($value['id'] == $quote_id) {
        $id = $key;
        $availToken = $value['availToken'];
        $echoToken = $value['echoToken'];
        $transfertype = $value['transfertype2'];
        $transferInfoCode = $value['transferInfoCode'];
        $typeTransferInfo = $value['typeTransferInfo'];
        $vehiclecode = $value['vehiclecode'];
        $codeIncomingOffice = $value['codeIncomingOffice'];
        $CodePickupLocation = $value['CodePickupLocation'];
        $CodeDestinationLocation = $value['CodeDestinationLocation'];
        $NameContract = $value['NameContract'];
        $codeType = $value['codeType'];
        $dateFrom = $value['dateFrom'];
        break;
    }
}
$rettime2 = str_replace(":", "", $rettime);
$arrtime2 = str_replace(":", "", $arrtime);
error_log("\r\n ANTES IF \r\n", 3, "/srv/www/htdocs/error_log");
$lang = "ENG";

error_log("\r\n transfertype: $transfertype \r\n", 3, "/srv/www/htdocs/error_log");

$xmlrequest = 'xml_request=<?xml version="1.0" encoding="UTF-8"?>
<ServiceAddRQ echoToken="' . $echoToken . '"
    xmlns="http://www.hotelbeds.com/schemas/2005/06/messages"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.hotelbeds.com/schemas/2005/06/messages ServiceAddRQ.xsd"
	version="2013/12">
    <Language>' . $lang . '</Language>
    <Credentials>
        <User>' . $hotelbedsTransfersuser . '</User>
        <Password>' . $hotelbedsTransferspassword . '</Password>
    </Credentials>
    <Service availToken="' . $availToken . '" transferType="' . $transfertype . '"
		xsi:type="ServiceTransfer">
        <ContractList>
            <Contract>
                <Name>' . $NameContract . '</Name>
                <IncomingOffice code="' . $codeIncomingOffice . '"/>
            </Contract>
        </ContractList>
        <DateFrom date="' . $dateFrom . '" time="' . $arrtime2 . '" />
        <TransferInfo xsi:type="ProductTransfer">
            <Code>' . $transferInfoCode . '</Code>
            <Type code="' . $codeType . '"/>
            <VehicleType code="' . $vehiclecode . '"/>
        </TransferInfo>
        <Paxes>
            <AdultCount>' . $adults . '</AdultCount>
            <ChildCount>' . $children . '</ChildCount>
        </Paxes>
        <PickupLocation xsi:type="ProductTransferTerminal">
            <Code>' . $CodePickupLocation . '</Code>
            <DateTime date="' . $dateFrom . '" time="' . $arrtime2 . '" />
        </PickupLocation>
        <DestinationLocation xsi:type="ProductTransferHotel">
            <Code>' . $CodeDestinationLocation . '</Code>
        </DestinationLocation>
    </Service>
</ServiceAddRQ>';

error_log("\r\n REQUEST: $xmlrequest \r\n", 3, "/srv/www/htdocs/error_log");

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
$xmlresult = curl_exec($ch);
error_log("\r\n Hotelbeds Transfers Response: $xmlresult \r\n", 3, "/srv/www/htdocs/error_log");
$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$node = $inputDoc->getElementsByTagName('ServiceAddRS');
$echoToken = $node->item(0)->getAttribute("echoToken");
$AuditData = $node->item(0)->getElementsByTagName('AuditData');

//Purchase
$Purchase = $node->item(0)->getElementsByTagName('Purchase');
$purchaseToken = $Purchase->item(0)->getAttribute("purchaseToken");
$timeToExpiration = $Purchase->item(0)->getAttribute("timeToExpiration");
$Status = $Purchase->item(0)->getElementsByTagName('Status');
if ($Status->length > 0) {
    $Status = $Status->item(0)->nodeValue;
}else {
    $Status = "";
}
$Language = $Purchase->item(0)->getElementsByTagName('Language');
if ($Language->length > 0) {
    $Language = $Language->item(0)->nodeValue;
}else {
    $Language = "";
}
error_log("\r\n Language: $Language \r\n", 3, "/srv/www/htdocs/error_log");
$CreationUser = $Purchase->item(0)->getElementsByTagName('CreationUser');
if ($CreationUser->length > 0) {
    $CreationUser = $CreationUser->item(0)->nodeValue;
}else {
    $CreationUser = "";
}
$TotalPrice = $Purchase->item(0)->getElementsByTagName('TotalPrice');
if ($TotalPrice->length > 0) {
    $TotalPrice = $TotalPrice->item(0)->nodeValue;
}else {
    $TotalPrice = "";
}
error_log("\r\n TotalPrice: $TotalPrice \r\n", 3, "/srv/www/htdocs/error_log");
$PendingAmount = $Purchase->item(0)->getElementsByTagName('PendingAmount');
if ($PendingAmount->length > 0) {
    $PendingAmount = $PendingAmount->item(0)->nodeValue;
}else {
    $PendingAmount = "";
}
$Agency = $Purchase->item(0)->getElementsByTagName('Agency');
$Code = $Agency->item(0)->getElementsByTagName('Code');
if ($Code->length > 0) {
    $Code = $Code->item(0)->nodeValue;
}else {
    $Code = "";
}
$Branch = $Agency->item(0)->getElementsByTagName('Branch');
if ($Branch->length > 0) {
    $Branch = $Branch->item(0)->nodeValue;
}else {
    $Branch = "";
}
$Currency = $Purchase->item(0)->getElementsByTagName('Currency');
$codeCurrency = $Currency->item(0)->getElementsByTagName('code');
if ($codeCurrency->length > 0) {
    $codeCurrency = $codeCurrency->item(0)->nodeValue;
}else {
    $codeCurrency = "";
}
$PaymentData = $Purchase->item(0)->getElementsByTagName('PaymentData');
$PaymentType = $PaymentData->item(0)->getElementsByTagName('PaymentType');
$codePaymentType = $PaymentType->item(0)->getElementsByTagName('code');
if ($codePaymentType->length > 0) {
    $codePaymentType = $codePaymentType->item(0)->nodeValue;
}else {
    $codePaymentType = "";
}
error_log("\r\n codePaymentType: $codePaymentType \r\n", 3, "/srv/www/htdocs/error_log");
//ServiceList
$ServiceList = $Purchase->item(0)->getElementsByTagName('ServiceList');
$Service = $ServiceList->item(0)->getElementsByTagName('Service');
$transferType = $Service->item(0)->getAttribute("transferType");
$SPUI = $Service->item(0)->getAttribute("SPUI");
$StatusService = $Service->item(0)->getElementsByTagName('Status');
if ($StatusService->length > 0) {
    $StatusService = $StatusService->item(0)->nodeValue;
}else {
    $StatusService = "";
}
$StatusService = $Service->item(0)->getElementsByTagName('Status');
if ($StatusService->length > 0) {
    $StatusService = $StatusService->item(0)->nodeValue;
}else {
    $StatusService = "";
}
$TotalAmount = $Service->item(0)->getElementsByTagName('TotalAmount');
if ($TotalAmount->length > 0) {
    $TotalAmount = $TotalAmount->item(0)->nodeValue;
}else {
    $TotalAmount = "";
}
error_log("\r\n TotalAmount: $TotalAmount \r\n", 3, "/srv/www/htdocs/error_log");
$NetPrice = $Service->item(0)->getElementsByTagName('NetPrice');
if ($NetPrice->length > 0) {
    $NetPrice = $NetPrice->item(0)->nodeValue;
}else {
    $NetPrice = "";
}
$Commission = $Service->item(0)->getElementsByTagName('Commission');
if ($Commission->length > 0) {
    $Commission = $Commission->item(0)->nodeValue;
}else {
    $Commission = "";
}
$ComissionPercentage = $Service->item(0)->getElementsByTagName('ComissionPercentage');
if ($ComissionPercentage->length > 0) {
    $ComissionPercentage = $ComissionPercentage->item(0)->nodeValue;
}else {
    $ComissionPercentage = "";
}
$RetailPrice = $Service->item(0)->getElementsByTagName('RetailPrice');
if ($RetailPrice->length > 0) {
    $RetailPrice = $RetailPrice->item(0)->nodeValue;
}else {
    $RetailPrice = "";
}
error_log("\r\n RetailPrice: $RetailPrice \r\n", 3, "/srv/www/htdocs/error_log");
$ComissionVAT = $Service->item(0)->getElementsByTagName('ComissionVAT');
if ($ComissionVAT->length > 0) {
    $ComissionVAT = $ComissionVAT->item(0)->nodeValue;
}else {
    $ComissionVAT = "";
}
$DepartureTravelInfo = $Service->item(0)->getElementsByTagName('DepartureTravelInfo');
if ($DepartureTravelInfo->length > 0) {
    $DepartureTravelInfo = $DepartureTravelInfo->item(0)->nodeValue;
}else {
    $DepartureTravelInfo = "";
}
error_log("\r\n DepartureTravelInfo: $DepartureTravelInfo \r\n", 3, "/srv/www/htdocs/error_log");
//ContractList
$ContractList = $Service->item(0)->getElementsByTagName('ContractList');
$Contract = $ContractList->item(0)->getElementsByTagName('Contract');
$Name = $Contract->item(0)->getElementsByTagName('Name');
if ($Name->length > 0) {
    $Name = $Name->item(0)->nodeValue;
}else {
    $Name = "";
}
$Sequence = $Contract->item(0)->getElementsByTagName('Sequence');
if ($Sequence->length > 0) {
    $Sequence = $Sequence->item(0)->nodeValue;
}else {
    $Sequence = "";
}
$IncomingOffice = $Contract->item(0)->getElementsByTagName('IncomingOffice');
$codeIncomingOffice = $IncomingOffice->item(0)->getElementsByTagName('code');
if ($codeIncomingOffice->length > 0) {
    $codeIncomingOffice = $codeIncomingOffice->item(0)->nodeValue;
}else {
    $codeIncomingOffice = "";
}
error_log("\r\n codeIncomingOffice: $codeIncomingOffice \r\n", 3, "/srv/www/htdocs/error_log");
//Supplier
$Supplier = $Service->item(0)->getElementsByTagName('Supplier');
$nameSupplier = $Supplier->item(0)->getAttribute("name");
$vatNumberSupplier = $Supplier->item(0)->getAttribute("vatNumber");
error_log("\r\n vatNumberSupplier: $vatNumberSupplier \r\n", 3, "/srv/www/htdocs/error_log");

//DateFrom
$DateFrom = $Service->item(0)->getElementsByTagName('DateFrom');
$date = $DateFrom->item(0)->getAttribute("date");

//Currency
$Currency = $Service->item(0)->getElementsByTagName('Currency');
if ($Currency->length > 0) {
    $codeC = $Currency->item(0)->getAttribute("code");
    $textC = $Currency->item(0)->nodeValue;
}else {
    $textC = "";
}
error_log("\r\n textC: $textC \r\n", 3, "/srv/www/htdocs/error_log");
//SellingPrice
$SellingPrice = $Service->item(0)->getElementsByTagName('SellingPrice');
$mandatorySellingPrice = $SellingPrice->item(0)->getAttribute("mandatory");
$textSellingPrice = $SellingPrice->item(0)->getElementsByTagName('text');
if ($textSellingPrice->length > 0) {
    $textSellingPrice = $textSellingPrice->item(0)->nodeValue;
}else {
    $textSellingPrice = "";
}

//AdditionalCostList
$AdditionalCostList = $Service->item(0)->getElementsByTagName('AdditionalCostList');
$AdditionalCost = $AdditionalCostList->item(0)->getElementsByTagName("AdditionalCost");
for ($kAux=0; $kAux < $AdditionalCost->length; $kAux++) { 
    $typeAC = $AdditionalCost->item($kAux)->getAttribute("type");
    $PriceAC = $AdditionalCost->item($kAux)->getElementsByTagName('Price');
    $AmountPrice = $PriceAC->item(0)->getElementsByTagName('Amount');
    if ($AmountPrice->length > 0) {
        $AmountPrice = $AmountPrice->item(0)->nodeValue;
    }else {
        $AmountPrice = "";
    }
}

//ModificationPolicyList
$ModificationPolicyList = $Service->item(0)->getElementsByTagName('ModificationPolicyList');
$ModificationPolicy = $ModificationPolicyList->item(0)->getElementsByTagName('ModificationPolicy');
for ($yAux=0; $yAux < $ModificationPolicy->length; $yAux++) { 
    $ModificationPolicy = $ModificationPolicy[$yAux];
}

//TransferInfo
$TransferInfo = $Service->item(0)->getElementsByTagName('TransferInfo');
$CodeTransferInfo = $TransferInfo->item(0)->getElementsByTagName('Code');
if ($CodeTransferInfo->length > 0) {
    $CodeTransferInfo = $CodeTransferInfo->item(0)->nodeValue;
}else {
    $CodeTransferInfo = "";
}
error_log("\r\n CodeTransferInfo: $CodeTransferInfo \r\n", 3, "/srv/www/htdocs/error_log");
$DescriptionList = $TransferInfo->item(0)->getElementsByTagName('DescriptionList');
$Description = $DescriptionList->item(0)->getElementsByTagName('Description');
for ($zAux=0; $zAux < $Description->length; $zAux++) { 
    $typeD = $Description->item($zAux)->getAttribute("type");
    $languageCode = $Description->item($zAux)->getAttribute("languageCode");
    $textD = $Description->item($zAux)->nodeValue;
}
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
$Type = $TransferInfo->item(0)->getElementsByTagName('Type');
if ($Type->length > 0) {
    $codeType = $Type->item(($Type->length) - 1)->getAttribute("code");
} else {
    $codeType = "";
}
$VehicleType = $TransferInfo->item(0)->getElementsByTagName('VehicleType');
$codeVT = $VehicleType->item(0)->getElementsByTagName('code');
if ($codeVT->length > 0) {
    $codeVT = $codeVT->item(0)->nodeValue;
} else {
    $codeVT = "";
}
error_log("\r\n codeVT: $codeVT \r\n", 3, "/srv/www/htdocs/error_log");
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
    error_log("\r\n maxstops: $maxstops \r\n", 3, "/srv/www/htdocs/error_log");
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


//Paxes
$Paxes = $Service->item(0)->getElementsByTagName('Paxes');
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
$GuestList = $Paxes->item(0)->getElementsByTagName('GuestList');
$Customer = $GuestList->item(0)->getElementsByTagName('Customer');
$typeCustomer = $Customer->item(0)->getAttribute("type");
$CustomerId = $Customer->item(0)->getElementsByTagName('CustomerId');
if ($CustomerId->length > 0) {
    $CustomerId = $CustomerId->item(0)->nodeValue;
} else {
    $CustomerId = "";
}
$Age = $Customer->item(0)->getElementsByTagName('Age');
if ($Age->length > 0) {
    $Age = $Age->item(0)->nodeValue;
} else {
    $Age = "";
}
error_log("\r\n Age: $Age \r\n", 3, "/srv/www/htdocs/error_log");
//PickupLocation
$PickupLocation = $Service->item(0)->getElementsByTagName('PickupLocation');
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
    error_log("\r\n NamePL: $NamePL \r\n", 3, "/srv/www/htdocs/error_log");
    $TerminalTypePL = $PickupLocation->item(0)->getElementsByTagName('TerminalType');
    if ($TerminalTypePL->length > 0) {
        $TerminalTypePL = $TerminalTypePL->item(0)->nodeValue;
    } else {
        $TerminalTypePL = "";
    }
    error_log("\r\n TerminalTypePL: $TerminalTypePL \r\n", 3, "/srv/www/htdocs/error_log");
    $TransferZonePL = $PickupLocation->item(0)->getElementsByTagName('TransferZone');
    $CodeTZpl = $TransferZonePL->item(0)->getElementsByTagName('Code');
    if ($CodeTZpl->length > 0) {
        $CodeTZpl = $CodeTZpl->item(0)->nodeValue;
    } else {
        $CodeTZpl = "";
    }
    error_log("\r\n CodeTZpl: $CodeTZpl \r\n", 3, "/srv/www/htdocs/error_log");
    //
    $CountryPL = $PickupLocation->item(0)->getElementsByTagName('Country');
    $codeCountryPL = $CountryPL->item(0)->getAttribute('code');
    error_log("\r\n codeCountryPL: $codeCountryPL \r\n", 3, "/srv/www/htdocs/error_log");
    $NameCountryPL = $CountryPL->item(0)->getElementsByTagName('Name');
    if ($NameCountryPL->length > 0) {
        $NameCountryPL = $NameCountryPL->item(0)->nodeValue;
    } else {
        $NameCountryPL = "";
    }
    error_log("\r\n NameCountryPL: $NameCountryPL \r\n", 3, "/srv/www/htdocs/error_log");
}

//DestinationLocation
$DestinationLocation = $Service->item(0)->getElementsByTagName('DestinationLocation');
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
$TransferZoneDL = $DestinationLocation->item(0)->getElementsByTagName('TransferZone');
$CodeTransferZoneDL = $TransferZoneDL->item(0)->getElementsByTagName('Code');
if ($CodeTransferZoneDL->length > 0) {
    $CodeTransferZoneDL = $CodeTransferZoneDL->item(0)->nodeValue;
} else {
    $CodeTransferZoneDL = "";
}
$LocationInformation = $DestinationLocation->item(0)->getElementsByTagName('LocationInformation');
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
$longitude = $GPSPoint->item(0)->getAttribute('longitude');
$latitude = $GPSPoint->item(0)->getAttribute('latitude');
error_log("\r\n latitude: $latitude \r\n", 3, "/srv/www/htdocs/error_log");

//ProductSpecifications
$ProductSpecifications = $Service->item(0)->getElementsByTagName('ProductSpecifications');
$MasterServiceType = $ProductSpecifications->item(0)->getElementsByTagName('MasterServiceType');
$codeMST = $MasterServiceType->item(0)->getAttribute('code');
$nameMST = $MasterServiceType->item(0)->getAttribute('name');
$MasterProductType = $ProductSpecifications->item(0)->getElementsByTagName('MasterProductType');
$codeMPT = $MasterProductType->item(0)->getAttribute('code');
$nameMPT = $MasterProductType->item(0)->getAttribute('name');
$MasterVehicleType = $ProductSpecifications->item(0)->getElementsByTagName('MasterVehicleType');
$codeMVT = $MasterVehicleType->item(0)->getAttribute('code');
$nameMVT = $MasterVehicleType->item(0)->getAttribute('name');
$TransferGeneralInfoList = $ProductSpecifications->item(0)->getElementsByTagName('TransferGeneralInfoList');
$TransferBulletPoint = $TransferGeneralInfoList->item(0)->getElementsByTagName('TransferBulletPoint');
for ($sAux = 0; $sAux < $TransferBulletPoint->length; $sAux ++) {
    $idTBP = $TransferBulletPoint->item($sAux)->getAttribute('id');
    $orderTBP = $TransferBulletPoint->item($sAux)->getAttribute('order');
    $DescriptionTBP = $TransferBulletPoint->item($sAux)->getElementsByTagName('Description');
    if ($DescriptionTBP->length > 0) {
        $DescriptionTBP = $DescriptionTBP->item(0)->nodeValue;
    } else {
        $DescriptionTBP = "";
    }
    $ValueTBP = $TransferBulletPoint->item($sAux)->getElementsByTagName('Value');
    if ($ValueTBP->length > 0) {
        $ValueTBP = $ValueTBP->item(0)->nodeValue;
    } else {
        $ValueTBP = "";
    }
    $MetricTBP = $TransferBulletPoint->item($sAux)->getElementsByTagName('Metric');
    if ($MetricTBP->length > 0) {
        $MetricTBP = $MetricTBP->item(0)->nodeValue;
    } else {
        $MetricTBP = "";
    }
}

//TransferPickupTime
$TransferPickupTime = $Service->item(0)->getElementsByTagName('TransferPickupTime');
$timeTPT = $TransferPickupTime->item(0)->getAttribute('time');
$dateTPT = $TransferPickupTime->item(0)->getAttribute('date');
error_log("\r\n dateTPT: $dateTPT \r\n", 3, "/srv/www/htdocs/error_log");

//TransferPickupInformation
$TransferPickupInformation = $Service->item(0)->getElementsByTagName('TransferPickupInformation');
$AddressTPI = $TransferPickupInformation->item(0)->getElementsByTagName('Address');
if ($AddressTPI->length > 0) {
    $AddressTPI = $AddressTPI->item(0)->nodeValue;
} else {
    $AddressTPI = "";
}
$NumberTPI = $TransferPickupInformation->item(0)->getElementsByTagName('Number');
if ($NumberTPI->length > 0) {
    $NumberTPI = $NumberTPI->item(0)->nodeValue;
} else {
    $NumberTPI = "";
}
$TownTPI = $TransferPickupInformation->item(0)->getElementsByTagName('Town');
if ($TownTPI->length > 0) {
    $TownTPI = $TownTPI->item(0)->nodeValue;
} else {
    $TownTPI = "";
}
$ZipTPI = $TransferPickupInformation->item(0)->getElementsByTagName('Zip');
if ($ZipTPI->length > 0) {
    $ZipTPI = $ZipTPI->item(0)->nodeValue;
} else {
    $ZipTPI = "";
}
$DescriptionTPI = $TransferPickupInformation->item(0)->getElementsByTagName('Description');
if ($DescriptionTPI->length > 0) {
    $DescriptionTPI = $DescriptionTPI->item(0)->nodeValue;
} else {
    $DescriptionTPI = "";
}
$GPSPoint = $TransferPickupInformation->item(0)->getElementsByTagName('GPSPoint');
$longitudeTPI = $GPSPoint->item(0)->getAttribute('longitude');
$latitudeTPI = $GPSPoint->item(0)->getAttribute('latitude');
error_log("\r\n latitudeTPI: $latitudeTPI \r\n", 3, "/srv/www/htdocs/error_log");

//ArrivalTravelInfo
$ArrivalTravelInfo = $Service->item(0)->getElementsByTagName('ArrivalTravelInfo');
$ArrivalInfo = $ArrivalTravelInfo->item(0)->getElementsByTagName('ArrivalInfo');
$CodeA = $ArrivalInfo->item(0)->getElementsByTagName('Code');
if ($CodeA->length > 0) {
    $CodeA = $CodeA->item(0)->nodeValue;
} else {
    $CodeA = "";
}
$NameA = $ArrivalInfo->item(0)->getElementsByTagName('Name');
if ($NameA->length > 0) {
    $NameA = $NameA->item(0)->nodeValue;
} else {
    $NameA = "";
}
$TerminalTypeA = $ArrivalInfo->item(0)->getElementsByTagName('TerminalType');
if ($TerminalTypeA->length > 0) {
    $TerminalTypeA = $TerminalTypeA->item(0)->nodeValue;
} else {
    $TerminalTypeA = "";
}
$DateTimeA = $ArrivalInfo->item(0)->getElementsByTagName('DateTime');
$timeA = $DateTimeA->item(0)->getAttribute('time');
$dateA = $DateTimeA->item(0)->getAttribute('date');
$CountryA = $PickupLocation->item(0)->getElementsByTagName('Country');
$codeCountryA = $CountryA->item(0)->getAttribute('code');
$NameCountryA = $CountryA->item(0)->getElementsByTagName('Name');
if ($NameCountryA->length > 0) {
    $NameCountryA = $NameCountryA->item(0)->nodeValue;
} else {
    $NameCountryA = "";
}
error_log("\r\n NameCountryA: $NameCountryA \r\n", 3, "/srv/www/htdocs/error_log");
//CancellationPolicies
$CancellationPolicies = $Service->item(0)->getElementsByTagName('CancellationPolicies');
$CancellationPolicy = $CancellationPolicies->item(0)->getElementsByTagName('CancellationPolicy');
$timeCP = $CancellationPolicy->item(0)->getAttribute('time');
$dateFromCP = $CancellationPolicy->item(0)->getAttribute('dateFrom');
$amountCP = $CancellationPolicy->item(0)->getAttribute('amount');
error_log("\r\n amountCP: $amountCP \r\n", 3, "/srv/www/htdocs/error_log");


if ($TotalPrice >= $data[$id]['transferprice']) {
    // Price Change
    $tmp = $TotalPrice;
    if ($hotelbedsTransfersMarkup != "") {
        if (is_numeric($hotelbedsTransfersMarkup)) {
            $tmp = $tmp + (($tmp * $hotelbedsTransfersMarkup) / 100);
            $tmp = number_format($tmp, 2);
        }
    }
    $data[$id]['transferprice'] = $tmp;
}
error_log("\r\n PASSOU AQUI \r\n", 3, "/srv/www/htdocs/error_log");
if ($vatNumberSupplier != "") {
    $data[$id]['transacno'] = $vatNumberSupplier;
    $data[$id]['holidayvalue'] = $TotalPrice;
    $data[$id]['currencycode'] = $codeCurrency;
    try {
        $sql = new Sql($db);
        $delete = $sql->delete();
        $delete->from('quote_session_hotelbedstransfers');
        $delete->where(array(
            'session_id' => $session_id . "-totals"
        ));
        error_log("\r\n PASSOU AQUI 2 \r\n", 3, "/srv/www/htdocs/error_log");
        $statement = $sql->prepareStatementForSqlObject($delete);
        $results = $statement->execute();
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('quote_session_hotelbedstransfers');
        $insert->values(array(
            'session_id' => $session_id . "-totals",
            'xmlrequest' => (string) $xmlrequest,
            'xmlresult' => (string) $xmlresult,
            'data' => base64_encode(serialize($data[$id])),
            'searchsettings' => ""
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        error_log("\r\n PASSOU AQUI 3 \r\n", 3, "/srv/www/htdocs/error_log");
    } catch (\Exception $e) {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($e->getMessage());
    }
}
$transfers = $data[$id];
$db->getDriver()
    ->getConnection()
    ->disconnect();
    error_log("\r\n EOF \r\n", 3, "/srv/www/htdocs/error_log");
?>