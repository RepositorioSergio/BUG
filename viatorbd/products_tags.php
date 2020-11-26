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
// echo "COMECOU CITIES";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = 'https://api.viator.com/partner/products/tags';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Content-Type' => 'application/json',
    'exp-api-key' => '5364bbaf-e4f7-4727-9e91-317e794dfbaa',
    'Accept-Language' => 'en-US',
    'Accept' => 'application/json;version=2.0'
));
$client->setUri($url);
$client->setMethod('GET');
// $client->setRawBody($raw);
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

echo $return;
echo $response;
echo $return;

$response = json_decode($response, true);

$config = new \Zend\Config\Config(include '../config/autoload/global.viator.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$tags = $response['tags'];
if (count($tags) > 0) {
    for ($j=0; $j < count($tags); $j++) { 
        $tagId = $tags[$j]['tagId'];
        $parentTagIds = $tags[$j]['parentTagIds'];
        if (count($parentTagIds) > 0) {
            $parentTagId = "";
            for ($i=0; $i < count($parentTagIds); $i++) { 
                $parentTagId = $parentTagIds[$i];

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('viator_productstags_parenttagids');
                    $insert->values(array(
                        'datetime_updated' => time(),
                        'parenttagid' => $parentTagId
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                    ->getConnection()
                    ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "Error 2: " . $e;
                    echo $return;
                }
            }
        }
        $allNamesByLocale = $tags[$j]['allNamesByLocale'];
        $de = $allNamesByLocale['de'];
        $no = $allNamesByLocale['no'];
        $sv = $allNamesByLocale['sv'];
        $pt = $allNamesByLocale['pt'];
        $en_AU = $allNamesByLocale['en_AU'];
        $en = $allNamesByLocale['en'];
        $it = $allNamesByLocale['it'];
        $fr = $allNamesByLocale['fr'];
        $en_UK = $allNamesByLocale['en_UK'];
        $es = $allNamesByLocale['es'];
        $zh = $allNamesByLocale['zh'];
        $zh_HK = $allNamesByLocale['zh_HK'];
        $zh_TW = $allNamesByLocale['zh_TW'];
        $ja = $allNamesByLocale['ja'];
        $zh_CN = $allNamesByLocale['zh_CN'];
        $da = $allNamesByLocale['da'];
        $nl = $allNamesByLocale['nl'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('viator_productstags');
            $insert->values(array(
                'datetime_updated' => time(),
                'tagid' => $tagId,
                'de' => $de,
                'no' => $no,
                'sv' => $sv,
                'pt' => $pt,
                'en_au' => $en_AU,
                'en' => $en,
                'it' => $it,
                'fr' => $fr,
                'en_uk' => $en_UK,
                'es' => $es,
                'zh' => $zh,
                'zh_hk' => $zh_HK,
                'zh_tw' => $zh_TW,
                'ja' => $ja,
                'zh_cn' => $zh_CN,
                'da' => $da,
                'nl' => $nl
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 1: " . $e;
            echo $return;
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>