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
echo "COMECOU PROPERTY";
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
/*
 * $raw = '{ "usuario" : "' . $specialtourspackagesuser . '", "password" : "' . $specialtourspackagespassword . '", "agencia" : "' . $specialtourspackagesagency . '" }';
 * $client = new Client();
 * $client->setOptions(array(
 * 'timeout' => 100,
 * 'sslverifypeer' => false,
 * 'sslverifyhost' => false
 * ));
 * $client->setHeaders(array(
 * 'Accept-Encoding' => 'gzip,deflate',
 * 'X-Powered-By' => 'Zend Framework',
 * 'Content-Length' => strlen($raw),
 * 'Content-Type' => 'application/x-www-form-urlencoded'
 * ));
 * $url = $specialtourspackagesserviceURL . "v1/clientes/login";
 * $client->setUri($url);
 * $client->setMethod('POST');
 * $client->setRawBody($raw);
 * $response = $client->send();
 * if ($response->isSuccess()) {
 * $response = $response->getBody();
 * } else {
 * $logger = new Logger();
 * $writer = new Writer\Stream('/srv/www/htdocs/error_log');
 * $logger->addWriter($writer);
 * $logger->info($client->getUri());
 * $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
 * echo $return;
 * echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
 * echo $return;
 * die();
 * }
 * $response = json_decode($response, true);
 * $token = $response['token'];
 * echo $return;
 * echo $token;
 * echo $return;
 */
$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$url = $didatravelserviceurl . "api/staticdata/GetPropertyCategoryList?\$format=json";
echo $return;
echo $url;
echo $return;
$raw = '{
    "Header": {
        "LicenseKey": "' . $didatravellicensekey . '",
        "ClientID": "' . $didatravelclientid . '"
    }
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
$PropertyCategorys = $Success['PropertyCategorys'];

$count = count($PropertyCategorys);
for ($i = 0; $i < $count; $i ++) {
    $ID = $PropertyCategorys[$i]['ID'];
    $Description_CN = $PropertyCategorys[$i]['Description_CN'];
    $Description_EN = $PropertyCategorys[$i]['Description_EN'];
    
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('didatravel_properties');
    $select->where(array(
        'ID' => $ID
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int) $data['ID'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'ID' => $ID,
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'Description_CN' => $Description_CN,
                'Description_EN' => $Description_EN
            );
            $where['ID = ?'] = $ID;
            $update = $sql->update('didatravel_properties', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('didatravel_properties');
            $insert->values(array(
                'ID' => $ID,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Description_CN' => $Description_CN,
                'Description_EN' => $Description_EN
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
        $insert->into('didatravel_properties');
        $insert->values(array(
            'ID' => $ID,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Description_CN' => $Description_CN,
            'Description_EN' => $Description_EN
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>

