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
echo "COMECOU CITIES<br/>";
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

$sql = "SELECT id FROM tbo_countries";
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
        $CountryCode = $row->id;

        $raw = '<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:hot="http://TekTravel/HotelBookingApi">
        <soap:Header xmlns:wsa="http://www.w3.org/2005/08/addressing" >
        <hot:Credentials UserName="wingstest" Password="Win@59491374">
        </hot:Credentials>
        <wsa:Action>http://TekTravel/HotelBookingApi/DestinationCityList</wsa:Action>
        <wsa:To>https://api.tbotechnology.in/hotelapi_v7/hotelservice.svc</wsa:To>
        </soap:Header>
        <soap:Body>
        <hot:DestinationCityListRequest>
        <hot:CountryCode>' . $CountryCode . '</hot:CountryCode>
        <hot:ReturnNewCityCodes>true</hot:ReturnNewCityCodes>
        </hot:DestinationCityListRequest>
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

        $config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
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
        $DestinationCityListResponse = $Body->item(0)->getElementsByTagName("DestinationCityListResponse");
        $CountryName = $DestinationCityListResponse->item(0)->getElementsByTagName("CountryName");
        if ($CountryName->length > 0) {
            $CountryName = $CountryName->item(0)->nodeValue;
        } else {
            $CountryName = "";
        }
        $CityList = $DestinationCityListResponse->item(0)->getElementsByTagName("CityList");
        $node = $CityList->item(0)->getElementsByTagName("City");
        if ($node->length > 0) {
            for ($i=0; $i < $node->length; $i++) { 
                $CityCode = $node->item($i)->getAttribute("CityCode");
                $CityName = $node->item($i)->getAttribute("CityName");
    
                echo $return;
                echo "CityCode: " . $CityCode;
                echo $return;

                try {
                    $sql = new Sql($db);
                    $select = $sql->select();
                    $select->from('tbo_cities');
                    $select->where(array(
                        'id' => $CityCode
                    ));
                    $statement = $sql->prepareStatementForSqlObject($select);
                    $result = $statement->execute();
                    $result->buffer();
                    $customers = array();
                    if ($result->valid()) {
                        $data = $result->current();
                        $id = (string)$data['id'];
                        if ($id != "") {
                            $config = new \Zend\Config\Config(include '../config/autoload/global.tbo.php');
                            $config = [
                                'driver' => $config->db->driver,
                                'database' => $config->db->database,
                                'username' => $config->db->username,
                                'password' => $config->db->password,
                                'hostname' => $config->db->hostname
                            ];
                            $dbUpdate = new \Zend\Db\Adapter\Adapter($config);
    
                            $data = array(
                                'datetime_created' => time(),
                                'datetime_updated' => 1,
                                'name' => $CityName,
                                'countrycode' => $CountryCode,
                                'countryname' => $CountryName
                            );
          
                            $sql    = new Sql($dbUpdate);
                            $update = $sql->update();
                            $update->table('tbo_cities');
                            $update->set($data);
                            $update->where(array('id' => $CityCode));
    
                            $statement = $sql->prepareStatementForSqlObject($update);
                            $results = $statement->execute();
                            $dbUpdate->getDriver()
                            ->getConnection()
                            ->disconnect(); 
                        } else {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('tbo_cities');
                            $insert->values(array(
                                'id' => $CityCode,
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'name' => $CityName,
                                'countrycode' => $CountryCode,
                                'countryname' => $CountryName
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
                        $insert->into('tbo_cities');
                        $insert->values(array(
                            'id' => $CityCode,
                            'datetime_created' => time(),
                            'datetime_updated' => 0,
                            'name' => $CityName,
                            'countrycode' => $CountryCode,
                            'countryname' => $CountryName
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>