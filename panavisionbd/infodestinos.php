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
$sql = "select value from settings where name='enablepanavisionpackages' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_panavisionpackages = $affiliate_id;
} else {
    $affiliate_id_panavisionpackages = 0;
}
$sql = "select value from settings where name='panavisionpackagesuser' and affiliate_id=$affiliate_id_panavisionpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panavisionpackagesuser = $row_settings['value'];
}
$sql = "select value from settings where name='panavisionpackagespassword' and affiliate_id=$affiliate_id_panavisionpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $panavisionpackagespassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='panavisionpackagesserviceURL' and affiliate_id=$affiliate_id_panavisionpackages";
$statement = $db->createStatement($sql);
$statement->prepare();
$result = $statement->execute();
$result->buffer();
if ($result->valid()) {
    $row = $result->current();
    $panavisionpackagesserviceURL = $row['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$raw = '<soapenv:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tms="TmsApi">
   <soapenv:Header/>
   <soapenv:Body>
      <tms:infoDestinos soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
   </soapenv:Body>
</soapenv:Envelope>';

$client = new Client();
$client->setOptions(array(
    'timeout' => 100,
    'sslverifypeer' => false,
    'sslverifyhost' => false
));
$client->setAuth($panavisionpackagesuser, $panavisionpackagespassword);
$client->setHeaders(array(
    'Accept-Encoding' => 'gzip,deflate',
    'X-Powered-By' => 'Zend Framework',
    'Content-Length' => strlen($raw),
    'Content-Type' => 'text/xml'
));
$client->setUri($panavisionpackagesserviceURL);
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

$config = new \Zend\Config\Config(include '../config/autoload/global.panavision.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];
$db = new \Zend\Db\Adapter\Adapter($config);

echo $return;
echo $response;
echo $return;
$response = htmlspecialchars_decode($response);
$start = strpos($response, "<destinos>");
$end = strpos($response, "</destinos>");
$response = substr($response, $start, $end - $start + 11);
/* echo $return;
echo $response;
echo $return; */

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$destino = $inputDoc->getElementsByTagName("destino");
for ($i = 0; $i < $destino->length; $i++) {
    $codCiudad = $destino->item($i)->getElementsByTagName("codCiudad");
    if ($codCiudad->length > 0) {
        $codCiudad = $codCiudad->item(0)->nodeValue;
    } else {
        $codCiudad = "";
    }
    echo $return;
    echo $codCiudad;
    echo $return;
    $nombreCiudad = $destino->item($i)->getElementsByTagName("nombreCiudad");
    if ($nombreCiudad->length > 0) {
        $nombreCiudad = $nombreCiudad->item(0)->nodeValue;
    } else {
        $nombreCiudad = "";
    }
    $codPais = $destino->item($i)->getElementsByTagName("codPais");
    if ($codPais->length > 0) {
        $codPais = $codPais->item(0)->nodeValue;
    } else {
        $codPais = "";
    }
    $nombrePais = $destino->item($i)->getElementsByTagName("nombrePais");
    if ($nombrePais->length > 0) {
        $nombrePais = $nombrePais->item(0)->nodeValue;
    } else {
        $nombrePais = "";
    }
    $latitud = $destino->item($i)->getElementsByTagName("latitud");
    if ($latitud->length > 0) {
        $latitud = $latitud->item(0)->nodeValue;
    } else {
        $latitud = "";
    }
    $longitud = $destino->item($i)->getElementsByTagName("longitud");
    if ($longitud->length > 0) {
        $longitud = $longitud->item(0)->nodeValue;
    } else {
        $longitud = "";
    }

    try {
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('destinos');
        $select->where(array(
        'codCiudad' => $codCiudad
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $data = $result->current();
            $id = $data['codCiudad'];
            if (strlen($id) > 0) {
                $sql = new Sql($db);
                $data = array(
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'codCiudad' => $codCiudad,
                    'nombreCiudad' => $nombreCiudad,
                    'codPais' => $codPais,
                    'nombrePais' => $nombrePais,
                    'latitud' => $latitud,
                    'longitud' => $longitud
                );
                $where['codCiudad = ?'] = $codCiudad;
                $update = $sql->update('destinos', $data, $where);
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('destinos');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'codCiudad' => $codCiudad,
                    'nombreCiudad' => $nombreCiudad,
                    'codPais' => $codPais,
                    'nombrePais' => $nombrePais,
                    'latitud' => $latitud,
                    'longitud' => $longitud
                ), $insert::VALUES_MERGE);
                $statement = $sql->prepareStatementForSqlObject($insert);
                $results = $statement->execute();
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            }
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('destinos');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codCiudad' => $codCiudad,
                'nombreCiudad' => $nombreCiudad,
                'codPais' => $codPais,
                'nombrePais' => $nombrePais,
                'latitud' => $latitud,
                'longitud' => $longitud
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
            ->getConnection()
            ->disconnect();
       }
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
