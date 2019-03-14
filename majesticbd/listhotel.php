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
$sql = "select value from settings where name='enablemajesticusa' and affiliate_id=$affiliate_id" . $branch_filter;
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $affiliate_id_majestic = $affiliate_id;
} else {
    $affiliate_id_majestic = 0;
}
$sql = "select value from settings where name='majesticusaLoginEmail' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaLoginEmail = $row_settings['value'];
}
$sql = "select value from settings where name='majesticusaPassword' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaPassword = base64_decode($row_settings['value']);
}
$sql = "select value from settings where name='majesticusaMarkup' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaMarkup = (double) $row_settings['value'];
} else {
    $majesticusaMarkup = 0;
}
$sql = "select value from settings where name='majesticusaServiceURL' and affiliate_id=$affiliate_id_majestic";
$statement = $db->createStatement($sql);
$statement->prepare();
$row_settings = $statement->execute();
$row_settings->buffer();
if ($row_settings->valid()) {
    $row_settings = $row_settings->current();
    $majesticusaServiceURL = $row_settings['value'];
}
$db->getDriver()
    ->getConnection()
    ->disconnect();

$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
$config = [
    'driver' => $config->db->driver,
    'database' => $config->db->database,
    'username' => $config->db->username,
    'password' => $config->db->password,
    'hostname' => $config->db->hostname
];


$raw = '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
xmlns:xsd="http://www.w3.org/2001/XMLSchema">
 <SOAP-ENV:Header>
     <m:AuthHeader xmlns:m="http://www.majesticusa.com/majesticweb_xml/">
         <m:Username>' . $majesticusaLoginEmail . '</m:Username>
         <m:Password>' . $majesticusaPassword . '</m:Password>
     </m:AuthHeader>
 </SOAP-ENV:Header>
 <SOAP-ENV:Body>
     <m:ListHotels xmlns:m="http://www.majesticusa.com/majesticweb_xml/">
         <m:hoteltypeid>1</m:hoteltypeid>		
         <m:arrival>2020-01-01T09:30:47.0Z</m:arrival>
         <m:departure>2020-12-31T09:30:47.0Z</m:departure>
         <m:adults>2</m:adults>
         <m:childs>0</m:childs>
     </m:ListHotels>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';

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
    'Content-Type' => 'text/xml'
));
$client->setUri($majesticusaServiceURL);
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
echo "RESPONSE";
/* echo $return;
echo $response;
echo $return; */
echo '<xmp>';
var_dump($response);
echo '</xmp>';
$config = new \Zend\Config\Config(include '../config/autoload/global.majestic.php');
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
$ListHotelsResponse = $Body->item(0)->getElementsByTagName("ListHotelsResponse");
$ListHotelsResult = $ListHotelsResponse->item(0)->getElementsByTagName("ListHotelsResult");
$Root = $ListHotelsResult->item(0)->getElementsByTagName("Root");
$node = $Root->item(0)->getElementsByTagName("Hotel");
echo $return;
echo "TAM: " . $node->length;
echo $return;
for ($i = 0; $i < $node->length; $i++) {
    echo $return;
    echo $i;
    echo $return;
    $id = $node->item($i)->getElementsByTagName("id");
    if ($id->length > 0) {
        $id = $id->item(0)->nodeValue;
    } else {
        $id = "";
    }
    echo $return;
    echo $id;
    echo $return;
    $name = $node->item($i)->getElementsByTagName("name");
    if ($name->length > 0) {
        $name = $name->item(0)->nodeValue;
    } else {
        $name = "";
    }
    $city = $node->item($i)->getElementsByTagName("city");
    if ($city->length > 0) {
        $city = $city->item(0)->nodeValue;
    } else {
        $city = "";
    }
    $country = $node->item($i)->getElementsByTagName("country");
    if ($country->length > 0) {
        $country = $country->item(0)->nodeValue;
    } else {
        $country = "";
    }
    $recomended = $node->item($i)->getElementsByTagName("recomended");
    if ($recomended->length > 0) {
        $recomended = $recomended->item(0)->nodeValue;
    } else {
        $recomended = "";
    }
    $stars = $node->item($i)->getElementsByTagName("stars");
    if ($stars->length > 0) {
        $stars = $stars->item(0)->nodeValue;
    } else {
        $stars = "";
    }

    try {
        $sql = new Sql($db);
        $select = $sql->select();
        $select->from('hoteis');
        $select->where(array(
            'id' => $id
        ));
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $result->buffer();
        $customers = array();
        if ($result->valid()) {
            $data = $result->current();
            $id = (int)$data['id'];
            if ($id > 0) {
                $sql = new Sql($db);
                $data = array(
                    'id' => $id,
                    'datetime_created' => time(),
                    'datetime_updated' => 1,
                    'name' => $name,
                    'city' => $city,
                    'country' => $country,
                    'recomended' => $recomended,
                    'stars' => $stars
                );
                $where['id = ?'] = $id;
                $update = $sql->update('hoteis', $data, $where);
                $db->getDriver()
                    ->getConnection()
                    ->disconnect();
            } else {
                $sql = new Sql($db);
                $insert = $sql->insert();
                $insert->into('hoteis');
                $insert->values(array(
                    'id' => $id,
                    'datetime_created' => time(),
                    'datetime_updated' => 0,
                    'name' => $name,
                    'city' => $city,
                    'country' => $country,
                    'recomended' => $recomended,
                    'stars' => $stars
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
            $insert->into('hoteis');
            $insert->values(array(
                'id' => $id,
                'datetime_created' => time(),
                'datetime_updated' => 0,
                'name' => $name,
                'city' => $city,
                'country' => $country,
                'recomended' => $recomended,
                'stars' => $stars
            ), $insert::VALUES_MERGE);
            $statement = $sql->prepareStatementForSqlObject($insert);
            $results = $statement->execute();
            $db->getDriver()
                ->getConnection()
                ->disconnect();
        }
    } catch (Exception $ex) {
        echo $return;
        echo "ERRO: " . $ex;
        echo $return;
    }
    
}

// EOF
$db->getDriver()
    ->getConnection()
    ->disconnect();
echo '<br/>Done';
?>