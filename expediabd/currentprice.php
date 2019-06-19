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

$url = 'https://test.ean.com/2.2/properties/availability/24051641/rooms/217206336/rates/268026852/price-check?token=OjogZxwwUV0aMTJQNmNjXzJXBVMJFFIAS1cMez8GUwlfHFYKGAhTKTcxaiBmQgYCXFUNXFEFTgMDWFsZBlYOAhlUBlcGHlQHVAQAB1IHV1UPCW8RDFUEBABVUlMYDgRaA0kCUFNTHlsMDVxMVQ0AWFxTBFJUD1oEUWUGBwdQBVJTEgEDVgQDAFJZSwZXUAUeDQUMCE8AAg1SGFYKA1YHBAJQAgMFUBAwAFEBV04yBLhgMQHwYzNRVAIGU_8wPwIZeF4MUER0XFzDNRZoT0RETFheFlxMSBFZBRgSF1tOABFfQHkNCwISTUFMGkFRRwtBc11WA3VWUEFJO51kZ4VnOldQ9jV7AAVSDksBUBkIXGFRCVsMXQ0GChkDUFU4_TlhCVYZWwLSMWPdYDnJNWfLMWCxMTbsZzKoZDcCfBun';
// echo $return;
// echo $url;
// echo $return;


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept: application/json",
    "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
    "Accept-Encoding: gzip",
    "Authorization: " . $authorization,
    "Customer-Ip: " . $ipaddress
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
$headers = curl_getinfo($ch);

if ($error != "") {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "NAO TEM ERROS.";
    echo $return;
}
curl_close($ch);

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

$status = $response['status'];
$links = $response['links'];
$book = $links['book'];
$method = $book['method'];
$href = $book['href'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('currentprice');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'status' => $status,
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

$occupancies = $response['occupancies'];
foreach ($occupancies as $key => $value) {
    $occupancie = $occupancies[$key];
    $nightly = $occupancie['nightly'];
    for ($kA=0; $kA < count($nightly); $kA++) { 
        $night = $nightly[$kA];
        for ($kB=0; $kB < count($night); $kB++) { 
            $type = $night[$kB]['type'];
            $value = $night[$kB]['value'];
            $currency = $night[$kB]['currency'];

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('nightlyCP');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'type' => $type,
                    'value' => $value,
                    'currency' => $currency
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

    $stay = $occupancie['stay'];
    for ($x=0; $x < count($stay); $x++) { 
        $type = $stay[$x]['type'];
        $value = $stay[$x]['value'];
        $currency = $stay[$x]['currency'];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('stayCP');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'type' => $type,
                'value' => $value,
                'currency' => $currency
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error 3: " . $e;
            echo $return;
        }
    }

    $fees = $occupancie['fees'];
    $mandatory_fee = $fees['mandatory_fee'];
    $billable_currency = $mandatory_fee['billable_currency'];
    $value = $billable_currency['value'];
    $currency = $billable_currency['currency'];
    $request_currency = $mandatory_fee['request_currency'];
    $valueRC = $request_currency['value'];
    $currencyRC = $request_currency['currency'];

    $totals = $occupancie['totals'];
    $marketing_fee = $totals['marketing_fee'];
    $billable_currency = $marketing_fee['billable_currency'];
    $valueBMFee = $billable_currency['value'];
    $currencyBMFee = $billable_currency['currency'];
    $request_currency = $marketing_fee['request_currency'];
    $valueRMFee = $request_currency['value'];
    $currencyRMFee = $request_currency['currency'];

    $inclusive = $totals['inclusive'];
    $billable_currency = $inclusive['billable_currency'];
    $valueBInclusive = $billable_currency['value'];
    $currencyBInclusive = $billable_currency['currency'];
    $request_currency = $inclusive['request_currency'];
    $valueRInclusive = $request_currency['value'];
    $currencyRInclusive = $request_currency['currency'];

    $exclusive = $totals['exclusive'];
    $billable_currency = $exclusive['billable_currency'];
    $valueBExclusive = $billable_currency['value'];
    $currencyBExclusive  = $billable_currency['currency'];
    $request_currency = $exclusive['request_currency'];
    $valueRExclusive = $request_currency['value'];
    $currencyRExclusive = $request_currency['currency'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('feesCP');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'value' => $value,
            'currency' => $currency,
            'valueRC' => $valueRC,
            'currencyRC' => $currencyRC,
            'valueBMFee' => $valueBMFee,
            'currencyBMFee' => $currencyBMFee,
            'valueRMFee' => $valueRMFee,
            'currencyRMFee' => $currencyRMFee,
            'valueBInclusive' => $valueBInclusive,
            'currencyBInclusive' => $currencyBInclusive,
            'valueRInclusive' => $valueRInclusive,
            'currencyRInclusive' => $currencyRInclusive,
            'valueBExclusive' => $valueBExclusive,
            'currencyBExclusive' => $currencyBExclusive,
            'valueRExclusive' => $valueRExclusive,
            'currencyRExclusive' => $currencyRExclusive,
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error 4: " . $e;
        echo $return;
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>