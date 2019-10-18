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
echo "COMECOU PRESSUPOSTO";
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

$raw = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getPresupuesto xmlns="http://tempuri.org/">
      <Id_Viaje>17480</Id_Viaje>
      <Fecha>20190811</Fecha>
      <Temporada>2019</Temporada>
      <Pais>ESP</Pais>
      <userName>CTMWS</userName>
      <userPassword>Ctmws123</userPassword>
      <Pasajeros>
        <ArrayOfString>
          <string>SG</string>
          <string></string>
        </ArrayOfString>
      </Pasajeros>
      <nochesAdicionalesIn>0</nochesAdicionalesIn>
      <nochesAdicionalesFin>0</nochesAdicionalesFin>
      <TrasladoLlegada>false</TrasladoLlegada>
      <TrasladoSalida>false</TrasladoSalida>
      <LugarTraladoLlegada></LugarTraladoLlegada>
      <LugarTrasladoSalida></LugarTrasladoSalida>
      <Guardar>false</Guardar>
    </getPresupuesto>
  </soap:Body>
</soap:Envelope>';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/">
  <soapenv:Header/>
  <soapenv:Body>
    <tem:getPresupuesto>
      <tem:Id_Viaje>17480</tem:Id_Viaje>
      <tem:Fecha>20190630</tem:Fecha>
      <tem:Temporada>2019</tem:Temporada>
      <tem:Pais>ESP</tem:Pais>
      <tem:userName>CTMWS</tem:userName>
      <tem:userPassword>Ctmws123</tem:userPassword>
      <tem:Pasajeros>
        <tem:ArrayOfString> 
          <tem:string>SG</tem:string>
          <tem:string></tem:string>
          <tem:string>0</tem:string>
          <tem:string>0</tem:string>
          <tem:string>N</tem:string>
          <tem:string>A</tem:string>
          <tem:string>N</tem:string>
        </tem:ArrayOfString>
      </tem:Pasajeros>
      <tem:nochesAdicionalesIn>0</tem:nochesAdicionalesIn>
      <tem:nochesAdicionalesFin>0</tem:nochesAdicionalesFin>
      <tem:TrasladoLlegada>false</tem:TrasladoLlegada>
      <tem:TrasladoSalida>false</tem:TrasladoSalida>
      <tem:LugarTraladoLlegada></tem:LugarTraladoLlegada>
      <tem:LugarTrasladoSalida></tem:LugarTrasladoSalida>
      <tem:Guardar>false</tem:Guardar>
    </tem:getPresupuesto>
  </soapenv:Body>
</soapenv:Envelope>
';

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
echo "<xmp>";
var_dump($response);
echo "</xmp>";

echo $return;
echo $response->getStatusCode() . " - " . $response->getReasonPhrase();
echo $return;
die();
}

echo "<xmp>";
var_dump($response);
echo "</xmp>";
die();
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
$getPresupuestoResponse = $Body->item(0)->getElementsByTagName("getPresupuestoResponse");
$getPresupuestoResult = $getPresupuestoResponse->item(0)->getElementsByTagName("getPresupuestoResult");

