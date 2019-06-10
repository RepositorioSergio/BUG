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
echo "COMECOU BUSCA<br/>";
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

$raw = '{	"idcidade":337,
	"strdestino":null,
	"tipodestino":null,
	"iataaeroporto":null,
	"dtinicial":"2019-12-22",
	"dtfinal":"2019-12-23",
	"moedapadrao":null,
	"nradt":3,
	"nrchd":2,
	"nrinf":1,
	"idadeschd":[3,1,7],
	"nrpagina":1,
	"nrporpagina":100,
	"nrlinhas":9999,
	"transactionId":null,
	"tokenPesquisa":null
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
$url = "http://www.mmcturismo.com/webapi/api/servico/servicobusca";

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

$transactionId = $response['transactionId'];
$tokenPesquisa = $response['tokenPesquisa'];
$pesquisa = $response['pesquisa'];
$idcidade = $pesquisa['idcidade'];
$dtinicial = $pesquisa['dtinicial'];
$dtfinal = $pesquisa['dtfinal'];
$nradt = $pesquisa['nradt'];
$nrchd = $pesquisa['nrchd'];
$nrinf = $pesquisa['nrinf'];

try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('servicos_pesquisa');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'transactionId' => $transactionId,
        'tokenPesquisa' => $tokenPesquisa,
        'idcidade' => $idcidade,
        'dtinicial' => $dtinicial,
        'dtfinal' => $dtfinal,
        'nradt' => $nradt,
        'nrchd' => $nrchd,
        'nrinf' => $nrinf
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

$servicos = $response['servicos'];
for ($i=0; $i < count($servicos); $i++) { 
    $idservico = $servicos[$i]['idservico'];
    $nmservico = $servicos[$i]['nmservico'];
    $dstipo = $servicos[$i]['dstipo'];
    $dsmoeda = $servicos[$i]['dsmoeda'];
    $vlservico = $servicos[$i]['vlservico'];
    $dsdescricao = $servicos[$i]['dsdescricao'];
    $dsintegrador = $servicos[$i]['dsintegrador'];
    $imgservico = $servicos[$i]['imgservico'];
    $tokenServico = $servicos[$i]['tokenServico'];

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('servicos');
        $insert->values(array(
            'idservico' => $idservico,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'nmservico' => $nmservico,
            'dstipo' => $dstipo,
            'dsmoeda' => $dsmoeda,
            'vlservico' => $vlservico,
            'dsdescricao' => $dsdescricao,
            'dsintegrador' => $dsintegrador,
            'imgservico' => $imgservico,
            'tokenServico' => $tokenServico
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
        ->getConnection()
        ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "Error2: " . $e;
        echo $return;
    }

    $data = '';
    $dtdisponiveis = $servicos[$i]['dtdisponiveis'];
    for ($j=0; $j < count($dtdisponiveis); $j++) { 
        $data = $dtdisponiveis[$j];

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('servicos_datas');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'data' => $data
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "Error3: " . $e;
            echo $return;
        }
    }
}


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>