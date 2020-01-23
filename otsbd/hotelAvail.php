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
echo "COMECOU HOTEL AVAIL<br/>";
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
$affiliate_id = 0;
$branch_filter = "";

$url = "https://sintesb.axisdata.net/apu-test/ota";

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '<OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" AvailRatesOnly="true" Version="0.1">
<POS>
    <Source>
        <RequestorID Instance="MF001" ID_Context="AxisData" ID="TEST" Type="22"/>
    </Source>
    <Source>
        <RequestorID Type="88" ID="TEST" MessagePassword="testpass"/>
    </Source>
</POS>
<AvailRequestSegments>
    <AvailRequestSegment>
        <StayDateRange End="2020-06-05" Start="2020-05-29"/>
        <RoomStayCandidates>
            <RoomStayCandidate Quantity="1" RPH="1">
                <GuestCounts>
                    <GuestCount Age="32" Count="2" AgeQualifyingCode="10"/>
                </GuestCounts>
            </RoomStayCandidate>
            <RoomStayCandidate Quantity="1" RPH="2">
                <GuestCounts>
                    <GuestCount Age="32" Count="2" AgeQualifyingCode="10"/>
                    <GuestCount Age="5" Count="2" AgeQualifyingCode="8"/>
                </GuestCounts>
            </RoomStayCandidate>
        </RoomStayCandidates>
        <HotelSearchCriteria>
            <Criterion>
                <RefPoint CodeContext="CountryCode">US</RefPoint>
                <RefPoint CodeContext="Destination">New York</RefPoint>
                <RefPoint CodeContext="Region">New York City Area</RefPoint>
            </Criterion>
        </HotelSearchCriteria>
    </AvailRequestSegment>
</AvailRequestSegments>
</OTA_HotelAvailRQ>';

