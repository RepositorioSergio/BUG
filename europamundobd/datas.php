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
echo "COMECOU DATAS";
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

$sql = "SELECT ID_Viaje FROM viagens";
$statement = $db->createStatement($sql);
try {
    $statement->prepare();
} catch (\Exception $e) {
    echo $return;
    echo $e->getMessage();
    echo $return;
    die();
}

$result = $statement->execute();
$result->buffer();
if ($result instanceof ResultInterface && $result->isQueryResult()) {
    $resultSet = new ResultSet();
    $resultSet->initialize($result);
    foreach ($resultSet as $row) {
        $ID_Viaje = $row->ID_Viaje;
        echo $return;
        echo $ID_Viaje;
        echo $return;

        $user = 'CTMWS';
        $pass = 'Ctmws123';

        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <getFechas xmlns="http://tempuri.org/">
            <Id_Viaje>' . $ID_Viaje . '</Id_Viaje>
            <userName>' . $user . '</userName>
            <userPassword>' . $pass . '</userPassword>
            </getFechas>
        </soap:Body>
        </soap:Envelope>';

        if ($ID_Viaje != 2794 and $ID_Viaje != 2537 and $ID_Viaje != 3035 and $ID_Viaje != 3730 and $ID_Viaje != 3589 and $ID_Viaje != 4510 and $ID_Viaje != 4568 and $ID_Viaje != 4675 and $ID_Viaje != 5545 and $ID_Viaje != 5659 and $ID_Viaje != 6002) {
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
            //die();
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
            $getFechasResponse = $Body->item(0)->getElementsByTagName("getFechasResponse");
            $getFechasResult = $getFechasResponse->item(0)->getElementsByTagName("getFechasResult");

            //diffgram
            $diffgram = $getFechasResult->item(0)->getElementsByTagName("diffgram");
            if ($diffgram->length > 0) {
                $NewDataSet = $diffgram->item(0)->getElementsByTagName("NewDataSet");
                if ($NewDataSet->length > 0) {
                    $Table = $NewDataSet->item(0)->getElementsByTagName("Table");
                    if ($Table->length > 0) {
                        for ($i=0; $i < $Table->length; $i++) { 
                            $Fecha = $Table->item($i)->getElementsByTagName("Fecha");
                            if ($Fecha->length > 0) {
                                $Fecha = $Fecha->item(0)->nodeValue;
                            } else {
                                $Fecha = "";
                            }
                            $Nombre_Temporada = $Table->item($i)->getElementsByTagName("Nombre_Temporada");
                            if ($Nombre_Temporada->length > 0) {
                                $Nombre_Temporada = $Nombre_Temporada->item(0)->nodeValue;
                            } else {
                                $Nombre_Temporada = "";
                            }
                            $PLAZAS_DISPONIBLES = $Table->item($i)->getElementsByTagName("PLAZAS_DISPONIBLES");
                            if ($PLAZAS_DISPONIBLES->length > 0) {
                                $PLAZAS_DISPONIBLES = $PLAZAS_DISPONIBLES->item(0)->nodeValue;
                            } else {
                                $PLAZAS_DISPONIBLES = "";
                            }
                            $BAJO_PETICION = $Table->item($i)->getElementsByTagName("BAJO_PETICION");
                            if ($BAJO_PETICION->length > 0) {
                                $BAJO_PETICION = $BAJO_PETICION->item(0)->nodeValue;
                            } else {
                                $BAJO_PETICION = "";
                            }
                            $PLAZAS_PETICION = $Table->item($i)->getElementsByTagName("PLAZAS_PETICION");
                            if ($PLAZAS_PETICION->length > 0) {
                                $PLAZAS_PETICION = $PLAZAS_PETICION->item(0)->nodeValue;
                            } else {
                                $PLAZAS_PETICION = "";
                            }
                            $SALIDA_PORTUGUES = $Table->item($i)->getElementsByTagName("SALIDA_PORTUGUES");
                            if ($SALIDA_PORTUGUES->length > 0) {
                                $SALIDA_PORTUGUES = $SALIDA_PORTUGUES->item(0)->nodeValue;
                            } else {
                                $SALIDA_PORTUGUES = "";
                            }

                            /* try {
                                $sql = new Sql($db);
                                $insert = $sql->insert();
                                $insert->into('datas');
                                $insert->values(array(
                                    'datetime_created' => time(),
                                    'datetime_updated' => 0,
                                    'Fecha' => $Fecha,
                                    'Nombre_Temporada' => $Nombre_Temporada,
                                    'PLAZAS_DISPONIBLES' => $PLAZAS_DISPONIBLES,
                                    'BAJO_PETICION' => $BAJO_PETICION,
                                    'PLAZAS_PETICION' => $PLAZAS_PETICION,
                                    'SALIDA_PORTUGUES' => $SALIDA_PORTUGUES
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
                            } */
                        }
                    }
                }
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