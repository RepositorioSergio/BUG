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

$config = new \Zend\Config\Config(include '../config/autoload/global.accor.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$filename = 'ota/0340.xml';
$response = file_get_contents($filename);

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$OTA_HotelDescriptiveContentNotifRQ = $inputDoc->getElementsByTagName("OTA_HotelDescriptiveContentNotifRQ");
$HotelDescriptiveContents = $OTA_HotelDescriptiveContentNotifRQ->item(0)->getElementsByTagName('HotelDescriptiveContents');
if ($HotelDescriptiveContents->length > 0) {
    $HotelDescriptiveContent = $HotelDescriptiveContents->item(0)->getElementsByTagName('HotelDescriptiveContent');
    if ($HotelDescriptiveContent->length > 0) {
        for ($i=0; $i < $HotelDescriptiveContent->length; $i++) { 
            $UnitOfMeasureCode = $HotelDescriptiveContent->item($i)->getAttribute('UnitOfMeasureCode');
            $HotelCode = $HotelDescriptiveContent->item($i)->getAttribute('HotelCode');
            $CurrencyCode = $HotelDescriptiveContent->item($i)->getAttribute('CurrencyCode');
            $HotelName = $HotelDescriptiveContent->item($i)->getAttribute('HotelName');
            $BrandName = $HotelDescriptiveContent->item($i)->getAttribute('BrandName');
            $BrandCode = $HotelDescriptiveContent->item($i)->getAttribute('BrandCode');
            $ChainCode = $HotelDescriptiveContent->item($i)->getAttribute('ChainCode');
            $ID = $HotelDescriptiveContent->item($i)->getAttribute('ID');
            $TimeZone = $HotelDescriptiveContent->item($i)->getAttribute('TimeZone');
            $LanguageCode = $HotelDescriptiveContent->item($i)->getAttribute('LanguageCode');

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('accor_descriptioncontents');
                $select->where(array(
                    'hotelcode' => $HotelCode,
                    'languagecode' => $LanguageCode
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (string) $data['hotelcode'];
                    if ($id != "") {
                        $sql = new Sql($db);
                        $data = array(
                            'hotelid' => $ID,
                            'hotelcode' => $HotelCode,
                            'name' => $HotelName,
                            'brandcode' => $BrandCode,
                            'brandname' => $BrandName,
                            'chaincode' => $ChainCode,
                            'unitofmeasurecode' => $UnitOfMeasureCode,
                            'currencycode' => $CurrencyCode,
                            'timezone' => $TimeZone,
                            'languagecode' => $LanguageCode
                            );
                            $where['hotelcode = ?']  = $HotelCode;
                        $update = $sql->update('accor_descriptioncontents', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('accor_descriptioncontents');
                        $insert->values(array(
                            'hotelid' => $ID,
                            'hotelcode' => $HotelCode,
                            'name' => $HotelName,
                            'brandcode' => $BrandCode,
                            'brandname' => $BrandName,
                            'chaincode' => $ChainCode,
                            'unitofmeasurecode' => $UnitOfMeasureCode,
                            'currencycode' => $CurrencyCode,
                            'timezone' => $TimeZone,
                            'languagecode' => $LanguageCode
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    }
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('accor_descriptioncontents');
                    $insert->values(array(
                        'hotelid' => $ID,
                        'hotelcode' => $HotelCode,
                        'name' => $HotelName,
                        'brandcode' => $BrandCode,
                        'brandname' => $BrandName,
                        'chaincode' => $ChainCode,
                        'unitofmeasurecode' => $UnitOfMeasureCode,
                        'currencycode' => $CurrencyCode,
                        'timezone' => $TimeZone,
                        'languagecode' => $LanguageCode
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }
            } catch (\Exception $e) {
                echo $return;
                echo "Error 1: " . $e;
                echo $return;
            }

            $HotelInfo = $HotelDescriptiveContent->item($i)->getElementsByTagName('HotelInfo');
            if ($HotelInfo->length > 0) {
                $WhenBuilt = $HotelInfo->item(0)->getAttribute('WhenBuilt');
                $HotelStatus = $HotelInfo->item(0)->getAttribute('HotelStatus');
                $HotelStatusCode = $HotelInfo->item(0)->getAttribute('HotelStatusCode');
                $HotelName = $HotelInfo->item(0)->getElementsByTagName('HotelName');
                if ($HotelName->length > 0) {
                    $HotelShortName = $HotelName->item(0)->getAttribute('HotelShortName');
                }
                $CategoryCodes = $HotelInfo->item(0)->getElementsByTagName('CategoryCodes');
                if ($CategoryCodes->length > 0) {
                    $LocationCategory = $CategoryCodes->item(0)->getElementsByTagName('LocationCategory');
                    if ($LocationCategory->length > 0) {
                        $LocationCategoryCode = $LocationCategory->item(0)->getAttribute('Code');
                    }
                    $SegmentCategory = $CategoryCodes->item(0)->getElementsByTagName('SegmentCategory');
                    if ($SegmentCategory->length > 0) {
                        $SegmentCategoryCode = $SegmentCategory->item(0)->getAttribute('Code');
                    }
                    $HotelCategory = $CategoryCodes->item(0)->getElementsByTagName('HotelCategory');
                    if ($HotelCategory->length > 0) {
                        $HotelCategoryCode = $LocationCategory->item(0)->getAttribute('Code');
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('accor_descriptioncontents_hotelinfo');
                        $select->where(array(
                            'hotelcode' => $HotelCode,
                            'hotelcategorycode' => $HotelCode,
                            'hotelshortname' => $HotelShortName
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string) $data['hotelcode'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'whenbuilt' => $WhenBuilt,
                                    'hotelstatus' => $HotelStatus,
                                    'hotelstatuscode' => $HotelStatusCode,
                                    'hotelshortname' => $HotelShortName,
                                    'locationcategorycode' => $LocationCategoryCode,
                                    'segmentcategorycode' => $SegmentCategoryCode,
                                    'hotelcategorycode' => $HotelCategoryCode,
                                    'hotelcode' => $HotelCode
                                    );
                                    $where['hotelcode = ?']  = $HotelCode;
                                $update = $sql->update('accor_descriptioncontents_hotelinfo', $data, $where);
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();   
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('accor_descriptioncontents_hotelinfo');
                                $insert->values(array(
                                    'whenbuilt' => $WhenBuilt,
                                    'hotelstatus' => $HotelStatus,
                                    'hotelstatuscode' => $HotelStatusCode,
                                    'hotelshortname' => $HotelShortName,
                                    'locationcategorycode' => $LocationCategoryCode,
                                    'segmentcategorycode' => $SegmentCategoryCode,
                                    'hotelcategorycode' => $HotelCategoryCode,
                                    'hotelcode' => $HotelCode
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('accor_descriptioncontents_hotelinfo');
                            $insert->values(array(
                                'whenbuilt' => $WhenBuilt,
                                'hotelstatus' => $HotelStatus,
                                'hotelstatuscode' => $HotelStatusCode,
                                'hotelshortname' => $HotelShortName,
                                'locationcategorycode' => $LocationCategoryCode,
                                'segmentcategorycode' => $SegmentCategoryCode,
                                'hotelcategorycode' => $HotelCategoryCode,
                                'hotelcode' => $HotelCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error 2: " . $e;
                        echo $return;
                    }

                    $GuestRoomInfo = $CategoryCodes->item(0)->getElementsByTagName('GuestRoomInfo');
                    if ($GuestRoomInfo->length > 0) {
                        for ($iAux=0; $iAux < $GuestRoomInfo->length; $iAux++) { 
                            $Code = $GuestRoomInfo->item($iAux)->getAttribute('Code');
                            $Quantity = $GuestRoomInfo->item($iAux)->getAttribute('Quantity');

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_hotelinfo_guestroominfo');
                                $select->where(array(
                                    'hotelcode' => $HotelCode,
                                    'code' => $Code
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'code' => $Code,
                                            'quantity' => $Quantity,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_hotelinfo_guestroominfo', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_hotelinfo_guestroominfo');
                                        $insert->values(array(
                                            'code' => $Code,
                                            'quantity' => $Quantity,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_hotelinfo_guestroominfo');
                                    $insert->values(array(
                                        'code' => $Code,
                                        'quantity' => $Quantity,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                    }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 3: " . $e;
                                echo $return;
                            }

                            $Description = $GuestRoomInfo->item($iAux)->getElementsByTagName('Description');
                            if ($Description->length > 0) {
                                $text2 = "";
                                $Text = $Description->item(0)->getElementsByTagName('Text');
                                if ($Text->length > 0) {
                                    for ($iAux2=0; $iAux2 < $Text->length; $iAux2++) { 
                                        $Language = $Text->item($iAux2)->getAttribute('Language');
                                        $text2 = $Text->item($iAux2)->nodeValue;

                                        try {
                                            $sql = new Sql($db);
                                            $select = $sql->select();
                                            $select->from('accor_descriptioncontents_hotelinfo_guestroominfo_descriptions');
                                            $select->where(array(
                                                'hotelcode' => $HotelCode,
                                                'language' => $Language
                                            ));
                                            $statement = $sql->prepareStatementForSqlObject($select);
                                            $result = $statement->execute();
                                            $result->buffer();
                                            $customers = array();
                                            if ($result->valid()) {
                                                $data = $result->current();
                                                $id = (string) $data['hotelcode'];
                                                if ($id != "") {
                                                    $sql = new Sql($db);
                                                    $data = array(
                                                        'language' => $Language,
                                                        'description' => $text2,
                                                        'hotelcode' => $HotelCode
                                                        );
                                                        $where['hotelcode = ?']  = $HotelCode;
                                                    $update = $sql->update('accor_descriptioncontents_hotelinfo_guestroominfo_descriptions', $data, $where);
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();   
                                                } else {
                                                    $sql = new Sql($db);
                                                    $insert = $sql->insert();
                                                    $insert->into('accor_descriptioncontents_hotelinfo_guestroominfo_descriptions');
                                                    $insert->values(array(
                                                        'language' => $Language,
                                                        'description' => $text2,
                                                        'hotelcode' => $HotelCode
                                                    ), $insert::VALUES_MERGE);
                                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                                    $results = $statement->execute();
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();
                                                }
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('accor_descriptioncontents_hotelinfo_guestroominfo_descriptions');
                                                $insert->values(array(
                                                    'language' => $Language,
                                                    'description' => $text2,
                                                    'hotelcode' => $HotelCode
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                                }
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 4: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $Descriptions = $HotelInfo->item(0)->getElementsByTagName('Descriptions');
                if ($Descriptions->length > 0) {
                    $Renovation = $Descriptions->item(0)->getElementsByTagName('Renovation');
                    if ($Renovation->length > 0) {
                        $RenovationCompletionDate = $Renovation->item(0)->getAttribute('RenovationCompletionDate');
                    }
                    $Position = $HotelInfo->item(0)->getElementsByTagName('Position');
                    if ($Position->length > 0) {
                        $Latitude = $Position->item(0)->getAttribute('Latitude');
                        $Longitude = $Position->item(0)->getAttribute('Longitude');
                    }
                    $Description = $Descriptions->item(0)->getElementsByTagName('Description');
                    if ($Description->length > 0) {
                        $InfoCode = $Description->item(0)->getAttribute('InfoCode');
                        $AdditionalDetailCode = $Description->item(0)->getAttribute('AdditionalDetailCode');

                        try {
                            $sql = new Sql($db);
                            $select = $sql->select();
                            $select->from('accor_descriptioncontents_hotelinfo_descriptions');
                            $select->where(array(
                                'hotelcode' => $HotelCode,
                                'infocode' => $InfoCode,
                                'additionaldetailcode' => $AdditionalDetailCode
                            ));
                            $statement = $sql->prepareStatementForSqlObject($select);
                            $result = $statement->execute();
                            $result->buffer();
                            $customers = array();
                            if ($result->valid()) {
                                $data = $result->current();
                                $id = (string) $data['hotelcode'];
                                if ($id != "") {
                                    $sql = new Sql($db);
                                    $data = array(
                                        'renovationcompletiondate' => $RenovationCompletionDate,
                                        'latitude' => $Latitude,
                                        'longitude' => $Longitude,
                                        'infocode' => $InfoCode,
                                        'additionaldetailcode' => $AdditionalDetailCode,
                                        'hotelcode' => $HotelCode
                                        );
                                        $where['hotelcode = ?']  = $HotelCode;
                                    $update = $sql->update('accor_descriptioncontents_hotelinfo_descriptions', $data, $where);
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();   
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_hotelinfo_descriptions');
                                    $insert->values(array(
                                        'renovationcompletiondate' => $RenovationCompletionDate,
                                        'latitude' => $Latitude,
                                        'longitude' => $Longitude,
                                        'infocode' => $InfoCode,
                                        'additionaldetailcode' => $AdditionalDetailCode,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('accor_descriptioncontents_hotelinfo_descriptions');
                                $insert->values(array(
                                    'renovationcompletiondate' => $RenovationCompletionDate,
                                    'latitude' => $Latitude,
                                    'longitude' => $Longitude,
                                    'infocode' => $InfoCode,
                                    'additionaldetailcode' => $AdditionalDetailCode,
                                    'hotelcode' => $HotelCode
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                                }
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 5: " . $e;
                            echo $return;
                        }

                        $text2 = "";
                        $Text = $Description->item(0)->getElementsByTagName('Text');
                        if ($Text->length > 0) {
                            for ($iAux2=0; $iAux2 < $Text->length; $iAux2++) { 
                                $Language = $Text->item($iAux2)->getAttribute('Language');
                                $text2 = $Text->item($iAux2)->nodeValue;

                                try {
                                    $sql = new Sql($db);
                                    $select = $sql->select();
                                    $select->from('accor_descriptioncontents_hotelinfo_descriptions');
                                    $select->where(array(
                                        'hotelcode' => $HotelCode,
                                        'language' => $Language
                                    ));
                                    $statement = $sql->prepareStatementForSqlObject($select);
                                    $result = $statement->execute();
                                    $result->buffer();
                                    $customers = array();
                                    if ($result->valid()) {
                                        $data = $result->current();
                                        $id = (string) $data['hotelcode'];
                                        if ($id != "") {
                                            $sql = new Sql($db);
                                            $data = array(
                                                'language' => $Language,
                                                'description' => $text2,
                                                'hotelcode' => $HotelCode
                                                );
                                                $where['hotelcode = ?']  = $HotelCode;
                                            $update = $sql->update('accor_descriptioncontents_hotelinfo_descriptions_description', $data, $where);
                                            $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();   
                                        } else {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('accor_descriptioncontents_hotelinfo_descriptions_description');
                                            $insert->values(array(
                                                'language' => $Language,
                                                'description' => $text2,
                                                'hotelcode' => $HotelCode
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                        }
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_hotelinfo_descriptions_description');
                                        $insert->values(array(
                                            'language' => $Language,
                                            'description' => $text2,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                        }
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "Error 6: " . $e;
                                    echo $return;
                                }

                            }
                        }
                    }
                }
            
                $Services = $HotelInfo->item(0)->getElementsByTagName('Services');
                if ($Services->length > 0) {
                    $Service = $Services->item(0)->getElementsByTagName('Service');
                    if ($Service->length > 0) {
                        for ($iAux3=0; $iAux3 < $Service->length; $iAux3++) { 
                            $Code = $Service->item($iAux3)->getAttribute('Code');
                            $Quantity = $Service->item($iAux3)->getAttribute('Quantity');
                            $ProximityCode = $Service->item($iAux3)->getAttribute('ProximityCode');

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_hotelinfo_services');
                                $select->where(array(
                                    'code' => $Code,
                                    'proximitycode' => $ProximityCode,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'code' => $Code,
                                            'quantity' => $Quantity,
                                            'proximitycode' => $ProximityCode,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_hotelinfo_services', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_hotelinfo_services');
                                        $insert->values(array(
                                            'code' => $Code,
                                            'quantity' => $Quantity,
                                            'proximitycode' => $ProximityCode,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_hotelinfo_services');
                                    $insert->values(array(
                                        'code' => $Code,
                                        'quantity' => $Quantity,
                                        'proximitycode' => $ProximityCode,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                    }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 7: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
            }
            $FacilityInfo = $HotelDescriptiveContent->item($i)->getElementsByTagName('FacilityInfo');
            if ($FacilityInfo->length > 0) {
                $MeetingRooms = $FacilityInfo->item(0)->getElementsByTagName('MeetingRooms');
                if ($MeetingRooms->length > 0) {
                    $SmallestRoomSpace = $MeetingRooms->item(0)->getAttribute('SmallestRoomSpace');
                    $LargestRoomSpace = $MeetingRooms->item(0)->getAttribute('LargestRoomSpace');
                    $LargestSeatingCapacity = $MeetingRooms->item(0)->getAttribute('LargestSeatingCapacity');
                    $SmallestSeatingCapacity = $MeetingRooms->item(0)->getAttribute('SmallestSeatingCapacity');

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('accor_descriptioncontents_descriptivecontent_meetingrooms');
                        $select->where(array(
                            'smallestroomspace' => $SmallestRoomSpace,
                            'largestroomspace' => $LargestRoomSpace,
                            'largestseatingcapacity' => $LargestSeatingCapacity,
                            'smallestseatingcapacity' => $SmallestSeatingCapacity,
                            'hotelcode' => $HotelCode
                        ));
                        $statement = $sql->prepareStatementForSqlObject($select);
                        $result = $statement->execute();
                        $result->buffer();
                        $customers = array();
                        if ($result->valid()) {
                            $data = $result->current();
                            $id = (string) $data['hotelcode'];
                            if ($id != "") {
                                $sql = new Sql($db);
                                $data = array(
                                    'smallestroomspace' => $SmallestRoomSpace,
                                    'largestroomspace' => $LargestRoomSpace,
                                    'largestseatingcapacity' => $LargestSeatingCapacity,
                                    'smallestseatingcapacity' => $SmallestSeatingCapacity,
                                    'hotelcode' => $HotelCode
                                    );
                                    $where['hotelcode = ?']  = $HotelCode;
                                $update = $sql->update('accor_descriptioncontents_descriptivecontent_meetingrooms', $data, $where);
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();   
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('accor_descriptioncontents_descriptivecontent_meetingrooms');
                                $insert->values(array(
                                    'smallestroomspace' => $SmallestRoomSpace,
                                    'largestroomspace' => $LargestRoomSpace,
                                    'largestseatingcapacity' => $LargestSeatingCapacity,
                                    'smallestseatingcapacity' => $SmallestSeatingCapacity,
                                    'hotelcode' => $HotelCode
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            }
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('accor_descriptioncontents_descriptivecontent_meetingrooms');
                            $insert->values(array(
                                'smallestroomspace' => $SmallestRoomSpace,
                                'largestroomspace' => $LargestRoomSpace,
                                'largestseatingcapacity' => $LargestSeatingCapacity,
                                'smallestseatingcapacity' => $SmallestSeatingCapacity,
                                'hotelcode' => $HotelCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        }
                    } catch (\Exception $e) {
                        echo $return;
                        echo "Error 8: " . $e;
                        echo $return;
                    }

                    $MeetingRoom = $MeetingRooms->item(0)->getElementsByTagName('MeetingRoom');
                    if ($MeetingRoom->length > 0) {
                        for ($iAux4=0; $iAux4 < $MeetingRoom->length; $iAux4++) { 
                            $ID = $MeetingRoom->item($iAux4)->getAttribute('ID');
                            $RoomName = $MeetingRoom->item($iAux4)->getAttribute('RoomName');
                            $MeetingRoomCapacity = $MeetingRoom->item($iAux4)->getAttribute('MeetingRoomCapacity');
                            $Dimension = $MeetingRoom->item($iAux4)->getElementsByTagName('Dimension');
                            if ($Dimension->length > 0) {
                                $Area = $Dimension->item(0)->getAttribute('Area');
                                $Height = $Dimension->item(0)->getAttribute('Height');
                                $Units = $Dimension->item(0)->getAttribute('Units');
                            }

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_meetrooms_meet');
                                $select->where(array(
                                    'meetingroomid' => $ID,
                                    'roomname' => $RoomName,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'meetingroomid' => $ID,
                                            'roomname' => $RoomName,
                                            'meetingroomcapacity' => $MeetingRoomCapacity,
                                            'area' => $Area,
                                            'height' => $Height,
                                            'units' => $Units,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_meetrooms_meet', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_meetrooms_meet');
                                        $insert->values(array(
                                            'meetingroomid' => $ID,
                                            'roomname' => $RoomName,
                                            'meetingroomcapacity' => $MeetingRoomCapacity,
                                            'area' => $Area,
                                            'height' => $Height,
                                            'units' => $Units,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_meetrooms_meet');
                                    $insert->values(array(
                                        'meetingroomid' => $ID,
                                        'roomname' => $RoomName,
                                        'meetingroomcapacity' => $MeetingRoomCapacity,
                                        'area' => $Area,
                                        'height' => $Height,
                                        'units' => $Units,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 9: " . $e;
                                echo $return;
                            }

                            $AvailableCapacities = $MeetingRoom->item($iAux4)->getElementsByTagName('AvailableCapacities');
                            if ($AvailableCapacities->length > 0) {
                                $MeetingRoomCapacity = $AvailableCapacities->item(0)->getElementsByTagName('MeetingRoomCapacity');
                                if ($MeetingRoomCapacity->length > 0) {
                                    for ($iAux5=0; $iAux5 < $MeetingRoomCapacity->length; $iAux5++) { 
                                        $MeetingRoomFormatCode = $MeetingRoomCapacity->item($iAux5)->getAttribute('MeetingRoomFormatCode');
                                        $Occupancy = $MeetingRoomCapacity->item($iAux5)->getElementsByTagName('Occupancy');
                                        if ($Occupancy->length > 0) {
                                            $MaxOccupancy = $Occupancy->item(0)->getAttribute('MaxOccupancy');
                                        }

                                        try {
                                            $sql = new Sql($db);
                                            $select = $sql->select();
                                            $select->from('accor_descriptioncontents_descriptivecontent_mroomcapacity');
                                            $select->where(array(
                                                'meetingroomformatcode' => $MeetingRoomFormatCode,
                                                'maxoccupancy' => $MaxOccupancy,
                                                'hotelcode' => $HotelCode
                                            ));
                                            $statement = $sql->prepareStatementForSqlObject($select);
                                            $result = $statement->execute();
                                            $result->buffer();
                                            $customers = array();
                                            if ($result->valid()) {
                                                $data = $result->current();
                                                $id = (string) $data['hotelcode'];
                                                if ($id != "") {
                                                    $sql = new Sql($db);
                                                    $data = array(
                                                        'meetingroomformatcode' => $MeetingRoomFormatCode,
                                                        'maxoccupancy' => $MaxOccupancy,
                                                        'hotelcode' => $HotelCode
                                                        );
                                                        $where['hotelcode = ?']  = $HotelCode;
                                                    $update = $sql->update('accor_descriptioncontents_descriptivecontent_mroomcapacity', $data, $where);
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();   
                                                } else {
                                                    $sql = new Sql($db);
                                                    $insert = $sql->insert();
                                                    $insert->into('accor_descriptioncontents_descriptivecontent_mroomcapacity');
                                                    $insert->values(array(
                                                        'meetingroomformatcode' => $MeetingRoomFormatCode,
                                                        'maxoccupancy' => $MaxOccupancy,
                                                        'hotelcode' => $HotelCode
                                                    ), $insert::VALUES_MERGE);
                                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                                    $results = $statement->execute();
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();
                                                }
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('accor_descriptioncontents_descriptivecontent_mroomcapacity');
                                                $insert->values(array(
                                                    'meetingroomformatcode' => $MeetingRoomFormatCode,
                                                    'maxoccupancy' => $MaxOccupancy,
                                                    'hotelcode' => $HotelCode
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                            }
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 10: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $GuestRooms = $FacilityInfo->item(0)->getElementsByTagName('GuestRooms');
                if ($GuestRooms->length > 0) {
                    $GuestRoom = $GuestRooms->item(0)->getElementsByTagName('GuestRoom');
                    if ($GuestRoom->length > 0) {
                        for ($iAux6=0; $iAux6 < $GuestRoom->length; $iAux6++) { 
                            $ID = $GuestRoom->item($iAux6)->getAttribute('ID');
                            $Code = $GuestRoom->item($iAux6)->getAttribute('Code');
                            $MaxOccupancy = $GuestRoom->item($iAux6)->getAttribute('MaxOccupancy');
                            $MaxAdultOccupancy = $GuestRoom->item($iAux6)->getAttribute('MaxAdultOccupancy');
                            $MaxChildOccupancy = $GuestRoom->item($iAux6)->getAttribute('MaxChildOccupancy');
                            $TypeRoom = $GuestRoom->item($iAux6)->getElementsByTagName('TypeRoom');
                            if ($TypeRoom->length > 0) {
                                $Name = $TypeRoom->item(0)->getAttribute('Name');
                                $RoomCategory = $TypeRoom->item(0)->getAttribute('RoomCategory');
                                $BedTypeCode = $TypeRoom->item(0)->getAttribute('BedTypeCode');
                            }

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_guestrooms');
                                $select->where(array(
                                    'guestroomid' => $ID,
                                    'code' => $Code,
                                    'name' => $Name,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'guestroomid' => $ID,
                                            'code' => $Code,
                                            'maxoccupancy' => $MaxOccupancy,
                                            'maxadultoccupancy' => $MaxAdultOccupancy,
                                            'maxchildoccupancy' => $MaxChildOccupancy,
                                            'name' => $Name,
                                            'roomcategory' => $RoomCategory,
                                            'bedtypecode' => $BedTypeCode,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_guestrooms', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_guestrooms');
                                        $insert->values(array(
                                            'guestroomid' => $ID,
                                            'code' => $Code,
                                            'maxoccupancy' => $MaxOccupancy,
                                            'maxadultoccupancy' => $MaxAdultOccupancy,
                                            'maxchildoccupancy' => $MaxChildOccupancy,
                                            'name' => $Name,
                                            'roomcategory' => $RoomCategory,
                                            'bedtypecode' => $BedTypeCode,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_guestrooms');
                                    $insert->values(array(
                                        'guestroomid' => $ID,
                                        'code' => $Code,
                                        'maxoccupancy' => $MaxOccupancy,
                                        'maxadultoccupancy' => $MaxAdultOccupancy,
                                        'maxchildoccupancy' => $MaxChildOccupancy,
                                        'name' => $Name,
                                        'roomcategory' => $RoomCategory,
                                        'bedtypecode' => $BedTypeCode,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 11: " . $e;
                                echo $return;
                            }

                            $Amenities = $GuestRoom->item($iAux6)->getElementsByTagName('Amenities');
                            if ($Amenities->length > 0) {
                                $Amenity = $Amenities->item(0)->getElementsByTagName('Amenity');
                                if ($Amenity->length > 0) {
                                    for ($iAux7=0; $iAux7 < $Amenity->length ; $iAux7++) { 
                                        $RoomAmenityCode = $Amenity->item($iAux7)->getAttribute('RoomAmenityCode');
                                        $IncludedInRateIndicator = $Amenity->item($iAux7)->getAttribute('IncludedInRateIndicator');
                                        $OperationSchedules = $Amenity->item($iAux7)->getElementsByTagName('OperationSchedules');
                                        if ($OperationSchedules->length > 0) {
                                            $OperationSchedule = $OperationSchedules->item(0)->getElementsByTagName('OperationSchedule');
                                            if ($OperationSchedule->length > 0) {
                                                $Charge = $OperationSchedule->item(0)->getElementsByTagName('Charge');
                                                if ($Charge->length > 0) {
                                                    $Type = $Charge->item(0)->getAttribute('Type');
                                                }
                                            }
                                        }

                                        try {
                                            $sql = new Sql($db);
                                            $select = $sql->select();
                                            $select->from('accor_descriptioncontents_descriptivecontent_guestrooms_amen');
                                            $select->where(array(
                                                'roomamenitycode' => $RoomAmenityCode,
                                                'type' => $Type,
                                                'hotelcode' => $HotelCode
                                            ));
                                            $statement = $sql->prepareStatementForSqlObject($select);
                                            $result = $statement->execute();
                                            $result->buffer();
                                            $customers = array();
                                            if ($result->valid()) {
                                                $data = $result->current();
                                                $id = (string) $data['hotelcode'];
                                                if ($id != "") {
                                                    $sql = new Sql($db);
                                                    $data = array(
                                                        'roomamenitycode' => $RoomAmenityCode,
                                                        'includedinrateindicator' => $IncludedInRateIndicator,
                                                        'type' => $Type,
                                                        'hotelcode' => $HotelCode
                                                        );
                                                        $where['hotelcode = ?']  = $HotelCode;
                                                    $update = $sql->update('accor_descriptioncontents_descriptivecontent_guestrooms_amen', $data, $where);
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();   
                                                } else {
                                                    $sql = new Sql($db);
                                                    $insert = $sql->insert();
                                                    $insert->into('accor_descriptioncontents_descriptivecontent_guestrooms_amen');
                                                    $insert->values(array(
                                                        'roomamenitycode' => $RoomAmenityCode,
                                                        'includedinrateindicator' => $IncludedInRateIndicator,
                                                        'type' => $Type,
                                                        'hotelcode' => $HotelCode
                                                    ), $insert::VALUES_MERGE);
                                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                                    $results = $statement->execute();
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();
                                                }
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('accor_descriptioncontents_descriptivecontent_guestrooms_amen');
                                                $insert->values(array(
                                                    'roomamenitycode' => $RoomAmenityCode,
                                                    'includedinrateindicator' => $IncludedInRateIndicator,
                                                    'type' => $Type,
                                                    'hotelcode' => $HotelCode
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                            }
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 12: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                            $Description = $GuestRoom->item($iAux6)->getElementsByTagName('Description');
                            if ($Description->length > 0) {
                                $text2 = "";
                                $Text = $Description->item(0)->getElementsByTagName('Text');
                                if ($Text->length > 0) {
                                    for ($iAux2=0; $iAux2 < $Text->length; $iAux2++) { 
                                        $Language = $Text->item($iAux2)->getAttribute('Language');
                                        $text2 = $Text->item($iAux2)->nodeValue;

                                        try {
                                            $sql = new Sql($db);
                                            $select = $sql->select();
                                            $select->from('accor_descriptioncontents_descriptivecontent_guestrooms_desc');
                                            $select->where(array(
                                                'language' => $Language,
                                                'hotelcode' => $HotelCode
                                            ));
                                            $statement = $sql->prepareStatementForSqlObject($select);
                                            $result = $statement->execute();
                                            $result->buffer();
                                            $customers = array();
                                            if ($result->valid()) {
                                                $data = $result->current();
                                                $id = (string) $data['hotelcode'];
                                                if ($id != "") {
                                                    $sql = new Sql($db);
                                                    $data = array(
                                                        'language' => $Language,
                                                        'description' => $text2,
                                                        'hotelcode' => $HotelCode
                                                        );
                                                        $where['hotelcode = ?']  = $HotelCode;
                                                    $update = $sql->update('accor_descriptioncontents_descriptivecontent_guestrooms_desc', $data, $where);
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();   
                                                } else {
                                                    $sql = new Sql($db);
                                                    $insert = $sql->insert();
                                                    $insert->into('accor_descriptioncontents_descriptivecontent_guestrooms_desc');
                                                    $insert->values(array(
                                                        'language' => $Language,
                                                        'description' => $text2,
                                                        'hotelcode' => $HotelCode
                                                    ), $insert::VALUES_MERGE);
                                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                                    $results = $statement->execute();
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();
                                                }
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('accor_descriptioncontents_descriptivecontent_guestrooms_desc');
                                                $insert->values(array(
                                                    'language' => $Language,
                                                    'description' => $text2,
                                                    'hotelcode' => $HotelCode
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                            }
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 13: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                $Restaurants = $FacilityInfo->item(0)->getElementsByTagName('Restaurants');
                if ($Restaurants->length > 0) {
                    $Quantity = $Restaurants->item(0)->getAttribute('Quantity');
                    $Restaurant = $Restaurants->item(0)->getElementsByTagName('Restaurant');
                    if ($Restaurant->length > 0) {
                        for ($iAux8=0; $iAux8 < $Restaurant->length; $iAux8++) { 
                            $ID = $Restaurant->item($iAux8)->getAttribute('ID');
                            $RestaurantName = $Restaurant->item($iAux8)->getAttribute('RestaurantName');
                            $ProximityCode = $Restaurant->item($iAux8)->getAttribute('ProximityCode');
                            $InfoCodes = $Restaurant->item($iAux8)->getElementsByTagName('InfoCodes');
                            if ($InfoCodes->length > 0) {
                                $InfoCode = $InfoCodes->item(0)->getElementsByTagName('InfoCode');
                                if ($InfoCode->length > 0) {
                                    $Code = $InfoCode->item(0)->getAttribute('Code');
                                    $Name = $InfoCode->item(0)->getAttribute('Name');
                                }
                            }

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_restaurants');
                                $select->where(array(
                                    'restaurantid' => $ID,
                                    'restaurantname' => $RestaurantName,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'restaurantid' => $ID,
                                            'restaurantname' => $RestaurantName,
                                            'proximitycode' => $ProximityCode,
                                            'infocode' => $Code,
                                            'infoname' => $Name,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_restaurants', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_restaurants');
                                        $insert->values(array(
                                            'restaurantid' => $ID,
                                            'restaurantname' => $RestaurantName,
                                            'proximitycode' => $ProximityCode,
                                            'infocode' => $Code,
                                            'infoname' => $Name,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_restaurants');
                                    $insert->values(array(
                                        'restaurantid' => $ID,
                                        'restaurantname' => $RestaurantName,
                                        'proximitycode' => $ProximityCode,
                                        'infocode' => $Code,
                                        'infoname' => $Name,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 14: " . $e;
                                echo $return;
                            }

                            $RestaurantDescription = $Restaurant->item($iAux8)->getElementsByTagName('RestaurantDescription');
                            if ($RestaurantDescription->length > 0) {
                                $text2 = "";
                                $Text = $RestaurantDescription->item(0)->getElementsByTagName('Text');
                                if ($Text->length > 0) {
                                    for ($iAux2=0; $iAux2 < $Text->length; $iAux2++) { 
                                        $Language = $Text->item($iAux2)->getAttribute('Language');
                                        $text2 = $Text->item($iAux2)->nodeValue;

                                        try {
                                            $sql = new Sql($db);
                                            $select = $sql->select();
                                            $select->from('accor_descriptioncontents_descriptivecontent_restdescription');
                                            $select->where(array(
                                                'language' => $Language,
                                                'hotelcode' => $HotelCode
                                            ));
                                            $statement = $sql->prepareStatementForSqlObject($select);
                                            $result = $statement->execute();
                                            $result->buffer();
                                            $customers = array();
                                            if ($result->valid()) {
                                                $data = $result->current();
                                                $id = (string) $data['hotelcode'];
                                                if ($id != "") {
                                                    $sql = new Sql($db);
                                                    $data = array(
                                                        'language' => $Language,
                                                        'description' => $text2,
                                                        'hotelcode' => $HotelCode
                                                        );
                                                        $where['hotelcode = ?']  = $HotelCode;
                                                    $update = $sql->update('accor_descriptioncontents_descriptivecontent_restdescription', $data, $where);
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();   
                                                } else {
                                                    $sql = new Sql($db);
                                                    $insert = $sql->insert();
                                                    $insert->into('accor_descriptioncontents_descriptivecontent_restdescription');
                                                    $insert->values(array(
                                                        'language' => $Language,
                                                        'description' => $text2,
                                                        'hotelcode' => $HotelCode
                                                    ), $insert::VALUES_MERGE);
                                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                                    $results = $statement->execute();
                                                    $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();
                                                }
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('accor_descriptioncontents_descriptivecontent_restdescription');
                                                $insert->values(array(
                                                    'language' => $Language,
                                                    'description' => $text2,
                                                    'hotelcode' => $HotelCode
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                            }
                                        } catch (\Exception $e) {
                                            echo $return;
                                            echo "Error 15: " . $e;
                                            echo $return;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            $Policies = $HotelDescriptiveContent->item($i)->getElementsByTagName('Policies');
            if ($Policies->length > 0) {
                $Policy = $Policies->item(0)->getElementsByTagName('Policy');
                if ($Policy->length > 0) {
                    for ($j=0; $j < $Policy->length; $j++) { 
                        $Start = $Policy->item($j)->getAttribute('Start');
                        $End = $Policy->item($j)->getAttribute('End');

                        try {
                            $sql = new Sql($db);
                            $select = $sql->select();
                            $select->from('accor_descriptioncontents_descriptivecontent_policy');
                            $select->where(array(
                                'language' => $Language,
                                'hotelcode' => $HotelCode
                            ));
                            $statement = $sql->prepareStatementForSqlObject($select);
                            $result = $statement->execute();
                            $result->buffer();
                            $customers = array();
                            if ($result->valid()) {
                                $data = $result->current();
                                $id = (string) $data['hotelcode'];
                                if ($id != "") {
                                    $sql = new Sql($db);
                                    $data = array(
                                        'language' => $Language,
                                        'description' => $text2,
                                        'hotelcode' => $HotelCode
                                        );
                                        $where['hotelcode = ?']  = $HotelCode;
                                    $update = $sql->update('accor_descriptioncontents_descriptivecontent_policy', $data, $where);
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();   
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_policy');
                                    $insert->values(array(
                                        'language' => $Language,
                                        'description' => $text2,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('accor_descriptioncontents_descriptivecontent_policy');
                                $insert->values(array(
                                    'language' => $Language,
                                    'description' => $text2,
                                    'hotelcode' => $HotelCode
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            }
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 16: " . $e;
                            echo $return;
                        }

                        $GuaranteePaymentPolicy = $Policy->item($j)->getElementsByTagName('GuaranteePaymentPolicy');
                        if ($GuaranteePaymentPolicy->length > 0) {
                            $GuaranteePayment = $GuaranteePaymentPolicy->item(0)->getElementsByTagName('GuaranteePayment');
                            if ($GuaranteePayment->length > 0) {
                                for ($jAux=0; $jAux < $GuaranteePayment->length; $jAux++) { 
                                    $PaymentCode = $GuaranteePayment->item($jAux)->getAttribute('PaymentCode');
                                    $GuaranteeType = $GuaranteePayment->item($jAux)->getAttribute('GuaranteeType');
                                    $HoldTime = $GuaranteePayment->item($jAux)->getAttribute('HoldTime');
                                    $Mon = $GuaranteePayment->item($jAux)->getAttribute('Mon');
                                    $Tue = $GuaranteePayment->item($jAux)->getAttribute('Tue');
                                    $Weds = $GuaranteePayment->item($jAux)->getAttribute('Weds');
                                    $Thur = $GuaranteePayment->item($jAux)->getAttribute('Thur');
                                    $Fri = $GuaranteePayment->item($jAux)->getAttribute('Fri');
                                    $Sat = $GuaranteePayment->item($jAux)->getAttribute('Sat');
                                    $Sun = $GuaranteePayment->item($jAux)->getAttribute('Sun');

                                    try {
                                        $sql = new Sql($db);
                                        $select = $sql->select();
                                        $select->from('accor_descriptioncontents_descriptivecontent_policy_guarantee');
                                        $select->where(array(
                                            'paymentcode' => $PaymentCode,
                                            'hotelcode' => $HotelCode
                                        ));
                                        $statement = $sql->prepareStatementForSqlObject($select);
                                        $result = $statement->execute();
                                        $result->buffer();
                                        $customers = array();
                                        if ($result->valid()) {
                                            $data = $result->current();
                                            $id = (string) $data['hotelcode'];
                                            if ($id != "") {
                                                $sql = new Sql($db);
                                                $data = array(
                                                    'paymentcode' => $PaymentCode,
                                                    'guaranteetype' => $GuaranteeType,
                                                    'holdtime' => $HoldTime,
                                                    'mon' => $Mon,
                                                    'tue' => $Tue,
                                                    'weds' => $Weds,
                                                    'thur' => $Thur,
                                                    'fri' => $Fri,
                                                    'sat' => $Sat,
                                                    'sun' => $Sun,
                                                    'hotelcode' => $HotelCode
                                                    );
                                                    $where['hotelcode = ?']  = $HotelCode;
                                                $update = $sql->update('accor_descriptioncontents_descriptivecontent_policy_guarantee', $data, $where);
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();   
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('accor_descriptioncontents_descriptivecontent_policy_guarantee');
                                                $insert->values(array(
                                                    'paymentcode' => $PaymentCode,
                                                    'guaranteetype' => $GuaranteeType,
                                                    'holdtime' => $HoldTime,
                                                    'mon' => $Mon,
                                                    'tue' => $Tue,
                                                    'weds' => $Weds,
                                                    'thur' => $Thur,
                                                    'fri' => $Fri,
                                                    'sat' => $Sat,
                                                    'sun' => $Sun,
                                                    'hotelcode' => $HotelCode
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                            }
                                        } else {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('accor_descriptioncontents_descriptivecontent_policy_guarantee');
                                            $insert->values(array(
                                                'paymentcode' => $PaymentCode,
                                                'guaranteetype' => $GuaranteeType,
                                                'holdtime' => $HoldTime,
                                                'mon' => $Mon,
                                                'tue' => $Tue,
                                                'weds' => $Weds,
                                                'thur' => $Thur,
                                                'fri' => $Fri,
                                                'sat' => $Sat,
                                                'sun' => $Sun,
                                                'hotelcode' => $HotelCode
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                        }
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error 17: " . $e;
                                        echo $return;
                                    }

                                    $Description = $GuaranteePayment->item($jAux)->getElementsByTagName('Description');
                                    if ($Description->length > 0) {
                                        $text2 = "";
                                        $Text = $Description->item(0)->getElementsByTagName('Text');
                                        if ($Text->length > 0) {
                                            for ($iAux2=0; $iAux2 < $Text->length; $iAux2++) { 
                                                $Language = $Text->item($iAux2)->getAttribute('Language');
                                                $text2 = $Text->item($iAux2)->nodeValue;

                                                try {
                                                    $sql = new Sql($db);
                                                    $select = $sql->select();
                                                    $select->from('accor_descriptioncontents_descriptivecontent_policy_gp_desc');
                                                    $select->where(array(
                                                        'language' => $Language,
                                                        'hotelcode' => $HotelCode
                                                    ));
                                                    $statement = $sql->prepareStatementForSqlObject($select);
                                                    $result = $statement->execute();
                                                    $result->buffer();
                                                    $customers = array();
                                                    if ($result->valid()) {
                                                        $data = $result->current();
                                                        $id = (string) $data['hotelcode'];
                                                        if ($id != "") {
                                                            $sql = new Sql($db);
                                                            $data = array(
                                                                'language' => $Language,
                                                                'description' => $text2,
                                                                'hotelcode' => $HotelCode
                                                                );
                                                                $where['hotelcode = ?']  = $HotelCode;
                                                            $update = $sql->update('accor_descriptioncontents_descriptivecontent_policy_gp_desc', $data, $where);
                                                            $db->getDriver()
                                                            ->getConnection()
                                                            ->disconnect();   
                                                        } else {
                                                            $sql = new Sql($db);
                                                            $insert = $sql->insert();
                                                            $insert->into('accor_descriptioncontents_descriptivecontent_policy_gp_desc');
                                                            $insert->values(array(
                                                                'language' => $Language,
                                                                'description' => $text2,
                                                                'hotelcode' => $HotelCode
                                                            ), $insert::VALUES_MERGE);
                                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                                            $results = $statement->execute();
                                                            $db->getDriver()
                                                            ->getConnection()
                                                            ->disconnect();
                                                        }
                                                    } else {
                                                        $sql = new Sql($db);
                                                        $insert = $sql->insert();
                                                        $insert->into('accor_descriptioncontents_descriptivecontent_policy_gp_desc');
                                                        $insert->values(array(
                                                            'language' => $Language,
                                                            'description' => $text2,
                                                            'hotelcode' => $HotelCode
                                                        ), $insert::VALUES_MERGE);
                                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                                        $results = $statement->execute();
                                                        $db->getDriver()
                                                        ->getConnection()
                                                        ->disconnect();
                                                    }
                                                } catch (\Exception $e) {
                                                    echo $return;
                                                    echo "Error 18: " . $e;
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
            $AreaInfo = $HotelDescriptiveContent->item($i)->getElementsByTagName('AreaInfo');
            if ($AreaInfo->length > 0) {
                $RefPoints = $AreaInfo->item(0)->getElementsByTagName('RefPoints');
                if ($RefPoints->length > 0) {
                    $RefPoint = $RefPoints->item(0)->getElementsByTagName('RefPoint');
                    if ($RefPoint->length > 0) {
                        for ($k=0; $k < $RefPoint->length; $k++) { 
                            $Direction = $RefPoint->item($k)->getAttribute('Direction');
                            $Distance = $RefPoint->item($k)->getAttribute('Distance');
                            $DistanceUnitName = $RefPoint->item($k)->getAttribute('DistanceUnitName');
                            $PrimaryIndicator = $RefPoint->item($k)->getAttribute('PrimaryIndicator');
                            $IndexPointCode = $RefPoint->item($k)->getAttribute('IndexPointCode');
                            $Name = $RefPoint->item($k)->getAttribute('Name');
                            $ToFrom = $RefPoint->item($k)->getAttribute('ToFrom');

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_areainfo_refpoint');
                                $select->where(array(
                                    'indexpointcode' => $IndexPointCode,
                                    'name' => $Name,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'direction' => $Direction,
                                            'distance' => $Distance,
                                            'distanceunitname' => $DistanceUnitName,
                                            'primaryindicator' => $PrimaryIndicator,
                                            'indexpointcode' => $IndexPointCode,
                                            'name' => $Name,
                                            'tofrom' => $ToFrom,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_areainfo_refpoint', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_areainfo_refpoint');
                                        $insert->values(array(
                                            'direction' => $Direction,
                                            'distance' => $Distance,
                                            'distanceunitname' => $DistanceUnitName,
                                            'primaryindicator' => $PrimaryIndicator,
                                            'indexpointcode' => $IndexPointCode,
                                            'name' => $Name,
                                            'tofrom' => $ToFrom,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_areainfo_refpoint');
                                    $insert->values(array(
                                        'direction' => $Direction,
                                        'distance' => $Distance,
                                        'distanceunitname' => $DistanceUnitName,
                                        'primaryindicator' => $PrimaryIndicator,
                                        'indexpointcode' => $IndexPointCode,
                                        'name' => $Name,
                                        'tofrom' => $ToFrom,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 19: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
                $Attractions = $AreaInfo->item(0)->getElementsByTagName('Attractions');
                if ($Attractions->length > 0) {
                    $Attraction = $Attractions->item(0)->getElementsByTagName('Attraction');
                    if ($Attraction->length > 0) {
                        for ($kAux=0; $kAux < $Attraction->length; $kAux++) { 
                            $ID = $Attraction->item($kAux)->getAttribute('ID');
                            $AttractionCategoryCode = $Attraction->item($kAux)->getAttribute('AttractionCategoryCode');
                            $AttractionName = $Attraction->item($kAux)->getAttribute('AttractionName');
                            $RefPoints = $Attraction->item($kAux)->getElementsByTagName('RefPoints');
                            if ($RefPoints->length > 0) {
                                $RefPoint = $RefPoints->item(0)->getElementsByTagName('RefPoint');
                                if ($RefPoint->length > 0) {
                                    $Direction = $RefPoint->item(0)->getAttribute('Direction');
                                    $Distance = $RefPoint->item(0)->getAttribute('Distance');
                                    $DistanceUnitName = $RefPoint->item(0)->getAttribute('DistanceUnitName');
                                    $IndexPointCode = $RefPoint->item(0)->getAttribute('IndexPointCode');
                                    $ToFrom = $RefPoint->item(0)->getAttribute('ToFrom');
                                }
                            }

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_areainfo_attrac');
                                $select->where(array(
                                    'attractionid' => $ID,
                                    'attractioncategorycode' => $AttractionCategoryCode,
                                    'attractionname' => $AttractionName,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'attractionid' => $ID,
                                            'attractioncategorycode' => $AttractionCategoryCode,
                                            'attractionname' => $AttractionName,
                                            'direction' => $Direction,
                                            'distance' => $Distance,
                                            'distanceunitname' => $DistanceUnitName,
                                            'indexpointcode' => $IndexPointCode,
                                            'tofrom' => $ToFrom,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_areainfo_attrac', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_areainfo_attrac');
                                        $insert->values(array(
                                            'attractionid' => $ID,
                                            'attractioncategorycode' => $AttractionCategoryCode,
                                            'attractionname' => $AttractionName,
                                            'direction' => $Direction,
                                            'distance' => $Distance,
                                            'distanceunitname' => $DistanceUnitName,
                                            'indexpointcode' => $IndexPointCode,
                                            'tofrom' => $ToFrom,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_areainfo_attrac');
                                    $insert->values(array(
                                        'attractionid' => $ID,
                                        'attractioncategorycode' => $AttractionCategoryCode,
                                        'attractionname' => $AttractionName,
                                        'direction' => $Direction,
                                        'distance' => $Distance,
                                        'distanceunitname' => $DistanceUnitName,
                                        'indexpointcode' => $IndexPointCode,
                                        'tofrom' => $ToFrom,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 20: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
                $Recreations = $AreaInfo->item(0)->getElementsByTagName('Recreations');
                if ($Recreations->length > 0) {
                    $Recreation = $Recreations->item(0)->getElementsByTagName('Recreation');
                    if ($Recreation->length > 0) {
                        for ($kAux2=0; $kAux2 < $Recreation->length; $kAux2++) { 
                            $Code = $Recreation->item($kAux2)->getAttribute('Code');
                            $ProximityCode = $Recreation->item($kAux2)->getAttribute('ProximityCode');
                            $Included = $Recreation->item($kAux2)->getAttribute('Included');

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_areainfo_recrea');
                                $select->where(array(
                                    'code' => $Code,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'code' => $Code,
                                            'proximitycode' => $ProximityCode,
                                            'included' => $Included,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_areainfo_recrea', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_areainfo_recrea');
                                        $insert->values(array(
                                            'code' => $Code,
                                            'proximitycode' => $ProximityCode,
                                            'included' => $Included,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_areainfo_recrea');
                                    $insert->values(array(
                                        'code' => $Code,
                                        'proximitycode' => $ProximityCode,
                                        'included' => $Included,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 21: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
            }
            $AffiliationInfo = $HotelDescriptiveContent->item($i)->getElementsByTagName('AffiliationInfo');
            if ($AffiliationInfo->length > 0) {
                $Awards = $AffiliationInfo->item(0)->getElementsByTagName('Awards');
                if ($Awards->length > 0) {
                    $Award = $Awards->item(0)->getElementsByTagName('Award');
                    if ($Award->length > 0) {
                        for ($w=0; $w < $Award->length; $w++) { 
                            $Provider = $Award->item($w)->getAttribute('Provider');
                            $Rating = $Award->item($w)->getAttribute('Rating');

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_affiliationinfo');
                                $select->where(array(
                                    'provider' => $Provider,
                                    'rating' => $Rating,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'provider' => $Provider,
                                            'rating' => $Rating,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_affiliationinfo', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_affiliationinfo');
                                        $insert->values(array(
                                            'provider' => $Provider,
                                            'rating' => $Rating,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_affiliationinfo');
                                    $insert->values(array(
                                        'provider' => $Provider,
                                        'rating' => $Rating,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 22: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
            }
            $ContactInfos = $HotelDescriptiveContent->item($i)->getElementsByTagName('ContactInfos');
            if ($ContactInfos->length > 0) {
                $ContactInfo = $ContactInfos->item(0)->getElementsByTagName('ContactInfo');
                if ($ContactInfo->length > 0) {
                    for ($x=0; $x < $ContactInfo->length; $x++) { 
                        $ContactProfileType = $ContactInfo->item($x)->getAttribute('ContactProfileType');
                        $Location = $ContactInfo->item($x)->getAttribute('Location');
                        $Addresses = $ContactInfo->item($x)->getElementsByTagName('Addresses');
                        if ($Addresses->length > 0) {
                            $Address = $Addresses->item(0)->getElementsByTagName('Address');
                            if ($Address->length > 0) {
                                $UseType = $Address->item(0)->getAttribute('UseType');
                                $AddressLine = $Address->item(0)->getElementsByTagName('AddressLine');
                                if ($AddressLine->length > 0) {
                                    $AddressLine = $AddressLine->item(0)->nodeValue;
                                } else {
                                    $AddressLine = "";
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
                                $CountryName = $Address->item(0)->getElementsByTagName('CountryName');
                                if ($CountryName->length > 0) {
                                    $Code = $CountryName->item(0)->getAttribute('Code');
                                    $CountryName = $CountryName->item(0)->nodeValue;
                                } else {
                                    $CountryName = "";
                                }
                            }
                        }
                        $Emails = $ContactInfo->item($x)->getElementsByTagName('Emails');
                        if ($Emails->length > 0) {
                            $Email = $Emails->item(0)->getElementsByTagName('Email');
                            if ($Email->length > 0) {
                                $EmailType = $Email->item(0)->getAttribute('EmailType');
                                $Email = $Email->item(0)->nodeValue;
                            } else {
                                $Email = "";
                            }
                        }
                        $URLs = $ContactInfo->item($x)->getElementsByTagName('URLs');
                        if ($URLs->length > 0) {
                            $URL = $URLs->item(0)->getElementsByTagName('URL');
                            if ($URL->length > 0) {
                                $URL = $URL->item(0)->nodeValue;
                            } else {
                                $URL = "";
                            }
                        }

                        try {
                            $sql = new Sql($db);
                            $select = $sql->select();
                            $select->from('accor_descriptioncontents_descriptivecontent_contactinfos');
                            $select->where(array(
                                'contactprofiletype' => $ContactProfileType,
                                'location' => $Location,
                                'usetype' => $UseType,
                                'addressline' => $AddressLine,
                                'cityname' => $CityName,
                                'postalcode' => $PostalCode,
                                'countryname' => $CountryName,
                                'emailtype' => $EmailType,
                                'email' => $Email,
                                'url' => $URL,
                                'hotelcode' => $HotelCode
                            ));
                            $statement = $sql->prepareStatementForSqlObject($select);
                            $result = $statement->execute();
                            $result->buffer();
                            $customers = array();
                            if ($result->valid()) {
                                $data = $result->current();
                                $id = (string) $data['hotelcode'];
                                if ($id != "") {
                                    $sql = new Sql($db);
                                    $data = array(
                                        'contactprofiletype' => $ContactProfileType,
                                        'location' => $Location,
                                        'usetype' => $UseType,
                                        'addressline' => $AddressLine,
                                        'cityname' => $CityName,
                                        'postalcode' => $PostalCode,
                                        'countryname' => $CountryName,
                                        'emailtype' => $EmailType,
                                        'email' => $Email,
                                        'url' => $URL,
                                        'hotelcode' => $HotelCode
                                        );
                                        $where['hotelcode = ?']  = $HotelCode;
                                    $update = $sql->update('accor_descriptioncontents_descriptivecontent_contactinfos', $data, $where);
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();   
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_contactinfos');
                                    $insert->values(array(
                                        'contactprofiletype' => $ContactProfileType,
                                        'location' => $Location,
                                        'usetype' => $UseType,
                                        'addressline' => $AddressLine,
                                        'cityname' => $CityName,
                                        'postalcode' => $PostalCode,
                                        'countryname' => $CountryName,
                                        'emailtype' => $EmailType,
                                        'email' => $Email,
                                        'url' => $URL,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('accor_descriptioncontents_descriptivecontent_contactinfos');
                                $insert->values(array(
                                    'contactprofiletype' => $ContactProfileType,
                                    'location' => $Location,
                                    'usetype' => $UseType,
                                    'addressline' => $AddressLine,
                                    'cityname' => $CityName,
                                    'postalcode' => $PostalCode,
                                    'countryname' => $CountryName,
                                    'emailtype' => $EmailType,
                                    'email' => $Email,
                                    'url' => $URL,
                                    'hotelcode' => $HotelCode
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                            }
                        } catch (\Exception $e) {
                            echo $return;
                            echo "Error 23: " . $e;
                            echo $return;
                        }

                        $Phones = $ContactInfo->item($x)->getElementsByTagName('Phones');
                        if ($Phones->length > 0) {
                            $Phone = $Phones->item(0)->getElementsByTagName('Phone');
                            if ($Phone->length > 0) {
                                for ($xAux=0; $xAux < $Phone->length; $xAux++) { 
                                    $PhoneTechType = $Phone->item($xAux)->getAttribute('PhoneTechType');
                                    $PhoneNumber = $Phone->item($xAux)->getAttribute('PhoneNumber');
                                    $PhoneLocationType = $Phone->item($xAux)->getAttribute('PhoneLocationType');
                                    $CountryAccessCode = $Phone->item($xAux)->getAttribute('CountryAccessCode');

                                    try {
                                        $sql = new Sql($db);
                                        $select = $sql->select();
                                        $select->from('accor_descriptioncontents_descriptivecontent_cinfos_phone');
                                        $select->where(array(
                                            'phonelocationtype' => $PhoneLocationType,
                                            'countryaccesscode' => $CountryAccessCode,
                                            'hotelcode' => $HotelCode
                                        ));
                                        $statement = $sql->prepareStatementForSqlObject($select);
                                        $result = $statement->execute();
                                        $result->buffer();
                                        $customers = array();
                                        if ($result->valid()) {
                                            $data = $result->current();
                                            $id = (string) $data['hotelcode'];
                                            if ($id != "") {
                                                $sql = new Sql($db);
                                                $data = array(
                                                    'phonetechtype' => $PhoneTechType,
                                                    'phonenumber' => $PhoneNumber,
                                                    'phonelocationtype' => $PhoneLocationType,
                                                    'countryaccesscode' => $CountryAccessCode,
                                                    'hotelcode' => $HotelCode
                                                    );
                                                    $where['hotelcode = ?']  = $HotelCode;
                                                $update = $sql->update('accor_descriptioncontents_descriptivecontent_cinfos_phone', $data, $where);
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();   
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('accor_descriptioncontents_descriptivecontent_cinfos_phone');
                                                $insert->values(array(
                                                    'phonetechtype' => $PhoneTechType,
                                                    'phonenumber' => $PhoneNumber,
                                                    'phonelocationtype' => $PhoneLocationType,
                                                    'countryaccesscode' => $CountryAccessCode,
                                                    'hotelcode' => $HotelCode
                                                ), $insert::VALUES_MERGE);
                                                $statement = $sql->prepareStatementForSqlObject($insert);
                                                $results = $statement->execute();
                                                $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                            }
                                        } else {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('accor_descriptioncontents_descriptivecontent_cinfos_phone');
                                            $insert->values(array(
                                                'phonetechtype' => $PhoneTechType,
                                                'phonenumber' => $PhoneNumber,
                                                'phonelocationtype' => $PhoneLocationType,
                                                'countryaccesscode' => $CountryAccessCode,
                                                'hotelcode' => $HotelCode
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                        }
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "Error 24: " . $e;
                                        echo $return;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $TPA_Extensions = $HotelDescriptiveContent->item($i)->getElementsByTagName('TPA_Extensions');
            if ($TPA_Extensions->length > 0) {
                $Labels = $TPA_Extensions->item(0)->getElementsByTagName('Labels');
                if ($Labels->length > 0) {
                    $Label = $Labels->item(0)->getElementsByTagName('Label');
                    if ($Label->length > 0) {
                        $Labelcode = $Label->item(0)->getAttribute('code');
                    }
                }
                $BreakfastPrices = $TPA_Extensions->item(0)->getElementsByTagName('BreakfastPrices');
                if ($BreakfastPrices->length > 0) {
                    $BreakfastPrice = $BreakfastPrices->item(0)->getElementsByTagName('BreakfastPrice');
                    if ($BreakfastPrice->length > 0) {
                        $amount = $BreakfastPrice->item(0)->getAttribute('amount');
                        $currencyCode = $BreakfastPrice->item(0)->getAttribute('currencyCode');
                    }
                }

                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('accor_descriptioncontents_descriptivecontent_tpaextensions');
                    $select->where(array(
                        'labelcode' => $Labelcode,
                        'hotelcode' => $HotelCode
                    ));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $result->buffer();
                    $customers = array();
                    if ($result->valid()) {
                        $data = $result->current();
                        $id = (string) $data['hotelcode'];
                        if ($id != "") {
                            $sql = new Sql($db);
                            $data = array(
                                'labelcode' => $Labelcode,
                                'amount' => $amount,
                                'currencycode' => $currencyCode,
                                'hotelcode' => $HotelCode
                                );
                                $where['hotelcode = ?']  = $HotelCode;
                            $update = $sql->update('accor_descriptioncontents_descriptivecontent_tpaextensions', $data, $where);
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();   
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('accor_descriptioncontents_descriptivecontent_tpaextensions');
                            $insert->values(array(
                                'labelcode' => $Labelcode,
                                'amount' => $amount,
                                'currencycode' => $currencyCode,
                                'hotelcode' => $HotelCode
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                        }
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('accor_descriptioncontents_descriptivecontent_tpaextensions');
                        $insert->values(array(
                            'labelcode' => $Labelcode,
                            'amount' => $amount,
                            'currencycode' => $currencyCode,
                            'hotelcode' => $HotelCode
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                    }
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 25: " . $e;
                    echo $return;
                }

                $BaseByGuestAmts = $TPA_Extensions->item(0)->getElementsByTagName('BaseByGuestAmts');
                if ($BaseByGuestAmts->length > 0) {
                    $BaseByGuestAmt = $BaseByGuestAmts->item(0)->getElementsByTagName('BaseByGuestAmt');
                    if ($BaseByGuestAmt->length > 0) {
                        for ($y=0; $y < $BaseByGuestAmt->length; $y++) { 
                            $amount = $BaseByGuestAmt->item($y)->getAttribute('amount');
                            $CurrencyCode = $BaseByGuestAmt->item($y)->getAttribute('CurrencyCode');
                            $AmountBeforeTax = $BaseByGuestAmt->item($y)->getAttribute('AmountBeforeTax');
                            $MaximumAmountBeforeTax = $BaseByGuestAmt->item($y)->getAttribute('MaximumAmountBeforeTax');

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_tpa_basebyguest');
                                $select->where(array(
                                    'currencycode' => $CurrencyCode,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'amount' => $amount,
                                            'currencycode' => $CurrencyCode,
                                            'amountbeforetax' => $AmountBeforeTax,
                                            'maximumamountbeforetax' => $MaximumAmountBeforeTax,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_tpa_basebyguest', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_tpa_basebyguest');
                                        $insert->values(array(
                                            'amount' => $amount,
                                            'currencycode' => $CurrencyCode,
                                            'amountbeforetax' => $AmountBeforeTax,
                                            'maximumamountbeforetax' => $MaximumAmountBeforeTax,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_tpa_basebyguest');
                                    $insert->values(array(
                                        'amount' => $amount,
                                        'currencycode' => $CurrencyCode,
                                        'amountbeforetax' => $AmountBeforeTax,
                                        'maximumamountbeforetax' => $MaximumAmountBeforeTax,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 27: " . $e;
                                echo $return;
                            }
                        }
                    }
                }
            }
            $GDS_Info = $HotelDescriptiveContent->item($i)->getElementsByTagName('GDS_Info');
            if ($GDS_Info->length > 0) {
                $GDS_Codes = $GDS_Info->item(0)->getElementsByTagName('GDS_Codes');
                if ($GDS_Codes->length > 0) {
                    $GDS_Code = $GDS_Codes->item(0)->getElementsByTagName('GDS_Code');
                    if ($GDS_Code->length > 0) {
                        for ($z=0; $z < $GDS_Code->length; $z++) { 
                            $ChainCode = $GDS_Code->item($z)->getAttribute('ChainCode');
                            $GDS_PropertyCode = $GDS_Code->item($z)->getAttribute('GDS_PropertyCode');
                            $GDS_Name = $GDS_Code->item($z)->getAttribute('GDS_Name');

                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('accor_descriptioncontents_descriptivecontent_gdsinfo');
                                $select->where(array(
                                    'gds_propertycode' => $GDS_PropertyCode,
                                    'gds_name' => $GDS_Name,
                                    'hotelcode' => $HotelCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string) $data['hotelcode'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'chaincode' => $ChainCode,
                                            'gds_propertycode' => $GDS_PropertyCode,
                                            'gds_name' => $GDS_Name,
                                            'hotelcode' => $HotelCode
                                            );
                                            $where['hotelcode = ?']  = $HotelCode;
                                        $update = $sql->update('accor_descriptioncontents_descriptivecontent_gdsinfo', $data, $where);
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();   
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('accor_descriptioncontents_descriptivecontent_gdsinfo');
                                        $insert->values(array(
                                            'chaincode' => $ChainCode,
                                            'gds_propertycode' => $GDS_PropertyCode,
                                            'gds_name' => $GDS_Name,
                                            'hotelcode' => $HotelCode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                    }
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('accor_descriptioncontents_descriptivecontent_gdsinfo');
                                    $insert->values(array(
                                        'chaincode' => $ChainCode,
                                        'gds_propertycode' => $GDS_PropertyCode,
                                        'gds_name' => $GDS_Name,
                                        'hotelcode' => $HotelCode
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "Error 28: " . $e;
                                echo $return;
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
