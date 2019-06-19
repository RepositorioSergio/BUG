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
$config = new \Zend\Config\Config(include '../config/autoload/global.expedia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);
$apiKey = "503fvdcg1tm02jcebf6m5pqj8j";
$secret = "a7435jst471jn";
$timestamp = time();
$authorization = 'EAN APIKey=' . $apiKey . ',Signature=' . hash("sha512", $apiKey . $secret . $timestamp) . ',timestamp=' . time();
// echo $return;
// echo "authorization: " . $authorization;
// echo $return;
$ipaddress = '';
if ($_SERVER['HTTP_CLIENT_IP']) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
} else if ($_SERVER['HTTP_X_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_X_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
} else if ($_SERVER['HTTP_FORWARDED_FOR']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
} else if ($_SERVER['HTTP_FORWARDED']) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
} else if ($_SERVER['REMOTE_ADDR']) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
} else {
    $ipaddress = 'UNKNOWN';
    $ipaddress = "142.44.216.144";
}

// echo $return;
// echo "IP: " . $ipaddress;
// echo $return;

$token = bin2hex(random_bytes(64));
// echo $return;
// echo "TOKEN: " . $token;
// echo $return;

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Accept: application/json",
    "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
    "Authorization: " . $authorization,
    "Content-Type: application/json",
    "Accept-Encoding: gzip",
    "Customer-Ip: " . $ipaddress
));
$url = 'https://test.ean.com/2.2/properties/24051641/payment-options?token=REhZAQsABAMGQhMHUlNCF1ELUj1DVhdLBQMES1REClxLQg5cRR8WBlIBSxZdCFRpTQxRWVUFBVABXQdVHwJVAwEbA1cDCh0NVFcHSAMMVgZcBwJQBAIGVx8IVURWUAdWRDtZVWgTVgBXRgBccWBiJH0vdENSQkoRXA5WRGsVVhdKX19db1xXWQIMA1IMWQUDFAMLUFsVVQhZBUxQBQJTH1EHUQNSVwQHAwIGXhJQQUEVX1wHEGkLEwwGTF0DGwEKAhlXAEJFXEsSXVZdaw9RBAEBAwMBAVEGGQJSBgEZUVABUB9cUVwITAINUQhRUABUXQxbBRFUUQ1RFFQRA1M5TAwJUQlRAgRaGlYHSwUAMQVZFFJ5UQ8TC3YCV01TAg4-FgZYFlpGQhtqAlddUVhmY0dUUkcNAQ4IQFUOXFJSDFoLVggFW04ACU4BUB5EAEYRDFYUPhVBXVVRCFxnCwANVAkCVFQOHgATRUZWCFcdD2QxJUAGWQcFCAoTFgoFUlMLHQgJSAcCEnBfAQIJR0NdWAlVBlUFAFsMUFM=';
// echo $return;
// echo $url;
// echo $return;
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
die();

$response = json_decode($response, true);
if ($response === false || $response === null) {
    echo $return;
    echo "NOT DECODE";
    echo $return;
}

if (json_last_error() == 0) {
    echo '- Nao houve erro! O parsing foi perfeito';
} else {
    echo 'Erro!<br/>';
    switch (json_last_error()) {
        
        case JSON_ERROR_DEPTH:
            echo ' - profundidade maxima excedida';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - state mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Caracter de controle encontrado';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Erro de sintaxe! String JSON mal-formada!';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Erro na codificação UTF-8';
            break;
        default:
            echo ' – Erro desconhecido';
            break;
    }
}

// echo "<xmp>";
// var_dump($response);
// echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.expedia.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$night = array();
$occupancie = array();

$affiliate_collect = $response['affiliate_collect'];
$name = $affiliate_collect['name'];

$credit_card = $response['credit_card'];
$name_credit_card = $credit_card['name'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('paymenttypes');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'name' => $name,
        'name_credit_card' => $name_credit_card
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

$card_options = $credit_card['card_options'];

for ($i = 0; $i < count($card_options); $i ++) {
    $name = $card_options[$i]['name'];
    $card_type = $card_options[$i]['card_type'];
    $processing_country = $card_options[$i]['processing_country'];
    echo $return;
    echo "name: " . $name;
    echo $return;
    
    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('card_options');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'name' => $name,
            'card_type' => $card_type,
            'processing_country' => $processing_country
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>