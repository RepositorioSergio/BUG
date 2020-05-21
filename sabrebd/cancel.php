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
echo "COMECOU CANCEL<br/>";
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
$BinarySecurityToken = 'Shared/IDL:IceSess\\/SessMgr:1\\.0.IDL/Common/!ICESMS\\/ACPCRTC!ICESMSLB\\/CRT.LB!1586168296687!8910!9';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Header>
   <eb:MessageHeader soapenv:mustUnderstand="0" xmlns:eb="http://www.ebxml.org/namespaces/messageHeader">
       <eb:From>
         <eb:PartyId></eb:PartyId>
      </eb:From>
      <eb:To>
         <eb:PartyId></eb:PartyId>
      </eb:To>
      <eb:CPAId>' . $CPAId . '</eb:CPAId>
      <eb:ConversationId>V1@0001@client</eb:ConversationId>
      <eb:Service>EndTransactionLLSRQ</eb:Service>
      <eb:Action>EndTransactionLLSRQ</eb:Action>
      <eb:MessageData>
         <eb:MessageId>mid:20001209-133003-2333@clientofsabre.com</eb:MessageId>
         <eb:Timestamp>' . $datetime . '</eb:Timestamp>
      </eb:MessageData>
   </eb:MessageHeader>
   <eb:Security soapenv:mustUnderstand="0" xmlns:eb="http://schemas.xmlsoap.org/ws/2002/12/secext">
      <eb:BinarySecurityToken>' . $BinarySecurityToken . '</eb:BinarySecurityToken>
   </eb:Security>
</soapenv:Header>
<soapenv:Body>
    <OTA_CancelRQ EchoToken="12345" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.opentravel.org/OTA/2003/05">
        <POS>
            <Source>
                <RequestorId ID="10" ID_Context="Synxis">
                <CompanyName Code="WSBE" />
                </RequestorId>
            </Source>
        </POS>
        <UniqueID Type="14" ID="10107SY000837" ID_Context="CrsConfirmNumber" />
        <Verification>
            <TPA_Extensions>
                <BasicPropertyInfo HotelCode="10107" ChainCode="1234" />
            </TPA_Extensions>
        </Verification>
        <TPA_Extensions>
            <WrittenConfInst>
                <SupplementalData Name="EmailTemplateCode" />
            </WrittenConfInst>
        </TPA_Extensions>
    </OTA_CancelRQ>
