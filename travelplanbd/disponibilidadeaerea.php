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
$sql = "select value from settings where name='enableglobaliapackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_globaliapackages = $affiliate_id;
} else {
    $affiliate_id_globaliapackages = 0;
}
$sql = "select value from settings where name='globaliapackagesCustomerID' and affiliate_id=$affiliate_id_globaliapackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $globaliapackagesCustomerID = $row_settings['value'];
}
$sql = "select value from settings where name='globaliapackagesserviceURL' and affiliate_id=$affiliate_id_globaliapackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $globaliapackagesserviceURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();
/*
 * $raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.reserva.wst"> <soapenv:Header/> <soapenv:Body> <typ:crearSesionRequest> <typ:idUsuario>' . $globaliapackagesCustomerID . '</typ:idUsuario> <typ:codIdi>ESP</typ:codIdi> <typ:codAge/> <typ:codSag/> </typ:crearSesionRequest> </soapenv:Body> </soapenv:Envelope> ';
 * $client = new Client();
 * $client->setOptions(array(
 * 'timeout' => 100,
 * 'sslverifypeer' => false,
 * 'sslverifyhost' => false
 * ));
 * $client->setHeaders(array(
 * 'Accept-Encoding' => 'gzip,deflate',
 * 'X-Powered-By' => 'Zend Framework',
 * 'Content-Length' => strlen($raw),
 * 'Content-Type' => 'application/x-www-form-urlencoded'
 * ));
 * $url = $specialtourspackagesserviceURL . "v1/clientes/login";
 * $client->setUri($url);
 * $client->setMethod('POST');
 * $client->setRawBody($raw);
 * $response = $client->send();
 * if ($response->isSuccess()) {
 * $response = $response->getBody();
 * } else {
 * $logger = new Logger();
 * $writer = new Writer\Stream('/srv/www/htdocs/error_log');
 * $logger->addWriter($writer);
 * $logger->info($client->getUri());
 * $logger->info($response->getStatusCode() . " - " . $response->getReasonPhrase());
 * echo $return;
 * echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
 * echo $return;
 * die();
 * }
 * $response = json_decode($response, true);
 * $token = $response['token'];
 * echo $return;
 * echo $token;
 * echo $return;
 */

$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = $globaliapackagesserviceURL . 'b2c/services/wstVuelosUnificados';

