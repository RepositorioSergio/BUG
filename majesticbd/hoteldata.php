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
$sql = "select value from settings where name='enablemajesticusa' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_majestic = $affiliate_id;
} else {
    $affiliate_id_majestic = 0;
}
$sql = "select value from settings where name='majesticusaLoginEmail' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='majesticusaPassword' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='majesticusaMarkup' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaMarkup = (double) $row_settings['value'];
} else {
    $majesticusaMarkup = 0;
}
$sql = "select value from settings where name='majesticusaServiceURL' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaServiceURL = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$count = 0;
$sql = "SELECT id FROM hoteis";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}


$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $hotel_id = $row->id;
        echo $return;
        echo $hotel_id;
        echo $return;

        $raw = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
        xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" 
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <SOAP-ENV:Header>
            <m:AuthHeader xmlns:m="http://www.majesticusa.com/majesticweb_xml/">
                <m:Username>' . $majesticusaLoginEmail . '</m:Username>
                <m:Password>' . $majesticusaPassword . '</m:Password>
            </m:AuthHeader>
        </SOAP-ENV:Header>
        <SOAP-ENV:Body>
            <m:HotelData xmlns:m="http://www.majesticusa.com/majesticweb_xml/">
                <m:Id>' . $hotel_id . '</m:Id>
                <m:HideRate>0</m:HideRate>
            </m:HotelData>
        </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>';
        echo $raw;
        $client = new Client();
        $client->setOptions(array(
            'timeout' => 100,
            'sslverifypeer' => false,
            'sslverifyhost' => false
        ));
        $client->setHeaders(array(
            'Accept-Encoding' => 'gzip,deflate',
            'X-Powered-By' => 'Zend Framework',
            'Content-Length' => strlen($raw),
            'Content-Type' => 'text/xml'
        ));
        $client->setUri($majesticusaServiceURL);
        $client->setMethod('POST');
        $client->setRawBody($raw);
        $response = $client->send();
        if ($response->isSuccess()) {
        $response = $response->getBody();
        } else {
        $logger = new Logger();
        $writer = new Writer\Stream('/srv/www/htdocs/error_log');
        $logger->addWriter($writer);
        $logger->info($client->getUri());
        $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
        echo $return;
        echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
        echo $return;
        die();
        }
        echo "RESPONSE";
        /* echo $return;
        echo $response;
        echo $return; */
        echo '<xmp>';
        var_dump($response);
        echo '</xmp>';
        $config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
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
        $Body = $Envelope->item(0)->getElementsByTagName("Body");
        $HotelDataResponse = $Body->item(0)->getElementsByTagName("HotelDataResponse");
        $HotelDataResult = $HotelDataResponse->item(0)->getElementsByTagName("HotelDataResult");
        $Root = $HotelDataResult->item(0)->getElementsByTagName("Root");
        $node = $Root->item(0)->getElementsByTagName("Hotel");
        echo $return;
        echo "TAM: " . $node->length;
        echo $return;
        for ($i = 0; $i < $node->length; $i++) {
            echo $return;
            echo $i;
            echo $return;
            $Id = $node->item($i)->getElementsByTagName("Id");
            if ($Id->length > 0) {
                $Id = $Id->item(0)->nodeValue;
            } else {
                $Id = "";
            }
            echo $return;
            echo $Id;
            echo $return;
            $Name = $node->item($i)->getElementsByTagName("Name");
            if ($Name->length > 0) {
                $Name = $Name->item(0)->nodeValue;
            } else {
                $Name = "";
            }
            //Address
            $Address = $node->item($i)->getElementsByTagName("Address");
            if ($Address->length > 0) {
                $Country = $Address->item(0)->getElementsByTagName("Country");
                if ($Country->length > 0) {
                    $Country = $Country->item(0)->nodeValue;
                } else {
                    $Country = "";
                }
                $State = $Address->item(0)->getElementsByTagName("State");
                if ($State->length > 0) {
                    $State = $State->item(0)->nodeValue;
                } else {
                    $State = "";
                }
                $City = $Address->item(0)->getElementsByTagName("City");
                if ($City->length > 0) {
                    $City = $City->item(0)->nodeValue;
                } else {
                    $City = "";
                }
                $Zone = $Address->item(0)->getElementsByTagName("Zone");
                if ($Zone->length > 0) {
                    $Zone = $Zone->item(0)->nodeValue;
                } else {
                    $Zone = "";
                }
                $Address2 = $Address->item(0)->getElementsByTagName("Address");
                if ($Address2->length > 0) {
                    $Address2 = $Address2->item(0)->nodeValue;
                } else {
                    $Address2 = "";
                }
                $ZipCode = $Address->item(0)->getElementsByTagName("ZipCode");
                if ($ZipCode->length > 0) {
                    $ZipCode = $ZipCode->item(0)->nodeValue;
                } else {
                    $ZipCode = "";
                }
            }
            //hoteltypeid
            $hoteltypeid = $node->item($i)->getElementsByTagName("hoteltypeid");
            if ($hoteltypeid->length > 0) {
                $hoteltypeid = $hoteltypeid->item(0)->nodeValue;
            } else {
                $hoteltypeid = "";
            }
            $Phone = $node->item($i)->getElementsByTagName("Phone");
            if ($Phone->length > 0) {
                $Phone = $Phone->item(0)->nodeValue;
            } else {
                $Phone = "";
            }
            $Fax = $node->item($i)->getElementsByTagName("Fax");
            if ($Fax->length > 0) {
                $Fax = $Fax->item(0)->nodeValue;
            } else {
                $Fax = "";
            }
            $CheckIN = $node->item($i)->getElementsByTagName("CheckIN");
            if ($CheckIN->length > 0) {
                $CheckIN = $CheckIN->item(0)->nodeValue;
            } else {
                $CheckIN = "";
            }
            $CheckOUT = $node->item($i)->getElementsByTagName("CheckOUT");
            if ($CheckOUT->length > 0) {
                $CheckOUT = $CheckOUT->item(0)->nodeValue;
            } else {
                $CheckOUT = "";
            }
            $Notes = $node->item($i)->getElementsByTagName("Notes");
            if ($Notes->length > 0) {
                $Notes = $Notes->item(0)->nodeValue;
            } else {
                $Notes = "";
            }
            $Category = $node->item($i)->getElementsByTagName("Category");
            if ($Category->length > 0) {
                $Category = $Category->item(0)->nodeValue;
            } else {
                $Category = "";
            }
            //resortfee
            $resortfee = $node->item($i)->getElementsByTagName("resortfee");
            if ($resortfee->length > 0) {
                $fee = $resortfee->item(0)->getElementsByTagName("fee");
                if ($fee->length > 0) {
                    $fee = $fee->item(0)->nodeValue;
                } else {
                    $fee = "";
                }
                $description = $resortfee->item(0)->getElementsByTagName("description");
                if ($description->length > 0) {
                    $description = $description->item(0)->nodeValue;
                } else {
                    $description = "";
                }
            }

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('hoteldata');
                $select->where(array(
                    'Id' => $Id
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (int)$data['Id'];
                    if ($id > 0) {
                        $sql = new Sql($db);
                        $data = array(
                            'Id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'Name' => $Name,
                            'Country' => $Country,
                            'State' => $State,
                            'City' => $City,
                            'Zone' => $Zone,
                            'Address' => $Address2,
                            'ZipCode' => $ZipCode,
                            'hoteltypeid' => $hoteltypeid,
                            'Phone' => $Phone,
                            'Fax' => $Fax,
                            'CheckIN' => $CheckIN,
                            'CheckOUT' => $CheckOUT,
                            'Notes' => $Notes,
                            'Category' => $Category,
                            'fee' => $fee,
                            'description' => $description
                        );
                        $where['Id = ?'] = $Id;
                        $update = $sql->update('hoteldata', $data, $where);
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hoteldata');
                        $insert->values(array(
                            'Id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Name' => $Name,
                            'Country' => $Country,
                            'State' => $State,
                            'City' => $City,
                            'Zone' => $Zone,
                            'Address' => $Address2,
                            'ZipCode' => $ZipCode,
                            'hoteltypeid' => $hoteltypeid,
                            'Phone' => $Phone,
                            'Fax' => $Fax,
                            'CheckIN' => $CheckIN,
                            'CheckOUT' => $CheckOUT,
                            'Notes' => $Notes,
                            'Category' => $Category,
                            'fee' => $fee,
                            'description' => $description
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
                    $insert->into('hoteldata');
                    $insert->values(array(
                        'Id' => $Id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Name' => $Name,
                        'Country' => $Country,
                        'State' => $State,
                        'City' => $City,
                        'Zone' => $Zone,
                        'Address' => $Address2,
                        'ZipCode' => $ZipCode,
                        'hoteltypeid' => $hoteltypeid,
                        'Phone' => $Phone,
                        'Fax' => $Fax,
                        'CheckIN' => $CheckIN,
                        'CheckOUT' => $CheckOUT,
                        'Notes' => $Notes,
                        'Category' => $Category,
                        'fee' => $fee,
                        'description' => $description
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                }
            } catch (Exception $ex) {
                echo $return;
                echo "ERRO: " . $ex;
                echo $return;
            }

            $Images = $node->item($i)->getElementsByTagName("Images");
            if ($Images->length > 0) {
                for ($j=0; $j < $Images->length; $j++) { 
                    $Image = $Images->item($j)->getElementsByTagName("Image");
                    if ($Image->length > 0) {
                        $Image = $Image->item(0)->nodeValue;
                    } else {
                        $Image = "";
                    }
                    $Thumbnail = $Images->item($j)->getElementsByTagName("Thumbnail");
                    if ($Thumbnail->length > 0) {
                        $Thumbnail = $Thumbnail->item(0)->nodeValue;
                    } else {
                        $Thumbnail = "";
                    }
                    $Description = $Images->item($j)->getElementsByTagName("Description");
                    if ($Description->length > 0) {
                        $Description = $Description->item(0)->nodeValue;
                    } else {
                        $Description = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hoteldata_images');
                        $insert->values(array(
                            'Id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Image' => $Image,
                            'Thumbnail' => $Thumbnail,
                            'Description' => $Description
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (Exception $ex) {
                        echo $return;
                        echo "ERRO: " . $ex;
                        echo $return;
                    }

                }
            }

            //SpecialNotes
            $SpecialNotes = $node->item($i)->getElementsByTagName("SpecialNotes");
            if ($SpecialNotes->length > 0) {
                for ($s=0; $s < $SpecialNotes->length; $s++) { 
                    $Note = $SpecialNotes->item($s)->getElementsByTagName("Note");
                    if ($Note->length > 0) {
                        $Note = $Note->item(0)->nodeValue;
                    } else {
                        $Note = "";
                    }
                    $From = $SpecialNotes->item($s)->getElementsByTagName("From");
                    if ($From->length > 0) {
                        $From = $From->item(0)->nodeValue;
                    } else {
                        $From = "";
                    }
                    $To = $SpecialNotes->item($s)->getElementsByTagName("To");
                    if ($To->length > 0) {
                        $To = $To->item(0)->nodeValue;
                    } else {
                        $To = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hoteldata_specialnotes');
                        $insert->values(array(
                            'Id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'Note' => $Note,
                            'FromS' => $From,
                            'ToS' => $To
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (Exception $ex) {
                        echo $return;
                        echo "ERRO: " . $ex;
                        echo $return;
                    }
                }
            }


            //RoomTypes
            $RoomTypes = $node->item($i)->getElementsByTagName("RoomTypes");
            if ($RoomTypes->length > 0) {
                for ($k=0; $k < $RoomTypes->length; $k++) { 
                    $RoomType = $RoomTypes->item($k)->getElementsByTagName("RoomType");
                    if ($RoomType->length > 0) {
                        $RoomType = $RoomType->item(0)->nodeValue;
                    } else {
                        $RoomType = "";
                    }

                    try {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('hoteldata_roomtypes');
                        $insert->values(array(
                            'Id' => $Id,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'RoomType' => $RoomType
                        ), $insert::VALUES_MERGE);
                        $statement = $sql->prepareStatementForSqlObject($insert);
                        $results = $statement->execute();
                        $db->getDriver()
                            ->getConnection()
                            ->disconnect();
                    } catch (Exception $ex) {
                        echo $return;
                        echo "ERRO: " . $ex;
                        echo $return;
                    }

                }
            }

            //Rates
            $Rates = $node->item($i)->getElementsByTagName("Rates");
            if ($Rates->length > 0) {
                $RoomTypeR = $Rates->item(0)->getElementsByTagName("RoomType");
                if ($RoomTypeR->length > 0) {
                    $RoomTypeR = $RoomTypeR->item(0)->nodeValue;
                } else {
                    $RoomTypeR = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('rates_roomtype');
                    $insert->values(array(
                        'Id' => $Id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'RoomType' => $RoomTypeR,
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (Exception $ex) {
                    echo $return;
                    echo "ERRO: " . $ex;
                    echo $return;
                }

                $Allotment = $Rates->item(0)->getElementsByTagName("Allotment");
                if ($Allotment->length > 0) {
                    for ($al=0; $al < $Allotment->length; $al++) { 
                        $FromA = $Allotment->item($al)->getElementsByTagName("From");
                        if ($FromA->length > 0) {
                            $FromA = $FromA->item(0)->nodeValue;
                        } else {
                            $FromA = "";
                        }
                        $ToA = $Allotment->item($al)->getElementsByTagName("To");
                        if ($ToA->length > 0) {
                            $ToA = $ToA->item(0)->nodeValue;
                        } else {
                            $ToA = "";
                        }
                        $Remarks = $Allotment->item($al)->getElementsByTagName("Remarks");
                        if ($Remarks->length > 0) {
                            $Remarks = $Remarks->item(0)->nodeValue;
                        } else {
                            $Remarks = "";
                        }
                        $MinStay = $Allotment->item($al)->getElementsByTagName("MinStay");
                        if ($MinStay->length > 0) {
                            $MinStay = $MinStay->item(0)->nodeValue;
                        } else {
                            $MinStay = "";
                        }
                        $MaxStay = $Allotment->item($al)->getElementsByTagName("MaxStay");
                        if ($MaxStay->length > 0) {
                            $MaxStay = $MaxStay->item(0)->nodeValue;
                        } else {
                            $MaxStay = "";
                        }

                        //NtFreePolicy
                        $NtFreePolicy = $Allotment->item($al)->getElementsByTagName("NtFreePolicy");
                        if ($NtFreePolicy->length > 0) {
                            $ntsfree = $NtFreePolicy->item(0)->getElementsByTagName("ntsfree");
                            if ($ntsfree->length > 0) {
                                $ntsfree = $ntsfree->item(0)->nodeValue;
                            } else {
                                $ntsfree = "";
                            }
                            $freeafter = $NtFreePolicy->item(0)->getElementsByTagName("freeafter");
                            if ($freeafter->length > 0) {
                                $freeafter = $freeafter->item(0)->nodeValue;
                            } else {
                                $freeafter = "";
                            }
                            $repeats = $NtFreePolicy->item(0)->getElementsByTagName("repeats");
                            if ($repeats->length > 0) {
                                $repeats = $repeats->item(0)->nodeValue;
                            } else {
                                $repeats = "";
                            }
                    
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('rates_allotment');
                            $insert->values(array(
                                'Id' => $Id,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'RoomType' => $RoomTypeR,
                                'FromA' => $FromA,
                                'ToA' => $ToA,
                                'Remarks' => $Remarks,
                                'MinStay' => $MinStay,
                                'MaxStay' => $MaxStay,
                                'ntsfree' => $ntsfree,
                                'freeafter' => $freeafter,
                                'repeats' => $repeats
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (Exception $ex) {
                            echo $return;
                            echo "ERRO: " . $ex;
                            echo $return;
                        }


                        //Mealplan
                        $Mealplan = $Allotment->item($al)->getElementsByTagName("Mealplan");
                        if ($Mealplan->length > 0) {
                            $Meal = $Mealplan->item(0)->getElementsByTagName("Meal");
                            if ($Meal->length > 0) {
                                for ($m=0; $m < $Meal->length; $m++) { 
                                    $IdM = $Meal->item($m)->getElementsByTagName("Id");
                                    if ($IdM->length > 0) {
                                        $IdM = $IdM->item(0)->nodeValue;
                                    } else {
                                        $IdM = "";
                                    }
                                    $CodeM = $Meal->item($m)->getElementsByTagName("Code");
                                    if ($CodeM->length > 0) {
                                        $CodeM = $CodeM->item(0)->nodeValue;
                                    } else {
                                        $CodeM = "";
                                    }
                                    $NameM = $Meal->item($m)->getElementsByTagName("Name");
                                    if ($NameM->length > 0) {
                                        $NameM = $NameM->item(0)->nodeValue;
                                    } else {
                                        $NameM = "";
                                    }
                                    $AgeType = $Meal->item($m)->getElementsByTagName("AgeType");
                                    if ($AgeType->length > 0) {
                                        $AgeType = $AgeType->item(0)->nodeValue;
                                    } else {
                                        $AgeType = "";
                                    }
                                    $GroupName = $Meal->item($m)->getElementsByTagName("GroupName");
                                    if ($GroupName->length > 0) {
                                        $GroupName = $GroupName->item(0)->nodeValue;
                                    } else {
                                        $GroupName = "";
                                    }
                                    $RefPrice = $Meal->item($m)->getElementsByTagName("RefPrice");
                                    if ($RefPrice->length > 0) {
                                        $RefPrice = $RefPrice->item(0)->nodeValue;
                                    } else {
                                        $RefPrice = "";
                                    }
                                    $IsIncluded = $Meal->item($m)->getElementsByTagName("IsIncluded");
                                    if ($IsIncluded->length > 0) {
                                        $IsIncluded = $IsIncluded->item(0)->nodeValue;
                                    } else {
                                        $IsIncluded = "";
                                    }

                                    try {
                                        $sql = new Sql($db);
                                        $select = $sql->select();
                                        $select->from('rates_mealplan');
                                        $select->where(array(
                                            'Id' => $IdM
                                        ));
                                        $statement = $sql->prepareStatementForSqlObject($select);
                                        $result = $statement->execute();
                                        $result->buffer();
                                        $customers = array();
                                        if ($result->valid()) {
                                            $data = $result->current();
                                            $id = (int)$data['Id'];
                                            if ($id > 0) {
                                                $sql = new Sql($db);
                                                $data = array(
                                                    'Id' => $IdM,
                                                    'datetime_created' => time(),
                                                    'datetime_updated' => 1,
                                                    'Code' => $CodeM,
                                                    'Name' => $NameM,
                                                    'AgeType' => $AgeType,
                                                    'GroupName' => $GroupName,
                                                    'RefPrice' => $RefPrice,
                                                    'IsIncluded' => $IsIncluded,
                                                    'IdHoteldata' => $Id
                                                );
                                                $where['Id = ?'] = $IdM;
                                                $update = $sql->update('rates_mealplan', $data, $where);
                                                $db->getDriver()
                                                    ->getConnection()
                                                    ->disconnect();
                                            } else {
                                                $sql = new Sql($db);
                                                $insert = $sql->insert();
                                                $insert->into('rates_mealplan');
                                                $insert->values(array(
                                                    'Id' => $IdM,
                                                    'datetime_created' => time(),
                                                    'datetime_updated' => 0,
                                                    'Code' => $CodeM,
                                                    'Name' => $NameM,
                                                    'AgeType' => $AgeType,
                                                    'GroupName' => $GroupName,
                                                    'RefPrice' => $RefPrice,
                                                    'IsIncluded' => $IsIncluded,
                                                    'IdHoteldata' => $Id
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
                                            $insert->into('rates_mealplan');
                                            $insert->values(array(
                                                'Id' => $IdM,
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'Code' => $CodeM,
                                                'Name' => $NameM,
                                                'AgeType' => $AgeType,
                                                'GroupName' => $GroupName,
                                                'RefPrice' => $RefPrice,
                                                'IsIncluded' => $IsIncluded,
                                                'IdHoteldata' => $Id
                                            ), $insert::VALUES_MERGE);
                                            $statement = $sql->prepareStatementForSqlObject($insert);
                                            $results = $statement->execute();
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        }
                                    } catch (Exception $ex) {
                                        echo $return;
                                        echo "ERRO: " . $ex;
                                        echo $return;
                                    }

                                }
                                
                            }
                        }

                        //Rate
                        $Rate = $Allotment->item($al)->getElementsByTagName("Rate");
                        if ($Rate->length > 0) {
                            for ($r=0; $r < $Rate->length; $r++) { 
                                $Acc = $Rate->item($r)->getElementsByTagName("Acc");
                                if ($Acc->length > 0) {
                                    $Acc = $Acc->item(0)->nodeValue;
                                } else {
                                    $Acc = "";
                                }
                                $RefPrice = $Rate->item($r)->getElementsByTagName("RefPrice");
                                if ($RefPrice->length > 0) {
                                    $RefPrice = $RefPrice->item(0)->nodeValue;
                                } else {
                                    $RefPrice = "";
                                }
                                $MaxPax = $Rate->item($r)->getElementsByTagName("MaxPax");
                                if ($MaxPax->length > 0) {
                                    $MaxPax = $MaxPax->item(0)->nodeValue;
                                } else {
                                    $MaxPax = "";
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('rates_rate');
                                    $insert->values(array(
                                        'Id' => $Id,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'RoomType' => $RoomTypeR,
                                        'Acc' => $Acc,
                                        'RefPrice' => $RefPrice,
                                        'MaxPax' => $MaxPax
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (Exception $ex) {
                                    echo $return;
                                    echo "ERRO: " . $ex;
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>