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
echo "COMECOU CITIES";
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
$sql = "select value from settings where name='enabledidatravel' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_didatravel = $affiliate_id;
} else {
    $affiliate_id_didatravel = 0;
}
echo "<br/> affiliate_id_didatravel " . $affiliate_id_didatravel;
$sql = "select value from settings where name='didatravelclientid' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravelclientid = $row_settings['value'];
}
echo "<br/> didatravelclientid " . $didatravelclientid;
$sql = "select value from settings where name='didatravellicensekey' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $didatravellicensekey = $row_settings['value'];
}
echo "<br/> didatravellicensekey " . $didatravellicensekey;
$sql = "select value from settings where name='didatravelserviceurl' and affiliate_id=$affiliate_id_didatravel";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $didatravelserviceurl = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
    
$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

//COUNTRIES
$url1 = $didatravelserviceurl . "api/staticdata/GetCountryList?\$format=json";
echo $return;
echo $url1;
echo $return;
$raw1 = '{
    "Header": {
        "LicenseKey": "' . $didatravellicensekey . '",
        "ClientID": "' . $didatravelclientid . '"
    }
}';

$client1 = new Client();
$client1->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client1->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'X-Powered-By' => 'Zend Framework',
    'Content-Length' => strlen($raw1),
    'Content-Type' => 'application/x-www-form-urlencoded'
));
$client1->setUri($url1);
$client1->setMethod('POST');
$client1->setRawBody($raw1);
$response1 = $client1->send();
if ($response1->isSuccess()) {
     $response1 = $response1->getBody();
} else {
    $logger = new Logger();
    $writer = new Writer\Stream('/srv/www/htdocs/error_log');
    $logger->addWriter($writer);
    $logger->info($client1->getUri());
    $logger->info($response1->getStatusCode() . " - " . $response1->getReasonPhrase());
    echo $return;
    echo $response1->getStatusCode() . " - " . $response1->getReasonPhrase();
    echo $return;
    die();
}
$response1 = json_decode($response1, true);

$Success = $response1['Success'];
$Countries = $Success['Countries'];
$count = count($Countries);
echo "ANTES COUNTRIES " . $count;
for ($k=0; $k < count($Countries); $k++) { 
    $ISOCountryCode = $Countries[$k]['ISOCountryCode'];
echo "ISOCountryCode" . $ISOCountryCode;
echo "ANTES CITIES";
//CITIES
$url = $didatravelserviceurl . "api/staticdata/GetCityList?\$format=json";
echo $return;
echo $url;
echo $return;
$raw = '{
    "IncludeSubCity": false,
    "Header": {
        "LicenseKey": "' . $didatravellicensekey . '",
        "ClientID": "' . $didatravelclientid . '"
    },
    "CountryCode": "' . $ISOCountryCode . '"
}';
echo $return;
echo $raw;
echo $return;
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'X-Powered-By' => 'Zend Framework',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/x-www-form-urlencoded'
));
$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
echo "RESPONSE";
$response = $client->send();
if ($response->isSuccess()) {
    echo "ENTROU IF";
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
echo $return;
echo $response;
echo $return;
$response = json_decode($response, true);
echo "<xmp>";
var_dump($response);
echo "</xmp>"; 

$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$Success = $response['Success'];
$Cities = $Success['Cities'];

$count = count($Cities);
for ($i=0; $i < $count; $i++) { 
    $CityLongName_CN = $Cities[$i]['CityLongName_CN'];
    $CityName_CN = $Cities[$i]['CityName_CN'];
    $CityLongName = $Cities[$i]['CityLongName'];
    $CityName = $Cities[$i]['CityName'];
    $CityCode = $Cities[$i]['CityCode'];
    $CountryCode = $Cities[$i]['CountryCode'];

    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('didatravel_cities');
    $select->where(array(
        'CityCode' => $CityCode
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int) $data['CityCode'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'CityLongName_CN' => $CityLongName_CN,
                'CityName_CN' => $CityName_CN,
                'CityLongName' => $CityLongName,
                'CityName' => $CityName,
                'CityCode' => $CityCode,
                'CountryCode' => $CountryCode
                 );
                $where['CityCode = ?']  = $CityCode;
            $update = $sql->update('didatravel_cities', $data, $where);
            $db->getDriver()
            ->getConnection()
            ->disconnect();   
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('didatravel_properties');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'CityLongName_CN' => $CityLongName_CN,
                'CityName_CN' => $CityName_CN,
                'CityLongName' => $CityLongName,
                'CityName' => $CityName,
                'CityCode' => $CityCode,
                'CountryCode' => $CountryCode
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
        $insert->into('didatravel_cities');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'CityLongName_CN' => $CityLongName_CN,
            'CityName_CN' => $CityName_CN,
            'CityLongName' => $CityLongName,
            'CityName' => $CityName,
            'CityCode' => $CityCode,
            'CountryCode' => $CountryCode
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    }
 
} 
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>