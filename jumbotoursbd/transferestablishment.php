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
echo "COMECOU TRANSFER ESTABLISHMENT<br/>";
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

$sql = "SELECT id FROM jumbo_transfers_airports";
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
        $airportcode = $row->id;

        $url = 'https://test.xtravelsystem.com/public/v1_0rc1/transferBookingHandler';

        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="http://xtravelsystem.com/v1_0rc1/transfer/types">
                <soapenv:Header/>
                <soapenv:Body>
                <typ:getTransferEstablishmentsV2>
                    <GetTransferEstablishmentsRQ_1>
                        <agencyCode>266333</agencyCode>
                        <brandCode>1</brandCode>
                        <pointOfSaleId>1</pointOfSaleId>
                        <airportCode>' . $airportcode . '</airportCode>
                        <areaCode></areaCode>
                        <cityCode></cityCode>
                        <fromRow>1</fromRow>
                        <language>en</language>
                        <numRows>100</numRows>
                        <searchName></searchName>
                    </GetTransferEstablishmentsRQ_1>
                </typ:getTransferEstablishmentsV2>
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
        $getTransferEstablishmentsV2Response = $Body->item(0)->getElementsByTagName("getTransferEstablishmentsV2Response");
        if ($getTransferEstablishmentsV2Response->length > 0) {
            $result = $getTransferEstablishmentsV2Response->item(0)->getElementsByTagName("result");
            if ($result->length > 0) {
                $fromRow = $result->item(0)->getElementsByTagName("fromRow");
                if ($fromRow->length > 0) {
                    $fromRow = $fromRow->item(0)->nodeValue;
                } else {
                    $fromRow = "";
                }
                $toRow = $result->item(0)->getElementsByTagName("toRow");
                if ($toRow->length > 0) {
                    $toRow = $toRow->item(0)->nodeValue;
                } else {
                    $toRow = "";
                }
                $totalRows = $result->item(0)->getElementsByTagName("totalRows");
                if ($totalRows->length > 0) {
                    $totalRows = $totalRows->item(0)->nodeValue;
                } else {
                    $totalRows = "";
                }
                $result2 = $result->item(0)->getElementsByTagName("result");
                if ($result2->length > 0) {
                    for ($i=0; $i < $result2->length; $i++) { 
                        $categoryName = $result2->item($i)->getElementsByTagName("categoryName");
                        if ($categoryName->length > 0) {
                            $categoryName = $categoryName->item(0)->nodeValue;
                        } else {
                            $categoryName = "";
                        }
                        $establishmentId = $result2->item($i)->getElementsByTagName("establishmentId");
                        if ($establishmentId->length > 0) {
                            $establishmentId = $establishmentId->item(0)->nodeValue;
                        } else {
                            $establishmentId = "";
                        }
                        $establishmentName = $result2->item($i)->getElementsByTagName("establishmentName");
                        if ($establishmentName->length > 0) {
                            $establishmentName = $establishmentName->item(0)->nodeValue;
                        } else {
                            $establishmentName = "";
                        }
                        $address = $result2->item($i)->getElementsByTagName("address");
                        if ($address->length > 0) {
                            $address2 = $address->item(0)->getElementsByTagName("address");
                            if ($address2->length > 0) {
                                $address2 = $address2->item(0)->nodeValue;
                            } else {
                                $address2 = "";
                            }
                            $cityCode = $address->item(0)->getElementsByTagName("cityCode");
                            if ($cityCode->length > 0) {
                                $cityCode = $cityCode->item(0)->nodeValue;
                            } else {
                                $cityCode = "";
                            }
                            $cityName = $address->item(0)->getElementsByTagName("cityName");
                            if ($cityName->length > 0) {
                                $cityName = $cityName->item(0)->nodeValue;
                            } else {
                                $cityName = "";
                            }
                            $countryCode = $address->item(0)->getElementsByTagName("countryCode");
                            if ($countryCode->length > 0) {
                                $countryCode = $countryCode->item(0)->nodeValue;
                            } else {
                                $countryCode = "";
                            }
                            $countryName = $address->item(0)->getElementsByTagName("countryName");
                            if ($countryName->length > 0) {
                                $countryName = $countryName->item(0)->nodeValue;
                            } else {
                                $countryName = "";
                            }
                            $email = $address->item(0)->getElementsByTagName("email");
                            if ($email->length > 0) {
                                $email = $email->item(0)->nodeValue;
                            } else {
                                $email = "";
                            }
                            $fax = $address->item(0)->getElementsByTagName("fax");
                            if ($fax->length > 0) {
                                $fax = $fax->item(0)->nodeValue;
                            } else {
                                $fax = "";
                            }
                            $stateCode = $address->item(0)->getElementsByTagName("stateCode");
                            if ($stateCode->length > 0) {
                                $stateCode = $stateCode->item(0)->nodeValue;
                            } else {
                                $stateCode = "";
                            }
                            $stateName = $address->item(0)->getElementsByTagName("stateName");
                            if ($stateName->length > 0) {
                                $stateName = $stateName->item(0)->nodeValue;
                            } else {
                                $stateName = "";
                            }
                            $telephone = $address->item(0)->getElementsByTagName("telephone");
                            if ($telephone->length > 0) {
                                $telephone = $telephone->item(0)->nodeValue;
                            } else {
                                $telephone = "";
                            }
                            $zipCode = $address->item(0)->getElementsByTagName("zipCode");
                            if ($zipCode->length > 0) {
                                $zipCode = $zipCode->item(0)->nodeValue;
                            } else {
                                $zipCode = "";
                            }
                        }
                        try {
                            $sql = new Sql($db);
                            $select = $sql->select();
                            $select->from('jumbo_transfers_establishments');
                            $select->where(array(
                                'id' => $establishmentId
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
                                        'name' => $establishmentName,
                                        'categoryname' => $categoryName,
                                        'address' => $address2,
                                        'citycode' => $cityCode,
                                        'cityname' => $cityName,
                                        'countrycode' => $countryCode,
                                        'countryname' => $countryName,
                                        'statecode' => $stateCode,
                                        'statename' => $stateName,
                                        'zipcode' => $zipCode,
                                        'email' => $email,
                                        'telephone' => $telephone,
                                        'fax' => $fax
                                    );
                
                                    $sql    = new Sql($dbUpdate);
                                    $update = $sql->update();
                                    $update->table('jumbo_transfers_establishments');
                                    $update->set($data);
                                    $update->where(array('id' => $establishmentId));
            
                                    $statement = $sql->prepareStatementForSqlObject($update);
                                    $results = $statement->execute();
                                    $dbUpdate->getDriver()
                                    ->getConnection()
                                    ->disconnect(); 
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('jumbo_transfers_establishments');
                                    $insert->values(array(
                                        'id' => $establishmentId,
                                        'datetime_updated' => time(),
                                        'name' => $establishmentName,
                                        'categoryname' => $categoryName,
                                        'address' => $address2,
                                        'citycode' => $cityCode,
                                        'cityname' => $cityName,
                                        'countrycode' => $countryCode,
                                        'countryname' => $countryName,
                                        'statecode' => $stateCode,
                                        'statename' => $stateName,
                                        'zipcode' => $zipCode,
                                        'email' => $email,
                                        'telephone' => $telephone,
                                        'fax' => $fax
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
                                $insert->into('jumbo_transfers_establishments');
                                $insert->values(array(
                                    'id' => $establishmentId,
                                    'datetime_updated' => time(),
                                    'name' => $establishmentName,
                                    'categoryname' => $categoryName,
                                    'address' => $address2,
                                    'citycode' => $cityCode,
                                    'cityname' => $cityName,
                                    'countrycode' => $countryCode,
                                    'countryname' => $countryName,
                                    'statecode' => $stateCode,
                                    'statename' => $stateName,
                                    'zipcode' => $zipCode,
                                    'email' => $email,
                                    'telephone' => $telephone,
                                    'fax' => $fax
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>