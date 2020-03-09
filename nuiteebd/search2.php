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

$sql = "SELECT citycode FROM destinations";
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
        $citycode = $row->citycode;

        $url = 'http://sandbox.nuitee.com/nuitee/Nuitee?wsdl';

        $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:nuit="http://www.nuitee.ma">
        <soapenv:Header/>
        <soapenv:Body>
        <nuit:performGetCityAvailability>
            <getCityAvailabilityReq>
                <login>
                    <language>en</language>
                    <password>Paulotest777</password>
                    <userName>Paulotest</userName>
                </login>
                <sessionId></sessionId>
                <checkInDate>2020-11-20</checkInDate>
                <checkOutDate>2020-11-27</checkOutDate>
                <cityCode>' . $citycode . '</cityCode>
                <hotelCodes></hotelCodes>
                <roomGuests>
                    <roomGuests>
                    <adultCount>2</adultCount>
                    <childCount>0</childCount>
                    </roomGuests>
                </roomGuests>
                <currency>USD</currency>
                <languageCode>en</languageCode>
                <timeout>1555</timeout>
            </getCityAvailabilityReq>
        </nuit:performGetCityAvailability>
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
        $performGetCityAvailabilityResponse = $Body->item(0)->getElementsByTagName('performGetCityAvailabilityResponse');
        if ($performGetCityAvailabilityResponse->length > 0) {
            $getCityAvailabilityRes = $performGetCityAvailabilityResponse->item(0)->getElementsByTagName('getCityAvailabilityRes');
            if ($getCityAvailabilityRes->length > 0) {
                $sessionId = $getCityAvailabilityRes->item(0)->getElementsByTagName('sessionId');
                if ($sessionId->length > 0) {
                    $sessionId = $sessionId->item(0)->nodeValue;
                } else {
                    $sessionId = "";
                }
                $checkInDate = $getCityAvailabilityRes->item(0)->getElementsByTagName('checkInDate');
                if ($checkInDate->length > 0) {
                    $checkInDate = $checkInDate->item(0)->nodeValue;
                } else {
                    $checkInDate = "";
                }
                $checkOutDate = $getCityAvailabilityRes->item(0)->getElementsByTagName('checkOutDate');
                if ($checkOutDate->length > 0) {
                    $checkOutDate = $checkOutDate->item(0)->nodeValue;
                } else {
                    $checkOutDate = "";
                }
                $currency = $getCityAvailabilityRes->item(0)->getElementsByTagName('currency');
                if ($currency->length > 0) {
                    $currency = $currency->item(0)->nodeValue;
                } else {
                    $currency = "";
                }
                if ($currency != "") {
                    echo $return;
                    echo "CITYCODE: " . $citycode;
                    echo $return;
                    echo "FIM";
                    die();
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
