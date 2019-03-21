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
echo "COMECOU RIU<br/>";
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
$sql = "select value from settings where name='enableriu' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_riu = $affiliate_id;
} else {
    $affiliate_id_riu = 0;
}
$sql = "select value from settings where name='riuLoginEmail' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='riuPassword' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $riuPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='riuServiceURL' and affiliate_id=$affiliate_id_riu";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $riuServiceURL = $row['value'];
}
echo $return;
echo $riuServiceURL;
echo $return;
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);


$raw = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:xsd="http://www.w3.org/2001/XMLSchema"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
<soap:Body>
    <loginXML xmlns="http://services.enginexml.rumbonet.riu.com">
        <in0 xmlns="http://services.enginexml.rumbonet.riu.com">
            <acceso xmlns="http://dtos.common.rumbonet.riu.com">XML</acceso>
            <codigoIdioma xmlns="http://dtos.common.rumbonet.riu.com">US</codigoIdioma>
            <codigoPais xmlns="http://dtos.common.rumbonet.riu.com">E</codigoPais>
            <ipCustomer xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" />
            <usuarioOpera xmlns="http://dtos.common.rumbonet.riu.com" xsi:nil="true" />
            <usuarioOperaId xmlns="http://dtos.common.rumbonet.riu.com">0</usuarioOperaId>
        </in0>
    </loginXML>
</soap:Body>
</soap:Envelope>';

$userpass = $riuLoginEmail . ':' . $riuPassword;
$login = base64_encode($userpass);

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-type: text/xml;charset=\"utf-8\"",
    "Accept: text/xml",
    "Cache-Control: no-cache",
    "Pragma: no-cache",
    "Authorization: Basic " . $login,
    "Content-length: ".strlen($raw)
));


