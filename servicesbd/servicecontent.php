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
echo "COMECOU SERVICE CONTENT";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.services.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'https://xml-uat.bookingengine.es/WebService/jp/operations/staticdatatransactions.asmx';

$email = 'waleed.medhat@wingsholding.com';
$password = 'Dkf94j512#';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns="http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
    <ServiceContent>
        <ServiceContentRQ Version="1.1" Language="en">
            <Login Password = "' . $password . '" Email = "' . $email . '"/>
            <ServiceContentList>
                <Service Code = "JcG+h+EZ0EznJonPYMNdrQ=="/>
                <Service Code = "6IwSkuinzsgXcNiI6VDtSQ=="/>
            </ServiceContentList>
        </ServiceContentRQ>
    </ServiceContent>
</soapenv:Body>
</soapenv:Envelope>';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/xml",
    "Content-type: text/xml",
    "Accept-Encoding: gzip, deflate",
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$xmlresult = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);
curl_close($ch);

echo "<xmp>";
var_dump($xmlresult);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.services.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$inputDoc = new DOMDocument();
$inputDoc->loadXML($xmlresult);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$ServiceContentResponse = $Body->item(0)->getElementsByTagName("ServiceContentResponse");
if ($ServiceContentResponse->length > 0) {
    $ContentRS = $ServiceContentResponse->item(0)->getElementsByTagName("ContentRS");
    if ($ContentRS->length > 0) {
        $IntCode = $ContentRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $ContentRS->item(0)->getAttribute("TimeStamp");
        $Url = $ContentRS->item(0)->getAttribute("Url");
        $Contents = $ContentRS->item(0)->getElementsByTagName("Contents");
        if ($Contents->length > 0) {
            $ServiceContent = $Contents->item(0)->getElementsByTagName("ServiceContent");
            if ($ServiceContent->length > 0) {
                for ($i=0; $i < $ServiceContent->length; $i++) { 
                    $Code = $ServiceContent->item($i)->getAttribute("Code");
                    $ServiceContentInfo = $ServiceContent->item($i)->getElementsByTagName("ServiceContentInfo");
                    if ($ServiceContentInfo->length > 0) {
                        $ServiceName = $ServiceContentInfo->item(0)->getElementsByTagName("ServiceName");
                        if ($ServiceName->length > 0) {
                            $ServiceName = $ServiceName->item(0)->nodeValue;
                        } else {
                            $ServiceName = "";
                        }
                        $Images = $ServiceContentInfo->item(0)->getElementsByTagName("Images");
                        if ($Images->length > 0) {
                            $Images = $Images->item(0)->nodeValue;
                        } else {
                            $Images = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('servicecontent_info');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'servicename' => $ServiceName,
                                'images' => $Images,
                                'servicecode' => $Code,
                            ), $insert::VALUES_MERGE);
                            $statement = $sql->prepareStatementForSqlObject($insert);
                            $results = $statement->execute();
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 1: ". $e;
                            echo $return;
                        }

                        $Descriptions = $ServiceContentInfo->item(0)->getElementsByTagName("Descriptions");
                        if ($Descriptions->length > 0) {
                            $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                for ($iAux=0; $iAux < $Description->length; $iAux++) { 
                                    $Type = $Description->item($iAux)->getAttribute("Type");
                                    $Description = $Description->item($iAux)->nodeValue;

                                    try {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('servicecontent_info_descriptions');
                                        $insert->values(array(
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'type' => $Type,
                                            'description' => $Description,
                                            'servicecode' => $Code
                                        ), $insert::VALUES_MERGE);
                                        $statement = $sql->prepareStatementForSqlObject($insert);
                                        $results = $statement->execute();
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } catch (\Exception $e) {
                                        echo $return;
                                        echo "ERRO 2: ". $e;
                                        echo $return;
                                    }
                                }
                            }
                        }
                    }
                    $ServiceZones = $ServiceContent->item($i)->getElementsByTagName("ServiceZones");
                    if ($ServiceZones->length > 0) {
                        $Zone = $ServiceZones->item(0)->getElementsByTagName("Zone");
                        if ($Zone->length > 0) {
                            for ($iAux2=0; $iAux2 < $Zone->length; $iAux2++) { 
                                $ZoneCode = $Zone->item($iAux2)->getAttribute("Code");
                                $JPDCode = $Zone->item($iAux2)->getAttribute("JPDCode");
                                $Name = $Zone->item($iAux2)->getElementsByTagName("Name");
                                if ($Name->length > 0) {
                                    $Name = $Name->item(0)->nodeValue;
                                } else {
                                    $Name = "";
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('servicecontent_zones');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'zonecode' => $ZoneCode,
                                        'name' => $Name,
                                        'jpdcode' => $JPDCode,
                                        'servicecode' => $Code
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 3: ". $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                    $ServiceOptions = $ServiceContent->item($i)->getElementsByTagName("ServiceOptions");
                    if ($ServiceOptions->length > 0) {
                        $ServiceOption = $ServiceOptions->item(0)->getElementsByTagName("ServiceOption");
                        if ($ServiceOption->length > 0) {
                            for ($iAux3=0; $iAux3 < $ServiceOption->length; $iAux3++) { 
                                $Order = $ServiceOption->item($iAux3)->getAttribute("Order");
                                $NumberOfDays = $ServiceOption->item($iAux3)->getAttribute("NumberOfDays");
                                $Name = $ServiceOption->item($iAux3)->getElementsByTagName("Name");
                                if ($Name->length > 0) {
                                    $Name = $Name->item(0)->nodeValue;
                                } else {
                                    $Name = "";
                                }
                                $Descriptions = $ServiceOption->item($iAux3)->getElementsByTagName("Descriptions");
                                if ($Descriptions->length > 0) {
                                    $Description = $Descriptions->item(0)->getElementsByTagName("Description");
                                    if ($Description->length > 0) {
                                        $Type = $Description->item(0)->getAttribute("Type");
                                        $Description = $Description->item(0)->nodeValue;
                                    } else {
                                        $Description = "";
                                    }
                                }

                                try {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('servicecontent_serviceoptions');
                                    $insert->values(array(
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $Name,
                                        'order' => $Order,
                                        'numberofdays' => $NumberOfDays,
                                        'type' => $Type,
                                        'description' => $Description,
                                        'servicecode' => $Code
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } catch (\Exception $e) {
                                    echo $return;
                                    echo "ERRO 4: ". $e;
                                    echo $return;
                                }
                            }
                        }
                    }
                    $ServiceType = $ServiceContent->item($i)->getElementsByTagName("ServiceType");
                    if ($ServiceType->length > 0) {
                        for ($j=0; $j < $ServiceType->length; $j++) { 
                            $ServiceTypeCode = $ServiceType->item($j)->getAttribute("Code");
                            $Name = $ServiceType->item($j)->getElementsByTagName("Name");
                            if ($Name->length > 0) {
                                $Name = $Name->item(0)->nodeValue;
                            } else {
                                $Name = "";
                            }
    
                            try {
                                $sql = new Sql($db);
                                $select = $sql->select();
                                $select->from('servicecontent_servicetype');
                                $select->where(array(
                                    'id' => $ServiceTypeCode
                                ));
                                $statement = $sql->prepareStatementForSqlObject($select);
                                $result = $statement->execute();
                                $result->buffer();
                                $customers = array();
                                if ($result->valid()) {
                                    $data = $result->current();
                                    $id = (string)$data['id'];
                                    if ($id != "") {
                                        $sql = new Sql($db);
                                        $data = array(
                                            'id' => $ServiceType,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 1,
                                            'name' => $Name,
                                            'servicecode' => $Code
                                        );
                                        $where['id = ?'] = $ServiceTypeCode;
                                        $update = $sql->update('servicecontent_servicetype', $data, $where);
                                        $db->getDriver()
                                            ->getConnection()
                                            ->disconnect();
                                    } else {
                                        $sql = new Sql($db);
                                        $insert = $sql->insert();
                                        $insert->into('servicecontent_servicetype');
                                        $insert->values(array(
                                            'id' => $ServiceTypeCode,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'name' => $Name,
                                            'servicecode' => $Code
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
                                    $insert->into('servicecontent_servicetype');
                                    $insert->values(array(
                                        'id' => $ServiceTypeCode,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $Name,
                                        'servicecode' => $Code
                                    ), $insert::VALUES_MERGE);
                                    $statement = $sql->prepareStatementForSqlObject($insert);
                                    $results = $statement->execute();
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                }
                            } catch (\Exception $e) {
                                echo $return;
                                echo "ERRO 5: ". $e;
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
