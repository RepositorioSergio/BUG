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
echo "COMECOU BREAKFAST";
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

$raw = '{
    "Header": {
        "LicenseKey": "' . $didatravellicensekey . '",
        "ClientID": "' . $didatravelclientid . '"
    }
}';


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
$url = $didatravelserviceurl . "api/staticdata/GetBreakfastTypeList?\$format=json";
echo "<br/> url " . $url;
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
    echo "<br/>" . $response->getStatusCode() . " - " . $response->getReasonPhrase();
    echo $return;
    die();
}
echo $return;
$response = json_decode($response, true);
echo $return;

$config = new \Zend\Config\Config(include '../config/autoload/global.didatravel.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$id = 0;
$Success = $response['Success'];
$Breakfasts = $Success['Breakfasts'];
for ($i = 0; $i < count($Breakfasts); $i++) {
    $ID = $Breakfasts[$i]['ID'];
    $Name_CN = $Breakfasts[$i]['Name_CN'];
    $Name = $Breakfasts[$i]['Name'];
    echo "<br/>" . $Name;
    
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('didatravel_breakfasts');
    $select->where(array(
        'ID' => $ID
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    echo "<br/> ANTES VALID";
    if ($result->valid()) {
        $data = $result->current();
        $id = (int) $data['ID'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'ID' => $ID,
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'Name_CN' => $Name_CN,
                'Name' => $Name
            );
            $where['ID = ?'] = $ID;
            $update = $sql->update('didatravel_breakfasts', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('didatravel_breakfasts');
            $insert->values(array(
                'ID' => $ID,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'Name_CN' => $Name_CN,
                'Name' => $Name
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
        $insert->into('didatravel_breakfasts');
        $insert->values(array(
            'ID' => $ID,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'Name_CN' => $Name_CN,
            'Name' => $Name
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