$client->setUri($riuServiceURL);
$client->setMethod('POST');
$client->setRawBody($raw);
$response = $client->send();
if ($response->isSuccess()) {
$headers = $response->getHeaders();
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
echo "<br/>RESPONSE";
$x = $headers->toArray();
$x = $x["Set-Cookie"][0];
$x = explode(";",$x);
$x = $x[0];
$x = explode("=",$x);
$JSESSIONID = $x[1];
echo "Session = ";
echo $JSESSIONID;

echo '<xmp>';
var_dump($response);
echo '</xmp>';
die();
$config = new \Zend\Config\Config(include '../config/autoload/global.riu.php');
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
$loginXMLResponse = $Body->item(0)->getElementsByTagName("loginXMLResponse");
$LoginAgenciaRsp = $loginXMLResponse->item(0)->getElementsByTagName("LoginAgenciaRsp");

$datosAgenciaDto = $LoginAgenciaRsp->item(0)->getElementsByTagName("datosAgenciaDto");
if ($datosAgenciaDto->length > 0) {
    $agenciaDeCadena = $datosAgenciaDto->item(0)->getElementsByTagName("agenciaDeCadena");
    if ($agenciaDeCadena->length > 0) {
        $agenciaDeCadena = $agenciaDeCadena->item(0)->nodeValue;
    } else {
        $agenciaDeCadena = "";
    }
    $agenciaDeGrupoDeCompras = $datosAgenciaDto->item(0)->getElementsByTagName("agenciaDeGrupoDeCompras");
    if ($agenciaDeGrupoDeCompras->length > 0) {
        $agenciaDeGrupoDeCompras = $agenciaDeGrupoDeCompras->item(0)->nodeValue;
    } else {
        $agenciaDeGrupoDeCompras = "";
    }
    $agenciaId = $datosAgenciaDto->item(0)->getElementsByTagName("agenciaId");
    if ($agenciaId->length > 0) {
        $agenciaId = $agenciaId->item(0)->nodeValue;
    } else {
        $agenciaId = "";
    }
    $agenciaIndependiente = $datosAgenciaDto->item(0)->getElementsByTagName("agenciaIndependiente");
    if ($agenciaIndependiente->length > 0) {
        $agenciaIndependiente = $agenciaIndependiente->item(0)->nodeValue;
    } else {
        $agenciaIndependiente = "";
    }
    $apellidosContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("apellidosContactoPrincipal");
    if ($apellidosContactoPrincipal->length > 0) {
        $apellidosContactoPrincipal = $apellidosContactoPrincipal->item(0)->nodeValue;
    } else {
        $apellidosContactoPrincipal = "";
    }
    $apellidosOtroContacto1 = $datosAgenciaDto->item(0)->getElementsByTagName("apellidosOtroContacto1");
    if ($apellidosOtroContacto1->length > 0) {
        $apellidosOtroContacto1 = $apellidosOtroContacto1->item(0)->nodeValue;
    } else {
        $apellidosOtroContacto1 = "";
    }
    $apellidosOtroContacto2 = $datosAgenciaDto->item(0)->getElementsByTagName("apellidosOtroContacto2");
    if ($apellidosOtroContacto2->length > 0) {
        $apellidosOtroContacto2 = $apellidosOtroContacto2->item(0)->nodeValue;
    } else {
        $apellidosOtroContacto2 = "";
    }
    $apellidosOtroContacto3 = $datosAgenciaDto->item(0)->getElementsByTagName("apellidosOtroContacto3");
    if ($apellidosOtroContacto3->length > 0) {
        $apellidosOtroContacto3 = $apellidosOtroContacto3->item(0)->nodeValue;
    } else {
        $apellidosOtroContacto3 = "";
    }
    $cifFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("cifFiscal");
    if ($cifFiscal->length > 0) {
        $cifFiscal = $cifFiscal->item(0)->nodeValue;
    } else {
        $cifFiscal = "";
    }
    $codIdioma = $datosAgenciaDto->item(0)->getElementsByTagName("codIdioma");
    if ($codIdioma->length > 0) {
        $codIdioma = $codIdioma->item(0)->nodeValue;
    } else {
        $codIdioma = "";
    }
    $codigoAgencia = $datosAgenciaDto->item(0)->getElementsByTagName("codigoAgencia");
    if ($codigoAgencia->length > 0) {
        $codigoAgencia = $codigoAgencia->item(0)->nodeValue;
    } else {
        $codigoAgencia = "";
    }
    $codigoError = $datosAgenciaDto->item(0)->getElementsByTagName("codigoError");
    if ($codigoError->length > 0) {
        $codigoError = $codigoError->item(0)->nodeValue;
    } else {
        $codigoError = "";
    }
    $codigoPostalContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("codigoPostalContactoPrincipal");
    if ($codigoPostalContactoPrincipal->length > 0) {
        $codigoPostalContactoPrincipal = $codigoPostalContactoPrincipal->item(0)->nodeValue;
    } else {
        $codigoPostalContactoPrincipal = "";
    }
    $codigoPostalFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("codigoPostalFacturacion");
    if ($codigoPostalFacturacion->length > 0) {
        $codigoPostalFacturacion = $codigoPostalFacturacion->item(0)->nodeValue;
    } else {
        $codigoPostalFacturacion = "";
    }
    $codigoPostalFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("codigoPostalFiscal");
    if ($codigoPostalFiscal->length > 0) {
        $codigoPostalFiscal = $codigoPostalFiscal->item(0)->nodeValue;
    } else {
        $codigoPostalFiscal = "";
    }
    $codigoTipoError = $datosAgenciaDto->item(0)->getElementsByTagName("codigoTipoError");
    if ($codigoTipoError->length > 0) {
        $codigoTipoError = $codigoTipoError->item(0)->nodeValue;
    } else {
        $codigoTipoError = "";
    }
    $datosPago = $datosAgenciaDto->item(0)->getElementsByTagName("datosPago");
    if ($datosPago->length > 0) {
        $datosPago = $datosPago->item(0)->nodeValue;
    } else {
        $datosPago = "";
    }
    $dirEmpContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("dirEmpContactoPrincipal");
    if ($dirEmpContactoPrincipal->length > 0) {
        $dirEmpContactoPrincipal = $dirEmpContactoPrincipal->item(0)->nodeValue;
    } else {
        $dirEmpContactoPrincipal = "";
    }
    $direccionFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("direccionFacturacion");
    if ($direccionFacturacion->length > 0) {
        $direccionFacturacion = $direccionFacturacion->item(0)->nodeValue;
    } else {
        $direccionFacturacion = "";
    }
    $direccionFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("direccionFiscal");
    if ($direccionFiscal->length > 0) {
        $direccionFiscal = $direccionFiscal->item(0)->nodeValue;
    } else {
        $direccionFiscal = "";
    }
    $emailContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("emailContactoPrincipal");
    if ($emailContactoPrincipal->length > 0) {
        $emailContactoPrincipal = $emailContactoPrincipal->item(0)->nodeValue;
    } else {
        $emailContactoPrincipal = "";
    }
    $emailFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("emailFacturacion");
    if ($emailFacturacion->length > 0) {
        $emailFacturacion = $emailFacturacion->item(0)->nodeValue;
    } else {
        $emailFacturacion = "";
    }
    $enviarPublicidad = $datosAgenciaDto->item(0)->getElementsByTagName("enviarPublicidad");
    if ($enviarPublicidad->length > 0) {
        $enviarPublicidad = $enviarPublicidad->item(0)->nodeValue;
    } else {
        $enviarPublicidad = "";
    }
    $estado = $datosAgenciaDto->item(0)->getElementsByTagName("estado");
    if ($estado->length > 0) {
        $estado = $estado->item(0)->nodeValue;
    } else {
        $estado = "";
    }
    $estadoAgenciaEnInterlocutoresComerciales = $datosAgenciaDto->item(0)->getElementsByTagName("estadoAgenciaEnInterlocutoresComerciales");
    if ($estadoAgenciaEnInterlocutoresComerciales->length > 0) {
        $estadoAgenciaEnInterlocutoresComerciales = $estadoAgenciaEnInterlocutoresComerciales->item(0)->nodeValue;
    } else {
        $estadoAgenciaEnInterlocutoresComerciales = "";
    }
    $idClienteComercial = $datosAgenciaDto->item(0)->getElementsByTagName("idClienteComercial");
    if ($idClienteComercial->length > 0) {
        $idClienteComercial = $idClienteComercial->item(0)->nodeValue;
    } else {
        $idClienteComercial = "";
    }
    $idClienteFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("idClienteFiscal");
    if ($idClienteFiscal->length > 0) {
        $idClienteFiscal = $idClienteFiscal->item(0)->nodeValue;
    } else {
        $idClienteFiscal = "";
    }
    $idClienteRemitidoFacturas = $datosAgenciaDto->item(0)->getElementsByTagName("idClienteRemitidoFacturas");
    if ($idClienteRemitidoFacturas->length > 0) {
        $idClienteRemitidoFacturas = $idClienteRemitidoFacturas->item(0)->nodeValue;
    } else {
        $idClienteRemitidoFacturas = "";
    }
    $interfasadoASef = $datosAgenciaDto->item(0)->getElementsByTagName("interfasadoASef");
    if ($interfasadoASef->length > 0) {
        $interfasadoASef = $interfasadoASef->item(0)->nodeValue;
    } else {
        $interfasadoASef = "";
    }
    $nombreAgencia = $datosAgenciaDto->item(0)->getElementsByTagName("nombreAgencia");
    if ($nombreAgencia->length > 0) {
        $nombreAgencia = $nombreAgencia->item(0)->nodeValue;
    } else {
        $nombreAgencia = "";
    }
    $nombreContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("nombreContactoPrincipal");
    if ($nombreContactoPrincipal->length > 0) {
        $nombreContactoPrincipal = $nombreContactoPrincipal->item(0)->nodeValue;
    } else {
        $nombreContactoPrincipal = "";
    }
    $nombreFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("nombreFiscal");
    if ($nombreFiscal->length > 0) {
        $nombreFiscal = $nombreFiscal->item(0)->nodeValue;
    } else {
        $nombreFiscal = "";
    }
    $nombreOficinaAgencia = $datosAgenciaDto->item(0)->getElementsByTagName("nombreOficinaAgencia");
    if ($nombreOficinaAgencia->length > 0) {
        $nombreOficinaAgencia = $nombreOficinaAgencia->item(0)->nodeValue;
    } else {
        $nombreOficinaAgencia = "";
    }
    $nombreOtroContacto1 = $datosAgenciaDto->item(0)->getElementsByTagName("nombreOtroContacto1");
    if ($nombreOtroContacto1->length > 0) {
        $nombreOtroContacto1 = $nombreOtroContacto1->item(0)->nodeValue;
    } else {
        $nombreOtroContacto1 = "";
    }
    $nombreOtroContacto2 = $datosAgenciaDto->item(0)->getElementsByTagName("nombreOtroContacto2");
    if ($nombreOtroContacto2->length > 0) {
        $nombreOtroContacto2 = $nombreOtroContacto2->item(0)->nodeValue;
    } else {
        $nombreOtroContacto2 = "";
    }
    $nombreOtroContacto3 = $datosAgenciaDto->item(0)->getElementsByTagName("nombreOtroContacto3");
    if ($nombreOtroContacto3->length > 0) {
        $nombreOtroContacto3 = $nombreOtroContacto3->item(0)->nodeValue;
    } else {
        $nombreOtroContacto3 = "";
    }
    $numeroFaxContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("numeroFaxContactoPrincipal");
    if ($numeroFaxContactoPrincipal->length > 0) {
        $numeroFaxContactoPrincipal = $numeroFaxContactoPrincipal->item(0)->nodeValue;
    } else {
        $numeroFaxContactoPrincipal = "";
    }
    $numeroFaxFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("numeroFaxFacturacion");
    if ($numeroFaxFacturacion->length > 0) {
        $numeroFaxFacturacion = $numeroFaxFacturacion->item(0)->nodeValue;
    } else {
        $numeroFaxFacturacion = "";
    }
    $numeroTelefonoContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("numeroTelefonoContactoPrincipal");
    if ($numeroTelefonoContactoPrincipal->length > 0) {
        $numeroTelefonoContactoPrincipal = $numeroTelefonoContactoPrincipal->item(0)->nodeValue;
    } else {
        $numeroTelefonoContactoPrincipal = "";
    }
    $numeroTelefonoFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("numeroTelefonoFacturacion");
    if ($numeroTelefonoFacturacion->length > 0) {
        $numeroTelefonoFacturacion = $numeroTelefonoFacturacion->item(0)->nodeValue;
    } else {
        $numeroTelefonoFacturacion = "";
    }
    $observacionesOtroContacto = $datosAgenciaDto->item(0)->getElementsByTagName("observacionesOtroContacto");
    if ($observacionesOtroContacto->length > 0) {
        $observacionesOtroContacto = $observacionesOtroContacto->item(0)->nodeValue;
    } else {
        $observacionesOtroContacto = "";
    }
    $operacionSolicitud = $datosAgenciaDto->item(0)->getElementsByTagName("operacionSolicitud");
    if ($operacionSolicitud->length > 0) {
        $operacionSolicitud = $operacionSolicitud->item(0)->nodeValue;
    } else {
        $operacionSolicitud = "";
    }
    $paisAgencia = $datosAgenciaDto->item(0)->getElementsByTagName("paisAgencia");
    if ($paisAgencia->length > 0) {
        $paisAgencia = $paisAgencia->item(0)->nodeValue;
    } else {
        $paisAgencia = "";
    }
    $paisContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("paisContactoPrincipal");
    if ($paisContactoPrincipal->length > 0) {
        $paisContactoPrincipal = $paisContactoPrincipal->item(0)->nodeValue;
    } else {
        $paisContactoPrincipal = "";
    }
    $paisFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("paisFacturacion");
    if ($paisFacturacion->length > 0) {
        $paisFacturacion = $paisFacturacion->item(0)->nodeValue;
    } else {
        $paisFacturacion = "";
    }
    $paisFaxContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("paisFaxContactoPrincipal");
    if ($paisFaxContactoPrincipal->length > 0) {
        $paisFaxContactoPrincipal = $paisFaxContactoPrincipal->item(0)->nodeValue;
    } else {
        $paisFaxContactoPrincipal = "";
    }
    $paisFaxFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("paisFaxFacturacion");
    if ($paisFaxFacturacion->length > 0) {
        $paisFaxFacturacion = $paisFaxFacturacion->item(0)->nodeValue;
    } else {
        $paisFaxFacturacion = "";
    }
    $paisFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("paisFiscal");
    if ($paisFiscal->length > 0) {
        $paisFiscal = $paisFiscal->item(0)->nodeValue;
    } else {
        $paisFiscal = "";
    }
    $paisTelefonoContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("paisTelefonoContactoPrincipal");
    if ($paisTelefonoContactoPrincipal->length > 0) {
        $paisTelefonoContactoPrincipal = $paisTelefonoContactoPrincipal->item(0)->nodeValue;
    } else {
        $paisTelefonoContactoPrincipal = "";
    }
    $paisTelefonoFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("paisTelefonoFacturacion");
    if ($paisTelefonoFacturacion->length > 0) {
        $paisTelefonoFacturacion = $paisTelefonoFacturacion->item(0)->nodeValue;
    } else {
        $paisTelefonoFacturacion = "";
    }
    $poblacionContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("poblacionContactoPrincipal");
    if ($poblacionContactoPrincipal->length > 0) {
        $poblacionContactoPrincipal = $poblacionContactoPrincipal->item(0)->nodeValue;
    } else {
        $poblacionContactoPrincipal = "";
    }
    $poblacionFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("poblacionFacturacion");
    if ($poblacionFacturacion->length > 0) {
        $poblacionFacturacion = $poblacionFacturacion->item(0)->nodeValue;
    } else {
        $poblacionFacturacion = "";
    }
    $poblacionFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("poblacionFiscal");
    if ($poblacionFiscal->length > 0) {
        $poblacionFiscal = $poblacionFiscal->item(0)->nodeValue;
    } else {
        $poblacionFiscal = "";
    }
    $prefijoFaxFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("prefijoFaxFacturacion");
    if ($prefijoFaxFacturacion->length > 0) {
        $prefijoFaxFacturacion = $prefijoFaxFacturacion->item(0)->nodeValue;
    } else {
        $prefijoFaxFacturacion = "";
    }
    $prefijoTelefonoContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("prefijoTelefonoContactoPrincipal");
    if ($prefijoTelefonoContactoPrincipal->length > 0) {
        $prefijoTelefonoContactoPrincipal = $prefijoTelefonoContactoPrincipal->item(0)->nodeValue;
    } else {
        $prefijoTelefonoContactoPrincipal = "";
    }
    $prefijoTelefonoFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("prefijoTelefonoFacturacion");
    if ($prefijoTelefonoFacturacion->length > 0) {
        $prefijoTelefonoFacturacion = $prefijoTelefonoFacturacion->item(0)->nodeValue;
    } else {
        $prefijoTelefonoFacturacion = "";
    }
    $prepago = $datosAgenciaDto->item(0)->getElementsByTagName("prepago");
    if ($prepago->length > 0) {
        $prepago = $prepago->item(0)->nodeValue;
    } else {
        $prepago = "";
    }
    $provinciaContactoPrincipal = $datosAgenciaDto->item(0)->getElementsByTagName("provinciaContactoPrincipal");
    if ($provinciaContactoPrincipal->length > 0) {
        $provinciaContactoPrincipal = $provinciaContactoPrincipal->item(0)->nodeValue;
    } else {
        $provinciaContactoPrincipal = "";
    }
    $provinciaFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("provinciaFacturacion");
    if ($provinciaFacturacion->length > 0) {
        $provinciaFacturacion = $provinciaFacturacion->item(0)->nodeValue;
    } else {
        $provinciaFacturacion = "";
    }
    $provinciaFiscal = $datosAgenciaDto->item(0)->getElementsByTagName("provinciaFiscal");
    if ($provinciaFiscal->length > 0) {
        $provinciaFiscal = $provinciaFiscal->item(0)->nodeValue;
    } else {
        $provinciaFiscal = "";
    }
    $remitenteFacturacion = $datosAgenciaDto->item(0)->getElementsByTagName("remitenteFacturacion");
    if ($remitenteFacturacion->length > 0) {
        $remitenteFacturacion = $remitenteFacturacion->item(0)->nodeValue;
    } else {
        $remitenteFacturacion = "";
    }
    $swivlohot = $datosAgenciaDto->item(0)->getElementsByTagName("swivlohot");
    if ($swivlohot->length > 0) {
        $swivlohot = $swivlohot->item(0)->nodeValue;
    } else {
        $swivlohot = "";
    }
    $usuarioId = $datosAgenciaDto->item(0)->getElementsByTagName("usuarioId");
    if ($usuarioId->length > 0) {
        $usuarioId = $usuarioId->item(0)->nodeValue;
    } else {
        $usuarioId = "";
    }

    try {
        $sql = new Sql($db);
        $insert = $sql->insert();
        $insert->into('login_datosAgenciaDto');
        $insert->values(array(
            'agenciaId' => $agenciaId,
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codigoAgencia' => $codigoAgencia,
            'agenciaDeCadena' => $agenciaDeCadena,
            'agenciaDeGrupoDeCompras' => $agenciaDeGrupoDeCompras,
            'agenciaIndependiente' => $agenciaIndependiente,
            'apellidosContactoPrincipal' => $apellidosContactoPrincipal,
            'apellidosOtroContacto1' => $apellidosOtroContacto1,
            'apellidosOtroContacto2' => $apellidosOtroContacto2,
            'apellidosOtroContacto3' => $apellidosOtroContacto3,
            'cifFiscal' => $cifFiscal,
            'codIdioma' => $codIdioma,
            'codigoError' => $codigoError,
            'codigoPostalContactoPrincipal' => $codigoPostalContactoPrincipal,
            'codigoPostalFacturacion' => $codigoPostalFacturacion,
            'codigoPostalFiscal' => $codigoPostalFiscal,
            'codigoTipoError' => $codigoTipoError,
            'datosPago' => $datosPago,
            'dirEmpContactoPrincipal' => $dirEmpContactoPrincipal,
            'direccionFacturacion' => $direccionFacturacion,
            'direccionFiscal' => $direccionFiscal,
            'emailContactoPrincipal' => $emailContactoPrincipal,
            'emailFacturacion' => $emailFacturacion,
            'enviarPublicidad' => $enviarPublicidad,
            'estado' => $estado,
            'estadoAgenciaEnInterlocutoresComerciales' => $estadoAgenciaEnInterlocutoresComerciales,
            'idClienteComercial' => $idClienteComercial,
            'idClienteFiscal' => $idClienteFiscal,
            'idClienteRemitidoFacturas' => $idClienteRemitidoFacturas,
            'interfasadoASef' => $interfasadoASef,
            'nombreAgencia' => $nombreAgencia,
            'nombreContactoPrincipal' => $nombreContactoPrincipal,
            'nombreFiscal' => $nombreFiscal,
            'nombreOficinaAgencia' => $nombreOficinaAgencia,
            'nombreOtroContacto1' => $nombreOtroContacto1,
            'nombreOtroContacto2' => $nombreOtroContacto2,
            'nombreOtroContacto3' => $nombreOtroContacto3,
            'numeroFaxContactoPrincipal' => $numeroFaxContactoPrincipal,
            'numeroFaxFacturacion' => $numeroFaxFacturacion,
            'numeroTelefonoContactoPrincipal' => $numeroTelefonoContactoPrincipal,
            'numeroTelefonoFacturacion' => $numeroTelefonoFacturacion,
            'observacionesOtroContacto' => $observacionesOtroContacto,
            'operacionSolicitud' => $operacionSolicitud,
            'paisAgencia' => $paisAgencia,
            'paisContactoPrincipal' => $paisContactoPrincipal,
            'paisFacturacion' => $paisFacturacion,
            'paisFiscal' => $paisFiscal,
            'paisTelefonoContactoPrincipal' => $paisTelefonoContactoPrincipal,
            'paisTelefonoFacturacion' => $paisTelefonoFacturacion,
            'poblacionContactoPrincipal' => $poblacionContactoPrincipal,
            'poblacionFacturacion' => $poblacionFacturacion,
            'poblacionFiscal' => $poblacionFiscal,
            'prefijoFaxContactoPrincipal' => $prefijoFaxContactoPrincipal,
            'prefijoFaxFacturacion' => $prefijoFaxFacturacion,
            'prefijoTelefonoContactoPrincipal' => $prefijoTelefonoContactoPrincipal,
            'prefijoTelefonoFacturacion' => $prefijoTelefonoFacturacion,
            'prepago' => $prepago,
            'provinciaContactoPrincipal' => $provinciaContactoPrincipal,
            'provinciaFacturacion' => $provinciaFacturacion,
            'provinciaFiscal' => $provinciaFiscal,
            'remitenteFacturacion' => $remitenteFacturacion,
            'swivlohot' => $swivlohot,
            'usuarioId' => $usuarioId
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    } catch (\Exception $e) {
        echo $return;
        echo "ERRO AGENCIA: " . $e;
        echo $return;
    }
}

$datosUsuarioAgencia = $LoginAgenciaRsp->item(0)->getElementsByTagName("datosUsuarioAgencia");
if ($datosUsuarioAgencia->length > 0) {
    $datosUsuario = $datosUsuarioAgencia->item(0)->getElementsByTagName("datosUsuario");
    if ($datosUsuario->length > 0) {
        $usuarioId = $datosUsuario->item(0)->getElementsByTagName("usuarioId");
        if ($usuarioId->length > 0) {
            $usuarioId = $usuarioId->item(0)->nodeValue;
        } else {
            $usuarioId = "";
        }
        $agenciaFComisionable = $datosUsuario->item(0)->getElementsByTagName("agenciaFComisionable");
        if ($agenciaFComisionable->length > 0) {
            $agenciaFComisionable = $agenciaFComisionable->item(0)->nodeValue;
        } else {
            $agenciaFComisionable = "";
        }
        $agenciaFNoComisionable = $datosUsuario->item(0)->getElementsByTagName("agenciaFNoComisionable");
        if ($agenciaFNoComisionable->length > 0) {
            $agenciaFNoComisionable = $agenciaFNoComisionable->item(0)->nodeValue;
        } else {
            $agenciaFNoComisionable = "";
        }
        $apecontacto001 = $datosUsuario->item(0)->getElementsByTagName("apecontacto001");
        if ($apecontacto001->length > 0) {
            $apecontacto001 = $apecontacto001->item(0)->nodeValue;
        } else {
            $apecontacto001 = "";
        }
        $apecontacto002 = $datosUsuario->item(0)->getElementsByTagName("apecontacto002");
        if ($apecontacto002->length > 0) {
            $apecontacto002 = $apecontacto002->item(0)->nodeValue;
        } else {
            $apecontacto002 = "";
        }
        $apecontacto003 = $datosUsuario->item(0)->getElementsByTagName("apecontacto003");
        if ($apecontacto003->length > 0) {
            $apecontacto003 = $apecontacto003->item(0)->nodeValue;
        } else {
            $apecontacto003 = "";
        }
        $apellido = $datosUsuario->item(0)->getElementsByTagName("apellido");
        if ($apellido->length > 0) {
            $apellido = $apellido->item(0)->nodeValue;
        } else {
            $apellido = "";
        }
        $cambiarPsw = $datosUsuario->item(0)->getElementsByTagName("cambiarPsw");
        if ($cambiarPsw->length > 0) {
            $cambiarPsw = $cambiarPsw->item(0)->nodeValue;
        } else {
            $cambiarPsw = "";
        }
        $cargo = $datosUsuario->item(0)->getElementsByTagName("cargo");
        if ($cargo->length > 0) {
            $cargo = $cargo->item(0)->nodeValue;
        } else {
            $cargo = "";
        }
        $cargotr1 = $datosUsuario->item(0)->getElementsByTagName("cargotr1");
        if ($cargotr1->length > 0) {
            $cargotr1 = $cargotr1->item(0)->nodeValue;
        } else {
            $cargotr1 = "";
        }
        $cargotr2 = $datosUsuario->item(0)->getElementsByTagName("cargotr2");
        if ($cargotr2->length > 0) {
            $cargotr2 = $cargotr2->item(0)->nodeValue;
        } else {
            $cargotr2 = "";
        }
        $cargotr3 = $datosUsuario->item(0)->getElementsByTagName("cargotr3");
        if ($cargotr3->length > 0) {
            $cargotr3 = $cargotr3->item(0)->nodeValue;
        } else {
            $cargotr3 = "";
        }
        $ciudadNacimiento = $datosUsuario->item(0)->getElementsByTagName("ciudadNacimiento");
        if ($ciudadNacimiento->length > 0) {
            $ciudadNacimiento = $ciudadNacimiento->item(0)->nodeValue;
        } else {
            $ciudadNacimiento = "";
        }
        $codcue = $datosUsuario->item(0)->getElementsByTagName("codcue");
        if ($codcue->length > 0) {
            $codcue = $codcue->item(0)->nodeValue;
        } else {
            $codcue = "";
        }
        $codcuerpc = $datosUsuario->item(0)->getElementsByTagName("codcuerpc");
        if ($codcuerpc->length > 0) {
            $codcuerpc = $codcuerpc->item(0)->nodeValue;
        } else {
            $codcuerpc = "";
        }
        $codigoPostal = $datosUsuario->item(0)->getElementsByTagName("codigoPostal");
        if ($codigoPostal->length > 0) {
            $codigoPostal = $codigoPostal->item(0)->nodeValue;
        } else {
            $codigoPostal = "";
        }
        $codigoUsuario = $datosUsuario->item(0)->getElementsByTagName("codigoUsuario");
        if ($codigoUsuario->length > 0) {
            $codigoUsuario = $codigoUsuario->item(0)->nodeValue;
        } else {
            $codigoUsuario = "";
        }
        $codigoUsuariocontacto001 = $datosUsuario->item(0)->getElementsByTagName("codigoUsuariocontacto001");
        if ($codigoUsuariocontacto001->length > 0) {
            $codigoUsuariocontacto001 = $codigoUsuariocontacto001->item(0)->nodeValue;
        } else {
            $codigoUsuariocontacto001 = "";
        }
        $codigoUsuariocontacto002 = $datosUsuario->item(0)->getElementsByTagName("codigoUsuariocontacto002");
        if ($codigoUsuariocontacto002->length > 0) {
            $codigoUsuariocontacto002 = $codigoUsuariocontacto002->item(0)->nodeValue;
        } else {
            $codigoUsuariocontacto002 = "";
        }
        $codigoUsuariocontacto003 = $datosUsuario->item(0)->getElementsByTagName("codigoUsuariocontacto003");
        if ($codigoUsuariocontacto003->length > 0) {
            $codigoUsuariocontacto003 = $codigoUsuariocontacto003->item(0)->nodeValue;
        } else {
            $codigoUsuariocontacto003 = "";
        }
        $cuerpccontanto001 = $datosUsuario->item(0)->getElementsByTagName("cuerpccontanto001");
        if ($cuerpccontanto001->length > 0) {
            $cuerpccontanto001 = $cuerpccontanto001->item(0)->nodeValue;
        } else {
            $cuerpccontanto001 = "";
        }
        $cuerpccontanto002 = $datosUsuario->item(0)->getElementsByTagName("cuerpccontanto002");
        if ($cuerpccontanto002->length > 0) {
            $cuerpccontanto002 = $cuerpccontanto002->item(0)->nodeValue;
        } else {
            $cuerpccontanto002 = "";
        }
        $cuerpccontanto003 = $datosUsuario->item(0)->getElementsByTagName("cuerpccontanto003");
        if ($cuerpccontanto003->length > 0) {
            $cuerpccontanto003 = $cuerpccontanto003->item(0)->nodeValue;
        } else {
            $cuerpccontanto003 = "";
        }
        $direccion = $datosUsuario->item(0)->getElementsByTagName("direccion");
        if ($direccion->length > 0) {
            $direccion = $direccion->item(0)->nodeValue;
        } else {
            $direccion = "";
        }
        $emacontacto001 = $datosUsuario->item(0)->getElementsByTagName("emacontacto001");
        if ($emacontacto001->length > 0) {
            $emacontacto001 = $emacontacto001->item(0)->nodeValue;
        } else {
            $emacontacto001 = "";
        }
        $emacontacto002 = $datosUsuario->item(0)->getElementsByTagName("emacontacto002");
        if ($emacontacto002->length > 0) {
            $emacontacto002 = $emacontacto002->item(0)->nodeValue;
        } else {
            $emacontacto002 = "";
        }
        $emacontacto003 = $datosUsuario->item(0)->getElementsByTagName("emacontacto003");
        if ($emacontacto003->length > 0) {
            $emacontacto003 = $emacontacto003->item(0)->nodeValue;
        } else {
            $emacontacto003 = "";
        }
        $email = $datosUsuario->item(0)->getElementsByTagName("email");
        if ($email->length > 0) {
            $email = $email->item(0)->nodeValue;
        } else {
            $email = "";
        }
        $enviarPublicidad = $datosUsuario->item(0)->getElementsByTagName("enviarPublicidad");
        if ($enviarPublicidad->length > 0) {
            $enviarPublicidad = $enviarPublicidad->item(0)->nodeValue;
        } else {
            $enviarPublicidad = "";
        }
        $estado = $datosUsuario->item(0)->getElementsByTagName("estado");
        if ($estado->length > 0) {
            $estado = $estado->item(0)->nodeValue;
        } else {
            $estado = "";
        }
        $estadoCivil = $datosUsuario->item(0)->getElementsByTagName("estadoCivil");
        if ($estadoCivil->length > 0) {
            $estadoCivil = $estadoCivil->item(0)->nodeValue;
        } else {
            $estadoCivil = "";
        }
        $faxNumero = $datosUsuario->item(0)->getElementsByTagName("faxNumero");
        if ($faxNumero->length > 0) {
            $faxNumero = $faxNumero->item(0)->nodeValue;
        } else {
            $faxNumero = "";
        }
        $faxPais = $datosUsuario->item(0)->getElementsByTagName("faxPais");
        if ($faxPais->length > 0) {
            $faxPais = $faxPais->item(0)->nodeValue;
        } else {
            $faxPais = "";
        }
        $faxPrefijo = $datosUsuario->item(0)->getElementsByTagName("faxPrefijo");
        if ($faxPrefijo->length > 0) {
            $faxPrefijo = $faxPrefijo->item(0)->nodeValue;
        } else {
            $faxPrefijo = "";
        }
        $fechaCaducidadPsw = $datosUsuario->item(0)->getElementsByTagName("fechaCaducidadPsw");
        if ($fechaCaducidadPsw->length > 0) {
            $fechaCaducidadPsw = $fechaCaducidadPsw->item(0)->nodeValue;
        } else {
            $fechaCaducidadPsw = "";
        }
        $fechaNacimiento = $datosUsuario->item(0)->getElementsByTagName("fechaNacimiento");
        if ($fechaNacimiento->length > 0) {
            $fechaNacimiento = $fechaNacimiento->item(0)->nodeValue;
        } else {
            $fechaNacimiento = "";
        }
        $idioma = $datosUsuario->item(0)->getElementsByTagName("idioma");
        if ($idioma->length > 0) {
            $idioma = $idioma->item(0)->nodeValue;
        } else {
            $idioma = "";
        }
        $localidad = $datosUsuario->item(0)->getElementsByTagName("localidad");
        if ($localidad->length > 0) {
            $localidad = $localidad->item(0)->nodeValue;
        } else {
            $localidad = "";
        }
        $medioComunicacion = $datosUsuario->item(0)->getElementsByTagName("medioComunicacion");
        if ($medioComunicacion->length > 0) {
            $medioComunicacion = $medioComunicacion->item(0)->nodeValue;
        } else {
            $medioComunicacion = "";
        }
        $movilNumero = $datosUsuario->item(0)->getElementsByTagName("movilNumero");
        if ($movilNumero->length > 0) {
            $movilNumero = $movilNumero->item(0)->nodeValue;
        } else {
            $movilNumero = "";
        }
        $movilPais = $datosUsuario->item(0)->getElementsByTagName("movilPais");
        if ($movilPais->length > 0) {
            $movilPais = $movilPais->item(0)->nodeValue;
        } else {
            $movilPais = "";
        }
        $movilPrefijo = $datosUsuario->item(0)->getElementsByTagName("movilPrefijo");
        if ($movilPrefijo->length > 0) {
            $movilPrefijo = $movilPrefijo->item(0)->nodeValue;
        } else {
            $movilPrefijo = "";
        }
        $nombre = $datosUsuario->item(0)->getElementsByTagName("nombre");
        if ($nombre->length > 0) {
            $nombre = $nombre->item(0)->nodeValue;
        } else {
            $nombre = "";
        }
        $nombreEmpresa = $datosUsuario->item(0)->getElementsByTagName("nombreEmpresa");
        if ($nombreEmpresa->length > 0) {
            $nombreEmpresa = $nombreEmpresa->item(0)->nodeValue;
        } else {
            $nombreEmpresa = "";
        }
        $nomcontacto001 = $datosUsuario->item(0)->getElementsByTagName("nomcontacto001");
        if ($nomcontacto001->length > 0) {
            $nomcontacto001 = $nomcontacto001->item(0)->nodeValue;
        } else {
            $nomcontacto001 = "";
        }
        $nomcontacto002 = $datosUsuario->item(0)->getElementsByTagName("nomcontacto002");
        if ($nomcontacto002->length > 0) {
            $nomcontacto002 = $nomcontacto002->item(0)->nodeValue;
        } else {
            $nomcontacto002 = "";
        }
        $nomcontacto003 = $datosUsuario->item(0)->getElementsByTagName("nomcontacto003");
        if ($nomcontacto003->length > 0) {
            $nomcontacto003 = $nomcontacto003->item(0)->nodeValue;
        } else {
            $nomcontacto003 = "";
        }
        $numero = $datosUsuario->item(0)->getElementsByTagName("numero");
        if ($numero->length > 0) {
            $numero = $numero->item(0)->nodeValue;
        } else {
            $numero = "";
        }
        $numeroHijos = $datosUsuario->item(0)->getElementsByTagName("numeroHijos");
        if ($numeroHijos->length > 0) {
            $numeroHijos = $numeroHijos->item(0)->nodeValue;
        } else {
            $numeroHijos = "";
        }
        $pais = $datosUsuario->item(0)->getElementsByTagName("pais");
        if ($pais->length > 0) {
            $pais = $pais->item(0)->nodeValue;
        } else {
            $pais = "";
        }
        $password = $datosUsuario->item(0)->getElementsByTagName("password");
        if ($password->length > 0) {
            $password = $password->item(0)->nodeValue;
        } else {
            $password = "";
        }
        $poblacion = $datosUsuario->item(0)->getElementsByTagName("poblacion");
        if ($poblacion->length > 0) {
            $poblacion = $poblacion->item(0)->nodeValue;
        } else {
            $poblacion = "";
        }
        $prepago = $datosUsuario->item(0)->getElementsByTagName("prepago");
        if ($prepago->length > 0) {
            $prepago = $prepago->item(0)->nodeValue;
        } else {
            $prepago = "";
        }
        $provincia = $datosUsuario->item(0)->getElementsByTagName("provincia");
        if ($provincia->length > 0) {
            $provincia = $provincia->item(0)->nodeValue;
        } else {
            $provincia = "";
        }
        $sector = $datosUsuario->item(0)->getElementsByTagName("sector");
        if ($sector->length > 0) {
            $sector = $sector->item(0)->nodeValue;
        } else {
            $sector = "";
        }
        $sexo = $datosUsuario->item(0)->getElementsByTagName("sexo");
        if ($sexo->length > 0) {
            $sexo = $sexo->item(0)->nodeValue;
        } else {
            $sexo = "";
        }
        $telefonoNumero = $datosUsuario->item(0)->getElementsByTagName("telefonoNumero");
        if ($telefonoNumero->length > 0) {
            $telefonoNumero = $telefonoNumero->item(0)->nodeValue;
        } else {
            $telefonoNumero = "";
        }
        $telefonoPais = $datosUsuario->item(0)->getElementsByTagName("telefonoPais");
        if ($telefonoPais->length > 0) {
            $telefonoPais = $telefonoPais->item(0)->nodeValue;
        } else {
            $telefonoPais = "";
        }
        $telefonoPrefijo = $datosUsuario->item(0)->getElementsByTagName("telefonoPrefijo");
        if ($telefonoPrefijo->length > 0) {
            $telefonoPrefijo = $telefonoPrefijo->item(0)->nodeValue;
        } else {
            $telefonoPrefijo = "";
        }
        $titular = $datosUsuario->item(0)->getElementsByTagName("titular");
        if ($titular->length > 0) {
            $titular = $titular->item(0)->nodeValue;
        } else {
            $titular = "";
        }

        
        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('login_datosUsuario');
            $insert->values(array(
                'usuarioId' => $usuarioId,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'agenciaFComisionable' => $agenciaFComisionable,
                'agenciaFNoComisionable' => $agenciaFNoComisionable,
                'apecontacto001' => $apecontacto001,
                'apecontacto002' => $apecontacto002,
                'apecontacto003' => $apecontacto003,
                'apellido' => $apellido,
                'cambiarPsw' => $cambiarPsw,
                'cargo' => $cargo,
                'cargotr1' => $cargotr1,
                'cargotr2' => $cargotr2,
                'cargotr3' => $cargotr3,
                'ciudadNacimiento' => $ciudadNacimiento,
                'codcue' => $codcue,
                'codcuerpc' => $codcuerpc,
                'codigoPostal' => $codigoPostal,
                'codigoUsuario' => $codigoUsuario,
                'codigoUsuariocontacto001' => $codigoUsuariocontacto001,
                'codigoUsuariocontacto002' => $codigoUsuariocontacto002,
                'codigoUsuariocontacto003' => $codigoUsuariocontacto003,
                'cuerpccontanto001' => $cuerpccontanto001,
                'cuerpccontanto002' => $cuerpccontanto002,
                'cuerpccontanto003' => $cuerpccontanto003,
                'direccion' => $direccion,
                'emacontacto001' => $emacontacto001,
                'emacontacto002' => $emacontacto002,
                'emacontacto003' => $emacontacto003,
                'email' => $email,
                'enviarPublicidad' => $enviarPublicidad,
                'estado' => $estado,
                'estadoCivil' => $estadoCivil,
                'faxNumero' => $faxNumero,
                'faxPais' => $faxPais,
                'faxPrefijo' => $faxPrefijo,
                'fechaCaducidadPsw' => $fechaCaducidadPsw,
                'fechaNacimiento' => $fechaNacimiento,
                'idioma' => $idioma,
                'localidad' => $localidad,
                'medioComunicacion' => $medioComunicacion,
                'movilNumero' => $movilNumero,
                'movilPais' => $movilPais,
                'movilPrefijo' => $movilPrefijo,
                'nombre' => $nombre,
                'nombreEmpresa' => $nombreEmpresa,
                'nomcontacto001' => $nomcontacto001,
                'nomcontacto002' => $nomcontacto002,
                'nomcontacto003' => $nomcontacto003,
                'numero' => $numero,
                'numeroHijos' => $numeroHijos,
                'pais' => $pais,
                'password' => $password,
                'poblacion' => $poblacion,
                'prepago' => $prepago,
                'provincia' => $provincia,
                'sector' => $sector,
                'sexo' => $sexo,
                'telefonoNumero' => $telefonoNumero,
                'telefonoPais' => $telefonoPais,
                'telefonoPrefijo' => $telefonoPrefijo,
                'titular' => $titular
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } catch (\Exception $e) {
            echo $return;
            echo "ERRO USUARIO: " . $e;
            echo $return;
        }
    }

    $listaCaracteristica = $datosUsuarioAgencia->item(0)->getElementsByTagName("listaCaracteristica");
    if ($listaCaracteristica->length > 0){
        $CaracteristicaDto = $listaCaracteristica->item(0)->getElementsByTagName("CaracteristicaDto");
        if ($CaracteristicaDto->length > 0){
            for ($i=0; $i < $CaracteristicaDto->length; $i++) {
                $caracteristica = $CaracteristicaDto->item(0)->getElementsByTagName("caracteristica");
                if ($caracteristica->length > 0) {
                    $caracteristica = $caracteristica->item(0)->nodeValue;
                } else {
                    $caracteristica = "";
                }
                $operacion = $CaracteristicaDto->item(0)->getElementsByTagName("operacion");
                if ($operacion->length > 0) {
                    $operacion = $operacion->item(0)->nodeValue;
                } else {
                    $operacion = "";
                }
                $tipoCaracteristica = $CaracteristicaDto->item(0)->getElementsByTagName("tipoCaracteristica");
                if ($tipoCaracteristica->length > 0) {
                    $tipoCaracteristica = $tipoCaracteristica->item(0)->nodeValue;
                } else {
                    $tipoCaracteristica = "";
                }

                try {
                    $sql = new Sql($db);
                    $insert = $sql->insert();
                    $insert->into('login_listaCaracteristica');
                    $insert->values(array(
                        'datetime_created' => time(),
                        'datetime_updated' => 0,
                        'caracteristica' => $caracteristica,
                        'operacion' => $operacion,
                        'tipoCaracteristica' => $tipoCaracteristica
                    ), $insert::VALUES_MERGE);
                    $statement = $sql->prepareStatementForSqlObject($insert);
                    $results = $statement->execute();
                    $db->getDriver()
                        ->getConnection()
                        ->disconnect();
                } catch (\Exception $e) {
                    echo $return;
                    echo "ERRO CARACT: " . $e;
                    echo $return;
                }
            }
        }
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