</soapenv:Body>
</soapenv:Envelope>';
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
$inputDoc->loadXML($response2);
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
$HotelPropertyDescriptionRS = $Body->item(0)->getElementsByTagName("HotelPropertyDescriptionRS");
if ($HotelPropertyDescriptionRS->length > 0) {
    $RoomStay = $HotelPropertyDescriptionRS->item(0)->getElementsByTagName("RoomStay");
    if ($RoomStay->length > 0) {
        $BasicPropertyInfo = $RoomStay->item(0)->getElementsByTagName("BasicPropertyInfo");
        if ($BasicPropertyInfo->length > 0) {
            $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
            $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
            $HotelCityCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCityCode");
            $Latitude = $BasicPropertyInfo->item(0)->getAttribute("Latitude");
            $Longitude = $BasicPropertyInfo->item(0)->getAttribute("Longitude");
            $GeoConfidenceLevel = $BasicPropertyInfo->item(0)->getAttribute("GeoConfidenceLevel");
            $RPH = $BasicPropertyInfo->item(0)->getAttribute("RPH");
            $ChainCode = $BasicPropertyInfo->item(0)->getAttribute("ChainCode");
            $NumFloors = $BasicPropertyInfo->item(0)->getAttribute("NumFloors");

            $CheckInTime = $BasicPropertyInfo->item(0)->getElementsByTagName("CheckInTime");
            if ($CheckInTime->length > 0) {
                $CheckInTime = $CheckInTime->item(0)->nodeValue;
            } else {
                $CheckInTime = "";
            }
            $CheckOutTime = $BasicPropertyInfo->item(0)->getElementsByTagName("CheckOutTime");
            if ($CheckOutTime->length > 0) {
                $CheckOutTime = $CheckOutTime->item(0)->nodeValue;
            } else {
                $CheckOutTime = "";
            }
            //Awards
            $Awards = $BasicPropertyInfo->item(0)->getElementsByTagName("Awards");
            if ($Awards->length > 0) {
                $AwardProvider = $Awards->item(0)->getElementsByTagName("AwardProvider");
                if ($AwardProvider->length > 0) {
                    $AwardProvider = $AwardProvider->item(0)->nodeValue;
                } else {
                    $AwardProvider = "";
                }
            }
            //Address
            $Address = $BasicPropertyInfo->item(0)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $AddressLine = $Address->item(0)->getElementsByTagName("AddressLine");
                if ($AddressLine->length > 0) {
                    for ($iAux=0; $iAux < $AddressLine->length; $iAux++) { 
                        $AddressLine = $AddressLine->item($iAux)->nodeValue;
                    }
                }
            }
            //ContactNumbers
            $ContactNumbers = $BasicPropertyInfo->item(0)->getElementsByTagName("ContactNumbers");
            if ($ContactNumbers->length > 0) {
                $ContactNumber = $ContactNumbers->item(0)->getElementsByTagName("ContactNumber");
                if ($ContactNumber->length > 0) {
                    $Phone = $ContactNumber->item(0)->getAttribute("Phone");
                    $Fax = $ContactNumber->item(0)->getAttribute("Fax");
                }
            }
            //DirectConnect
            $DirectConnect = $BasicPropertyInfo->item(0)->getElementsByTagName("DirectConnect");
            if ($DirectConnect->length > 0) {
                $Alt_Avail = $DirectConnect->item(0)->getElementsByTagName("Alt_Avail");
                if ($Alt_Avail->length > 0) {
                    $Alt_Avail_Ind = $Alt_Avail->item(0)->getAttribute("Ind");
                }
                $DC_AvailParticipant = $DirectConnect->item(0)->getElementsByTagName("DC_AvailParticipant");
                if ($DC_AvailParticipant->length > 0) {
                    $DC_AvailParticipant_Ind = $DC_AvailParticipant->item(0)->getAttribute("Ind");
                }
                $DC_SellParticipant = $DirectConnect->item(0)->getElementsByTagName("DC_SellParticipant");
                if ($DC_SellParticipant->length > 0) {
                    $DC_SellParticipant_Ind = $DC_SellParticipant->item(0)->getAttribute("Ind");
                }
                $RatesExceedMax = $DirectConnect->item(0)->getElementsByTagName("RatesExceedMax");
                if ($RatesExceedMax->length > 0) {
                    $RatesExceedMax_Ind = $RatesExceedMax->item(0)->getAttribute("Ind");
                }
                $UnAvail = $DirectConnect->item(0)->getElementsByTagName("UnAvail");
                if ($UnAvail->length > 0) {
                    $UnAvail_Ind = $UnAvail->item(0)->getAttribute("Ind");
                }
            }
            //LocationDescription
            $IndexData = $BasicPropertyInfo->item(0)->getElementsByTagName("IndexData");
            if ($IndexData->length > 0) {
                $Index = $IndexData->item(0)->getElementsByTagName("Index");
                if ($Index->length > 0) {
                    for ($i=0; $i < $Index->length; $i++) { 
                        $TransportationCode = $Index->item($i)->getAttribute("TransportationCode");
                        $Point = $Index->item($i)->getAttribute("Point");
                        $LocationCode = $Index->item($i)->getAttribute("LocationCode");
                        $DistanceDirection = $Index->item($i)->getAttribute("DistanceDirection");
                        $CountryState = $Index->item($i)->getAttribute("CountryState");
                    }
                }
            }
            //PropertyOptionInfo
            $PropertyOptionInfo = $BasicPropertyInfo->item(0)->getElementsByTagName("PropertyOptionInfo");
            if ($PropertyOptionInfo->length > 0) {
                $ADA_Accessible = $PropertyOptionInfo->item(0)->getElementsByTagName("ADA_Accessible");
                if ($ADA_Accessible->length > 0) {
                    $ADA_Accessible_Ind = $ADA_Accessible->item(0)->getAttribute("Ind");
                }
                $AdultsOnly = $PropertyOptionInfo->item(0)->getElementsByTagName("AdultsOnly");
                if ($AdultsOnly->length > 0) {
                    $AdultsOnly_Ind = $AdultsOnly->item(0)->getAttribute("Ind");
                }
                $BeachFront = $PropertyOptionInfo->item(0)->getElementsByTagName("BeachFront");
                if ($BeachFront->length > 0) {
                    $BeachFront_Ind = $BeachFront->item(0)->getAttribute("Ind");
                }
                $Breakfast = $PropertyOptionInfo->item(0)->getElementsByTagName("Breakfast");
                if ($Breakfast->length > 0) {
                    $Breakfast_Ind = $Breakfast->item(0)->getAttribute("Ind");
                }
                $BusinessCenter = $PropertyOptionInfo->item(0)->getElementsByTagName("BusinessCenter");
                if ($BusinessCenter->length > 0) {
                    $BusinessCenter_Ind = $BusinessCenter->item(0)->getAttribute("Ind");
                }
                $BusinessReady = $PropertyOptionInfo->item(0)->getElementsByTagName("BusinessReady");
                if ($BusinessReady->length > 0) {
                    $BusinessReady_Ind = $BusinessReady->item(0)->getAttribute("Ind");
                }
                $Conventions = $PropertyOptionInfo->item(0)->getElementsByTagName("Conventions");
                if ($Conventions->length > 0) {
                    $Conventions_Ind = $Conventions->item(0)->getAttribute("Ind");
                }
                $Dataport = $PropertyOptionInfo->item(0)->getElementsByTagName("Dataport");
                if ($Dataport->length > 0) {
                    $Dataport_Ind = $Dataport->item(0)->getAttribute("Ind");
                }
                $Dining = $PropertyOptionInfo->item(0)->getElementsByTagName("Dining");
                if ($Dining->length > 0) {
                    $Dining_Ind = $Dining->item(0)->getAttribute("Ind");
                }
                $DryClean = $PropertyOptionInfo->item(0)->getElementsByTagName("DryClean");
                if ($DryClean->length > 0) {
                    $DryClean_Ind = $DryClean->item(0)->getAttribute("Ind");
                }
                $EcoCertified = $PropertyOptionInfo->item(0)->getElementsByTagName("EcoCertified");
                if ($EcoCertified->length > 0) {
                    $EcoCertified_Ind = $EcoCertified->item(0)->getAttribute("Ind");
                }
                $ExecutiveFloors = $PropertyOptionInfo->item(0)->getElementsByTagName("ExecutiveFloors");
                if ($ExecutiveFloors->length > 0) {
                    $ExecutiveFloors_Ind = $ExecutiveFloors->item(0)->getAttribute("Ind");
                }
                $FitnessCenter = $PropertyOptionInfo->item(0)->getElementsByTagName("FitnessCenter");
                if ($FitnessCenter->length > 0) {
                    $FitnessCenter_Ind = $FitnessCenter->item(0)->getAttribute("Ind");
                }
                $FreeLocalCalls = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeLocalCalls");
                if ($FreeLocalCalls->length > 0) {
                    $FreeLocalCalls_Ind = $FreeLocalCalls->item(0)->getAttribute("Ind");
                }
                $FreeParking = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeParking");
                if ($FreeParking->length > 0) {
                    $FreeParking_Ind = $FreeParking->item(0)->getAttribute("Ind");
                }
                $FreeShuttle = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeShuttle");
                if ($FreeShuttle->length > 0) {
                    $FreeShuttle_Ind = $FreeShuttle->item(0)->getAttribute("Ind");
                }
                $FreeWifiInMeetingRooms = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeWifiInMeetingRooms");
                if ($FreeWifiInMeetingRooms->length > 0) {
                    $FreeWifiInMeetingRooms_Ind = $FreeWifiInMeetingRooms->item(0)->getAttribute("Ind");
                }
                $FreeWifiInPublicSpaces = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeWifiInPublicSpaces");
                if ($FreeWifiInPublicSpaces->length > 0) {
                    $FreeWifiInPublicSpaces_Ind = $FreeWifiInPublicSpaces->item(0)->getAttribute("Ind");
                }
                $FreeWifiInRooms = $PropertyOptionInfo->item(0)->getElementsByTagName("FreeWifiInRooms");
                if ($FreeWifiInRooms->length > 0) {
                    $FreeWifiInRooms_Ind = $FreeWifiInRooms->item(0)->getAttribute("Ind");
                }
                $FullServiceSpa = $PropertyOptionInfo->item(0)->getElementsByTagName("FullServiceSpa");
                if ($FullServiceSpa->length > 0) {
                    $FullServiceSpa_Ind = $FullServiceSpa->item(0)->getAttribute("Ind");
                }
                $GameFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("GameFacilities");
                if ($GameFacilities->length > 0) {
                    $GameFacilities_Ind = $GameFacilities->item(0)->getAttribute("Ind");
                }
                $Golf = $PropertyOptionInfo->item(0)->getElementsByTagName("Golf");
                if ($Golf->length > 0) {
                    $Golf_Ind = $Golf->item(0)->getAttribute("Ind");
                }
                $HighSpeedInternet = $PropertyOptionInfo->item(0)->getElementsByTagName("HighSpeedInternet");
                if ($HighSpeedInternet->length > 0) {
                    $HighSpeedInternet_Ind = $HighSpeedInternet->item(0)->getAttribute("Ind");
                }
                $HypoallergenicRooms = $PropertyOptionInfo->item(0)->getElementsByTagName("HypoallergenicRooms");
                if ($HypoallergenicRooms->length > 0) {
                    $HypoallergenicRooms_Ind = $HypoallergenicRooms->item(0)->getAttribute("Ind");
                }
                $IndoorPool = $PropertyOptionInfo->item(0)->getElementsByTagName("IndoorPool");
                if ($IndoorPool->length > 0) {
                    $IndoorPool_Ind = $IndoorPool->item(0)->getAttribute("Ind");
                }
                $InRoomCoffeeTea = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomCoffeeTea");
                if ($InRoomCoffeeTea->length > 0) {
                    $InRoomCoffeeTea_Ind = $InRoomCoffeeTea->item(0)->getAttribute("Ind");
                }
                $InRoomMiniBar = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomMiniBar");
                if ($InRoomMiniBar->length > 0) {
                    $InRoomMiniBar_Ind = $InRoomMiniBar->item(0)->getAttribute("Ind");
                }
                $InRoomRefrigerator = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomRefrigerator");
                if ($InRoomRefrigerator->length > 0) {
                    $InRoomRefrigerator_Ind = $InRoomRefrigerator->item(0)->getAttribute("Ind");
                }
                $InRoomSafe = $PropertyOptionInfo->item(0)->getElementsByTagName("InRoomSafe");
                if ($InRoomSafe->length > 0) {
                    $InRoomSafe_Ind = $InRoomSafe->item(0)->getAttribute("Ind");
                }
                $InteriorDoorways = $PropertyOptionInfo->item(0)->getElementsByTagName("InteriorDoorways");
                if ($InteriorDoorways->length > 0) {
                    $InteriorDoorways_Ind = $InteriorDoorways->item(0)->getAttribute("Ind");
                }
                $Jacuzzi = $PropertyOptionInfo->item(0)->getElementsByTagName("Jacuzzi");
                if ($Jacuzzi->length > 0) {
                    $Jacuzzi_Ind = $Jacuzzi->item(0)->getAttribute("Ind");
                }
                $KidsFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("KidsFacilities");
                if ($KidsFacilities->length > 0) {
                    $KidsFacilities_Ind = $KidsFacilities->item(0)->getAttribute("Ind");
                }
                $KitchenFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("KitchenFacilities");
                if ($KitchenFacilities->length > 0) {
                    $KitchenFacilities_Ind = $KitchenFacilities->item(0)->getAttribute("Ind");
                }
                $MealService = $PropertyOptionInfo->item(0)->getElementsByTagName("MealService");
                if ($MealService->length > 0) {
                    $MealService_Ind = $MealService->item(0)->getAttribute("Ind");
                }
                $MeetingFacilities = $PropertyOptionInfo->item(0)->getElementsByTagName("MeetingFacilities");
                if ($MeetingFacilities->length > 0) {
                    $MeetingFacilities_Ind = $MeetingFacilities->item(0)->getAttribute("Ind");
                }
                $NoAdultTV = $PropertyOptionInfo->item(0)->getElementsByTagName("NoAdultTV");
                if ($NoAdultTV->length > 0) {
                    $NoAdultTV_Ind = $NoAdultTV->item(0)->getAttribute("Ind");
                }
                $NonSmoking = $PropertyOptionInfo->item(0)->getElementsByTagName("NonSmoking");
                if ($NonSmoking->length > 0) {
                    $NonSmoking_Ind = $NonSmoking->item(0)->getAttribute("Ind");
                }
                $OutdoorPool = $PropertyOptionInfo->item(0)->getElementsByTagName("OutdoorPool");
                if ($OutdoorPool->length > 0) {
                    $OutdoorPoolJacuzzi_Ind = $OutdoorPool->item(0)->getAttribute("Ind");
                }
                $Pets = $PropertyOptionInfo->item(0)->getElementsByTagName("Pets");
                if ($Pets->length > 0) {
                    $Pets_Ind = $Pets->item(0)->getAttribute("Ind");
                }
                $Pool = $PropertyOptionInfo->item(0)->getElementsByTagName("Pool");
                if ($Pool->length > 0) {
                    $Pool_Ind = $Pool->item(0)->getAttribute("Ind");
                }
                $PublicTransportationAdjacent = $PropertyOptionInfo->item(0)->getElementsByTagName("PublicTransportationAdjacent");
                if ($PublicTransportationAdjacent->length > 0) {
                    $PublicTransportationAdjacent_Ind = $PublicTransportationAdjacent->item(0)->getAttribute("Ind");
                }
                $RateAssured = $PropertyOptionInfo->item(0)->getElementsByTagName("RateAssured");
                if ($RateAssured->length > 0) {
                    $RateAssured_Ind = $RateAssured->item(0)->getAttribute("Ind");
                }
                $Recreation = $PropertyOptionInfo->item(0)->getElementsByTagName("Recreation");
                if ($Recreation->length > 0) {
                    $Recreation_Ind = $Recreation->item(0)->getAttribute("Ind");
                }
                $RestrictedRoomAccess = $PropertyOptionInfo->item(0)->getElementsByTagName("RestrictedRoomAccess");
                if ($RestrictedRoomAccess->length > 0) {
                    $RestrictedRoomAccess_Ind = $RestrictedRoomAccess->item(0)->getAttribute("Ind");
                }
                $RoomService = $PropertyOptionInfo->item(0)->getElementsByTagName("RoomService");
                if ($RoomService->length > 0) {
                    $RoomService_Ind = $RoomService->item(0)->getAttribute("Ind");
                }
                $RoomService24Hours = $PropertyOptionInfo->item(0)->getElementsByTagName("RoomService24Hours");
                if ($RoomService24Hours->length > 0) {
                    $RoomService24Hours_Ind = $RoomService24Hours->item(0)->getAttribute("Ind");
                }
                $RoomsWithBalcony = $PropertyOptionInfo->item(0)->getElementsByTagName("RoomsWithBalcony");
                if ($RoomsWithBalcony->length > 0) {
                    $RoomsWithBalcony_Ind = $RoomsWithBalcony->item(0)->getAttribute("Ind");
                }
                $SkiInOutProperty = $PropertyOptionInfo->item(0)->getElementsByTagName("SkiInOutProperty");
                if ($SkiInOutProperty->length > 0) {
                    $SkiInOutProperty_Ind = $SkiInOutProperty->item(0)->getAttribute("Ind");
                }
                $SmokeFree = $PropertyOptionInfo->item(0)->getElementsByTagName("SmokeFree");
                if ($SmokeFree->length > 0) {
                    $SmokeFree_Ind = $SmokeFree->item(0)->getAttribute("Ind");
                }
                $SmokingRoomsAvail = $PropertyOptionInfo->item(0)->getElementsByTagName("SmokingRoomsAvail");
                if ($SmokingRoomsAvail->length > 0) {
                    $SmokingRoomsAvail_Ind = $SmokingRoomsAvail->item(0)->getAttribute("Ind");
                }
                $Tennis = $PropertyOptionInfo->item(0)->getElementsByTagName("Tennis");
                if ($Tennis->length > 0) {
                    $Tennis_Ind = $Tennis->item(0)->getAttribute("Ind");
                }
                $WaterPurificationSystem = $PropertyOptionInfo->item(0)->getElementsByTagName("WaterPurificationSystem");
                if ($WaterPurificationSystem->length > 0) {
                    $WaterPurificationSystem_Ind = $WaterPurificationSystem->item(0)->getAttribute("Ind");
                }
                $Wheelchair = $PropertyOptionInfo->item(0)->getElementsByTagName("Wheelchair");
                if ($Wheelchair->length > 0) {
                    $Wheelchair_Ind = $Wheelchair->item(0)->getAttribute("Ind");
                }
            }
            //PropertyTypeInfo
            $PropertyTypeInfo = $BasicPropertyInfo->item(0)->getElementsByTagName("PropertyTypeInfo");
            if ($PropertyTypeInfo->length > 0) {
                $AllInclusive = $PropertyTypeInfo->item(0)->getElementsByTagName("AllInclusive");
                if ($AllInclusive->length > 0) {
                    $AllInclusive_Ind = $AllInclusive->item(0)->getAttribute("Ind");
                }
                $Apartments = $PropertyTypeInfo->item(0)->getElementsByTagName("Apartments");
                if ($Apartments->length > 0) {
                    $Apartments_Ind = $Apartments->item(0)->getAttribute("Ind");
                }
                $BedBreakfast = $PropertyTypeInfo->item(0)->getElementsByTagName("BedBreakfast");
                if ($BedBreakfast->length > 0) {
                    $BedBreakfast_Ind = $BedBreakfast->item(0)->getAttribute("Ind");
                }
                $Castle = $PropertyTypeInfo->item(0)->getElementsByTagName("Castle");
                if ($Castle->length > 0) {
                    $Castle_Ind = $Castle->item(0)->getAttribute("Ind");
                }
                $Conventions = $PropertyTypeInfo->item(0)->getElementsByTagName("Conventions");
                if ($Conventions->length > 0) {
                    $Conventions_Ind = $Conventions->item(0)->getAttribute("Ind");
                }
                $Economy = $PropertyTypeInfo->item(0)->getElementsByTagName("Economy");
                if ($Economy->length > 0) {
                    $Economy_Ind = $Economy->item(0)->getAttribute("Ind");
                }
                $ExtendedStay = $PropertyTypeInfo->item(0)->getElementsByTagName("ExtendedStay");
                if ($ExtendedStay->length > 0) {
                    $ExtendedStay_Ind = $ExtendedStay->item(0)->getAttribute("Ind");
                }
                $Farm = $PropertyTypeInfo->item(0)->getElementsByTagName("Farm");
                if ($Farm->length > 0) {
                    $Farm_Ind = $Farm->item(0)->getAttribute("Ind");
                }
                $First = $PropertyTypeInfo->item(0)->getElementsByTagName("First");
                if ($First->length > 0) {
                    $First_Ind = $First->item(0)->getAttribute("Ind");
                }
                $Luxury = $PropertyTypeInfo->item(0)->getElementsByTagName("Luxury");
                if ($Luxury->length > 0) {
                    $Luxury_Ind = $Luxury->item(0)->getAttribute("Ind");
                }
                $Moderate = $PropertyTypeInfo->item(0)->getElementsByTagName("Moderate");
                if ($Moderate->length > 0) {
                    $Moderate_Ind = $Moderate->item(0)->getAttribute("Ind");
                }
                $Motel = $PropertyTypeInfo->item(0)->getElementsByTagName("Motel");
                if ($Motel->length > 0) {
                    $Motel_Ind = $Motel->item(0)->getAttribute("Ind");
                }
                $Resort = $PropertyTypeInfo->item(0)->getElementsByTagName("Resort");
                if ($Resort->length > 0) {
                    $Resort_Ind = $Resort->item(0)->getAttribute("Ind");
                }
                $Suites = $PropertyTypeInfo->item(0)->getElementsByTagName("Suites");
                if ($Suites->length > 0) {
                    $Suites_Ind = $Suites->item(0)->getAttribute("Ind");
                }
            }
            //SpecialOffers
            $SpecialOffers = $BasicPropertyInfo->item(0)->getElementsByTagName("SpecialOffers");
            if ($SpecialOffers->length > 0) {
                $SpecialOffers_Ind = $SpecialOffers->item(0)->getAttribute("Ind");
            }
            //Taxes
            $Taxes = $BasicPropertyInfo->item(0)->getElementsByTagName("Taxes");
            if ($Taxes->length > 0) {
                $TaxesText = $Taxes->item(0)->getElementsByTagName("Text");
                if ($TaxesText->length > 0) {
                    $TaxesText = $TaxesText->item(0)->nodeValue;
                } else {
                    $TaxesText = "";
                }
            }
            //VendorMessages
            $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName("VendorMessages");
            if ($VendorMessages->length > 0) {
                $AdditionalAttractions = $VendorMessages->item(0)->getElementsByTagName("AdditionalAttractions");
                if ($AdditionalAttractions->length > 0) {
                    $Text = $AdditionalAttractions->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Attractions = $VendorMessages->item(0)->getElementsByTagName("Attractions");
                if ($Attractions->length > 0) {
                    $Text = $Attractions->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Cancellation = $VendorMessages->item(0)->getElementsByTagName("Cancellation");
                if ($Cancellation->length > 0) {
                    $Text = $Cancellation->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Deposit = $VendorMessages->item(0)->getElementsByTagName("Deposit");
                if ($Deposit->length > 0) {
                    $Text = $Deposit->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Description = $VendorMessages->item(0)->getElementsByTagName("Description");
                if ($Description->length > 0) {
                    $Text = $Description->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Dining = $VendorMessages->item(0)->getElementsByTagName("Dining");
                if ($Dining->length > 0) {
                    $Text = $Dining->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Directions = $VendorMessages->item(0)->getElementsByTagName("Directions");
                if ($Directions->length > 0) {
                    $Text = $Directions->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Facilities = $VendorMessages->item(0)->getElementsByTagName("Facilities");
                if ($Facilities->length > 0) {
                    $Text = $Facilities->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Guarantee = $VendorMessages->item(0)->getElementsByTagName("Guarantee");
                if ($Guarantee->length > 0) {
                    $Text = $Guarantee->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Location = $VendorMessages->item(0)->getElementsByTagName("Location");
                if ($Location->length > 0) {
                    $Text = $Location->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $MiscServices = $VendorMessages->item(0)->getElementsByTagName("MiscServices");
                if ($MiscServices->length > 0) {
                    $Text = $MiscServices->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Policies = $VendorMessages->item(0)->getElementsByTagName("Policies");
                if ($Policies->length > 0) {
                    $Text = $Policies->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Recreation = $VendorMessages->item(0)->getElementsByTagName("Recreation");
                if ($Recreation->length > 0) {
                    $Text = $Recreation->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Rooms = $VendorMessages->item(0)->getElementsByTagName("Rooms");
                if ($Rooms->length > 0) {
                    $Text = $Rooms->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Safety = $VendorMessages->item(0)->getElementsByTagName("Safety");
                if ($Safety->length > 0) {
                    $Text = $Safety->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Services = $VendorMessages->item(0)->getElementsByTagName("Services");
                if ($Services->length > 0) {
                    $Text = $Services->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
                $Transportation = $VendorMessages->item(0)->getElementsByTagName("Transportation");
                if ($Transportation->length > 0) {
                    $Text = $Transportation->item(0)->getElementsByTagName("Text");
                    if ($Text->length > 0) {
                        for ($j=0; $j < $Text->length; $j++) { 
                            $Text = $Text->item($j)->nodeValue;
                        }
                    }
                }
            }
        }
        $Guarantee = $RoomStay->item(0)->getElementsByTagName("Guarantee");
        if ($Guarantee->length > 0) {
            $GuaranteesAccepted = $Guarantee->item(0)->getElementsByTagName("GuaranteesAccepted");
            if ($GuaranteesAccepted->length > 0) {
                $PaymentCard = $GuaranteesAccepted->item(0)->getElementsByTagName("PaymentCard");
                if ($PaymentCard->length > 0) {
                    for ($k=0; $k < $PaymentCard->length; $k++) { 
                        $Type = $PaymentCard->item($k)->getAttribute("Type");
                        $Code = $PaymentCard->item($k)->getAttribute("Code");
                    }
                }
            }
        }
        $TimeSpan = $RoomStay->item(0)->getElementsByTagName("TimeSpan");
        if ($TimeSpan->length > 0) {
            $Start = $TimeSpan->item(0)->getAttribute("Start");
            $End = $TimeSpan->item(0)->getAttribute("End");
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
