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
echo "COMECOU READ XML<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.travelport.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'BrandingLite.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$TravelportBrandingLite = $inputDoc->getElementsByTagName("TravelportBrandingLite");
if ($TravelportBrandingLite->length > 0) {
    $FareFamily = $TravelportBrandingLite->item(0)->getElementsByTagName("FareFamily");
    if ($FareFamily->length > 0) {
        for ($i=0; $i < $FareFamily->length; $i++) { 
            $Carrier = $FareFamily->item($i)->getAttribute("Carrier");
            $FareFamilyID = $FareFamily->item($i)->getAttribute("FareFamilyID");
            $ATPCOSequenceNumber = $FareFamily->item($i)->getAttribute("ATPCOSequenceNumber");
            $ProgramName = $FareFamily->item($i)->getAttribute("ProgramName");
            $ATPCOProgramCode = $FareFamily->item($i)->getAttribute("ATPCOProgramCode");
            // FlightDateRange
            $FlightDateRange = $FareFamily->item($i)->getElementsByTagName("FlightDateRange");
            if ($FlightDateRange->length > 0) {
                $EffectiveTravelDate = $FlightDateRange->item(0)->getAttribute("EffectiveTravelDate");
                $DiscontinueTravelDate = $FlightDateRange->item(0)->getAttribute("DiscontinueTravelDate");
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('farefamily');
                $insert->values(array(
                    'id' => $FareFamilyID,
                    'datetime_updated' => time(),
                    'carrier' => $Carrier,
                    'atpcosequencenumber' => $ATPCOSequenceNumber,
                    'programname' => $ProgramName,
                    'atpcoprogramcode' => $ATPCOProgramCode,
                    'effectivetraveldate' => $EffectiveTravelDate,
                    'discontinuetraveldate' => $DiscontinueTravelDate
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } catch (\Exception $e) {
                echo $return;
                echo "Error 1: " . $e;
                echo $return;
            }

            // GeographicLocationDetails
            $GeographicLocationDetails = $FareFamily->item($i)->getElementsByTagName("GeographicLocationDetails");
            if ($GeographicLocationDetails->length > 0) {
                $GeoLocation = $GeographicLocationDetails->item(0)->getElementsByTagName("GeoLocation");
                if ($GeoLocation->length > 0) {
                    for ($j=0; $j < $GeoLocation->length; $j++) { 
                        $Loc1Type = $GeoLocation->item($j)->getAttribute("Loc1Type");
                        $Loc1Code = $GeoLocation->item($j)->getAttribute("Loc1Code");
                        $Loc2Type = $GeoLocation->item($j)->getAttribute("Loc2Type");
                        $Loc2Code = $GeoLocation->item($j)->getAttribute("Loc2Code");
                        $Permitted = $GeoLocation->item($j)->getAttribute("Permitted");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('farefamily_geolocation');
                            $insert->values(array(
                                'datetime_updated' => time(),
                                'loc1type' => $Loc1Type,
                                'loc1code' => $Loc1Code,
                                'loc2type' => $Loc2Type,
                                'loc2code' => $Loc2Code,
                                'permitted' => $Permitted,
                                'farefamilyid' => $FareFamilyID
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 2: " . $e;
                            echo $return;
                        }
                    }
                }
            }
            // IncludedBrandList
            $IncludedBrandList = $FareFamily->item($i)->getElementsByTagName("IncludedBrandList");
            if ($IncludedBrandList->length > 0) {
                $Brand = $IncludedBrandList->item(0)->getElementsByTagName("Brand");
                if ($Brand->length > 0) {
                    for ($k=0; $k < $Brand->length; $k++) { 
                        $BrandID = $Brand->item($k)->getAttribute("BrandID");
                        $ATPCOBrandName = $Brand->item($k)->getAttribute("ATPCOBrandName");
                        $ATPCOBrandCode = $Brand->item($k)->getAttribute("ATPCOBrandCode");
                        $TravelportBrandName = $Brand->item($k)->getAttribute("TravelportBrandName");
                        $Hierarchy = $Brand->item($k)->getAttribute("Hierarchy");

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('farefamily_brand');
                            $insert->values(array(
                                'datetime_updated' => time(),
                                'brandid' => $BrandID,
                                'atpcobrandname' => $ATPCOBrandName,
                                'atpcobrandcode' => $ATPCOBrandCode,
                                'travelportbrandname' => $TravelportBrandName,
                                'hierarchy' => $Hierarchy,
                                'farefamilyid' => $FareFamilyID
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 3: " . $e;
                            echo $return;
                        }
                        // ImageLocation
                        $ImageLocation = $Brand->item($k)->getElementsByTagName("ImageLocation");
                        if ($ImageLocation->length > 0) {
                            for ($kAux=0; $kAux < $ImageLocation->length; $kAux++) { 
                                $CDNImage = $ImageLocation->item($kAux)->getElementsByTagName("CDNImage");
                                if ($CDNImage->length > 0) {
                                    $ImageId = $CDNImage->item(0)->getAttribute("ImageId");
                                    $Type = $CDNImage->item(0)->getAttribute("Type");
                                    $ImageWidth = $CDNImage->item(0)->getAttribute("ImageWidth");
                                    $ImageHeight = $CDNImage->item(0)->getAttribute("ImageHeight");
                                    $CDNImage = $CDNImage->item(0)->nodeValue;
                                } else {
                                    $CDNImage = "";
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('farefamily_brand_imagelocation');
                                    $insert->values(array(
                                        'datetime_updated' => time(),
                                        'imageid' => $ImageId,
                                        'type' => $Type,
                                        'imagewidth' => $ImageWidth,
                                        'imageheight' => $ImageHeight,
                                        'cdnimage' => $CDNImage,
                                        'farefamilyid' => $FareFamilyID
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 4: " . $e;
                                    echo $return;
                                }
                            }
                        }
                        // PriorityServices
                        $PriorityServices = $Brand->item($k)->getElementsByTagName("PriorityServices");
                        if ($PriorityServices->length > 0) {
                            $PriorityService = $PriorityServices->item(0)->getElementsByTagName("PriorityService");
                            if ($PriorityService->length > 0) {
                                for ($kAux2=0; $kAux2 < $PriorityService->length; $kAux2++) { 
                                    $ServiceID = $PriorityService->item($kAux2)->getAttribute("ServiceID");
                                    $Tag = $PriorityService->item($kAux2)->getAttribute("Tag");
                                    $ServiceChargeable = $PriorityService->item($kAux2)->getAttribute("ServiceChargeable");
                                    $DisplayOrder = $PriorityService->item($kAux2)->getAttribute("DisplayOrder");

                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('farefamily_brand_priorityservices');
                                        $insert->values(array(
                                            'datetime_updated' => time(),
                                            'serviceid' => $ServiceID,
                                            'tag' => $Tag,
                                            'servicechargeable' => $ServiceChargeable,
                                            'displayorder' => $DisplayOrder,
                                            'farefamilyid' => $FareFamilyID
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error 5: " . $e;
                                        echo $return;
                                    }
                                }
                            }
                        }
                        // OptionalServices
                        $OptionalServices = $Brand->item($k)->getElementsByTagName("OptionalServices");
                        if ($OptionalServices->length > 0) {
                            $OptionalService = $OptionalServices->item(0)->getElementsByTagName("OptionalService");
                            if ($OptionalService->length > 0) {
                                for ($kAux3=0; $kAux3 < $OptionalService->length; $kAux3++) { 
                                    $ServiceID = $OptionalService->item($kAux3)->getAttribute("ServiceID");
                                    $Tag = $OptionalService->item($kAux3)->getAttribute("Tag");
                                    $ServiceChargeable = $OptionalService->item($kAux3)->getAttribute("ServiceChargeable");
                                    $DisplayOrder = $OptionalService->item($kAux3)->getAttribute("DisplayOrder");
                                    $LoyaltyDetails = $OptionalService->item($kAux3)->getElementsByTagName("LoyaltyDetails");
                                    if ($LoyaltyDetails->length > 0) {
                                        $AccrualType = $LoyaltyDetails->item(0)->getAttribute("AccrualType");
                                        $Percent = $LoyaltyDetails->item(0)->getAttribute("Percent");
                                    }
                                    $Text = $OptionalService->item($kAux3)->getElementsByTagName("Text");
                                    if ($Text->length > 0) {
                                        $Type = $Text->item(0)->getAttribute("Type");
                                        $Text = $Text->item(0)->nodeValue;
                                    } else {
                                        $Text = "";
                                    }

                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('farefamily_brand_optionalservices');
                                        $insert->values(array(
                                            'datetime_updated' => time(),
                                            'serviceid' => $ServiceID,
                                            'tag' => $Tag,
                                            'servicechargeable' => $ServiceChargeable,
                                            'displayorder' => $DisplayOrder,
                                            'accrualtype' => $AccrualType,
                                            'percent' => $Percent,
                                            'type' => $Type,
                                            'optionalservice' => $OptionalService,
                                            'farefamilyid' => $FareFamilyID
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error 6: " . $e;
                                        echo $return;
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
