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
echo "COMECOU PRE RESERVA";
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

$config = new \Zend\Config\Config(include '../config/autoload/global.europamundo.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

$user = 'CTMWS';
$pass = 'Ctmws123';


$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
<soapenv:Header/>
<soapenv:Body>
  <tem:setPreReserva>
    <tem:Localizador>XXXVV41</tem:Localizador>
    <tem:Fecha>20190630</tem:Fecha>
    <tem:ID_Viaje>17480</tem:ID_Viaje>
    <tem:Rotativo>N</tem:Rotativo>
    <tem:Sector>N</tem:Sector>
    <tem:Paradas>S</tem:Paradas>
    <tem:Ciudad_Incorporacion>0</tem:Ciudad_Incorporacion>
    <tem:Ciudad_Finalizacion>0</tem:Ciudad_Finalizacion>
    <tem:Ciudad_Parada>5</tem:Ciudad_Parada>
    <tem:Tiempo_Parada>14</tem:Tiempo_Parada>
    <tem:Plazas>2</tem:Plazas>
    <tem:PlazasAdulto>1</tem:PlazasAdulto>
    <tem:PlazasNinio>1</tem:PlazasNinio>
    <tem:Pasajeros>
      <tem:ArrayOfString>
        <tem:string>Appellido apellido</tem:string>
        <tem:string>Nombre adulto</tem:string>
        <tem:string>30</tem:string>
        <tem:string>adulto</tem:string>
        <tem:string>H</tem:string>
        <tem:string>S</tem:string>
        <tem:string>N</tem:string>
        <tem:string></tem:string>
        <tem:string>N</tem:string>
        <tem:string>1122331d</tem:string>
        <tem:string>1122331p</tem:string>
        <tem:string>19800312</tem:string>
        <tem:string>italiana</tem:string>
      </tem:ArrayOfString>
      <tem:ArrayOfString>
        <tem:string>Appellido apellido</tem:string>
        <tem:string>Nombre ninio</tem:string>
        <tem:string>5</tem:string>
        <tem:string>nino</tem:string>
        <tem:string>M</tem:string>
        <tem:string>S</tem:string>
        <tem:string>N</tem:string>
        <tem:string></tem:string>
        <tem:string>N</tem:string>
        <tem:string>1122332d</tem:string>
        <tem:string>1122332p</tem:string>
        <tem:string>20000909</tem:string>
        <tem:string>italiana</tem:string>
      </tem:ArrayOfString>
    </tem:Pasajeros>
    <tem:Observaciones>texto obsv</tem:Observaciones>
    <tem:NOCHES_ADICIONALES_INICIO>0</tem:NOCHES_ADICIONALES_INICIO>
    <tem:NOCHES_ADICIONALES_FINAL>0</tem:NOCHES_ADICIONALES_FINAL>
    <tem:Observaciones_Bono>lo que me da la gana</tem:Observaciones_Bono>
    <tem:TrasladoLlegada>
      <tem:CO_RESERVA></tem:CO_RESERVA>
      <tem:TIPO_TRASLADO>L</tem:TIPO_TRASLADO>
      <tem:MODIFICACION>0</tem:MODIFICACION>
      <tem:FECHA>20190630</tem:FECHA>
      <tem:CIUDAD>Roma</tem:CIUDAD>
      <tem:OPCION_ELEGIDA>FD</tem:OPCION_ELEGIDA>
      <tem:NUM_VUELO></tem:NUM_VUELO>
      <tem:COMPANYA_AEREA></tem:COMPANYA_AEREA>
      <tem:AEROPUERTO></tem:AEROPUERTO>
      <tem:CIUDAD_PROC_DEST></tem:CIUDAD_PROC_DEST>
      <tem:NOMBRE_HOTEL></tem:NOMBRE_HOTEL>
      <tem:DIRECCION_HOTEL></tem:DIRECCION_HOTEL>
      <tem:HORA_RECOGIDA></tem:HORA_RECOGIDA>
      <tem:HOTEL_RECOGIDA></tem:HOTEL_RECOGIDA>
      <tem:ID_HOTEL></tem:ID_HOTEL>
      <tem:DIRECCION_LLEGADA></tem:DIRECCION_LLEGADA>
      <tem:DIRECCION_DESTINO></tem:DIRECCION_DESTINO>
      <tem:OPC_FALTAN_DAT>PD</tem:OPC_FALTAN_DAT>
      <tem:MODIFICADO_MENOS_48H></tem:MODIFICADO_MENOS_48H>
      <tem:IDAGENTE_MODIFICACION>0</tem:IDAGENTE_MODIFICACION>
      <tem:FECHA_MODIFICACION></tem:FECHA_MODIFICACION>
    </tem:TrasladoLlegada>
    <tem:TrasladoSalida>
      <tem:CO_RESERVA></tem:CO_RESERVA>
      <tem:TIPO_TRASLADO>S</tem:TIPO_TRASLADO>
      <tem:MODIFICACION>0</tem:MODIFICACION>
      <tem:FECHA>20190729</tem:FECHA>
      <tem:CIUDAD>Praga</tem:CIUDAD>
      <tem:OPCION_ELEGIDA>FD</tem:OPCION_ELEGIDA>
      <tem:NUM_VUELO></tem:NUM_VUELO>
      <tem:COMPANYA_AEREA></tem:COMPANYA_AEREA>
      <tem:AEROPUERTO></tem:AEROPUERTO>
      <tem:CIUDAD_PROC_DEST></tem:CIUDAD_PROC_DEST>
      <tem:NOMBRE_HOTEL></tem:NOMBRE_HOTEL>
      <tem:DIRECCION_HOTEL></tem:DIRECCION_HOTEL>
      <tem:HORA_RECOGIDA></tem:HORA_RECOGIDA>
      <tem:HOTEL_RECOGIDA></tem:HOTEL_RECOGIDA>
      <tem:ID_HOTEL></tem:ID_HOTEL>
      <tem:DIRECCION_LLEGADA></tem:DIRECCION_LLEGADA>
      <tem:DIRECCION_DESTINO></tem:DIRECCION_DESTINO>
      <tem:OPC_FALTAN_DAT>PD</tem:OPC_FALTAN_DAT>
      <tem:MODIFICADO_MENOS_48H></tem:MODIFICADO_MENOS_48H>
      <tem:IDAGENTE_MODIFICACION>0</tem:IDAGENTE_MODIFICACION>
      <tem:FECHA_MODIFICACION></tem:FECHA_MODIFICACION>
    </tem:TrasladoSalida>
    <tem:userName>CTMWS</tem:userName>
    <tem:userPassword>Ctmws123</tem:userPassword>
  </tem:setPreReserva>
</soapenv:Body>
</soapenv:Envelope>';

echo $return;
echo $raw;
echo $return;
$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setHeaders(array(
    "Content-Type: text/xml",
    "Accept: text/xml",
    "Content-length: " . strlen($raw)
));

$client->setUri('http://desarrollo.selfip.com/webserv/ServiceDatos.asmx');
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
echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();
}

