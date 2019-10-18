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
echo "COMECOU FULL DETAIL";
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

        $user = 'CTMWS';
        $pass = 'Ctmws123';

        $raw = '<?xml version="1.0" encoding="utf-8"?>
        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
        <soap:Body>
            <getViajeFullDetail xmlns="http://tempuri.org/">
                <ID_Viaje>' . $ID_Viaje . '</ID_Viaje>
                <userName>' . $user . '</userName>
                <userPassword>' . $pass . '</userPassword>
            </getViajeFullDetail>
        </soap:Body>
        </soap:Envelope>';

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
        $getViajeFullDetailResponse = $Body->item(0)->getElementsByTagName("getViajeFullDetailResponse");
        $getViajeFullDetailResult = $getViajeFullDetailResponse->item(0)->getElementsByTagName("getViajeFullDetailResult");

        //diffgram
        $diffgram = $getViajeFullDetailResult->item(0)->getElementsByTagName("diffgram");
        if ($diffgram->length > 0) {
            $NewDataSet = $diffgram->item(0)->getElementsByTagName("NewDataSet");
            if ($NewDataSet->length > 0) {
                $Table = $NewDataSet->item(0)->getElementsByTagName("Table");
                if ($Table->length > 0) {
                    for ($i=0; $i < $Table->length; $i++) { 
                        $Salida_Garantizada = $Table->item($i)->getElementsByTagName("Salida_Garantizada");
                        if ($Salida_Garantizada->length > 0) {
                            $Salida_Garantizada = $Salida_Garantizada->item(0)->nodeValue;
                        } else {
                            $Salida_Garantizada = "";
                        }
                        $Media_Pension = $Table->item($i)->getElementsByTagName("Media_Pension");
                        if ($Media_Pension->length > 0) {
                            $Media_Pension = $Media_Pension->item(0)->nodeValue;
                        } else {
                            $Media_Pension = "";
                        }
                        $ID_Viaje = $Table->item($i)->getElementsByTagName("ID_Viaje");
                        if ($ID_Viaje->length > 0) {
                            $ID_Viaje = $ID_Viaje->item(0)->nodeValue;
                        } else {
                            $ID_Viaje = "";
                        }
                        $Nombre_Viaje = $Table->item($i)->getElementsByTagName("Nombre_Viaje");
                        if ($Nombre_Viaje->length > 0) {
                            $Nombre_Viaje = $Nombre_Viaje->item(0)->nodeValue;
                        } else {
                            $Nombre_Viaje = "";
                        }
                        $Fecha_Inicio_America = $Table->item($i)->getElementsByTagName("Fecha_Inicio_America");
                        if ($Fecha_Inicio_America->length > 0) {
                            $Fecha_Inicio_America = $Fecha_Inicio_America->item(0)->nodeValue;
                        } else {
                            $Fecha_Inicio_America = "";
                        }
                        $ROTATIVO = $Table->item($i)->getElementsByTagName("ROTATIVO");
                        if ($ROTATIVO->length > 0) {
                            $ROTATIVO = $ROTATIVO->item(0)->nodeValue;
                        } else {
                            $ROTATIVO = "";
                        }
                        $Viaje_Incluye = $Table->item($i)->getElementsByTagName("Viaje_Incluye");
                        if ($Viaje_Incluye->length > 0) {
                            $Viaje_Incluye = $Viaje_Incluye->item(0)->nodeValue;
                        } else {
                            $Viaje_Incluye = "";
                        }
                        $Traslado_Llegada = $Table->item($i)->getElementsByTagName("Traslado_Llegada");
                        if ($Traslado_Llegada->length > 0) {
                            $Traslado_Llegada = $Traslado_Llegada->item(0)->nodeValue;
                        } else {
                            $Traslado_Llegada = "";
                        }
                        $Traslado_Salida = $Table->item($i)->getElementsByTagName("Traslado_Salida");
                        if ($Traslado_Salida->length > 0) {
                            $Traslado_Salida = $Traslado_Salida->item(0)->nodeValue;
                        } else {
                            $Traslado_Salida = "";
                        }
                        $VIAJE_A_MEDIDA = $Table->item($i)->getElementsByTagName("VIAJE_A_MEDIDA");
                        if ($VIAJE_A_MEDIDA->length > 0) {
                            $VIAJE_A_MEDIDA = $VIAJE_A_MEDIDA->item(0)->nodeValue;
                        } else {
                            $VIAJE_A_MEDIDA = "";
                        }
                        $PARADAS = $Table->item($i)->getElementsByTagName("PARADAS");
                        if ($PARADAS->length > 0) {
                            $PARADAS = $PARADAS->item(0)->nodeValue;
                        } else {
                            $PARADAS = "";
                        }
                        $Confirmacion_Inmediata = $Table->item($i)->getElementsByTagName("Confirmacion_Inmediata");
                        if ($Confirmacion_Inmediata->length > 0) {
                            $Confirmacion_Inmediata = $Confirmacion_Inmediata->item(0)->nodeValue;
                        } else {
                            $Confirmacion_Inmediata = "";
                        }
                        $Pasajero_Club = $Table->item($i)->getElementsByTagName("Pasajero_Club");
                        if ($Pasajero_Club->length > 0) {
                            $Pasajero_Club = $Pasajero_Club->item(0)->nodeValue;
                        } else {
                            $Pasajero_Club = "";
                        }
                        $TIPOVIAJE = $Table->item($i)->getElementsByTagName("TIPOVIAJE");
                        if ($TIPOVIAJE->length > 0) {
                            $TIPOVIAJE = $TIPOVIAJE->item(0)->nodeValue;
                        } else {
                            $TIPOVIAJE = "";
                        }
                        $NOMBRE_VIAJE_PORTUGUES = $Table->item($i)->getElementsByTagName("NOMBRE_VIAJE_PORTUGUES");
                        if ($NOMBRE_VIAJE_PORTUGUES->length > 0) {
                            $NOMBRE_VIAJE_PORTUGUES = $NOMBRE_VIAJE_PORTUGUES->item(0)->nodeValue;
                        } else {
                            $NOMBRE_VIAJE_PORTUGUES = "";
                        }
                        $COLOR_VIAJE = $Table->item($i)->getElementsByTagName("COLOR_VIAJE");
                        if ($COLOR_VIAJE->length > 0) {
                            $COLOR_VIAJE = $COLOR_VIAJE->item(0)->nodeValue;
                        } else {
                            $COLOR_VIAJE = "";
                        }
                        $TEMPORADA = $Table->item($i)->getElementsByTagName("TEMPORADA");
                        if ($TEMPORADA->length > 0) {
                            $TEMPORADA = $TEMPORADA->item(0)->nodeValue;
                        } else {
                            $TEMPORADA = "";
                        }
                        $Numero_Dias = $Table->item($i)->getElementsByTagName("Numero_Dias");
                        if ($Numero_Dias->length > 0) {
                            $Numero_Dias = $Numero_Dias->item(0)->nodeValue;
                        } else {
                            $Numero_Dias = "";
                        }
                        $WordEuros = $Table->item($i)->getElementsByTagName("WordEuros");
                        if ($WordEuros->length > 0) {
                            $WordEuros = $WordEuros->item(0)->nodeValue;
                        } else {
                            $WordEuros = "";
                        }
                        $WordDolares = $Table->item($i)->getElementsByTagName("WordDolares");
                        if ($WordDolares->length > 0) {
                            $WordDolares = $WordDolares->item(0)->nodeValue;
                        } else {
                            $WordDolares = "";
                        }
                        $PdfEuros = $Table->item($i)->getElementsByTagName("PdfEuros");
                        if ($PdfEuros->length > 0) {
                            $PdfEuros = $PdfEuros->item(0)->nodeValue;
                        } else {
                            $PdfEuros = "";
                        }
                        $PdfDolares = $Table->item($i)->getElementsByTagName("PdfDolares");
                        if ($PdfDolares->length > 0) {
                            $PdfDolares = $PdfDolares->item(0)->nodeValue;
                        } else {
                            $PdfDolares = "";
                        }
                        $WordBrasil = $Table->item($i)->getElementsByTagName("WordBrasil");
                        if ($WordBrasil->length > 0) {
                            $WordBrasil = $WordBrasil->item(0)->nodeValue;
                        } else {
                            $WordBrasil = "";
                        }
                        $PdfBrasil = $Table->item($i)->getElementsByTagName("PdfBrasil");
                        if ($PdfBrasil->length > 0) {
                            $PdfBrasil = $PdfBrasil->item(0)->nodeValue;
                        } else {
                            $PdfBrasil = "";
                        }
                        $EXCLUSIVO_BRASIL = $Table->item($i)->getElementsByTagName("EXCLUSIVO_BRASIL");
                        if ($EXCLUSIVO_BRASIL->length > 0) {
                            $EXCLUSIVO_BRASIL = $EXCLUSIVO_BRASIL->item(0)->nodeValue;
                        } else {
                            $EXCLUSIVO_BRASIL = "";
                        }
                        $EXCLUSIVO_ATOM = $Table->item($i)->getElementsByTagName("EXCLUSIVO_ATOM");
                        if ($EXCLUSIVO_ATOM->length > 0) {
                            $EXCLUSIVO_ATOM = $EXCLUSIVO_ATOM->item(0)->nodeValue;
                        } else {
                            $EXCLUSIVO_ATOM = "";
                        }
                        $VIAJE_OBSERVACIONES = $Table->item($i)->getElementsByTagName("VIAJE_OBSERVACIONES");
                        if ($VIAJE_OBSERVACIONES->length > 0) {
                            $VIAJE_OBSERVACIONES = $VIAJE_OBSERVACIONES->item(0)->nodeValue;
                        } else {
                            $VIAJE_OBSERVACIONES = "";
                        }
                        $TIPO_VIAJE_ESP = $Table->item($i)->getElementsByTagName("TIPO_VIAJE_ESP");
                        if ($TIPO_VIAJE_ESP->length > 0) {
                            $TIPO_VIAJE_ESP = $TIPO_VIAJE_ESP->item(0)->nodeValue;
                        } else {
                            $TIPO_VIAJE_ESP = "";
                        }
                        $COLOR_VIAJE_ESP = $Table->item($i)->getElementsByTagName("COLOR_VIAJE_ESP");
                        if ($COLOR_VIAJE_ESP->length > 0) {
                            $COLOR_VIAJE_ESP = $COLOR_VIAJE_ESP->item(0)->nodeValue;
                        } else {
                            $COLOR_VIAJE_ESP = "";
                        }
                        $PdfEspana = $Table->item($i)->getElementsByTagName("PdfEspana");
                        if ($PdfEspana->length > 0) {
                            $PdfEspana = $PdfEspana->item(0)->nodeValue;
                        } else {
                            $PdfEspana = "";
                        }
                        $WordEspana = $Table->item($i)->getElementsByTagName("WordEspana");
                        if ($WordEspana->length > 0) {
                            $WordEspana = $WordEspana->item(0)->nodeValue;
                        } else {
                            $WordEspana = "";
                        }
                        $UrlImagenAlta = $Table->item($i)->getElementsByTagName("UrlImagenAlta");
                        if ($UrlImagenAlta->length > 0) {
                            $UrlImagenAlta = $UrlImagenAlta->item(0)->nodeValue;
                        } else {
                            $UrlImagenAlta = "";
                        }
                        $UrlImagenEspAlta = $Table->item($i)->getElementsByTagName("UrlImagenEspAlta");
                        if ($UrlImagenEspAlta->length > 0) {
                            $UrlImagenEspAlta = $UrlImagenEspAlta->item(0)->nodeValue;
                        } else {
                            $UrlImagenEspAlta = "";
                        }
                        $UrlImagenBrasilAlta = $Table->item($i)->getElementsByTagName("UrlImagenBrasilAlta");
                        if ($UrlImagenBrasilAlta->length > 0) {
                            $UrlImagenBrasilAlta = $UrlImagenBrasilAlta->item(0)->nodeValue;
                        } else {
                            $UrlImagenBrasilAlta = "";
                        }
                        $UrlImagenMedia = $Table->item($i)->getElementsByTagName("UrlImagenMedia");
                        if ($UrlImagenMedia->length > 0) {
                            $UrlImagenMedia = $UrlImagenMedia->item(0)->nodeValue;
                        } else {
                            $UrlImagenMedia = "";
                        }
                        $UrlImagenEspMedia = $Table->item($i)->getElementsByTagName("UrlImagenEspMedia");
                        if ($UrlImagenEspMedia->length > 0) {
                            $UrlImagenEspMedia = $UrlImagenEspMedia->item(0)->nodeValue;
                        } else {
                            $UrlImagenEspMedia = "";
                        }
                        $UrlImagenBrasilMedia = $Table->item($i)->getElementsByTagName("UrlImagenBrasilMedia");
                        if ($UrlImagenBrasilMedia->length > 0) {
                            $UrlImagenBrasilMedia = $UrlImagenBrasilMedia->item(0)->nodeValue;
                        } else {
                            $UrlImagenBrasilMedia = "";
                        }
                        $UrlImagenBaja = $Table->item($i)->getElementsByTagName("UrlImagenBaja");
                        if ($UrlImagenBaja->length > 0) {
                            $UrlImagenBaja = $UrlImagenBaja->item(0)->nodeValue;
                        } else {
                            $UrlImagenBaja = "";
                        }
                        $UrlImagenEspBaja = $Table->item($i)->getElementsByTagName("UrlImagenEspBaja");
                        if ($UrlImagenEspBaja->length > 0) {
                            $UrlImagenEspBaja = $UrlImagenEspBaja->item(0)->nodeValue;
                        } else {
                            $UrlImagenEspBaja = "";
                        }
                        $UrlImagenBrasilBaja = $Table->item($i)->getElementsByTagName("UrlImagenBrasilBaja");
                        if ($UrlImagenBrasilBaja->length > 0) {
                            $UrlImagenBrasilBaja = $UrlImagenBrasilBaja->item(0)->nodeValue;
                        } else {
                            $UrlImagenBrasilBaja = "";
                        }
                        $VIAJE_FOLLETO_PORTUGAL = $Table->item($i)->getElementsByTagName("VIAJE_FOLLETO_PORTUGAL");
                        if ($VIAJE_FOLLETO_PORTUGAL->length > 0) {
                            $VIAJE_FOLLETO_PORTUGAL = $VIAJE_FOLLETO_PORTUGAL->item(0)->nodeValue;
                        } else {
                            $VIAJE_FOLLETO_PORTUGAL = "";
                        }
                        $UrlImagenPortugalAlta = $Table->item($i)->getElementsByTagName("UrlImagenPortugalAlta");
                        if ($UrlImagenPortugalAlta->length > 0) {
                            $UrlImagenPortugalAlta = $UrlImagenPortugalAlta->item(0)->nodeValue;
                        } else {
                            $UrlImagenPortugalAlta = "";
                        }
                        $UrlImagenPortugalMedia = $Table->item($i)->getElementsByTagName("UrlImagenPortugalMedia");
                        if ($UrlImagenPortugalMedia->length > 0) {
                            $UrlImagenPortugalMedia = $UrlImagenPortugalMedia->item(0)->nodeValue;
                        } else {
                            $UrlImagenPortugalMedia = "";
                        }
                        $UrlImagenPortugalBaja = $Table->item($i)->getElementsByTagName("UrlImagenPortugalBaja");
                        if ($UrlImagenPortugalBaja->length > 0) {
                            $UrlImagenPortugalBaja = $UrlImagenPortugalBaja->item(0)->nodeValue;
                        } else {
                            $UrlImagenPortugalBaja = "";
                        }
                        $WordPortugal = $Table->item($i)->getElementsByTagName("WordPortugal");
                        if ($WordPortugal->length > 0) {
                            $WordPortugal = $WordPortugal->item(0)->nodeValue;
                        } else {
                            $WordPortugal = "";
                        }
                        $PdfPortugal = $Table->item($i)->getElementsByTagName("PdfPortugal");
                        if ($PdfPortugal->length > 0) {
                            $PdfPortugal = $PdfPortugal->item(0)->nodeValue;
                        } else {
                            $PdfPortugal = "";
                        }
                        $WordEnglish = $Table->item($i)->getElementsByTagName("WordEnglish");
                        if ($WordEnglish->length > 0) {
                            $WordEnglish = $WordEnglish->item(0)->nodeValue;
                        } else {
                            $WordEnglish = "";
                        }
                        $PdfEnglish = $Table->item($i)->getElementsByTagName("PdfEnglish");
                        if ($PdfEnglish->length > 0) {
                            $PdfEnglish = $PdfEnglish->item(0)->nodeValue;
                        } else {
                            $PdfEnglish = "";
                        }
                        $WordEnglishDolares = $Table->item($i)->getElementsByTagName("PdfEnglish");
                        if ($WordEnglishDolares->length > 0) {
                            $WordEnglishDolares = $WordEnglishDolares->item(0)->nodeValue;
                        } else {
                            $WordEnglishDolares = "";
                        }
                        $PdfEnglishDolares = $Table->item($i)->getElementsByTagName("PdfEnglishDolares");
                        if ($PdfEnglishDolares->length > 0) {
                            $PdfEnglishDolares = $PdfEnglishDolares->item(0)->nodeValue;
                        } else {
                            $PdfEnglishDolares = "";
                        }
                        $NOMBRE_VIAJE_ENGLISH = $Table->item($i)->getElementsByTagName("NOMBRE_VIAJE_ENGLISH");
                        if ($NOMBRE_VIAJE_ENGLISH->length > 0) {
                            $NOMBRE_VIAJE_ENGLISH = $NOMBRE_VIAJE_ENGLISH->item(0)->nodeValue;
                        } else {
                            $NOMBRE_VIAJE_ENGLISH = "";
                        }
                        $PERMITE_CUADRUPLE = $Table->item($i)->getElementsByTagName("PERMITE_CUADRUPLE");
                        if ($PERMITE_CUADRUPLE->length > 0) {
                            $PERMITE_CUADRUPLE = $PERMITE_CUADRUPLE->item(0)->nodeValue;
                        } else {
                            $PERMITE_CUADRUPLE = "";
                        }
                        $WordRuso = $Table->item($i)->getElementsByTagName("WordRuso");
                        if ($WordRuso->length > 0) {
                            $WordRuso = $WordRuso->item(0)->nodeValue;
                        } else {
                            $WordRuso = "";
                        }
                        $PdfRuso = $Table->item($i)->getElementsByTagName("PdfRuso");
                        if ($PdfRuso->length > 0) {
                            $PdfRuso = $PdfRuso->item(0)->nodeValue;
                        } else {
                            $PdfRuso = "";
                        }

                        /* try {
                            $sql = new Sql($db);
                            $insert = $sql->insert();
                            $insert->into('viagemFulldetail');
                            $insert->values(array(
                                'datetime_created' => time(),
                                'datetime_updated' => 0,
                                'Salida_Garantizada' => $Salida_Garantizada,
                                'Media_Pension' => $Media_Pension,
                                'ID_Viaje' => $ID_Viaje,
                                'Nombre_Viaje' => $Nombre_Viaje,
                                'Fecha_Inicio_America' => $Fecha_Inicio_America,
                                'ROTATIVO' => $ROTATIVO,
                                'Viaje_Incluye' => $Viaje_Incluye,
                                'Traslado_Llegada' => $Traslado_Llegada,
                                'Traslado_Salida' => $Traslado_Salida,
                                'VIAJE_A_MEDIDA' => $VIAJE_A_MEDIDA,
                                'PARADAS' => $PARADAS,
                                'Confirmacion_Inmediata' => $Confirmacion_Inmediata,
                                'Pasajero_Club' => $Pasajero_Club,
                                'TIPOVIAJE' => $TIPOVIAJE,
                                'NOMBRE_VIAJE_PORTUGUES' => $NOMBRE_VIAJE_PORTUGUES,
                                'COLOR_VIAJE' => $COLOR_VIAJE,
                                'TEMPORADA' => $TEMPORADA,
                                'Numero_Dias' => $Numero_Dias,
                                'WordEuros' => $WordEuros,
                                'WordDolares' => $WordDolares,
                                'PdfEuros' => $PdfEuros,
                                'PdfDolares' => $PdfDolares,
                                'WordBrasil' => $WordBrasil,
                                'PdfBrasil' => $PdfBrasil,
                                'EXCLUSIVO_BRASIL' => $EXCLUSIVO_BRASIL,
                                'EXCLUSIVO_ATOM' => $EXCLUSIVO_ATOM,
                                'VIAJE_OBSERVACIONES' => $VIAJE_OBSERVACIONES,
                                'TIPO_VIAJE_ESP' => $TIPO_VIAJE_ESP,
                                'COLOR_VIAJE_ESP' => $COLOR_VIAJE_ESP,
                                'PdfEspana' => $PdfEspana,
                                'WordEspana' => $WordEspana,
                                'UrlImagenAlta' => $UrlImagenAlta,
                                'UrlImagenEspAlta' => $UrlImagenEspAlta,
                                'UrlImagenBrasilAlta' => $UrlImagenBrasilAlta,
                                'UrlImagenMedia' => $UrlImagenMedia,
                                'UrlImagenEspMedia' => $UrlImagenEspMedia,
                                'UrlImagenBrasilMedia' => $UrlImagenBrasilMedia,
                                'UrlImagenBaja' => $UrlImagenBaja,
                                'UrlImagenEspBaja' => $UrlImagenEspBaja,
                                'UrlImagenBrasilBaja' => $UrlImagenBrasilBaja,
                                'VIAJE_FOLLETO_PORTUGAL' => $VIAJE_FOLLETO_PORTUGAL,
                                'UrlImagenPortugalAlta' => $UrlImagenPortugalAlta,
                                'UrlImagenPortugalMedia' => $UrlImagenPortugalMedia,
                                'UrlImagenPortugalBaja' => $UrlImagenPortugalBaja,
                                'WordPortugal' => $WordPortugal,
                                'PdfPortugal' => $PdfPortugal,
                                'WordEnglish' => $WordEnglish,
                                'PdfEnglish' => $PdfEnglish,
                                'WordEnglishDolares' => $WordEnglishDolares,
                                'PdfEnglishDolares' => $PdfEnglishDolares,
                                'NOMBRE_VIAJE_ENGLISH' => $NOMBRE_VIAJE_ENGLISH,
                                'PERMITE_CUADRUPLE' => $PERMITE_CUADRUPLE,
                                'WordRuso' => $WordRuso,
                                'PdfRuso' => $PdfRuso
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>