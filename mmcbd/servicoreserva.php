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
echo "COMECOU RESERVA<br/>";
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

$raw = '{	"tokenPolitica":"dEFNQUFCK0xDQUFBQUFBQUJBQmRrOStPbXpnVXhsK0ZqYlIzbmRZMlF6V1p1eVZnVWpLWVlMQUozRGsyV3hLYlA1T1FDYUhxQSszMVBrSmZiR2xucTQ0cTJiSjAvRHZuKzg2Ui9XV3hQWW1wVzRsV1ZrWTBWVHQwaThjRkFuQjVCOUVkWEdZQVBQNVlpM2NMVHd3aXJVNHZCL21XUWVndHc0VTVLS0dxYldjT3cwR0tueUM0djBQM0dRU1B5SjdCOXc4MmZFQTJ1Z1AyYTlwUC9EY2ZmL1hmL2oxYndsS3pzcVVxQ3k0L1FQVGhlOEU1MkZ6TUhPM0ZTVmp5VFpyMTdaOGZLQUIvV3FxelhvVHBUbk1CNjFTZForL2lqemRxcjJZWGo4UHBVcjFiWkZXenVweUgzMmJ4dHpEbjc1ZWRydHBmelZNZmh3bkRxdzBZdmZsMFgzZm9Ka2Q4NGhsT2xOMjdXVjZqbUEzblJDOVp2S09ZWWZOVTVYZ2pzTHl4ckM2b2pTK2tJUzNsTHM4MTVJSkhWMmtpdXdTMFY0Y2g1bm5vWnF5L0ptMC9SVmx5S2ozL0pXWDFpazlsa0VIdTdRUE11YWR2SlZlY3J2R2x5TzhSUjBPZjZvZHJxc21Xb1hGVXUraVc2cXV0R0hrV25JcEtsK3NJbUxUS291dSs3U09KT0U3OUlSVjVPUEoxNzFZN2RTblhaTGZmZllLNWgzbHV6QmdkWFVUc3NKaDl4WGtRSHRJc2dveDM5Z2FRSjZMSmxBZWtWNzdCQlNzM21UWm4xVGhPZWl6UE5OZFQwcXFrT0twcndTTW9ZVjhuamJabG5rQTUxVjJpZVUwYmJGZU5lczU5L3N3QW5NZW8rbElYOTFGZ25KS1g2OWpIZVdMVFVXb0ZxVjlyaVVoY01wd1hBSmEwNlk5cEs1SDB3MmVaOXkvU2g3dVloWlJ4NmV5OTJxOU03MlRwY3Azd2NrVndmV2FIQjZjeVdLdW1CREV2S1FzS3A1allqWEp6eXdEdUZYQSt5V010NG5YZFZFM2tFRC84eU1ENFJBSUNaWDRGeEI5aEhKaDUxcVRld00rVHNQdHRhc3k2bkdZOVNLZGlVbHZWZHJDdzNVazE5T09la3dQVnc3U0J0TXVDNFNLT3BtWnRlS1VvUFBLZ1hpblAxQXI3SjZhTDIyYUs0QVpndWc5R1dub3VTWExsSnNuaS8zZjM2ek8xRjJPKy9nZVpTZlpJdEFNQUFBPT0",
    "tokenBooking":null,
    "dataServico":"2019-12-22",
    "pessoas":[
        { "nome":"Antonio", "sobrenome":"Ramos", "dtnascimento":"1986-05-04", "dssexo":"M", "email":"antonioramos@gmail.com", "telefone":"" },
        { "nome":"Manuela", "sobrenome":"Ramos", "dtnascimento":"1987-05-04", "dssexo":"F", "email":"manuelaramos@gmail.com", "telefone":"" },
        { "nome":"Ana", "sobrenome":"Ramos", "dtnascimento":"1985-05-04", "dssexo":"F", "email":"anaramos@gmail.com", "telefone":"" },
        { "nome":"Edgar", "sobrenome":"Ramos", "dtnascimento":"2012-01-04", "dssexo":"M", "email":"antonioramos@gmail.com", "telefone":"" },
        { "nome":"Alberta", "sobrenome":"Ramos", "dtnascimento":"2016-01-04", "dssexo":"F", "email":"antonioramos@gmail.com", "telefone":"" },
        { "nome":"Gisela", "sobrenome":"Ramos", "dtnascimento":"2018-02-04", "dssexo":"F", "email":"antonioramos@gmail.com", "telefone":"" }
    ],
    "DadosReserva":{
        "AeroportoChegada":"GRU",
        "AeroportoSaida":"IGU",
        "CiaChegada":"GOL",
        "CiaSaida":"GOL",
        "VooChegada":"1234",
        "VooSaida":"1234",
        "DataVooChegada":"2019-12-22",
        "DataVooSaida":"2019-12-22",
        "HoraVooChegada":"19:00:00",
        "HoraVooSaida":"17:30:00",
        "LocalChegadaSaida":"Hotel Ibis",
        "Observacoes":"",
        "SolicitacaoEspecial":""
    }
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
$url = "http://www.mmcturismo.com/webapi/api/servico/servicoreserva";

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

$mensagem = $response['mensagem'];
$localizador = $response['localizador'];
$tokenBooking = $response['tokenBooking'];
$dataBase = $response['dataBase'];
$sucesso = $response['sucesso'];


try {
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('servicos_reserva');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'mensagem' => $mensagem,
        'localizador' => $localizador,
        'tokenBooking' => $tokenBooking,
        'dataBase' => $dataBase,
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


// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>