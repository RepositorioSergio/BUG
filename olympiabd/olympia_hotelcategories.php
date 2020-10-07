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

$config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://parsystest.olympia.it/NewAvailabilityServlet/staticdata/OTA2014A';

$raw = '<soap-env:Envelope xmlns:soap-env="http://schemas.xmlsoap.org/soap/envelope/">
<soap-env:Header>
    <wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
        <wsse:Username>628347</wsse:Username>
        <wsse:Password>clubtest</wsse:Password>
        <Context>olympia_europe_ts</Context>
    </wsse:Security>
</soap-env:Header>
<soap-env:Body>
    <OTA_ReadRQ xmlns:ns="http://www.opentravel.org/OTA/2003/05/common" xmlns="http://www.opentravel.org/OTA/2003/05" TimeStamp="2015-07-16T06:38:10.60">
        <ReadRequests>
            <HotelReadRequest>
                <TPA_Extensions>
                    <RequestType>GetHotelCategories</RequestType>
                </TPA_Extensions>
            </HotelReadRequest>
        </ReadRequests>
    </OTA_ReadRQ>
</soap-env:Body>
</soap-env:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type: text/xml; charset=utf-8',
    'Accept: application/xml',
    'Content-Length: ' . strlen($raw)
));

$client->setUri($url);
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

echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
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
$OTA_ReadRS = $inputDoc->getElementsByTagName('OTA_ReadRS');
$ReadResponse = $OTA_ReadRS->item(0)->getElementsByTagName('ReadResponse');
$HotelCategories = $ReadResponse->item(0)->getElementsByTagName('HotelCategories');
if ($HotelCategories->length > 0) {
    $HotelCategory = $HotelCategories->item(0)->getElementsByTagName('HotelCategory');
    if ($HotelCategory->length > 0) {
        for ($i=0; $i < $HotelCategory->length; $i++) { 
            $CategoryCode = $HotelCategory->item($i)->getAttribute('CategoryCode');
            $CategoryName = $HotelCategory->item($i)->getElementsByTagName('CategoryName');
            if ($CategoryName->length > 0) {
                $CategoryName = $CategoryName->item(0)->nodeValue;
            } else {
                $CategoryName = "";
            }
            $CategoryLevel = $HotelCategory->item($i)->getElementsByTagName('CategoryLevel');
            if ($CategoryLevel->length > 0) {
                $CategoryLevel = $CategoryLevel->item(0)->nodeValue;
            } else {
                $CategoryLevel = "";
            }

            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('olympia_hotelcategories');
                $select->where(array(
                    'id' => $CategoryCode
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id = (int)$data['id'];
                    if ($id > 0) {
                        $config = new \Zend\Config\Config(include '../config/autoload/global.olympia.php');
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
                            'name' => $CategoryName, 
                            'categorylevel' => $CategoryLevel
                        );
      
                        $sql    = new Sql($dbUpdate);
                        $update = $sql->update();
                        $update->table('olympia_hotelcategories');
                        $update->set($data);
                        $update->where(array('id' => $CategoryCode));

                        $statement = $sql->prepareStatementForSqlObject($update);
                        $results = $statement->execute();
                        $dbUpdate->getDriver()
                        ->getConnection()
                        ->disconnect(); 
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('olympia_hotelcategories');
                        $insert->values(array(
                            'id' => $CategoryCode,
                            'datetime_updated' => time(),
                            'name' => $CategoryName, 
                            'categorylevel' => $CategoryLevel
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
                    $insert->into('olympia_hotelcategories');
                    $insert->values(array(
                        'id' => $CategoryCode,
                        'datetime_updated' => time(),
                        'name' => $CategoryName, 
                        'categorylevel' => $CategoryLevel
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>