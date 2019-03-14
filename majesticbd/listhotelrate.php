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
                <m:ListHotelsRates xmlns:m="http://www.majesticusa.com/majesticweb_xml/">
                    <m:hotelid>
                        <m:int>' . $hotel_id . '</m:int>
                    </m:hotelid>
                </m:ListHotelsRates>
            </SOAP-ENV:Body>
        </SOAP-ENV:Envelope>';
        
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
        //echo "RESPONSE";
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
        $ListHotelsRatesResponse = $Body->item(0)->getElementsByTagName("ListHotelsRatesResponse");
        $ListHotelsRatesResult = $ListHotelsRatesResponse->item(0)->getElementsByTagName("ListHotelsRatesResult");
        $Root = $ListHotelsRatesResult->item(0)->getElementsByTagName("Root");
        $Hotel = $Root->item(0)->getElementsByTagName("Hotel");
        $Id = $Hotel->item(0)->getElementsByTagName("Id");
        if ($Id->length > 0) {
            $Id = $Id->item(0)->nodeValue;
        } else {
            $Id = "";
        }
        echo $return;
        echo $Id;
        echo $return;
        $Name = $Hotel->item(0)->getElementsByTagName("Name");
        if ($Name->length > 0) {
            $Name = $Name->item(0)->nodeValue;
        } else {
            $Name = "";
        }
        try {
            $sql = new Sql($db);
            $select = $sql->select();
            $select->from('hotelrates');
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
                        'Name' => $Name
                    );
                    $where['Id = ?'] = $Id;
                    $update = $sql->update('hotelrates', $data, $where);
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } else {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('hotelrates');
                    $insert->values(array(
                        'Id' => $Id,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'Name' => $Name
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
                $insert->into('hotelrates');
                $insert->values(array(
                    'Id' => $Id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Name' => $Name
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
        $rates = $Hotel->item(0)->getElementsByTagName("rates");
        $Rates = $rates->item(0)->getElementsByTagName("Rates");
        echo $return;
        echo "TAM: " . $Rates->length;
        echo $return;
        for ($i = 0; $i < $Rates->length; $i++) {
            echo $return;
            echo $i;
            echo $return;
            $RoomTypeR = $Rates->item($i)->getElementsByTagName("RoomType");
            if ($RoomTypeR->length > 0) {
                $RoomTypeR = $RoomTypeR->item(0)->nodeValue;
            } else {
                $RoomTypeR = "";
            }
        
            $Allotment = $Rates->item($i)->getElementsByTagName("Allotment");
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
                        $insert->into('hotelrates_allotment');
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
                                $insert->into('hotelrates_rate');
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
                            
                            $SpecialPrice = $Rate->item($r)->getElementsByTagName("SpecialPrice");
                            if ($SpecialPrice->length > 0) {
                                for ($sp=0; $sp < $SpecialPrice->length; $sp++) { 
                                    $Day = $SpecialPrice->item($sp)->getElementsByTagName("Day");
                                    if ($Day->length > 0) {
                                        $Day = $Day->item(0)->nodeValue;
                                    } else {
                                        $Day = "";
                                    }
                                    $RefPrice = $SpecialPrice->item($sp)->getElementsByTagName("RefPrice");
                                    if ($RefPrice->length > 0) {
                                        $RefPrice = $RefPrice->item(0)->nodeValue;
                                    } else {
                                        $RefPrice = "";
                                    }
                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('hotelrates_specialprice');
                                        $insert->values(array(
                                            'Id' => $Id,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'Acc' => $Acc,
                                            'Day' => $Day,
                                            'RefPrice' => $RefPrice
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
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>