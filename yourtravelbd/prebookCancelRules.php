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

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];


$url = 'http://xml.youtravel.com/webservices/get_canx_policy.asp?token=yyeypbiawipgwwaiaepyjypypaeapaqapawwegej';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'text/xml;charset=ISO-8859-1',
    'Content-Length' => '0'
));
$client->setUri($url);
$client->setMethod('POST');
//$client->setRawBody($raw);
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

echo "RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>';

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
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
$HtSearchRq = $inputDoc->getElementsByTagName("HtSearchRq");

$Room = $HtSearchRq->item(0)->getElementsByTagName("Room");
if ($Room->length > 0) {
    $Room = $Room->item(0)->nodeValue;
} else {
    $Room = "";
}

$Policies = $HtSearchRq->item(0)->getElementsByTagName("Policies");
if ($Policies->length > 0) {
    $Policy = $Policies->item(0)->getElementsByTagName("Policy");
    if ($Policy->length > 0) {
        for ($i=0; $i < $Policy->length; $i++) { 
            $FromDate = $Policy->item($i)->getElementsByTagName("FromDate");
            if ($FromDate->length > 0) {
                $FromDate = $FromDate->item(0)->nodeValue;
            } else {
                $FromDate = "";
            }
            $Fees = $Policy->item($i)->getElementsByTagName("Fees");
            if ($Fees->length > 0) {
                $Fees = $Fees->item(0)->nodeValue;
            } else {
                $Fees = "";
            }
            $Currency = $Policy->item($i)->getElementsByTagName("Currency");
            if ($Currency->length > 0) {
                $Currency = $Currency->item(0)->nodeValue;
            } else {
                $Currency = "";
            }
        }
    }
}

/* try {
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('hoteis');
    $select->where(array(
        'id' => $id
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = (int)$data['id'];
        if ($id > 0) {
            $sql = new Sql($db);
            $data = array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
            );
            $where['id = ?'] = $id;
            $update = $sql->update('hoteis', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('hoteis');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
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
        $insert->into('hoteis');
        $insert->values(array(
            'id' => $id,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'city' => $city,
            'country' => $country,
            'recomended' => $recomended,
            'stars' => $stars
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
} */

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>