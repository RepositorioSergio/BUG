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

$config = new \Zend\Config\Config(include '../config/autoload/global.nuitee.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$url = 'http://ws.nuitee.com/nuitee/Nuitee?WSDL';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:nuit="http://www.nuitee.ma">
<soapenv:Header/>
<soapenv:Body>
   <nuit:performAllDestinations>
      <!--Optional:-->
      <allDestinationsReq>
         <!--Optional:-->
         <sessionId></sessionId>
         <login>
            <!--Optional:-->
            <language>en</language>
            <!--Optional:-->
            <password>Club12020</password>
            <!--Optional:-->
            <userName>Club1Robert</userName>
         </login>
      </allDestinationsReq>
   </nuit:performAllDestinations>
</soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type: text/xml; charset=utf-8',
    'Accept: text/xml',
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
$config = new \Zend\Config\Config(include '../config/autoload/global.nuitee.php');
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
$Envelope = $inputDoc->getElementsByTagName('Envelope');
$Body = $Envelope->item(0)->getElementsByTagName('Body');
$performAllDestinationsResponse = $Body->item(0)->getElementsByTagName('performAllDestinationsResponse');
if ($performAllDestinationsResponse->length > 0) {
    $allDestinationsRes = $performAllDestinationsResponse->item(0)->getElementsByTagName('allDestinationsRes');
    if ($allDestinationsRes->length > 0) {
        $destinations = $allDestinationsRes->item(0)->getElementsByTagName('destinations');
        if ($destinations->length > 0) {
            for ($i=0; $i < $destinations->length; $i++) { 
                $cityCode = $destinations->item($i)->getElementsByTagName('cityCode');
                if ($cityCode->length > 0) {
                    $cityCode = $cityCode->item(0)->nodeValue;
                } else {
                    $cityCode = "";
                }
                $cityName = $destinations->item($i)->getElementsByTagName('cityName');
                if ($cityName->length > 0) {
                    $cityName = $cityName->item(0)->nodeValue;
                } else {
                    $cityName = "";
                }
                $countryCode = $destinations->item($i)->getElementsByTagName('countryCode');
                if ($countryCode->length > 0) {
                    $countryCode = $countryCode->item(0)->nodeValue;
                } else {
                    $countryCode = "";
                }
                $countryName = $destinations->item($i)->getElementsByTagName('countryName');
                if ($countryName->length > 0) {
                    $countryName = $countryName->item(0)->nodeValue;
                } else {
                    $countryName = "";
                }
                $stateCode = $destinations->item($i)->getElementsByTagName('stateCode');
                if ($stateCode->length > 0) {
                    $stateCode = $stateCode->item(0)->nodeValue;
                } else {
                    $stateCode = "";
                }
                $stateName = $destinations->item($i)->getElementsByTagName('stateName');
                if ($stateName->length > 0) {
                    $stateName = $stateName->item(0)->nodeValue;
                } else {
                    $stateName = "";
                }

                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('destinations');
                    $select->where(array(
                        'citycode' => $cityCode
                    ));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $result->buffer();
                    $customers = array();
                    if ($result->valid()) {
                        $data = $result->current();
                        $cityCode = (int)$data['citycode'];
                        if ($cityCode > 0) {
                            $sql = new Sql($db);
                            $data = array(
                                'citycode' => $cityCode,
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'cityname' => $cityName,
                                'countrycode' => $countryCode,
                                'countryname' => $countryName,
                                'statecode' => $stateCode,
                                'statename' => $stateName
                            );
                            $where['citycode = ?'] = $cityCode;
                            $update = $sql->update('destinations', $data, $where);
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('destinations');
                            $insert->values(array(
                                'citycode' => $cityCode,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'cityname' => $cityName,
                                'countrycode' => $countryCode,
                                'countryname' => $countryName,
                                'statecode' => $stateCode,
                                'statename' => $stateName
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
                        $insert->into('destinations');
                        $insert->values(array(
                            'citycode' => $cityCode,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'cityname' => $cityName,
                            'countrycode' => $countryCode,
                            'countryname' => $countryName,
                            'statecode' => $stateCode,
                            'statename' => $stateName
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
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>