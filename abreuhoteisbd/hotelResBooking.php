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
echo "COMECOU HOTEL BOOKING";
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
$affiliate_id_abreu = 0;
$branch_filter = "";
$sql = "select value from settings where name='AbreuUsername' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuUsername = $row_settings['value'];
}

$sql = "select value from settings where name='Abreupassword' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreupassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='AbreuMarkup' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuMarkup = (double) $row_settings['value'];
} else {
    $AbreuMarkup = 0;
}
// URL
$sql = "select value from settings where name='AbreuHOTELBOOKING' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuHOTELBOOKING = $row_settings['value'];
}

$sql = "select value from settings where name='AbreuContext' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuContext = $row_settings['value'];
}
$sql = "select value from settings where name='AbreuOnRequest' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $AbreuOnRequest = $row_settings['value'];
}

$sql = "select value from settings where name='Abreub2cMarkup' and affiliate_id=$affiliate_id_abreu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $Abreub2cMarkup = $row_settings['value'];
}
    
$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '<?xml version="1.0" encoding="utf-8"?> <soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/">   <soap-env:Header> <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">       <wsse:Username>' . $AbreuUsername . '</wsse:Username>       <wsse:Password>' . $Abreupassword . '</wsse:Password>       <Context>' . $AbreuContext . '</Context>     </wsse:Security>   </soap-env:Header>   <soap-env:Body>     <OTA_HotelResRQ xmlns="http://parsec.es/hotelapi/OTA2014Compact" Transaction="Booking" DetailLevel="2">       <UniqueID Type="ClientReference" ID="20387"/>       <HotelRes>         <Rooms>           <Room>             <RoomRate BookingCode=""/>             <Guests>               <Guest AgeCode="A" LeadGuest="1">                 <PersonName>                   <NamePrefix>Mr.</NamePrefix>                   <GivenName>Carlos</GivenName>                   <Surname>Smith</Surname>                 </PersonName>               </Guest>   <Guest AgeCode="A">  <PersonName>    <NamePrefix>Mrs.</NamePrefix>  <GivenName>Martina</GivenName>     <Surname>Smith</Surname> </PersonName>  </Guest>   </Guests>           </Room>   <Room>             <RoomRate BookingCode=""/>             <Guests>               <Guest AgeCode="A">                 <PersonName>                   <NamePrefix>Mr.</NamePrefix>                   <GivenName>Alvaro</GivenName>                   <Surname>Juarez</Surname>                 </PersonName>               </Guest>   <Guest AgeCode="A">                 <PersonName>                   <NamePrefix>Mrs.</NamePrefix>                   <GivenName>Alice</GivenName>                   <Surname>Juarez</Surname>                 </PersonName>               </Guest>     </Guests>           </Room>   </Rooms>   </HotelRes>     </OTA_HotelResRQ>   </soap-env:Body> </soap-env:Envelope>';

echo "<xmp>";
var_dump($raw);
echo "</xmp>";


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $AbreuHOTELBOOKING);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/xml",
    "Content-type: text/xml",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.abreu.php');
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
$OTA_BookingInfoRS = $inputDoc->getElementsByTagName('OTA_BookingInfoRS');

