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
echo "COMECOU ZONES<br/>";
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
    
$config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sql = "SELECT id FROM countries";
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
        $countrycode = $row->id;

        $url = 'https://test.xtravelsystem.com/public/v1_0rc1/commonsHandler';

        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/common/types">
        <soapenv:Header/>
        <soapenv:Body>
        <typ:getZones>
            <GetZonesRQ_1>
                <agencyCode>266333</agencyCode>
                <brandCode>1</brandCode>
                <pointOfSaleId>1</pointOfSaleId>
                <countryCode>' . $countrycode . '</countryCode>
                <language>EN</language>
            </GetZonesRQ_1>
        </typ:getZones>
        </soapenv:Body>
        </soapenv:Envelope>';

        $headers = array(
            "Content-type: text/xml",
            "Accept-Encoding: gzip, deflate",
            "Content-length: " . strlen($raw)
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 65000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
        curl_setopt($ch,CURLOPT_ENCODING , "gzip, deflate");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        echo $return;
        echo $error;
        echo $return;
        echo "<xmp>";
        var_dump($response);
        echo "</xmp>"; 

        $config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
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
        $getZonesResponse = $Body->item(0)->getElementsByTagName("getZonesResponse");
        if ($getZonesResponse->length > 0) {
            $result = $getZonesResponse->item(0)->getElementsByTagName("result");
            if ($result->length > 0) {
                $zones = $result->item(0)->getElementsByTagName("zones");
                if ($zones->length > 0) {
                    for ($i=0; $i < $zones->length; $i++) { 
                        $code = $zones->item($i)->getElementsByTagName("code");
                        if ($code->length > 0) {
                            $code = $code->item(0)->nodeValue;
                        } else {
                            $code = 0;
                        }
                        $name = $zones->item($i)->getElementsByTagName("name");
                        if ($name->length > 0) {
                            $name = $name->item(0)->nodeValue;
                        } else {
                            $name = "";
                        }
                        $cities = $zones->item($i)->getElementsByTagName("cities");
                        if ($cities->length > 0) {
                            $citycode = $cities->item(0)->getElementsByTagName("code");
                            if ($citycode->length > 0) {
                                $citycode = $citycode->item(0)->nodeValue;
                            } else {
                                $citycode = 0;
                            }
                            $cityname = $cities->item(0)->getElementsByTagName("name");
                            if ($cityname->length > 0) {
                                $cityname = $cityname->item(0)->nodeValue;
                            } else {
                                $cityname = "";
                            }
                        }

                        try {
                            $sql = new Sql($db);
                            $select = $sql->select();
                            $select->from('zones');
                            $select->where(array(
                                'id' => $code
                            ));
                            $statement = $sql->prepareStatementForSqlObject($select);
                            $result = $statement->execute();
                            $result->buffer();
                            $customers = array();
                            if ($result->valid()) {
                                $data = $result->current();
                                $id = (string)$data['id'];
                                if ($id != "") {
                                    $config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
                                    $config = [
                                        'driver' => $config->db->driver,
                                        'database' => $config->db->database,
                                        'username' => $config->db->username,
                                        'password' => $config->db->password,
                                        'hostname' => $config->db->hostname
                                    ];
                                    $dbUpdate = new \Zend\Db\Adapter\Adapter($config);
            
                                    $data = array(
                                        'datetime_updated' => time(),
                                        'name' => $name,
                                        'citycode' => $citycode,
                                        'cityname' => $cityname
                                    );
                
                                    $sql    = new Sql($dbUpdate);
                                    $update = $sql->update();
                                    $update->table('zones');
                                    $update->set($data);
                                    $update->where(array('id' => $code));
            
                                    $statement = $sql->prepareStatementForSqlObject($update);
                                    $results = $statement->execute();
                                    $dbUpdate->getDriver()
                                    ->getConnection()
                                    ->disconnect(); 
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('zones');
                                    $insert->values(array(
                                        'id' => $code,
                                        'datetime_updated' => time(),
                                        'name' => $name,
                                        'citycode' => $citycode,
                                        'cityname' => $cityname
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
                                $insert->into('zones');
                                $insert->values(array(
                                    'id' => $code,
                                    'datetime_updated' => time(),
                                    'name' => $name,
                                    'citycode' => $citycode,
                                    'cityname' => $cityname
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO: ". $e;
                            echo $return;
                        }

                        $airports = $zones->item($i)->getElementsByTagName("airports");
                        if ($airports->length > 0) {
                            for ($j=0; $j < $airports->length; $j++) { 
                                $airportcode = $airports->item($j)->getElementsByTagName("code");
                                if ($airportcode->length > 0) {
                                    $airportcode = $airportcode->item(0)->nodeValue;
                                } else {
                                    $airportcode = 0;
                                }
                                $name = $airports->item($j)->getElementsByTagName("name");
                                if ($name->length > 0) {
                                    $name = $name->item(0)->nodeValue;
                                } else {
                                    $name = "";
                                }
                                $iataCode = $airports->item($j)->getElementsByTagName("iataCode");
                                if ($iataCode->length > 0) {
                                    $iataCode = $iataCode->item(0)->nodeValue;
                                } else {
                                    $iataCode = "";
                                }
                                $type = $airports->item($j)->getElementsByTagName("type");
                                if ($type->length > 0) {
                                    $type = $type->item(0)->nodeValue;
                                } else {
                                    $type = "";
                                }
        
                                try {
                                    $sql = new Sql($db);
                                    $select = $sql->select();
                                    $select->from('zones_airports');
                                    $select->where(array(
                                        'id' => $airportcode
                                    ));
                                    $statement = $sql->prepareStatementForSqlObject($select);
                                    $result = $statement->execute();
                                    $result->buffer();
                                    $customers = array();
                                    if ($result->valid()) {
                                        $data = $result->current();
                                        $id = (string)$data['id'];
                                        if ($id != "") {
                                            $config = new \Zend\Config\Config(include '../config/autoload/global.jumbotours.php');
                                            $config = [
                                                'driver' => $config->db->driver,
                                                'database' => $config->db->database,
                                                'username' => $config->db->username,
                                                'password' => $config->db->password,
                                                'hostname' => $config->db->hostname
                                            ];
                                            $dbUpdate = new \Zend\Db\Adapter\Adapter($config);
                    
                                            $data = array(
                                                'datetime_updated' => time(),
                                                'name' => $name,
                                                'iatacode' => $iataCode,
                                                'type' => $type,
                                                'zonecode' => $code,
                                                'countrycode' => $countrycode
                                            );
                        
                                            $sql    = new Sql($dbUpdate);
                                            $update = $sql->update();
                                            $update->table('zones_airports');
                                            $update->set($data);
                                            $update->where(array('id' => $airportcode));
                    
                                            $statement = $sql->prepareStatementForSqlObject($update);
                                            $results = $statement->execute();
                                            $dbUpdate->getDriver()
                                            ->getConnection()
                                            ->disconnect(); 
                                        } else {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('zones_airports');
                                            $insert->values(array(
                                                'id' => $airportcode,
                                                'datetime_updated' => time(),
                                                'name' => $name,
                                                'iatacode' => $iataCode,
                                                'type' => $type,
                                                'zonecode' => $code,
                                                'countrycode' => $countrycode
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
                                        $insert->into('zones_airports');
                                        $insert->values(array(
                                            'id' => $airportcode,
                                            'datetime_updated' => time(),
                                            'name' => $name,
                                            'iatacode' => $iataCode,
                                            'type' => $type,
                                            'zonecode' => $code,
                                            'countrycode' => $countrycode
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    }
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO: ". $e;
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