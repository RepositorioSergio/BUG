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
echo "COMECOU SERVICE PORTFOLIO";
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

$raw = '<soapenv:Envelope xmlns:soapenv = "http://schemas.xmlsoap.org/soap/envelope/" xmlns = "http://www.juniper.es/webservice/2007/">
<soapenv:Header/>
<soapenv:Body>
    <ServicePortfolio>
        <ServicePortfolioRQ Version = "1.1" Language = "en" Page = "1" RecordsPerPage = "100">
            <Login Password = "' . $password . '" Email = "' . $email . '"/>
        </ServicePortfolioRQ>
    </ServicePortfolio>
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
$ServicePortfolioResponse = $Body->item(0)->getElementsByTagName("ServicePortfolioResponse");
if ($ServicePortfolioResponse->length > 0) {
    $ServicePortfolioRS = $ServicePortfolioResponse->item(0)->getElementsByTagName("ServicePortfolioRS");
    if ($ServicePortfolioRS->length > 0) {
        $IntCode = $ServicePortfolioRS->item(0)->getAttribute("IntCode");
        $TimeStamp = $ServicePortfolioRS->item(0)->getAttribute("TimeStamp");
        $Url = $ServicePortfolioRS->item(0)->getAttribute("Url");
        $ServicePortfolio = $ServicePortfolioRS->item(0)->getElementsByTagName("ServicePortfolio");
        if ($ServicePortfolio->length > 0) {
            $Page = $ServicePortfolio->item(0)->getAttribute("Page");
            $RecordsPerPage = $ServicePortfolio->item(0)->getAttribute("RecordsPerPage");
            $TotalPages = $ServicePortfolio->item(0)->getAttribute("TotalPages");
            $TotalRecords = $ServicePortfolio->item(0)->getAttribute("TotalRecords");
            $Service = $ServicePortfolio->item(0)->getElementsByTagName("Service");
            if ($Service->length > 0) {
                for ($i=0; $i < $Service->length; $i++) { 
                    $Code = $Service->item($i)->getAttribute("Code");
                    $IntCode = $Service->item($i)->getAttribute("IntCode");
                    $ServiceTypeCode = $Service->item($i)->getAttribute("ServiceTypeCode");
                    $Name = $Service->item($i)->getElementsByTagName("Name");
                    if ($Name->length > 0) {
                        $Name = $Name->item(0)->nodeValue;
                    } else {
                        $Name = "";
                    }
                    $Options = $Service->item($i)->getElementsByTagName("Options");
                    if ($Options->length > 0) {
                        $ServiceOption = $Options->item(0)->getElementsByTagName("ServiceOption");
                        if ($ServiceOption->length > 0) {
                            $ServiceOptionCode = $ServiceOption->item(0)->getAttribute("Code");
                            $Order = $ServiceOption->item(0)->getAttribute("Order");
                            $NumberOfDays = $ServiceOption->item(0)->getAttribute("NumberOfDays");
                            $ServiceOptionName = $ServiceOption->item(0)->getElementsByTagName("Name");
                            if ($ServiceOptionName->length > 0) {
                                $ServiceOptionName = $ServiceOptionName->item(0)->nodeValue;
                            } else {
                                $ServiceOptionName = "";
                            }
                            $Description = $ServiceOption->item(0)->getElementsByTagName("Description");
                            if ($Description->length > 0) {
                                $Description = $Description->item(0)->nodeValue;
                            } else {
                                $Description = "";
                            }
                        }
                    }

                    try {
                        $sql = new Sql($db);
                        $select = $sql->select();
                        $select->from('serviceportfolio');
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
                                    'name' => $Name,
                                    'intcode' => $IntCode,
                                    'servicetypecode' => $ServiceTypeCode,
                                    'serviceoptioncode' => $ServiceOptionCode,
                                    'order' => $Order,
                                    'numberofdays' => $NumberOfDays,
                                    'serviceoptionname' => $ServiceOptionName,
                                    'description' => $Description 
                                );
                                $where['id = ?'] = $Code;
                                $update = $sql->update('serviceportfolio', $data, $where);
                                $db->getDriver()
                                    ->getConnection()
                                    ->disconnect();
                            } else {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('serviceportfolio');
                                $insert->values(array(
                                    'id' => $Code,
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'name' => $Name,
                                    'intcode' => $IntCode,
                                    'servicetypecode' => $ServiceTypeCode,
                                    'serviceoptioncode' => $ServiceOptionCode,
                                    'order' => $Order,
                                    'numberofdays' => $NumberOfDays,
                                    'serviceoptionname' => $ServiceOptionName,
                                    'description' => $Description
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
                            $insert->into('serviceportfolio');
                            $insert->values(array(
                                'id' => $Code,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $Name,
                                'intcode' => $IntCode,
                                'servicetypecode' => $ServiceTypeCode,
                                'serviceoptioncode' => $ServiceOptionCode,
                                'order' => $Order,
                                'numberofdays' => $NumberOfDays,
                                'serviceoptionname' => $ServiceOptionName,
                                'description' => $Description
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

                    $Zones = $Service->item($i)->getElementsByTagName("Zones");
                    if ($Zones->length > 0) {
                        $Zone = $Zones->item(0)->getElementsByTagName("Zone");
                        if ($Zone->length > 0) {
                            for ($iAux=0; $iAux < $Zone->length; $iAux++) { 
                                $ZoneCode = $Zone->item($iAux)->getAttribute("Code");

                                try {
                                    $sql = new Sql($db);
                                    $select = $sql->select();
                                    $select->from('serviceportfolio_zones');
                                    $select->where(array(
                                        'id' => $ZoneCode
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
                                                'id' => $ZoneCode,
                                                'datetime_created' => time(),
                                                'datetime_updated' => 1,
                                                'serviceportfolioid' => $Code
                                            );
                                            $where['id = ?'] = $ZoneCode;
                                            $update = $sql->update('serviceportfolio_zones', $data, $where);
                                            $db->getDriver()
                                                ->getConnection()
                                                ->disconnect();
                                        } else {
                                            $sql = new Sql($db);
                                            $insert = $sql->insert();
                                            $insert->into('serviceportfolio_zones');
                                            $insert->values(array(
                                                'id' => $ZoneCode,
                                                'datetime_created' => time(),
                                                'datetime_updated' => 0,
                                                'serviceportfolioid' => $Code
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
                                        $insert->into('serviceportfolio_zones');
                                        $insert->values(array(
                                            'id' => $ZoneCode,
                                            'datetime_created' => time(),
                                            'datetime_updated' => 0,
                                            'serviceportfolioid' => $Code
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
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
