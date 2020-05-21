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
echo "COMECOU RATE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://sws-crt.cert.havail.sabre.com';

date_default_timezone_set("UTC");
$datetime = date('Y-m-d\TH:i:s');
$CPAId = 'IA8H';
$BinarySecurityToken = 'Shared/IDL:IceSess\/SessMgr:1\.0.IDL/Common/!ICESMS\/ACPCRTD!ICESMSLB\/CRT.LB!1586774481758!4960!17';

$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
<soap:Header>
    <eb:MessageHeader eb:version="2.0.0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader">
        <eb:From>
            <eb:PartyId>111</eb:PartyId>
        </eb:From>
        <eb:To>
            <eb:PartyId>222</eb:PartyId>
        </eb:To>
        <eb:CPAId>IA8H</eb:CPAId>
        <eb:ConversationId>1234567890</eb:ConversationId>
        <eb:Action>GetHotelDetailsRQ</eb:Action>
        <eb:MessageData>
            <eb:MessageId>LbQ26Jnofb4Q8f3Pk15Mg5</eb:MessageId>
            <eb:Timestamp>2020-04-13T11:11:31</eb:Timestamp>
        </eb:MessageData>
    </eb:MessageHeader>
    <wsse:Security xmlns:wsse="http://schemas.xmlsoap.org/ws/2002/12/secext">
        <wsse:BinarySecurityToken>' . $BinarySecurityToken . '</wsse:BinarySecurityToken>
    </wsse:Security>
</soap:Header>
<soap:Body>
    <GetHotelDetailsRQ xmlns="http://services.sabre.com/hotel/details/v2_0_0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="2.0.0" xsi:schemaLocation="http://services.sabre.com/hotel/details/v2_0_0 GetHotelDetailsRQ_v2.0.0.xsd">
  <SearchCriteria>
            <HotelRefs>
                <HotelRef HotelCode="0044303" CodeContext="SABRE"/>
            </HotelRefs>
            <RateInfoRef CurrencyCode="USD" PrepaidQualifier="IncludePrepaid">
                <StayDateRange StartDate="2020-11-22" EndDate="2020-11-24"/>
                <Rooms>
                    <Room Index="1" Adults="2" Children="0">
                    </Room>
                </Rooms>
                <InfoSource>100</InfoSource>
            </RateInfoRef>
            <HotelContentRef>
                <DescriptiveInfoRef>
                    <PropertyInfo>true</PropertyInfo>
                    <LocationInfo>true</LocationInfo>
                    <Amenities>true</Amenities>
                    <Descriptions>
                        <Description Type="ShortDescription"/>
                        <Description Type="Alerts"/>
                        <Description Type="Dining"/>
                        <Description Type="Facilities"/>
                        <Description Type="Recreation"/>
                        <Description Type="Services"/>
                        <Description Type="Attractions"/>
                        <Description Type="CancellationPolicy"/>
                        <Description Type="DepositPolicy"/>
                        <Description Type="Directions"/>
                        <Description Type="Policies"/>
                        <Description Type="SafetyInfo"/>
                        <Description Type="TransportationInfo"/>
                    </Descriptions>
                    <AdditionalCharges>true</AdditionalCharges>
                  <PointsOfInterest>true</PointsOfInterest>
                    <Airports>true</Airports>
                    <AcceptedCreditCards>true</AcceptedCreditCards>
                    <GuaranteePolicies>true</GuaranteePolicies>
                         <SecurityFeatures>true</SecurityFeatures>
                </DescriptiveInfoRef>
                <MediaRef MaxItems="ALL">
                    <MediaTypes>
                        <Images>
                            <Image Type="MEDIUM"/>
                        </Images>
                        <PanoramicMedias>
                            <PanoramicMedia Type="HD360"/>
                        </PanoramicMedias>
                        <Videos>
                            <Video Type="VIDEO360"/>
                        </Videos>
                    </MediaTypes>
                    <AdditionalInfo>
                        <Info Type="CAPTION">true</Info>
                    </AdditionalInfo>
                    <Languages>
                        <Language Code="EN"/>
                    </Languages>
                </MediaRef>
            </HotelContentRef>
        </SearchCriteria>
    </GetHotelDetailsRQ>
