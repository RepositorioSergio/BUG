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
echo "COMECOU CANCELAR RESERVA<br/>";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$raw = '{	
    "tokenBooking":"bHdBQUFCK0xDQUFBQUFBQUJBQmRqTHNLQWpFUVJmOWw2a1JtOG5hNjdiYlhUaXlDbXlJUURDUWJHOWwvTjRVZzJCeTRCKzU1UTk1YTZxbTlJckJHWjEwUXNNVjlMbEJJWjRsR0tuTWxaUEtzN2NrR0g4Z0VpWm9SUWN6MzZDTzJYSUZKT1RRQ3ZyVysxajBWNE9jbzVTZVgxRkw5bDVmSi9LZ2QrRWJXZUUvMzR3T25UeWlsbHdBQUFBPT0"
}
';

$token = 'e_-3xLPVRsteK2CjsNhGtzPy26RYqDle1QUIFYG1HJM8oGNTbYGwXywWU_8KANfVlbNqLdB8d6lpCsjSBSDhaEnVqai-Gt-7my7y7ON6taCHuwASiADLhLmoUi4V17DuU6chNGG5WDXvmOf-YmL_RjRL-j87v6LwwdjKFCN6uP5TRygD1_6MxGbxN2H-NvuThQOvAl6M9ELpdUethw5YPzEjPmi_jcaDjB_tJIfv8kQ-qy6I81xagf8VnI-7KsqYbhJkiEdgOsfLalfQidId46_nRKF3tNtV1HXAB4yi2uTTxFtj-YaENdXh4P4sPM3g-krF2rLdxeLGqaYB7F_YIisZXem1nSS1J5QHGFVFUsGHdPfy0DyP9oC27kbzzYirfgILRKFcKJC9aOEzpDlbpt7Z8iZ24rYkbP3UBtkBfr1fPNDV4Uy7FodLAYLaTHiVuKlOp1QJh_e_ouwsaHwKWYi9DictdaW5_xjNMxwrELkhEGpxxnHWMkKO-dSXfhP1rt3KbkXY7LxaBQxwgLxCYjtQeqeG9SL-JkcBQzgKOkg';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: application/json",
    "Authorization: Bearer " . $token,
    "Content-length: " . strlen($raw)
));
$url = "http://www.mmcturismo.com/webapi/api/servico/servicocancelareserva";

/* $client->setUri($url);
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
} */ 

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url );
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 65000);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "Authorization: Bearer " . $token,
    "Content-length: " . strlen($raw)
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$error = curl_error($ch);
if ($response === false) {
    echo $return;
    echo "ERRO: " . $error;
    echo $return;
} else {
    echo $return;
    echo "NAO TEM ERRO";
    echo $return;
}

$headers = curl_getinfo($ch);
curl_close($ch);


$response = json_decode($response, true);
echo "<br/>RESPONSE";
echo '<xmp>';
var_dump($response);
echo '</xmp>'; 

$config = new \Zend\Config\Config(include '../config/autoload/global.mmc.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$sucesso = $response['sucesso'];

$mensagem = '';
$mensagens = $response['mensagens'];
for ($i=0; $i < count($mensagens); $i++) { 
    $mensagem = $mensagens[$i];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('cancelareserva');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'mensagem' => $mensagem,
            'sucesso' => $sucesso
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error: " . $e;
        echo $return;
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>