$cadRes = 'rO0ABXNyABh1dGlsaWRhZGVzLkNhZGVuYVJlc2VydmEAAAAAAAAAAQIAAUwABG1hcGF0AA9MamF2YS91dGlsL01hcDt4cHNyABFqYXZhLnV0aWwuSGFzaE1hcAUH2sHDFmDRAwACRgAKbG9hZEZhY3RvckkACXRocmVzaG9sZHhwP0AAAAAAADB3CAAAAEAAAAAbdAAGc3RyTW9kdAALNEAxQDEjMCMwIzt0AAlpZFVzdWFyaW90AAVDVE1UMHQABnB2cFNlcnNyABFqYXZhLmxhbmcuSW50ZWdlchLioKT3gYc4AgABSQAFdmFsdWV4cgAQamF2YS5sYW5nLk51bWJlcoaslR0LlOCLAgAAeHAAAoxYdAAGcHZwVHJhc3EAfgAKAAAAAHQABnJlZlNlcnNxAH4ACgABy7V0AAZpZGlvbWF0AANFU1B0AAZmZWNJbml0AAowNi8wNC8yMDE5dAAGYXB0RGVwcHQABmlkR3J1cHNxAH4ACgAAAAN0AAZlZGFOaW50AAMzMDt0AAllc3REaXNTZXJzcQB)AAoAAAACdAAGYXB0QXJydAACLTF0AAZudW1BZGxzcQB)AAoAAAABdAAJZXN0RGlzVHJhcQB)ABt0AAZyZWZQcm9zcQB)AAoABG8fdAAHcmVnaW1lbnQAAlRJdAAGc3dpUmVzdAABTnQABm51bUluZnEAfgAOdAAJY2F0ZWdvcmlhdAACQ0x0AAZ0aXBTZXJ0AAhDSVJDVUlUT3QABmZlY0ZpbnQACjEzLzA0LzIwMTl0AAZzd2lEaXJzcgARamF2YS5sYW5nLkJvb2xlYW7NIHKA1Zz67gIAAVoABXZhbHVleHAAdAAGbnVtTmlucQB)AA50AAZlZGFkZXN0AAMzMDt0AAZzd2lWdWVxAH4AJnQACWNvZGFncnVwYXQABVBBUjU2dAAGY29uUHJlcHg=';
$ideSes = "52535383408122501180";

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.vuelosUnificados.wst">    <soapenv:Header/>    <soapenv:Body>       <typ:disponibilidadAereaUnificadaRequest>          <typ:idUsuario>' . $globaliapackagesCustomerID . '</typ:idUsuario>         <typ:cadRes>' . $cadRes . '</typ:cadRes>          <typ:ideSes>' . $ideSes . '</typ:ideSes>       </typ:disponibilidadAereaUnificadaRequest>     </soapenv:Body>  </soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'X-Powered-By' => 'Zend Framework',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'application/x-www-form-urlencoded'
));
$client->setUri($url);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
$response = $response->getBody();
echo "<br/> PASSOU 3";

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
//include "/srv/www/htdocs/specialtours/travelplan/agrupaciones_debug.php";
echo "RESPONSE";
/* echo $return;
echo $response;
echo $return; */
echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
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
$Envelope = $inputDoc->getElementsByTagName("Envelope");
$Body = $Envelope->item(0)->getElementsByTagName("Body");
$disponibilidadAereaUnificadaResponse = $Body->item(0)->getElementsByTagName("disponibilidadAereaUnificadaResponse");
$agrupacionesCircuitoResponseRow = $disponibilidadAereaUnificadaResponse->item(0)->getElementsByTagName("agrupacionesCircuitoResponseRow");
$node = $agrupacionesCircuitoResponseRow->item(0)->getElementsByTagName("detalleUnificado");
for ($iAux = 0; $iAux < $node->length; $iAux++) {
    //detalleIda
    $detalleIda = $node->item($iAUX)->getElementsByTagName("detalleIda");
    $horDep = $detalleIda->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleIda->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleIda->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleIda->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleIda->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horArr = $detalleIda->item(0)->getElementsByTagName("horArr");
    if ($horArr->length > 0) {
        $horArr = $horArr->item(0)->nodeValue;
    } else {
        $horArr = "";
    }
    $aptDep = $detalleIda->item(0)->getElementsByTagName("aptDep");
    if ($aptDep->length > 0) {
        $aptDep = $aptDep->item(0)->nodeValue;
    } else {
        $aptDep = "";
    }
    $dscAptDep = $detalleIda->item(0)->getElementsByTagName("dscAptDep");
    if ($dscAptDep->length > 0) {
        $dscAptDep = $dscAptDep->item(0)->nodeValue;
    } else {
        $dscAptDep = "";
    }
    $paiAptDep = $detalleIda->item(0)->getElementsByTagName("paiAptDep");
    if ($paiAptDep->length > 0) {
        $paiAptDep = $paiAptDep->item(0)->nodeValue;
    } else {
        $paiAptDep = "";
    }
    $aptArr = $detalleIda->item(0)->getElementsByTagName("aptArr");
    if ($aptArr->length > 0) {
        $aptArr = $aptArr->item(0)->nodeValue;
    } else {
        $aptArr = "";
    }
    $dscAptArr = $detalleIda->item(0)->getElementsByTagName("dscAptArr");
    if ($dscAptArr->length > 0) {
        $dscAptArr = $dscAptArr->item(0)->nodeValue;
    } else {
        $dscAptArr = "";
    }
    $paiAptArr = $detalleIda->item(0)->getElementsByTagName("paiAptArr");
    if ($paiAptArr->length > 0) {
        $paiAptArr = $paiAptArr->item(0)->nodeValue;
    } else {
        $paiAptArr = "";
    }
    $codCia = $detalleIda->item(0)->getElementsByTagName("codCia");
    if ($codCia->length > 0) {
        $codCia = $codCia->item(0)->nodeValue;
    } else {
        $codCia = "";
    }
    $dscCia = $detalleIda->item(0)->getElementsByTagName("dscCia");
    if ($dscCia->length > 0) {
        $dscCia = $dscCia->item(0)->nodeValue;
    } else {
        $dscCia = "";
    }
    $codCiaOpe = $detalleIda->item(0)->getElementsByTagName("codCiaOpe");
    if ($codCiaOpe->length > 0) {
        $codCiaOpe = $codCiaOpe->item(0)->nodeValue;
    } else {
        $codCiaOpe = "";
    }
    $dscCiaOpe = $detalleIda->item(0)->getElementsByTagName("dscCiaOpe");
    if ($dscCiaOpe->length > 0) {
        $dscCiaOpe = $dscCiaOpe->item(0)->nodeValue;
    } else {
        $dscCiaOpe = "";
    }
    $numVue = $detalleIda->item(0)->getElementsByTagName("numVue");
    if ($numVue->length > 0) {
        $numVue = $numVue->item(0)->nodeValue;
    } else {
        $numVue = "";
    }
    //claseAerea
    $claseAerea = $detalleIda->item(0)->getElementsByTagName("claseAerea");
    $codCla = $claseAerea->item(0)->getElementsByTagName("codCla");
    if ($codCla->length > 0) {
        $codCla = $codCla->item(0)->nodeValue;
    } else {
        $codCla = "";
    }
    $dscCla = $claseAerea->item(0)->getElementsByTagName("dscCla");
    if ($dscCla->length > 0) {
        $dscCla = $dscCla->item(0)->nodeValue;
    } else {
        $dscCla = "";
    }
    $estInv = $claseAerea->item(0)->getElementsByTagName("estInv");
    if ($estInv->length > 0) {
        $estInv = $estInv->item(0)->nodeValue;
    } else {
        $estInv = "";
    }
    $pvp = $claseAerea->item(0)->getElementsByTagName("pvp");
    if ($pvp->length > 0) {
        $pvp = $pvp->item(0)->nodeValue;
    } else {
        $pvp = "";
    }
    $pvpCar = $claseAerea->item(0)->getElementsByTagName("pvpCar");
    if ($pvpCar->length > 0) {
        $pvpCar = $pvpCar->item(0)->nodeValue;
    } else {
        $pvpCar = "";
    }

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('disponibilidadearea_detalleIda');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'horDep' => $horDep,
        'horArr' => $horArr,
        'aptDep' => $aptDep,
        'dscAptDep' => $dscAptDep,
        'paiAptDep' => $paiAptDep,
        'aptArr' => $aptArr,
        'dscAptArr' => $dscAptArr,
        'paiAptArr' => $paiAptArr,
        'codCia' => $codCia,
        'dscCia' => $dscCia,
        'codCiaOpe' => $codCiaOpe,
        'dscCiaOpe' => $dscCiaOpe,
        'numVue' => $numVue,
        'codCla' => $codCla,
        'dscCla' => $dscCla,
        'estInv' => $estInv,
        'pvp' => $pvp,
        'pvpCar' => $pvpCar
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();

    //detalleIda
    $detalleVta = $node->item($iAUX)->getElementsByTagName("detalleVta");
    $horDep = $detalleVta->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleVta->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleVta->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleVta->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horDep = $detalleVta->item(0)->getElementsByTagName("horDep");
    if ($horDep->length > 0) {
        $horDep = $horDep->item(0)->nodeValue;
    } else {
        $horDep = "";
    }
    $horArr = $detalleVta->item(0)->getElementsByTagName("horArr");
    if ($horArr->length > 0) {
        $horArr = $horArr->item(0)->nodeValue;
    } else {
        $horArr = "";
    }
    $aptDep = $detalleVta->item(0)->getElementsByTagName("aptDep");
    if ($aptDep->length > 0) {
        $aptDep = $aptDep->item(0)->nodeValue;
    } else {
        $aptDep = "";
    }
    $dscAptDep = $detalleVta->item(0)->getElementsByTagName("dscAptDep");
    if ($dscAptDep->length > 0) {
        $dscAptDep = $dscAptDep->item(0)->nodeValue;
    } else {
        $dscAptDep = "";
    }
    $paiAptDep = $detalleVta->item(0)->getElementsByTagName("paiAptDep");
    if ($paiAptDep->length > 0) {
        $paiAptDep = $paiAptDep->item(0)->nodeValue;
    } else {
        $paiAptDep = "";
    }
    $aptArr = $detalleVta->item(0)->getElementsByTagName("aptArr");
    if ($aptArr->length > 0) {
        $aptArr = $aptArr->item(0)->nodeValue;
    } else {
        $aptArr = "";
    }
    $dscAptArr = $detalleVta->item(0)->getElementsByTagName("dscAptArr");
    if ($dscAptArr->length > 0) {
        $dscAptArr = $dscAptArr->item(0)->nodeValue;
    } else {
        $dscAptArr = "";
    }
    $paiAptArr = $detalleVta->item(0)->getElementsByTagName("paiAptArr");
    if ($paiAptArr->length > 0) {
        $paiAptArr = $paiAptArr->item(0)->nodeValue;
    } else {
        $paiAptArr = "";
    }
    $codCia = $detalleVta->item(0)->getElementsByTagName("codCia");
    if ($codCia->length > 0) {
        $codCia = $codCia->item(0)->nodeValue;
    } else {
        $codCia = "";
    }
    $dscCia = $detalleVta->item(0)->getElementsByTagName("dscCia");
    if ($dscCia->length > 0) {
        $dscCia = $dscCia->item(0)->nodeValue;
    } else {
        $dscCia = "";
    }
    $codCiaOpe = $detalleVta->item(0)->getElementsByTagName("codCiaOpe");
    if ($codCiaOpe->length > 0) {
        $codCiaOpe = $codCiaOpe->item(0)->nodeValue;
    } else {
        $codCiaOpe = "";
    }
    $dscCiaOpe = $detalleVta->item(0)->getElementsByTagName("dscCiaOpe");
    if ($dscCiaOpe->length > 0) {
        $dscCiaOpe = $dscCiaOpe->item(0)->nodeValue;
    } else {
        $dscCiaOpe = "";
    }
    $numVue = $detalleVta->item(0)->getElementsByTagName("numVue");
    if ($numVue->length > 0) {
        $numVue = $numVue->item(0)->nodeValue;
    } else {
        $numVue = "";
    }
    //claseAerea
    $claseAerea = $detalleVta->item(0)->getElementsByTagName("claseAerea");
    $codCla = $claseAerea->item(0)->getElementsByTagName("codCla");
    if ($codCla->length > 0) {
        $codCla = $codCla->item(0)->nodeValue;
    } else {
        $codCla = "";
    }
    $dscCla = $claseAerea->item(0)->getElementsByTagName("dscCla");
    if ($dscCla->length > 0) {
        $dscCla = $dscCla->item(0)->nodeValue;
    } else {
        $dscCla = "";
    }
    $estInv = $claseAerea->item(0)->getElementsByTagName("estInv");
    if ($estInv->length > 0) {
        $estInv = $estInv->item(0)->nodeValue;
    } else {
        $estInv = "";
    }
    $pvp = $claseAerea->item(0)->getElementsByTagName("pvp");
    if ($pvp->length > 0) {
        $pvp = $pvp->item(0)->nodeValue;
    } else {
        $pvp = "";
    }
    $pvpCar = $claseAerea->item(0)->getElementsByTagName("pvpCar");
    if ($pvpCar->length > 0) {
        $pvpCar = $pvpCar->item(0)->nodeValue;
    } else {
        $pvpCar = "";
    }

    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('disponibilidadearea_detalleVta');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'horDep' => $horDep,
        'horArr' => $horArr,
        'aptDep' => $aptDep,
        'dscAptDep' => $dscAptDep,
        'paiAptDep' => $paiAptDep,
        'aptArr' => $aptArr,
        'dscAptArr' => $dscAptArr,
        'paiAptArr' => $paiAptArr,
        'codCia' => $codCia,
        'dscCia' => $dscCia,
        'codCiaOpe' => $codCiaOpe,
        'dscCiaOpe' => $dscCiaOpe,
        'numVue' => $numVue,
        'codCla' => $codCla,
        'dscCla' => $dscCla,
        'estInv' => $estInv,
        'pvp' => $pvp,
        'pvpCar' => $pvpCar
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();


    //cadRes
    $cadRes = $node->item($iAUX)->getElementsByTagName("cadRes");
    if ($cadRes->length > 0) {
        $cadRes = $cadRes->item(0)->nodeValue;
    } else {
        $cadRes = "";
    }
    $sql = new Sql($db);
    $insert = $sql->insert();
    $insert->into('disponibilidadearea_cadRes');
    $insert->values(array(
        'datetime_created' => time(),
        'datetime_updated' => 0,
        'cadRes' => $cadRes
    ), $insert::VALUES_MERGE);
    $statement = $sql->prepareStatementForSqlObject($insert);
    $results = $statement->execute();
    $db->getDriver()
        ->getConnection()
        ->disconnect();
    
   
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/> Done';
?>