</soap:Body>
</soap:Envelope>
';
echo '<xmp>';
var_dump($raw);
echo '</xmp>';

$headers = array(
    "Content-Type: text/xml;charset=utf-8",
    "Accept-Encoding: gzip",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
curl_close($ch);

echo '<xmp>';
var_dump($response);
echo '</xmp>';


$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Header = $Envelope->item(0)->getElementsByTagName("Header");
$MessageHeader = $Header->item(0)->getElementsByTagName("MessageHeader");
if ($MessageHeader->length > 0) {
    $From = $MessageHeader->item(0)->getElementsByTagName("From");
    if ($From->length > 0) {
        $FromPartyId = $From->item(0)->getElementsByTagName("PartyId");
        if ($FromPartyId->length > 0) {
            $type = $FromPartyId->item(0)->getAttribute("type");
            $FromPartyId = $FromPartyId->item(0)->nodeValue;
        } else {
            $FromPartyId = "";
        }
    }
    $To = $MessageHeader->item(0)->getElementsByTagName("To");
    if ($To->length > 0) {
        $ToPartyId = $To->item(0)->getElementsByTagName("PartyId");
        if ($ToPartyId->length > 0) {
            $ToPartyId = $ToPartyId->item(0)->nodeValue;
        } else {
            $ToPartyId = "";
        }
    }
    $CPAId = $MessageHeader->item(0)->getElementsByTagName("CPAId");
    if ($CPAId->length > 0) {
        $CPAId = $CPAId->item(0)->nodeValue;
    } else {
        $CPAId = "";
    }
    $ConversationId = $MessageHeader->item(0)->getElementsByTagName("ConversationId");
    if ($ConversationId->length > 0) {
        $ConversationId = $ConversationId->item(0)->nodeValue;
    } else {
        $ConversationId = "";
    }
    $MessageData = $MessageHeader->item(0)->getElementsByTagName("MessageData");
    if ($MessageData->length > 0) {
        $MessageId = $MessageData->item(0)->getElementsByTagName("MessageId");
        if ($MessageId->length > 0) {
            $MessageId = $MessageId->item(0)->nodeValue;
        } else {
            $MessageId = "";
        }
        $RefToMessageId = $MessageData->item(0)->getElementsByTagName("RefToMessageId");
        if ($RefToMessageId->length > 0) {
            $RefToMessageId = $RefToMessageId->item(0)->nodeValue;
        } else {
            $RefToMessageId = "";
        }
    }
}
$Security = $Header->item(0)->getElementsByTagName("Security");
if ($Security->length > 0) {
    $BinarySecurityToken = $Security->item(0)->getElementsByTagName("BinarySecurityToken");
    if ($BinarySecurityToken->length > 0) {
        $BinarySecurityToken = $BinarySecurityToken->item(0)->nodeValue;
    } else {
        $BinarySecurityToken = "";
    }
}

$Body = $Envelope->item(0)->getElementsByTagName("Body");
$GetHotelDetailsRS = $Body->item(0)->getElementsByTagName("GetHotelDetailsRS");
if ($GetHotelDetailsRS->length > 0) {
    $HotelDetailsInfo = $GetHotelDetailsRS->item(0)->getElementsByTagName("HotelDetailsInfo");
    if ($HotelDetailsInfo->length > 0) {
        $HotelInfo = $HotelDetailsInfo->item(0)->getElementsByTagName("HotelInfo");
        if ($HotelInfo->length > 0) {
            $HotelCode = $HotelInfo->item(0)->getAttribute("HotelCode");
            $HotelName = $HotelInfo->item(0)->getAttribute("HotelName");
            $SabreHotelCode = $HotelInfo->item(0)->getAttribute("SabreHotelCode");
            $BrandCode = $HotelInfo->item(0)->getAttribute("BrandCode");
            $CodeContext = $HotelInfo->item(0)->getAttribute("CodeContext");
            $BrandName = $HotelInfo->item(0)->getAttribute("BrandName");
            $Status = $HotelInfo->item(0)->getAttribute("Status");
            $ChainCode = $HotelInfo->item(0)->getAttribute("ChainCode");
            $ChainName = $HotelInfo->item(0)->getAttribute("ChainName");
        }
        $HotelDescriptiveInfo = $HotelDetailsInfo->item(0)->getElementsByTagName("HotelDescriptiveInfo");
        if ($HotelDescriptiveInfo->length > 0) {
            $PropertyInfo = $HotelDescriptiveInfo->item(0)->getElementsByTagName("PropertyInfo");
            if ($PropertyInfo->length > 0) {
                $Rooms = $PropertyInfo->item(0)->getAttribute("Rooms");
                $Floors = $PropertyInfo->item(0)->getAttribute("Floors");
                $PropertyFeatures = $PropertyInfo->item(0)->getElementsByTagName("PropertyFeatures");
                if ($PropertyFeatures->length > 0) {
                    $PropertyFeatures = $PropertyInfo->item(0)->getElementsByTagName("PropertyFeatures");
                    if ($PropertyFeatures->length > 0) {
                        $PropertyFeaturesInfo = $PropertyFeatures->item(0)->getAttribute("Info");
                        $PropertyFeatures = $PropertyFeatures->item(0)->nodeValue;
                    } else {
                        $PropertyFeatures = "";
                    }
                }
                $Policies = $PropertyInfo->item(0)->getElementsByTagName("Policies");
                if ($Policies->length > 0) {
                    $Policy = $Policies->item(0)->getElementsByTagName("Policy");
                    if ($Policy->length > 0) {
                        for ($i=0; $i < $Policy->length; $i++) { 
                            $Text = $Policy->item($i)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                $Type = $Text->item(0)->getAttribute("Type");
                                $Text = $Text->item(0)->nodeValue;
                            } else {
                                $Text = "";
                            }
                        }
                    }
                }
                $PropertyQualityInfo = $PropertyInfo->item(0)->getElementsByTagName("PropertyQualityInfo");
                if ($PropertyQualityInfo->length > 0) {
                    $PropertyQuality = $PropertyQualityInfo->item(0)->getElementsByTagName("PropertyQuality");
                    if ($PropertyQuality->length > 0) {
                        $Code = $PropertyQuality->item(0)->getAttribute("Code");
                        $Description = $PropertyQuality->item(0)->getAttribute("Description");
                    }
                }
                $PropertyTaxes = $PropertyInfo->item(0)->getElementsByTagName("PropertyTaxes");
                if ($PropertyTaxes->length > 0) {
                    $PropertyTax = $PropertyTaxes->item(0)->getElementsByTagName("PropertyTax");
                    if ($PropertyTax->length > 0) {
                        $PropertyTaxInfo = $PropertyTax->item(0)->getAttribute("Info");
                        $PropertyTax = $PropertyTax->item(0)->nodeValue;
                    } else {
                        $PropertyTax = "";
                    }
                }
                $RoomTraits = $PropertyInfo->item(0)->getElementsByTagName("RoomTraits");
                if ($RoomTraits->length > 0) {
                    $RoomTrait = $RoomTraits->item(0)->getElementsByTagName("RoomTrait");
                    if ($RoomTrait->length > 0) {
                        $RoomTraitInfo = $RoomTrait->item(0)->getAttribute("Info");
                        $RoomTrait = $RoomTrait->item(0)->nodeValue;
                    } else {
                        $RoomTrait = "";
                    }
                }
                $RequestableAmenities = $PropertyInfo->item(0)->getElementsByTagName("RequestableAmenities");
                if ($RequestableAmenities->length > 0) {
                    $Amenity = $RequestableAmenities->item(0)->getElementsByTagName("Amenity");
                    if ($Amenity->length > 0) {
                        for ($i=0; $i < $Amenity->length; $i++) { 
                            $Info = $Amenity->item($i)->getAttribute("Info");
                            $Amenity = $Amenity->item($i)->nodeValue;
                        }
                    }
                }
            }
            $LocationInfo = $HotelDescriptiveInfo->item(0)->getElementsByTagName("LocationInfo");
            if ($LocationInfo->length > 0) {
                $Longitude = $LocationInfo->item(0)->getAttribute("Longitude");
                $Latitude = $LocationInfo->item(0)->getAttribute("Latitude");
                $Address = $LocationInfo->item(0)->getElementsByTagName("Address");
                if ($Address->length > 0) {
                    $AddressLine1 = $Address->item(0)->getElementsByTagName("AddressLine1");
                    if ($AddressLine1->length > 0) {
                        $AddressLine1 = $AddressLine1->item(0)->nodeValue;
                    } else {
                        $AddressLine1 = "";
                    }
                    $CityName = $Address->item(0)->getElementsByTagName("CityName");
                    if ($CityName->length > 0) {
                        $CityCode = $CityName->item(0)->getAttribute("CityCode");
                        $CityName = $CityName->item(0)->nodeValue;
                    } else {
                        $CityName = "";
                    }
                    $StateProv = $Address->item(0)->getElementsByTagName("StateProv");
                    if ($StateProv->length > 0) {
                        $StateCode = $StateProv->item(0)->getAttribute("StateCode");
                        $StateProv = $StateProv->item(0)->nodeValue;
                    } else {
                        $StateProv = "";
                    }
                    $PostalCode = $Address->item(0)->getElementsByTagName("PostalCode");
                    if ($PostalCode->length > 0) {
                        $PostalCode = $PostalCode->item(0)->nodeValue;
                    } else {
                        $PostalCode = "";
                    }
                    $CountryName = $Address->item(0)->getElementsByTagName("CountryName");
                    if ($CountryName->length > 0) {
                        $CountryNameCode = $CountryName->item(0)->getAttribute("Code");
                        $CountryName = $CountryName->item(0)->nodeValue;
                    } else {
                        $CountryName = "";
                    }
                }
                $Neighborhoods = $LocationInfo->item(0)->getElementsByTagName("Neighborhoods");
                if ($Neighborhoods->length > 0) {
                    $Neighborhood = $Neighborhoods->item(0)->getElementsByTagName("Neighborhood");
                    if ($Neighborhood->length > 0) {
                        $Neighborhood = $Neighborhood->item(0)->nodeValue;
                    } else {
                        $Neighborhood = "";
                    }
                }
                $Contact = $LocationInfo->item(0)->getElementsByTagName("Contact");
                if ($Contact->length > 0) {
                    $Fax = $Contact->item(0)->getAttribute("Fax");
                    $Phone = $Contact->item(0)->getAttribute("Phone");
                }
            }
            $Amenities = $HotelDescriptiveInfo->item(0)->getElementsByTagName("Amenities");
            if ($Amenities->length > 0) {
                $Amenity = $Amenities->item(0)->getElementsByTagName("Amenity");
                if ($Amenity->length > 0) {
                    $amenit = "";
                    for ($j=0; $j < $Amenity->length; $j++) { 
                        $Code = $Amenity->item($j)->getAttribute("Code");
                        $Description = $Amenity->item($j)->getAttribute("Description");
                        $amenit = $Amenity->item($j)->nodeValue;
                    }
                }
            }
            $SecurityFeatures = $HotelDescriptiveInfo->item(0)->getElementsByTagName("SecurityFeatures");
            if ($SecurityFeatures->length > 0) {
                $SecurityFeature = $SecurityFeatures->item(0)->getElementsByTagName("SecurityFeature");
                if ($SecurityFeature->length > 0) {
                    $SecurityFeatureCode = $SecurityFeature->item(0)->getAttribute("Code");
                    $SecurityFeature = $SecurityFeature->item(0)->nodeValue;
                } else {
                    $SecurityFeature = "";
                }
            }
            $AdditionalCharges = $HotelDescriptiveInfo->item(0)->getElementsByTagName("AdditionalCharges");
            if ($AdditionalCharges->length > 0) {
                $AdditionalCharge = $AdditionalCharges->item(0)->getElementsByTagName("AdditionalCharge");
                if ($AdditionalCharge->length > 0) {
                    for ($k=0; $k < $AdditionalCharge->length; $k++) { 
                        $Code = $AdditionalCharge->item($k)->getAttribute("Code");
                        $Description = $AdditionalCharge->item($k)->getAttribute("Description");
                        $Amount = $AdditionalCharge->item($k)->getAttribute("Amount");
                    }
                }
            }
            $Descriptions = $HotelDescriptiveInfo->item(0)->getElementsByTagName("Descriptions");
            if ($Descriptions->length > 0) {
                $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    for ($l=0; $l < $Description->length; $l++) { 
                        $Text = $Description->item($l)->getElementsByTagName("Text");
                        if ($Text->length > 0) {
                            $Type = $Text->item(0)->getAttribute("Type");
                            $Text = $Text->item(0)->nodeValue;
                        } else {
                            $Text = "";
                        }
                    }
                }
            }
            $Airports = $HotelDescriptiveInfo->item(0)->getElementsByTagName("Airports");
            if ($Airports->length > 0) {
                $Airport = $Airports->item(0)->getElementsByTagName("Airport");
                if ($Airport->length > 0) {
                    for ($r=0; $r < $Airport->length; $r++) { 
                        $AirportOrCityCountryCode = $Airport->item($r)->getAttribute("AirportOrCityCountryCode");
                        $AirportOrCityStateCode = $Airport->item($r)->getAttribute("AirportOrCityStateCode");
                        $DirectionId = $Airport->item($r)->getAttribute("DirectionId");
                        $AirportOrCityCode = $Airport->item($r)->getAttribute("AirportOrCityCode");
                        $Primary = $Airport->item($r)->getAttribute("Primary");
                        $AirportOrCityDescription = $Airport->item($r)->getAttribute("AirportOrCityDescription");
                        $DistanceFromHotel = $Airport->item($r)->getAttribute("DistanceFromHotel");
                        $UOM = $Airport->item($r)->getAttribute("UOM");
                        $TransportationCode = $Airport->item($r)->getAttribute("TransportationCode");
                    }
                }
            }
            $PointsOfInterest = $HotelDescriptiveInfo->item(0)->getElementsByTagName("PointsOfInterest");
            if ($PointsOfInterest->length > 0) {
                $PointOfInterest = $PointsOfInterest->item(0)->getElementsByTagName("PointOfInterest");
                if ($PointOfInterest->length > 0) {
                    for ($s=0; $s < $PointOfInterest->length; $s++) { 
                        $UOM = $PointOfInterest->item($s)->getAttribute("UOM");
                        $StateCode = $PointOfInterest->item($s)->getAttribute("StateCode");
                        $DirectionId = $PointOfInterest->item($s)->getAttribute("DirectionId");
                        $DistanceFromHotel = $PointOfInterest->item($s)->getAttribute("DistanceFromHotel");
                        $UOM = $PointOfInterest->item($s)->getAttribute("UOM");
                        $TransportationCode = $PointOfInterest->item($s)->getAttribute("TransportationCode");
                        $CountryCode = $PointOfInterest->item($s)->getAttribute("CountryCode");
                    }
                }
            }
            $AcceptedCreditCards = $HotelDescriptiveInfo->item(0)->getElementsByTagName("AcceptedCreditCards");
            if ($AcceptedCreditCards->length > 0) {
                $AcceptedCreditCards = $AcceptedCreditCards->item(0)->nodeValue;
            }
            $GuaranteePolicies = $HotelDescriptiveInfo->item(0)->getElementsByTagName("GuaranteePolicies");
            if ($GuaranteePolicies->length > 0) {
                $GuaranteePolicies = $GuaranteePolicies->item(0)->nodeValue;
            }
        }
        $HotelRateInfo = $HotelDetailsInfo->item(0)->getElementsByTagName("HotelRateInfo");
        if ($HotelRateInfo->length > 0) {
            $RateInfos = $HotelRateInfo->item(0)->getElementsByTagName("RateInfos");
            if ($RateInfos->length > 0) {
                $ShopKey = $RateInfos->item(0)->getAttribute("ShopKey");
                $RateInfo = $HotelRateInfo->item(0)->getElementsByTagName("RateInfo");
                if ($RateInfo->length > 0) {
                    for ($r=0; $r < $RateInfo->length; $r++) { 
                        $TaxInclusive = $RateInfo->item($r)->getAttribute("TaxInclusive");
                        $EndDate = $RateInfo->item($r)->getAttribute("EndDate");
                        $RateSource = $RateInfo->item($r)->getAttribute("RateSource");
                        $RateKey = $RateInfo->item($r)->getAttribute("RateKey");
                        $AverageNightlyRate = $RateInfo->item($r)->getAttribute("AverageNightlyRate");
                        $IncidentalsInclusive = $RateInfo->item($r)->getAttribute("IncidentalsInclusive");
                        $AmountBeforeTax = $RateInfo->item($r)->getAttribute("AmountBeforeTax");
                        $AdditionalFeesInclusive = $RateInfo->item($r)->getAttribute("AdditionalFeesInclusive");
                        $AmountAfterTax = $RateInfo->item($r)->getAttribute("AmountAfterTax");
                        $CurrencyCode = $RateInfo->item($r)->getAttribute("CurrencyCode");
                        $StartDate = $RateInfo->item($r)->getAttribute("StartDate");
                        $Commission = $RateInfo->item($r)->getElementsByTagName("Commission");
                        if ($Commission->length > 0) {
                            $Type = $Commission->item(0)->getAttribute("Type");
                            $Percent = $Commission->item(0)->getAttribute("Percent");
                            $CommissionDescription = $Commission->item(0)->getElementsByTagName("CommissionDescription");
                            if ($CommissionDescription->length > 0) {
                                $Text = $CommissionDescription->item(0)->getElementsByTagName("Text");
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
            $Rooms = $HotelRateInfo->item(0)->getElementsByTagName("Rooms");
            if ($Rooms->length > 0) {
                $Room = $Rooms->item(0)->getElementsByTagName("Room");
                if ($Room->length > 0) {
                    for ($x=0; $x < $Room->length; $x++) { 
                        $RoomIndex = $Room->item($x)->getAttribute("RoomIndex");
                        $GuestRoomInfo = $Room->item($x)->getAttribute("GuestRoomInfo");
                        $Amenities = $Room->item($x)->getElementsByTagName("Amenities");
                        if ($Amenities->length > 0) {
                            $Amenity = $Amenities->item(0)->getElementsByTagName("Amenity");
                            if ($Amenity->length > 0) {
                                for ($j=0; $j < $Amenity->length; $j++) { 
                                    $Code = $Amenity->item($j)->getAttribute("Code");
                                    $Description = $Amenity->item($j)->getAttribute("Description");
                                }
                            }
                        }
                        $BedTypes = $Room->item($x)->getElementsByTagName("BedTypes");
                        if ($BedTypes->length > 0) {
                            $BedType = $BedTypes->item(0)->getElementsByTagName("BedType");
                            if ($BedType->length > 0) {
                                $BedTypeCode = $BedType->item(0)->getAttribute("Code");
                            }
                        }
                        $RoomDescription = $Room->item($x)->getElementsByTagName("RoomDescription");
                        if ($RoomDescription->length > 0) {
                            $RoomDescriptionName = $RoomDescription->item(0)->getAttribute("Name");
                            $Text = $RoomDescription->item(0)->getElementsByTagName("Text");
                            if ($Text->length > 0) {
                                $Text = $Text->item(0)->nodeValue;
                            } else {
                                $Text = "";
                            }
                        }
                        $AdditionalDetails = $Room->item($x)->getElementsByTagName("AdditionalDetails");
                        if ($AdditionalDetails->length > 0) {
                            $AdditionalDetail = $AdditionalDetails->item(0)->getElementsByTagName("AdditionalDetail");
                            if ($AdditionalDetail->length > 0) {
                                $AdditionalDetailCode = $AdditionalDetail->item(0)->getAttribute("Code");
                                $AdditionalDetailText = $AdditionalDetail->item(0)->getElementsByTagName("Text");
                                if ($AdditionalDetailText->length > 0) {
                                    $AdditionalDetailText = $AdditionalDetailText->item(0)->nodeValue;
                                } else {
                                    $AdditionalDetailText = "";
                                }
                            }
                        }
                        $RatePlans = $Room->item($x)->getElementsByTagName("RatePlans");
                        if ($RatePlans->length > 0) {
                            $RatePlan = $RatePlans->item(0)->getElementsByTagName("RatePlan");
                            if ($RatePlan->length > 0) {
                                $RateSource = $RatePlan->item(0)->getAttribute("RateSource");
                                $RateKey = $RatePlan->item(0)->getAttribute("RateKey");
                                $RateSource = $RatePlan->item(0)->getAttribute("RateSource");
                                $RatePlanCode = $RatePlan->item(0)->getAttribute("RatePlanCode");
                                $LimitedAvailability = $RatePlan->item(0)->getAttribute("LimitedAvailability");
                                $PrepaidIndicator = $RatePlan->item(0)->getAttribute("PrepaidIndicator");
                                $RatePlanName = $RatePlan->item(0)->getAttribute("RatePlanName");
                                $ProductCode = $RatePlan->item(0)->getAttribute("ProductCode");
                                $RatePlanType = $RatePlan->item(0)->getAttribute("RatePlanType");
                                $RatePlanInclusions = $RatePlan->item(0)->getElementsByTagName("RatePlanInclusions");
                                if ($RatePlanInclusions->length > 0) {
                                    $RatePlanInclusionDescription = $RatePlanInclusions->item(0)->getElementsByTagName("RatePlanInclusionDescription");
                                    if ($RatePlanInclusionDescription->length > 0) {
                                        $RatePlanInclusionDescriptionCode = $RatePlanInclusionDescription->item(0)->getAttribute("Code");
                                    }
                                }
                                $RateInfo = $RatePlan->item(0)->getElementsByTagName("RateInfo");
                                if ($RateInfo->length > 0) {
                                    $TaxInclusive = $RateInfo->item(0)->getAttribute("TaxInclusive");
                                    $EndDate = $RateInfo->item(0)->getAttribute("EndDate");
                                    $AverageNightlyRate = $RateInfo->item(0)->getAttribute("AverageNightlyRate");
                                    $IncidentalsInclusive = $RateInfo->item(0)->getAttribute("IncidentalsInclusive");
                                    $AmountBeforeTax = $RateInfo->item(0)->getAttribute("AmountBeforeTax");
                                    $AdditionalFeesInclusive = $RateInfo->item(0)->getAttribute("AdditionalFeesInclusive");
                                    $AmountAfterTax = $RateInfo->item(0)->getAttribute("AmountAfterTax");
                                    $CurrencyCode = $RateInfo->item(0)->getAttribute("CurrencyCode");
                                    $StartDate = $RateInfo->item(0)->getAttribute("StartDate");
                                    $Taxes = $RateInfo->item(0)->getElementsByTagName("Taxes");
                                    if ($Taxes->length > 0) {
                                        $TaxesCurrencyCode = $Taxes->item(0)->getAttribute("CurrencyCode");
                                        $TaxesAmount = $Taxes->item(0)->getAttribute("Amount");
                                    }
                                    $RoomExtras = $RateInfo->item(0)->getElementsByTagName("RoomExtras");
                                    if ($RoomExtras->length > 0) {
                                        $RoomExtra = $RoomExtras->item(0)->getElementsByTagName("RoomExtra");
                                        if ($RoomExtra->length > 0) {
                                            for ($y=0; $y < $RoomExtra->length; $y++) { 
                                                $Code = $RoomExtra->item($y)->getAttribute("Code");
                                                $Amount = $RoomExtra->item($y)->getAttribute("Amount");
                                                $CurrencyCode = $RoomExtra->item($y)->getAttribute("CurrencyCode");
                                                $Text = $RoomExtra->item($y)->getElementsByTagName("Text");
                                                if ($Text->length > 0) {
                                                    $Text = $Text->item(0)->nodeValue;
                                                } else {
                                                    $Text = "";
                                                }
                                            }
                                        }
                                    }
                                    $CancelPenalties = $RateInfo->item(0)->getElementsByTagName("CancelPenalties");
                                    if ($CancelPenalties->length > 0) {
                                        $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName("CancelPenalty");
                                        if ($CancelPenalty->length > 0) {
                                            $Refundable = $CancelPenalty->item(0)->getAttribute("Refundable");
                                            $Deadline = $CancelPenalty->item(0)->getElementsByTagName("Deadline");
                                            if ($Deadline->length > 0) {
                                                $OffsetUnitMultiplier = $Deadline->item(0)->getAttribute("OffsetUnitMultiplier");
                                                $OffsetTimeUnit = $Deadline->item(0)->getAttribute("OffsetTimeUnit");
                                                $OffsetDropTime = $Deadline->item(0)->getAttribute("OffsetDropTime");
                                            }
                                        }
                                    }
                                    $Guarantee = $RateInfo->item(0)->getElementsByTagName("Guarantee");
                                    if ($Guarantee->length > 0) {
                                        $GuaranteeType = $Guarantee->item(0)->getAttribute("GuaranteeType");
                                        $GuaranteesAccepted = $Guarantee->item(0)->getElementsByTagName("GuaranteesAccepted");
                                        if ($GuaranteesAccepted->length > 0) {
                                            $GuaranteeAccepted = $GuaranteesAccepted->item(0)->getElementsByTagName("GuaranteeAccepted");
                                            if ($GuaranteeAccepted->length > 0) {
                                                for ($z=0; $z < $GuaranteeAccepted->length; $z++) { 
                                                    $GuaranteeTypeCode = $GuaranteeAccepted->item($z)->getAttribute("GuaranteeTypeCode");
                                                    $PaymentCards = $GuaranteeAccepted->item($z)->getElementsByTagName("PaymentCards");
                                                    if ($PaymentCards->length > 0) {
                                                        $PaymentCard = $PaymentCards->item(0)->getElementsByTagName("PaymentCard");
                                                        if ($PaymentCard->length > 0) {
                                                            $payment = "";
                                                            for ($zAux=0; $zAux < $PaymentCard->length; $zAux++) { 
                                                                $CardCode = $PaymentCard->item($zAux)->getAttribute("CardCode");
                                                                $payment = $PaymentCard->item($zAux)->nodeValue;
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
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
