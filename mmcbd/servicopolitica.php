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
echo "COMECOU POLITICA<br/>";
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

$raw = '{	"tokenServico":"REFJQUFCK0xDQUFBQUFBQUJBQjFrVTFQd3pBTWh2OUtsQk9UOXRFUFlLeWFKaFcyUThYR3FuNmNnRVBVWk1VaVMwclM3Z0RpditOVWJBTUpwQnpzMTQrZDEvSUhCVzZGT1VDbGFVVDkyZVdVRHFuYW42V2tpSk8wSkNPU2xxdXMySkw3dUNqaVRaekZHM0lSeTMwbnpMc2VFSEtSaWJxVHpBeXduZHNXbXI1WDFVWllxMjB2N3JYZ0ROWGJiSTM1UVo3K0NLNkNzWGNpdElFYUZKTklsdm55SjZsRWkzQndQYjdwYVM1c1phQmk3cWQ1czhoQkVhRmF3emdqWEVqU01QUFdpZkY4MGl5ZVZHOEFWQ3Rxckd1REhkc2NOZUFWY01ZRmpjSnc2cEJqcWpvcE1XOUJRUVc5bDhEelp5TS9HQVZCNFhsUi85elFkdmZ0OVZRUGY5YzUyRVlyT0Fpd05IcjhaOHhmemM5NEJ6U0xLNGN1cWw0NEx1OGlVRHNhK1gza3pPSllHZzc5b1RzYzdPdno1Y3BzdmR6bVNiRktzK1RoTGtuajlhUnJwR2JjVGhCdDlhdFErUkYyKzM1K0FRbGxRZDBNQWdBQQ",
    "tokenPesquisa":"S2dFQUFCK0xDQUFBQUFBQUJBQmRqejBPd2pBTWhlL2lPVWhwT2xUTkRkZ1kyQkNEVmFmVUlqZ2xTU2ZFM1VtS1ZINHNEL2IzbnBMbkJ6QU5URWdPYk50MkNsS081RkptQ1dCbDhWNUI1am44SWNhTTZHS1lROHdicE9MZ2dkR0RCYU9iZnRlWW5URkhyZTNhVUIwank2L2VmdXUzNEFobnBJamJveEtSY29sV3AyRWlzS1pPTENQWXB1U293ZFBLVDYxcVZIZXU2b3lYOHMxcUtFdjQ3RnBYNGxrbVRHRDdVdVc2aUpKd3lCeGtUOXZKNGVyazROSjk0WVJ2K0h3QjRPTFRvU29CQUFBPQ",
    "dataServico":"2019-12-22"
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
$url = "http://www.mmcturismo.com/webapi/api/servico/servicopolitica";

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

$PrazoCancelamento = $response['PrazoCancelamento'];
$DataServico = $response['DataServico'];
$ValidadePolitica = $response['ValidadePolitica'];
$PoliticaCancelamento = $response['PoliticaCancelamento'];
$PoliticaValida = $response['PoliticaValida'];
$TemCustoCancelamento = $response['TemCustoCancelamento'];
$TokenServico = $response['TokenServico'];
$TokenPolitica = $response['TokenPolitica'];


try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('servicos_politica');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'PrazoCancelamento' => $PrazoCancelamento,
        'DataServico' => $DataServico,
        'ValidadePolitica' => $ValidadePolitica,
        'PoliticaCancelamento' => $PoliticaCancelamento,
        'PoliticaValida' => $PoliticaValida,
        'TemCustoCancelamento' => $TemCustoCancelamento,
        'TokenServico' => $TokenServico,
        'TokenPolitica' => $TokenPolitica
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>