echo "<xmp>";
var_dump($response);
echo "</xmp>";

$config = new \Zend\Config\Config(include '../config/autoload/global.europamundo.php');
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
$setPreReservaResponse = $Body->item(0)->getElementsByTagName("setPreReservaResponse");
$setPreReservaResult = $setPreReservaResponse->item(0)->getElementsByTagName("setPreReservaResult");

//diffgram
$diffgram = $setPreReservaResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $PreReserva = $diffgram->item(0)->getElementsByTagName("PreReserva");
    if ($PreReserva->length > 0) {
        $PreReserva2 = $PreReserva->item(0)->getElementsByTagName("PreReserva");
        if ($PreReserva2->length > 0) {
            $Codigo_Reserva = $PreReserva2->item(0)->getElementsByTagName("Codigo_Reserva");
            if ($Codigo_Reserva->length > 0) {
                $Codigo_Reserva = $Codigo_Reserva->item(0)->nodeValue;
            } else {
                $Codigo_Reserva = "";
            }
            $Fecha_PreReserva = $PreReserva2->item(0)->getElementsByTagName("Fecha_PreReserva");
            if ($Fecha_PreReserva->length > 0) {
                $Fecha_PreReserva = $Fecha_PreReserva->item(0)->nodeValue;
            } else {
                $Fecha_PreReserva = "";
            }
            $Hora_PreReserva = $PreReserva2->item(0)->getElementsByTagName("Hora_PreReserva");
            if ($Hora_PreReserva->length > 0) {
                $Hora_PreReserva = $Hora_PreReserva->item(0)->nodeValue;
            } else {
                $Hora_PreReserva = "";
            }
            $Fecha_Limite_Confirma = $PreReserva2->item(0)->getElementsByTagName("Fecha_Limite_Confirma");
            if ($Fecha_Limite_Confirma->length > 0) {
                $Fecha_Limite_Confirma = $Fecha_Limite_Confirma->item(0)->nodeValue;
            } else {
                $Fecha_Limite_Confirma = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('prereserva');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'Codigo_Reserva' => $Codigo_Reserva,
                    'Fecha_PreReserva' => $Fecha_PreReserva,
                    'Hora_PreReserva' => $Hora_PreReserva,
                    'Fecha_Limite_Confirma' => $Fecha_Limite_Confirma
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
    }
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>
