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

$raw = '{
    "version": "1",
    "browser_accept_header": "*/*",
    "encoded_browser_metadata": "ZW5jb2RlZF9icm93c2VyX21ldGFkYXRh",
    "preferred_challenge_window_size": "medium",
    "merchant_url": "https://server.adomainname.net",
    "customer_account_details": {
      "authentication_method": "guest",
      "authentication_timestamp": "2019-06-18T12:10:00.000Z",
      "create_date": "2019-09-15",
      "change_date": "2019-09-17",
      "password_change_date": "2019-09-17",
      "add_card_attempts": 1,
      "account_purchases": 1
    },
    "payments": [
      {
        "type": "customer_card",
        "card_type": "VI",
        "number": "4111111111111111",
        "security_code": "123",
        "expiration_month": "08",
        "expiration_year": "2025",
        "billing_contact": {
          "given_name": "John",
          "family_name": "Smith",
          "email": "smith@example.com",
          "phone": "4875550077",
          "address": {
            "line_1": "555 1st St",
            "line_2": "10th Floor",
            "line_3": "Unit 12",
            "city": "Seattle",
            "state_province_code": "WA",
            "postal_code": "98121",
            "country_code": "US"
          }
        },
        "enrollment_date": "2019-09-15"
      }
    ]
  }';

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
$url = 'https://test.ean.com/2.2/payment-sessions?token=OjogZxwwUV0aMTJQNmNjXzJXBVMJFFIAS1cMez8GUwlfHFYKGAhTKTcxaiBmQgYCXFUNXFEFTgMDWFsZBlYOAhlUBlcGHlQHVAQAB1IHV1UPCW8RDFUEBABVUlMYDgRaA0kCUFNTHlsMDVxMVQ0AWFxTBFJUD1oEUWUGBwdQBVJTEgEDVgQDAFJZSwZXUAUeDQUMCE8AAg1SGFYKA1YHBAJQAgMFUBAwAFEBV04yBLhgMQHwYzNRVAIGU_8wPwIZeF4MUER0XFzDNRZoT0RETFheFlxMSBFZBRgSF1tOABFfQHkNCwISTUFMGkFRRwtBc11WA3VWUEFJO51kZ4VnOldQ9jV7AAVSDksBUBkIXGFRCVsMXQ0GChkDUFU4_TlhCVYZWwLSMWPdYDnJNWfLMWCxMTbsZzKoZDcCfBun';
// echo $return;
// echo $url;
// echo $return;
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

echo $return;
echo $response;
echo $return;

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

$payment_session_id = $response['payment_session_id'];
$encoded_init_config = $response['encoded_init_config'];
$links = $response['links'];
$book = $links['book'];
$method = $book['method'];
$href = $book['href'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('registerpayment');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'payment_session_id' => $payment_session_id,
        'encoded_init_config' => $encoded_init_config,
        'method' => $method,
        'href' => $href
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>