//diffgram
$diffgram = $getPresupuestoResult->item(0)->getElementsByTagName("diffgram");
if ($diffgram->length > 0) {
    $Total = $diffgram->item(0)->getElementsByTagName("Total");
    if ($Total->length > 0) {
        $Presupuesto = $Total->item(0)->getElementsByTagName("Presupuesto");
        if ($Presupuesto->length > 0) {
            $PrecioViaje = $Presupuesto->item(0)->getElementsByTagName("PrecioViaje");
            if ($PrecioViaje->length > 0) {
                $PrecioViaje = $PrecioViaje->item(0)->nodeValue;
            } else {
                $PrecioViaje = "";
            }
            $PrecioMP = $Presupuesto->item(0)->getElementsByTagName("PrecioMP");
            if ($PrecioMP->length > 0) {
                $PrecioMP = $PrecioMP->item(0)->nodeValue;
            } else {
                $PrecioMP = "";
            }
            $NochesInicio = $Presupuesto->item(0)->getElementsByTagName("NochesInicio");
            if ($NochesInicio->length > 0) {
                $NochesInicio = $NochesInicio->item(0)->nodeValue;
            } else {
                $NochesInicio = "";
            }
            $NochesFin = $Presupuesto->item(0)->getElementsByTagName("NochesFin");
            if ($NochesFin->length > 0) {
                $NochesFin = $NochesFin->item(0)->nodeValue;
            } else {
                $NochesFin = "";
            }
            $SupIndPrecioViaje = $Presupuesto->item(0)->getElementsByTagName("SupIndPrecioViaje");
            if ($SupIndPrecioViaje->length > 0) {
                $SupIndPrecioViaje = $SupIndPrecioViaje->item(0)->nodeValue;
            } else {
                $SupIndPrecioViaje = "";
            }
            $SupIndNochesInicio = $Presupuesto->item(0)->getElementsByTagName("SupIndNochesInicio");
            if ($SupIndNochesInicio->length > 0) {
                $SupIndNochesInicio = $SupIndNochesInicio->item(0)->nodeValue;
            } else {
                $SupIndNochesInicio = "";
            }
            $SupIndNochesFin = $Presupuesto->item(0)->getElementsByTagName("SupIndNochesFin");
            if ($SupIndNochesFin->length > 0) {
                $SupIndNochesFin = $SupIndNochesFin->item(0)->nodeValue;
            } else {
                $SupIndNochesFin = "";
            }
            $TrasladoLlegada = $Presupuesto->item(0)->getElementsByTagName("TrasladoLlegada");
            if ($TrasladoLlegada->length > 0) {
                $TrasladoLlegada = $TrasladoLlegada->item(0)->nodeValue;
            } else {
                $TrasladoLlegada = "";
            }
            $TrasladoSalida = $Presupuesto->item(0)->getElementsByTagName("TrasladoSalida");
            if ($TrasladoSalida->length > 0) {
                $TrasladoSalida = $TrasladoSalida->item(0)->nodeValue;
            } else {
                $TrasladoSalida = "";
            }
            $DescTrPrecioViaje = $Presupuesto->item(0)->getElementsByTagName("DescTrPrecioViaje");
            if ($DescTrPrecioViaje->length > 0) {
                $DescTrPrecioViaje = $DescTrPrecioViaje->item(0)->nodeValue;
            } else {
                $DescTrPrecioViaje = "";
            }
            $DescTrNochesIn = $Presupuesto->item(0)->getElementsByTagName("DescTrNochesIn");
            if ($DescTrNochesIn->length > 0) {
                $DescTrNochesIn = $DescTrNochesIn->item(0)->nodeValue;
            } else {
                $DescTrNochesIn = "";
            }
            $DescTrNochesFin = $Presupuesto->item(0)->getElementsByTagName("DescTrNochesFin");
            if ($DescTrNochesFin->length > 0) {
                $DescTrNochesFin = $DescTrNochesFin->item(0)->nodeValue;
            } else {
                $DescTrNochesFin = "";
            }
            $DescPCPrecioViaje = $Presupuesto->item(0)->getElementsByTagName("DescPCPrecioViaje");
            if ($DescPCPrecioViaje->length > 0) {
                $DescPCPrecioViaje = $DescPCPrecioViaje->item(0)->nodeValue;
            } else {
                $DescPCPrecioViaje = "";
            }
            $DescPCNochesIn = $Presupuesto->item(0)->getElementsByTagName("DescPCNochesIn");
            if ($DescPCNochesIn->length > 0) {
                $DescPCNochesIn = $DescPCNochesIn->item(0)->nodeValue;
            } else {
                $DescPCNochesIn = "";
            }
            $DescPCNochesFin = $Presupuesto->item(0)->getElementsByTagName("DescPCNochesFin");
            if ($DescPCNochesFin->length > 0) {
                $DescPCNochesFin = $DescPCNochesFin->item(0)->nodeValue;
            } else {
                $DescPCNochesFin = "";
            }
            $DescMenorPrecioViaje = $Presupuesto->item(0)->getElementsByTagName("DescMenorPrecioViaje");
            if ($DescMenorPrecioViaje->length > 0) {
                $DescMenorPrecioViaje = $DescMenorPrecioViaje->item(0)->nodeValue;
            } else {
                $DescMenorPrecioViaje = "";
            }
            $DescMenorNochesIn = $Presupuesto->item(0)->getElementsByTagName("DescMenorNochesIn");
            if ($DescMenorNochesIn->length > 0) {
                $DescMenorNochesIn = $DescMenorNochesIn->item(0)->nodeValue;
            } else {
                $DescMenorNochesIn = "";
            }
            $DescMenorNochesFin = $Presupuesto->item(0)->getElementsByTagName("DescMenorNochesFin");
            if ($DescMenorNochesFin->length > 0) {
                $DescMenorNochesFin = $DescMenorNochesFin->item(0)->nodeValue;
            } else {
                $DescMenorNochesFin = "";
            }
            $DescPCSupIndPrecioViaje = $Presupuesto->item(0)->getElementsByTagName("DescPCSupIndPrecioViaje");
            if ($DescPCSupIndPrecioViaje->length > 0) {
                $DescPCSupIndPrecioViaje = $DescPCSupIndPrecioViaje->item(0)->nodeValue;
            } else {
                $DescPCSupIndPrecioViaje = "";
            }
            $DescPCSupIndNochesIn = $Presupuesto->item(0)->getElementsByTagName("DescPCSupIndNochesIn");
            if ($DescPCSupIndNochesIn->length > 0) {
                $DescPCSupIndNochesIn = $DescPCSupIndNochesIn->item(0)->nodeValue;
            } else {
                $DescPCSupIndNochesIn = "";
            }
            $DescPCSupIndNochesFin = $Presupuesto->item(0)->getElementsByTagName("DescPCSupIndNochesFin");
            if ($DescPCSupIndNochesFin->length > 0) {
                $DescPCSupIndNochesFin = $DescPCSupIndNochesFin->item(0)->nodeValue;
            } else {
                $DescPCSupIndNochesFin = "";
            }
            $DescMenorSupIndPrecioViaje = $Presupuesto->item(0)->getElementsByTagName("DescMenorSupIndPrecioViaje");
            if ($DescMenorSupIndPrecioViaje->length > 0) {
                $DescMenorSupIndPrecioViaje = $DescMenorSupIndPrecioViaje->item(0)->nodeValue;
            } else {
                $DescMenorSupIndPrecioViaje = "";
            }
            $DescMenorSupIndNochesIn = $Presupuesto->item(0)->getElementsByTagName("DescMenorSupIndNochesIn");
            if ($DescMenorSupIndNochesIn->length > 0) {
                $DescMenorSupIndNochesIn = $DescMenorSupIndNochesIn->item(0)->nodeValue;
            } else {
                $DescMenorSupIndNochesIn = "";
            }
            $DescMenorSupIndNochesFin = $Presupuesto->item(0)->getElementsByTagName("DescMenorSupIndNochesFin");
            if ($DescMenorSupIndNochesFin->length > 0) {
                $DescMenorSupIndNochesFin = $DescMenorSupIndNochesFin->item(0)->nodeValue;
            } else {
                $DescMenorSupIndNochesFin = "";
            }
            $CoPresupuesto = $Presupuesto->item(0)->getElementsByTagName("CoPresupuesto");
            if ($CoPresupuesto->length > 0) {
                $CoPresupuesto = $CoPresupuesto->item(0)->nodeValue;
            } else {
                $CoPresupuesto = "";
            }

            try {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('pressuposto');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'PrecioViaje' => $PrecioViaje,
                    'PrecioMP' => $PrecioMP,
                    'NochesInicio' => $NochesInicio,
                    'NochesFin' => $NochesFin,
                    'SupIndPrecioViaje' => $SupIndPrecioViaje,
                    'SupIndNochesInicio' => $SupIndNochesInicio,
                    'SupIndNochesFin' => $SupIndNochesFin,
                    'TrasladoLlegada' => $TrasladoLlegada,
                    'TrasladoSalida' => $TrasladoSalida,
                    'DescTrPrecioViaje' => $DescTrPrecioViaje,
                    'DescTrNochesIn' => $DescTrNochesIn,
                    'DescTrNochesFin' => $DescTrNochesFin,
                    'DescPCPrecioViaje' => $DescPCPrecioViaje,
                    'DescPCNochesIn' => $DescPCNochesIn,
                    'DescPCNochesFin' => $DescPCNochesFin,
                    'DescMenorPrecioViaje' => $DescMenorPrecioViaje,
                    'DescMenorNochesIn' => $DescMenorNochesIn,
                    'DescMenorNochesFin' => $DescMenorNochesFin,
                    'DescPCSupIndPrecioViaje' => $DescPCSupIndPrecioViaje,
                    'DescPCSupIndNochesIn' => $DescPCSupIndNochesIn,
                    'DescPCSupIndNochesFin' => $DescPCSupIndNochesFin,
                    'DescMenorSupIndPrecioViaje' => $DescMenorSupIndPrecioViaje,
                    'DescMenorSupIndNochesIn' => $DescMenorSupIndNochesIn,
                    'DescMenorSupIndNochesFin' => $DescMenorSupIndNochesFin,
                    'CoPresupuesto' => $CoPresupuesto
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