$HotelResList = $OTA_BookingInfoRS->item(0)->getElementsByTagName('HotelResList');
if ($HotelResList->length > 0) {
    $HotelRes = $HotelResList->item(0)->getElementsByTagName('HotelRes');
    if ($HotelRes->length > 0) {
        $NewItem = $HotelRes->item(0)->getAttribute('NewItem');
        $LastModifyDateTime = $HotelRes->item(0)->getAttribute('LastModifyDateTime');
        $CreateDateTime = $HotelRes->item(0)->getAttribute('CreateDateTime');
        $ResStatus = $HotelRes->item(0)->getAttribute('ResStatus');
        //Rooms
        $Rooms = $HotelRes->item(0)->getElementsByTagName('Rooms');
        if ($Rooms->length > 0) {
            $Room = $Rooms->item(0)->getElementsByTagName('Room');
            if ($Room->length > 0) {
                for ($i=0; $i < $Room->length; $i++) { 
                    $RoomType = $Room->item($i)->getElementsByTagName('RoomType');
                    if ($RoomType->length > 0) {
                        $Code = $RoomType->item(0)->getAttribute('Code');
                        $Name = $RoomType->item(0)->getAttribute('Name');
                    }
                    //RoomRate
                    $RoomRate = $Room->item($i)->getElementsByTagName('RoomRate');
                    if ($RoomRate->length > 0) {
                        $End = $RoomRate->item(0)->getAttribute('End');
                        $Start = $RoomRate->item(0)->getAttribute('Start');
                        $MealPlan = $RoomRate->item(0)->getAttribute('MealPlan');

                        $Total = $RoomRate->item(0)->getElementsByTagName('Total');
                        if ($Total->length > 0) {
                            $Currency = $Total->item(0)->getAttribute('Currency');
                            $Commission = $Total->item(0)->getAttribute('Commission');
                            $Amount = $Total->item(0)->getAttribute('Amount');
                        }
                        $CancelPenalties = $RoomRate->item(0)->getElementsByTagName('CancelPenalties');
                        if ($CancelPenalties->length > 0) {
                            $NonRefundable = $CancelPenalties->item(0)->getAttribute('NonRefundable');
                            $CancellationCostsToday = $CancelPenalties->item(0)->getAttribute('CancellationCostsToday');

                            $CancelPenalty = $CancelPenalties->item(0)->getElementsByTagName('CancelPenalty');
                            if ($CancelPenalty->length > 0) {
                                for ($j=0; $j < $CancelPenalty->length; $j++) { 
                                    $Deadline = $CancelPenalty->item($j)->getElementsByTagName('Deadline');
                                    if ($Deadline->length > 0) {
                                        $Units = $Deadline->item(0)->getAttribute('Units');
                                        $TimeUnit = $Deadline->item(0)->getAttribute('TimeUnit');
                                    } else {
                                        $Units = "";
                                        $TimeUnit = "";
                                    }
                                    $Charge = $CancelPenalty->item($j)->getElementsByTagName('Charge');
                                    if ($Charge->length > 0) {
                                        $Currency = $Charge->item(0)->getAttribute('Currency');
                                        $Amount = $Charge->item(0)->getAttribute('Amount');
                                    } else {
                                        $Currency = "";
                                        $Amount = "";
                                    }
                                }
                            }
                        }
                    }
                    //Guests
                    $Guests = $Room->item($i)->getElementsByTagName('Guests');
                    if ($Guests->length > 0) {
                        $Guest = $Guests->item(0)->getElementsByTagName('Guest');
                        if ($Guest->length > 0) {
                            for ($iAux=0; $iAux < $Guest->length; $iAux++) { 
                                $LeadGuest = $Guest->item($iAux)->getAttribute('LeadGuest');
                                $AgeCode = $Guest->item($iAux)->getAttribute('AgeCode');
                                $PersonName = $Guest->item($iAux)->getElementsByTagName('PersonName');
                                if ($PersonName->length > 0) {
                                    $NamePrefix = $PersonName->item(0)->getElementsByTagName('NamePrefix');
                                    if ($NamePrefix->length > 0) {
                                        $NamePrefix = $NamePrefix->item(0)->nodeValue;
                                    } else {
                                        $NamePrefix = "";
                                    }
                                    $GivenName = $PersonName->item(0)->getElementsByTagName('GivenName');
                                    if ($GivenName->length > 0) {
                                        $GivenName = $GivenName->item(0)->nodeValue;
                                    } else {
                                        $GivenName = "";
                                    }
                                    $Surname = $PersonName->item(0)->getElementsByTagName('Surname');
                                    if ($Surname->length > 0) {
                                        $Surname = $Surname->item(0)->nodeValue;
                                    } else {
                                        $Surname = "";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //Info
        $Info = $HotelRes->item(0)->getElementsByTagName('Info');
        if ($Info->length > 0) {
            # code...
        }
        //HotelResInfo
        $HotelResInfo = $HotelRes->item(0)->getElementsByTagName('HotelResInfo');
        if ($HotelResInfo->length > 0) {
            # code...
        }
    }
}

$ResGlobalInfo = $OTA_BookingInfoRS->item(0)->getElementsByTagName('ResGlobalInfo');
if ($ResGlobalInfo->length > 0) {
    # code...
}

?>