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
echo "COMECOU SERVICE CATALOGUE";
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
    <ServiceCatalogueData>
        <ServiceCatalogueDataRQ Version="1.1" Language="en">
            <Login Password = "' . $password . '" Email = "' . $email . '"/>
        </ServiceCatalogueDataRQ>
    </ServiceCatalogueData>
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
$ServiceCatalogueDataResponse = $Body->item(0)->getElementsByTagName("ServiceCatalogueDataResponse");
if ($ServiceCatalogueDataResponse->length > 0) {
    $CatalogueDataRS = $ServiceCatalogueDataResponse->item(0)->getElementsByTagName("CatalogueDataRS");
    if ($CatalogueDataRS->length > 0) {
        $IntCode = $CatalogueDataRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $CatalogueDataRS->item(0)->getAttribute("TimeStamp");
        $Url = $CatalogueDataRS->item(0)->getAttribute("Url");
        $ServiceStaticData = $CatalogueDataRS->item(0)->getElementsByTagName("ServiceStaticData");
        if ($ServiceStaticData->length > 0) {
            $ServiceCategoryList = $ServiceStaticData->item(0)->getElementsByTagName("ServiceCategoryList");
            if ($ServiceCategoryList->length > 0) {
                $ServiceCategory = $ServiceCategoryList->item(0)->getElementsByTagName("ServiceCategory");
                if ($ServiceCategory->length > 0) {
                    for ($i=0; $i < $ServiceCategory->length; $i++) { 
                        $Code = $ServiceCategory->item($i)->getAttribute("Code");
                        $Name = $ServiceCategory->item($i)->getElementsByTagName("Name");
                        if ($Name->length > 0) {
                            $Name = $Name->item(0)->nodeValue;
                        } else {
                            $Name = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $select = $sql->select();
                            $select->from('servicecatalogue_servicecategory');
                            $select->where(array(
                                'id' => $Code
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
                                        'id' => $Code,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 1,
                                        'name' => $Name
                                    );
                                    $where['id = ?'] = $Code;
                                    $update = $sql->update('servicecatalogue_servicecategory', $data, $where);
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('servicecatalogue_servicecategory');
                                    $insert->values(array(
                                        'id' => $Code,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $Name
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
                                $insert->into('servicecatalogue_servicecategory');
                                $insert->values(array(
                                    'id' => $Code,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 1: ". $e;
                            echo $return;
                        }
                    }
                }
            }
            $ServiceTypeList = $ServiceStaticData->item(0)->getElementsByTagName("ServiceTypeList");
            if ($ServiceTypeList->length > 0) {
                $ServiceType = $ServiceTypeList->item(0)->getElementsByTagName("ServiceType");
                if ($ServiceType->length > 0) {
                    for ($j=0; $j < $ServiceType->length; $j++) { 
                        $Code = $ServiceType->item($j)->getAttribute("Code");
                        $Name = $ServiceType->item($j)->getElementsByTagName("Name");
                        if ($Name->length > 0) {
                            $Name = $Name->item(0)->nodeValue;
                        } else {
                            $Name = "";
                        }

                        try {
                            $sql = new Sql($db);
                            $select = $sql->select();
                            $select->from('servicecatalogue_servicetype');
                            $select->where(array(
                                'id' => $Code
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
                                        'id' => $Code,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 1,
                                        'name' => $Name
                                    );
                                    $where['id = ?'] = $Code;
                                    $update = $sql->update('servicecatalogue_servicetype', $data, $where);
                                    $db->getDriver()
                                        ->getConnection()
                                        ->disconnect();
                                } else {
                                    $sql = new Sql($db);
                                    $insert = $sql->insert();
                                    $insert->into('servicecatalogue_servicetype');
                                    $insert->values(array(
                                        'id' => $Code,
                                        'datetime_created' => time(),
                                        'datetime_updated' => 0,
                                        'name' => $Name
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
                                $insert->into('servicecatalogue_servicetype');
                                $insert->values(array(
                                    'id' => $Code,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name
                                ), $insert::VALUES_MERGE);
                                $statement = $sql->prepareStatementForSqlObject($insert);
                                $results = $statement->execute();
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            }
                        } catch (\Exception $e) {
                            echo $return;
                            echo "ERRO 2: ". $e;
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