$headers = array(
    "Accept: application/xml",
    "Content-type: application/x-www-form-urlencoded",
    "Content-Encoding: UTF-8",
    "Accept-Encoding: gzip,deflate",
    "Content-length: " . strlen($raw)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
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
$OTA_HotelAvailRS = $inputDoc->getElementsByTagName("OTA_HotelAvailRS");
//HotelStays
$HotelStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("HotelStays");
if ($HotelStays->length > 0) {
    $HotelStay = $HotelStays->item(0)->getElementsByTagName("HotelStay");
    if ($HotelStay->length > 0) {
        for ($i=0; $i < $HotelStay->length; $i++) { 
            $BasicPropertyInfo = $HotelStay->item($i)->getElementsByTagName("BasicPropertyInfo");
            if ($BasicPropertyInfo->length > 0) {
                $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                $HotelName = $BasicPropertyInfo->item(0)->getAttribute("HotelName");
                $AreaID = $BasicPropertyInfo->item(0)->getAttribute("AreaID");
                $HotelCodeContext = $BasicPropertyInfo->item(0)->getAttribute("HotelCodeContext");

                $Award = $BasicPropertyInfo->item(0)->getElementsByTagName("Award");
                if ($Award->length > 0) {
                    $Rating = $Award->item(0)->getAttribute("Rating");
                } else {
                    $Rating = "";
                }

                $img = "";
                $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName("VendorMessages");
                if ($VendorMessages->length > 0) {
                    $VendorMessage = $VendorMessages->item(0)->getElementsByTagName("VendorMessage");
                    if ($VendorMessage->length > 0) {
                        $Title = $VendorMessage->item(0)->getAttribute("Title");
                        $SubSection = $VendorMessage->item(0)->getElementsByTagName("SubSection");
                        if ($SubSection->length > 0) {
                            $Paragraph = $SubSection->item(0)->getElementsByTagName("Paragraph");
                            if ($Paragraph->length > 0) {
                                $Image = $Paragraph->item(0)->getElementsByTagName("Image");
                                if ($Image->length > 0) {
                                    for ($iAux=0; $iAux < $Image->length; $iAux++) { 
                                        $img = $Image->item($iAux)->nodeValue;

                                        try {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('images_hotelavail');
                                            $insert->values(array(
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'image' => $img,
                                                'hotelcode' => $HotelCode
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } catch (Exception $ex) {
                                            echo $return;
                                            echo "ERRO 2: " . $ex;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
            } else {
                $HotelCode = "";
                $HotelName = "";
                $AreaID = "";
                $HotelCodeContext = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hotelstay_hotelavail');
                $insert->values(array(
                    'hotelcode' => $HotelCode,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'hotelname' => $HotelName,
                    'hotelcodecontext' => $HotelCodeContext,
                    'areaid' => $AreaID,
                    'rating' => $Rating,
                    'title' => $Title
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO 1: " . $ex;
                echo $return;
            }
            
        }
    }
}

//Areas
$txt = "";
$Areas = $OTA_HotelAvailRS->item(0)->getElementsByTagName("Areas");
if ($Areas->length > 0) {
    $Area = $Areas->item(0)->getElementsByTagName("Area");
    if ($Area->length > 0) {
        for ($j=0; $j < $Area->length; $j++) { 
            $AreaID = $Area->item($j)->getAttribute("AreaID");

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('area_hotelavail');
                $insert->values(array(
                    'areaid' => $AreaID,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'hotelcode' => $HotelCode
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO 3: " . $ex;
                echo $return;
            }

            $AreaDescription = $Area->item($j)->getElementsByTagName("AreaDescription");
            if ($AreaDescription->length > 0) {
                $Name = $AreaDescription->item($jAux)->getAttribute("Name");

                $Text = $AreaDescription->item($jAux)->getElementsByTagName("Text");
                if ($Text->length > 0) {
                    for ($jAux2=0; $jAux2 < $Text->length; $jAux2++) { 
                        $txt = $Text->item($jAux2)->nodeValue;

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('areadescription_hotelavail');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'text' => $txt,
                                'name' => $Name,
                                'areaid' => $AreaID,
                                'hotelcode' => $HotelCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (Exception $ex) {
                            echo $return;
                            echo "ERRO 4: " . $ex;
                            echo $return;
                        }
                    }
                }
            }
        }
    }
}

//RoomStays
$text2 = "";
$RoomStays = $OTA_HotelAvailRS->item(0)->getElementsByTagName("RoomStays");
if ($RoomStays->length > 0) {
    $RoomStay = $RoomStays->item(0)->getElementsByTagName("RoomStay");
    if ($RoomStay->length > 0) {
        for ($k=0; $k < $RoomStay->length; $k++) { 
            $ResponseType = $RoomStay->item($k)->getAttribute("ResponseType");
            $RPH = $RoomStay->item($k)->getAttribute("RPH");
            $RoomStayCandidateRPH = $RoomStay->item($k)->getAttribute("RoomStayCandidateRPH");

            $RoomTypes = $RoomStay->item($k)->getElementsByTagName("RoomTypes");
            if ($RoomTypes->length > 0) {
                $RoomType = $RoomTypes->item(0)->getElementsByTagName("RoomType");
                if ($RoomType->length > 0) {
                    $RoomTypeCode = $RoomType->item(0)->getAttribute("RoomTypeCode");
                    //RoomDescription
                    $RoomDescription = $RoomType->item(0)->getElementsByTagName("RoomDescription");
                    if ($RoomDescription->length > 0) {
                        $Text = $RoomDescription->item(0)->getElementsByTagName("Text");
                        if ($Text->length > 0) {
                            $Language = $Text->item(0)->getAttribute("Language");
                            $text2 = $Text->item(0)->nodeValue;
                        }
                    }
                    //Amenities
                    $amenit = "";
                    $Amenities = $RoomType->item(0)->getElementsByTagName("Amenities");
                    if ($Amenities->length > 0) {
                        $Amenity = $Amenities->item(0)->getElementsByTagName("Amenity");
                        if ($Amenity->length > 0) {
                            for ($t=0; $t < $Amenity->length; $t++) { 
                                $amenit = $Amenity->item($t)->nodeValue;
                            }
                        }
                    }
                }
            }

            $RoomRates = $RoomStay->item($k)->getElementsByTagName("RoomRates");
            if ($RoomRates->length > 0) {
                $RoomRate = $RoomRates->item(0)->getElementsByTagName("RoomRate");
                if ($RoomRate->length > 0) {
                    $RoomTypeCode2 = $RoomRate->item(0)->getAttribute("RoomTypeCode");
                    $NumberOfUnits = $RoomRate->item(0)->getAttribute("NumberOfUnits");
                    //Rates
                    $Rates = $RoomRate->item(0)->getElementsByTagName("Rates");
                    if ($Rates->length > 0) {
                        $Rate = $Rates->item(0)->getElementsByTagName("Rate");
                        if ($Rate->length > 0) {
                            $Base = $Rate->item(0)->getElementsByTagName("Base");
                            if ($Base->length > 0) {
                                $BaseCurrencyCode = $Base->item(0)->getAttribute("CurrencyCode");
                                $BaseAmountAfterTax = $Base->item(0)->getAttribute("AmountAfterTax");
                            }
                            $Total = $Rate->item(0)->getElementsByTagName("Total");
                            if ($Total->length > 0) {
                                $TotalCurrencyCode = $Total->item(0)->getAttribute("CurrencyCode");
                                $TotalAmountAfterTax = $Total->item(0)->getAttribute("AmountAfterTax");
                            }
                        }
                    }
                    //Features
                    $Features = $RoomRate->item(0)->getElementsByTagName("Features");
                    if ($Features->length > 0) {
                        $Feature = $Features->item(0)->getElementsByTagName("Feature");
                        if ($Feature->length > 0) {
                            $Description = $Feature->item(0)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                $Text = $Description->item(0)->getElementsByTagName('Text');
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
            //GuestCounts
            $GuestCounts = $RoomStay->item($k)->getElementsByTagName("GuestCounts");
            if ($GuestCounts->length > 0) {
                $GuestCount = $GuestCounts->item(0)->getElementsByTagName("GuestCount");
                if ($GuestCount->length > 0) {
                    $Count = $GuestCount->item(0)->getAttribute("Count");
                    $AgeQualifyingCode = $GuestCount->item(0)->getAttribute("AgeQualifyingCode");
                }
            }
            //TimeSpan
            $TimeSpan = $RoomStay->item($k)->getElementsByTagName("TimeSpan");
            if ($TimeSpan->length > 0) {
                $End = $TimeSpan->item(0)->getAttribute("End");
                $Start = $TimeSpan->item(0)->getAttribute("Start");
            }
            //Reference
            $Reference = $RoomStay->item($k)->getElementsByTagName("Reference");
            if ($Reference->length > 0) {
                $ID_Context = $Reference->item(0)->getAttribute("ID_Context");
                $ReferenceID = $Reference->item(0)->getAttribute("ID");
                $Type = $Reference->item(0)->getAttribute("Type");
            }
            //BasicPropertyInfo
            $BasicPropertyInfo = $RoomStay->item($k)->getElementsByTagName("BasicPropertyInfo");
            if ($BasicPropertyInfo->length > 0) {
                $HotelCode = $BasicPropertyInfo->item(0)->getAttribute("HotelCode");
                $VendorMessages = $BasicPropertyInfo->item(0)->getElementsByTagName('VendorMessages');
                if ($VendorMessages->length > 0) {
                    $VendorMessage = $VendorMessages->item(0)->getElementsByTagName('VendorMessage');
                    if ($VendorMessage->length > 0) {
                        $VendorMessageTitle = $VendorMessage->item(0)->getAttribute("Title");
                        $VendorMessageInfoType = $VendorMessage->item(0)->getAttribute("InfoType");

                        $SubSection = $VendorMessage->item(0)->getElementsByTagName('SubSection');
                        if ($SubSection->length > 0) {
                            for ($x=0; $x < $SubSection->length; $x++) { 
                                $SubCode = $SubSection->item($x)->getAttribute("SubCode");
                                $SubTitle = $SubSection->item($x)->getAttribute("SubTitle");

                                $Paragraph = $SubSection->item($x)->getElementsByTagName('Paragraph');
                                if ($Paragraph->length > 0) {
                                    $ParagraphText = $Paragraph->item(0)->getElementsByTagName('Text');
                                    if ($ParagraphText->length > 0) {
                                        $ParagraphText = $ParagraphText->item(0)->nodeValue;
                                    } else {
                                        $ParagraphText = "";
                                    }
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('subsection_hotelavail');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'subcode' => $SubCode,
                                        'subtitle' => $SubTitle,
                                        'paragraphtext' => $ParagraphText,
                                        'roomtypecode' => $RoomTypeCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (Exception $ex) {
                                    echo $return;
                                    echo "ERRO 6: " . $ex;
                                    echo $return;
                                }
                            }
                        }
                    }
                }
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('roomstay_hotelavail');
                $insert->values(array(
                    'roomtypecode' => $RoomTypeCode,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'responsetype' => $ResponseType,
                    'rph' => $RPH,
                    'roomstaycandidaterph' => $RoomStayCandidateRPH,
                    'numberofunits' => $NumberOfUnits,
                    'basecurrencycode' => $BaseCurrencyCode,
                    'baseamountaftertax' => $BaseAmountAfterTax,
                    'totalcurrencycode' => $TotalCurrencyCode,
                    'totalamountaftertax' => $TotalAmountAfterTax,
                    'description' => $Text,
                    'count' => $Count,
                    'agequalifyingcode' => $AgeQualifyingCode,
                    'end' => $End,
                    'start' => $Start,
                    'id_context' => $ID_Context,
                    'referenceid' => $ReferenceID,
                    'type' => $Type,
                    'hotelcode' => $HotelCode,
                    'vendormessagetitle' => $VendorMessageTitle,
                    'vendormessageinfotype' => $VendorMessageInfoType
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO 5: " . $ex;
                echo $return;
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