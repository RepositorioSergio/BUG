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
   <nuit:performHotelList>
      <!--Optional:-->
      <hotelListReq>
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
         <cityId>2686</cityId>
      </hotelListReq>
   </nuit:performHotelList>
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
$performHotelListResponse = $Body->item(0)->getElementsByTagName('performHotelListResponse');
if ($performHotelListResponse->length > 0) {
    $hotelListRes = $performHotelListResponse->item(0)->getElementsByTagName('hotelListRes');
    if ($hotelListRes->length > 0) {
        $hotelInfo = $hotelListRes->item(0)->getElementsByTagName('hotelInfo');
        if ($hotelInfo->length > 0) {
            for ($i=0; $i < $hotelInfo->length; $i++) { 
                $hotelCode = $hotelInfo->item($i)->getElementsByTagName('hotelCode');
                if ($hotelCode->length > 0) {
                    $hotelCode = $hotelCode->item(0)->nodeValue;
                } else {
                    $hotelCode = "";
                }
                $hotelName = $hotelInfo->item($i)->getElementsByTagName('hotelName');
                if ($hotelName->length > 0) {
                    $hotelName = $hotelName->item(0)->nodeValue;
                } else {
                    $hotelName = "";
                }
                $hotelAddress = $hotelInfo->item($i)->getElementsByTagName('hotelAddress');
                if ($hotelAddress->length > 0) {
                    $hotelAddress = $hotelAddress->item(0)->nodeValue;
                } else {
                    $hotelAddress = "";
                }
                $hotelPictureUrl = $hotelInfo->item($i)->getElementsByTagName('hotelPictureUrl');
                if ($hotelPictureUrl->length > 0) {
                    $hotelPictureUrl = $hotelPictureUrl->item(0)->nodeValue;
                } else {
                    $hotelPictureUrl = "";
                }
                $latitude = $hotelInfo->item($i)->getElementsByTagName('latitude');
                if ($latitude->length > 0) {
                    $latitude = $latitude->item(0)->nodeValue;
                } else {
                    $latitude = "";
                }
                $longitude = $hotelInfo->item($i)->getElementsByTagName('longitude');
                if ($longitude->length > 0) {
                    $longitude = $longitude->item(0)->nodeValue;
                } else {
                    $longitude = "";
                }
                $starRating = $hotelInfo->item($i)->getElementsByTagName('starRating');
                if ($starRating->length > 0) {
                    $starRating = $starRating->item(0)->nodeValue;
                } else {
                    $starRating = "";
                }

                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('hotels');
                    $select->where(array(
                        'hotelcode' => $hotelCode
                    ));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $result->buffer();
                    $customers = array();
                    if ($result->valid()) {
                        $data = $result->current();
                        $hotelCode = (int)$data['hotelcode'];
                        if ($hotelCode > 0) {
                            $sql = new Sql($db);
                            $data = array(
                                'hotelcode' => $hotelCode,
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'hotelname' => $hotelName,
                                'hoteladdress' => $hotelAddress,
                                'hotelpictureurl' => $hotelPictureUrl,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'starrating' => $starRating
                            );
                            $where['hotelcode = ?'] = $hotelCode;
                            $update = $sql->update('hotels', $data, $where);
                            $db->getDriver()
                                ->getConnection()
                                ->disconnect();
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('hotels');
                            $insert->values(array(
                                'hotelcode' => $hotelCode,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'hotelname' => $hotelName,
                                'hoteladdress' => $hotelAddress,
                                'hotelpictureurl' => $hotelPictureUrl,
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'starrating' => $starRating
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
                        $insert->into('hotels');
                        $insert->values(array(
                            'hotelcode' => $hotelCode,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'hotelname' => $hotelName,
                            'hoteladdress' => $hotelAddress,
                            'hotelpictureurl' => $hotelPictureUrl,
                            'latitude' => $latitude,
                            'longitude' => $longitude,
                            'starrating' => $starRating
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