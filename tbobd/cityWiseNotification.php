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
echo "COMECOU CITYWISE<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$user = 'wingstest';
$pass = 'Win@59491374';

$raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
<soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing">
    <hot:Credentials UserName="' . $user . '" Password="' . $pass . '">
    </hot:Credentials>
    <wsa:Action>http://TekTravel/HotelBookingApi/GiataHotelCodes</wsa:Action>
    <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
</soap:Header>
<soap:Body>
    <hot:CityWiseNotificationRequest/>
</soap:Body>
</soap:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: application/soap+xml; charset=utf-8",
    "Content-length: ".strlen($raw)
));
$url =  "https://api.tbotechnology.in/HotelAPI_V7/HotelService.svc";

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
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();

$config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$hotelFacil = '';
$Attr = '';

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$CityWiseNotificationResponse = $Body->item(0)->getElementsByTagName("CityWiseNotificationResponse");

$CityWiseNotifications = $CityWiseNotificationResponse->item(0)->getElementsByTagName("CityWiseNotifications");
if ($CityWiseNotifications->length > 0) {
    $CityWiseNotification = $CityWiseNotifications->item(0)->getElementsByTagName("CityWiseNotification");
    if ($CityWiseNotification->length > 0) {
        for ($i=0; $i < $CityWiseNotification->length; $i++) { 
            $CityCode = $CityWiseNotification->item($i)->getAttribute("CityCode");
            $CityName = $CityWiseNotification->item($i)->getAttribute("CityName");
            $CountryCode = $CityWiseNotification->item($i)->getAttribute("CountryCode");
            $CountryName = $CityWiseNotification->item($i)->getAttribute("CountryName");
            $Caption = $CityWiseNotification->item($i)->getAttribute("Caption");
            $Text = $CityWiseNotification->item($i)->getAttribute("Text");


            try {
                $sql = new Sql($db);
                $select = $sql->select();
                $select->from('CityWiseNotifications');
                $select->where(array(
                    'CityCode' => $CityCode
                ));
                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();
                $result->buffer();
                $customers = array();
                if ($result->valid()) {
                    $data = $result->current();
                    $id =  $data['CityCode'];
                    if ($id->length > 0) {
                        $sql = new Sql($db);
                        $data = array(
                            'CityCode' => $CityCode,
                            'datetime_created' => time(),
                            'datetime_updated' => 1,
                            'CityName' => $CityName,
                            'CountryCode' => $CountryCode,
                            'CountryName' => $CountryName,
                            'Caption' => $Caption,
                            'Text' => $Text
                            );
                            $where['CityCode = ?']  = $CityCode;
                        $update = $sql->update('CityWiseNotifications', $data, $where);
                        $db->getDriver()
                        ->getConnection()
                        ->disconnect();   
                    } else {
                        $sql = new Sql($db);
                        $insert = $sql->insert();
                        $insert->into('CityWiseNotifications');
                        $insert->values(array(
                            'CityCode' => $CityCode,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'CityName' => $CityName,
                            'CountryCode' => $CountryCode,
                            'CountryName' => $CountryName,
                            'Caption' => $Caption,
                            'Text' => $Text
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
                    $insert->into('CityWiseNotifications');
                    $insert->values(array(
                        'CityCode' => $CityCode,
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'CityName' => $CityName,
                        'CountryCode' => $CountryCode,
                        'CountryName' => $CountryName,
                        'Caption' => $Caption,
                        'Text' => $Text
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                }
            } catch (\Exception $e) {
                echo $return;
                echo "ERRO: " . $e;
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