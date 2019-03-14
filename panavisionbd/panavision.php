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
      <tms:consultarCircuitos soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"/>
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

$response = htmlspecialchars_decode($response);
$start = strpos($response, "<catalogo>");
$end = strpos($response, "</catalogo>");
$response = substr($response, $start, $end - $start + 11);
/* echo $return;
echo $response;
echo $return; */

$inputDoc = new DOMDocument();
$inputDoc->loadXML($response);
$circuito = $inputDoc->getElementsByTagName("circuito");
for ($i = 0; $i < $circuito->length; $i++) {
    $idproducto = $circuito->item($i)->getElementsByTagName("idproducto");
    if ($idproducto->length > 0) {
        $idproducto = $idproducto->item(0)->nodeValue;
    } else {
        $idproducto = "";
    }
    echo $return;
    echo $idproducto;
    echo $return;
    $idfolleto = $circuito->item($i)->getElementsByTagName("idfolleto");
    if ($idfolleto->length > 0) {
        $idfolleto = $idfolleto->item(0)->nodeValue;
    } else {
        $idfolleto = "";
    }
    $idcircuito = $circuito->item($i)->getElementsByTagName("idcircuito");
    if ($idcircuito->length > 0) {
        $idcircuito = $idcircuito->item(0)->nodeValue;
    } else {
        $idcircuito = "";
    }
    $nombre = $circuito->item($i)->getElementsByTagName("nombre");
    if ($nombre->length > 0) {
        $nombre = $nombre->item(0)->nodeValue;
    } else {
        $nombre = "";
    }
    $idzona = $circuito->item($i)->getElementsByTagName("idzona");
    if ($idzona->length > 0) {
        $idzona = $idzona->item(0)->nodeValue;
    } else {
        $idzona = "";
    }
    $origen = $circuito->item($i)->getElementsByTagName("origen");
    if ($origen->length > 0) {
        $origen = $origen->item(0)->nodeValue;
    } else {
        $origen = "";
    }

    try {
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('circuitos');
        $select->where(array(
        'idcircuito' => $idcircuito
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $data = $result->current();
            $id = $data['idcircuito'];
            if (strlen($id) > 0) {
                $sql = new Sql($db);
                $data = array(
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'idproducto' => $idproducto,
                    'idfolleto' => $idfolleto,
                    'idcircuito' => $idcircuito,
                    'nombre' => $nombre,
                    'idzona' => $idzona,
                    'origen' => $origen
                );
                $where['idcircuito = ?'] = $idcircuito;
                $update = $sql->update('circuitos', $data, $where);
                $db->getDriver()
                ->getConnection()
                ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('circuitos');
                $insert->values(array(
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'idproducto' => $idproducto,
                    'idfolleto' => $idfolleto,
                    'idcircuito' => $idcircuito,
                    'nombre' => $nombre,
                    'idzona' => $idzona,
                    'origen' => $origen
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
            $insert->into('circuitos');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idproducto' => $idproducto,
                'idfolleto' => $idfolleto,
                'idcircuito' => $idcircuito,
                'nombre' => $nombre,
                'idzona' => $idzona,
                'origen' => $origen
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
    
    $fsalida = $circuito->item($i)->getElementsByTagName("fsalida");
    $date = "";
    for ($j = 0; $j < $fsalida->length; $j++) {
        $date = $fsalida->item($j)->nodeValue;
        echo $return;
        echo $date;
        echo $return;

        try {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('circuitos_datasaida');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'idcircuito' => $idcircuito,
                'fsalida' => $date
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

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>
