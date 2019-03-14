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

$config = new \Zend\Config\Config(include '../config/autoload/global.travelplan.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];

$url = $globaliapackagesserviceURL . 'b2c/services/wstReserva';

$ideSes = "52535383408122501180";
$cadRes = 'rO0ABXNyABh1dGlsaWRhZGVzLkNhZGVuYVJlc2VydmEAAAAAAAAAAQIAAUwABG1hcGF0AA9MamF2YS91dGlsL01hcDt4cHNyABFqYXZhLnV0aWwuSGFzaE1hcAUH2sHDFmDRAwACRgAKbG9hZEZhY3RvckkACXRocmVzaG9sZHhwP0AAAAAAADB3CAAAAEAAAAAbdAAGc3RyTW9kdAALNEAxQDEjMCMwIzt0AAlpZFVzdWFyaW90AAVDVE1UMHQABnB2cFNlcnNyABFqYXZhLmxhbmcuSW50ZWdlchLioKT3gYc4AgABSQAFdmFsdWV4cgAQamF2YS5sYW5nLk51bWJlcoaslR0LlOCLAgAAeHAAAoxYdAAGcHZwVHJhc3EAfgAKAAAAAHQABnJlZlNlcnNxAH4ACgABy7V0AAZpZGlvbWF0AANFU1B0AAZmZWNJbml0AAowNi8wNC8yMDE5dAAGYXB0RGVwcHQABmlkR3J1cHNxAH4ACgAAAAN0AAZlZGFOaW50AAMzMDt0AAllc3REaXNTZXJzcQB)AAoAAAACdAAGYXB0QXJydAACLTF0AAZudW1BZGxzcQB)AAoAAAABdAAJZXN0RGlzVHJhcQB)ABt0AAZyZWZQcm9zcQB)AAoABG8fdAAHcmVnaW1lbnQAAlRJdAAGc3dpUmVzdAABTnQABm51bUluZnEAfgAOdAAJY2F0ZWdvcmlhdAACQ0x0AAZ0aXBTZXJ0AAhDSVJDVUlUT3QABmZlY0ZpbnQACjEzLzA0LzIwMTl0AAZzd2lEaXJzcgARamF2YS5sYW5nLkJvb2xlYW7NIHKA1Zz67gIAAVoABXZhbHVleHAAdAAGbnVtTmlucQB)AA50AAZlZGFkZXN0AAMzMDt0AAZzd2lWdWVxAH4AJnQACWNvZGFncnVwYXQABVBBUjU2dAAGY29uUHJlcHg=';

$raw = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:typ="types.reserva.wst">     <soapenv:Header/>     <soapenv:Body>        <typ:presupuestoRequest>           <typ:idUsuario>' . $globaliapackagesCustomerID . '</typ:idUsuario>           <typ:cadRes>' . $cadRes . '</typ:cadRes>           <!--Optional:-->           <typ:cadVueIda/>           <!--Optional:-->           <typ:cadVueVta/>           <typ:ideSes>' . $ideSes . '</typ:ideSes>        </typ:presupuestoRequest>     </soapenv:Body>  </soapenv:Envelope>';

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
$agrupacionesCircuitoResponse = $Body->item(0)->getElementsByTagName("agrupacionesCircuitoResponse");
$node = $agrupacionesCircuitoResponse->item(0)->getElementsByTagName("agrupacionesCircuitoResponseRow");
for ($iAux = 0; $iAux < $node->length; $iAux ++) {
    $codAgr = $node->item($iAUX)->getElementsByTagName("codAgr");
    if ($codAgr->length > 0) {
        $codAgr = $codAgr->item(0)->nodeValue;
    } else {
        $codAgr = "";
    }
    
    $sql = new Sql($db);
    $select = $sql->select();
    $select->from('agrupacion_arbolResponseRowTypeCont');
    $select->where(array(
        'codAgr' => $codAgr
    ));
    $statement = $sql->prepareStatementForSqlObject($select);
    $result = $statement->execute();
    $result->buffer();
    $customers = array();
    if ($result->valid()) {
        $data = $result->current();
        $id = $data['codAgr'];
        if (count($id) > 0) {
            $sql = new Sql($db);
            $data = array(
                'datetime_created' => time(),
                'datetime_updated' => 1,
                'codAgr' => $codAgr
            );
            $where['codAgr = ?'] = $codAgr;
            $update = $sql->update('agrupacion_arbolResponseRowTypeCont', $data, $where);
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        } else {
            $sql = new Sql($db);
            $insert = $sql->insert();
            $insert->into('agrupacion_arbolResponseRowTypeCont');
            $insert->values(array(
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'codAgr' => $codAgr
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
        $insert->into('agrupacion_arbolResponseRowTypeCont');
        $insert->values(array(
            'datetime_created' => time(),
            'datetime_updated' => 0,
            'codAgr' => $codAgr
        ), $insert::VALUES_MERGE);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $results = $statement->execute();
        $db->getDriver()
            ->getConnection()
            ->disconnect();
    }
    
   
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo 'Done';
?>