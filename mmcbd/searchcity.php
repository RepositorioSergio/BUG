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
echo "COMECOU CITY<br/>";
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

$raw = '';
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
    "Content-length: ".strlen($raw)
));
$url = "http://www.mmcturismo.com/webapi/api/city/searchcity";

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

for ($i=0; $i < count($response); $i++) { 
    $idcidade = $response[$i]['idcidade'];
    $dscidade = $response[$i]['dscidade'];
    $iderp = $response[$i]['iderp'];
    $dsestado = $response[$i]['dsestado'];
    $idpais = $response[$i]['idpais'];
    $dspais = $response[$i]['dspais'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('cities');
        $insert->values(array(
            'idcidade' => $idcidade,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'dscidade' => $dscidade,
            'iderp' => $iderp,
            'dsestado' => $dsestado,
            'idpais' => $idpais,
            'dspais' => $dspais
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